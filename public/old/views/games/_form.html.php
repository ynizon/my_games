<div class="clear10"></div>

<form method="POST" action="<?php echo $action ?>">
  <input type="hidden" name="_method" id="_method" value="<?php echo $method ?>" />

  <table class="formtable">
		<tr>
			<td><?php echo ('Name');?>:</td>
			<td><input type="text" name="game[name]" id="game_name" value="<?php echo htmlspecialchars($game->name) ?>" /></td>
		</tr>

		<tr>
			<td><?php echo ('Rounds');?>:</td>
			<td><input type="text" name="game[rounds]" id="game_rounds" value="<?php echo htmlspecialchars($game->rounds) ?>" /></td>
		</tr>	

		<tr>
			<td><?php echo ('Mode');?>:</td>
			<td><input type="text" name="game[mode]" id="game_mode" value="<?php echo htmlspecialchars($game->mode) ?>" /></td>
		</tr>
		
		<tr>
			<td><?php echo ('Picture');?>:</td>
			<td><input type="text" name="game[picture]" id="game_picture" value="<?php echo htmlspecialchars($game->picture) ?>" /></td>
		</tr>

		<tr>
			<td>&nbsp;</td>
			<td>
				<div class="fl">
					<?php echo link_to(_('Cancel'), 'games'), "\n" ?>
				</div>
				<div class="fr">
					<input type="submit" value="Save" />
				</div>
			</td>
		</tr>
	</table>
</form>
