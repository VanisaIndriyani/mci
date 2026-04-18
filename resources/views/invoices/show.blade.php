<x-app-layout>
    @section('title', 'Detail Invoice')

    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center w-100">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('invoices.index') }}" class="btn btn-light rounded-circle shadow-sm" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h5 class="mb-0 fw-bold">INV: {{ $invoice->invoice_number }}</h5>
                    <div class="text-muted small">Relasi SJ {{ $invoice->delivery?->delivery_number }} • {{ $invoice->delivery?->purchaseOrder?->customer_name }}</div>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a class="btn btn-outline-warning shadow-sm" href="{{ route('invoices.edit', $invoice) }}">
                    <i class="bi bi-pencil-square me-1"></i> Edit
                </a>
                <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus Invoice ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger shadow-sm">
                        <i class="bi bi-trash3 me-1"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="row g-4">
        <div class="col-lg-8">
            <!-- Payment Status Banner -->
            <div class="card border-0 shadow-sm mb-4 overflow-hidden">
                @php
                    $isPaid = $invoice->status === 'paid';
                    $bannerBg = $isPaid ? 'bg-success' : 'bg-primary';
                @endphp
                <div class="{{ $bannerBg }} p-4 text-white">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-uppercase small fw-bold opacity-75" style="letter-spacing: 1px;">Status Penagihan</div>
                            <div class="h2 fw-bold mb-0">{{ strtoupper($invoice->status) }}</div>
                        </div>
                        <div class="col-auto text-end">
                            <div class="text-uppercase small fw-bold opacity-75" style="letter-spacing: 1px;">Total Tagihan</div>
                            <div class="h2 fw-bold mb-0">IDR {{ number_format($invoice->amount, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="text-muted small mb-1 text-uppercase fw-bold" style="font-size: 0.65rem;">Customer</div>
                            <div class="fw-bold text-dark">{{ $invoice->delivery?->purchaseOrder?->customer_name }}</div>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <div class="text-muted small mb-1 text-uppercase fw-bold" style="font-size: 0.65rem;">Tanggal Invoice</div>
                            <div class="fw-bold text-dark">{{ $invoice->invoice_date?->format('d M Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Traceability Info -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-diagram-3 me-2 text-primary"></i> Penelusuran Data (Traceability)</h6>
                </div>
                <div class="card-body p-4 pt-0">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 d-flex align-items-center gap-3">
                                <div class="bg-primary-subtle text-primary rounded p-2">
                                    <i class="bi bi-file-earmark-text h4 mb-0"></i>
                                </div>
                                <div>
                                    <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.6rem;">Purchase Order</div>
                                    <a href="{{ route('purchase-orders.show', $invoice->delivery?->purchaseOrder) }}" class="fw-bold text-dark text-decoration-none">
                                        {{ $invoice->delivery?->purchaseOrder?->po_number }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 d-flex align-items-center gap-3">
                                <div class="bg-info-subtle text-info rounded p-2">
                                    <i class="bi bi-truck h4 mb-0"></i>
                                </div>
                                <div>
                                    <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.6rem;">Surat Jalan</div>
                                    <a href="{{ route('deliveries.show', $invoice->delivery) }}" class="fw-bold text-dark text-decoration-none">
                                        {{ $invoice->delivery?->delivery_number }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if($invoice->notes)
                        <div class="mt-4">
                            <h6 class="fw-bold small text-muted text-uppercase mb-2">Catatan Penagihan</h6>
                            <div class="p-3 bg-light rounded-3 text-dark small italic">
                                "{{ $invoice->notes }}"
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Documents -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-archive-fill me-2 text-primary"></i> Berkas Digital Invoice</h6>
                </div>
                <div class="card-body p-4 pt-0">
                    @if($invoice->documentArchives->isEmpty())
                        <div class="text-center py-4 bg-light rounded-3 border-dashed border-2 border-muted">
                            <i class="bi bi-file-earmark-arrow-up h2 d-block mb-2 text-muted opacity-50"></i>
                            <div class="small text-muted">Belum ada dokumen diunggah.</div>
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($invoice->documentArchives as $doc)
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
</x-app-layout>
