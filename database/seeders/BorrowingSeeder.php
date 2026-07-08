<?php

namespace Database\Seeders;

use App\Models\Borrowing;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class BorrowingSeeder extends Seeder
{
    /**
     * Membuat riwayat peminjaman yang realistis & tersebar sepanjang tahun
     * (12 bulan terakhir) agar grafik pada dashboard terlihat "hidup" saat
     * pertama kali demo, lengkap dengan variasi status:
     *   - dipinjam    : baru dipinjam, belum jatuh tempo
     *   - terlambat   : sudah lewat batas kembali tapi belum dikembalikan
     *   - dikembalikan: sudah selesai dikembalikan (mayoritas data historis)
     */
    public function run(): void
    {
        // Reset agar seeder aman dijalankan berulang kali (idempotent)
        Borrowing::query()->delete(); // borrowing_details ikut terhapus (cascade)
        DB::table('products')->update(['stock' => DB::raw('total_stock')]);

        $admin = User::where('email', 'admin@telkomsel.test')->first();
        $staff = User::where('email', 'staff@telkomsel.test')->first();
        $recorders = array_values(array_filter([$staff, $admin]));

        $products = Product::orderBy('id')->get();

        if (empty($recorders) || $products->isEmpty()) {
            return;
        }

        $names = [
            'Budi Santoso', 'Siti Rahma', 'Andi Wijaya', 'Dewi Lestari', 'Rian Pratama',
            'Fajar Nugroho', 'Maya Sari', 'Agus Setiawan', 'Nur Aini', 'Bayu Kurniawan',
            'Lina Marlina', 'Hendra Gunawan', 'Wulan Ramadhani', 'Yusuf Ramadhan', 'Indah Permatasari',
            'Doni Saputra', 'Tika Anggraini', 'Eko Prasetyo', 'Ratna Dewi', 'Arif Rahman',
            'Nadia Kusuma', 'Wahyu Hidayat', 'Sri Wahyuni', 'Rudi Hartono', 'Putri Amelia',
            'Taufik Hidayat', 'Dian Puspita', 'Ilham Maulana', 'Fitri Handayani', 'Galih Prakoso',
        ];

        $now = Carbon::now();
        $productCount = $products->count();
        $nameCount = count($names);
        $recorderCount = count($recorders);

        $activeUsage = []; // product_id => total kuantitas yang sedang aktif dipinjam (belum kembali)
        $recordIndex = 0;

        $createBorrowing = function (Carbon $borrowDate, Carbon $dueDate, string $status, ?Carbon $returnDate) use (
            $products, $names, $recorders, $productCount, $nameCount, $recorderCount, &$activeUsage, &$recordIndex
        ) {
            $i = $recordIndex;

            $borrowing = Borrowing::create([
                'borrower_name' => $names[$i % $nameCount],
                'user_id' => $recorders[$i % $recorderCount]->id,
                'borrow_date' => $borrowDate,
                'due_date' => $dueDate,
                'return_date' => $returnDate,
                'status' => $status,
            ]);

            $isActive = in_array($status, [Borrowing::STATUS_DIPINJAM, Borrowing::STATUS_TERLAMBAT], true);
            $itemCount = ($i % 3 === 0) ? 2 : 1;
            $createdItems = 0;

            for ($j = 0; $j < $itemCount; $j++) {
                $product = $products[($i + $j * 7) % $productCount];
                $quantity = ($i % 2 === 0) ? 1 : 2;

                if ($isActive) {
                    $used = $activeUsage[$product->id] ?? 0;
                    $available = $product->total_stock - $used;

                    if ($available <= 0) {
                        continue;
                    }

                    $quantity = min($quantity, $available);
                    $activeUsage[$product->id] = $used + $quantity;
                }

                $borrowing->details()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'condition_on_return' => $status === Borrowing::STATUS_DIKEMBALIKAN ? 'baik' : null,
                ]);

                $createdItems++;
            }

            // Jaga-jaga: hindari transaksi kosong tanpa barang (barang kebetulan sudah habis dipinjam di skenario ini)
            if ($createdItems === 0) {
                $borrowing->delete();

                return;
            }

            $recordIndex++;
        };

        // ================= Kelompok 1: baru dipinjam, belum jatuh tempo (1-6 hari lalu) =================
        for ($i = 0; $i < 6; $i++) {
            $borrowDate = (clone $now)->subDays(6 - $i);
            $dueDate = (clone $borrowDate)->addDays(7);
            $createBorrowing($borrowDate, $dueDate, Borrowing::STATUS_DIPINJAM, null);
        }

        // ================= Kelompok 2: 8-30 hari lalu, campuran terlambat & sudah dikembalikan =================
        for ($i = 0; $i < 12; $i++) {
            $dayOffset = 8 + ($i * 2); // 8, 10, 12, ... 30 hari lalu
            $borrowDate = (clone $now)->subDays($dayOffset);
            $dueDate = (clone $borrowDate)->addDays(7);

            if ($i % 3 === 0) {
                $createBorrowing($borrowDate, $dueDate, Borrowing::STATUS_TERLAMBAT, null);
            } else {
                $returnDate = (clone $dueDate)->addDays($i % 2 === 0 ? -1 : 2);
                $createBorrowing($borrowDate, $dueDate, Borrowing::STATUS_DIKEMBALIKAN, $returnDate);
            }
        }

        // ================= Kelompok 3: 31-365 hari lalu, riwayat panjang (mayoritas sudah dikembalikan) =================
        $olderCount = 72;
        for ($i = 0; $i < $olderCount; $i++) {
            $dayOffset = 31 + (int) round($i * (365 - 31) / max($olderCount - 1, 1));
            $borrowDate = (clone $now)->subDays($dayOffset);
            $dueDate = (clone $borrowDate)->addDays(7);

            // Sisakan segelintir sebagai "terlambat kronis" yang belum pernah dikembalikan
            if ($i === 10 || $i === 45) {
                $createBorrowing($borrowDate, $dueDate, Borrowing::STATUS_TERLAMBAT, null);

                continue;
            }

            $returnDate = (clone $dueDate)->addDays(($i % 5) - 2); // variasi -2..+2 hari dari jatuh tempo
            $createBorrowing($borrowDate, $dueDate, Borrowing::STATUS_DIKEMBALIKAN, $returnDate);
        }

        // ================= Sinkronkan stok akhir barang berdasarkan peminjaman yang masih aktif =================
        foreach ($products as $product) {
            $used = $activeUsage[$product->id] ?? 0;
            $product->update(['stock' => max($product->total_stock - $used, 0)]);
        }
    }
}
