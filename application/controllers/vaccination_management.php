<?php
class Vaccination_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	function index() {
		$this -> dashboard();
	}

	public function upload() {
		$data['title'] = "DHIS Data Upload";
		$data['module_view'] = "dhis_upload_view";
		$this -> base_params($data);
	}

	public function data_listing() {
		$data['title'] = "DHIS Data Available";
		$data['module_view'] = "dhis_upload_listing";
		$this -> load -> database();
		$sql = "select count(*) as total,reporting_period from dhis_data group by reporting_period";
		$query = $this -> db -> query($sql);
		$data['uploaded_data'] = $query -> result_array();
		$this -> base_params($data);
	}

	public function delete_data($period) {
		$this -> load -> database();
		$delete_period = $this -> db -> escape_str(urldecode($period));
		$sql = "delete from dhis_data where reporting_period = '$delete_period'";
		$query = $this -> db -> query($sql);
		redirect("vaccination_management/data_listing");
	}

	function get_cummulative_graph($year = 0, $antigens = 0, $district = 0, $facility = 0, $type = 0) {
		$graph_sub_title = "";
		if ($year == 0) {
			$year = date('y');
		}
		//Start creating the sql query
		$sql = "select reporting_period, ";
		$antigen_count = 0;
		$antigen_array = array();
		$antigen_titles = array("dpt1_admin" => "DPT 1", "dpt2_admin" => "DPT 2", "dpt3_admin" => "DPT 3", "opv1_admin" => "OPV 1", "opv2_admin" => "OPV 2", "opv3_admin" => "OPV 3", "opv_birth_admin" => "OPV Birth", "pn1_admin" => "Pneumococal 1", "pn2_admin" => "Pneumococal 2", "pn3_admin" => "Pneumococal 3", "tt_pregnant" => "TT Pregnant Women", "tt_trauma" => "TT Trauma", "yellow_admin" => "Yellow Fever", "measles_admin" => "Measles", "bcg_admin" => "BCG");
		$antigen_data = array();
		$selected_antigens = array();
		$this -> load -> database();
		//If no antigens have been specified, get data for dpt1 and 3
		if (strlen($antigens) == 1) {
			$selected_antigens = array("dpt1_admin", "dpt3_admin");
		} else {
			$selected_antigens = explode("-", $antigens);
			array_pop($selected_antigens);
		}
		$counter = 1;
		//For each of the selected antigens, append it's retrieval chunk onto the sql query
		foreach ($selected_antigens as $selected_antigen) {
			array_push($antigen_array, $antigen_titles[$selected_antigen]);
			if (sizeof($selected_antigens) != $counter) {
				$sql .= "sum(" . $selected_antigen . ") as `" . $antigen_titles[$selected_antigen] . "`, ";
			} else {
				$sql .= "sum(" . $selected_antigen . ") as `" . $antigen_titles[$selected_antigen] . "` ";
			}
			$counter++;
		}
		//Finish creating the query based on whether the user is filtering down to a district or not
		if ($district == 0) {
			$graph_sub_title = "Nationwide";
			$sql .= " from dhis_data where reporting_period like '%$year%' group by reporting_period";
		} else if ($district > 0) {
			if ($facility == 0) {
				$district_object = Districts::getDistrict($district);
				$graph_sub_title = "In " . $district_object -> name . " District";
				$sql .= " from dhis_data d left join facilities f on d.facility_code = f.facilitycode where reporting_period like '%$year%' and f.district = '" . $district . "' group by reporting_period";
			}
			if ($facility > 0) {
				$facility_name = Facilities::getFacilityName($facility);
				$graph_sub_title = "In " . $facility_name;
				$sql .= " from dhis_data where reporting_period like '%$year%' and facility_code = '" . $facility . "' group by reporting_period";
			}

		}
		$query = $this -> db -> query($sql);
		$immunizations = $query -> result_array();
		foreach ($immunizations as $immunization) {
			foreach ($antigen_array as $antigen_dataset) {
				$antigen_data[$antigen_dataset][$immunization['reporting_period']] = $immunization["$antigen_dataset"];
			}
		}
		$chart = '<chart caption="Immunization Data" subcaption="' . $graph_sub_title . ' For \'' . $year . '" connectNullData="1" showValues="0" formatNumberScale="0" lineDashGap="6" xAxisName="Month" yAxisName="Cummulative Immunized" showValues="0" showBorder="0" showAlternateHGridColor="0" divLineAlpha="10"  bgColor="FFFFFF"  exportEnabled="1" exportHandler="' . base_url() . 'Scripts/FusionCharts/ExportHandlers/PHP/FCExporter.php" exportAtClient="0" exportAction="download">
<categories>
<category label="Jan"/>
<category label="Feb"/>
<category label="Mar"/>
<category label="Apr"/>
<category label="May"/>
<category label="Jun"/>
<category label="Jul"/>
<category label="Aug"/>
<category label="Sep"/>
<category label="Oct"/>
<category label="Nov"/>
<category label="Dec"/> 
</categories>';
		//Loop through all the months in the specified year
		$str_start = $year . "-01-01";
		$str_end = $year . "-12-31";
		$start = $month = strtotime($str_start);
		$end = strtotime($str_end);
		$loop_from = $str_start;
		$loop_to = $str_end;
		$days = array();
		$counter = 0;
		//create an array of the various months
		while (strtotime($loop_from) <= strtotime($loop_to)) {
			$days[$counter] = date('M-y', strtotime($loop_from));
			$loop_from = date("d-m-Y", strtotime("+1 month", strtotime($loop_from)));
			$counter++;
		}
		foreach ($antigen_array as $antigen_dataset) {
			$chart .= "<dataset seriesName='$antigen_dataset'>";
			$antigen_dataset_data = $antigen_data[$antigen_dataset];
			//Keep track of the cummulative totals
			$cummulative = 0;
			$counter = 0;
			while ($month < $end) {
				//get the description of the months
				$current_month = date('M-y', $month);
				if (isset($antigen_dataset_data[$current_month])) {
					//check if the next value is non-existent, if so, display a dotted line, else, display a kawaida line
					if (sizeof($antigen_dataset_data) != $counter) {
						if($type == 0){
							$cummulative += $antigen_dataset_data[$current_month];
						}
						else if($type == 1){
							$cummulative = $antigen_dataset_data[$current_month];
						}
						
						if (isset($antigen_dataset_data[$days[$counter + 1]])) {
							$chart .= '<set value="' . $cummulative . '"/>';
						} else {
							$chart .= '<set value="' . $cummulative . '" dashed="1"/>';
						}
					}

				} else {
					$chart .= '<set  />';
				}
				$counter++;

				$month = strtotime("+1 month", $month);
			}
			$start = $month = strtotime($str_start);
			$chart .= "</dataset>";
		}

		$chart .= "</chart>";
		echo $chart;
	}

	public function dashboard() {
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
			$this -> fetch_data();
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
				$dhis_data -> Measles_Stock = $row[55];
				$dhis_data -> Measles_Received = $row[56];
				$dhis_data -> Measles_Remaining = $row[57];
				$dhis_data -> Vitamin_100_Stock = $row[58];
				$dhis_data -> Vitamin_100_Received = $row[59];
				$dhis_data -> Vitamin_100_Remaining = $row[60];
				$dhis_data -> Vitamin_200_Stock = $row[61];
				$dhis_data -> Vitamin_200_Received = $row[62];
				$dhis_data -> Vitamin_200_Remaining = $row[63];
				$dhis_data -> Vitamin_50_Stock = $row[64];
				$dhis_data -> Vitamin_50_Received = $row[65];
				$dhis_data -> Vitamin_50_Remaining = $row[66];
				$dhis_data -> Vitamin_200000_Iu = $row[67];
				$dhis_data -> Vitamin_Lactating = $row[68];
				$dhis_data -> Vitamin_Supplement = $row[69];
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
