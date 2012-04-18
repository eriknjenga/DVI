<div class="chart_section">
<label for="vaccine">Select Vaccine:</label>
<select name="vaccine" id="vaccine_select">
<option>BCG</option>
<option>Polio</option>
<option>Pneumococcal</option>
<option>Measles</option>
<option>Pentavalent</option>
<option>Yellow Fever</option>
<option>Rotavirus</option>
</select>
<label for="year">Year:</label>
<select name="year" id="vaccine_select">
<option>2005</option>
<option>2006</option>
<option>2007</option>
<option>2008</option>
<option>2009</option>
<option>2010</option>
<option>2011</option>
</select>
<input name="submit" type="submit" class="button" value="Filter Graph">
<div id="chart_area">

</div>

</div>

  <script type="text/javascript">
		   var chart = new FusionCharts("<?php echo site_url()?>Scripts/FusionCharts/MSCombiDY2D.swf", "ChartId", "600", "300", "0", "0");
		   chart.setDataURL("<?php echo site_url()?>XML/stock_levels.xml");
		   chart.render("chart_area");
		</script> 