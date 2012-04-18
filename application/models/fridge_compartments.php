<?php

class Fridge_Compartments extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Name', 'varchar', 20);
		$this -> hasColumn('Identifier', 'varchar', 20);
	}

	public function setUp() {
		$this -> setTableName('fridge_compartments');
		$this -> hasMany('Vaccines as Vaccines', array('local' => 'id', 'foreign' => 'Fridge_Compartment'));
	}

	public function getAll() {
		$query = Doctrine_Query::create() -> select("id,Name") -> from("Fridge_Compartments");
		$compartments = $query -> execute();
		return $compartments;
	}

	public function getCompartmentVaccines($identifier) {
		$query = Doctrine_Query::create() -> select("*") -> from("Fridge_Compartments")->where("Identifier = '$identifier'");
		$compartments = $query -> execute();
		$vaccines = $compartments[0]->Vaccines;
		return $vaccines;
	}

}
