<script type="text/javascript">
	$(document).ready(function() {
		$("#start_date").datepicker();
		$("#end_date").datepicker();
	});

</script>
<?php
$attributes = array('enctype' => 'multipart/form-data');
echo form_open('consumption_management/download', $attributes);
echo validation_errors('
<p class="error">', '</p>
');
?>

<table border="0" class="data-table">
	<tr>
		<th class="subsection-title" colspan="2">Specify Parameters</th>
	</tr>
	<tbody>
		<tr>
			<td><span class="mandatory">*</span>From</td>
			<td>
			<input type="text" name="start_date" id="start_date" />
			</td>
		</tr>
		<tr>
			<td><span class="mandatory">*</span>To</td>
			<td>
			<input type="text" name="end_date" id="end_date" />
			</td>
		</tr>
		<tr>
			<td align="center" colspan=2>
			<input name="submit" type="submit"
			class="button" value="Download Report">
			</td>
		</tr>
	</tbody>
</table>
<?php echo form_close();?>