<?php

class Report_Management extends MY_Controller {
	function __construct() {
		parent::__construct();

	}

	public function index() {
		$this -> view_report();
	}

	public function manage_recipients() {
		$data['title'] = "System Reports";
		$data['content_view'] = "reports_view";
		$data['quick_link'] = "manage_recipients";
		$data['report'] = "manage_recipients";
		$data['recipients'] = Email_Recipients::getAll();
		$this -> base_params($data);
	}

	public function view_report($report = "consumption") {
		$data['title'] = "System Reports";
		$data['content_view'] = "reports_view";
		if ($report == "consumption") {
			redirect("consumption_management");
		}
		if ($report == "issues") {
			redirect("vaccine_issues_management");
		}
		//Code for getting the Store summaries at the various vaccine store around the country
		if ($report == "store_summaries") {
			$data['quick_link'] = "store_summaries";
			$regions = Regions::getAllRegions();
			$vaccines = Vaccines::getAll_Minified();
			$national_values = array();
			$regional_values = array();
			$this_month = date('m');
			for ($month = 1; $month <= $this_month; $month++) {
				foreach ($vaccines as $vaccine) {
					$timestamp = date("U", mktime(0, 0, 0, $month, 1, date("Y")));
					$national_balance = Disbursements::getNationalPeriodBalance($vaccine -> id, $timestamp);
					$national_values[$month][$vaccine -> id] = $national_balance;
					//Get the balance at that time

					foreach ($regions as $region) {
						$regional_balance = Disbursements::getRegionalPeriodBalance($region -> id, $vaccine -> id, $timestamp);
						$regional_values[$month][$region -> id][$vaccine -> id] = $regional_balance;
						//Get the Balance at that time
					}
				}
			}
			$data['national_values'] = $national_values;
			$data['regional_values'] = $regional_values;
			$data['regional_stores'] = $regions;
		}

		//Code for getting the tally summaries for the various recipients
		
else if ($report == "store_tallies") {
			$post = $this -> input -> post();
			if ($post) {
				$login_level = $this -> session -> userdata('user_group');
				$vaccines = Vaccines::getAll_Minified();
				$from = strtotime($this -> input -> post('from'));
				$to = strtotime($this -> input -> post('to'));
				$this -> session -> set_userdata(array("store_tallies_from" => $from, "store_tallies_to" => $to));
				$items = 20;
				$order_by = "Quantity";
				$order = "DESC";
				$offset = 0;
				$district_or_region_id = $this -> session -> userdata('district_province_id');
				$tallies = null;
				if ($login_level == 1) {//National Level
					foreach ($vaccines as $vaccine) {
						$tallies[$vaccine -> id] = Disbursements::getNationalRecipientTally($vaccine -> id, $from, $to, $offset, $items, $order_by, $order);
					}
				}

				if ($login_level == 2) {//Regional Level
					foreach ($vaccines as $vaccine) {
						$tallies[$vaccine -> id] = Disbursements::getRegionalRecipientTally($district_or_region_id, $vaccine -> id, $from, $to, $offset, $items, $order_by, $order);
					}
				}
				$data['tallies'] = $tallies;
			}
			$data['quick_link'] = "store_tallies";
		}

		//Code for getting the Stock Movement
		else if ($report == "vaccine_movement") {
			$post = $this -> input -> post();
			if ($post) {
				$login_level = $this -> session -> userdata('user_group');
				$vaccines = Vaccines::getAll_Minified();
				$from = strtotime($this -> input -> post('from'));
				$to = strtotime($this -> input -> post('to'));
				$items = 20;
				$order_by = "Quantity";
				$order = "DESC";
				$offset = 0;
				$district_or_region_id = $this -> session -> userdata('district_province_id');
				$received = null;
				$issued = null;
				$beginning_balance = null;
				$current_balance = null;
				if ($login_level == 1) {//National Level
					foreach ($vaccines as $vaccine) {
						$received[$vaccine -> id] = Disbursements::getNationalReceiptsTotals($vaccine -> id, $from, $to);
						$issued[$vaccine -> id] = Disbursements::getNationalIssuesTotals($vaccine -> id, $from, $to);
						$beginning_balance[$vaccine -> id] = Disbursements::getNationalPeriodBalance($vaccine -> id, $from);
						$current_balance[$vaccine -> id] = Disbursements::getNationalPeriodBalance($vaccine -> id, $to);
					}
				}

				if ($login_level == 2) {//Regional Level
					/*foreach($vaccines as $vaccine){
					 $tallies[$vaccine->id] = Disbursements::getRegionalRecipientTally($district_or_region_id,$vaccine->id,$from,$to,$offset,$items,$order_by,$order);
					 }*/
				}

				$data['received'] = $received;
				$data['issued'] = $issued;
				$data['beginning_balance'] = $beginning_balance;
				$data['current_balance'] = $current_balance;
			}
			$data['quick_link'] = "vaccine_movement";
		}

		$data['report'] = $report;
		$this -> base_params($data);
	}

	public function export_store_tallies($vaccine, $vaccine_name) {
		$from = $this -> session -> userdata("store_tallies_from");
		$to = $this -> session -> userdata("store_tallies_to");
		$district_or_region_id = $this -> session -> userdata('district_province_id');
		$login_level = $this -> session -> userdata('user_group');

		$items = 20;
		$order_by = "Quantity";
		$order = "DESC";
		$offset = 0;

		$headers = "Period Tally For " . $vaccine_name . "\t\nFirst Issued\t Issued To \t Total Amount(Doses)\t\n";
		$data = "";
		if ($login_level == 1) {//National Level
			$tallies = Disbursements::getNationalRecipientTally($vaccine, $from, $to, $offset, $items, $order_by, $order);
		}

		if ($login_level == 2) {//Regional Level
			$tallies = Disbursements::getRegionalRecipientTally($district_or_region_id, $vaccine, $from, $to, $offset, $items, $order_by, $order);
		}

		foreach ($tallies as $tally) {
			$data .= $tally -> Date_Issued . "\t";
			$issued_to = "";
			if ($tally -> Issued_To_Region != null) {
				$issued_to = $tally -> Region_Issued_To -> name;
			} else if ($tally -> Issued_To_District != null) {
				$issued_to = $tally -> District_Issued_To -> name;
			}
			$data .= $issued_to . "\t" . $tally -> Quantity . "\n";

		}
		header("Content-type: application/vnd.ms-excel; name='excel'");
		header("Content-Disposition: filename=store_tallies_export.xls");
		// Fix for crappy IE bug in download.
		header("Pragma: ");
		header("Cache-Control: ");
		echo $headers . $data;
	}

	private function base_params($data) {
		$data['vaccines'] = Vaccines::getAll_Minified();
		$data['scripts'] = array("jquery-ui.js", "tab.js");
		$data['styles'] = array("jquery-ui.css", "tab.css");
		$data['link'] = "report_management";
		$this -> load -> view('template', $data);

	}

	public function save_recipient() {
		$names = $this -> input -> post("names");
		$emails = $this -> input -> post("emails");
		$existing_recipients = Email_Recipients::getAll();
		//First delete all the existing plans
		foreach ($existing_recipients as $existing_recipient) {
			$existing_recipient -> delete();
		}
		//Then add the new Plans
		$counter = 0;
		foreach ($names as $name) {
			if (strlen($name) > 2) {
				$recipient = new Email_Recipients();
				$recipient -> Name = $names[$counter];
				$recipient -> Email = $emails[$counter];
				$recipient -> save();
				$counter++;
			} else {
				continue;
			}
		}
		redirect("report_management");
	}

}
