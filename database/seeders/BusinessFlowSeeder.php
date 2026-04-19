<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\PurchaseOrder;
use App\Models\Delivery;
use App\Models\Invoice;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class BusinessFlowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);

        $year = 2025;
        $targetCount = 30;

        if (PurchaseOrder::query()->whereYear('po_date', $year)->count() >= 20) {
            return;
        }

        $customers = [
            'PT. Toyota Motor Manufacturing',
            'PT. Astra Honda Motor',
            'PT. Denso Indonesia',
            'PT. Mitsubishi Motors',
            'PT. Suzuki Indomobil',
            'PT. Komatsu Indonesia',
            'PT. Daihatsu Motor',
            'PT. Panasonic Manufacturing',
            'PT. Schneider Electric',
            'PT. Unilever Indonesia',
        ];

        $products = [
            'Jig Welding Frame',
            'Special Purpose Machine Alpha',
            'Mechanical Joint Type A',
            'Conveyor System v2',
            'Bracket Support Engine',
            'Base Plate Precision',
            'Clamping Tool Set',
            'Robot Arm Extension',
            'Cooling System Unit',
            'Press Tool Module',
            'Shaft Drive Gear',
            'Sensor Housing Case',
            'Pallet Handler',
            'Casting Mold B1',
            'Pneumatic Control Box',
            'Fixture Assembly Line',
            'Inspection Gauge Set',
            'Hydraulic Press Adapter',
        ];

        $existingMaxId = (int) (PurchaseOrder::query()->max('id') ?? 0);

        for ($i = 0; $i < $targetCount; $i++) {
            $seq = $existingMaxId + $i + 1;
            $month = (($i % 12) + 1);
            $day = min(25, 2 + (($i * 3) % 24));

            $poDate = Carbon::create($year, $month, $day);
            $qty = 5 + (($i * 7) % 60);
            $unitPrice = 750000 + (($i * 175000) % 2250000);
            $totalAmount = $qty * $unitPrice;

            $stageRoll = $i % 10;
            $stage = match (true) {
                $stageRoll <= 2 => 'po_only',
                $stageRoll <= 4 => 'delivered',
                $stageRoll <= 7 => 'invoiced_issued',
                default => 'invoiced_paid',
            };

            $poStatus = match ($stage) {
                'po_only' => 'diproses',
                'delivered' => 'dikirim',
                'invoiced_issued' => 'ditagih',
                'invoiced_paid' => 'selesai',
            };

            $po = PurchaseOrder::create([
                'po_number' => 'PO/MCI/'.$year.'/' . str_pad($seq, 4, '0', STR_PAD_LEFT),
                'po_date' => $poDate,
                'customer_name' => $customers[$seq % count($customers)],
                'product_name' => $products[$seq % count($products)],
                'quantity' => $qty,
                'unit' => 'Pcs',
                'unit_price' => $unitPrice,
                'total_amount' => $totalAmount,
                'status' => $poStatus,
                'created_by' => $admin->id,
            ]);

            if ($stage === 'po_only') {
                continue;
            }

            $deliveryDate = $poDate->copy()->addDays(2 + ($i % 9));
            $delivery = Delivery::create([
                'purchase_order_id' => $po->id,
                'delivery_number' => 'SJ/MCI/'.$year.'/' . str_pad($seq, 4, '0', STR_PAD_LEFT),
                'delivery_date' => $deliveryDate,
                'shipped_quantity' => min($qty, max(1, $qty - ($i % 3))),
                'notes' => 'Pengiriman barang sesuai PO',
                'created_by' => $admin->id,
            ]);

            if ($stage === 'delivered') {
                continue;
            }

            $invoiceDate = $deliveryDate->copy()->addDays(1 + ($i % 7));
            $invoiceStatus = $stage === 'invoiced_paid' ? 'paid' : 'issued';

            Invoice::create([
                'delivery_id' => $delivery->id,
                'invoice_number' => 'INV/MCI/'.$year.'/' . str_pad($seq, 4, '0', STR_PAD_LEFT),
                'invoice_date' => $invoiceDate,
                'amount' => $totalAmount,
                'status' => $invoiceStatus,
                'notes' => 'Penagihan otomatis (dummy)',
                'created_by' => $admin->id,
            ]);
        }
    }
}
