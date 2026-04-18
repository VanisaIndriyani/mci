<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Invoice;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $year = 2025; // Sesuai permintaan: periode 2025
        
        $outstandingPoCount = PurchaseOrder::query()
            ->whereDoesntHave('deliveries')
            ->count();

        $inDeliveryCount = Delivery::query()
            ->whereDoesntHave('invoice')
            ->count();

        $completedPoCount = PurchaseOrder::query()
            ->where('status', 'selesai')
            ->count();
            
        $totalPoCount = PurchaseOrder::query()->count();
        $totalRevenue = Invoice::query()->where('status', 'paid')->sum('amount');

        // Specific statistics for 2025
        $poThisYear = PurchaseOrder::query()->whereYear('po_date', $year)->count();
        $revenueThisYear = Invoice::query()->whereYear('invoice_date', $year)->where('status', 'paid')->sum('amount');

        $recentOutstandingOrders = PurchaseOrder::query()
            ->whereDoesntHave('deliveries')
            ->latest()
            ->limit(5)
            ->get();
            
        // Real Activity Feed
        $activities = collect()
            ->concat(PurchaseOrder::latest()->limit(5)->get()->map(fn($item) => [
                'type' => 'po',
                'title' => 'PO #' . $item->po_number . ' diterbitkan',
                'customer' => $item->customer_name,
                'time' => $item->created_at,
                'status' => 'info',
                'url' => route('purchase-orders.show', $item)
            ]))
            ->concat(Delivery::latest()->limit(5)->get()->map(fn($item) => [
                'type' => 'delivery',
                'title' => 'SJ #' . $item->delivery_number . ' dikirim',
                'customer' => $item->purchaseOrder?->customer_name,
                'time' => $item->created_at,
                'status' => 'primary',
                'url' => route('deliveries.show', $item)
            ]))
            ->concat(Invoice::latest()->limit(5)->get()->map(fn($item) => [
                'type' => 'invoice',
                'title' => 'Tagihan #' . $item->invoice_number . ' dibuat',
                'customer' => $item->delivery?->purchaseOrder?->customer_name,
                'time' => $item->created_at,
                'status' => 'success',
                'url' => route('invoices.show', $item)
            ]))
            ->sortByDesc('time')
            ->take(8)
            ->values();

        // Monthly trends data using more efficient queries
        $poTrends = PurchaseOrder::selectRaw('MONTH(po_date) as month, COUNT(*) as count')
            ->whereYear('po_date', $year)
            ->groupBy('month')
            ->pluck('count', 'month')
            ->all();

        $deliveryTrends = Delivery::selectRaw('MONTH(delivery_date) as month, COUNT(*) as count')
            ->whereYear('delivery_date', $year)
            ->groupBy('month')
            ->pluck('count', 'month')
            ->all();

        $invoiceTrends = Invoice::selectRaw('MONTH(invoice_date) as month, COUNT(*) as count')
            ->whereYear('invoice_date', $year)
            ->groupBy('month')
            ->pluck('count', 'month')
            ->all();

        $trends = [
            'po' => array_map(fn($m) => $poTrends[$m] ?? 0, range(1, 12)),
            'delivery' => array_map(fn($m) => $deliveryTrends[$m] ?? 0, range(1, 12)),
            'invoice' => array_map(fn($m) => $invoiceTrends[$m] ?? 0, range(1, 12))
        ];

        return view('dashboards.admin', [
            'outstandingPo' => $outstandingPoCount,
            'inDelivery' => $inDeliveryCount,
            'completedPo' => $completedPoCount,
            'totalPo' => $totalPoCount,
            'totalRevenue' => $totalRevenue,
            'poThisYear' => $poThisYear,
            'revenueThisYear' => $revenueThisYear,
            'recentOutstandingOrders' => $recentOutstandingOrders,
            'trends' => $trends,
            'latestActivity' => $activities,
        ]);
    }
}
