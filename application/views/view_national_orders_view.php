<style>
	.quick_menu {
		font-size: 14px;
		width: 90%;
		margin: 5px auto;
		overflow: hidden;
	}
	.quick_menu a {
		border-bottom: 1px solid #DDD;
	}

</style>
<div class="section_title">
	<?php echo $title;?>
</div>
<div class="quick_menu">
	<a class="quick_menu_link <?php
	if ($quick_link == "vaccine_orders") {echo "quick_menu_active";
	}
	?>" href="<?php echo site_url("orders_management/new_order");?>">Vaccine Orders</a>
	<a class="quick_menu_link <?php
	if ($quick_link == "other_orders") {echo "quick_menu_active";
	}
	?>" href="<?php echo site_url("orders_management/misc_order");?>">Misc. Orders</a>
</div>
<?php if (isset($pagination)):
?>
<div style="width:450px; margin:0 auto 60px auto">
	<?php echo $pagination;?>
</div>
<?php endif;?>
<table border="0" class="data-table">
	<th class="subsection-title" colspan="11">Vaccine Orders</th>
	<tr>
		<th>Origin</th>
		<th>Vaccine</th>
		<th>Qty. Requested</th>
		<th>Qty. Approved</th>
		<th>Pickup Date</th>
		<th>Made By</th>
		<th>Made On</th>
		<th>Accepted By</th>
		<th>Accepted On</th>
		<th>Action</th>
	</tr>
	<?php
foreach ($orders as $order) {
if(strlen($order->Region)>0){
$origin = $order->Origin_Region->name;
}
else if(strlen($order->District)>0){
$origin = $order->Origin_District->name;
}
	?>
	<tr style="color:<?php
	if ($order -> Approved == 0) {echo 'red';
	} else {echo 'green';
	}
	?>">
		<td><?php echo $origin;?></td>
		<td><?php echo $order -> Vaccine_Ordered -> Name;?></td>
		<td><?php echo $order -> Quantity;?></td>
		<td><?php echo $order -> Accepted_Quantity;?></td>
		<td><?php echo $order -> Pickup_Date;?></td>
		<td><?php echo $order -> Order_Maker -> Full_Name;?></td>
		<td><?php echo date("m/d/Y", $order -> Order_Made_On);?></td>
		<td><?php echo $order -> Order_Accepter -> Full_Name;?></td>
		<td><?php
		if (strlen($order -> Order_Accepted_On) > 0) {echo date("m/d/Y", $order -> Order_Accepted_On);
		}
		?></td>
		<td><a class="link" style="color:green" href="<?php echo base_url()."orders_management/approve/".$order->id?>">Approve</a></td>
	</tr>
	<?php }?>
</table>
<?php if (isset($pagination)):
?>
<div style="width:450px; margin:0 auto 60px auto">
	<?php echo $pagination;?>
</div>
<?php endif;?>