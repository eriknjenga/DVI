
<div class="section_title"><?php echo $title;?></div>
<?php
$this->load->view('vaccine_tabs');
if(isset($disbursement)){ 
$Vaccine_Id = $disbursement['Vaccine_Id'];
$Date_Issued = $disbursement['Date_Issued'];
$Quantity = $disbursement['Quantity'];
$Batch_Number = $disbursement['Batch_Number'];
$Voucher_Number = $disbursement['Voucher_Number'];
$Stock_At_Hand = $disbursement['Stock_At_Hand'];
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
$Stock_At_Hand= "";
}

?>

<script type="text/javascript">
$(document).ready(function() {
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
	var vaccine_stocks = Array();
	
	<?php 
	foreach($vaccines as $vaccine){
		if(!isset($total_received[$vaccine['id']]['Totals'])){
		$total_received[$vaccine['id']]['Totals'] = 0;
	}
	if(!isset($total_issued[$vaccine['id']]['Totals'])){
		$total_issued[$vaccine['id']]['Totals'] = 0;
	}
 
	?>
		vaccine_stocks["vaccine_<?php echo $vaccine['id'];?>"] = <?php echo $stock_balance[$vaccine['id']];?>;
	<?php }
	?> 

	
	$(".vaccine_name").click(function (){
		$("#vaccine_stock").attr("value",vaccine_stocks[$(this).attr("id")]);
		$("#vaccine_stock").html(vaccine_stocks[$(this).attr("id")]); 
		$("#current_tab").attr("value",$(this).attr("id"));
	});
	$("#doses").keyup(function(){
		var doses = $("#doses").attr("value");
		if(!isNaN(doses) && doses.length>0){
		var stock = $("#vaccine_stock").attr("value");
		$("#vaccine_stock").html(stock-doses+" remaining");
		}
		});
	<?php if(isset($Vaccine_Id)){?>
	$("#vaccine_<?php echo $Vaccine_Id;?>").click();
	<?php }
	?>
	});
function cleanup(){
	$("#reset_vaccine_form").click();
	$( "#date_issued" ).datepicker('setDate', '<?php echo $Date_Issued;?>'); 
	$("#viles").html(""); 
}
</script>

<div id="form_area"><?php
$attributes = array('enctype' => 'multipart/form-data');
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
			<td><span class="mandatory">*</span> Date Issued (Change if
			Backdated)</td>
			<td><?php

			$data_date_issued= array(
				'name'        => 'date_issued', 'id'=>'date_issued', 'value' =>$Date_Issued
				);
				echo form_input($data_date_issued); ?></td>
		</tr>
		<tr>
			<td><span class="mandatory">*</span> Issued to (Type First Few
			Characters)</td>
			<td><?php

			$data_issued_to= array(
				 'name'        => 'issued_to', 'id'=>'issued_to' 
				 );
				 echo form_input($data_issued_to); ?></td>
		</tr>
		<tr>
			<td><span class="mandatory">*</span>No. of Doses Issued</td>
			<td><?php

			$data_doses = array(
				 'name'        => 'doses',
					'id' => 'doses', 'value' =>$Quantity
				 );
				 echo form_input($data_doses); ?></td>
		</tr>
		<tr>
			<td><span class="mandatory">*</span>Reported Stock at Hand</td>
			<td><?php

			$data_stock = array(
				 'name'        => 'stock_at_hand',
					'id' => 'stock_at_hand', 'value' =>$Stock_At_Hand
				 );
				 echo form_input($data_stock); ?></td>
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
<style type="text/css">
#batch_information {
	float: left;
	min-width: 100px;
}

#vaccine_information {
	float: left;
	min-width: 100px;
}

#form_area {
	float: left;
}
</style>


<div id="vaccine_information">
<table border="0" class="data-table">

	<tr>
		<th class="subsection-title" colspan="2">Vaccine Information (Not
		Editable)</th>
	</tr>
	<tr><td>Stock Left: </td>
		<td class="subsection-title" ><span id="vaccine_stock" value="" style="color: #E01B4C; font-weight:bold; font-size:16px;"></span></td>
	</tr>
	<tr id="viles"></tr>
</table>


</div>
