<?php

namespace App\Http\Controllers;

use App\Http\Requests\GameRequest;
use App\Repositories\GameRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GameController extends Controller
{
    protected GameRepository $gameRepository;

    public function __construct(GameRepository $gameRepository)
    {
        $this->gameRepository = $gameRepository;
    }

    public function index()
    {
        $this->checkPermission();

        $games = $this->gameRepository->get();

        return view('game/index', compact('games'));
    }

    public function create()
    {
        $this->checkPermission();

        return view('game/create');
    }

    public function show($id)
    {
        return redirect('/games/'.$id."/edit");
    }

    public function edit($id)
    {
        $this->checkPermission();

        $game = $this->gameRepository->getById($id);

        return view('game/edit', compact('game'));
    }

    public function update(GameRequest $request, $id)
    {
        $this->gameRepository->update($id, $request->validated());

        return redirect('/games')->withOk("Le jeu " . $request->input('name') . " a été modifié.");
    }

    public function destroy($id)
    {
        $this->checkPermission();

        $this->gameRepository->destroy($id);
        return redirect()->back();
    }

    public function store(GameRequest $request)
    {
        try {
            $this->gameRepository->store($request->validated());

            return redirect('/games')->withOk("Le jeu " . $request->input('name') . " a été créé.");
        } catch (\Exception $e) {
            Log::error($e);
            return redirect('/games')->withError("Le jeu " . $request->input('name') . " n'a pas pu être créé.");
        }
    }

    private function checkPermission()
    {
        if (!Auth::user()->can("game-edit")) {
            abort(403);
        }
    }
}
