<script type="text/javascript">
var url = "";
$(function() {
	//Create Javascript Array for holding all autocomplete suggestions. They are not too many :-)
	var autocomplete_elements = Array();

//Loop through all the stores returned from the backend to generate your autocomplete suggestions
autocomplete_elements[0] = {value: "All Stores", id: "national_0"}
	<?php 
	$current_level = "";
			if(isset($districts)){
			//Create PHP arrays to traverse all districts and provinces
			
			$counter = 1;
			foreach($districts as $district){
			if($district['id'] == $this->session->userdata("district")){
				$current_level = $district['name'];
			}
			?>
			autocomplete_elements[<?php echo $counter;?>] = {value: "<?php echo $district['name'];?>", id: "district_<?php echo $district['id'];?>"}
			<?php 
			$counter++;
			}
			}
			if(isset($regions)){
			foreach($regions as $region){
			if($region['id'] == $this->session->userdata("region")){
				$current_level = $region['name'];
			}
			?>
			autocomplete_elements[<?php echo $counter;?>] = {value: "<?php echo $region['name'];?>", id: "region_<?php echo $region['id'];?>"}
			<?php 
			$counter++;
			}
			}
			if($current_level == ""){
			$current_level = "All Stores";
			}
			
	?>
	//Finish creating Autocomplete array
	//Create the jqueryui autocomplete object
	$( "#store" ).autocomplete({
	         	source: autocomplete_elements,
	         	select: function(event, ui) {
     			 var selected_id = ui.item.id; 
     			$( "#selected_store_id" ).attr("value",selected_id);  
     			 }
	         }); 
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

	$(".delete").click(function(){
		url = " <?php echo base_url().'disbursement_management/delete_disbursement/'?>" +$(this).attr("disbursement");
		$("#confirm_delete").dialog('open'); 
		});
	$(".export_link").click(function(){
		var vaccine_name = $(this).attr("vaccine_name");
		var vaccine_id = $(this).attr("vaccine");
		var confirmation_text = "This action will export all ledger entries in the time period specified for "+vaccine_name +" vaccine.\nAre you sure you want to continue?";
		var confirm_export = confirm(confirmation_text);
		
		
		if(confirm_export == true){
			var export_link = "<?php echo base_url()?>/disbursement_management/export/"+vaccine_id;
			window.location = export_link;
		}
		else if(confirm_export == false){

		}
	});
});

function delete_record(){
	window.location = url;
}
function cleanup(){
}
</script> 
<style type="text/css">
#current_filter{
font-weight: bold;
border:2px solid #DDD;
padding:5px;
font-size:12px;
}
</style>
<div title="Confirm Delete!" id="confirm_delete" style="width: 300px; height: 150px; margin: 5px auto 5px auto;">
Are you sure you want to delete this record?
</div>
	
<div class="section_title"><?php echo $title;?></div>
<div>

<div class="filter"> 
<div id="current_filter">
Currently Showing Records For: <?php echo $current_level;?> <a class="link" href = "<?php echo base_url()?>/disbursement_management/reset_filters">Reset Filters</a>
</div>
<fieldset>
<legend>Filter Options</legend>
<?php echo form_open('disbursement_management/view_disbursements');?>
<label for="from"><b>Select Store: </b></label>
<input type="text" id="store" name="store"/>
<input type="hidden" name="selected_store_id" id="selected_store_id"/>

<label for="from"><b>From</b></label>
<input type="text" id="from" name="from"/>
<label for="to"><b>to</b></label>
<input type="text" id="to" name="to"/>
</br>
<label for="order_by"><b>Order By</b></label>
<select name="order_by">
<option <?php if($this->session->userdata('order_by') == "Date_Issued_Timestamp"){echo "selected";}?> value="Date_Issued_Timestamp">Date Issued</option>  
</select>
<label for="order"><b>Order</b></label>
<select name="order">
<option <?php if($this->session->userdata('order') == "DESC"){echo "selected";}?> value="DESC">Descending</option> 
<option <?php if($this->session->userdata('order') == "ASC"){echo "selected";}?> value="ASC">Ascending</option>
</select>
<label for="per_page"><b>Records per Page</b></label>
<select name="per_page">
<option <?php if($this->session->userdata('per_page') == 10){echo "selected";}?> value="10">10</option> 
<option <?php if($this->session->userdata('per_page') == 20){echo "selected";}?> value="20">20</option>
<option <?php if($this->session->userdata('per_page') == 50){echo "selected";}?> value="50">50</option>
<option <?php if($this->session->userdata('per_page') == 70){echo "selected";}?> value="70">70</option>
<option <?php if($this->session->userdata('per_page') == 100){echo "selected";}?> value="100">100</option>
</select>
<input type="submit" class="button" name="submit" value="Filter Records"/>
</form>
</fieldset>

