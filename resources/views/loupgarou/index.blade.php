@extends('layouts.app')

@section('content')
    <main class="main-content  mt-0">
        <div class="page-header align-items-start min-vh-100 bgcustom">
            <span class="mask bg-gradient-dark opacity-6"></span>
            <div class="container my-auto">
                <div class="row">
                    <div class="col-lg-4 col-md-8 col-12 mx-auto">
                        <div class="card z-index-0 fadeIn3 fadeInBottom">
                            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                                <div class="bg-gradient-primary shadow-primary border-radius-lg py-3 pe-1">
                                    <h4 class="text-white font-weight-bolder text-center mt-2 mb-0">
                                        {{ $gameName }}
                                    </h4>
                                </div>
                            </div>

                            <div class="card-body">
                                <div id="intro" class="intro">
                                    {{ __("messages.There is") . " ". $nbWolvesTotal." ". __("messages.Wolfs")}}.<br/>
                                    @if ($hasThief)
                                        {{ __("messages.ThiefNote") }}<br/>
                                    @endif
                                    {{ __("messages.CardSelect")}}:<br/>
                                    <ul id="list" class="list-group">

                                    </ul>
                                    <br/>
                                    <input onclick="nextCard()" type="button" value="{{ __("messages.DiscoverRoles") }}" class="btnstep btn btn-primary" />
                                </div>

                                <div id="game" class="brainstorm inv">
                                    <div style="margin:10px;">
                                        <h3>{{ __("messages.Player")}} <span class="step">X</span></h3>
                                        <p id="passphone"></p>
                                        <!-- Carte de role : dos mysterieux, retournee quand le joueur la touche -->
                                        <div id="rolecard" class="fun-flip" onclick="revealCard()">
                                            <div class="fun-flip-inner">
                                                <div class="fun-flip-face fun-flip-back">
                                                    <span class="fun-moon-small">🌕</span>
                                                    <span class="fun-wolf">🐺</span>
                                                    <span>{{ __("messages.TapToReveal") }}</span>
                                                </div>
                                                <div class="fun-flip-face fun-flip-front">
                                                    <img id="cardpic" src="" alt="" />
                                                    <span id="cardname" class="fun-role">-</span>
                                                </div>
                                            </div>
                                        </div>
                                        <p id="carddesc" class="inv"></p>
                                    </div>
                                    <input id="btnrole" onclick="roleButton()" type="button" value="" class="btnstep btn btn-primary" />
                                </div>

                                <div id="endinggame" class="inv brainstorm">
                                    <div>
                                        <span class="fun-howl" aria-hidden="true">🐺</span>
                                        <h3>{{ __("messages.This is the night")}}</h3>

                                        <p>
                                            {{ __("messages.Master Take Control")}}
                                            <br/>
                                            <span id="master"></span><br/>
                                            <img src="/images/wolf_capitaine.png" /><br/>
                                        </p>
                                    </div>

                                    <input onclick="window.location.reload();" type="button" value="{{ __("messages.PlayAgain") }}" class="btnstep btn btn-primary" />
                                    &nbsp;&nbsp;&nbsp;<a class='btn btn-primary' href='/'>{{ __("messages.Home")}}</a>
                                </div>
                            </div>
                            <div id="footer">
                                <a onclick="if (window.confirm('{{ str_replace("'","\'",__("messages.back_to_homepage"))}} ?')){window.location.href='/';}" >{{ __("messages.back_to_homepage")}}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Paquet construit par le serveur (roles choisis + loups + villageois), deja valide
        var arrCardsForThisMatch = @json($deck);
        var arrCardsForThisGame = [];
        var oCurrentCard = null;
        var step = 0;
        var bRevealed = false;
        var T = {
            passPhone: @json(__("messages.PassPhone")),
            youPlay: @json(__("messages.You play this role")),
            tapToReveal: @json(__("messages.TapToReveal")),
            hideMyRole: @json(__("messages.HideMyRole")),
            endDeal: @json(__("messages.EndDeal"))
        };

        Fun.ambiance("loupgarou");

        $(document).ready(function () {
            var sList = $("#list");
            arrCardsForThisMatch.forEach(function (item) {
                sList.append(
                    $('<li class="list-group-item"></li>')
                        .append($('<img />').attr("src", item.image))
                        .append(document.createTextNode(" " + item.name))
                );
            });

            var iCaptain = Math.floor(Math.random() * arrCardsForThisMatch.length) + 1;
            $("#master").text(@json(__("messages.The captain is")) + " " + iCaptain);
            startGame();
        });

        // Bouton "Decouvrir les roles" : premier joueur
        function nextCard() {
            if (arrCardsForThisGame.length == 0) {
                return;
            }
            $("#intro").slideUp();
            $("#game").show();
            playSound('/sounds/ok.mp3');
            showCard();
        }

        // Joueur suivant : carte face cachee
        function showCard() {
            oCurrentCard = arrCardsForThisGame.shift();
            step++;
            bRevealed = false;
            $(".step").text(step);
            $("#passphone").text(T.passPhone.replace(":player", step));
            $("#carddesc").addClass("inv").text(oCurrentCard.help || "");
            $("#btnrole").val(T.tapToReveal);

            // La carte revient sur le dos sans animation : le role suivant ne doit jamais apparaitre
            var oCard = $("#rolecard");
            oCard.find(".fun-flip-inner").css("transition", "none");
            oCard.removeClass("revealed");
            $("#cardname").text(oCurrentCard.name);
            $("#cardpic").attr("src", oCurrentCard.image);
            void oCard[0].getBoundingClientRect();
            oCard.find(".fun-flip-inner").css("transition", "");
        }

        function revealCard() {
            if (bRevealed || oCurrentCard === null) {
                return;
            }
            bRevealed = true;
            Fun.sound("flip");
            $("#rolecard").addClass("revealed");
            $("#passphone").text(T.youPlay.trim());
            setTimeout(function () {
                $("#carddesc").removeClass("inv").hide().fadeIn("slow");
            }, 500);
            $("#btnrole").val(arrCardsForThisGame.length > 0 ? T.hideMyRole : T.endDeal);
        }

        function roleButton() {
            if (!bRevealed) {
                revealCard();
            } else if (arrCardsForThisGame.length > 0) {
                playSound('/sounds/ok.mp3');
                showCard();
            } else {
                endSet();
            }
        }

        function endSet() {
            oCurrentCard = null;
            $("#game").slideUp("slow");
            $("#endinggame").show();
            Fun.sound("howl");
        }

        function startGame() {
            step = 0;
            $("#intro").show();
            arrCardsForThisGame = arrCardsForThisMatch.slice();
            shuffle(arrCardsForThisGame);
        }
    </script>
@endsection
