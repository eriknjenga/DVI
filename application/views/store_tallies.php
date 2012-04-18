<script type="text/javascript">
$(function() {
	var dates = $( "#from, #to" ).datepicker({
		defaultDate: "+1w",
		changeMonth: true,
		changeYear: true, 
		onSelect: function( selectedDate ) {
			var option = this.id == "from" ? "minDate" : "maxDate",
				instance = $( this ).data( "datepicker" ),
				date = $.datepicker.parseDate(
					instance.settings.dateFormat ||
					$.datepicker._defaults.dateFormat,
					selectedDate, instance.settings );
			dates.not( this ).datepicker( "option", option, date );
		}
	});
	$(".export_link").click(function(){
		var vaccine_name = $(this).attr("vaccine_name");
		var vaccine_id = $(this).attr("vaccine");
		var confirmation_text = "This action will export records in the time period specified for "+vaccine_name +" vaccine.\nAre you sure you want to continue?";
		var confirm_export = confirm(confirmation_text);
		
		
		if(confirm_export == true){
			var export_link = "<?php echo base_url()?>report_management/export_store_tallies/"+vaccine_id+"/"+vaccine_name;
			window.location = export_link;
		}
		else if(confirm_export == false){

		}
	});
});
function cleanup(){
	
}

</script>
<style type="text/css">
#filter { 
	display: block;
	width: 80%;
	margin: 10px auto;
}

.filter_input {
	border: 1px solid black;
}
</style>
<div id="filter">
<form action="<?php echo base_url()."report_management/view_report/store_tallies"?>" method="post">
<fieldset><legend> Select Date Range</legend> 
<label for="from">Start Date</label> 
<input type="text" id="from" name="from"/>
<label for="to">End Date</label> 
<input type="text" id="to" name="to"/>

<input type="submit" name="surveillance" class="button"	value="View Store Tallies" /> 
	</fieldset>
</form>
</div>
<div>
<?php 

if(isset($tallies)){
$this->load->view('vaccine_tabs'); 
foreach($vaccines as $vaccine){?>
<div id="<?php echo $vaccine->id?>">
<table border="0" class="data-table" id = "table_<?php echo $vaccine->id?>">
<a href="#" class="link export_link" style="margin-left:20px" vaccine="<?php echo $vaccine->id;?>" vaccine_name="<?php echo $vaccine->Name;?>">Export as Excell Sheet</a>
	<th class="subsection-title" colspan="11">Period Tally For <?php echo $vaccine->Name?>
	</th>
	<tr>
		<th>First Issue</th>
		<th>Issued to</th>
		<th>Total Ammount (Doses)</th>
	</tr>
<?php 
foreach($tallies[$vaccine->id] as $tally){ 
?>
<tr>
<td>
<?php echo $tally->Date_Issued;?>
</td>
<td>
<?php
if($tally->Issued_To_Region != null){?>
<a class="link" href="<?php echo site_url("disbursement_management/drill_down/0/".$tally->Issued_To_Region);?>"><?php echo $tally->Region_Issued_To->name?></a>
<?php }

else if($tally->Issued_To_District != null){?>
<a class="link" href="<?php echo site_url("disbursement_management/drill_down/1/".$tally->Issued_To_District);?>"><?php echo $tally->District_Issued_To->name?></a>
<?php }

 


?>
</td>
<td>
<?php echo $tally->Quantity;?>
</td>
</tr>
<?php }?>
</table>
</div>
<?php }
}
?>
</div>