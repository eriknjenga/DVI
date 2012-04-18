<script type="text/javascript">
$(document).ready(function() {
 
	});
</script>
<div class="section_title"><?php echo $title;?></div>
<?php
$attributes = array('enctype' => 'multipart/form-data');
echo form_open('facility_management/search',$attributes);
echo validation_errors('
<p class="error">','</p>
'); 
?>

<table border="0" class="data-table">

	<tr>
		<th class="subsection-title" colspan="2">Search Facility</th>

	</tr>
	<tbody> 
		<tr>
			<td><span class="mandatory">*</span> Facility Name</td>
			<td><?php

			$data_search = array(
				'name'        => 'search',
			);
			echo form_input($data_search); ?></td>
		</tr>
	 
			<tr>
				<td align="center" colspan=2><input name="submit" type="submit"
					class="button" value="Search"> </td>
			</tr>
	
	</tbody>
</table>
<?php echo form_close();?>