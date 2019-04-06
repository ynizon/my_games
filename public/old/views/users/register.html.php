<?php
global $tabLanguages;
?>
<div class="clear10" ></div>
<form method="post" action = "<?php echo URL;?>/users/register">
	<p>
		<?php echo _('Register your email');?>
	</p>
	<table class="formtable">
		<tr>
			<td>
				<?php echo _('Email');?> :
			</td>
			<td>
				<input type="text" name="email" value="<?php echo $email;?>"/>
			</td>
		</tr>
		<tr>
			<td><?php echo _('Language');?>:</td>
			<td><select name="language" id="language" >		
				<?php
				foreach ($tabLanguages as $key=>$country){
				?>
					<option value="<?php echo $key;?>" ><?php echo $country;?></option>
				<?php
				}
				?>
				</select>
			</td>
		</tr> 
		<tr>
			<td>&nbsp;</td>
			<td >
				<input type="submit" value="<?php echo _('Send');?>" />
			</td>
		</tr>
	</table>
</form>
