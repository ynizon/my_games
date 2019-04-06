<?php
function find_rounds() {	
    $sql =
        "SELECT " .
        "* " .
        "FROM ".DB_PREFIX."rounds ";
    return find_objects_by_sql($sql);
}

function find_round_by_id($id) {
    $sql =
        "SELECT " .
        "* " .
        "FROM ".DB_PREFIX."rounds " .
        "WHERE id=:id";
    $tab = find_object_by_sql($sql, array(':id' => $id));
	$round= null;
	foreach ($tab as $o){
		$round = $o;
	}
	return $round;
}

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

function update_round_obj($round_obj) {
    return update_object($round_obj, 'rounds', round_columns());
}

function create_round_obj($round_obj) {
    return create_object($round_obj, 'rounds', round_columns());
}

function delete_round_obj($man_obj) {
    delete_object_by_id($man_obj->id, 'rounds');
}

function delete_round_by_id($round_id) {
    delete_object_by_id($round_id, 'rounds');
}

function make_round_obj($params, $obj = null) {
    return make_model_object($params, $obj);
}

function round_columns() {
    return array('id_game', 'name', 'order');
}

function round_data_filters() {
    return array(
        'round[name]' => FILTER_SANITIZE_SPECIAL_CHARS,
        'round[id_game]' => array("filter"  => FILTER_VALIDATE_INT,
                             "flags"   => FILTER_FLAG_ARRAY,
                             "options" => array("min_range" => 1)),
        'round[order]' => array("filter"  => FILTER_VALIDATE_INT,
                        "flags"   => FILTER_FLAG_ARRAY,
                        "options" => array("min_range" => 1)),
    );
}
