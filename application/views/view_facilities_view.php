
<?php if (isset($pagination)): ?>
<div style="width:450px; margin:0 auto 60px auto">
<?php echo $pagination; ?>
</div>
<?php endif; ?>
<table border="0" class="data-table">
	<th class="subsection-title" colspan="11">Facilities</th>
	<tr>
		<th>Facility Code</th> 
		<th>Name</th>		
		<th>Type</th> 
		<th>District</th>  
	</tr>
 <?php  
 foreach($facilities as $facility){?>
 <tr>
 <td>
 <?php echo $facility->facilitycode;?>
 </td> 
   <td>
 <?php echo $facility->name;?>
 </td>
  <td>
 <?php echo $facility->Type->Name;?>
 </td>
   <td>
 <?php echo $facility->Parent_District->name;?>
 </td>
 </tr>
 
 <?php }
 ?>
	 
 

</table> 
<?php if (isset($pagination)): ?>
<div style="width:450px; margin:0 auto 60px auto">
<?php echo $pagination; ?>
</div>
<?php endif; ?>