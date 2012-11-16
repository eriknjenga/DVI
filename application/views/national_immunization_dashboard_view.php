<script>
$(function() {
		var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/MSLine.swf"?>", "ChartId", "850", "450", "0", "0");
		var url = '<?php echo base_url()."vaccination_management/get_cummulative_graph"?>'; 
		chart.setDataURL(url);
		chart.render("immunization_graph_container");
		$("#filter_district").change(function() {
			var selected_district = $(this).attr("value");
				 $('#filter_facility').slideUp('slow', function() {
					    // Animation complete.
					  });
					$.each($("#filter_facility"), function(i, v) {
						$(this).children('option').remove();
						$(this).append($("<option value='0'>All Facilities</option>"));
					});
					$.ajax({
					  url: '<?php echo base_url()."facility_management/get_district_facilities/"?>'+selected_district,
					  success: function(data) {
					  	var json_data = jQuery.parseJSON(data);
					  	$.each(json_data, function() {
					  		var code = this['facility_code'];
					  		var facility_name = this['name']; 
					  		$("#filter_facility").append($("<option></option>").attr("value", code).text(facility_name));
					  	});
					   $('#filter_facility').slideDown('slow', function() {
					    // Animation complete.
					  });
					  	
					  }
					});
		});
		$("#filter_graph").click(function(){
			var vaccine_string = "";
				$(".antigens").each(function(index,item) { 
					if($(this).is(':checked')){
						var vaccine_id = $(this).attr("immunization");
				  		vaccine_string += vaccine_id+"-";
					}		 
				});
				var selected_year = $("#filter_year").find(":selected").attr("value");
				var selected_district = $("#filter_district").find(":selected").attr("value");
				var selected_facility = $("#filter_facility").find(":selected").attr("value");
				var selected_type = $("#filter_type").find(":selected").attr("value");
				var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/MSLine.swf"?>", "ChartId", "850", "450", "0", "0");	
				var url = '<?php echo base_url();?>vaccination_management/get_cummulative_graph/'+selected_year+'/'+vaccine_string+'/'+selected_district+'/'+selected_facility+'/'+selected_type; 
				chart.setDataURL(url);
				chart.render("immunization_graph_container");
		});
});
		
</script>
<style>
	#center_content {
		height: 1000px;
	}
	#mos_legend {
		width: 400px;
		height: 10px;
	}
	.graph {
		width: 460px;
		float: left;
		margin: 10px;
	}
	#graph_content {
		width: 100%;
		margin: 0 auto; 
	}
	.larger_graph {
		float: right;
	}
	.larger_graph_container {
		width: 950px;
		height: 500px;
	}
	.legend_content {
		width: 100%;
		overflow: hidden;
		margin-top: 5px;
	} 
	#graph_filter,#immunization_graph_container {
		width: 850px;
		margin: 0 auto;
		overflow: hidden;
	}
	#filter_facility{
		width:200px;
	}
</style>
<div id="graph_content">
	<div id="graph_filter">
		<b>Immunization(s)</b>
		</br>
		<table class="data-table">
			<tr>
				<th colspan="3">DTP-HepB+Hib</th>
				<th colspan="4">OPV</th>
				<th colspan="1">BCG</th>
			</tr>
			<tr>
				<td>
				<input type="checkbox" class="antigens" checked immunization="dpt1_admin"/>
				DPT 1 </td>
				<td>
				<input type="checkbox" class="antigens" immunization="dpt2_admin"/>
				DPT 2 </td>
				<td>
				<input type="checkbox" class="antigens" checked immunization="dpt3_admin"/>
				DPT 3 </td>
				<td>
				<input type="checkbox" class="antigens" immunization="opv1_admin"/>
				OPV 1 </td>
				<td>
				<input type="checkbox" class="antigens" immunization="opv2_admin"/>
				OPV 2 <td>
				<input type="checkbox" class="antigens" immunization="opv3_admin"/>
				OPV 3 <td>
				<input type="checkbox" class="antigens" immunization="opv_birth_admin"/>
				OPV Birth <td>
				<input type="checkbox" class="antigens" immunization="bcg_admin"/>
				BCG </td>
			</tr>
			<tr>
				<th colspan="3">PCV-10</th>
				<th colspan="2">Typhoid</th>
				<th colspan="1">Yellow Fever</th>
				<th colspan="1">Measles</th>
				<th colspan="1"> </th>
			</tr>
			<tr>
				<td>
				<input type="checkbox" class="antigens" immunization="pn1_admin"/>
				Pn. 1 </td>
				<td>
				<input type="checkbox" class="antigens" immunization="pn2_admin"/>
				Pn. 2</td>
				<td>
				<input type="checkbox" class="antigens" immunization="pn3_admin"/>
				Pn. 3</td>
				<td>
				<input type="checkbox" class="antigens" immunization="tt_pregnant"/>
				Preg.</td>
				<td>
				<input type="checkbox" class="antigens" immunization="tt_trauma"/>
				Trauma <td>
				<input type="checkbox" class="antigens" immunization="yellow_admin"/>
				YF</td>
				<td>
				<input type="checkbox" class="antigens" immunization="measles_admin"/>
				Measles </td>
				<td>  </td>
			</tr>
		</table>
		<br/>
		<b>District:</b>
		<select id="filter_district" style="width: 110px;">
			<option value="0">All Districts</option>
			<?php 
foreach($districts as $district){
		if(strlen($district['name'])>0){
			?>
			<option value="<?php echo $district['ID'];?>"><?php echo $district['name'];?></option>
			<?php 
			}
			}
			?>
		</select>
		<b>Facility:</b>
		<select id="filter_facility" style="width: 110px;">
			<option value="0">All Facilities</option>
		</select>
		<b>Graph Type:</b>
		<select id="filter_type" style="width: 110px;">
			<option value="0">Cummulative Immunizations</option>
			<option value="1">Month-on-Month Immunizations</option>
		</select>
		<b>Year:</b>
		<select id="filter_year">
			<?php
$year = date('y');
$counter = 0;
for($x=0;$x<=10;$x++){
			?>
			<option <?php
			if ($counter == 0) {echo "selected";
			}
			?> value="<?php echo $year;?>">'<?php echo $year;?></option>
			<?php
			$counter++;
			$year--;
			}
			?>
		</select>

		<input type="button" id="filter_graph" value="Filter Graph" class="button"/>
	</div>
	<div id = "immunization_graph_container"></div>
</div>