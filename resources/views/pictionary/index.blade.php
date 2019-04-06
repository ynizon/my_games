@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default globalpanel" style="text-align:center">
                <div class="panel-heading">Pictionary - <?php echo __("messages.Set");?> <span class="step">1</span>&nbsp;&nbsp;&nbsp;<span id="spanpause" class="inv" ><i onclick="pause()" id="btn_pause" class="fa fa-pause pointer" ></i></span></div>

                <div class="panel-body" >
					<div id="intro" class="intro slider">
						<h1><?php echo __("messages.Draw");?> <span class="step">1</span>/<?php echo $nbsets;?></h1>
						<p><?php echo __("messages.goal_pictionary");?>.						
						</p>
						<br/>
						<input onclick="startSet()" type="button" value="<?php echo __("messages.Team 1 Start");?>" class="btnstep btn btn-primary" />
					</div>
	
					<div id="game" class="pictionary inv">
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
						
						<div id="cardname">-</div>
						
						<div style="margin:auto;width:100px;">
							<i class="fa fa-check pointer btnplay arrondivalidate" id="validate" onclick="nextCard(true,true)"></i>	
							<br/>
							<i class="fa fa-close pointer btnplay arrondicancel" id="cancel" onclick="nextCard(false,true)"></i>							
						</div>
					</div>
					
					<div id="endinggame" class="inv pictionary">
						<div>
							<h1><?php echo __("messages.Set");?> <span class="step"></span></h1>
							<h2><?php echo __("messages.Lookfor");?> !</h2>
							<p><span id="score">0</span> <?php echo __("messages.sentences_found");?><br/>
								<ul id="list" class="list-group">
									
								</ul>
							</p>
							
						</div>
						
						<input onclick="initGame()" type="button" value="Equipe suivante" class="btnstep btnstepend btn btn-primary" />
					</div>
					
					<div id="endingset" class="inv pictionary">
						<div>
							<h1><?php echo __("messages.Set");?> <span class="step"></span></h1>
							<h2 id="finishset"><?php echo __("messages.Endset");?></h2>
							<table class="table table-striped">
								<thead>
									<tr>
										<td><b><?php echo __("messages.Teams");?></b></td>
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
							<input onclick="nextSet()" type="button" value="<?php echo __("messages.Next Set");?>" class="btnstep btn btn-primary" />
						</div>
						
					</div>
					
					<script>
						var arrCardsTeam = [];
						var step = 0;
						var nbTeam = <?php echo $nbteams;?>;
						var nbSets = <?php echo $nbsets;?>;
						var iTeam= 1;
						var iSet = 0;
						$(".btnstep").val("<?php echo __("messages.Team");?> "+iTeam+", <?php echo __("messages.go");?> !");
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
						
						var iTimeLimit = 30;
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
								setTimeout(function(){ progress(); }, 1000);
							}else{
								if ($('#progress').val()!=-99){
									nextCard(false,false);
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
							var tx = db.transaction("pictionary");
							var store = tx.objectStore("pictionary");
							
							//Get the cards for this game
							store.openCursor().onsuccess = function(event) {
								var cursor = event.target.result;
								if (cursor) {
									arrAllCards.push(cursor.value);
									cursor.continue();
								}else{
									if (arrAllCards.length == 0) {
										alert("<?php echo __("messages.ErrorGetCard");?>.");
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
							var objectStore = db.transaction(["pictionary"], "readwrite").objectStore("pictionary");
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
							var k=0;
							if (arrAllCards.length < 1){
								alert("<?php echo __("messages.ErrorGetCard");?>.");
							}else{
								while (arrCardsForThisMatch.length<arrAllCards.length){
									var item = arrAllCards[k];
									if (!arrCardsForThisMatchId.includes(item.id)){									
										arrCardsForThisMatch.push(item);
										arrCardsForThisMatchId.push(item.id);
									}
									k++;
								}
								
								startMatch();
							}
						}
						
						function shuffleCards(){
							shuffle(arrCardsForThisMatch);
						}
						
						//bScore is false when the time is done
						function nextCard(bValidate,bScore){
							if (bClickOk){
								bClickOk = false;
								
								$("#cardname").fadeOut("fast", function() {
									$("#cardname").html("");
								});
								arrCardsForThisGame.shift();
								
								arrCardsTeam.push({name:$("#cardname").html(),find:bValidate});
									
								iCard++;
								if (bValidate){
									var audio = new Audio('/sounds/ok.mp3');
									audio.play();
									iScore++;
									$("#total-"+iTeam).html(parseInt($("#total-"+iTeam).html())+1);
								}else{
									if (bScore){
										iScore--;
										var audio = new Audio('/sounds/error.mp3');
										audio.play();
										$("#total-"+iTeam).html(parseInt($("#total-"+iTeam).html())-1);
									}									
								}
								

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
								
								showCard();
								$(".btnplay").show();
								
								if (arrCardsForThisGame.length==0){
									endGame();
								}								
							}
						}
						
						function showCard(){
							if (arrCardsForThisGame.length>0){								
								$("#cardname").fadeIn("fast", function() {
									$("#cardname").html(arrCardsForThisGame[0].name);
									var item = JSON.parse(arrCardsForThisGame[0].description);

									bClickOk = true;
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
							if ((step-1)==nbSets){
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
									sWin = "<?php echo __("messages.Draw game");?> "+sWin;
								}else{
									sWin = "<?php echo __("messages.Congratulations Team");?> "+sWin;
								}
								$("#finishset").html(sWin);
								$("#finish").html("<a class='btn btn-primary' href='#' onclick='window.location.reload();'><?php echo __("messages.Play again");?></a>&nbsp;&nbsp;&nbsp;<a class='btn btn-primary' href='/'><?php echo __("messages.Home");?></a>");
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
							nextGame();							
						}
						
						function endGame(){
							endClock();
							$('#progress').val(-99);
							$("#game" ).slideUp( "slow" );
							
							sList = "";
							arrCardsTeam.forEach (function(item){
								sInfo = "warning";
								if (item.find){
									sInfo = "success";
								}
								sList = sList+ '<li class="pointer list-group-item list-group-item-'+sInfo+'"><span onclick="changeThis(this)">'+item.name+'</span></li>';
							});
							$("#list").html(sList);
							$("#score").html($(".list-group-item-success").length+"/"+arrCardsTeam.length +" ");
							$("#spanpause").hide();
							$("#endinggame").show();

							var audio = new Audio('/sounds/beep.mp3');
							audio.play();
							
							iTeam++;

							$(".btnstep").val("<?php echo __("messages.Team");?> "+iTeam+", <?php echo __("messages.go");?> !");
							if (iTeam > <?php echo $nbteams;?>){
								iTeam = 1;								
								iSet++;
								$(".btnstep").val("<?php echo __("messages.Team");?> "+iTeam+", <?php echo __("messages.go");?> !");
								$(".btnstepend").val("<?php echo __("messages.Endset");?>");								
							}
							
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
								$("#total-"+iTeamtmp).html(parseInt($("#total-"+iTeamtmp).html())-1);
								arrCardsForThisGame.push(oCard);
								arrCardsForThisSet.push(oCard);
							}
							$(oItem).parent().toggleClass('list-group-item-warning');
							$(oItem).parent().toggleClass('list-group-item-success');
								
							$("#score").html($(".list-group-item-success").length+"/"+arrCardsTeam.length +" ");
						}
						
						function nextGame(){
							shuffle(arrCardsForThisSet);
							arrCardsOK = [];
							arrCardsTeam = [];
							iScore = 0;
							
							$( ".intro" ).slideUp( "slow" );							
							$("#endingset").hide();
							$("#endinggame").hide();
							
							if (iSet==step){
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
							if (<?php echo $nbteams;?><3){
								$(".player3").hide();
							}
							if (<?php echo $nbteams;?><4){
								$(".player4").hide();
							}
							
							step=1;
							iTeam= 1;
							$(".btnstep").val("<?php echo __("messages.Team");?> "+iTeam+", <?php echo __("messages.go");?> !");
							score1 = 0;
							score2 = 0;
							score3 = 0;
							score4 = 0;
							iCard = 0;
							iSet = 0;
							for (var k=1;k<=4;k++){
								$("#total-"+k).html(0);								
							}
							initSet();
							
						}
						
					</script>
                </div>			
            </div>
			
			<div id="footer">
				<a onclick="if (window.confirm('<?php echo str_replace("'","\'",__("messages.back_to_homepage"));?> ?')){window.location.href='/';}" ><?php echo __("messages.back_to_homepage");?></a>
			</div>
        </div>
    </div>
</div>
@endsection