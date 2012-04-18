<script type="text/javascript">
	$(document).ready(function() {
		$(".add").click(function() {
			var cloned_object = $('#recipients tr:last').clone(true);
			cloned_object.insertAfter('#recipients tr:last');
			return false;
		});
	});
</script>
<?php

$attributes = array('enctype' => 'multipart/form-data');
echo form_open('report_management/save_recipient', $attributes);
echo validation_errors('
<p class="error">', '</p>
');
?>
<table border="0" class="data-table" id="recipients">
	<th class="subsection-title" colspan="13">Email Recipients</th>
	<tr>
		<th>Recipient Name</th>
		<th>Email Address</th>
		<th>Add New</th>
	</tr>
	<?php if(isset($recipients[0])){
foreach($recipients as $recipient){

	?>
	<tr>
		<td>
		<input type="text" name="names[]" class="names" id="0" value="<?php echo $recipient -> Name;?>" />
		</td>
		<td>
		<input type="text" name="emails[]" class="emails" value="<?php echo $recipient -> Email;?>"/>
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
		<input type="text" name="names[]" class="names" />
		</td>
		<td>
		<input type="text" name="emails[]" class="emails" />
		</td>
		<td>
		<input type="button" class="add button" value="New"/>
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
		class="button" value="Save Recipients">
		</td>
	</tr>
	</tbody>
</table>
<?php echo form_close();?>