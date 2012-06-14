<div class="section_title">
	<?php echo $title;?>
</div>
<div id="notification_panel">
	<div id="notification_panel_image"></div>
	<div id="notification_panel_text">
		Fill in the following form to record your vaccine receipts
	</div>
</div>
<?php
$this -> load -> view('vaccine_tabs');
if (isset($disbursement)) { 
	$Vaccine_Id = $disbursement['Vaccine_Id'];
	$Date_Received = $disbursement['Date_Issued'];
	$Quantity = $disbursement['Quantity'];
	$Batch_Number = $disbursement['Batch_Number'];
	$Voucher_Number = $disbursement['Voucher_Number']; 
	if ($disbursement['Issued_By_Region'] > 0) {
		$Issued_By = "region_" . $disbursement['Issued_By_Region'];
	} else if ($disbursement['Issued_By_District'] > 0) {
		$Issued_By = "district_" . $disbursement['Issued_By_District'];
	} else if ($disbursement['Issued_By_National'] =="0") {
		$Issued_By = "national_0";
	}
} else {
	$Vaccine_Id = "";
	$Date_Received = Date('m/d/Y');
	$Quantity = "";
	$Batch_Number = "";
	$Voucher_Number = "";
	$Issued_By = ""; 
}
?>

<script type="text/javascript">
		$(document).ready(function() {
	$("#add_receivables_form").validationEngine();
	//Create Javascript Array for holding all autocomplete suggestions. They are not too many
	var autocomplete_elements = Array();
	autocomplete_elements[0] = {value: "Central Vaccine Store", id: "national_0"};
	<?php 
	if($Issued_By == "national_0"){$Issued_By = "Central Vaccine Store";};
	if(isset($districts)){
	//Create PHP arrays to traverse all districts and provinces
	$counter = 1;
	foreach($districts as $district){
	if($Issued_By == "district_".$district['id']){$Issued_By = $district['name'];};
	?>
	autocomplete_elements[<?php echo $counter;?>] = {value: "<?php echo $district['name'];?>", id: "district_<?php echo $district['id'];?>"}
	<?php 
	$counter++;
	}
	}
	if(isset($regions)){
	foreach($regions as $region){
	if($Issued_By == "region_".$region['id']){$Issued_By = $region['name'];};
	?>
	autocomplete_elements[<?php echo $counter;?>] = {value: "<?php echo $region['name'];?>", id: "region_<?php echo $region['id'];?>"}
	<?php 
	$counter++;
	}
	} 
	?>  
	//Finish creating Autocomplete array
	//Create the jqueryui autocomplete object
	$( "#received_from" ).autocomplete({
	source: autocomplete_elements,
	select: function(event, ui) {
	var selected_id = ui.item.id;
	$( "#received_from_id" ).attr("value",selected_id);
	}
	});
	$( "#received_from" ).autocomplete("search","<?php echo $Issued_By;?>");

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
	$attributes = array('enctype' => 'multipart/form-data','id'=>'add_receivables_form'); 
	if(isset($edit)){
	echo form_open('disbursement_management/save_receipt/'.$id,$attributes);
	}
	else{
	echo form_open('disbursement_management/save_receipt',$attributes);
	}

	echo validation_errors('
<p class="error">', '</p>
');
	?>

	<table border="0" class="data-table">
		<tr>
			<th class="subsection-title" colspan="2">Receipt Information for <span id="vaccine_name_label" style="color: #E01B4C;"></span></th>
		</tr>
		<tbody>
			<input type="hidden" id="current_tab" />
			<input type="hidden" id="received_from_id" name="received_from_id" />
			<input type="hidden" id="vaccine_id" name="vaccine_id" />
			<tr>
				<td colspan="4"><em>Enter required details below:-</em></td>
			</tr>
			<tr>
				<td><span class="mandatory">*</span> Received From (Type First Few
				Characters)</td>
				<td><?php

				$data_received_from = array('name' => 'received_from', 'id' => 'received_from','class'=>'validate[required]');
				echo form_input($data_received_from);
				?></td>
			</tr>
			<tr>
				<td><span class="mandatory">*</span> Date Received </td>
				<td><?php

				$data_date_issued = array('name' => 'date_received', 'id' => 'date_received','value' =>$Date_Received,'class'=>'validate[required]');
				echo form_input($data_date_issued);
				?></td>
			</tr>
			<tr>
				<td><span class="mandatory">*</span>No. of Doses Received</td>
				<td><?php

				$data_doses = array('name' => 'doses', 'id' => 'doses','value' =>$Quantity,'class'=>'validate[required,custom[integer]]');
				echo form_input($data_doses);
				?></td>
			</tr>
			<tr>
				<td> Batch Number</td>
				<td><?php

				$data_batch_number = array('name' => 'batch_number','value' =>$Batch_Number,);
				echo form_input($data_batch_number);
				?></td>
			</tr>
			<tr>
				<td>Voucher Number</td>
				<td><?php

				$data_voucher_number = array('name' => 'voucher_number', 'id' => 'voucher_number','value' =>$Voucher_Number,);
				echo form_input($data_voucher_number);
				?></td>
			</tr>
			<tr>
				<td align="center" colspan=2>
				<input name="submit" type="submit"
				class="button" value="Save Receipt Information">
				<input name="reset"
				type="reset" class="button" value="Reset Fields" id="reset_vaccine_form">
				</td>
			</tr>
		</tbody>
	</table>
	<?php echo form_close();?>
</div>
<style type="text/css">
	#batch_information {
		float: left;
		min-width: 100px;
	}
	#form_area {
		float: left;
	}
</style>
