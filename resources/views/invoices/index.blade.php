<x-app-layout>
    @section('title', 'Data Invoice')

    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center w-100">
            <h5 class="mb-0 fw-bold">Data Penagihan (Invoice)</h5>
            <div class="d-flex gap-2">
                @if(auth()->user()->isAdmin())
                    <button type="button" class="btn btn-primary-subtle text-primary fw-bold px-3 py-2 rounded-3 border-0" data-bs-toggle="modal" data-bs-target="#createInvoiceModal">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Tagihan
                    </button>
                    <button type="button" class="btn btn-info-subtle text-info fw-bold px-3 py-2 rounded-3 border-0" data-bs-toggle="modal" data-bs-target="#ocrInvoiceModal">
                        <i class="bi bi-upc-scan me-1"></i> Upload Invoice (OCR)
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
            <form action="{{ route('invoices.index') }}" method="GET" class="row g-2">
                <div class="col-lg-4 col-md-12">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Cari No Invoice, No SJ, No PO, atau Customer..." value="{{ request('search') }}">
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
                        <option value="">All Status</option>
                        <option value="issued" @selected(request('status') === 'issued')>ISSUED</option>
                        <option value="paid" @selected(request('status') === 'paid')>PAID</option>
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
                        <th class="ps-4 py-3 text-uppercase small fw-bold text-muted">No Invoice</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Tanggal</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Relasi</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted text-end">Amount</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted text-center">Status</th>
                        <th class="pe-4 py-3 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $invoice->invoice_number }}</div>
                                <div class="text-muted small">ID: #{{ $invoice->id }}</div>
                            </td>
                            <td>
                                <div class="text-dark">{{ $invoice->invoice_date?->format('d M Y') }}</div>
                            </td>
                            <td>
                                <div class="fw-medium text-dark">
                                    <span class="text-muted small">PO:</span> {{ $invoice->delivery?->purchaseOrder?->po_number }}
                                </div>
                                <div class="text-muted small"><span class="text-muted small">SJ:</span> {{ $invoice->delivery?->delivery_number }}</div>
                                <div class="text-primary small fw-semibold">{{ $invoice->delivery?->purchaseOrder?->customer_name }}</div>
                            </td>
                            <td class="text-end">
                                <div class="fw-bold text-dark">IDR {{ number_format($invoice->amount, 0, ',', '.') }}</div>
                            </td>
                            <td class="text-center">
                                @php
                                    $badgeClass = match($invoice->status) {
                                        'issued' => 'bg-primary-subtle text-primary',
                                        'paid' => 'bg-success-subtle text-success',
                                        default => 'bg-light text-dark'
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }} px-3 py-2 rounded-pill text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                                    {{ $invoice->status }}
                                </span>
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm btn-light text-primary rounded-circle shadow-sm" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(auth()->user()->isAdmin())
                                        <button type="button" class="btn btn-sm btn-light text-warning rounded-circle shadow-sm edit-invoice-btn"
                                            style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;"
                                            data-bs-toggle="modal" data-bs-target="#editInvoiceModal"
                                            data-update-url="{{ route('invoices.update', $invoice) }}"
                                            data-number="{{ $invoice->invoice_number }}"
                                            data-delivery-id="{{ $invoice->delivery_id }}"
                                            data-delivery-label="{{ $invoice->delivery?->delivery_number }} • PO {{ $invoice->delivery?->purchaseOrder?->po_number }} • {{ $invoice->delivery?->purchaseOrder?->customer_name }}"
                                            data-date="{{ $invoice->invoice_date?->format('Y-m-d') }}"
                                            data-amount="{{ $invoice->amount }}"
                                            data-status="{{ $invoice->status }}"
                                            data-notes="{{ $invoice->notes }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus Invoice ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light text-danger rounded-circle shadow-sm" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-receipt h1 d-block mb-3 opacity-25"></i>
                                    Belum ada data Invoice.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-top py-4 px-4">
            {{ $invoices->links() }}
        </div>
    </div>

    <!-- OCR Invoice Modal -->
    <div class="modal fade" id="ocrInvoiceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title fw-bold"><i class="bi bi-scan me-2"></i> Upload OCR Invoice</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('invoices.ocr.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Pilih Surat Jalan Relasi <span class="text-danger">*</span></label>
                            <select class="form-select @error('delivery_id') is-invalid @enderror" name="delivery_id" required>
                                <option value="">-- Pilih Surat Jalan --</option>
                                @foreach($deliveries as $delivery)
                                    <option value="{{ $delivery->id }}">
                                        {{ $delivery->delivery_number }} • PO {{ $delivery->purchaseOrder?->po_number }} • {{ $delivery->purchaseOrder?->customer_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('delivery_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-semibold">Dokumen Invoice <span class="text-danger">*</span></label>
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

    <!-- Edit Invoice Modal -->
    <div class="modal fade" id="editInvoiceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i> Edit Invoice</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editInvoiceForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        <input type="hidden" name="delivery_id" id="edit_delivery_id">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Relasi Surat Jalan</label>
                                <input type="text" class="form-control" id="edit_delivery_label" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">No Invoice <span class="text-danger">*</span></label>
                                <input type="text" name="invoice_number" id="edit_invoice_number" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tanggal Invoice <span class="text-danger">*</span></label>
                                <input type="date" name="invoice_date" id="edit_invoice_date" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Jumlah Tagihan (Amount)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">Rp</span>
                                    <input type="number" name="amount" id="edit_amount" class="form-control" min="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                <select name="status" id="edit_status" class="form-select" required>
                                    <option value="issued">ISSUED</option>
                                    <option value="paid">PAID</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Catatan</label>
                                <textarea name="notes" id="edit_notes" class="form-control" rows="2" placeholder="Informasi tambahan penagihan"></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Upload Dokumen Invoice</label>
                                <input type="file" name="document" class="form-control">
                                <div class="form-text">PDF, JPG, atau PNG (Maks 10MB)</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0 py-3">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const editForm = document.getElementById('editInvoiceForm');
            const editButtons = document.querySelectorAll('.edit-invoice-btn');

            const deliveryIdInput = document.getElementById('edit_delivery_id');
            const deliveryLabelInput = document.getElementById('edit_delivery_label');
            const numberInput = document.getElementById('edit_invoice_number');
            const dateInput = document.getElementById('edit_invoice_date');
            const amountInput = document.getElementById('edit_amount');
            const statusSelect = document.getElementById('edit_status');
            const notesInput = document.getElementById('edit_notes');

            editButtons.forEach(btn => {
                btn.addEventListener('click', function () {
                    editForm.action = this.dataset.updateUrl;
                    deliveryIdInput.value = this.dataset.deliveryId || '';
                    deliveryLabelInput.value = this.dataset.deliveryLabel || '';
                    numberInput.value = this.dataset.number || '';
                    dateInput.value = this.dataset.date || '';
                    amountInput.value = this.dataset.amount || 0;
                    statusSelect.value = this.dataset.status || 'issued';
                    notesInput.value = this.dataset.notes || '';
                });
            });
        });
    </script>

    <!-- Create Invoice Modal -->
    <div class="modal fade" id="createInvoiceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i> Tambah Invoice Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('invoices.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Pilih Surat Jalan Relasi <span class="text-danger">*</span></label>
                                <select class="form-select @error('delivery_id') is-invalid @enderror" name="delivery_id" required>
                                    <option value="">-- Pilih Surat Jalan --</option>
                                    @foreach($deliveries as $delivery)
                                        <option value="{{ $delivery->id }}" {{ old('delivery_id') == $delivery->id ? 'selected' : '' }}>
                                            {{ $delivery->delivery_number }} • PO {{ $delivery->purchaseOrder?->po_number }} • {{ $delivery->purchaseOrder?->customer_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('delivery_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">No Invoice <span class="text-danger">*</span></label>
                                <input type="text" name="invoice_number" class="form-control @error('invoice_number') is-invalid @enderror" value="{{ old('invoice_number') }}" placeholder="Contoh: INV/2025/001" required>
                                @error('invoice_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tanggal Invoice <span class="text-danger">*</span></label>
                                <input type="date" name="invoice_date" class="form-control @error('invoice_date') is-invalid @enderror" value="{{ old('invoice_date', date('Y-m-d')) }}" required>
                                @error('invoice_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Jumlah Tagihan (Amount) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">Rp</span>
                                    <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount', 0) }}" min="0" required>
                                </div>
                                @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="issued" {{ old('status') == 'issued' ? 'selected' : '' }}>ISSUED</option>
                                    <option value="paid" {{ old('status') == 'paid' ? 'selected' : '' }}>PAID</option>
                                </select>
                                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Catatan</label>
                                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="2" placeholder="Informasi tambahan penagihan">{{ old('notes') }}</textarea>
                                @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Upload Dokumen Invoice</label>
                                <input type="file" name="document" class="form-control @error('document') is-invalid @enderror">
                                <div class="form-text">PDF, JPG, atau PNG (Maks 10MB)</div>
                                @error('document') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0 py-3">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary px-4">Simpan Invoice</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
