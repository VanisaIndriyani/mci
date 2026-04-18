<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit Surat Jalan: {{ $delivery->delivery_number }}</h5>
            <a class="btn btn-outline-secondary btn-sm" href="{{ route('deliveries.show', $delivery) }}">Kembali</a>
        </div>
    </x-slot>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('deliveries.update', $delivery) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">PO</label>
                        <select class="form-select @error('purchase_order_id') is-invalid @enderror" name="purchase_order_id" required>
                            @foreach($purchaseOrders as $po)
                                <option value="{{ $po->id }}" @if((int) old('purchase_order_id', $delivery->purchase_order_id) === $po->id) selected @endif>
                                    {{ $po->po_number }} • {{ $po->customer_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('purchase_order_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">No Surat Jalan</label>
                        <input class="form-control @error('delivery_number') is-invalid @enderror" name="delivery_number" value="{{ old('delivery_number', $delivery->delivery_number) }}" required>
                        @error('delivery_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" class="form-control @error('delivery_date') is-invalid @enderror" name="delivery_date" value="{{ old('delivery_date', $delivery->delivery_date?->format('Y-m-d')) }}" required>
                        @error('delivery_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Qty Kirim</label>
                        <input type="number" class="form-control @error('shipped_quantity') is-invalid @enderror" name="shipped_quantity" value="{{ old('shipped_quantity', $delivery->shipped_quantity) }}" min="0">
                        @error('shipped_quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label">Catatan</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" name="notes" rows="3">{{ old('notes', $delivery->notes) }}</textarea>
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

            <form class="mt-3" method="POST" action="{{ route('deliveries.destroy', $delivery) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Hapus Surat Jalan ini?')">Hapus Surat Jalan</button>
            </form>
        </div>
    </div>
</x-app-layout>
