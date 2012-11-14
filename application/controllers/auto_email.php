<?php
ob_start();
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class auto_email extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> library('pagination');
		//LOADING HELPERS TO BECOME AVAILBALE IN ALL CONSTRUCTOR METHODS
		$this -> load -> helper(array('form', 'url', 'file'));
		//$this->data="";

	}

	public function index() {
		$from = date('Y-m-d', strtotime('-30 days'));
		$to = date("Y-m-d");
		$this -> printReport($from, $to);

	}

	public function echoTitles() {
		return "<tr><th>Vaccine</th><th>Opening Stock</th><th>Total Receipts</th><th>Total Issued</th><th>Adjustments</th><th>Closing Stock</th><th>MOS Balance</th><th>Date of last physical count</th></tr>";
	}

	public function printReport($from, $to) {
		$this -> load -> database();
		$user_groups = User_groups::getAllGroups();
		foreach ($user_groups as $user_group) {

			@$identifier = $user_group["Identifier"];
			@$population = 0;
			@$opening_balance = 0;
			@$closing_balance = 0;
			@$sql_consumption = "";
			@$store = "";
			@$district_or_region = "";
			@$vaccines = Vaccines::getAll_Minified();

			if ($identifier == 'provincial_officer') {

				$provinces = Provinces::getAllProvinces();
				foreach ($provinces as $province) {
					$data_buffer = "
			  <style>
			  table.data-table {
			  table-layout: fixed;
			  width: 700px;
			  }
			  table.data-table td {
			  width: 100px;
			  }
			  </style>
			  ";

					$start_date = $from;
					$end_date = $to;
					$data_buffer .= "<table class='data-table'>";
					$data_buffer .= $this -> echoTitles();
					foreach ($vaccines as $vaccine) {
						@$district_or_region = $province["id"];
						@$region_object = Regions::getRegion($district_or_region);
						@$store = $region_object -> name;
						@$population = Regional_Populations::getRegionalPopulation($district_or_region, date('Y'));
						@$opening_balance = Disbursements::getRegionalPeriodBalance($district_or_region, $vaccine -> id, strtotime($start_date));
						@$closing_balance = Disbursements::getRegionalPeriodBalance($district_or_region, $vaccine -> id, strtotime($end_date));
						@$sql_consumption = "select (SELECT max(str_to_date(Date_Issued,'%m/%d/%Y'))  FROM `disbursements` where Owner = '" . $owner . "' and str_to_date(Date_Issued,'%m/%d/%Y') between str_to_date('" . $start_date . "','%m/%d/%Y') and str_to_date('" . $end_date . "','%m/%d/%Y') and Vaccine_Id = '" . $vaccine -> id . "' and total_stock_balance>0)as last_stock_count,(SELECT sum(Quantity)FROM `disbursements` where Issued_By_Region = '" . $district_or_region . "' and Owner = '" . $owner . "' and str_to_date(Date_Issued,'%m/%d/%Y') between str_to_date('" . $start_date . "','%m/%d/%Y') and
  str_to_date('" . $end_date . "','%m/%d/%Y') and Vaccine_Id = '" . $vaccine -> id . "')as total_issued,(SELECT sum(Quantity) FROM `disbursements` where Issued_To_Region = '" . $district_or_region . "' and Owner = '" . $owner . "' and str_to_date(Date_Issued,'%m/%d/%Y') between str_to_date('" . $start_date . "','%m/%d/%Y') and
  str_to_date('" . $end_date . "','%m/%d/%Y') and Vaccine_Id = '" . $vaccine -> id . "')as total_received";
						@$query = $this -> db -> query($sql_consumption);
						@$vaccine_data = $query -> row();
						@$monthly_requirement = ceil(($vaccine -> Doses_Required * $population * $vaccine -> Wastage_Factor) / 12);
						@$data_buffer .= "<tr><td>" . $vaccine -> Name . "</td><td>" . number_format($opening_balance + 0) . "</td><td>" . number_format($vaccine_data -> total_received + 0) . "</td><td>" . number_format($vaccine_data -> total_issued + 0) . "</td><td>" . number_format(($closing_balance - ($opening_balance + $vaccine_data -> total_received - $vaccine_data -> total_issued)) + 0) . "</td><td>" . number_format($closing_balance + 0) . "</td><td>" . number_format(($closing_balance / $monthly_requirement), 1) . "</td><td>" . $vaccine_data -> last_stock_count . "</td></tr>";
					}//end of foreach vaccines
					@$vals = 1;
					@$data_buffer .= "</table>";
					@$this -> generatePDF($data_buffer, $start_date, $end_date, $store, $district_or_region, $vals, $store);
					@$data_buffer = "";
				}
			}
			if ($identifier == 'district_officer') {
				@$districts = Districts::getAllDistricts();
				foreach ($districts as $district) {
					@$data_buffer = "
			  <style>
			  table.data-table {
			  table-layout: fixed;
			  width: 700px;
			  }
			  table.data-table td {
			  width: 100px;
			  }
			  </style>
			  ";

					@$start_date = $from;
					@$end_date = $to;
					@$data_buffer .= "<table class='data-table'>";
					@$data_buffer .= $this -> echoTitles();
					foreach ($vaccines as $vaccine) {
						@$district_or_region = $district["id"];
						@$district_object = Districts::getDistrict($district_or_region);
						@$store = $district_object -> name;
						@$population = District_Populations::getDistrictPopulation($district_or_region, date('Y'));
						@$opening_balance = Disbursements::getDistrictPeriodBalance($district_or_region, $vaccine -> id, strtotime($start_date));
						@$closing_balance = Disbursements::getDistrictPeriodBalance($district_or_region, $vaccine -> id, strtotime($end_date));
						@$owner = "D" . $district_or_region;
						@$sql_consumption = "select (SELECT max(str_to_date(Date_Issued,'%m/%d/%Y'))  FROM `disbursements` where Owner = '" . $owner . "' and str_to_date(Date_Issued,'%m/%d/%Y') between str_to_date('" . $start_date . "','%m/%d/%Y') and str_to_date('" . $end_date . "','%m/%d/%Y') and Vaccine_Id = '" . $vaccine -> id . "' and total_stock_balance>0)as last_stock_count,(SELECT sum(Quantity)FROM `disbursements` where Issued_By_District = '" . $district_or_region . "' and Owner = '" . $owner . "' and str_to_date(Date_Issued,'%m/%d/%Y') between str_to_date('" . $start_date . "','%m/%d/%Y') and
  str_to_date('" . $end_date . "','%m/%d/%Y') and Vaccine_Id = '" . $vaccine -> id . "')as total_issued,(SELECT sum(Quantity) FROM `disbursements` where Issued_To_District = '" . $district_or_region . "' and Owner = '" . $owner . "' and str_to_date(Date_Issued,'%m/%d/%Y') between str_to_date('" . $start_date . "','%m/%d/%Y') and
  str_to_date('" . $end_date . "','%m/%d/%Y') and Vaccine_Id = '" . $vaccine -> id . "')as total_received";
						@$query = $this -> db -> query($sql_consumption);
						@$vaccine_data = $query -> row();
						@$monthly_requirement = ceil(($vaccine -> Doses_Required * $population * $vaccine -> Wastage_Factor) / 12);
						@$data_buffer .= "<tr><td>" . $vaccine -> Name . "</td><td>" . number_format($opening_balance + 0) . "</td><td>" . number_format($vaccine_data -> total_received + 0) . "</td><td>" . number_format($vaccine_data -> total_issued + 0) . "</td><td>" . number_format(($closing_balance - ($opening_balance + $vaccine_data -> total_received - $vaccine_data -> total_issued)) + 0) . "</td><td>" . number_format($closing_balance + 0) . "</td><td>" . number_format(($closing_balance / $monthly_requirement), 1) . "</td><td>" . $vaccine_data -> last_stock_count . "</td></tr>";
					}//end of foreach vaccines
					@$vals = 2;
					@$data_buffer .= "</table>";
					@$this -> generatePDF($data_buffer, $start_date, $end_date, $store, $district_or_region, $vals, $store);
					@$data_buffer = "";
				}
			}
			if ($identifier == 'national_officer') {

				$data_buffer = "
			  <style>
			  table.data-table {
			  table-layout: fixed;
			  width: 700px;
			  }
			  table.data-table td {
			  width: 100px;
			  }
			  </style>
			  ";
				$start_date = $from;
				$end_date = $to;
				$data_buffer .= "<table class='data-table'>";
				$data_buffer .= $this -> echoTitles();
				$store = "Central Vaccines Store";
				foreach ($vaccines as $vaccine) {
					@$population = Regional_Populations::getNationalPopulation(date('Y'));
					@$opening_balance = Disbursements::getNationalPeriodBalance($vaccine -> id, strtotime($start_date));
					@$closing_balance = Disbursements::getNationalPeriodBalance($vaccine -> id, strtotime($end_date));
					@$sql_consumption = "select (SELECT max(str_to_date(Date_Issued,'%m/%d/%Y'))  FROM `disbursements` where Owner = 'N0' and str_to_date(Date_Issued,'%m/%d/%Y') between str_to_date('" . $start_date . "','%m/%d/%Y') and str_to_date('" . $end_date . "','%m/%d/%Y') and Vaccine_Id = '" . $vaccine -> id . "' and total_stock_balance>0)as last_stock_count,(SELECT sum(Quantity)FROM `disbursements` where Issued_By_National = '0' and Owner = 'N0' and str_to_date(Date_Issued,'%m/%d/%Y') between str_to_date('" . $start_date . "','%m/%d/%Y') and
  str_to_date('" . $end_date . "','%m/%d/%Y') and Vaccine_Id = '" . $vaccine -> id . "')as total_issued,(SELECT sum(Quantity) FROM `disbursements` where Issued_To_National = '0' and Owner = 'N0' and str_to_date(Date_Issued,'%m/%d/%Y') between str_to_date('" . $start_date . "','%m/%d/%Y') and
  str_to_date('" . $end_date . "','%m/%d/%Y') and Vaccine_Id = '" . $vaccine -> id . "')as total_received";
					@$query = $this -> db -> query($sql_consumption);
					@$vaccine_data = $query -> row();
					@$monthly_requirement = ceil(($vaccine -> Doses_Required * $population * $vaccine -> Wastage_Factor) / 12);
					@$data_buffer .= "<tr><td>" . $vaccine -> Name . "</td><td>" . number_format($opening_balance + 0) . "</td><td>" . number_format($vaccine_data -> total_received + 0) . "</td><td>" . number_format($vaccine_data -> total_issued + 0) . "</td><td>" . number_format(($closing_balance - ($opening_balance + $vaccine_data -> total_received - $vaccine_data -> total_issued)) + 0) . "</td><td>" . number_format($closing_balance + 0) . "</td><td>" . number_format(($closing_balance / $monthly_requirement), 1) . "</td><td>" . $vaccine_data -> last_stock_count . "</td></tr>";
				}//end of foreach vaccines
				@$vals = 3;
				@$data_buffer .= "</table>";
				@$this -> generatePDF($data_buffer, $start_date, $end_date, $store, $district_or_region, $vals, $store);
				@$data_buffer = "";

			}

		}//end of foreach user_group

	}//end of function

	function generatePDF($data, $start_date, $end_date, $store, $district_or_region, $vals, $store) {
		$html_title = "<img src='Images/coat_of_arms-resized.png' style='position:absolute; width:96px; height:92px; top:0px; left:0px; '></img>";
		$html_title .= "<h3 style='text-align:center; text-decoration:underline; margin-top:-50px;'>Vaccine Consumption Summary For " . $store . "</h3>";
		$html_title .= "<h5 style='text-align:center;'> from: " . $start_date . " to: " . $end_date . "</h5>";

		$this -> load -> library('mpdf');
		$this -> mpdf = new mPDF('c', 'A4');
		$this -> mpdf -> SetTitle('Vaccine Consumption');
		$this -> mpdf -> WriteHTML($html_title);
		$this -> mpdf -> simpleTables = true;
		$this -> mpdf -> WriteHTML($data);
		$this -> mpdf -> WriteHTML($html_footer);

		$report_name = "Vaccine Consumption for $store.pdf";
		$path = $_SERVER["DOCUMENT_ROOT"];
		$handler = $path . "/DVI/application/pdf/" . $report_name;
		write_file($handler, $this -> mpdf -> Output($report_name, 'S'));
		$this -> email_sender($report_name, $start_date, $end_date, $district_or_region, $vals, $store);

	}

	//function that does the actual sending and design of the pdf
	function email_sender($report_name, $start_date, $end_date, $district_or_region, $vals, $store) {
		//setting the connection variables
		$config['protocol'] = 'smtp';
		$config['smtp_host'] = 'ssl://smtp.googlemail.com';
		$config['smtp_port'] = 465;
		$config['smtp_user'] = 'D.V.I.VaccinesKenya@gmail.com';
		$config['smtp_pass'] = 'projectDVI';
		ini_set("SMTP", "ssl://smtp.gmail.com");
		ini_set("smtp_port", "465");
		ini_set("max_execution_time", "50000");
		if ($vals == 1) {

			$emails = Emails::getProvinceEmails($district_or_region);
		} else if ($vals == 2) {

			$emails = Emails::getDistrictEmails($district_or_region);
		} else if ($vals == 3) {

			$emails = Emails::getEmails();
		}

		//pulling emails from the DB

		$this -> load -> library('email', $config);
		$path = $_SERVER["DOCUMENT_ROOT"];
		$file = $path . "/DVI/application/pdf/" . $report_name;
		//puts the path where the pdf's are stored

		foreach ($emails as $email) {
			$this -> email -> attach($file);
			$address = $email['email'];
			$this -> email -> set_newline("\r\n");

			$this -> email -> from('D.V.I.VaccinesKenya@gmail.com', "DVI MAILER");
			//user variable displays current user logged in from sessions
			$this -> email -> to("$address");
			$this -> email -> subject('MONTHLY REPORT FOR ' . "$store");
			$this -> email -> message('Please find the Report Attached for ' . "$store" . ' Period of ' . "$start_date" . ' to ' . "$end_date");

			//success message else show the error
			if ($this -> email -> send()) {
				echo 'Your email was sent, successfully to ' . $address . '<br/>';
				//unlink($file);
				$this -> email -> clear(TRUE);

			} else {
				show_error($this -> email -> print_debugger());
			}

		}
		ob_end_flush();
		unlink($file);
		//delete the attachment after sending to avoid clog up of pdf's
	}

}
