<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class email_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> library('pagination');
		//LOADING HELPERS TO BECOME AVAILBALE IN ALL CONSTRUCTOR METHODS
		$this -> load -> helper(array('form', 'url'));

	}

	public function index() {
		$this -> view_data();
	}

	public function add_new() {
		$data['title'] = "Add New Email";
		$data['module_view'] = "add_email_view";
		$this -> base_params($data);
	}

	public function view_data() {
		$div = $this -> session -> userdata('user_group');
		//getting the session user group
		$div2 = $this -> session -> userdata('district_province_id');
		$data['emailsnsms'] = array();
		if ($div == 1) {
			$data['emailsnsms'] = Emails::getNational();
		}
		if ($div == 2) {
			$data['emailsnsms'] = Emails::getNational();
		}
		if ($div == 3) {
			$data['emailsnsms'] = Emails::getRegional($div2);
		}
		if ($div == 4) {
			$data['emailsnsms'] = Emails::getDistrict($div2);
		}

		$data['title'] = "Email and SMS Management";
		$data['module_view'] = "email_recipients_view";
		$this -> base_params($data);
	}

	public function edit_data($id) {
		$data['email'] = Emails::getEmail($id);
		$data['title'] = "Email-SMS Management::Edit Details";
		$data['module_view'] = "add_email_view";
		$this -> base_params($data);
	}

	//performs the update function
	public function save_changes($id) {

		$this -> load -> database();
		@$email = trim($this -> input -> post('mail'));
		@$number = trim($this -> input -> post('number'));
		@$recepient = trim($this -> input -> post('recepient'));
		@$stock = trim($this -> input -> post('combo1'));
		@$consumption = trim($this -> input -> post('combo2'));
		@$capacity = trim($this -> input -> post('combo3'));

		$querry = "UPDATE `emails` SET  `email` =  '$email', `recepient` = '$recepient', `number` = '$number', `stockout` = '$stock', `consumption` = '$consumption',	`coldchain` = '$capacity' WHERE  `emails`.`ID` ='$id'";
		$this -> db -> query($querry);

		redirect(email_Management);

	}

	public function show() {
		$data['view'] = "add_emailsms_view";
		$this -> base_params($data);
	}

	public function change_availability($code) {
		$data = Emails::setValidEmails($code);
		redirect("email_management");
	}

	public function change_inavailability($code) {
		$data = Emails::setInvalidEmails($code);
		redirect("email_management");
	}

	//Function that loads template the loads the email sucess view
	public function save() {
		$record_id = $this -> input -> post('record_id');
		//Check if we are in editing mode first; if so, retrieve the edited record. if not, create a new one!
		if (strlen($record_id) > 0) {
			$email = Emails::getEmail($record_id);
		} else {
			$email = new Emails();
		}
		$div = $this -> session -> userdata('user_group');
		//getting the session user group
		$div2 = $this -> session -> userdata('district_province_id');
		if ($div == 1) {
			$national = "1";
			$district = "0";
			$provincial = "0";

		}

		if ($div == 2) {
			$national = "1";
			$district = "0";
			$provincial = "0";

		}

		if ($div == 3) {
			$national = "0";
			$district = "0";
			$provincial = $div2;

		}

		if ($div == 4) {

			$provincial = "0";
			$national = "0";
			$district = $div2;
		}

		$email -> email = $this -> input -> post("email");
		$email -> provincial = $provincial;
		$email -> district = $district;
		$email -> national = $national;
		$email -> valid = "1";
		$email -> stockout = $this -> input -> post("combo1");
		$email -> consumption = $this -> input -> post("combo2");
		$email -> coldchain = $this -> input -> post("combo3");
		$email -> recepient = $this -> input -> post("rep");
		$email -> number = $this -> input -> post("number");
		//var_dump($this->input->post());
		//var_dump($email);
		$email -> save();
		redirect("email_management");
	}

	public function remove($id) {
		Emails::removeEmail($id);
		redirect('email_management/getEmailsList');
	}

	public function add($id) {
		Emails::addEmail($id);
		redirect('email_management/getEmailsList');
	}

	//hold the content of the template
	private function base_params($data) {
		$data['scripts'] = array("jquery-ui.js");
		$data['content_view'] = "admin_view";
		$data['quick_link'] = "email_management";
		$data['link'] = "system_administration";
		$this -> load -> view('template', $data);
	}

}
