<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'guest_name',
        'guest_email',
        'message',
        'is_from_admin',
        'is_read',
        'type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
