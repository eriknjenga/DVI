
<div class="section_title"><?php echo $title;?></div>
<?php
if(isset($disbursement)){ 
$Vaccine_Id = $disbursement['Vaccine_Id'];
$Date_Issued = $disbursement['Date_Issued'];
$Quantity = $disbursement['Quantity'];
$Batch_Number = $disbursement['Batch_Number'];
$Voucher_Number = $disbursement['Voucher_Number']; 
if($disbursement['Issued_To_Region']>0){
$Issued_To = "region_".$disbursement['Issued_To_Region'];
}
else if ($disbursement['Issued_To_District']>0){
$Issued_To = "district_".$disbursement['Issued_To_District'];
}
else if ($disbursement['Issued_To_Facility']>0){
$Issued_To = "facility_".$disbursement['Issued_To_Facility'];
}
}
else{
$Vaccine_Id = "";
$Date_Issued = Date('m/d/Y');
$Quantity = "";
$Batch_Number = "";
$Voucher_Number = "";
$Issued_To = ""; 
}

?>

<script type="text/javascript">
$(document).ready(function() {
	$("#add_disbursement_form").validationEngine();
	//Create Javascript Array for holding all autocomplete suggestions. They are not too many
	var autocomplete_elements = Array();
	<?php 
	if(isset($districts)){
	//Create PHP arrays to traverse all districts and provinces
	$counter = 0;
	foreach($districts as $district){
	if($Issued_To == "district_".$district['id']){$Issued_To = $district['name'];};
	?>
	autocomplete_elements[<?php echo $counter;?>] = {value: "<?php echo $district['name'];?>", id: "district_<?php echo $district['id'];?>"}
	<?php 
	$counter++;
	}
	}
	if(isset($regions)){
	foreach($regions as $region){
	if($Issued_To == "region_".$region['id']){$Issued_To = $region['name'];};
	?>
	autocomplete_elements[<?php echo $counter;?>] = {value: "<?php echo $region['name'];?>", id: "region_<?php echo $region['id'];?>"}
	<?php 
	$counter++;
	}
	}
	if(isset($facilities)){
	foreach($facilities as $facility){
	if($Issued_To == "facility_".$facility['facilitycode']){$Issued_To = $facility['name'];};
	?>
	autocomplete_elements[<?php echo $counter;?>] = {value: "<?php echo $facility['name'];?>", id: "facility_<?php echo $facility['facilitycode'];?>"}
	<?php 
	$counter++;
	}
	}
	if(isset($additional_facilities)){
	foreach($additional_facilities as $additional_facility){?>
	autocomplete_elements[<?php echo $counter;?>] = {value: "<?php echo $additional_facility->Facilities['name'];?>", id: "facility_<?php echo $additional_facility->Facilities['facilitycode'];?>"}
	<?php 
	$counter++;
	}
	} 
	?>  
	//Finish creating Autocomplete array
	//Create the jqueryui autocomplete object
	$( "#issued_to" ).autocomplete({
	         	source: autocomplete_elements,
	         	select: function(event, ui) {
     			 var selected_id = ui.item.id; 
     			$( "#issued_to_id" ).attr("value",selected_id); 
     			 }
	         });
	$( "#issued_to" ).autocomplete("search","<?php echo $Issued_To;?>"); 
    //Create all the datepickers
	var default_datepicker_options = {"changeMonth": true, "changeYear": true, "defaultDate":'<?php echo $Date_Issued?>'};
	$( "#date_issued" ).datepicker(default_datepicker_options);
	});
function cleanup(){
	$("#reset_vaccine_form").click();
	$( "#date_issued" ).datepicker('setDate', '<?php echo $Date_Issued;?>'); 
	$("#viles").html(""); 
}
</script>

<div id="form_area"><?php
$attributes = array('enctype' => 'multipart/form-data','id'=>'add_disbursement_form');
if(isset($edit)){
echo form_open('disbursement_management/save/'.$id,$attributes);
}
else{
echo form_open('disbursement_management/save',$attributes);
}
echo validation_errors('
<p class="error">','</p>
'); 
?>

<table border="0" class="data-table">

	<tr>
		<th class="subsection-title" colspan="2">Disbursement Information for
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
			<td><span class="mandatory">*</span>Vaccine</td>
			<td><select name="vaccine_id"> 
						<?php foreach($vaccines as $vaccine){?>
							<option value="<?php echo $vaccine->id?>" <?php if ($vaccine->id == $Vaccine_Id){echo 'selected';}?>><?php echo $vaccine->Name;?></option>
						<?php }?>
					</select></td>
		</tr>
		<tr>
			<td><span class="mandatory">*</span> Date Issued (Change if
			Backdated)</td>
			<td><?php

			$data_date_issued= array(
				'name'        => 'date_issued', 'id'=>'date_issued', 'value' =>$Date_Issued,'class'=>'validate[required]'
				);
				echo form_input($data_date_issued); ?></td>
		</tr>
		<tr>
			<td><span class="mandatory">*</span> Issued to (Type First Few
			Characters)</td>
			<td><?php

			$data_issued_to= array(
				 'name'        => 'issued_to', 'id'=>'issued_to' ,'class'=>'validate[required]'
				 );
				 echo form_input($data_issued_to); ?></td>
		</tr>
		<tr>
			<td><span class="mandatory">*</span>No. of Doses Issued</td>
			<td><?php

			$data_doses = array(
				 'name'        => 'doses',
					'id' => 'doses', 'value' =>$Quantity,'class'=>'validate[required,custom[integer]]'
				 );
				 echo form_input($data_doses); ?></td>
		</tr>
		<tr>
			<td>Batch Number</td>
			<td><?php

			$data_batch_number = array(
				 'name'        => 'batch_number', 'value' =>$Batch_Number
				 );
				 echo form_input($data_batch_number); ?></td>
		</tr>

		<tr>
			<td>Voucher Number</td>
			<td><?php

			$data_voucher_number = array(
				 'name'        => 'voucher_number', 'id'=>'voucher_number', 'value' =>$Voucher_Number
				 );
				 echo form_input($data_voucher_number); ?></td>
		</tr>

		<tr>
			<td align="center" colspan=2><input name="submit" type="submit"
				class="button" value="Save Disbursement Information"> <input
				name="reset" type="reset" class="button" value="Reset Fields"
				id="reset_vaccine_form"></td>
		</tr>

	</tbody>
</table>
				 <?php echo form_close();?></div>
 
