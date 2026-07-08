<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'category_id',
        'stock',
        'total_stock',
        'location',
        'condition',
        'image',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'stock' => 'integer',
            'total_stock' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function borrowingDetails(): HasMany
    {
        return $this->hasMany(BorrowingDetail::class);
    }

    /**
     * Scope pencarian sederhana berdasarkan nama atau kode barang.
     */
    public function scopeSearch($query, ?string $term)
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('code', 'like', "%{$term}%")
                ->orWhere('location', 'like', "%{$term}%");
        });
    }

    /**
     * Barang dengan stok menipis (bonus fitur notifikasi).
     */
    public function scopeLowStock($query, int $threshold)
    {
        return $query->where('stock', '<=', $threshold);
    }

    public function isAvailable(): bool
    {
        return $this->stock > 0;
    }

    public function statusLabel(): string
    {
        return $this->isAvailable() ? 'Tersedia' : 'Habis / Sedang Dipinjam';
    }
}
