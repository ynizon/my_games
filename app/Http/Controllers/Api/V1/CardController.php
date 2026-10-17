<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CardRequest;
use App\Http\Resources\CardResource;
use App\Models\Card;
use App\Repositories\CardRepository;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Cartes proposees par l'utilisateur connecte (memes droits que sur le site).
 */
class CardController extends Controller
{
    protected CardRepository $cardRepository;

    public function __construct(CardRepository $cardRepository)
    {
        $this->cardRepository = $cardRepository;
    }

    public function index(Request $request)
    {
        $this->checkPermission($request);
        $cards = $this->cardRepository->getMyCards();
        if ($request->filled('game_id')) {
            $cards = $cards->where('game_id', (int) $request->input('game_id'))->values();
        }

        return CardResource::collection($cards);
    }

    public function show(Request $request, $id)
    {
        $card = $this->cardRepository->getById($id);
        $this->checkPermission($request, $card);

        return new CardResource($card);
    }

    public function store(CardRequest $request)
    {
        $this->checkPermission($request);
        $card = $this->cardRepository->store($this->inputs($request));

        return (new CardResource($card->refresh()))->response()->setStatusCode(201);
    }

    public function update(CardRequest $request, $id)
    {
        $card = $this->cardRepository->getById($id);
        $this->checkPermission($request, $card);
        $this->cardRepository->update($id, $this->inputs($request, $card));

        return new CardResource($card->refresh());
    }

    public function destroy(Request $request, $id)
    {
        $this->checkPermission($request, $this->cardRepository->getById($id));
        $this->cardRepository->destroy($id);

        return response()->noContent();
    }

    /**
     * Cartes au nom proche, pour eviter les doublons avant de proposer une carte.
     */
    public function checkDouble(Request $request)
    {
        $this->checkPermission($request);
        $attributes = $request->validate([
            'name' => 'required|string|max:255',
            'game_id' => 'required|integer',
            'lang' => ['required', Rule::in(array_keys(config('app.langs')))],
        ]);

        return CardResource::collection(
            $this->cardRepository->checkDouble($attributes['lang'], $attributes['game_id'], $attributes['name'])
        );
    }

    /**
     * Champs valides. Les mots ("words") sont ranges dans la description JSON de la carte.
     * Une carte proposee par un utilisateur repasse en attente de validation (status = 0).
     */
    private function inputs(CardRequest $request, ?Card $card = null): array
    {
        $request->validate(['words' => 'nullable|array', 'words.*' => 'nullable|string|max:255']);
        $inputs = $request->validated();

        $nbWords = Card::WORDS_BY_GAME[$inputs['game_id']] ?? 0;
        if ($nbWords == 0) {
            $inputs['description'] = "[]";
        } elseif ($request->has('words')) {
            $description = json_decode((string) $card?->description, true);
            $description = is_array($description) ? $description : array();
            $words = array_values((array) $request->input('words'));
            for ($k = 1; $k <= $nbWords; $k++) {
                $description["word".$k] = (string) ($words[$k - 1] ?? "");
            }
            $description += ["description" => ""];
            $inputs['description'] = json_encode($description);
        }

        if (!$request->user()->can("card-edit")) {
            $inputs['status'] = 0;
        }

        return $inputs;
    }

    /**
     * card-edit : gere toutes les cartes ; card-propose : uniquement les siennes.
     */
    private function checkPermission(Request $request, ?Card $card = null)
    {
        $user = $request->user();
        if ($user->can("card-edit")) {
            return;
        }

        if (!$user->can("card-propose")) {
            abort(403);
        }

        if ($card !== null && $card->user_id != $user->id) {
            abort(403);
        }
    }
}
