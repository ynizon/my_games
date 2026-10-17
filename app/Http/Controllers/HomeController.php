<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\GameRepository;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{

    protected GameRepository $gameRepository;

    public function __construct(GameRepository $gameRepository)
    {
        $this->gameRepository = $gameRepository;
    }

    public function info()
    {
        $games = $this->gameRepository->get();
        return view('info', compact('games'));
    }

    public function index(Request $request)
    {
        if ($request->input("error") == "getcard" && isset($_COOKIE["getcard"])) {
            unset($_COOKIE["getcard"]);
        }

        $langs = config("app.langs");
        $user = Auth::user();
        $lang = config("app.locale");
        if ($user) {
            //On memorise sa date de connexion
            $user->logged_date = date("Y-m-d");
            $user->save();
            if (array_key_exists((string) $user->lang, $langs)) {
                setcookie('locale', $user->lang);
                $lang = $user->lang;
            }
        }

        //Change lang (seulement vers une langue proposee)
        $newLang = (string) $request->input("lang");
        if ($newLang != "" && array_key_exists($newLang, $langs)) {
            $_COOKIE["locale"] = $newLang;
            setcookie('locale', $newLang);
            if ($user) {
                $user->lang = $newLang;
                $user->save();
            }
            return redirect("/");
        }

        //Check locale
        if (!isset($_COOKIE["locale"]) || !array_key_exists((string) $_COOKIE["locale"], $langs)) {
            $_COOKIE["locale"] = config("app.locale");
            setcookie('locale', config("app.locale"));
            return redirect("/");
        } else {
            $lang = $_COOKIE["locale"];
        }

        //Redirection (uniquement vers une page du site)
        $redirect = (string) $request->get("redirect");
        if ($this->isLocalPath($redirect)) {
            return redirect($redirect);
        }

        $games = $this->gameRepository->get();
        App::setLocale($lang);

        return view('index', compact('games', 'lang'));
    }

    private function isLocalPath(string $path): bool
    {
        return str_starts_with($path, '/') && !str_starts_with($path, '//') && !str_contains($path, '\\');
    }
}
