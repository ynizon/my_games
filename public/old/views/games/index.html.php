<h2><?php echo _("Games");?></h2>
<?php echo link_to(_('New game'), 'games/new') ?>
<hr/>


<ul>
<?php foreach ($games as $game) { ?>
  <li>
    <?php echo link_to(htmlspecialchars($game->name), 'games', $game->id, 'edit') ?>
    - <a href="<?php echo url_for('games', $game->id);?>" onclick="if (confirm('<?php echo _("Are you sure");?>?')) { var f = document.createElement('form'); f.style.display = 'none'; this.parentNode.appendChild(f); f.method = 'POST'; f.action = this.href; var m = document.createElement('input'); m.setAttribute('type', 'hidden'); m.setAttribute('name', '_method'); m.setAttribute('value', 'DELETE'); f.appendChild(m); f.submit(); };return false;"><?php echo _('Delete');?></a>
  </li>
<?php } ?>
</ul>

