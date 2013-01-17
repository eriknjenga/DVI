  <script>
	$(function() {
			
		//start with the mos graph
		var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/MSColumn2D.swf"?>", "ChartId", "950", "400", "0", "0");
		var url = '<?php echo base_url()."months_of_stock/get_mos_balance/1/0/0"?>'; 
		chart.setDataURL(url);
		chart.render("mos_forecast");
		//then the cold chain graph
		var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/StackedBar2D.swf"?>", "ChartId", "950", "400", "0", "0");
		var url = '<?php echo base_url()."cold_chain/get_utilization/1/0/0"?>'; 
		chart.setDataURL(url);
		chart.render("cold_chain");
		$("#refresh_mos_graph").click(function(){ 
				var selected_region = $("#filter_mos_region").find(":selected").attr("value");
				var national = 0;
				if(selected_region == 0){
					national = 1;
				}
				//Modify the link that downloads more data
				var data_url = '<?php echo base_url();?>months_of_stock/download/'+national+'/'+selected_region+'/0'; 
				$("#mos_data_download").attr("href",data_url); 
				var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/MSColumn2D.swf"?>", "ChartId", "950", "400", "0", "0");	
				var url = '<?php echo base_url();?>months_of_stock/get_mos_balance/'+national+'/'+selected_region+'/0'; 
				chart.setDataURL(url);
				chart.render("mos_forecast");
		}); 
		$("#refresh_cold_chain_graph").click(function(){ 
				var selected_region = $("#filter_cold_chain_region").find(":selected").attr("value");
				var national = 0;
				if(selected_region == 0){
					national = 1;
				}
				//Modify the link that downloads more data
				var data_url = '<?php echo base_url();?>cold_chain/download/'+national+'/'+selected_region+'/0'; 
				$("#cold_chain_data_download").attr("href",data_url); 
				var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/StackedBar2D.swf"?>", "ChartId", "950", "400", "0", "0");	
				var url = '<?php echo base_url();?>cold_chain/get_utilization/'+national+'/'+selected_region+'/0'; 
				chart.setDataURL(url);
				chart.render("cold_chain");
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
		width:950px; 
		height: 400px;
		margin: 0 auto;
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
		<b>Region:</b>
		<select id="filter_mos_region" style="width: 110px;">
			<option value="0">Nationwide</option>
			<?php 
foreach($regions as $region){
		if(strlen($region->name)>0){
			?>
			<option value="<?php echo $region->id;?>"><?php echo $region->name;?></option>
			<?php 
			}
			}
			?>
		</select>
		<input type="button" id="refresh_mos_graph" value="Filter Graph" class="button"/>
	<a id="mos_data_download" class="link" href="<?php echo base_url().'months_of_stock/download/1/0/0';?>">Download Data</a>
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
<div class="graph">
<div class="larger_graph">
		<b>Region:</b>
		<select id="filter_cold_chain_region" style="width: 110px;">
			<option value="0">Nationwide</option>
			<?php 
foreach($regions as $region){
		if(strlen($region->name)>0){
			?>
			<option value="<?php echo $region->id;?>"><?php echo $region->name;?></option>
			<?php 
			}
			}
			?>
		</select>
		<input type="button" id="refresh_cold_chain_graph" value="Filter Graph" class="button"/>
	<a id="cold_chain_data_download" class="link" href="<?php echo base_url().'cold_chain/download/1/0/0';?>">Download Data</a>
</div>
<div id = "cold_chain" title="Cold Chain Utilization"  style="margin-top: 30px;"></div>
</div> 

</div>  