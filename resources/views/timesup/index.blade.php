@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default globalpanel" style="text-align:center">
                <div class="panel-heading">Time's up - {{ __("messages.Set")}} <span class="step">1</span></div>

				<script>
				var iCardDeck =20;
				var iTimeLimit = 30;
				</script>
                <div class="panel-body" >
					<div id="intro1" class="intro slider">
						<h1>{{ __("messages.Describe")}} !</h1>
                        <br/>
						<p>{{ __("messages.goal_timesup1")}}.
						</p>
						<br/>
						<input onclick="startSet(1)" type="button" value="{{ __("messages.Team 1 Start")}}" class="btnstep btn btn-primary" />
					</div>

					<div id="intro2" class="intro inv">
						<h1>{{ __("messages.Oneword")}} !</h1>
                        <br/>
                        <p>{{ __("messages.goal_timesup2")}}.
						</p>
						<br/>
						<input onclick="startSet(2)" type="button" value="{{ __("messages.Team 1 Start")}}" class="btnstep btn btn-primary" />
					</div>

					<div id="intro3" class="intro inv">
						<h1>{{ __("messages.Mime")}} !</h1>
                        <br/>
						<p>{{ __("messages.goal_timesup3")}}.
						<p>.
						</p>
						<br/>
						<input onclick="startSet(3)" type="button" value="{{ __("messages.Team 1 Start")}}" class="btnstep btn btn-primary" />
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
                                        <div class="one-third">
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
							<h1>{{ __("messages.Set")}} <span class="step"></span></h1>
							<p><span id="score">0</span><br/>
								<input onclick="initGame()" type="button" value="{{ __("messages.Next Team")}}" class="pointer btnstep btn btn-primary" /><br/>
								<ul id="list" class="list-group">

								</ul>
							</p>
						</div>
					</div>

					<div id="endingset" class="inv timesup">
						<div>
							<h1>{{ __("messages.Set")}} <span class="step"></span></h1>
							<h2 id="finishset">{{ __("messages.Endset")}}</h2>
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
							<input onclick="nextSet()" type="button" value="{{ __("messages.Next Set")}}" class="pointer btnstep btn btn-primary" />
						</div>

					</div>

					<script>
						var arrCardsTeam = [];
						var step = 0;
						var nbTeam = {{ $nbteams}};
						var iTeam= 1;
						$(".btnstep").val("Equipe "+iTeam+", allez-y !");
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
						var progressTimeout = null;
						var bPause = false;
						function progress() {
							var val = 1;
							if (bPause){
								val = 0;
							}
							var ava = document.getElementById("progress");

							if ($('#progress').val()<=iTimeLimit && $('#progress').val()>=1) {
								$('#progress').val($('#progress').val()-val);
								$("#chrono").html($('#progress').val());

								progressTimeout = setTimeout(function(){ progress(); }, 1000);
							}else{
								if ($('#progress').val()!=-99){
									endGame();
								}
							}
						}

						function pause(){
							$("#btn_pause").toggleClass("fa-play");
							$("#btn_pause").toggleClass("fa-pause");
							if (bPause){
								bPause = false;
							}else{
								bPause = true;
							}
							var audio = new Audio('/sounds/pause.mp3');
							audio.play();
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
							var tx = db.transaction("timesup");
							var store = tx.objectStore("timesup");

							//Get the cards for this game
							store.openCursor().onsuccess = function(event) {
								var cursor = event.target.result;
								if (cursor) {
									arrAllCards.push(cursor.value);
									cursor.continue();
								}else{
									if (arrAllCards.length == 0) {
										alert("{{ __("messages.ErrorGetCard")}}.");
										window.location.href="/";
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
							var objectStore = db.transaction(["timesup"], "readwrite").objectStore("timesup");
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
								$(".btnplay").hide();
								$("#cardname").fadeOut("fast", function() {
									$("#cardname").html("");
								});
								arrCardsForThisGame.shift();

								arrCardsTeam.push({name:$("#cardname").html(),find:bValidate});

								if (bValidate){
									var audio = new Audio('/sounds/ok.mp3');
									audio.play();
									iScore++;
									switch(iTeam){
										case 1:
											score1++;
											break;
										case 2:
											score2++;
											break;
										case 3:
											score3++;
											break;
										case 4:
											score4++;
											break;
									}

									$("#total-"+step+"-"+iTeam).html(parseInt($("#total-"+step+"-"+iTeam).html())+1);
									$("#total-"+iTeam).html(parseInt($("#total-"+iTeam).html())+1);

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

								showCard();
								$(".btnplay").show();

								if (arrCardsForThisGame.length==0){
									endGame();
								}
							}
						}

						function showCard(){
							if (arrCardsForThisGame.length>0){
								$("#nbcards").html(arrCardsForThisGame.length);

								$("#cardname").fadeIn("fast", function() {
									$("#cardname").html(arrCardsForThisGame[0].name);
									bClickOk = true;
								});
							}
						}


						function startSet(pStep){
							step = pStep;
							nextSet();
							initGame();
						}

						function initSet(){
							shuffleCards();
							arrCardsForThisSet = arrCardsForThisMatch.slice();
							$(".step").html(step);
						}

						function endSet(){
							$("#spanpause").hide();
							$("#endinggame" ).slideUp( "slow" );
							$("#endingset").show();
							step++;
							if (step==4){
								//Who has win ?
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

						function nextSet(){
							initSet();
							$("#intro"+step).show();
							$("#endingset").hide();

						}

						function initGame(){
							arrCardsForThisGame = arrCardsForThisSet.slice();
							nextGame();
						}

						function endGame(){
							endClock();
							$('#progress').val(-99);
							$("#game" ).slideUp( "slow" );
							$("#score").html(iScore+"/"+arrCardsTeam.length +" ");
							sList = "";
							arrCardsTeam.forEach (function(item){
								sInfo = "warning";
								if (item.find){
									sInfo = "success";
								}
								sList = sList+ '<li class="pointer list-group-item list-group-item-'+sInfo+'"><span onclick="changeThis(this)">'+item.name+'</span>&nbsp;&nbsp;&nbsp;<a target="biography" href="https://{{ explode("_",$_COOKIE["locale"])[0]}}.wikipedia.org/wiki/'+biography(item.name)+'"><i class="fa fa-link"></i></a></li>';
							});
							$("#list").html(sList);
							$("#spanpause").hide();
							$("#endinggame").show();

							var audio = new Audio('/sounds/beep.mp3');
							audio.play();

							iTeam++;
							if (iTeam > {{ $nbteams}}){
								iTeam = 1;
							}
							$(".btnstep").val("Equipe "+iTeam+", allez-y !");
						}

						function changeThis(oItem){
							var iTeamtmp = iTeam-1;
							if (iTeamtmp==0){
								iTeamtmp = nbTeam;
							}
							var audio = new Audio('/sounds/pass.mp3');
							audio.play();
							if ($(oItem).parent().hasClass('list-group-item-warning')){
								//Card was finally found
								var oCard = null;
								var indexCard = -1;
								var k = 0;
								while (indexCard == -1 && k < arrCardsForThisGame.length){
									var oCard = arrCardsForThisGame[k];
									if (oCard.name == $(oItem).html()){
										indexCard = k;
									}
									k=k+1;
								}
								arrCardsForThisGame.splice(indexCard, 1);
								var indexCard = -1;
								var k = 0;
								while (indexCard == -1 && k < arrCardsForThisSet.length){
									var oCard = arrCardsForThisSet[k];
									if (oCard.name == $(oItem).html()){
										indexCard = k;
									}
									k=k+1;
								}
								arrCardsForThisSet.splice(indexCard, 1);

								iScore++;
								$("#total-"+step+"-"+iTeamtmp).html(parseInt($("#total-"+step+"-"+iTeamtmp).html())+1);
								$("#total-"+iTeamtmp).html(parseInt($("#total-"+iTeamtmp).html())+1);

							}else{
								//Card was finally not found
								var oCard = null;
								var indexCard = -1;
								var k = 0;
								while (indexCard == -1 && k < arrCardsForThisMatch.length){
									var oCard = arrCardsForThisMatch[k];
									if (oCard.name == $(oItem).html()){
										indexCard = k;
									}
									k=k+1;
								}
								iScore--;
								$("#total-"+step+"-"+iTeamtmp).html(parseInt($("#total-"+step+"-"+iTeamtmp).html())-1);
								$("#total-"+iTeamtmp).html(parseInt($("#total-"+iTeamtmp).html())-1);
								arrCardsForThisGame.push(oCard);
								arrCardsForThisSet.push(oCard);
							}
							$(oItem).parent().toggleClass('list-group-item-warning');
							$(oItem).parent().toggleClass('list-group-item-success');

							$("#score").html(iScore+"/"+arrCardsTeam.length +" ");
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
								initClock(iTimeLimit);
								showCard();
								progress();
							}
						}

						function startMatch(){
							if ({{$nbteams}}<3){
								$(".player3").hide();
							}
							if ({{ $nbteams}}<4){
								$(".player4").hide();
							}

							step=1;
							iTeam= 1;
							$(".btnstep").val("{{ __("messages.Team")}} "+iTeam+", {{ __("messages.go")}} !");
							score1 = 0;
							score2 = 0;
							score3 = 0;
							score4 = 0;

							for (var k=1;k<=4;k++){
								$("#total-"+k).html(0);
								for (var j=1;j<=3;j++){
									$("#total-"+j+"-"+k).html(0);
								}
							}
							initSet();

						}

					</script>


                </div>

            </div>
			<div id="footer">
				<a onclick="if (window.confirm('{{ str_replace("'","\'",__("messages.back_to_homepage"))}} ?')){window.location.href='/';}" >{{ __("messages.back_to_homepage")}}</a>
			</div>
        </div>
    </div>
</div>
@endsection
