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
                                        Brainstorm
                                    </h4>
                                </div>
                            </div>

                            <div class="card-body">
                                <div id="intro" class="intro slider">
                                    <h3>{{ __("messages.Lookfor")}}<span class="steph3"><span class="step">1</span>/{{$nbcards}}</span><h3>
                                    <p>{!! __("messages.goal_brainstorm")!!}.
                                    </p>
                                    <br/>
                                    <input onclick="startSet()" type="button" value="{{ __("messages.Team 1 Start")}}" class="btnstep btn btn-primary" />
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

                                <div id="endinggame" class="inv brainstorm">
                                    <div>
                                        <h3>{{ __("messages.Set")}} <span class="step"></span></h3>
                                        <h4>{!! __("messages.Lookfor")!!} !</h4>
                                        <p><span id="score">0</span> {{ __("messages.sentences_found")}}<br/>
                                        <ul id="list" class="list-group">

                                        </ul>
                                        </p>

                                    </div>

                                    <input onclick="initGame()" type="button" value="Equipe suivante" class="btnstep btnstepend btn btn-primary" />
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
                                        <input onclick="nextSet()" type="button" value="{{ __("messages.Next Set")}}" class="btnstep btn btn-primary" />
                                    </div>

                                </div>

                                <script>
                                    var arrCardsTeam = [];
                                    var step = 0;
                                    var nbTeam = {{ $nbteams}};
                                    var nbCards = {{ $nbcards}};
                                    var iTeam= 1;
                                    var iCard = 0;
                                    $(".btnstep").val("{{ __("messages.Team")}} "+iTeam+", {{ __("messages.go")}} !");
                                    $("#chrono2").hide();
                                    var score1 = 0;
                                    var score2 = 0;
                                    var score3 = 0;
                                    var score4 = 0;
                                    var bClickOk = true;
                                    var arrCardsForThisSet = [];
                                    var arrCardsForThisGame = [];
                                    var arrCardsForThisMatch = [];
                                    var arrCardsForThisMatchId = [];
                                    var iScore = 0;

                                    var iCardDeck ={{ $nbcards}};
                                    var iTimeLimit = 30;
                                    var bPause = false;
                                    document.getElementById('nbcheck').innerText = '0';

                                    function progress() {
                                        var val = 1;
                                        if (bPause){
                                            val = 0;
                                        }
                                        var ava = document.getElementById("progress");

                                        if ($('#progress').val()<=iTimeLimit && $('#progress').val()>=1) {
                                            $('#progress').val($('#progress').val()-val);
                                            $("#chrono").html($('#progress').val());
                                            setTimeout(function(){ progress(); }, 1000);
                                        }else{
                                            if ($('#progress').val()!=-99){
                                                var audio = new Audio('/sounds/finish.mp3');
                                                audio.play();
                                            }
                                        }
                                    }

                                    function pause(audio){
                                        $("#btn_pause").toggleClass("fa-play");
                                        $("#btn_pause").toggleClass("fa-pause");
                                        if (bPause){
                                            bPause = false;
                                        }else{
                                            bPause = true;
                                        }
                                        if (audio) {
                                            var audio = new Audio('/sounds/pause.mp3');
                                            audio.play();
                                        }
                                    }

                                    // This works on all devices/browsers, and uses IndexedDBShim as a final fallback
                                    var indexedDB = window.indexedDB || window.mozIndexedDB || window.webkitIndexedDB || window.msIndexedDB || window.shimIndexedDB;

                                    //Get cards for country in indexedDB
                                    var arrAllCards = [];

                                    // Open (or create) the database
                                    var open = indexedDB.open("MyGames", 1);

                                    var i=0;
                                    open.onsuccess = function() {
                                        // Start a new transaction
                                        var db = open.result;
                                        var tx = db.transaction("brainstorm");
                                        var store = tx.objectStore("brainstorm");

                                        //Get the cards for this game
                                        store.openCursor().onsuccess = function(event) {
                                            var cursor = event.target.result;
                                            if (cursor) {
                                                arrAllCards.push(cursor.value);
                                                cursor.continue();
                                            }else{
                                                if (arrAllCards.length == 0) {
                                                    alert("{{ __("messages.ErrorGetCard")}}");
                                                    window.location.href="/?error=getcard";
                                                }else{
                                                    getCards();
                                                }
                                            }
                                        };


                                        // Close the db when the transaction is done
                                        tx.oncomplete = function() {
                                            db.close();
                                        };
                                    }

                                    function updateCard(oItem){
                                        var db = open.result;
                                        var objectStore = db.transaction(["brainstorm"], "readwrite").objectStore("brainstorm");
                                        var request = objectStore.get(oItem.id);
                                        request.onerror = function(event) {
                                            // Gestion des erreurs!
                                        };
                                        request.onsuccess = function(event) {
                                            // On récupère l'ancienne valeur que nous souhaitons mettre à jour
                                            var data = request.result;

                                            // On met à jour ce(s) valeur(s) dans l'objet
                                            data.created = sToday;

                                            // Et on remet cet objet à jour dans la base
                                            var requestUpdate = objectStore.put(data);
                                            requestUpdate.onerror = function(event) {
                                                // Faire quelque chose avec l’erreur
                                            };
                                            requestUpdate.onsuccess = function(event) {
                                                // Succès - la donnée est mise à jour !
                                            };
                                        };
                                    }

                                    //Get cards for this game
                                    function getCards(){
                                        arrCardsForThisMatch = [];
                                        arrCardsForThisMatchId = [];
                                        var iTry = 0;
                                        if (arrAllCards.length <nbTeam*iCardDeck){
                                            alert("{{ __("messages.ErrorGetCard")}}.");
                                        }else{
                                            while (arrCardsForThisMatch.length<nbTeam*iCardDeck){
                                                var item = arrAllCards[Math.floor(Math.random()*arrAllCards.length)];
                                                if (!arrCardsForThisMatchId.includes(item.id)){
                                                    //We try 5 times to play with not the same cards for the same day
                                                    //It they have not enough card, we take...
                                                    if (item.created == sToday && iTry < 4){
                                                        iTry++;
                                                    }else{
                                                        updateCard(item);
                                                        arrCardsForThisMatch.push(item);
                                                        arrCardsForThisMatchId.push(item.id);
                                                    }
                                                }
                                            }

                                            startMatch();
                                        }
                                    }

                                    function shuffleCards(){
                                        shuffle(arrCardsForThisMatch);
                                    }

                                    function nextCard(bValidate){
                                        if (bClickOk){
                                            bClickOk = false;
                                            document.getElementById('nbcheck').innerText = '0';
                                            $("#cardname").fadeOut("fast", function() {
                                                $("#cardname").html("");
                                                $("#cardwords").html();
                                            });
                                            arrCardsForThisGame.shift();

                                            arrCardsTeam.push({name:$("#cardname").html(),find:bValidate});

                                            if (bValidate){
                                                var audio = new Audio('/sounds/ok.mp3');
                                                audio.play();
                                                iScore = $(".form-check-input:checked").length;

                                                switch(iTeam){
                                                    case 1:
                                                        score1 = score1+iScore;
                                                        break;
                                                    case 2:
                                                        score2 = score2+iScore;
                                                        break;
                                                    case 3:
                                                        score3 = score3+iScore;
                                                        break;
                                                    case 4:
                                                        score4 = score4+iScore;
                                                        break;
                                                }
                                                iCard++;

                                                $("#total-"+iTeam).html(parseInt($("#total-"+iTeam).html())+iScore);

                                                var indexCard = -1;
                                                var k = 0;
                                                while (indexCard == -1 && k < arrCardsForThisSet.length){
                                                    var oCard = arrCardsForThisSet[k];
                                                    if (oCard.name == $("#cardname").html()){
                                                        indexCard = k;
                                                    }
                                                    k=k+1;
                                                }
                                                arrCardsForThisSet.splice(indexCard, 1);
                                            }else{
                                                var audio = new Audio('/sounds/error.mp3');
                                                audio.play();
                                            }

                                            endGame();
                                        }
                                    }

                                    function showCard(){
                                        if (arrCardsForThisGame.length>0){
                                            $("#nbcards").html(arrCardsForThisGame.length);

                                            $("#cardname").fadeIn("fast", function() {
                                                $("#cardname").html(arrCardsForThisGame[0].name);
                                                var sList = "";
                                                var item = JSON.parse(arrCardsForThisGame[0].description);
                                                for (var k=1; k<= 10;k++){
                                                    sList = sList+"<li><label class='form-check-label' for='item"+k+"'><input id='item"+k+"' type='checkbox' class='form-check-input' value='1'/>&nbsp;&nbsp;"+eval("item.word"+k)+"</label></li>";
                                                }

                                                $("#cardwords").html(sList);
                                                bClickOk = true;
                                                $('.form-check-input').change(function() {
                                                    let nbCheck = $(".form-check-input:checked").length
                                                    document.getElementById('nbcheck').innerText = nbCheck;
                                                });
                                            });
                                        }
                                    }


                                    function startSet(){
                                        nextSet();
                                        initGame();
                                    }

                                    function initSet(){
                                        $(".step").html(step);
                                    }

                                    function endSet(){
                                        iCard = 0;
                                        $("#spanpause").hide();
                                        $("#endinggame" ).slideUp( "slow" );
                                        $("#endingset").show();

                                        step++;
                                        if ((step-1)==nbCards){
                                            //Who has win ?
                                            score1 = parseInt($("#total-1").html());
                                            score2 = parseInt($("#total-2").html());
                                            score3 = parseInt($("#total-3").html());
                                            score4 = parseInt($("#total-4").html());
                                            var score = score1;
                                            if (score2>score){
                                                score=score2;
                                            }
                                            if (score3>score){
                                                score=score3;
                                            }
                                            if (score4>score){
                                                score=score4;
                                            }

                                            var bDraw = false;
                                            var sWin = "";
                                            if (score == score1){
                                                sWin = sWin + "1";
                                            }
                                            if (score == score2){
                                                if (sWin != ""){
                                                    sWin = sWin + ",";
                                                    bDraw = true;
                                                }
                                                sWin = sWin + "2";
                                            }
                                            if (score == score3 && nbTeam>2){
                                                if (sWin != ""){
                                                    sWin = sWin + ",";
                                                    bDraw = true;
                                                }
                                                sWin = sWin + "3";
                                            }
                                            if (score == score4 && nbTeam>3){
                                                if (sWin != ""){
                                                    sWin = sWin + ",";
                                                    bDraw = true;
                                                }
                                                sWin = sWin + "4";
                                            }

                                            if (bDraw){
                                                sWin = "{{ __("messages.Draw game")}} "+sWin;
                                            }else{
                                                sWin = "{{ __("messages.Congratulations Team")}} "+sWin;
                                            }
                                            $("#finishset").html(sWin);
                                            $("#finish").html("<a class='btn btn-primary' href='#' onclick='window.location.reload();'>{{ __("messages.Play again")}}</a>&nbsp;&nbsp;&nbsp;<a class='btn btn-primary' href='/'>{{ __("messages.Home")}}</a>");
                                            var audio = new Audio('/sounds/finish.mp3');
                                            audio.play();
                                        }
                                    }

                                    function prepareTimer() {
                                        pause(false);
                                        $("#cardwords").hide();
                                        $("#chrono").hide();
                                        $("#chrono2").html(6);
                                        $("#chrono2").show();
                                        $(".clash-card__unit-stats").hide();
                                        updatePauseTimer();
                                    }

                                    function updatePauseTimer() {
                                        let iTimePause = parseInt($("#chrono2").html()) -1;
                                        if (iTimePause <= 0) {
                                            $("#cardwords").show();
                                            $("#chrono").show();
                                            $("#chrono2").hide();
                                            $(".clash-card__unit-stats").show();
                                            $("#progress").val(iTimeLimit);
                                            pause(false);
                                        } else {
                                            $("#chrono2").html(iTimePause);

                                            setTimeout(function(){
                                                updatePauseTimer();
                                            }, 1000);
                                        }
                                    }

                                    function nextSet(){
                                        initSet();
                                        $("#intro").show();
                                        $("#endingset").hide();
                                    }

                                    function initGame(){
                                        arrCardsForThisGame = arrCardsForThisSet.slice();

                                        if (iCard == nbTeam){
                                            endSet();
                                        }else{
                                            nextGame();
                                        }
                                    }

                                    function endGame(){
                                        $('#progress').val(-99);
                                        $("#game" ).slideUp( "slow" );
                                        $("#score").html(iScore+"/10 ");

                                        sList = "";
                                        arrCardsTeam.forEach (function(item){
                                            sInfo = "warning";
                                            if (item.find){
                                                sInfo = "success";
                                            }
                                            sList = sList+ '<li class="list-group-item list-group-item-'+sInfo+'">'+item.name+'</li>';
                                        });
                                        $("#list").html(sList);
                                        $("#spanpause").hide();
                                        $("#endinggame").show();

                                        var audio = new Audio('/sounds/beep.mp3');
                                        audio.play();

                                        iTeam++;
                                        $(".btnstep").val("{{ __("messages.Team")}} "+iTeam+", {{ __("messages.go")}} !");
                                        if (iTeam > {{ $nbteams}}){
                                            iTeam = 1;
                                            $(".btnstep").val("{{ __("messages.Team")}} "+iTeam+", {{ __("messages.go")}} !");
                                            $(".btnstepend").val("{{ __("messages.Endset")}}");
                                        }
                                    }

                                    function nextGame(){
                                        shuffle(arrCardsForThisSet);
                                        arrCardsOK = [];
                                        arrCardsTeam = [];
                                        iScore = 0;

                                        $( ".intro" ).slideUp( "slow" );
                                        $("#endingset").hide();
                                        $("#endinggame").hide();

                                        if (arrCardsForThisSet.length==0){
                                            endSet();
                                        }else{
                                            $("#progress").val(iTimeLimit);
                                            $("#game").show();
                                            $("#spanpause").show();
                                            //initClock(iTimeLimit);
                                            showCard();
                                            progress();
                                            prepareTimer();
                                        }
                                    }

                                    function renewCard() {
                                        var item = arrAllCards[Math.floor(Math.random()*arrAllCards.length)];
                                        if (arrCardsForThisMatchId.includes(item.id)){
                                            var iTry = 0;
                                            while (arrCardsForThisMatch.length<nbTeam*iCardDeck){
                                                var item = arrAllCards[Math.floor(Math.random()*arrAllCards.length)];
                                                if (!arrCardsForThisMatchId.includes(item.id)){
                                                    //We try 5 times to play with not the same cards for the same day
                                                    //It they have not enough card, we take...
                                                    if (item.created == sToday && iTry < 4){
                                                        iTry++;
                                                    }
                                                }
                                            }
                                        }

                                        //updateCard(item);
                                        arrCardsForThisGame.shift();
                                        arrCardsForThisGame.push(item);

                                        var indexCard = -1;
                                        var k = 0;
                                        var cardId = -1;
                                        while (indexCard == -1 && k < arrCardsForThisSet.length){
                                            var oCard = arrCardsForThisSet[k];
                                            if (oCard.name == $("#cardname").html()){
                                                indexCard = k;
                                                cardId = oCard.id;
                                            }
                                            k=k+1;
                                        }
                                        arrCardsForThisSet.splice(indexCard, 1);
                                        arrCardsForThisSet.push(item);

                                        indexCard = -1;
                                        k = 0;
                                        while (indexCard == -1 && k < arrCardsForThisMatch.length){
                                            var oCard = arrCardsForThisMatch[k];
                                            if (oCard.name == $("#cardname").html()){
                                                indexCard = k;
                                                cardId = oCard.id;
                                            }
                                            k=k+1;
                                        }
                                        arrCardsForThisMatch.splice(indexCard,1);
                                        arrCardsForThisMatch.push(item);

                                        indexCard = -1;
                                        k = 0;
                                        while (indexCard == -1 && k < arrCardsForThisMatchId.length){
                                            var oCard = arrCardsForThisMatchId[k];
                                            if (oCard == cardId){
                                                indexCard = k;
                                            }
                                            k=k+1;
                                        }
                                        arrCardsForThisMatchId.splice(indexCard,1);
                                        arrCardsForThisMatchId.push(item.id);

                                        showCard();
                                        $("#chrono").html(iTimeLimit);
                                        $("#chrono2").html(6);
                                        $('#progress').val(iTimeLimit);
                                    }

                                    function startMatch(){
                                        if ({{$nbteams}}<3){
                                            $(".player3").hide();
                                        }
                                        if ({{$nbteams}}<4){
                                            $(".player4").hide();
                                        }

                                        step=1;
                                        iTeam= 1;
                                        $(".btnstep").val("{{ __("messages.Team")}} "+iTeam+", {{ __("messages.go")}} !");
                                        score1 = 0;
                                        score2 = 0;
                                        score3 = 0;
                                        score4 = 0;
                                        iCard = 0;
                                        for (var k=1;k<=4;k++){
                                            $("#total-"+k).html(0);
                                        }
                                        shuffleCards();
                                        arrCardsForThisSet = arrCardsForThisMatch.slice();
                                        initSet();
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
