<div class="section_title"><?php echo $title;?></div>
<script type="text/javascript">
$(document).ready(function() {
	//Add the row cloning code
	$(".add").click(function() { 
		$(this).closest("tr").find(".issued_to").autocomplete("destroy");
			var cloned_object = $(this).closest("tr").clone(true);
			
			cloned_object.insertAfter($(this).closest("tr"));
			refreshDatePickers();
			
			//cloned_object.find(".issued_to").removeClass("ui-autocomplete-input");  
			cloned_object.find(".issued_to").attr("value",""); 
			cloned_object.find(".issued_to").focus(); 
			var counter = 0;
		$('.issued_to').each(function() { 
			var new_id = "issued_to_" + counter;
			$(this).attr("id", new_id);
			$(this).autocomplete("destroy");
			/*$(this).autocomplete({
	         	source: autocomplete_elements,
	         	create: function(event, ui) { console.log("Hahah"); },
	         	select: function(event, ui) {
     			 var selected_id = ui.item.id; 
     			 $(this).closest(".issued_to_id").attr("value",selected_id); 
     			 }
	         }); */
			counter++;
		});
		cloned_object.find(".issued_to").focus();  
			 
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
	refreshDatePickers(); 
	//refreshAutoComplete(autocomplete_elements);
	$(".issued_to:not(.ui-autocomplete-input)").live("focus", function (event) {
   		 $(this).autocomplete({
	         	source: autocomplete_elements,
	         	select: function(event, ui) {
     			 var selected_id = ui.item.id;  
     			 $(this).closest("tr").find(".issued_to_id").attr("value",selected_id); 
     			 }
	         });
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
	function refreshAutoComplete(autocomplete_elements) { 
		
		var counter = 0;
		$('.issued_to').each(function() {
			alert();
			var new_id = "issued_to_" + counter;
			$(this).attr("id", new_id);
			$(this).autocomplete("destroy");
			/*$(this).autocomplete({
	         	source: autocomplete_elements,
	         	create: function(event, ui) { console.log("Hahah"); },
	         	select: function(event, ui) {
     			 var selected_id = ui.item.id; 
     			 $(this).closest(".issued_to_id").attr("value",selected_id); 
     			 }
	         }); */
			counter++;
		});

	}  
</script>
<style>
	input[type="text"]{
		width:90px;
	}
</style>
<div id="form_area" style="width: 100%;"><?php
$attributes = array('enctype' => 'multipart/form-data','id'=>'add_disbursement_form');
echo form_open('disbursement_management/save_batch',$attributes);
echo validation_errors('
<p class="error">','</p>
'); 
?>

<table border="0" class="data-table" style="margin:0 auto;">


	<thead>
			<th><span class="mandatory">*</span>Vaccine</th>
			<th><span class="mandatory">*</span>Date Issued</th>
			<th><span class="mandatory">*</span>Issued To</th>
			<th><span class="mandatory">*</span>Doses Issued</th>
			<th>Batch Number</th>
			<th>Voucher Number</th>
			<th>Action</th>
		</thead>
	<tbody>
		<tr>
		<input type="hidden" class="issued_to_id" name="issued_to_id[]" /> 
			<td>
					<select name="vaccine_id[]"> 
						<?php foreach($vaccines as $vaccine){?>
							<option value="<?php echo $vaccine->id?>"><?php echo $vaccine->Name;?></option>
						<?php }?>
					</select>
				</td>
			<td><?php

			$data_date_issued= array(
				'name'        => 'date_issued[]','class'=>'date validate[required]'
				);
				echo form_input($data_date_issued); ?></td>
			<td><?php

			$data_issued_to= array(
				 'name'        => 'issued_to[]', 'id'=>'issued_to','class'=>'issued_to validate[required]'
				 );
				 echo form_input($data_issued_to); ?></td>
			<td><?php

			$data_doses = array(
				 'name'        => 'doses[]',
					'id' => 'doses','class'=>'validate[required,custom[integer]]'
				 );
				 echo form_input($data_doses); ?></td>
			<td><?php

			$data_batch_number = array(
				 'name'        => 'batch_number[]'
				 );
				 echo form_input($data_batch_number); ?></td> 
			<td><?php

			$data_voucher_number = array(
				 'name'        => 'voucher_number[]', 'id'=>'voucher_number'
				 );
				 echo form_input($data_voucher_number); ?></td>
				 <td><input type="button" class="add button" value="+"> <input type="button" class="remove button" value="-"></td>
		</tr>

		<tr>
			<td align="center" colspan=8><input name="submit" type="submit"
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