</div>


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
$archive_date = date('m')-1 ."/15/".date('Y');
$archive_timestamp = strtotime($archive_date);
		
foreach($vaccines as $vaccine){
?>

<div id="<?php echo $vaccine->id?>">



<table border="0" class="data-table" id = "table_<?php echo $vaccine->id?>">
<a href="#" class="link export_link" style="margin-left:20px" vaccine="<?php echo $vaccine->id;?>" vaccine_name="<?php echo $vaccine->Name;?>">Export as Excell Sheet</a>
	<th class="subsection-title" colspan="11">Vaccine Ledger For <?php echo $vaccine->Name?>
	</th>
	<tr>
		<th rowspan="2">Date</th>
		<th rowspan="2">Vaccines/Diluents </br>
		To/From</th>
		<th colspan="2">Ammount (Doses)</th>
		<th rowspan="2">Store Balance in Doses</th>
		<th rowspan="2">Receiving Store</br>Stock at Hand</th>
		<th rowspan="2">Voucher Number</th>
		<th colspan="2">Vaccine Information</th>		
		<th rowspan="2">Entered By</th>
		<th rowspan="2">Action</th>
	</tr>
	<tr>
		<th>Received</th>
		<th>Issued</th>
		<th>Lot/Batch No.</th>
		<th>Expiry Date</th>
	</tr>
	 <tr><td>Balance From Previous Period</td><td colspan="10" style="font-weight:bold"><?php echo $balances[$vaccine->id]?></td></tr>
	<?php
	$vaccine_totals = $balances[$vaccine->id]; 
	$vaccine_disbursements = $disbursements[$vaccine->id];
	if(count($vaccine_disbursements) == 0){?>
	<tr><td colspan="9">No Records Exist For This Vaccine</td></tr>
	<?php }
	else{

	foreach($vaccine_disbursements as $disbursement){
		$received = false;
		$store_identity = "";
		$date = $disbursement->Date_Issued;
		$timestamp = strtotime($date);
	?>
	<tr>

		<td><?php echo date("d/m/Y",strtotime($disbursement->Date_Issued));?></td>

		<?php
		//retrieve user identifier
		$identifier = $this->session->userdata('user_identifier');
		if($identifier == "national_officer"){
			$store_identity = "N0"; 
			if($disbursement->Issued_To_National == "0"){
			$received = true;
			?>
			<td>UNICEF (New Batch)</td>
			<td style="color: green"><?php $vaccine_totals +=$disbursement->Quantity; echo $disbursement->Quantity?></td>
			<td></td>
			<?php 
 
			}
			if($disbursement->Issued_By_National == "0"){
			if($disbursement->Issued_To_Region != null){?>
			<td><a class="link" href="<?php echo site_url("disbursement_management/drill_down/0/".$disbursement->Issued_To_Region);?>"><?php echo $disbursement->Region_Issued_To->name?></a></td>
			<td></td>
			<td style="color: red"><?php $vaccine_totals -=$disbursement->Quantity; echo $disbursement->Quantity?></td>
			<?php }
			else if( $disbursement->Issued_To_District != null){?>
			<td><a class="link" href="<?php echo site_url("disbursement_management/drill_down/1/".$disbursement->Issued_To_District);?>"><?php echo $disbursement->District_Issued_To->name?></a></td>
			<td></td>
			<td style="color: red"><?php $vaccine_totals -=$disbursement->Quantity; echo $disbursement->Quantity?></td>
			<?php }
			}		

		}
		else if($identifier == "provincial_officer"){
		$store_identity = "R".$this->session->userdata('district_province_id'); 
		if($disbursement->Issued_To_Region == $this->session->userdata('district_province_id')){
		if($disbursement->Issued_By_Region != null){
		$received = true;
		?>
		<td><?php echo $disbursement->Region_Issued_By->name?></td>
		<td style="color: green"><?php $vaccine_totals +=$disbursement->Quantity; echo $disbursement->Quantity?></td>
		<td></td>
		<?php }
		else if($disbursement->Issued_By_National == "0"){
		$received = true;
		?>
		<td>National Store</td>
		<td style="color: green"><?php $vaccine_totals +=$disbursement->Quantity; echo $disbursement->Quantity?></td>
		<td></td>
		<?php }
		else if($disbursement->Issued_By_District != null){
		$received = true;
		?>
		<td><?php echo $disbursement->District_Issued_By->name?></td>
		<td style="color: green"><?php $vaccine_totals +=$disbursement->Quantity; echo $disbursement->Quantity?></td>
		<td></td>
		<?php }
		}
		if($disbursement->Issued_By_Region == $this->session->userdata('district_province_id')){
		if($disbursement->Issued_To_Region != null){?>
		<td><a class="link" href="<?php echo site_url("disbursement_management/drill_down/0/".$disbursement->Issued_To_Region);?>"><?php echo $disbursement->Region_Issued_To->name?></a></td>
		<td></td>
		<td style="color: red"><?php $vaccine_totals -=$disbursement->Quantity; echo $disbursement->Quantity?></td>
		<?php }
		else if( $disbursement->Issued_To_District != null){?>
		<td><a class="link" href="<?php echo site_url("disbursement_management/drill_down/1/".$disbursement->Issued_To_District);?>"><?php echo $disbursement->District_Issued_To->name?></a></td>
		<td></td>
		<td style="color: red"><?php $vaccine_totals -=$disbursement->Quantity; echo $disbursement->Quantity?></td>
		<?php }
		}
		}
		else if($identifier== "district_officer"){
			$store_identity = "D".$this->session->userdata('district_province_id');
		if($disbursement->Issued_To_District == $this->session->userdata('district_province_id')){
		if($disbursement->Issued_By_Region != null){
		$received = true;
		?>
		<td><?php echo $disbursement->Region_Issued_By->name?></td>
		<td style="color: green"><?php $vaccine_totals +=$disbursement->Quantity; echo $disbursement->Quantity?></td>
		<td></td>
		<?php }
		else if($disbursement->Issued_By_National == "0"){
		$received = true;
		?>
		<td>National Store</td>
		<td style="color: green"><?php $vaccine_totals +=$disbursement->Quantity; echo $disbursement->Quantity?></td>
		<td></td>
		<?php }
		else if($disbursement->Issued_By_District != null){
		$received = true;
		?>
		<td><?php echo $disbursement->District_Issued_By->name?></td>
		<td style="color: green"><?php $vaccine_totals +=$disbursement->Quantity; echo $disbursement->Quantity?></td>
		<td></td>
		<?php }
		}
		if($disbursement->Issued_By_District == $this->session->userdata('district_province_id')){
		if( $disbursement->Issued_To_District != null){?>
		<td><a class="link" href="<?php echo site_url("disbursement_management/drill_down/1/".$disbursement->Issued_To_District);?>"><?php echo $disbursement->District_Issued_To->name?></a></td>
		<td></td>
		<td style="color: red"><?php $vaccine_totals -=$disbursement->Quantity; echo $disbursement->Quantity?></td>
		<?php }
		else if($disbursement->Issued_To_Facility != null){?>
		<td><?php echo $disbursement->Facility_Issued_To->name?></td>
		<td></td>
		<td style="color: red"><?php $vaccine_totals -=$disbursement->Quantity; echo $disbursement->Quantity?></td>
		<?php }
		}
		}
		if($disbursement->Issued_To_Region=='' && $disbursement->Issued_To_National=='' && $disbursement->Issued_To_District==''){?>
			<td>Physical Stock Count</td>
			<td>-</td>
			<td>-</td>
		<?php }
		?>
		<!--<td><?php echo $vaccine_totals;?></td>-->
		<td><?php echo $disbursement->Total_Stock_Balance;?></td>
		<td><?php echo $disbursement->Stock_At_Hand + $disbursement->Quantity;?></td>
		<td><?php echo $disbursement->Voucher_Number;?></td>
		<?php if($disbursement->Batch_Number != null){?>
		<td><?php echo $disbursement->Batch_Number?></td>
		<td><?php echo $disbursement->Batch->Expiry_Date?></td>
		<?php }
		else{?>
		<td>N/A</td>
		<td>N/A</td>
		<?php }
		?>


		
		<td><?php echo $disbursement->User->Full_Name?></td>
		<td>
		<?php 

		
		if($received && $disbursement->Owner == $store_identity && $disbursement->Batch_Id == ""){?>
		<a href="<?php echo base_url()."disbursement_management/add_receipt/".$disbursement->id?>" class="link">Edit</a> | 
		<a class="link delete" disbursement = "<?php echo $disbursement->id?>">Delete</a>
		<?php }
		else if($disbursement->Issued_To_Region=='' && $disbursement->Issued_To_National=='' && $disbursement->Issued_To_District==''){?>
		<a href="<?php echo base_url()."disbursement_management/stock_count/".$disbursement->id?>" class="link">Edit</a> | 
		<a class="link delete" disbursement = "<?php echo $disbursement->id?>">Delete</a>
		<?php }
		else if(!$received){?>
		<a href="<?php echo base_url()."disbursement_management/new_disbursement/".$disbursement->id?>" class="link">Edit</a> | 
		<a class="link delete" disbursement = "<?php echo $disbursement->id?>">Delete</a>
		<?php 
		}
		else{
			echo "None";
		}
		?>
		</td>
	</tr>
	<?php  

		 
	}	
	}
	?>



</table> 
<?php if (isset($pagination[$vaccine->id])): ?>
<div style="width:450px; margin:0 auto 60px auto">
<?php echo $pagination[$vaccine->id]; ?>
</div>
<?php endif; ?>
</div> 
	<?php

}
?>
</div>
