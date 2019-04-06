<?php
//Pictionary
?>
<!-- Usefull for swipe -->
<script type="text/javascript" src="<?php echo URL;?>/public/js/jquery.mobile-1.4.5.min.js"></script>
<script>
	$(document).ready(function () {	 
		$("a").attr("rel","external");
		$("#ul_footer").html('<li><a href="#"><?php echo _('Player');?> : <?php echo $part->next_player;?> - <?php echo _('Round');?> <?php echo $part->round;?>/<?php echo $part->nb_rounds;?></a></li>');
	});
</script>
<?php

$tabDescription = unserialize($part->description);
?>
<div class="clear" ></div>
<div class="card">		
	<div class="clear" ></div>
	
	<table class="mytable minwidth">
		<tr id="tr_lacarte">
			<td>
				<div style="" class="tc myborderround" id="lacarte">
					<h1 id="card_name"></h1>									
				</div>				
			</td>
		</tr>
		
		<tr>
			<td>
				<div class="padtop" style="clear:both" id="btn_during_game">
					<div class="fl">
						<input type="button" onclick="changeCard(-1)" class="btn_kc" />
					</div>
					<div class="fr">
						<input type="button" onclick="changeCard(1)" class="btn_ok" />
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
				<div class="padtop" >
					<input type="button" onclick="pause()" class="btn_pause" />
				</div>
			</td>
		</tr>
	</table>
	
	<div class="padtop" style="display:none" id="summary">
		<h2><?php echo _("Summary");?></h2>
		<ul style="list-style:none;padding-left:10px;" id="summary-list">
		</ul>
	</div>
	
	
	<div class="card_bottom">		
		<div class="fl">
			<progress id="progress" class="progressall" value="0" max="60"></progress>
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
		
		$( "#tr_lacarte" ).on( "swiperight", swiperightHandler  );
		$( "#tr_lacarte" ).on( "swipeleft", swipeleftHandler  );
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
			if ($("#summary_"+i).attr("src") == "<?php echo URL;?>/public/images/check.png"){
				$("#summary_"+i).attr("src", "<?php echo URL;?>/public/images/forward.png");
				cards_seen[i] = 0;
			}else{
				$("#summary_"+i).attr("src", "<?php echo URL;?>/public/images/uncheck.png");
				cards_seen[i] = -1;
			}
		}
	}
	
	function show_card(){
		$('#card_name').text(cards[iCard]);
		$('#card_number').text(iCards);		
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
					iCard++;			
					if (iCard >= iCards.length){
						iCard = 0;
					}
					
					var ava = document.getElementById("progress");			
					if (ava.value == ava.max){
						showSummary();
					}else{
						show_card();
					}	
				}

				$("#lacarte").css('background',"#fff");
				$("#tr_lacarte").fadeIn( 1000, function() {							
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
			$("#btn_pass").css('display',"");
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