<?php

# GET /part
function parts_index() {
	if (!isset($_SESSION["id"])){redirect2(URL);}
	$games_data = find_games();
	$games = array();
	foreach ($games_data as $game){
		$games[$game->id] = $game->name;
	}
	set('games', $games);
    set('parts', find_parts(true));
    return html('parts/index.html.php');
}


# GET /parts/new
function parts_new() {
	if (!isset($_SESSION["id"])){redirect2(URL);}
	$games_data = find_games();
	set('games', make_part_obj($games_data));
    $part_data = part_data_from_form();
    set('part', make_part_obj($part_data));
		
	//Cards for wolf
	$cards = find_my_cards($_SESSION["language"], "", 4);
    set ("cards",$cards);
	
	return html('parts/new.html.php');
}

# POST /parts
function parts_create() {
	if (!isset($_SESSION["id"])){redirect2(URL);}
    $part_data = part_data_from_form();
    $part = make_part_obj($part_data);
	
	if (isset($_POST["chk_wolf"])){
		$part->description = serialize(array("wolf"=>$_POST["chk_wolf"]));
	}
    create_part_obj($part);	
			
	redirect2('parts/'.$part->id.'/go');
}

# DELETE /parts/:id
function parts_destroy() {
	if (!isset($_SESSION["id"])){redirect2(URL);}
	$part = find_part_by_id(filter_var(params('id'), FILTER_VALIDATE_INT));	
	
	if ($_SESSION["role"] == "admin" or $part->id_user == $_SESSION["id"]){
		delete_part_by_id(filter_var(params('id'), FILTER_VALIDATE_INT));
		redirect2('users/home');
	}else{
		return html('forbidden.html.php'); 
	}
}

function get_part_or_404() {
    $part = find_part_by_id(filter_var(params('id'), FILTER_VALIDATE_INT));

    if (is_null($part)) {
        halt(NOT_FOUND, _("This part doesn't exist."));
    }
    return $part;
}

function part_data_from_form() {
    return isset($_POST['part']) && is_array($_POST['part']) ? $_POST['part'] : array();
}


function parts_go(){
	if (!isset($_SESSION["id"])){redirect2(URL);}
	$part = get_part_or_404();
	$games_data = find_games();
	$games = array();
	foreach ($games_data as $game){
		$games[$game->id] = $game;
	}
	set('games', $games);
	$user = find_user_by_id($_SESSION["id"]);
	set('user', $user);
		
	if ($part->id_user == $_SESSION["id"] or in_array($part->id,$_SESSION["share"])){
		switch ($part->id_game){
			case 1:
			case 2:
				if ($part->round > $games[$part->id_game]->rounds){
					$tabDescription = unserialize($part->description);		
					$cards = find_cards_by_list($tabDescription["cards"], $bRandom, $games[$part->id_game]->mode);
					$cards_removed = find_cards_by_list($tabDescription["removed"], $bRandom, $games[$part->id_game]->mode);
					set('cards', $cards);
					set('cards_removed', $cards_removed);
				}
				break;
			
			case 5:
				//WOLF
				$tab = array();
				$tabDescription = unserialize($part->description);
				foreach ($tabDescription["cards"] as $oCard=>$id_card){
					$tab[$id_card] = array();
				}
				set('cards', $tabDescription["cards"]);
				
				$all_cards = array();
				$all_cards_tmp = find_cards_by_list($tab, false, 4);
				foreach ($all_cards_tmp as $oCard){
					$all_cards[$oCard->id] = $oCard->name;
				}				
				set('all_cards', $all_cards);				
				break;
				
			case 6:
				//PETITMEURTRE
				$tab = array();
				$tabDescription = unserialize($part->description);
				$i = 0;
				$card = array();
				
				if (isset($tabDescription["cards"][0])){
					$card = find_card_by_id($tabDescription["cards"][0]);
				}
				
				set('card', $card);
				break;
		}
		
		set('part', $part);

		if (file_exists('views/parts/go'.$part->id_game.'.html.php')){
			return html('parts/go'.$part->id_game.'.html.php');
		}else{
			return html('parts/go.html.php');
		}
	}else{
		return html('forbidden.html.php');
	}
}

