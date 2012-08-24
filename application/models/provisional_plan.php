<?php

class Provisional_Plan extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('year', 'varchar', 2);
		$this -> hasColumn('vaccine', 'varchar', 5);
		$this -> hasColumn('expected_date', 'varchar', 5);
		$this -> hasColumn('expected_amount', 'varchar', 20);
		$this -> hasColumn('modified_by', 'varchar', 20);
	}

	public function setUp() {
		$this -> setTableName('provisional_plan');
	}

	public function getCurrentPlan($vaccine) {
		$current_year = date('Y');
		$query = Doctrine_Query::create() -> select("*") -> from("Provisional_Plan") -> where("vaccine = '$vaccine' and year = '$current_year'");
		$plan = $query -> execute();
		return $plan;
	}

	public function getYearlyPlan($year, $vaccine) {
		$query = Doctrine_Query::create() -> select("*") -> from("Provisional_Plan") -> where("vaccine = '$vaccine' and year = '$year'");
		$plans = $query -> execute();
		return $plans;
	}

	public static function getNextDelivery($vaccine) {
		$query = Doctrine_Query::create() -> select("datediff(str_to_date(expected_date,'%m/%d/%Y'),now()) as difference,date_format(str_to_date(expected_date,'%m/%d/%Y'),'%d/%m/%Y') as next_shipment") -> from("Provisional_Plan") -> where("vaccine = '$vaccine' and str_to_date(expected_date,'%m/%d/%Y') >= now()")->orderBy("str_to_date(expected_date,'%m/%d/%Y') asc")->limit('1');
		$plans = $query -> execute(array(), Doctrine::HYDRATE_ARRAY);
		return $plans;
	}

}
