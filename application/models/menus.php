<?php

class Menus extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Menu_Text', 'varchar', 20);
		$this -> hasColumn('Menu_Url', 'varchar', 100);
	}

	public function setUp() {
		$this -> setTableName('menus');
	}

	public function getAll() {
		$query = Doctrine_Query::create() -> select("*") -> from("Menus");
		$menus = $query -> execute();
		echo $this -> session -> userdata('user_group');
		return $menus;
	}

}
