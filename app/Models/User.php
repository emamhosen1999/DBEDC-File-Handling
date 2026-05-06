<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\HasUlid;

#[Fillable(['name', 'email', 'password', 'google_id', 'department_id', 'role', 'avatar_url', 'is_active', 'email_notifications', 'push_notifications', 'wechat_openid', 'wechat_unionid', 'provider', 'last_login'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasUlid;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'email_notifications' => 'boolean',
            'push_notifications' => 'boolean',
            'last_login' => 'datetime',
        ];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function assignedTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function createdTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'created_by');
    }

    public function createdLetters(): HasMany
    {
        return $this->hasMany(Letter::class, 'created_by');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function preferences(): HasMany
    {
        return $this->hasMany(UserPreference::class);
    }

    public function pushSubscriptions(): HasMany
    {
        return $this->hasMany(PushSubscription::class);
    }

    public function managedDepartment(): HasMany
    {
        return $this->hasMany(Department::class, 'manager_id');
    }
}