function parts_play(){
	if (!isset($_SESSION["id"])){redirect2(URL);}
	
	$part = get_part_or_404();
	$games_data = find_games();
	$games = array();
	foreach ($games_data as $game){
		$games[$game->id] = $game;
	}
	set('games', $games);
	if ($part->id_user == $_SESSION["id"] or in_array($part->id,$_SESSION["share"])){
		set('part', $part);
		$user = find_user_by_id($_SESSION["id"]);
		set('user', $user);
		$tabDescription = unserialize($part->description);		
		$bRandom = true;
		
		switch ($games[$part->id_game]->mode ){
			case 1:
				//Times up 
				//round 2 => no shuffle
				if ($part->round == 2){
					$bRandom = false;
				}
				$cards = find_cards_by_list($tabDescription["cards"], $bRandom, $games[$part->id_game]->mode);
				break;
			
			case 4:
				//Wolf
				$cards = $tabDescription["cards"];				
				$id_card = $cards[$part->next_player -1 ];
				$card = find_card_by_id($id_card);
				set("card", $card);
				set("next_button", true);
				break;
			
			case 6:	
				//Petit meurtre
				$cards = $tabDescription["cards"];
				$id_card = $cards[0];
				$card = find_card_by_id($id_card);
				set("card", $card);
				break;
				
			case 8:
				//Animals
				$tabCards = shuffle_start($part);
				$id_card = $tabCards[rand(0,count($tabCards)-1)]->id;
				$card = find_card_by_id($id_card);
				$card = getFlickrPicture($card);				
				set("card", $card);
				break;
				
			default:
				$cards = find_cards_not_in_list($tabDescription["cards"], $bRandom, $games[$part->id_game]->mode);
				break;
		}
		
		set('cards', $cards);
		return html('parts/play'.$games[$part->id_game]->mode.'.html.php');
	}else{
		return html('forbidden.html.php');
	}
}


/* Wolf join (just player not admin of the part)
 They have access only to their card
*/
function parts_playwolf(){
	if (!isset($_SESSION["id"])){redirect2(URL);}
	
	$part = get_part_or_404();
	$games_data = find_games();
	$games = array();
	foreach ($games_data as $game){
		$games[$game->id] = $game;
	}
	set('games', $games);
	if ($part->id_user == $_SESSION["id"] or in_array($part->id,$_SESSION["share"])){
		set('part', $part);
		$user = find_user_by_id($_SESSION["id"]);
		set('user', $user);
		$tabDescription = unserialize($part->description);		
		$bRandom = true;
				
		$cards = $tabDescription["cards"];				
		$id_card = $cards[$part->next_player -1 ];
		$card = find_card_by_id($id_card);
		set("card", $card);
		set('cards', $cards);
		
		if (isset($_GET["mode"])){
			//New part
			if ($_GET["mode"] == "refresh"){
				if (isset($_SESSION["wolf_player"])){
					unset($_SESSION["wolf_player"]);
					header("location: ".URL ."/parts/".$part->id."/playwolf");
					exit();
				}
			}
		}
		
		if (isset($_SESSION["wolf_player"])){
			//Its only refresh
		}else{
			$_SESSION["wolf_player"] = $part->next_player;
			$part->next_player++;
			if ($part->next_player > $part->nb_players){
				$part->next_player = 1;
				$part->round++;
			}
		}
		update_part_obj($part);
		
		set("next_button", false);
		return html('parts/play'.$games[$part->id_game]->mode.'.html.php');
	}else{
		return html('forbidden.html.php');
	}
}

