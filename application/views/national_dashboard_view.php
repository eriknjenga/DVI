<script>
	$(function() {
		$.tabs('#tabs a');
		$(".year_filter").change(function(){
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
			   load_consumption_graph(selected_year, $(this).attr("vaccine"));			
		});
	});
	function cleanup() {
		var vaccine = $("#tabs").find(".selected").attr("id");
		get_vaccine_graphs(vaccine);
	}

	function get_vaccine_graphs(vaccine) {
		//If we have already loaded the graphs for this vaccine, no need to do it again
		if($("#" + vaccine).attr("loaded") == "true") { 
			return false;
		}
		else{ 
			console.log("loading");
			//else, load all the pertinent graphs
			$("#" + vaccine).attr("loaded", "true");
			//get the vaccine id
			vaccine = vaccine.replace("vaccine_", '');
			var year = <?php echo date("Y")?>;
			//load the consumption graph
			load_consumption_graph(year,vaccine);
			load_months_of_stock_graph(vaccine);
			load_coverage_graph(vaccine);
		}


	}

	function load_consumption_graph(year,vaccine) {
		//start with the consumption graph
		var consumption_graph_id = "consumption_graph_container_" + vaccine;
		var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/Column3D.swf"?>", "ChartId", "450", "350", "0", "0");
		var url = '<?php echo base_url()."consumption_management/getNationalConsumption/"?>'+year+'/' + vaccine; 
		chart.setDataURL(url);
		chart.render(consumption_graph_id);

	}
	function load_months_of_stock_graph(vaccine) {
		//start with the consumption graph
		var graph_id = "months_of_stock_graph_container_" + vaccine;
		var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/Column3D.swf"?>", "ChartId", "850", "450", "0", "0");
		var url = '<?php echo base_url()."months_of_stock/getMonthsOfStock/"?>' + vaccine; 
		chart.setDataURL(url);
		chart.render(graph_id);

	}
	function load_coverage_graph(vaccine) {
		//start with the consumption graph
		var graph_id = "coverage_graph_container_" + vaccine;
		var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/Pie3D.swf"?>", "ChartId", "450", "350", "0", "0");
		var url = '<?php echo base_url()."coverage_management/getNationalCoverage/"?>' + vaccine; 
		chart.setDataURL(url);
		chart.render(graph_id);

	}
</script>
<style>
	.vaccine_container.hidden{ 
		    position: absolute;
    		left: -9999px;
	}
	.vaccine_container{
		display:block;
		  position: absolute;
		  width:90%;
	}
	#center_content{
		height:1000px;
	}
</style>

<?php
$this -> load -> view("vaccine_tabs");
?>
<a class="link" href="<?php echo base_url().'home_controller/dashboard'?>">View Tabular Statistics</a>
<?php
foreach($vaccines as $vaccine){
?> 
<div id="<?php echo $vaccine->id?>" class="vaccine_container">
	<div id = "months_of_stock_graph_container_<?php echo $vaccine -> id;?>" style="margin:0 auto; width:900px" title="Months of Stock Left for <?php echo $vaccine -> Name;?>" class="months_of_stock_graph"></div>
		<div  id="filter_<?php echo $vaccine->id;?>" style="margin:0 auto; width:200px;"> Select Filter Year:<select class="year_filter" vaccine = "<?php echo $vaccine->id;?>" >
		<?php
		//Display options for up to 5 year ago 
		$start_year = date('Y');
		$end_counter = $start_year-5;
		for($start_year; $start_year>=$end_counter;$start_year--){?>
			<option value="<?php echo $start_year;?>"><?php echo $start_year;?></option>
		<?php }
		?>
		</select></div>
		<div style="width:900px; margin:0 auto">
		<div id = "consumption_graph_container_<?php echo $vaccine -> id;?>" style="margin:0 auto; width:450px; float:left;" title="Monthly Consumption for <?php echo $vaccine -> Name;?>" class="months_of_stock_graph"></div>
		<div id = "coverage_graph_container_<?php echo $vaccine -> id;?>" style="margin:0 auto; width:450px; float:left;" title="Coverage for <?php echo $vaccine -> Name;?>" class="months_of_stock_graph"></div>
</div>
		

</div>
<?php }?>