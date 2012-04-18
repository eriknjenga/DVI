<style type="text/css">
.chart_section{
border-top:2px solid #969696;
padding:10px;
width:650px;
margin:0 auto 0 auto;
}
#dashboard_menu{
padding:10px;
margin:10px;
font-size:14px;
}

</style>
<div class="section_title"><?php echo $title;?></div>

<div id="dashboard_menu">
<a href="<?php echo site_url("Home_Controller/dashboard/country_stock_view");?>" class="link">Country Stock Snapshot</a> 
</div>
<?php 
$this->load->view($dashboard);
?>