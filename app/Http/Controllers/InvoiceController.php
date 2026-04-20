<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Invoice;
use App\Services\Ocr\DocumentOcrStub;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Invoice::query()->with(['delivery.purchaseOrder']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('delivery.purchaseOrder', function($pq) use ($search) {
                      $pq->where('po_number', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('delivery', function($dq) use ($search) {
                      $dq->where('delivery_number', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('invoice_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('invoice_date', '<=', $request->date_to);
        }

        if ($request->filled('status') && in_array($request->status, ['issued', 'paid'], true)) {
            $query->where('status', $request->status);
        }

        $invoices = $query->latest()->paginate(15)->withQueryString();

        $deliveries = Delivery::query()
            ->with('purchaseOrder')
            ->whereDoesntHave('invoice')
            ->orderByDesc('id')
            ->get();

        return view('invoices.index', [
            'invoices' => $invoices,
            'deliveries' => $deliveries,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $deliveries = Delivery::query()
            ->with('purchaseOrder')
            ->whereDoesntHave('invoice')
            ->orderByDesc('id')
            ->get();

        return view('invoices.create', [
            'deliveries' => $deliveries,
            'prefill' => session('invoice_prefill', []),
            'pendingDocument' => session('invoice_pending_document'),
            'selectedDeliveryId' => request()->integer('delivery_id'),
        ]);
    }

    public function ocr()
    {
        $deliveries = Delivery::query()
            ->with('purchaseOrder')
            ->whereDoesntHave('invoice')
            ->orderByDesc('id')
            ->get();

        return view('invoices.ocr', [
            'deliveries' => $deliveries,
            'selectedDeliveryId' => request()->integer('delivery_id'),
        ]);
    }

    public function ocrStore(Request $request, DocumentOcrStub $ocr)
    {
        $validated = $request->validate([
            'delivery_id' => ['required', 'integer', 'exists:deliveries,id'],
            'document' => ['required', 'file', 'max:10240'],
        ]);

        $file = $validated['document'];
        $disk = 'public';
        $path = $file->store('archives/invoice/pending', $disk);

        $request->session()->flash('invoice_prefill', array_filter($ocr->extract('invoice', $file)));
        $request->session()->flash('invoice_pending_document', [
            'disk' => $disk,
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'kind' => 'invoice',
            'delivery_id' => (int) $validated['delivery_id'],
        ]);

        return redirect()->route('invoices.create', ['delivery_id' => $validated['delivery_id']]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'delivery_id' => ['required', 'integer', 'exists:deliveries,id'],
            'invoice_number' => ['required', 'string', 'max:100', 'unique:invoices,invoice_number'],
            'invoice_date' => ['required', 'date'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
            'document' => ['nullable', 'file', 'max:10240'],
        ]);

        $userId = $request->user()?->id;

        $invoice = DB::transaction(function () use ($request, $validated, $userId) {
            $invoice = Invoice::create([
                ...collect($validated)->except('document')->all(),
                'created_by' => $userId,
            ]);

            $poStatus = ($validated['status'] ?? 'issued') === 'paid' ? 'selesai' : 'ditagih';
            $invoice->delivery->purchaseOrder()->update(['status' => $poStatus]);

            $this->attachDocumentIfAny($request, $invoice);

            return $invoice;
        });

        return redirect()->route('invoices.show', $invoice);
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['delivery.purchaseOrder', 'documentArchives']);

        return view('invoices.show', [
            'invoice' => $invoice,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice $invoice)
    {
        $deliveries = Delivery::query()->with('purchaseOrder')->orderByDesc('id')->get();

        return view('invoices.edit', [
            'invoice' => $invoice,
            'deliveries' => $deliveries,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'delivery_id' => ['required', 'integer', 'exists:deliveries,id'],
            'invoice_number' => ['required', 'string', 'max:100', 'unique:invoices,invoice_number,'.$invoice->id],
            'invoice_date' => ['required', 'date'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
            'document' => ['nullable', 'file', 'max:10240'],
        ]);

        DB::transaction(function () use ($request, $invoice, $validated) {
            $invoice->update(collect($validated)->except('document')->all());
            $this->attachDocumentIfAny($request, $invoice);

            $poStatus = ($validated['status'] ?? $invoice->status) === 'paid' ? 'selesai' : 'ditagih';
            $invoice->delivery?->purchaseOrder()?->update(['status' => $poStatus]);
        });

        return redirect()->route('invoices.show', $invoice);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        return redirect()->route('invoices.index');
    }

    private function attachDocumentIfAny(Request $request, Invoice $invoice): void
    {
        $disk = 'public';

        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $path = $file->store('archives/invoice', $disk);

            $invoice->documentArchives()->create([
                'kind' => 'invoice',
                'original_name' => $file->getClientOriginalName(),
                'disk' => $disk,
                'path' => $path,
                'mime_type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
                'uploaded_by' => $request->user()?->id,
            ]);

            return;
        }

        $pending = $request->session()->pull('invoice_pending_document');

        if (! is_array($pending) || empty($pending['path']) || empty($pending['disk'])) {
            return;
        }

        $pendingDisk = $pending['disk'];
        $pendingPath = $pending['path'];
        $finalPath = 'archives/invoice/'.$invoice->invoice_number.'-'.basename($pendingPath);

        if (Storage::disk($pendingDisk)->exists($pendingPath)) {
            Storage::disk($pendingDisk)->move($pendingPath, $finalPath);
        }

        $invoice->documentArchives()->create([
            'kind' => 'invoice',
            'original_name' => $pending['original_name'] ?? basename($finalPath),
            'disk' => $pendingDisk,
            'path' => $finalPath,
            'mime_type' => $pending['mime_type'] ?? null,
            'size' => $pending['size'] ?? null,
            'uploaded_by' => $request->user()?->id,
        ]);
    }
}
