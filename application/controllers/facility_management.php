<?php

class Facility_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> library('pagination');
	}

	public function index() {
		$this -> view_list();
	}

	public function whole_list($offset = 0) {
		$items_per_page = 20;
		$number_of_facilities = Facilities::getTotalNumber();
		$facilities = Facilities::getPagedFacilities($offset, $items_per_page);
		if ($number_of_facilities > $items_per_page) {
			$config['base_url'] = base_url() . "facility_management/whole_list/";
			$config['total_rows'] = $number_of_facilities;
			$config['per_page'] = $items_per_page;
			$config['uri_segment'] = 3;
			$config['num_links'] = 5;
			$this -> pagination -> initialize($config);
			$data['pagination'] = $this -> pagination -> create_links();
		}

		$data['facilities'] = $facilities;
		$data['title'] = "Facility Management::All Facilities";
		$data['module_view'] = "view_facilities_view";
		$this -> new_base_params($data);
	}

	public function district_list($offset = 0) {
		$district = $this -> session -> userdata("district_province_id");
		$items_per_page = 20;
		$number_of_facilities = Facilities::getTotalNumber($district);
		$facilities = Facilities::getPagedFacilities($offset, $items_per_page, $district);
		if ($number_of_facilities > $items_per_page) {
			$config['base_url'] = base_url() . "facility_management/district_list/";
			$config['total_rows'] = $number_of_facilities;
			$config['per_page'] = $items_per_page;
			$config['uri_segment'] = 3;
			$config['num_links'] = 5;
			$this -> pagination -> initialize($config);
			$data['pagination'] = $this -> pagination -> create_links();
		}

		$data['facilities'] = $facilities;
		$data['title'] = "Facility Management::All Facilities";
		$data['content_view'] = "view_district_facilities";
		$data['link'] = "facility_management/district_list";
		$this -> base_params($data);
	}

	public function view_list() {
		$additional_facilities = new Additional_Facilities();
		$returned = $additional_facilities -> getExtraFacilities($this -> session -> userdata('district_province_id'));
		$data['facilities'] = $returned;
		$data['title'] = "Facility Management::My Additional Facilities";
		$data['content_view'] = "view_extra_facilities_view";
		$data['link'] = "facility_management";
		$this -> base_params($data);
	}

	public function add() {
		$data['title'] = "Facility Management::Add Extra Facility";
		$data['content_view'] = "add_extra_facility_view";
		$data['quick_link'] = "new_extra_facility";
		$this -> base_params($data);
	}

	public function new_facility() {
		$data['title'] = "Facility Management:: Add New Facility";
		$data['content_view'] = "add_facility_view";
		$data['link'] = "facility_management/district_list";
		$data['quick_link'] = "new_facility";
		$data['types'] = Facility_Types::getAll();
		$this -> base_params($data);
	}

	public function edit_facility($code) {
		$facility = Facilities::getFacility($code);
		$data['facility'] = $facility;
		$data['title'] = "Facility Management::Edit Details For -- " . $facility -> name;
		$data['content_view'] = "add_facility_view";
		$data['link'] = "facility_management/district_list";
		$data['fridges'] = Fridges::getAll();
		$data['types'] = Facility_Types::getAll();
		$data['facility_fridges'] = Facility_Fridges::getFacilityFridges($code);
		$this -> base_params($data);
	}

	public function search() {
		$search_term = $this -> input -> post('search');
		$data['facilities'] = Facilities::search($search_term);
		$data['search_term'] = $search_term;
		$data['title'] = "Facility Management::Click on a Facility";
		$data['content_view'] = "search_facilities_result_view";
		$this -> base_params($data);
	}

	//This method is for saving the details of additional facilities
	public function save($code) {
		$additional_facility = new Additional_Facilities();
		$exists = $additional_facility -> record_exists($this -> session -> userdata('district_province_id'), $code);
		if (!$exists) {
			$additional_facility -> District_Id = $this -> session -> userdata('district_province_id');
			$additional_facility -> Facility = $code;
			$additional_facility -> Added_By = $this -> session -> userdata('user_id');
			$additional_facility -> Timestamp = date('U');
			$additional_facility -> save();
		}
		redirect("facility_management");
	}

	//This method is for saving a new/edited facility
	public function save_details() {
		$district = $this -> session -> userdata("district_province_id");
		$facility_id = $this -> input -> post('facility_id');
		//Check if we are in editing mode first; if so, retrieve the edited record. if not, create a new one!
		if (strlen($facility_id) > 0) {
			$facility = Facilities::getFacility($facility_id);
			//Retrieve the fridges for this facility
			$fridges = Facility_Fridges::getFacilityFridges($facility_id);
			//Delete all these existing facility-fridge combinations
			foreach ($fridges as $fridge) {
				$fridge -> delete();
			}
		} else {
			$facility = new Facilities();
		}

		$facility -> facilitycode = $this -> input -> post('facilitycode');
		$facility -> name = $this -> input -> post('name');
		$facility -> facilitytype = $this -> input -> post('type');
		$facility -> district = $district;
		$facility -> email = $this -> input -> post('email');
		$facility -> phone = $this -> input -> post('phone');
		$facility -> save();
		$facility_id = $facility -> id;
		$fridges = $this -> input -> post('fridges');
		$counter = 0;
		foreach ($fridges as $fridge) {
			if ($fridge > 0) {
				$facility_fridge = new Facility_Fridges();
				$facility_fridge->Facility = $facility_id;
				$facility_fridge->Fridge = $fridge;
				$facility_fridge->Timestamp = date('U');
				$facility_fridge->save();
				$counter++;
			} else {
				$counter++;
				continue;
			}

		}

		redirect("facility_management/district_list");
	}

	public function remove($code) {
		$facility = Additional_Facilities::get_facility($this -> session -> userdata('district_province_id'), $code);
		$facility -> delete();
		redirect("facility_management");
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
		$data['quick_link'] = "facility_management";
		$data['link'] = "system_administration";
		$this -> load -> view('template', $data);

	}

}
