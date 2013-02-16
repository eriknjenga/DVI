<?php
class Stock_Out_Recipient extends Doctrine_Record {

	public function setTableDefinition() {
		$this -> hasColumn('Email', 'varchar', 50);
		$this -> hasColumn('Full_Name', 'varchar', 50);
		$this -> hasColumn('District', 'varchar', 10);
		$this -> hasColumn('Disabled', 'varchar', 2);
	}

	public function setUp() {
		$this -> setTableName('stock_out_recipient');
		$this -> hasOne('Districts as District_Object', array('local' => 'District', 'foreign' => 'id'));
	}

	public static function mappingExists($email, $district) {
		$query = Doctrine_Query::create() -> select("*") -> from("Stock_Out_Recipient") -> where("Email = '$email' and District = '$district'");
		$user = $query -> execute();
		if (isset($user[0])) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public static function getRecipient($id) {
		$query = Doctrine_Query::create() -> select("*") -> from("Stock_Out_Recipient") -> where("id = '$id'");
		$user = $query -> execute();
		return $user[0];
	}

	public static function getTotalNumber() {
		$query = Doctrine_Query::create() -> select("COUNT(*) as Total_Users") -> from("Stock_Out_Recipient");
		$count = $query -> execute();
		return $count[0] -> Total_Users;
	}

	public function getPagedUsers($offset, $items) {
		$query = Doctrine_Query::create() -> select("*") -> from("Stock_Out_Recipient") -> orderBy("id") -> offset($offset) -> limit($items);
		$users = $query -> execute();
		return $users;
	}

}
