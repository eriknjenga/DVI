<script type="text/javascript">
$(document).ready(function() {
	});
function cleanup(){
	$("#reset_vaccine_form").click();
}
</script>
<div class="section_title"><?php echo $title;?></div>
<?php
$attributes = array('enctype' => 'multipart/form-data');
echo form_open('alert_management/save_alert',$attributes);
echo validation_errors('
<p class="error">','</p>
'); 
?>

<table border="0" class="data-table">
	<tbody>
		<tr>
			<td colspan="4"><em>Enter required details below:-</em></td>
		</tr>
		<tr>
			<td><span class="mandatory">*</span> Vaccine (If alert level exists it won't be shown)</td>
			
						<td><?php
			$options_vaccine = array(
                  '0'  => 'BCG',
                  '1'    => 'Polio',
                  '2'   => 'Pneumococcal',
                  '3' => 'Measles'
                  );
                  echo form_dropdown("vaccine",$options_vaccine); ?></td>
                  
                  
                  
		</tr>
		<tr>
			<td><span class="mandatory">*</span> Stock Level</td>
			<td><?php

			$data_stock_level= array(
				 'name'        => 'stock_level'
				 );
				 echo form_input($data_stock_level); ?></td>
		</tr>
		<tr>
			<td><span class="mandatory">*</span> Text Message Alert</br>(Enter Phone Numbers Separated by a Comma)</td>
				 				<td><?php
				$data_text_message = array(
				 'name'        => 'text_message',"cols"=>"20","rows"=>"4"
				 );
				 echo form_textarea($data_text_message); ?></td>
				 
				 
				 
		</tr>
		
			<tr>
				<td align="center" colspan=2><input name="submit" type="submit"
					class="button" value="Save Batch Information"> <input name="reset"
					type="reset" class="button" value="Reset Fields" id="reset_vaccine_form"></td>
			</tr>
	
	</tbody>
</table>
				 <?php echo form_close();?>