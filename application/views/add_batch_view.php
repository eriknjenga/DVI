<?php 
if(isset($batch)){ 
$id = $batch['id'];
$batch_number = $batch['Batch_Number'];
$expiry_date = $batch['Expiry_Date'];
$manufacturing_date = $batch['Manufacturing_Date'];
$manufacturer = $batch['Manufacturer'];
$lot_number = $batch['Lot_Number'];
$origin_country = $batch['Origin_Country'];
$arrival_date = $batch['Arrival_Date'];
$quantity = $batch['Quantity'];
$vaccine_id = $batch['Vaccine_Id'];
}
else{
$id = "";
$batch_number = "";
$expiry_date = "";
$manufacturing_date = "";
$manufacturer = "";
$lot_number = "";
$origin_country = "";
$arrival_date = "";
$quantity = "";
$vaccine_id = "";
}
$this->load->view('vaccine_tabs');
?>

<script type="text/javascript">
$(document).ready(function() {
	$("#add_batch_form").validationEngine();
	var default_datepicker_options = {"changeMonth": true, "changeYear": true};
	$( "#expiry_date" ).datepicker(default_datepicker_options);
	$( "#manufacturing_date" ).datepicker(default_datepicker_options);
	$( "#arrival_date" ).datepicker(default_datepicker_options);
	<?php if(isset($vaccine_id)){?>
	$("#vaccine_<?php echo $vaccine_id;?>").click();
	<?php }
	?>
	});
function cleanup(){
	$("#reset_vaccine_form").click();
}
</script>
<div class="section_title"><?php echo $title;?></div>
<?php
$attributes = array("method"=>"POST",'id'=>'add_batch_form');
if(isset($batch)){
echo form_open('batch_management/save_batch/'.$id,$attributes);
}
else{
echo form_open('batch_management/save_batch',$attributes);
}
echo validation_errors('
<p class="error">','</p>
'); 
?>

<table border="0" class="data-table">

	<tr>
		<th class="subsection-title" colspan="2">Batch Information for <span
			id="vaccine_name_label" style="color: #E01B4C;"></span></th>

	</tr>
	<tbody>
		<input type="hidden" name="vaccine_id" id="vaccine_id" />
		<tr>
			<td colspan="4"><em>Enter required details below:-</em></td>
		</tr>
		<tr>
			<td>Batch Number</td>
			<td><?php

			$data_batch_number = array(
				'name'        => 'batch_number', 'value' =>$batch_number
			);
			echo form_input($data_batch_number); ?></td>
		</tr>

		<tr>
			<td><span class="mandatory">*</span>Quantity Received</td>
			<td><?php

			$data_quantity = array(
				 'name'        => 'quantity','id'=>'quantity', 'value' =>$quantity,'class'=>'validate[required,custom[integer]]'
				 );
				 echo form_input($data_quantity); ?></td>
		</tr>
		<tr>
			<td>Arrival Date</td>
			<td><?php

			$data_arrival_date = array(
				 'name'        => 'arrival_date','id'=>"arrival_date", 'value' =>$arrival_date,'class'=>'validate[required]'
				 );
				 echo form_input($data_arrival_date); ?></td>
		</tr>
		
		<tr>
			<td>Expiry Date</td>
			<td><?php

			$data_expiry_date= array(
				 'name'        => 'expiry_date', 'id'=>'expiry_date', 'value' =>$expiry_date
				 );
				 echo form_input($data_expiry_date); ?></td>
		</tr>
		<tr>
			<td>Manufacturing Date</td>
			<td><?php

			$data_manufacturing_date = array(
				 'name'        => 'manufacturing_date', 'id'=>'manufacturing_date', 'value' =>$manufacturing_date
				 );
				 echo form_input($data_manufacturing_date); ?></td>
		</tr>
		<tr>
			<td>Manufacturer</td>
			<td><?php

			$data_manufacturer = array(
				 'name'        => 'manufacturer', 'value' =>$manufacturer
				 );
				 echo form_input($data_manufacturer); ?></td>
		</tr>
		<tr>
			<td>PO Number</td>
			<td><?php

			$data_lot_number = array(
				 'name'        => 'lot_number', 'value' =>$lot_number
				 );
				 echo form_input($data_lot_number); ?></td>
		</tr>
		<tr>
			<td>Country of Origin</td>
			<td><?php

			$data_origin_country = array(
				 'name'        => 'origin_country', 'value' =>$origin_country
				 );
				 echo form_input($data_origin_country); ?></td>
		</tr>



		<td align="center" colspan=2><input name="submit" type="submit"
			class="button" value="Save Batch Information"> <input name="reset"
			type="reset" class="button" value="Reset Fields"
			id="reset_vaccine_form"></td>
		</tr>

	</tbody>
</table>
				 <?php echo form_close();?>