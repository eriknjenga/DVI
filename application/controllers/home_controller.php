<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Home_Controller extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	public function index() {
		$this -> platform_home();
	}

	public function platform_home() {
		//Check if the user is already logged in and if so, take him to their home page. Else, display the platform home page.
		$user_id = $this -> session -> userdata('user_id');
		if (strlen($user_id) > 0) {
			redirect("home_controller/new_dashboard");
		}
		$this -> load -> database();
		//Retrieve only the districts in the mfl
		$sql = "SELECT distinct district,d.ID,d.name FROM `facilities` f left join districts d on f.district = d.id order by name";
		$query = $this -> db -> query($sql);
		$data['districts'] = $query -> result_array(); 
		$data['current'] = "home_controller";
		$data['title'] = "System Dashboard";
		$data['banner_text'] = "System Dashboard";
		$data['content_view'] = "national_immunization_dashboard_view";
		$data['scripts'] = array("FusionCharts/FusionCharts.js", "jquery-ui.js", "tab.js");
		$this -> load -> view("platform_template", $data);
	}

	public function dashboard($dashboard = "country_stock_view") {
		$year = date('Y');
		$data['title'] = "Home Page::Dashboards";
		$data['content_view'] = "home_view";
		$data['vaccines'] = Vaccines::getAll();
		$data['styles'] = array("jquery-ui.css", "tab.css");
		$data['script_urls'] = array("http://maps.google.com/maps/api/js?sensor=false");
		$data['scripts'] = array("FusionCharts/FusionCharts.js", "markerclusterer/src/markerclusterer.js", "markerclusterer/src/jsapi.js", "jquery-ui.js", "tab.js");
		$data['dashboard'] = $dashboard;
		$from = date('U');

		$national_balances = array();
		$regional_balances = array();
		$regional_stores = Regions::getAllRegions();
		//Get Statistics for each of the vaccines.
		foreach ($data['vaccines'] as $vaccine) {
			$national_balances[$vaccine -> id] = array(Disbursements::getNationalPeriodBalance($vaccine -> id, $from), Regional_Populations::getNationalPopulation(date('Y')));
			foreach ($regional_stores as $regional_store) {
				$regional_balances[$vaccine -> id][$regional_store -> id] = array(Disbursements::getRegionalPeriodBalance($regional_store -> id, $vaccine -> id, $from), Regional_Populations::getRegionalPopulation($regional_store -> id, date('Y')));
			}
		}
		$data['national_stocks'] = $national_balances;
		$data['regional_stocks'] = $regional_balances;
		$data['regional_stores'] = $regional_stores;

		$data['link'] = "home";
		$this -> load -> view('template', $data);
	}

	public function new_dashboard() {
		$identifier = $this -> session -> userdata('user_identifier');
		if ($this -> session -> userdata('user_identifier') == 'national_officer') {
			$data['content_view'] = "national_dashboard_view";
		}
		if ($this -> session -> userdata('user_identifier') == 'district_officer') {
			$data['content_view'] = "district_dashboard_view";
		}
		if ($this -> session -> userdata('user_identifier') == 'provincial_officer') {
			$data['content_view'] = "regional_dashboard_view";
		}

		$year = date('Y');
		$data['title'] = "System Dashboard";

		$data['vaccines'] = Vaccines::getAll_Minified();
		$data['styles'] = array("jquery-ui.css", "tab.css");
		$data['scripts'] = array("FusionCharts/FusionCharts.js", "jquery-ui.js", "advanced_tabs.js");
		$data['link'] = "home";
		$this -> load -> view('template', $data);

	}

}
