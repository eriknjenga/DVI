<?php

class Periodic_Reports extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> helper('file');
		$config = Array('protocol' => 'smtp', 'smtp_host' => 'ssl://smtp.googlemail.com', 'smtp_port' => 465, 'smtp_user' => 'dvi.kenya@gmail.com', // change it to yours
			'smtp_pass' => 'summaries', // change it to yours
			'mailtype' => 'html', 'charset' => 'iso-8859-1', 'wordwrap' => TRUE);
		$this -> load -> library('email', $config);
		$this -> email -> set_newline("\r\n");
		$this -> load -> helper('directory');

	}

	public function index() {
		$final_html = "";
		$css = "<style type='text/css'>
		td{
			border:1px solid black; 
		}
		.no_border{
			border: none;
		} 
		.regionals td{
			min-width:110px;
		}
		th, .title{
			background:#DDD;
			border:none;
			padding:0;
			margin:0;
		}
		
		</style>";
		$final_html .= $css;
		$html_title = "<img src='Images/coat_of_arms.png' style='position:absolute;  width:160px; top:0px; right:0px; margin-bottom:-100px;margin-right:-100px;'></img>";
		$html_title .= "<h2 style='text-align:center; text-decoration:underline;'>Republic of Kenya</h2>";
		$html_title .= "<h3 style='text-align:center; text-decoration:underline;'>Ministry of Public Health and Sanitation</h3>";
		$html_title .= "<h1 style='text-align:center; text-decoration:underline;'>MONTHLY VACCINE STOCK MANAGEMENT REPORT</h1>";
		//echo "$html_title";
		$final_html .= $html_title;
		$final_html .= $this -> create_national_report();
		$this -> load -> library('mpdf');
		$mpdf = new mPDF('', 'A4-L', 0, '', 15, 15, 16, 16, 9, 9, '');
		$mpdf -> SetTitle('MONTHLY VACCINE STOCK MANAGEMENT REPORT');
		$mpdf -> WriteHTML($final_html);

		$regions = Regions::getAllRegions();
		foreach ($regions as $region) {
			$regional_report = $this -> create_regional_reports($region);
			$mpdf -> AddPage();
			$mpdf -> WriteHTML($regional_report);
			//
		}
		//echo $final_html
		$mpdf -> Output('Summaries/Monthly_Summary.pdf', 'F');

		$this -> email_reports();

	}

	function email_reports() {
		$this -> email -> from('reports@dvi.co.ke', 'Vaccine Summaries');
		$email_recipients = Email_Recipients::getAll();
		//Retrieve all reports in order to attach them to the email
		$files = directory_map('Summaries');
		//Loop through all files and attach them one by one
		foreach ($files as $file) {
			$this -> email -> attach("Summaries/" . $file);
		}
		$this -> email -> subject('Vaccine Status Summaries For Kenya');
		$this -> email -> message('Monthly Vaccine Summary is Attached');
		//Retrieve all intended recipients of this email
		foreach ($email_recipients as $recipient) {
			$email = $recipient -> Email;
			$this -> email -> cc($email);
			$this -> email -> send();
			echo $this -> email -> print_debugger();
		}

	}

	function write_file($name, $data) {
		if (!write_file("Summaries/" . $name, $data)) {
			echo 'Unable to write the file';
		} else {
			echo 'File written!';
		}
	}

	function create_national_report() {
		$year = date('Y');
		$total_vaccines = Vaccines::getTotalNumber();
		$total_vaccines *= 2;
		$html = "<table border='2px solid black'>";
		$html .= "<tr ><th rowspan=3>Analytical Areas</th><th style='text-align: center' colspan=" . $total_vaccines . ">Summary Report for Vaccine Status in Kenya</th></tr>";
		$html .= "<tr ><th style='text-align: center' colspan=" . $total_vaccines . ">Depot: National Store Reporting Date: " . date("d/m/Y") . "</th></tr>";
		$headers = "Summary Report for Vaccine Status in Kenya\n\t\nDepot: National Store\tReporting Date: " . date("d/m/Y") . "\t\n";
		$data = "Analytical Areas\t";
		$vaccines = Vaccines::getAll();

		$from = date("U", mktime(0, 0, 0, 1, 1, date('Y')));
		//This sets the begining date as the 1st of january of that particular year
		$to = date('U');
		//This sets the end date as the current time when the report is being generated
		//Loop all vaccines and create a table data element for it
		$html .= "<tr>";
		foreach ($vaccines as $vaccine) {
			$html .= "<td colspan=2 style='background-color:#" . $vaccine -> Tray_Color . "'>" . $vaccine -> Name . "</td>";
		}
		$html .= "</tr>";
		//New Line!
		//Begin adding data for the areas being analysed!

		$html .= "<tr><td class='title'>Annual Needs Coverage</td>";
		//Loop all vaccines and append the needs coverage for that particular vaccine in that store
		foreach ($vaccines as $vaccine) {
			$population = Regional_Populations::getNationalPopulation($year);
			$yearly_requirement = $population * $vaccine -> Doses_Required * $vaccine -> Wastage_Factor;
			$vaccine_totals = Disbursements::getNationalReceiptsTotals($vaccine -> id, $from, $to);
			$coverage = ceil(($vaccine_totals / $yearly_requirement) * 100);
			$html .= "<td colspan=2>" . $coverage . "%</td>";
		}
		$html .= "</tr>";
		$html .= "<tr><td class='title'>Number of Days of Stock Outage</td>";
		//Loop all vaccines and append the needs coverage for that particular vaccine in that store
		foreach ($vaccines as $vaccine) {
			$html .= "<td colspan=2>N/A</td>";
		}
		$html .= "</tr>";
		//New Line

		$html .= "<tr><td class='title'>Stock Availability (Stock at Hand)</td>";
		//Loop all vaccines and append the stock at hand for that particular vaccine in that store
		foreach ($vaccines as $vaccine) {
			$stock_at_hand = Disbursements::getNationalPeriodBalance($vaccine -> id, $to);
			$html .= "<td colspan=2>" . $stock_at_hand . "</td>";
		}
		$html .= "</tr>";
		//New Line

		$html .= "<tr><td class='title'>Stock at Hand Forecast (In Months)</td>";
		//Loop all vaccines and append the stock at hand forecast for that particular vaccine in that store
		foreach ($vaccines as $vaccine) {
			$population = Regional_Populations::getNationalPopulation($year);
			$population = str_replace(",", "", $population);
			$monthly_requirement = ceil(($vaccine -> Doses_Required * $population * $vaccine -> Wastage_Factor) / 12);
			$stock_at_hand = Disbursements::getNationalPeriodBalance($vaccine -> id, $to);
			$forecast = $stock_at_hand / $monthly_requirement;
			$forecast = number_format($forecast, 2, '.', '');
			$html .= "<td colspan=2>" . $forecast . "</td>";
		}
		$html .= "</tr>";
		//New Line

		$html .= "<tr><td class='title'>Shipments Expected/Received Dates</td>";
		//Loop all vaccines and append the shipments expected for that particular vaccine in that store
		foreach ($vaccines as $vaccine) {
			//Get and display the expected dates
			$plans = Provisional_Plan::getYearlyPlan($year, $vaccine -> id);
			$plans_string = "";
			$html .= "<td><table>";
			foreach ($plans as $plan) {
				$plans_string = $plan -> expected_date . " (" . $plan -> expected_amount . ") ";
				$html .= "<tr><td class='no_border'>" . $plans_string . "</td></tr>";
			}
			if (strlen($plans_string) < 1) {
				$plans_string = "None";
				$html .= "<tr><td class='no_border'>" . $plans_string . "</td></tr>";
			}

			$html .= "</table></td>";

			$receipts = Batches::getYearlyReceipts($year, $vaccine -> id);
			$receipts_string = "";
			$html .= "<td><table>";
			foreach ($receipts as $receipt) {
				$receipts_string = $receipt -> Arrival_Date . " (" . $receipt -> Total . ") ";
				$html .= "<tr><td class='no_border'>" . $receipts_string . "</td></tr>";
			}
			if (strlen($receipts_string) < 1) {
				$receipts_string = "None";
				$html .= "<tr><td class='no_border'>" . $receipts_string . "</td></tr>";
			}

			$html .= "</table></td>";

		}
		$html .= "</tr>";
		//New Line

		//New Line
		/*header("Content-type: application/vnd.ms-excel; name='excel'");
		 header("Content-Disposition: filename=Country_Vaccine_Status_Summary.xls");
		 // Fix for crappy IE bug in download.
		 header("Pragma: ");
		 header("Cache-Control: ");*/
		$result = $headers . $data;

		$html .= "</table>";
		return $html;
	}

	function create_regional_reports($region) {
		$year = date('Y');
		$total_vaccines = Vaccines::getTotalNumber();
		$html = "<table border='2px solid black' class='regionals'>";
		$html .= "<tr ><th rowspan=3>Analytical Areas</th><th style='text-align: center' colspan=" . $total_vaccines . ">Summary Report for Vaccine Status in Kenya</th></tr>";
		$html .= "<tr ><th style='text-align: center' colspan=" . $total_vaccines . ">Depot: " . $region -> name . " Reporting Date: " . date("d/m/Y") . "</th></tr>";
		$headers = "Summary Report for Vaccine Status in Kenya\n\t\nDepot: National Store\tReporting Date: " . date("d/m/Y") . "\t\n";
		$data = "Analytical Areas\t";
		$vaccines = Vaccines::getAll();

		$from = date("U", mktime(0, 0, 0, 1, 1, date('Y')));
		//This sets the begining date as the 1st of january of that particular year
		$to = date('U');
		//This sets the end date as the current time when the report is being generated
		//Loop all vaccines and append the vaccine name in the excel sheet content.
		$html .= "<tr>";
		foreach ($vaccines as $vaccine) {
			$html .= "<td  style='background-color:#" . $vaccine -> Tray_Color . "'>" . $vaccine -> Name . "</td>";
		}
		$html .= "</tr>";
		//New Line!
		//Begin adding data for the areas being analysed!

		$html .= "<tr><td class='title'>Annual Needs Coverage</td>";
		//Loop all vaccines and append the needs coverage for that particular vaccine in that store
		foreach ($vaccines as $vaccine) {
			$population = Regional_Populations::getRegionalPopulation($region -> id, $year);
			$yearly_requirement = $population * $vaccine -> Doses_Required * $vaccine -> Wastage_Factor;
			$vaccine_totals = Disbursements::getRegionalReceiptsTotals($region -> id, $vaccine -> id, $from, $to);
			$coverage = ceil(($vaccine_totals / $yearly_requirement) * 100);
			$html .= "<td >" . $coverage . "%</td>";
		}
		$html .= "</tr>";
		//New Line

		$html .= "<tr><td class='title'>Stock Availability (Stock at Hand)</td>";
		//Loop all vaccines and append the stock at hand for that particular vaccine in that store
		foreach ($vaccines as $vaccine) {
			$stock_at_hand = Disbursements::getRegionalPeriodBalance($region -> id, $vaccine -> id, $to);
			$html .= "<td >" . $stock_at_hand . "</td>";
		}
		$html .= "</tr>";
		//New Line

		$html .= "<tr><td class='title'>Stock at Hand Forecast (In Months)</td>";
		//Loop all vaccines and append the stock at hand forecast for that particular vaccine in that store
		foreach ($vaccines as $vaccine) {
			$population = Regional_Populations::getRegionalPopulation($region -> id, $year);
			$population = str_replace(",", "", $population);
			$monthly_requirement = ceil(($vaccine -> Doses_Required * $population * $vaccine -> Wastage_Factor) / 12);
			$stock_at_hand = Disbursements::getRegionalPeriodBalance($region -> id, $vaccine -> id, $to);
			$forecast = $stock_at_hand / $monthly_requirement;
			$forecast = number_format($forecast, 2, '.', '');
			$html .= "<td>" . $forecast . "</td>";
		}
		$html .= "</tr></table>";
		return $html;
	}

	public function update_timestamps() {
		$disbursements = Disbursements::getAll();
		foreach ($disbursements as $disbursement) {
			$current = $disbursement -> Date_Issued;
			$converted = strtotime($current);
			$test = date("d/m/Y", $converted);
			echo $current . " becomes " . $converted . " which is " . $test . "<br>";
			$disbursement -> Date_Issued_Timestamp = $converted;
			$disbursement -> save();
		}
	}

}
