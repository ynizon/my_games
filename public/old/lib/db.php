<?php

function find_objects_by_sql($sql = '', $params = array()) {
    $db = option('db_conn');

    $result = array();
    $stmt = $db->prepare($sql);
    if ($stmt->execute($params)) {
        while ($obj = $stmt->fetch(PDO::FETCH_OBJ)) {
            $result[] = $obj;
        }
    }
    return $result;
}

function find_object_by_sql($sql = '', $params = array()) {
    $db = option('db_conn');
	$tab = array();
    $stmt = $db->prepare($sql);
    if ($stmt->execute($params)){
		while ($obj = $stmt->fetch(PDO::FETCH_OBJ)) {
			$tab[]= $obj;
		}
        return $tab;
    }
    return $tab;
}

function make_model_object($params, $obj = null) {
    if (is_null($obj)) {
        $obj = new stdClass();
    }
    foreach ($params as $key => $value) {
        $obj->$key = $value;
    }
    return $obj;
}

function delete_object_by_id($obj_id, $table) {
	$db = option('db_conn');

    $stmt = $db->prepare("DELETE FROM `".DB_PREFIX."$table` WHERE id = ?");
    $stmt->execute(array($obj_id));
}

function sql($sql) {
    $db = option('db_conn');

    $stmt = $db->prepare($sql);
    $stmt->execute();
}

function add_colon($x) { return ':' . $x; };

function create_object($object, $table, $obj_columns = array()) {
    $db = option('db_conn');

    if (!count($obj_columns)) {
        $obj_columns = array_keys(get_object_vars($object));
    }
    unset($obj_columns['id']);

    $sql =
        "INSERT INTO `".DB_PREFIX."$table` (" .
        implode(', ', $obj_columns) .
        ') VALUES (' .
        implode(', ', array_map('add_colon', $obj_columns)) . ')';

		
    $stmt = $db->prepare($sql);
    foreach ($obj_columns as $column) {
        if (isset($object->$column)){
			$stmt->bindValue(':' . $column, $object->$column);
		}else{
			$stmt->bindValue(':' . $column, '');
		}
    }

    $stmt->execute();
    return $db->lastInsertId();
}

function name_eq_colon_name($x) { return $x . ' = :' . $x; };

function update_object($object, $table, $obj_columns = array()) {
	$db = option('db_conn');

    if (!count($obj_columns)) {
        $obj_columns = array_keys(get_object_vars($object));
    }

    $sql =
        "UPDATE `".DB_PREFIX."$table` SET " .
        implode(', ', array_map('name_eq_colon_name', $obj_columns)) .
        ' WHERE id = :id';

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $object->id);
    foreach ($obj_columns as $column) {
        $stmt->bindValue(':' . $column, $object->$column);
    }

    return $stmt->execute();
}

function update_user($object, $table, $obj_columns = array(), $bUpdatePassword = true) {
	$db = option('db_conn');

    if (!count($obj_columns)) {
        $obj_columns = array_keys(get_object_vars($object));
    }
	if ($object->created == ""){
		$object->created = date("Y-m-d");
	}
	
	if ($bUpdatePassword){
		if ($object->password == ""){
			$tabTmp = array();
			foreach ($obj_columns as $o){
				if ($o != "password"){
					$tabTmp[] = $o;
				}
			}
			$obj_columns = $tabTmp;
		}else{
			$object->password = md5($object->password);
		}
	}
	
    $sql =
        "UPDATE `".DB_PREFIX."$table` SET " .
        implode(', ', array_map('name_eq_colon_name', $obj_columns)) .
        ' WHERE id = :id';

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $object->id);
    foreach ($obj_columns as $column) {
        $stmt->bindValue(':' . $column, $object->$column);
    }

    return $stmt->execute();
}