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
		//Get the stock balances for each of the vaccines at this point in time
		foreach ($freezer_vaccines as $vaccine) {
			$volume = $vaccine -> Vaccine_Packed_Volume;
			$stock_balance = Disbursements::getNationalPeriodBalance($vaccine -> id, $now);
			$volume_occupied = $volume * $stock_balance;
			$freezer_capacities[$vaccine -> id] = $volume_occupied;
			$total_net_volume_minus_20deg -= $volume_occupied;
		}
		//Get the stock balances for each of the vaccines at this point in time
		foreach ($fridge_vaccines as $vaccine) {
			$volume = $vaccine -> Vaccine_Packed_Volume;
			$stock_balance = Disbursements::getNationalPeriodBalance($vaccine -> id, $now);
			$volume_occupied = $volume * $stock_balance;
			$freezer_capacities[$vaccine -> id] = $volume_occupied;
			$total_net_volume_4deg -= $volume_occupied;
		}

		$chart = '<chart palette="1" decimals="2" caption="Cold Chain Utilization" shownames="1" showvalues="0" showSum="1" overlapColumns="0" clickURL="' . base_url() . 'cold_chain/national_utilization_report">
<categories>
<category label="+4 (Fridge)"/>
<category label="-25 (Freezer)"/>
</categories>';
		foreach ($all_vaccines as $vaccine) {
			$chart .= '<dataset seriesName="' . $vaccine -> Name . '">';
			if (isset($fridge_vaccines[$vaccine -> id])) {
				$chart .= '<set value="' . $fridge_vaccines[$vaccine -> id] . '"/>';
			}
			if (isset($freezer_capacities[$vaccine -> id])) {
				$chart .= '<set value="' . $freezer_capacities[$vaccine -> id] . '"/>';
			}
			$chart .= '</dataset>';
		}
		$chart .= '<dataset seriesName="Available" showValues="0" color="FFFFFF">
<set value="' . $total_net_volume_4deg . '"/>
<set value="' . $total_net_volume_minus_20deg . '"/>
</dataset>';
		$chart .= '</chart>';
		echo $chart;
	}

	public function national_utilization_report() {

	}

}
