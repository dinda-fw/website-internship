<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Elektronik', 'description' => 'Perangkat elektronik kantor: laptop, monitor, printer, kamera, dsb.'],
            ['name' => 'Furniture', 'description' => 'Perabotan kantor: kursi, meja, lemari, sofa, dsb.'],
            ['name' => 'ATK', 'description' => 'Alat Tulis Kantor & perlengkapan administrasi'],
            ['name' => 'Jaringan', 'description' => 'Perangkat jaringan & infrastruktur telekomunikasi'],
            ['name' => 'Kendaraan Operasional', 'description' => 'Kendaraan dinas & operasional kantor'],
            ['name' => 'Audio Visual', 'description' => 'Perangkat audio, video, dan multimedia untuk presentasi/meeting'],
            ['name' => 'Peralatan K3', 'description' => 'Peralatan Keselamatan dan Kesehatan Kerja'],
            ['name' => 'Kebersihan', 'description' => 'Peralatan kebersihan & kenyamanan ruangan'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['name' => $category['name']], $category);
        }
    }
}
