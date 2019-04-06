<?php
function remove_old_parts() {
    $sql =
        "DELETE " .
        "FROM ".DB_PREFIX."parts where round > 3 and (id_game = 1 or id_game = 2) and created < '".date("Y-m-d")."'";
    sql($sql);
	
	$sql =
        "DELETE " .
        "FROM ".DB_PREFIX."parts where round > 1 and (id_game = 2 or id_game = 3) and created < '".date("Y-m-d")."'";
    sql($sql);
	
	/*
	$sql =
        "DELETE " .
        "FROM ".DB_PREFIX."connexions where created < '".date("Y-m-d")."'";
    sql($sql);
	*/
	
	$sql =
        "DELETE " .
        "FROM ".DB_PREFIX."parts where created < '".(date("Y")-1)."-31-12'";
    sql($sql);
}

function find_parts($bAll = false) {
    $sql =
        "SELECT " .
        "* " .
        "FROM ".DB_PREFIX."parts ";
	if (!$bAll){	
		$sql  .= " where id_user = ".$_SESSION["id"];
	}
	$sql .= " order by id desc";
	
    return find_objects_by_sql($sql);
}

function find_part_by_id($id) {
    $sql =
        "SELECT " .
        "* " .
        "FROM ".DB_PREFIX."parts " .
        "WHERE id=:id";	
    $tab = find_object_by_sql($sql, array(':id' => $id));
	$part= null;
	foreach ($tab as $o){
		$part = $o;
	}
	return $part;
}


function find_part_by_password($password) {
    $sql =
        "SELECT " .
        "* " .
        "FROM ".DB_PREFIX."parts " .
        "WHERE password=:password";	
    $tab = find_object_by_sql($sql, array(':password' => $password));
	$part= null;
	foreach ($tab as $o){
		$part = $o;
	}
	return $part;
}

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

function update_part_obj($part_obj) {
    return update_object($part_obj, 'parts', part_columns());
}

function create_part_obj($part_obj) {
	$user = find_user_by_id($_SESSION["id"]);
	$user->nb_parts++;
	update_user($user, 'users', user_columns(),false);

	
	$part_obj->created = date("Y-m-d");	
	$part_obj->id_user = $_SESSION["id"];
	$part_obj->next_player = 1;
	$part_obj->round = 1;	
	$part_obj->next_round = 1;	
	
		
	$games_data = find_games();
	$games = array();
	foreach ($games_data as $game){
		$games[$game->id] = $game;
	}		
	
	$tabScore = array();
	
	$iNbPlayers = 0;
	if ($part_obj->nb_players > 0){
		$iNbPlayers = $part_obj->nb_players;
	}else{
		$iNbPlayers = $part_obj->nb_teams;
	}
	for ($i = 1; $i <= $iNbPlayers; $i++){
		$tabScore[$i] = array();
		for ($k = 1; $k <=$games[$part_obj->id_game]->rounds; $k++){
			$tabScore[$i][$k] = 0;
		}
	}
	$part_obj->score = serialize($tabScore);	
	
	
	if (!isset($part_obj->id)){
		$part_obj->id = create_object($part_obj, 'parts', part_columns());
		$part_obj->password = rand_uniqid(mktime($part_obj->timestamp));
	}
	
	//Creation of the game	(with cards shuffle)
	if ($part_obj->id_game == 1 or $part_obj->id_game == 2){		
		//Times'up => nb cards * nb players
		$part_obj->nb_rounds = $games[$part_obj->id_game]->rounds;
		$tabCards = shuffle_start($part_obj);
		
		$tab = array();
		foreach ($tabCards as $oCard){
			$tab[$oCard->id] = array();
		}
		$part_obj->description = serialize(array("cards"=>$tab));
	}else{
		//No cards
		$tabCards = array();
		switch ($part_obj->id_game){
			default:
				$part_obj->description = serialize(array("cards"=>$tab));
				$part_obj->nb_rounds = $games[$part_obj->id_game]->rounds;
				break;
				
			case 5:
				//Wolf
				$tabDesc = unserialize($part_obj->description);
				$tab = array();
				if (isset($tabDesc["cards"])){
					$tab = $tabDesc["cards"];
					shuffle($tab);	
				}else{
					foreach ($tabDesk["wolf"] as $id){
						$tab[$id]= array();
					}
					$tabCards = find_cards_by_list($tab,true,4);
					
					//Look for standard card
					$tabCardsStandards = find_cards_for_wolf();	
					$oCardWolf = null;
					$oCardPeople = null;
					foreach ($tabCardsStandards as $oCard){
						if ($oCardPeople == null){
							$oCardPeople = $oCard;
						}else{
							$oCardWolf = $oCard;	
						}
					}
					
					$iNbWolf = 2;
					if ($part_obj->nb_players>11){
						$iNbWolf = 3;	
						if ($part_obj->nb_players>16){
							$iNbWolf = 4;	
						}
					}

					$iNbPlayer = 0;
					while ($iNbPlayer < $iNbWolf){
						$tabCards[] = $oCardWolf;
						$iNbPlayer++;
					}
					
					$iNbPlayer = count($tabCards);
					while ($iNbPlayer < $part_obj->nb_players){
						$tabCards[] = $oCardPeople;
						$iNbPlayer++;
					}
					
					$tab = array();
					shuffle($tabCards);	
					foreach ($tabCards as $oCard){
						$tab[] = $oCard->id;				
					}					
				}
				$part_obj->description = serialize(array("cards"=>$tab));
				break;
				
			case 6:
				//Petit meurtre
				$part_obj->nb_rounds = $part_obj->nb_players;
				$tabCards = find_cards_not_in_list(array(), true, $games[$part_obj->id_game]->mode,1);	

				$i = 0;
				$tab = array();
				$tabSeen = array();
				$tabGuilty = array();
				foreach ($tabCards as $oCard){
					if ($i < $iNbPlayers){
						$tab[] = $oCard->id ;
						$tabSeen[$oCard->id ] = $oCard->id ;
						$tabGuilty[$oCard->id] = rand(1,$iNbPlayers);
						$i++;
					}
				}
				$part_obj->description = serialize(array("cards"=>$tab,"seen"=>$tabSeen, "guilty"=>$tabGuilty));
				
				for ($i = 1; $i <= $iNbPlayers; $i++){
					$tabScore[$i] = array();
					for ($k = 1; $k <=1; $k++){
						$tabScore[$i][$k] = 0;
					}
				}
				$part_obj->score = serialize($tabScore);
				break;
			
			case 3:	
				//Taboo
			case 7:
				//Pictionary
				/*
				3 rounds or maybe 1 per player ?
				$part_obj->nb_rounds = $part_obj->nb_players;
				for ($i = 1; $i <= $part_obj->nb_rounds; $i++){
					$tabScore[$i] = array();
					for ($k = 1; $k <=$part_obj->nb_rounds; $k++){
						$tabScore[$i][$k] = 0;
					}
				}
				
				$part_obj->nb_players = 0;
				$part_obj->score = serialize($tabScore);
				*/
				break;
		}
	}
		
	update_object($part_obj, 'parts', part_columns());
	
	return $part_obj->id;
}