function parts_post(){
	if (!isset($_SESSION["id"])){redirect2(URL);}
	
	$part = get_part_or_404();
	$games_data = find_games();
	$games = array();
	foreach ($games_data as $game){
		$games[$game->id] = $game;
	}
	set('games', $games);
	if ($part->id_user == $_SESSION["id"] or in_array($part->id,$_SESSION["share"])){
		$tabCards = array();
		
		$tabDescription = unserialize($part->description);
		$tabCardsRemoved = array();
		if (isset($tabDescription["removed"])){
			$tabCardsRemoved = $tabDescription["removed"];
		}
		$tabScore = unserialize($part->score);
		$tabCardsTmp = $tabDescription["cards"];
		foreach ($tabCardsTmp as $cardid => $oCard){
			$tabCards[$cardid] = $oCard;
		}

		if (isset($tabDescription["end"])){
			unset($tabDescription["end"]);
		}
		
		$iNbPlayers = 0;
		if ($part->nb_players > 0){
			$iNbPlayers = $part->nb_players;
		}else{
			$iNbPlayers = $part->nb_teams;
		}

		
		switch ($part->id_game){
			case 1:
			case 2:
			//Times'up
				if ($part->round == 1){
					$part->next_player++;
					
					//Flag viewed cards
					$tabCardsSeen = unserialize(str_replace("'",'"',$_POST["cards_seen"]));
					foreach ($tabCardsSeen as $cardid){
						$tabCards[$cardid]["seen"] = 1;
					}
					
					//Remove cards from the player deck
					foreach ($_POST["chkcard"] as $cardid){
						$tabCards[$cardid]["removed"] = 1;
						$tabCardsRemoved[$cardid] = [$cardid];
					}
					
					if ($part->next_player > $iNbPlayers){
						//The game wil Start 
						$part->next_round++;
						$part->round = 2;
						$part->next_player = 1;
						$tabDescription["end"] = 1;
						
						//Remove cards from the deck
						foreach ($tabCards as $cardid => $oCard){
							if (isset($oCard["removed"])){
								unset ($tabCards[$cardid]);
							}
							if (isset($oCard["seen"])){
								unset ($tabCards[$cardid]["seen"]);
							}
						}				
					}
					
					//Update cards deck
					$tabDescription["removed"] = $tabCardsRemoved;
					$tabDescription["cards"] = $tabCards;
					$part->description = serialize($tabDescription);
					
					update_part_obj($part);
					header("location: ".URL."/parts/".$part->id."/go");			
				}else{
					$iScore = $tabScore[$part->next_player][$part->round];
					$iCard = 0;
					foreach ($_POST["cards_id"] as $value=>$id){
						if ($_POST["cards_seen"][$iCard] == 1){
							$tabCards[$id]["seen"] = 1;
							$iScore++;
						}
						$iCard++;
					}					
					
					$tabScore[$part->next_player][$part->round] = $iScore;
					
					$part->next_player++;
					if ($part->next_player > $iNbPlayers){
						$part->next_player = 1;
						$part->next_round++;
					}
				
				
					$iDeckCard = 0;
					foreach ($tabCards as $cardid => $oCard){
						if (!isset($oCard["seen"])){
							$iDeckCard ++;
						}
					}
					
					//Next round
					if ($iDeckCard == 0){
						$tabDescription["end"] = 1;
						$part->round++;
						foreach ($tabCards as $cardid => $oCard){
							if (isset($oCard["seen"])){
								unset ($tabCards[$cardid]["seen"]);
							}
						}
					}
					
					$tabDescription["cards"] = $tabCards;
					$part->description = serialize($tabDescription);
					$part->score = serialize($tabScore);
					
					update_part_obj($part);
				}
				break;
			
			case 3:
				//Taboo
				$iScore = $tabScore[$part->next_player][$part->round];
				$iCard = 0;
				foreach ($_POST["cards_id"] as $value=>$id){
					if ($_POST["cards_seen"][$iCard] == 1){
						$tabCards[$id] = 1;
						$iScore++;
					}
					if ($_POST["cards_seen"][$iCard] == -1){
						$tabCards[$id] = 1;
						$iScore--;
					}
					$iCard++;
				}
				$tabScore[$part->next_player][$part->round] = $iScore;
					
				$part->next_player++;
				if ($part->next_player > $iNbPlayers){
					$part->next_player = 1;
					$part->round++;
					$part->next_round++;
				}		
				
				$tabDescription["cards"] = $tabCards;
				$part->description = serialize($tabDescription);
				$part->score = serialize($tabScore);			
				
				update_part_obj($part);
				break;
			
			case  4:
				//Brainstorm
				$iScore = (int) $_POST["score"];	
				$tabScore[$part->next_player][$part->round] = $iScore;
				
				$part->next_player++;
				if ($part->next_player > $iNbPlayers){
					$part->next_player = 1;
					$part->round++;
					$part->next_round++;
				}		
				
				$tabDescription["cards"] = $tabCards;
				$part->description = serialize($tabDescription);
				$part->score = serialize($tabScore);			
				
				update_part_obj($part);
				break;
				
			case  5:
				//Wolf				
				$part->next_player++;
				if ($part->next_player > $iNbPlayers){
					$part->next_player = 1;
					$part->round++;
					$part->next_round++;
				}

				update_part_obj($part);
				break;
				
			case  6:
				//Petit meurtre				
				if (!isset($tabDescription["players"])){
					$tabDescription["players"] = array();
				}
				if (!isset($tabDescription["players"][$part->id])){
					$tabDescription["players"][$part->id] = array();
				}
				$iPlayer = (int) $_POST["player"];
				$tabDescription["players"][$part->id][] = $iPlayer;
				
				$part->next_player++;
				if ($part->next_player > $iNbPlayers){
					unset ($tabDescription["players"]);
					$part->next_player = 1;				
					$part->next_round++;
				}
				
				if( $part->next_round==4){
					$part->next_round = 1;
					$part->round++;
				}
				
				$part->description = serialize($tabDescription);

				update_part_obj($part);
				break;
				
			case 7:
				//Pictionary
				$iScore = $tabScore[$part->next_player][$part->round];
				$iCard = 0;
				foreach ($_POST["cards_id"] as $value=>$id){
					if ($_POST["cards_seen"][$iCard] == 1){
						$tabCards[$id] = 1;
						$iScore++;
					}
					if ($_POST["cards_seen"][$iCard] == -1){
						$tabCards[$id] = 1;
						//$iScore--;
					}
					$iCard++;
				}
				$tabScore[$part->next_player][$part->round] = $iScore;
					
				$part->next_player++;
				if ($part->next_player > $iNbPlayers){
					$part->next_player = 1;
					$part->round++;
					$part->next_round++;
				}		
				
				$tabDescription["cards"] = $tabCards;
				$part->description = serialize($tabDescription);
				$part->score = serialize($tabScore);			
				
				update_part_obj($part);
				break;
				
			case  8:
				//Animals
				$iScore = (int) $_POST["score"];	
				$tabScore[$part->next_player][$part->round] = $iScore;
				
				$part->next_player++;
				if ($part->next_player > $iNbPlayers){
					$part->next_player = 1;
					$part->round++;
					$part->next_round++;
				}		
				
				$part->score = serialize($tabScore);			
				
				update_part_obj($part);
				break;
		}
		
		exit();
	}else{
		return html('forbidden.html.php');
	}	
}

