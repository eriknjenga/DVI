<?php

class Vaccine_Administration extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Name', 'varchar', 20);
	}

	public function setUp() {
		$this -> setTableName('vaccine_administration'); 
	}

	public function getAll() {
		$query = Doctrine_Query::create() -> select("id,Name") -> from("Vaccine_Administration");
		$administration = $query -> execute();
		return $administration;
	}

}
