<?php
# GET /card
function cards_index() {
	if (!isset($_SESSION["id"])){redirect2(URL);}
	$games_data = find_games();
	$games = array();
	foreach ($games_data as $game){
		$games[$game->id] = $game;
	}
	unset($games[2]);

	set('games', $games);
	set('cards', find_my_cards_moderate($_SESSION["language"]));
    return html('cards/index.html.php');
}

# GET /cards/:id/edit
function cards_edit() {
	if (!isset($_SESSION["id"])){redirect2(URL);}
    $games_data = find_games();
	$modes = array();
	foreach ($games_data as $game){
		$modes[$game->mode] = $game->name;
	}
	set('modes', $modes);
	$card = get_card_or_404();
	if ($_SESSION["role"] == "admin" or $card->created_by == $_SESSION["id"]){
		set('card', $card);
		return html('cards/edit.html.php');
	}else{
		return html('forbidden.html.php'); 
	}
}

# PUT /cards/:id
function cards_update() {
	if (!isset($_SESSION["id"])){redirect2(URL);}
    $card_data = card_data_from_form();
    $card = get_card_or_404();
	if ($_SESSION["role"] == "admin" or $card->created_by == $_SESSION["id"]){
		$card = make_card_obj($card_data, $card);

		update_card_obj($card);
		header("location: " .URL.'/games/cards?id_mode='.$card->mode);
	}else{
		return html('forbidden.html.php'); 
	}
}

# GET /cards/new
function cards_new() {
	if (!isset($_SESSION["id"])){redirect2(URL);}
	$games_data = find_games();
	$modes = array();
	foreach ($games_data as $game){
		$modes[$game->mode] = $game->name;
	}
	set('modes', $modes);
	
    $card_data = card_data_from_form();
    set('card', make_card_obj($card_data));
    return html('cards/new.html.php');
}

# POST /cards
function cards_create() {
	if (!isset($_SESSION["id"])){redirect2(URL);}
    $card_data = card_data_from_form();	
    $card = make_card_obj($card_data);
	$card->created = date("Y-m-d");
	$card->created_by = $_SESSION["id"];
	$card->status = 0;
	if ($_SESSION["role"] == "admin"){
		$card->status = 1;
	}
    create_card_obj($card);
	$iCount = count_cards($_SESSION["language"],$card->mode);
	add_success(_("The card was successfully created") . " (".$iCount.")");
    header("location: " .URL.'/cards/new?id_mode='.$card->mode);
}

# DELETE /cards/:id
function cards_destroy() {
	if (!isset($_SESSION["id"])){redirect2(URL);}
	if ($_SESSION["role"] == "admin"){
		delete_card_by_id(filter_var(params('id'), FILTER_VALIDATE_INT));
		redirect('cards');
	}else{
		return html('forbidden.html.php'); 
	}
}

function get_card_or_404() {
    $card = find_card_by_id(filter_var(params('id'), FILTER_VALIDATE_INT));
    if (is_null($card)) {
        halt(NOT_FOUND, _("This card doesn't exist."));
    }
    return $card;
}

function card_data_from_form() {
    return isset($_POST['card']) && is_array($_POST['card']) ? $_POST['card'] : array();
}

function cards_list(){
	$sFilter = "";
	if (isset($_POST["name"])){
		$sFilter = trim($_POST["name"]);
	}
	$id_mode = 0;
	if (isset($_POST["id_mode"])){
		$id_mode = (int) $_POST["id_mode"];
	}
	$cards = find_my_cards($_SESSION["language"], $sFilter, $id_mode);	
	set('cards', $cards);	
	return html('cards/list.html.php','layout/ajax.html.php');
}

function cards_export(){
	cards_db_to_xml();
	set("success",_("Export successfull"));
	return html('cards/export.html.php');
}

function cards_import(){
	$db = option('db_conn'); 
	cards_xml_to_db($db);
	set("success",_("Import successfull"));
	return html('cards/import.html.php');
}