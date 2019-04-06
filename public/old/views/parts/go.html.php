<?php 
$tabDescription = unserialize($part->description);
$tabScore = unserialize($part->score);

$iNbPlayers = 0;
if ($part->nb_players > 0){
	$iNbPlayers = $part->nb_players;
}else{
	$iNbPlayers = $part->nb_teams;
}

?>
<h1><?php echo htmlspecialchars($games[$part->id_game]->name);?></h1>


<?php
if ($part->round <= $part->nb_rounds){
?>
	<div>
		<?php
		//If times up 
		if ($games[$part->id_game]->mode == 1){
			if (isset($tabDescription["end"])){											
				?>
				<h3><?php echo _("Round ended");?></h3>
				<?php
			}
		}				
		?>
		<div>
			<div class="fl">
				<h2><?php if ($part->nb_players > 0){echo _("Ready player");}else{echo _("Ready team");}?> <?php echo $part->next_player;?> ?
				</h2>				
			</div>
			<div class="fl bt_play" style="padding-left:15px;">
				<a href="<?php echo URL;?>/parts/<?php echo $part->id;?>/play" title="GO"><img src="<?php echo URL;?>/public/images/forward.png" /></a>
			</div>
		</div>
		<br style="clear:both" />
		<?php 
		if ($part->id_game == 4){
		?>
			<div>(<?php echo _("Give the phone to your opponent.");?>)</div>
		<?php
		}
		?>		
	</div>
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

<meta http-equiv="refresh" content="15">
<div class="minwidth">
	<h2><?php echo _("The game");?></h2>
	<p>
		<?php
		switch ($part->id_game){			
			case 3:
				echo _("TB");
				break;
				
			case 4:
				echo _("BRAINSTORM");
				break;
				
			case 5:
				echo _("LOUPGAROU");
				break;			
		}
		?>
	</p>
	<table class="mytable myborder">
		<?php
		if ($part->nb_teams == 0){
		?>
			<tr>
				<td>
					<?php echo _('Players');?> :
				</td>
				<td>
					<?php echo $part->nb_players;?>
				</td>
			</tr>
		<?php
		}else{
		?>
			<tr>
				<td>
					<?php echo _('Teams');?> :
				</td>
				<td>
					<?php echo $part->nb_teams;?>
				</td>
			</tr>
		<?php
		}
		
		if ($part->round <= $part->nb_rounds){
		?>
			<tr>
				<td>
					<?php echo _('Round');?> :
				</td>
				<td>
					<?php
					echo $part->round;?>/<?php echo $part->nb_rounds;				
					?>
				</td>
			</tr>		
		<?php
		}
		?>		
	</table>
	
	
<?php
if ($part->id_game == 5){
	//WOLF
	if ($_SESSION["id"] == $part->id_user){
		$sid_card = strtolower(str_replace(" ","_",$card->name)); 
		?>
		<h2><?php echo _("Remember it");?></h2>
		<table class="mytable myborder minwidth">
			<tr class="tableheader">
				<td>
					<?php echo _('Players');?> :
				</td>
				<td>
				</td>
			</tr>
			
			
			<?php 			
			for ($i = 1; $i <= $iNbPlayers;$i++){
				$sid_card = strtolower(str_replace(" ","_",$all_cards[$cards[$i-1]])); 
			?>
				<tr>
					<td class="tc">					
						<?php 
						echo $i;
						if ($i < $part->next_player){
							?>
							&nbsp;&nbsp;&nbsp;
							<img src="<?php echo URL;?>/public/images/check.png" />
							<?php
						}
						?>					
					</td>
					<td class="tc">					
						<a style="cursor:pointer;" onclick="if (window.confirm('<?php echo _("Viewing player");?> <?php echo $i;?>')){showMe(<?php echo $i;?>)}"><img src="<?php echo URL;?>/public/images/forward.png" /></a>
						&nbsp;
						<img class="player" id="player_<?php echo $i;?>" src="" />
					</td>				
				</tr>
			<?php
			}
			?>
		</table>
		<script>
			$(document).ready(function () {	  
				$(".player").css("display","none");
				<?php 
				for ($i = 1; $i <= $iNbPlayers;$i++){
					$card = $cards[$i-1];
					$sid_card = strtolower(str_replace(" ","_",$all_cards[$cards[$i-1]])); 
					?>
					$("#player_<?php echo $i;?>").attr("src","<?php echo URL;?>/public/images/wolf_<?php echo $sid_card;?>.png");			
					<?php
				}
				?>
				
			});
			
			function showMe(iPlayer){
				$("#player_"+iPlayer).css("display","");			
				setTimeout(function(){ $(".player").css("display","none"); }, 3000); 
			}
		</script>
	<?php
	}
}else{
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
		<?php 
		for ($k = 1; $k <= $part->nb_rounds;$k++){
		?>
			<tr>
				<td>
					<?php echo _('Round');?> <?php echo $k;?>					
				</td>
				<?php 
				for ($i = 1; $i <= $iNbPlayers;$i++){
				?>
					<td class="tc">
						<?php
						if ($part->round == $k and $part->next_player == $i){
							echo "<b>";
						}
						
						echo $tabScore[$i][$k];						
						
						if ($part->round == $k and $part->next_player == $i){
							echo "</b>";
						}
						?>
					</td>
				<?php
				}				
				?>
			</tr>
		<?php
		}
		?>
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
				for ($k = 1; $k <= $part->nb_rounds;$k++){
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
}
?>	
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