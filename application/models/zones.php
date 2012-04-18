<?php

class Zones extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Name', 'varchar', 20);
	}

	public function setUp() {
		$this -> setTableName('zones'); 
	}

	public function getAll() {
		$query = Doctrine_Query::create() -> select("id,Name") -> from("zones");
		$zones = $query -> execute();
		return $zones;
	}

}
