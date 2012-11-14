<div class="quick_menu">
	<a class="quick_menu_link" href="<?php echo site_url("vaccination_management/data_listing");?>">&lt;&lt; Available Data</a>
</div>
<?php echo form_open_multipart('vaccination_management/do_upload');?>
<table class="data-table">
	<tr>
		<td>
			File
		</td>
		<td>
			<input type="file" name="userfile" size="20"/>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<input type="submit" value="upload" class="button"/>
		</td>
	</tr>


</table>
</form>