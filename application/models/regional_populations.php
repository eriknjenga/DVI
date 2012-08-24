<?php

class Regional_Populations extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('id', 'varchar', 10);
		$this -> hasColumn('name', 'varchar', 100);
		$this -> hasColumn('population', 'varchar', 20);
		$this -> hasColumn('year', 'varchar', 5);
		$this -> hasColumn('region_id', 'varchar', 5);
	}

	public function setUp() {
		$this -> setTableName('regional_populations');
	}

	public static function getRegionalPopulation($region, $year) {
		$query = Doctrine_Query::create() -> select("population") -> from("regional_populations") -> where("region_id = '$region' and year='$year'");
		$population = $query -> execute(array(), Doctrine::HYDRATE_ARRAY);
		if (isset($population[0])) {
			return str_replace(',', '', $population[0]['population']);
		} else {
			$query = Doctrine_Query::create() -> select("population") -> from("regional_populations") -> where("region_id = '$region'") -> OrderBy("id desc") -> limit(0);
			$population = $query -> execute(array(), Doctrine::HYDRATE_ARRAY);
			if (isset($population[0])) {
				return str_replace(',', '', $population[0]['population']);
			} else {
				return '0';
			}
		}
	}

	public static function getNationalPopulation($year) {
		$regions = Regions::getAllRegions();
		$total_population = 0;
		foreach ($regions as $region) {
			$region_id = $region->id;
			$query = Doctrine_Query::create() -> select("population") -> from("regional_populations") -> where("region_id = '$region_id' and year='$year'");
			$population_object = $query -> execute(array(), Doctrine::HYDRATE_ARRAY);
			if (isset($population_object[0])) {
				$total_population += str_replace(',', '', $population_object[0]['population']);
			} else {
				$query = Doctrine_Query::create() -> select("population") -> from("regional_populations") -> where("region_id = '$region_id'") -> OrderBy("id desc") -> limit(0);
				$population_object = $query -> execute(array(), Doctrine::HYDRATE_ARRAY); 
				if (isset($population_object[0])) {
					$total_population += str_replace(',', '', $population_object[0]['population']);
				} else {
					$total_population += 0;
				}
			}
		}
		return $total_population;
	}

	public static function getAllForRegion($region) {
		$query = Doctrine_Query::create() -> select("population,year") -> from("regional_populations") -> where("region_id = '$region'");
		$populations = $query -> execute();
		return $populations;
	}

}
