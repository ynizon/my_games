{{-- Mise en page des erreurs : meme fond que l'accueil, sans Vite ni base de donnees pour s'afficher meme quand le site plante --}}
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="robots" content="noindex">
    <title>@yield('code') - {{ config('app.name') }}</title>

    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900" />
    <link href="{{ asset('assets') }}/css/material-dashboard.css?v=3.0.0" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fun.css') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="/images/favicon/favicon-32x32.png">
    <meta name="theme-color" content="#1e1b4b">

    <style>
        .error-code {
            font-size: 96px;
            font-weight: 900;
            line-height: 1;
            letter-spacing: -.04em;
            margin: 8px 0 4px;
            background: linear-gradient(90deg, #fff, #c7d2fe);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .error-card { text-align: center; padding-bottom: 20px; }
        .error-actions { display: flex; flex-wrap: wrap; gap: 10px; justify-content: center; margin-top: 8px; }
        .error-actions .btn { margin: 0; }
        .error-debug { text-align: left; font-size: 12.5px; word-break: break-word; color: var(--ko); }
    </style>
</head>
<body class="bg-gray-200 theme-home">
    <main class="main-content mt-0">
        <div class="page-header align-items-start min-vh-100 bgcustom">
            <span class="mask bg-gradient-dark opacity-6"></span>
            <div class="container my-auto home">
                <div class="row">
                    <div class="col-lg-5 col-md-8 col-12 mx-auto">
                        <header class="home-hero">
                            <div class="home-logo" aria-hidden="true">@yield('icon', '🎲')</div>
                            <div class="error-code">@yield('code')</div>
                            <p>@yield('title')</p>
                        </header>

                        <section class="info-card error-card">
                            <p>@yield('message')</p>
                            @yield('details')
                            <div class="error-actions">
                                <a class="btn btn-primary" href="/">Retour aux jeux</a>
                                <a class="btn btn-outline-secondary" href="javascript:history.back()">Page précédente</a>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
            <x-footers.guest></x-footers.guest>
        </div>
    </main>

    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/fun.js') }}"></script>
    <script>
        if (window.Fun && window.jQuery) {
            Fun.ambiance("home");
        }
    </script>
</body>
</html>
