<div class="quick_menu">
<a class="quick_menu_link" href="<?php echo site_url("vaccine_management");?>">&lt; &lt; Listing</a>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		$('#tray_color').ColorPicker({
			onSubmit : function(hsb, hex, rgb, el) {
				$(el).val(hex);
				$(el).ColorPickerHide();
			},
			onBeforeShow : function() {
				$(this).ColorPickerSetColor(this.value);
			}
		});
	});

</script>
<?php
if (isset($vaccine)) {
	$name = $vaccine -> Name;
	$designation = $vaccine -> Designation;
	$formulation_id = $vaccine -> Formulation;
	$administration_id = $vaccine -> Administration;
	$presentation = $vaccine -> Presentation;
	$vaccine_packed_volume = $vaccine -> Vaccine_Packed_Volume;
	$diluents_packed_volume = $vaccine -> Diluents_Packed_Volume;
	$vaccine_vial_price = $vaccine -> Vaccine_Vial_Price;
	$vaccine_dose_price = $vaccine -> Vaccine_Dose_Price;
	$doses_required = $vaccine -> Doses_Required;
	$wastage_factor = $vaccine -> Wastage_Factor;
	$tray_color = $vaccine -> Tray_Color;
	$vaccine_id = $vaccine -> id;
	$fridge_compartment = $vaccine -> Fridge_Compartment;
} else {
	$name = "";
	$designation = "";
	$formulation_id = "";
	$administration_id = "";
	$presentation = "";
	$vaccine_packed_volume = "";
	$diluents_packed_volume = "";
	$vaccine_vial_price = "";
	$vaccine_dose_price = "";
	$doses_required = "";
	$wastage_factor = "";
	$tray_color = "";
	$vaccine_id = "";
	$fridge_compartment = "";

}

$attributes = array('enctype' => 'multipart/form-data');
echo form_open('vaccine_management/save_vaccine', $attributes);
echo validation_errors('
<p class="error">', '</p>
');
?>
<input type="hidden" name="vaccine_id" value = "<?php echo $vaccine_id;?>"/>
<table border="0" class="data-table">

	<tr>
		<th class="subsection-title" colspan="2">Vaccine Information</th>

	</tr>
	<tbody>
		<tr>
			<td colspan="4"><em>Enter required details below:-</em></td>
		</tr>
		<tr>
			<td><span class="mandatory">*</span> Vaccine Name</td>
			<td><?php

			$data_name = array('name' => 'name', 'value' => $name);
			echo form_input($data_name);
 ?></td>
		</tr>
		
		
		<tr>
			<td> Doses Required</td>
			<td><?php

			$data_doses_required = array('name' => 'doses_required', 'value' => $doses_required);
			echo form_input($data_doses_required);
 ?></td>
		</tr>
		
		
		<tr>
			<td><span class="mandatory">*</span> Wastage Factor</td>
			<td><?php

			$data_wastage_factor = array('name' => 'wastage_factor', 'value' => $wastage_factor);
			echo form_input($data_wastage_factor);
 ?></td>
		</tr>
		<tr>
			<td><span class="mandatory">*</span> Tray Color</td>
			<td><?php

			$data_tray_color = array('name' => 'tray_color', 'id' => 'tray_color', 'value' => $tray_color);
			echo form_input($data_tray_color);
 ?></td>
		</tr>
		
		
		
		<tr>
			<td> Vaccine Designation</td>
			<td><?php

			$data_designation = array('name' => 'designation', 'value' => $designation);
			echo form_input($data_designation);
 ?></td>
		</tr>
		<tr>
			<td> Vaccine Formulation</td>
			<td>
			<select name="formulation">
				<option>None Selected</option>
				<?php
foreach($formulations as $formulation){
				?>
				<option value="<?php echo $formulation->id?>" <?php
				if ($formulation -> id == $formulation_id) {echo "selected";
				}
				?> ><?php echo $formulation -> Name;?></option>
				<?php }?>
			</select></td>
		</tr>
		
				<tr>
			<td> Mode of Administration</td>
			<td>
			<select name="administration">
				<option>None Selected</option>
				<?php
foreach($administration as $mode){
				?>
				<option value="<?php echo $mode->id?>" <?php
				if ($mode -> id == $administration_id) {echo "selected";
				}
				?> ><?php echo $mode -> Name;?></option>
				<?php }?>
			</select></td>
		</tr>
		
		 
		<tr>
			<td> Vaccine Presentation
			(Doses/Vial)</td>
			<td><?php

			$data_presentation = array('name' => 'presentation');
			echo form_input($data_presentation);
 ?></td>
		</tr>
				<tr>
			<td> Fridge Compartment</td>
			<td>
			<select name="fridge_compartment">
				<option>None Selected</option>
				<?php
foreach($fridge_compartments as $fridge_compartment_object){
				?>
				<option value="<?php echo $fridge_compartment_object->id?>" <?php
				if ($fridge_compartment_object -> id == $fridge_compartment) {echo "selected";
				}
				?> ><?php echo $fridge_compartment_object -> Name;?></option>
				<?php }?>
			</select></td>
		</tr>
		<tr>
			<td> Vaccine Packed Volume (cm3/dose)</td>
			<td><?php

			$data_vaccine_packed_volume = array('name' => 'vaccine_packed_volume', 'value' => $vaccine_packed_volume);
			echo form_input($data_vaccine_packed_volume);
 ?></td>
		</tr>
		<tr>
			<td> Diluents Packed Volume
			(cm3/dose)</td>
			<td><?php

			$data_diluents_packed_volume = array('name' => 'diluents_packed_volume', 'value' => $diluents_packed_volume);
			echo form_input($data_diluents_packed_volume);
 ?></td>
		</tr>
		<tr>
			<tr>
				<td>Vaccine Price ($USD/Vial)</td>
				<td><?php

				$data_vaccine_vial_price = array('name' => 'vaccine_vial_price', 'value' => $vaccine_vial_price);
				echo form_input($data_vaccine_vial_price);
 ?></td>
			</tr>
			<tr>
				<td>Vaccine Price ($USD/Dose)</td>
				<td><?php

				$data_vaccine_dose_price = array('name' => 'vaccine_dose_price', 'value' => $vaccine_dose_price);
				echo form_input($data_vaccine_dose_price);
 ?></td>
			</tr>
			<tr>
				<td align="center" colspan=2><input name="submit" type="submit"
					class="button" value="Save Vaccine Information"> <input
					name="reset" type="reset" class="button" value="Reset Fields"></td>
			</tr>
	
	</tbody>
</table>
<?php echo form_close();?>