<?php
//Times' up 
$tabDescription = unserialize($part->description);

if ($part->round == 1){
	?>	
	<h2><?php echo _('Player');?> : <?php echo $part->next_player;?></h2>
	<script type="text/javascript">
		function chkCards(){
			r = true;
			if ($(".chkcard:checked").length!=2){
				alert('<?php echo str_replace("'"," ",_('You need to remove 2 cards'));?>');
				r = false;
			}
			
			return r;
		}
	</script>
	<form method="post" onsubmit="return chkCards()" action="<?php echo URL;?>/parts/<?php echo $part->id;?>/post">		
		<p>
			<?php echo _('You need to remove 2 cards from your deck. Memorize the others.');?>
		</p>
		<table class="formtable">
			<?php
			$iCard = 0;
			$sListeCard = array();
			foreach ($cards as $card){
				if ($iCard  < $part->nb_cards){
					$sListeCard[]= $card->id;
			?>
					<tr>
						<td>
							<div class="fl">
								<input name="chkcard[]" id="chkcard_<?php echo $card->id;?>" class="chkcard" type="checkbox" value="<?php echo $card->id;?>" />
							</div>
							<div class="fl" style="padding-left:10px;font-size:18px">
								<label for="chkcard_<?php echo $card->id;?>" ><?php echo htmlspecialchars($card->name);?></label>
							</div>
						</td>
					</tr>
			<?php
				}
				$iCard++;
			}
			?>
			<tr>
				<td class="tc">
					<input type="hidden" name="cards_seen" value="<?php echo str_replace('"',"'",serialize($sListeCard));?>" />
					<input type="submit" class="mybutton" value="<?php echo _("Save");?>"/>
				</td>
			</tr>
		</table>
	</form>
	<?php
}else{
?>
	<!-- Usefull for swipe -->
	<script type="text/javascript" src="<?php echo URL;?>/public/js/jquery.mobile-1.4.5.min.js"></script>
	<script>
		$(document).ready(function () {	 
			$("a").attr("rel","external");
			$("#ul_footer").html('<li><a href="#"><?php echo _('Player');?> : <?php echo $part->next_player;?> - <?php echo _('Round');?> <?php echo $part->round-1;?>/<?php echo $part->nb_rounds-1;?></a></li>');
		});
	</script>
	<div class="clear10" ></div>
	<div class="card">
		<div class="clear" ></div>
		<table class="mytable minwidth">	
			<tr id="tr_lacarte">
				<td>
					<div class="tc myborderround heighttimesup" id="lacarte">
						<h1 id="card_name"></h1>				
					</div>					
				</td>
			</tr>
			
			<tr>
				<td>
					<div class="clear10" ></div>
					
					<div class="padtop" style="clear:both" id="btn_during_game">
						<div class="fl">							
							<input type="button" onclick="changeCard(-1)" class="btn_kc" id="btn_kc" />
						</div>
						<div class="fr">
							<input type="button" onclick="changeCard(1)" class="btn_ok" id="btn_ok" />
						</div>
					</div>
					
					<div class="padtop" style="display:none;clear:both" id="btn_after_game">
						<div class="fr">
							<input type="button" onclick="changeCard(0)" class="btn_next" id="btn_next" />
						</div>
					</div>
				</td>
			</tr>			
						
			<tr class="tr_pause">
				<td class="tc">
					<div class="clear10" ></div>
					<div class="padtop">
						<input type="button" onclick="pause()" class="btn_pause" />
					</div>
				</td>
			</tr>
		</table>
		
		<div style="padding-top:20px;display:none" id="summary">
			<h2><?php echo _("Summary");?></h2>
			<ul style="list-style:none;padding-left:10px;" id="summary-list">
			</ul>
		</div>
		
		<div class="clear10" ></div>
		
		<div class="card_bottom">			
			<div class="fl">
				<progress id="progress" value="0" max="60"></progress>
			</div>
			<div class="fr">
				<?php echo _('Deck');?>: <span id="card_number"></span>		
			</div>
		</div>
		
	</div>

	<script>
		var cards = [];
		var cards_id = [];
		var cards_seen = [];
		<?php
		foreach ($cards as $card){
		?>
			cards.push("<?php echo htmlspecialchars($card->name);?>");
			cards_id.push("<?php echo $card->id;?>");
			cards_seen.push(0);
		<?php
		}
		?>
		var bPause = false;
		var iCard = 0;
		var iCards = cards.length;
		<?php
		if ($user->sound == 1){
			?>
			var bAudio = true;
			<?php
		}else{
			?>
			var bAudio = false;
			<?php
		}
		?>
		
		
		$(document).ready(function () {	 
			show_card();
			progress();
			<?php
			if ($part->round <= 2){
				?>
				$("#btn_kc").css('display','none');				
				<?php
			}else{
				?>
				$( "#tr_lacarte" ).on( "swipeleft", swipeleftHandler  );
				<?php
			}
			?>
						
			$( "#tr_lacarte" ).on( "swiperight", swiperightHandler  );
		});
		
		function swipeleftHandler ( event ){			
			changeCard(-1);
		}
		
		function swiperightHandler ( event ){
			changeCard(1);			
		}
		  
		function showSummary(){
			$("#btn_kc").css('display','');
			$(".card_bottom").css('display','none');
			$(".tr_pause").css('display','none');
			$("#btn_after_game").css('display','');
			$("#btn_during_game").css('display','none');
			$("#summary").css('display','');
			for (i = 0; i < cards_seen.length; i++){
				if (cards_seen[i] != 0){
					var sSeen = "";
					switch (cards_seen[i]){
						case -1:
							sSeen = "uncheck.png";
							break;
						case 1:
							sSeen = "check.png";
							break;
					}
					
					$("#summary-list").append("<li style='cursor:pointer' onclick='correctMe("+i+")'><img id='summary_"+i+"' src='<?php echo URL;?>/public/images/"+sSeen+"' />&nbsp;&nbsp;"+cards[i]+"</li>");
				}
			}
			
			if (bAudio){
				var audio = new Audio('<?php echo URL;?>/public/sounds/beep.mp3');
				audio.play();
			}	
		}
		
		function correctMe(i){
			if ($("#summary_"+i).attr("src") == "<?php echo URL;?>/public/images/uncheck.png"){
				$("#summary_"+i).attr("src", "<?php echo URL;?>/public/images/check.png");
				cards_seen[i] = 1;
			}else{
				$("#summary_"+i).attr("src", "<?php echo URL;?>/public/images/uncheck.png");
				cards_seen[i] = -1;
			}
		}
		
		function show_card(){
			$('#card_name').text(cards[iCard]);
			$('#card_number').text(iCards);
			cards_seen[iCard] = -1; //Not found by default			
		}
		
		function changeCard(iBtn){
			if (iBtn == 0){
				finished();
			}else{
				switch (iBtn){
					case -1:
						$("#lacarte").css('background',"#D41D1D");
						if (bAudio){
							var audio = new Audio('<?php echo URL;?>/public/sounds/pass.mp3');
							audio.play();
						}
						break;
											
					case 1:
						$("#lacarte").css('background',"#06A51B");
						if (bAudio){
							var audio = new Audio('<?php echo URL;?>/public/sounds/next.mp3');
							audio.play();
						}
						break;
				}
				$( "#tr_lacarte" ).fadeOut( 500, function() {								
					cards_seen[iCard] = iBtn;					

					//Rest of cards
					var iNb = 0;
					for (i = 0; i < cards_seen.length; i++){
						if (cards_seen[i] == 0){
							iNb++;
						}
					}
					iCards = iNb;
					
					if (iCards == 0){
						showSummary();
					}else{
						var ava = document.getElementById("progress");			
						if (ava.value == ava.max){
							showSummary();
						}else{
							iCard++;			
							if (iCard >= iCards.length){
								iCard = 0;
							}
							show_card();
						}				
					}
					
					$("#lacarte").css('background',"#fff");
					$( "#tr_lacarte" ).fadeIn( 1000, function() {			
					});
				});
			}							
		}
		
		function progress() {
			var val = 1;
			if (bPause){
				val = 0;
			}
			var ava = document.getElementById("progress");
			if((ava.value+val)<=ava.max && (ava.value+val)>=0) {
				ava.value += val;
				setTimeout(function(){ progress(); }, 1000);
			}else{
				$("#lacarte").css('background',"#ccc");
				$("#card_name").html("<?php echo _("Time is up");?>");				
				showSummary();
			}
		}
		
		function pause(){
			if (bPause){
				bPause = false;
				$(".btn_pause").toggleClass("btn_pause2");
			}else{
				bPause = true;
				$(".btn_pause").toggleClass("btn_pause2");
			}
			var audio = new Audio('<?php echo URL;?>/public/sounds/pause.mp3');
			audio.play();
		}
		
		function finished(){
			var sUrl = "<?php echo URL;?>/parts/<?php echo $part->id;?>/post";
			$.post(sUrl, { cards_seen: cards_seen, cards_id: cards_id, id_part:<?php echo $part->id;?>}, function(data) {			 				 
				if (data != ""){
					alert("ERROR: " + data);
				}else{
					window.location.replace("<?php echo URL;?>/parts/<?php echo $part->id;?>/go");
				}
			 });
		}
		
	</script>
	
<?php
}
?>