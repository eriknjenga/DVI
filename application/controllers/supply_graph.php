<?php
class Supply_Graph extends MY_Controller {
	function __construct() {
		parent::__construct();

	}

	public function get($type, $id, $vaccine, $year = "") {
		$monthly_opening_stocks = array();
		if ($year == "") {
			$year = date('Y');
		}
		$year_start = date("U", mktime(0, 0, 0, 1, 1, $year));
		$vaccine_object = Vaccines::getVaccine($vaccine);
		$counter = 2;
		if ($type == 0) {
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

		} else if ($type == 2) {
			//National Store
			$population = regional_populations::getNationalPopulation($year);
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
				$monthly_opening_stocks[$month] = Disbursements::getNationalPeriodBalance($vaccine, $to);
				$counter += 2;
			}
			$upper_limit = $monthly_requirement * 4;
			$lower_limit = $monthly_requirement;
		}

		$chart = '
<chart caption="Monthly Stock at Hand Summary" subcaption="For the year ' . $year . '" xAxisName="Month" yAxisName="Quantity"  numberSuffix=" doses" showValues="0" alternateHGridColor="FCB541" alternateHGridAlpha="20" divLineColor="FCB541" divLineAlpha="50" canvasBorderColor="666666" baseFontColor="666666" lineColor="FCB541">
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
<dataset seriesName="Upper Limit" color="269600" anchorBorderColor="269600" anchorBgColor="269600">';

		for ($x = 1; $x <= 36; $x++) {
			$cumulative_value = $upper_limit;
			$chart .= '<set value="' . $cumulative_value . '"/>';
		}

		$chart .= '</dataset>

<dataset seriesName="Receipts" color="0008FF" anchorBorderColor="0008FF" anchorBgColor="0008FF">
<set  value="' . $monthly_opening_stocks[1] . '"/>
<set  value="' . $monthly_opening_stocks[2] . '"/>
<set  value="' . $monthly_opening_stocks[3] . '"/>
<set value="' . $monthly_opening_stocks[4] . '"/>
<set  value="' . $monthly_opening_stocks[5] . '"/>
<set value="' . $monthly_opening_stocks[6] . '"/>
<set  value="' . $monthly_opening_stocks[7] . '"/>
<set  value="' . $monthly_opening_stocks[8] . '"/>
<set  value="' . $monthly_opening_stocks[9] . '"/>
<set  value="' . $monthly_opening_stocks[10] . '"/>
<set  value="' . $monthly_opening_stocks[11] . '"/>
<set value="' . $monthly_opening_stocks[12] . '"/>
<set  value="' . $monthly_opening_stocks[13] . '"/>
<set  value="' . $monthly_opening_stocks[14] . '"/>
<set  value="' . $monthly_opening_stocks[15] . '"/>
<set value="' . $monthly_opening_stocks[16] . '"/>
<set  value="' . $monthly_opening_stocks[17] . '"/>
<set value="' . $monthly_opening_stocks[18] . '"/>
<set  value="' . $monthly_opening_stocks[19] . '"/>
<set  value="' . $monthly_opening_stocks[20] . '"/>
<set  value="' . $monthly_opening_stocks[21] . '"/>
<set  value="' . $monthly_opening_stocks[22] . '"/>
<set  value="' . $monthly_opening_stocks[23] . '"/>
<set value="' . $monthly_opening_stocks[24] . '"/>
<set value="' . $monthly_opening_stocks[25] . '"/>
<set  value="' . $monthly_opening_stocks[26] . '"/>
<set  value="' . $monthly_opening_stocks[27] . '"/>
<set  value="' . $monthly_opening_stocks[28] . '"/>
<set value="' . $monthly_opening_stocks[29] . '"/>
<set  value="' . $monthly_opening_stocks[30] . '"/>
<set value="' . $monthly_opening_stocks[31] . '"/>
<set  value="' . $monthly_opening_stocks[32] . '"/>
<set  value="' . $monthly_opening_stocks[33] . '"/>
<set  value="' . $monthly_opening_stocks[34] . '"/>
<set  value="' . $monthly_opening_stocks[35] . '"/>
<set  value="' . $monthly_opening_stocks[36] . '"/> 
</dataset>
<dataset seriesName="Lower Limit" color="FF0000" anchorBorderColor="FF0000" anchorBgColor="FF0000">';

		for ($x = 1; $x <= 36; $x++) {
			$cumulative_value = $lower_limit;
			$chart .= '<set value="' . $cumulative_value . '"/>';
		}

		$chart .= '</dataset>
<styles>
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

}
