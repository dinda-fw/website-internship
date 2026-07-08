<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BorrowingApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $borrowings = Borrowing::with(['details.product', 'user'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest('borrow_date')
            ->paginate($request->get('per_page', 10));

        return response()->json($borrowings);
    }

    public function store(Request $request): JsonResponse
    {
        if (! $request->user()->hasRole(['admin', 'staff'])) {
            return response()->json(['message' => 'Anda tidak memiliki akses.'], 403);
        }

        $validated = $request->validate([
            'borrower_name' => ['required', 'string', 'max:255'],
            'borrow_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:borrow_date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $borrowing = DB::transaction(function () use ($validated, $request) {
            $borrowing = Borrowing::create([
                'borrower_name' => $validated['borrower_name'],
                'user_id' => $request->user()->id,
                'borrow_date' => $validated['borrow_date'],
                'due_date' => $validated['due_date'] ?? null,
                'status' => Borrowing::STATUS_DIPINJAM,
            ]);

            foreach ($validated['items'] as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                abort_if($product->stock < $item['quantity'], 422, "Stok \"{$product->name}\" tidak mencukupi.");

                $borrowing->details()->create([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                ]);

                $product->decrement('stock', $item['quantity']);
            }

            return $borrowing;
        });

        return response()->json($borrowing->load('details.product'), 201);
    }

    public function show(Borrowing $borrowing): JsonResponse
    {
        return response()->json($borrowing->load(['details.product', 'user']));
    }

    public function returnItem(Request $request, Borrowing $borrowing): JsonResponse
    {
        if ($borrowing->status === Borrowing::STATUS_DIKEMBALIKAN) {
            return response()->json(['message' => 'Peminjaman ini sudah dikembalikan.'], 422);
        }

        $validated = $request->validate([
            'return_date' => ['required', 'date'],
        ]);

        DB::transaction(function () use ($borrowing, $validated) {
            foreach ($borrowing->details as $detail) {
                $detail->product()->increment('stock', $detail->quantity);
            }

            $borrowing->update([
                'return_date' => $validated['return_date'],
                'status' => Borrowing::STATUS_DIKEMBALIKAN,
            ]);
        });

        return response()->json($borrowing->fresh()->load('details.product'));
    }
}
