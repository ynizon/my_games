<h2><?php echo _("Cards") ;?></h2>

<form method="get" id="myform" action="<?php echo URL;?>/games/cards">
	<select name="id_mode" id="id_mode" onchange="document.getElementById('myform').submit();">
		<option value=""><?php echo _("Select your game");?></option>
		<?php 
		foreach ($games as $game) { 
			if ($game->mode != 4){//Not wolf
		?>
				<option <?php if ($game->mode == $id_mode){echo "selected";}?> value="<?php echo $game->mode;?>"><?php echo $game->name;?></option>
		<?php 
			}
		}
		?>
	</select>
	
	<?php
	if ($id_mode != 4 and $id_mode != 0){//Not wolf
	?>
		&nbsp;&nbsp;&nbsp;<a id="mylink" href="<?php echo URL;?>/cards/new?id_mode=<?php echo $id_mode;?>"><?php echo _('New card');?></a>
	<?php
	}
	
	if ($_SESSION["role"] == "admin"){
		?><br/><br/><a href="<?php echo URL;?>/cards/export"><?php echo _('Export');?></a>&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;
		<a href="<?php echo URL;?>/cards/import"><?php echo _('Import');?></a>
	<?php
	}
	?>
	<br/>
</form>

<?php 
foreach ($games as $game) { 
	if ($game->mode == $id_mode){
		$iNbCards = 0;
		foreach ($cards as $card) { 
			if ($card->mode == $game->mode){
				$iNbCards++;
			}
		}
		?>
		<h2><?php echo htmlspecialchars($game->name). " (".$iNbCards.")";?></h2>
		<ul>
		<?php 
		foreach ($cards as $card) { 
			if ($card->mode == $game->mode){
				?>
			  <li>
				<?php echo link_to(htmlspecialchars($card->name), 'cards', $card->id, 'edit') ?>
				- <a href="<?php echo url_for('cards', $card->id);?>" onclick="if (confirm('<?php echo _("Are you sure");?>?')) { var f = document.createElement('form'); f.style.display = 'none'; this.parentNode.appendChild(f); f.method = 'POST'; f.action = this.href; var m = document.createElement('input'); m.setAttribute('type', 'hidden'); m.setAttribute('name', '_method'); m.setAttribute('value', 'DELETE'); f.appendChild(m); f.submit(); };return false;"><?php echo _('Delete');?></a>
			  </li>
		<?php } 
		}
		?>
		</ul>
		<hr/>	
<?php 
	}
}
?>
