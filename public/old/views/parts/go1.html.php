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
		if (isset($tabDescription["end"])){
			if ($part->round > 2){
			?>
				<h3><?php echo _("Round ended");?></h3>
			<?php
			}else{
				?>
				<h3><?php echo _("The game will start");?></h3>
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
		$sBestPlayer = _("Congratulations players")." ".$sBestPlayer;
	}else{
		$sBestPlayer = _("Congratulations player")." ".$tabBestPlayer[0];
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
	<?php
	if ($part->round<$part->nb_rounds){
	?>
		<h2><?php echo _("The game");?></h2>
		<p>
			<?php		
				echo _("TS_".$part->round);		
			?>
		</p>	
	<?php
	}
	
	if ($part->round > 1){
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
			for ($k = 2; $k <= $part->nb_rounds;$k++){
			?>
				<tr>
					<td>
						<?php echo _('Round');?> <?php echo $k-1;?>					
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
					for ($k = 2; $k <= $part->nb_rounds;$k++){
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
		if ($part->nb_players % 2 != 0){
		?>
			<h2><?php echo _("Score star");?></h2>
			
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
				for ($k = 2; $k <= $part->nb_rounds;$k++){
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
								
								$iCumul = 0;
								$iCumul = $iCumul + $tabScore[$i][$k];
								if ($i == 1){
									$iCumul = $iCumul + $tabScore[$iNbPlayers][$k];
								}else{
									$iCumul = $iCumul + $tabScore[$i-1][$k];
								}
								echo $iCumul;
								
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
							$iCumul = 0;
							$iCumul = $iCumul + $tabScore[$i][$k];
							if ($i == 1){
								$iCumul = $iCumul + $tabScore[$iNbPlayers][$k];
							}else{
								$iCumul = $iCumul + $tabScore[$i-1][$k];
							}
							$iScore = $iScore + $iCumul;
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
							$iCumul = 0;
							$iCumul = $iCumul + $tabScore[$i][$k];
							if ($i == 1){
								$iCumul = $iCumul + $tabScore[$iNbPlayers][$k];
							}else{
								$iCumul = $iCumul + $tabScore[$i-1][$k];
							}
							$iScore = $iScore + $iCumul;
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

<?php
if ($part->round > $part->nb_rounds){
	if ($part->id_game == 1 or $part->id_game == 2){
		?>
		<div>
			<h2><?php echo _("Learn them");?></h2>
			<table class="mytable myborder minwidth">
				<?php
				foreach ($cards_removed as $card){
				?>
					<tr>
						<td>
							<a target="_blank" href='https://<?php echo substr($card->language,0,2);?>.wikipedia.org/wiki/<?php echo str_replace(" ","_",htmlspecialchars($card->name));?>'><?php echo $card->name;?></a> (<?php echo _("Removed");?>) 
						</td>
					</tr>	
				<?php			
				}
				foreach ($cards as $card){
				?>
					<tr>
						<td>
							<a target="_blank" href='https://<?php echo substr($card->language,0,2);?>.wikipedia.org/wiki/<?php echo str_replace(" ","_",htmlspecialchars($card->name));?>'><?php echo $card->name;?></a>
						</td>
					</tr>	
				<?php			
				}
				?>
			</table>
		</div>
<?php
	}
}
?>