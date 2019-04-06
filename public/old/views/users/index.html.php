<h2><?php echo _("Users");?></h2>
<?php echo link_to(_('New user'), 'users/new') ?>
<hr/>

<ul>
<?php foreach ($users as $user) { ?>
  <li>
    <?php echo link_to(htmlspecialchars($user->email) . " (".$user->nb_parts.")", 'users', $user->id, 'edit') ?>
    - <a href="<?php echo link_todelete("Delete",'users', $user->id);?>" onclick="if (confirm('<?php echo _("Are you sure");?>?')) { var f = document.createElement('form'); f.style.display = 'none'; this.parentNode.appendChild(f); f.method = 'POST'; f.action = this.href; var m = document.createElement('input'); m.setAttribute('type', 'hidden'); m.setAttribute('name', '_method'); m.setAttribute('value', 'DELETE'); f.appendChild(m); f.submit(); };return false;"><?php echo _("Delete");?></a>
  </li>
<?php } ?>
</ul>

