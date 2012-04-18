<div class="section_title"><?php echo $title;?></div>
<div id="notification_panel">
<div id="notification_panel_image"></div>
<div id="notification_panel_text">
Fill in the Following Form <b>ONLY</b> if you received your vaccines directly from the <b>National Level</b>.
</div>
</div> 

	<?php
	$this->load->view('vaccine_tabs');
	?>
	
<script type="text/javascript">
$(document).ready(function() {
    //Create all the datepickers
	var default_datepicker_options = {"changeMonth": true, "changeYear": true};
	$( "#date_received" ).datepicker(default_datepicker_options);
	$( "#date_received" ).datepicker('setDate', new Date()); 
 
	});
function cleanup(){
	$("#reset_vaccine_form").click();
	$( "#date_received" ).datepicker('setDate', new Date());  
}
</script>

<div id="form_area">

	
	<?php
$attributes = array('enctype' => 'multipart/form-data');
echo form_open('disbursement_management/save_receipt',$attributes);

echo validation_errors('
<p class="error">','</p>
'); 
?>

<table border="0" class="data-table">

	<tr>
		<th class="subsection-title" colspan="2">Receipt Information for
		<span id="vaccine_name_label" style="color: #E01B4C;"></span></th>
	</tr>
	<tbody>
		<input type="hidden" id="current_tab" />
		<input type="hidden" id="issued_to_id" name="issued_to_id" />
		<input type="hidden" id="vaccine_id" name="vaccine_id" />
		<tr>
			<td colspan="4"><em>Enter required details below:-</em></td>
		</tr>
		<tr>
			<td><span class="mandatory">*</span> Date Received </td>
			<td><?php

			$data_date_issued= array(
				'name'        => 'date_received', 'id'=>'date_received'
				);
				echo form_input($data_date_issued); ?></td>
		</tr>
 
		<tr>
			<td><span class="mandatory">*</span>No. of Doses Received</td>
			<td><?php

			$data_doses = array(
				 'name'        => 'doses',
					'id' => 'doses'
				 );
				 echo form_input($data_doses); ?></td>
		</tr>
		<tr>
			<td>  Batch Number</td>
			<td><?php

			$data_batch_number = array(
				 'name'        => 'batch_number'
				 );
				 echo form_input($data_batch_number); ?></td>
		</tr>

				<tr>
			<td>Voucher Number</td>
			<td><?php

			$data_voucher_number = array(
				 'name'        => 'voucher_number', 'id'=>'voucher_number'
				 );
				 echo form_input($data_voucher_number); ?></td>
		</tr>
 
			<tr>
				<td align="center" colspan=2><input name="submit" type="submit"
					class="button" value="Save Receipt Information"> <input name="reset"
					type="reset" class="button" value="Reset Fields" id="reset_vaccine_form"></td>
			</tr>
	
	</tbody>
</table>
				 <?php echo form_close();?></div>
<style type="text/css">
#batch_information {
	float: left;
	min-width: 100px;
}
 

#form_area {
	float: left;
}

</style>
 
