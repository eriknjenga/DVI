<?php
class Fridges extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Item_Type', 'varchar', 2);
		$this -> hasColumn('Library_Id', 'varchar', 20);
		$this -> hasColumn('PQS', 'varchar', 2);
		$this -> hasColumn('Model_Name', 'varchar', 20);
		$this -> hasColumn('Manufacturer', 'varchar', 20);
		$this -> hasColumn('Power_Source', 'varchar', 2);
		$this -> hasColumn('Refrigerant_Gas_Type', 'varchar', 10);
		$this -> hasColumn('Net_Vol_4deg', 'varchar', 10);
		$this -> hasColumn('Net_Vol_Minus_20deg', 'varchar', 10);
		$this -> hasColumn('Freezing_Capacity', 'varchar', 10);
		$this -> hasColumn('Gross_Vol_4deg', 'varchar', 10);
		$this -> hasColumn('Gross_Vol_Minus_20deg', 'varchar', 10);
		$this -> hasColumn('Price', 'varchar', 10);
		$this -> hasColumn('Elec_To_Run', 'varchar', 10);
		$this -> hasColumn('Gas_To_Run', 'varchar', 10);
		$this -> hasColumn('Kerosene_To_Run', 'varchar', 10);
		$this -> hasColumn('Zone', 'varchar', 2);
		$this -> hasColumn('Active', 'varchar', 5);
	}

	public function setUp() {
		$this -> setTableName('fridges');
		$this -> hasOne('Power_Sources as Power', array('local' => 'Power_Source', 'foreign' => 'id'));
		$this -> hasOne('Item_Types as Type', array('local' => 'Item_Type', 'foreign' => 'id'));
		$this -> hasOne('Zones as Fridge_Zone', array('local' => 'Zone', 'foreign' => 'id'));
		$this -> hasOne('Refrigerant_Gas_Types as Gas_Type', array('local' => 'Refrigerant_Gas_Type', 'foreign' => 'id'));
	}

	public static function getTotalNumber() {
		$query = Doctrine_Query::create() -> select("COUNT(*) as Total_Fridges") -> from("Fridges");
		$count = $query -> execute();
		return $count[0] -> Total_Fridges;
	}

	public function getPagedFridges($offset, $items) {
		$query = Doctrine_Query::create() -> select("*") -> from("Fridges") -> orderBy("Model_Name") -> offset($offset) -> limit($items);
		$fridges = $query -> execute();
		return $fridges;
	}

	public static function getFridge($id) {
		$query = Doctrine_Query::create() -> select("*") -> from("Fridges") -> where("id = '$id'");
		$fridge = $query -> execute();
		return $fridge[0];
	}
	public function getAll() {
		$query = Doctrine_Query::create() -> select("id,Model_Name,Manufacturer") -> from("Fridges")->OrderBy("Manufacturer","DESC");
		$fridges = $query -> execute();
		return $fridges;
	}

}
