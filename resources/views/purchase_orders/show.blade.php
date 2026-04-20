<x-app-layout>
    @section('title', 'Detail Purchase Order')

    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center w-100">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('purchase-orders.index') }}" class="btn btn-light rounded-circle shadow-sm" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h5 class="mb-0 fw-bold">PO: {{ $purchaseOrder->po_number }}</h5>
                    <div class="text-muted small">Dibuat oleh {{ $purchaseOrder->creator?->name ?? 'System' }} pada {{ $purchaseOrder->created_at->format('d M Y') }}</div>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a class="btn btn-outline-warning shadow-sm" href="{{ route('purchase-orders.edit', $purchaseOrder) }}" title="Edit PO">
                    <i class="bi bi-pencil-square me-1"></i> Edit
                </a>
                @if($purchaseOrder->deliveries->isEmpty())
                    <form action="{{ route('purchase-orders.destroy', $purchaseOrder) }}" method="POST" onsubmit="return confirm('Hapus PO ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger shadow-sm" title="Hapus PO">
                            <i class="bi bi-trash3 me-1"></i> Hapus
                        </button>
                    </form>
                @endif
                @if($purchaseOrder->status === 'ditagih')
                    <form action="{{ route('purchase-orders.complete', $purchaseOrder) }}" method="POST" onsubmit="return confirm('Tandai PO ini sebagai SELESAI?')">
                        @csrf
                        <button type="submit" class="btn btn-success shadow-sm">
                            <i class="bi bi-check-all me-1"></i> Selesaikan PO
                        </button>
                    </form>
                @endif
                <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#createDeliveryModalFromPo">
                    <i class="bi bi-truck me-1"></i> Tambah Surat Jalan
                </button>
            </div>
        </div>
    </x-slot>

    @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Main Info -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-info-circle-fill me-2 text-primary"></i> Informasi Utama PO</h6>
                </div>
                <div class="card-body p-4 pt-0">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3">
                                <div class="text-muted small text-uppercase fw-bold mb-1" style="font-size: 0.65rem;">Customer</div>
                                <div class="fw-bold text-dark h5 mb-0">{{ $purchaseOrder->customer_name }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3">
                                <div class="text-muted small text-uppercase fw-bold mb-1" style="font-size: 0.65rem;">Produk</div>
                                <div class="fw-bold text-dark h5 mb-0">{{ $purchaseOrder->product_name }}</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="text-muted small mb-1">Tanggal PO</div>
                            <div class="fw-semibold text-dark">{{ $purchaseOrder->po_date?->format('d M Y') }}</div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="text-muted small mb-1">Quantity</div>
                            <div class="fw-semibold text-dark">{{ number_format($purchaseOrder->quantity) }} {{ $purchaseOrder->unit }}</div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="text-muted small mb-1">Harga Satuan</div>
                            <div class="fw-semibold text-dark">IDR {{ number_format($purchaseOrder->unit_price, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="text-muted small mb-1">PO Status</div>
                            @php
                                $badgeClass = match($purchaseOrder->status) {
                                    'diproses' => 'bg-secondary-subtle text-secondary',
                                    'dikirim' => 'bg-primary-subtle text-primary',
                                    'ditagih' => 'bg-warning-subtle text-warning',
                                    'selesai' => 'bg-success-subtle text-success',
                                    default => 'bg-light text-dark'
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }} rounded-pill text-uppercase px-3" style="font-size: 0.7rem;">{{ $purchaseOrder->status }}</span>
                        </div>
                        <div class="col-12 mt-4">
                            <div class="p-3 border-start border-primary border-4 bg-primary-subtle rounded-end-3">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <div class="text-primary small fw-bold text-uppercase" style="font-size: 0.65rem;">Total Nilai PO</div>
                                        <div class="h3 fw-bold text-primary mb-0">IDR {{ number_format($purchaseOrder->total_amount, 0, ',', '.') }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-wallet2 h1 mb-0 text-primary opacity-25"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if($purchaseOrder->notes)
                        <div class="mt-4">
                            <h6 class="fw-bold small text-muted text-uppercase mb-2">Catatan Internal</h6>
                            <div class="p-3 bg-light rounded-3 text-dark small italic">
                                "{{ $purchaseOrder->notes }}"
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Deliveries Table -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom-0 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-truck me-2 text-primary"></i> Surat Jalan (Pengiriman)</h6>
                </div>
                <div class="card-body p-0">
                    @if($purchaseOrder->deliveries->isEmpty())
                        <div class="p-5 text-center">
                            <i class="bi bi-box-seam h1 d-block mb-3 text-muted opacity-25"></i>
                            <div class="text-muted">Belum ada pengiriman untuk PO ini.</div>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr class="small text-muted text-uppercase">
                                        <th class="ps-4">No SJ</th>
                                        <th>Tanggal Kirim</th>
                                        <th class="text-center">Penagihan</th>
                                        <th class="pe-4 text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($purchaseOrder->deliveries as $delivery)
                                        <tr>
                                            <td class="ps-4 fw-bold text-dark">{{ $delivery->delivery_number }}</td>
                                            <td>{{ $delivery->delivery_date?->format('d M Y') }}</td>
                                            <td class="text-center">
                                                @if($delivery->invoice)
                                                    <span class="badge bg-success-subtle text-success rounded-pill px-3" style="font-size: 0.65rem;">TERTAGIH</span>
                                                @else
                                        <span class="badge bg-danger-subtle text-danger rounded-pill px-3" style="font-size: 0.65rem;">NOT INVOICED</span>
                                                @endif
                                            </td>
                                            <td class="pe-4 text-end">
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('deliveries.show', $delivery) }}" class="btn btn-light border" title="Lihat Detail">
                                                        <i class="bi bi-eye text-primary"></i>
                                                    </a>
                                                    @if(!$delivery->invoice)
                                                        <a href="{{ route('invoices.create', ['delivery_id' => $delivery->id]) }}" class="btn btn-light border" title="Buat Invoice">
                                                            <i class="bi bi-receipt text-success"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar Info: Archives -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-archive-fill me-2 text-primary"></i> Berkas Digital PO</h6>
                </div>
                <div class="card-body p-4 pt-0">
                    @if($purchaseOrder->documentArchives->isEmpty())
                        <div class="text-center py-4 bg-light rounded-3 border-dashed border-2 border-muted">
                            <i class="bi bi-file-earmark-arrow-up h2 d-block mb-2 text-muted opacity-50"></i>
                            <div class="small text-muted">Belum ada dokumen diunggah.</div>
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($purchaseOrder->documentArchives as $doc)
                                <div class="list-group-item px-0 py-3 d-flex align-items-center gap-3 border-0 border-bottom">
                                    <div class="bg-light p-2 rounded text-primary">
                                        @if(Str::contains($doc->mime_type, 'pdf'))
                                            <i class="bi bi-file-earmark-pdf text-danger h4 mb-0"></i>
                                        @else
                                            <i class="bi bi-file-earmark-image text-success h4 mb-0"></i>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <div class="fw-bold text-dark text-truncate small" title="{{ $doc->original_name }}">{{ $doc->original_name }}</div>
                                        <div class="text-muted" style="font-size: 0.7rem;">{{ $doc->created_at?->format('d M Y, H:i') }}</div>
                                    </div>
                                    <a target="_blank" href="{{ \Illuminate\Support\Facades\Storage::disk($doc->disk)->url($doc->path) }}" class="btn btn-sm btn-light border rounded-circle">
                                        <i class="bi bi-box-arrow-up-right"></i>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Action / Summary -->
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3 small text-uppercase" style="letter-spacing: 1px;">Alur Bisnis PO</h6>
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px;">1</div>
                            <div class="fw-semibold small">Purchase Order (Input)</div>
                            <i class="bi bi-check-circle-fill ms-auto text-white"></i>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px;">2</div>
                            <div class="fw-semibold small">Surat Jalan (Pengiriman)</div>
                            @if(!$purchaseOrder->deliveries->isEmpty())
                                <i class="bi bi-check-circle-fill ms-auto text-white"></i>
                            @else
                                <i class="bi bi-circle ms-auto text-white opacity-50"></i>
                            @endif
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px;">3</div>
                            <div class="fw-semibold small">Invoice (Penagihan)</div>
                            @php $hasInvoice = $purchaseOrder->deliveries->whereNotNull('invoice')->count() > 0; @endphp
                            @if($hasInvoice)
                                <i class="bi bi-check-circle-fill ms-auto text-white"></i>
                            @else
                                <i class="bi bi-circle ms-auto text-white opacity-50"></i>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Delivery Modal Specific for this PO -->
    <div class="modal fade" id="createDeliveryModalFromPo" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title fw-bold"><i class="bi bi-truck me-2"></i> Buat Surat Jalan Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('deliveries.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="purchase_order_id" value="{{ $purchaseOrder->id }}">
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <div class="p-3 bg-light rounded-3 mb-2">
                                    <div class="text-muted small mb-1">Merespon PO</div>
                                    <div class="fw-bold text-dark">{{ $purchaseOrder->po_number }} • {{ $purchaseOrder->customer_name }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">No Surat Jalan <span class="text-danger">*</span></label>
                                <input type="text" name="delivery_number" class="form-control" placeholder="SJ/2025/..." required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tanggal Kirim <span class="text-danger">*</span></label>
                                <input type="date" name="delivery_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Catatan Pengiriman</label>
                                <textarea name="notes" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Upload Dokumen SJ</label>
                                <input type="file" name="document" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">Simpan Surat Jalan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
