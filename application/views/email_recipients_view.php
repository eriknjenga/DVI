<?php ob_start();?>
<!-- <div class"section_title">  </div> -->
<div class="quick_menu">
	<a class="quick_menu_link" href="<?php echo base_url()."email_management/add_new"?>">Add Recepient</a>
</div>
<table border="0" class="data-table" >
	<th class="subsection-title" colspan="11">Emails and SMS List</th>
	<tr>
		<th>Recepient Name</th>
		<th>Email Address</th>
		<th>Mobile Number</th>
		<th>Stock Out</th>
		<th>Consumption</th>
		<th>Cold-Chain Capacity</th>
		<th>Action</th>
	</tr>
	<?php

foreach($emailsnsms as $data)
{
	?>
	<tr>
		<td><?php echo $data['recepient']
		?></td>
		<td><?php echo $data ['email']
		?></td>
		<td><?php echo $data ['number']
		?></td>
		<td align="center"><?php
		if ($data['stockout'] == '0') {
			echo "<span style='color:red'>No</span>";
		}
		if ($data['stockout'] == '1') {
			echo "<span style='color:green'>Yes</span>";
		}
		?>

		</td ><td align="center"><?php
		if ($data['consumption'] == '0') {
			echo "<span style='color:red'>No</span>";
		}
		if ($data['consumption'] == '1') {
			echo "<span style='color:green'>Yes</span>";
		}
		?></td>
		<td align="center"><?php
		if ($data['coldchain'] == '0') {
			echo "<span style='color:red'>No</span>";
		}
		if ($data['coldchain'] == '1') {
			echo "<span style='color:green'>Yes</span>";
		}
		?></td>
		<td><a  class="link" href="<?php echo base_url() . "email_management/edit_data/" . $data['id'];?>">Edit &nbsp;</a>| <?php

if($data['valid']== 1){
		?>
		<a class="link" style="color:red" href="<?php echo base_url() . "email_management/change_inavailability/" . $data['id'];?>">Disable</a><?php }
			else
			{
		?>
		<a class="link" style="color:green" href="<?php echo base_url() . "email_management/change_availability/" . $data['id'];?>">&nbsp;Enable</a><?php }?></td>
	</tr>
	<?php }?>
</table>
</div> 