<script type="text/javascript">
var bHelp=false;
$(document).ready(function () {	 
	updateGame();	
	$("#id_game").focus();	
});
function chkTeam(){
	var r = true;
	
	if ($('#tr_teams').css('display') == 'none'){
		switch ($("#id_game").val()){
			case "5"://Wolf
				if ($("#nb_players").val() < 9){
					alert("<?php echo _("Number of players incorrect");?> >=8");
					r = false;
					$("#nb_players").focus();
				}
				break;
				
			case "6"://Petit meurtre
				if ($("#nb_players").val() < 4){
					alert("<?php echo _("Number of players incorrect");?> >=4");
					r = false;
					$("#nb_players").focus();
				}else{
					if ($("#nb_players").val() > 6){
						alert("<?php echo _("Number of players incorrect");?> <= 6");
						r = false;
						$("#nb_players").focus();
					}
				}
				break;
			
			case "8"://Animals
				if ($("#nb_players").val() > 6){
					alert("<?php echo _("Number of players incorrect");?> <=6");
					r = false;
					$("#nb_players").focus();
				}
				break;
				
			default:
				if ($("#nb_players").val() < 2){
					alert("<?php echo _("Number of players incorrect");?>");
					r = false;
					$("#nb_players").focus();
				}
				break;
		}		
	}else{
		if ($("#nb_teams").val() < 2){
			alert("<?php echo _("Number of teams incorrect");?>");
			r = false;
			$("#nb_teams").focus();
		}
	}
	return  r;
}

