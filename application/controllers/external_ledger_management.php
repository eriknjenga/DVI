<?php

class External_Ledger_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> library('pagination');

	}

	public function view_ledger($type, $id, $paged_vaccine = null, $date_from = null, $date_to = null, $offset = 0, $default_offset = 0) {
		//get current district/region
		$district_or_province = $this -> session -> userdata('district_province_id');
		//get current level
		$identifier = $this -> session -> userdata('user_identifier');
		$dummy_identifier = "";
		//Determine if the user is trying to view the ledger for his/her own store
		//Type 0 means we are drilling down to a region
		if ($type == 0) {
			if ($identifier == "provincial_officer" && $district_or_province == $id) {
				redirect("disbursement_management/view_disbursements");
			}
			$dummy_identifier = "provincial_officer";
		}
		//Type 1 means we are drilling down to a district
		else if ($type == 1) {
			if ($identifier == "district_officer" && $district_or_province == $id) {
				redirect("disbursement_management/view_disbursements");
			}
			$dummy_identifier = "district_officer";
		}
		//Type 2 means we are drilling down to the whole country
		else if ($type == 2) {
			if ($identifier == "national_officer") {
				redirect("disbursement_management/view_disbursements");
			}
			$dummy_identifier = "national_officer";
		}
		$data['identifier'] = $dummy_identifier;
		$data['district_or_province'] = $id;
		
		$district_or_province = $id;
		//Now display the 'foreign' ledger  
		$to = $this -> input -> post('to');
		$from = $this -> input -> post('from');
		$store = $this -> input -> post('selected_store_id');
		$order_by = $this -> input -> post('order_by');
		$order = $this -> input -> post('order');
		$per_page = $this -> input -> post('per_page');
		if ($to == false) {
			$to = date("U", mktime(0, 0, 0, 1, 1, date("Y") + 1));
		} else if ($to == true) {
			$to = strtotime($to);
		}
		if ($from == false) {
			$from = date("U", mktime(0, 0, 0, 1, 1, date('Y')));
		} else if ($from == true) {
			$from = strtotime($from);
		}

		if ($date_from != null) {
			$from = $date_from;
		} else if ($date_to != null) {
			$to = $date_to;
		}
		

		//Check if the user has specified how many items he/she wants per page. If not, default to 10 items per page.
		if ($per_page > 0) {
			$this -> session -> set_userdata(array("from" => $from, "to" => $to, "per_page" => $per_page, "order_by" => $order_by, "order" => $order));
		} else {
			$temp = $this -> session -> userdata('per_page');
			if ($temp == false) {
				$this -> session -> set_userdata(array("from" => $from, "to" => $to, "per_page" => 10, "order_by" => "Unix_Timestamp(str_to_date(Date_Issued,'%m/%d/%Y'))", "order" => "DESC"));
			}
		}
		$items_per_page = $this -> session -> userdata('per_page');
		$order_by = $this -> session -> userdata('order_by');
		$order = $this -> session -> userdata('order');

		$region = 0;
		$district = 0;
		if ($store != null) {
			$split_parts = explode("_", $store);
			$type = $split_parts[0];
			$id = $split_parts[1];
			if ($type == "district") {
				$district = $id;
				$this -> session -> set_userdata(array("region" => ""));
				$this -> session -> set_userdata(array("district" => $district));
			} else if ($type == "region") {
				$region = $id;
				$this -> session -> set_userdata(array("district" => ""));
				$this -> session -> set_userdata(array("region" => $region));
			} else if ($type == "national") {
				$this -> session -> set_userdata(array("district" => ""));
				$this -> session -> set_userdata(array("region" => ""));
			}
		}
		$district = $this -> session -> userdata('district');
		$region = $this -> session -> userdata('region');

		$data['title'] = "External Vaccine Stock Ledger For The Period Between " . date('d/m/Y', $from) . " to " . date('d/m/Y', $to);
		$data['content_view'] = "view_external_ledger";
		$data['vaccines'] = Vaccines::getAll_Minified();
		$return_array = array();
		$balances = array();
		
		if ($type == 2) {//National Level

			foreach ($data['vaccines'] as $vaccine) {
				//skip the vaccine that is currently being browsed through
				if ($vaccine -> id == $paged_vaccine) {
					continue;
				}
				$total_disbursements = Disbursements::getTotalNationalDisbursements($vaccine -> id, $from, $to, $district, $region);

				if ($total_disbursements > $items_per_page) {
					$config['base_url'] = base_url() . "external_ledger_management/view_ledger/". $type . "/" . $id . "/"  . $vaccine -> id . "/" . $from . "/" . $to;
					$config['total_rows'] = $total_disbursements;
					$config['per_page'] = $items_per_page;
					$config['uri_segment'] = 9;
					$config['num_links'] = 5;
					$this -> pagination -> initialize($config);
					$data['pagination'][$vaccine -> id] = $this -> pagination -> create_links();
				}
				if ($order == "ASC") {
					$balances[$vaccine -> id] = Disbursements::getNationalPeriodBalance($vaccine -> id, $from);
				} else if ($order == "DESC") {
					$balances[$vaccine -> id] = Disbursements::getNationalPeriodBalance($vaccine -> id, $to);
				}

				$return_array[$vaccine -> id] = Disbursements::getNationalDisbursements($vaccine -> id, $from, $to, $default_offset, $items_per_page, $district, $region, $order_by, $order, $balances[$vaccine -> id]);

			}

			if ($paged_vaccine != null) {
				$data['paged_vaccine'] = $paged_vaccine;
				$total_disbursements = Disbursements::getTotalNationalDisbursements($paged_vaccine, $from, $to, $district, $region);

				if ($total_disbursements > $items_per_page) {
					$config['base_url'] = base_url() . "external_ledger_management/view_ledger/". $type . "/" . $id . "/"  . $paged_vaccine . "/" . $from . "/" . $to;
					$config['total_rows'] = $total_disbursements;
					$config['per_page'] = $items_per_page;
					$config['uri_segment'] = 8;
					$config['num_links'] = 5;
					$this -> pagination -> initialize($config);
					$data['pagination'][$paged_vaccine] = $this -> pagination -> create_links();
				}
				if ($order == "ASC") {
					$balances[$paged_vaccine] = Disbursements::getNationalPeriodBalance($paged_vaccine, $from);
				} else if ($order == "DESC") {
					$balances[$paged_vaccine] = Disbursements::getNationalPeriodBalance($paged_vaccine, $to);
				}
				$return_array[$paged_vaccine] = Disbursements::getNationalDisbursements($paged_vaccine, $from, $to, $offset, $items_per_page, $district, $region, $order_by, $order, $balances[$paged_vaccine]);

			}
		} else if ($type == 0) {//Regional Store Level
			foreach ($data['vaccines'] as $vaccine) {
				if ($vaccine -> id == $paged_vaccine) {
					continue;
				}
				$total_disbursements = Disbursements::getTotalRegionalDisbursements($district_or_province, $vaccine -> id, $from, $to);

				if ($total_disbursements > $items_per_page) {
					$config['base_url'] = base_url() . "external_ledger_management/view_ledger/". $type . "/" . $id . "/"  . $vaccine -> id . "/" . $from . "/" . $to;
					$config['total_rows'] = $total_disbursements;
					$config['per_page'] = $items_per_page;
					$config['uri_segment'] = 9;
					$config['num_links'] = 5;
					$this -> pagination -> initialize($config);
					$data['pagination'][$vaccine -> id] = $this -> pagination -> create_links();
				}
				if ($order == "ASC") {
					$balances[$vaccine -> id] = Disbursements::getRegionalPeriodBalance($district_or_province, $vaccine -> id, $from);
				} else if ($order == "DESC") {
					$balances[$vaccine -> id] = Disbursements::getRegionalPeriodBalance($district_or_province, $vaccine -> id, $to);
				}
				$return_array[$vaccine -> id] = Disbursements::getRegionalDisbursements($district_or_province, $vaccine -> id, $from, $to, $default_offset, $items_per_page, $district, $region, $order_by, $order, $balances[$vaccine -> id]);

			}

			if ($paged_vaccine != null) {
				$data['paged_vaccine'] = $paged_vaccine;
				$total_disbursements = Disbursements::getTotalRegionalDisbursements($district_or_province, $paged_vaccine, $from, $to);

				if ($total_disbursements > $items_per_page) {
					$config['base_url'] = base_url() . "external_ledger_management/view_ledger/". $type . "/" . $id . "/"  . $paged_vaccine. "/" . $from . "/" . $to;
					$config['total_rows'] = $total_disbursements;
					$config['per_page'] = $items_per_page;
					$config['uri_segment'] = 8;
					$config['num_links'] = 5;
					$this -> pagination -> initialize($config);
					$data['pagination'][$paged_vaccine] = $this -> pagination -> create_links();
				}
				if ($order == "ASC") {
					$balances[$paged_vaccine] = Disbursements::getRegionalPeriodBalance($district_or_province, $paged_vaccine, $from);
				} else if ($order == "DESC") {
					$balances[$paged_vaccine] = Disbursements::getRegionalPeriodBalance($district_or_province, $paged_vaccine, $to);
				}
				$return_array[$paged_vaccine] = Disbursements::getRegionalDisbursements($district_or_province, $paged_vaccine, $from, $to, $offset, $items_per_page, $district, $region, $order_by, $order, $balances[$paged_vaccine]);

			}
		} else if ($type == 1) {//District Store Level
			foreach ($data['vaccines'] as $vaccine) {
				if ($vaccine -> id == $paged_vaccine) {
					continue;
				}
				$total_disbursements = Disbursements::getTotalDistrictDisbursements($district_or_province, $vaccine -> id, $from, $to);

				if ($total_disbursements > $items_per_page) {
					$config['base_url'] = base_url() . "external_ledger_management/view_ledger/". $type . "/" . $id . "/"  . $vaccine -> id. "/" . $from . "/" . $to;
					$config['total_rows'] = $total_disbursements;
					$config['per_page'] = $items_per_page;
					$config['uri_segment'] = 9;
					$config['num_links'] = 5;
					$this -> pagination -> initialize($config);
					$data['pagination'][$vaccine -> id] = $this -> pagination -> create_links();
				}
				if ($order == "ASC") {
					$balances[$vaccine -> id] = Disbursements::getDistrictPeriodBalance($district_or_province, $vaccine -> id, $from);
				} else if ($order == "DESC") {
					$balances[$vaccine -> id] = Disbursements::getDistrictPeriodBalance($district_or_province, $vaccine -> id, $to);
				}
				$return_array[$vaccine -> id] = Disbursements::getDistrictDisbursements($district_or_province, $vaccine -> id, $from, $to, $default_offset, $items_per_page, $district, $order_by, $order, $balances[$vaccine -> id]);

			}

			if ($paged_vaccine != null) {
				$data['paged_vaccine'] = $paged_vaccine;
				$total_disbursements = Disbursements::getTotalDistrictDisbursements($district_or_province, $paged_vaccine, $from, $to);

				if ($total_disbursements > $items_per_page) {
					$config['base_url'] = base_url() . "external_ledger_management/view_ledger/". $type . "/" . $id . "/"  .$paged_vaccine. "/" . $from . "/" . $to;
					$config['total_rows'] = $total_disbursements;
					$config['per_page'] = $items_per_page;
					$config['uri_segment'] = 8;
					$config['num_links'] = 5;
					$this -> pagination -> initialize($config);
					$data['pagination'][$paged_vaccine] = $this -> pagination -> create_links();
				}
				if ($order == "ASC") {
					$balances[$paged_vaccine] = Disbursements::getDistrictPeriodBalance($district_or_province, $paged_vaccine, $from);
				} else if ($order == "DESC") {
					$balances[$paged_vaccine] = Disbursements::getDistrictPeriodBalance($district_or_province, $paged_vaccine, $to);
				}
				$return_array[$paged_vaccine] = Disbursements::getDistrictDisbursements($district_or_province, $paged_vaccine, $from, $to, $offset, $items_per_page, $district, $order_by, $order, $balances[$paged_vaccine]);

			}
		}
		$data['type'] = $type;
		$data['disbursements'] = $return_array;
		$data['balances'] = $balances;
		$data['stylesheets'] = array("pagination.css");
		//Get all the districts and regions so as to enable drilling down to a particular store
		$data['districts'] = Districts::getAllDistricts();
		$data['regions'] = Regions::getAllRegions();
		$this -> base_params_min($data);
	}
	public function reset_filters($type,$district_or_province) {
		$this -> session -> set_userdata(array('per_page' => ''));
		$this -> session -> set_userdata(array('order_by' => ''));
		$this -> session -> set_userdata(array('order' => ''));
		$this -> session -> set_userdata(array("region" => ""));
		$this -> session -> set_userdata(array("district" => "")); 
		$url = "external_ledger_management/view_ledger/".$type."/".$district_or_province;
		redirect($url);
	}
	private function base_params($data) {
		$data['vaccines'] = Vaccines::getAll_Minified();
		$data['scripts'] = array("jquery-ui.js", "tab.js");
		$data['styles'] = array("jquery-ui.css", "tab.css");
		$data['link'] = "disbursement_management";
		$this -> load -> view('template', $data);

	}
		private function base_params_min($data) {
		$data['scripts'] = array("jquery-ui.js", "tab.js");
		$data['styles'] = array("jquery-ui.css", "tab.css");
		$data['link'] = "disbursement_management";
		$this -> load -> view('template', $data);
	}

}
