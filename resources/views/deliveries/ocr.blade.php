<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Upload OCR Surat Jalan</h5>
            <a class="btn btn-outline-secondary btn-sm" href="{{ route('deliveries.index') }}">Kembali</a>
        </div>
    </x-slot>

    @if($purchaseOrders->isEmpty())
        <div class="alert alert-warning">
            Belum ada data PO. Sistem tidak mengizinkan input Surat Jalan sebelum PO tersedia.
            <a href="{{ route('purchase-orders.create') }}" class="alert-link">Tambah PO</a>
        </div>
    @else
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="alert alert-info">
                    OCR versi awal ini memakai stub (tebakan dari nama file). Contoh: <span class="fw-semibold">sj_SJ001_2025-02-05.pdf</span>
                </div>

                <form method="POST" action="{{ route('deliveries.ocr.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Pilih PO</label>
                            <select class="form-select @error('purchase_order_id') is-invalid @enderror" name="purchase_order_id" required>
                                <option value="">Pilih PO</option>
                                @foreach($purchaseOrders as $po)
                                    <option value="{{ $po->id }}" @if((int) old('purchase_order_id', $selectedPurchaseOrderId) === $po->id) selected @endif>
                                        {{ $po->po_number }} • {{ $po->customer_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('purchase_order_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Dokumen Surat Jalan</label>
                            <input type="file" class="form-control @error('document') is-invalid @enderror" name="document" required>
                            @error('document')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Proses OCR</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</x-app-layout>
