<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'nickname' => $this->nickname,
            'email' => $this->email,
            'lang' => $this->lang,
            'roles' => $this->roles->pluck('name'),
            'permissions' => [
                'card_edit' => $this->can('card-edit'),
                'card_propose' => $this->can('card-propose'),
            ],
        ];
    }
}
