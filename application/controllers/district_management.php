<?php

class District_Management extends MY_Controller {
	function __construct() {

		parent::__construct();
		$this -> load -> library('pagination');
	}

	public function index() {
		$this -> view_list();
	}

	public function view_list($offset = 0) {
		$items_per_page = 20;
		$number_of_districts = Districts::getTotalNumber();
		$districts = Districts::getPagedDistricts($offset, $items_per_page);
		if ($number_of_districts > $items_per_page) {
			$config['base_url'] = base_url() . "district_management/view_list/";
			$config['total_rows'] = $number_of_districts;
			$config['per_page'] = $items_per_page;
			$config['uri_segment'] = 3;
			$config['num_links'] = 5;
			$this -> pagination -> initialize($config);
			$data['pagination'] = $this -> pagination -> create_links();
		}

		$data['districts'] = $districts;
		$data['title'] = "District Management::All My Districts";
		$data['module_view'] = "view_districts_view";
		$this -> base_params($data);
	}

	public function add() {
		$data['title'] = "District Management::Add New District"; 
		$data['quick_link'] = "new_district";
		$data['module_view'] = "add_district_view";
		$data['quick_link'] = "new_district";
		$data['provinces'] = Provinces::getAllProvinces();
		$this -> base_params($data);
	}

	public function save() {
		$name = $this -> input -> post("name");
		$province = $this -> input -> post("province");
		$latitude = $this -> input -> post("latitude");
		$longitude = $this -> input -> post("longitude");
		$years = $this -> input -> post("years");
		$populations = $this -> input -> post("populations");
		$district_id = $this -> input -> post("district_id");

		//Check if we are in editing mode first; if so, retrieve the edited record. if not, create a new one!
		if (strlen($district_id) > 0) {
			$district = Districts::getDistrict($district_id);
			//also, retrieve the district-population combinations for future comparisons. Delete the old ones
			$district_populations = District_Populations::getAllForDistrict($district_id);
			//Delete all these existing combinations
			foreach ($district_populations as $district_population) {
				$district_population -> delete();
			}
		} else {
			$district = new Districts();
		}

		$district -> name = $name;
		$district -> province = $province;
		$district -> latitude = $latitude;
		$district -> longitude = $longitude;
		$district -> save();
		$district_id = $district -> id;
		$counter = 0;
		//Loop to get all the year-population combinations. Only add the ones that have actual values
		foreach ($years as $year) {
			if (strlen($year) > 1) {
				$district_population = new District_Populations();
				$district_population -> name = $name;
				$district_population -> population = $populations[$counter];
				$district_population -> year = $years[$counter];
				$district_population -> district_id = $district_id;
				$district_population -> save();
				$counter++;
			} else {
				$counter++;
				continue;
			}

		}
		redirect("district_management");
	}

	public function change_availability($code, $availability) {
		$district = Districts::getDistrict($code);
		$district -> disabled = $availability;
		$district -> save();
		redirect("district_management");
	}

	public function edit_district($code) {
		$district = Districts::getDistrict($code);
		$data['district_populations'] = District_Populations::getAllForDistrict($code);
		$data['district'] = $district;
		$data['title'] = "District Management::Edit " . $district -> name . " District";
		$data['module_view'] = "add_district_view";
		$data['quick_link'] = "new_district";
		$data['provinces'] = Provinces::getAllProvinces();
		$this -> base_params($data);
	}

	private function base_params($data) {
		$data['scripts'] = array("jquery-ui.js", "tab.js");
		$data['styles'] = array("jquery-ui.css", "tab.css");
		$data['quick_link'] = "district_management";
		$data['link'] = "system_administration";
		$data['content_view'] = "admin_view";
		$this -> load -> view('template', $data);

	}

}
