<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Regional_Statistics extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	public function index() {
		$this -> regional_statistics();
	}

	public function regional_statistics() {
		$data['regions'] = Regions::getAllRegions(); 
		$data['vaccines'] = Vaccines::getAll_Minified();
		$data['link'] = "home";
		$data['quick_link'] = "regional_statistics";
		$data['title'] = "Regional Analysis";
		$data['banner_text'] = "Regional Analysis";
		$data['content_view'] = "regional_analysis_view";
		$data['scripts'] = array("FusionCharts/FusionCharts.js", "jquery-ui.js", "advanced_tabs.js");
		$this -> load -> view("template", $data);
	}
 
}
