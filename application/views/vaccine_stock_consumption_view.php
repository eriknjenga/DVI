<script>
$(function() {
		$("#filter_graph").click(function(){
				var selected_year = $("#filter_year").find(":selected").attr("value");
				var selected_vaccine = $("#filter_vaccine").find(":selected").attr("value");
				var selected_start_month = $("#filter_start_month").find(":selected").attr("value");
				var selected_end_month = $("#filter_end_month").find(":selected").attr("value");  
				var url = '<?php echo base_url();?>vaccine_stock_consumption/ranking/'+selected_year+'/'+selected_vaccine+'/'+selected_start_month+'/'+selected_end_month; 
				window.location = url;
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
		<b>District:</b>
		<select id="filter_vaccine" style="width: 110px;"> 
			<?php 
foreach($vaccines as $vaccine){
			?>
			<option value="<?php echo $vaccine->id;?>"><?php echo $vaccine->Name;?></option>
			<?php 
			} 
			?>
		</select>
		<b>Year:</b>
		<select id="filter_year">
			<?php
$year = date('Y');
$counter = 0;
for($x=0;$x<=10;$x++){
			?>
			<option <?php
			if ($counter == 0) {echo "selected";
			}
			?> value="<?php echo $year;?>"><?php echo $year;?></option>
			<?php
			$counter++;
			$year--;
			}
			?>
		</select>
		<b>Start Month:</b>
		<select id="filter_start_month">
			<option value="1">January</option>
			<option value="2">February</option>
			<option value="3">March</option>
			<option value="4">April</option>
			<option value="5">May</option>
			<option value="6">June</option>
			<option value="7">July</option>
			<option value="8">August</option>
			<option value="9">September</option>
			<option value="10">October</option>
			<option value="11">November</option>
			<option value="12">December</option>	
		</select>
		<b>End Month:</b>
		<select id="filter_end_month">
			<option value="12">December</option>
			<option value="11">November</option>
			<option value="10">October</option>
			<option value="9">September</option>
			<option value="8">August</option>
			<option value="7">July</option>
			<option value="6">June</option>
			<option value="5">May</option>
			<option value="4">April</option>
			<option value="3">March</option>
			<option value="2">February</option>
			<option value="1">January</option>	
		</select>

		<input type="button" id="filter_graph" value="Filter Graph" class="button"/>
	</div>
	<div id = "results_container">
		<table class="data-table">
			<caption><?php echo $table_title;?></caption>
			<tr><th>District Name</th><th>Children Immunized</th><th>Doses Received</th></tr>
		
		<?php 
		$alt_checker = 0;
			foreach($district_details as $detail){
				
				?>
				<tr <?php if($alt_checker == 0){ $alt_checker = 1; echo "class='normal_row'";} else{{ $alt_checker = 0; echo "class='alternate_row'";}} ?>><td><?php echo $detail['name']; ?></td><td><?php echo $detail['total_administered']; ?></td><td><?php echo $detail['total_received']; ?></td></tr>
			<?php }
		?>
		</table>
	</div>
</div>