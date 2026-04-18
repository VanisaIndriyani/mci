<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Tambah Surat Jalan</h5>
            <a class="btn btn-outline-secondary btn-sm" href="{{ route('deliveries.index') }}">Kembali</a>
        </div>
    </x-slot>

    @if($purchaseOrders->isEmpty())
        <div class="alert alert-warning">
            Belum ada data PO. Sistem tidak mengizinkan input Surat Jalan sebelum PO tersedia.
            <a href="{{ route('purchase-orders.create') }}" class="alert-link">Tambah PO</a>
        </div>
    @else
        @if($pendingDocument)
            <div class="alert alert-info">
                Dokumen OCR sudah diupload: <span class="fw-semibold">{{ $pendingDocument['original_name'] ?? 'dokumen' }}</span>. Silakan review data lalu simpan.
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST" action="{{ route('deliveries.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">PO (wajib)</label>
                            <select class="form-select @error('purchase_order_id') is-invalid @enderror" name="purchase_order_id" required>
                                <option value="">Pilih PO</option>
                                @foreach($purchaseOrders as $po)
                                    <option value="{{ $po->id }}" @if((int) old('purchase_order_id', $pendingDocument['purchase_order_id'] ?? $selectedPurchaseOrderId) === $po->id) selected @endif>
                                        {{ $po->po_number }} • {{ $po->customer_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('purchase_order_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">No Surat Jalan</label>
                            <input class="form-control @error('delivery_number') is-invalid @enderror" name="delivery_number" value="{{ old('delivery_number', $prefill['delivery_number'] ?? '') }}" required>
                            @error('delivery_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" class="form-control @error('delivery_date') is-invalid @enderror" name="delivery_date" value="{{ old('delivery_date', $prefill['delivery_date'] ?? '') }}" required>
                            @error('delivery_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Qty Kirim</label>
                            <input type="number" class="form-control @error('shipped_quantity') is-invalid @enderror" name="shipped_quantity" value="{{ old('shipped_quantity', $prefill['shipped_quantity'] ?? '') }}" min="0">
                            @error('shipped_quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Catatan</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" name="notes" rows="3">{{ old('notes', $prefill['notes'] ?? '') }}</textarea>
                            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Upload Dokumen (opsional)</label>
                            <input type="file" class="form-control @error('document') is-invalid @enderror" name="document">
                            @error('document')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text">Jika pakai OCR, upload dulu lewat menu “Upload OCR”.</div>
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Simpan Surat Jalan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</x-app-layout>
