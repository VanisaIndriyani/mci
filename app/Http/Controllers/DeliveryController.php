<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\PurchaseOrder;
use App\Services\Ocr\DocumentOcrStub;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DeliveryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Delivery::query()->with(['purchaseOrder', 'invoice']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('delivery_number', 'like', "%{$search}%")
                  ->orWhereHas('purchaseOrder', function($pq) use ($search) {
                      $pq->where('po_number', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('delivery_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('delivery_date', '<=', $request->date_to);
        }

        if ($request->filled('status')) {
            if ($request->status === 'belum_ditagih') {
                $query->whereDoesntHave('invoice');
            }
            if ($request->status === 'sudah_ditagih') {
                $query->whereHas('invoice');
            }
        }

        $deliveries = $query->latest()->paginate(15)->withQueryString();
        $purchaseOrders = PurchaseOrder::query()
            ->whereDoesntHave('deliveries')
            ->orderByDesc('id')
            ->get();

        return view('deliveries.index', [
            'deliveries' => $deliveries,
            'purchaseOrders' => $purchaseOrders,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $purchaseOrders = PurchaseOrder::query()
            ->whereDoesntHave('deliveries')
            ->orderByDesc('id')
            ->get();

        return view('deliveries.create', [
            'purchaseOrders' => $purchaseOrders,
            'prefill' => session('delivery_prefill', []),
            'pendingDocument' => session('delivery_pending_document'),
            'selectedPurchaseOrderId' => request()->integer('purchase_order_id'),
        ]);
    }

    public function ocr()
    {
        $purchaseOrders = PurchaseOrder::query()
            ->whereDoesntHave('deliveries')
            ->orderByDesc('id')
            ->get();

        return view('deliveries.ocr', [
            'purchaseOrders' => $purchaseOrders,
            'selectedPurchaseOrderId' => request()->integer('purchase_order_id'),
        ]);
    }

    public function ocrStore(Request $request, DocumentOcrStub $ocr)
    {
        $validated = $request->validate([
            'purchase_order_id' => ['required', 'integer', 'exists:purchase_orders,id'],
            'document' => ['required', 'file', 'max:10240'],
        ]);

        $file = $validated['document'];
        $disk = 'public';
        $path = $file->store('archives/delivery/pending', $disk);

        $request->session()->flash('delivery_prefill', array_filter($ocr->extract('delivery', $file)));
        $request->session()->flash('delivery_pending_document', [
            'disk' => $disk,
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'kind' => 'delivery',
            'purchase_order_id' => (int) $validated['purchase_order_id'],
        ]);

        return redirect()->route('deliveries.create', ['purchase_order_id' => $validated['purchase_order_id']]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'purchase_order_id' => ['required', 'integer', 'exists:purchase_orders,id'],
            'delivery_number' => ['required', 'string', 'max:100'],
            'delivery_date' => ['required', 'date'],
            'shipped_quantity' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
            'document' => ['nullable', 'file', 'max:10240'],
        ]);

        $userId = $request->user()?->id;

        $delivery = DB::transaction(function () use ($request, $validated, $userId) {
            $delivery = Delivery::create([
                ...collect($validated)->except('document')->all(),
                'created_by' => $userId,
            ]);

            $delivery->purchaseOrder()->update(['status' => 'dikirim']);

            $this->attachDocumentIfAny($request, $delivery);

            return $delivery;
        });

        return redirect()
            ->route('deliveries.show', $delivery)
            ->with('success', 'Data pengiriman berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Delivery $delivery)
    {
        $delivery->load(['purchaseOrder', 'invoice', 'documentArchives']);

        return view('deliveries.show', [
            'delivery' => $delivery,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Delivery $delivery)
    {
        $purchaseOrders = PurchaseOrder::query()->orderByDesc('id')->get();

        return view('deliveries.edit', [
            'delivery' => $delivery,
            'purchaseOrders' => $purchaseOrders,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Delivery $delivery)
    {
        $validated = $request->validate([
            'purchase_order_id' => ['required', 'integer', 'exists:purchase_orders,id'],
            'delivery_number' => ['required', 'string', 'max:100'],
            'delivery_date' => ['required', 'date'],
            'shipped_quantity' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
            'document' => ['nullable', 'file', 'max:10240'],
        ]);

        DB::transaction(function () use ($request, $delivery, $validated) {
            $delivery->update(collect($validated)->except('document')->all());
            $this->attachDocumentIfAny($request, $delivery);
        });

        return redirect()
            ->route('deliveries.show', $delivery)
            ->with('success', 'Data pengiriman berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Delivery $delivery)
    {
        $delivery->delete();

        return redirect()->route('deliveries.index');
    }

    private function attachDocumentIfAny(Request $request, Delivery $delivery): void
    {
        $disk = 'public';

        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $path = $file->store('archives/delivery', $disk);

            $delivery->documentArchives()->create([
                'kind' => 'delivery',
                'original_name' => $file->getClientOriginalName(),
                'disk' => $disk,
                'path' => $path,
                'mime_type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
                'uploaded_by' => $request->user()?->id,
            ]);

            return;
        }

        $pending = $request->session()->pull('delivery_pending_document');

        if (! is_array($pending) || empty($pending['path']) || empty($pending['disk'])) {
            return;
        }

        $pendingDisk = $pending['disk'];
        $pendingPath = $pending['path'];
        $finalPath = 'archives/delivery/'.$delivery->delivery_number.'-'.basename($pendingPath);

        if (Storage::disk($pendingDisk)->exists($pendingPath)) {
            Storage::disk($pendingDisk)->move($pendingPath, $finalPath);
        }

        $delivery->documentArchives()->create([
            'kind' => 'delivery',
            'original_name' => $pending['original_name'] ?? basename($finalPath),
            'disk' => $pendingDisk,
            'path' => $finalPath,
            'mime_type' => $pending['mime_type'] ?? null,
            'size' => $pending['size'] ?? null,
            'uploaded_by' => $request->user()?->id,
        ]);
    }
}
