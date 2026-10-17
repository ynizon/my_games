<?php

namespace App\Services;

use App\Helpers\Helper;
use App\Repositories\CardRepository;
use Illuminate\Support\Collection;

/**
 * Cartes et distribution du Loup-Garou de Thiercelieux (site web et API mobile).
 */
class WerewolfDeck
{
    public const GAME_ID = 4;
    // Cartes ajoutees d'office (non selectionnables)
    public const VILLAGER = "Villageois";
    public const WOLF = "Loup Garou";
    // Une seule carte selectionnee, mais 5 joueurs (2 soeurs + 3 freres)
    private const SIBLINGS = ["Frères et Soeurs" => 5];
    // Roles speciaux qui sont aussi des loups
    private const SPECIAL_WOLVES = ["Loup Garou Blanc", "Infect pere des loups", "Grand mechant loup"];

    protected CardRepository $cardRepository;

    public function __construct(CardRepository $cardRepository)
    {
        $this->cardRepository = $cardRepository;
    }

    /**
     * Cartes actives du Loup-Garou, avec leur extension, le nom de leur image
     * et leurs regles de distribution (role, nombre d'exemplaires, loup ou non).
     */
    public function cards($locale): Collection
    {
        return $this->cardRepository->getForLangAndGame($locale, self::GAME_ID)
            ->sortBy('id')
            ->values()
            ->each(function ($card) {
                $description = json_decode((string) $card->description, true);
                $card->extension = is_array($description) && !empty($description["word1"]) ? $description["word1"] : "Standard";
                $card->image = "/images/wolf_".Helper::slugify($card->name).".png";
                $key = "messages.Loup ".$card->name;
                $card->help = __($key) === $key ? "" : __($key);
                $card->role = match ($card->name) {
                    self::VILLAGER => "villager",
                    self::WOLF => "wolf",
                    default => "special",
                };
                $card->copies = self::SIBLINGS[$card->name] ?? 1;
                $card->is_wolf = $card->name == self::WOLF || in_array($card->name, self::SPECIAL_WOLVES);
            });
    }

    /**
     * Roles speciaux que l'on peut choisir dans les reglages.
     */
    public function selectableCards($locale): Collection
    {
        return $this->cards($locale)->where('role', 'special')->values();
    }

    /**
     * Paquet distribue aux joueurs : roles speciaux choisis + loups + villageois.
     * Renvoie null s'il y a plus de roles que de joueurs.
     */
    public function build($locale, int $nbplayers, int $nbwolfs, array $selectedIds): ?array
    {
        $cards = $this->cards($locale)->keyBy('id');
        $villager = $cards->firstWhere('role', 'villager');
        $wolf = $cards->firstWhere('role', 'wolf');
        if (!$villager || !$wolf) {
            abort(404);
        }

        $selectedIds = array_unique(array_map('intval', $selectedIds));
        $deck = array();
        $nbWolvesTotal = $nbwolfs;
        foreach ($selectedIds as $id) {
            $card = $cards->get($id);
            if (!$card || $card->role != 'special') {
                continue;
            }
            for ($i = 0; $i < $card->copies; $i++) {
                $deck[] = $card;
            }
            if ($card->is_wolf) {
                $nbWolvesTotal++;
            }
        }

        if (count($deck) + $nbwolfs > $nbplayers) {
            return null;
        }

        for ($i = 0; $i < $nbwolfs; $i++) {
            $deck[] = $wolf;
        }
        while (count($deck) < $nbplayers) {
            $deck[] = $villager;
        }

        $deck = array_map(function ($card) {
            return ['id' => $card->id, 'name' => $card->name, 'image' => $card->image, 'help' => $card->help];
        }, $deck);

        return [
            'deck' => $deck,
            'nbWolvesTotal' => $nbWolvesTotal,
            'hasThief' => collect($deck)->contains('name', 'Voleur'),
        ];
    }
}
