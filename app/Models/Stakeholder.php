<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HasUlid;

class Stakeholder extends Model
{
    use HasFactory, HasUlid;

    protected $fillable = [
        'name',
        'code',
        'color',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function letters(): HasMany
    {
        return $this->hasMany(Letter::class);
    }
}
