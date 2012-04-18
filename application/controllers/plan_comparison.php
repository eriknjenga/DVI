<?php
class Plan_Comparison extends MY_Controller {
function __construct()
{
parent::__construct();

}

public function get($year,$vaccine)
{
$receipts = Batches::getYearlyReceipts($year,$vaccine);
$plans = Provisional_Plan::getYearlyPlan($year, $vaccine);
$month_margins = array(0,31,60,90,121,151,182,213,243,274,304,335,366); 
$chart =  '
<chart palette="2" caption="Provisional Plan" subcaption="For the year '.$year.'" xAxisName="Date" yAxisName="Quantity"  showValues="0" alternateHGridColor="FCB541" alternateHGridAlpha="20" divLineColor="FCB541" divLineAlpha="50" canvasBorderColor="666666" baseFontColor="666666" lineColor="FCB541" xAxisMaxValue="366" xAxisMinValue="0">
<categories verticalLineColor="666666" verticalLineThickness="1">
<category label="Jan" x="0" showVerticalLine="1"/>
<category label="Feb" x="31" showVerticalLine="1"/>
<category label="Mar" x="60" showVerticalLine="1"/>
<category label="Apr" x="90" showVerticalLine="1"/>
<category label="May" x="121" showVerticalLine="1"/>
<category label="Jun" x="151" showVerticalLine="1"/>
<category label="Jul" x="182" showVerticalLine="1"/>
<category label="Aug" x="213" showVerticalLine="1"/>
<category label="Sep" x="243" showVerticalLine="1"/>
<category label="Oct" x="274" showVerticalLine="1"/>
<category label="Nov" x="304" showVerticalLine="1"/>
<category label="Dec" x="335" showVerticalLine="1"/>
<category label="Jan" x="366" showVerticalLine="1"/>
</categories> 
<dataSet seriesName="Planned Arrivals" color="009900" anchorSides="3" anchorRadius="7" anchorBgColor="D5FFD5" anchorBorderColor="009900">';
foreach($plans as $plan){
	$date = $plan->expected_date;
	$quantity = $plan->expected_amount;
	$split_date = explode("/",$date);
	$month = $split_date[0]-1;
	$month_delimiter =  $month_margins[$month];
	$day = $split_date[1];
	$x_axis_value = $month_delimiter+$day;
	$chart.='<set y="'.$quantity.'" x="'.$x_axis_value.'" toolText="'.$quantity." Expected on ".$date.'"/>';
	
}
$chart.='
</dataSet>
 
<dataSet seriesName="Actual Arrivals" color="0000FF" anchorSides="7" anchorRadius="7" anchorBgColor="C6C6FF" anchorBorderColor="0000FF">';
foreach($receipts as $receipt){
	$date = $receipt->Arrival_Date;
	$quantity = $receipt->Total;
	$split_date = explode("/",$date);
	$month = $split_date[0]-1;
	$month_delimiter =  $month_margins[$month];
	$day = $split_date[1];
	$x_axis_value = $month_delimiter+$day;
	$chart.='<set y="'.$quantity.'" x="'.$x_axis_value.'" toolText="'.$quantity." (Batch No. ".$receipt->Batch_Number.") Arrived on ".$date.'"/>';
	
}
$chart.='
</dataSet>
<styles>
<definition>
<style name="Anim1" type="animation" param="_xscale" start="0" duration="1"/>
<style name="Anim2" type="animation" param="_alpha" start="0" duration="0.6"/>
<style name="DataShadow" type="Shadow" alpha="40"/>
</definition>
<application>
<apply toObject="DIVLINES" styles="Anim1"/>
<apply toObject="HGRID" styles="Anim2"/>
<apply toObject="DATALABELS" styles="DataShadow,Anim2"/>
</application>
</styles>
</chart>
';

echo $chart;
}
}