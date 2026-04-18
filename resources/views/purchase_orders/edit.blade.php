<x-app-layout>
    @section('title', 'Edit Purchase Order')

    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center w-100">
            <h5 class="mb-0 fw-bold">Edit PO: <span class="text-primary">{{ $purchaseOrder->po_number }}</span></h5>
            <a class="btn btn-outline-secondary btn-sm shadow-sm" href="{{ route('purchase-orders.index') }}">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </x-slot>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('purchase-orders.update', $purchaseOrder) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">No PO <span class="text-danger">*</span></label>
                        <input class="form-control @error('po_number') is-invalid @enderror" name="po_number" value="{{ old('po_number', $purchaseOrder->po_number) }}" required>
                        @error('po_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Tanggal PO <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('po_date') is-invalid @enderror" name="po_date" value="{{ old('po_date', $purchaseOrder->po_date?->format('Y-m-d')) }}" required>
                        @error('po_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                            @foreach(['diproses','dikirim','ditagih','selesai'] as $s)
                                <option value="{{ $s }}" @if(old('status', $purchaseOrder->status) === $s) selected @endif>{{ strtoupper($s) }}</option>
                            @endforeach
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Customer <span class="text-danger">*</span></label>
                        <input class="form-control @error('customer_name') is-invalid @enderror" name="customer_name" value="{{ old('customer_name', $purchaseOrder->customer_name) }}" required>
                        @error('customer_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nama Produk <span class="text-danger">*</span></label>
                        <input class="form-control @error('product_name') is-invalid @enderror" name="product_name" value="{{ old('product_name', $purchaseOrder->product_name) }}" required>
                        @error('product_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Qty <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('quantity') is-invalid @enderror" name="quantity" value="{{ old('quantity', $purchaseOrder->quantity) }}" min="0" required>
                        @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Unit</label>
                        <input class="form-control @error('unit') is-invalid @enderror" name="unit" value="{{ old('unit', $purchaseOrder->unit) }}">
                        @error('unit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Harga Satuan</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">Rp</span>
                            <input type="number" step="0.01" class="form-control @error('unit_price') is-invalid @enderror" name="unit_price" value="{{ old('unit_price', $purchaseOrder->unit_price) }}" min="0">
                        </div>
                        @error('unit_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Catatan</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" name="notes" rows="3">{{ old('notes', $purchaseOrder->notes) }}</textarea>
                        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Tambah Dokumen (Opsional)</label>
                        <input type="file" class="form-control @error('document') is-invalid @enderror" name="document">
                        <div class="form-text">Biarkan kosong jika tidak ingin menambah/mengganti dokumen.</div>
                        @error('document')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mt-5 d-flex justify-content-between">
                    <div>
                        @if($purchaseOrder->deliveries->isEmpty())
                            <button type="button" class="btn btn-outline-danger shadow-sm" onclick="if(confirm('Hapus PO ini?')) document.getElementById('delete-po-form').submit();">
                                <i class="bi bi-trash me-1"></i> Hapus PO
                            </button>
                        @endif
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('purchase-orders.index') }}" class="btn btn-light px-4">Batal</a>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">Simpan Perubahan</button>
                    </div>
                </div>
            </form>

            <form id="delete-po-form" method="POST" action="{{ route('purchase-orders.destroy', $purchaseOrder) }}" class="d-none">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
</x-app-layout>
