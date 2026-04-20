<x-app-layout>
    @section('title', 'Data Surat Jalan')

    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center w-100">
            <h5 class="mb-0 fw-bold">Data Pengiriman (Surat Jalan)</h5>
            <div class="d-flex gap-2">
                @if(auth()->user()->isAdmin())
                    <button type="button" class="btn btn-primary-subtle text-primary fw-bold px-3 py-2 rounded-3 border-0" data-bs-toggle="modal" data-bs-target="#createDeliveryModal">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Pengiriman
                    </button>
                    <button type="button" class="btn btn-info-subtle text-info fw-bold px-3 py-2 rounded-3 border-0" data-bs-toggle="modal" data-bs-target="#ocrDeliveryModal">
                        <i class="bi bi-upc-scan me-1"></i> Upload SJ (OCR)
                    </button>
                @endif
            </div>
        </div>
    </x-slot>

    @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form action="{{ route('deliveries.index') }}" method="GET" class="row g-2">
                <div class="col-lg-4 col-md-12">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Cari No SJ, No PO, atau Customer..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-lg-2 col-md-6">
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-lg-2 col-md-6">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="belum_ditagih" @selected(request('status') === 'belum_ditagih')>NOT INVOICED</option>
                        <option value="sudah_ditagih" @selected(request('status') === 'sudah_ditagih')>INVOICED</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <button type="submit" class="btn btn-primary w-100 shadow-sm">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-uppercase small fw-bold text-muted">No Surat Jalan</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Tanggal</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">PO / Customer</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted text-center">Status</th>
                        <th class="pe-4 py-3 text-end"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($deliveries as $delivery)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $delivery->delivery_number }}</div>
                                <div class="text-muted small">ID: #{{ $delivery->id }}</div>
                            </td>
                            <td>
                                <div class="text-dark">{{ $delivery->delivery_date?->format('d M Y') }}</div>
                            </td>
                            <td>
                                <div class="fw-medium text-dark">
                                    <a href="{{ route('purchase-orders.show', $delivery->purchaseOrder) }}" class="text-decoration-none">
                                        {{ $delivery->purchaseOrder?->po_number }}
                                    </a>
                                </div>
                                <div class="text-muted small">{{ $delivery->purchaseOrder?->customer_name }}</div>
                            </td>
                            <td class="text-center">
                                @if($delivery->invoice)
                                    <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill text-uppercase" style="font-size: 0.7rem;">INVOICED</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill text-uppercase" style="font-size: 0.7rem;">NOT INVOICED</span>
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm rounded-circle shadow-sm" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-2">
                                        <li><a class="dropdown-item rounded-2" href="{{ route('deliveries.show', $delivery) }}"><i class="bi bi-eye me-2 text-primary"></i> Detail</a></li>
                                        @if(auth()->user()->isAdmin())
                                            <li><a class="dropdown-item rounded-2" href="{{ route('deliveries.edit', $delivery) }}"><i class="bi bi-pencil me-2 text-warning"></i> Edit</a></li>
                                            @if(!$delivery->invoice)
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('deliveries.destroy', $delivery) }}" method="POST" onsubmit="return confirm('Hapus Surat Jalan ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item rounded-2 text-danger"><i class="bi bi-trash me-2"></i> Hapus</button>
                                                    </form>
                                                </li>
                                            @endif
                                        @endif
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-truck h1 d-block mb-3 opacity-25"></i>
                                    Belum ada data Pengiriman.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-top py-4 px-4">
            {{ $deliveries->links() }}
        </div>
    </div>

    <!-- OCR Delivery Modal -->
    <div class="modal fade" id="ocrDeliveryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title fw-bold"><i class="bi bi-scan me-2"></i> Upload OCR Surat Jalan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('deliveries.ocr.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Pilih PO Relasi <span class="text-danger">*</span></label>
                            <select class="form-select @error('purchase_order_id') is-invalid @enderror" name="purchase_order_id" required>
                                <option value="">-- Pilih Purchase Order --</option>
                                @foreach($purchaseOrders as $po)
                                    <option value="{{ $po->id }}">{{ $po->po_number }} • {{ $po->customer_name }}</option>
                                @endforeach
                            </select>
                            @error('purchase_order_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-semibold">Dokumen Surat Jalan <span class="text-danger">*</span></label>
                            <input type="file" name="document" class="form-control @error('document') is-invalid @enderror" required>
                            @error('document') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="form-text mt-2">Format: PDF, JPG, PNG (Maks 10MB)</div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0 py-3">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary px-4">Proses OCR</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Create Delivery Modal -->
    <div class="modal fade" id="createDeliveryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i> Tambah Surat Jalan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('deliveries.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Pilih PO Relasi <span class="text-danger">*</span></label>
                                <select class="form-select @error('purchase_order_id') is-invalid @enderror" name="purchase_order_id" required>
                                    <option value="">-- Pilih Purchase Order --</option>
                                    @foreach($purchaseOrders as $po)
                                        <option value="{{ $po->id }}" {{ old('purchase_order_id') == $po->id ? 'selected' : '' }}>
                                            {{ $po->po_number }} • {{ $po->customer_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('purchase_order_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">No Surat Jalan <span class="text-danger">*</span></label>
                                <input type="text" name="delivery_number" class="form-control @error('delivery_number') is-invalid @enderror" value="{{ old('delivery_number') }}" placeholder="Contoh: SJ/2025/001" required>
                                @error('delivery_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tanggal Kirim <span class="text-danger">*</span></label>
                                <input type="date" name="delivery_date" class="form-control @error('delivery_date') is-invalid @enderror" value="{{ old('delivery_date', date('Y-m-d')) }}" required>
                                @error('delivery_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Catatan</label>
                                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="2" placeholder="Informasi tambahan pengiriman">{{ old('notes') }}</textarea>
                                @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Upload Dokumen Surat Jalan</label>
                                <input type="file" name="document" class="form-control @error('document') is-invalid @enderror">
                                <div class="form-text">PDF, JPG, atau PNG (Maks 10MB)</div>
                                @error('document') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0 py-3">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary px-4">Simpan Surat Jalan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
