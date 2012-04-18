<?php
class Additional_Facilities extends Doctrine_Record{

public function setTableDefinition() {
$this->hasColumn('District_Id', 'varchar',10);
$this->hasColumn('Facility', 'varchar', 10);
$this->hasColumn('Added_By', 'varchar', 10);
$this->hasColumn('Timestamp', 'varchar',20);
}

public function setUp() {
$this->setTableName('additional_facilities');
$this->hasOne('Facilities as Facilities', array(
			'local' => 'Facility',
			'foreign' => 'facilitycode'
			));
}
public function getExtraFacilities($district){
$query = Doctrine_Query::create()->select("Facility")->from("Additional_Facilities")->where("District_Id = '".$district."'");
$additional_facilities = $query->execute();
return $additional_facilities;
}
public function record_exists($district,$code){
$query = Doctrine_Query::create()->select("Facility")->from("Additional_Facilities")->where("District_Id = '".$district."' and Facility = '$code'");
$additional_facilities = $query->fetchOne(); 
if($additional_facilities){
return true;
}
else{
return false;
}
}

public static function get_facility($district,$code){
$query = Doctrine_Query::create()->select("Facility")->from("Additional_Facilities")->where("District_Id = '".$district."' and Facility = '$code'");
$additional_facilities = $query->fetchOne(); 
return $additional_facilities;
}
}