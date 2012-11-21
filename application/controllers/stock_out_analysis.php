<?php
class Stock_Out_Analysis extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	function index() {
		$this -> analysis();
	}

	public function analysis($year = 0, $month = 0) {
		$district_antigen_stock_out = array();
		$table_string = "";
		if ($year == 0) {
			$year = date('Y');
		}
		if ($month == 0) {
			$month = date('n');
		}
		$period = date("M-y", mktime(0, 0, 0, $month, 1, $year));
		$vaccine_objects = Vaccines::getAll_Minified();
		//loop through all the vaccines to get their wastage data
		$this -> load -> database();
		foreach ($vaccine_objects as $vaccine_object) {
			$sql = "";
			$dhis_stock = $vaccine_object -> Dhis_Stock;
			$dhis_received = $vaccine_object -> Dhis_Received;
			$dhis_remaining = $vaccine_object -> Dhis_Remaining;

			if ($dhis_stock == "" || $dhis_received == "" || $dhis_remaining == "") {
				continue;
			}
			//Retrieve all the district details.
			$sql = "SELECT dis.name as district_name,dis.ID as district_id,count(*) as total_facilities,district FROM dhis_data d left join facilities f on d.facility_code = f.facilitycode and d.facility_name = f.name left join districts dis on f.district = dis.ID  where reporting_period = '$period'  and ($dhis_remaining = '0' or $dhis_remaining = '') and $dhis_stock+$dhis_received>0 group by dis.ID";
			//echo $sql . "<br><br><br><br>";
			$query = $this -> db -> query($sql);
			$district_stock_outs = $query -> result_array();
			//Handle the results
			foreach ($district_stock_outs as $stock_out_data) {
				$district_antigen_stock_out[$stock_out_data['district_name']][$vaccine_object -> id] = $stock_out_data['total_facilities'];
				if ($stock_out_data['district_id'] > 0) {
					$district_antigen_stock_out[$stock_out_data['district_name']]["district_id"] = $stock_out_data['district_id'];
					$district_antigen_stock_out[$stock_out_data['district_name']]["district_name"] = $stock_out_data['district_name'];
				} else {
					$district_antigen_stock_out[$stock_out_data['district_name']]["district_id"] = 0;
					$district_antigen_stock_out[$stock_out_data['district_name']]["district_name"] = 'Undefined District';
				}

			}

		}
		//var_dump($district_antigen_stock_out);
		$data['selected_year'] = $year;
		$data['selected_month'] = $month;
		$data['district_details'] = $district_antigen_stock_out;
		$data['vaccines'] = $vaccine_objects;
		$data['current'] = "stock_out_analysis";
		$data['title'] = "Vaccine Stock Out Analysis";
		$data['banner_text'] = "Vaccine Stock Out Analysis";
		$data['content_view'] = "vaccine_stock_out_analysis_view";
		$data['scripts'] = array("table_sorter.js");
		$this -> load -> view("platform_template", $data);
	}

	public function district_analysis($year, $month, $district) {
		$district_antigen_stock_out = array();
		$table_string = "";
		if ($year == 0) {
			$year = date('Y');
		}
		if ($month == 0) {
			$month = date('n');
		}
		$period = date("M-y", mktime(0, 0, 0, $month, 1, $year));
		$vaccine_objects = Vaccines::getAll_Minified();
		//loop through all the vaccines to get their wastage data
		$this -> load -> database();
		foreach ($vaccine_objects as $vaccine_object) {
			$sql = "";
			$dhis_stock = $vaccine_object -> Dhis_Stock;
			$dhis_received = $vaccine_object -> Dhis_Received;
			$dhis_remaining = $vaccine_object -> Dhis_Remaining;

			if ($dhis_stock == "" || $dhis_received == "" || $dhis_remaining == "") {
				continue;
			}
			//Retrieve all the district details.
			$sql = "SELECT facility_name FROM dhis_data d left join facilities f on d.facility_code = f.facilitycode and d.facility_name = f.name where reporting_period = '$period'  and ($dhis_remaining = '0' or $dhis_remaining = '') and $dhis_stock+$dhis_received>0 and district = $district";
			if ($district == 0) {
				$sql = "SELECT facility_name FROM dhis_data d left join facilities f on d.facility_code = f.facilitycode and d.facility_name = f.name where reporting_period = '$period'  and ($dhis_remaining = '0' or $dhis_remaining = '') and $dhis_stock+$dhis_received>0 and district is null";
			}
			//echo $sql . "<br><br><br><br>";
			$query = $this -> db -> query($sql);
			$district_stock_outs = $query -> result_array();
			//Handle the results
			foreach ($district_stock_outs as $district_stock_out) {
				$district_antigen_stock_out[$district_stock_out['facility_name']][$vaccine_object -> id] = '1';
			}

		}
		//Loop through all the fetched data to generate the final table
		$final_table = "<table id='facility_table' class='data-table'>	<thead>
		<tr>
			<th>Facility Name</th>";
		foreach ($vaccine_objects as $vaccine_object) {
			$final_table .= "<th>" . $vaccine_object -> Name . "</th>";
		}

		$final_table .= "</tr></thead><tbody>";

		$facility_names = array_keys($district_antigen_stock_out);
		$counter = 0;
		foreach ($district_antigen_stock_out as $stock_out_data) {
			$final_table .= "<tr><td><a class='link facility_details' facility_name='" . $facility_names[$counter] . "'>" . $facility_names[$counter] . "</a></td>";
			//Loop through all the vaccines to get the column values
			foreach ($vaccine_objects as $vaccine_object) {
				if (isset($stock_out_data[$vaccine_object -> id])) {
					$final_table .= "<td>Yes</td>";
				} else {
					$final_table .= "<td>-</td>";
				}

			}
			$final_table .= "</tr>";
			$counter++;
		}
		$final_table.="</tbody></table>";
		echo $final_table;
	}

	public function facility_analysis() {
		$year = $this -> input -> post('year');
		$month = $this -> input -> post('period');
		$facility = $this -> input -> post('facility_name');
		$facility_summary = array();
		$table_string = "";
		if ($year == 0) {
			$year = date('Y');
		}
		if ($month == 0) {
			$month = date('n');
		}
		$period = date("M-y", mktime(0, 0, 0, $month, 1, $year));
		$vaccine_objects = Vaccines::getAll_Minified();
		//loop through all the vaccines to get their wastage data
		$this -> load -> database();
		foreach ($vaccine_objects as $vaccine_object) {
			$sql = "";
			$dhis_columns = $vaccine_object -> Dhis_Columns;
			$dhis_stock = $vaccine_object -> Dhis_Stock;
			$dhis_received = $vaccine_object -> Dhis_Received;
			$dhis_remaining = $vaccine_object -> Dhis_Remaining;

			if ($dhis_columns == "" || $dhis_stock == "" || $dhis_received == "" || $dhis_remaining == "") {
				continue;
			}
			//construct the consumption equation
			$dhis_consumption_columns = $dhis_stock . "+" . $dhis_received . "-" . $dhis_remaining;
			//Break up the query to add all the requisite columns that are to be summed from the dhis table
			$columns_array = explode(',', $dhis_columns);
			//Retrieve all the district details.
			$sql = "SELECT facility_name,$dhis_stock as opening_stock,$dhis_received as received_stock,$dhis_remaining as remaining_stock,(";
			$counter = 0;
			foreach ($columns_array as $column) {
				//Append the column to the sql query
				$sql .= $column;
				//Check if there is another column, if so, append a '+' sign
				if (isset($columns_array[$counter + 1])) {
					$sql .= "+";
				}
				$counter++;
			}
			$sql .= ") as total_administered  FROM dhis_data where reporting_period = '$period'  and facility_name = '$facility'";

			//echo $sql . "<br><br><br><br>";
			$query = $this -> db -> query($sql);
			$facility_statistics = $query -> result_array();
			//Handle the results
			foreach ($facility_statistics as $facility_data) {
				$facility_summary[$vaccine_object -> id]['opening_stock'] = $facility_data['opening_stock'];
				$facility_summary[$vaccine_object -> id]['received_stock'] = $facility_data['received_stock'];
				$facility_summary[$vaccine_object -> id]['remaining_stock'] = $facility_data['remaining_stock'];
				$facility_summary[$vaccine_object -> id]['total_administered'] = $facility_data['total_administered'];
			}

		}
		//var_dump($facility_summary);
		//Loop through all the fetched data to generate the final table
		$final_table = "<table id='facility_table' class='data-table'>	<thead>
		<tr>
			<th>Antigen</th><th>Opening Stock</th><th>Received Stock</th><th>Closing Stock</th><th>Consumption</th><th>Total Administered</th><th>Wastage</th></tr></thead><tbody>";
		foreach ($vaccine_objects as $vaccine_object) {
			$final_table .= "<tr><td>" . $vaccine_object -> Name . "</td>";
			if(isset($facility_summary[$vaccine_object->id])){
				$consumption = ($facility_summary[$vaccine_object->id]['opening_stock']+$facility_summary[$vaccine_object->id]['received_stock']-$facility_summary[$vaccine_object->id]['remaining_stock']);
				$wastage = "N/A";
				if($consumption>0 and $facility_summary[$vaccine_object->id]['total_administered']>0){
					$wastage = number_format((($consumption - $facility_summary[$vaccine_object->id]['total_administered'])/$consumption),2);
				}
				$final_table .= "<td>" . $facility_summary[$vaccine_object->id]['opening_stock'] . "</td><td>" . $facility_summary[$vaccine_object->id]['received_stock'] . "</td><td>" . $facility_summary[$vaccine_object->id]['remaining_stock'] . "</td><td>" . $consumption. "</td><td>" . $facility_summary[$vaccine_object->id]['total_administered'] . "</td><td>" . $wastage . "</td>";
			}
			else{
				$final_table .= "<td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>";
			}
			
			
			
			
			
			$final_table .= "</tr>";
		}

		$final_table.="</tbody></table>";
		echo $final_table;
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
