<?php

echo html('games/_form.html.php', null, array('game' => $game, 'method' => 'POST', 'action' => url_for('games')));

?>
