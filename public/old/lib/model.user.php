<?php

function find_users() {
    $sql =
        "SELECT " .
        "* " .
        "FROM ".DB_PREFIX."users ";
    return find_objects_by_sql($sql);
}

function find_user_by_id($id) {
    $sql =
        "SELECT " .
        "* " .
        "FROM ".DB_PREFIX."users " .
        "WHERE id=:id";
    
	$tab = find_object_by_sql($sql, array(':id' => $id));
	$user= null;
	foreach ($tab as $o){
		$user = $o;
	}
	return $user;
}

function find_user_by_email($email) {
    $sql =
        "SELECT " .
        "* " .
        "FROM ".DB_PREFIX."users " .
        "WHERE email=:email";
    
	$tab = find_object_by_sql($sql, array(':email' => $email));
	$user= null;
	foreach ($tab as $o){
		$user = $o;
	}
	return $user;
}

function auth_user($email, $password) {
    $email = trim($email);
	$password2 = $password;
	$password = md5($password);
	
	if ($password2 == ""){
		$password2 = "-";
	}
	
	$sql =
        "SELECT " .
        "* " .
        "FROM ".DB_PREFIX."users " .
        "WHERE email=:email and (password=:password or password2=:password2)";
	$tab = find_object_by_sql($sql, array(':email' => $email, ':password' => $password, ':password2' => $password2));	
	$user= null;
	foreach ($tab as $o){
		$user = $o;
		
		if ($o->password2 == $password2){
			$o->password2 = "";
			update_user_obj($user);
		}
		
		remove_connexion();
	}
	
	return $user;
}

function count_user($email, $id) {
    $email = trim($email);
	
	$sql =
        "SELECT " .
        "count(*) as nb " .
        "FROM ".DB_PREFIX."users " .
        "WHERE email=:email and id != :id";
	$tab = find_object_by_sql($sql, array(':email' => $email, ":id"=>$id));	
	
	$i= 0;
	foreach ($tab as $o){
		$i = $o->nb;
	}
	
	return $i;
}


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

function update_user_obj($user_obj) {
    return update_object($user_obj, 'users', user_columns());
}

function create_user_obj($user_obj) {
    return create_object($user_obj, 'users', user_columns());
}

function delete_user_obj($man_obj) {
    delete_object_by_id($man_obj->id, 'users');
}

function delete_user_by_id($user_id) {
    delete_object_by_id($user_id, 'users');
}

function make_user_obj($params, $obj = null) {
    return make_model_object($params, $obj);
}

function user_columns() {
    return array('email', 'password', 'password2', 'role', 'language', 'created', 'sound', 'nb_parts');
}

function user_data_filters() {
    return array(
        'user[email]' => FILTER_SANITIZE_SPECIAL_CHARS,
		'user[sound]' => FILTER_VALIDATE_INT,
		'user[nb_parts]' => FILTER_VALIDATE_INT,
		'user[created]' => FILTER_SANITIZE_SPECIAL_CHARS,
		'user[password2]' => FILTER_SANITIZE_SPECIAL_CHARS,
        'user[password]' => FILTER_SANITIZE_SPECIAL_CHARS,
		'user[language]' => FILTER_SANITIZE_SPECIAL_CHARS,
		'user[role]' => FILTER_SANITIZE_SPECIAL_CHARS,
    );
}

function remove_connexion(){
	//Remove all connexions with this ip
	$db = option('db_conn');
	$sql ="DELETE FROM " .
		DB_PREFIX."connexions where ip = :ip and created= :created";		
	$ip = $_SERVER["REMOTE_ADDR"];
	$stmt = $db->prepare($sql);
	$stmt->bindValue(':ip', $ip);
	$stmt->bindValue(':created' ,date("Y-m-d"));
	$stmt->execute();    
}