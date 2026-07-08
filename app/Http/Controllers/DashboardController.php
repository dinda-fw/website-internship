<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\BorrowingDetail;
use App\Models\Product;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalBarang = Product::sum('total_stock');
        $barangTersedia = Product::sum('stock');
        $barangDipinjam = max($totalBarang - $barangTersedia, 0);
        $totalJenisBarang = Product::count();

        $lowStockThreshold = (int) config('inventaris.low_stock_threshold', env('LOW_STOCK_THRESHOLD', 5));
        $lowStockProducts = Product::lowStock($lowStockThreshold)->orderBy('stock')->get();

        // Grafik peminjaman per bulan (tahun berjalan).
        // Catatan: pengelompokan per bulan sengaja dilakukan di sisi PHP (bukan
        // memakai fungsi SQL seperti MONTH()) agar query ini portabel dan
        // berjalan sama baiknya di SQLite, MySQL, maupun PostgreSQL.
        $year = now()->year;

        $monthlyRaw = Borrowing::whereYear('borrow_date', $year)
            ->get(['borrow_date'])
            ->groupBy(fn (Borrowing $b) => (int) $b->borrow_date->format('n'))
            ->map->count();

        $monthlyBorrowings = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyBorrowings[] = (int) ($monthlyRaw[$m] ?? 0);
        }

        $recentBorrowings = Borrowing::with(['details.product', 'user'])
            ->latest('borrow_date')
            ->take(5)
            ->get();

        return view('dashboard', [
            'totalBarang' => $totalBarang,
            'totalJenisBarang' => $totalJenisBarang,
            'barangTersedia' => $barangTersedia,
            'barangDipinjam' => $barangDipinjam,
            'lowStockProducts' => $lowStockProducts,
            'lowStockThreshold' => $lowStockThreshold,
            'monthlyBorrowings' => $monthlyBorrowings,
            'chartYear' => $year,
            'recentBorrowings' => $recentBorrowings,
        ]);
    }
}
