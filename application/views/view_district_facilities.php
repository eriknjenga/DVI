<div class="section_title"><?php echo $title;?></div>
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
		<th>Action</th>
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
 
 <td>
  <a href="<?php echo base_url()."facility_management/edit_facility/".$facility->id?>" class="link">Edit Details </a>
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