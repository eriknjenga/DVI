<?php

class Vaccine_Formulations extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Name', 'varchar', 20);
	}

	public function setUp() {
		$this -> setTableName('vaccine_formulations'); 
	}

	public function getAll() {
		$query = Doctrine_Query::create() -> select("id,Name") -> from("Vaccine_Formulations");
		$formulations = $query -> execute();
		return $formulations;
	}

}
