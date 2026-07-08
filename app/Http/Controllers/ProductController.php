<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::with('category')
            ->search($request->get('q'))
            ->when($request->filled('category_id'), fn ($q) => $q->where('category_id', $request->category_id))
            ->when($request->filled('condition'), fn ($q) => $q->where('condition', $request->condition))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('products.create', compact('categories'));
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $validated['total_stock'] = $validated['stock'];

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function show(Product $product): View
    {
        $product->load(['category', 'borrowingDetails.borrowing']);

        return view('products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        $categories = Category::orderBy('name')->get();

        return view('products.edit', compact('product', 'categories'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $validated = $request->validated();

        // Selisih stok baru dipakai untuk menyesuaikan total_stock agar konsisten
        $stockDiff = $validated['stock'] - $product->stock;
        $validated['total_stock'] = $product->total_stock + $stockDiff;

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->borrowingDetails()->exists()) {
            return back()->with('error', 'Barang tidak dapat dihapus karena memiliki riwayat peminjaman.');
        }

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Barang berhasil dihapus.');
    }
}
