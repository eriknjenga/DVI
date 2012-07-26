<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Vaccine_Issues_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	function index() {
		$this -> view_issues_interface();
	}

	public function view_issues_interface() {
		$data['title'] = "Vaccine Issues";
		$data['quick_link'] = "issues";
		$this -> base_params($data);
	}

	private function base_params($data) {
		$data['link'] = "report_management";
		$data['content_view'] = "reports_view";
		$data['report'] = "issues_parameters_view";
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
			width: 1100px;
			}
			table.data-table td {
			width: 100px;
			}
			</style>
			";
			$start_date = $this -> input -> post("start_date");
			$end_date = $this -> input -> post("end_date");
			$data_buffer .= "<table class='data-table'>";
			$vaccines = Vaccines::getAll_Minified();
			$data_buffer .= $this -> echoTitles($vaccines);
			$population = Regional_Populations::getNationalPopulation(date('Y'));

			$sql_issues = "select vaccine_summaries.*,group_concat(vaccine_id,'-',quantity) as vaccine_issues from (SELECT vaccine_id,sum(Quantity) as quantity,issued_to_region,issued_to_district,issued_to_facility FROM `disbursements` where owner = 'N0' and Issued_By_National = '0'  and vaccine_id != '' and str_to_date(date_issued,'%m/%d/%Y') between str_to_date('" . $start_date . "','%m/%d/%Y') and str_to_date('" . $end_date . "','%m/%d/%Y')  group by vaccine_id,issued_to_region,issued_to_district,issued_to_facility) vaccine_summaries group by issued_to_region,issued_to_district,issued_to_facility";
			$query = $this -> db -> query($sql_issues);
			$issues_data = $query -> result_array();
			foreach ($issues_data as $recipient_data) {
				$population = 0;
				$recipient = "";
				if (isset($recipient_data['issued_to_region'])) {
					$population = Regional_Populations::getRegionalPopulation($recipient_data['issued_to_region'], date('Y'));
					$recipient = Regions::getRegionName($recipient_data['issued_to_region']);
				} else if (isset($recipient_data['issued_to_district'])) {
					$population = District_Populations::getDistrictPopulation($recipient_data['issued_to_district'], date('Y'));
					$recipient = Districts::getDistrictName($recipient_data['issued_to_district']);
				} else if (isset($recipient_data['issued_to_facility'])) {
					$recipient = $recipient_data['issued_to_facility'];
				}
				$data_buffer .= "<tr><td>" . $recipient . "</td><td>" . $population . "</td>";
				//Get the vaccine data
				$vaccine_data = $recipient_data['vaccine_issues'];
				$separated_data = explode(',', $vaccine_data);
				$final_vaccine_data = array();
				foreach ($separated_data as $vaccine_issue) {
					$further_separation = explode("-", $vaccine_issue);
					$final_vaccine_data[$further_separation[0]] = $further_separation[1];
				}
				foreach ($vaccines as $vaccine) {
					$doses = 0;
					$mos = 0;
					$population = str_replace(',', '', $population);
					if (isset($final_vaccine_data[$vaccine -> id])) {
						$doses = $final_vaccine_data[$vaccine -> id];
					}
					if ($population != 0 && $doses != 0) {
						$monthly_requirement = ceil(($vaccine -> Doses_Required * $population * $vaccine -> Wastage_Factor) / 12);
						$mos = ceil($doses / $monthly_requirement);
					}
					$data_buffer .= "<td>" . $doses . "</td><td>" . $mos . "</td>";
				}
				$data_buffer .= "</tr>";

			}
			$data_buffer .= "</table>";
			$this -> generatePDF($data_buffer, $start_date, $end_date);
		} else {
			$this -> view_transactions_interface();
		}

	}

	public function echoTitles($vaccines) {
		$initial_headers = "<tr><th>Recipient</th><th>Population</th>";
		foreach ($vaccines as $vaccine) {
			$initial_headers .= "<th>" . $vaccine -> Name . "</th><th>MOS</th>";
		}
		$initial_headers .= "</tr>";
		return $initial_headers;
	}

	function generatePDF($data, $start_date, $end_date) {
		$html_title = "<img src='Images/coat_of_arms-resized.png' style='position:absolute; width:96px; height:92px; top:0px; left:0px; '></img>";
		$html_title .= "<h3 style='text-align:center; text-decoration:underline; margin-top:-50px;'>Vaccine Issues Summary</h3>";
		$html_title .= "<h5 style='text-align:center;'> from: " . $start_date . " to: " . $end_date . "</h5>";

		$this -> load -> library('mpdf');
		$this -> mpdf = new mPDF('c', 'A4-L');
		$this -> mpdf -> SetTitle('Vaccine Issues');
		$this -> mpdf -> WriteHTML($html_title);
		$this -> mpdf -> simpleTables = true;
		$this -> mpdf -> WriteHTML($data);
		$this -> mpdf -> WriteHTML($html_footer);
		$report_name = "Vaccine Issues.pdf";
		$this -> mpdf -> Output($report_name, 'D');
	}

	public function validate_form() {
		$this -> form_validation -> set_rules('start_date', 'Start Date', 'trim|required|xss_clean');
		$this -> form_validation -> set_rules('end_date', 'End Date', 'trim|required|xss_clean');
		return $this -> form_validation -> run();
	}

}
