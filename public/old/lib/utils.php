<?php
//Setup database
function setup_db($db){
	global $tabLanguages; 
	include_once("lib/db.php");
	include_once("model.card.php");
		
	$sql = "SHOW TABLES LIKE '".DB_PREFIX."users'" ;
    $result = array();
    $stmt = $db->prepare($sql);
    if ($stmt->execute($params)) {
        while ($obj = $stmt->fetch(PDO::FETCH_OBJ)) {
            $result[] = $obj;
        }
    }
	if (count($result)==0){	
		//Create database
		$bImport = true;	
	
		$sql = file_get_contents("db/schema.sql");		
		$sql = str_replace("mg_",DB_PREFIX,$sql);
		$stmt = $db->prepare($sql);
		$stmt->execute();
		
		set('success',_('Database successfully created'));
	}

	$sql = "select count(*) as nb FROM ".DB_PREFIX."cards" ;
    $result = array();
    $stmt = $db->prepare($sql);
    if ($stmt->execute($params)) {
        while ($obj = $stmt->fetch(PDO::FETCH_OBJ)) {
			if ($obj->nb == 0){
				cards_xml_to_db($db);
			}
        }
    }
	
	
}

/* Import file to database */
function cards_xml_to_db($db){	
	global $tabLanguages; 
	include_once("lib/db.php");
	include_once("model.card.php");
	
	$sql = "TRUNCATE TABLE ".DB_PREFIX."cards" ;
    $stmt = $db->prepare($sql);
	$stmt->execute();
	
	//Import Data	
	$sRep = "db";
	foreach ($tabLanguages as $sLangue => $sLanguage){
		$tabFiles = scandir($sRep);
		
		foreach ($tabFiles as $sFile){
			if ($sFile != ".." and $sFile != "."){
				if (strpos($sFile,$sLangue."_") !== false and strpos($sFile,".xml") !== false){
					if (file_exists($sRep."/".$sFile)){
						$docXML = new DOMDocument();			
						$sContenu = file_get_contents($sRep."/".$sFile);
						try{
							if ($docXML->loadXML($sContenu)){
								$sXpath = "//CARTES/*";
								$xpath = new DOMXPath($docXML);
								$lNodes = $xpath->query($sXpath);
							
								foreach ($lNodes as $oNode){
									$card_data = make_model_object(array());
									
									$iMode = 1;
									$card_data->created_by=0;
									$card_data->category=0;
									$card_data->name = "";
									$card_data->difficulty = 1;
									$card_data->mode=$iMode;
									$card_data->language = $sLangue;
									$card_data->word1 = "";
									$card_data->word2 = "";
									$card_data->word3 = "";
									$card_data->word4 = "";
									$card_data->word5 = "";
									$card_data->word6 = "";
									$card_data->word7 = "";
									$card_data->word8 = "";
									$card_data->word9 = "";
									$card_data->word10 = "";
									$card_data->word11 = "";
									$card_data->word12 = "";
									$card_data->persons = "";
									$card_data->description = "";
									$card_data->status = 1;
																			
									foreach ($oNode->childNodes as $oNodeKeywords) {
										if ($oNodeKeywords->nodeType == XML_ELEMENT_NODE) {
											switch ($oNodeKeywords->nodeName){
												case "NOM":
													$card_data->name = $oNodeKeywords->nodeValue;									
													break;
												case "MODE":
													$card_data->mode = $oNodeKeywords->nodeValue;									
													break;
												case "PERSONS":
													$card_data->persons = $oNodeKeywords->nodeValue;									
													break;
												case "DESCRIPTION":
													$card_data->description = $oNodeKeywords->nodeValue;									
													break;
												case "DIFFICULTE":
													$card_data->difficulty = $oNodeKeywords->nodeValue;
													break;
												case "LANGUE":
													$card_data->language = $oNodeKeywords->nodeValue;
													break;
												case "MOT1":
													$card_data->word1 = $oNodeKeywords->nodeValue;
													break;
												case "MOT2":
													$card_data->word2 = $oNodeKeywords->nodeValue;
													break;
												case "MOT3":
													$card_data->word3 = $oNodeKeywords->nodeValue;
													break;
												case "MOT4":
													$card_data->word4 = $oNodeKeywords->nodeValue;
													break;
												case "MOT5":
													$card_data->word5 = $oNodeKeywords->nodeValue;
													break;
												case "MOT6":
													$card_data->word6 = $oNodeKeywords->nodeValue;
													break;
												case "MOT7":
													$card_data->word7 = $oNodeKeywords->nodeValue;
													break;
												case "MOT8":
													$card_data->word8 = $oNodeKeywords->nodeValue;
													break;
												case "MOT9":
													$card_data->word9 = $oNodeKeywords->nodeValue;
													break;
												case "MOT10":
													$card_data->word10 = $oNodeKeywords->nodeValue;
													break;
												case "MOT11":
													$card_data->word11 = $oNodeKeywords->nodeValue;
													break;
												case "MOT12":
													$card_data->word12 = $oNodeKeywords->nodeValue;
													break;											
											}
										}
									}												
									create_card_obj($card_data); 
								}
							}
						}catch(Exception $e){exit();
							set("error",$e->getMessages());
						}
					}
				}
			}
		}
	}
}

