<?php
# GET /game
function games_index() {
	if (!isset($_SESSION["id"])){redirect2(URL);}
    set('games', find_games());
    return html('games/index.html.php');
}

# GET /games/:id/edit
function games_edit() {
	if (!isset($_SESSION["id"])){redirect2(URL);}
    if ($_SESSION["role"] == "admin"){
		$game = get_game_or_404();
		set('game', $game);
		return html('games/edit.html.php');
	}else{
		return html('forbidden.html.php'); 
	}
	
}

# PUT /games/:id
function games_update() {
	if (!isset($_SESSION["id"])){redirect2(URL);}
	if ($_SESSION["role"] == "admin"){
		$game_data = game_data_from_form();
		$game = get_game_or_404();
		$game = make_game_obj($game_data, $game);

		update_game_obj($game);
		redirect2('games');
	}else{
		return html('forbidden.html.php'); 
	}
}

# GET /games/new
function games_new() {
	if (!isset($_SESSION["id"])){redirect2(URL);}
	if ($_SESSION["role"] == "admin"){
		$game_data = game_data_from_form();
		set('game', make_game_obj($game_data));
		return html('games/new.html.php');
	}else{
		return html('forbidden.html.php'); 
	}
}

# POST /games
function games_create() {
	if (!isset($_SESSION["id"])){redirect2(URL);}
	if ($_SESSION["role"] == "admin"){
		$game_data = game_data_from_form();
		$game = make_game_obj($game_data);

		create_game_obj($game);
		redirect2('games');
	}else{
		return html('forbidden.html.php'); 
	}
}

# DELETE /games/:id
function games_destroy() {
	if (!isset($_SESSION["id"])){redirect2(URL);}
	if ($_SESSION["role"] == "admin"){
		delete_game_by_id(filter_var(params('id'), FILTER_VALIDATE_INT));
		redirect2('games');
	}else{
		return html('forbidden.html.php'); 
	}
}

function get_game_or_404() {
    $game = find_game_by_id(filter_var(params('id'), FILTER_VALIDATE_INT));
    if (is_null($game)) {
        halt(NOT_FOUND, _("This game doesn't exist."));
    }
    return $game;
}

function game_data_from_form() {
    return isset($_POST['game']) && is_array($_POST['game']) ? $_POST['game'] : array();
}

function games_cards(){
	$id_mode = 0;
	if (isset($_GET["id_mode"])){
		$id_mode = $_GET["id_mode"];
	}
	set('id_mode', $id_mode);
	
	if (!isset($_SESSION["id"])){redirect2(URL);}
	$games_data = find_games();
	$games = array();
	foreach ($games_data as $game){
		$games[$game->id] = $game;
	}
	unset($games[2]);

	set('games', $games);
	set('cards', find_my_cards($_SESSION["language"]));
	
	return html('games/cards.html.php'); 
}