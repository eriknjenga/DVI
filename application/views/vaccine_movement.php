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
<form
	action="<?php echo base_url()."report_management/view_report/vaccine_movement"?>"
	method="post">
<fieldset><legend> Select Date Range</legend> <label for="from">Start
Date</label> <input type="text" id="from" name="from" /> <label for="to">End
Date</label> <input type="text" id="to" name="to" /> <input
	type="submit" name="vaccine_movement" class="button"
	value="View Vaccine Movement" /></fieldset>
</form>
</div>
<?php
if(isset($received)){



?>
<div>

<table border="0" class="data-table">
	<th class="subsection-title" colspan="11">Movement of Stock in the
	Period</th>
	<tr>
		<th>Vaccine</th>
		<th>Stock at Beginning of Period</th>
		<th>New Arrivals</th>
		<th>Total Issued</th>
		<th>Stock Balance</th>
	</tr>
	<?php
	foreach($vaccines as $vaccine){?>
	<tr>
		<td><?php echo $vaccine->Name;?></td>
		<td><?php echo $beginning_balance[$vaccine->id];?></td>
		<td><?php echo $received[$vaccine->id];?></td>
		<td><?php echo $issued[$vaccine->id];?></td>
		<td><?php echo $current_balance[$vaccine->id];?></td>
	</tr>
	<?php }
	?>

</table>

</div>
	<?php
}
?>