<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Services\OtpTokenManagerService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VerificationRequest extends FormRequest
{
    /**
     * @var OtpTokenManagerService|null
     */
    private $tokenManager;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $tokenManager = $this->getManager();

        return $tokenManager->exists();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $tokenManager = $this->getManager();

        return [
            'otp' => [
                'required',
                'string',
                'max:6',
                function (string $attribute, string $value, $fail) use ($tokenManager) {
                    if ($tokenManager->expired()) {
                        $fail('Otp expired.');
                    }
                    if (!$tokenManager->checkOtp($value)) {
                        $fail('Otp is not matched.');
                    }
                },
            ],
        ];
    }

    public function getManager(): OtpTokenManagerService
    {
        if ($this->tokenManager) {
            return $this->tokenManager;
        }
        $this->tokenManager = new OtpTokenManagerService(Auth::user()->email);

        return $this->tokenManager;
    }

    public function verify(): bool
    {
        $user = Auth::user();
        DB::beginTransaction();
        try {
            if ($this->tokenManager->verified()) {
                if ($user->markEmailAsVerified()) {
                    Auth::setUser($user);
                    $this->tokenManager->revoked();
                    DB::commit();

                    return true;
                }
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        DB::rollBack();

        return false;
    }
}
