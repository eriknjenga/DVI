<style>
	.quick_menu {
		font-size: 14px;
		width: 90%;
		margin: 5px auto;
		overflow: hidden;
	}
	.quick_menu a {
		border-bottom: 1px solid #DDD;
	}
</style>
<script>
	$(function() {
			$(".freezer_filter").change(function(){
			var selected_year = $(this).attr("value");
			//Get the last year of the dropdown list
			var last_year = $(this).children("option:last-child").attr("value");
			//If user has clicked on the last year element of the dropdown list, add 5 more
			if($(this).attr("value") == last_year){
				last_year--;
				var new_last_year = last_year - 5;
				for(last_year; last_year>=new_last_year;last_year--){
					var cloned_object = $(this).children("option:last-child").clone(true);
					cloned_object.attr("value", last_year);
					cloned_object.text(last_year);
					$(this).append(cloned_object); 
				}
			}
			//Refresh the line graph
var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/MSLine.swf"?>", "ChartId", "800", "450", "0", "0");
var url = '<?php echo base_url()."fridge_capacity_utilization/utilization/freezer/"?>'+selected_year;
chart.setDataURL(url);
chart.render('freezer_graph');
			
		});
					$(".fridge_filter").change(function(){
			var selected_year = $(this).attr("value");
			//Get the last year of the dropdown list
			var last_year = $(this).children("option:last-child").attr("value");
			//If user has clicked on the last year element of the dropdown list, add 5 more
			if($(this).attr("value") == last_year){
				last_year--;
				var new_last_year = last_year - 5;
				for(last_year; last_year>=new_last_year;last_year--){
					var cloned_object = $(this).children("option:last-child").clone(true);
					cloned_object.attr("value", last_year);
					cloned_object.text(last_year);
					$(this).append(cloned_object); 
				}
			}
			//Refresh the line graph
var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/MSLine.swf"?>", "ChartId", "800", "450", "0", "0");
var url = '<?php echo base_url()."fridge_capacity_utilization/utilization/fridge/"?>'+selected_year;
chart.setDataURL(url);
chart.render('fridge_graph');
			
		});
$("#freezer_graph_container").dialog({
autoOpen : false,
height : 550,
width : 850,
modal : true
});
$("#fridge_graph_container").dialog({
autoOpen : false,
height : 550,
width : 850,
modal : true
});

$("#freezer_utilization").click(function() {
var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/MSLine.swf"?>", "ChartId", "800", "450", "0", "0");
var url = '<?php echo base_url()."fridge_capacity_utilization/utilization/freezer"?>';
chart.setDataURL(url);
chart.render('freezer_graph');
$("#freezer_graph_container").dialog('open');
});
$("#fridge_utilization").click(function() { 
var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/MSLine.swf"?>
	", "ChartId", "800", "450", "0", "0");
	var url = '<?php echo base_url()."fridge_capacity_utilization/utilization/fridge"?>
	';
	chart.setDataURL(url);
	chart.render('fridge_graph');
	$("#fridge_graph_container").dialog('open');
	});
	});
</script>
<div class="section_title">
	<?php echo $title;?>
</div>
<div class="quick_menu">
	<a class="quick_menu_link" href="<?php echo site_url("fridge_management/new_equipment");?>">New Equipment</a>
	<a class="quick_menu_link" id="freezer_utilization" href="#">Freezer Usage</a>
	<a class="quick_menu_link" id="fridge_utilization" href="#">Fridge Usage</a>
</div>
<table border="0" class="data-table">
	<th class="subsection-title" colspan="11">Refrigeration Equipment Available at This Store</th>
	<tr>
		<th>Item Type</th>
		<th>Library Code</th>
		<th>PQS</th>
		<th>Model Name</th>
		<th>Manufacturer</th>
		<th>Power Source</th>
		<th>Refrigerant Gas Type</th>
		<th>Action</th>
	</tr>
	<?php
//create instances of the metrics being calculated
$total_net_volume_4deg = 0;
$total_net_volume_minus_20deg = 0;
$total_gross_volume_4deg = 0;
$total_gross_volume_minus_20deg = 0;
$total_elec_to_run = 0;
$total_gas_to_run = 0;
$total_kerosene_to_run = 0;

