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
                                <div id="intro" class="intro slider">
                                    <h3>{{ __("messages.Lookfor")}} <span class="steph3"><span class="step">1</span>/{{ $nbsets}}</span></h3>
                                    <p>{!! __("messages.goal_taboo")!!}.
                                    </p>
                                    <br/>
                                    <input id="btnstart" onclick="startSet()" type="button" value="{{ __("messages.Team 1 Start")}}" class="btnstep btn btn-primary" />
                                </div>

                                <div id="game" class="taboo inv">
                                    <div class="slide-container">
                                        <div class="wrapper">
                                            <div class="clash-card barbarian">
                                                <div class="clash-card__noimage">
                                                    <input type="hidden" id="progress" value="" />
                                                    <div id="chrono" >0</div>
                                                </div>
                                                <div class="clash-card__level clash-card__level--barbarian">{{ __("messages.Remaining cards")}} : <span id="nbcards" >0</span></div>
                                                <div class="clash-card__unit-name">
                                                    <span id="cardname">-</span>
                                                </div>
                                                <div class="clash-card__unit-description">
                                                    <div style="margin:10px;">
                                                        <ul id="cardwords" class="form-check">
                                                        </ul>
                                                    </div>
                                                </div>

                                                <div class="clash-card__unit-stats clash-card__unit-stats--barbarian clearfix">
                                                    <div class="one-third">
                                                        <i class="fa fa-close pointer btnplay arrondicancel" id="cancel" onclick="nextCard(false)"></i>

                                                        <div class="stat-value">{{ __("messages.MISSED")}} (-1)</div>
                                                    </div>

                                                    <div class="one-third">
                                                        <span id="spanpause" class="inv" ><i onclick="pause()" id="btn_pause" class="fa fa-pause pointer" ></i></span>

                                                        <div class="stat-value">{{ __("messages.PAUSE")}}</div>
                                                    </div>

                                                    <div class="one-third no-border">
                                                        <i class="fa fa-check pointer btnplay arrondivalidate" id="validate" onclick="nextCard(true)"></i>

                                                        <div class="stat-value">{{ __("messages.OK")}} (+1)</div>
                                                    </div>

                                                </div>

                                            </div> <!-- end clash-card barbarian-->
                                        </div> <!-- end wrapper -->
                                    </div> <!-- end container -->
                                </div>

                                <div id="endinggame" class="inv taboo">
                                    <div>
                                        <h3>{{ __("messages.Set")}} <span class="step"></span></h3>
                                        <h4>{{ __("messages.Lookfor")}} !</h4>
                                        <p><span id="score">0</span> {{ __("messages.sentences_found")}}<br/>
                                        <ul id="list" class="list-group">

                                        </ul>
                                        </p>

                                    </div>

                                    <input id="btnnextteam" onclick="initGame()" type="button" value="{{ __("messages.NextTeam") }}" class="btnstep btnstepend btn btn-primary" />
                                </div>

                                <div id="endingset" class="inv taboo">
                                    <div>
                                        <h3>{{ __("messages.Set")}} <span class="step"></span></h3>
                                        <h4 id="finishset">{{ __("messages.Endset")}}</h4>
                                        <table class="table table-striped">
                                            <thead>
                                            <tr>
                                                <td><b>{{ __("messages.Teams")}}</b></td>
                                                <td><b>1</b></td>
                                                <td><b>2</b></td>
                                                <td class="player3"><b>3</b></td>
                                                <td class="player4"><b>4</b></td>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td>Total</td>
                                                <td id="total-1">0</td>
                                                <td id="total-2">0</td>
                                                <td class="player3" id="total-3">0</td>
                                                <td class="player4" id="total-4">0</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div id="finish">
                                        <input id="btnnextset" onclick="nextSet()" type="button" value="{{ __("messages.Next Set")}}" class="btnstep btn btn-primary" />
                                    </div>

                                </div>

                                <script>
                                    var nbTeam = {{ $nbteams }};
                                    var nbSets = {{ $nbsets }};
                                    var iTimeLimit = 30;
                                    var NB_WORDS = 5;
                                    var TXT = {
                                        teamGo: @json(__("messages.TeamGo")),
                                        endSet: @json(__("messages.Endset")),
                                        draw: @json(__("messages.Draw game")),
                                        congratulations: @json(__("messages.Congratulations Team")),
                                        playAgain: @json(__("messages.Play again")),
                                        home: @json(__("messages.Home")),
                                        noCards: @json(__("messages.NoCardsLoaded"))
                                    };

                                    // Etat de la partie : loading, intro, playing, turnEnd, setEnd, finished
                                    var sState = "loading";
                                    var step = 0;               // manche affichee
                                    var iSet = 0;               // manches terminees
                                    var iTeam = 1;              // equipe qui joue
                                    var iLastTeam = 1;          // equipe du dernier tour (pour les corrections)
                                    var arrTotals = [0, 0, 0, 0];
                                    var arrDeck = [];           // cartes pas encore jouees dans le match
                                    var arrTurn = [];           // pioche du tour en cours
                                    var oCurrentCard = null;
                                    var arrCardsTeam = [];      // cartes du tour : {card, find, delta}
                                    var bClickOk = false;
                                    var bPause = false;
                                    var iTimeLeft = 0;
                                    var progressTimeout = null;

                                    function teamGoLabel(iNum) {
                                        return TXT.teamGo.replace(":team", iNum);
                                    }

                                    function renderTotals() {
                                        for (var k = 1; k <= 4; k++) {
                                            $("#total-" + k).text(arrTotals[k - 1]);
                                        }
                                    }

                                    function addToTotal(iNum, iDelta) {
                                        arrTotals[iNum - 1] += iDelta;
                                        renderTotals();
                                    }

                                    //Chrono : un seul minuteur actif a la fois
                                    Fun.ambiance("taboo");

                                    function startTimer() {
                                        clearTimeout(progressTimeout);
                                        iTimeLeft = iTimeLimit;
                                        $("#progress").val(iTimeLeft);
                                        $("#chrono").text(iTimeLeft);
                                        progressTimeout = setTimeout(progress, 1000);
                                    }

                                    function progress() {
                                        if (sState != "playing") {
                                            return;
                                        }
                                        if (!bPause) {
                                            iTimeLeft--;
                                            Fun.tick(iTimeLeft);
                                        }
                                        $("#progress").val(iTimeLeft);
                                        $("#chrono").text(iTimeLeft);
                                        if (iTimeLeft <= 0) {
                                            endGame();
                                        } else {
                                            progressTimeout = setTimeout(progress, 1000);
                                        }
                                    }

                                    function setPause(bValue) {
                                        bPause = bValue;
                                        $("#btn_pause").toggleClass("fa-play", bPause).toggleClass("fa-pause", !bPause);
                                    }

                                    function pause() {
                                        if (sState != "playing") {
                                            return;
                                        }
                                        setPause(!bPause);
                                        playSound('/sounds/pause.mp3');
                                    }

                                    //Cartes du jeu (enregistrees dans le navigateur par la page d'accueil)
                                    loadGameCards(@json($gameSlug), TXT.noCards, function (arrAllCards) {
                                        var arrIds = [];
                                        arrDeck = [];
                                        arrAllCards.forEach(function (card) {
                                            if (!arrIds.includes(card.id)) {
                                                arrIds.push(card.id);
                                                arrDeck.push(card);
                                            }
                                        });
                                        startMatch();
                                    });

                                    function startMatch() {
                                        if (nbTeam < 3) {
                                            $(".player3").hide();
                                        }
                                        if (nbTeam < 4) {
                                            $(".player4").hide();
                                        }

                                        step = 1;
                                        iSet = 0;
                                        iTeam = 1;
                                        arrTotals = [0, 0, 0, 0];
                                        renderTotals();
                                        $("#btnstart, #btnnextteam").val(teamGoLabel(iTeam));
                                        initSet();
                                        sState = "intro";
                                    }

                                    function initSet() {
                                        $(".step").text(step);
                                    }

                                    //Bouton de l'intro (debut de manche)
                                    function startSet() {
                                        if (sState != "intro") {
                                            return;
                                        }
                                        startTurn();
                                    }

                                    //Bouton de fin de tour (equipe suivante / fin de manche)
                                    function initGame() {
                                        if (sState != "turnEnd") {
                                            return;
                                        }
                                        startTurn();
                                    }

                                    //Bouton de fin de manche
                                    function nextSet() {
                                        if (sState != "setEnd") {
                                            return;
                                        }
                                        sState = "intro";
                                        initSet();
                                        $("#endingset").hide();
                                        $("#intro").show();
                                    }

                                    function startTurn() {
                                        arrCardsTeam = [];
                                        oCurrentCard = null;
                                        $(".intro").slideUp("slow");
                                        $("#endingset").hide();
                                        $("#endinggame").hide();

                                        if (iSet == step) {
                                            endSet();
                                            return;
                                        }

                                        sState = "playing";
                                        setPause(false);
                                        bClickOk = false;
                                        // Cartes jamais jouees d'abord, puis les plus anciennement jouees
                                        arrTurn = leastPlayedFirst(arrDeck);
                                        $("#cardname").text("-");
                                        $("#cardwords").empty();
                                        $("#game").show();
                                        $("#spanpause").show();

                                        if (arrTurn.length == 0) {
                                            //Plus aucune carte : le tour se termine tout de suite
                                            $("#nbcards").text(0);
                                            endGame();
                                            return;
                                        }
                                        startTimer();
                                        showCard();
                                    }

                                    function showCard() {
                                        var card = arrTurn.shift();
                                        oCurrentCard = card;
                                        markCardsPlayed([card.id]);
                                        bClickOk = false;
                                        $("#nbcards").text(arrTurn.length + 1);

                                        $("#cardname").stop(true, true).fadeOut("fast", function () {
                                            $("#cardname").text(card.name);
                                            Fun.cardIn("#cardname");
                                            var list = $("#cardwords").empty();
                                            getCardWords(card.description, NB_WORDS).forEach(function (sWord) {
                                                list.append($("<li></li>").text(sWord));
                                            });
                                        }).fadeIn("fast", function () {
                                            bClickOk = true;
                                        });
                                    }

                                    //bValidate : carte trouvee (+1) ou passee (-1)
                                    function nextCard(bValidate) {
                                        if (sState != "playing" || !bClickOk || bPause || oCurrentCard === null) {
                                            return;
                                        }
                                        bClickOk = false;

                                        var card = oCurrentCard;
                                        oCurrentCard = null;
                                        var iDelta = bValidate ? 1 : -1;
                                        arrCardsTeam.push({card: card, find: bValidate, delta: iDelta});
                                        addToTotal(iTeam, iDelta);
                                        playSound(bValidate ? '/sounds/ok.mp3' : '/sounds/error.mp3');

                                        //La carte jouee ne reviendra plus dans le match
                                        arrDeck = arrDeck.filter(function (oCard) {
                                            return oCard.id !== card.id;
                                        });

                                        if (arrTurn.length == 0) {
                                            $("#nbcards").text(0);
                                            endGame();
                                        } else {
                                            showCard();
                                        }
                                    }

                                    //Fin du tour (temps ecoule ou pioche vide). La carte affichee
                                    //quand le temps est ecoule n'a pas ete jouee : elle reste dans le paquet.
                                    function endGame() {
                                        if (sState != "playing") {
                                            return;
                                        }
                                        sState = "turnEnd";
                                        clearTimeout(progressTimeout);
                                        bClickOk = false;
                                        oCurrentCard = null;
                                        $("#progress").val(-99);
                                        $("#game").slideUp("slow");

                                        var list = $("#list").empty();
                                        arrCardsTeam.forEach(function (entry, index) {
                                            var span = $('<span></span>').text(entry.card.name).on("click", function () {
                                                changeThis(index, $(this).parent());
                                            });
                                            list.append(
                                                $('<li class="pointer list-group-item"></li>')
                                                    .addClass(entry.find ? "list-group-item-success" : "list-group-item-warning")
                                                    .append(span)
                                            );
                                        });
                                        renderTurnScore();
                                        $("#spanpause").hide();
                                        $("#endinggame").show();
                                        playSound('/sounds/beep.mp3');
                                        if (arrCardsTeam.some(function (entry) { return entry.find; })) {
                                            Fun.confetti();
                                        }

                                        iLastTeam = iTeam;
                                        iTeam++;
                                        if (iTeam > nbTeam) {
                                            iTeam = 1;
                                            iSet++;
                                            $("#btnnextteam").val(TXT.endSet);
                                        } else {
                                            $("#btnnextteam").val(teamGoLabel(iTeam));
                                        }
                                        $("#btnstart").val(teamGoLabel(iTeam));
                                    }

                                    function renderTurnScore() {
                                        var iFound = arrCardsTeam.filter(function (entry) {
                                            return entry.find;
                                        }).length;
                                        $("#score").text(iFound + "/" + arrCardsTeam.length + " ");
                                    }

                                    //Correction apres le tour : trouvee <-> ratee (le score suit le changement reel)
                                    function changeThis(index, li) {
                                        if (sState != "turnEnd") {
                                            return;
                                        }
                                        var entry = arrCardsTeam[index];
                                        entry.find = !entry.find;
                                        var iNewDelta = entry.find ? 1 : -1;
                                        addToTotal(iLastTeam, iNewDelta - entry.delta);
                                        entry.delta = iNewDelta;
                                        playSound('/sounds/pass.mp3');

                                        li.toggleClass("list-group-item-warning", !entry.find)
                                            .toggleClass("list-group-item-success", entry.find);
                                        renderTurnScore();
                                    }

                                    function endSet() {
                                        sState = "setEnd";
                                        $("#spanpause").hide();
                                        $("#endinggame").slideUp("slow");
                                        $("#endingset").show();
                                        $("#btnnextteam").val(teamGoLabel(iTeam));

                                        step++;
                                        if ((step - 1) == nbSets) {
                                            sState = "finished";
                                            //Gagnant(s) parmi les equipes en jeu uniquement
                                            var arrWinners = getWinners(arrTotals.slice(0, nbTeam));
                                            var sWin = (arrWinners.length > 1 ? TXT.draw : TXT.congratulations) + " " + arrWinners.join(",");
                                            $("#finishset").text(sWin);
                                            $("#finish").empty()
                                                .append($("<a class='btn btn-primary' href='#'></a>").text(TXT.playAgain).on("click", function (e) {
                                                    e.preventDefault();
                                                    window.location.reload();
                                                }))
                                                .append(document.createTextNode("   "))
                                                .append($("<a class='btn btn-primary' href='/'></a>").text(TXT.home));
                                            Fun.celebrate(arrWinners);
                                        }
                                    }
                                </script>
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
@endsection
