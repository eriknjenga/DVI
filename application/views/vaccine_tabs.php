<script type="text/javascript">
$(document).ready(function() {


$(".vaccine_name").click(function (){
	$("#vaccine_name_label").html($(this).attr("name")); 
	$("#vaccine_id").attr("value",$(this).attr("id").substring(8,$(this).attr("id").length));
	cleanup();
	
	 
}
);
$.tabs('#tabs a');
});
</script>
<div id="tabs" class="htabs">
<?php 
foreach($vaccines as $vaccine){?>
<a id="vaccine_<?php echo $vaccine->id ?>" tab="#<?php echo $vaccine->id ?>" class="vaccine_name" name="<?php echo $vaccine->Name?>" style="background-color: <?php echo '#'.$vaccine->Tray_Color;?>"><?php echo $vaccine->Name?></a> 
<?php }

?>
</div>
