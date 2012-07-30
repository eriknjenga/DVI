<?php
class Bad_Vaccines extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Date_Issued', 'varchar', 20);
		$this -> hasColumn('Quantity', 'varchar', 20);
		$this -> hasColumn('Batch_Number', 'varchar', 20);
		$this -> hasColumn('Voucher_Number', 'varchar', 20);
		$this -> hasColumn('Stock_At_Hand', 'varchar', 20);
		$this -> hasColumn('Vaccine_Id', 'varchar', 10);
		$this -> hasColumn('Issued_To_Region', 'varchar', 5);
		$this -> hasColumn('Issued_To_District', 'varchar', 5);
		$this -> hasColumn('Issued_To_Facility', 'varchar', 10);
		$this -> hasColumn('Issued_To_National', 'varchar', 2);
		$this -> hasColumn('Issued_By_National', 'varchar', 5);
		$this -> hasColumn('Issued_By_Region', 'varchar', 5);
		$this -> hasColumn('Issued_By_District', 'varchar', 5);
		$this -> hasColumn('Timestamp', 'varchar', 32);
		$this -> hasColumn('Added_By', 'varchar', 20);
		$this -> hasColumn('Batch_Id', 'varchar', 5);
		$this -> hasColumn('Date_Issued_Timestamp', 'varchar', 32);
		$this -> hasColumn('Owner', 'varchar', 20);
		$this -> hasColumn('Comment', 'text');
	}

	public function setUp() {
		$this -> setTableName('bad_vaccines');
	}

}
