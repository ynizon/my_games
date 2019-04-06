<?php
//Animal
$tabDescription = unserialize($part->description);
?>

<div class="clear" ></div>

<div class="card">
	<div class="clear" ></div>
	<table class="mytable minwidth">
		<tr id="tr_lacarte">
			<td>
				<div style="" class="tc myborderround" id="lacarte">
					<h1 id="card_name" style="font-size:22px"><?php echo $card->name;?></h1>									
				</div>
				
				<div class="clear"></div>
				
				<div style="" class="tc myborderround" >
					<img id="myPicture" src="" style="width:100%;height:350px;"/>					
				</div>
			</td>
		</tr>
		
		<tr>
			<td>
				<div class="clear10" ></div>
					
				<div class="padtop" style="clear:both" id="btn_during_game">
					<div class="fl">							
						<input type="button" onclick="changeCard(0)" class="btn_kc" id="btn_kc" />
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
					
		<tr>
			<td class="tc">
				<div >
					<input type="button" onclick="pause()" class="btn_pause" />
				</div>
				<div id="preload">
					<?php
					for ($i = 1; $i<=12; $i++){
						$sField = "word".$i;
						?>
						<img src="<?php echo $card->$sField;?>" />
						<?php
					}
					?>
				</div>
			</td>
		</tr>
	</table>
	
	<div class="card_bottom" style="padding-top:10px">		
		<div class="fl">
				<progress id="progress" value="0" max="120"></progress>
			</div>
			<div class="fr">
				<?php echo _('Deck');?>: <span id="card_number"></span>		
			</div> 
	</div>
	
</div>

<script>	
	var bPause = false;
	var iCard = 0;
	var iCards = 12;
	var iScore = 0;
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
		$("#preload").css("display","none");
		show_card();
		progress();
		$("#ul_footer").html('<li><a href="#"><?php echo _('Player');?> : <?php echo $part->next_player;?> - <?php echo _('Round');?> <?php echo $part->round;?>/<?php echo $part->nb_rounds;?></a></li>');
		if ($(window).height()<350){
			$("nav").css("display","none");
			$(".clear40").css("display","none");
		}
	});
	
	function changeCard(i){
		iScore = iScore + i;
		show_card();
	}
	
	function show_card(){
		iCard++;
		iCards--;
		$('#card_number').text(iCards);
		$( "#myPicture" ).fadeOut( 500, function() {		
			switch (iCard){
				case 1:
					$("#myPicture").attr("src","<?php echo $card->word1;?>");
					break;
				case 2:
					$("#myPicture").attr("src","<?php echo $card->word2;?>");
					break;
				case 3:
					$("#myPicture").attr("src","<?php echo $card->word3;?>");
					break;
				case 4:
					$("#myPicture").attr("src","<?php echo $card->word4;?>");
					break;
				case 5:
					$("#myPicture").attr("src","<?php echo $card->word5;?>");
					break;
				case 6:
					$("#myPicture").attr("src","<?php echo $card->word6;?>");
					break;
				case 7:
					$("#myPicture").attr("src","<?php echo $card->word7;?>");
					break;
				case 8:
					$("#myPicture").attr("src","<?php echo $card->word8;?>");
					break;
				case 9:
					$("#myPicture").attr("src","<?php echo $card->word9;?>");
					break;
				case 10:
					$("#myPicture").attr("src","<?php echo $card->word10;?>");
					break;
				case 11:
					$("#myPicture").attr("src","<?php echo $card->word11;?>");
					break;
				case 12:
					$("#myPicture").attr("src","<?php echo $card->word12;?>");
					break;
				case 13:
					finished();
					break;
			}
			
			$( "#myPicture" ).fadeIn( 500, function() {
			});
		});
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
			finished();
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
		if (bAudio){
			var audio = new Audio('<?php echo URL;?>/public/sounds/ding.mp3');
			audio.play();
		}		
		
		var sUrl = "<?php echo URL;?>/parts/<?php echo $part->id;?>/post";
		$.post(sUrl, { score:iScore, id_part:<?php echo $part->id;?>}, function(data) {			 				 
			if (data != ""){
				alert("ERROR: " + data);
			}else{
				window.location.replace("<?php echo URL;?>/parts/<?php echo $part->id;?>/go");
			}
		});
	}
</script>