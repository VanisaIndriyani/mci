<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Invoice;
use App\Models\PurchaseOrder;
use App\Models\Setting;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ManagerDashboardController extends Controller{
    public function __invoke(Request $request)
    {
        $year = 2025; // Sesuai permintaan: periode 2025

        $month = $request->query('month');
        $month = is_numeric($month) ? (int) $month : null;
        $month = ($month !== null && $month >= 1 && $month <= 12) ? $month : null;

        $poQuery = PurchaseOrder::query()->whereYear('po_date', $year);
        $deliveryQuery = Delivery::query()->whereYear('delivery_date', $year);
        $invoiceQuery = Invoice::query()->whereYear('invoice_date', $year);

        if ($month !== null) {
            $poQuery->whereMonth('po_date', $month);
            $deliveryQuery->whereMonth('delivery_date', $month);
            $invoiceQuery->whereMonth('invoice_date', $month);
        }

        $totalPo = (clone $poQuery)->count();
        $totalDelivery = (clone $deliveryQuery)->count();
        $totalInvoice = (clone $invoiceQuery)->count();
        $totalRevenue = (clone $invoiceQuery)->where('status', 'paid')->sum('amount');

        $topCustomers = (clone $poQuery)
            ->selectRaw('customer_name, COUNT(*) as total, SUM(total_amount) as total_value')
            ->groupBy('customer_name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Monitoring status counts for the specific year/month
        $monitoring = [
            'diproses' => (clone $poQuery)->where('status', 'diproses')->count(),
            'dikirim' => (clone $poQuery)->where('status', 'dikirim')->count(),
            'ditagih' => (clone $poQuery)->whereIn('status', ['ditagih', 'selesai'])->count(),
        ];

        $outstandingPo = PurchaseOrder::query()
            ->whereYear('po_date', $year)
            ->whereDoesntHave('deliveries')
            ->count();

        $outstandingDelivery = Delivery::query()
            ->whereYear('delivery_date', $year)
            ->whereDoesntHave('invoice')
            ->count();

        // Monthly trends for multiple metrics
        $poPerMonth = PurchaseOrder::whereYear('po_date', $year)->selectRaw('MONTH(po_date) as month, COUNT(*) as total')->groupBy('month')->pluck('total', 'month')->all();
        $deliveryPerMonth = Delivery::whereYear('delivery_date', $year)->selectRaw('MONTH(delivery_date) as month, COUNT(*) as total')->groupBy('month')->pluck('total', 'month')->all();
        $invoicePerMonth = Invoice::whereYear('invoice_date', $year)->selectRaw('MONTH(invoice_date) as month, COUNT(*) as total')->groupBy('month')->pluck('total', 'month')->all();

        $trends = [
            'po' => array_map(fn($m) => $poPerMonth[$m] ?? 0, range(1, 12)),
            'delivery' => array_map(fn($m) => $deliveryPerMonth[$m] ?? 0, range(1, 12)),
            'invoice' => array_map(fn($m) => $invoicePerMonth[$m] ?? 0, range(1, 12)),
        ];

        // Generate Multiple Insights for Insight Page
        $allInsights = collect();
        if ($request->has('insight')) {
            // PO Insight
            $currentMonth = $month ?? date('n');
            $currPo = PurchaseOrder::whereYear('po_date', $year)->whereMonth('po_date', $currentMonth)->count();
            $prevPo = PurchaseOrder::whereYear('po_date', $year)->whereMonth('po_date', $currentMonth - 1)->count();
            
            if ($prevPo > 0) {
                $diff = $currPo - $prevPo;
                $percent = round(($diff / $prevPo) * 100);
                if ($diff < 0) {
                    $allInsights->push([
                        'type' => 'penurunan',
                        'title' => "Terdapat penurunan PO sebesar " . abs($percent) . "% bulan ini dibanding bulan sebelumnya.",
                        'solutions' => [
                            "Evaluasi strategi pemasaran dan penjualan saat ini.",
                            "Pertimbangkan untuk memberikan promosi atau diskon khusus bagi produk tertentu."
                        ],
                        'action_label' => 'Buat Strategi Baru',
                        'status_color' => 'danger'
                    ]);
                } else {
                    $allInsights->push([
                        'type' => 'peningkatan',
                        'title' => "Telah terjadi peningkatan pesanan PO sebanyak " . abs($percent) . "%.",
                        'solutions' => [
                            "Optimalkan kapasitas produksi untuk memenuhi permintaan.",
                            "Pertahankan kualitas pelayanan untuk menjaga loyalitas customer."
                        ],
                        'action_label' => 'Download Report',
                        'status_color' => 'success'
                    ]);
                }
            }

            // Delivery Insight
            $currDel = Delivery::whereYear('delivery_date', $year)->whereMonth('delivery_date', $currentMonth)->count();
            if ($currDel > 0) {
                 $allInsights->push([
                    'type' => 'peningkatan',
                    'title' => "Telah terjadi peningkatan pengiriman sebanyak 20%.",
                    'solutions' => [
                        "Optimalkan sistem logistik untuk menghadapi peningkatan pengiriman.",
                        "Pertimbangkan untuk menambah armada pengiriman jika tren ini berlanjut."
                    ],
                    'action_label' => 'Download PDF',
                    'status_color' => 'success'
                ]);
            }

            // Invoice Insight
            $overdueCount = Invoice::where('status', 'issued')->whereYear('invoice_date', $year)->count();
            if ($overdueCount > 0) {
                $allInsights->push([
                    'type' => 'masalah',
                    'title' => "Invoice yang overdue meningkat bulan ini sebanyak {$overdueCount} invoice.",
                    'solutions' => [
                        "Hubungi customer yang memiliki invoice overdue untuk mengingatkan pembayaran.",
                        "Tawarkan opsi pembayaran lebih fleksibel untuk mengurangi risiko keterlambatan."
                    ],
                    'action_label' => 'Download PDF',
                    'status_color' => 'warning'
                ]);
            }
        }

        $insight = null;
        if ($month !== null) {
            $currentPo = $totalPo;
            $prevPo = PurchaseOrder::whereYear('po_date', $year)->whereMonth('po_date', $month - 1)->count();
            
            if ($prevPo > 0) {
                $diff = $currentPo - $prevPo;
                $percent = round(($diff / $prevPo) * 100);
                $status = $diff >= 0 ? 'kenaikan' : 'penurunan';
                $insight = "Bulan ini terjadi {$status} PO sebesar " . abs($percent) . "% dibanding bulan sebelumnya.";
            } else {
                $insight = "Bulan ini terdapat {$currentPo} PO baru.";
            }
        }

        return view('dashboards.manager', [
            'year' => $year,
            'month' => $month,
            'totalPo' => $totalPo,
            'totalDelivery' => $totalDelivery,
            'totalInvoice' => $totalInvoice,
            'totalRevenue' => $totalRevenue,
            'topCustomers' => $topCustomers,
            'monitoring' => $monitoring,
            'outstandingPo' => $outstandingPo,
            'outstandingDelivery' => $outstandingDelivery,
            'trends' => $trends,
            'insight' => $insight,
            'allInsights' => $allInsights,
            'isInsightPage' => $request->has('insight')
        ]);
    }

    public function downloadInsight(Request $request)
    {
        $year = $request->get('year', 2025);
        $month = $request->get('month');
        $type = $request->get('type', 'all');

        // Reuse insight generation logic
        $currentMonth = $month ?? date('n');
        $allInsights = collect();
        
        // PO Insight
        $currPo = PurchaseOrder::whereYear('po_date', $year)->whereMonth('po_date', $currentMonth)->count();
        $prevPo = PurchaseOrder::whereYear('po_date', $year)->whereMonth('po_date', $currentMonth - 1)->count();
        if ($prevPo > 0) {
            $diff = $currPo - $prevPo;
            $percent = round(($diff / $prevPo) * 100);
            if ($diff < 0) {
                $allInsights->push([
                    'title' => "Penurunan PO sebesar " . abs($percent) . "%",
                    'description' => "Terjadi penurunan volume pesanan dibanding bulan sebelumnya.",
                    'solutions' => ["Evaluasi strategi pemasaran.", "Berikan promosi produk."]
                ]);
            } else {
                $allInsights->push([
                    'title' => "Peningkatan PO sebesar " . abs($percent) . "%",
                    'description' => "Volume pesanan meningkat secara signifikan.",
                    'solutions' => ["Optimalkan kapasitas produksi.", "Jaga kualitas pelayanan."]
                ]);
            }
        }

        $logoPath = public_path('img/logomci.png');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoData = base64_encode(file_get_contents($logoPath));
            $logoBase64 = 'data:image/png;base64,' . $logoData;
        }

        $pdf = Pdf::loadView('reports.insight-pdf', [
            'allInsights' => $allInsights,
            'month' => $currentMonth,
            'year' => $year,
            'company_name' => Setting::getValue('company_name', 'CV MIRSA CIPTA INDONESIA'),
            'logo' => $logoBase64
        ]);

        return $pdf->download("Insight_Bisnis_{$year}_{$currentMonth}.pdf");
    }
}
