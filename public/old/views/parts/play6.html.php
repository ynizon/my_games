<?php
//PETIT Meurtre
$tabDescription = unserialize($part->description);
$tabPersons = explode("\n",htmlspecialchars($card->persons));
$bGuilty = false;
$tabGuilty = $tabDescription["guilty"];

if ($tabGuilty[$card->id] == $_GET["player"]){
	$bGuilty = true;
}
?>
<div class="minwidth">
	<div class="fl">
		<h2>
			<?php echo _('Player');?> : 
			<?php 
			echo $part->next_player . " - ".$tabPersons[$_GET["player"]-1];
			?>
		</h2>		
	</div>
	<div style="text-align:center;margin:auto;clear:both;">
		<div>
			<div class="fl" style="width:75%">
				<?php
				if ($bGuilty){
					?>
						<div class="guilty_msg">
							<?php echo _("You are guilty.");?>
						</div>
					<?php
				}else{
					?>
						<div class="innocent_msg">
							<?php echo _("You are innocent.");?>
						</div>
					<?php		
				}
				?>
			</div>
			<div class="fr">
				<input type="button" onclick="start()" class="btn_next" id="btn_next" />
			</div>
		</div>
		<div class="clear10"></div>
		
		<progress class="hideforstart progressall" id="progress" value="0" max="<?php if ($part->round == 1){echo "6";}else{echo "3";} ?>0"></progress>
	</div>
</div>

<div class="clear"></div>

<div class="card">
	
	<table class="mytable minwidth padtop">	
		<tr id="tr_lacarte">
			<td>
				<div style="height:200px;line-height:200px;" class="tc myborderround" id="lacarte">
					<?php
					if ($part->round == 1){
						if ($bGuilty){
							?>
							<h1><?php echo htmlspecialchars($card->word7);?></h1>
							<h1><?php echo htmlspecialchars($card->word8);?></h1>
							<h1><?php echo htmlspecialchars($card->word9);?></h1>
							<?php
						}else{
							?>
							<h1><?php echo htmlspecialchars($card->word1);?></h1>
							<h1><?php echo htmlspecialchars($card->word2);?></h1>
							<h1><?php echo htmlspecialchars($card->word3);?></h1>
							<?php
						}
					}else{
						if ($bGuilty){
							?>
							<h1><?php echo htmlspecialchars($card->word10);?></h1>
							<h1><?php echo htmlspecialchars($card->word11);?></h1>
							<h1><?php echo htmlspecialchars($card->word12);?></h1>
							<?php
						}else{
							?>
							<h1><?php echo htmlspecialchars($card->word4);?></h1>
							<h1><?php echo htmlspecialchars($card->word5);?></h1>
							<h1><?php echo htmlspecialchars($card->word6);?></h1>
							<?php
						}
					}
					?>					
				</div>				
			</td>
		</tr>
		
		<tr>
			<td>
				<div class="padtop" style="clear:both">
					<div class="fr">
						<input type="button" onclick="finished()" class="btn_ok hideforstart" id="btn_ok" />
					</div>
				</div>
			</td>
		</tr>
		
		<tr>
			<td class="tc">
				<div class="padtop">
					<input type="button" onclick="pause()" class="btn_pause hideforstart" />
				</div>
			</td>
		</tr>
	</table>	
	
	<div class="card_bottom">		
		<div class="fl">
			
		</div>		
	</div>
</div>

<script>	
	var bPause = false;
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
		$(".hideforstart").css("display","none");
	});
	
	function start() {	 
		if ($(window).height()<350){
			$("nav").css("display","none");
			$(".clear40").css("display","none");
		}
		progress();
		$("#btn_next").css("display","none");
		$(".hideforstart").css("display","");
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
	
	function finished(){
		var sUrl = "<?php echo URL;?>/parts/<?php echo $part->id;?>/post";
		$.post(sUrl, {player : <?php echo $_GET["player"];?>, id_part:<?php echo $part->id;?>}, function(data) {
			if (data != ""){
				alert("ERROR: " + data);
			}else{
				window.location.replace("<?php echo URL;?>/parts/<?php echo $part->id;?>/go");
			}
		 });
	}
	
</script>