<?php

class System_Administration extends MY_Controller {
	function __construct() {
		parent::__construct();

	}

	public function index() {
		$this -> admin_view();
	}

	public function admin_view() {
		redirect("email_management");
	}

	private function base_params($data) {
		$data['scripts'] = array("jquery-ui.js", "tab.js");
		$data['styles'] = array("jquery-ui.css", "tab.css");
		$data['link'] = "system_administration";
		$this -> load -> view('template', $data);

	}

}
