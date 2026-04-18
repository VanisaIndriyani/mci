<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Tambah PO</h5>
            <a class="btn btn-outline-secondary btn-sm" href="{{ route('purchase-orders.index') }}">Kembali</a>
        </div>
    </x-slot>

    @if($pendingDocument)
        <div class="alert alert-info">
            Dokumen OCR sudah diupload: <span class="fw-semibold">{{ $pendingDocument['original_name'] ?? 'dokumen' }}</span>. Silakan review data lalu simpan.
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('purchase-orders.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">No PO</label>
                        <input class="form-control @error('po_number') is-invalid @enderror" name="po_number" value="{{ old('po_number', $prefill['po_number'] ?? '') }}" required>
                        @error('po_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tanggal PO</label>
                        <input type="date" class="form-control @error('po_date') is-invalid @enderror" name="po_date" value="{{ old('po_date', $prefill['po_date'] ?? '') }}" required>
                        @error('po_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Customer</label>
                        <input class="form-control @error('customer_name') is-invalid @enderror" name="customer_name" value="{{ old('customer_name', $prefill['customer_name'] ?? '') }}" required>
                        @error('customer_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nama Produk</label>
                        <input class="form-control @error('product_name') is-invalid @enderror" name="product_name" value="{{ old('product_name', $prefill['product_name'] ?? '') }}" required>
                        @error('product_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Qty</label>
                        <input type="number" class="form-control @error('quantity') is-invalid @enderror" name="quantity" value="{{ old('quantity', $prefill['quantity'] ?? 0) }}" min="0" required>
                        @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Unit</label>
                        <input class="form-control @error('unit') is-invalid @enderror" name="unit" value="{{ old('unit', $prefill['unit'] ?? '') }}">
                        @error('unit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Harga Satuan</label>
                        <input type="number" step="0.01" class="form-control @error('unit_price') is-invalid @enderror" name="unit_price" value="{{ old('unit_price', $prefill['unit_price'] ?? 0) }}" min="0">
                        @error('unit_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                    <button type="submit" class="btn btn-primary">Simpan PO</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
