<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Months_Of_Stock extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	public function get_national_mos_balance() {
		$vaccines = Vaccines::getAll_Minified();
		$date = date("m/d/Y");
		$months_required = array();
		$chart = '
<chart showLegend="0"  decimals="2" caption="Months of Stock Left" xAxisName="Antigen" yAxisName="Months of Stock" showValues="1" decimals="0" formatNumberScale="0" clickURL="' . base_url() . 'disbursement_management/drill_down/2/0">';
		$chart .= "<categories>";
		foreach ($vaccines as $vaccine_object) {
			$chart .= '<category label="' . $vaccine_object -> Name . '"/>';
		}
		$chart .= "</categories>";
		foreach ($vaccines as $vaccine_object) {
			$months_of_stock = array();
			$year = date('Y');
			$now = date('U');
			$expected_delivery = Provisional_Plan::getNextDelivery($vaccine_object -> id);
			$months_till_shipment = 0;
			if (isset($expected_delivery[0])) {
				$days_till_shipment = $expected_delivery[0]['difference'];
				if (isset($days_till_shipment)) {
					$months_till_shipment = number_format(($days_till_shipment / 30), 1);
				}
			}
			//echo $months_till_shipment;
			$months_required[$vaccine_object -> id] = $months_till_shipment;

		}
		$chart .= '<dataset seriesName="MOS" color="000000" showValues="1">';
		foreach ($vaccines as $vaccine_object) {
			$months_of_stock = array();
			$year = date('Y');
			$now = date('U');
			//Get National Data
			$population = regional_populations::getNationalPopulation($year);
			$population = str_replace(",", "", $population);
			$monthly_requirement = ceil(($vaccine_object -> Doses_Required * $population * $vaccine_object -> Wastage_Factor) / 12);
			$stock_balance = Disbursements::getNationalPeriodBalance($vaccine_object -> id, $now);
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
			/*	$expected_delivery = Provisional_Plan::getNextDelivery($vaccine_object -> id);
			 $days_till_shipment = $expected_delivery[0]['difference'];
			 $months_till_shipment = 0;
			 if (isset($days_till_shipment)) {
			 $months_till_shipment = number_format(($days_till_shipment / 30), 1);
			 }*/
			//echo $months_till_shipment;

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

	function download_national() {
		$vaccines = Vaccines::getAll_Minified();
		$date = date("m/d/Y");
		$months_required = array();
		$data_buffer = "
			<style>
			table.data-table {
			table-layout: fixed;
			width: 700px;
			}
			table.data-table td {
			width: 100px;
			text-align:center;
			}
			</style> 
			";
		$data_buffer .= "<table class='data-table'>";
		$data_buffer .= $this -> echoTitles();

		foreach ($vaccines as $vaccine_object) {
			$months_of_stock = array();
			$year = date('Y');
			$now = date('U');
			//Get National Data
			$population = regional_populations::getNationalPopulation($year);
			$population = str_replace(",", "", $population);
			$monthly_requirement = ceil(($vaccine_object -> Doses_Required * $population * $vaccine_object -> Wastage_Factor) / 12);
			$expected_delivery = Provisional_Plan::getNextDelivery($vaccine_object -> id);
			$stock_balance = Disbursements::getNationalPeriodBalance($vaccine_object -> id, $now);
			$months_till_shipment = 0;
			$next_shipment = "N/A";
			if (isset($expected_delivery[0])) {
				$next_shipment = $expected_delivery[0]['next_shipment'];
				$days_till_shipment = $expected_delivery[0]['difference'];
				if (isset($days_till_shipment)) {
					$months_till_shipment = number_format(($days_till_shipment / 30), 1);
				}
			}
			$months_left = 0;
			if ($stock_balance > 0) {
				$months_left = number_format(($stock_balance / $monthly_requirement), 1);
			}
			$data_buffer .= "<tr><td>" . $vaccine_object -> Name . "</td><td>" . number_format($stock_balance) . "</td><td>" . $months_left . "</td><td>" . $next_shipment . "</td><td>" . $months_till_shipment . "</td><td>" . $monthly_requirement . "</td><td>" . number_format((($months_till_shipment-$months_left)*$monthly_requirement),2) . "</td></tr>";

		}
		$data_buffer .= "</table>";
		$this -> generatePDF($data_buffer);
		//echo $data_buffer;
	}

	public function echoTitles() {
		return "<tr><th>Antigen</th><th>Current Stock Balance</th><th>MOS Left</th><th>Next Shipment Date</th><th>MOS Needed</th><th>Monthly Requirement</th><th>Doses Needed</th></tr>";
	}

	function generatePDF($data) {
		$html_title = "<img src='Images/coat_of_arms-resized.png' style='position:absolute; width:96px; height:92px; top:0px; left:0px; '></img>";
		$html_title .= "<h3 style='text-align:center; text-decoration:underline; margin-top:-50px;'>Antigen MOS Balance</h3>";
		$date = date('d/m/Y');
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
