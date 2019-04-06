<?php
# GET /round
function rounds_index() {
	if (!isset($_SESSION["id"])){redirect2(URL);}
	if ($_SESSION["role"] == "admin"){
		set('rounds', find_rounds());
		return html('rounds/index.html.php');
	}else{
		return html('forbidden.html.php'); 
	}
}

# GET /rounds/:id/edit
function rounds_edit() {
	if (!isset($_SESSION["id"])){redirect2(URL);}
	if ($_SESSION["role"] == "admin"){
		$round = get_round_or_404();
		set('round', $round);
		return html('rounds/edit.html.php');
	}else{
		return html('forbidden.html.php'); 
	}
}

# PUT /rounds/:id
function rounds_update() {
	if (!isset($_SESSION["id"])){redirect2(URL);}
	if ($_SESSION["role"] == "admin"){
		$round_data = round_data_from_form();
		$round = get_round_or_404();
		$round = make_round_obj($round_data, $round);

		update_round_obj($round);
		redirect('rounds');
	}else{
		return html('forbidden.html.php'); 
	}
}

# GET /rounds/new
function rounds_new() {
	if (!isset($_SESSION["id"])){redirect2(URL);}
	if ($_SESSION["role"] == "admin"){
		$round_data = round_data_from_form();
		set('round', make_round_obj($round_data));
		return html('rounds/new.html.php');
	}else{
		return html('forbidden.html.php'); 
	}
}

# POST /rounds
function rounds_create() {
	if (!isset($_SESSION["id"])){redirect2(URL);}
	if ($_SESSION["role"] == "admin"){
		$round_data = round_data_from_form();
		$round = make_round_obj($round_data);

		create_round_obj($round);
		redirect('rounds');
	}else{
		return html('forbidden.html.php'); 
	}
}

# DELETE /rounds/:id
function rounds_destroy() {
	if (!isset($_SESSION["id"])){redirect2(URL);}
	if ($_SESSION["role"] == "admin"){
		delete_round_by_id(filter_var(params('id'), FILTER_VALIDATE_INT));
		redirect('rounds');
	}else{
		return html('forbidden.html.php'); 
	}
}

function get_round_or_404() {
    $round = find_round_by_id(filter_var(params('id'), FILTER_VALIDATE_INT));
    if (is_null($round)) {
        halt(NOT_FOUND, _("This round doesn't exist."));
    }
    return $round;
}

function round_data_from_form() {
    return isset($_POST['round']) && is_array($_POST['round']) ? $_POST['round'] : array();
}
