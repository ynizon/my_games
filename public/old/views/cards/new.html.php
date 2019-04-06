<?php

echo html('cards/_form.html.php', null, array('card' => $card, 'method' => 'POST', 'action' => url_for('cards')));

?>