function updateGame(){	
	$('#tr_wolf_settings').css('display','none');
	$('#tr_teams').css('display','none');
	$('#tr_players').css('display','none');
	$('#tr_card').css('display','none');
	$('.rule').css('display','none');
	$('#rule_'+$("#id_game").val()).css('display','');
	
	for (var i = 10 ; i < 100; i++){
		if (document.getElementById('team_'+i)){
			$("#team_"+i).css('display','');
		}
		if (document.getElementById('player_'+i)){
			$("#player_"+i).css('display','');
		}
	}
	
	switch ($("#id_game").val()){
		case "1":
			//Timesup
			$('#tr_card').css('display','');
			$('#tr_players').css('display','');
			$('#tr_teams').css('display','');
			$("#nb_players").val(4);
			$("#nb_teams").val(2);
			for (var i = 11 ; i < 100; i++){
				if (document.getElementById('player_'+i)){
					$("#player_"+i).css('display','none');
				}
			}
			break;
		
		case "2":
			//Timesup extended
			$('#tr_card').css('display','');
			$('#tr_players').css('display','');
			$('#tr_teams').css('display','');
			$("#nb_players").val(4);
			$("#nb_teams").val(2);
			for (var i = 11 ; i < 100; i++){
				if (document.getElementById('player_'+i)){
					$("#player_"+i).css('display','none');
				}
			}
			break;
		
		case "3":
			//Taboo
			$('#tr_teams').css('display','');
			//$('#tr_players').css('display','');
			$("#nb_players").val(0);
			$("#nb_teams").val(2);
			
			break;
		
		case "4":
			//Brainstorm
			$('#tr_teams').css('display','');
			$("#nb_players").val(0);
			$("#nb_teams").val(2);
			break;
		
		case "5":
			//Wolf
			$('#tr_players').css('display','');
			$('#tr_wolf_settings').css('display','');
			$("#nb_players").val(9);
			$("#nb_teams").val(0);
			for (var i = 0 ; i < 9; i++){
				if (document.getElementById('player_'+i)){
					$("#player_"+i).css('display','none');
				}
			}
			break;
		
		case "6":
			//Petit meurtre
			$('#tr_players').css('display','');
			$("#nb_players").val(5);
			$("#nb_teams").val(0);
			for (var i = 0 ; i < 4; i++){
				$("#player_"+i).css('display','none');
			}
			for (var i = 8 ; i < 100; i++){
				if (document.getElementById('player_'+i)){
					$("#player_"+i).css('display','none');
				}
			}
			break;
			
		case "7":
			//Pictionary
			$('#tr_teams').css('display','');
			//$('#tr_players').css('display','');
			$("#nb_players").val(0);
			$("#nb_teams").val(2);
			break;
		
		case "8":
			//Animals
			$('#tr_players').css('display','');
			$("#nb_players").val(3);
			$("#nb_teams").val(0);
			for (var i = 7 ; i < 100; i++){
				if (document.getElementById('player_'+i)){
					$("#player_"+i).css('display','none');
				}
			}
			break;
	}
	$(".formtable").addClass("minwidth");
	$(".help_special_cards").css('display','none');
}
</script>
<div class="clear10" ></div>
<form method="post" onsubmit="return chkTeam()" method="post" action="<?php echo URL;?>/parts">
	<table class="formtable minwidth">	
		<tr>
			<td width="100">
				<?php echo _('Game');?> :
			</td>
			<td>
				<select name="part[id_game]" id="id_game" onchange="updateGame()">
					<?php
					foreach ($games as $game){
						if ($game->id != 8 or FLICKR_API_KEY != ""){
					?>
							<option value="<?php echo $game->id;?>"><?php echo $game->name;?></option>
					<?php
						}
					}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<?php echo _("Rules");?> :
			</td>
			<td>
				<?php
				for ($i = 1; $i <= 8; $i++){
				?>
				<p class="rule" id="rule_<?php echo $i;?>">
					<?php echo _("rule_".$i);?>
				</p>
				<?php
				}
				?>
			</td>
		</tr>
		<tr id="tr_players">
			<td>
				# <?php echo _("Players");?> :
			</td>
			<td>
				<select name="part[nb_players]" id="nb_players">
					<?php
					for ($i = 0; $i<=24; $i++){
					?>
						<option id="player_<?php echo $i;?>" style="<?php if ($i<2){echo "display:none";}?>" <?php if($i==4){echo "selected";} ?> value="<?php echo $i;?>"><?php echo $i;?></option>
					<?php
					}
					?>
				</select>
			</td>
		</tr>
		<tr id="tr_teams" style="display:none">
			<td>
				# <?php echo _("Teams");?> :
			</td>
			<td>
				<select name="part[nb_teams]" id="nb_teams" >
					<?php
					for ($i = 0; $i<=4; $i++){
					?>
						<option id="team_<?php echo $i;?>" style="<?php if ($i<2){echo "display:none";}?>" value="<?php echo $i;?>"><?php echo $i;?></option>
					<?php
					}
					?>
				</select>
			</td>
		</tr>
		
		<tr id="tr_card">
			<td>
				# <?php echo _("Cards");?> / <?php echo _("Player");?> :
			</td>
			<td>
				<select name="part[nb_cards]" id="nb_cards">
					<?php
					for ($i = 10; $i>=3; $i--){						
					?>
						<option value="<?php echo $i;?>"><?php echo $i;?></option>
					<?php
					}
					?>
				</select> (<?php echo _("you will remove 2 cards");?>)
			</td>
		</tr>
		<tr style="display:none">
			<td>
				<?php echo _("Password");?> :
			</td>
			<td>
				<input type="text" name="part[password]" value="" />
			</td>
		</tr>
		<tr id="tr_wolf_settings" style="display:none">
			<td valign="top">
				<?php echo _("Special cards");?> :
				<br/>
				<input type="button" onclick="if(bHelp){$('.help_special_cards').css('display','none');bHelp=false;}else{$('.help_special_cards').css('display','');bHelp=true;}" value="<?php echo _("Help");?>" />
			</td>
			<td>
				<ul style="list-style:none;padding:0;">
				<?php
				$sold_ext = "";
				foreach ($cards as $card){
					if ($card->word1 != "Standard" ){
						$sid_card = strtolower(str_replace(" ","_",$card->name));
					?>
						<li style="clear:both">
							<?php
							if ($card->word1 != $sold_ext){
								?>
								<hr/>
								<?php
								echo "<b>".$card->word1." :</b>";
							}
							?>
							<div>								
								<div>
									<div class='fl' style="padding:10px;padding-left:0px;width:180px;">
										<input type="checkbox" value="<?php echo $card->id;?>" name="chk_wolf[]" id="card_<?php echo $sid_card;?>" /><label for="card_<?php echo $sid_card;?>" /><?php echo $card->name;?>
									</div>
									<div class='fl'>
										<img title="<?php echo $card->word1;?>" src="<?php echo URL;?>/public/images/wolf_<?php echo $sid_card;?>.png" onclick="if(!$('#card_<?php echo $sid_card;?>').prop('checked')){$('#card_<?php echo $sid_card;?>').prop('checked', true);}else{$('#card_<?php echo $sid_card;?>').prop('checked', false);}" />
									</div>
								</div>
								<div style="clear:both" class="help_special_cards">
									<?php echo _("DESC_".$card->name);?>
								</div>
							</div>
							<?php							
							$sold_ext = $card->word1;
							?>
						</li>
					<?php
					}
				}
				?>
				</ul>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<input type="submit" value="<?php echo _("Create");?>" />
			</td>
		</tr>
	</table>
	
	<br style="clear:both" />
	<br style="clear:both" />
	<br style="clear:both" />
	<br style="clear:both" />
	<br style="clear:both" />
	<br style="clear:both" />
</form>