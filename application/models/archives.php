<?php

class Archives extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('National_Store', 'varchar', 2);
		$this -> hasColumn('Regional_Store_Id', 'varchar', 5);
		$this -> hasColumn('District_Store_Id', 'varchar', 5);
		$this -> hasColumn('Stock_At_Hand', 'varchar', 20);
		$this -> hasColumn('Date_Of_Archive', 'varchar', 20);
		$this -> hasColumn('Vaccine_Id', 'varchar', 20);
	}

	public function setUp() {
		$this -> setTableName('archives');
	}

}
