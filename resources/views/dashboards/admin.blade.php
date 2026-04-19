<x-app-layout>
    @section('title', 'Admin Dashboard')

    <!-- Top Filters & Period -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex gap-2 overflow-auto pb-1" style="max-width: 70%;">
            <select class="form-select shadow-sm border-0 bg-white fw-bold text-primary" style="width: 100px;">
                <option>2025</option>
            </select>
            <div class="vr mx-2 opacity-10"></div>
            <button class="btn btn-white shadow-sm border-0 text-nowrap px-3 rounded-pill small fw-semibold">
                <i class="bi bi-clock-history text-warning me-1"></i> Outstanding
            </button>
            <button class="btn btn-white shadow-sm border-0 text-nowrap px-3 rounded-pill small fw-semibold">
                <i class="bi bi-truck text-primary me-1"></i> In Delivery
            </button>
            <button class="btn btn-white shadow-sm border-0 text-nowrap px-3 rounded-pill small fw-semibold">
                <i class="bi bi-check-circle text-success me-1"></i> Completed
            </button>
        </div>
        <div class="text-end d-none d-md-block">
            <div class="text-muted small fw-medium">Hari ini</div>
            <div class="fw-bold text-dark">{{ now()->translatedFormat('d F Y') }}</div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm overflow-hidden bg-primary text-white">
                <div class="card-body p-4 position-relative">
                    <div class="text-white-50 small fw-bold text-uppercase mb-1" style="font-size: 0.65rem; letter-spacing: 1px;">Revenue (Paid) 2025</div>
                    <div class="h3 fw-bold mb-0">Rp {{ number_format($revenueThisYear, 0, ',', '.') }}</div>
                    <div class="text-white-50 small mt-2 fw-medium">Total: Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
                    <i class="bi bi-wallet2 position-absolute end-0 bottom-0 mb-n2 me-n2 opacity-25" style="font-size: 4rem;"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm border-start border-warning border-4">
                <div class="card-body p-4 position-relative">
                    <div class="text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.65rem; letter-spacing: 1px;">Outstanding PO</div>
                    <div class="h3 fw-bold text-dark mb-0">{{ $outstandingPo }}</div>
                    <div class="text-warning small mt-2 fw-bold">Menunggu Pengiriman</div>
                    <i class="bi bi-clock-history position-absolute end-0 bottom-0 mb-n2 me-n2 opacity-10 text-warning" style="font-size: 4rem;"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm border-start border-info border-4">
                <div class="card-body p-4 position-relative">
                    <div class="text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.65rem; letter-spacing: 1px;">In Delivery</div>
                    <div class="h3 fw-bold text-dark mb-0">{{ $inDelivery }}</div>
                    <div class="text-info small mt-2 fw-bold">Menunggu Penagihan</div>
                    <i class="bi bi-truck position-absolute end-0 bottom-0 mb-n2 me-n2 opacity-10 text-info" style="font-size: 4rem;"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm border-start border-success border-4">
                <div class="card-body p-4 position-relative">
                    <div class="text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.65rem; letter-spacing: 1px;">Completed PO</div>
                    <div class="h3 fw-bold text-dark mb-0">{{ $completedPo }}</div>
                    <div class="text-success small mt-2 fw-bold">Selesai / Terbayar</div>
                    <i class="bi bi-check-circle position-absolute end-0 bottom-0 mb-n2 me-n2 opacity-10 text-success" style="font-size: 4rem;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions & Search -->
    <div class="row g-4 mb-4">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm bg-white">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-lightning-charge-fill text-warning me-2"></i>Aksi Cepat</h6>
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('purchase-orders.index') }}" class="btn btn-primary-subtle text-primary fw-bold px-3 py-2 rounded-3 border-0">
                                    <i class="bi bi-file-earmark-plus me-1"></i> Purchase Order
                                </a>
                                <a href="{{ route('deliveries.index') }}" class="btn btn-info-subtle text-info fw-bold px-3 py-2 rounded-3 border-0">
                                    <i class="bi bi-truck me-1"></i> Surat Jalan
                                </a>
                                <a href="{{ route('invoices.index') }}" class="btn btn-success-subtle text-success fw-bold px-3 py-2 rounded-3 border-0">
                                    <i class="bi bi-receipt me-1"></i> Invoice
                                </a>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-search text-primary me-2"></i>Pencarian Global</h6>
                            <form action="{{ route('archives.index') }}" method="GET">
                                <div class="input-group bg-light rounded-pill p-1 border">
                                    <span class="input-group-text bg-transparent border-0 px-3"><i class="bi bi-search text-muted"></i></span>
                                    <input type="text" name="search" class="form-control bg-transparent border-0 shadow-none" placeholder="Cari nomor dokumen, customer, atau produk di arsip...">
                                    <button class="btn btn-primary rounded-pill px-4 fw-bold" type="submit">Cari Data</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Section -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-4 d-flex justify-content-between align-items-center border-0 px-4">
            <div>
                <h6 class="mb-1 fw-bold text-dark">Analisis Alur Bisnis (2025)</h6>
                <p class="text-muted small mb-0">Statistik perbandingan bulanan PO, Pengiriman, dan Penagihan</p>
            </div>
            <div class="text-end">
                <span class="badge bg-primary rounded-pill px-3 py-2">Tahun 2025</span>
            </div>
        </div>
        <div class="card-body px-4 pb-4">
            <div style="height: 350px;">
                <canvas id="adminPoChart"></canvas>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Recent Outstanding Orders -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-4 border-0 px-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-dark">Antrian PO Outstanding</h6>
                        <a href="{{ route('purchase-orders.index') }}" class="btn btn-link btn-sm text-decoration-none p-0 fw-bold text-primary">Lihat Semua <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr class="small text-uppercase text-muted fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">
                                <th class="ps-4 py-3">No PO / Customer</th>
                                <th class="py-3">Produk</th>
                                <th class="py-3 text-end">Amount</th>
                                <th class="pe-4 py-3 text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOutstandingOrders as $po)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark mb-1">{{ $po->po_number }}</div>
                                        <div class="text-primary small fw-semibold" style="font-size: 0.75rem;">{{ $po->customer_name }}</div>
                                    </td>
                                    <td>
                                        <div class="text-dark small text-truncate" style="max-width: 150px;">{{ $po->product_name }}</div>
                                        <div class="text-muted small" style="font-size: 0.7rem;">{{ $po->po_date->format('d M Y') }}</div>
                                    </td>
                                    <td class="text-end">
                                        <div class="fw-bold text-dark small">Rp {{ number_format($po->total_amount, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <a href="{{ route('purchase-orders.show', $po) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3" style="font-size: 0.7rem; font-weight: 600;">
                                            Proses <i class="bi bi-chevron-right ms-1"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted small">
                                        <div class="py-3">
                                            <i class="bi bi-check2-circle d-block h1 mb-3 text-success opacity-50"></i>
                                            <div class="fw-bold text-dark">Semua PO Sudah Terproses!</div>
                                            <p class="mb-0">Tidak ada antrian PO yang belum memiliki surat jalan.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Latest Activity Feed -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-4 border-0 px-4">
                    <h6 class="mb-0 fw-bold text-dark">Aktivitas Sistem Terkini</h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($latestActivity as $activity)
                            <div class="list-group-item px-4 py-3 border-0 border-bottom">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="rounded-circle bg-{{ $activity['status'] }}-subtle text-{{ $activity['status'] }} d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px; flex-shrink: 0;">
                                        @if($activity['type'] === 'po')
                                            <i class="bi bi-file-earmark-plus-fill"></i>
                                        @elseif($activity['type'] === 'delivery')
                                            <i class="bi bi-truck-flatbed"></i>
                                        @else
                                            <i class="bi bi-receipt-cutoff"></i>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <div class="fw-bold text-dark small">{{ $activity['title'] }}</div>
                                            <span class="badge bg-light text-muted fw-normal" style="font-size: 0.6rem;">{{ $activity['time']->diffForHumans() }}</span>
                                        </div>
                                        <div class="text-muted text-truncate mb-2" style="font-size: 0.75rem;">Customer: <span class="fw-semibold text-dark">{{ $activity['customer'] }}</span></div>
                                        <a href="{{ $activity['url'] }}" class="btn btn-sm btn-link p-0 text-primary text-decoration-none small fw-bold" style="font-size: 0.7rem;">
                                            Lihat Detail <i class="bi bi-arrow-right small"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-5 text-center text-muted small">
                                <i class="bi bi-activity h1 d-block mb-3 opacity-25"></i>
                                Belum ada aktivitas yang tercatat.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('adminPoChart');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                    datasets: [
                        {
                            label: 'Purchase Orders',
                            data: @json($trends['po']),
                            borderColor: '#0d6efd',
                            backgroundColor: 'rgba(13, 110, 253, 0.05)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointBackgroundColor: '#fff',
                            pointBorderColor: '#0d6efd',
                            pointBorderWidth: 2,
                        },
                        {
                            label: 'Surat Jalan',
                            data: @json($trends['delivery']),
                            borderColor: '#6c757d',
                            backgroundColor: 'transparent',
                            borderWidth: 2,
                            borderDash: [5, 5],
                            fill: false,
                            tension: 0.4,
                            pointRadius: 3,
                        },
                        {
                            label: 'Invoices',
                            data: @json($trends['invoice']),
                            borderColor: '#198754',
                            backgroundColor: 'transparent',
                            borderWidth: 2,
                            fill: false,
                            tension: 0.4,
                            pointRadius: 3,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: { 
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 20,
                                font: { size: 12, weight: '500' }
                            }
                        },
                        tooltip: {
                            backgroundColor: '#fff',
                            titleColor: '#000',
                            bodyColor: '#666',
                            borderColor: '#eee',
                            borderWidth: 1,
                            padding: 12,
                            displayColors: true,
                            boxPadding: 6
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f8f9fa', drawTicks: false },
                            ticks: { 
                                precision: 0,
                                color: '#999',
                                padding: 10
                            },
                            border: { display: false }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { 
                                color: '#999',
                                padding: 10
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
