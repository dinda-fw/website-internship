<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Borrowing extends Model
{
    use HasFactory;

    const STATUS_DIPINJAM = 'dipinjam';
    const STATUS_DIKEMBALIKAN = 'dikembalikan';
    const STATUS_TERLAMBAT = 'terlambat';

    protected $fillable = [
        'borrower_name',
        'user_id',
        'borrow_date',
        'due_date',
        'return_date',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'borrow_date' => 'date',
            'due_date' => 'date',
            'return_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(BorrowingDetail::class);
    }

    public function statusBadgeColor(): string
    {
        return match ($this->status) {
            self::STATUS_DIPINJAM => 'warning',
            self::STATUS_DIKEMBALIKAN => 'success',
            self::STATUS_TERLAMBAT => 'danger',
            default => 'secondary',
        };
    }
}
