<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\OtpVerificationToken;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OtpTokenManagerService
{
    /**
     * @var int in seconds
     */
    private $expires;

    /**
     * @var int in seconds
     */
    private $throttle;

    /**
     * @var OtpVerificationToken|null
     */
    private $otpVerificationToken;

    public function __construct(private string $email)
    {
        $this->expires = 240; // 240 seconds
        $this->throttle = 240; // 240 seconds
    }

    /**
     * Create a new token record.
     */
    public function create(): OtpVerificationToken
    {
        if ($this->recentlyCreatedToken()) {
            throw new \Exception('Verification code already sent!', Response::HTTP_FORBIDDEN);
        }

        $this->deleteExisting();

        // We will create a new, random token for the user so that we can e-mail them
        // a safe link to the password reset form. Then we will insert a record in
        // the database so that we can verify the token within the actual reset.
        $token = $this->createNewToken();

        $this->otpVerificationToken = OtpVerificationToken::create([
            'email' => $this->getEmail(),
            'token' => Hash::make($token),
            'otp' => $this->generateOtp(),
            'created_at' => new Carbon(),
        ]);

        return $this->otpVerificationToken;
    }

    /**
     * Delete all existing reset tokens from the database.
     */
    private function deleteExisting(): void
    {
        $record = $this->getRecord();

        if ($record) {
            $record->delete();
        }
    }

    /**
     * Create a new token for the user.     *.
     */
    private function createNewToken(): string
    {
        return Hash::make(Str::random(20));
    }

    /**
     * Determine if a record exists.
     */
    public function exists(): bool
    {
        $record = $this->getRecord();

        return $record && !$record->verified && !$record->revoked;
    }

    /**
     * Determine if a token record is valid.
     *
     * @param string $token
     */
    public function checkToken(#[\SensitiveParameter] $token): bool
    {
        $record = $this->getRecord();

        return $record && Hash::check($token, $record->token);
    }

    /**
     * Determine if otp is valid.
     *
     * @param string $otp
     */
    public function checkOtp(#[\SensitiveParameter] string $otp): bool
    {
        $record = $this->getRecord();

        return $record && $otp === $record->otp;
    }

    /**
     * Determine if the token has expired.
     */
    public function expired(): bool
    {
        $record = $this->getRecord();

        return $record && Carbon::parse($record->created_at)->addSeconds($this->expires)->isPast();
    }

    /**
     * Determine if the given user recently created a token.
     */
    public function recentlyCreatedToken(): bool
    {
        $record = $this->getRecord();

        return $record && !$record->verified && !$record->revoked && $this->tokenRecentlyCreated($record->created_at);
    }

    /**
     * Determine if the token was recently created.
     *
     * @param string $createdAt
     */
    protected function tokenRecentlyCreated($createdAt): bool
    {
        if ($this->throttle <= 0) {
            return false;
        }

        return Carbon::parse($createdAt)->addSeconds(
            $this->throttle
        )->isFuture();
    }

    private function getEmail(): string
    {
        return $this->email;
    }

    public function getRecord(): ?OtpVerificationToken
    {
        if (!$this->otpVerificationToken) {
            $this->otpVerificationToken = OtpVerificationToken::where('email', $this->getEmail())->first();
        }

        return $this->otpVerificationToken;
    }

    private function generateOtp(): string
    {
        return (string) rand(100000, 999999);
    }

    public function verified(): bool
    {
        $record = $this->getRecord();

        if ($record) {
            $record->update(['verified' => 1]);

            return true;
        }

        return false;
    }

    public function revoked(): bool
    {
        $record = $this->getRecord();

        if ($record) {
            $record->update(['revoked' => 1]);

            return true;
        }

        return false;
    }
}
