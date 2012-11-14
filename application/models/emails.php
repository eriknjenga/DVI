<?php

class Emails extends Doctrine_Record {

	public function setTableDefinition() {
		$this -> hasColumn('email', 'varchar', 200);
		$this -> hasColumn('provincial', 'varchar', 30);
		$this -> hasColumn('district', 'varchar', 30);
		$this -> hasColumn('national', 'int', 11);
		$this -> hasColumn('valid', 'int', 11);
		$this -> hasColumn('stockout', 'int', 11);
		$this -> hasColumn('consumption', 'int', 10);
		$this -> hasColumn('coldchain', 'int', 10);
		$this -> hasColumn('recepient', 'varchar', 50);
		$this -> hasColumn('number', 'varchar', 50);
	}

	public function setUp() {
		$this -> setTableName('emails');
	}

	//assists to dosplay data from db to view
	public static function emailandsms() {
		$query = Doctrine_Query::create() -> select("id,number,email,stockout,consumption,coldchain,recepient,valid") -> from("emails") -> orderBy("id");
		$emails = $query -> execute(array(), Doctrine::HYDRATE_ARRAY);
		return $emails;
	}

	public static function getNational() {
		$query = Doctrine_Query::create() -> select("id,number,email,stockout,consumption,coldchain,recepient,valid") -> from("emails") -> where("national = '1'") -> orderBy("id");
		$emails = $query -> execute(array(), Doctrine::HYDRATE_ARRAY);
		return $emails;
	}

	public static function getRegional($region) {
		$query = Doctrine_Query::create() -> select("id,number,email,stockout,consumption,coldchain,recepient,valid") -> from("emails") -> where("provincial = '$region'") -> orderBy("id");
		$emails = $query -> execute(array(), Doctrine::HYDRATE_ARRAY);
		return $emails;
	}

	public static function getDistrict($district) {
		$query = Doctrine_Query::create() -> select("id,number,email,stockout,consumption,coldchain,recepient,valid") -> from("emails") -> where("district = '$district'") -> orderBy("id");
		$emails = $query -> execute(array(), Doctrine::HYDRATE_ARRAY);
		return $emails;
	}

	public static function getEmails() {
		$query = Doctrine_Query::create() -> select("id,email,valid") -> from("emails") -> where("national = '1'") -> orderBy("email asc");
		$emails = $query -> execute(array(), Doctrine::HYDRATE_ARRAY);
		return $emails;
	}

	public static function getProvinceEmails($store) {

		$query = Doctrine_Query::create() -> select("id,email,valid") -> from("emails") -> where("provincial = '$store'") -> orderBy("email asc");
		$emails = $query -> execute(array(), Doctrine::HYDRATE_ARRAY);
		return $emails;
	}

	public static function getDistrictEmails($store) {
		$query = Doctrine_Query::create() -> select("id,email,valid") -> from("emails") -> where("district ='$store'") -> orderBy("id asc");
		$emails = $query -> execute(array(), Doctrine::HYDRATE_ARRAY);
		return $emails;
	}

	//sets valid to one in the db
	public static function setValidEmails($code) {
		$query = Doctrine_Query::create() -> update('emails') -> set('valid', '1') -> where('id ="' . $code . '"');
		$emails = $query -> execute();
		return $emails;

	}

	//sets valid to zero in db
	public static function setInvalidEmails($code) {
		$query = Doctrine_Query::create() -> update('emails') -> set('valid', '0') -> where('id ="' . $code . '"');
		$emails = $query -> execute();
		return $emails;

	}

	public static function getEmail($id) {
		$query = Doctrine_Query::create() -> select("*") -> from("emails") -> where("id = '$id'");
		$emails = $query -> execute();
		return $emails[0];
	}

}
?>