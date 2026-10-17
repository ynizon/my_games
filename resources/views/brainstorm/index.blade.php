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
                                    <h3>{{ __("messages.Lookfor")}}<span class="steph3"><span class="step">1</span>/{{$nbcards}}</span></h3>
                                    <p>{!! __("messages.goal_brainstorm")!!}.
                                    </p>
                                    <br/>
                                    <input id="btnstart" onclick="startSet()" type="button" value="{{ __("messages.Team 1 Start")}}" class="btnstep btn btn-primary" />
                                </div>

                                <div id="game" class="brainstorm inv">

                                    <div class="slide-container">
                                        <div class="wrapper">
                                            <div class="clash-card barbarian">
                                                <div class="clash-card__noimage">
                                                    <input type="hidden" id="progress" value="" />
                                                    <div id="chrono">0</div>
                                                    <div id="chrono2">0</div>
                                                </div>
                                                <div class="clash-card__level clash-card__level--barbarian">{{ __("messages.Remaining cards")}} : <span id="nbcards" >0</span>
                                                <div class="pointer" onclick="renewCard()" title="{{__("messages.Renew card")}}"><i class="fa-solid fa-rotate-right"></i></div>
                                                </div>
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
                                                        <i id="button3rd" class="fa-solid fa-trophy" ></i>

                                                        <div id="nbcheck" class="stat-value"></div>
                                                    </div>

                                                    <div class="one-third">
                                                        <span id="spanpause" class="inv" ><i onclick="pause(true)" id="btn_pause" class="fa fa-pause pointer" ></i></span>

                                                        <div class="stat-value">{{__("messages.PAUSE")}}</div>
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

                                <!-- Verification : on coche les reponses trouvees qu'on n'a pas eu le temps de cocher -->
                                <div id="review" class="inv brainstorm review">
                                    <h3>{{ __("messages.ReviewTitle") }}</h3>
                                    <p class="review-hint">{{ __("messages.ReviewHint") }}</p>
                                    <h4 id="reviewtheme" class="review-theme"></h4>
                                    <ul id="reviewwords" class="form-check review-words"></ul>
                                    <input id="btnreview" onclick="confirmReview()" type="button" value="" class="btnstep btn btn-primary" />
                                </div>

                                <div id="endinggame" class="inv brainstorm">
                                    <div>
                                        <h3>{{ __("messages.Set")}} <span class="step"></span></h3>
                                        <h4>{!! __("messages.Lookfor")!!} !</h4>
                                        <p><span id="score">0</span> {{ __("messages.sentences_found")}}</p>
                                        <ul id="list" class="list-group mb-4">

                                        </ul>

                                    </div>

                                    <input id="btnnext" onclick="initGame()" type="button" value="{{ __("messages.NextTeam") }}" class="btnstep btnstepend btn btn-primary" />
                                </div>

                                <div id="endingset" class="inv brainstorm">
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
                                    var nbCards = {{ $nbcards }};
                                    var iTimeLimit = 60;
                                    var iCountdown = 6;
                                    var iMaxWords = 10;
                                    var T = {
                                        teamGo: @json(__("messages.TeamGo")),
                                        endSet: @json(__("messages.Endset")),
                                        errorGetCard: @json(__("messages.ErrorGetCard")),
                                        noCardsLoaded: @json(__("messages.NoCardsLoaded")),
                                        draw: @json(__("messages.Draw game")),
                                        congratulations: @json(__("messages.Congratulations Team")),
                                        playAgain: @json(__("messages.Play again")),
                                        home: @json(__("messages.Home")),
                                        reviewValidate: @json(__("messages.ReviewValidate"))
                                    };

                                    // Etat de la partie
                                    // sPhase : loading | intro | countdown | playing | review | turnEnd | setEnd | finished
                                    var sPhase = "loading";
                                    var step = 0;
                                    var iTeam = 1;
                                    var iCard = 0;
                                    var arrScores = [];
                                    var iScore = 0;
                                    var arrAllCards = [];
                                    var arrCardsForThisMatchId = [];
                                    var arrCardsForThisSet = [];
                                    var oCurrentCard = null;
                                    var arrCurrentWords = [];
                                    var arrCardsTeam = [];

                                    // Chrono
                                    var iRemaining = iTimeLimit;
                                    var bPause = false;
                                    var progressTimeout = null;
                                    var countdownTimeout = null;

                                    Fun.ambiance("brainstorm");
                                    $("#chrono2").hide();
                                    document.getElementById('nbcheck').innerText = '0';
                                    $("#cardwords").on("change", ".form-check-input", function () {
                                        document.getElementById('nbcheck').innerText = $(".form-check-input:checked").length;
                                        $(this).closest("li").toggleClass("fun-checked", this.checked);
                                        if (this.checked) {
                                            Fun.sound("sparkle");
                                        }
                                    });

                                    $("#reviewwords").on("change", ".form-check-input", function () {
                                        $(this).closest("li").toggleClass("fun-checked", this.checked);
                                        if (this.checked) {
                                            Fun.sound("sparkle");
                                        }
                                        refreshReviewButton();
                                    });

                                    function teamGoLabel(iNumTeam) {
                                        return T.teamGo.replace(":team", iNumTeam);
                                    }

                                    function clearTimers() {
                                        clearTimeout(progressTimeout);
                                        clearTimeout(countdownTimeout);
                                        progressTimeout = null;
                                        countdownTimeout = null;
                                    }

                                    function progress() {
                                        progressTimeout = null;
                                        if (sPhase != "playing") {
                                            return;
                                        }
                                        if (!bPause) {
                                            iRemaining--;
                                            $("#progress").val(iRemaining);
                                            $("#chrono").text(iRemaining);
                                            Fun.tick(iRemaining);
                                        }
                                        if (iRemaining <= 0) {
                                            timeUp();
                                        } else {
                                            progressTimeout = setTimeout(progress, 1000);
                                        }
                                    }

                                    // Fin du temps : on passe a la verification des reponses
                                    function timeUp() {
                                        playSound('/sounds/finish.mp3');
                                        nextCard(true);
                                    }

                                    function setPause(bValue) {
                                        bPause = bValue;
                                        $("#btn_pause").toggleClass("fa-play", bPause).toggleClass("fa-pause", !bPause);
                                    }

                                    // Bouton pause (pendant le tour uniquement)
                                    function pause(bSound) {
                                        if (sPhase != "playing") {
                                            return;
                                        }
                                        setPause(!bPause);
                                        if (bSound) {
                                            playSound('/sounds/pause.mp3');
                                        }
                                    }

                                    loadGameCards(@json($gameSlug), T.noCardsLoaded, function (arrCards) {
                                        // Seules les cartes ayant au moins un mot sont jouables
                                        arrAllCards = arrCards.filter(function (oCard) {
                                            return getCardWords(oCard.description, iMaxWords).length > 0;
                                        });
                                        getCards();
                                    });

                                    // Tire les cartes du match : en priorite celles jamais jouees, puis les plus anciennes
                                    function getCards() {
                                        var iNeeded = nbTeam * nbCards;
                                        if (arrAllCards.length < iNeeded) {
                                            alert(T.errorGetCard + ".");
                                            window.location.href = @json("/".$gameSlug."/settings");
                                            return;
                                        }

                                        var arrCardsForThisMatch = leastPlayedFirst(arrAllCards).slice(0, iNeeded);
                                        arrCardsForThisMatchId = arrCardsForThisMatch.map(function (oCard) { return oCard.id; });
                                        markCardsPlayed(arrCardsForThisMatchId);

                                        startMatch(arrCardsForThisMatch);
                                    }

                                    function startMatch(arrCardsForThisMatch) {
                                        if (nbTeam < 3) {
                                            $(".player3").hide();
                                        }
                                        if (nbTeam < 4) {
                                            $(".player4").hide();
                                        }

                                        step = 1;
                                        iTeam = 1;
                                        iCard = 0;
                                        arrScores = [];
                                        for (var k = 1; k <= nbTeam; k++) {
                                            arrScores.push(0);
                                        }
                                        for (var k = 1; k <= 4; k++) {
                                            $("#total-" + k).text(0);
                                        }
                                        $("#btnstart").val(teamGoLabel(iTeam));
                                        arrCardsForThisSet = arrCardsForThisMatch.slice();
                                        shuffle(arrCardsForThisSet);
                                        initSet();
                                        sPhase = "intro";
                                    }

                                    function initSet() {
                                        $(".step").text(step);
                                    }

                                    // Bouton "Equipe 1, allez-y" de l'intro
                                    function startSet() {
                                        if (sPhase != "intro") {
                                            return;
                                        }
                                        initSet();
                                        $("#endingset").hide();
                                        nextGame();
                                    }

                                    // Bouton de l'ecran de fin de tour
                                    function initGame() {
                                        if (sPhase != "turnEnd") {
                                            return;
                                        }
                                        if (iCard == nbTeam) {
                                            endSet();
                                        } else {
                                            nextGame();
                                        }
                                    }

                                    // Bouton "Manche suivante"
                                    function nextSet() {
                                        if (sPhase != "setEnd") {
                                            return;
                                        }
                                        initSet();
                                        $("#intro").show();
                                        $("#endingset").hide();
                                        $("#btnstart").val(teamGoLabel(iTeam));
                                        sPhase = "intro";
                                    }

                                    // Debut du tour d'une equipe
                                    function nextGame() {
                                        arrCardsTeam = [];
                                        iScore = 0;

                                        $(".intro").slideUp("slow");
                                        $("#endingset").hide();
                                        $("#endinggame").hide();

                                        if (arrCardsForThisSet.length == 0) {
                                            endSet();
                                            return;
                                        }

                                        oCurrentCard = arrCardsForThisSet.shift();
                                        clearTimers();
                                        setPause(false);
                                        iRemaining = iTimeLimit;
                                        $("#progress").val(iRemaining);
                                        $("#chrono").text(iRemaining);
                                        $("#game").show();
                                        $("#spanpause").show();
                                        showCard();
                                        prepareTimer();
                                    }

                                    function showCard() {
                                        $("#nbcards").text(arrCardsForThisSet.length + 1);
                                        arrCurrentWords = getCardWords(oCurrentCard.description, iMaxWords);
                                        document.getElementById('nbcheck').innerText = '0';

                                        $("#cardname").hide().text(oCurrentCard.name).fadeIn("fast");
                                        Fun.cardIn("#cardname");
                                        var oList = $("#cardwords").empty();
                                        arrCurrentWords.forEach(function (sWord, k) {
                                            var sId = "item" + (k + 1);
                                            oList.append(
                                                $("<li></li>").append(
                                                    $("<label class='form-check-label'></label>").attr("for", sId)
                                                        .append($("<input type='checkbox' class='form-check-input' value='1' />").attr("id", sId))
                                                        .append(document.createTextNode("  " + sWord))
                                                )
                                            );
                                        });
                                    }

                                    // Decompte avant le debut du tour (mots caches)
                                    function prepareTimer() {
                                        sPhase = "countdown";
                                        $("#cardwords").hide();
                                        $("#chrono").hide();
                                        $("#chrono2").text(iCountdown).show();
                                        $(".clash-card__unit-stats").hide();
                                        updatePauseTimer();
                                    }

                                    function updatePauseTimer() {
                                        countdownTimeout = null;
                                        var iTimePause = parseInt($("#chrono2").text()) - 1;
                                        if (iTimePause <= 0) {
                                            $("#cardwords").show();
                                            $("#chrono").show();
                                            $("#chrono2").hide();
                                            $(".clash-card__unit-stats").show();
                                            sPhase = "playing";
                                            Fun.sound("go");
                                            progressTimeout = setTimeout(progress, 1000);
                                        } else {
                                            $("#chrono2").text(iTimePause);
                                            Fun.sound("tick");
                                            countdownTimeout = setTimeout(updatePauseTimer, 1000);
                                        }
                                    }

                                    // Bouton OK ou fin du temps : verification des reponses avant de compter les points
                                    function nextCard(bValidate) {
                                        if (sPhase != "playing") {
                                            return;
                                        }
                                        sPhase = "review";
                                        clearTimers();
                                        setPause(false);

                                        var arrChecked = $("#cardwords .form-check-input").map(function () { return this.checked; }).get();
                                        $("#reviewtheme").text(oCurrentCard.name);
                                        var oList = $("#reviewwords").empty();
                                        arrCurrentWords.forEach(function (sWord, k) {
                                            var sId = "review" + (k + 1);
                                            var bChecked = bValidate && arrChecked[k] === true;
                                            oList.append(
                                                $("<li></li>").toggleClass("fun-checked", bChecked).append(
                                                    $("<label class='form-check-label'></label>").attr("for", sId)
                                                        .append($("<input type='checkbox' class='form-check-input' value='1' />").attr("id", sId).prop("checked", bChecked))
                                                        .append(document.createTextNode("  " + sWord))
                                                )
                                            );
                                        });
                                        refreshReviewButton();

                                        $("#spanpause").hide();
                                        $("#game").hide();
                                        $("#review").show();
                                    }

                                    function refreshReviewButton() {
                                        $("#btnreview").val(T.reviewValidate.replace(":count", $("#reviewwords .form-check-input:checked").length));
                                    }

                                    // Bouton "Valider" de la verification : les reponses cochees sont comptees
                                    function confirmReview() {
                                        if (sPhase != "review") {
                                            return;
                                        }
                                        sPhase = "turnEnd";
                                        iScore = $("#reviewwords .form-check-input:checked").length;
                                        playSound('/sounds/ok.mp3');
                                        $("#review").hide();

                                        arrScores[iTeam - 1] += iScore;
                                        $("#total-" + iTeam).text(arrScores[iTeam - 1]);
                                        arrCardsTeam.push({card: oCurrentCard, find: iScore > 0});
                                        iCard++;

                                        endGame();
                                    }

                                    // Changer de carte (le chrono continue)
                                    function renewCard() {
                                        if (sPhase != "playing" || iRemaining <= 0) {
                                            return;
                                        }
                                        var arrCandidates = arrAllCards.filter(function (oCard) {
                                            return !arrCardsForThisMatchId.includes(oCard.id);
                                        });
                                        if (arrCandidates.length == 0) {
                                            return;
                                        }
                                        oCurrentCard = leastPlayedFirst(arrCandidates)[0];
                                        arrCardsForThisMatchId.push(oCurrentCard.id);
                                        markCardsPlayed([oCurrentCard.id]);
                                        showCard();
                                    }

                                    // Fin du tour d'une equipe
                                    function endGame() {
                                        $("#progress").val(-99);
                                        $("#game").hide();
                                        $("#score").text(iScore + "/" + arrCurrentWords.length + " ");

                                        var oList = $("#list").empty();
                                        arrCardsTeam.forEach(function (item) {
                                            oList.append(
                                                $("<li></li>")
                                                    .addClass("list-group-item list-group-item-" + (item.find ? "success" : "warning"))
                                                    .text(item.card.name)
                                            );
                                        });
                                        $("#spanpause").hide();
                                        $("#endinggame").show();

                                        playSound('/sounds/beep.mp3');
                                        if (iScore > 0) {
                                            Fun.confetti();
                                        }

                                        iTeam++;
                                        if (iTeam > nbTeam) {
                                            iTeam = 1;
                                        }
                                        $("#btnnext").val(iCard == nbTeam ? T.endSet : teamGoLabel(iTeam));
                                    }

                                    // Fin de manche (toutes les equipes ont joue)
                                    function endSet() {
                                        if (sPhase == "setEnd" || sPhase == "finished") {
                                            return;
                                        }
                                        sPhase = "setEnd";
                                        iCard = 0;
                                        $("#spanpause").hide();
                                        $("#endinggame").slideUp("slow");
                                        $("#endingset").show();

                                        step++;
                                        if ((step - 1) == nbCards) {
                                            sPhase = "finished";
                                            var arrWinners = getWinners(arrScores);
                                            var sWin = (arrWinners.length > 1 ? T.draw : T.congratulations) + " " + arrWinners.join(",");
                                            $("#finishset").text(sWin);
                                            $("#finish").empty()
                                                .append($("<a class='btn btn-primary' href='#'></a>").text(T.playAgain).on("click", function (e) {
                                                    e.preventDefault();
                                                    window.location.reload();
                                                }))
                                                .append(document.createTextNode("   "))
                                                .append($("<a class='btn btn-primary' href='/'></a>").text(T.home));
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
