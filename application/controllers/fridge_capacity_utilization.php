<?php
class Fridge_Capacity_Utilization extends MY_Controller {
	function __construct() {
		parent::__construct();

	}

	public function utilization($type, $year = "") {
		$total_occupied_capacity = array();
		if ($year == "") {
			$year = date('Y');
		}
		$year_start = date("U", mktime(0, 0, 0, 1, 1, $year));
		$vaccines = Fridge_Compartments::getCompartmentVaccines($type);
		$counter = 2;
		$user_identifier = $this -> session -> userdata("user_identifier");
		$district_province_id = $this -> session -> userdata("user_identifier");
		$fridges = null;
		if ($user_identifier == "regional_officer") {
			//Regional Store
			for ($month = 1; $month <= 24; $month++) {
				//Get the month
				$month_number = $counter / 2;
				//If it is an even number, get values for the 21st, if it's odd, get values for the 7th
				if ($month % 2 == 0) {
					$month_date = 21;
				} else {
					$month_date = 7;
				}
				$to = date("U", mktime(0, 0, 0, $month_number, $month_date, $year));
				//Get the stock balances for each of the vaccines at this point in time
				$total_volume = 0;
				foreach ($vaccines as $vaccine) {
					$volume = $vaccine -> Vaccine_Packed_Volume;
					$stock_balance = Disbursements::getRegionalPeriodBalance($district_province_id, $vaccine -> id, $to);
					$volume_occupied = $volume * $stock_balance;
					$total_volume += $volume_occupied;
				}

				$total_occupied_capacity[$month] = $total_volume;
				$counter += 2;
			}
			//Get the Fridges of this store
			$fridges = Regional_Fridges::getRegionFridges($district_province_id);

		} else if ($user_identifier == "district_officer") {
			//District Store
			for ($month = 1; $month <= 24; $month++) {
				//Get the month
				$month_number = $counter / 2;
				//If it is an even number, get values for the 21st, if it's odd, get values for the 7th
				if ($month % 2 == 0) {
					$month_date = 21;
				} else {
					$month_date = 7;
				}
				$to = date("U", mktime(0, 0, 0, $month_number, $month_date, $year));
				//Get the stock balances for each of the vaccines at this point in time
				$total_volume = 0;
				foreach ($vaccines as $vaccine) {
					$volume = $vaccine -> Vaccine_Packed_Volume;
					$stock_balance = Disbursements::getDistrictPeriodBalance($district_province_id, $vaccine -> id, $to);
					$volume_occupied = $volume * $stock_balance;
					$total_volume += $volume_occupied;
				}

				$total_occupied_capacity[$month] = $total_volume;
				$counter += 2;
			}
			//Get the Fridges of this store
			$fridges = District_Fridges::getDistrictFridges($district_province_id);

		} else if ($user_identifier == "national_officer") {
			//National Store
			for ($month = 1; $month <= 24; $month++) {
				//Get the month
				$month_number = $counter / 2;
				//If it is an even number, get values for the 21st, if it's odd, get values for the 7th
				if ($month % 2 == 0) {
					$month_date = 21;
				} else {
					$month_date = 7;
				}
				$to = date("U", mktime(0, 0, 0, $month_number, $month_date, $year));
				//Get the stock balances for each of the vaccines at this point in time
				$total_volume = 0;
				foreach ($vaccines as $vaccine) {
					$volume = $vaccine -> Vaccine_Packed_Volume;
					$stock_balance = Disbursements::getNationalPeriodBalance($vaccine -> id, $to);
					$volume_occupied = $volume * $stock_balance;
					$total_volume += $volume_occupied;
				}

				$total_occupied_capacity[$month] = $total_volume;
				$counter += 2;
			}
			//Get the Fridges of this store
			$fridges = National_Fridges::getNationalFridges();
		}

		$net_volume = 0;
		//loop through all the fridges to get the total capacity
		foreach ($fridges as $fridge) {
			if ($type == "freezer") {

				$net_volume += $fridge -> Fridge_Equipment -> Net_Vol_Minus_20deg;
			} else if ($type == "fridge") {

				$net_volume += $fridge -> Fridge_Equipment -> Net_Vol_4deg;
			}

		}
		//convert from m3 to cm3

		$net_volume /= 1000;
		$chart = '
<chart caption="Annual Capacity Utilization" subcaption="For the year ' . $year . '" xAxisName="Month" yAxisName="Volume"  numberSuffix="m3" showValues="0" alternateHGridColor="FCB541" alternateHGridAlpha="20" divLineColor="FCB541" divLineAlpha="50" canvasBorderColor="666666" baseFontColor="666666" lineColor="FCB541">
<categories>
<category label="Jan"/>
<category label=""/>
<category label="Feb"/>
<category label=""/>
<category label="Mar"/>
<category label=""/>
<category label="Apr"/>
<category label=""/>
<category label="May"/>
<category label=""/>
<category label="Jun"/>
<category label=""/>
<category label="Jul"/>
<category label=""/>
<category label="Aug"/>
<category label=""/>
<category label="Sep"/>
<category label=""/>
<category label="Oct"/>
<category label=""/>
<category label="Nov"/>
<category label=""/>
<category label="Dec"/>
<category label=""/>
</categories> 

<dataset seriesName="Capacity Utilized" color="0008FF" anchorBorderColor="0008FF" anchorBgColor="0008FF">
<set  value="' . $total_occupied_capacity[1] / 1000000 . '"/>
<set  value="' . $total_occupied_capacity[2] / 1000000 . '"/>
<set  value="' . $total_occupied_capacity[3] / 1000000 . '"/>
<set value="' . $total_occupied_capacity[4] / 1000000 . '"/>
<set  value="' . $total_occupied_capacity[5] / 1000000 . '"/>
<set value="' . $total_occupied_capacity[6] / 1000000 . '"/>
<set  value="' . $total_occupied_capacity[7] / 1000000 . '"/>
<set  value="' . $total_occupied_capacity[8] / 1000000 . '"/>
<set  value="' . $total_occupied_capacity[9] / 1000000 . '"/>
<set  value="' . $total_occupied_capacity[10] / 1000000 . '"/>
<set  value="' . $total_occupied_capacity[11] / 1000000 . '"/>
<set value="' . $total_occupied_capacity[12] / 1000000 . '"/>
<set  value="' . $total_occupied_capacity[13] / 1000000 . '"/>
<set  value="' . $total_occupied_capacity[14] / 1000000 . '"/>
<set  value="' . $total_occupied_capacity[15] / 1000000 . '"/>
<set value="' . $total_occupied_capacity[16] / 1000000 . '"/>
<set  value="' . $total_occupied_capacity[17] / 1000000 . '"/>
<set value="' . $total_occupied_capacity[18] / 1000000 . '"/>
<set  value="' . $total_occupied_capacity[19] / 1000000 . '"/>
<set  value="' . $total_occupied_capacity[20] / 1000000 . '"/>
<set  value="' . $total_occupied_capacity[21] / 1000000 . '"/>
<set  value="' . $total_occupied_capacity[22] / 1000000 . '"/>
<set  value="' . $total_occupied_capacity[23] / 1000000 . '"/>
<set value="' . $total_occupied_capacity[24] / 1000000 . '"/>
</dataset>
<dataset seriesName="Net Volume" color="FF0000" anchorBorderColor="FF0000" anchorBgColor="FF0000">';

		for ($x = 1; $x <= 24; $x++) {
			$chart .= '<set value="' . $net_volume . '"/>';
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
