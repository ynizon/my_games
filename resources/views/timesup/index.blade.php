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
                            <script>
                                var iCardDeck =20;
                                var iTimeLimit = 30;
                                Fun.ambiance("timesup");
                            </script>

                            <div class="card-body">
                                <div id="intro1" class="intro slider">
                                    <h3>{{ __("messages.Describe")}} !</h3>
                                    <br/>
                                    <p>{!! __("messages.goal_timesup1") !!}.</p>
                                    <br/>
                                    <input onclick="startSet(1)" type="button" value="{{ __("messages.Team 1 Start")}}" class="btnteam btn btn-primary" />
                                </div>

                                <div id="intro2" class="intro inv">
                                    <h3>{{ __("messages.Oneword")}} !</h3>
                                    <br/>
                                    <p>{!! __("messages.goal_timesup2") !!}</p>
                                    <br/>
                                    <input onclick="startSet(2)" type="button" value="{{ __("messages.Team 1 Start")}}" class="btnteam btn btn-primary" />
                                </div>

                                <div id="intro3" class="intro inv">
                                    <h3>{{ __("messages.Mime")}} !</h3>
                                    <br/>
                                    <p>{!! __("messages.goal_timesup3")!!}</p>
                                    <br/>
                                    <input onclick="startSet(3)" type="button" value="{{ __("messages.Team 1 Start")}}" class="btnteam btn btn-primary" />
                                </div>

                                <div id="game" class="timesup inv">
                                    <div class="slide-container">
                                        <div class="wrapper">
                                            <div class="clash-card barbarian">
                                                <div class="clash-card__image clash-card__image--barbarian">
                                                    <input type="hidden" id="progress" value="" />
                                                    <div>
                                                        <div class="inline">
                                                            <div class="timer" >
                                                                <svg class="rotate" viewbox="0 0 250 250">
                                                                    <path id="timeloader" transform="translate(125, 125)" />
                                                                    <text id="chrono" x="110" y="140"  font-size="60" fill="#636b6f">0</text>
                                                                </svg>
                                                                <div class="dots">
                                                                    <span class="time deg0"></span>
                                                                    <span class="time deg45"></span>
                                                                    <span class="time deg90"></span>
                                                                    <span class="time deg135"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <script src="{{ asset('js/clock.js') }}" ></script>
                                                </div>
                                                <div class="clash-card__level clash-card__level--barbarian">{{ __("messages.Remaining cards")}} : <span id="nbcards" >0</span></div>
                                                <div class="clash-card__unit-name">
                                                    <span id="cardname">-</span>
                                                </div>
                                                <div class="clash-card__unit-description">

                                                </div>

                                                <div class="clash-card__unit-stats clash-card__unit-stats--barbarian clearfix">
                                                    <div class="one-third" id="blockcancel">
                                                        <i class="fa fa-close pointer btnplay arrondicancel" id="cancel" onclick="nextCard(false)"></i>

                                                        <div class="stat-value">{{ __("messages.MISSED")}}</div>
                                                    </div>

                                                    <div class="one-third">
                                                        <span id="spanpause" class="inv" ><i onclick="pause()" id="btn_pause" class="fa fa-pause pointer" ></i></span>

                                                        <div class="stat-value">{{ __("messages.PAUSE")}}</div>
                                                    </div>

                                                    <div class="one-third no-border">
                                                        <i class="fa fa-check pointer btnplay arrondivalidate" id="validate" onclick="nextCard(true)"></i>

                                                        <div class="stat-value">{{ __("messages.OK")}}</div>
                                                    </div>

                                                </div>

                                            </div> <!-- end clash-card barbarian-->
                                        </div> <!-- end wrapper -->
                                    </div> <!-- end container -->
                                </div>

                                <div id="endinggame" class="inv timesup">
                                    <div>
                                        <h3>{{ __("messages.Set")}} <span class="step"></span></h3>
                                        <p><span id="score">0</span><br/>
                                            <input onclick="initGame()" type="button" value="{{ __("messages.NextTeam")}}" class="pointer btnteam btn btn-primary" />
                                        </p>
                                        <ul id="list" class="list-group">
                                        </ul>
                                    </div>
                                </div>

                                <div id="endingset" class="inv timesup">
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
                                                <td>{{ __("messages.Set")}} 1</td>
                                                <td id="total-1-1">0</td>
                                                <td id="total-1-2">0</td>
                                                <td class="player3" id="total-1-3">0</td>
                                                <td class="player4" id="total-1-4">0</td>
                                            </tr>
                                            <tr>
                                                <td>{{ __("messages.Set")}} 2</td>
                                                <td id="total-2-1">0</td>
                                                <td id="total-2-2">0</td>
                                                <td class="player3" id="total-2-3">0</td>
                                                <td class="player4" id="total-2-4">0</td>
                                            </tr>
                                            <tr>
                                                <td>{{ __("messages.Set")}} 3</td>
                                                <td id="total-3-1">0</td>
                                                <td id="total-3-2">0</td>
                                                <td class="player3" id="total-3-3">0</td>
                                                <td class="player4" id="total-3-4">0</td>
                                            </tr>
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
                                        <input onclick="nextSet()" type="button" value="{{ __("messages.Next Set")}}" class="pointer btn btn-primary" />
                                    </div>

                                </div>

                                <script>
                                    var nbTeam = {{ $nbteams }};
                                    var sTeamGo = @json(__("messages.TeamGo"));
                                    var sWikiLang = @json(explode("_", $_COOKIE["locale"] ?? config("app.locale"))[0]);

                                    // Ecran courant : intro, play, teamend, setend, end (ignore les doubles clics)
                                    var sScreen = "loading";
                                    var step = 0;
                                    var iTeam = 1;
                                    // Points par manche et par equipe : arrScores[manche][equipe]
                                    var arrScores = {1: {}, 2: {}, 3: {}};
                                    var arrCardsForThisMatch = [];
                                    // Cartes encore a deviner dans la manche (une carte passee revient a la fin)
                                    var arrCardsForThisSet = [];
                                    // Resultat du tour de l'equipe : une entree par carte (id), dans l'ordre d'apparition
                                    var arrCardsTeam = [];
                                    var oCurrentCard = null;
                                    var bClickOk = false;
                                    var progressTimeout = null;
                                    var bPause = false;

                                    function setTeamLabels() {
                                        $(".btnteam").val(sTeamGo.replace(":team", iTeam));
                                    }

                                    function stopProgress() {
                                        if (progressTimeout !== null) {
                                            clearTimeout(progressTimeout);
                                            progressTimeout = null;
                                        }
                                    }

                                    function progress() {
                                        progressTimeout = null;
                                        if (sScreen != "play") {
                                            return;
                                        }
                                        var iTime = parseInt($('#progress').val());
                                        if (!bPause) {
                                            iTime--;
                                            $('#progress').val(iTime);
                                            $("#chrono").text(iTime);
                                            Fun.tick(iTime);
                                        }
                                        if (iTime <= 0) {
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
                                        if (sScreen != "play") {
                                            return;
                                        }
                                        setPause(!bPause);
                                        playSound('/sounds/pause.mp3');
                                    }

                                    loadGameCards(@json($gameSlug), @json(__("messages.NoCardsLoaded")), getCards);

                                    //Get cards for this game : en priorite des cartes jamais jouees, puis les plus anciennes
                                    function getCards(arrAllCards) {
                                        var iNeeded = nbTeam * iCardDeck;
                                        if (arrAllCards.length < iNeeded) {
                                            alert(@json(__("messages.ErrorGetCard")) + ".");
                                            window.location.href = @json("/".$gameSlug."/settings");
                                            return;
                                        }

                                        arrCardsForThisMatch = leastPlayedFirst(arrAllCards).slice(0, iNeeded);
                                        markCardsPlayed(arrCardsForThisMatch.map(function (oCard) { return oCard.id; }));

                                        startMatch();
                                    }

                                    function findCardIndex(arrCards, iId) {
                                        for (var k = 0; k < arrCards.length; k++) {
                                            if (arrCards[k].id == iId) {
                                                return k;
                                            }
                                        }
                                        return -1;
                                    }

                                    function removeCard(arrCards, iId) {
                                        var k = findCardIndex(arrCards, iId);
                                        if (k > -1) {
                                            arrCards.splice(k, 1);
                                        }
                                    }

                                    //Enregistre le resultat d'une carte pour le tour (la derniere decision compte)
                                    function setTeamResult(oCard, bFind) {
                                        var k = findCardIndex(arrCardsTeam.map(function (item) { return item.card; }), oCard.id);
                                        if (k > -1) {
                                            arrCardsTeam[k].find = bFind;
                                        } else {
                                            arrCardsTeam.push({card: oCard, find: bFind});
                                        }
                                    }

                                    function addScore(iSet, iTeamScore, iPoints) {
                                        arrScores[iSet][iTeamScore] = (arrScores[iSet][iTeamScore] || 0) + iPoints;
                                        refreshScores();
                                    }

                                    function getTeamTotal(iTeamScore) {
                                        var iTotal = 0;
                                        for (var j = 1; j <= 3; j++) {
                                            iTotal += arrScores[j][iTeamScore] || 0;
                                        }
                                        return iTotal;
                                    }

                                    function refreshScores() {
                                        for (var k = 1; k <= 4; k++) {
                                            for (var j = 1; j <= 3; j++) {
                                                $("#total-" + j + "-" + k).text(arrScores[j][k] || 0);
                                            }
                                            $("#total-" + k).text(getTeamTotal(k));
                                        }
                                    }

                                    function nextCard(bValidate) {
                                        if (sScreen != "play" || !bClickOk || bPause || oCurrentCard === null) {
                                            return;
                                        }
                                        //Manche 1 : on ne peut pas passer
                                        if (!bValidate && step == 1) {
                                            return;
                                        }
                                        bClickOk = false;

                                        var oCard = oCurrentCard;
                                        oCurrentCard = null;
                                        removeCard(arrCardsForThisSet, oCard.id);
                                        setTeamResult(oCard, bValidate);

                                        if (bValidate) {
                                            playSound('/sounds/ok.mp3');
                                            addScore(step, iTeam, 1);
                                        } else {
                                            //Carte passee : elle retourne sous la pioche
                                            playSound('/sounds/error.mp3');
                                            arrCardsForThisSet.push(oCard);
                                        }

                                        if (arrCardsForThisSet.length == 0) {
                                            endGame();
                                        } else {
                                            showCard();
                                        }
                                    }

                                    function showCard() {
                                        oCurrentCard = arrCardsForThisSet[0];
                                        $("#nbcards").text(arrCardsForThisSet.length);
                                        $("#cardname").stop(true, true).hide().text(oCurrentCard.name).fadeIn("fast", function () {
                                            bClickOk = true;
                                        });
                                        Fun.cardIn("#cardname");
                                    }

                                    function startSet(pStep) {
                                        if (sScreen != "intro" || pStep != step) {
                                            return;
                                        }
                                        initGame();
                                    }

                                    function initSet() {
                                        arrCardsForThisSet = arrCardsForThisMatch.slice();
                                        shuffle(arrCardsForThisSet);
                                        $(".step").text(step);
                                        $("#blockcancel").css("visibility", step == 1 ? "hidden" : "visible");
                                    }

                                    function endSet() {
                                        sScreen = "setend";
                                        $("#spanpause").hide();
                                        $("#endinggame").slideUp("slow");
                                        $("#endingset").show();
                                        $("#finishset").text(@json(__("messages.Endset")));
                                        if (step == 3) {
                                            sScreen = "end";
                                            var arrTotals = [];
                                            for (var k = 1; k <= nbTeam; k++) {
                                                arrTotals.push(getTeamTotal(k));
                                            }
                                            var arrWinners = getWinners(arrTotals);
                                            if (arrWinners.length > 1) {
                                                $("#finishset").text(@json(__("messages.Draw game")) + " " + arrWinners.join(","));
                                            } else {
                                                $("#finishset").text(@json(__("messages.Congratulations Team")) + " " + arrWinners[0]);
                                            }
                                            $("#finish").html("<a class='btn btn-primary' href='#' onclick='window.location.reload();'>{{ __("messages.Play again")}}</a>&nbsp;&nbsp;&nbsp;<a class='btn btn-primary' href='/'>{{ __("messages.Home")}}</a>");
                                            Fun.celebrate(arrWinners);
                                        }
                                    }

                                    function nextSet() {
                                        if (sScreen != "setend") {
                                            return;
                                        }
                                        sScreen = "intro";
                                        step++;
                                        initSet();
                                        $("#intro" + step).show();
                                        $("#endingset").hide();
                                    }

                                    function initGame() {
                                        if (sScreen != "intro" && sScreen != "teamend") {
                                            return;
                                        }
                                        nextGame();
                                    }

                                    function endGame() {
                                        if (sScreen != "play") {
                                            return;
                                        }
                                        sScreen = "teamend";
                                        stopProgress();
                                        endClock();
                                        bClickOk = false;
                                        //La carte affichee a la fin du temps n'est pas jouee : elle reste dans la pioche
                                        oCurrentCard = null;
                                        $('#progress').val(-99);
                                        $("#game").slideUp("slow");

                                        var iTeamPlayed = iTeam;
                                        var oList = $("#list").empty();
                                        arrCardsTeam.forEach(function (item) {
                                            var oLine = $('<li class="pointer list-group-item"></li>')
                                                .addClass(item.find ? "list-group-item-success" : "list-group-item-warning");
                                            $('<span></span>').text(item.card.name).on("click", function () {
                                                changeThis(item, oLine, iTeamPlayed);
                                            }).appendTo(oLine);
                                            oLine.append(document.createTextNode("   "));
                                            $('<a target="biography"><i class="fa fa-link"></i></a>')
                                                .attr("href", "https://" + sWikiLang + ".wikipedia.org/wiki/" + biography(item.card.name))
                                                .appendTo(oLine);
                                            oList.append(oLine);
                                        });
                                        refreshTeamScore();
                                        $("#spanpause").hide();
                                        $("#endinggame").show();

                                        playSound('/sounds/beep.mp3');
                                        if (arrCardsTeam.some(function (item) { return item.find; })) {
                                            Fun.confetti();
                                        }

                                        iTeam++;
                                        if (iTeam > nbTeam) {
                                            iTeam = 1;
                                        }
                                        setTeamLabels();
                                    }

                                    function refreshTeamScore() {
                                        var iFound = arrCardsTeam.filter(function (item) { return item.find; }).length;
                                        $("#score").text(iFound + "/" + arrCardsTeam.length + " ");
                                    }

                                    //Correction apres le tour : la carte a finalement ete trouvee (ou non)
                                    function changeThis(item, oLine, iTeamPlayed) {
                                        if (sScreen != "teamend") {
                                            return;
                                        }
                                        playSound('/sounds/pass.mp3');
                                        if (!item.find) {
                                            item.find = true;
                                            removeCard(arrCardsForThisSet, item.card.id);
                                            addScore(step, iTeamPlayed, 1);
                                        } else {
                                            item.find = false;
                                            if (findCardIndex(arrCardsForThisSet, item.card.id) == -1) {
                                                arrCardsForThisSet.push(item.card);
                                            }
                                            addScore(step, iTeamPlayed, -1);
                                        }
                                        oLine.toggleClass('list-group-item-warning', !item.find);
                                        oLine.toggleClass('list-group-item-success', item.find);
                                        refreshTeamScore();
                                    }

                                    function nextGame() {
                                        $(".intro").slideUp("slow");
                                        $("#endingset").hide();
                                        $("#endinggame").hide();

                                        if (arrCardsForThisSet.length == 0) {
                                            endSet();
                                            return;
                                        }

                                        sScreen = "play";
                                        arrCardsTeam = [];
                                        shuffle(arrCardsForThisSet);
                                        setPause(false);
                                        stopProgress();
                                        $("#progress").val(iTimeLimit);
                                        $("#chrono").text(iTimeLimit);
                                        $("#game").show();
                                        $("#spanpause").show();
                                        initClock(iTimeLimit);
                                        showCard();
                                        progressTimeout = setTimeout(progress, 1000);
                                    }

                                    function startMatch() {
                                        if (nbTeam < 3) {
                                            $(".player3").hide();
                                        }
                                        if (nbTeam < 4) {
                                            $(".player4").hide();
                                        }

                                        step = 1;
                                        iTeam = 1;
                                        setTeamLabels();
                                        arrScores = {1: {}, 2: {}, 3: {}};
                                        refreshScores();
                                        initSet();
                                        sScreen = "intro";
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
