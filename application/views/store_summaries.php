<script type="text/javascript">
$(document).ready(function() {
$.tabs('#tabs a');
});
</script>
<div style="margin-top:30px;">
<div id="tabs" class="htabs">
<?php 
$months = array("Jan", "Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
$counter = 1;
foreach($months as $month){

?>
<a id="<?php echo $month ?>" tab="#month_<?php echo $counter; ?>" class="month_name"><?php echo $month;?></a> 
<?php 
$counter++;
}

?>
</div>
<?php 
for($month = 1; $month<=date('m'); $month++){
?>
<div id="month_<?php echo $month;?>">

<table border="0" class="data-table" style="margin:0 auto 0 auto;">
<tr>
<th rowspan="2">Store</th>
<th colspan = "<?php echo count($vaccines);?>">Vaccine</th>
</tr>
<tr>
<?php 
foreach($vaccines as $vaccine){?>
	<th style="background-color:<?php echo '#'.$vaccine->Tray_Color;?>; color:white;"><?php echo $vaccine->Name;?></th>
	 
<?php 
}
?>
</tr>
<tr>
<td>National Store</td>
<?php  
foreach($vaccines as $vaccine){

?>
<td><?php echo $national_values[$month][$vaccine->id]?></td>	
	 
<?php 
}
?>
</tr>

<?php 
foreach($regional_stores as $regional_store){?>
<tr>
<td>  <a class="link" href="<?php echo site_url('disbursement_management/drill_down/0/'.$regional_store->id);?>"> <?php echo $regional_store->name;?> </a>  </td>
<?php foreach($vaccines as $vaccine){?>
<td><?php echo $regional_values[$month][$regional_store->id][$vaccine->id]?></td>
<?php }
?>
</tr>
<?php }
?>
</table> 
</div>
<?php 
}

for($month = date('m')+1; $month<=12; $month++){
?>
<div id="month_<?php echo $month;?>">
No Data Available!
</div>
<?php }?>
</div>