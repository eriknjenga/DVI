<div class="quick_menu">
<a class="quick_menu_link" href="<?php echo site_url("district_management/add");?>">New District</a>
</div>
<?php if (isset($pagination)):
?>
<div style="width:450px; margin:0 auto 60px auto">
	<?php echo $pagination;?>
</div>
<?php endif;?>
<table border="0" class="data-table">
	<th class="subsection-title" colspan="11">Districts</th>
	<tr>
		<th>Name</th>
		<th>Province</th>
		<th>Latitude</th>
		<th>Longitude</th>
		<th>Disabled?</th>
		<th>Action</th>
	</tr>
	<?php
foreach($districts as $district){
	?>
	<tr>
		<td><?php echo $district -> name;?></td>
		<td><?php echo $district -> Province -> name;?></td>
		<td><?php echo $district -> latitude;?></td>
		<td><?php echo $district -> longitude;?></td>
		<td><?php
			if ($district -> disabled == 0) {echo "No";
			} else {echo "Yes";
			};
		?></td>
		<td><a href="<?php echo base_url()."district_management/edit_district/".$district->id?>" class="link">Edit </a>| <?php
if($district->disabled == 0){
		?>
		<a class="link" style="color:red" href="<?php echo base_url()."district_management/change_availability/".$district->id."/1"?>">Disable</a><?php }
			else{
		?>
		<a class="link" style="color:green" href="<?php echo base_url()."district_management/change_availability/".$district->id."/0"?>">Enable</a><?php }?></td>
	</tr>
	<?php }?>
</table>
<?php if (isset($pagination)):
?>
<div style="width:450px; margin:0 auto 60px auto">
	<?php echo $pagination;?>
</div>
<?php endif;?>