<?php
class MOS_Trend extends MY_Controller {
	function __construct() {
		parent::__construct();

	}

	public function get($type, $id, $vaccine, $year = "") {
		$monthly_opening_stocks = array();
		$vaccine_objects = array();
		if ($year == "0") {
			$year = date('Y');
		}
		if ($vaccine == "0") {
			$vaccine_object = Vaccines::getFirstVaccine();
			$vaccine_objects[0] = $vaccine_object;
		} else if (strlen($vaccine) > 0) {
			$vaccines_array = explode('-', $vaccine);
			$counter = 0;
			foreach ($vaccines_array as $vaccines_element) {
				if (strlen($vaccines_element) > 0) {
					$vaccine_object = Vaccines::getVaccine($vaccines_element);
					$vaccine_objects[$counter] = $vaccine_object;
				}
				$counter++;
			}
		}
		$year_start = date("U", mktime(0, 0, 0, 1, 1, $year));

		$counter = 2;
		/*if ($type == 0) {
		 //Regional Store
		 $population = regional_populations::getRegionalPopulation($id, $year);
		 $population = str_replace(",", "", $population);
		 $monthly_requirement = ceil(($vaccine_object -> Doses_Required * $population * $vaccine_object -> Wastage_Factor) / 12);
		 for ($month = 1; $month <= 36; $month++) {
		 //Get the month
		 $month_number = $counter / 3;
		 //If it is an even number, get values for the 21st, if it's odd, get values for the 7th
		 if ($month % 3 == 0) {
		 $month_date = 7;
		 } else if ($month % 3 == 1) {
		 $month_date = 21;
		 } else if ($month % 3 == 2) {
		 $month_date = 28;
		 }
		 $to = date("U", mktime(0, 0, 0, $month_number, $month_date, $year));
		 $monthly_opening_stocks[$month] = Disbursements::getRegionalPeriodBalance($id, $vaccine, $to);
		 $counter += 3;
		 }
		 $upper_limit = $monthly_requirement * 4;
		 $lower_limit = $monthly_requirement;
		 } else if ($type == 1) {
		 //District Store
		 $population = district_populations::getDistrictPopulation($id, $year);
		 $population = str_replace(",", "", $population);
		 $monthly_requirement = ceil(($vaccine_object -> Doses_Required * $population * $vaccine_object -> Wastage_Factor) / 12);
		 for ($month = 1; $month <= 36; $month++) {
		 //Get the month
		 $month_number = $counter / 2;
		 //If it is an even number, get values for the 21st, if it's odd, get values for the 7th
		 if ($month % 3 == 0) {
		 $month_date = 7;
		 } else if ($month % 3 == 1) {
		 $month_date = 21;
		 } else if ($month % 3 == 2) {
		 $month_date = 28;
		 }
		 $to = date("U", mktime(0, 0, 0, $month_number, $month_date, $year));
		 $monthly_opening_stocks[$month] = Disbursements::getDistrictPeriodBalance($id, $vaccine, $to);
		 $counter += 2;
		 }
		 $upper_limit = $monthly_requirement * 2;
		 $lower_limit = ceil($monthly_requirement / 2);

		 } else*/
		if ($type == 2) {
			//National Store
			$population = regional_populations::getNationalPopulation($year);
			$population = str_replace(",", "", $population);

			foreach ($vaccine_objects as $vaccine_object) {
				$monthly_requirement = ceil(($vaccine_object -> Doses_Required * $population * $vaccine_object -> Wastage_Factor) / 12);
				for ($month = 1; $month <= 36; $month++) {
					$mos_balance = 0;
					//Get the month
					$month_number = ceil($month / 3);
					//If it is an even number, get values for the 21st, if it's odd, get values for the 7th
					if ($month % 3 == 0) {
						$month_date = 28;
					} else if ($month % 3 == 1) {
						$month_date = 7;
					} else if ($month % 3 == 2) {
						$month_date = 21;
					}
					$to = date("U", mktime(0, 0, 0, $month_number, $month_date, $year));
					$stock_balance = Disbursements::getNationalPeriodBalance($vaccine_object -> id, $to);
					if ($stock_balance > 0) {
						$mos_balance = number_format(($stock_balance / $monthly_requirement), 2);
					}
					$monthly_opening_stocks[$vaccine_object -> id][$month] = $mos_balance;
					$counter += 2;
				}
			}

		}

		$chart = '
<chart bgColor="FFFFFF" showBorder="0" showAlternateHGridColor="0" divLineAlpha="10" caption="Monthly Stock at Hand Summary" subcaption="For the year ' . $year . '" xAxisName="Month" yAxisName="Months of Stock"  showValues="0" >
<categories>
<category label="Jan"/>
<category label=""/>
<category label=""/>
<category label="Feb"/>
<category label=""/>
<category label=""/>
<category label="Mar"/>
<category label=""/>
<category label=""/>
<category label="Apr"/>
<category label=""/>
<category label=""/>
<category label="May"/>
<category label=""/>
<category label=""/>
<category label="Jun"/>
<category label=""/>
<category label=""/>
<category label="Jul"/>
<category label=""/>
<category label=""/>
<category label="Aug"/>
<category label=""/>
<category label=""/>
<category label="Sep"/>
<category label=""/>
<category label=""/>
<category label="Oct"/>
<category label=""/>
<category label=""/>
<category label="Nov"/>
<category label=""/>
<category label=""/>
<category label="Dec"/>
<category label=""/>
<category label=""/>
</categories> 
<dataset seriesName="3 Months of Stock" color="269600" anchorBorderColor="269600" anchorBgColor="269600">';

		for ($x = 1; $x <= 36; $x++) {
			$chart .= '<set value="3"/>';
		}

		$chart .= '</dataset>';
		foreach ($vaccine_objects as $vaccine_object) {
			$chart .= '<dataset seriesName="' . $vaccine_object -> Name . ' Balance">
<set  value="' . $monthly_opening_stocks[$vaccine_object -> id][1] . '"/>
<set  value="' . $monthly_opening_stocks[$vaccine_object -> id][2] . '"/>
<set  value="' . $monthly_opening_stocks[$vaccine_object -> id][3] . '"/>
<set value="' . $monthly_opening_stocks[$vaccine_object -> id][4] . '"/>
<set  value="' . $monthly_opening_stocks[$vaccine_object -> id][5] . '"/>
<set value="' . $monthly_opening_stocks[$vaccine_object -> id][6] . '"/>
<set  value="' . $monthly_opening_stocks[$vaccine_object -> id][7] . '"/>
<set  value="' . $monthly_opening_stocks[$vaccine_object -> id][8] . '"/>
<set  value="' . $monthly_opening_stocks[$vaccine_object -> id][9] . '"/>
<set  value="' . $monthly_opening_stocks[$vaccine_object -> id][10] . '"/>
<set  value="' . $monthly_opening_stocks[$vaccine_object -> id][11] . '"/>
<set value="' . $monthly_opening_stocks[$vaccine_object -> id][12] . '"/>
<set  value="' . $monthly_opening_stocks[$vaccine_object -> id][13] . '"/>
<set  value="' . $monthly_opening_stocks[$vaccine_object -> id][14] . '"/>
<set  value="' . $monthly_opening_stocks[$vaccine_object -> id][15] . '"/>
<set value="' . $monthly_opening_stocks[$vaccine_object -> id][16] . '"/>
<set  value="' . $monthly_opening_stocks[$vaccine_object -> id][17] . '"/>
<set value="' . $monthly_opening_stocks[$vaccine_object -> id][18] . '"/>
<set  value="' . $monthly_opening_stocks[$vaccine_object -> id][19] . '"/>
<set  value="' . $monthly_opening_stocks[$vaccine_object -> id][20] . '"/>
<set  value="' . $monthly_opening_stocks[$vaccine_object -> id][21] . '"/>
<set  value="' . $monthly_opening_stocks[$vaccine_object -> id][22] . '"/>
<set  value="' . $monthly_opening_stocks[$vaccine_object -> id][23] . '"/>
<set value="' . $monthly_opening_stocks[$vaccine_object -> id][24] . '"/>
<set value="' . $monthly_opening_stocks[$vaccine_object -> id][25] . '"/>
<set  value="' . $monthly_opening_stocks[$vaccine_object -> id][26] . '"/>
<set  value="' . $monthly_opening_stocks[$vaccine_object -> id][27] . '"/>
<set  value="' . $monthly_opening_stocks[$vaccine_object -> id][28] . '"/>
<set value="' . $monthly_opening_stocks[$vaccine_object -> id][29] . '"/>
<set  value="' . $monthly_opening_stocks[$vaccine_object -> id][30] . '"/>
<set value="' . $monthly_opening_stocks[$vaccine_object -> id][31] . '"/>
<set  value="' . $monthly_opening_stocks[$vaccine_object -> id][32] . '"/>
<set  value="' . $monthly_opening_stocks[$vaccine_object -> id][33] . '"/>
<set  value="' . $monthly_opening_stocks[$vaccine_object -> id][34] . '"/>
<set  value="' . $monthly_opening_stocks[$vaccine_object -> id][35] . '"/>
<set  value="' . $monthly_opening_stocks[$vaccine_object -> id][36] . '"/> 
</dataset>';
		}
		$chart .= '<styles>
<definition>
<style name="Anim1" type="animation" param="_xscale" start="0" duration="1"/>
<style name="Anim2" type="animation" param="_alpha" start="0" duration="0.6"/>
<style name="DataShadow" type="Shadow" alpha="40"/>
</definition>
<application>
<apply toObject="DIVLINES" styles="Anim1"/>
<apply toObject="HGRID" styles="Anim2"/>
<apply toObject="DATALABELS" styles="DataShadow,Anim2"/>
</application>
</styles>
</chart>
';

		echo $chart;
	}

