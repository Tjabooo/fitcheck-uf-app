<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistrationAttempt extends Model
{
    protected $fillable = [
        'ip_address',
        'attempt_time'
    ];
}
