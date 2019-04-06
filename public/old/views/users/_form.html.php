<?php
global $tabLanguages;
?>
<div class="clear10"></div>
<form method="POST" action="<?php echo $action ?>">
  <input type="hidden" name="_method" id="_method" value="<?php echo $method ?>" />

  <table class="formtable">
	<tr>
		<td><?php echo _("Email");?>:</td>
		<td><input type="text" name="user[email]" id="user_email" value="<?php echo htmlspecialchars($user->email) ?>" /></td>
	</tr>
	<tr>
		<td><?php echo _("Password");?>:</td>
		<td><input type="password" name="user[password]" id="user_password" value="" /></td>
	</tr>
	<?php
	/*
	<tr>
		<td><?php echo _("Created");?>:</td>
		<td><input class="jqdate" type="text" name="user[created]" id="user_year" value="<?php echo $user->created ?>" /></td>
	</tr>
	<?php
	*/
	if ($_SESSION["role"] == "admin"){
	?>
	<tr>
		<td><?php echo _("Roles");?>:</td>
		<td><select name="user[role]" id="user_role" >
				<option <?php if ($user->role == "user"){echo "selected";} ?> value="user" ><?php echo _('user');?></option>
				<option <?php if ($user->role == "admin"){echo "selected";} ?> value="admin" ><?php echo _('admin');?></option>
			</select>
		</td>
	</tr>
	<?php
	}
	?>
	<tr>
		<td><?php echo _("Sound");?>:</td>
		<td><select name="user[sound]" id="user_sound" >
				<option <?php if ($user->sound == "1"){echo "selected";} ?> value="1" ><?php echo _('Yes');?></option>
				<option <?php if ($user->sound == "0"){echo "selected";} ?> value="0" ><?php echo _('No');?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td><?php echo _("Language");?>:</td>
		<td><select name="user[language]" id="user_language" >		
			<?php
			foreach ($tabLanguages as $key=>$country){
			?>
				<option <?php if ($user->language == $key){echo "selected";} ?> value="<?php echo $key;?>" ><?php echo $country;?></option>
			<?php
			}
			?>
			</select>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>
			<div class="fl">
				<?php echo link_to(_('Cancel'), 'users'), "\n" ?>
			</div>
			<div class="fr">
				<input type="submit" value="<?php echo _("Save");?>" />
			</div>
		</td>
	</tr>
  </table>

</form>
