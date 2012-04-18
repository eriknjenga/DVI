<script>
	$(document).ready(function() {
var default_datepicker_options = {"changeMonth": true, "changeYear": true};
$( "#pickup_date" ).datepicker(default_datepicker_options);
});
</script>
<?php
$attributes = array('enctype' => 'multipart/form-data');
echo form_open('orders_management/save_approval', $attributes);
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
			<td><b>Origin of Order</b></td>
			<td><?php echo $order -> Origin_District -> name . $order -> Origin_Region -> name;?></td>
		</tr>
		<tr>
			<td><b>Order Made By</b></td>
			<td><?php echo $order -> Order_Maker -> Full_Name;?></td>
		</tr>
		<tr>
			<td><b>Vaccine Requested</b></td>
			<td><?php echo $order -> Vaccine_Ordered -> Name;?></td>
		</tr>
		<tr>
			<td><b>Requested Quantity</b></td>
			<td><?php echo $order -> Quantity;?></td>
		</tr>
		<tr>
			<td><b>Approved Quantity</b></td>
			<td>
			<input type="text" name="approved_quantity"/>
			<input type="hidden" name="order_id" value="<?php echo $order->id;?>"/>
			</td>
		</tr>
		<tr>
			<td><b>Pick up Date</b></td>
			<td>
			<input type="text" name="pickup_date" id="pickup_date"/>
			</td>
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