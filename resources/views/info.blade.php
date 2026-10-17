@extends('layouts.app')

@section('content')
    @php
        // Meme ordre et memes icones que l'accueil : Loup-Garou en dernier
        $WEREWOLF = \App\Http\Controllers\PlayController::WEREWOLF;
        $infoGames = $games->filter(fn ($game) => $game->status == 1)
            ->sortBy(fn ($game) => (Helper::gameSlug($game->name) == $WEREWOLF ? '1' : '0').$game->name)
            ->values();
    @endphp
    <main class="main-content mt-0">
        <div class="page-header align-items-start min-vh-100 bgcustom">
            <span class="mask bg-gradient-dark opacity-6"></span>
            <div class="container my-auto home">
                <div class="row">
                    <div class="col-lg-6 col-md-9 col-12 mx-auto">
                        <div class="home-top home-top--start">
                            <a class="home-back" href="/">‹ {{ __("messages.Back") }}</a>
                        </div>

                        <header class="home-hero">
                            <div class="home-logo home-logo--still" aria-hidden="true">📖</div>
                            <h1>Informations</h1>
                        </header>

                        <section class="info-card">
                            <h2>{{ __("messages.GameRules") }}</h2>
                            @foreach ($infoGames as $game)
                                @php($slug = Helper::gameSlug($game->name))
                                <a class="info-game" href="/{{ $slug }}/settings">
                                    <span class="info-game__icon tile-{{ $slug }}" aria-hidden="true">{{ Helper::gameIcon($game->name) }}</span>
                                    <span>
                                        <strong>{{ $game->name }}</strong>
                                        <span class="info-game__desc">{{ __("messages.description_".strtolower($game->name)) }}</span>
                                    </span>
                                </a>
                            @endforeach
                        </section>

                        <section class="info-card">
                            <h2><span aria-hidden="true">📶</span> {{ __("messages.play_disconnected") }}</h2>
                            <p>{{ __("messages.play_disconnected_explain") }}</p>
                        </section>

                        <section class="info-card">
                            <h2><span aria-hidden="true">💜</span> {{ __("messages.SupportMe") }}</h2>
                            <p>{!! __("messages.SupportMe_explain") !!}</p>
                        </section>

                        <section class="info-card">
                            <h2><span aria-hidden="true">©️</span> {{ __("messages.Copyright") }}</h2>
                            <p>
                                {!! __("messages.Copyright_explain") !!}
                                <a href="https://github.com/ynizon/my_games">github.com/ynizon/my_games</a>.
                            </p>
                            <p>{{ __("messages.Credits") }}</p>
                        </section>
                    </div>
                </div>
            </div>
            <x-footers.guest></x-footers.guest>
        </div>
    </main>
    <script>
        Fun.ambiance("home");
    </script>
@endsection
