<?php

class National_Fridges extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Fridge', 'varchar', 20);
	}

	public function setUp() {
		$this -> setTableName('national_fridges');
		$this -> hasOne('Fridges as Fridge_Equipment', array('local' => 'Fridge', 'foreign' => 'id'));
	}

	public static function getNationalFridges() {
		$query = Doctrine_Query::create() -> select("Fridge") -> from("National_Fridges");
		$fridges = $query -> execute();
		return $fridges;
	}

	public static function getFridge($id) {
		$query = Doctrine_Query::create() -> select("Fridge") -> from("National_Fridges")->where("id = '$id'");
		$fridge = $query -> execute();
		return $fridge[0];
	}

}
