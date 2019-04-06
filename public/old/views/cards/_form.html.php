<?php
global $tabLanguages;
?>
<div class="clear10" ></div>
<div>
	<form method="POST" action="<?php echo $action ?>">
	  <input type="hidden" name="_method" id="_method" value="<?php echo $method ?>" />
		<script type="text/javascript">	
			
			function findCards(){
				var sUrl = "<?php echo URL;?>/cards/list"; 
				if ($('#card_name').val().length >= 3){
					$.post(sUrl, { name: $('#card_name').val(), id_mode: $('#card_mode').val()}, function(data) {			 				 
						$("#examples").html(data);
					});	
				}
			}
			function changeGame(){
				$(".word").css('display','none');
				$("#tr_description").css('display','none');
				$("#tr_persons").css('display','none');
				$(".wordguilty").css('display','none');
				
				switch ($('#card_mode').val()){
					case "1":				
						for (var i = 1; i <=1; i++){
							$("#word"+i).css('display','');
						}
						break;
					case "2":
						for (var i = 1; i <=10; i++){
							$("#word"+i).css('display','');
						}
						break;
					case "3":
						for (var i = 1; i <=5; i++){
							$("#word"+i).css('display','');
						}
						break;
					case "6":
						$("#tr_description").css('display','');
						$("#tr_persons").css('display','');
						$(".wordguilty").css('display','');
						for (var i = 1; i <=12; i++){
							$("#word"+i).css('display','');
						}
						break;	
					case "7":						
						break;
				}
			}
			
			$(document).ready(function () {	 
				<?php
				if (isset($_GET["id_mode"])){
					?>
					$("#card_mode").val(<?php echo (int) $_GET["id_mode"];?>);
					<?php
				}
				?>
				changeGame();
				$("#card_name").focus();	
			}); 
		</script>
		<table class="formtable">
			<tr>
				<td><?php echo _('Game');?>:</td>
				<td><select name="card[mode]" id="card_mode" onchange="changeGame()">
						<?php
						foreach ($modes as $key=>$mode){
							if ($key != 4){//Not wolf
						?>
							<option <?php if ($card->mode == $key){echo "selected";} ?> value="<?php echo $key;?>" ><?php echo $mode;?></option>
						<?php	
							}
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td><?php echo _('Name');?>:</td>
				<td><input type="text" name="card[name]" id="card_name" size="40" value="<?php echo htmlspecialchars($card->name) ?>" onkeyup="findCards()"/></td>
			</tr>
			<tr id="tr_description">
				<td><?php echo _('Description');?>:</td>
				<td><textarea name="card[description]" cols="50" rows="5"><?php echo htmlspecialchars($card->description) ?></textarea></td>
			</tr>
			<tr id="tr_persons">
				<td><?php echo _('Persons');?>:</td>
				<td><textarea name="card[persons]" cols="50" rows="6"><?php echo htmlspecialchars($card->persons) ?></textarea></td>
			</tr>
			<?php
			for ($i = 1; $i<=12; $i++){
				$word = "word".$i;
			?>
				<tr  class="word" id="word<?php echo $i;?>">
					<td><?php echo _('Word') . " " .$i;?> <span class='wordguilty'>(<?php if($i<=6){echo _("Innocent");}else{echo _("Guilty");}?>)</span>:</td>
					<td><input type="text" name="card[word<?php echo $i;?>]"  value="<?php echo htmlspecialchars($card->$word) ?>" size="30"/></td>
				</tr>
			<?php
			}
			
			?>
			<tr>
				<td><?php echo _('Language');?>:</td>
				<td><select name="card[language]" id="card_language" >		
					<?php
					foreach ($tabLanguages as $key=>$country){
					?>
						<option <?php if ($card->language == $key){echo "selected";} ?> value="<?php echo $key;?>" ><?php echo $country;?></option>
					<?php
					}
					?>
					</select>
				</td>
			</tr>
			<tr>
				<td><?php echo _('Difficulty');?>:</td>
				<td><select name="card[difficulty]" id="card_difficulty" >
						<option <?php if ($card->difficulty == "1"){echo "selected";} ?> value="1" ><?php echo _("Easy");?></option>
						<option <?php if ($card->difficulty == "2"){echo "selected";} ?> value="2" ><?php echo _("Medium");?></option>
						<option <?php if ($card->difficulty == "3"){echo "selected";} ?> value="3" ><?php echo _("Hard");?></option>
					</select>
				</td>
			</tr>
			<?php
			/*
			<tr>
				<td><?php echo _('Category');?>:</td>
				<td><select name="card[category]" id="card_category" >
						<option <?php if ($card->category == "0"){echo "selected";} ?> value="0" ></option>
					</select>
				</td>
			</tr>
					
			
			*/
			if ($card->id != 0)	{
			?>
				<tr>
					<td><?php echo _('Status');?>:</td>
					<td><select name="card[status]" id="card_status" >
							<option <?php if ($card->status == "1"){echo "selected";} ?> value="1" ><?php echo _('Accepted');?></option>
							<option <?php if ($card->status == "0"){echo "selected";} ?> value="0" ><?php echo _('Moderate');?></option>					
							<option <?php if ($card->status == "-1"){echo "selected";} ?> value="-1" ><?php echo _('Rejected');?></option>
						</select>
					</td>
				</tr>
			<?php
			}
			?>
			<tr>
				<td>&nbsp;</td>
				<td>
					<div class="fl">
						<?php echo link_to(_('Cancel'), 'cards'), "\n" ?>
					</div>
					<div class="fr">
						<input type="submit" value="<?php echo _('Save');?>" />
					</div>
				</td>
			</tr>
		</table>


	</form>
</div>	

<hr/>

<div id="examples">
</div>
