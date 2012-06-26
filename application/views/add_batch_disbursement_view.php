
<div class="section_title"><?php echo $title;?></div>
<?php
$this->load->view('vaccine_tabs');
?>

<script type="text/javascript">
$(document).ready(function() {
	//Add the row cloning code
	$(".add").click(function() { 
			var cloned_object = $(this).closest("tr").clone(true);
			cloned_object.insertAfter($(this).closest("tr"));
			return false;
		});
	$(".remove").click(function() {
			var current_row = $(this).closest("tr");
			if(current_row[0].rowIndex == 2){
				return false;
			}
			else{
				current_row.remove();
			}
		});
		
	$("#add_disbursement_form").validationEngine();
	//Create Javascript Array for holding all autocomplete suggestions. They are not too many
	var autocomplete_elements = Array();
	<?php 
	if(isset($districts)){
	//Create PHP arrays to traverse all districts and provinces
	$counter = 0;
	foreach($districts as $district){
	?>
	autocomplete_elements[<?php echo $counter;?>] = {value: "<?php echo $district['name'];?>", id: "district_<?php echo $district['id'];?>"}
	<?php 
	$counter++;
	}
	}
	if(isset($regions)){
	foreach($regions as $region){
	?>
	autocomplete_elements[<?php echo $counter;?>] = {value: "<?php echo $region['name'];?>", id: "region_<?php echo $region['id'];?>"}
	<?php 
	$counter++;
	}
	}
	if(isset($facilities)){
	foreach($facilities as $facility){
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
    //Create all the datepickers
	var default_datepicker_options = {"changeMonth": true, "changeYear": true};
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
	$(".vaccine_name")[0].click();
	});
function cleanup(){
	$("#reset_vaccine_form").click();
	$("#viles").html(""); 
}
</script>

<div id="form_area"><?php
$attributes = array('enctype' => 'multipart/form-data','id'=>'add_disbursement_form');
echo form_open('disbursement_management/save',$attributes);
echo validation_errors('
<p class="error">','</p>
'); 
?>

<table border="0" class="data-table">


	<thead>
	<tr>
		<th class="subsection-title" colspan="7">Disbursement Information for
		<span id="vaccine_name_label" style="color: #E01B4C;"></span></th>
	</tr>
			<th><span class="mandatory">*</span>Date Issued</th>
			<th><span class="mandatory">*</span>Issued To</th>
			<th><span class="mandatory">*</span>Doses Issued</th>
			<th>Reported Stock</th>
			<th>Batch Number</th>
			<th>Voucher Number</th>
			<th>Action</th>
		</thead>
	<tbody>
		<tr>
		<input type="hidden" id="current_tab" />
		<input type="hidden" id="issued_to_id" name="issued_to_id" />
		<input type="hidden" id="vaccine_id" name="vaccine_id" />
			<td><?php

			$data_date_issued= array(
				'name'        => 'date_issued', 'id'=>'date_issued','class'=>'validate[required]'
				);
				echo form_input($data_date_issued); ?></td>
			<td><?php

			$data_issued_to= array(
				 'name'        => 'issued_to', 'id'=>'issued_to' ,'class'=>'validate[required]'
				 );
				 echo form_input($data_issued_to); ?></td>
			<td><?php

			$data_doses = array(
				 'name'        => 'doses',
					'id' => 'doses','class'=>'validate[required,custom[integer]]'
				 );
				 echo form_input($data_doses); ?></td>
			<td><?php

			$data_stock = array(
				 'name'        => 'stock_at_hand',
					'id' => 'stock_at_hand'
				 );
				 echo form_input($data_stock); ?></td>
			<td><?php

			$data_batch_number = array(
				 'name'        => 'batch_number'
				 );
				 echo form_input($data_batch_number); ?></td> 
			<td><?php

			$data_voucher_number = array(
				 'name'        => 'voucher_number', 'id'=>'voucher_number'
				 );
				 echo form_input($data_voucher_number); ?></td>
				 <td><input type="button" class="add button" value="+"> <input type="button" class="remove button" value="-"></td>
		</tr>

		<tr>
			<td align="center" colspan=7><input name="submit" type="submit"
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
