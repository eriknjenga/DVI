<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Consumption_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	function index() {
		$this -> view_consumption_interface();
	}

	public function view_consumption_interface() {
		$data['title'] = "Vaccine Consumption";
		$data['content_view'] = "consumption_parameters_view";
		$data['quick_link'] = "consumption";
		$this -> base_params($data);
	}

	private function base_params($data) {
		$data['link'] = "report_management";
		$data['content_view'] = "reports_view";
		$data['report'] = "consumption_parameters_view";
		$data['scripts'] = array("jquery-ui.js", "tab.js");
		$data['styles'] = array("jquery-ui.css", "tab.css");
		$this -> load -> view('template', $data);
	}

	public function download() {
		$this -> load -> database();

		$valid = $this -> validate_form();
		if ($valid) {
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
			$start_date = $this -> input -> post("start_date");
			$end_date = $this -> input -> post("end_date");
			$data_buffer .= "<table class='data-table'>";
			$data_buffer .= $this -> echoTitles();
			$district_or_region = $this -> session -> userdata('district_province_id');
			$identifier = $this -> session -> userdata('user_identifier');
			$population = 0;
			$opening_balance = 0;
			$closing_balance = 0;
			$sql_consumption = "";
			$store = "";
			$vaccines = Vaccines::getAll_Minified();
			foreach ($vaccines as $vaccine) {
				if ($identifier == 'provincial_officer') {
					$region_object = Regions::getRegion($district_or_region);
					$store = $region_object -> name;
					$population = Regional_Populations::getRegionalPopulation($district_or_region, date('Y'));
					$opening_balance = Disbursements::getRegionalPeriodBalance($district_or_region, $vaccine -> id, strtotime($start_date));
					$closing_balance = Disbursements::getRegionalPeriodBalance($district_or_region, $vaccine -> id, strtotime($end_date));
					$owner = "R" . $district_or_region;
					$sql_consumption = "select (SELECT date_format(max(str_to_date(Date_Issued,'%m/%d/%Y')),'%d/%m/%Y')  FROM `disbursements` where Owner = '" . $owner . "' and str_to_date(Date_Issued,'%m/%d/%Y') between str_to_date('" . $start_date . "','%m/%d/%Y') and str_to_date('" . $end_date . "','%m/%d/%Y') and Vaccine_Id = '" . $vaccine -> id . "' and total_stock_balance>0)as last_stock_count,(SELECT sum(Quantity)FROM `disbursements` where Issued_By_Region = '" . $district_or_region . "' and Owner = '" . $owner . "' and str_to_date(Date_Issued,'%m/%d/%Y') between str_to_date('" . $start_date . "','%m/%d/%Y') and
str_to_date('" . $end_date . "','%m/%d/%Y') and Vaccine_Id = '" . $vaccine -> id . "')as total_issued,(SELECT sum(Quantity) FROM `disbursements` where Issued_To_Region = '" . $district_or_region . "' and Owner = '" . $owner . "' and str_to_date(Date_Issued,'%m/%d/%Y') between str_to_date('" . $start_date . "','%m/%d/%Y') and
str_to_date('" . $end_date . "','%m/%d/%Y') and Vaccine_Id = '" . $vaccine -> id . "')as total_received";
				} else if ($identifier == 'district_officer') {
					$district_object = Districts::getDistrict($district_or_region);
					$store = $district_object -> name;
					$population = District_Populations::getDistrictPopulation($district_or_region, date('Y'));
					$opening_balance = Disbursements::getDistrictPeriodBalance($district_or_region, $vaccine -> id, strtotime($start_date));
					$closing_balance = Disbursements::getDistrictPeriodBalance($district_or_region, $vaccine -> id, strtotime($end_date));
					$owner = "D" . $district_or_region;
					$sql_consumption = "select (SELECT date_format(max(str_to_date(Date_Issued,'%m/%d/%Y')),'%d/%m/%Y')  FROM `disbursements` where Owner = '" . $owner . "' and str_to_date(Date_Issued,'%m/%d/%Y') between str_to_date('" . $start_date . "','%m/%d/%Y') and str_to_date('" . $end_date . "','%m/%d/%Y') and Vaccine_Id = '" . $vaccine -> id . "' and total_stock_balance>0)as last_stock_count,(SELECT sum(Quantity)FROM `disbursements` where Issued_By_District = '" . $district_or_region . "' and Owner = '" . $owner . "' and str_to_date(Date_Issued,'%m/%d/%Y') between str_to_date('" . $start_date . "','%m/%d/%Y') and
str_to_date('" . $end_date . "','%m/%d/%Y') and Vaccine_Id = '" . $vaccine -> id . "')as total_issued,(SELECT sum(Quantity) FROM `disbursements` where Issued_To_District = '" . $district_or_region . "' and Owner = '" . $owner . "' and str_to_date(Date_Issued,'%m/%d/%Y') between str_to_date('" . $start_date . "','%m/%d/%Y') and
str_to_date('" . $end_date . "','%m/%d/%Y') and Vaccine_Id = '" . $vaccine -> id . "')as total_received";
				} else if ($identifier == 'national_officer') {
					$store = "Central Vaccines Store";
					$population = Regional_Populations::getNationalPopulation(date('Y'));
					$opening_balance = Disbursements::getNationalPeriodBalance($vaccine -> id, strtotime($start_date));
					$closing_balance = Disbursements::getNationalPeriodBalance($vaccine -> id, strtotime($end_date));
					$sql_consumption = "select (SELECT date_format(max(str_to_date(Date_Issued,'%m/%d/%Y')),'%d/%m/%Y')  FROM `disbursements` where Owner = 'N0' and str_to_date(Date_Issued,'%m/%d/%Y') between str_to_date('" . $start_date . "','%m/%d/%Y') and str_to_date('" . $end_date . "','%m/%d/%Y') and Vaccine_Id = '" . $vaccine -> id . "' and total_stock_balance>0)as last_stock_count,(SELECT sum(Quantity)FROM `disbursements` where Issued_By_National = '0' and Owner = 'N0' and str_to_date(Date_Issued,'%m/%d/%Y') between str_to_date('" . $start_date . "','%m/%d/%Y') and
str_to_date('" . $end_date . "','%m/%d/%Y') and Vaccine_Id = '" . $vaccine -> id . "')as total_issued,(SELECT sum(Quantity) FROM `disbursements` where Issued_To_National = '0' and Owner = 'N0' and str_to_date(Date_Issued,'%m/%d/%Y') between str_to_date('" . $start_date . "','%m/%d/%Y') and
str_to_date('" . $end_date . "','%m/%d/%Y') and Vaccine_Id = '" . $vaccine -> id . "')as total_received";
				}
				$query = $this -> db -> query($sql_consumption);
				$vaccine_data = $query -> row();
				$monthly_requirement = ceil(($vaccine -> Doses_Required * $population * $vaccine -> Wastage_Factor) / 12);
				$data_buffer .= "<tr><td style='text-align:left;'>" . $vaccine -> Name . "</td><td>" . number_format($opening_balance + 0) . "</td><td>" . number_format($vaccine_data -> total_received + 0) . "</td><td>" . number_format($vaccine_data -> total_issued + 0) . "</td><td>" . number_format(($closing_balance - ($opening_balance + $vaccine_data -> total_received - $vaccine_data -> total_issued)) + 0) . "</td><td>" . number_format($closing_balance + 0) . "</td><td>" . number_format(($closing_balance / $monthly_requirement), 1) . "</td><td>" . $vaccine_data -> last_stock_count . "</td></tr>";
			}
			$data_buffer .= "</table>";
			$this -> generatePDF($data_buffer, $start_date, $end_date, $store);
		} else {
			$this -> view_transactions_interface();
		}

	}

	public function echoTitles() {
		return "<tr><th>Vaccine</th><th>Opening Stock</th><th>Total Receipts</th><th>Total Issued</th><th>Adjustments</th><th>Closing Stock</th><th>MOS Balance</th><th>Date of last physical count</th></tr>";
	}

	function generatePDF($data, $start_date, $end_date, $store) {
		$html_title = "<img src='Images/coat_of_arms-resized.png' style='position:absolute; width:96px; height:92px; top:0px; left:0px; '></img>";
		$html_title .= "<h3 style='text-align:center; text-decoration:underline; margin-top:-50px;'>Vaccine Consumption Summary For " . $store . "</h3>";
		$start_date = date('d/m/Y',strtotime($start_date));
		$end_date = date('d/m/Y',strtotime($end_date));
		$html_title .= "<h5 style='text-align:center;'> from: " . $start_date . " to: " . $end_date . "</h5>";

		$this -> load -> library('mpdf');
		$this -> mpdf = new mPDF('c', 'A4');
		$this -> mpdf -> SetTitle('Vaccine Consumption');
		$this -> mpdf -> WriteHTML($html_title);
		$this -> mpdf -> simpleTables = true;
		$this -> mpdf -> WriteHTML($data);
		$this -> mpdf -> WriteHTML($html_footer);
		$report_name = "Vaccine Consumption.pdf";
		$this -> mpdf -> Output($report_name, 'D');
	}

	public function validate_form() {
		$this -> form_validation -> set_rules('start_date', 'Start Date', 'trim|required|xss_clean');
		$this -> form_validation -> set_rules('end_date', 'End Date', 'trim|required|xss_clean');
		return $this -> form_validation -> run();
	}

}
