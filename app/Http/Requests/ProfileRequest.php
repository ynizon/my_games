<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'nickname' => 'nullable|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('gam_users', 'email')->ignore($this->user()->id)],
            'password' => 'nullable|string|min:5|max:255',
            'lang' => ['nullable', Rule::in(array_keys(config('app.langs')))],
        ];
    }
}
