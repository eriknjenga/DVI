<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Coverage_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

 

 
	public function getNationalCoverage($vaccine) {
		$months_of_stock = array();
		$year = date('Y');
		$now = date('U');
		$to = date("U", mktime(0, 0, 0, 1, 1, date("Y") + 1));
		$from = date("U", mktime(0, 0, 0, 1, 1, date('Y')));
		//Get National Data
		$population = regional_populations::getNationalPopulation($year);
		$population = str_replace(",", "", $population);
		$vaccine_object = Vaccines::getVaccine($vaccine);
		$yearly_requirement = ceil(($vaccine_object -> Doses_Required * $population * $vaccine_object -> Wastage_Factor));
		$stock_receipts =	Disbursements::getNationalReceiptsTotals($vaccine, $from, $to); 
		$percentage_coverage = ceil(($stock_receipts/$yearly_requirement)*100); 
		$chart = '
<chart caption="Estimated Coverage" palette="4" numberSuffix="%" decimals="0" enableSmartLabels="1" enableRotation="0"   bgAlpha="40,100" bgRatio="0,100" bgAngle="360" showBorder="0" startingAngle="70">
<set label="Covered" value="' . $percentage_coverage . '" isSliced="1"/>
<set label="Pending" value="' . (100-$percentage_coverage) . '"/>

</chart>';   
		echo $chart;
	}
	public function getRegionCoverage($vaccine) {
		$months_of_stock = array();
		$region = $this -> session -> userdata('district_province_id'); 
		$year = date('Y');
		$now = date('U');
		$to = date("U", mktime(0, 0, 0, 1, 1, date("Y") + 1));
		$from = date("U", mktime(0, 0, 0, 1, 1, date('Y')));
		//Get National Data
		$population = regional_populations::getRegionalPopulation($region,$year);
		$population = str_replace(",", "", $population);
		$vaccine_object = Vaccines::getVaccine($vaccine);
		$yearly_requirement = ceil(($vaccine_object -> Doses_Required * $population * $vaccine_object -> Wastage_Factor));
		$stock_receipts =	Disbursements::getRegionalReceiptsTotals($region,$vaccine, $from, $to); 
		$percentage_coverage = ceil(($stock_receipts/$yearly_requirement)*100); 
		$chart = '
<chart caption="Estimated Coverage" palette="4" numberSuffix="%" decimals="0" enableSmartLabels="1" enableRotation="0"   bgAlpha="40,100" bgRatio="0,100" bgAngle="360" showBorder="0" startingAngle="70">
<set label="Covered" value="' . $percentage_coverage . '" isSliced="1"/>
<set label="Pending" value="' . (100-$percentage_coverage) . '"/>

</chart>';   
		echo $chart;
	}

}
