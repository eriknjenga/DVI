<?php

class Fridge_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> library('pagination');
	}

	public function index() {
		$this -> listing();
	}

	public function listing($offset = 0) {
		$items_per_page = 20;
		$number_of_fridges = Fridges::getTotalNumber();
		$fridges = Fridges::getPagedFridges($offset, $items_per_page);
		if ($number_of_fridges > $items_per_page) {
			$config['base_url'] = base_url() . "fridge_management/listing/";
			$config['total_rows'] = $number_of_fridges;
			$config['per_page'] = $items_per_page;
			$config['uri_segment'] = 3;
			$config['num_links'] = 5;
			$this -> pagination -> initialize($config);
			$data['pagination'] = $this -> pagination -> create_links();
		}

		$data['fridges'] = $fridges;
		$data['title'] = "Fridge Management::All Fridge";
		$data['module_view'] = "view_fridges_view";
		$this -> new_base_params($data);
	}

	public function my_fridges($offset = 0) {
		//Retrieve the list of fridges for a store depending on whether it is the national store, a regional store or a district store.
		$identifier = $this -> session -> userdata('user_identifier');
		if ($identifier == "national_officer") {
			$fridges = National_Fridges::getNationalFridges();
		}
		if ($identifier == "provincial_officer") {
			$region = $this -> session -> userdata('district_province_id');
			$fridges = Regional_Fridges::getRegionFridges($region);
		}
		if ($identifier == "district_officer") {
			$district = $this -> session -> userdata('district_province_id');
			$fridges = District_Fridges::getDistrictFridges($district);
		}

		$data['fridges'] = $fridges;
		$data['title'] = "Fridge Management::All My Fridge";
		$data['content_view'] = "view_my_fridges_view";
		$data['link'] = "fridge_management/my_fridges";
		$this -> graph_base_params($data);
	}

	public function view_list() {
		$additional_facilities = new Additional_Facilities();
		$returned = $additional_facilities -> getExtraFacilities($this -> session -> userdata('district_province_id'));
		$data['facilities'] = $returned;
		$data['title'] = "Facility Management::All My Facilities";
		$data['content_view'] = "view_extra_facilities_view";
		$this -> base_params($data);
	}

	//This method is called when we want to add a new fridge to the system
	public function add() {
		$data['title'] = "Fridge Management::Add New Fridge";
		$data['module_view'] = "add_fridge_view";
		$data['quick_link'] = "new_fridge";
		$data['power_sources'] = Power_Sources::getAll();
		$data['item_types'] = Item_Types::getAll();
		$data['gas_types'] = Refrigerant_Gas_Types::getAll();
		$data['zones'] = Zones::getAll();
		$this -> new_base_params($data);
	}

	//This method is called when we want to assign new equipment to a particular store
	public function new_equipment() {
		$data['title'] = "Fridge Management::Add New Equipment to Store";
		$data['content_view'] = "add_new_equipment_view";
		$data['quick_link'] = "new_equipment";
		$data['fridges'] = Fridges::getAll();
		$data['link'] = "fridge_management/my_fridges";
		$this -> base_params($data);
	}

	//This method is to save the new equipment list that has been created
	public function save_equipment() {
		$identifier = $this -> session -> userdata('user_identifier');
		$fridges = $this -> input -> post('fridges');
		$counter = 0;
		if ($identifier == "national_officer") {
			foreach ($fridges as $fridge) {
				if ($fridge > 0) {
					$national_fridge = new National_Fridges();
					$national_fridge -> Fridge = $fridge;
					$national_fridge -> save();
					$counter++;
				} else {
					$counter++;
					continue;
				}
			}
		}
		if ($identifier == "provincial_officer") {
			$region = $this -> session -> userdata('district_province_id');
			foreach ($fridges as $fridge) {
				if ($fridge > 0) {
					$region_fridge = new Regional_Fridges();
					$region_fridge -> Region = $region;
					$region_fridge -> Fridge = $fridge;
					$region_fridge -> Timestamp = date('U');
					$region_fridge -> save();
					$counter++;
				} else {
					$counter++;
					continue;
				}
			}
		}
		if ($identifier == "district_officer") {
			$district = $this -> session -> userdata('district_province_id');
			foreach ($fridges as $fridge) {
				if ($fridge > 0) {
					$district_fridge = new District_Fridges();
					$district_fridge -> District = $district;
					$district_fridge -> Fridge = $fridge;
					$district_fridge -> Timestamp = date('U');
					$district_fridge -> save();
					$counter++;
				} else {
					$counter++;
					continue;
				}
			}
		}
		redirect("fridge_management/my_fridges");
	}

	//This function is to remove the selected equipment
	public function remove_equipment($id) {
		$identifier = $this -> session -> userdata('user_identifier');
		if ($identifier == "national_officer") {
			$equipment = National_Fridges::getFridge($id);
			$equipment -> delete();
		}
		if ($identifier == "provincial_officer") {
			$equipment = Regional_Fridges::getFridge($id);
			$equipment -> delete();
		}
		if ($identifier == "district_officer") {
			$equipment = District_Fridges::getFridge($id);
			$equipment -> delete();
		}
		redirect("fridge_management/my_fridges");
	}

	public function search() {
		$search_term = $this -> input -> post('search');
		$data['facilities'] = Facilities::search($search_term);
		$data['search_term'] = $search_term;
		$data['title'] = "Facility Management::Click on a Facility";
		$data['content_view'] = "search_facilities_result_view";
		$this -> base_params($data);
	}

	public function edit_fridge($code) {
		$fridge = Fridges::getFridge($code);
		$data['fridge'] = $fridge;
		$data['title'] = "Fridge Management::Edit Fridge Details";
		$data['module_view'] = "add_fridge_view";
		$data['quick_link'] = "new_fridge";
		$data['power_sources'] = Power_Sources::getAll();
		$data['item_types'] = Item_Types::getAll();
		$data['gas_types'] = Refrigerant_Gas_Types::getAll();
		$data['zones'] = Zones::getAll();
		$this -> new_base_params($data);
	}

	public function change_availability($code, $availability) {
		$fridge = Fridges::getFridge($code);
		$fridge -> Active = $availability;
		$fridge -> save();
		redirect("fridge_management");
	}

	public function save() {
		$fridge_id = $this -> input -> post('fridge_id');
		//Check if we are in editing mode first; if so, retrieve the edited record. if not, create a new one!
		if (strlen($fridge_id) > 0) {
			$fridge = Fridges::getFridge($fridge_id);
		} else {
			$fridge = new Fridges();
		}

		$fridge -> Item_Type = $this -> input -> post('item_type');
		$fridge -> Library_Id = $this -> input -> post('library_id');
		$fridge -> PQS = $this -> input -> post('pqs');
		$fridge -> Model_Name = $this -> input -> post('model');
		$fridge -> Manufacturer = $this -> input -> post('manufacturer');
		$fridge -> Power_Source = $this -> input -> post('power_source');
		$fridge -> Refrigerant_Gas_Type = $this -> input -> post('gas_type');
		$fridge -> Net_Vol_4deg = $this -> input -> post('net_vol_4deg');
		$fridge -> Net_Vol_Minus_20deg = $this -> input -> post('net_vol_minus_20deg');
		$fridge -> Freezing_Capacity = $this -> input -> post('freezing_capacity');
		$fridge -> Gross_Vol_4deg = $this -> input -> post('gross_vol_4deg');
		$fridge -> Gross_Vol_Minus_20deg = $this -> input -> post('gross_vol_minus_20deg');
		$fridge -> Price = $this -> input -> post('price');
		$fridge -> Elec_To_Run = $this -> input -> post('electricity');
		$fridge -> Gas_To_Run = $this -> input -> post('gas');
		$fridge -> Kerosene_To_Run = $this -> input -> post('kerosene');
		$fridge -> Zone = $this -> input -> post('zone');
		$fridge -> save();
		redirect("fridge_management");
	}

	public function remove($code) {
		$facility = Additional_Facilities::get_facility($this -> session -> userdata('district_province_id'), $code);
		$facility -> delete();
		redirect("facility_management");
	}

	private function graph_base_params($data) {
		$data['scripts'] = array("jquery-ui.js", "tab.js","FusionCharts/FusionCharts.js");
		$data['styles'] = array("jquery-ui.css", "tab.css");
		$this -> load -> view('template', $data);
	}

	private function base_params($data) {
		$data['scripts'] = array("jquery-ui.js", "tab.js");
		$data['styles'] = array("jquery-ui.css", "tab.css");
		$this -> load -> view('template', $data);
	}

	private function new_base_params($data) {
		$data['scripts'] = array("jquery-ui.js", "tab.js");
		$data['styles'] = array("jquery-ui.css", "tab.css");
		$data['content_view'] = "admin_view";
		$data['quick_link'] = "fridge_management";
		$data['link'] = "system_administration";
		$this -> load -> view('template', $data);

	}

}
