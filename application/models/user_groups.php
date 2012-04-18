<?php

class User_Groups extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Name', 'varchar', 50);
		$this -> hasColumn('Identifier', 'varchar', 20);
	}

	public function setUp() {
		$this -> setTableName('user_group');
		$this -> hasMany('User_Access as User_Access', array('local' => 'id', 'foreign' => 'user_group'));
	}

	public function getAllGroups() {
		$query = Doctrine_Query::create() -> select("id,Name") -> from("User_Groups");
		$groups = $query -> execute();
		return $groups;
	}

}
