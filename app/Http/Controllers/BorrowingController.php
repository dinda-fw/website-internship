<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBorrowingRequest;
use App\Models\Borrowing;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use App\Notifications\LowStockNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class BorrowingController extends Controller
{
    public function index(Request $request): View
    {
        $borrowings = Borrowing::with(['details.product', 'user'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('q'), function ($q) use ($request) {
                $q->where('borrower_name', 'like', '%'.$request->q.'%');
            })
            ->latest('borrow_date')
            ->paginate(10)
            ->withQueryString();

        return view('borrowings.index', compact('borrowings'));
    }

    public function create(): View
    {
        $products = Product::where('stock', '>', 0)->orderBy('name')->get();

        return view('borrowings.create', compact('products'));
    }

    public function store(StoreBorrowingRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $request) {
            $borrowing = Borrowing::create([
                'borrower_name' => $validated['borrower_name'],
                'user_id' => $request->user()->id,
                'borrow_date' => $validated['borrow_date'],
                'due_date' => $validated['due_date'] ?? null,
                'status' => Borrowing::STATUS_DIPINJAM,
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    abort(422, "Stok barang \"{$product->name}\" tidak mencukupi. Sisa stok: {$product->stock}.");
                }

                $borrowing->details()->create([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                ]);

                $product->decrement('stock', $item['quantity']);

                $this->notifyIfLowStock($product->fresh());
            }
        });

        return redirect()->route('borrowings.index')->with('success', 'Peminjaman berhasil dicatat.');
    }

    public function show(Borrowing $borrowing): View
    {
        $borrowing->load(['details.product', 'user']);

        return view('borrowings.show', compact('borrowing'));
    }

    /**
     * Proses pengembalian barang: mengembalikan stok & memperbarui status.
     */
    public function returnItem(Request $request, Borrowing $borrowing): RedirectResponse
    {
        if ($borrowing->status === Borrowing::STATUS_DIKEMBALIKAN) {
            return back()->with('error', 'Peminjaman ini sudah dikembalikan sebelumnya.');
        }

        $validated = $request->validate([
            'return_date' => ['required', 'date'],
            'conditions' => ['nullable', 'array'],
            'conditions.*' => ['nullable', 'in:baik,rusak_ringan,rusak_berat'],
        ]);

        DB::transaction(function () use ($borrowing, $validated) {
            foreach ($borrowing->details as $detail) {
                $detail->update([
                    'condition_on_return' => $validated['conditions'][$detail->id] ?? 'baik',
                ]);

                $detail->product()->increment('stock', $detail->quantity);
            }

            $borrowing->update([
                'return_date' => $validated['return_date'],
                'status' => Borrowing::STATUS_DIKEMBALIKAN,
            ]);
        });

        return redirect()->route('borrowings.index')->with('success', 'Pengembalian barang berhasil dicatat.');
    }

    /**
     * Kirim notifikasi email ke admin & manager jika stok barang sudah menipis.
     * Ambang batas diatur di config/inventaris.php (LOW_STOCK_THRESHOLD pada .env).
     */
    private function notifyIfLowStock(Product $product): void
    {
        $threshold = (int) config('inventaris.low_stock_threshold', 5);

        if ($product->stock > $threshold) {
            return;
        }

        $recipients = User::whereHas('role', fn ($q) => $q->whereIn('name', [Role::ADMIN, Role::MANAGER]))->get();

        if ($recipients->isNotEmpty()) {
            Notification::send($recipients, new LowStockNotification($product));
        }
    }
}
