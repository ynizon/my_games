<div class="minwidth">
	<h2><?php echo link_to(_('Start a game'), 'parts/new') ?></h2>
	
	<?php
	if (count($parts)>0){
	?>
	<h2><?php echo _("My parts");?></h2>
	<ul>
		<?php foreach ($parts as $part) { ?>
		  <li>
			<?php echo  link_to($games[$part->id_game]." (". my_date($part->created). ")" ,"parts",$part->id,"go");?> - 
			<a href="<?php echo link_todelete("Delete",'parts', $part->id);?>" onclick="if (confirm('<?php echo _('Are you sure');?>?')) { var f = document.createElement('form'); f.style.display = 'none'; this.parentNode.appendChild(f); f.method = 'POST'; f.action = this.href; var m = document.createElement('input'); m.setAttribute('type', 'hidden'); m.setAttribute('name', '_method'); m.setAttribute('value', 'DELETE'); f.appendChild(m); f.submit(); };return false;"><?php echo _("Delete");?></a>
		  </li>
		<?php } ?>	
	</ul>
	<?php
	}
	?>
	
	<h2><?php echo _("Join your friends");?></h2>
	<form method="post">
		<?php echo _("Enter the code to join your friends");?><br/>
		<input type="text" name="share" value="" maxlength="6" size="6"/>&nbsp;
		<input type="submit" value="<?php echo _("Send");?>" />
	</form>


	<h2><?php echo _("Administration");?></h2>
	<ul>
		  <li><?php echo link_to(_('Cards'), 'games/cards') ?></li>
		  <?php
			if ($_SESSION["role"] == "admin"){
				if (count($cards) > 0){
					?>
					<li><?php echo link_to(_('Moderate cards') . "(".count($cards).")", 'cards') ?></li>
					<?php
				}
		  ?>
		  <li><?php echo link_to(_('Parts'), 'parts') ?></li>
		  
		  <li><?php echo link_to(_('Users'), 'users') ?></li>
		  <li><?php echo link_to(_('Games'), 'games') ?></li>
		  <?php
			}
		 ?>

	</ul>
</div>