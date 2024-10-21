<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailVerificationToken extends Model
{
    protected $fillable = [
        'user_id',
        'token',
        'expires',
        'last_sent'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
