<?php

class Power_Sources extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Name', 'varchar', 20);
	}

	public function setUp() {
		$this -> setTableName('power_sources'); 
	}

	public function getAll() {
		$query = Doctrine_Query::create() -> select("id,Name") -> from("Power_Sources");
		$sources = $query -> execute();
		return $sources;
	}

}
