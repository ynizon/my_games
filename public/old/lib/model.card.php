<?php
function count_cards($sLanguage = "", $id_mode = 0) {
    $sql =
        "SELECT " .
        "count(*) as nb " .
        "FROM ".DB_PREFIX."cards where status = 1";
		
	if ($sLanguage != ""){
		$sql .= " and language = '".$sLanguage."'";
	}
	if ($id_mode != 0){
		$sql .= " and mode = '".$id_mode."'";
	}
	
	
	$o = find_objects_by_sql($sql);
	
	$iCpt = $o[0]->nb;
	return $iCpt;    
}

function find_my_cards($sLanguage = "", $sFilter = "", $id_mode = 0) {
    $sql =
        "SELECT " .
        "* " .
        "FROM ".DB_PREFIX."cards where status = 1";
	
	if ($_SESSION["role"] == "user"){
		$sql .= " and created_by = ".$_SESSION["id"];
	}
	if ($sLanguage != ""){
		$sql .= " and language = '".$sLanguage."'";
	}
	if ($id_mode != 0){
		$sql .= " and mode = '".$id_mode."'";
	}
	
	if ($sFilter != ""){
		$sFilter ="%".$sFilter."%";
		$sql .= " and name like :name";		
		return find_objects_by_sql($sql, array(":name"=>$sFilter)) ;
	}else{
		$sql .= " order by name";
		return find_objects_by_sql($sql);
	}
	
    
}

function find_my_cards_moderate($sLanguage = "") {	    
	$sql =
        "SELECT " .
        "* " .
        "FROM ".DB_PREFIX."cards where status != 1 and created_by = ".$_SESSION["id"];
		
	if ($sLanguage != ""){
		$sql .= " and language = '".$sLanguage."'";
	}

    return find_objects_by_sql($sql) ;
}

function find_cards($sLanguage = "") {
    $sql =
        "SELECT " .
        "* " .
        "FROM ".DB_PREFIX."cards ";
	if ($sLanguage != ""){
		$sql .= " where language = '".$sLanguage."'";
	}
    return find_objects_by_sql($sql);
}

function find_card_by_id($id) {	
    $sql =
        "SELECT " .
        "* " .
        "FROM ".DB_PREFIX."cards " .
        "WHERE id=:id";
    
	$tab = find_object_by_sql($sql, array(':id' => $id));
	$card= null;
	foreach ($tab as $o){
		$card = $o;
	}
	return $card;
}

function find_cards_by_list($tab = array(), $bRandom = true, $iMode = 0) {	
    $list = "0";

	foreach ($tab as $id=>$card){
		if (!isset($card["seen"])){
			$list.= ",".$id;
		}
	}
	$sql =
        "SELECT " .
        "* " .
        "FROM ".DB_PREFIX."cards " .
        "WHERE id IN (".$list.") and  status = 1 and mode = " .$iMode;
	
	if ($bRandom){
		$sql .=" order by rand()";    
	}else{
		$sql .=" order by id";    
	}
	
	$tab = find_objects_by_sql($sql);
	
	return $tab;
}

function find_cards_for_wolf() {	   
	$sql =
        "SELECT " .
        "* " .
        "FROM ".DB_PREFIX."cards " .
        "WHERE status = 1 and word1 = :word1 order by name desc";
	
	$tab = find_objects_by_sql($sql, array(":word1"=>"Standard"));
	
	return $tab;
}



function find_cards_not_in_list($tab = array(), $bRandom = true, $iMode = 0, $iLimit = 0) {	
    $list = "0";

	foreach ($tab as $id=>$card){
		$list.=",".$id;
	}
	$sql =
        "SELECT " .
        "* " .
        "FROM ".DB_PREFIX."cards where  status = 1 and mode = " .$iMode.
        " and id NOT IN (".$list.") ";
	
	if ($bRandom){
		$sql .=" order by rand()";    
	}else{
		$sql .=" order by id";    
	}
	
	if ($iLimit != 0){
		$sql .=" LIMIT 0,".$iLimit;
	}
	
	$tab = find_objects_by_sql($sql);
	
	return $tab;
}
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

