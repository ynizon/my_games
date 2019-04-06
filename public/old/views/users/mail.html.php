<div class="clear10" ></div>
<form method="post" action = "<?php echo URL;?>/users/mail">
	<p>
		<?php echo _("Send a new password by email");?>
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
			<td>&nbsp;</td>
			<td >
				<input type="submit" value="<?php echo _("Send");?>" />
			</td>
		</tr>
	</table>
</form>
