<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleRequest extends FormRequest
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
                'unique:roles,name',
            ],
            'permissions' => [
                'required',
                'array',
                Rule::exists('permissions', 'name'),
            ],
            'permissions.*' => 'required|string',
        ];
    }

    public function updateRules(): array
    {
        return [
            'name' => [
                'string',
                Rule::unique('roles')->ignore($this->role->id),
            ],
            'permissions' => [
                'array',
                Rule::exists('permissions', 'name'),
            ],
            'permissions.*' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'permissions' => 'Permissions are required',
        ];
    }
}
