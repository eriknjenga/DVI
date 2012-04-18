<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Country_Stock_Map extends MY_Controller {
    function __construct()
    {
        parent::__construct();
          $this->load->library('MY_Xml_writer');
    }
 
public function plot($vaccine)
{  
 // Initiate class
    $xml = new MY_Xml_writer;
    
    $xml->setRootName('markers');
    $xml->initiate();
    $year = date('U');
    $mapped_districts = Districts::getMappedDistricts();
    $mapped_regions = Regions::getAllRegions();
   
    foreach($mapped_districts as $mapped_district){ 
    //$stock = Disbursements::getDistrictStockAtHand($mapped_district->id,$vaccine);
    $stock = Disbursements::getDistrictPeriodBalance($mapped_district->id,$vaccine,$year);
    $xml->startBranch('marker', array('name' => $mapped_district->name." District Store",'lat' => $mapped_district->latitude,'lng' => $mapped_district->longitude,'facility_id' => $mapped_district->id,'stock' => $stock)); // start branch 1-1
    $xml->endBranch();
    }
    
    foreach($mapped_regions as $mapped_region){ 
   // $stock = Disbursements::getRegionalStockAtHand($mapped_region->id,$vaccine);
    $stock = Disbursements::getRegionalPeriodBalance($mapped_region->id,$vaccine,$year);
    $xml->startBranch('marker', array('name' => $mapped_region->name,'lat' => $mapped_region->latitude,'lng' => $mapped_region->longitude,'facility_id' => $mapped_region->id,'stock' => $stock)); // start branch 1-1
    $xml->endBranch();
    } 
    //$stock = Disbursements::getNationalStockAtHand($vaccine);
    $stock = Disbursements::getNationalPeriodBalance($vaccine,$year);
    $xml->startBranch('marker', array('name' => "Central Vaccine Store",'lat' => "-1.304507",'lng' => "36.806191",'facility_id' => 0,'stock' => $stock)); // start branch 1-1
    $xml->endBranch();
    // Print the XML to screen
    $xml->getXml(true); 
}

 
}