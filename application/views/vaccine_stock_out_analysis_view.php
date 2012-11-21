<script>
	$(document).ready(function() { 
		if($('#myTable tr').length>1){
			 $("#myTable").tablesorter( {sortList: [[1,1]]} ); 
		}
		
		 $("#filter_graph").click(function(){
				var selected_year = $("#filter_year").find(":selected").attr("value");
				var selected_period = $("#filter_period").find(":selected").attr("value"); 
				var url = '<?php echo base_url();?>stock_out_analysis/analysis/'+selected_year+'/'+selected_period; 
				window.location = url;
		});
		$(".district_fetch").click(function(){
			var district = $(this).attr("district");
			var district_name = $(this).attr("district_name");
			var dialog_title = district_name+" District Facility Stock Outs"
			var selected_year = $("#filter_year").find(":selected").attr("value");
			var selected_period = $("#filter_period").find(":selected").attr("value"); 
			var url = '<?php echo base_url();?>stock_out_analysis/district_analysis/'+selected_year+'/'+selected_period+'/'+district;
			 $("#district_details").html("Loading data...");
			 $("#district_details").dialog({title: dialog_title});
			$( "#district_details" ).dialog('open');
			$.get(url, function(html) { 
	             // append the "ajax'd" data to the table body 
	             $("#district_details").html(html); 
	             	if($('#facility_table tr').length>1){
						 $("#facility_table").tablesorter( {sortList: [[1,1]]} ); 
					} 
       		 });  
			
		});
		$(".facility_details").live("click",function(){
			var facility = $(this).attr("facility_name"); 
			var dialog_title = facility+" Stock Summary"
			var selected_year = $("#filter_year").find(":selected").attr("value");
			var selected_period = $("#filter_period").find(":selected").attr("value"); 
			var url = '<?php echo base_url();?>stock_out_analysis/facility_analysis/';
			 $("#facility_details_dialog").html("Loading data...");
			 $("#facility_details_dialog").dialog({title: dialog_title});
			$( "#facility_details_dialog" ).dialog('open');
			$.post(url,{year:selected_year ,period:selected_period,facility_name:facility}, function(html) { 
	             // append the "ajax'd" data to the table body 
	             $("#facility_details_dialog").html(html);  
       		 });  
			
		});
		$( "#district_details" ).dialog({
            height: 600,
            width: 900,
            modal: true,
            autoOpen: false
        });
        $( "#facility_details_dialog" ).dialog({
            height: 300,
            width: 500,
            modal: true,
            autoOpen: false
        });
	});

</script>
<style>
 
	#graph_filter,#immunization_graph_container {
		width: 850px;
		margin: 0 auto;
		overflow: hidden;
	}
	#filter_facility{
		width:200px;
	}


</style>
	<div id="graph_filter"> 
		<b>Year:</b>
		<select id="filter_year">
			<?php
$year = date('Y');
$counter = 0;
for($x=0;$x<=10;$x++){
			?>
			<option <?php
			if ($selected_year == $year) {echo "selected";
			}
			?> value="<?php echo $year;?>"><?php echo $year;?></option>
			<?php
			$counter++;
			$year--;
			}
			?>
		</select>
		<b>Analysis Period:</b>
		<select id="filter_period">
			<?php  
for ($i = 1; $i <= 12; $i++) {
    $timestamp = mktime(0, 0, 0, $i, 1);?>
    <option  <?php
			if ($selected_month == $i) {echo "selected";
			}
			?> value="<?php echo  $i;?>"><?php echo date('F',$timestamp);?></option>
    <?php 
}
			?> 
		</select>

		<input type="button" id="filter_graph" value="Filter Analysis" class="button"/>
	</div>
<table id="myTable" class="data-table">
	<thead>
		<tr>
			<th>District</th>
			<?php 
				foreach($vaccines as $vaccine_object){?>
					<th><?php echo $vaccine_object->Name;?></th>
				<?php }
			?> 
		</tr>
	</thead>
	<tbody>
		<?php 
		//loop through all the districts
		foreach($district_details as $district_details_object){ ?>
				<tr>
					<td><a district = "<?php echo $district_details_object['district_id']; ?>" district_name = "<?php echo $district_details_object['district_name']; ?>" class="link district_fetch"><?php echo $district_details_object['district_name'];?></a></td>
					<?php 
					//loop through all vaccines to get wastage statistics for each
					foreach($vaccines as $vaccine_object){
						if(isset($district_details_object[$vaccine_object->id])){?>
							<td><?php echo $district_details_object[$vaccine_object->id];?></td>
						<?php }
						else{?>
							<td>-</td>
						<?php }
						
					}?>
				</tr>
				<?php }?> 
	</tbody>
</table>
<div id="district_details">
Loading details...	
</div>
<div id="facility_details_dialog">
Loading details...	
</div>