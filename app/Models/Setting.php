<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUlid;

class Setting extends Model
{
    use HasUlid;

    protected $fillable = [
        'setting_key',
        'setting_value',
        'setting_group',
        'data_type',
        'is_public',
        'description',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'setting_value' => 'array',
    ];
}
