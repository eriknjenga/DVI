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
<style type="text/css" media="screen">
#logo{
background: url("<?php echo base_url().'Images/DVI_logo_resized.png'?>") no-repeat;

}
#logo2{
position:absolute;
top:0;
left:0;  
background: url("<?php echo base_url().'Images/coat_of_arms-resized.png'?>") no-repeat;
}
#alerts_panel_image{
background: url("<?php echo base_url().'Images/alert_resized.png'?>") no-repeat;
}
#notification_panel_image{
background: url("<?php echo base_url().'Images/Notification_Resized.png'?>") no-repeat;
}
</style>
</head>

<body>
<div id="wrapper">
<div style="margin:0 auto 0 auto;" id="logo"><a href="<?php echo base_url();?>" ></a></div> 
<div id="top-panel">
<div>
<a href="<?php echo base_url();?>" id="logo2"></a>
<span id="main_title">Division of Vaccines and Immunization (DVI) </span>
<span id="sub_title">Kenyan Ministry of Public Health and Sanitation </span>

</div>
<div id="logged_in">
<span class="login_details">Logged in as:</span><?php echo $this -> session -> userdata('full_name');?><br>
<?php
$user_levels = array("Administrator", "National Level", "Provincial Store", "District Store");
?>
<span class="login_details" style="color: #E01B4C">Access Level:</span><?php echo $this -> session -> userdata('user_group_name');?><br>
<span class="login_details">Date:</span><?php echo date("d-m-Y");?><br>
<a class="link" href="<?php echo site_url("user_management/change_password");?>"  >Change Password</a> -
<a class="link" href="<?php echo site_url("user_management/logout");?>"  >Logout</a>
</div>
</div>
<div id="main_wrapper">
<div id="level2">
<!-- 
<div id="alerts_panel">
<div id="alerts_panel_image"></div>
<div id="alerts_panel_text">
<ul>
<li>Alert Example (e.g. Polio Vaccine levels in Kiambu East are Low)</li>
</ul>
</div>

</div> 
-->
<div id="top_menu">

<div id="top_menu_container">
<a href="<?php echo site_url();?>" class="top_menu_link <?php
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

 
</div>
<style type="text/css">
#top_menu_container{
width:<?php echo $menu_counter * 135;?>px; 
margin-left:auto; 
margin-right:auto;
}

</style>

</div>

<?php
if($identifier != "general_user")
{
?>
<div id="quick_menu">
<span style="color: #555; text-align: center; font:12px; font-weight:bold; margin:5px">Quick Menu</span>
<div style="width:auto; margin-right:auto;">
<a  class="quick_menu_link <?php
if ($quick_link == "new_disbursement") {echo "quick_menu_active";
}
?>" href="<?php echo site_url("disbursement_management/new_disbursement");?>">Issue Vaccines</a>
<a  style="width:150px;" class="quick_menu_link <?php
if ($quick_link == "new_batch_disbursement") {echo "quick_menu_active";
}
?>" href="<?php echo site_url("disbursement_management/new_batch_disbursement");?>">Batch Disbursements</a>
<a  class="quick_menu_link <?php
if ($quick_link == "stock_count") {echo "quick_menu_active";
}
?>" href="<?php echo site_url("disbursement_management/stock_count");?>">Stock Count</a>

  <?php
  if($admin_national_only){?>
<a class="quick_menu_link <?php
if ($quick_link == "new_batch") {echo "quick_menu_active";
}
?>" href="<?php echo site_url("batch_management/new_batch");?>">Add New Stock</a> 
 <?php }?>
 

 <?php if($district_only){?>
<a href="<?php echo site_url("facility_management/new_facility");?>" class="quick_menu_link <?php
if ($quick_link == "new_facility") {echo "quick_menu_active";
}
?>">Add New Facility</a>
<a href="<?php echo site_url("facility_management/add");?>" class="quick_menu_link <?php
if ($quick_link == "new_extra_facility") {echo "quick_menu_active";
}
?>">Add Extra Facility</a>

 <?php }?>
  <?php 

  if($district_only || $region_only){?>
<a href="<?php echo site_url("disbursement_management/add_receipt");?>" class="quick_menu_link <?php
if ($quick_link == "new_receipt") {echo "quick_menu_active";
}
?>">Add Receivables</a>

 <?php }?>
 
</div>
<?php }?>

</div>




</div>

<div id="container">
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
