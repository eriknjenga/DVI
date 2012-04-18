<?php

class Batch_Management extends MY_Controller {
	function __construct() {
		parent::__construct();

		if ($this -> session -> userdata('user_group') >= 2) {
			redirect("user_management");
		}
		$this -> load -> library('pagination');

	}

	public function index() {
		$this -> view_batches();
	}

	public function new_batch() {
		$data['title'] = "Stock Management::Add New Stock";
		$data['content_view'] = "add_batch_view";
		$data['quick_link'] = "new_batch";
		$this -> base_params($data);
	}

	public function view_batches($paged_vaccine = null, $offset = 0) {
		$default_offset = 0;
		$data['title'] = "Stock Management::All Batches";
		$data['content_view'] = "view_batches_view";
		$vaccines = Vaccines::getAll_Minified();
		$vaccine_plans = array();
		$items_per_page = 20;
		foreach ($vaccines as $vaccine) {
			//skip the vaccine that is currently being browsed through
			if ($vaccine -> id == $paged_vaccine) {
				continue;
			}
			$total_number = Batches::getTotalNumber($vaccine -> id);
			if ($total_number > $items_per_page) {
				$config['base_url'] = base_url() . "batch_management/view_batches/" . $vaccine -> id;
				$config['total_rows'] = $total_number;
				$config['per_page'] = $items_per_page;
				$config['uri_segment'] = 5;
				$config['num_links'] = 5;
				$this -> pagination -> initialize($config);
				$data['pagination'][$vaccine -> id] = $this -> pagination -> create_links();
			}
			$vaccine_plans[$vaccine -> id] = Provisional_Plan::getCurrentPlan($vaccine -> id);
			$batch_years[$vaccine -> id] = Batches::getDistinctYears($vaccine -> id);
			$batches[$vaccine -> id] = Batches::getVaccineBatches($vaccine -> id, $default_offset, $items_per_page);
		}
		//
		
		
		
		
		if ($paged_vaccine != null) {
		
			$data['paged_vaccine'] = $paged_vaccine;
			$total_number = Batches::getTotalNumber($paged_vaccine);

			if ($total_number > $items_per_page) {
				$config['base_url'] = base_url() . "batch_management/view_batches/" . $paged_vaccine;
				$config['total_rows'] = $total_number;
				$config['per_page'] = $items_per_page;
				$config['uri_segment'] = 4;
				$config['num_links'] = 5;
				$this -> pagination -> initialize($config);
				$data['pagination'][$paged_vaccine] = $this -> pagination -> create_links();
				 
				 
			}
			$vaccine_plans[$paged_vaccine] = Provisional_Plan::getCurrentPlan($paged_vaccine);
			$batch_years[$paged_vaccine] = Batches::getDistinctYears($paged_vaccine);
			$batches[$paged_vaccine] = Batches::getVaccineBatches($paged_vaccine, $offset, $items_per_page);

		}
		
		
		
		
		
		
		$data['vaccine_plans'] = $vaccine_plans;
		$data['batch_years'] = $batch_years;
		$data['batches'] = $batches;
		$this -> base_params($data);
	}

