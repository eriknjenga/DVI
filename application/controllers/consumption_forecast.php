<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Consumption_Forecast extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	public function national_forecast($vaccine = 0, $year = "0") {
		if ($year == "0") {
			$year = date('Y');
		}
		if ($vaccine == "0") {
			$vaccine_object = Vaccines::getFirstVaccine();
		} else if (strlen($vaccine) > 0) {
			$vaccine_object = Vaccines::getVaccine($vaccine);
		}
		//Get the start and end dates for all the four quarters
		$quarter_one_start = date("U", mktime(0, 0, 0, 1, 1, $year));
		$quarter_one_end = date("U", mktime(0, 0, 0, 3, 31, $year));
		$quarter_two_start = date("U", mktime(0, 0, 0, 4, 1, $year));
		$quarter_two_end = date("U", mktime(0, 0, 0, 6, 30, $year));
		$quarter_three_start = date("U", mktime(0, 0, 0, 7, 1, $year));
		$quarter_three_end = date("U", mktime(0, 0, 0, 9, 30, $year));
		$quarter_four_start = date("U", mktime(0, 0, 0, 10, 1, $year));
		$quarter_four_end = date("U", mktime(0, 0, 0, 12, 31, $year));

		//Set the maximum value
		$max_value = 0;
		//Get the consumption for each of the quarters
		$quarter_one_consumption = Disbursements::getNationalIssuesTotals($vaccine_object -> id, $quarter_one_start, $quarter_one_end);
		if ($quarter_one_consumption > $max_value) {
			$max_value = $quarter_one_consumption;
		}
		$quarter_two_consumption = Disbursements::getNationalIssuesTotals($vaccine_object -> id, $quarter_two_start, $quarter_two_end);
		if ($quarter_two_consumption > $max_value) {
			$max_value = $quarter_two_consumption;
		}
		$quarter_three_consumption = Disbursements::getNationalIssuesTotals($vaccine_object -> id, $quarter_three_start, $quarter_three_end);
		if ($quarter_three_consumption > $max_value) {
			$max_value = $quarter_three_consumption;
		}
		$quarter_four_consumption = Disbursements::getNationalIssuesTotals($vaccine_object -> id, $quarter_four_start, $quarter_four_end);
		if ($quarter_four_consumption > $max_value) {
			$max_value = $quarter_four_consumption;
		}
		//Get the national population
		$population = regional_populations::getNationalPopulation($year);
		$population = str_replace(",", "", $population);
		//Get the monthly requirement
		$monthly_requirement = ceil(($vaccine_object -> Doses_Required * $population * $vaccine_object -> Wastage_Factor) / 12);
		$quarterly_consumption = $monthly_requirement;
		$max_value += 10;
		if ($quarterly_consumption > $max_value) {
			$max_value = $quarterly_consumption;
		}
		$chart = '<chart bgColor="FFFFFF" showBorder="0" caption="Forecast vs. Consumption for ' . $vaccine_object -> Name . '" xAxisName="Quarterly Consumption" yAxisName="Doses" showValues="0" decimals="0" formatNumberScale="0" useRoundEdges="0">
<set label="Jan - Mar" value="' . $quarter_one_consumption . '"/>
<set label="Apr - Jun" value="' . $quarter_two_consumption . '"/>
<set label="Jul - Sep" value="' . $quarter_three_consumption . '"/>
<set label="Oct - Dec" value="' . $quarter_four_consumption . '"/>

    <trendLines>
        <line startValue="' . $quarterly_consumption . '" color="#009933" displayvalue="Forecasted" toolText="Forecasted Quarterly Consumption"/> 
    </trendLines>
</chart>';
		echo $chart;
	}

	function download_national_forecast($year = "0") {
		if ($year == "0") {
			$year = date('Y');
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
			.center{
				text-align: center !important;
			}
			</style> 
			";
		$data_buffer .= "<table class='data-table'>";
		$data_buffer .= $this -> echoTitles();
		//Get the start and end dates for all the quarters
		$quarter_one_start = date("U", mktime(0, 0, 0, 1, 1, $year));
		$quarter_one_end = date("U", mktime(0, 0, 0, 3, 31, $year));
		$quarter_two_start = date("U", mktime(0, 0, 0, 4, 1, $year));
		$quarter_two_end = date("U", mktime(0, 0, 0, 6, 30, $year));
		$quarter_three_start = date("U", mktime(0, 0, 0, 7, 1, $year));
		$quarter_three_end = date("U", mktime(0, 0, 0, 9, 30, $year));
		$quarter_four_start = date("U", mktime(0, 0, 0, 10, 1, $year));
		$quarter_four_end = date("U", mktime(0, 0, 0, 12, 31, $year));

		foreach ($vaccines as $vaccine_object) {
			$months_of_stock = array();

			$now = date('U');
			//Get National Data
			$population = regional_populations::getNationalPopulation($year);
			$population = str_replace(",", "", $population);
			$monthly_requirement = ceil(($vaccine_object -> Doses_Required * $population * $vaccine_object -> Wastage_Factor) / 12);
			$quarterly_consumption = $monthly_requirement * 3;

			//Get the consumption for each of the quarters
			$quarter_one_consumption = Disbursements::getNationalIssuesTotals($vaccine_object -> id, $quarter_one_start, $quarter_one_end);
			$quarter_two_consumption = Disbursements::getNationalIssuesTotals($vaccine_object -> id, $quarter_two_start, $quarter_two_end);
			$quarter_three_consumption = Disbursements::getNationalIssuesTotals($vaccine_object -> id, $quarter_three_start, $quarter_three_end);
			$quarter_four_consumption = Disbursements::getNationalIssuesTotals($vaccine_object -> id, $quarter_four_start, $quarter_four_end);
			$data_buffer .= "<tr><td class='leftie'>" . $vaccine_object -> Name . "</td><td class='center'>" . number_format($quarterly_consumption) . "</td><td class='center'>" . number_format($quarter_one_consumption) . "</td><td class='center'>" . number_format($quarterly_consumption - $quarter_one_consumption) . "</td><td class='center'>" . number_format($quarter_two_consumption) . "</td><td class='center'>" . number_format($quarterly_consumption - $quarter_two_consumption) . "</td><td class='center'>" . number_format($quarter_three_consumption) . "</td><td class='center'>" . number_format($quarterly_consumption - $quarter_three_consumption) . "</td><td class='center'>" . number_format($quarter_four_consumption) . "</td><td class='center'>" . number_format($quarterly_consumption - $quarter_four_consumption) . "</td></tr>";
		}
		$data_buffer .= "</table>";
		$this -> generatePDF($data_buffer,$year);
		//echo $data_buffer;
	}

	public function echoTitles() {
		$title = "<tr><th rowspan='2'>Antigen</th><th rowspan='2'>Quarterly Forecast</th><th colspan='2'>Quarter 1</th><th colspan='2'>Quarter 2</th><th colspan='2'>Quarter 3</th><th colspan='2'>Quarter 4</th></tr>";
		$title .= "<tr><th>Consumed</th><th>Difference</th><th>Consumed</th><th>Difference</th><th>Consumed</th><th>Difference</th><th>Consumed</th><th>Difference</th></tr>";
		return $title;
	}

	function generatePDF($data,$year) {
		$html_title = "<img src='Images/coat_of_arms-resized.png' style='position:absolute; width:96px; height:92px; top:0px; left:0px; '></img>";
		$html_title .= "<h3 style='text-align:center; text-decoration:underline; margin-top:-50px;'>Antigen Consumption Vs. Forecast</h3>";
		$date = date('d/m/Y');
		$html_title .= "<h5 style='text-align:center;'>for the year: ".$year." as at: " . $date . "</h5>";

		$this -> load -> library('mpdf');
		$this -> mpdf = new mPDF('c', 'A4');
		$this -> mpdf -> SetTitle('Vaccine Consumption Vs. Forecast');
		$this -> mpdf -> WriteHTML($html_title);
		$this -> mpdf -> simpleTables = true;
		$this -> mpdf -> WriteHTML($data);
		$this -> mpdf -> WriteHTML($html_footer);
		$report_name = "Vaccine Consumption Vs. Forecast.pdf";
		$this -> mpdf -> Output($report_name, 'D');
	}

}
