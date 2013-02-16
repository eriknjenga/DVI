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
<a href="<?php echo site_url("Report_Management/view_report/consumption");?>" class="quick_menu_link <?php if($quick_link == "consumption"){echo "quick_menu_active";}?>">Consumption</a> 
<a href="<?php echo site_url("Report_Management/view_report/issues");?>" class="quick_menu_link <?php if($quick_link == "issues"){echo "quick_menu_active";}?>">Stock Issues</a>
<a style="width: 200px;" href="<?php echo site_url("Report_Management/view_report/district_stock_outs");?>" class="quick_menu_link <?php if($quick_link == "district_stock_out"){echo "quick_menu_active";}?>">District Stock Status Recipients</a>
</div>
<?php 
$this->load->view($report);
?>
