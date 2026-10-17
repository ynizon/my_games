@extends('layouts.app')

@section('content')
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
                            <div class="home-logo home-logo--still" aria-hidden="true">🗑️</div>
                            <h1>Supprimer mon compte</h1>
                        </header>

                        @if (session('deleted'))
                            <section class="info-card">
                                <h2>✅ Compte supprimé</h2>
                                <p>Votre compte et vos données ont été supprimés.</p>
                            </section>
                        @endif

                        <section class="info-card">
                            <h2>Depuis l'application Android</h2>
                            <p>
                                Ouvrez l'application « MyGames », allez dans <strong>Mon compte</strong>, puis
                                <strong>Supprimer mon compte</strong> et confirmez avec votre mot de passe.
                            </p>
                        </section>

                        <section class="info-card">
                            <h2>Depuis le site</h2>
                            <p>
                                <a href="/sign-in">Connectez-vous</a>, ouvrez votre <a href="/profile">profil</a>,
                                puis <strong>Supprimer mon compte</strong> en bas de la page.
                            </p>
                        </section>

                        <section class="info-card">
                            <h2>Par e-mail</h2>
                            <p>
                                Si vous ne pouvez plus vous connecter, écrivez à
                                <a href="mailto:ynizon@gmail.com?subject=Suppression%20de%20compte%20MyGames">ynizon@gmail.com</a>
                                depuis l'adresse de votre compte. La suppression est faite sous 30 jours.
                            </p>
                        </section>

                        <section class="info-card">
                            <h2>Ce qui est supprimé</h2>
                            <ul>
                                <li>votre nom, pseudo, e-mail, mot de passe et préférences : supprimés immédiatement et définitivement ;</li>
                                <li>vos connexions (sur tous les appareils) et vos cartes en attente de validation : supprimées ;</li>
                                <li>vos cartes déjà validées : conservées dans les jeux, mais sans votre nom ni lien avec vous.</li>
                            </ul>
                            <p><a href="/confidentialite">Règles de confidentialité</a></p>
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
