<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Create_Archives extends MY_Controller {
    function __construct()
    {
        parent::__construct();
    }
 
public function index()
{ 

//Get the Earliest Disbursement Date and the Earliest Archive Date. Do the neccesary!

$earliest_disbursement_date = Disbursements::getEarliestDisbursement();
$earliest_archive_date = Archive_Logs::getEarliestArchive(); 
if($earliest_archive_date == null){
$earliest_archive_date = date('m/d/Y');
}
$split_disbursement_date = explode('/',$earliest_disbursement_date);
$split_archive_date = explode('/',$earliest_archive_date);

var_dump($split_disbursement_date);
var_dump($split_archive_date);
$total_months = (($split_archive_date[0] - $split_disbursement_date[0])+(($split_archive_date[2] - $split_disbursement_date[2])*12));

$dates_to_archive = array();
for($archive_month = 0; $archive_month<$total_months; $archive_month++){
$expected_month = $archive_month+$split_disbursement_date[0];
$year = $split_disbursement_date[2];
//echo $expected_month." and ".$year;
if($expected_month>12){
$year += ceil(($expected_month - 12)/12);
$expected_month -= 12;
}
$archive_date = $expected_month."/15/".$year;
$dates_to_archive[$archive_month] = $archive_date; 
}

var_dump($dates_to_archive);

//foreach($dates_to_archive as $date_to_archive){
//Check if this date's archive has been done. If not do it now! 
$expected_archive_date = "6/15/2011"; 
$expected_archive_timestamp = strtotime($expected_archive_date); 
$archive_exists = Archive_Logs::archiveExists($expected_archive_date);
if(!$archive_exists){
 echo "Creating Archive!";

 //Loop through all vaccines
 $vaccines = Vaccines::getAll();
 $regions = Regions::getAllRegions();
 $districts = Districts::getAllDistricts();
 foreach($vaccines as $vaccine){ 
	 //National Archive First
	 $national_archive = new Archives();
	 $national_archive->National_Store = "0";
	 $national_archive->Stock_At_Hand = Disbursements::getNationalStockAtHand($vaccine->id,$expected_archive_timestamp); 
	 $national_archive->Date_Of_Archive = $expected_archive_date;
	 $national_archive->Vaccine_Id = $vaccine->id;
	  
	 $national_archive->save();
	//Do archives for all the regions
	 foreach ($regions as $region){
		 $regional_archive = new Archives();
		 $regional_archive->Regional_Store_Id = $region->id;
		 $regional_archive->Stock_At_Hand = Disbursements::getRegionalStockAtHand($region->id, $vaccine->id,$expected_archive_timestamp); 
		 $regional_archive->Date_Of_Archive = $expected_archive_date;
		 $regional_archive->Vaccine_Id = $vaccine->id;
		 $regional_archive->save();
	 }
	 
	 //Do archives for all the districts. :-(
	 foreach($districts as $district){
	 	 $district_archive = new Archives();
		 $district_archive->District_Store_Id = $district->id;
		 $district_archive->Stock_At_Hand = Disbursements::getDistrictStockAtHand($district->id, $vaccine->id,$expected_archive_timestamp); 
		 $district_archive->Date_Of_Archive = $expected_archive_date;
		 $district_archive->Vaccine_Id = $vaccine->id;
		 $district_archive->save();
	 }
 }
 
 $archive_log = new Archive_Logs();
 $archive_log->Archive_Date = $expected_archive_date;
 $archive_log->Timestamp = date('U');
 $archive_log->Save();
}
else{
echo "This archive already exists!";
}


//}

}
 
}