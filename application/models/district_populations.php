<?php

class District_Populations extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('name', 'varchar', 100);
		$this -> hasColumn('population', 'varchar', 20);
		$this -> hasColumn('year', 'varchar', 5);
		$this -> hasColumn('district_id', 'varchar', 10);
	}

	public function setUp() {
		$this -> setTableName('district_populations');
	}

	public static function getDistrictPopulation($district, $year) {
		$query = Doctrine_Query::create() -> select("population") -> from("district_populations") -> where("district_id = '$district' and year = '$year'");
		$population = $query -> execute(array(), Doctrine::HYDRATE_ARRAY);
		if (isset($population[0])) {
			return $population[0]['population'];
		} else {
			$query = Doctrine_Query::create() -> select("population") -> from("district_populations") -> where("district_id = '$district'") -> OrderBy("id desc") -> limit(1);
			$population = $query -> execute(array(), Doctrine::HYDRATE_ARRAY);
			if (isset($population[0])) {
				return $population[0]['population'];
			} else {
				return '0';
			}
		}

	}

	public static function getAllForDistrict($district) {
		$query = Doctrine_Query::create() -> select("population,year") -> from("district_populations") -> where("district_id = '$district'");
		$populations = $query -> execute();
		return $populations;
	}

}
