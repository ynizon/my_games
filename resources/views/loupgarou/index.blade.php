@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default globalpanel" style="text-align:center">
                <div class="panel-heading">Loups Garous de Thiercelieux</div>

                <div class="panel-body" >
					<div id="intro" class="intro">
						<?php echo __("messages.There is");?> <?php echo $nbwolfs;?> <?php echo __("messages.Wolfs");?>.<br/>
						<?php echo __("messages.CardSelect");?>:<br/>
						<ul id="list" class="list-group">

						</ul>
						<input onclick="nextCard()" type="button" value="Découvrir les rôles" class="btnstep btn btn-primary" />
					</div>

					<div id="game" class="brainstorm inv">
						<div style="margin:10px;">
							<h1><?php echo __("messages.Player");?> <span class="step">X</span></h1>
							<p><?php echo __("messages.You play this role");?>
								<span id="cardname">-</span>.<br/>
								<img id="cardpic" src="" /><br/><br/>
								<span id="carddesc">-</span><br/>
							</p>
						</div>
						<br/>
						<div style="margin:auto;width:100px;">
							<i class="fa fa-check pointer btnplay arrondivalidate" id="validate" onclick="endCard()"></i>
						</div>
					</div>

					<div id="endinggame" class="inv brainstorm">
						<div>
							<h1><?php echo __("messages.This is the night");?></h1>

							<p>
								<?php echo __("messages.Master Take Control");?>
								<br/>
								<span id="master"></span><br/>
								<img src="/images/wolf_capitaine.png" /><br/>
							</p>
						</div>

						<input onclick="window.location.reload();" type="button" value="Rejouer" class="btnstep btn btn-primary" />
						&nbsp;&nbsp;&nbsp;<a class='btn btn-primary' href='/'><?php echo __("messages.Home");?></a>
					</div>

					<script>
						var step = 0;
						var iCard = 0;
						var bClickOk = true;
						var arrCardsForThisGame = [];
						var arrCardsForThisMatch = [];
						var arrCardsForThisMatchId = [];
						var translations = [];
						<?php
						//Translations
						foreach ($cards as $card){
							?>
							var oItem = {id:<?php echo $card->id;?>,name:"<?php echo $card->name;?>",description:"<?php echo __("messages.Loup ".$card->name);?>"};
							translations.push(oItem);
							<?php
						}
						?>

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
							var tx = db.transaction("loupgaroudethiercelieux");
							var store = tx.objectStore("loupgaroudethiercelieux");

							//Get the cards for this game
							store.openCursor().onsuccess = function(event) {
								var cursor = event.target.result;
								if (cursor) {
									arrAllCards.push(cursor.value);
									cursor.continue();
								}else{
									if (arrAllCards.length == 0) {
										alert("<?php echo __("messages.ErrorGetCard");?>.");
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

						function findGetParameter(parameterName) {
							var result = null,
								tmp = [];
							location.search
								.substr(1)
								.split("&")
								.forEach(function (item) {
								  tmp = item.split("=");
								  if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
								});
							return result;
						}

						//Get cards for this game
						function getCards(){
							arrCardsForThisMatch = [];
							arrCardsForThisMatchId = [];

                            var nbPlayers = {{$nbplayers}};
                            var oCardWolf = null;
                            var oCardPeople = null;
                            var oCardBrotherOrSister = null;
                            var cardsId = <?php echo json_encode($cardsid);?>;
                            if (cardsId.length > nbPlayers) {
                                alert("<?php echo __("messages.TooSpecialCards");?>.");
                                window.location.href="/";
                            }
                            var k = 0;
                            var iSpecialCards = 0;
                            while (k < cardsId.length){
                                for (z =0; z<arrAllCards.length; z++){
                                    var item = arrAllCards[z];
                                    if (cardsId[k] == item.id){
                                        switch (item.name ){
                                            case "Villageois":
                                                oCardPeople = item;
                                                break;
                                            case "Loup Garou":
                                                oCardWolf = item;
                                                break;
                                            case "Loup Frères et Soeurs":
                                                oCardBrotherOrSister = item;
                                                break;
                                            default:
                                                iSpecialCards++;
                                                arrCardsForThisMatch.push(item);
                                                arrCardsForThisMatchId.push(item.id);
                                                break;
                                        }
                                    }
                                }

                                k++;
                            }

                            //Adding brothers and sisters
                            if (oCardBrotherOrSister != null){
                                for (k=0;k< 5;k++){
                                    var item = oCardBrotherOrSister;
                                    arrCardsForThisMatch.push(item);
                                    arrCardsForThisMatchId.push(item.id);
                                }
                            }

                            //Adding wolf
                            var nbwolfs = <?php echo $nbwolfs;?>;
                            for (k=0;k< nbwolfs;k++){
                                var item = oCardWolf;
                                arrCardsForThisMatch.push(item);
                                arrCardsForThisMatchId.push(item.id);
                            }

                            //Adding people
                            var nbpeople = <?php echo $nbplayers-$nbwolfs;?>-iSpecialCards;
                            for (k=0;k<nbpeople;k++){
                                var item = oCardPeople;
                                arrCardsForThisMatch.push(item);
                                arrCardsForThisMatchId.push(item.id);
                            }

							var item = Math.floor(Math.random()*arrCardsForThisMatch.length)+1;
							$("#master").html("<?php echo __("messages.The captain is");?> "+item);
							sList = "";
							arrCardsForThisMatch.forEach (function(item){
								var re = / /gi;
                                var re2 = /è/gi;
								var sBase = item.name.toLowerCase().replace(re,"-").replace(re2,"e");
								sList = sList+ '<li class="list-group-item"><img src="/images/wolf_'+sBase+'.png" />&nbsp;'+item.name+'</li>';
							});
							$("#list").html(sList);
							startGame();
						}

						/**
						 * Shuffles array in place. ES6 version
						 * @param {Array} a items An array containing the items.
						 */
						function shuffle(a) {
							for (let i = a.length - 1; i > 0; i--) {
								const j = Math.floor(Math.random() * (i + 1));
								[a[i], a[j]] = [a[j], a[i]];
							}
						}

						function shuffleCards(){
							shuffle(arrCardsForThisMatch);
						}

						function nextCard(){
							if (bClickOk){
								$("#intro").slideUp();
								$("#game").show();
								bClickOk = false;

								$("#cardname").fadeOut("fast", function() {
									$("#cardname").html("");
									$("#carddesc").html("");
									$("#cardpic").hide();
									$("#cardwords").html();
								});

								var audio = new Audio('/sounds/ok.mp3');
								audio.play();
								iCard++;
								step++;

								showCard();

							}
						}

						function showCard(){
							if (arrCardsForThisGame.length>0){
								$("#cardname").fadeIn("fast", function() {
									var re = / /gi;
									var sBase = arrCardsForThisGame[0].name.toLowerCase().replace(re,"_");
									$("#cardname").html(arrCardsForThisGame[0].name);
									var z = 0;
									while (z<translations.length){
										if (translations[z].id == arrCardsForThisGame[0].id){
											$("#carddesc").html(translations[z].description);
										}
										z++;
									}

									$("#cardpic").attr("src","/images/wolf_"+sBase+".png");
									$("#cardpic").show();

									bClickOk = true;
									arrCardsForThisGame.shift();
								});
							}
						}

						function endCard(){
							$("#game").hide();

							if (arrCardsForThisGame.length > 0){
								$("#intro").show();
								$(".step").html(step);
							}else{
								endSet();
							}

						}

						function endSet(){
							iCard = 0;
							$("#game" ).slideUp( "slow" );
							$("#endinggame").show();

							var audio = new Audio('/sounds/finish.mp3');
							audio.play();
						}

						function nextPlayer(){
							initSet();
							$("#intro").show();
							$("#endingset").hide();

						}

						function startGame(){
							step=1;
							iCard = 0;
							$("#intro").show();
							$(".step").html(step);
							shuffleCards();
							arrCardsForThisGame = arrCardsForThisMatch.slice();
						}

						function endGame(){
							$("#game" ).slideUp( "slow" );
							$("#endinggame").show();

							var audio = new Audio('/sounds/beep.mp3');
							audio.play();
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
