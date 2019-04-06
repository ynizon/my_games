<?php

echo html('cards/_form.html.php', null, array('card' => $card, 'method' => 'PUT', 'action' => url_for('cards', $card->id)));

?>