	public function download_national_mos_trend($selected_year = 0) {
		$year = date('Y');
		if ($selected_year != "0") {
			$year = $selected_year;
		}
		$counter = 2;
		$vaccine_objects = Vaccines::getAll();
		$population = regional_populations::getNationalPopulation($year);
		$population = str_replace(",", "", $population);

		foreach ($vaccine_objects as $vaccine_object) {
			$monthly_requirement = ceil(($vaccine_object -> Doses_Required * $population * $vaccine_object -> Wastage_Factor) / 12);
			for ($month = 1; $month <= 36; $month++) {
				$mos_balance = 0;
				//Get the month
				$month_number = ceil($month / 3);
				//If it is an even number, get values for the 21st, if it's odd, get values for the 7th
				if ($month % 3 == 0) {
					$month_date = 28;
				} else if ($month % 3 == 1) {
					$month_date = 7;
				} else if ($month % 3 == 2) {
					$month_date = 21;
				}
				$to = date("U", mktime(0, 0, 0, $month_number, $month_date, $year));
				$stock_balance = Disbursements::getNationalPeriodBalance($vaccine_object -> id, $to);
				//$stock_balance = 0;
				if ($stock_balance > 0) {
					$mos_balance = number_format(($stock_balance / $monthly_requirement), 2);
				}
				$monthly_opening_stocks[$month][$vaccine_object -> id]['stock_balance'] = number_format($stock_balance+0);
				$monthly_opening_stocks[$month][$vaccine_object -> id]['mos_balance'] = $mos_balance;
				$counter += 2;
			}
		}
		$data_buffer = "
			<style>
			table.data-table {
			table-layout: fixed;
			width: 1000px;
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
		$counter = 2;
		$data_buffer .= $this -> echoTitles($vaccine_objects);
		for ($month = 1; $month <= 36; $month++) {
			$data_buffer .= "<tr>";
			$month_number = ceil($month / 3);
			//If it is an even number, get values for the 21st, if it's odd, get values for the 7th
			if ($month % 3 == 0) {
				$month_date = 28;
			} else if ($month % 3 == 1) {
				$month_date = 7;
			} else if ($month % 3 == 2) {
				$month_date = 21;
			}
			$date = date("M-d", mktime(0, 0, 0, $month_number, $month_date, $year));
			$data_buffer .= "<td>" . $date . "</td>";
			foreach ($vaccine_objects as $vaccine_object) {
				$data_buffer .= "<td class='center'>" . $monthly_opening_stocks[$month][$vaccine_object -> id]['mos_balance'] . "</td><td class='right'>" . $monthly_opening_stocks[$month][$vaccine_object -> id]['stock_balance'] . "</td>";
			}
			$data_buffer .= "</tr>";
			$counter += 2;
		}
		$data_buffer .= "</table>";
		$this -> generatePDF($data_buffer,$year);
		//	var_dump($monthly_opening_stocks);
	}

	function generatePDF($data,$year) {
		$html_title = "<img src='Images/coat_of_arms-resized.png' style='position:absolute; width:96px; height:92px; top:0px; left:0px; '></img>";
		$html_title .= "<h3 style='text-align:center; text-decoration:underline; margin-top:-50px;'>Antigen MOS Balance Trend</h3>";
		$date = date('d-M-Y');
		$html_title .= "<h5 style='text-align:center;'> for the year: ".$year." as at: " . $date . "</h5>";

		$this -> load -> library('mpdf');
		$this -> mpdf = new mPDF('c', 'A4-L');
		$this -> mpdf -> SetTitle('Monthly Stock At Hand Summary');
		$this -> mpdf -> WriteHTML($html_title);
		$this -> mpdf -> simpleTables = true;
		$this -> mpdf -> WriteHTML($data);
		$this -> mpdf -> WriteHTML($html_footer);
		$report_name = "Monthly Stock At Hand Summary.pdf";
		$this -> mpdf -> Output($report_name, 'D');
	}

	public function echoTitles($vaccines) {
		$initial_headers = "<tr><th rowspan='2'>Date</th>";
		foreach ($vaccines as $vaccine) {
			$initial_headers .= "<th colspan='2'>" . $vaccine -> Name . "</th>";
		}
		$initial_headers .= "</tr><tr>";
		foreach ($vaccines as $vaccine) {
			$initial_headers .= "<th>MOS</th><th>Stock</th>";
		}
		$initial_headers .= "</tr>";
		return $initial_headers;
	}

}
