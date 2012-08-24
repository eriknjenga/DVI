<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Home_Controller extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	public function index() {
		$this -> new_dashboard();
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
		
		$data['vaccines'] = Vaccines::getAll();
		$data['styles'] = array("jquery-ui.css", "tab.css");
		$data['scripts'] = array("FusionCharts/FusionCharts.js","jquery-ui.js", "advanced_tabs.js");
		$data['link'] = "home";
		$this -> load -> view('template', $data);

	}

}
