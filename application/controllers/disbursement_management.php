<?php
class Disbursement_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> library('pagination');
	}

	public function index() {
		$this -> view_disbursements();
	}

	public function new_disbursement($id = null) {

		if ($id != null) {
			$disbursement = Disbursements::getDisbursement($id);
			$data['disbursement'] = $disbursement[0];
			$data['edit'] = true;
			$data['id'] = $id;
		}

		$districts = new Districts();
		$regions = new Regions();
		$facilities = new Facilities();
		$additional_facilities = new Additional_Facilities();
		$data['vaccines'] = Vaccines::getAll_Minified();
		$archive_date = date('U');
		$data['stock_balance'] = array();
		$district_or_region = $this -> session -> userdata('district_province_id');

		//Retrieve the user identifier from the session
		$identifier = $this -> session -> userdata('user_identifier');
		//Check if it's a provincial officer
		if ($identifier == 'provincial_officer') {
			foreach ($data['vaccines'] as $vaccine) {
				$data['stock_balance'][$vaccine -> id] = Disbursements::getRegionalPeriodBalance($district_or_region, $vaccine -> id, $archive_date);
			}
			$data['districts'] = $districts -> getAllDistricts();
			$data['regions'] = $regions -> getAllRegions();
		} else if ($identifier == 'district_officer') {
			foreach ($data['vaccines'] as $vaccine) {
				$data['stock_balance'][$vaccine -> id] = Disbursements::getDistrictPeriodBalance($district_or_region, $vaccine -> id, $archive_date);
			}
			$district_province = $districts -> getDistrictProvince($district_or_region);
			$data['districts'] = $districts -> getProvinceDistricts($district_province['province']);
			$data['facilities'] = $facilities -> getDistrictFacilities($district_or_region);
			$data['additional_facilities'] = $additional_facilities -> getExtraFacilities($district_or_region);
		} else if ($identifier == 'national_officer') {
			foreach ($data['vaccines'] as $vaccine) {
				$data['stock_balance'][$vaccine -> id] = Disbursements::getNationalPeriodBalance($vaccine -> id, $archive_date);
			}
			$data['districts'] = $districts -> getAllDistricts();
			$data['regions'] = $regions -> getAllRegions();
		}

		$data['title'] = "Disbursement Management::Disburse Vaccines";
		$data['content_view'] = "add_disbursement_view";
		$data['quick_link'] = "new_disbursement";
		$this -> base_params($data);
	}

	public function new_batch_disbursement() {
		$districts = new Districts();
		$regions = new Regions();
		$facilities = new Facilities();
		$additional_facilities = new Additional_Facilities();
		$data['vaccines'] = Vaccines::getAll_Minified();
		$archive_date = date('U');
		$data['stock_balance'] = array();
		$district_or_region = $this -> session -> userdata('district_province_id');

		//Retrieve the user identifier from the session
		$identifier = $this -> session -> userdata('user_identifier');
		//Check if it's a provincial officer
		if ($identifier == 'provincial_officer') {
			foreach ($data['vaccines'] as $vaccine) {
				$data['stock_balance'][$vaccine -> id] = Disbursements::getRegionalPeriodBalance($district_or_region, $vaccine -> id, $archive_date);
			}
			$data['districts'] = $districts -> getAllDistricts();
			$data['regions'] = $regions -> getAllRegions();
		} else if ($identifier == 'district_officer') {
			foreach ($data['vaccines'] as $vaccine) {
				$data['stock_balance'][$vaccine -> id] = Disbursements::getDistrictPeriodBalance($district_or_region, $vaccine -> id, $archive_date);
			}
			$district_province = $districts -> getDistrictProvince($district_or_region);
			$data['districts'] = $districts -> getProvinceDistricts($district_province['province']);
			$data['facilities'] = $facilities -> getDistrictFacilities($district_or_region);
			$data['additional_facilities'] = $additional_facilities -> getExtraFacilities($district_or_region);
		} else if ($identifier == 'national_officer') {
			foreach ($data['vaccines'] as $vaccine) {
				$data['stock_balance'][$vaccine -> id] = Disbursements::getNationalPeriodBalance($vaccine -> id, $archive_date);
			}
			$data['districts'] = $districts -> getAllDistricts();
			$data['regions'] = $regions -> getAllRegions();
		}

		$data['title'] = "Disbursement Management::Disburse Vaccines";
		$data['content_view'] = "add_batch_disbursement_view";
		$data['quick_link'] = "new_disbursement";
		$this -> base_params($data);
	}

	public function add_receipt($id = null) {
		if ($id != null) {
			$disbursement = Disbursements::getDisbursement($id);
			$data['disbursement'] = $disbursement[0];
			$data['edit'] = true;
			$data['id'] = $id;
		}

		$districts = new Districts();
		$regions = new Regions();
		$data['districts'] = $districts -> getAllDistricts();
		$data['regions'] = $regions -> getAllRegions(); 
		$data['title'] = "Disbursement Management::Add Stock Deliveries";
		$data['content_view'] = "add_receipt_view";
		$data['quick_link'] = "new_receipt";
		$this -> base_params($data);
	}

	public function stock_count($id = null) {
		if ($id != null) {
			$disbursement = Disbursements::getDisbursement($id);
			$data['disbursement'] = $disbursement[0];
			$data['edit'] = true;
			$data['id'] = $id;
		}
		$data['title'] = "Disbursement Management::Stock Count";
		$data['content_view'] = "add_stock_count";
		$data['quick_link'] = "stock_count";
		$this -> base_params($data);
	}

	public function view_receipts() {
		$data['title'] = "Disbursement Management::All Receipts From National Store";
		$data['content_view'] = "view_receipts_view";
		$identifier = $this -> session -> userdata('user_identifier');
		$district_region_id = $this -> session -> userdata('district_province_id');

		$data['receipts'] = Disbursements::getNationalReceipts($identifier, $district_region_id);
		$this -> base_params($data);
	}

	public function view_disbursements($paged_vaccine = null, $date_from = null, $date_to = null, $offset = 0, $default_offset = 0) {
		$district_or_province = $this -> session -> userdata('district_province_id');
		$this -> load -> helper('to_excel');
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

		$data['title'] = "Disbursement Management::Vaccine Stock Ledger For The Period Between " . date('d/m/Y', $from) . " to " . date('d/m/Y', $to);
		$data['content_view'] = "view_ledger_view";
		$data['vaccines'] = Vaccines::getAll_Minified();
		$return_array = array();
		$balances = array();

		//Retrieve the user identifier from the session
		$identifier = $this -> session -> userdata('user_identifier');

		if ($identifier == "national_officer") {//National Level

			foreach ($data['vaccines'] as $vaccine) {
				//skip the vaccine that is currently being browsed through
				if ($vaccine -> id == $paged_vaccine) {
					continue;
				}
				$total_disbursements = Disbursements::getTotalNationalDisbursements($vaccine -> id, $from, $to, $district, $region);

				if ($total_disbursements > $items_per_page) {
					$config['base_url'] = base_url() . "disbursement_management/view_disbursements/" . $vaccine -> id . "/" . $from . "/" . $to;
					$config['total_rows'] = $total_disbursements;
					$config['per_page'] = $items_per_page;
					$config['uri_segment'] = 7;
					$config['num_links'] = 5;
					$this -> pagination -> initialize($config);
					$data['pagination'][$vaccine -> id] = $this -> pagination -> create_links();
				}
				$balances[$vaccine -> id] = Disbursements::getNationalPeriodBalance($vaccine -> id, $from);
				$return_array[$vaccine -> id] = Disbursements::getNationalDisbursements($vaccine -> id, $from, $to, $default_offset, $items_per_page, $district, $region, $order_by, $order, $balances[$vaccine -> id]);

			}

			if ($paged_vaccine != null) {
				$data['paged_vaccine'] = $paged_vaccine;
				$total_disbursements = Disbursements::getTotalNationalDisbursements($paged_vaccine, $from, $to, $district, $region);

				if ($total_disbursements > $items_per_page) {
					$config['base_url'] = base_url() . "disbursement_management/view_disbursements/" . $paged_vaccine . "/" . $from . "/" . $to;
					$config['total_rows'] = $total_disbursements;
					$config['per_page'] = $items_per_page;
					$config['uri_segment'] = 6;
					$config['num_links'] = 5;
					$this -> pagination -> initialize($config);
					$data['pagination'][$paged_vaccine] = $this -> pagination -> create_links();
				}
				$balances[$paged_vaccine] = Disbursements::getNationalPeriodBalance($paged_vaccine, $from);
				$return_array[$paged_vaccine] = Disbursements::getNationalDisbursements($paged_vaccine, $from, $to, $offset, $items_per_page, $district, $region, $order_by, $order, $balances[$paged_vaccine]);

			}
		} else if ($identifier == "provincial_officer") {//Regional Store Level
			foreach ($data['vaccines'] as $vaccine) {
				if ($vaccine -> id == $paged_vaccine) {
					continue;
				}
				$total_disbursements = Disbursements::getTotalRegionalDisbursements($district_or_province, $vaccine -> id, $from, $to, $district, $region);

				if ($total_disbursements > $items_per_page) {
					$config['base_url'] = base_url() . "disbursement_management/view_disbursements/" . $vaccine -> id . "/" . $from . "/" . $to;
					$config['total_rows'] = $total_disbursements;
					$config['per_page'] = $items_per_page;
					$config['uri_segment'] = 7;
					$config['num_links'] = 5;
					$this -> pagination -> initialize($config);
					$data['pagination'][$vaccine -> id] = $this -> pagination -> create_links();
				}
				$balances[$vaccine -> id] = Disbursements::getRegionalPeriodBalance($district_or_province, $vaccine -> id, $from);
				$return_array[$vaccine -> id] = Disbursements::getRegionalDisbursements($district_or_province, $vaccine -> id, $from, $to, $default_offset, $items_per_page, $district, $region, $order_by, $order, $balances[$vaccine -> id]);

			}

			if ($paged_vaccine != null) {
				$data['paged_vaccine'] = $paged_vaccine;
				$total_disbursements = Disbursements::getTotalRegionalDisbursements($district_or_province, $paged_vaccine, $from, $to, $district, $region);

				if ($total_disbursements > $items_per_page) {
					$config['base_url'] = base_url() . "disbursement_management/view_disbursements/" . $paged_vaccine . "/" . $from . "/" . $to;
					$config['total_rows'] = $total_disbursements;
					$config['per_page'] = $items_per_page;
					$config['uri_segment'] = 6;
					$config['num_links'] = 5;
					$this -> pagination -> initialize($config);
					$data['pagination'][$paged_vaccine] = $this -> pagination -> create_links();
				}
				$balances[$paged_vaccine] = Disbursements::getRegionalPeriodBalance($district_or_province, $paged_vaccine, $from);
				$return_array[$paged_vaccine] = Disbursements::getRegionalDisbursements($district_or_province, $paged_vaccine, $from, $to, $offset, $items_per_page, $district, $region, $order_by, $order, $balances[$paged_vaccine]);

			}
		} else if ($identifier == "district_officer") {//District Store Level
			foreach ($data['vaccines'] as $vaccine) {
				if ($vaccine -> id == $paged_vaccine) {
					continue;
				}
				$total_disbursements = Disbursements::getTotalDistrictDisbursements($district_or_province, $vaccine -> id, $from, $to, $district);

				if ($total_disbursements > $items_per_page) {
					$config['base_url'] = base_url() . "disbursement_management/view_disbursements/" . $vaccine -> id . "/" . $from . "/" . $to;
					$config['total_rows'] = $total_disbursements;
					$config['per_page'] = $items_per_page;
					$config['uri_segment'] = 7;
					$config['num_links'] = 5;
					$this -> pagination -> initialize($config);
					$data['pagination'][$vaccine -> id] = $this -> pagination -> create_links();
				}
				$balances[$vaccine -> id] = Disbursements::getDistrictPeriodBalance($district_or_province, $vaccine -> id, $from);
				$return_array[$vaccine -> id] = Disbursements::getDistrictDisbursements($district_or_province, $vaccine -> id, $from, $to, $default_offset, $items_per_page, $district, $order_by, $order, $balances[$vaccine -> id]);

			}

			if ($paged_vaccine != null) {
				$data['paged_vaccine'] = $paged_vaccine;
				$total_disbursements = Disbursements::getTotalDistrictDisbursements($district_or_province, $paged_vaccine, $from, $to, $district);

				if ($total_disbursements > $items_per_page) {
					$config['base_url'] = base_url() . "disbursement_management/view_disbursements/" . $paged_vaccine . "/" . $from . "/" . $to;
					$config['total_rows'] = $total_disbursements;
					$config['per_page'] = $items_per_page;
					$config['uri_segment'] = 6;
					$config['num_links'] = 5;
					$this -> pagination -> initialize($config);
					$data['pagination'][$paged_vaccine] = $this -> pagination -> create_links();
				}
				$balances[$paged_vaccine] = Disbursements::getDistrictPeriodBalance($district_or_province, $paged_vaccine, $from);
				$return_array[$paged_vaccine] = Disbursements::getDistrictDisbursements($district_or_province, $paged_vaccine, $from, $to, $offset, $items_per_page, $district, $order_by, $order, $balances[$paged_vaccine]);

			}
		}

		$data['disbursements'] = $return_array;
		$data['balances'] = $balances;
		$data['stylesheets'] = array("pagination.css");
		//Get all the districts and regions so as to enable drilling down to a particular store
		$data['districts'] = Districts::getAllDistricts();
		$data['regions'] = Regions::getAllRegions();
		$this -> base_params_min($data);
	}

	public function save($edit = null) {
		if ($edit != null) {
			$disbursements = Disbursements::getDisbursementObject($edit);
			$disbursement = $disbursements[0];
		} else {
			$disbursement = new Disbursements();
		}
		$disbursement -> Date_Issued = $this -> input -> post('date_issued');
		$disbursement -> Quantity = $this -> input -> post('doses');
		$disbursement -> Batch_Number = $this -> input -> post('batch_number');
		$disbursement -> Stock_At_Hand = $this -> input -> post('stock_at_hand');
		$disbursement -> Voucher_Number = $this -> input -> post('voucher_number');
		$disbursement -> Vaccine_Id = $this -> input -> post('vaccine_id');
		$disbursement -> Timestamp = date('U');
		$disbursement -> Added_By = $this -> session -> userdata('user_id');
		$disbursement -> Date_Issued_Timestamp = strtotime($this -> input -> post('date_issued'));

		$issued_to_id = $this -> input -> post('issued_to_id');
		$split_parts = explode("_", $issued_to_id);
		$type = $split_parts[0];
		$id = $split_parts[1];
		if ($type == "district") {
			$disbursement -> Issued_To_District = $id;
		} else if ($type == "region") {
			$disbursement -> Issued_To_Region = $id;
		} else if ($type == "facility") {
			$disbursement -> Issued_To_Facility = $id;
		}

		$identifier = $this -> session -> userdata('user_identifier');
		if ($identifier == "national_officer") {
			$disbursement -> Issued_By_National = "0";
			$disbursement -> Owner = "N0";
		} else if ($identifier == "provincial_officer") {
			$disbursement -> Issued_By_Region = $this -> session -> userdata('district_province_id');
			$disbursement -> Owner = "R" . $this -> session -> userdata('district_province_id');
		} else if ($identifier == "district_officer") {
			$disbursement -> Issued_By_District = $this -> session -> userdata('district_province_id');
			$disbursement -> Owner = "D" . $this -> session -> userdata('district_province_id');
		}
		$disbursement -> save();

		if ($edit != null) {
			redirect("disbursement_management");
		} else {
			redirect("disbursement_management/new_disbursement");
		}
	}

	public function save_receipt($edit = null) {
		if ($edit != null) {
			$disbursements = Disbursements::getDisbursementObject($edit);
			$disbursement = $disbursements[0];
		} else {
			$disbursement = new Disbursements();
		}
		$disbursement -> Date_Issued = $this -> input -> post('date_received');
		$disbursement -> Quantity = $this -> input -> post('doses');
		$disbursement -> Batch_Number = $this -> input -> post('batch_number');
		$disbursement -> Voucher_Number = $this -> input -> post('voucher_number');
		$disbursement -> Vaccine_Id = $this -> input -> post('vaccine_id');
		$disbursement -> Timestamp = date('U');
		$disbursement -> Added_By = $this -> session -> userdata('user_id');
		$disbursement -> Date_Issued_Timestamp = strtotime($this -> input -> post('date_received'));

		$identifier = $this -> session -> userdata('user_identifier');
		if ($identifier == "provincial_officer") {
			$disbursement -> Issued_To_Region = $this -> session -> userdata('district_province_id');
			$disbursement -> Owner = "R" . $this -> session -> userdata('district_province_id');
		} else if ($identifier == "district_officer") {
			$disbursement -> Issued_To_District = $this -> session -> userdata('district_province_id');
			$disbursement -> Owner = "D" . $this -> session -> userdata('district_province_id');
		}

		$issued_to_id = $this -> input -> post('received_from_id');
		$split_parts = explode("_", $issued_to_id);
		$type = $split_parts[0];
		$id = $split_parts[1];
		if ($type == "district") {
			$disbursement -> Issued_By_District = $id;
		} else if ($type == "region") {
			$disbursement -> Issued_By_Region = $id;
		} else if ($type == "national") {
			$disbursement -> Issued_By_National = "0";
		}
		$disbursement -> save();
		redirect("disbursement_management");
	}

	public function save_stock_count($edit = null) {
		if ($edit != null) {
			$disbursements = Disbursements::getDisbursementObject($edit);
			$disbursement = $disbursements[0];
		} else {
			$disbursement = new Disbursements();
		}
		$disbursement -> Date_Issued = $this -> input -> post('date_received');
		$disbursement -> Total_Stock_Balance = $this -> input -> post('doses');
		$disbursement -> Vaccine_Id = $this -> input -> post('vaccine_id');
		$disbursement -> Timestamp = date('U');
		$disbursement -> Added_By = $this -> session -> userdata('user_id');
		$disbursement -> Date_Issued_Timestamp = strtotime($this -> input -> post('date_received'));
		$identifier = $this -> session -> userdata('user_identifier');
		if ($identifier == "national_officer") {
			$disbursement -> Owner = "N0";
		} else if ($identifier == "provincial_officer") {
			$disbursement -> Owner = "R" . $this -> session -> userdata('district_province_id');
		} else if ($identifier == "district_officer") {
			$disbursement -> Owner = "D" . $this -> session -> userdata('district_province_id');
		}
		$disbursement -> save();
		redirect("disbursement_management");
	}

	public function drill_down($type, $id) {

		$to = $this -> input -> post('to');
		$from = $this -> input -> post('from');

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

		$data['type'] = $type;
		$data['id'] = $id;
		$data['title'] = "Disbursement Management::Receipts Log For The Period Between " . date('d/m/Y', $from) . " to " . date('d/m/Y', $to);
		$data['content_view'] = "view_receipts_view";
		$data['vaccines'] = Vaccines::getAll_Minified();
		$return_array = array();
		$current_stock = array();
		$population = 0;
		$year = date('Y');
		$balances = array();
		$archive_date = date('U');
		//Type 0 means we are drilling down to a region
		if ($type == 0) {
			$data['recipient'] = Regions::getRegionName($id);
			$data['type'] = 0;
			$data['store_id'] = $id;
			foreach ($data['vaccines'] as $vaccine) {
				$return_array[$vaccine -> id] = Disbursements::getRegionalReceipts($id, $vaccine -> id, $from, $to);
				$current_stock[$vaccine -> id] = Disbursements::getRegionalPeriodBalance($id, $vaccine -> id, $archive_date);
			}
			$population = Regional_Populations::getRegionalPopulation($id, $year);
		}
		//Type 1 means we are drilling down to a district
		else if ($type == 1) {
			$data['recipient'] = Districts::getDistrictName($id);
			$data['type'] = 1;
			$data['store_id'] = $id;
			foreach ($data['vaccines'] as $vaccine) {
				$return_array[$vaccine -> id] = Disbursements::getDistrictReceipts($id, $vaccine -> id, $from, $to);
				//$current_stock[$vaccine->id] = Disbursements::getDistrictStockAtHand($id,$vaccine->id);
				$current_stock[$vaccine -> id] = Disbursements::getDistrictPeriodBalance($id, $vaccine -> id, $archive_date);

			}
			$population = District_Populations::getDistrictPopulation($id, $year);

		}
		//Type 2 means we are drilling down to the whole country
		if ($type == 2) {
			$data['recipient'] = "National Store";
			$data['type'] = 2;
			$data['store_id'] = "0";
			foreach ($data['vaccines'] as $vaccine) {
				$return_array[$vaccine -> id] = Disbursements::getNationalReceived($vaccine -> id, $from, $to);
				$current_stock[$vaccine -> id] = Disbursements::getNationalPeriodBalance($vaccine -> id, $archive_date);
			}
			$population = Regional_Populations::getNationalPopulation($year);
		}

		$data['population'] = $population;
		$data['disbursements'] = $return_array;
		$data['current_stocks'] = $current_stock;
		$this -> base_params_min_graph($data);
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

	private function base_params_min_graph($data) {
		$data['scripts'] = array("jquery-ui.js", "tab.js", "FusionCharts/FusionCharts.js");
		$data['styles'] = array("jquery-ui.css", "tab.css");
		$data['link'] = "disbursement_management";
		$this -> load -> view('template', $data);
	}

	public function export($vaccine) {
		//Retrieve the user identifier
		$level = $this -> session -> userdata('user_identifier');
		$from = $this -> session -> userdata('from');
		$to = $this -> session -> userdata('to');
		if ($to == false) {
			$to = date("U", mktime(0, 0, 0, 1, 1, date("Y") + 1));
		}
		if ($from == false) {
			$from = date("U", mktime(0, 0, 0, 1, 1, date('Y')));
		}

		$offset = 0;
		$items_per_page = 100;
		$district = $this -> session -> userdata('district');
		$region = $this -> session -> userdata('region');
		$order_by = $this -> session -> userdata('order_by');
		$order = $this -> session -> userdata('order');
		$origin_region = $this -> session -> userdata('district_province_id');
		$disbursements = null;

		$data = null;
		if ($level == "national_officer") {//National Level
			$balance = Disbursements::getNationalPeriodBalance($vaccine, $from);
			$disbursements = Disbursements::getNationalDisbursements($vaccine, $from, $to, $offset, $items_per_page, $district, $region, $order_by, $order, $balance);
		} else if ($level == "provincial_officer") {//Regional Level
			$balance = Disbursements::getRegionalPeriodBalance($origin_region, $vaccine, $from);
			$disbursements = Disbursements::getRegionalDisbursements($origin_region, $vaccine, $from, $to, $offset, $items_per_page, $district, $region, $order_by, $order, $balance);
		}

		$reducing_balance = $balance;
		$headers = "Balance From Previous Period: " . $balance . "\t\nDate Issued\t Vaccines To/From \t Amount Received\t Amount Issued \tStore Balance\t Voucher Number\t Batch Number\t Vaccine Expiry Date\t Recorded By\t\n";
		foreach ($disbursements as $disbursement) {
			$data .= $disbursement -> Date_Issued . "\t";
			//If the vaccines were issued to a Region, display the name
			if ($disbursement -> Issued_To_Region != null && $disbursement -> Issued_To_Region != $origin_region) {
				$data .= $disbursement -> Region_Issued_To -> name . "\t";
				$data .= " \t" . $disbursement -> Quantity . "\t";
			}
			//If the vaccines were received from a region (apart from ourselves ofcourse) display the name
			if ($disbursement -> Issued_By_Region != null && $disbursement -> Issued_By_Region != $origin_region) {
				$data .= $disbursement -> Region_Issued_By -> name . "\t";
				$data .= $disbursement -> Quantity . "\t\t";
			}

			//If the vaccines were issued to a Region, display the name
			if ($disbursement -> Issued_To_District != null) {
				$data .= $disbursement -> District_Issued_To -> name . "\t";
				$data .= " \t" . $disbursement -> Quantity . "\t";
			}

			//If the vaccines were issued to the Central store, Display UNICEF as the source
			if ($disbursement -> Issued_To_National == "0") {
				$data .= "UNICEF\t";
				$data .= $disbursement -> Quantity . "\t\t";
			}

			//If the vaccines were issued by Central store and if this is not the national store, Display National Store as the source
			if ($disbursement -> Issued_By_National == "0" && $level != "national_officer") {
				$data .= "Central Vaccine Stores\t";
				$data .= $disbursement -> Quantity . "\t\t";
			}
			//If no source is specified, display the physical stock count
			if ($disbursement -> Issued_To_Region == '' && $disbursement -> Issued_To_National == '' && $disbursement -> Issued_To_District == '') {
				$data .= "Physical Stock Count\t";
				$data .= "\t\t";
			}
			$data .= $disbursement -> Total_Stock_Balance . "\t";
			$data .= $disbursement -> Voucher_Number . "\t";
			$data .= $disbursement -> Batch_Number . "\t";
			$data .= $disbursement -> Batch -> Expiry_Date . "\t";
			$data .= $disbursement -> User -> Full_Name . "\t";

			$data .= "\n";
		}

		header("Content-type: application/vnd.ms-excel; name='excel'");
		header("Content-Disposition: filename=export.xls");
		// Fix for crappy IE bug in download.
		header("Pragma: ");
		header("Cache-Control: ");
		echo $headers . $data;

	}

	public function delete_disbursement($id) {
		$disbursements_array = Disbursements::getDisbursementObject($id);
		$disbursement = $disbursements_array[0];
		$disbursement -> delete();
		$data['deleted'] = true;
		redirect("disbursement_management");
	}

	public function reset_filters() {
		$this -> session -> set_userdata(array('per_page' => ''));
		$this -> session -> set_userdata(array('order_by' => ''));
		$this -> session -> set_userdata(array('order' => ''));
		$this -> session -> set_userdata(array("region" => ""));
		$this -> session -> set_userdata(array("district" => ""));
		redirect("disbursement_management");
	}

	public function correct_date_timestamps() {
		$disbursements = Disbursements::getAll();
		foreach ($disbursements as $disbursement) {
			if ($disbursement -> Date_Issued_Timestamp == null) {
				echo $disbursement -> Date_Issued . " changes to " . strtotime($disbursement -> Date_Issued) . " and back to " . date("m/d/y", strtotime($disbursement -> Date_Issued)) . "<br>";
				$disbursement -> Date_Issued_Timestamp = strtotime($disbursement -> Date_Issued);
				$disbursement -> save();
			}

			//
		}
	}

}
