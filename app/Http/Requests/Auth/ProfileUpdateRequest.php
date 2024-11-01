<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['string', 'max:255'],
            'email' => ['string', 'lowercase', 'email', 'max:255', Rule::unique('users')->whereNull('deleted_at')->ignore(Auth::id())],
            'phone' => ['nullable', 'string', 'max:20', Rule::unique('users')->where('phone_country_code', $this->phone_country_code)->whereNull('deleted_at')->ignore(Auth::id())],
            'phone_country_code' => ['nullable', 'string', 'max:5'],
            'country_code' => ['nullable', 'string', 'max:3'],
        ];
    }
}
