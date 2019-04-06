<h2><?php echo _("Cards already in database");?></h2>
<ul>
<?php 
	foreach ($cards as $card) { 
		?>
		  <li>
			<?php echo htmlspecialchars($card->name); ?>
		  </li>
	<?php 
	}
	?>
</ul>	
