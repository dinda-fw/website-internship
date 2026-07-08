<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('borrowings', function (Blueprint $table) {
            $table->id();
            $table->string('borrower_name');                 // Nama Peminjam
            $table->foreignId('user_id')                     // Petugas yang mencatat peminjaman
                ->constrained('users')
                ->cascadeOnDelete();
            $table->date('borrow_date');                      // Tanggal Pinjam
            $table->date('due_date')->nullable();             // Batas waktu pengembalian
            $table->date('return_date')->nullable();          // Tanggal Kembali (aktual)
            $table->enum('status', ['dipinjam', 'dikembalikan', 'terlambat'])->default('dipinjam');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['status']);
            $table->index(['borrow_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('borrowings');
    }
};
