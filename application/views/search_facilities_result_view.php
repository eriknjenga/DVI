<div class="section_title"><?php echo $title;?></div>
<div id="BCG">
<table border="0" class="data-table">
	<th class="subsection-title" colspan="11">Search Results for '<?php echo $search_term;?>'</th>
	<tr>
		<th>Facility Name</th>
		<th>Facility Code</th>

	</tr>
	<?php 
	foreach($facilities as $facility){?>
		<tr>
		<td>
		<a class="link" href="<?php echo site_url("facility_management/save/".$facility->facilitycode);?>" ><?php echo $facility->name?></a>
		</td>
		<td><?php echo $facility->facilitycode?></td>
		</td>
	</tr>
	<?php }
	?>

</table>
</div>