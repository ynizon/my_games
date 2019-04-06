<?php
function link_to($params = null) {
    $params = func_get_args();
    $name = array_shift($params);
	//$url = call_user_func_array('url_for', $params);
	//return "<a href='".$url."'>".$name."</a>";
    
	$urlx = "";
	foreach ($params as $p){
		if ($urlx != ""){
			$urlx .= "/";
		}
		$urlx .= $p;
	}
    return "<a href='".URL."/".$urlx."'>".$name."</a>";
}

function link_todelete($params = null) {
    $params = func_get_args();
    $name = array_shift($params);
    //$url = call_user_func_array('url_for', $params);
	$urlx = "";
	foreach ($params as $p){
		if ($urlx != ""){
			$urlx .= "/";
		}
		$urlx .= $p;
	}
    return URL."/".$urlx;
}

function option_tag($id, $title, $act_id) {
    $s = '<option value="' . $id . '"';
    $s .= ($id == $act_id) ? ' selected="true"' : '';
    $s .= '>' . $title . '</option>';
    return $s;
}

function my_date($sDate = '2012-12-31'){
	if ($_SESSION["language"] == "fr_FR"){
		return substr($sDate,8,2)."/".substr($sDate,5,2)."/".substr($sDate,0,4);
	}else{
		return $sDate;
	}
}