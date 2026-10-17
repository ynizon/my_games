<?php

namespace App\Http\Resources;

use App\Helpers\Helper;
use App\Http\Controllers\PlayController;
use App\Models\Card;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $slug = Helper::gameSlug($this->name);
        $params = PlayController::GAMES[$slug]['params'] ?? array();

        return [
            'id' => $this->id,
            'slug' => $slug,
            'name' => $this->name,
            'description' => $this->description,
            // Nombre de mots a saisir sur une carte de ce jeu (0 : aucun)
            'nb_words' => Card::WORDS_BY_GAME[$this->id] ?? 0,
            // Bornes des reglages de partie : ['nbteams' => ['min' => 2, 'max' => 4], ...]
            'settings' => array_map(fn ($bounds) => ['min' => $bounds[0], 'max' => $bounds[1]], $params),
        ];
    }
}
