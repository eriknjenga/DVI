<?php

class Other_Orders extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('District', 'varchar', 10);
		$this -> hasColumn('Region', 'varchar', 10);
		$this -> hasColumn('Items_Ordered', 'text'); 
		$this -> hasColumn('Active', 'varchar', 2);
		$this -> hasColumn('Approved', 'varchar', 2);  
		$this -> hasColumn('Approved_By', 'varchar', 10);
		$this -> hasColumn('Made_By', 'varchar', 10);
		$this -> hasColumn('Timestamp', 'varchar', 32);
		$this -> hasColumn('Reply', 'text');
	}

	public function setUp() {
		$this -> setTableName('other_orders');
		$this -> hasOne('Districts as Origin_District', array('local' => 'District', 'foreign' => 'id'));
		$this -> hasOne('Regions as Origin_Region', array('local' => 'Region', 'foreign' => 'id'));
		$this -> hasOne('User as Order_Maker', array('local' => 'Made_By', 'foreign' => 'id'));
		$this -> hasOne('User as Order_Accepter', array('local' => 'Approved_By', 'foreign' => 'id'));
	}

}
