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
                                    <h3>{{ __("messages.Draw")}} <span class="steph3"><span class="step">1</span>/{{ $nbsets}}</span></h3>
                                    <p>{!! __("messages.goal_pictionary") !!}.
                                    </p>
                                    <br/>
                                    <input onclick="startSet()" type="button" value="{{ __("messages.Team 1 Start")}}" class="btnstep btnstart btn btn-primary" />
                                </div>

                                <div id="game" class="pictionary inv">
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
                                                <div class="clash-card__level clash-card__level--barbarian"></div>
                                                <div class="clash-card__unit-name">
                                                    <span id="cardname">-</span>
                                                </div>
                                                <div class="clash-card__unit-description">

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

                                <div id="endinggame" class="inv pictionary">
                                    <div>
                                        <h3>{{ __("messages.Set")}} <span class="step"></span></h3>
                                        <h4>{!! __("messages.Lookfor")!!} !</h4>
                                        <p><span id="score">0</span> {{ __("messages.sentences_found")}}<br/>
                                        <ul id="list" class="list-group">

                                        </ul>
                                        </p>

                                    </div>

                                    <input onclick="initGame()" type="button" value="{{ __("messages.NextTeam") }}" class="btnstepend btn btn-primary" />
                                </div>

                                <div id="endingset" class="inv pictionary">
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
                                        <input onclick="nextSet()" type="button" value="{{ __("messages.Next Set")}}" class="btnstep btn btn-primary" />
                                    </div>

                                </div>

                                <script>
                                    var nbTeam = {{ $nbteams }};
                                    var nbSets = {{ $nbsets }};
                                    var iTimeLimit = 30;

                                    var TXT_TEAM_GO = @json(__("messages.TeamGo"));
                                    var TXT_ENDSET = @json(__("messages.Endset"));
                                    var TXT_DRAW_GAME = @json(__("messages.Draw game"));
                                    var TXT_CONGRATULATIONS = @json(__("messages.Congratulations Team"));
                                    var TXT_PLAY_AGAIN = @json(__("messages.Play again"));
                                    var TXT_HOME = @json(__("messages.Home"));

                                    // Etat de la partie : loading, intro, turn, turnEnd, setEnd, finished
                                    var sPhase = "loading";
                                    var step = 0;
                                    var iTeam = 1;
                                    var iSet = 0;
                                    var arrScores = [];
                                    var arrCardsForThisMatch = [];  //Toutes les cartes
                                    var arrCardsForThisSet = [];    //Pioche (cartes pas encore jouees)
                                    var arrMissedCards = [];        //Cartes ratees, remises dans la pioche quand elle est vide
                                    var arrCardsTeam = [];          //Cartes jouees pendant le tour : {card, find, delta, missDelta, team}
                                    var oCurrentCard = null;
                                    var bClickOk = false;
                                    var bPause = false;
                                    var iTimeLeft = 0;
                                    var progressTimeout = null;

                                    function teamGoLabel(iNum){
                                        return TXT_TEAM_GO.replace(":team", iNum);
                                    }

                                    Fun.ambiance("pictionary");

                                    function progress() {
                                        progressTimeout = null;
                                        if (sPhase != "turn") {
                                            return;
                                        }
                                        if (!bPause) {
                                            iTimeLeft--;
                                            Fun.tick(iTimeLeft);
                                        }
                                        $("#progress").val(iTimeLeft);
                                        $("#chrono").text(iTimeLeft);
                                        if (iTimeLeft <= 0) {
                                            timeUp();
                                        } else {
                                            progressTimeout = setTimeout(progress, 1000);
                                        }
                                    }

                                    function stopTimer(){
                                        if (progressTimeout !== null) {
                                            clearTimeout(progressTimeout);
                                            progressTimeout = null;
                                        }
                                    }

                                    function setPause(bValue){
                                        bPause = bValue;
                                        $("#btn_pause").toggleClass("fa-play", bPause).toggleClass("fa-pause", !bPause);
                                    }

                                    function pause(){
                                        if (sPhase != "turn") {
                                            return;
                                        }
                                        setPause(!bPause);
                                        playSound('/sounds/pause.mp3');
                                    }

                                    loadGameCards(@json($gameSlug), @json(__("messages.NoCardsLoaded")), function (arrAllCards) {
                                        arrCardsForThisMatch = arrAllCards.slice();
                                        startMatch();
                                    });

                                    //Pioche une carte ; si la pioche est vide, on y remet les cartes ratees (ou toutes les cartes)
                                    function drawCard(){
                                        if (arrCardsForThisSet.length == 0) {
                                            if (arrMissedCards.length > 0) {
                                                arrCardsForThisSet = arrMissedCards;
                                                shuffle(arrCardsForThisSet);
                                            } else {
                                                arrCardsForThisSet = leastPlayedFirst(arrCardsForThisMatch);
                                            }
                                            arrMissedCards = [];
                                        }
                                        var oCard = arrCardsForThisSet.shift();
                                        markCardsPlayed([oCard.id]);
                                        return oCard;
                                    }

                                    function removeById(arrCards, iId){
                                        var index = arrCards.findIndex(function (oCard) { return oCard.id == iId; });
                                        if (index > -1) {
                                            arrCards.splice(index, 1);
                                        }
                                    }

                                    function addScore(iNumTeam, iDelta){
                                        arrScores[iNumTeam - 1] += iDelta;
                                        $("#total-" + iNumTeam).text(arrScores[iNumTeam - 1]);
                                    }

                                    //Enregistre la carte jouee ; delta : +1 trouvee, -1 ratee, 0 temps ecoule
                                    function recordCard(oCard, bFind, iDelta){
                                        arrCardsTeam.push({card: oCard, find: bFind, delta: iDelta, missDelta: bFind ? -1 : iDelta, team: iTeam});
                                        addScore(iTeam, iDelta);
                                        if (!bFind) {
                                            arrMissedCards.push(oCard);
                                        }
                                    }

                                    function nextCard(bValidate){
                                        if (sPhase != "turn" || !bClickOk || bPause || oCurrentCard === null) {
                                            return;
                                        }
                                        bClickOk = false;
                                        recordCard(oCurrentCard, bValidate, bValidate ? 1 : -1);
                                        playSound(bValidate ? '/sounds/ok.mp3' : '/sounds/error.mp3');
                                        showCard();
                                    }

                                    function showCard(){
                                        oCurrentCard = drawCard();
                                        var sName = oCurrentCard.name;
                                        $("#cardname").stop(true, true).fadeOut("fast", function() {
                                            $("#cardname").text(sName).fadeIn("fast", function() {
                                                bClickOk = true;
                                            });
                                            Fun.cardIn("#cardname");
                                        });
                                    }

                                    function timeUp(){
                                        if (sPhase != "turn") {
                                            return;
                                        }
                                        //La carte en cours compte comme ratee, sans penalite
                                        if (oCurrentCard !== null) {
                                            recordCard(oCurrentCard, false, 0);
                                            oCurrentCard = null;
                                        }
                                        endGame();
                                    }

                                    function startSet(){
                                        if (sPhase != "intro") {
                                            return;
                                        }
                                        nextGame();
                                    }

                                    function initSet(){
                                        $(".step").text(step);
                                    }

                                    function endSet(){
                                        sPhase = "setEnd";
                                        $("#spanpause").hide();
                                        $("#endinggame").slideUp("slow");
                                        $("#endingset").show();

                                        step++;
                                        if ((step-1) == nbSets){
                                            sPhase = "finished";
                                            var arrWinners = getWinners(arrScores);
                                            var sWin = (arrWinners.length > 1 ? TXT_DRAW_GAME : TXT_CONGRATULATIONS) + " " + arrWinners.join(",");
                                            $("#finishset").text(sWin);
                                            $("#finish").empty()
                                                .append($("<a class='btn btn-primary' href='#'></a>").text(TXT_PLAY_AGAIN).on("click", function (e) {
                                                    e.preventDefault();
                                                    window.location.reload();
                                                }))
                                                .append(document.createTextNode("   "))
                                                .append($("<a class='btn btn-primary' href='/'></a>").text(TXT_HOME));
                                            Fun.celebrate(arrWinners);
                                        }
                                    }

                                    function nextSet(){
                                        if (sPhase != "setEnd") {
                                            return;
                                        }
                                        sPhase = "intro";
                                        initSet();
                                        $(".btnstart").val(teamGoLabel(iTeam));
                                        $("#intro").show();
                                        $("#endingset").hide();
                                    }

                                    function initGame(){
                                        if (sPhase != "turnEnd") {
                                            return;
                                        }
                                        nextGame();
                                    }

                                    function endGame(){
                                        if (sPhase != "turn") {
                                            return;
                                        }
                                        sPhase = "turnEnd";
                                        stopTimer();
                                        endClock();
                                        bClickOk = false;
                                        $('#progress').val(-99);
                                        $("#game").slideUp("slow");

                                        var oList = $("#list").empty();
                                        arrCardsTeam.forEach(function (item, index) {
                                            oList.append(
                                                $('<li class="pointer list-group-item"></li>')
                                                    .addClass(item.find ? "list-group-item-success" : "list-group-item-warning")
                                                    .append($("<span></span>").text(item.card.name).on("click", function () {
                                                        changeThis(index, this);
                                                    }))
                                            );
                                        });
                                        refreshTeamScore();
                                        $("#spanpause").hide();
                                        $("#endinggame").show();

                                        playSound('/sounds/beep.mp3');
                                        if (arrCardsTeam.some(function (item) { return item.find; })) {
                                            Fun.confetti();
                                        }

                                        iTeam++;
                                        if (iTeam > nbTeam){
                                            iTeam = 1;
                                            iSet++;
                                            $(".btnstepend").val(TXT_ENDSET);
                                        } else {
                                            $(".btnstepend").val(teamGoLabel(iTeam));
                                        }
                                        $(".btnstart").val(teamGoLabel(iTeam));
                                    }

                                    function refreshTeamScore(){
                                        var iFound = arrCardsTeam.filter(function (item) { return item.find; }).length;
                                        $("#score").text(iFound + "/" + arrCardsTeam.length + " ");
                                    }

                                    //Correction en fin de tour : la carte etait finalement trouvee / ratee
                                    function changeThis(index, oSpan){
                                        if (sPhase != "turnEnd") {
                                            return;
                                        }
                                        var item = arrCardsTeam[index];
                                        var iOldDelta = item.delta;
                                        item.find = !item.find;
                                        item.delta = item.find ? 1 : item.missDelta;
                                        addScore(item.team, item.delta - iOldDelta);

                                        if (item.find) {
                                            //Plus besoin de la rejouer
                                            removeById(arrMissedCards, item.card.id);
                                            removeById(arrCardsForThisSet, item.card.id);
                                        } else {
                                            arrMissedCards.push(item.card);
                                        }

                                        playSound('/sounds/pass.mp3');
                                        $(oSpan).parent().toggleClass('list-group-item-warning', !item.find).toggleClass('list-group-item-success', item.find);
                                        refreshTeamScore();
                                    }

                                    function nextGame(){
                                        arrCardsTeam = [];

                                        $(".intro").slideUp("slow");
                                        $("#endingset").hide();
                                        $("#endinggame").hide();

                                        if (iSet == step){
                                            endSet();
                                        }else{
                                            sPhase = "turn";
                                            setPause(false);
                                            stopTimer();
                                            iTimeLeft = iTimeLimit;
                                            $("#progress").val(iTimeLeft);
                                            $("#chrono").text(iTimeLeft);
                                            $("#game").show();
                                            $("#spanpause").show();
                                            initClock(iTimeLimit);
                                            showCard();
                                            progressTimeout = setTimeout(progress, 1000);
                                        }
                                    }

                                    function startMatch(){
                                        if (nbTeam < 3){
                                            $(".player3").hide();
                                        }
                                        if (nbTeam < 4){
                                            $(".player4").hide();
                                        }

                                        step = 1;
                                        iTeam = 1;
                                        iSet = 0;
                                        arrScores = [];
                                        for (var k = 1; k <= nbTeam; k++){
                                            arrScores.push(0);
                                            $("#total-" + k).text(0);
                                        }
                                        $(".btnstart").val(teamGoLabel(iTeam));
                                        arrMissedCards = [];
                                        // Cartes jamais jouees d'abord, puis les plus anciennement jouees
                                        arrCardsForThisSet = leastPlayedFirst(arrCardsForThisMatch);
                                        initSet();
                                        sPhase = "intro";
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
