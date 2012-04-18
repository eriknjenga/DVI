<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Consumption_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	public function getNationalConsumption($year, $vaccine) {
		$monthly_issues = array();
		for ($month = 1; $month <= 12; $month++) {
			$first_minute = mktime(0, 0, 0, $month, 1, $year);
			$last_minute = mktime(23, 59, 0, $month, date('t', $first_minute), $year);
			$monthly_issues[$month] = Disbursements::getNationalIssuesTotals($vaccine, $first_minute, $last_minute);

		}
		$chart = '
<chart caption="Monthly Vaccine Consumption for ' . $year . '" xAxisName="Month" yAxisName="Quantity" showValues="0" decimals="0" formatNumberScale="0">
<set label="Jan" value="' . $monthly_issues[1] . '"/>
<set label="Feb" value="' . $monthly_issues[2] . '"/>
<set label="Mar" value="' . $monthly_issues[3] . '"/>
<set label="Apr" value="' . $monthly_issues[4] . '"/>
<set label="May" value="' . $monthly_issues[5] . '"/>
<set label="Jun" value="' . $monthly_issues[6] . '"/>
<set label="Jul" value="' . $monthly_issues[7] . '"/>
<set label="Aug" value="' . $monthly_issues[8] . '"/>
<set label="Sep" value="' . $monthly_issues[9] . '"/>
<set label="Oct" value="' . $monthly_issues[10] . '"/>
<set label="Nov" value="' . $monthly_issues[11] . '"/>
<set label="Dec" value="' . $monthly_issues[12] . '"/>
</chart>
';

		echo $chart;
	}

	public function getRegionConsumption($year, $vaccine) {
		$monthly_issues = array();
		$region = $this -> session -> userdata('district_province_id'); 
		for ($month = 1; $month <= 12; $month++) {
			$first_minute = mktime(0, 0, 0, $month, 1, $year);
			$last_minute = mktime(23, 59, 0, $month, date('t', $first_minute), $year);
			$monthly_issues[$month] = Disbursements::getRegionalIssuesTotals($vaccine, $first_minute, $last_minute,$region);

		}
		$chart = '
<chart caption="Monthly Vaccine Consumption for ' . $year . '" xAxisName="Month" yAxisName="Quantity" showValues="0" decimals="0" formatNumberScale="0">
<set label="Jan" value="' . $monthly_issues[1] . '"/>
<set label="Feb" value="' . $monthly_issues[2] . '"/>
<set label="Mar" value="' . $monthly_issues[3] . '"/>
<set label="Apr" value="' . $monthly_issues[4] . '"/>
<set label="May" value="' . $monthly_issues[5] . '"/>
<set label="Jun" value="' . $monthly_issues[6] . '"/>
<set label="Jul" value="' . $monthly_issues[7] . '"/>
<set label="Aug" value="' . $monthly_issues[8] . '"/>
<set label="Sep" value="' . $monthly_issues[9] . '"/>
<set label="Oct" value="' . $monthly_issues[10] . '"/>
<set label="Nov" value="' . $monthly_issues[11] . '"/>
<set label="Dec" value="' . $monthly_issues[12] . '"/>
</chart>
';

		echo $chart;
	}

}
