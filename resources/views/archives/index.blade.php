<x-app-layout>
    @section('title', 'Arsip Dokumen Terintegrasi')

    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center w-100">
            <h5 class="mb-0 fw-bold text-dark">Arsip Dokumen Terintegrasi</h5>
            <div class="text-muted small fw-medium">
                <i class="bi bi-info-circle me-1"></i> Total {{ $archives->total() }} Dokumen
            </div>
        </div>
    </x-slot>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('archives.index') }}" class="row g-2">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Cari nama file..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="kind" class="form-select border-1">
                        <option value="">Semua Jenis Dokumen</option>
                        <option value="po" @if(request('kind') === 'po') selected @endif>Purchase Order (PO)</option>
                        <option value="delivery" @if(request('kind') === 'delivery') selected @endif>Surat Jalan (SJ)</option>
                        <option value="invoice" @if(request('kind') === 'invoice') selected @endif>Invoice (Penagihan)</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 shadow-sm">
                        <i class="bi bi-filter me-1"></i> Filter
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('archives.index') }}" class="btn btn-light w-100 border">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-uppercase small fw-bold text-muted">Nama Dokumen</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted text-center">Jenis</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Relasi Data</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Tgl Upload</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Uploader</th>
                        <th class="pe-4 py-3 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($archives as $doc)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-light p-2 rounded text-primary">
                                        @if(Str::contains($doc->mime_type, 'pdf'))
                                            <i class="bi bi-file-earmark-pdf-fill h4 mb-0 text-danger"></i>
                                        @elseif(Str::contains($doc->mime_type, 'image'))
                                            <i class="bi bi-file-earmark-image-fill h4 mb-0 text-success"></i>
                                        @else
                                            <i class="bi bi-file-earmark-fill h4 mb-0"></i>
                                        @endif
                                    </div>
                                    <div class="overflow-hidden">
                                        <div class="fw-bold text-dark text-truncate" style="max-width: 250px;" title="{{ $doc->original_name }}">
                                            {{ $doc->original_name }}
                                        </div>
                                        <div class="text-muted small">{{ number_format($doc->size / 1024, 1) }} KB • {{ strtoupper(explode('/', $doc->mime_type)[1] ?? 'FILE') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                @php
                                    $kindBadge = match($doc->kind) {
                                        'po' => 'bg-info-subtle text-info',
                                        'delivery' => 'bg-primary-subtle text-primary',
                                        'invoice' => 'bg-warning-subtle text-warning',
                                        default => 'bg-light text-dark'
                                    };
                                @endphp
                                <span class="badge {{ $kindBadge }} px-3 py-2 rounded-pill text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">
                                    {{ $doc->kind === 'po' ? 'Purchase Order' : ($doc->kind === 'delivery' ? 'Surat Jalan' : 'Invoice') }}
                                </span>
                            </td>
                            <td>
                                @if($doc->documentable)
                                    @php
                                        $route = match($doc->kind) {
                                            'po' => route('purchase-orders.show', $doc->documentable),
                                            'delivery' => route('deliveries.show', $doc->documentable),
                                            'invoice' => route('invoices.show', $doc->documentable),
                                            default => '#'
                                        };
                                        $label = match($doc->kind) {
                                            'po' => $doc->documentable->po_number,
                                            'delivery' => $doc->documentable->delivery_number,
                                            'invoice' => $doc->documentable->invoice_number,
                                            default => '-'
                                        };
                                    @endphp
                                    <a href="{{ $route }}" class="text-decoration-none fw-semibold">
                                        <i class="bi bi-link-45deg"></i> {{ $label }}
                                    </a>
                                @else
                                    <span class="text-muted small italic">Data dihapus</span>
                                @endif
                            </td>
                            <td>
                                <div class="text-dark small">{{ $doc->created_at?->translatedFormat('d M Y') }}</div>
                                <div class="text-muted small" style="font-size: 0.7rem;">{{ $doc->created_at?->format('H:i') }} WIB</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-secondary-subtle text-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 0.7rem;">
                                        {{ strtoupper(substr($doc->uploader?->name ?? 'S', 0, 1)) }}
                                    </div>
                                    <span class="small">{{ $doc->uploader?->name ?? 'System' }}</span>
                                </div>
                            </td>
                            <td class="pe-4 text-end">
                                <div class="btn-group btn-group-sm shadow-sm">
                                    <a class="btn btn-white border" target="_blank" href="{{ \Illuminate\Support\Facades\Storage::disk($doc->disk)->url($doc->path) }}" title="Buka Dokumen">
                                        <i class="bi bi-box-arrow-up-right text-primary"></i>
                                    </a>
                                    <a class="btn btn-white border" href="{{ \Illuminate\Support\Facades\Storage::disk($doc->disk)->url($doc->path) }}" download title="Download">
                                        <i class="bi bi-download text-success"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-folder2-open h1 d-block mb-3 opacity-25"></i>
                                    Belum ada dokumen dalam arsip.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-top py-4 px-4">
            {{ $archives->links() }}
        </div>
    </div>
</x-app-layout>
