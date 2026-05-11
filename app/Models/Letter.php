<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUlid;

class Letter extends Model
{
    use HasFactory, HasUlid, SoftDeletes;

    protected $fillable = [
        'reference',
        'title',
        'description',
        'sender',
        'recipient',
        'subject',
        'letter_date',
        'due_date',
        'priority',
        'status',
        'department_id',
        'assigned_to',
        'stakeholder_id',
        'file_path',
        'file_name',
        'file_size',
        'file_mime_type',
        'created_by',
        'completed_at',
    ];

    protected $casts = [
        'letter_date' => 'date',
        'due_date' => 'date',
        'completed_at' => 'datetime',
        'file_size' => 'integer',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function stakeholder(): BelongsTo
    {
        return $this->belongsTo(Stakeholder::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
