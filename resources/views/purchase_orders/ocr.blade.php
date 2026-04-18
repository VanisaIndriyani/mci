<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Upload OCR PO</h5>
            <a class="btn btn-outline-secondary btn-sm" href="{{ route('purchase-orders.index') }}">Kembali</a>
        </div>
    </x-slot>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="alert alert-info">
                OCR versi awal ini memakai stub: sistem mencoba menebak No PO / Tanggal / Customer dari nama file.
                Contoh nama file: <span class="fw-semibold">po_PO123_2025-01-10_PT_ABC.pdf</span>
            </div>

            <form method="POST" action="{{ route('purchase-orders.ocr.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Dokumen PO</label>
                    <input type="file" class="form-control @error('document') is-invalid @enderror" name="document" required>
                    @error('document')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Proses OCR</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
