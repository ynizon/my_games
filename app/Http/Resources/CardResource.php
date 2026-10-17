<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $description = json_decode((string) $this->description, true);

        return [
            'id' => $this->id,
            'game_id' => $this->game_id,
            'name' => $this->name,
            'lang' => $this->lang,
            'country' => $this->country,
            'difficulty' => $this->difficulty,
            // Mots associes (Brainstorm, Taboo, extension du Loup-Garou), deja decodes
            'words' => array_values(array_filter(
                is_array($description) ? $description : array(),
                fn ($value, $key) => str_starts_with((string) $key, 'word') && $value !== '' && $value !== null,
                ARRAY_FILTER_USE_BOTH
            )),
            'status' => $this->status,
            // Champs calcules pour le Loup-Garou
            'extension' => $this->when(isset($this->extension), fn () => $this->extension),
            'image' => $this->when(isset($this->image), fn () => url($this->image)),
            'help' => $this->when(isset($this->help), fn () => $this->help),
            'role' => $this->when(isset($this->role), fn () => $this->role),
            'copies' => $this->when(isset($this->copies), fn () => $this->copies),
            'is_wolf' => $this->when(isset($this->is_wolf), fn () => $this->is_wolf),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
