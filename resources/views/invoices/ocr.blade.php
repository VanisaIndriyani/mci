<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Upload OCR Invoice</h5>
            <a class="btn btn-outline-secondary btn-sm" href="{{ route('invoices.index') }}">Kembali</a>
        </div>
    </x-slot>

    @if($deliveries->isEmpty())
        <div class="alert alert-warning">
            Belum ada Surat Jalan yang bisa ditagih. Sistem tidak mengizinkan input Invoice sebelum ada Pengiriman.
            <a href="{{ route('deliveries.create') }}" class="alert-link">Tambah Surat Jalan</a>
        </div>
    @else
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="alert alert-info">
                    OCR versi awal ini memakai stub (tebakan dari nama file). Contoh: <span class="fw-semibold">inv_INV999_2025-03-01.pdf</span>
                </div>

                <form method="POST" action="{{ route('invoices.ocr.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Pilih Surat Jalan</label>
                            <select class="form-select @error('delivery_id') is-invalid @enderror" name="delivery_id" required>
                                <option value="">Pilih Surat Jalan</option>
                                @foreach($deliveries as $delivery)
                                    <option value="{{ $delivery->id }}" @if((int) old('delivery_id', $selectedDeliveryId) === $delivery->id) selected @endif>
                                        {{ $delivery->delivery_number }} • PO {{ $delivery->purchaseOrder?->po_number }} • {{ $delivery->purchaseOrder?->customer_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('delivery_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Dokumen Invoice</label>
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
