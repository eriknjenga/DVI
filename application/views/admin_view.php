<style type="text/css">
.chart_section{
border-top:2px solid #969696;
padding:10px;
width:650px;
margin:0 auto 0 auto;
}
.dashboard_menu{ 
font-size:14px;
width:90%;
margin:0 auto;
border-bottom: 1px solid #DDD;
overflow:hidden;
}
.quick_menu{ 
font-size:14px;
width:90%;
margin:5px auto; 
overflow:hidden;
}
.quick_menu a{ 
border-bottom: 1px solid #DDD;
}

</style>
<div class="section_title"><?php echo $title;?></div>

<div class="dashboard_menu">
<a href="<?php echo site_url("vaccine_management");?>" class="quick_menu_link <?php if($quick_link == "vaccine_management"){echo "quick_menu_active";}?>">Vaccines</a> 
<a href="<?php echo site_url("user_management/listing");?>" class="quick_menu_link <?php if($quick_link == "user_management"){echo "quick_menu_active";}?>">Users</a> 
<a href="<?php echo site_url("district_management");?>" class="quick_menu_link <?php if($quick_link == "district_management"){echo "quick_menu_active";}?>">Districts</a>
<a href="<?php echo site_url("region_management");?>" class="quick_menu_link <?php if($quick_link == "region_management"){echo "quick_menu_active";}?>">Regions</a> 
<a href="<?php echo site_url("facility_management/whole_list");?>" class="quick_menu_link <?php if($quick_link == "facility_management"){echo "quick_menu_active";}?>">Facilities</a> 
<a href="<?php echo site_url("fridge_management/listing");?>" class="quick_menu_link <?php if($quick_link == "fridge_management"){echo "quick_menu_active";}?>">Fridges</a>
<a href="<?php echo site_url("vaccination_management/upload");?>" class="quick_menu_link <?php if($quick_link == "vaccination_management"){echo "quick_menu_active";}?>">DHIS Data Upload</a>  
 
</div>
<?php 
$this->load->view($module_view);
?>
