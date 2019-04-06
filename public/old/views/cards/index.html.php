<h2><?php  echo _('Moderation'); ?></h2>
<br/>

<?php 
foreach ($games as $game) { 
	?>
	<h2><?php echo htmlspecialchars($game->name);?></h2>
	<ul>
	<?php 
	foreach ($cards as $card) { 
		if ($card->mode == $game->mode){
			if ($card->status != 1){
			?>
			  <li>
				<?php echo link_to(htmlspecialchars($card->name), 'cards', $card->id, 'edit') ?>
				- <a href="<?php echo url_for('cards', $card->id);?>" onclick="if (confirm('<?php echo ("Are you sure");?>?')) { var f = document.createElement('form'); f.style.display = 'none'; this.parentNode.appendChild(f); f.method = 'POST'; f.action = this.href; var m = document.createElement('input'); m.setAttribute('type', 'hidden'); m.setAttribute('name', '_method'); m.setAttribute('value', 'DELETE'); f.appendChild(m); f.submit(); };return false;"><?php echo _('Delete');?></a>
			  </li>
	<?php 	} 
		}
	}
	?>
	</ul>
	<hr/>	
<?php 
}
?>