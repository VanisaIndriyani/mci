<!DOCTYPE html>
<html>
<head>
    <title>Insight Bisnis - {{ $company_name }}</title>
    <style>
        @page { margin: 1cm; }
        body { font-family: 'Helvetica', Arial, sans-serif; color: #2c3e50; line-height: 1.5; margin: 0; padding: 0; }
        
        /* Header Styling */
        .header { width: 100%; border-bottom: 2px solid #3498db; padding-bottom: 20px; margin-bottom: 30px; }
        .header-table { width: 100%; border-collapse: collapse; }
        .logo-cell { width: 80px; vertical-align: top; }
        .logo-img { width: 70px; height: auto; }
        .company-info { padding-left: 20px; vertical-align: middle; }
        .company-name { font-size: 20px; font-weight: bold; color: #2980b9; text-transform: uppercase; margin: 0; }
        .report-title { font-size: 16px; color: #7f8c8d; margin: 5px 0 0 0; font-weight: normal; }
        
        /* Meta Styling */
        .meta-info { width: 100%; margin-bottom: 30px; font-size: 11px; color: #95a5a6; }
        .meta-table { width: 100%; }
        
        /* Insight Card Styling */
        .insight-section { margin-bottom: 25px; page-break-inside: avoid; }
        .insight-card { 
            border: 1px solid #e1e8ed; 
            border-radius: 10px; 
            overflow: hidden;
            background-color: #fcfcfc;
        }
        .insight-header { 
            padding: 12px 20px; 
            font-weight: bold; 
            font-size: 14px;
            color: #fff;
        }
        .bg-danger { background-color: #e74c3c; }
        .bg-success { background-color: #27ae60; }
        .bg-warning { background-color: #f39c12; }
        .bg-primary { background-color: #3498db; }
        
        .insight-body { padding: 20px; }
        .insight-desc { font-size: 13px; color: #34495e; margin-bottom: 15px; font-weight: 500; }
        
        .solutions-box { 
            background-color: #fff; 
            border: 1px solid #eee; 
            border-radius: 6px; 
            padding: 15px;
        }
        .solutions-title { 
            font-size: 11px; 
            font-weight: bold; 
            text-transform: uppercase; 
            color: #7f8c8d; 
            margin-bottom: 10px;
            border-bottom: 1px solid #f1f1f1;
            padding-bottom: 5px;
        }
        .solution-item { 
            font-size: 12px; 
            color: #2c3e50; 
            margin-bottom: 6px; 
            padding-left: 15px;
            position: relative;
        }
        .bullet { color: #3498db; margin-right: 8px; font-weight: bold; }
        
        /* Footer Styling */
        .footer { 
            position: fixed; 
            bottom: -10px; 
            left: 0; 
            width: 100%; 
            text-align: center; 
            font-size: 9px; 
            color: #bdc3c7; 
            border-top: 1px solid #ecf0f1;
            padding-top: 10px;
        }
        
        .no-data { text-align: center; padding: 100px 0; color: #bdc3c7; font-style: italic; }
    </style>
</head>
<body>
    <!-- Header with Logo -->
    <div class="header">
        <table class="header-table">
            <tr>
                <td class="logo-cell">
                    @if($logo)
                        <img src="{{ $logo }}" class="logo-img" alt="Logo">
                    @endif
                </td>
                <td class="company-info">
                    <h1 class="company-name">{{ $company_name }}</h1>
                    <h2 class="report-title">Laporan Insight Strategis Bisnis</h2>
                </td>
            </tr>
        </table>
    </div>

    <!-- Meta Information -->
    <div class="meta-info">
        <table class="meta-table">
            <tr>
                <td align="left">Periode: <strong>{{ \Illuminate\Support\Carbon::create($year, $month, 1)->translatedFormat('F Y') }}</strong></td>
                <td align="right">Tanggal Cetak: <strong>{{ date('d F Y H:i') }}</strong></td>
            </tr>
        </table>
    </div>

    <!-- Insight Cards -->
    @forelse($allInsights as $insight)
        <div class="insight-section">
            <div class="insight-card">
                @php
                    $statusClass = 'bg-primary';
                    if(strpos(strtolower($insight['title']), 'penurunan') !== false) $statusClass = 'bg-danger';
                    if(strpos(strtolower($insight['title']), 'peningkatan') !== false) $statusClass = 'bg-success';
                    if(strpos(strtolower($insight['title']), 'overdue') !== false || strpos(strtolower($insight['title']), 'masalah') !== false) $statusClass = 'bg-warning';
                @endphp
                <div class="insight-header {{ $statusClass }}">
                    {{ strtoupper($insight['title']) }}
                </div>
                <div class="insight-body">
                    <div class="insight-desc">{{ $insight['description'] }}</div>
                    
                    <div class="solutions-box">
                        <div class="solutions-title">Rekomendasi Solusi Strategis:</div>
                        @foreach($insight['solutions'] as $sol)
                            <div class="solution-item">
                                <span class="bullet">&bull;</span> {{ $sol }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="no-data">
            <p>Tidak ada data insight strategis yang ditemukan untuk periode ini.</p>
        </div>
    @endforelse

    <!-- Footer -->
    <div class="footer">
        &copy; {{ date('Y') }} {{ $company_name }}. Dokumen ini dihasilkan secara otomatis oleh Sistem Monitoring Terintegrasi MCI. Halaman 1 dari 1
    </div>
</body>
</html>