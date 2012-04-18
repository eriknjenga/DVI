<?php

class Item_Types extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Name', 'varchar', 20);
	}

	public function setUp() {
		$this -> setTableName('item_types'); 
	}

	public function getAll() {
		$query = Doctrine_Query::create() -> select("id,Name") -> from("Item_Types");
		$types = $query -> execute();
		return $types;
	}

}
