<script type="text/javascript">
	$(document).ready(function() {
		refreshDatePickers();
		$(".add").click(function() {
			var cloned_object = $('#populations tr:last').clone(true);
			cloned_object.insertAfter('#populations tr:last');
			
			refreshDatePickers();
			return false;
		});
	});
	function refreshDatePickers() {
		var counter = 0;
		$('.date').each(function() {
			var new_id = "date_" + counter;
			$(this).attr("id", new_id);
			$(this).datepicker("destroy");
			$(this).not('.hasDatePicker').datepicker();
			counter++;
		});
	}
</script>
<div class="section_title">
	<?php echo $title;?>
</div>
<?php

$attributes = array('enctype' => 'multipart/form-data');
echo form_open('batch_management/save_plan', $attributes);
echo validation_errors('
<p class="error">', '</p>
');
?>
<input type="hidden" name="vaccine" value="<?php echo $vaccine -> id;?>"/>
<table border="0" class="data-table" id="populations">
	<th class="subsection-title" colspan="13">Expected Arrivals</th>
	<tr>
		<th>Date Expected</th>
		<th>Quantity Expected</th>
		<th>Add New</th>
	</tr>
	<?php if(isset($plans[0])){
foreach($plans as $plan){

	?>
	<tr drug_row="0">
		<td>
		<input type="text" name="dates[]" class="date" id="0" value="<?php echo $plan -> expected_date;?>" />
		</td>
		<td>
		<input type="text" name="amounts[]" class="amounts" value="<?php echo $plan -> expected_amount;?>"/>
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
		<input type="text" name="dates[]" class="date" />
		</td>
		<td>
		<input type="text" name="amounts[]" class="amount" />
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
		class="button" value="Save Plan">
		</td>
	</tr>
	</tbody>
</table>
<?php echo form_close();?>