<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Months_Of_Stock extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	public function get_mos_balance($national = "", $region = "", $district = "") {
		$title = "";
		if ($national > 0 || $region == 0) {
			$title = "MOS Available at CVS Vs MoS Required to next Shipment";
		}
		if ($region > 0) {
			$region_object = Regions::getRegion($region);
			$title = "MOS Available at " . $region_object -> name . " Vs. MoS Needed to  Next Refill";
		}
		if ($district > 0) {
			$district_object = Districts::getDistrict($district);
			$title = "MOS Available at " . $district_object -> name . " District Store";
		}

		$vaccines = Vaccines::getAll_Minified();
		$date = date("m/d/Y");
		$months_required = array();
		$chart = '
<chart showLegend="0" bgColor="FFFFFF" showBorder="0" plotGradientColor="" showAlternateHGridColor="0" divLineAlpha="10" decimals="2" caption="' . $title . '" xAxisName="Antigen" yAxisName="Months of Stock" showValues="1" decimals="0" formatNumberScale="0" clickURL="' . base_url() . 'disbursement_management/drill_down/2/0">';
		$chart .= "<categories>";
		foreach ($vaccines as $vaccine_object) {
			$chart .= '<category label="' . $vaccine_object -> Name . '"/>';
		}
		$chart .= "</categories>";
		foreach ($vaccines as $vaccine_object) {
			$months_till_shipment = 0;
			//Check the level of the graph being generated
			if ($national > 0) {
				$months_of_stock = array();
				$year = date('Y');
				$now = date('U');
				$expected_delivery = Provisional_Plan::getNextDelivery($vaccine_object -> id);

				if (isset($expected_delivery[0])) {
					$days_till_shipment = $expected_delivery[0]['difference'];
					if (isset($days_till_shipment)) {
						$months_till_shipment = number_format(($days_till_shipment / 30), 1);
					}
				}
			}
			if ($region > 0) {
				$months_till_shipment = 2;
			}
			if ($district > 0) {
				$months_till_shipment = 2;
			}
			//echo $months_till_shipment;
			$months_required[$vaccine_object -> id] = $months_till_shipment;

		}
		$chart .= '<dataset seriesName="MOS" color="000000" showValues="1">';
		$year = date('Y');
		$population = 0;
		if ($national > 0) {
			$population = Regional_Populations::getNationalPopulation($year);
		}
		if ($region > 0) {
			$population = Regional_Populations::getRegionalPopulation($region, $year);
		}
		if ($district > 0) {
			$population = District_Populations::getDistrictPopulation($district, $year);
		}

		foreach ($vaccines as $vaccine_object) {
			$months_of_stock = array();
			$year = date('Y');
			$now = date('U');

			$stock_balance = 0;
			if ($national > 0) {
				$stock_balance = Disbursements::getNationalPeriodBalance($vaccine_object -> id, $now);
			}
			if ($region > 0) {
				$stock_balance = Disbursements::getRegionalPeriodBalance($region, $vaccine_object -> id, $now);
			}
			if ($district > 0) {
				$stock_balance = Disbursements::getDistrictPeriodBalance($district, $vaccine_object -> id, $now);
			}

			$population = str_replace(",", "", $population);
			$monthly_requirement = ceil(($vaccine_object -> Doses_Required * $population * $vaccine_object -> Wastage_Factor) / 12);
			$months_left = 0;
			if ($stock_balance > 0) {
				$months_left = number_format(($stock_balance / $monthly_requirement), 1);
			}

			$color = "";
			if ($months_required[$vaccine_object -> id] > $months_left) {
				$color = "E60000";
			}
			if ($months_required[$vaccine_object -> id] < $months_left) {
				$color = "3DE600";
			}
			if ($months_required[$vaccine_object -> id] == $months_left) {
				$color = "F6BD0F";
			}

			$chart .= '<set value="' . $months_left . '" color="' . $color . '"/>';
		}
		$chart .= "</dataset>";
		$chart .= '<dataset seriesName="MOS Needed" color="AFD8F8" showValues="1">';
		foreach ($vaccines as $vaccine_object) {
			$chart .= '<set value="' . $months_required[$vaccine_object -> id] . '"/>';

		}
		$chart .= "</dataset>";
		$chart .= '
</chart>
';

		echo $chart;
	}

	function download($national = "", $region = "", $district = "") {
		$title = "";
		if ($national > 0) {
			$title = "MOS Available at Central Vaccine Store";
		}
		if ($region > 0) {
			$region_object = Regions::getRegion($region);
			$title = "MOS Available at " . $region_object -> name;
		}
		if ($district > 0) {
			$district_object = Districts::getDistrict($district);
			$title = "MOS Available at " . $district_object -> name . " District Store";
		}
		$vaccines = Vaccines::getAll_Minified();
		$date = date("m/d/Y");
		$months_required = array();
		$data_buffer = "
			<style>
			table.data-table {
			table-layout: fixed;
			width: 700px;
			border-collapse:collapse;
			border:1px solid black;
			}
			table.data-table td, th {
			width: 100px;
			border: 1px solid black;
			}
			.leftie{
				text-align: left !important;
			}
			.right{
				text-align: right !important;
			}
			.center{
				text-align: center !important;
			}
			</style> 
			";
		$data_buffer .= "<table class='data-table'>";
		$data_buffer .= $this -> echoTitles();

		foreach ($vaccines as $vaccine_object) {
			$months_of_stock = array();
			$year = date('Y');
			$now = date('U');
			$population = 0;
			$stock_balance = 0;
			if ($national > 0) {
				$population = Regional_Populations::getNationalPopulation($year);
				$stock_balance = Disbursements::getNationalPeriodBalance($vaccine_object -> id, $now);
			}
			if ($region > 0) {
				$population = Regional_Populations::getRegionalPopulation($region, $year);
				$stock_balance = Disbursements::getRegionalPeriodBalance($region, $vaccine_object -> id, $now);
			}
			if ($district > 0) {
				$population = District_Populations::getDistrictPopulation($district, $year);
				$stock_balance = Disbursements::getDistrictPeriodBalance($district, $vaccine_object -> id, $now);
			}
			$population = str_replace(",", "", $population);
			$monthly_requirement = ceil(($vaccine_object -> Doses_Required * $population * $vaccine_object -> Wastage_Factor) / 12);
			$months_till_shipment = 0;
			if ($national > 0) {
				$months_of_stock = array();
				$year = date('Y');
				$now = date('U');
				$expected_delivery = Provisional_Plan::getNextDelivery($vaccine_object -> id);

				if (isset($expected_delivery[0])) {
					$next_shipment = $expected_delivery[0]['next_shipment'];
					$days_till_shipment = $expected_delivery[0]['difference'];
					if (isset($days_till_shipment)) {
						$months_till_shipment = number_format(($days_till_shipment / 30), 1);
					}
				} else {
					$months_till_shipment = 3;
					$next_shipment = "N/A";
				}
			}
			if ($region > 0) {
				$months_till_shipment = 2;
				$next_shipment = "N/A";
			}
			if ($district > 0) {
				$months_till_shipment = 2;
				$next_shipment = "N/A";
			}

			$doses_needed = "N/A";
			if ($stock_balance > 0) {
				$months_left = number_format(($stock_balance / $monthly_requirement), 1);
			}
			if ($months_left > $months_till_shipment) {
				$doses_needed = "None";
			} else {
				$doses_needed = number_format((($months_till_shipment - $months_left) * $monthly_requirement), 2);
			}

			$monthly_requirement = number_format($monthly_requirement + 0);
			$data_buffer .= "<tr><td class='leftie'>" . $vaccine_object -> Name . "</td><td class='right'>" . number_format($stock_balance) . "</td><td class='center'>" . $months_left . "</td><td class='center'>" . $next_shipment . "</td><td class='center'>" . $months_till_shipment . "</td><td class='right'>" . $monthly_requirement . "</td><td class='right'>" . $doses_needed . "</td></tr>";

		}
		$data_buffer .= "</table>";
		$this -> generatePDF($data_buffer, $title);
		//echo $data_buffer;
	}

	public function echoTitles() {
		return "<tr><th>Antigen</th><th>Current Stock Balance</th><th>MOS Available</th><th>Next Shipment Date</th><th>MOS Needed</th><th>Monthly Requirement</th><th>Additional Doses Needed</th></tr>";
	}

	function generatePDF($data, $title) {
		$html_title = "<img src='Images/coat_of_arms-resized.png' style='position:absolute; width:96px; height:92px; top:0px; left:0px; '></img>";
		$html_title .= "<h3 style='text-align:center; text-decoration:underline; margin-top:-50px;'>" . $title . "</h3>";
		$date = date('d-M-Y');
		$html_title .= "<h5 style='text-align:center;'> as at: " . $date . "</h5>";

		$this -> load -> library('mpdf');
		$this -> mpdf = new mPDF('c', 'A4');
		$this -> mpdf -> SetTitle('Vaccine MOS Status');
		$this -> mpdf -> WriteHTML($html_title);
		$this -> mpdf -> simpleTables = true;
		$this -> mpdf -> WriteHTML($data);
		$this -> mpdf -> WriteHTML($html_footer);
		$report_name = "Vaccine MOS Status.pdf";
		$this -> mpdf -> Output($report_name, 'D');
	}

}
