<?php
function users_logout(){
	if (!isset($_SESSION["id"])){redirect2(URL);}
	
	unset($_SESSION["id"]);
	unset($_SESSION["role"]);
	unset($_SESSION["language"]);
	
	return html('users/logout.html.php');
}

function users_login() {
	//Remove old parts
	remove_old_parts();
	
	$bOk = false;
	$tabOptions = array();
	$iConnexionError = scan_ip();
	
	if ($iConnexionError>4){
		set('error', _('IP Blacklist'));	
		return html('users/login.html.php','layout/default.html.php',$tabOptions);
	}else{
		if (isset($_POST["email"]) and isset($_POST["password"])){		
			$user = auth_user(trim($_POST["email"]), trim($_POST["password"]));
			
			if ($user){
				$bOk = true;
				$_SESSION["id"] = $user->id;
				$_SESSION["role"] = $user->role;
				$_SESSION["language"] = $user->language;
				$_SESSION["share"] = array();
			}else{
				set('error', _('Email or password incorrect'));
				ban_ip($iConnexionError);
			}
			
			$tabOptions = array("email"=>$_POST["email"], "password"=>$_POST["password"]);
		}
		
		if (isset($_SESSION["id"])){
			$bOk = true;
		}
		if ($bOk){
			redirect2("users/home");		
		}else{		
			return html('users/login.html.php','layout/default.html.php',$tabOptions);
		}
	}
}



# GET /users
function users_index() {
	if (!isset($_SESSION["id"])){redirect2(URL);}
    if ($_SESSION["role"] == "user"){
		return html('forbidden.html.php'); 
	}else{
		set('users', find_users());
		return html('users/index.html.php');
	}
}

function users_home(){
	if (!isset($_SESSION["id"])){redirect2(URL);}	
	
	if (isset($_SESSION["wolf_player"])){
		//If you are here, the game was ended, so a new # player will given to you after new join
		unset($_SESSION["wolf_player"]);
	}
	if (isset($_POST["share"])){
		if (!isset($_SESSION["share"])){
			$_SESSION["share"] = array();
		}
		$part = find_part_by_password($_POST["share"]);
		if (!isset($part)){
			set("error",_("Incorrect code"));
		}else{
			$_SESSION["share"][]= $part->id;
			if ($part->id_game == 5){
				redirect2("parts/".$part->id."/playwolf");
			}else{
				redirect2("parts/".$part->id."/go");
			}
			exit();
		}			
	}

	$games_data = find_games();
	$games = array();
	foreach ($games_data as $game){
		$games[$game->id] = $game->name;
	}
	set('games', $games);
	set('parts', find_parts());
	set('cards', find_my_cards_moderate($_SESSION["language"]));
	
	return html('users/home.html.php');
	
}

# GET /users/:id/edit
function users_edit() {
	if (!isset($_SESSION["id"])){redirect2(URL);}
    $user = get_user_or_404();
	
	if ($_SESSION["role"] == "admin" or $user->id == $_SESSION["id"]){
		set('user', $user);    
		return html('users/edit.html.php');
	}else{
		return html('forbidden.html.php'); 		
	}
}

# PUT /users/:id
function users_update() {
	if (!isset($_SESSION["id"])){redirect2(URL);}
    $user_data = user_data_from_form();
    $user = get_user_or_404();
	$sOldRole = $user->role;
	
	if ($_SESSION["role"] == "admin" or $user->id == $_SESSION["id"]){
		//Look for the same email in the database
		$iNbUser = count_user($user_data["email"], $user->id);
		
		if ($iNbUser == 0){		
			if ($user_data["password"] != ""){
				$user = make_user_obj($user_data, $user);
				//Protect hack
				if ($sOldRole != $user->role){
					if ($_SESSION["role"] != "admin"){
						$user->role = "admin";
					}
				}
				update_user($user, 'users', user_columns(),true);
				add_success(_("Account updated"));
				
				if ($_SESSION["role"] == "admin"){
					redirect2('users');
				}else{
					redirect2('users/home');
				}
			}else{
				add_error(_("Password empty"));
				redirect2 ('users/'.$user->id.'/edit'); 
			}
		}else{
			add_error(_("This email was already registered."));
			redirect2 ('users/'.$user->id.'/edit'); 
		}
	}else{
		return html('forbidden.html.php'); 
	}
}

# GET /users/new
function users_new() {
	if (!isset($_SESSION["id"])){redirect2(URL);}
	if ($_SESSION["role"] == "admin"){
		$user_data = user_data_from_form();
		set('user', make_user_obj($user_data));
    
		return html('users/new.html.php');
	}else{
		return html('forbidden.html.php'); 
	}
}

# POST /users
function users_create() {
	if (!isset($_SESSION["id"])){redirect2(URL);}
	if ($_SESSION["role"] == "admin"){
		$user_data = user_data_from_form();
		$user = make_user_obj($user_data);
		$user->created = date("Y-m-d");
		$user->nb_parts = 0;
		create_user_obj($user);

		redirect2('users');
	}else{
		return html('forbidden.html.php'); 
	}
}

# DELETE /users/:id
function users_destroy() {
	if (!isset($_SESSION["id"])){redirect2(URL);}
	if ($_SESSION["role"] == "admin"){
		delete_user_by_id(filter_var(params('id'), FILTER_VALIDATE_INT));
		redirect2('users');
	}else{
		return html('forbidden.html.php'); 
	}
}

function get_user_or_404() {
    $user = find_user_by_id(filter_var(params('id'), FILTER_VALIDATE_INT));
    if (is_null($user)) {
        halt(NOT_FOUND, _("This user doesn't exist."));
    }
    return $user;
}

function user_data_from_form() {
    return isset($_POST['user']) && is_array($_POST['user']) ? $_POST['user'] : array();
}

function users_mail() {
	if (isset($_POST["email"])){
		$user = find_user_by_email($_POST["email"]);

		if ($user->id > 0){
			$user->password2 = uniqid();
			update_user($user, 'users', user_columns(), true);
			set("success",_("A new password was sent by email"));
			
			$message = _("Your new password is")." ".$user->password2. " . \n"._("Please change it in your settings") . " ".URL;
			mail($user->email, _('Account update'), $message);
			
			remove_connexion();
		}
	}
    return html('users/mail.html.php');
}


function users_register() {
	if (REGISTER){
		if (isset($_POST["email"])){
			$user = find_user_by_email($_POST["email"]);

			if ($user->id > 0){
				set("error",_("This email was already registered."));
			}else{
				$sCode = uniqid();
				$user = make_user_obj(array());
				$user->email = $_POST["email"];
				$user->language = $_POST["language"];
				$user->sound = 1;
				$user->nb_parts = 0;
				$user->password = md5($sCode);
				$user->password2 = "";
				$user->role = "user";
				$user->created = date("Y-m-d");
				create_user_obj($user);
				set("success",_("Your password was sent by email"));
				
				$message = _("Your password is") . " ".$sCode. " . \n"._("Please change it in your settings"). " ".URL;
				mail($user->email, _('Account update'), $message);
				return html('users/login.html.php');
				exit();
			}
		}
		
		return html('users/register.html.php');
	}else{
		return html('forbidden.html.php'); 	
	}
}