function parts_refresh() {
	if (!isset($_SESSION["id"])){redirect2(URL);}
    $part = find_part_by_id(filter_var(params('id'), FILTER_VALIDATE_INT));	
	if ($part->id_user == $_SESSION["id"] or $_SESSION["role"] == "admin"){
		//Reset current part
		$part->round = 1;
		$part->next_player = 1;
		$part->next_round = 1;
		update_part_obj($part);		
		
		$id_old_part = create_part_obj($part);	
		$old_part = find_part_by_id(filter_var($id_old_part, FILTER_VALIDATE_INT));	
		
		$tabColumns = part_columns();
		foreach ($tabColumns as $sColumn){
			$old_part->$sColumn = $part->$sColumn;
		}
		update_part_obj($old_part);
		
		redirect2('parts/'.$part->id.'/go');		
	}else{
		return html('forbidden.html.php');
	}
    	
	redirect2('parts/'.$part->id.'/go');
}

/* Only for petit meurtre */
function change_story() {
	if (!isset($_SESSION["id"])){redirect2(URL);}
    $part = find_part_by_id(filter_var(params('id'), FILTER_VALIDATE_INT));	
	if ($part->id_user == $_SESSION["id"] or $_SESSION["role"] == "admin"){
		if ($part->id_game == 6){
			$games_data = find_games();
			$games = array();
			foreach ($games_data as $game){
				$games[$game->id] = $game;
			}
			$tabDescription = unserialize($part->description);
			
			$tab = array();
			$tabSeen = array();
			foreach ($tabDescription["seen"] as $id_card){
				$tabSeen[$id_card] = $id_card;
			}
			
			$iNbPlayers = $part->nb_players;
			$tabCards = find_cards_not_in_list($tabSeen, true, $games[$part->id_game]->mode,1);
			
			//No more cards
			if (count($tabCards) == 0){
				$tabSeen = array();				
			}
			
			$i = 0;			
			$tabGuilty = array();
			foreach ($tabCards as $oCard){
				if ($i < $iNbPlayers){
					$tab[] = $oCard->id ;
					$tabSeen[$oCard->id] = $oCard->id;
					$tabGuilty[$oCard->id] = rand(1,$iNbPlayers);
					$i++;
				}
			}
			
			$part->description = serialize(array("cards"=>$tab, "seen"=>$tabSeen, "guilty"=>$tabGuilty));
			
			
			//Score
			$tabScore = unserialize($part->score);
			if (isset($_POST["found"])){
				if ($_POST["found"] == "true"){
					$tabScore[$part->round][1]++;
				}
				$part->next_round = 1;
				$part->round++;
				$part->next_player = 1;
			}			
			
			$part->score =  serialize($tabScore);
			
			update_part_obj($part);					
		}
		
		if (isset($_POST["found"])){			
			exit();
		}else{
			redirect2('parts/'.$part->id.'/go');		
		}
	}else{
		return html('forbidden.html.php');
	}
    	
	redirect2('parts/'.$part->id.'/go');
}