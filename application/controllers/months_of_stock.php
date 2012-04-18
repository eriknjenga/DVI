<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Months_Of_Stock extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	public function getMonthsOfStock($vaccine) {
		$months_of_stock = array();
		$year = date('Y');
		$now = date('U');
		//Get National Data
		$population = regional_populations::getNationalPopulation($year);
		$population = str_replace(",", "", $population);
		$vaccine_object = Vaccines::getVaccine($vaccine);
		$monthly_requirement = ceil(($vaccine_object -> Doses_Required * $population * $vaccine_object -> Wastage_Factor) / 12);
		$stock_balance = Disbursements::getNationalPeriodBalance($vaccine, $now);
		$months_left = $stock_balance / $monthly_requirement;
		$chart = '
<chart caption="Months of Stock Left" xAxisName="Month" yAxisName="Months" showValues="0" decimals="0" formatNumberScale="0">
<set label="CVS" value="' . $months_left . '"/>';

		//Get Regional Data
		$regions = Regions::getAllRegions();
		foreach ($regions as $region) {
			$population = regional_populations::getRegionalPopulation($region -> id, $year);
			$population = str_replace(",", "", $population);
			$monthly_requirement = ceil(($vaccine_object -> Doses_Required * $population * $vaccine_object -> Wastage_Factor) / 12);
			$stock_balance = Disbursements::getRegionalPeriodBalance($region -> id, $vaccine, $now);
			$months_of_stock = $stock_balance / $monthly_requirement;
			$chart .= '<set label="' . $region -> name . '" value="' . $months_of_stock . '"/>';
		}

		$chart .= '
</chart>
';

		echo $chart;
	}

}
