<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('user-edit');
    }

    public function rules(): array
    {
        $id = $this->route('user');

        return [
            'name' => 'required|string|max:255',
            'nickname' => 'nullable|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('gam_users', 'email')->ignore($id)],
            'password' => [$id ? 'nullable' : 'required', 'string', 'min:5', 'max:255'],
            'status' => 'nullable|in:0,1',
            'role' => ['nullable', Rule::in(array_keys(config('app.users_roles')))],
        ];
    }
}
