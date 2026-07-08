<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    const ADMIN = 'admin';
    const STAFF = 'staff';
    const MANAGER = 'manager';

    protected $fillable = [
        'name',
        'label',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
