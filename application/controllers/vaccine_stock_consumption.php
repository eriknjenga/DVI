<?php
class Vaccine_Stock_Consumption extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	function index() {
		$this -> ranking();
	}

	public function ranking($year = 0, $vaccine = 0, $from = 0, $to = 0) {
		$table_string = "";
		$vaccine_object = null;
		$start_month = 0;
		$end_month = 0;
		if ($year == 0) {
			$year = date('Y');
		}
		//If no dates are selected, make the default to be jan to dec of the current year
		if ($to == 0) {
			$to = date("U", mktime(0, 0, 0, 12, 31, date("Y")));
			$end_month = 12;
		} else if ($to != 0) {
			$a_date = "$year-$to-1";
			$last_day = date("t", strtotime($a_date));
			$end_month = $to;
			$to = date("U", mktime(0, 0, 0, $to, $last_day, $year));
			
		}
		if ($from == 0) {
			$from = date("U", mktime(0, 0, 0, 1, 1, date('Y')));
			$start_month = 1;
		} else if ($from != 0) {
			$start_month = $from;
			$from = date("U", mktime(0, 0, 0, $from, 1, $year));
		}
		//If no vaccine is selected, get the first vaccine in the system
		if ($vaccine == "0") {
			$vaccine_object = Vaccines::getFirstVaccine();
			$vaccine = $vaccine_object -> id;
		} else if (strlen($vaccine) > 0) {
			$vaccine_object = Vaccines::getVaccine($vaccine);
			$vaccine = $vaccine_object -> id;
		}
		$table_title = $vaccine_object->Name." from ".date("M-d-y",$from)." to ".date("M-d-y",$to);
		$this -> load -> database();
		//Retrieve all the district details.
		$sql = "select d.name, district_summaries.*, (total_administered/total_received) as coefficient from (select immunization_data.*,sum(quantity) as total_received  from (SELECT district, sum(";
		//Break up the query to add all the requisite columns that are to be summed from the dhis table
		$dhis_columns = $vaccine_object -> Dhis_Columns;
		$columns_array = explode(',', $dhis_columns);
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
		$sql .= ") as total_administered FROM dhis_data d left join facilities f on d.facility_code = f.facilitycode where unix_timestamp(str_to_date(reporting_period,'%M-%y')) between '$from' and '$to' group by district) immunization_data left join disbursements dis on district = issued_to_district where Vaccine_Id = '$vaccine' and Date_Issued_Timestamp between '$from' and '$to' and Owner != concat('D',district) group by district) district_summaries left join districts d on district = d.ID order by coefficient desc";
		//echo $sql;
		$query = $this -> db -> query($sql);
		$district_details = $query -> result_array();
		$data['table_title'] = $table_title;
		$data['selected_year'] = $year;
		$data['selected_vaccine'] = $vaccine;
		$data['selected_start_month'] = $start_month;
		$data['selected_end_month'] = $end_month; 
		$data['district_details'] = $district_details;
		$data['current'] = "vaccine_stock_consumption";
		$data['title'] = "Vaccine Stock Consumption";
		$data['banner_text'] = "Vaccine Stock Consumption";
		$data['vaccines'] = Vaccines::getAll_Minified();
		$data['content_view'] = "vaccine_stock_consumption_view";
		$data['scripts'] = array("jquery-ui.js", "tab.js", "table_sorter.js");
		$data['styles'] = array("table_sorter.css");
		$this -> load -> view("platform_template", $data);
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
