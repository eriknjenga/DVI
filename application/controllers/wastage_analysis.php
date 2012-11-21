<?php
class Wastage_Analysis extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	function index() {
		$this -> analysis();
	}

	public function analysis($year = 0, $month = 0) {
		$district_antigen_wastage = array();
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
			$sql = " select district_id,wastage from (select @rownum:=@rownum+1 as row_number,facility_data.*, ((total_consumed-total_administered)/total_consumed) as wastage from (SELECT  f.district as district_id, facility_name, district, (";

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
			$sql .= ") as total_administered,(";
			//add the chunk of the query that calculates consumption
			$sql .= $dhis_consumption_columns;
			$sql .= ") as total_consumed FROM dhis_data d left join facilities f on d.facility_code = f.facilitycode where reporting_period = '$period') facility_data, (SELECT @rownum:=0) r  having wastage>0 and total_consumed>0 and total_administered>0 order by district, wastage desc) wastage_info, (select (row_number + floor(count(*)/2)) as median, count(*) as total_number, row_number,district from (select @rownum:=@rownum+1 as `row_number`,facility_data.*, ((total_consumed-total_administered)/total_consumed) as wastage from (SELECT facility_name, district, (";

			//Break up the query to add all the requisite columns that are to be summed from the dhis table
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
			$sql .= ") as total_administered,(";
			//add the chunk of the query that calculates consumption
			$sql .= $dhis_consumption_columns;
			$sql .= ") as total_consumed FROM dhis_data d left join facilities f on d.facility_code = f.facilitycode where reporting_period = 'Jun-12') facility_data, (SELECT @rownum:=0) r  having wastage>0 and total_consumed>0 and total_administered>0 order by district, wastage desc) wastage_data group by district) median_facilities where wastage_info.row_number = median";

			//echo $sql."<br><br><br><br>";
			$query = $this -> db -> query($sql);
			$district_wastage = $query -> result_array();
			//Handle the results
			foreach ($district_wastage as $wastage_data) {
				$district_antigen_wastage[$wastage_data['district_id']][$vaccine_object -> id] = number_format($wastage_data['wastage'], 2);
			}

		}
		//Retrieve only the districts in the mfl
		$sql = "SELECT distinct district,d.ID,d.name as name FROM `facilities` f left join districts d on f.district = d.id order by name";
		$query = $this -> db -> query($sql);
		$data['selected_year'] = $year;
		$data['selected_month'] = $month;
		$data['districts'] = $query -> result_array();
		$data['district_details'] = $district_antigen_wastage;
		$data['vaccines'] = $vaccine_objects;
		$data['current'] = "wastage_analysis";
		$data['title'] = "Vaccine Wastage Analysis";
		$data['banner_text'] = "Vaccine Wastage Analysis";
		$data['content_view'] = "vaccine_wastage_analysis_view";
		$data['scripts'] = array("table_sorter.js");
		$this -> load -> view("platform_template", $data);
	}

	public function district_analysis($year, $month, $district) {
		$district_antigen_wastage = array();
		$table_string = "";
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
			$sql = " select facility_data.*, ((total_consumed-total_administered)/total_consumed) as wastage from (SELECT district, (";

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
			$sql .= ") as total_administered,(";
			//add the chunk of the query that calculates consumption
			$sql .= $dhis_consumption_columns;
			$sql .= ") as total_consumed, facility_name FROM dhis_data d left join facilities f on d.facility_code = f.facilitycode where reporting_period = '$period' and f.district= '$district') facility_data having wastage>0 and total_consumed>0 and total_administered>0 order by wastage desc;";

			//echo $sql."<br><br><br><br>";
			$query = $this -> db -> query($sql);
			$district_wastage = $query -> result_array();
			//Handle the results
			foreach ($district_wastage as $wastage_data) {
				$district_antigen_wastage[$wastage_data['facility_name']][$vaccine_object -> id] = number_format($wastage_data['wastage'], 2);
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

		$facility_names = array_keys($district_antigen_wastage);
		$counter = 0;
		foreach ($district_antigen_wastage as $wastage_data) {
			$final_table .= "<tr><td><a class='link facility_details' facility_name='" . $facility_names[$counter] . "'>" . $facility_names[$counter] . "</a></td>";
			//Loop through all the vaccines to get the column values
			foreach ($vaccine_objects as $vaccine_object) {
				if (isset($wastage_data[$vaccine_object -> id])) {
					$final_table .= "<td>" . $wastage_data[$vaccine_object -> id] . "</td>";
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

	private function base_params($data) {
		$data['scripts'] = array("jquery-ui.js", "tab.js");
		$data['styles'] = array("jquery-ui.css", "tab.css");
		$data['content_view'] = "admin_view";
		$data['quick_link'] = "vaccination_management";
		$data['link'] = "system_administration";
		$this -> load -> view('template', $data);
	}

}
