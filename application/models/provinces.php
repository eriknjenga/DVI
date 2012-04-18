<?php
 
class Provinces extends Doctrine_Record{
public function setTableDefinition() {
$this->hasColumn('name', 'varchar',100); 
}
public function setUp() {
$this->setTableName('provinces');
}
	
	public function getAllProvinces(){
	$query = Doctrine_Query::create()->select("id,name")->from("Provinces");
	$provinces = $query->execute();
	return $provinces; 
	}
	
	public static function getRegionName($id){
		$query = Doctrine_Query::create()->select("name")->from("Provinces")->where("ID = '$id'");
		$province = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
		return $province[0]['name']; 
	}
}