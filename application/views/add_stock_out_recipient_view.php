<div class="quick_menu">
	<a class="quick_menu_link" href="<?php echo site_url("district_stock_out");?>">&lt; &lt; Listing</a>
</div> 
<?php
if (isset($user)) {
	$name = $user -> Full_Name;
	$district_province_id = $user -> District; 
	$email = $user -> Email;
	$user_id = $user -> id;
} else {
	$name = "";
	$district_province_id = ""; 
	$email = "";
	$user_id = "";

}
$attributes = array('enctype' => 'multipart/form-data');
echo form_open('district_stock_out/save', $attributes);
echo validation_errors('
<p class="error">', '</p>
');
?>
<input type="hidden" name="user_id" value = "<?php echo $user_id;?>"/>
<table border="0" class="data-table">
	<tr>
		<th class="subsection-title" colspan="2">User Details</th>
	</tr>
	<tbody>
		<tr>
			<td><span class="mandatory">*</span> Full Name</td>
			<td><?php

			$data_search = array('name' => 'name', 'value' => $name);
			echo form_input($data_search);
			?></td>
		</tr>
		<tr>
			<td><span class="mandatory">*</span> Email Address</td>
			<td><?php

			$data_search = array('name' => 'email', 'value' => $email);
			echo form_input($data_search);
			?></td>
		</tr> 
		<tr id="district_selector">
			<td> District</td>
			<td>
			<select name="district" >
				<option value="">None Selected</option>
				<?php
foreach($districts as $district){
				?>
				<option value="<?php echo $district->id?>" <?php
				if ($district -> id == $district_province_id) {echo "selected";
				}
				?> ><?php echo $district->name
					?></option>
				<?php }?>
			</select></td>
		</tr>
		<tr>
			<td align="center" colspan=2>
			<input name="submit" type="submit"
			class="button" value="Save User">
			</td>
		</tr>
	</tbody>
</table>
<?php echo form_close();?>