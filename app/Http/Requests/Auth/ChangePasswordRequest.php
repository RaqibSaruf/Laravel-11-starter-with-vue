<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ChangePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'current_password' => [
                'required',
                'min:6',
                function ($attribute, $value, $fail) {
                    if (!password_verify($value, Auth::user()->password)) {
                        $fail("The  $attribute is invalid");
                    }
                },
            ],
            'password' => 'required|string|confirmed|min:6|max:60',
        ];
    }
}
