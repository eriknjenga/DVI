<?php

class Vaccine_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
		if ($this -> session -> userdata('user_group') >= 2) {
			redirect("user_management");
		}
	}

	public function index() {
		$this -> view_vaccines();
	}

	public function new_vaccine() {
		$data['title'] = "Vaccine Management::Add New Vaccine";
		$data['module_view'] = "add_vaccine_view";
		$data['scripts'] = array("jquery-ui.js", "colorpicker/js/colorpicker.js");
		$data['styles'] = array("jquery-ui.css", "colorpicker/css/colorpicker.css");
		$data['administration'] = Vaccine_Administration::getAll();
		$data['formulations'] = Vaccine_Formulations::getAll();
		$data['fridge_compartments'] = Fridge_Compartments::getAll();
		$this -> base_params($data);
	}

	public function view_vaccines() {
		$data['vaccines'] = Vaccines::getAll();
		$data['title'] = "Vaccine Management::All Vaccines";
		$data['module_view'] = "view_vaccines_view";
		$this -> base_params($data);
	}

	public function change_availability($code, $availability) {
		$vaccine = Vaccines::getVaccine($code);
		$vaccine -> Active = $availability;
		$vaccine -> save();
		redirect("vaccine_management");
	}

	public function edit_vaccine($code) {
		$vaccine = Vaccines::getVaccine($code);
		$data['vaccine'] = $vaccine;
		$data['title'] = "Vaccine Management::Edit Vaccine Details";
		$data['module_view'] = "add_vaccine_view";
		$data['scripts'] = array("jquery-ui.js", "colorpicker/js/colorpicker.js");
		$data['styles'] = array("jquery-ui.css", "colorpicker/css/colorpicker.css");
		$data['administration'] = Vaccine_Administration::getAll();
		$data['formulations'] = Vaccine_Formulations::getAll();
		$data['fridge_compartments'] = Fridge_Compartments::getAll();
		$this -> base_params($data);
	}

	public function save_vaccine() {
		$vaccine_id = $this -> input -> post('vaccine_id');
		//Check if we are in editing mode first; if so, retrieve the edited record. if not, create a new one!
		if (strlen($vaccine_id) > 0) {
			$vaccine = Vaccines::getVaccine($vaccine_id);
		} else {
			$vaccine = new Vaccines();
		}

		$vaccine -> Name = $this -> input -> post("name");
		$vaccine -> Doses_Required = $this -> input -> post("doses_required");
		$vaccine -> Wastage_Factor = $this -> input -> post("wastage_factor");
		$vaccine -> Designation = $this -> input -> post("designation");
		$vaccine -> Formulation = $this -> input -> post("formulation");
		$vaccine -> Administration = $this -> input -> post("administration");
		$vaccine -> Presentation = $this -> input -> post("presentation");
		$vaccine -> Vaccine_Packed_Volume = $this -> input -> post("vaccine_packed_volume");
		$vaccine -> Diluents_Packed_Volume = $this -> input -> post("diluents_packed_volume");
		$vaccine -> Vaccine_Vial_Price = $this -> input -> post("vaccine_vial_price");
		$vaccine -> Vaccine_Dose_Price = $this -> input -> post("vaccine_dose_price");
		$vaccine -> Fridge_Compartment = $this -> input -> post("fridge_compartment");
		$vaccine -> Added_By = $this -> session -> userdata('user_id');
		$vaccine -> Timestamp = date('U');
		$vaccine -> Tray_Color = $this -> input -> post("tray_color");
		$vaccine -> save();
		redirect("vaccine_management");
	}

	private function base_params($data) {

		$data['quick_link'] = "vaccine_management";
		$data['link'] = "system_administration";
		$data['content_view'] = "admin_view";
		$this -> load -> view('template', $data);

	}

}
