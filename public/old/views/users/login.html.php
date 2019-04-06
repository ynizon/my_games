
<form method="post" action = "<?php echo URL;?>/users/login">
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
			<td>
				<?php echo _('Password');?> :
			</td>
			<td>
				<input type="password" name="password" value="<?php echo $password;?>"/>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<input type="submit" value="<?php echo _('Send');?>" />
			</td>
		</tr>
	</table>
</form>

<?php
if (REGISTER){
?>
	<a href='<?php echo URL;?>/users/register'><?php echo _('Register your email');?></a><br/>
<?php
}
?>
<a href='<?php echo URL;?>/users/mail'><?php echo _('Forgot your password');?></a>