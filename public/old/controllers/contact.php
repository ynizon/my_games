<?php

# GET /
function contact_page() {
	global $contact_email;
	if (isset($_POST["email"])){
		if ("" !=$_POST["email"]){
			set("success",_("Your message has been sent."));
				
			$message = $_POST["message"];
			$headers = 'From: '.$_POST["email"] . "\r\n" .
			 'Reply-To: '.$_POST["email"]."\r\n" .
			 'X-Mailer: PHP/' . phpversion();
			 			 
			mail(CONTACT_EMAIL, _('New message'), $message, $headers);
		}else{
			set("error",_("Email empty."));
		}
	}
	return html('contact/index.html.php');	
}
