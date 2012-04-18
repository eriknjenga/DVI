<?php

class Refrigerant_Gas_Types extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Name', 'varchar', 20);
	}

	public function setUp() {
		$this -> setTableName('refrigerant_gas_types'); 
	}

	public function getAll() {
		$query = Doctrine_Query::create() -> select("id,Name") -> from("Refrigerant_Gas_Types");
		$types = $query -> execute();
		return $types;
	}

}
