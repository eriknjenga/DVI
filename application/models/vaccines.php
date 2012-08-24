<?php
class Vaccines extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Name', 'varchar', 50);
		$this -> hasColumn('Designation', 'int', 20);
		$this -> hasColumn('Formulation', 'varchar', 2);
		$this -> hasColumn('Administration', 'int', 2);
		$this -> hasColumn('Presentation', 'varchar', 20);
		$this -> hasColumn('Vaccine_Packed_Volume', 'varchar', 5);
		$this -> hasColumn('Diluents_Packed_Volume', 'varchar', 5);
		$this -> hasColumn('Vaccine_Vial_Price', 'varchar', 5);
		$this -> hasColumn('Vaccine_Dose_Price', 'varchar', 5);
		$this -> hasColumn('Added_By', 'varchar', 3);
		$this -> hasColumn('Timestamp', 'varchar', 32);
		$this -> hasColumn('Doses_Required', 'varchar', 5);
		$this -> hasColumn('Wastage_Factor', 'varchar', 5);
		$this -> hasColumn('Tray_Color', 'varchar', 10);
		$this -> hasColumn('Active', 'varchar', 5);
		$this -> hasColumn('Fridge_Compartment', 'varchar', 5);
	}

	public function setUp() {
		$this -> setTableName('vaccines');
		$this -> hasOne('User as User', array('local' => 'Added_By', 'foreign' => 'id'));
		$this -> hasOne('Fridge_Compartments as Compartment', array('local' => 'Fridge_Compartment', 'foreign' => 'id'));
		$this -> hasMany('Batches as Batches', array('local' => 'id', 'foreign' => 'Vaccine_Id'));
	}

	public static function getAll() {
		$query = Doctrine_Query::create() -> select("Name,Doses_Required,Wastage_Factor,Added_By,Timestamp,Tray_Color") -> from("vaccines")->orderBy("Name asc");
		$vaccines = $query -> execute();
		return $vaccines;
	}

	public static function getTotalNumber() {
		$query = Doctrine_Query::create() -> select("count(*) as Total") -> from("vaccines");
		$vaccines = $query -> execute();
		return $vaccines[0] -> Total;
	}

	public static function getAll_Minified() {
		$query = Doctrine_Query::create() -> select("id,Name,Doses_Required,Wastage_Factor,Tray_Color") -> from("vaccines") -> where("Active = '1'")->orderBy("Name asc");
		$vaccines = $query -> execute();
		return $vaccines;
	}

	public static function getVaccine($id) {
		$query = Doctrine_Query::create() -> select("Name,Doses_Required,Wastage_Factor") -> from("vaccines") -> where("id = '$id'");
		$vaccine = $query -> execute();
		return $vaccine[0];
	}

}
