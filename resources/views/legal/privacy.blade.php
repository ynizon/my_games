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
                            <div class="home-logo home-logo--still" aria-hidden="true">🔒</div>
                            <h1>Règles de confidentialité</h1>
                        </header>

                        <section class="info-card">
                            <h2>Qui sommes-nous ?</h2>
                            <p>
                                Le site games.gameandme.fr et l'application Android « MyGames »
                                (fr.gameandme.games) sont édités à titre personnel par Yohann Nizon.
                                Contact : <a href="mailto:ynizon@gmail.com">ynizon@gmail.com</a>.
                            </p>
                        </section>

                        <section class="info-card">
                            <h2>Jouer sans compte</h2>
                            <p>
                                Les jeux sont utilisables sans compte. Aucune donnée personnelle n'est alors collectée :
                                l'application télécharge seulement la liste des jeux et des cartes.
                            </p>
                        </section>

                        <section class="info-card">
                            <h2>Données du compte</h2>
                            <p>Un compte est facultatif et sert uniquement à proposer vos propres cartes. Nous conservons :</p>
                            <ul>
                                <li>votre nom, votre pseudo (facultatif) et votre adresse e-mail ;</li>
                                <li>votre mot de passe, chiffré (jamais lisible) ;</li>
                                <li>votre langue et la date de votre dernière connexion ;</li>
                                <li>le nom de l'appareil connecté (par exemple « Samsung SM-A515F »), associé à votre jeton de connexion ;</li>
                                <li>les cartes que vous proposez.</li>
                            </ul>
                            <p>
                                Ces données servent uniquement au fonctionnement du compte et à la validation des cartes.
                                Elles ne sont ni vendues, ni partagées, ni utilisées à des fins publicitaires.
                                L'adresse e-mail sert seulement à la réinitialisation du mot de passe.
                            </p>
                        </section>

                        <section class="info-card">
                            <h2>Hébergement et sécurité</h2>
                            <p>
                                Les données sont hébergées en France chez OVHcloud. Les échanges entre l'application et le serveur
                                sont chiffrés (HTTPS).
                            </p>
                        </section>

                        <section class="info-card">
                            <h2>Cookies et mesure d'audience</h2>
                            <p>
                                Le site utilise des cookies techniques (session, sécurité des formulaires, langue choisie)
                                et peut utiliser Google Analytics pour mesurer sa fréquentation.
                                L'application Android n'utilise ni publicité, ni outil de mesure d'audience.
                            </p>
                        </section>

                        <section class="info-card">
                            <h2>Durée de conservation et suppression</h2>
                            <p>
                                Les données sont conservées tant que le compte existe. Vous pouvez le supprimer à tout moment
                                depuis l'application (Mon compte), depuis votre profil sur le site, ou en écrivant à
                                <a href="mailto:ynizon@gmail.com">ynizon@gmail.com</a>.
                                <a href="/suppression-compte">Comment supprimer mon compte</a>.
                            </p>
                        </section>

                        <section class="info-card">
                            <h2>Vos droits</h2>
                            <p>
                                Conformément au RGPD, vous pouvez accéder à vos données, les corriger ou les effacer
                                en écrivant à <a href="mailto:ynizon@gmail.com">ynizon@gmail.com</a>.
                                Vous pouvez aussi saisir la CNIL (<a href="https://www.cnil.fr">cnil.fr</a>).
                            </p>
                            <p>Dernière mise à jour : 17 octobre 2026.</p>
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
