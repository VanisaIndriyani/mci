<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit Invoice: {{ $invoice->invoice_number }}</h5>
            <a class="btn btn-outline-secondary btn-sm" href="{{ route('invoices.show', $invoice) }}">Kembali</a>
        </div>
    </x-slot>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('invoices.update', $invoice) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Surat Jalan</label>
                        <select class="form-select @error('delivery_id') is-invalid @enderror" name="delivery_id" required>
                            @foreach($deliveries as $delivery)
                                <option value="{{ $delivery->id }}" @if((int) old('delivery_id', $invoice->delivery_id) === $delivery->id) selected @endif>
                                    {{ $delivery->delivery_number }} • PO {{ $delivery->purchaseOrder?->po_number }} • {{ $delivery->purchaseOrder?->customer_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('delivery_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">No Invoice</label>
                        <input class="form-control @error('invoice_number') is-invalid @enderror" name="invoice_number" value="{{ old('invoice_number', $invoice->invoice_number) }}" required>
                        @error('invoice_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" class="form-control @error('invoice_date') is-invalid @enderror" name="invoice_date" value="{{ old('invoice_date', $invoice->invoice_date?->format('Y-m-d')) }}" required>
                        @error('invoice_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Amount</label>
                        <input type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror" name="amount" value="{{ old('amount', $invoice->amount) }}" min="0">
                        @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                            @foreach(['issued','paid'] as $s)
                                <option value="{{ $s }}" @if(old('status', $invoice->status) === $s) selected @endif>{{ strtoupper($s) }}</option>
                            @endforeach
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label">Catatan</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" name="notes" rows="3">{{ old('notes', $invoice->notes) }}</textarea>
                        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label">Tambah Dokumen (opsional)</label>
                        <input type="file" class="form-control @error('document') is-invalid @enderror" name="document">
                        @error('document')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>

            <form class="mt-3" method="POST" action="{{ route('invoices.destroy', $invoice) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Hapus Invoice ini?')">Hapus Invoice</button>
            </form>
        </div>
    </div>
</x-app-layout>
