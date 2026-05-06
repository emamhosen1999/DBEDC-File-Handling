<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasUlid;

class UserPreference extends Model
{
    use HasUlid;

    protected $fillable = [
        'user_id',
        'quick_actions',
        'dashboard_layout',
        'theme_preference',
        'default_view',
        'items_per_page',
    ];

    protected $casts = [
        'quick_actions' => 'array',
        'dashboard_layout' => 'array',
        'items_per_page' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
