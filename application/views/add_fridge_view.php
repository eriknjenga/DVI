<script type="text/javascript">
	$(document).ready(function() {
		$(".add").click(function() {
			var cloned_object = $('#populations tr:last').clone(true);
			cloned_object.insertAfter('#populations tr:last');
			return false;
		});
	});

</script>
<?php
if (isset($fridge)) {
	$item_type = $fridge -> Item_Type;
	$library_id = $fridge -> Library_Id;
	$pqs = $fridge -> PQS;
	$model_name = $fridge -> Model_Name;
	$manufacturer = $fridge -> Manufacturer;
	$power_source = $fridge -> Power_Source;
	$fridge_gas_type = $fridge -> Refrigerant_Gas_Type;
	$net_vol_4deg = $fridge -> Net_Vol_4deg;
	$net_vol_minus_20deg = $fridge -> Net_Vol_Minus_20deg;
	$freezing_capacity = $fridge -> Freezing_Capacity;
	$gross_vol_4deg = $fridge -> Gross_Vol_4deg;
	$gross_vol_minus_20deg = $fridge -> Gross_Vol_Minus_20deg;
	$price = $fridge -> Price;
	$elec_to_run = $fridge -> Elec_To_Run;
	$gas_to_run = $fridge -> Gas_To_Run;
	$kerosene_to_run = $fridge -> Kerosene_To_Run;
	$zone = $fridge -> Zone; 
	$fridge_id = $fridge -> id;
} else {
	$item_type = "";
	$library_id = "";
	$pqs = "";
	$model_name = "";
	$manufacturer = "";
	$power_source = "";
	$fridge_gas_type = "";
	$net_vol_4deg = "";
	$net_vol_minus_20deg = "";
	$freezing_capacity = "";
	$gross_vol_4deg = "";
	$gross_vol_minus_20deg = "";
	$price = "";
	$elec_to_run = "";
	$gas_to_run = "";
	$kerosene_to_run = "";
	$zone = "";
	$fridge_id = "";

}
$attributes = array('method'=>'post');
echo form_open('fridge_management/save', $attributes);
echo validation_errors('
<p class="error">', '</p>
');
?>
<input type="hidden" name="fridge_id" value = "<?php echo $fridge_id;?>"/>
<table border="0" class="data-table">
	<tr>
		<th class="subsection-title" colspan="2">Fridge Details</th>
	</tr>
	<tbody>
		<tr>
			<td> Item Type</td>
			<td>
			<select name="item_type">
				<?php
foreach($item_types as $type){
				?>
				<option value="<?php echo $type->id?>" <?php
				if ($type -> id == $item_type) {echo "selected";
				}
				?> ><?php echo $type -> Name;?></option>
				<?php }?>
			</select></td>
		</tr>
		<tr>
			<td> Library ID</td>
			<td><?php

			$data_library = array('name' => 'library_id', 'value' => $library_id);
			echo form_input($data_library);
			?></td>
		</tr>
		<tr>
			<td> PQS</td>
			<td><?php

			$data_pqs = array('name' => 'pqs', 'value' => $pqs);
			echo form_input($data_pqs);
			?></td>
		</tr>
		<tr>
			<td> Model Name</td>
			<td><?php

			$data_model = array('name' => 'model', 'value' => $model_name);
			echo form_input($data_model);
			?></td>
		</tr>
		<tr>
			<td> Manufacturer</td>
			<td><?php

			$data_manufacturer = array('name' => 'manufacturer', 'value' => $manufacturer);
			echo form_input($data_manufacturer);
			?></td>
		</tr>
		<tr>
			<td> Power Source</td>
			<td>
			<select name="power_source">
				<?php
foreach($power_sources as $source){
				?>
				<option value="<?php echo $source->id?>" <?php
				if ($source -> id == $power_source) {echo "selected";
				}
				?> ><?php echo $source -> Name;?></option>
				<?php }?>
			</select></td>
		</tr>
		<tr>
			<td> Refrigerant_Gas_Type</td>
			<td>
			<select name="gas_type">
				<?php
foreach($gas_types as $type){
				?>
				<option value="<?php echo $type->id?>" <?php
				if ($type -> id == $fridge_gas_type) {echo "selected";
				}
				?> ><?php echo $type -> Name;?></option>
				<?php }?>
			</select></td>
		</tr>
		<tr>
			<td> Net Volume (4 degrees)</td>
			<td><?php

			$data_net_vol_4deg = array('name' => 'net_vol_4deg', 'value' => $net_vol_4deg);
			echo form_input($data_net_vol_4deg);
			?></td>
		</tr>
		<tr>
			<td> Net Volume (-20 degrees)</td>
			<td><?php

			$data_net_vol_minus_20deg = array('name' => 'net_vol_minus_20deg', 'value' => $net_vol_minus_20deg);
			echo form_input($data_net_vol_minus_20deg);
			?></td>
		</tr>
		<tr>
			<td> Gross Volume (4 degrees)</td>
			<td><?php

			$data_gross_vol_4deg = array('name' => 'gross_vol_4deg', 'value' => $gross_vol_4deg);
			echo form_input($data_gross_vol_4deg);
			?></td>
		</tr>
		<tr>
			<td> Gross Volume (-20 degrees)</td>
			<td><?php

			$data_gross_vol_minus_20deg = array('name' => 'gross_vol_minus_20deg', 'value' => $gross_vol_minus_20deg);
			echo form_input($data_gross_vol_minus_20deg);
			?></td>
		</tr>
		<tr>
			<td> Freezing Capacity</td>
			<td><?php

			$data_freezing = array('name' => 'freezing_capacity', 'value' => $freezing_capacity);
			echo form_input($data_freezing);
			?></td>
		</tr>
		<tr>
			<td>Price</td>
			<td><?php

			$data_price = array('name' => 'price', 'value' => $price);
			echo form_input($data_price);
			?></td>
		</tr>
		<tr>
			<td>Electricity to Run</td>
			<td><?php

			$data_elec = array('name' => 'electricity', 'value' => $elec_to_run);
			echo form_input($data_elec);
			?></td>
		</tr>
		<tr>
			<td>Gas to Run</td>
			<td><?php

			$data_gas = array('name' => 'gas', 'value' => $gas_to_run);
			echo form_input($data_gas);
			?></td>
		</tr>
		<tr>
			<td>Kerosene to Run</td>
			<td><?php

			$data_kerosene = array('name' => 'kerosene', 'value' => $kerosene_to_run);
			echo form_input($data_kerosene);
			?></td>
		</tr>
		<tr>
			<td>Zone</td>
			<td>
			<select name="zone">
				<?php
foreach($zones as $working_zone){
				?>
				<option value="<?php echo $working_zone->id?>" <?php
				if ($working_zone -> id == $zone) {echo "selected";
				}
				?> ><?php echo $working_zone->Name
					?></option>
				<?php }?>
			</select></td>
		</tr>
			<tr>
		<td align="center" colspan=2>
		<input name="submit" type="submit"
		class="button" value="Save Details">
		</td>
	</tr>
	</tbody>
</table>
 
<?php echo form_close();?>