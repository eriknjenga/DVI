<?php
echo validation_errors('
<p class="error">', '</p>
');
?>
<form action="<?php echo base_url().'user_management/save_new_password'?>" method="post" style="margin:0 auto; width:300px;">
	<table border="0" class="data-table">
		<tr>
			<th class="subsection-title" colspan="2">Change Password</th>
		</tr>
		<tbody>
			<tr>
				<td><span class="mandatory">*</span> Old Password</td>
				<td>
				<input type="password" name="old_password" id="old_password">
				</td>
			</tr>
			<tr>
				<td><span class="mandatory">*</span> New Password</td>
				<td>
				<input type="password" name="new_password" id="new_password">
				</td>
			</tr>
			<tr>
				<td><span class="mandatory">*</span> Confirm New Password</td>
				<td>
				<input type="password" name="new_password_confirm" id="new_password_confirm">
				</td>
			</tr>
			<tr>
				<td colspan="2">
				<input type="submit" class="button" name="register" id="register" value="Change Password">
				</td>
			</tr>
		</tbody>
	</table>
</form>