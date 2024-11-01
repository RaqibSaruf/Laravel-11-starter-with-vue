<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Models\User;
use App\Services\OtpTokenManagerService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class NewPasswordRequest extends FormRequest
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
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'confirmed', 'min:6', 'max:60'],
        ];
    }

    public function findUser(): User
    {
        $user = User::where('email', $this->email)->first();
        if (!$user) {
            throw ValidationException::withMessages(['email' => 'Email not exists']);
        }

        return $user;
    }

    public function getManager(): OtpTokenManagerService
    {
        if ($this->tokenManager) {
            return $this->tokenManager;
        }
        $this->tokenManager = new OtpTokenManagerService($this->email);

        return $this->tokenManager;
    }

    public function verify(): bool
    {
        DB::beginTransaction();
        try {
            if ($this->tokenManager->verified() && $this->tokenManager->revoked()) {
                DB::commit();

                return true;
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        DB::rollBack();

        return false;
    }
}
