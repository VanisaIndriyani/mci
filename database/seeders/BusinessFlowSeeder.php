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
        
        $data = [
            [
                'customer' => 'PT. Toyota Motor Manufacturing',
                'products' => ['Jig Welding Frame', 'Special Purpose Machine Alpha', 'Mechanical Joint Type A'],
            ],
            [
                'customer' => 'PT. Astra Honda Motor',
                'products' => ['Conveyor System v2', 'Bracket Support Engine', 'Base Plate Precision'],
            ],
            [
                'customer' => 'PT. Denso Indonesia',
                'products' => ['Clamping Tool Set', 'Robot Arm Extension', 'Cooling System Unit'],
            ],
            [
                'customer' => 'PT. Mitsubishi Motors',
                'products' => ['Press Tool Module', 'Shaft Drive Gear', 'Sensor Housing Case'],
            ],
            [
                'customer' => 'PT. Suzuki Indomobil',
                'products' => ['Pallet Handler', 'Casting Mold B1', 'Pneumatic Control Box'],
            ],
        ];

        foreach ($data as $index => $item) {
            $month = $index + 1;
            $poDate = Carbon::create(2025, $month, 10);
            $qty = ($index + 1) * 10;
            $unitPrice = 1500000 + ($index * 500000);
            $totalAmount = $qty * $unitPrice;

            // 1. Create Purchase Order
            $po = PurchaseOrder::create([
                'po_number' => 'PO/MCI/2025/' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'po_date' => $poDate,
                'customer_name' => $item['customer'],
                'product_name' => $item['products'][array_rand($item['products'])],
                'quantity' => $qty,
                'unit' => 'Pcs',
                'unit_price' => $unitPrice,
                'total_amount' => $totalAmount,
                'status' => $index < 3 ? 'selesai' : 'dikirim',
                'created_by' => $admin->id,
            ]);

            // 2. Create Delivery (Surat Jalan)
            $deliveryDate = $poDate->copy()->addDays(5);
            $delivery = Delivery::create([
                'purchase_order_id' => $po->id,
                'delivery_number' => 'SJ/MCI/2025/' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'delivery_date' => $deliveryDate,
                'notes' => 'Pengiriman barang sesuai PO',
                'created_by' => $admin->id,
            ]);

            // 3. Create Invoice (Penagihan)
            $invoiceDate = $deliveryDate->copy()->addDays(3);
            Invoice::create([
                'delivery_id' => $delivery->id,
                'invoice_number' => 'INV/MCI/2025/' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'invoice_date' => $invoiceDate,
                'amount' => $totalAmount,
                'status' => $index < 2 ? 'paid' : 'issued',
                'notes' => 'Penagihan tahap 1',
                'created_by' => $admin->id,
            ]);
        }
    }
}
