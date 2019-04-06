<?php

# GET /
function main_page() {	
	if (isset($_SESSION["id"])){
		redirect2("users/home");
	}
	return html('main.html.php');	
}
