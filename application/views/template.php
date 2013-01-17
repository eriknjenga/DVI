<?php
if (!$this -> session -> userdata('user_id')) {
	redirect("User_Management");
}
if (!isset($link)) {
	$link = null;
}
if (!isset($quick_link)) {
	$quick_link = null;
}
$admin_national_only = false;
$district_only = false;
$region_only = false;
$identifier = $this -> session -> userdata('user_identifier');
if ($this -> session -> userdata('user_identifier') == 'national_officer') {
	$admin_national_only = true;
}
if ($this -> session -> userdata('user_identifier') == 'district_officer') {
	$district_only = true;
}
if ($this -> session -> userdata('user_identifier') == 'provincial_officer') {
	 
	$region_only = true;
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $title;?></title>
<link href="<?php echo base_url().'CSS/style.css'?>" type="text/css" rel="stylesheet"/>
<link href="<?php echo base_url().'CSS/pagination.css'?>" type="text/css" rel="stylesheet"/>
<link href="<?php echo base_url().'CSS/validator.css'?>" type="text/css" rel="stylesheet"/>
<script src="<?php echo base_url().'Scripts/jquery.js'?>" type="text/javascript"></script>
<script src="<?php echo base_url().'Scripts/validationEngine-en.js'?>" type="text/javascript"></script>
<script src="<?php echo base_url().'Scripts/validator.js'?>" type="text/javascript"></script>

<?php
if (isset($script_urls)) {
	foreach ($script_urls as $script_url) {
		echo "<script src=\"" . $script_url . "\" type=\"text/javascript\"></script>";
	}
}
?>

<?php
if (isset($scripts)) {
	foreach ($scripts as $script) {
		echo "<script src=\"" . base_url() . "Scripts/" . $script . "\" type=\"text/javascript\"></script>";
	}
}
?>


 
<?php
if (isset($styles)) {
	foreach ($styles as $style) {
		echo "<link href=\"" . base_url() . "CSS/" . $style . "\" type=\"text/css\" rel=\"stylesheet\"></link>";
	}
}
?>
<script type="text/javascript">
	$(document).ready(function() {
		$("#my_profile_link_container").hover(function() {
			var html = "<a href='<?php echo base_url().'user_management/change_password'?>' class='top_menu_link temp_link'>Change Password</a> <a href='<?php echo base_url().'user_management/logout'?>' class='top_menu_link temp_link'>Logout</a> ";
			$("#my_profile_link").css('display','none'); 
			$(this).append(html);
		}, function() {
			$("#my_profile_link").css('display','block');
			$(this).find(".temp_link").remove();
		});
	});

</script>
<style type="text/css" media="screen">
#logo{
background: url("<?php echo base_url().'Images/DVI_logo_resized.png'?>") no-repeat;

} 
	#my_profile_link_container .generated_link{
		display: none;
	}
	#my_profile_link{ 
		width: 150px !important;
		margin:0px !important;
		padding:0px !important;
	}
	#my_profile_link_continer{
		min-width: 150px !important;
		background-color: red;
		height:100px;
	}
	.temp_link{
		font-size: 10px;
		width:100px !important;
		background-color: #B80000;  
		margin:0px;
	}
</style>
</head>

<body>
<div id="wrapper"> 
<div id="top-panel"> 
		<div class="logo">
			<a class="logo" href="<?php echo base_url();?>" ></a> 
</div>

				<div id="system_title">
					<span style="display: block; font-weight: bold; font-size: 14px; margin:2px;">Ministry of Medical Services/Public Health and Sanitation</span>
					<span style="display: block; font-size: 12px;">Divison of Vaccines and Immunization</span>
				</div>
<div id="main_wrapper">
<div id="level2">
 
<div id="top_menu">

<div id="top_menu_container">
<a href="<?php echo site_url();?>" class="first_link top_menu_link <?php
if ($link == "home") {echo "top_menu_active";
}
?>">Home</a>
<?php
//This counter keeps track of how many top menus are being displayed and them change the width of the container accordingly. The default is 1 because of the default home menu
$menu_counter = 1;
//Retrieve all accessible menus from the session
$menus= $this -> session -> userdata('menus');
//Loop through all menus to display them in the top panel menu section
foreach($menus as $menu){?>
	<a href="<?php echo site_url($menu['menu_url']);?>" class="top_menu_link <?php
	if ($link == $menu['menu_url']) {echo "top_menu_active";
	}
?>"><?php echo $menu['menu_text']?></a>
	
<?php
		//increment the menu counter
		$menu_counter++;
		}
	?>

 <div id="my_profile_link_container" style="display: inline">
<a ref="#" class="top_menu_link" id="my_profile_link"><?php echo $this -> session -> userdata('full_name');?></a>
</div>
</div> 

</div>
</div>




</div>
</div>

<div id="container">
	<?php
if($identifier != "general_user")
{
?>
<div id="sub_menu">
<div style="width:auto; margin-right:auto;">
<a  class="top_menu_link sub_menu_link <?php
if ($quick_link == "new_disbursement") {echo "top_menu_active";
}
?>" href="<?php echo site_url("disbursement_management/new_batch_disbursement");?>">Issue Vaccines</a>

<a  class="top_menu_link sub_menu_link <?php
if ($quick_link == "stock_count") {echo "top_menu_active";
}
?>" href="<?php echo site_url("disbursement_management/stock_count");?>">Stock Count</a>

  <?php
  if($admin_national_only){?>
<a class="top_menu_link sub_menu_link <?php
if ($quick_link == "new_batch") {echo "top_menu_active";
}
?>" href="<?php echo site_url("batch_management/new_batch");?>">Stock Delivery</a> 
<a style="width: 200px !important;" class="last_link top_menu_link sub_menu_link <?php
if ($quick_link == "regional_statistics") {echo "top_menu_active";
}
?>" href="<?php echo site_url("regional_statistics");?>">Regional Statistics</a> 
  
 
 <?php }?>
 

 <?php if($district_only){?>
<a href="<?php echo site_url("facility_management/new_facility");?>" class="top_menu_link sub_menu_link <?php
if ($quick_link == "new_facility") {echo "top_menu_active";
}
?>">Add New Facility</a>
<a href="<?php echo site_url("facility_management/add");?>" class="top_menu_link sub_menu_link <?php
if ($quick_link == "new_extra_facility") {echo "top_menu_active";
}
?>">Add Extra Facility</a>

 <?php }?>
  <?php 

  if($district_only || $region_only){?>
<a href="<?php echo site_url("disbursement_management/add_receipt");?>" class="top_menu_link sub_menu_link <?php
if ($quick_link == "new_receipt") {echo "top_menu_active";
}
?>">Stock Delivery</a>

 <?php }?>
 
</div>
<?php }?>

</div>
  <div id="content">
<div id="center_content">

<?php $this -> load -> view($content_view);?>

</div>  
  <!-- end .content --></div>

  <!-- end .container --></div>

  <!--End Wrapper div--></div>
        <div id="footer">
  <?php $this -> load -> view("footer");?>
    </div>
</body>
</html>
