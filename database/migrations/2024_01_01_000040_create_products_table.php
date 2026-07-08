<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();      // Kode Barang, mis: BRG-0001
            $table->string('name');                     // Nama Barang
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->unsignedInteger('stock')->default(0);        // Stok tersedia saat ini
            $table->unsignedInteger('total_stock')->default(0);  // Total stok yang dimiliki (tersedia + dipinjam)
            $table->string('location')->nullable();     // Lokasi Penyimpanan
            $table->enum('condition', ['baik', 'rusak_ringan', 'rusak_berat'])->default('baik'); // Kondisi Barang
            $table->string('image')->nullable();         // path gambar (bonus fitur)
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
