<style type="text/css">
.chart_section{
border-top:2px solid #969696;
padding:10px;
width:650px;
margin:0 auto 0 auto;
}
#dashboard_menu{ 
font-size:14px;
width:90%;
margin:0 auto;
border-bottom: 1px solid #DDD;
overflow:hidden;
}

</style>
<div class="section_title"><?php echo $title;?></div>

<div id="dashboard_menu">
<a href="<?php echo site_url("Report_Management/view_report/store_summaries");?>" class="quick_menu_link <?php if($quick_link == "store_summaries"){echo "quick_menu_active";}?>">Store Summaries</a> 
<a href="<?php echo site_url("Report_Management/view_report/store_tallies");?>" class="quick_menu_link <?php if($quick_link == "store_tallies"){echo "quick_menu_active";}?>">Store Tallies</a>
<a href="<?php echo site_url("Report_Management/view_report/vaccine_movement");?>" class="quick_menu_link <?php if($quick_link == "vaccine_movement"){echo "quick_menu_active";}?>">Stock Movement</a> 
<a href="<?php echo site_url("Report_Management/manage_recipients");?>" class="quick_menu_link <?php if($quick_link == "manage_recipients"){echo "quick_menu_active";}?>">Email Recepients</a> 
</div>
<?php 
$this->load->view($report);
?>
