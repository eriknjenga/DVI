<?php

class District_Fridges extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('District', 'varchar', 100);
		$this -> hasColumn('Fridge', 'varchar', 20);
		$this -> hasColumn('Timestamp', 'varchar', 5);
	}

	public function setUp() {
		$this -> setTableName('district_fridges');
		$this -> hasOne('Fridges as Fridge_Equipment', array('local' => 'Fridge', 'foreign' => 'id'));
	}

	public static function getDistrictFridges($district) {
		$query = Doctrine_Query::create() -> select("Fridge") -> from("District_Fridges") -> where("District = '$district'");
		$fridges = $query -> execute();
		return $fridges;
	}

	public static function getFridge($id) {
		$query = Doctrine_Query::create() -> select("Fridge") -> from("District_Fridges") -> where("id = '$id'");
		$fridge = $query -> execute();
		return $fridge[0];
	}

}
