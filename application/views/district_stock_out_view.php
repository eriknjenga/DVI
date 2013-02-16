 <div class="quick_menu">
<a class="quick_menu_link" href="<?php echo site_url("district_stock_out/add_recipient");?>">New Recipient</a>
</div>
<?php if (isset($pagination)): ?>
<div style="width:450px; margin:0 auto 60px auto">
<?php echo $pagination; ?>
</div>
<?php endif; ?>
<table border="0" class="data-table">
	<th class="subsection-title" colspan="11">District Stock Status Recipients</th>
	<tr>
		<th>Recipient Name</th> 
		<th>Email Address</th>		
		<th>District Requested</th> 
		<th>Action</th>
	</tr>
 <?php 
 if(isset($users)){ 
 foreach($users as $user){?>
 <tr>
 <td>
 <?php echo $user->Full_Name;?>
 </td> 
   <td>
 <?php echo $user->Email;?>
 </td>
  <td>
 <?php echo $user->District_Object->name;?>
 </td>

 
 <td>
  <a href="<?php echo base_url()."district_stock_out/edit_recipient/".$user->id?>" class="link">Edit</a>|
  <?php
if($user->Disabled == 0){
		?>
		<a class="link" style="color:red" href="<?php echo base_url()."district_stock_out/change_availability/".$user->id."/1"?>">Disable</a><?php }
			else{
		?>
		<a class="link" style="color:green" href="<?php echo base_url()."district_stock_out/change_availability/".$user->id."/0"?>">Enable</a><?php }?>
		
 </td>
 </tr>
 
 <?php }
 }
 ?>
	 
 

</table> 
<?php if (isset($pagination)): ?>
<div style="width:450px; margin:0 auto 60px auto">
<?php echo $pagination; ?>
</div>
<?php endif; ?>