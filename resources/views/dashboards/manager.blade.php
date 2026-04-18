<x-app-layout>
    @section('title', $isInsightPage ? 'Insight Bisnis' : 'Dashboard')

    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center w-100">
            <div>
                <h4 class="mb-0 fw-bold text-dark">{{ $isInsightPage ? 'Insight Bisnis' : 'Dashboard' }}</h4>
            </div>
            <form method="GET" action="{{ request()->fullUrl() }}" class="d-flex gap-2 align-items-center">
                @if($isInsightPage) <input type="hidden" name="insight" value="1"> @endif
                <select name="month" class="form-select border-0 shadow-sm bg-white fw-semibold text-muted rounded-3" style="min-width: 180px;" onchange="this.form.submit()">
                    <option value="">Semua Bulan ({{ $year }})</option>
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" @if($month === (int)$m) selected @endif>
                            {{ \Illuminate\Support\Carbon::create($year, $m, 1)->translatedFormat('F') }} ({{ $year }})
                        </option>
                    @endforeach
                </select>
                <div class="bg-white rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                    <i class="bi bi-person-circle text-muted fs-5"></i>
                </div>
            </form>
        </div>
    </x-slot>

    @if(!$isInsightPage)
        <!-- Dashboard Content (Previous Code) -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden text-white" style="background-color: #4a89dc;">
                    <div class="card-body p-4 d-flex align-items-center gap-3">
                        <div class="bg-white bg-opacity-25 rounded-3 p-3"><i class="bi bi-box-seam fs-3"></i></div>
                        <div>
                            <div class="small fw-medium opacity-75">Total PO Bulan Ini</div>
                            <div class="h2 fw-bold mb-0">{{ $totalPo }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden text-white" style="background-color: #f6bb42;">
                    <div class="card-body p-4 d-flex align-items-center gap-3">
                        <div class="bg-white bg-opacity-25 rounded-3 p-3"><i class="bi bi-truck fs-3"></i></div>
                        <div>
                            <div class="small fw-medium opacity-75">Total Pengiriman</div>
                            <div class="h2 fw-bold mb-0">{{ $totalDelivery }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden text-white" style="background-color: #8cc152;">
                    <div class="card-body p-4 d-flex align-items-center gap-3">
                        <div class="bg-white bg-opacity-25 rounded-3 p-3"><i class="bi bi-hourglass-split fs-3"></i></div>
                        <div>
                            <div class="small fw-medium opacity-75">Total Invoice</div>
                            <div class="h4 fw-bold mb-0 text-nowrap">IDR {{ number_format($totalRevenue/1000000, 1, ',', '.') }}M</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden text-white" style="background-color: #3bafda;">
                    <div class="card-body p-4 d-flex align-items-center gap-3">
                        <div class="bg-white bg-opacity-25 rounded-3 p-3"><i class="bi bi-file-earmark-break fs-3"></i></div>
                        <div>
                            <div class="small fw-medium opacity-75">Outstanding Order</div>
                            <div class="h2 fw-bold mb-0">{{ $outstandingPo }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-9">
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-header bg-white border-0 py-4 px-4"><h6 class="mb-0 fw-bold text-dark">Jumlah PO per Bulan</h6></div>
                            <div class="card-body px-4 pb-4"><div style="height: 250px;"><canvas id="poMonthlyChart"></canvas></div></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-header bg-white border-0 py-4 px-4"><h6 class="mb-0 fw-bold text-dark">Customer Terbanyak</h6></div>
                            <div class="card-body px-4 pb-4"><div style="height: 250px;"><canvas id="customerChart"></canvas></div></div>
                        </div>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 bg-white p-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-primary-subtle text-primary rounded-3 p-2"><i class="bi bi-file-earmark-check fs-4"></i></div>
                                <div><div class="text-muted small fw-bold">Total PO</div><div class="h4 fw-bold mb-0">{{ $totalPo }}</div></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 bg-white p-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-warning-subtle text-warning rounded-3 p-2"><i class="bi bi-file-earmark-spreadsheet fs-4"></i></div>
                                <div><div class="text-muted small fw-bold">Total Amount</div><div class="h4 fw-bold mb-0">{{ number_format($totalRevenue/1000, 0, ',', '.') }}K</div></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 bg-white p-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-success-subtle text-success rounded-3 p-2"><i class="bi bi-stack fs-4"></i></div>
                                <div><div class="text-muted small fw-bold">Total Bring order</div><div class="h4 fw-bold mb-0">{{ $outstandingPo }}</div></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4"><h6 class="mb-0 fw-bold text-dark">Monitoring</h6></div>
                    <div class="card-body px-4 pb-4">
                        <div class="d-flex flex-column gap-3">
                            <div class="d-flex align-items-center justify-content-between p-2">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 28px; height: 28px; font-size: 0.75rem;">1</div>
                                    <span class="small fw-semibold text-muted">PO Masih Diproses</span>
                                </div>
                                <span class="fw-bold text-dark">{{ $monitoring['diproses'] }}</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between p-2">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 28px; height: 28px; font-size: 0.75rem;">2</div>
                                    <span class="small fw-semibold text-muted">Sedang Dikirim</span>
                                </div>
                                <span class="fw-bold text-dark">{{ $monitoring['dikirim'] }}</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between p-2">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 28px; height: 28px; font-size: 0.75rem;">3</div>
                                    <span class="small fw-semibold text-muted">Sudah Ditagih</span>
                                </div>
                                <span class="fw-bold text-dark">{{ $monitoring['ditagih'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
                    <div class="card-header bg-white border-0 pt-4 px-4"><h6 class="mb-0 fw-bold text-dark"><i class="bi bi-chat-left-dots me-2"></i>Insight</h6></div>
                    <div class="card-body px-4 pb-4">
                        <div class="p-3 bg-light rounded-3 border-0">
                            @if($insight) <p class="small text-muted mb-3 lh-base">{{ $insight }}</p>
                            @else <p class="small text-muted mb-3 lh-base">Pilih bulan tertentu untuk melihat analisis perbandingan volume PO.</p>
                            @endif
                            @if($topCustomers->isNotEmpty())
                                <div class="mt-2 pt-2 border-top">
                                    <div class="text-muted small fw-bold text-uppercase mb-2" style="font-size: 0.6rem;">Customer Terbanyak Bulan Ini:</div>
                                    <div class="fw-bold text-primary small"><i class="bi bi-chevron-right small"></i> {{ $topCustomers->first()->customer_name }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Insight Bisnis Page Content -->
        <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
            <div class="card-body p-4 p-lg-5">
                <!-- Filter Bar (Matching Reference) -->
                <div class="row g-3 mb-4 pb-3 border-bottom align-items-center">
                    <div class="col-md-auto">
                        <div class="dropdown">
                            <button class="btn btn-light border-0 shadow-sm rounded-3 px-4 fw-bold text-muted dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                Periode: <span class="text-dark">Bulan Ini</span>
                            </button>
                            <ul class="dropdown-menu border-0 shadow-lg rounded-3">
                                <li><a class="dropdown-item fw-medium py-2" href="#">Bulan Ini</a></li>
                                <li><a class="dropdown-item fw-medium py-2" href="#">Kuartal Terakhir</a></li>
                                <li><a class="dropdown-item fw-medium py-2" href="#">Tahun Ini</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-auto">
                        <div class="dropdown">
                            <button class="btn btn-light border-0 shadow-sm rounded-3 px-4 fw-bold text-muted dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                Tipe: <span class="text-dark">Semua Tipe</span>
                            </button>
                            <ul class="dropdown-menu border-0 shadow-lg rounded-3">
                                <li><a class="dropdown-item fw-medium py-2" href="#">Semua Tipe</a></li>
                                <li><a class="dropdown-item fw-medium py-2" href="#">Peningkatan</a></li>
                                <li><a class="dropdown-item fw-medium py-2" href="#">Penurunan</a></li>
                                <li><a class="dropdown-item fw-medium py-2" href="#">Masalah</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-auto">
                        <div class="dropdown">
                            <button class="btn btn-light border-0 shadow-sm rounded-3 px-4 fw-bold text-muted dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                Urutan: <span class="text-dark">Terbaru</span>
                            </button>
                            <ul class="dropdown-menu border-0 shadow-lg rounded-3">
                                <li><a class="dropdown-item fw-medium py-2" href="#">Terbaru</a></li>
                                <li><a class="dropdown-item fw-medium py-2" href="#">Terlama</a></li>
                                <li><a class="dropdown-item fw-medium py-2" href="#">Prioritas Tinggi</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4 ms-auto">
                        <div class="input-group bg-light rounded-pill p-1 border">
                            <span class="input-group-text bg-transparent border-0 px-3"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" id="insight-search" class="form-control bg-transparent border-0 shadow-none small" placeholder="Search insights...">
                        </div>
                    </div>
                </div>

                <!-- Insight Tabs (Matching Reference) -->
                <div class="d-flex flex-wrap gap-3 align-items-center justify-content-between mb-5">
                    <div class="d-flex gap-2" id="insight-tabs">
                        <button class="btn btn-primary-subtle text-primary rounded-pill px-4 fw-bold border-0 filter-btn active" data-filter="all">Semua</button>
                        <button class="btn btn-light rounded-pill px-4 fw-bold text-muted border-0 shadow-sm filter-btn" data-filter="penurunan">Penurunan</button>
                        <button class="btn btn-light rounded-pill px-4 fw-bold text-muted border-0 shadow-sm filter-btn" data-filter="peningkatan"><i class="bi bi-graph-up-arrow text-success me-1"></i> Peningkatan</button>
                        <button class="btn btn-light rounded-pill px-4 fw-bold text-muted border-0 shadow-sm filter-btn" data-filter="masalah"><i class="bi bi-exclamation-triangle text-warning me-1"></i> Masalah</button>
                    </div>
                    <div class="text-muted fw-bold" style="font-size: 0.85rem;"><span id="insight-count">{{ $allInsights->count() }}</span> Insights</div>
                </div>

                <!-- Premium Insight Cards -->
                <div class="d-flex flex-column gap-4" id="insight-container">
                    @forelse($allInsights as $item)
                        <div class="card border-0 border-start border-{{ $item['status_color'] }} border-5 rounded-4 shadow-sm bg-white insight-card transition-all" data-type="{{ $item['type'] }}">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-start gap-4">
                                    <div class="bg-{{ $item['status_color'] }} bg-opacity-10 text-{{ $item['status_color'] }} rounded-4 d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px; flex-shrink: 0;">
                                        @if($item['type'] == 'penurunan') <i class="bi bi-graph-down-arrow fs-4"></i>
                                        @elseif($item['type'] == 'peningkatan') <i class="bi bi-graph-up-arrow fs-4"></i>
                                        @else <i class="bi bi-exclamation-triangle fs-4"></i> @endif
                                    </div>
                                    
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="mb-0 fw-bold text-{{ $item['status_color'] }} fs-5 lh-base pe-5">{{ $item['title'] }}</h6>
                                        </div>
                                        
                                        <div class="p-4 bg-light bg-opacity-50 rounded-4 border border-white">
                                            <div class="text-muted small fw-bold text-uppercase mb-3" style="letter-spacing: 1px; font-size: 0.65rem;">Solusi Strategis</div>
                                            <ul class="list-unstyled mb-0 d-flex flex-column gap-3">
                                                @foreach($item['solutions'] as $sol)
                                                    <li class="small text-dark d-flex align-items-start gap-3">
                                                        <div class="bg-{{ $item['status_color'] }} rounded-circle mt-1" style="width: 6px; height: 6px; flex-shrink: 0;"></div>
                                                        <span class="fw-medium lh-base">{{ $sol }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>

                                        <div class="d-flex justify-content-end mt-4">
                                            <a href="{{ route('manager.download-insight', ['month' => $month, 'year' => $year]) }}" class="btn btn-{{ $item['status_color'] == 'danger' ? 'warning' : ($item['status_color'] == 'warning' ? 'success' : 'primary') }} rounded-pill px-5 py-2 fw-bold small shadow hover-scale">
                                                <i class="bi {{ $item['action_label'] == 'Download PDF' ? 'bi-file-earmark-pdf' : 'bi-lightning-charge' }} me-2"></i>
                                                {{ $item['action_label'] }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <div class="bg-light d-inline-block p-4 rounded-circle mb-4">
                                <i class="bi bi-chat-left-dots h1 text-muted opacity-50"></i>
                            </div>
                            <p class="text-muted fw-bold fs-5">Tidak ada insight strategis untuk periode ini.</p>
                            <p class="text-muted small">Coba ubah filter atau pilih bulan lain untuk melihat analisis baru.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Premium Pagination -->
                <div class="mt-5 pt-4 d-flex justify-content-center">
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm mb-0 gap-2">
                            <li class="page-item disabled">
                                <a class="page-link rounded-circle d-flex align-items-center justify-content-center border-0 bg-light text-muted" style="width: 36px; height: 36px;" href="#"><i class="bi bi-chevron-left"></i></a>
                            </li>
                            <li class="page-item active">
                                <a class="page-link rounded-circle d-flex align-items-center justify-content-center border-0 shadow-sm" style="width: 36px; height: 36px;" href="#">1</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link rounded-circle d-flex align-items-center justify-content-center border-0 bg-light text-dark fw-bold" style="width: 36px; height: 36px;" href="#">2</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link rounded-circle d-flex align-items-center justify-content-center border-0 bg-light text-dark fw-bold" style="width: 36px; height: 36px;" href="#">3</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link rounded-circle d-flex align-items-center justify-content-center border-0 bg-light text-dark fw-bold" style="width: 36px; height: 36px;" href="#"><i class="bi bi-chevron-right"></i></a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>

        <style>
            .insight-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.08) !important; }
            .transition-all { transition: all 0.3s ease; }
            .hover-scale:hover { transform: scale(1.05); }
            .btn-primary-subtle { background-color: #e7f1ff; color: #0d6efd; }
            .insight-card.d-none { display: none !important; }
        </style>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const filterBtns = document.querySelectorAll('.filter-btn');
                const searchInput = document.getElementById('insight-search');
                const insightCards = document.querySelectorAll('.insight-card');
                const countSpan = document.getElementById('insight-count');

                function filterInsights() {
                    const activeFilter = document.querySelector('.filter-btn.active').dataset.filter;
                    const searchTerm = searchInput.value.toLowerCase();
                    let visibleCount = 0;

                    insightCards.forEach(card => {
                        const type = card.dataset.type;
                        const text = card.innerText.toLowerCase();
                        
                        const matchesFilter = activeFilter === 'all' || type === activeFilter;
                        const matchesSearch = text.includes(searchTerm);

                        if (matchesFilter && matchesSearch) {
                            card.classList.remove('d-none');
                            visibleCount++;
                        } else {
                            card.classList.add('d-none');
                        }
                    });

                    countSpan.innerText = visibleCount;
                }

                filterBtns.forEach(btn => {
                    btn.addEventListener('click', () => {
                        filterBtns.forEach(b => {
                            b.classList.remove('active', 'btn-primary-subtle', 'text-primary');
                            b.classList.add('btn-light', 'text-muted', 'shadow-sm');
                        });
                        
                        btn.classList.add('active', 'btn-primary-subtle', 'text-primary');
                        btn.classList.remove('btn-light', 'text-muted', 'shadow-sm');
                        
                        filterInsights();
                    });
                });

                searchInput.addEventListener('input', filterInsights);

                // Simulation for buttons
                document.querySelectorAll('.insight-card button').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        const label = e.target.innerText.trim();
                        Swal.fire({
                            title: 'Fitur Simulasi',
                            text: 'Menjalankan aksi: ' + label,
                            icon: 'info',
                            timer: 2000,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });
                    });
                });
            });
        </script>
    @endif

    @if(!$isInsightPage)
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const poCtx = document.getElementById('poMonthlyChart');
                new Chart(poCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                        datasets: [{ label: 'Jumlah PO', data: @json($trends['po']), backgroundColor: '#4a89dc', borderRadius: 5, barThickness: 15 }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, grid: { color: '#f0f0f0', drawTicks: false }, ticks: { precision: 0, color: '#aaa', font: { size: 10 } }, border: { display: false } },
                            x: { grid: { display: false }, ticks: { color: '#aaa', font: { size: 10 } } }
                        }
                    }
                });

                const customerCtx = document.getElementById('customerChart');
                const customerData = @json($topCustomers);
                new Chart(customerCtx, {
                    type: 'bar',
                    data: {
                        labels: customerData.map(c => c.customer_name.substring(0, 15) + (c.customer_name.length > 15 ? '...' : '')),
                        datasets: [{ label: 'Total PO', data: customerData.map(c => c.total), backgroundColor: '#8cc152', borderRadius: 5, barThickness: 25 }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, grid: { color: '#f0f0f0', drawTicks: false }, ticks: { precision: 0, color: '#aaa', font: { size: 10 } }, border: { display: false } },
                            x: { grid: { display: false }, ticks: { color: '#aaa', font: { size: 10 } } }
                        }
                    }
                });
            });
        </script>
    @endif
</x-app-layout>