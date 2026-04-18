<?php

namespace App\Http\Controllers;

use App\Models\DocumentArchive;
use App\Models\PurchaseOrder;
use App\Services\Ocr\DocumentOcrStub;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PurchaseOrder::query()->withCount('deliveries');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('po_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('product_name', 'like', "%{$search}%");
            });
        }

        $purchaseOrders = $query->latest()->paginate(15);

        return view('purchase_orders.index', [
            'purchaseOrders' => $purchaseOrders,
            'prefill' => session('po_prefill', []),
            'pendingDocument' => session('po_pending_document'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return redirect()->route('purchase-orders.index');
    }

    public function ocr()
    {
        return redirect()->route('purchase-orders.index');
    }

    public function ocrStore(Request $request, DocumentOcrStub $ocr)
    {
        $validated = $request->validate([
            'document' => ['required', 'file', 'max:10240'],
        ]);

        $file = $validated['document'];
        $disk = 'public';
        $path = $file->store('archives/po/pending', $disk);

        $request->session()->flash('po_prefill', array_filter($ocr->extract('po', $file)));
        $request->session()->flash('po_pending_document', [
            'disk' => $disk,
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'kind' => 'po',
        ]);

        return redirect()->route('purchase-orders.index')->with('open_modal', 'createPoModal');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'po_number' => ['required', 'string', 'max:100', 'unique:purchase_orders,po_number'],
            'po_date' => ['required', 'date'],
            'customer_name' => ['required', 'string', 'max:255'],
            'product_name' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:0'],
            'unit' => ['nullable', 'string', 'max:50'],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'document' => ['nullable', 'file', 'max:10240'],
        ]);

        $userId = $request->user()?->id;

        $purchaseOrder = DB::transaction(function () use ($request, $validated, $userId) {
            $unitPrice = $validated['unit_price'] ?? 0;
            $totalAmount = ($validated['quantity'] ?? 0) * $unitPrice;

            $purchaseOrder = PurchaseOrder::create([
                ...collect($validated)->except('document', 'unit_price')->all(),
                'unit_price' => $unitPrice,
                'total_amount' => $totalAmount,
                'status' => 'diproses',
                'created_by' => $userId,
            ]);

            $this->attachDocumentIfAny($request, $purchaseOrder, 'po');

            return $purchaseOrder;
        });

        return redirect()->route('purchase-orders.show', $purchaseOrder);
    }

    /**
     * Display the specified resource.
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['deliveries.invoice', 'documentArchives']);

        return view('purchase_orders.show', [
            'purchaseOrder' => $purchaseOrder,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PurchaseOrder $purchaseOrder)
    {
        return view('purchase_orders.edit', [
            'purchaseOrder' => $purchaseOrder,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $validated = $request->validate([
            'po_number' => ['required', 'string', 'max:100', 'unique:purchase_orders,po_number,'.$purchaseOrder->id],
            'po_date' => ['required', 'date'],
            'customer_name' => ['required', 'string', 'max:255'],
            'product_name' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:0'],
            'unit' => ['nullable', 'string', 'max:50'],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
            'document' => ['nullable', 'file', 'max:10240'],
        ]);

        DB::transaction(function () use ($request, $purchaseOrder, $validated) {
            $unitPrice = $validated['unit_price'] ?? 0;
            $totalAmount = ($validated['quantity'] ?? 0) * $unitPrice;

            $purchaseOrder->update([
                ...collect($validated)->except('document', 'unit_price')->all(),
                'unit_price' => $unitPrice,
                'total_amount' => $totalAmount,
            ]);
            $this->attachDocumentIfAny($request, $purchaseOrder, 'po');
        });

        return redirect()->route('purchase-orders.show', $purchaseOrder);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->delete();

        return redirect()->route('purchase-orders.index');
    }

    public function complete(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->update(['status' => 'selesai']);

        return redirect()->route('purchase-orders.show', $purchaseOrder)
            ->with('status', 'PO berhasil ditandai sebagai SELESAI.');
    }

    private function attachDocumentIfAny(Request $request, PurchaseOrder $purchaseOrder, string $kind): void
    {
        $disk = 'public';

        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $path = $file->store('archives/po', $disk);

            $purchaseOrder->documentArchives()->create([
                'kind' => $kind,
                'original_name' => $file->getClientOriginalName(),
                'disk' => $disk,
                'path' => $path,
                'mime_type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
                'uploaded_by' => $request->user()?->id,
            ]);

            return;
        }

        $pending = $request->session()->pull('po_pending_document');

        if (! is_array($pending) || empty($pending['path']) || empty($pending['disk'])) {
            return;
        }

        $pendingDisk = $pending['disk'];
        $pendingPath = $pending['path'];
        $finalPath = 'archives/po/'.$purchaseOrder->po_number.'-'.basename($pendingPath);

        if ($pendingDisk === $disk && Storage::disk($pendingDisk)->exists($pendingPath)) {
            Storage::disk($pendingDisk)->move($pendingPath, $finalPath);
        }

        $purchaseOrder->documentArchives()->create([
            'kind' => $kind,
            'original_name' => $pending['original_name'] ?? basename($finalPath),
            'disk' => $pendingDisk,
            'path' => $finalPath,
            'mime_type' => $pending['mime_type'] ?? null,
            'size' => $pending['size'] ?? null,
            'uploaded_by' => $request->user()?->id,
        ]);
    }
}
