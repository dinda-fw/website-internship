<?php

namespace App\Exports;

use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection(): Collection
    {
        return Product::with('category')->orderBy('name')->get();
    }

    public function headings(): array
    {
        return [
            'Kode Barang',
            'Nama Barang',
            'Kategori',
            'Stok Tersedia',
            'Total Stok',
            'Lokasi Penyimpanan',
            'Kondisi',
            'Status',
        ];
    }

    public function map($product): array
    {
        return [
            $product->code,
            $product->name,
            $product->category->name ?? '-',
            $product->stock,
            $product->total_stock,
            $product->location ?? '-',
            ucwords(str_replace('_', ' ', $product->condition)),
            $product->statusLabel(),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
