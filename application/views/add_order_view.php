<script type="text/javascript">
	$(document).ready(function() {
		$(".add").click(function() { 
			var cloned_object = $(this).closest('tr').clone(true);
			cloned_object.insertAfter($(this).closest('tr'));
			return false;
		});
	});

</script>
<?php
$attributes = array('enctype' => 'multipart/form-data');
echo form_open('orders_management/save_order', $attributes);
echo validation_errors('
<p class="error">', '</p>
');
?>
<table border="0" class="data-table">
	<tr>
		<th class="subsection-title" colspan="5">Order Details</th>
	</tr>
	<tbody>
		<tr>
			<td>Vaccine</td>
			<td>
			<select name="vaccine[]">
				<?php
foreach($vaccines as $vaccine){
				?>
				<option value="<?php echo $vaccine->id?>"><?php echo $vaccine -> Name;?></option>
				<?php }?>
			</select></td>
			<td>Quantity</td>
			<td><?php

			$data_search = array('name' => 'quantity[]');
			echo form_input($data_search);
			?></td>
			<td>
			<input type="button" class="add button" value="Add"/>
			</td>
		</tr>
		<tr>
			<td>Other Items Needed</td>
			<td colspan="4"><?php

			$data_search = array('name' => 'other_items', 'rows' => '5', 'cols' => '30');
			echo form_textarea($data_search);
			?></td>
		</tr>
			<tr>
		<td align="center" colspan=5>
		<input name="submit" type="submit"
		class="button" value="Save Order">
		</td>
	</tr>
	</tbody>
</table>
<?php echo form_close();?>