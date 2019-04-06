<?php

echo html('games/_form.html.php', null, array('game' => $game, 'method' => 'PUT', 'action' => url_for('games', $game->id)));

?>
