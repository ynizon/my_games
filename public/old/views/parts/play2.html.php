<?php
//Brainstorm
$tabDescription = unserialize($part->description);
?>

<div class="clear" ></div>

<div class="card">
	<div class="clear" ></div>
	<table class="mytable minwidth">
		<tr id="tr_lacarte">
			<td>
				<div style="" class="tc myborderround" id="lacarte">
					<h1 id="card_name" style="font-size:22px"></h1>									
				</div>
				
				<div class="clear"></div>
				
				<div style="" class="tc myborderround" >
					<table class="mytable" width="100%">
						<?php
						for ($i = 1; $i<= 10 ; $i++){
						?>
							<tr>
								<td><input type="checkbox" class="chk_game" onclick="checkCard()" value="1" id="chk_word_<?php echo $i;?>" name="chk_word[]" /></td>
								<td class="tl"><label for="chk_word_<?php echo $i;?>"><span style="font-size:18px;" id="word_<?php echo $i;?>"></span></td>
							</tr>
						<?php
						}
						?>
					</table>
				</div>
			</td>
		</tr>
		
		<tr>
			<td>
				<div class="padtop" style="clear:both">
					<div class="fr">
						<input id="btn_ok" type="button" onclick="next()" class="btn_ok" />
					</div>
				</div>
			</td>
		</tr>			
					
		<tr>
			<td class="tc">
				<div >
					<input type="button" onclick="pause()" class="btn_pause" />
				</div>
			</td>
		</tr>
	</table>
	
	<div class="card_bottom" style="padding-top:10px">		
		<div class="fl">
			<progress id="progress" class="progressall" value="0" max="60"></progress>
		</div>		
	</div>
	
</div>

<script>
	var cards = [];
	var cards1 = [];
	var cards2 = [];
	var cards3 = [];
	var cards4 = [];
	var cards5 = [];
	var cards6 = [];
	var cards7 = [];
	var cards8 = [];
	var cards9 = [];
	var cards10 = [];
	var cards_id = [];
	var cards_seen = [];
	<?php
	foreach ($cards as $card){
	?>
		cards.push("<?php echo str_replace('"',"'",htmlspecialchars($card->name));?>");
		cards1.push("<?php echo str_replace('"',"'",htmlspecialchars($card->word1));?>");
		cards2.push("<?php echo str_replace('"',"'",htmlspecialchars($card->word2));?>");
		cards3.push("<?php echo str_replace('"',"'",htmlspecialchars($card->word3));?>");
		cards4.push("<?php echo str_replace('"',"'",htmlspecialchars($card->word4));?>");
		cards5.push("<?php echo str_replace('"',"'",htmlspecialchars($card->word5));?>");
		cards6.push("<?php echo str_replace('"',"'",htmlspecialchars($card->word6));?>");
		cards7.push("<?php echo str_replace('"',"'",htmlspecialchars($card->word7));?>");
		cards8.push("<?php echo str_replace('"',"'",htmlspecialchars($card->word8));?>");
		cards9.push("<?php echo str_replace('"',"'",htmlspecialchars($card->word9));?>");
		cards10.push("<?php echo str_replace('"',"'",htmlspecialchars($card->word10));?>");
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
		$("#ul_footer").html('<li><a href="#"><?php echo _('Player');?> : <?php echo $part->next_player;?> - <?php echo _('Round');?> <?php echo $part->round;?>/<?php echo $part->nb_rounds;?></a></li>');
		if ($(window).height()<350){
			$("nav").css("display","none");
			$(".clear40").css("display","none");
		}
	});
	
	function show_card(){
		$('#card_name').text(cards[iCard]);
		$('#word_1').text(cards1[iCard]);
		$('#word_2').text(cards2[iCard]);
		$('#word_3').text(cards3[iCard]);
		$('#word_4').text(cards4[iCard]);
		$('#word_5').text(cards5[iCard]);
		$('#word_6').text(cards6[iCard]);
		$('#word_7').text(cards7[iCard]);
		$('#word_8').text(cards8[iCard]);
		$('#word_9').text(cards9[iCard]);
		$('#word_10').text(cards10[iCard]);
		$('#card_number').text(iCards);
	}
	
	function checkCard(){
		if (bAudio){
			var audio = new Audio('<?php echo URL;?>/public/sounds/next.mp3');
			audio.play();
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
			if (bAudio){
				var audio = new Audio('<?php echo URL;?>/public/sounds/beep.mp3');
				audio.play();
			}
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
	
	function next(){
		var iScore = $(".chk_game:checked").length;
		var sUrl = "<?php echo URL;?>/parts/<?php echo $part->id;?>/post";
		$.post(sUrl, { cards_seen: cards_seen, score:iScore, cards_id: cards_id, id_part:<?php echo $part->id;?>}, function(data) {			 				 
			if (data != ""){
				alert("ERROR: " + data);
			}else{
				window.location.replace("<?php echo URL;?>/parts/<?php echo $part->id;?>/go");
			}
		 });		
	}
	
	function finished(){
		if (bAudio){
			var audio = new Audio('<?php echo URL;?>/public/sounds/ding.mp3');
			audio.play();
		}		
	}
</script>