<script type="text/javascript">
	$(document).ready(function() {
		$(".add").click(function() {
			var cloned_object = $('#fridges tr:last').clone(true);
			cloned_object.insertAfter('#fridges tr:last');
			return false;
		});
	});

</script>
<div class="section_title">
	<?php echo $title;?>
</div>
<?php
if (isset($facility)) {
	$code = $facility -> facilitycode;
	$name = $facility -> name;
	$type = $facility -> facilitytype;
	$phone = $facility -> phone;
	$email = $facility -> email;
	$facility_id = $facility -> id;
} else {
	$code = "";
	$name = "";
	$type = "";
	$phone = "";
	$email = "";
	$facility_id = "";

}
$attributes = array('enctype' => 'multipart/form-data');
echo form_open('facility_management/save_details', $attributes);
echo validation_errors('
<p class="error">', '</p>
');
?>
<input type="hidden" name="facility_id" value = "<?php echo $facility_id;?>"/>
<table border="0" class="data-table">
	<tr>
		<th class="subsection-title" colspan="2">Facility Details</th>
	</tr>
	<tbody>
		<?php
//check if we are editing the details and if we are, hide the facilitycode field
if($facility_id > 0){
		?>
		<input type="hidden" name="facilitycode" value = "<?php echo $code;?>"/>
		<?php }
			//Else, show the facilitycode field
			else{
		?>
		<tr>
			<td><span class="mandatory">*</span> Facility Code</td>
			<td><?php

			$data_code = array('name' => 'facilitycode', 'value' => $code);
			echo form_input($data_code);
			?></td>
		</tr>
		<?php }?>

		<tr>
			<td><span class="mandatory">*</span> Facility Name</td>
			<td><?php

			$data_name = array('name' => 'name', 'value' => $name);
			echo form_input($data_name);
			?></td>
		</tr>
		<tr>
			<td><span class="mandatory">*</span> Facility Type</td>
			<td>
			<select name="type">
				<?php
foreach($types as $facility_type){
				?>
				<option value="<?php echo $facility_type->id?>" <?php
				if ($facility_type -> id == $type) {echo "selected";
				}
				?> ><?php echo $facility_type->Name
					?></option>
				<?php }?>
			</select></td>
		</tr>
		<tr>
			<td> Phone Contact(s)</td>
			<td><?php

			$data_phone = array('name' => 'phone', 'value' => $phone);
			echo form_input($data_phone);
			?></td>
		</tr>
		<tr>
			<td>Email Address(es)</td>
			<td><?php

			$data_email = array('name' => 'email', 'value' => $email);
			echo form_input($data_email);
			?></td>
		</tr>
	</tbody>
</table>
<table border="0" class="data-table" id="fridges">
	<th class="subsection-title" colspan="13">Add Fridges</th>
	<tr>
		<th>Fridge</th>
		<th>Add New</th>
	</tr>
	<?php if(isset($facility_fridges[0])){
foreach($facility_fridges as $facility_fridge){

	?>
	<tr fridge_row="0">
		<td>
		<select name="fridges[]">
			<option value="0">No Fridge Selected</option>
			<?php
foreach($fridges as $fridge){
			?>
			<option value="<?php echo $fridge->id?>" <?php
			if ($fridge -> id == $facility_fridge->Fridge) {echo "selected";
			}
			?> ><?php echo $fridge -> Manufacturer . " " . $fridge -> Model_Name;?></option>
			<?php }?>
		</select></td>
		<td>
		<input type="button" class="add button" value="Add"/>
		</td>
	</tr>
	<?php
	}//end foreach loop
	}//endif
	else{
	?>
	<tr fridge_row="0">
		<td>
		<select name="fridges[]">
			<option value="0">No Fridge Selected</option>
			<?php
foreach($fridges as $fridge){
			?>
			<option value="<?php echo $fridge->id?>"><?php echo $fridge -> Manufacturer . " " . $fridge -> Model_Name;?></option>
			<?php }?>
		</select></td>
		<td>
		<input type="button" class="add button" value="Add"/>
		</td>
	</tr>
	<?php
	}
	?>
</table>
<table class="data-table">
	<tr>
		<td align="center" colspan=2>
		<input name="submit" type="submit"
		class="button" value="Save Facility Details">
		</td>
	</tr>
	</tbody>
</table>
<?php echo form_close();?>