foreach($fridges as $fridge){
$total_net_volume_4deg += $fridge -> Fridge_Equipment -> Net_Vol_4deg;
$total_net_volume_minus_20deg += $fridge -> Fridge_Equipment -> Net_Vol_Minus_20deg;
$total_gross_volume_4deg += $fridge -> Fridge_Equipment -> Gross_Vol_4deg;
$total_gross_volume_minus_20deg += $fridge -> Fridge_Equipment -> Gross_Vol_Minus_20deg;
$total_elec_to_run += $fridge -> Fridge_Equipment -> Elec_To_Run;
$total_gas_to_run += $fridge -> Fridge_Equipment -> Gas_To_Run;
$total_kerosene_to_run += $fridge -> Fridge_Equipment -> Kerosene_To_Run;

	?>
	<tr>
		<td><?php echo $fridge -> Fridge_Equipment -> Type -> Name;?></td>
		<td><?php echo $fridge -> Fridge_Equipment -> Library_Id;?></td>
		<td><?php echo $fridge -> Fridge_Equipment -> PQS;?></td>
		<td><?php echo $fridge -> Fridge_Equipment -> Model_Name;?></td>
		<td><?php echo $fridge -> Fridge_Equipment -> Manufacturer;?></td>
		<td><?php echo $fridge -> Fridge_Equipment -> Power -> Name;?></td>
		<td><?php echo $fridge -> Fridge_Equipment -> Gas_Type -> Name;?></td>
		<td><a href="<?php echo base_url()."fridge_management/remove_equipment/".$fridge->id?>" class="link">Remove</a></td>
	</tr>
	<?php }?>
</table>
<table border="0" class="data-table">
	<th class="subsection-title" colspan="11">Refrigeration Metrics at This Store</th>
	<tr>
		<th>Metric</th>
		<th>Total Value</th>
	</tr>
	<tr>
		<td>Total Net Volume (4&deg;)</td>
		<td><?php echo $total_net_volume_4deg;?></td>
	</tr>
	<tr>
		<td>Total Net Volume (-20&deg;)</td>
		<td><?php echo $total_net_volume_minus_20deg;?></td>
	</tr>
	<tr>
		<td>Total Gross Volume (4&deg;)</td>
		<td><?php echo $total_gross_volume_4deg;?></td>
	</tr>
	<tr>
		<td>Total Gross Volume (-20&deg;)</td>
		<td><?php echo $total_gross_volume_minus_20deg;?></td>
	</tr>
	<tr>
		<td>Total Electricity Consumed</td>
		<td><?php echo $total_elec_to_run;?></td>
	</tr>
	<tr>
		<td>Total Gas Consumed</td>
		<td><?php echo $total_gas_to_run;?></td>
	</tr>
	<tr>
		<td>Total Kerosene Consumed</td>
		<td><?php echo $total_kerosene_to_run;?></td>
	</tr>
</table>
<div id="freezer_graph_container" title="Freezer Capacity Utilization">
	Select Filter Year:
	<select class="freezer_filter">
		<?php
//Display options for up to 5 year ago
$start_year = date('Y');
$end_counter = $start_year-5;
for($start_year; $start_year>=$end_counter;$start_year--){
		?>
		<option value="<?php echo $start_year;?>"><?php echo $start_year;?></option>
		<?php }?>
	</select>
	<div id="freezer_graph" title="Freezer Capacity Utilization"></div>
</div>
<div id="fridge_graph_container" title="Fridge Capacity Utilization">
	Select Filter Year:
	<select class="fridge_filter">
		<?php
//Display options for up to 5 year ago
$start_year = date('Y');
$end_counter = $start_year-5;
for($start_year; $start_year>=$end_counter;$start_year--){
		?>
		<option value="<?php echo $start_year;?>"><?php echo $start_year;?></option>
		<?php }?>
	</select>
	<div id="fridge_graph" title="Fridge Capacity Utilization"></div>
</div>
