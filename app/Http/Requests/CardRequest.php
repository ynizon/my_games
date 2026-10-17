<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CardRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Les droits sur la carte sont verifies dans CardController
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'game_id' => 'required|integer|exists:gam_games,id',
            'lang' => ['required', Rule::in(array_keys(config('app.langs')))],
            'country' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'difficulty' => 'nullable|in:1,2,3',
            'status' => 'nullable|in:0,1',
        ];
    }
}
