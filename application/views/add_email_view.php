<script type="text/javascript">
	$(document).ready(function() {
		$("#add_email").validationEngine();

	});

</script>
<div class="quick_menu">
	<a class="quick_menu_link" href="<?php echo base_url()."email_management"?>">Listing</a>
</div>
<?php
if (isset($email)) { 
	$email_address = $email -> email;
	$stockout = $email -> stockout;
	$consumption = $email -> consumption;
	$coldchain = $email -> coldchain;
	$recipient = $email -> recepient;
	$number = $email -> number;
	$email_id = $email -> id; 
} else {
	$email_address = "";
	$stockout = "";
	$consumption = "";
	$coldchain = "";
	$recipient = "";
	$number = "";
	$email_id = ""; 
}
$attributes = array('enctype' => 'multipart/form-data', 'id' => 'add_email');
echo form_open('email_management/save', $attributes);
?>
<input type="hidden" name="record_id" value = "<?php echo $email_id;?>"/>
<table border="0" class="data-table">
	<tr>
		<th class="subsection-title" colspan="2">Input the Details</th>
	</tr>
	<tbody>
		<tr>
			<td>Recepient &nbsp;<span class="mandatory">*</span></td>
			<td>
			<input type="text" name="rep" id="rep" class="validate[required]" value="<?php echo $recipient;?>" />
			</td>
		</tr>
		<tr>
			<td>Email Address &nbsp;<span class="mandatory">*</span></td>
			<td>
			<input type="text" name="email" id="email"  class="validate[required,custom[email]]" value="<?php echo $email_address;?>"/>
			</td>
		</tr>
		<tr>
			<td>Phone Number &nbsp;<span class="mandatory">*</span></td>
			<td>
			<input type="text" name="number" id="number" class="validate[required]" value="<?php echo $number;?>" />
			</td>
		</tr>
		<tr>
			<td>Stock Out &nbsp;<span class="mandatory"></span></td>
			<td>
			<select id="combo1" name="combo1">
				<option value="chose">Choose One</option>
				<option value="1" <?php if($stockout == '1'){echo 'selected';}?>>Receive SMS</option>
				<option value="0" <?php if($stockout == '0'){echo 'selected';}?>>No SMS</option>
			</select></td>
		</tr>
		<tr>
			<td>Consumption &nbsp;<span class="mandatory"></span></td>
			<td>
			<select id="combo2" name="combo2">
				<option value="chose">Choose One</option>
				<option value="1" <?php if($consumption == '1'){echo 'selected';}?>>Receive SMS</option>
				<option value="0" <?php if($consumption == '0'){echo 'selected';}?>>No SMS</option>
			</select></td>
		</tr>
		<tr>
			<td>Cold Chain Capacity &nbsp;<span class="mandatory"></span></td>
			<td>
			<select id="combo3" name="combo3">
				<option value="chose">Choose One</option>
				<option value="1" <?php if($coldchain == '1'){echo 'selected';}?>>Receive SMS</option>
				<option value="0" <?php if($coldchain == '0'){echo 'selected';}?>>No SMS</option>
			</select></td>
		</tr>
		</tr>
		<tr>
			<td align="center" colspan=2>
			<input name="submit" type="submit" class="button" value="Save Recipient">
			<input	name="reset" type="reset" class="button" value="Clear Fields">
			</td>
			</tr>
			</tbody>
			</table>
			</form>
