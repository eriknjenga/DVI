<script type="text/javascript">
var url = "";
$(function() {
$("#confirm_delete").dialog( {
	height: 150,
	width: 300,
	modal: true,
	autoOpen: false,
	buttons: {
		"Delete Record": function() {
			delete_record();
		},
		Cancel: function() {
			$( this ).dialog( "close" );
		}
	}
	
	} );
			$(".view_plan_graph").click(function(){    
				var year = $(this).attr("current_year");
				var vaccine = $(this).attr("vaccine");
				showPlanGraph(year, vaccine);
				 //Retrieve the id of the drop down list containing years for this vaccine
			   var batch_years = "batch_years_"+$(this).attr("vaccine");
			   //Now display this drop down since it is initially hidden.
			   $("#"+batch_years).css("display","block");
			    var large_container = "plan_graph_div_"+vaccine;	
			   		$("#"+large_container).dialog( {
						height: 550,
						width: 850,
						modal: true
						} );

			});
			$(".filter_year").change(function(){
				
				var year = $(this).attr("value");
				var vaccine = $(this).attr("vaccine");
				showPlanGraph(year, vaccine);
			});
			

$(".delete").click(function(){
	url = " <?php echo 'batch_management/delete_batch/'?>" +$(this).attr("batch");
	$("#confirm_delete").dialog('open'); 
	});
});
function delete_record(){
	window.location = url;
}
function cleanup(){
}
function showPlanGraph(year, vaccine){
					//Retrieve the id of the div supposed to contain this graph
			   var graph_container = "graph_container_"+vaccine;
			  		  
			   var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/Scatter.swf"?>", "ChartId", "800", "450", "0", "0");
			   var url = '<?php echo base_url()."plan_comparison/get/"?>'+year+'/' + vaccine; 
			   chart.setDataURL(url);		   
			   chart.render(graph_container); 
}

</script>
<div title="Confirm Delete!" id="confirm_delete" style="width: 300px; height: 150px; margin: 5px auto 5px auto;">
Are you sure you want to delete this record?
</div>
<div class="section_title"><?php echo $title;?></div>
<?php
$this->load->view('vaccine_tabs');
//This code checks if the user is browsing through the ledger pages of a particular vaccine. If they are, automatically take them to the tab for that vaccine!
if(isset($paged_vaccine)){?>
<script type="text/javascript">
$(function() {
$("#vaccine_<?php echo $paged_vaccine;?>").click();
}); 
</script>
<?php }

foreach($vaccines as $vaccine){?>
<div id="<?php echo $vaccine->id?>">
	<a class="link" href="batch_management/provisional_plan/<?php echo $vaccine->id;?>" >Modify <?php echo date('Y');?> Provisional Plan For <?php echo $vaccine->Name;?> </a>
	<span style="font-size: 16px; margin:0 10px;">or</span>
	<a class="link view_plan_graph" vaccine = "<?php echo $vaccine->id;?>" current_year = "<?php echo date('Y');?>" >View How <?php echo $vaccine->Name;?> Receipts have gone according to plan </a>
<table border="0" class="data-table">
<th class="subsection-title" colspan="9">Received Stock For <?php echo $vaccine->Name?> </th>
	<tr>
		<th>Batch Number</th>
		<th>Expiry Date</th>
		<th>Manufacturing Date</th>
		<th>Manufacturer</th>
		<th>PO Number</th>
		<th>Arrival Date</th>
		<th>Quantity</th>
		<th>Added By</th>
		<th>Action</th>
	</tr>
<?php 
$vaccine_batches = $batches[$vaccine->id];;
foreach($vaccine_batches as $batch){?>
		<tr>
		<td><?php echo $batch->Batch_Number?></td>
		<td><?php echo $batch->Expiry_Date?></td>
		<td><?php echo $batch->Manufacturing_Date?></td>
		<td><?php echo $batch->Manufacturer?></td>
		<td><?php echo $batch->Lot_Number?></td>
		<td><?php if($batch->Arrival_Date != null){ echo  date("d/m/Y",strtotime($batch->Arrival_Date));} else{echo "None Provided";}?></td>
		<td><?php echo $batch->Quantity?></td>
		<td><?php echo $batch->User->Full_Name?></td>
		<td><a href="<?php echo base_url()."batch_management/edit_batch/".$batch->id?>" class="link">Edit</a>
		<a class="link delete" batch = "<?php echo $batch->id?>">Delete</a>
		</td>
	</tr>
<?php }
?>
</table>
<table border="0" class="data-table">
<th class="subsection-title" colspan="9"><?php echo date('Y');?> Provisional Plan For <?php echo $vaccine->Name?> </th>
	<tr>
		<th>Date</th>
		<th>Quantity</th>
	</tr>
<?php 
$plans = $vaccine_plans[$vaccine->id];
foreach($plans as $plan){?>
		<tr>
		<td><?php echo $plan->expected_date?></td>
		<td><?php echo $plan->expected_amount?></td>
 
	</tr>
<?php }
?>
</table>
<?php if (isset($pagination[$vaccine->id])): ?>
<div style="width:450px; margin:0 auto 60px auto">
<?php echo $pagination[$vaccine->id]; ?>
</div>
<?php endif; ?>
</div> 
<div id="plan_graph_div_<?php echo $vaccine->id;?>"  >
	<div  id="batch_years_<?php echo $vaccine->id;?>"  style ="display: none;">
		Select a <b>year</b> to filter your query: 
	<select class="filter_year" vaccine = "<?php echo $vaccine->id;?>"> 
		<?php 
		$years = $batch_years[$vaccine->id]; 
		foreach($years as $batch_year){?>
			<option value"<?php echo $batch_year->Year;?>"><?php echo $batch_year->Year;?></option>
		<?php }
		?>
		</select>
		</div>
	<div id = "graph_container_<?php echo $vaccine->id;?>" title="Plan Accordance for <?php echo $vaccine->Name;?>">
		</div>
	
</div>

<?php }
?>

 
