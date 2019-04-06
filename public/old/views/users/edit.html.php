<?php
echo html('users/_form.html.php', null, array('user' => $user, 'method' => 'PUT', 'action' => URL."/users/".$user->id));

?>
