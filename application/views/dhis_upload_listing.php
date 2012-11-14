<div class="quick_menu">
	<a class="quick_menu_link" href="<?php echo site_url("vaccination_management/upload");?>">Upload Data</a>
</div>
<table class="data-table">
	<thead>
		<tr>
			<th>Reporting Period</th>
			<th>Number of Records</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
		<?php 
		foreach($uploaded_data as $period_data){?>
			<tr>
				<td>
					<?php echo $period_data['reporting_period']?>
				</td>
				<td>
					<?php echo $period_data['total']?>
				</td>
				<td>
					<a class="link" href="<?php echo base_url().'vaccination_management/delete_data/'.$period_data['reporting_period'];?>">Delete Period Data</a>
				</td>
			</tr>
		<?php }
		?>
	</tbody>
</table> 