	public function save_batch($edit_id = false) {
		if ($this -> input -> post("submit")) {
			if ($this -> _submit_validate() === FALSE) {
				$data['title'] = "Stock Management::Add New Stock (Error)";
				$data['content_view'] = "add_batch_view";
				$this -> base_params($data);
			} else {
				if ($edit_id != false) {
					$batch = Batches::getBatch($edit_id);
					$batch = $batch[0];
					$disbursement = Disbursements::getBatchEntry($edit_id);
					$disbursement = $disbursement[0];
				} else if ($edit_id == false) {
					$batch = new Batches();
					$disbursement = new Disbursements();
				}

				$batch -> Vaccine_Id = $this -> input -> post("vaccine_id");
				$batch -> Batch_Number = $this -> input -> post("batch_number");
				$batch -> Expiry_Date = $this -> input -> post("expiry_date");
				$batch -> Manufacturing_Date = $this -> input -> post("manufacturing_date");
				$batch -> Manufacturer = $this -> input -> post("manufacturer");
				$batch -> Lot_Number = $this -> input -> post("lot_number");
				$batch -> Origin_Country = $this -> input -> post("origin_country");
				$batch -> Arrival_Date = $this -> input -> post("arrival_date");
				$batch -> Quantity = str_replace(",", "", $this -> input -> post("quantity"));
				$batch -> Timestamp = date("U");
				$batch -> Added_By = $this -> session -> userdata('user_id');
				$batch -> Year = date('Y');
				$batch -> save();

				$disbursement -> Batch_Id = $batch -> id;
				$disbursement -> Date_Issued = $this -> input -> post("arrival_date");
				$disbursement -> Quantity = str_replace(",", "", $this -> input -> post("quantity"));
				$disbursement -> Batch_Number = $this -> input -> post("batch_number");
				$disbursement -> Vaccine_Id = $this -> input -> post("vaccine_id");
				$disbursement -> Issued_To_National = "0";
				$disbursement -> Timestamp = date("U");
				$disbursement -> Added_By = $this -> session -> userdata('user_id');
				$disbursement -> Stock_At_Hand = Disbursements::getNationalPeriodBalance($this -> input -> post("vaccine_id"), date("U"));
				$disbursement -> Date_Issued_Timestamp = strtotime($this -> input -> post('arrival_date'));
				$disbursement -> save();
				redirect("batch_management");
			}
		}
	}

	private function _submit_validate() {
		// validation rules
		$this -> form_validation -> set_rules('quantity', 'Stock Quantity', 'trim|required|min_length[2]|max_length[50]');

		return $this -> form_validation -> run();
	}

	private function base_params($data) {
		$data['vaccines'] = Vaccines::getAll_Minified();
		$data['scripts'] = array("jquery-ui.js", "tab.js", "FusionCharts/FusionCharts.js");
		$data['styles'] = array("jquery-ui.css", "tab.css");
		$data['link'] = "batch_management";
		$this -> load -> view('template', $data);

	}

	public function delete_batch($id) {
		if (isset($id)) {
			$batches_array = Batches::getBatch($id);
			$batch = $batches_array[0];
			$batch -> delete();
			$disbursements_array = Disbursements::getBatchEntry($id);
			$disbursement = $disbursements_array[0];
			$disbursement -> delete();
			$data['deleted'] = true;
			redirect("batch_management");
		} else {
			redirect("batch_management");
		}
	}

	public function edit_batch($id) {
		$batch = Batches::getBatch($id);
		$data['batch'] = $batch[0];
		$data['title'] = "Stock Management::Edit Stock Entry";
		$data['content_view'] = "add_batch_view";
		$this -> base_params($data);
	}

	public function provisional_plan($vaccine) {
		$plan = Provisional_Plan::getCurrentPlan($vaccine);
		$data['plans'] = $plan;
		$data['vaccine'] = Vaccines::getVaccine($vaccine);
		$data['title'] = "Stock Management:: " . date('Y') . " Provisional Plan For " . $data['vaccine'] -> Name;
		$data['content_view'] = "plan_view";
		$this -> base_params($data);
	}

	public function save_plan() {
		$vaccine = $this -> input -> post("vaccine");
		$dates = $this -> input -> post("dates");
		$amounts = $this -> input -> post("amounts");
		$existing_plans = Provisional_Plan::getCurrentPlan($vaccine);
		//First delete all the existing plans
		foreach ($existing_plans as $existing_plan) {
			$existing_plan -> delete();
		}
		//Then add the new Plans
		$counter = 0;
		foreach ($dates as $date) {
			if (strlen($date) > 2) {
				$plan = new Provisional_Plan();
				$plan -> vaccine = $vaccine;
				$plan -> year = date('Y');
				$plan -> expected_date = $dates[$counter];
				$plan -> expected_amount = $amounts[$counter];
				$plan -> modified_by = $this -> session -> userdata('user_id');
				$plan -> save();
				$counter++;
			} else {
				continue;
			}
		}
		redirect("batch_management");
	}

}
