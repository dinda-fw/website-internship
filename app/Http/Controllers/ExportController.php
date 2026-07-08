<?php

namespace App\Http\Controllers;

use App\Exports\ProductsExport;
use App\Models\Borrowing;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function productsPdf()
    {
        $products = Product::with('category')->orderBy('name')->get();

        $pdf = Pdf::loadView('exports.products_pdf', compact('products'))->setPaper('a4', 'landscape');

        return $pdf->download('laporan-barang-'.now()->format('Y-m-d').'.pdf');
    }

    public function productsExcel()
    {
        return Excel::download(new ProductsExport, 'laporan-barang-'.now()->format('Y-m-d').'.xlsx');
    }

    public function borrowingsPdf(Request $request)
    {
        $borrowings = Borrowing::with(['details.product', 'user'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest('borrow_date')
            ->get();

        $pdf = Pdf::loadView('exports.borrowings_pdf', compact('borrowings'))->setPaper('a4', 'landscape');

        return $pdf->download('laporan-peminjaman-'.now()->format('Y-m-d').'.pdf');
    }
}
