<?php

class Batches extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Batch_Number', 'varchar', 10);
		$this -> hasColumn('Expiry_Date', 'varchar', 20);
		$this -> hasColumn('Manufacturing_Date', 'varchar', 20);
		$this -> hasColumn('Manufacturer', 'varchar', 50);
		$this -> hasColumn('Lot_Number', 'varchar', 10);
		$this -> hasColumn('Origin_Country', 'varchar', 100);
		$this -> hasColumn('Arrival_Date', 'varchar', 20);
		$this -> hasColumn('Quantity', 'varchar', 15);
		$this -> hasColumn('Timestamp', 'varchar', 20);
		$this -> hasColumn('Added_By', 'varchar', 5);
		$this -> hasColumn('Vaccine_Id', 'varchar', 5);
		$this -> hasColumn('Year', 'varchar', 4);
	}

	public function setUp() {
		$this -> setTableName('batches');
		$this -> hasOne('User as User', array('local' => 'Added_By', 'foreign' => 'id'));
	}

	public static function getVaccineTotals() {
		$query = Doctrine_Query::create() -> select("Vaccine_Id, SUM(Quantity) as Totals") -> from("Batches") -> groupBy("Vaccine_Id");
		$totals = $query -> execute(array(), Doctrine::HYDRATE_ARRAY);
		return $totals;
	}

	//Retrieve a batch given its ID
	public static function getBatch($id) {
		$query = Doctrine_Query::create() -> select("*") -> from("Batches") -> where("id = '$id'") -> limit('1');
		$batch = $query -> execute();
		return $batch;
	}

	public static function getYearlyReceipts($year, $vaccine) {
		$query = Doctrine_Query::create() -> select("Batch_Number, Quantity as Total, Arrival_Date") -> from("Batches") -> where("Vaccine_Id = '$vaccine' and Arrival_Date like '%$year%'");
		$receipts = $query -> execute();
		return $receipts;
	}

	public static function getDistinctYears($vaccine) {
		$query = Doctrine_Query::create() -> select("distinct(Year) as Year") -> from("Batches") -> where("Vaccine_Id = '$vaccine'") -> orderBy("Year Desc");
		$years = $query -> execute();
		return $years;
	}

	public static function getTotalNumber($vaccine) {
		$query = Doctrine_Query::create() -> select("COUNT(*) as Total_Batches") -> from("Batches") -> where("Vaccine_Id = '$vaccine'");
	
		$count = $query -> execute();
		return $count[0] -> Total_Batches;
	}

	public function getVaccineBatches($vaccine,$offset, $items) {
		$query = Doctrine_Query::create() -> select("*") -> from("Batches")->where("Vaccine_Id = '$vaccine'")->orderBy('id') -> offset($offset) -> limit($items);
		$batches = $query -> execute();
		return $batches;
	}

}