function delete_part_obj($man_obj) {
    delete_object_by_id($man_obj->id, 'parts');
}

function delete_part_by_id($part_id) {
    delete_object_by_id($part_id, 'parts');
}

function make_part_obj($params, $obj = null) {
    return make_model_object($params, $obj);
}

function part_columns() {
    return array( 'id_user', 'created', 'password', 'description', 'nb_players', 'nb_teams', 'id_game', 'nb_cards', 'round', 'next_player', 'score', 'timestamp', 'nb_rounds', 'next_round');
}

function part_data_filters() {
    return array(
        'part[description]' => FILTER_SANITIZE_SPECIAL_CHARS,
		'part[score]' => FILTER_SANITIZE_SPECIAL_CHARS,
		'part[password]' => FILTER_SANITIZE_SPECIAL_CHARS,
		'part[created]' => FILTER_SANITIZE_SPECIAL_CHARS,
		'part[nb_teams]' => FILTER_VALIDATE_INT,
		'part[round]' => FILTER_VALIDATE_INT,
		'part[nb_cards]' => FILTER_VALIDATE_INT,
		'part[nb_rounds]' => FILTER_VALIDATE_INT,
		'part[nb_players]' => FILTER_VALIDATE_INT,
		'part[timestamp]' => FILTER_SANITIZE_SPECIAL_CHARS,
		'part[next_player]' => array("filter"  => FILTER_VALIDATE_INT,
                             "flags"   => FILTER_FLAG_ARRAY,
                             "options" => array("min_range" => 1)),
		'part[next_round]' => array("filter"  => FILTER_VALIDATE_INT,
                             "flags"   => FILTER_FLAG_ARRAY,
                             "options" => array("min_range" => 1)),
        'part[id_user]' => array("filter"  => FILTER_VALIDATE_INT,
                             "flags"   => FILTER_FLAG_ARRAY,
                             "options" => array("min_range" => 1)),
		'part[id_game]' => array("filter"  => FILTER_VALIDATE_INT,
                             "flags"   => FILTER_FLAG_ARRAY,
                             "options" => array("min_range" => 1)),
    );
}
