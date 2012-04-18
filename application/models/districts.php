<?php
class Districts extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('name', 'varchar', 100);
		$this -> hasColumn('province', 'int', 14);
		$this -> hasColumn('comment', 'varchar', 32);
		$this -> hasColumn('flag', 'int', 32);
		$this -> hasColumn('latitude', 'varchar', 100);
		$this -> hasColumn('longitude', 'varchar', 100);
		$this -> hasColumn('disabled', 'varchar', 1);
	}

	public function setUp() {
		$this -> setTableName('districts');
		$this -> hasOne('Provinces as Province', array('local' => 'province', 'foreign' => 'id'));
	}

	public function getAllDistricts() {
		$query = Doctrine_Query::create() -> select("id,name,province,latitude,longitude,disabled") -> from("Districts") -> orderBy("name");
		$districts = $query -> execute();
		return $districts;
	}

	public function getProvinceDistricts($province) {
		$query = Doctrine_Query::create() -> select("id,name") -> from("Districts") -> where("province = '$province'");
		$districts = $query -> execute();
		return $districts;
	}

	public function getDistrictProvince($district) {
		$query = Doctrine_Query::create() -> select("province") -> from("Districts") -> where("id = '$district'");
		$province = $query -> fetchOne();
		return $province;
	}

	public static function getDistrictName($id) {
		$query = Doctrine_Query::create() -> select("name") -> from("Districts") -> where("ID = '$id'");
		$district = $query -> execute(array(), Doctrine::HYDRATE_ARRAY);
		return $district[0]['name'];
	}

	public static function getMappedDistricts() {
		$query = Doctrine_Query::create() -> select("id,name,latitude,longitude") -> from("Districts") -> where('latitude != ""');
		$districts = $query -> execute();
		return $districts;
	}

	public static function getTotalNumber() {
		$query = Doctrine_Query::create() -> select("COUNT(*) as Total_Districts") -> from("Districts");
		$count = $query -> execute();
		return $count[0] -> Total_Districts;
	}

	public function getPagedDistricts($offset, $items) {
		$query = Doctrine_Query::create() -> select("id,name,province,latitude,longitude,disabled") -> from("Districts") -> orderBy("name") -> offset($offset) -> limit($items);
		$districts = $query -> execute();
		return $districts;
	}

	public static function getDistrict($id) {
		$query = Doctrine_Query::create() -> select("*") -> from("Districts") -> where("ID = '$id'");
		$district = $query -> execute();
		return $district[0];
	}

}
