<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $title;?></title>
<link href="<?php echo base_url().'CSS/style.css'?>" type="text/css"
	rel="stylesheet" />
<link href="<?php echo base_url().'CSS/validator.css'?>" type="text/css"
	rel="stylesheet" />
<script src="<?php echo base_url().'Scripts/jquery.js'?>"
	type="text/javascript"></script>
<script src="<?php echo base_url().'Scripts/validationEngine-en.js'?>"
	type="text/javascript"></script>
<script src="<?php echo base_url().'Scripts/validator.js'?>"
	type="text/javascript"></script>

<style type="text/css" media="screen">
#logo {
	position: absolute;
	top: 0;
	left: 0;
	background:
		url("<?php echo base_url().'Images/coat_of_arms-resized.png'?>")
		no-repeat;
}

#center_content {
	width: 27%;
	border-left: 2px solid #DDD;
	border-bottom: 2px solid #DDD;
	border-right: 2px solid #DDD;
	min-height: 200px;
	margin: 0 0 0 10px;
	overflow: hidden;
	margin: 0 auto 0 auto;
}

#content {
	width: 100%;
}

.data-table {
	width: 95%;
	height: 95%;
}
tr{
height:40px;
}
</style>
<script type="text/javascript">
$(document).ready(function() {
    $("#login_form").validationEngine({ 
        inlineValidation: false,
         success :  function() { alert("Validation Success"); },
         failure : function() { alert("Validation Failure"); }
       });
   })

</script>
</head>

<body>
<div id="wrapper">
<div id="top-panel">
<div><a href="<?php echo base_url();?>" id="logo"></a> <span
	id="main_title">Division of Vaccines and Immunization (DVI) </span> <span
	id="sub_title">Kenyan Ministry of Public Health and Sanitation </span>
</div>
</div>
<div id="main_wrapper">

<div id="container">
<div id="content">
<div id="center_content">
<div class="section_title"><?php echo $title;?></div>

<?php
$attributes = array('enctype' => 'multipart/form-data',"class"=>"login_form","id"=>"login_form");
echo form_open('User_Management/login_submit',$attributes);
echo validation_errors('
<p class="error">','</p>
'); 
?>

<table border="0" class="data-table">
	<tbody>
		<tr>
			<td style="width: 40%"><span class="mandatory">*</span> Username</td>
			<td><?php

			$data_username = array(
				'name'        => 'username',
				'id'		=>'username',
				'style'=>'width:95%',
				'class'       =>  "validate[required,minSize[6],maxSize[32]]",
			);
			echo form_input($data_username); ?></td>
		</tr>
		<tr>
			<td style="width: 40%"><span class="mandatory">*</span> Password</td>
			<td><?php

			$data_password= array(
				 'name'        => 'password',
				'id'		=>'password',
				 'style'=>'width:95%',
				 'class'       =>  "validate[required,minSize[6],maxSize[32]]",
			);
			echo form_password($data_password); ?></td>
		</tr>

		<tr>
			<td align="center" colspan=2><input name="submit" type="submit"
				class="button" value="Login"> <input name="reset" type="reset"
				class="button" value="Reset Fields"></td>
		</tr>

	</tbody>
</table>
			<?php echo form_close();?></div>
<!-- end .content --></div>
<div id="footer">
<?php $this->load->view("footer");?>
</div>
<!-- end .container --></div>
<!--End Wrapper div--></div>

</body>
</html>
