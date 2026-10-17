@extends('layouts.app')

@section('content')
    @php
        // Loup-Garou en dernier, les autres jeux par ordre alphabetique
        $WEREWOLF = \App\Http\Controllers\PlayController::WEREWOLF;
        $homeGames = $games->filter(fn ($game) => $game->status == 1)
            ->sortBy(fn ($game) => (Helper::gameSlug($game->name) == $WEREWOLF ? '1' : '0').$game->name)
            ->values();
    @endphp
    <main class="main-content mt-0">
        <div class="page-header align-items-start min-vh-100 bgcustom">
            <span class="mask bg-gradient-dark opacity-6"></span>
            <div class="container my-auto home">
                <div class="row">
                    <div class="col-lg-5 col-md-8 col-12 mx-auto">
                        <div class="home-top">
                            <select class="home-lang" id="mylang" aria-label="Langue" onchange="window.location='/?lang='+this.value+'&redirect=/'">
                                @foreach (config("app.langs") as $code => $langtmp)
                                    <option value="{{$code}}" @if ($lang == $code) selected @endif >{{$langtmp}}</option>
                                @endforeach
                            </select>
                        </div>

                        <header class="home-hero">
                            <div class="home-logo" aria-hidden="true">🎲</div>
                            <h1>{{ config('app.name') }}</h1>
                            <p>{{ __("messages.Tagline") }}</p>
                        </header>

                        <div id="loader" class="home-loader">
                            <span class="home-spinner" aria-hidden="true"></span>
                            {{ __("messages.LoadingCards") }}
                        </div>

                        <nav id="allgames" class="home-games" aria-label="{{ __("messages.select-game") }}" style="display:none">
                            <h2>{{ __("messages.select-game") }}</h2>
                            @foreach ($homeGames as $game)
                                @php($slug = Helper::gameSlug($game->name))
                                <a class="game-tile tile-{{ $slug }}" href="/{{ $slug }}/settings">
                                    <span class="game-tile__icon" aria-hidden="true">{{ Helper::gameIcon($game->name) }}</span>
                                    <span class="game-tile__body">
                                        <span class="game-tile__name">{{ $game->name }}</span>
                                        @if (Lang::has("messages.desc_".$slug))
                                            <span class="game-tile__desc">{{ __("messages.desc_".$slug) }}</span>
                                        @endif
                                    </span>
                                    <span class="game-tile__arrow" aria-hidden="true">›</span>
                                </a>
                            @endforeach
                        </nav>

                        <p class="home-help">
                            <?php echo __("messages.HelpMe");?> <a href="mailto:ynizon@gmail.com">ynizon@gmail.com</a>.<br/>
                            <?php echo __("messages.Info");?>.
                        </p>
                    </div>
                </div>
            </div>
            <x-footers.guest></x-footers.guest>
        </div>
    </main>
    <script>
        // Les cartes sont gardees dans le navigateur (IndexedDB) pour jouer sans connexion
        var stores = @json($games->mapWithKeys(fn ($game) => [$game->id => Helper::gameSlug($game->name)]));
        var cardsVersion = @json(Helper::cardsVersion($lang));

        Fun.ambiance("home");

        function showGames() {
            $("#loader").hide();
            $("#allgames").fadeIn("fast");
        }

        function storeCards(arrAllCards) {
            var oIndexedDB = window.indexedDB || window.mozIndexedDB || window.webkitIndexedDB || window.msIndexedDB || window.shimIndexedDB;

            // On repart d'une base vide, puis on attend la fin de la suppression avant de la recreer
            var deleteRequest = oIndexedDB.deleteDatabase("MyGames");
            deleteRequest.onerror = deleteRequest.onsuccess = function () {
                var open = oIndexedDB.open("MyGames", 1);

                open.onupgradeneeded = function () {
                    var db = open.result;
                    Object.values(stores).forEach(function (sStore) {
                        if (!db.objectStoreNames.contains(sStore)) {
                            db.createObjectStore(sStore, {keyPath: "id"});
                        }
                    });
                };

                open.onerror = showGames;
                open.onsuccess = function () {
                    var db = open.result;
                    var tx = db.transaction(Object.values(stores), "readwrite");
                    arrAllCards.forEach(function (card) {
                        var sStore = stores[card.game_id];
                        if (sStore !== undefined) {//Carte d'un jeu inconnu : ignoree
                            tx.objectStore(sStore).put(card);
                        }
                    });

                    // Les jeux ne sont proposes qu'une fois toutes les cartes enregistrees
                    tx.oncomplete = function () {
                        db.close();
                        setCookie("getcard", "ok", 90);
                        setCookie("cardsversion", cardsVersion, 90);
                        showGames();
                    };
                    tx.onerror = tx.onabort = function () {
                        db.close();
                        eraseCookie("getcard");
                        showGames();
                    };
                };
            };
        }

        if (getCookie("getcard") == null || getCookie("cardsversion") != cardsVersion) {
            $.getJSON("/cards/getall?lang=" + encodeURIComponent($("#mylang").val()) + "&game_id=0", storeCards)
                .fail(showGames);
        } else {
            showGames();
        }
    </script>
@endsection
