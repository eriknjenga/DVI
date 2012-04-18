<div class="quick_menu">
<a class="quick_menu_link" href="<?php echo site_url("region_management/add");?>">New Region</a>
</div>

<table border="0" class="data-table">
	<th class="subsection-title" colspan="11">Regions</th>
	<tr>
		<th>Name</th>  		
		<th>Latitude</th>
		<th>Longitude</th>
		<th>Disabled?</th> 
		<th>Action</th>
	</tr>
 <?php 
 foreach($regions as $region){?>
 <tr>
 <td>
 <?php echo $region->name;?>
 </td> 
    
  <td>
 <?php echo $region->latitude;?>
 </td>
  <td>
 <?php echo $region->longitude;?>
 </td>
  <td>
 <?php if($region->disabled == 0){echo "No";}else{echo "Yes";};?>
 </td>
 <td>
  <a href="<?php echo base_url()."region_management/edit_region/".$region->id?>" class="link">Edit </a>|
  <?php
  if($region->disabled == 0){?>
  	   <a class="link" style="color:red" href="<?php echo base_url()."region_management/change_availability/".$region->id."/1"?>">Disable</a> 
  <?php }
  else{?>
  	   <a class="link" style="color:green" href="<?php echo base_url()."region_management/change_availability/".$region->id."/0"?>">Enable</a> 
 <?php }
  ?>

 </td>
 </tr>
 
 <?php }
 ?>
	 
 

</table>  