<h2><?php echo _("Parts");?></h2>
<?php
if (count($parts)>0){
?>
	<ul>
	<?php foreach ($parts as $part) { ?>
	  <li>
		<?php echo  link_to($games[$part->id_game]." (". my_date($part->created). ")" ,"parts",$part->id,"go");?> - 
		<a href="<?php echo link_todelete("Delete",'parts', $part->id);?>" onclick="if (confirm('<?php echo _("Are you sure");?>?')) { var f = document.createElement('form'); f.style.display = 'none'; this.parentNode.appendChild(f); f.method = 'POST'; f.action = this.href; var m = document.createElement('input'); m.setAttribute('type', 'hidden'); m.setAttribute('name', '_method'); m.setAttribute('value', 'DELETE'); f.appendChild(m); f.submit(); };return false;"><?php echo _('Delete');?></a>
	  </li>
	<?php } ?>
	</ul>
<?php
}
?>