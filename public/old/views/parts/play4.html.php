<?php
//Wolf
$tabDescription = unserialize($part->description);
$sid_card = strtolower(str_replace(" ","_",htmlspecialchars($card->name))); 
?>
<div class="minwidth">
	<div class="fl">
		<h2>
			<?php echo _('Player');?> : 
			<?php 
			if ($next_button){
				echo $part->next_player;
			}else{
				echo $_SESSION["wolf_player"];
			}
			?>
		</h2>
	</div>
	<div class="fr" style="padding-top:10px">
		<img src="<?php echo URL;?>/public/images/wolf_<?php echo $sid_card;?>.png" />
	</div>
</div>
<br style="clear:both"/>

<div class="card">
	
	<table class="mytable minwidth padtop">
		<tr id="tr_lacarte">
			<td>
				<div class="tc myborderround heightwolf" id="lacarte">
					<h1 id="card_name"><?php echo _(htmlspecialchars($card->name));?></h1>					
				</div>				
			</td>
		</tr>
		
		<tr>
			<td>
				<div class="padtop" style="clear:both">
					<div class="fl">
						<p style="">
							<?php echo _("DESC_".htmlspecialchars($card->name));?>
						</p>
					</div>
					<div class="fr">
						<?php
						if ($next_button){
						?>
							<input type="button" onclick="finished()" class="btn_ok" id="btn_ok" />
						<?php
						}
						?>
					</div>
				</div>
			</td>
		</tr>
		
		<?php 
		if (!$next_button){?>
			<tr>
				<td class="tc">
					<div class="padtop" style="clear:both">
						<a href="<?php echo URL;?>/parts/<?php echo $part->id;?>/playwolf?mode=refresh"><img src="<?php echo URL;?>/public/images/refresh.png" /></a>
					</div>
				</td>
			</tr>
		<?php
		}
		?>
	</table>	
	
</div>

<script>	
	function finished(){
		var sUrl = "<?php echo URL;?>/parts/<?php echo $part->id;?>/post";
		$.post(sUrl, {id_part:<?php echo $part->id;?>}, function(data) {
			if (data != ""){
				alert("ERROR: " + data);
			}else{
				window.location.replace("<?php echo URL;?>/parts/<?php echo $part->id;?>/go");
			}
		 });
	}
	
</script>