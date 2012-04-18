<script type="text/javascript">
	$(document).ready(function() {
		$(".add").click(function() {
			var cloned_object = $('#populations tr:last').clone(true);
			cloned_object.insertAfter('#populations tr:last');
			return false;
		});
	});

</script>
<?php
if (isset($district)) {
	$name = $district -> name;
	$province_id = $district -> province;
	$latitude = $district -> latitude;
	$longitude = $district -> longitude;
	$district_id = $district->id;
} else {
	$name = "";
	$province_id = "";
	$latitude = "";
	$longitude = "";
	$district_id = "";

}
$attributes = array('enctype' => 'multipart/form-data');
echo form_open('district_management/save', $attributes);
echo validation_errors('
<p class="error">', '</p>
');
?>
<input type="hidden" name="district_id" value = "<?php echo $district_id; ?>"/>
<table border="0" class="data-table">
	<tr>
		<th class="subsection-title" colspan="2">District Details</th>
	</tr>
	<tbody>
		<tr>
			<td><span class="mandatory">*</span> District Name</td>
			<td><?php

			$data_search = array('name' => 'name', 'value' => $name);
			echo form_input($data_search);
			?></td>
		</tr>
		<tr>
			<td><span class="mandatory">*</span> Province</td>
			<td>
			<select name="province">
				<?php
foreach($provinces as $province){
				?>
				<option value="<?php echo $province->id?>" <?php
				if ($province -> id == $province_id) {echo "selected";
				}
				?> ><?php echo $province->name
					?></option>
				<?php }?>
			</select></td>
		</tr>
		<tr>
			<td> Latitude</td>
			<td><?php

			$data_search = array('name' => 'latitude', 'value' => $latitude);
			echo form_input($data_search);
			?></td>
		</tr>
		<tr>
			<td> Longitude</td>
			<td><?php

			$data_search = array('name' => 'longitude', 'value' => $longitude);
			echo form_input($data_search);
			?></td>
		</tr>
	</tbody>
</table>
<table border="0" class="data-table" id="populations">
	<th class="subsection-title" colspan="13">Add Populations</th>
	<tr>
		<th>Year</th>
		<th>Population</th>
		<th>Add New</th>
	</tr>
	<?php if(isset($district_populations[0])){
foreach($district_populations as $district_population){

	?>
	<tr drug_row="0">
		<td>
		<input type="text" name="years[]" class="year" value="<?php echo $district_population -> year;?>" />
		</td>
		<td>
		<input type="text" name="populations[]" class="population" value="<?php echo $district_population -> population;?>"/>
		</td>
		<td>
		<input type="button" class="add button" value="Add"/>
		</td>
	</tr>
	<?php
	}//end foreach loop
	}//endif
	else{
	?>
	<tr drug_row="0">
		<td>
		<input type="text" name="years[]" class="year" />
		</td>
		<td>
		<input type="text" name="populations[]" class="population" />
		</td>
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
		class="button" value="Save District">
		</td>
	</tr>
	</tbody>
</table>
<?php echo form_close();?>