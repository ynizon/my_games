<?php 
//PETITMEURTRE
$tabDescription = unserialize($part->description);
$tabScore = unserialize($part->score);
$tabGuilty = $tabDescription["guilty"];
$iNbPlayers = $part->nb_players;

?>
<h1><?php echo $games[$part->id_game]->name;?></h1>

<script>
$(document).ready(function () {	  
	<?php
	if ($part->next_round<=2 and $part->round <= $part->nb_rounds){
	?>
		$("#ul_footer").html('<li><a href="#"><?php echo _("Player")." " . $part->round."/".$part->nb_players." - ". _("Round") . " " . $part->next_round."/2";?></a></li>');
	<?php
	}
	?>
});
</script>

<div class="minwidth">
	<?php
	if ($part->round <= $part->nb_rounds){
	?>
		<div>			
			<div>
				<div class="fl">
					<h2><?php echo _("The game");?></h2>
				</div>
				<?php
				if ($part->round > $part->nb_rounds){
				?>
					<div class="fr" style="padding-top:22px">
						<?php
						if ($_SESSION["id"] == $part->id_user){
						?>
							<a href="#" onclick="if(window.confirm('<?php echo _("You will reset the game");?>'){window.location.href='"<?php echo URL;?>/parts/<?php echo $part->id;?>/refresh';}"><img src="<?php echo URL;?>/public/images/refresh.png" /></a>
						<?php
						}
						?>	
					</div>
				<?php
				}
				?>
			</div>
		</div>
		
		<div class="clear"></div>	
		
		<p>
			<?php				
			echo _("PETITMEURTRE");
			$secondes = 60;
			if ($part->next_round == 2){
				$secondes = 30;
			}
			echo str_replace("@",$secondes,_("PETITMEURTRE_SEC"));
			?>
		</p>
		
		
		<div>
			<div class="fl">
				<h2 style="margin-top:0"><?php echo _("The story");?></h2>
			</div>
			<?php
			if ($part->next_player == 1 and $part->next_round == 1){
			?>
				<div class="fr" >
					<?php
					if ($_SESSION["id"] == $part->id_user){
					?>
						<a href="<?php echo URL;?>/parts/<?php echo $part->id;?>/change_story"><img src="<?php echo URL;?>/public/images/refresh.png" /></a>
					<?php
					}
					?>	
				</div>
			<?php
			}
			?>
		</div>
		
		<br style="clear:both"/ >
		<p>
			<?php 
			if ($card == null){
				echo _("No more stories");
			}else{
				echo htmlspecialchars($card->description);
			}
			?><br/>
		</p>
		
		<?php
		$tabPersons = array();
		$tabPersons = explode("\n",htmlspecialchars($card->persons));
		if ($part->next_round <= 2){	
			?>
			<h2><?php echo _("Persons to investigate");?></h2>
			<ul style="list-style:none;padding:0;">
				<?php
				$i = 1;			
				foreach ($tabPersons as $sPerson){			
					if (trim($sPerson) != "" and $i <= $part->nb_players){
						$bDone = false;
						if (isset($tabDescription["players"])){
							if (in_array($i,$tabDescription["players"][$part->id])){
								$bDone = true;
							}
						}
						?><li style="padding-bottom:5px">
							<?php
							if ($bDone){
								?>
								<img src="<?php echo URL;?>/public/images/check.png" />
								<?php echo ($i). " - " .$sPerson;?>
								<?php
							}else{
								?>
								<a href="<?php echo URL;?>/parts/<?php echo $part->id;?>/play?player=<?php echo $i;?>" title="GO"><img src="<?php echo URL;?>/public/images/forward.png" /></a>&nbsp;
								<a href="<?php echo URL;?>/parts/<?php echo $part->id;?>/play?player=<?php echo $i;?>" title="GO"><?php echo ($i). " - " .$sPerson;?></a>
								<?php
							}
							?>						
						</li>
						<?php
					}
					$i++;
				}
				?>
			</ul>	
		<?php
		}else{
			?>
			<h2><?php echo _("Player") . " " .$part->next_player." - ". _("Choose the guilty");?></h2>
			<ul style="list-style:none;padding:0;">
				<?php
				$i = 1;
				foreach ($tabPersons as $sPerson){			
					if (trim($sPerson) != "" and $i <= $part->nb_players){
						$bDone = false;
						if (isset($tabDescription["players"])){
							if (in_array($i,$tabDescription["players"][$part->id])){
								$bDone = true;
							}
						}
						?>
						<li style="padding-bottom:5px" >
							<div style="cursor:pointer" onclick="chooseGuilty(<?php echo $i;?>)">
								<img src="<?php echo URL;?>/public/images/forward.png" class="guilty" id="guilty_<?php echo $i;?>"/>&nbsp;
								<?php echo ($i). " - " .$sPerson;?>
							</div>
						</li>
						<?php
					}
					$i++;
				}
				?>
			</ul>
			<script>
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
				var iGuilty = "<?php echo $tabGuilty[$card->id];?>";
				function chooseGuilty(iPlayer){
					if (bAudio){
						if (iGuilty == iPlayer){
							var audio = new Audio('<?php echo URL;?>/public/sounds/next.mp3');
						}else{
							var audio = new Audio('<?php echo URL;?>/public/sounds/pass.mp3');
						}
						audio.play();
					}
					$(".guilty").attr('src',"<?php echo URL;?>/public/images/uncheck.png");
					$("#guilty_"+iGuilty).attr('src',"<?php echo URL;?>/public/images/check.png");
					
					
					var bFound = false;
					if (iGuilty == iPlayer){
						bFound = true;
					}
					var sUrl = "<?php echo URL;?>/parts/<?php echo $part->id;?>/change_story";
					$.post(sUrl, { found: bFound, id_part:<?php echo $part->id;?>}, function(data) {			 				 
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
		
	<?php
	}else{
		//Look for best player
		$sBestPlayer = "";
		$tabBestPlayer = array();
		$iTopScore = 0;
		for ($i = 1; $i <= $iNbPlayers;$i++){
			$iScore = 0;
			for ($k = 1; $k <= $part->nb_rounds;$k++){
				$iScore = $iScore + $tabScore[$i][$k];
			}
			if ($iScore > $iTopScore){
				$iTopScore = $iScore;			
			}
		}
		for ($i = 1; $i <= $iNbPlayers;$i++){
			$iScore = 0;
			for ($k = 1; $k <= $part->nb_rounds;$k++){
				$iScore = $iScore + $tabScore[$i][$k];
			}
			if ($iScore == $iTopScore){
				$tabBestPlayer[] = $i;
			}
		}
		if (count($tabBestPlayer)>1){
			foreach ($tabBestPlayer as $i){
				if ($sBestPlayer != ""){
					$sBestPlayer .= ",";
				}
				$sBestPlayer .= $i;
			}
			if ($part->nb_players > 0){
				$sBestPlayer = _("Congratulations players")." ".$sBestPlayer;
			}else{
				$sBestPlayer = _("Congratulations teams")." ".$sBestPlayer;
			}
		}else{
			if ($part->nb_players > 0){
				$sBestPlayer = _("Congratulations player")." ".$tabBestPlayer[0];
			}else{
				$sBestPlayer = _("Congratulations team")." ".$tabBestPlayer[0];
			}
		}
		?>
		<div>
			<div>
				<div class="fl">
					<h3><?php echo _("Game ended");?><br/><?php echo $sBestPlayer;?></h3>
					<?php
					if ($_SESSION["id"] == $part->id_user){
					?>
						<a href="<?php echo URL;?>/parts/<?php echo $part->id;?>/refresh"><img src="<?php echo URL;?>/public/images/refresh.png" /></a>
					<?php
					}
					?>
				</div>
			</div>
			<br style="clear:both" />
		</div>
	<?php
	}
	?>
	
	
	<h2><?php echo _("Score");?></h2>
	
	<table class="mytable myborder minwidth">
		<tr class="tableheader">
			<td>
				<?php echo _('Players');?> :
			</td>
			<?php 
			for ($i = 1; $i <= $iNbPlayers;$i++){
			?>
				<td class="tc">					
					<?php echo $i;?>					
				</td>
			<?php
			}
			?>
		</tr>
		
		<tr>
			<td>
				<?php echo _('Total');?>
			</td>
			<?php
			//Look for top score
			$iTopScore = 0;
			for ($i = 1; $i <= $iNbPlayers;$i++){
				$iScore = 0;
				for ($k = 1; $k <= $part->nb_rounds;$k++){
					$iScore = $iScore + $tabScore[$i][$k];
				}
				if ($iScore > $iTopScore){
					$iTopScore = $iScore;
				}
			}
			
			for ($i = 1; $i <= $iNbPlayers;$i++){
			?>
			<td class="tc">
				<?php 
				$iScore = 0;
				for ($k = 1; $k <= 1;$k++){
					$iScore = $iScore + $tabScore[$i][$k];
				}
				if ($iTopScore == $iScore){echo "<b>";}
				echo $iScore;
				if ($iTopScore == $iScore){echo "</b>";}
				?>
			</td>
			<?php
			}
			?>
		</tr>
	</table>
	
	<?php
	if ($part->id_user == $_SESSION["id"]){
	?>
		<h2><?php echo _("Share");?></h2>
		<p>
			<?php
			echo _("Give this code to your friends"). " : ".$part->password; 
			?>
		</p>
	<?php
	}
	?>
</div>