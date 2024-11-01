<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\RolesEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function rules(): array
    {
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            return $this->updateRules();
        }

        return [
            'name' => [
                'required',
                'string',
                'min:3',
            ],
            'email' => [
                'required',
                'string',
                'email',
                Rule::unique('users')
                    ->whereNull('deleted_at'),
            ],
            'phone_country_code' => 'nullable|string|max:6',
            'phone' => [
                'nullable',
                'string',
                'max:15',
                Rule::unique('users')
                    ->where('phone_country_code', $this->phone_country_code ?? '')
                    ->whereNull('deleted_at'),
            ],
            'country_code' => 'nullable|string|max:3',
            'role_id' => [
                'required',
                'integer',
                Rule::exists('roles', 'id')
                    ->whereNotIn('name', [RolesEnum::SUPERADMIN->value, RolesEnum::ADMIN->value]),
            ],
            'password' => ['required', 'confirmed', 'min:6', 'max:60'],
        ];
    }

    public function updateRules(): array
    {
        return [
            'name' => [
                'string',
                'min:3',
            ],
            'email' => [
                'string',
                'email',
                Rule::unique('users')
                    ->whereNull('deleted_at')
                    ->ignore($this->user->id),
            ],
            'phone_country_code' => 'nullable|string|max:6',
            'phone' => [
                'nullable',
                'string',
                'max:15',
                Rule::unique('users')
                    ->where('phone_country_code', $this->phone_country_code ?? '')
                    ->whereNull('deleted_at')
                    ->ignore($this->user->id),
            ],
            'country_code' => 'nullable|string|max:3',
            'role_id' => [
                'integer',
                Rule::exists('roles', 'id')
                    ->whereNotIn('name', [RolesEnum::SUPERADMIN->value, RolesEnum::ADMIN->value]),
            ],
        ];
    }

    public function messages()
    {
        return [
            'role_id' => 'Role is not exists',
        ];
    }
}
