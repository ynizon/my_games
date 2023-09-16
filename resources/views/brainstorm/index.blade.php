@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default globalpanel" style="text-align:center">
                <div class="panel-heading">
					Brainstorm - {{ __("messages.Set")}} <span class="step">1</span>
				</div>

                <div class="panel-body" >
					<div id="intro" class="intro slider">
						<h1>{{ __("messages.Lookfor")}} <span class="step">1</span>/{{$nbcards}}</h1>
						<p>{{ __("messages.goal_brainstorm")}}.
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
                                            <i id="button3rd" class="fa-solid fa-trophy" ></i>

                                            <div id="nbcheck" class="stat-value"></div>
                                        </div>

                                        <div class="one-third">
                                            <span id="spanpause" class="inv" ><i onclick="pause()" id="btn_pause" class="fa fa-pause pointer" ></i></span>

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
							<h1>{{ __("messages.Set")}} <span class="step"></span></h1>
							<h2>{{ __("messages.Lookfor")}} !</h2>
							<p><span id="score">0</span> {{ __("messages.sentences_found")}}<br/>
								<ul id="list" class="list-group">

								</ul>
							</p>

						</div>

						<input onclick="initGame()" type="button" value="Equipe suivante" class="btnstep btnstepend btn btn-primary" />
					</div>

					<div id="endingset" class="inv brainstorm">
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
							shuffleCards();
							arrCardsForThisSet = arrCardsForThisMatch.slice();
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
							}
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
