<?php
class Vaccination_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	public function upload() {
		$data['title'] = "DHIS Data Upload";
		$data['module_view'] = "dhis_upload_view";
		$this -> base_params($data);
	}

	function do_upload() {
		$config['upload_path'] = "./";
		$config['allowed_types'] = 'csv';
		$config['file_name'] = "dhis_data";
		$this -> load -> library('upload', $config);

		if (!$this -> upload -> do_upload()) {
			$error = array('error' => $this -> upload -> display_errors());
			$data['title'] = "DHIS Data Upload";
			$data['module_view'] = "upload_error";
			$data['error'] = $this -> upload -> display_errors();
			$this -> base_params($data);

		} else {
			//fetch the data from the csv file then delete it
			$this->fetch_data();
		}
	}

	public function fetch_data() {
		$this -> load -> library('csvreader');

		$filePath = './dhis_data.csv';
		$resource = @fopen($filePath, 'r');
		if (!$resource) {
			$data['error'] = "An error was encountered while opening the file that you uploaded. Please confirm it's contents then try again.";
			$data['module_view'] = "upload_error";
			$this -> base_params($data);
			return;
		}

		$data = $this -> csvreader -> parse_file($filePath, false);
		$records = 0;
		if ($data == true) {
			$records = count($data);
			$counter = 0;
			foreach ($data as $row) {
				if ($counter == 0) {
					$counter++;
					continue;
				}
				$dhis_data = new Dhis_Data();
				$dhis_data -> Reporting_Period = $row[4];
				$dhis_data -> Facility_Name = $row[5];
				$dhis_data -> Facility_Code = $row[7];
				$dhis_data -> Bcg_Admin = $row[14];
				$dhis_data -> Dpt1_Admin = $row[15];
				$dhis_data -> Dpt2_Admin = $row[16];
				$dhis_data -> Dpt3_Admin = $row[17];
				$dhis_data -> Fully_Immunized_Children = $row[18];
				$dhis_data -> Measles_Admin = $row[19];
				$dhis_data -> Opv1_Admin = $row[20];
				$dhis_data -> Opv2_Admin = $row[21];
				$dhis_data -> Opv3_Admin = $row[22];
				$dhis_data -> Opv_Birth_Admin = $row[23];
				$dhis_data -> Pn1_Admin = $row[24];
				$dhis_data -> Pn2_Admin = $row[25];
				$dhis_data -> Pn3_Admin = $row[26];
				$dhis_data -> Tt_Pregnant = $row[27];
				$dhis_data -> Tt_Trauma = $row[28];
				$dhis_data -> Vitamin_2_5 = $row[29];
				$dhis_data -> Vitamin_12_59 = $row[30];
				$dhis_data -> Vitamin_6_11 = $row[31];
				$dhis_data -> Vitamin_Adult = $row[31];
				$dhis_data -> Vitamin_6_11_Months = $row[33];
				$dhis_data -> Vitamin_Older_Than_One_Year = $row[34];
				$dhis_data -> Vitamin_Lactating_Mothers = $row[35];
				$dhis_data -> Yellow_Admin = $row[36];
				$dhis_data -> Bcg_Stock = $row[37];
				$dhis_data -> Bcg_Received = $row[38];
				$dhis_data -> Bcg_Remaining = $row[39];
				$dhis_data -> Dpt_Stock = $row[40];
				$dhis_data -> Dpt_Received = $row[41];
				$dhis_data -> Dpt_Remaining = $row[42];
				$dhis_data -> Opv_Stock = $row[43];
				$dhis_data -> Opv_Received = $row[44];
				$dhis_data -> Opv_Remaining = $row[45];
				$dhis_data -> Pn_Stock = $row[46];
				$dhis_data -> Pn_Received = $row[47];
				$dhis_data -> Pn_Remaining = $row[48];
				$dhis_data -> Tt_Stock = $row[49];
				$dhis_data -> Tt_Received = $row[50];
				$dhis_data -> Tt_Remaining = $row[51];
				$dhis_data -> Yellow_Stock = $row[52];
				$dhis_data -> Yellow_Received = $row[53];
				$dhis_data -> Yellow_Remaining = $row[54];
				$dhis_data -> Vitamin_100_Stock = $row[55];
				$dhis_data -> Vitamin_100_Received = $row[56];
				$dhis_data -> Vitamin_100_Remaining = $row[57];
				$dhis_data -> Vitamin_200_Stock = $row[58];
				$dhis_data -> Vitamin_200_Received = $row[59];
				$dhis_data -> Vitamin_200_Remaining = $row[60];
				$dhis_data -> Vitamin_50_Stock = $row[61];
				$dhis_data -> Vitamin_50_Received = $row[62];
				$dhis_data -> Vitamin_50_Remaining = $row[63];
				$dhis_data -> Vitamin_200000_Iu = $row[64];
				$dhis_data -> Vitamin_Lactating = $row[65];
				$dhis_data -> Vitamin_Supplement = $row[66];
				$dhis_data -> save();

			}
		}
		echo fclose($resource);
		chmod($filePath, 0777);
		unlink($filePath);
		$data['records'] = $records;
		$data['title'] = "DHIS Data Upload";
		$data['module_view'] = "upload_success";
		$data['error'] = $this -> upload -> display_errors();
		$this -> base_params($data);
	}

	private function base_params($data) {
		$data['scripts'] = array("jquery-ui.js", "tab.js");
		$data['styles'] = array("jquery-ui.css", "tab.css");
		$data['content_view'] = "admin_view";
		$data['quick_link'] = "vaccination_management";
		$data['link'] = "system_administration";
		$this -> load -> view('template', $data);
	}

}
