<?php

declare(strict_types=1);

namespace App\Traits\Interfaces;

use Illuminate\Contracts\Auth\MustVerifyEmail;

interface MustVerifyAccount extends MustVerifyEmail
{
    public function hasVerifiedPhone(): bool;

    public function markPhoneAsVerified(): bool;

    public function getPhoneForVerification(): string;

    public function isVerified(): bool;
}
