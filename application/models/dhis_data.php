<?php

class Dhis_Data extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Reporting_Period', 'varchar', 10);
		$this -> hasColumn('Facility_Name', 'text');
		$this -> hasColumn('Facility_Code', 'varchar', 10);
		$this -> hasColumn('Bcg_Admin', 'varchar', 10);
		$this -> hasColumn('Dpt1_Admin', 'varchar', 10);
		$this -> hasColumn('Dpt2_Admin', 'varchar', 10);
		$this -> hasColumn('Dpt3_Admin', 'varchar', 10);
		$this -> hasColumn('Fully_Immunized_Children', 'varchar', 10);
		$this -> hasColumn('Measles_Admin', 'varchar', 10);
		$this -> hasColumn('Opv1_Admin', 'varchar', 10);
		$this -> hasColumn('Opv2_Admin', 'varchar', 10);
		$this -> hasColumn('Opv3_Admin', 'varchar', 10);
		$this -> hasColumn('Opv_Birth_Admin', 'varchar', 10);
		$this -> hasColumn('Pn1_Admin', 'varchar', 10);
		$this -> hasColumn('Pn2_Admin', 'varchar', 10);
		$this -> hasColumn('Pn3_Admin', 'varchar', 10);
		$this -> hasColumn('Tt_Pregnant', 'varchar', 10);
		$this -> hasColumn('Tt_Trauma', 'varchar', 10);
		$this -> hasColumn('Vitamin_2_5', 'varchar', 10);
		$this -> hasColumn('Vitamin_12_59', 'varchar', 10);
		$this -> hasColumn('Vitamin_6_11', 'varchar', 10);
		$this -> hasColumn('Vitamin_Adult', 'varchar', 10);
		$this -> hasColumn('Vitamin_6_11_Months', 'varchar', 10);
		$this -> hasColumn('Vitamin_Older_Than_One_Year', 'varchar', 10);
		$this -> hasColumn('Vitamin_Lactating_Mothers', 'varchar', 10);
		$this -> hasColumn('Yellow_Admin', 'varchar', 10);
		$this -> hasColumn('Bcg_Stock', 'varchar', 10);
		$this -> hasColumn('Bcg_Received', 'varchar', 10);
		$this -> hasColumn('Bcg_Remaining', 'varchar', 10);
		$this -> hasColumn('Dpt_Stock', 'varchar', 10);
		$this -> hasColumn('Dpt_Received', 'varchar', 10);
		$this -> hasColumn('Dpt_Remaining', 'varchar', 10);
		$this -> hasColumn('Opv_Stock', 'varchar', 10);
		$this -> hasColumn('Opv_Received', 'varchar', 10);
		$this -> hasColumn('Opv_Remaining', 'varchar', 10);
		$this -> hasColumn('Pn_Stock', 'varchar', 10);
		$this -> hasColumn('Pn_Received', 'varchar', 10);
		$this -> hasColumn('Pn_Remaining', 'varchar', 10);
		$this -> hasColumn('Tt_Stock', 'varchar', 10);
		$this -> hasColumn('Tt_Received', 'varchar', 10);
		$this -> hasColumn('Tt_Remaining', 'varchar', 10);
		$this -> hasColumn('Yellow_Stock', 'varchar', 10);
		$this -> hasColumn('Yellow_Received', 'varchar', 10);
		$this -> hasColumn('Yellow_Remaining', 'varchar', 10);
		$this -> hasColumn('Measles_Stock', 'varchar', 10);
		$this -> hasColumn('Measles_Received', 'varchar', 10);
		$this -> hasColumn('Measles_Remaining', 'varchar', 10);
		$this -> hasColumn('Vitamin_100_Stock', 'varchar', 10);
		$this -> hasColumn('Vitamin_100_Received', 'varchar', 10);
		$this -> hasColumn('Vitamin_100_Remaining', 'varchar', 10);
		$this -> hasColumn('Vitamin_200_Stock', 'varchar', 10);
		$this -> hasColumn('Vitamin_200_Received', 'varchar', 10);
		$this -> hasColumn('Vitamin_200_Remaining', 'varchar', 10);
		$this -> hasColumn('Vitamin_50_Stock', 'varchar', 10);
		$this -> hasColumn('Vitamin_50_Received', 'varchar', 10);
		$this -> hasColumn('Vitamin_50_Remaining', 'varchar', 10);
		$this -> hasColumn('Vitamin_200000_Iu', 'varchar', 10);
		$this -> hasColumn('Vitamin_Lactating', 'varchar', 10);
		$this -> hasColumn('Vitamin_Supplement', 'varchar', 10);
	}

	public function setUp() {
		$this -> setTableName('dhis_data');
	}

}
