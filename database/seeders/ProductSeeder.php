<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Data barang dibuat cukup banyak & bervariasi (termasuk beberapa yang
     * stoknya menipis/habis) agar dashboard, pencarian, filter, dan fitur
     * notifikasi stok menipis terlihat realistis saat pertama kali demo.
     */
    public function run(): void
    {
        $categoryIds = Category::pluck('id', 'name');

        // format: [code, name, category, stock, location, condition]
        $products = [
            // ================= Elektronik =================
            ['BRG-0001', 'Laptop Dell Latitude 5440', 'Elektronik', 8, 'Gudang IT Lt. 2', 'baik'],
            ['BRG-0002', 'Laptop Lenovo ThinkPad E14', 'Elektronik', 6, 'Gudang IT Lt. 2', 'baik'],
            ['BRG-0003', 'Proyektor Epson EB-X51', 'Elektronik', 3, 'Ruang AV Lt. 1', 'baik'],
            ['BRG-0004', 'Proyektor BenQ MX535', 'Elektronik', 2, 'Ruang AV Lt. 1', 'baik'],
            ['BRG-0005', 'Monitor LG 24 Inch', 'Elektronik', 10, 'Gudang IT Lt. 2', 'baik'],
            ['BRG-0006', 'Monitor Samsung 27 Inch', 'Elektronik', 4, 'Gudang IT Lt. 2', 'baik'],
            ['BRG-0007', 'Printer Epson L3210', 'Elektronik', 5, 'Ruang Admin Lt. 1', 'rusak_ringan'],
            ['BRG-0008', 'Printer HP LaserJet Pro', 'Elektronik', 3, 'Ruang Admin Lt. 1', 'baik'],
            ['BRG-0009', 'Kamera DSLR Canon EOS 90D', 'Elektronik', 1, 'Ruang Marketing Lt. 4', 'baik'],
            ['BRG-0010', 'Scanner Canon CanoScan', 'Elektronik', 0, 'Ruang Admin Lt. 1', 'rusak_berat'],

            // ================= Furniture =================
            ['BRG-0011', 'Kursi Kantor Ergonomis', 'Furniture', 25, 'Gudang Umum Lt. 1', 'baik'],
            ['BRG-0012', 'Kursi Rapat', 'Furniture', 40, 'Gudang Umum Lt. 1', 'baik'],
            ['BRG-0013', 'Meja Rapat Lipat', 'Furniture', 4, 'Gudang Umum Lt. 1', 'rusak_ringan'],
            ['BRG-0014', 'Meja Kerja Staff', 'Furniture', 18, 'Gudang Umum Lt. 1', 'baik'],
            ['BRG-0015', 'Lemari Arsip Besi', 'Furniture', 6, 'Gudang Umum Lt. 1', 'baik'],
            ['BRG-0016', 'Sofa Ruang Tunggu', 'Furniture', 2, 'Lobby Lt. 1', 'baik'],
            ['BRG-0017', 'Partisi Kantor', 'Furniture', 9, 'Gudang Umum Lt. 1', 'rusak_ringan'],
            ['BRG-0018', 'Rak Buku Kayu', 'Furniture', 5, 'Ruang Perpustakaan Lt. 2', 'baik'],

            // ================= ATK =================
            ['BRG-0019', 'Whiteboard 120x90', 'ATK', 6, 'Gudang ATK', 'baik'],
            ['BRG-0020', 'Flipchart Stand', 'ATK', 3, 'Gudang ATK', 'baik'],
            ['BRG-0021', 'Proyektor Screen Portable', 'ATK', 4, 'Ruang AV Lt. 1', 'baik'],
            ['BRG-0022', 'Stapler Besar Heavy Duty', 'ATK', 12, 'Gudang ATK', 'baik'],
            ['BRG-0023', 'Mesin Fotokopi Kecil', 'ATK', 1, 'Ruang Admin Lt. 1', 'baik'],
            ['BRG-0024', 'Laminating Machine', 'ATK', 2, 'Gudang ATK', 'baik'],

            // ================= Jaringan =================
            ['BRG-0025', 'Router Mikrotik RB1100', 'Jaringan', 5, 'Server Room Lt. 3', 'baik'],
            ['BRG-0026', 'Switch HP 24 Port', 'Jaringan', 4, 'Server Room Lt. 3', 'baik'],
            ['BRG-0027', 'Switch Cisco 48 Port', 'Jaringan', 2, 'Server Room Lt. 3', 'baik'],
            ['BRG-0028', 'Kabel UTP Cat6 (roll)', 'Jaringan', 30, 'Gudang IT Lt. 2', 'baik'],
            ['BRG-0029', 'Access Point Ubiquiti', 'Jaringan', 8, 'Server Room Lt. 3', 'baik'],
            ['BRG-0030', 'Modem Fiber Optik', 'Jaringan', 6, 'Server Room Lt. 3', 'baik'],
            ['BRG-0031', 'Server Rack 12U', 'Jaringan', 1, 'Server Room Lt. 3', 'baik'],
            ['BRG-0032', 'UPS APC 1000VA', 'Jaringan', 3, 'Server Room Lt. 3', 'rusak_ringan'],

            // ================= Kendaraan Operasional =================
            ['BRG-0033', 'Mobil Operasional Avanza', 'Kendaraan Operasional', 2, 'Parkiran Basement', 'baik'],
            ['BRG-0034', 'Mobil Operasional Innova', 'Kendaraan Operasional', 1, 'Parkiran Basement', 'baik'],
            ['BRG-0035', 'Motor Operasional Honda Vario', 'Kendaraan Operasional', 3, 'Parkiran Basement', 'baik'],
            ['BRG-0036', 'Sepeda Lipat Kantor', 'Kendaraan Operasional', 4, 'Parkiran Basement', 'baik'],

            // ================= Audio Visual =================
            ['BRG-0037', 'Speaker Aktif JBL', 'Audio Visual', 4, 'Ruang AV Lt. 1', 'baik'],
            ['BRG-0038', 'Mic Wireless Shure', 'Audio Visual', 6, 'Ruang AV Lt. 1', 'baik'],
            ['BRG-0039', 'Layar TV LED 55 Inch', 'Audio Visual', 2, 'Ruang Rapat Utama Lt. 5', 'baik'],
            ['BRG-0040', 'Video Conference Kit', 'Audio Visual', 3, 'Ruang Rapat Utama Lt. 5', 'baik'],
            ['BRG-0041', 'Tripod Kamera', 'Audio Visual', 5, 'Ruang Marketing Lt. 4', 'baik'],
            ['BRG-0042', 'Lighting Ring Light', 'Audio Visual', 4, 'Ruang Marketing Lt. 4', 'baik'],

            // ================= Peralatan K3 =================
            ['BRG-0043', 'APAR (Alat Pemadam Api Ringan)', 'Peralatan K3', 15, 'Setiap Lantai', 'baik'],
            ['BRG-0044', 'Helm Safety', 'Peralatan K3', 20, 'Gudang Umum Lt. 1', 'baik'],
            ['BRG-0045', 'Rompi Safety', 'Peralatan K3', 25, 'Gudang Umum Lt. 1', 'baik'],
            ['BRG-0046', 'Kotak P3K', 'Peralatan K3', 10, 'Setiap Lantai', 'baik'],
            ['BRG-0047', 'Safety Shoes', 'Peralatan K3', 12, 'Gudang Umum Lt. 1', 'baik'],

            // ================= Kebersihan =================
            ['BRG-0048', 'Vacuum Cleaner', 'Kebersihan', 5, 'Gudang Umum Lt. 1', 'baik'],
            ['BRG-0049', 'Dispenser Air', 'Kebersihan', 8, 'Setiap Lantai', 'baik'],
            ['BRG-0050', 'Kipas Angin Berdiri', 'Kebersihan', 6, 'Gudang Umum Lt. 1', 'rusak_ringan'],
            ['BRG-0051', 'AC Portable', 'Kebersihan', 3, 'Ruang Server Lt. 3', 'baik'],
            ['BRG-0052', 'Air Purifier', 'Kebersihan', 2, 'Ruang Direksi Lt. 5', 'baik'],
        ];

        foreach ($products as [$code, $name, $categoryName, $stock, $location, $condition]) {
            Product::updateOrCreate(
                ['code' => $code],
                [
                    'name' => $name,
                    'category_id' => $categoryIds[$categoryName] ?? null,
                    'stock' => $stock,
                    'total_stock' => $stock,
                    'location' => $location,
                    'condition' => $condition,
                    // Gambar placeholder bergaya kartu (dibuat otomatis per kategori),
                    // sudah tersedia di storage/app/public/products/{code}.jpg
                    'image' => "products/{$code}.jpg",
                ]
            );
        }
    }
}
