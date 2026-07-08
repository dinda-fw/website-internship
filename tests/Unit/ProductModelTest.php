<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_low_stock_scope_only_returns_products_at_or_below_threshold(): void
    {
        $category = Category::create(['name' => 'Elektronik']);

        Product::create([
            'code' => 'TST-001', 'name' => 'Barang Stok Menipis', 'category_id' => $category->id,
            'stock' => 2, 'total_stock' => 10, 'condition' => 'baik',
        ]);

        Product::create([
            'code' => 'TST-002', 'name' => 'Barang Stok Aman', 'category_id' => $category->id,
            'stock' => 20, 'total_stock' => 20, 'condition' => 'baik',
        ]);

        $lowStock = Product::lowStock(5)->get();

        $this->assertCount(1, $lowStock);
        $this->assertEquals('TST-001', $lowStock->first()->code);
    }

    public function test_is_available_reflects_current_stock(): void
    {
        $category = Category::create(['name' => 'Elektronik']);

        $product = Product::create([
            'code' => 'TST-003', 'name' => 'Barang Habis', 'category_id' => $category->id,
            'stock' => 0, 'total_stock' => 5, 'condition' => 'baik',
        ]);

        $this->assertFalse($product->isAvailable());
    }
}
