<?php

class Vaccine_Orders extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('District', 'varchar', 10);
		$this -> hasColumn('Region', 'varchar', 10);
		$this -> hasColumn('Vaccine', 'varchar', 10);
		$this -> hasColumn('Quantity', 'varchar', 20);
		$this -> hasColumn('Active', 'varchar', 2);
		$this -> hasColumn('Approved', 'varchar', 2);
		$this -> hasColumn('Accepted_Quantity', 'varchar', 20);
		$this -> hasColumn('Pickup_Date', 'varchar', 20);
		$this -> hasColumn('Accepted_By', 'varchar', 10);
		$this -> hasColumn('Made_By', 'varchar', 10);
		$this -> hasColumn('Order_Made_On', 'varchar', 32);
		$this -> hasColumn('Order_Accepted_On', 'varchar', 32);
	}

	public function setUp() {
		$this -> setTableName('vaccine_orders');
		$this -> hasOne('Districts as Origin_District', array('local' => 'District', 'foreign' => 'id'));
		$this -> hasOne('Regions as Origin_Region', array('local' => 'Region', 'foreign' => 'id'));
		$this -> hasOne('User as Order_Maker', array('local' => 'Made_By', 'foreign' => 'id'));
		$this -> hasOne('Vaccines as Vaccine_Ordered', array('local' => 'Vaccine', 'foreign' => 'id'));
		$this -> hasOne('User as Order_Accepter', array('local' => 'Accepted_By', 'foreign' => 'id'));
	}

	public static function getTotalNumber() {
		$query = Doctrine_Query::create() -> select("COUNT(*) as Total_Orders") -> from("Vaccine_Orders");
		$count = $query -> execute();
		return $count[0] -> Total_Orders;
	}

	public function getPagedOrders($offset, $items) {
		$query = Doctrine_Query::create() -> select("*") -> from("Vaccine_Orders") -> OrderBy("id desc") -> offset($offset) -> limit($items);
		$orders = $query -> execute();
		return $orders;
	}

	public static function getTotalRegionalNumber($region) {
		$query = Doctrine_Query::create() -> select("COUNT(*) as Total_Orders") -> from("Vaccine_Orders") -> where("Region = '$region'");
		$count = $query -> execute();
		return $count[0] -> Total_Orders;
	}

	public function getPagedRegionalOrders($region, $offset, $items) {
		$query = Doctrine_Query::create() -> select("*") -> from("Vaccine_Orders") -> where("Region = '$region'") -> OrderBy("id desc") -> offset($offset) -> limit($items);
		$orders = $query -> execute();
		return $orders;
	}

	public static function getTotalDistrictNumber($district) {
		$query = Doctrine_Query::create() -> select("COUNT(*) as Total_Orders") -> from("Vaccine_Orders") -> where("District = '$district'");
		$count = $query -> execute();
		return $count[0] -> Total_Orders;
	}

	public function getPagedDistrictOrders($district, $offset, $items) {
		$query = Doctrine_Query::create() -> select("*") -> from("Vaccine_Orders") -> where("District = '$district'") -> OrderBy("id desc") -> offset($offset) -> limit($items);
		$orders = $query -> execute();
		return $orders;
	}

	public static function getDetails($id) {
		$query = Doctrine_Query::create() -> select("*") -> from("Vaccine_Orders") -> where("id = '$id'");
		$order = $query -> execute();
		return $order[0];
	}

}
