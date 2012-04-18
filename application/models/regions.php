<?php

class Regions extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('name', 'varchar', 100);
		$this -> hasColumn('latitude', 'varchar', 100);
		$this -> hasColumn('longitude', 'varchar', 100);
		$this -> hasColumn('disabled', 'varchar', 2);
	}

	public function setUp() {
		$this -> setTableName('regions');
	}

	public function getAllRegions() {
		$query = Doctrine_Query::create() -> select("id,name") -> from("Regions");
		$provinces = $query -> execute();
		return $provinces;
	}

	public static function getRegionName($id) {
		$query = Doctrine_Query::create() -> select("name") -> from("Regions") -> where("id = '$id'");
		$region = $query -> execute(array(), Doctrine::HYDRATE_ARRAY);
		return $region[0]['name'];
	}

	public static function getRegion($id) {
		$query = Doctrine_Query::create() -> select("*") -> from("Regions") -> where("id = '$id'");
		$region = $query -> execute();
		return $region[0];
	}

}
