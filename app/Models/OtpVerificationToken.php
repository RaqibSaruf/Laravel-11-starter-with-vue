<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpVerificationToken extends Model
{
    protected $fillable = ['email', 'token', 'otp', 'verified', 'revoked', 'created_at'];

    protected $hidden = ['otp', 'verified', 'revoked'];

    public $timestamps = false;
}
