<div class="quick_menu">
	<a class="quick_menu_link" href="<?php echo site_url("user_management/listing");?>">&lt; &lt; Listing</a>
</div>
<script type="text/javascript">
	$(document).ready(function() {3
		$(".user_group").change(function() {
			var identifier = $(this).find(":selected").attr("usergroup");
			if(identifier == "national_officer") {
				$("#region_selector").css("display", "none");
				$("#district_selector").css("display", "none");
			} else if(identifier == "provincial_officer") {
				$("#district_selector").css("display", "none");
				$("#region_selector").css("display", "table-row");
			} else if(identifier == "district_officer") {
				$("#region_selector").css("display", "none");
				$("#district_selector").css("display", "table-row");
			} else {
				$("#region_selector").css("display", "none");
				$("#district_selector").css("display", "none");
			}
		});
	});

</script>
<?php
if (isset($user)) {
	$name = $user -> Full_Name;
	$district_province_id = $user -> District_Province_Id;
	$user_group = $user -> User_Group;
	$username = $user -> Username;
	$user_id = $user -> id;
} else {
	$name = "";
	$district_province_id = "";
	$user_group = "";
	$username = "";
	$user_id = "";

}
$attributes = array('enctype' => 'multipart/form-data');
echo form_open('user_management/save', $attributes);
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
			<td><span class="mandatory">*</span> Username</td>
			<td><?php

			$data_search = array('name' => 'username', 'value' => $username);
			echo form_input($data_search);
			?></td>
		</tr>
		<tr>
			<td> User Group</td>
			<td>
			<select name="user_group" class="user_group">
				<option value=''>None Selected</option>
				<?php
foreach($groups as $group){
				?>
				<option value="<?php echo $group->id?>" usergroup="<?php echo $group->Identifier?>" <?php
				if ($group -> id == $user_group) {echo "selected";
				}
				?> ><?php echo $group->Name
					?></option>
				<?php }?>
			</select></td>
		</tr>
		<tr id="region_selector">
			<td> Region</td>
			<td>
			<select name="region" >
				<option value="">None Selected</option>
				<?php
foreach($regions as $region){
				?>
				<option value="<?php echo $region->id?>" <?php
				if ($region -> id == $district_province_id) {echo "selected";
				}
				?> ><?php echo $region->name
					?></option>
				<?php }?>
			</select></td>
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