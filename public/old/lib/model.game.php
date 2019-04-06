<?php
function find_games() {	
    return find_objects_by_sql("SELECT * FROM `".DB_PREFIX."games`");
}

function find_game_by_id($id) {	
    $sql =
        "SELECT * " .
        "FROM ".DB_PREFIX."games " .
        "WHERE id=:id";
    
	$tab = find_object_by_sql($sql, array(':id' => $id));
	$game= null;
	foreach ($tab as $o){
		$game = $o;
	}
	return $game;
}

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

function update_game_obj($game_obj) {
    return update_object($game_obj, 'games', game_columns());
}

function create_game_obj($game_obj) {
    return create_object($game_obj, 'games', game_columns());
}

function delete_game_obj($man_obj) {
    delete_object_by_id($man_obj->id, 'games');
}

function delete_game_by_id($game_id) {
    delete_object_by_id($game_id, 'games');
}

function make_game_obj($params, $obj = null) {
    return make_model_object($params, $obj);
}

function game_columns() {
    return array('name', 'rounds', 'picture','mode');
}


function game_data_filters() {
    return array(
        'part[name]' => FILTER_SANITIZE_SPECIAL_CHARS,
        'part[rounds]' => array("filter"  => FILTER_VALIDATE_INT,
                             "flags"   => FILTER_FLAG_ARRAY,
                             "options" => array("min_range" => 1)),
		'part[mode]' => array("filter"  => FILTER_VALIDATE_INT,
                             "flags"   => FILTER_FLAG_ARRAY,
                             "options" => array("min_range" => 1)),
        'part[picture]' => FILTER_SANITIZE_SPECIAL_CHARS,
    );
}