function update_card_obj($card_obj) {
    return update_object($card_obj, 'cards', card_columns());
}

function create_card_obj($card_obj) {
    return create_object($card_obj, 'cards', card_columns());
}

function delete_card_obj($man_obj) {
    delete_object_by_id($man_obj->id, 'cards');
}

function delete_card_by_id($card_id) {
    delete_object_by_id($card_id, 'cards');
}

function make_card_obj($params, $obj = null) {
    return make_model_object($params, $obj);
}

function card_columns() {
    return array('name', 'word1', 'word2', 'word3', 'word4', 'word5', 'word6', 'word7', 'word8', 'word9', 'word10', 'word11', 'word12', 'mode', 'language', 'difficulty', 'category', 'created_by', 'status', 'description','persons');
}

function card_data_filters() {
    return array(
		'card[name]' => FILTER_SANITIZE_SPECIAL_CHARS,
		'card[description]' => FILTER_SANITIZE_SPECIAL_CHARS,
		'card[persons]' => FILTER_SANITIZE_SPECIAL_CHARS,
		'card[word1]' => FILTER_SANITIZE_SPECIAL_CHARS,
		'card[word2]' => FILTER_SANITIZE_SPECIAL_CHARS,
		'card[word3]' => FILTER_SANITIZE_SPECIAL_CHARS,
		'card[word4]' => FILTER_SANITIZE_SPECIAL_CHARS,
		'card[word5]' => FILTER_SANITIZE_SPECIAL_CHARS,
		'card[word6]' => FILTER_SANITIZE_SPECIAL_CHARS,
		'card[word7]' => FILTER_SANITIZE_SPECIAL_CHARS,
		'card[word8]' => FILTER_SANITIZE_SPECIAL_CHARS,
		'card[word9]' => FILTER_SANITIZE_SPECIAL_CHARS,
		'card[word10]' => FILTER_SANITIZE_SPECIAL_CHARS,
		'card[word11]' => FILTER_SANITIZE_SPECIAL_CHARS,
		'card[word12]' => FILTER_SANITIZE_SPECIAL_CHARS,
		'card[mode]' => FILTER_VALIDATE_INT,
		'card[status]' => FILTER_VALIDATE_INT,
        'card[language]' => FILTER_SANITIZE_SPECIAL_CHARS,
		'card[difficulty]' => FILTER_VALIDATE_INT,
        'card[category]' => array("filter"  => FILTER_VALIDATE_INT,
                        "flags"   => FILTER_FLAG_ARRAY,
                        "options" => array("min_range" => 1)),
		'card[created_by]' => array("filter"  => FILTER_VALIDATE_INT,
                        "flags"   => FILTER_FLAG_ARRAY,
                        "options" => array("min_range" => 0))
    );
}

/* Shuffle cards for the beginning */
function shuffle_start($part_obj){
	$tabCards = array();
	
	//What mode ?
	$mode = 0;
	$games_data = find_games();
	foreach ($games_data as $game){
		if ($game->id == $part_obj->id_game){
			$mode = $game->mode;	
		}
	}
		
	$iNbPlayers = 0;
	if ($part_obj->nb_players > 0){
		$iNbPlayers = $part_obj->nb_players;
	}else{
		$iNbPlayers = $part_obj->nb_teams;
	}

	if ($mode == 1){		
		//If time'sup 
		$sql =
			"SELECT " .
			"id " .
			"FROM ".DB_PREFIX."cards " .
			"WHERE language = '".$_SESSION["language"]."' and status = 1 and mode =:mode order by rand() LIMIT 0, " . ($iNbPlayers * $part_obj->nb_cards);
		$tabCards = find_object_by_sql($sql, array(':mode' => $mode));
	}else{
		$sql =
			"SELECT " .
			"id " .
			"FROM ".DB_PREFIX."cards " .
			"WHERE language = '".$_SESSION["language"]."' and status = 1 and mode =:mode order by rand()";
		$tabCards = find_object_by_sql($sql, array(':mode' => $mode));
	}
	
	return $tabCards;
}
