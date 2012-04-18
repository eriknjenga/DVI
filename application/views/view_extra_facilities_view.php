<div class="section_title"><?php echo $title;?></div>
<div id="BCG">
<table border="0" class="data-table">
	<th class="subsection-title" colspan="11">Data on Vaccines</th>
	<tr>
		<th>Facility Name</th>
		<th>Facility Code</th>
		<th>Action</th>

	</tr>
	<?php 
	foreach($facilities as $facility){?>
		<tr>
		<td><?php echo $facility->Facilities->name?></td>
		<td><?php echo $facility->Facilities->facilitycode?></td>
		<td><a class="link" href="<?php echo site_url("facility_management/remove/".$facility->Facilities->facilitycode);?>" >Remove</a>
	 
		</td>
	</tr>
	<?php }
	?>

</table>
</div>