function redirect2($url){
	header("location: ".URL."/".$url);
}

function scan_ip(){
	$db = option('db_conn');
	$sql =
        "SELECT " .
        "retry " .
        "FROM ".DB_PREFIX."connexions " .
        "WHERE ip=:ip and created = :created";
    $ip = $_SERVER["REMOTE_ADDR"];
	$tab = find_object_by_sql($sql, array(':ip' => $ip, ":created"=>date("Y-m-d") ));
	
	$iRetry = 0;
	foreach ($tab as $o){
		$iRetry = $o->retry;
	}
	return $iRetry;
}

function ban_ip($iConnexionError){
	$db = option('db_conn');
	if ($iConnexionError == 0){
		$sql =
        "INSERT INTO " .
        DB_PREFIX."connexions (ip, created, retry) VALUES (:ip, :created, 1)";
	}else{
		$sql =
        "UPDATE " .
        DB_PREFIX."connexions set retry = retry +1 where ip = :ip and created = :created";
	}
    $ip = $_SERVER["REMOTE_ADDR"];
	$stmt = $db->prepare($sql);
	$stmt->bindValue(':ip', $ip);
	$stmt->bindValue(':created' ,date("Y-m-d"));
	$stmt->execute();
}

/* Generate a unique code with timestamp */
function rand_uniqid($in, $to_num = false, $pad_up = false, $passKey = null)
{
    $index = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    if ($passKey !== null) {
        // Although this function's purpose is to just make the
        // ID short - and not so much secure,
        // you can optionally supply a password to make it harder
        // to calculate the corresponding numeric ID

        for ($n = 0; $n<strlen($index); $n++) {
            $i[] = substr( $index,$n ,1);
        }

        $passhash = hash('sha256',$passKey);
        $passhash = (strlen($passhash) < strlen($index))
            ? hash('sha512',$passKey)
            : $passhash;

        for ($n=0; $n < strlen($index); $n++) {
            $p[] =  substr($passhash, $n ,1);
        }

        array_multisort($p,  SORT_DESC, $i);
        $index = implode($i);
    }

    $base  = strlen($index);

    if ($to_num) {
        // Digital number  <<--  alphabet letter code
        $in  = strrev($in);
        $out = 0;
        $len = strlen($in) - 1;
        for ($t = 0; $t <= $len; $t++) {
            $bcpow = bcpow($base, $len - $t);
            $out   = $out + strpos($index, substr($in, $t, 1)) * $bcpow;
        }

        if (is_numeric($pad_up)) {
            $pad_up--;
            if ($pad_up > 0) {
                $out -= pow($base, $pad_up);
            }
        }
        $out = sprintf('%F', $out);
        $out = substr($out, 0, strpos($out, '.'));
    } else {
        // Digital number  -->>  alphabet letter code
        if (is_numeric($pad_up)) {
            $pad_up--;
            if ($pad_up > 0) {
                $in += pow($base, $pad_up);
            }
        }

        $out = "";
        for ($t = floor(log($in, $base)); $t >= 0; $t--) {
            $bcp = bcpow($base, $t);
            $a   = floor($in / $bcp) % $base;
            $out = $out . substr($index, $a, 1);
            $in  = $in - ($a * $bcp);
        }
        $out = strrev($out); // reverse
    }

    return $out;
}

