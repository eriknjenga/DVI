<?php

class Facility_Fridges extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Facility', 'varchar', 100);
		$this -> hasColumn('Fridge', 'varchar', 20);
		$this -> hasColumn('Timestamp', 'varchar', 5);
	}

	public function setUp() {
		$this -> setTableName('facility_fridges');
	}


	public static function getFacilityFridges($facility) {
		$query = Doctrine_Query::create() -> select("Fridge") -> from("Facility_Fridges") -> where("Facility = '$facility'");
		$fridges = $query -> execute();
		return $fridges;
	}

}
