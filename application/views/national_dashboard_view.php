<script>
	$(function() {
		//Create the dialog to contain the larger graph
		$("#larger_graph_container").dialog( {
			height: 500,
			width: 950,
			modal: true,
			autoOpen: false
			});
		$("#mos_larger_graph_container").dialog( {
			height: 600,
			width: 950,
			modal: true,
			autoOpen: false
			});
		//start with the mos graph
		var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/MSColumn2D.swf"?>", "ChartId", "460", "300", "0", "0");
		var url = '<?php echo base_url()."months_of_stock/get_national_mos_balance/"?>'; 
		chart.setDataURL(url);
		chart.render("mos_forecast");
		//then the cold chain graph
		var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/StackedBar2D.swf"?>", "ChartId", "370", "300", "0", "0");
		var url = '<?php echo base_url()."cold_chain/get_national_utilization/"?>'; 
		chart.setDataURL(url);
		chart.render("cold_chain");
		//get the % occupied in the fridge
		var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionWidgets/Charts/Cylinder.swf"?>", "ChartId", "400", "300", "0", "0");
		var url = '<?php echo base_url()."cold_chain/get_national_fridge_occupancy/"?>'; 
		chart.setDataURL(url);
		chart.render("fridge_occupancy");
		
		$(".view_larger_graph").click(function(){
			var id  = $(this).attr("id"); 
			
			if(id == "mos_graph"){
					$("#mos_larger_graph_container").dialog("open");
					var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/MSColumn2D.swf"?>", "ChartId", "900", "450", "0", "0");
					var url = '<?php echo base_url()."months_of_stock/get_national_mos_balance/"?>'; 
					chart.setDataURL(url);
					chart.render("mos_larger_graph");
			}
			if(id == "cold_chain_graph"){
				$("#larger_graph_container").dialog("open");
				var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/StackedBar2D.swf"?>", "ChartId", "900", "450", "0", "0");
				var url = '<?php echo base_url()."cold_chain/get_national_utilization/"?>'; 
				chart.setDataURL(url);
				chart.render("larger_graph_container");
			}
		});
	});
</script>
<style>
	#center_content{
		height:1000px;
	}
	#mos_legend{
		width:400px; 
		height: 10px; 
	}
	.graph{
		width:460px;
		float:left;
		margin: 10px;
	}
	.top_graphs_container{
		width:980px;
		margin: 0 auto;
	}
	.larger_graph{
		float: right;
	}
	.larger_graph_container{
		width:950px;
		height: 500px;
	}
	.legend_content{
		width:100%;
		overflow: hidden;
		margin-top: 5px;
	}
</style>


<div class="top_graphs_container">
<div class="graph">
<div class="larger_graph">
	<a class="link view_larger_graph" href="#" id="mos_graph">Enlarge</a> | <a class="link" href="<?php echo base_url();?>months_of_stock/download_national">Download Data</a>
</div>
<div id="mos_legend">
	<div style="width:20px; height:20px; background-color: #E60000;float:left"></div>
	<div style="float:left; padding:5px;">Order Now </div>
	<div style="width:20px; height:20px; background-color: #F6BD0F;float:left"></div>
	<div style="float:left; padding:5px;">Order Soon</div>
	<div style="width:20px; height:20px; background-color: #3DE600;float:left"></div>
	<div style="float:left; padding:5px;">Sufficient</div>
</div>
<div id = "mos_forecast" title="Months of Stock" ></div>
</div>
<div class="graph" style="width: 370px;">
<div class="larger_graph">
	<a class="link view_larger_graph" href="#" id="cold_chain_graph">Enlarge</a> | <a class="link" href="<?php echo base_url();?>cold_chain/download_national">Download Data</a>
</div>
<div id = "cold_chain" title="Cold Chain Utilization"  style="margin-top: 30px;"></div>
</div>
<div class="graph" style="width: 400px;">
<div class="larger_graph">
<a class="link" href="<?php echo base_url();?>cold_chain/download_national">Download Data</a>
</div>
<div id = "fridge_occupancy" title="Fridge Occupancy"></div>
</div>
</div>
<div id="larger_graph_container"></div>
<div id="mos_larger_graph_container">
<div id="mos_larger_graph"></div>
<div id="mos_detailed_legend">
	<div class="legend_content">
	<div style="width:20px; height:20px; background-color: #E60000;float:left"></div>
	<div style="float:left; padding:5px;"><b>Order Now: </b> Vaccine will not last till next scheduled shipment. Place order immediately to avoid stock out</div></div>
		<div class="legend_content">
	<div style="width:20px; height:20px; background-color: #F6BD0F;float:left"></div>
	<div style="float:left; padding:5px;"><b>Order Soon: </b> Vaccine will be below the safety level by next scheduled shipment. Order soon or reschedule shipment<div></div>
			<div class="legend_content">
	<div style="width:20px; height:20px; background-color: #3DE600;float:left"></div>
	<div style="float:left; padding:5px;"><b>Sufficient: </b> Vaccine will be above safety level at next shipment date. Monitor stock levels</div></div>

</div>
</div>
