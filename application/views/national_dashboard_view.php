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
			height: 620,
			width: 950,
			modal: true,
			autoOpen: false
		});
		$("#mos_trend_larger_graph_container").dialog( {
			height: 620,
			width: 950,
			modal: true,
			autoOpen: false
		});
		$("#forecast_larger_graph_container").dialog( {
			height: 620,
			width: 950,
			modal: true,
			autoOpen: false
		});
		$("#recipients_larger_graph_container").dialog( {
			height: 620,
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
		var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionWidgets/Charts/Cylinder.swf"?>", "ChartId", "310", "300", "0", "0"); 
		var url = '<?php echo base_url()."cold_chain/get_national_fridge_occupancy/"?>'; 
		chart.setDataURL(url);
		chart.render("fridge_occupancy");
		//get the % occupied in the fridge
		var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionWidgets/Charts/Cylinder.swf"?>", "ChartId", "100", "300", "0", "0"); 
		var url = '<?php echo base_url()."cold_chain/get_national_freezer_occupancy/"?>'; 
		chart.setDataURL(url);
		chart.render("freezer_occupancy");
		//get the trend of mos for different antigens
		var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/MSLine.swf"?>", "ChartId", "400", "300", "0", "0"); 
		var url = '<?php echo base_url()."mos_trend/get/2/0/0/0"?>'; 
		chart.setDataURL(url);
		chart.render("mos_trend");
		//get the consumption vs. forecast for various vaccines
		var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/Column2D.swf"?>", "ChartId", "400", "300", "0", "0"); 
		var url = '<?php echo base_url()."consumption_forecast/national_forecast/0"?>'; 
		chart.setDataURL(url);
		chart.render("forecast");
		//get the distribution graph
		var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/Scatter.swf"?>", "ChartId", "400", "300", "0", "0"); 
		var url = '<?php echo base_url()."antigen_recipients/national_recipients/0/0/0"?>'; 
		chart.setDataURL(url);
		chart.render("stock_distribution");
		
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
				$("#larger_graph_container").dialog({title: "National Cold Chain Utilization"});
				$("#larger_graph_container").dialog("open");
				var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/StackedBar2D.swf"?>", "ChartId", "900", "450", "0", "0");	
				var url = '<?php echo base_url()."cold_chain/get_national_utilization/"?>'; 
				chart.setDataURL(url);
				chart.render("larger_graph_container");
			}
			if(id == "mos_trend_graph"){
				$("#mos_trend_larger_graph_container").dialog('option', 'title', 'Antigen MOS Balance Trend');
				$("#mos_trend_larger_graph_container").dialog("open");
				var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/MSLine.swf"?>", "ChartId", "900", "450", "0", "0");	
				var url = '<?php echo base_url()."mos_trend/get/2/0/0/0"?>'; 
				chart.setDataURL(url);
				chart.render("mos_trend_larger_graph");
			}
			if(id == "forecast_graph"){
				$("#forecast_larger_graph_container").dialog('option', 'title', 'Antigen Consumption Vs. Forecast');
				$("#forecast_larger_graph_container").dialog("open");
				var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/Column2D.swf"?>", "ChartId", "900", "450", "0", "0"); 
				var url = '<?php echo base_url()."consumption_forecast/national_forecast/0"?>'; 
				chart.setDataURL(url);
				chart.render("forecast_larger_graph");
			}
			if(id == "recipients_graph"){
				$("#recipients_larger_graph_container").dialog({title: "National Antigen Recipients Distribution"});
				$("#recipients_larger_graph_container").dialog("open");
				var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/Scatter.swf"?>", "ChartId", "900", "450", "0", "0"); 
				var url = '<?php echo base_url()."antigen_recipients/national_recipients/0/0/0"?>'; 
				chart.setDataURL(url);
				chart.render("recipients_larger_graph");
			}
		});
		$("#refresh_mos_trend_graph").click(function(){
			var vaccine_string = "";
				$(".antigens").each(function(index,item) { 
					if($(this).is(':checked')){
						var vaccine_id = $(this).attr("vaccine");
				  		vaccine_string += vaccine_id+"-";
					}		 
				});
				var selected_year = $("#mos_trend_year").find(":selected").attr("value");
				//Modify the link that downloads more data
				var data_url = '<?php echo base_url();?>mos_trend/download_national_mos_trend/'+selected_year;
				$("#trend_data_download").attr("href",data_url);
				$("#mos_trend_larger_graph_container").dialog('option', 'title', 'Antigen MOS Balance Trend');
				$("#mos_trend_larger_graph_container").dialog("open");
				var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/MSLine.swf"?>", "ChartId", "900", "450", "0", "0");	
				var url = '<?php echo base_url()?>mos_trend/get/2/0/'+vaccine_string+'/'+selected_year+''; 
				chart.setDataURL(url);
				chart.render("mos_trend_larger_graph");
		});
		$("#refresh_forecast_graph").click(function(){
				var selected_vaccine = $("#forecast_vaccine").find(":selected").attr("value");
				var selected_year = $("#forecast_year").find(":selected").attr("value");
				var data_url = '<?php echo base_url();?>consumption_forecast/download_national_forecast/'+selected_year;
				$("#forecast_data_download").attr("href",data_url);
				$("#forecast_larger_graph_container").dialog('option', 'title', 'Antigen Consumption Vs. Forecast');
				$("#forecast_larger_graph_container").dialog("open"); 
				var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/Column2D.swf"?>", "ChartId", "900", "450", "0", "0");  
				var url = '<?php echo base_url()?>consumption_forecast/national_forecast/'+selected_vaccine+'/'+selected_year; 
				chart.setDataURL(url);
				chart.render("forecast_larger_graph");
		});
		$("#refresh_recipients_graph").click(function(){
				var selected_vaccine = $("#issued_vaccine").find(":selected").attr("value");
				var selected_quarter = $("#issued_quarter").find(":selected").attr("value");
				var selected_year = $("#distribution_year").find(":selected").attr("value");
				var data_url = '<?php echo base_url();?>antigen_recipients/download_national_recipients/'+selected_year+'/'+selected_quarter;
				$("#recipients_data_download").attr("href",data_url);
				$("#recipients_larger_graph_container").dialog({title: "National Antigen Recipients Distribution"});
				$("#recipients_larger_graph_container").dialog("open");
				var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/Scatter.swf"?>", "ChartId", "900", "450", "0", "0"); 
				var url = '<?php echo base_url();?>antigen_recipients/national_recipients/'+selected_vaccine+'/'+selected_year+'/'+selected_quarter+''; 
				chart.setDataURL(url);
				chart.render("recipients_larger_graph");
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

<a class="link" style="margin-left:5px;" href="<?php echo site_url('home_controller/dashboard')?>">View Tabular Stock Status</a>
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
	<a class="link view_larger_graph" id="cold_chain_graph">Enlarge</a> | <a class="link" href="<?php echo base_url();?>cold_chain/download_national">Download Data</a>
</div>
<div id = "cold_chain" title="Cold Chain Utilization"  style="margin-top: 30px;"></div>
</div>
<div class="graph" style="width: 310px;"> 
<div id = "fridge_occupancy" title="Fridge Occupancy"></div>
</div>
<div class="graph" style="width: 100px; margin-top: -5px; margin-left: -10px;">
<div class="larger_graph">
<a class="link" href="<?php echo base_url();?>cold_chain/download_national">Download Data</a>
</div>
<div id = "freezer_occupancy" title="Fridge Occupancy"></div>
</div>
<div class="graph" style="width: 350px; margin-left: 50px;">
<div class="larger_graph" style="margin-top: -20px;">
	<a class="link view_larger_graph" id="mos_trend_graph">View More</a> | <a class="link" href="<?php echo base_url();?>mos_trend/download_national_mos_trend">Download Data</a>
</div>
<div id = "mos_trend" title="Antigen MOS Trend"  style="margin-top: 0px;"></div>
</div>
<div class="graph" style="width: 350px; margin-left: 50px;">
<div class="larger_graph" style="margin-top: -20px;">
	<a class="link view_larger_graph" id="forecast_graph">View More</a> | <a class="link" href="<?php echo base_url();?>consumption_forecast/download_national_forecast">Download Data</a>
</div>
<div id = "forecast" title="Antigen Consumption vs. Forecast"  style="margin-top: 0px;"></div>
</div>
<div class="graph" style="width: 350px; margin-left: 50px;">
<div class="larger_graph" style="margin-top: -20px;">
	<a class="link view_larger_graph" id="recipients_graph">View More</a> | <a class="link" href="<?php echo base_url();?>antigen_recipients/download_national_recipients">Download Data</a>
</div>
<div id = "stock_distribution" title="District Stock Distribution"  style="margin-top: 0px;"></div>
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
	<div style="float:left; padding:5px;"><b>Order Soon: </b> Vaccine will be below the safety level by next scheduled shipment. Order soon or reschedule shipment</div></div>
	<div class="legend_content">
	<div style="width:20px; height:20px; background-color: #3DE600;float:left"></div>
	<div style="float:left; padding:5px;"><b>Sufficient: </b> Vaccine will be above safety level at next shipment date. Monitor stock levels</div></div>
	<div class="legend_content">
	<div style="width:20px; height:20px; background-color: #B6E3E3;float:left"></div>
	<div style="float:left; padding:5px;"><b>Current Stock: </b> Current MoS held at the store</div></div>

</div>
</div>
<div id="mos_trend_larger_graph_container">
<div id="mos_trend_filter">
	<b>Antigen(s)</b><?php 
		$counter = 0;
		foreach($vaccines as $vaccine){ ?>
			<input type="checkbox" <?php if($counter == 0){echo "checked";}?> class="antigens" vaccine="<?php echo $vaccine->id;?>"/><?php echo $vaccine->Name;?>
			
		<?php 
		$counter++;
		}
	?>
	<br/><b>Analysis Year:</b> <select id="mos_trend_year">
	<?php  
		$year = date('Y');
		$counter = 0;
		for($x=0;$x<=10;$x++){ ?>
			<option <?php if($counter == 0){echo "selected";}?> value="<?php echo $year;?>"><?php echo $year;?></option>
			
		<?php 
		$counter++;
		$year--;
		}
	?>
	</select>
	<input type="button" id="refresh_mos_trend_graph" value="Filter Graph" class="button"/>
	<a class="link" id="trend_data_download" href="<?php echo base_url();?>mos_trend/download_national_mos_trend">Download Data</a>
</div>
<div id="mos_trend_larger_graph"></div>
</div>
<div id="forecast_larger_graph_container">
<div id="forecast_filter">
	<b>Antigen</b><select id="forecast_vaccine">
	<?php 
		$counter = 0;
		foreach($vaccines as $vaccine){ ?>
			<option <?php if($counter == 0){echo "selected";}?> value="<?php echo $vaccine->id;?>"><?php echo $vaccine->Name;?></option>
			
		<?php 
		$counter++;
		}
	?>
	</select>
	<b>Analysis Year:</b> <select id="forecast_year">
	<?php  
		$year = date('Y');
		$counter = 0;
		for($x=0;$x<=10;$x++){ ?>
			<option <?php if($counter == 0){echo "selected";}?> value="<?php echo $year;?>"><?php echo $year;?></option>
			
		<?php 
		$counter++;
		$year--;
		}
	?>
	</select>
	<input type="button" id="refresh_forecast_graph" value="Filter Graph" class="button"/>
	<a class="link" id="forecast_data_download" href="<?php echo base_url();?>antigen_recipients/download_national_recipients">Download Data</a>
</div>
<div id="forecast_larger_graph"></div>
</div>
<div id="recipients_larger_graph_container">
<div id="recipients_filter">
	<b>Antigen</b><select id="issued_vaccine">
	<?php 
		$counter = 0;
		foreach($vaccines as $vaccine){ ?>
			<option <?php if($counter == 0){echo "selected";}?> value="<?php echo $vaccine->id;?>"><?php echo $vaccine->Name;?></option>
			
		<?php 
		$counter++;
		}
	?>
	</select>
	<b>Period: </b><select id="issued_quarter">
			<option selected="" value="1">Jan - Mar</option>
			<option value="2">Apr - Jun</option>
			<option value="3">Jul - Sep</option>
			<option value="4">Oct - Dec</option>
	</select>
	<b>Analysis Year:</b> <select id="distribution_year">
	<?php  
		$year = date('Y');
		$counter = 0;
		for($x=0;$x<=10;$x++){ ?>
			<option <?php if($counter == 0){echo "selected";}?> value="<?php echo $year;?>"><?php echo $year;?></option>
			
		<?php 
		$counter++;
		$year--;
		}
	?>
	</select>
	<input type="button" id="refresh_recipients_graph" value="Filter Graph" class="button"/>
	<a class="link" id="recipients_data_download" href="<?php echo base_url();?>consumption_forecast/download_national_forecast">Download Data</a>
</div>
<div id="recipients_larger_graph"></div>
</div>