/* Usefull to add messages with redirection */
function add_success($s){
	$_SESSION["success"] = $s;
}

/* Usefull to add messages with redirection */
function add_error($s){
	$_SESSION["error"] = $s;
}

/* Export cards database to files */
function cards_db_to_xml(){
	global $tabLanguages;
	foreach ($tabLanguages as $sLangue => $sLanguage){
		$cards = find_cards($sLangue);
		$tabXml = array();
		foreach ($cards as $card){
			if (!isset($tabXml[$card->mode])){
				$tabXml[$card->mode] = array();
			}
			$tabXml[$card->mode][]= $card;
		}
		
		foreach ($tabXml as $mode=>$cards){
			$s = '<?xml version="1.0" encoding="UTF-8"?>';
			$s .= "\n<CARTES>";
			foreach ($cards as $card){
				$s.= "
	<GAME>
		<NOM>".$card->name."</NOM>
		<DIFFICULTE>".$card->difficulty."</DIFFICULTE>
		<LANGUE>".$card->language."</LANGUE>
		<MODE>".$card->mode."</MODE>
		<MOT1>".$card->word1."</MOT1>
		<MOT2>".$card->word2."</MOT2>
		<MOT3>".$card->word3."</MOT3>
		<MOT4>".$card->word4."</MOT4>
		<MOT5>".$card->word5."</MOT5>
		<MOT6>".$card->word6."</MOT6>
		<MOT7>".$card->word7."</MOT7>
		<MOT8>".$card->word8."</MOT8>
		<MOT9>".$card->word9."</MOT9>
		<MOT10>".$card->word10."</MOT10>
		<MOT11>".$card->word11."</MOT11>
		<MOT12>".$card->word12."</MOT12>
		<DESCRIPTION>".$card->description."</DESCRIPTION>
		<PERSONS>".$card->persons."</PERSONS>
	</GAME>
						";
			}
			$s .= "\n</CARTES>";
			file_put_contents("db/".$sLangue."_".$mode.".xml",$s);
		}
	}
}

function getFlickrPicture($card){
	if (FLICKR_API_KEY != ""){
		$params = array(
			'api_key'	=> FLICKR_API_KEY,
			'method'	=> 'flickr.photos.search',
			'tags'	=> $card->name,
			'format'	=> 'php_serial',
		);
		$encoded_params = array();
		foreach ($params as $k => $v){
			$encoded_params[] = urlencode($k).'='.urlencode($v);
		}

		$url = "https://api.flickr.com/services/rest/?".implode('&', $encoded_params);
		
		$rsp = file_get_contents($url);
		$rsp_obj = unserialize($rsp);
		$tabPhotos = $rsp_obj["photos"]["photo"];
		
		$i = 1;
		while  ($i <= 12 and count($tabPhotos)>0){
			$iRnd = rand(0, count($tabPhotos)-1);
			$oPhoto = $tabPhotos[$iRnd];			
			$sField = "word".$i;
			$card->$sField ="https://farm".$oPhoto["farm"].".staticflickr.com/".$oPhoto["server"]."/".$oPhoto["id"]."_".$oPhoto["secret"].".jpg";
			unset ($tabPhotos[$iRnd]);
			$i++;
		}
	}
	
	return $card;
}
?>
