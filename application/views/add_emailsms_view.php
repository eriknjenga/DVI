

<a class="quick_menu_link" href="<?php echo base_url()."email_management"?>">&nbsp; Back </a>
<p>&nbsp;</p>
<script type="text/javascript">
	$(document).ready(function() {
		$('#tray_color').ColorPicker({
			onSubmit : function(hsb, hex, rgb, el) {
				$(el).val(hex);
				$(el).ColorPickerHide();
			},
			onBeforeShow : function() {
				$(this).ColorPickerSetColor(this.value);
			}
		});
	});

</script>


<?php
foreach ($emails as $data) {
}


$attributes = array('enctype' => 'multipart/form-data');
echo form_open('email_management/save_changes/'.$data['ID'], $attributes);
echo validation_errors('
<p class="error">', '</p>
');
?>
<input type="hidden" name="id" value = "<?php echo $IDS =$data['ID'];?>"/>

<p align="center" ><strong><span  class="mandatory">Please enter the required details below:-</span></strong></p>

<table border="0" class="data-table">
	<tr>
			<th colspan="2" style="background-color: #CCCCCC">Personal Information</th>
			</tr>
		
		<tr>
			<td style="width: 250px">Recepient Name</td>
			<td> 
				<?php $recepient = $data['recepient']; ?>
	 <input type="text" name="recepient" size="45" value="<?php echo $recepient ?>" required /> 
 </td>
		</tr>
		
		<tr>
			<th colspan="2">&nbsp;</th>
			</tr>
		
		<tr>
			<th colspan="2" style="background-color: #CCCCCC">Contact Notifications</th>
			</tr>
		
		<tr>
			<td> Email Address</td>
			<td><?php $email = $data['email'];?>
				 <input type="text" name="mail" size="45" value="<?php echo $email ?>"  pattern=".+@.+\.(com|edu|org|co.uk|co.ke|gov)"   required /> 
				
				
			</td>
		</tr>		
		
		<tr>
			<td><span class="mandatory"></span> Mobile Number</td>
			<td><?php $phone = $data['number'];?>
				  <input type="text" name="number" size="45" pattern="(254)\d{9}"  required  value="<?php echo $phone ?>" /> <strong></strong>
			</td>
	
			</tr>
			
			
		<tr>
			<th colspan="2">&nbsp;</th>
			</tr>
			
		<tr>
			<th colspan="2" style="background-color: #CCCCCC">SMS Notifications</th>
			</tr>
			
			
			
		<tr>
			<td>Stock Outs </td>
			<td>
			<select id="combo1"name="combo1">
	<?php $stock = $data['stockout'];?>				
				 
	<?php 
	if ($stock==1)
	{					 
  echo "<option value='1'>Enable</option>";
  echo"<option value='0'>Disable</option>";
	}
	if ($stock==0)
		 {
			
echo"<option value='0'>Disable</option>";		
	echo"<option value='1'>Enable</option>";
		}
  ?>
</select>
			</td>	
	
			</tr>
			
			<tr>
			<td>Consumption</td>
			<td>
			<select id="combo2" name="combo2">
				<?php $stock = $data['consumption'];?>				
				 
	<?php 
	if ($stock==1)
	{					 
  echo "<option value='1'>Enable</option>";
  echo"<option value='0'>Disable</option>";
	}
	if ($stock==0)
		 {
			
	echo"<option value='0'>Disable</option>";		
	echo"<option value='1'>Enable</option>";
		}
  ?>
</select>
			</td>	
	
			</tr>
			
			<tr>
			<td>Cold Chain Capacity</td>
			<td>
			<select id="combo3" name="combo3">
				<?php $stock = $data['coldchain'];?>				
				 
	
	<?php 
	if ($stock==1)
	{					 
  echo "<option value='1'>Enable</option>";
  echo"<option value='0'>Disable</option>";
	}
	if ($stock==0)
		 {
	echo"<option value='0'>Disable</option>";		
	echo"<option value='1'>Enable</option>";
	 
		}
  ?>
  
</select>
			</td>	
			</tr>
			
			<tr>
				<td align="center" colspan=2>
					<input  name="submit" type="submit"	class="button" style="width: 150px" value="Save Changes"> &nbsp;&nbsp;&nbsp;&nbsp;
					<input	name="reset" style="width: 100px" type="reset" class="button" value="Reset Fields"></td>
			</tr>
</table>
