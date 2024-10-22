<?php

declare(strict_types=1);

namespace App\Traits;

trait VerifyAccount
{
    /**
     * Determine if the user has verified their phone number.
     *
     * @return bool
     */
    public function hasVerifiedPhone(): bool
    {
        return ! is_null($this->phone_verified_at);
    }

    /**
     * Mark the given user's phone as verified.
     *
     * @return bool
     */
    public function markPhoneAsVerified(): bool
    {
        return $this->forceFill([
            'phone_verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    /**
     * Get the phone address that should be used for verification.
     *
     * @return string
     */
    public function getPhoneForVerification(): string
    {
        return $this->phone ?? '';
    }

    public function isVerified(): bool
    {
        return ! is_null($this->email_verified_at) || ! is_null($this->phone_verified_at);
    }
}
