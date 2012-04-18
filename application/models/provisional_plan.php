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

	public function getYearlyPlan($year,$vaccine) {
		$query = Doctrine_Query::create() -> select("*") -> from("Provisional_Plan") -> where("vaccine = '$vaccine' and year = '$year'");
		$plans = $query -> execute();
		return $plans;
	}

}
