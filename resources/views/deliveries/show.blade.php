<x-app-layout>
    @section('title', 'Detail Surat Jalan')

    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center w-100">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('deliveries.index') }}" class="btn btn-light rounded-circle shadow-sm" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h5 class="mb-0 fw-bold">SJ: {{ $delivery->delivery_number }}</h5>
                    <div class="text-muted small">Merespon PO {{ $delivery->purchaseOrder?->po_number }} • {{ $delivery->purchaseOrder?->customer_name }}</div>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a class="btn btn-outline-warning shadow-sm" href="{{ route('deliveries.edit', $delivery) }}">
                    <i class="bi bi-pencil-square me-1"></i> Edit
                </a>
                @if(!$delivery->invoice)
                    <form action="{{ route('deliveries.destroy', $delivery) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus Surat Jalan ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger shadow-sm">
                            <i class="bi bi-trash3 me-1"></i> Hapus
                        </button>
                    </form>
                    <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#createInvoiceModalFromSj">
                        <i class="bi bi-receipt me-1"></i> Buat Invoice
                    </button>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-truck me-2 text-primary"></i> Informasi Pengiriman</h6>
                </div>
                <div class="card-body p-4 pt-0">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <div class="text-muted small text-uppercase fw-bold mb-1" style="font-size: 0.65rem;">Customer</div>
                                <div class="fw-bold text-dark h5 mb-0">{{ $delivery->purchaseOrder?->customer_name }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <div class="text-muted small text-uppercase fw-bold mb-1" style="font-size: 0.65rem;">Item / Produk</div>
                                <div class="fw-bold text-dark h5 mb-0">{{ $delivery->purchaseOrder?->product_name }}</div>
                            </div>
                        </div>
                        <div class="col-md-4 col-6">
                            <div class="text-muted small mb-1">Tanggal Kirim</div>
                            <div class="fw-semibold text-dark">{{ $delivery->delivery_date?->format('d M Y') }}</div>
                        </div>
                        <div class="col-md-4 col-6">
                            <div class="text-muted small mb-1">Relasi PO</div>
                            <a href="{{ route('purchase-orders.show', $delivery->purchaseOrder) }}" class="fw-bold text-primary text-decoration-none">
                                <i class="bi bi-link-45deg"></i> {{ $delivery->purchaseOrder?->po_number }}
                            </a>
                        </div>
                        <div class="col-md-4 col-6">
                            <div class="text-muted small mb-1">Status Penagihan</div>
                            @if($delivery->invoice)
                                <span class="badge bg-success-subtle text-success rounded-pill px-3" style="font-size: 0.65rem;">TERTAGIH</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger rounded-pill px-3" style="font-size: 0.65rem;">BELUM DITAGIH</span>
                            @endif
                        </div>
                    </div>
                    @if($delivery->notes)
                        <div class="mt-4">
                            <h6 class="fw-bold small text-muted text-uppercase mb-2">Catatan Pengiriman</h6>
                            <div class="p-3 bg-light rounded-3 text-dark small italic">
                                "{{ $delivery->notes }}"
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Invoice Link -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-receipt me-2 text-primary"></i> Data Penagihan (Invoice)</h6>
                </div>
                <div class="card-body p-4 pt-0">
                    @if(!$delivery->invoice)
                        <div class="text-center py-4 bg-light rounded-3">
                            <i class="bi bi-receipt h1 d-block mb-3 text-muted opacity-25"></i>
                            <div class="text-muted small mb-3">Belum ada invoice yang diterbitkan untuk pengiriman ini.</div>
                            <button class="btn btn-primary btn-sm px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#createInvoiceModalFromSj">
                                <i class="bi bi-plus-lg me-1"></i> Buat Invoice Sekarang
                            </button>
                        </div>
                    @else
                        <div class="p-3 bg-success-subtle border-start border-success border-4 rounded-end-3 d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-success small fw-bold text-uppercase" style="font-size: 0.65rem;">Nomor Invoice</div>
                                <div class="h5 fw-bold text-success mb-0">{{ $delivery->invoice->invoice_number }}</div>
                                <div class="text-muted small mt-1">Tanggal Tagihan: {{ $delivery->invoice->invoice_date?->format('d M Y') }}</div>
                            </div>
                            <a href="{{ route('invoices.show', $delivery->invoice) }}" class="btn btn-success shadow-sm">
                                <i class="bi bi-eye-fill"></i> Lihat Invoice
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Documents -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-archive-fill me-2 text-primary"></i> Berkas Digital SJ</h6>
                </div>
                <div class="card-body p-4 pt-0">
                    @if($delivery->documentArchives->isEmpty())
                        <div class="text-center py-4 bg-light rounded-3 border-dashed border-2 border-muted">
                            <i class="bi bi-file-earmark-arrow-up h2 d-block mb-2 text-muted opacity-50"></i>
                            <div class="small text-muted">Belum ada dokumen diunggah.</div>
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($delivery->documentArchives as $doc)
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
        </div>
    </div>

    <!-- Create Invoice Modal Specific for this SJ -->
    <div class="modal fade" id="createInvoiceModalFromSj" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title fw-bold"><i class="bi bi-receipt me-2"></i> Buat Invoice Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('invoices.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="delivery_id" value="{{ $delivery->id }}">
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <div class="p-3 bg-light rounded-3 mb-2">
                                    <div class="text-muted small mb-1">Merespon Surat Jalan</div>
                                    <div class="fw-bold text-dark">{{ $delivery->delivery_number }} • PO {{ $delivery->purchaseOrder?->po_number }}</div>
                                    <div class="text-primary small fw-semibold">{{ $delivery->purchaseOrder?->customer_name }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">No Invoice <span class="text-danger">*</span></label>
                                <input type="text" name="invoice_number" class="form-control" placeholder="INV/2025/..." required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tanggal Invoice <span class="text-danger">*</span></label>
                                <input type="date" name="invoice_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Jumlah Tagihan <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">Rp</span>
                                    <input type="number" name="amount" class="form-control" value="{{ $delivery->purchaseOrder?->total_amount }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="issued">ISSUED</option>
                                    <option value="paid">PAID</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Catatan Penagihan</label>
                                <textarea name="notes" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Upload Dokumen Invoice</label>
                                <input type="file" name="document" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">Simpan Invoice</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
