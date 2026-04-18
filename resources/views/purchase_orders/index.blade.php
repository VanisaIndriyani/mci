<x-app-layout>
    @section('title', 'Data Purchase Order')

    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center w-100">
            <h5 class="mb-0 fw-bold">Data PO (Purchase Order)</h5>
            <div class="d-flex gap-2">
                @if(auth()->user()->isAdmin())
                    <button type="button" class="btn btn-primary-subtle text-primary fw-bold px-3 py-2 rounded-3 border-0" data-bs-toggle="modal" data-bs-target="#createPoModal">
                        <i class="bi bi-plus-lg me-1"></i> Tambah PO
                    </button>
                    <button type="button" class="btn btn-info-subtle text-info fw-bold px-3 py-2 rounded-3 border-0" data-bs-toggle="modal" data-bs-target="#ocrPoModal">
                        <i class="bi bi-upc-scan me-1"></i> Upload PO (OCR)
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
            <form action="{{ route('purchase-orders.index') }}" method="GET" class="row g-2">
                <div class="col-md-10">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Cari No PO, Customer, atau Produk..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 shadow-sm">Cari</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm overflow-hidden rounded-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light border-bottom">
                    <tr>
                        <th class="ps-4 py-4 text-uppercase small fw-bold text-muted" style="letter-spacing: 0.5px;">Nomor PO / ID</th>
                        <th class="py-4 text-uppercase small fw-bold text-muted" style="letter-spacing: 0.5px;">Tanggal & Customer</th>
                        <th class="py-4 text-uppercase small fw-bold text-muted" style="letter-spacing: 0.5px;">Produk & Kuantitas</th>
                        <th class="py-4 text-uppercase small fw-bold text-muted text-end" style="letter-spacing: 0.5px;">Total Amount</th>
                        <th class="py-4 text-uppercase small fw-bold text-muted text-center" style="letter-spacing: 0.5px;">Status Alur</th>
                        <th class="pe-4 py-4 text-end" style="letter-spacing: 0.5px;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($purchaseOrders as $po)
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="fw-bold text-dark mb-1" style="font-size: 0.95rem;">{{ $po->po_number }}</div>
                                <div class="text-muted small fw-medium">ID: #{{ $po->id }}</div>
                            </td>
                            <td class="py-3">
                                <div class="text-dark fw-bold mb-1" style="font-size: 0.9rem;">{{ $po->po_date?->format('d M Y') }}</div>
                                <div class="text-primary small fw-semibold text-uppercase" style="font-size: 0.75rem;">{{ $po->customer_name }}</div>
                            </td>
                            <td class="py-3">
                                <div class="text-dark fw-semibold mb-1 text-truncate" style="max-width: 220px;">{{ $po->product_name }}</div>
                                <div class="text-muted small">
                                    <span class="badge bg-light text-dark border fw-normal">{{ number_format($po->quantity) }} {{ $po->unit }}</span>
                                </div>
                            </td>
                            <td class="text-end py-3">
                                <div class="fw-bold text-dark" style="font-size: 1rem;">IDR {{ number_format($po->total_amount, 0, ',', '.') }}</div>
                                <div class="text-muted small" style="font-size: 0.7rem;">@ Rp {{ number_format($po->unit_price, 0, ',', '.') }}</div>
                            </td>
                            <td class="text-center py-3">
                                @php
                                    $statusConfig = match($po->status) {
                                        'diproses' => ['class' => 'bg-danger text-white', 'label' => 'OUTSTANDING'],
                                        'dikirim' => ['class' => 'bg-warning text-dark', 'label' => 'ON DELIVERY'],
                                        'ditagih' => ['class' => 'bg-info text-white', 'label' => 'INVOICED'],
                                        'selesai' => ['class' => 'bg-success text-white', 'label' => 'COMPLETED'],
                                        default => ['class' => 'bg-light text-dark', 'label' => strtoupper($po->status)]
                                    };
                                @endphp
                                <span class="badge {{ $statusConfig['class'] }} px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.65rem; font-weight: 700; letter-spacing: 0.5px;">
                                    {{ $statusConfig['label'] }}
                                </span>
                            </td>
                            <td class="pe-4 text-end py-3">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('purchase-orders.show', $po) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold shadow-sm" style="font-size: 0.75rem;">
                                        <i class="bi bi-eye me-1"></i> Lihat
                                    </a>
                                    @if(auth()->user()->isAdmin())
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light rounded-circle shadow-sm" type="button" data-bs-toggle="dropdown" style="width: 32px; height: 32px;">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-2 rounded-3" style="min-width: 150px;">
                                                <li>
                                                    <a href="{{ route('purchase-orders.edit', $po) }}" class="dropdown-item py-2 rounded-2 small fw-bold text-warning">
                                                        <i class="bi bi-pencil-square me-2"></i> Edit Data
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider opacity-50"></li>
                                                <li>
                                                    <form action="{{ route('purchase-orders.destroy', $po) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus PO ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item py-2 rounded-2 small fw-bold text-danger">
                                                            <i class="bi bi-trash-fill me-2"></i> Hapus PO
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-inbox h1 d-block mb-3 opacity-25"></i>
                                    Belum ada data Purchase Order.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-top py-4 px-4">
            {{ $purchaseOrders->links() }}
        </div>
    </div>

    <!-- OCR PO Modal -->
    <div class="modal fade" id="ocrPoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title fw-bold"><i class="bi bi-scan me-2"></i> Upload OCR Purchase Order</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('purchase-orders.ocr.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="alert alert-info border-0 shadow-sm small">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            Sistem akan mencoba mengekstrak No PO, Tanggal, dan Customer dari nama file atau konten dokumen.
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Pilih Dokumen PO</label>
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

    <!-- Create PO Modal -->
    <div class="modal fade" id="createPoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i> Tambah Purchase Order</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('purchase-orders.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4">
                        @if(session('po_pending_document'))
                            <div class="alert alert-info border-0 shadow-sm small mb-4">
                                <i class="bi bi-file-earmark-check-fill me-2"></i>
                                Dokumen OCR sudah diupload: <strong>{{ session('po_pending_document')['original_name'] }}</strong>. Silakan review data lalu simpan.
                            </div>
                        @endif
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">No PO <span class="text-danger">*</span></label>
                                <input type="text" name="po_number" class="form-control @error('po_number') is-invalid @enderror" value="{{ old('po_number', $prefill['po_number'] ?? '') }}" placeholder="Contoh: PO/2025/001" required>
                                @error('po_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tanggal PO <span class="text-danger">*</span></label>
                                <input type="date" name="po_date" class="form-control @error('po_date') is-invalid @enderror" value="{{ old('po_date', $prefill['po_date'] ?? date('Y-m-d')) }}" required>
                                @error('po_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Customer <span class="text-danger">*</span></label>
                                <input type="text" name="customer_name" class="form-control @error('customer_name') is-invalid @enderror" value="{{ old('customer_name', $prefill['customer_name'] ?? '') }}" placeholder="Nama PT atau Individu" required>
                                @error('customer_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Nama Produk <span class="text-danger">*</span></label>
                                <input type="text" name="product_name" class="form-control @error('product_name') is-invalid @enderror" value="{{ old('product_name', $prefill['product_name'] ?? '') }}" placeholder="Contoh: Gear Mesin A" required>
                                @error('product_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Qty <span class="text-danger">*</span></label>
                                <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity', $prefill['quantity'] ?? 0) }}" min="0" required>
                                @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Unit</label>
                                <input type="text" name="unit" class="form-control @error('unit') is-invalid @enderror" value="{{ old('unit', $prefill['unit'] ?? 'Pcs') }}" placeholder="Pcs, Kg, m, dll">
                                @error('unit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Harga Satuan</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">Rp</span>
                                    <input type="number" step="0.01" name="unit_price" class="form-control @error('unit_price') is-invalid @enderror" value="{{ old('unit_price', $prefill['unit_price'] ?? 0) }}" min="0">
                                </div>
                                @error('unit_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Catatan</label>
                                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="2" placeholder="Informasi tambahan (opsional)">{{ old('notes', $prefill['notes'] ?? '') }}</textarea>
                                @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Upload Dokumen (Opsional)</label>
                                <input type="file" name="document" class="form-control @error('document') is-invalid @enderror">
                                <div class="form-text">File PDF, JPG, atau PNG (Maks 10MB)</div>
                                @error('document') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0 py-3">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary px-4">Simpan PO</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if(session('open_modal'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var myModal = new bootstrap.Modal(document.getElementById('{{ session('open_modal') }}'));
                myModal.show();
            });
        </script>
    @endif
</x-app-layout>
