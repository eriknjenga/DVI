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
			}
			</style>
			";
			$start_date = $this -> input -> post("start_date");
			$end_date = $this -> input -> post("end_date");
			$data_buffer .= "<table class='data-table'>";
			$data_buffer .= $this -> echoTitles();
			$vaccines = Vaccines::getAll_Minified();
			$population = Regional_Populations::getNationalPopulation(date('Y'));
			foreach ($vaccines as $vaccine) {
				$opening_balance = Disbursements::getNationalPeriodBalance($vaccine -> id, strtotime($start_date));
				$closing_balance = Disbursements::getNationalPeriodBalance($vaccine -> id, strtotime($end_date));
				$sql_consumption = "select (SELECT max(str_to_date(Date_Issued,'%m/%d/%Y'))  FROM `disbursements` where Owner = 'N0' and str_to_date(Date_Issued,'%m/%d/%Y') between str_to_date('" . $start_date . "','%m/%d/%Y') and str_to_date('" . $end_date . "','%m/%d/%Y') and Vaccine_Id = '" . $vaccine -> id . "' and total_stock_balance>0)as last_stock_count,(SELECT sum(Quantity)FROM `disbursements` where Issued_By_National = '0' and Owner = 'N0' and str_to_date(Date_Issued,'%m/%d/%Y') between str_to_date('" . $start_date . "','%m/%d/%Y') and
str_to_date('" . $end_date . "','%m/%d/%Y') and Vaccine_Id = '" . $vaccine -> id . "')as total_issued,(SELECT sum(Quantity) FROM `disbursements` where Issued_To_National = '0' and Owner = 'N0' and str_to_date(Date_Issued,'%m/%d/%Y') between str_to_date('" . $start_date . "','%m/%d/%Y') and
str_to_date('" . $end_date . "','%m/%d/%Y') and Vaccine_Id = '" . $vaccine -> id . "')as total_received";
				$query = $this -> db -> query($sql_consumption);
				$vaccine_data = $query -> row();
				$monthly_requirement = ceil(($vaccine -> Doses_Required * $population * $vaccine -> Wastage_Factor) / 12);
				$data_buffer .= "<tr><td>" . $vaccine -> Name . "</td><td>" . number_format($opening_balance + 0) . "</td><td>" . number_format($vaccine_data -> total_received + 0) . "</td><td>" . number_format($vaccine_data -> total_issued + 0) . "</td><td>" . number_format(($closing_balance - ($opening_balance + $vaccine_data -> total_received - $vaccine_data -> total_issued)) + 0) . "</td><td>" . number_format($closing_balance + 0) . "</td><td>" . floor($closing_balance / $monthly_requirement) . "</td><td>" . $vaccine_data -> last_stock_count . "</td></tr>";
			}
			$data_buffer .= "</table>";
			$this -> generatePDF($data_buffer, $start_date, $end_date);
		} else {
			$this -> view_transactions_interface();
		}

	}

	public function echoTitles() {
		return "<tr><th>Vaccine</th><th>Opening Stock</th><th>Total Receipts</th><th>Total Issued</th><th>Adjustments</th><th>Closing Stock</th><th>MOS Balance</th><th>Date of last physical count</th></tr>";
	}

	function generatePDF($data, $start_date, $end_date) {
		$html_title = "<img src='Images/coat_of_arms-resized.png' style='position:absolute; width:96px; height:92px; top:0px; left:0px; '></img>";
		$html_title .= "<h3 style='text-align:center; text-decoration:underline; margin-top:-50px;'>Vaccine Consumption Summary</h3>";
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
