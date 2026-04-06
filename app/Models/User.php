<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        // 1. Check if email matches the master admin in .env
        $masterAdmin = env('ADMIN_EMAIL');
        if ($masterAdmin && $this->email === $masterAdmin) {
            return true;
        }

        return $this->role === 'admin' || (bool) $this->getAttribute('is_admin');
    }

    /**
     * Attribute for Blade: $user->is_admin
     */
    public function getIsAdminAttribute(): bool
    {
        return $this->isAdmin();
    }
}
