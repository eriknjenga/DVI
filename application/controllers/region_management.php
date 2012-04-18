<?php

class Region_Management extends MY_Controller {
	function __construct() {

		parent::__construct(); 
	}

	public function index() {
		$this -> view_list();
	}

	public function view_list() {  
		$regions = Regions::getAllRegions();
		$data['regions'] = $regions;
		$data['title'] = "Region Management::All The Regions";
		$data['module_view'] = "view_regions_view";
		$this -> base_params($data);
	}

	public function add() {
		$data['title'] = "Region Management::Add New Region";
		$data['quick_link'] = "new_region"; 
		$data['module_view'] = "add_region_view";
		$this -> base_params($data);
	}

	public function save() { 		
		$name = $this -> input -> post("name"); 
		$latitude = $this -> input -> post("latitude");
		$longitude = $this -> input -> post("longitude");
		$years = $this -> input -> post("years");
		$populations = $this -> input -> post("populations");
		$region_id = $this -> input -> post("region_id"); 
		//Check if we are in editing mode first; if so, retrieve the edited record. if not, create a new one!
		if(strlen($region_id)>0){
			
			 $region = Regions::getRegion($region_id); 
			 
			 //also, retrieve the region-population combinations for future comparisons. Delete the old ones
			 $region_populations = Regional_Populations::getAllForRegion($region_id);
			 foreach($region_populations as $region_population){ 
			 	$region_population->delete();
			 }
		}
		else{
			$region = new Regions();
		}
		
		$region -> name = $name; 
		$region -> latitude = $latitude;
		$region -> longitude = $longitude;
		$region -> save(); 
		$region_id = $region -> id;
		$counter = 0;  
		//Loop to get all the year-population combinations. Only add the ones that have actual values
		foreach ($years as $year) { 
			if (strlen($year)>1) { 
				$region_population = new Regional_Populations();
				$region_population -> name = $name;
				$region_population -> population = $populations[$counter];
				$region_population -> year = $years[$counter];
				$region_population -> region_id = $region_id;  
				$region_population -> save();
				$counter++;
			} else {
				$counter++;
				continue;
			}

		} 
		redirect("region_management");
	}

	public function change_availability($code, $availability) {
		$region = Regions::getRegion($code);
		$region -> disabled = $availability;
		$region -> save();
		redirect("region_management");
	}

	public function edit_region($code) {
		$region = Regions::getRegion($code);
		$data['region_populations'] = Regional_Populations::getAllForRegion($code);
		$data['region'] = $region;
		$data['title'] = "Region Management::Edit ".$region->name;
		$data['module_view'] = "add_region_view";
		//$data['quick_link'] = "new_region"; 
		$this -> base_params($data);
	}

	private function base_params($data) {
		$data['scripts'] = array("jquery-ui.js", "tab.js");
		$data['styles'] = array("jquery-ui.css", "tab.css");
		$data['link'] = "system_administration";
		$data['quick_link'] = "region_management";
		$data['content_view'] = "admin_view";
		$this -> load -> view('template', $data);

	}

}
