<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $products = Product::with('category')
            ->search($request->get('q'))
            ->paginate($request->get('per_page', 10));

        return response()->json($products);
    }

    public function store(Request $request): JsonResponse
    {
        if (! $request->user()->hasRole(['admin', 'staff'])) {
            return response()->json(['message' => 'Anda tidak memiliki akses.'], 403);
        }

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:30', 'unique:products,code'],
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'stock' => ['required', 'integer', 'min:0'],
            'location' => ['nullable', 'string', 'max:255'],
            'condition' => ['required', 'in:baik,rusak_ringan,rusak_berat'],
            'description' => ['nullable', 'string'],
        ]);

        $validated['total_stock'] = $validated['stock'];

        $product = Product::create($validated);

        return response()->json($product, 201);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json($product->load('category'));
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        if (! $request->user()->hasRole(['admin', 'staff'])) {
            return response()->json(['message' => 'Anda tidak memiliki akses.'], 403);
        }

        $validated = $request->validate([
            'code' => ['sometimes', 'string', 'max:30', 'unique:products,code,'.$product->id],
            'name' => ['sometimes', 'string', 'max:255'],
            'category_id' => ['sometimes', 'exists:categories,id'],
            'stock' => ['sometimes', 'integer', 'min:0'],
            'location' => ['nullable', 'string', 'max:255'],
            'condition' => ['sometimes', 'in:baik,rusak_ringan,rusak_berat'],
            'description' => ['nullable', 'string'],
        ]);

        $product->update($validated);

        return response()->json($product);
    }

    public function destroy(Request $request, Product $product): JsonResponse
    {
        if (! $request->user()->hasRole(['admin'])) {
            return response()->json(['message' => 'Hanya admin yang dapat menghapus barang.'], 403);
        }

        $product->delete();

        return response()->json(['message' => 'Barang berhasil dihapus.']);
    }

    public function dashboardSummary(): JsonResponse
    {
        return response()->json([
            'total_barang' => Product::sum('total_stock'),
            'barang_tersedia' => Product::sum('stock'),
            'jenis_barang' => Product::count(),
            'stok_menipis' => Product::lowStock((int) config('inventaris.low_stock_threshold'))->count(),
        ]);
    }
}
