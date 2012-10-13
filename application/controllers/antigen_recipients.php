<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Antigen_Recipients extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	public function recipients($vaccine = 0, $selected_year = 0, $selected_quarter = 0, $national = "", $region = "", $district = "") {
		$this -> load -> database();
		$year = date('Y');
		$quarter = 1;
		$quarter_start_date = 0;
		$quarter_end_date = 0;
		$periods = array(1 => "Jan - Mar", 2 => "Apr - Jun", 3 => "Jul - Sep", 4 => "Oct - Dec");
		if ($selected_year != "0") {
			$year = $selected_year;
		}
		if ($selected_quarter != "0") {
			$quarter = $selected_quarter;
		}
		if ($vaccine == "0") {
			$vaccine_object = Vaccines::getFirstVaccine();
		} else if (strlen($vaccine) > 0) {
			$vaccine_object = Vaccines::getVaccine($vaccine);
		}
		//Figure out which quarter has been selected and get the start and end dates for that quarter
		if ($quarter == 1) {
			$quarter_start_date = date("U", mktime(0, 0, 0, 1, 1, $year));
			$quarter_end_date = date("U", mktime(0, 0, 0, 3, 31, $year));
		}
		if ($quarter == 2) {
			$quarter_start_date = date("U", mktime(0, 0, 0, 4, 1, $year));
			$quarter_end_date = date("U", mktime(0, 0, 0, 6, 30, $year));
		}
		if ($quarter == 3) {
			$quarter_start_date = date("U", mktime(0, 0, 0, 7, 1, $year));
			$quarter_end_date = date("U", mktime(0, 0, 0, 9, 30, $year));
		}
		if ($quarter == 4) {
			$quarter_start_date = date("U", mktime(0, 0, 0, 10, 1, $year));
			$quarter_end_date = date("U", mktime(0, 0, 0, 12, 31, $year));
		}
		//query to get all the districts that received vaccines from the selected store in that period
		if ($national > 0) {
			$sql_recipients = "select districts_issued.*,sum(quantity) as total_received,d2.name as district_name  from (select distinct issued_to_district as district_id from disbursements where owner = 'N0' and issued_to_district>0 and date_issued_timestamp between '" . $quarter_start_date . "' and '" . $quarter_end_date . "') districts_issued left join disbursements d on district_id  = d.issued_to_district left join districts d2 on district_id = d2.ID where date_issued_timestamp between '" . $quarter_start_date . "' and '" . $quarter_end_date . "' and owner != concat('D',district_id) and vaccine_id = '" . $vaccine_object -> id . "' group by district_id";
		}
		if ($region > 0) {
			$sql_recipients = "select districts_issued.*,sum(quantity) as total_received,d2.name as district_name  from (select distinct issued_to_district as district_id from disbursements where owner = R'".$region."' and issued_to_district>0 and date_issued_timestamp between '" . $quarter_start_date . "' and '" . $quarter_end_date . "') districts_issued left join disbursements d on district_id  = d.issued_to_district left join districts d2 on district_id = d2.ID where date_issued_timestamp between '" . $quarter_start_date . "' and '" . $quarter_end_date . "' and owner != concat('D',district_id) and vaccine_id = '" . $vaccine_object -> id . "' group by district_id";
		}
		if ($district > 0) {
			$sql_recipients = "select districts_issued.*,sum(quantity) as total_received,d2.name as district_name  from (select distinct issued_to_district as district_id from disbursements where owner = 'D".$district."' and issued_to_district>0 and date_issued_timestamp between '" . $quarter_start_date . "' and '" . $quarter_end_date . "') districts_issued left join disbursements d on district_id  = d.issued_to_district left join districts d2 on district_id = d2.ID where date_issued_timestamp between '" . $quarter_start_date . "' and '" . $quarter_end_date . "' and owner != concat('D',district_id) and vaccine_id = '" . $vaccine_object -> id . "' group by district_id";
		}

		//echo $sql_recipients;
		$query = $this -> db -> query($sql_recipients);
		$recipients_data = $query -> result_array();
		$consumption = array();
		$forecast = array();
		$districts = array();
		$counter = 0;
		$max_forecast = 0;
		foreach ($recipients_data as $recipient_district) {
			$population = District_Populations::getDistrictPopulation($recipient_district['district_id'], date('Y'));
			$monthly_requirement = 0;
			if ($population > 0) {
				$monthly_requirement = ceil(($vaccine_object -> Doses_Required * $population * $vaccine_object -> Wastage_Factor) / 12);
			}
			$monthly_requirement *= 3;
			if ($monthly_requirement > $max_forecast) {
				$max_forecast = $monthly_requirement;
			}
			$forecast[$counter] = $monthly_requirement;
			$consumption[$counter] = $recipient_district['total_received'];
			$districts[$counter] = $recipient_district['district_name'];
			$counter++;
		}
		//Create the labels for the x axis
		$x_axis_increments = ($max_forecast / 10);
		$x_axis_increments_counter = 0;
		$chart = '<chart bgColor="FFFFFF" showAlternateHGridColor="0" divLineAlpha="10" showBorder="0" xAxisLabelMode="auto" caption="Consumption Vs. Forecast for ' . $vaccine_object -> Name . '" subCaption="for ' . $periods[$quarter] . ', ' . $year . '"  yAxisName="Consumption" xAxisName="Forecast" showLegend="0" xAxisMaxValue="' . $max_forecast . '" xAxisMinValue="0" formatNumberScale="0">
		<categories verticalLineColor="666666" verticalLineThickness="1">';
		for ($x = 0; $x <= 10; $x++) {
			$x_axis_increments_counter += $x_axis_increments;
			$chart .= '<category label="' . number_format($x_axis_increments_counter) . '" x="' . $x_axis_increments_counter . '" showVerticalLine="1"/>';
		}
		$chart .= '</categories>';
		$counter = 0;
		foreach ($districts as $district_data) {
			$chart .= '<dataSet seriesName="' . $district_data . '" color="009900" anchorSides="3" anchorRadius="4" anchorBgColor="D5FFD5" anchorBorderColor="009900"><set y="' . $consumption[$counter] . '" x="' . $forecast[$counter] . '"/></dataSet> ';
			$counter++;
		}
		$chart .= '
<dataset seriesName="Ideal" color="009900" anchorSides="3" anchorRadius="4" anchorBgColor="D5FFD5" anchorBorderColor="009900" drawLine="1" anchorAlpha="0"><set y="0" x="0" />';

		foreach ($forecast as $for_element) {
			$chart .= '<set y="' . $for_element . '" x="' . $for_element . '" />';
		}
		$chart .= '
</dataset>
</chart>';
		echo $chart;
	}

	public function download_national_recipients($selected_year = 0, $selected_quarter = 0) {
		$this -> load -> database();
		$year = date('Y');
		$quarter = 1;
		$quarter_start_date = 0;
		$quarter_end_date = 0;
		$periods = array(1 => "Jan - Mar", 2 => "Apr - Jun", 3 => "Jul - Sep", 4 => "Oct - Dec");
		if ($selected_year != "0") {
			$year = $selected_year;
		}
		if ($selected_quarter != "0") {
			$quarter = $selected_quarter;
		}
		$vaccines = Vaccines::getAll_Minified();
		//Figure out which quarter has been selected and get the start and end dates for that quarter
		if ($quarter == 1) {
			$quarter_start_date = date("U", mktime(0, 0, 0, 1, 1, $year));
			$quarter_end_date = date("U", mktime(0, 0, 0, 3, 31, $year));
		}
		if ($quarter == 2) {
			$quarter_start_date = date("U", mktime(0, 0, 0, 4, 1, $year));
			$quarter_end_date = date("U", mktime(0, 0, 0, 6, 30, $year));
		}
		if ($quarter == 3) {
			$quarter_start_date = date("U", mktime(0, 0, 0, 7, 1, $year));
			$quarter_end_date = date("U", mktime(0, 0, 0, 9, 30, $year));
		}
		if ($quarter == 4) {
			$quarter_start_date = date("U", mktime(0, 0, 0, 10, 1, $year));
			$quarter_end_date = date("U", mktime(0, 0, 0, 12, 31, $year));
		}
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
			.right{
				text-align: right !important;
			}
			</style> 
			";
		$data_buffer .= "<table class='data-table'>";
		$data_buffer .= $this -> echoTitles($vaccines);
		$district_data = array();
		foreach ($vaccines as $vaccine_object) {
			//query to get all the districts that received vaccines from the national store in that period
			$sql_recipients = "select districts_issued.*,sum(quantity) as total_received,d2.name as district_name  from (select distinct issued_to_district as district_id from disbursements where owner = 'N0' and issued_to_district>0 and date_issued_timestamp between '" . $quarter_start_date . "' and '" . $quarter_end_date . "') districts_issued left join disbursements d on district_id  = d.issued_to_district left join districts d2 on district_id = d2.ID where date_issued_timestamp between '" . $quarter_start_date . "' and '" . $quarter_end_date . "' and owner != concat('D',district_id) and vaccine_id = '" . $vaccine_object -> id . "' group by district_id order by d2.name";
			//echo $sql_recipients;
			$query = $this -> db -> query($sql_recipients);
			$recipients_data = $query -> result_array();
			$consumption = array();
			$forecast = array();
			$districts = array();
			$counter = 0;
			$max_forecast = 0;
			foreach ($recipients_data as $recipient_district) {
				$population = District_Populations::getDistrictPopulation($recipient_district['district_id'], date('Y'));
				$monthly_requirement = 0;
				if ($population > 0) {
					$monthly_requirement = ceil(($vaccine_object -> Doses_Required * $population * $vaccine_object -> Wastage_Factor) / 12);
				}
				$monthly_requirement *= 3;
				if ($monthly_requirement > $max_forecast) {
					$max_forecast = $monthly_requirement;
				}
				$forecast[$counter] = $monthly_requirement;
				$consumption[$counter] = $recipient_district['total_received'];
				$districts[$counter] = $recipient_district['district_name'];
				$district_data[$recipient_district['district_id']]['district'] = $recipient_district['district_name'];
				$district_data[$recipient_district['district_id']][$vaccine_object -> id] = array('vaccine' => $vaccine_object -> id, 'forecast' => $monthly_requirement, 'consumption' => $recipient_district['total_received']);
				$counter++;
			}
		}
		//var_dump($district_data);
		foreach ($district_data as $row_data) {
			$data_buffer .= "<tr><td class='leftie'>" . $row_data['district'] . "</td>";
			foreach ($vaccines as $vaccine_object) {
				if (isset($row_data[$vaccine_object -> id])) {
					$data_buffer .= "<td class='right'>" . number_format($row_data[$vaccine_object -> id]['forecast'] + 0) . "</td><td class='right'>" . number_format($row_data[$vaccine_object -> id]['consumption'] + 0) . "</td>";
				} else {
					$data_buffer .= "<td class='center'>-</td><td class='center'>-</td>";
				}
			}
			$data_buffer .= "</tr>";
		}
		$data_buffer .= "</table>";
		$this -> generatePDF($data_buffer, $periods[$quarter], $year);
		//echo $data_buffer;
	}

	public function echoTitles($vaccines) {
		$initial_headers = "<thead><tr><th rowspan='2'>District</th>";
		foreach ($vaccines as $vaccine) {
			$initial_headers .= "<th colspan='2'>" . $vaccine -> Name . "</th>";
		}
		$initial_headers .= "</tr><tr>";
		foreach ($vaccines as $vaccine) {
			$initial_headers .= "<th>Forecast</th><th>Consumption</th>";
		}
		$initial_headers .= "</tr></thead>";
		return $initial_headers;
	}

	function generatePDF($data, $quarter, $year) {
		$html_title = "<img src='Images/coat_of_arms-resized.png' style='position:absolute; width:96px; height:92px; top:0px; left:0px; '></img>";
		$html_title .= "<h3 style='text-align:center; text-decoration:underline; margin-top:-50px;'>Antigen Stock Distribution</h3>";
		$date = date('d-M-Y');
		$html_title .= "<h5 style='text-align:center;'> for : " . $quarter . ", " . $year . " as at: " . $date . "</h5>";

		$this -> load -> library('mpdf');
		$this -> mpdf = new mPDF('c', 'A4-L');
		$this -> mpdf -> SetTitle('Antigen Stock Distribution');
		$this -> mpdf -> WriteHTML($html_title);
		$this -> mpdf -> simpleTables = true;
		$this -> mpdf -> WriteHTML($data);
		$this -> mpdf -> WriteHTML($html_footer);
		$report_name = "Antigen Stock Distribution.pdf";
		$this -> mpdf -> Output($report_name, 'D');
	}

}
