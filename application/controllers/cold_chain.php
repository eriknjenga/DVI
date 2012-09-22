<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Cold_Chain extends MY_Controller {
	function __construct() {
		parent::__construct();

	}

	public function get_national_utilization() {
		$freezer_vaccines = Fridge_Compartments::getCompartmentVaccines("freezer");
		$fridge_vaccines = Fridge_Compartments::getCompartmentVaccines("fridge");
		$national_fridges = National_Fridges::getNationalFridges();
		$all_vaccines = Vaccines::getAll_Minified();
		$freezer_capacities = array();
		$fridge_capacities = array();
		$now = date("U");
		$total_net_volume_4deg = 0;
		$total_net_volume_minus_20deg = 0;
		//Get the total Capacities of all the fridges
		foreach ($national_fridges as $fridge) {
			$total_net_volume_4deg += $fridge -> Fridge_Equipment -> Net_Vol_4deg;
			$total_net_volume_minus_20deg += $fridge -> Fridge_Equipment -> Net_Vol_Minus_20deg;
		}
		$freezer_content = array();
		$counter = 0;
		//Get the stock balances for each of the vaccines at this point in time
		foreach ($freezer_vaccines as $vaccine) {
			$volume = $vaccine -> Vaccine_Packed_Volume;
			$stock_balance = Disbursements::getNationalPeriodBalance($vaccine -> id, $now);
			$volume_occupied = $volume * $stock_balance;
			if ($volume_occupied > 0) {
				$volume_occupied = $volume_occupied / 1000;
				$freezer_capacities[$counter] = $volume_occupied;
				$total_net_volume_minus_20deg -= $volume_occupied;
			} else {
				$freezer_capacities[$counter] = 0;
			}
			$freezer_content[$counter] = $vaccine -> Name;
			$counter++;
		}
		$fridge_content = array();
		$counter = 0;
		//Get the stock balances for each of the vaccines at this point in time
		foreach ($fridge_vaccines as $vaccine) {
			$volume = $vaccine -> Vaccine_Packed_Volume;
			$stock_balance = Disbursements::getNationalPeriodBalance($vaccine -> id, $now);
			$volume_occupied = $volume * $stock_balance;
			if ($volume_occupied > 0) {
				$volume_occupied = $volume_occupied / 1000;
				$fridge_capacities[$counter] = $volume_occupied;
				$total_net_volume_4deg -= $volume_occupied;
			} else {
				$fridge_capacities[$counter] = 0;
			}
			$fridge_content[$counter] = $vaccine -> Name;
			$counter++;
		}

		$chart = '<chart palette="1" bgColor="FFFFFF" plotGradientColor="" showAlternateHGridColor="0" showAlternateVGridColor="0" divLineAlpha="20" showBorder="0" decimals="2" caption="Cold Chain Utilization - Central Vaccine Store" xAxisName="Compartment" yAxisName="Capacity (Litres)" shownames="1" showvalues="0" showSum="1" overlapColumns="0" clickURL="' . base_url() . 'cold_chain/national_utilization_report">
<categories>
<category label="+4"/>
<category label="-20"/>
</categories>';
		$counter = 0;
		foreach ($all_vaccines as $vaccine) {

			if (isset($fridge_capacities[$counter])) {
				$chart .= '<dataset seriesName="' . $fridge_content[$counter] . '">';
				$chart .= '<set value="' . $fridge_capacities[$counter] . '"/>';
				$chart .= '<set value="0"/>';
				$chart .= '</dataset>';
			}
			if (isset($freezer_capacities[$counter])) {
				$chart .= '<dataset seriesName="' . $freezer_content[$counter] . '">';
				$chart .= '<set value="0"/>';
				$chart .= '<set value="' . $freezer_capacities[$counter] . '"/>';
				$chart .= '</dataset>';
			}
			$counter++;
		}
		$chart .= '<dataset seriesName="Available" showValues="0" color="FFFFFF">
<set value="' . $total_net_volume_4deg . '"/>
<set value="' . $total_net_volume_minus_20deg . '"/>
</dataset>';
		$chart .= '</chart>';
		echo $chart;
	}

	function download_national() {
		$freezer_vaccines = Fridge_Compartments::getCompartmentVaccines("freezer");
		$fridge_vaccines = Fridge_Compartments::getCompartmentVaccines("fridge");
		$national_fridges = National_Fridges::getNationalFridges();
		$all_vaccines = Vaccines::getAll_Minified();
		$freezer_stock = array();
		$fridge_stock = array();
		$freezer_capacities = array();
		$fridge_capacities = array();
		$now = date("U");
		$total_net_volume_4deg = 0;
		$total_net_volume_minus_20deg = 0;
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
		//Get the total Capacities of all the fridges
		foreach ($national_fridges as $fridge) {
			$total_net_volume_4deg += $fridge -> Fridge_Equipment -> Net_Vol_4deg;
			$total_net_volume_minus_20deg += $fridge -> Fridge_Equipment -> Net_Vol_Minus_20deg;
		}
		$freezer_capacity = $total_net_volume_minus_20deg;
		$fridge_capacity = $total_net_volume_4deg;
		//Get the stock balances for each of the vaccines at this point in time
		foreach ($freezer_vaccines as $vaccine) {
			$volume = $vaccine -> Vaccine_Packed_Volume;
			$stock_balance = Disbursements::getNationalPeriodBalance($vaccine -> id, $now);
			$freezer_stock[$vaccine -> id] = $stock_balance;
			$volume_occupied = $volume * $stock_balance;
			if ($volume_occupied > 0) {
				$volume_occupied = $volume_occupied / 1000;
				$freezer_capacities[$vaccine -> id] = $volume_occupied;
				$total_net_volume_minus_20deg -= $volume_occupied;
			} else {
				$freezer_capacities[$vaccine -> id] = 0;
			}

		}
		//Get the stock balances for each of the vaccines at this point in time
		foreach ($fridge_vaccines as $vaccine) {
			$volume = $vaccine -> Vaccine_Packed_Volume;
			$stock_balance = Disbursements::getNationalPeriodBalance($vaccine -> id, $now);
			$volume_occupied = $volume * $stock_balance;
			if ($volume_occupied > 0) {
				$volume_occupied = $volume_occupied / 1000;
				$fridge_capacities[$vaccine -> id] = $volume_occupied;
				$total_net_volume_4deg -= $volume_occupied;
			} else {
				$fridge_capacities[$vaccine -> id] = 0;
			}
			$fridge_stock[$vaccine -> id] = $stock_balance;

		}
		$fridge_totals = 0;
		$freezer_totals = 0;
		foreach ($all_vaccines as $vaccine) {
			$data_buffer .= "<tr><td style='text-align: left;'>" . $vaccine -> Name . "</td>";
			if (isset($fridge_capacities[$vaccine -> id])) {
				$data_buffer .= "<td class='right'>" . number_format($fridge_stock[$vaccine -> id] + 0) . "</td><td  class='right'>" . number_format($fridge_capacities[$vaccine -> id] + 0) . "</td><td  class='right'>N/A</td>";
				$fridge_totals += $fridge_capacities[$vaccine -> id];
			}
			if (isset($freezer_capacities[$vaccine -> id])) {
				$data_buffer .= "<td  class='right'>" . number_format($freezer_stock[$vaccine -> id] + 0) . "</td><td  class='right'>N/A</td><td  class='right'>" . number_format($freezer_capacities[$vaccine -> id] + 0) . "</td>";
				$freezer_totals += $freezer_capacities[$vaccine -> id];
			}

			$data_buffer .= "</tr>";
		}
		$data_buffer .= "<tr><td style='text-align: left;'>Totals</td><td>-</td><td class='right'>" . number_format($fridge_totals + 0) . "</td><td class='right'>" . number_format($freezer_totals + 0) . "</td></tr>";
		$data_buffer .= "</table>";
		$data_buffer .= "<table class='data-table' style='margin-top:50px;'><tr><th>Statistic</th><th>(+2 to +8)</th><th>(-15 to -25)</th></tr>";
		$data_buffer .= "<tr><td style='text-align: left;'>Total Net Volume (Litres)</th><td  class='right'>" . number_format($fridge_capacity, 2) . "</td><td  class='right'>" . number_format($freezer_capacity, 2) . "</td></tr>";
		$data_buffer .= "<tr><td style='text-align: left;'>Total Occupied Capacity (Litres)</td><td  class='right'>" . number_format(($fridge_capacity - $total_net_volume_4deg), 2) . "</td><td  class='right'>" . number_format(($freezer_capacity - $total_net_volume_minus_20deg), 2) . "</td></tr>";
		$data_buffer .= "<tr><td style='text-align: left;'>Available Capacity (Litres)</td><td  class='right'>" . number_format($total_net_volume_4deg, 2) . "</td><td  class='right'>" . number_format($total_net_volume_minus_20deg, 2) . "</td></tr></table>";
		$this -> generatePDF($data_buffer);
		//echo $data_buffer;
	}

	public function echoTitles() {
		return "<tr><th>Antigen</th><th>Current Stock Balance</th><th>(+2 to +8) Capacity Occupied</th><th>(-15 to -25) Capacity Occupied</th></tr>";
	}

	public function get_national_fridge_occupancy() {
		$fridge_vaccines = Fridge_Compartments::getCompartmentVaccines("fridge");
		$freezer_vaccines = Fridge_Compartments::getCompartmentVaccines("freezer");
		$fridge_capacities = array();
		$now = date("U");
		$total_net_volume_4deg = 0;
		$total_net_volume_minus_20deg = 0;
		$occupied_capacity = 0;
		$freezer_occupied_capacity = 0;
		$national_fridges = National_Fridges::getNationalFridges();
		foreach ($national_fridges as $fridge) {
			$total_net_volume_4deg += $fridge -> Fridge_Equipment -> Net_Vol_4deg;
			$total_net_volume_minus_20deg += $fridge -> Fridge_Equipment -> Net_Vol_Minus_20deg;
		}
		//Get the stock balances for each of the vaccines at this point in time
		foreach ($fridge_vaccines as $vaccine) {
			$volume = $vaccine -> Vaccine_Packed_Volume;
			$stock_balance = Disbursements::getNationalPeriodBalance($vaccine -> id, $now);
			$volume_occupied = $volume * $stock_balance;
			if ($volume_occupied > 0) {
				$volume_occupied = $volume_occupied / 1000;
				$occupied_capacity += $volume_occupied;
			}
		}
		foreach ($freezer_vaccines as $vaccine) {
			$volume = $vaccine -> Vaccine_Packed_Volume;
			$stock_balance = Disbursements::getNationalPeriodBalance($vaccine -> id, $now);
			$volume_occupied = $volume * $stock_balance;
			if ($volume_occupied > 0) {
				$volume_occupied = $volume_occupied / 1000;
				$freezer_occupied_capacity += $volume_occupied;
			}

		}
		$percentage_occupied = (number_format(($occupied_capacity / $total_net_volume_4deg), 3) * 100);
		$freezer_percentage_occupied = (number_format(($freezer_occupied_capacity / $total_net_volume_minus_20deg), 3) * 100);
		echo '<chart bgColor="FFFFFF" showBorder="0" showCanvasBase="1"  cylRadius="20" upperLimit="100" lowerLimit="0" tickMarkGap="5" numberSuffix="%" caption="Fridge Occupied">
<value>' . $percentage_occupied . '</value>
<annotations>
<annotationGroup>
<annotation type="rectangle" xPos="100" yPos="70" toXPos="300" toYPos="130" radius="0" fillcolor="333333" fillAlpha="5"/>
<annotation type="line" xPos="100" yPos="70" toYPos="130" color="333333" thickness="2"/>
<annotation type="line" xPos="300" yPos="70" toYPos="130" color="333333" thickness="2"/>
<annotation type="line" xPos="100" yPos="70" toXPos="105" color="333333" thickness="2"/>
<annotation type="line" xPos="100" yPos="130" toXPos="105" color="333333" thickness="2"/>
<annotation type="line" xPos="300" yPos="70" toXPos="295" color="333333" thickness="2"/>
<annotation type="line" xPos="300" yPos="130" toXPos="295" color="333333" thickness="2"/>
<annotation type="text" label="Capacity of +2 to +8 Occupied" font="Verdana" xPos="115" yPos="75" align="left" vAlign="left" fontcolor="333333" fontSize="10" isBold="1"/>
<annotation type="text" label="(expressed as a % of the total)" font="Verdana" xPos="114" yPos="90" align="left" vAlign="left" fontcolor="333333" fontSize="10"/>
<annotation type="text" label="Occupied: ' . $occupied_capacity . '/' . $total_net_volume_4deg . '" font="Verdana" xPos="115" yPos="105" align="left" vAlign="left" fontcolor="333333" fontSize="10" isbold="1"/>
<annotation type="text" label="Fridge" font="Verdana" xPos="10" yPos="285" align="left" vAlign="left" fontcolor="333333" fontSize="10"/>
</annotationGroup>
<annotationGroup>
<annotation type="rectangle" xPos="100" yPos="0" toXPos="300" toYPos="60" radius="0" fillcolor="333333" fillAlpha="5"/>
<annotation type="line" xPos="100" yPos="0" toYPos="60" color="333333" thickness="2"/>
<annotation type="line" xPos="300" yPos="0" toYPos="60" color="333333" thickness="2"/>
<annotation type="line" xPos="100" yPos="0" toXPos="105" color="333333" thickness="2"/>
<annotation type="line" xPos="100" yPos="60" toXPos="105" color="333333" thickness="2"/>
<annotation type="line" xPos="300" yPos="0" toXPos="295" color="333333" thickness="2"/>
<annotation type="line" xPos="300" yPos="60" toXPos="295" color="333333" thickness="2"/>
<annotation type="text" label="Capacity of -15 to -25 Occupied" font="Verdana" xPos="115" yPos="5" align="left" vAlign="left" fontcolor="333333" fontSize="10" isBold="1"/>
<annotation type="text" label="(expressed as a % of the total)" font="Verdana" xPos="114" yPos="20" align="left" vAlign="left" fontcolor="333333" fontSize="10"/>
<annotation type="text" label="Occupied: ' . $freezer_occupied_capacity . '/' . $total_net_volume_minus_20deg . '" font="Verdana" xPos="115" yPos="35" align="left" vAlign="left" fontcolor="333333" fontSize="10" isbold="1"/>
</annotationGroup>

</annotations>
</chart>

';
	}

	public function get_national_freezer_occupancy() {
		$freezer_vaccines = Fridge_Compartments::getCompartmentVaccines("freezer");
		$freezer_capacities = array();
		$now = date("U");
		$total_net_volume_minus_20deg = 0;
		$occupied_capacity = 0;
		$national_fridges = National_Fridges::getNationalFridges();
		foreach ($national_fridges as $fridge) {
			$total_net_volume_minus_20deg += $fridge -> Fridge_Equipment -> Net_Vol_Minus_20deg;
		}
		//Get the stock balances for each of the vaccines at this point in time
		foreach ($freezer_vaccines as $vaccine) {
			$volume = $vaccine -> Vaccine_Packed_Volume;
			$stock_balance = Disbursements::getNationalPeriodBalance($vaccine -> id, $now);
			$volume_occupied = $volume * $stock_balance;
			if ($volume_occupied > 0) {
				$volume_occupied = $volume_occupied / 1000;
				$occupied_capacity += $volume_occupied;
			}

		}
		$percentage_occupied = (number_format(($occupied_capacity / $total_net_volume_minus_20deg), 3) * 100);
		echo '<chart bgColor="FFFFFF" showBorder="0" showCanvasBase="1"  cylRadius="20" upperLimit="100" lowerLimit="0" tickMarkGap="5" numberSuffix="%" caption="% of Fridge Occupied">
<value>' . $percentage_occupied . '</value>
<annotations>
<annotationGroup>
<annotation type="text" label="Freezer" font="Verdana" xPos="10" yPos="285" align="left" vAlign="left" fontcolor="333333" fontSize="10"/>
</annotationGroup>

</annotations>
</chart>';
	}

	public function national_utilization_report() {

	}

	function generatePDF($data) {
		$html_title = "<img src='Images/coat_of_arms-resized.png' style='position:absolute; width:96px; height:92px; top:0px; left:0px; '></img>";
		$html_title .= "<h3 style='text-align:center; text-decoration:underline; margin-top:-50px;'>Antigen Cold Chain Occupation</h3>";
		$date = date('d-M-Y');
		$html_title .= "<h5 style='text-align:center;'> as at: " . $date . "</h5>";

		$this -> load -> library('mpdf');
		$this -> mpdf = new mPDF('c', 'A4');
		$this -> mpdf -> SetTitle('Vaccine Cold Chain Occupation');
		$this -> mpdf -> WriteHTML($html_title);
		$this -> mpdf -> simpleTables = true;
		$this -> mpdf -> WriteHTML($data);
		$this -> mpdf -> WriteHTML($html_footer);
		$report_name = "Vaccine Cold Chain Occupation.pdf";
		$this -> mpdf -> Output($report_name, 'D');
	}

}
