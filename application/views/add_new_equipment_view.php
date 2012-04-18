<script type="text/javascript">
	$(document).ready(function() {
		$(".add").click(function() {
			var cloned_object = $('#fridges tr:last').clone(true);
			cloned_object.insertAfter('#fridges tr:last');
			return false;
		});
	});

</script>
<?php
$attributes = array('enctype' => 'multipart/form-data');
echo form_open('fridge_management/save_equipment', $attributes);
echo validation_errors('
<p class="error">', '</p>
');
?>
<table border="0" class="data-table" id="fridges">
	<th class="subsection-title" colspan="13">Add Refrigeration Equipment</th>
	<tr>
		<th>Fridge</th>
		<th>Add New</th>
	</tr>
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
</table>
<table class="data-table">
	<tr>
		<td align="center" colspan=2>
		<input name="submit" type="submit"
		class="button" value="Save Equipment List">
		</td>
	</tr>
	</tbody>
</table>
<?php echo form_close();?>