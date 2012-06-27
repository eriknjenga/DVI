<?php
class Disbursements extends Doctrine_Record {
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
		$this -> hasColumn('Total_Stock_Balance', 'varchar', 32);
		$this -> hasColumn('Total_Stock_In', 'varchar', 32);
		$this -> hasColumn('Total_Stock_Out', 'varchar', 32);
		$this -> hasColumn('Owner', 'varchar', 20);
	}

	public function setUp() {
		$this -> setTableName('disbursements');
		$this -> hasOne('Vaccines as Vaccines', array('local' => 'Vaccine_Id', 'foreign' => 'id'));
		$this -> hasOne('User as User', array('local' => 'Added_By', 'foreign' => 'id'));
		$this -> hasOne('Batches as Batch', array('local' => 'Batch_Number', 'foreign' => 'Batch_Number'));
		$this -> hasOne('Regions as Region_Issued_To', array('local' => 'Issued_To_Region', 'foreign' => 'id'));
		$this -> hasOne('Regions as Region_Issued_By', array('local' => 'Issued_By_Region', 'foreign' => 'id'));
		$this -> hasOne('Districts as District_Issued_To', array('local' => 'Issued_To_District', 'foreign' => 'id'));
		$this -> hasOne('Districts as District_Issued_By', array('local' => 'Issued_By_District', 'foreign' => 'id'));
		$this -> hasOne('Facilities as Facility_Issued_To', array('local' => 'Issued_To_Facility', 'foreign' => 'facilitycode'));
	}

	public static function getAll() {
		$query = Doctrine_Query::create() -> select("*") -> from("Disbursements");
		$disbursements = $query -> execute();
		return $disbursements;
	}

	public static function getNationalReceipts($identifier, $district_region_id) {
		$owner = "N0";
		if ($identifier == "provincial_officer") {
			$query = Doctrine_Query::create() -> select("Date_Issued,Quantity,Vaccine_Id,Batch_Number,Voucher_Number") -> from("Disbursements") -> where("Issued_To_Region = '$district_region_id' and Issued_By_National = '0' and Owner = '$owner'") -> orderBy("Date_Issued_Timestamp desc");

		} else if ($identifier == "district_officer") {
			$query = Doctrine_Query::create() -> select("Date_Issued,Quantity,Vaccine_Id,Batch_Number,Voucher_Number") -> from("Disbursements") -> where("Issued_To_District = '$district_region_id' and Issued_By_National = '0' and Owner = '$owner'") -> orderBy("Date_Issued_Timestamp desc");
		}
		$disbursements = $query -> execute();
		return $disbursements;
	}

	/*public static function getNationalDisbursements($vaccine, $from, $to, $offset, $items, $district_store = 0, $regional_store = 0, $order_by, $order) {
	 if ($district_store > 0) {
	 $query = Doctrine_Query::create() -> select("Date_Issued,Issued_To_National,Issued_To_Region,Issued_To_District,Quantity,Vaccine_Id,Batch_Number,Voucher_Number,Added_By,Stock_At_Hand") -> from("Disbursements") -> where("(Issued_By_National = '0' or Issued_To_National = '0') and Issued_To_District = '$district_store' and Vaccine_Id = '$vaccine' and Date_Issued_Timestamp between '$from' and '$to'") -> offset($offset) -> limit($items) -> orderBy("$order_by $order");
	 } elseif ($regional_store > 0) {
	 $query = Doctrine_Query::create() -> select("Date_Issued,Issued_To_National,Issued_To_Region,Issued_To_District,Quantity,Vaccine_Id,Batch_Number,Voucher_Number,Added_By,Stock_At_Hand") -> from("Disbursements") -> where("(Issued_By_National = '0' or Issued_To_National = '0') and Issued_To_Region = '$regional_store' and Vaccine_Id = '$vaccine' and Date_Issued_Timestamp between '$from' and '$to'") -> offset($offset) -> limit($items) -> orderBy("$order_by $order");
	 } else {
	 $query = Doctrine_Query::create() -> select("Date_Issued,Issued_To_National,Issued_To_Region,Issued_To_District,Quantity,Vaccine_Id,Batch_Number,Voucher_Number,Added_By,Stock_At_Hand") -> from("Disbursements") -> where("(Issued_By_National = '0' or Issued_To_National = '0') and Vaccine_Id = '$vaccine' and Date_Issued_Timestamp between '$from' and '$to'") -> offset($offset) -> limit($items) -> orderBy("$order_by $order");
	 }
	 $disbursements = $query -> execute();
	 return $disbursements;
	 }*/
	public static function getNationalDisbursements($vaccine, $from, $to, $offset, $items, $district_store = 0, $regional_store = 0, $order_by, $order, $balance = 0) {
		$owner = "N0";
		$balance_sql = "SET @stock_in = $balance";
		Doctrine_Manager::getInstance() -> getCurrentConnection() -> standaloneQuery($balance_sql) -> execute();
		Doctrine_Manager::getInstance() -> getCurrentConnection() -> standaloneQuery('SET @stock_out = 0;') -> execute();
		Doctrine_Manager::getInstance() -> getCurrentConnection() -> standaloneQuery('SET @stock_balance = 0;') -> execute();
		$query = new Doctrine_RawSql();
		if ($district_store > 0) {
			if ($order == "DESC") {
				$query -> addComponent('d', 'Disbursements') -> select('{d.Date_Issued},{d.Issued_To_National},{d.Issued_To_Region},{d.Issued_To_District},{d.Quantity},{d.Vaccine_Id},{d.Batch_Number},{d.Voucher_Number},{d.Added_By},{d.Stock_At_Hand},{d.Total_Stock_In},{d.Total_Stock_Out},{d.Total_Stock_Balance}') -> from("(SELECT id,Date_Issued,Issued_To_National,Issued_To_Region,Issued_To_District,Quantity,Vaccine_Id,Batch_Number,Voucher_Number,Added_By,Stock_At_Hand, (case when Issued_By_National='0' then (@stock_out := @stock_out + Quantity) else case when total_stock_balance > 0 then @stock_out :=0 end  end) as total_stock_out, (case when Issued_To_National='0' then (@stock_in := @stock_in + Quantity) else case when total_stock_balance > 0 then @stock_in :=total_stock_balance end end) as total_stock_in, (case when total_stock_balance > 0 then @stock_balance :=total_stock_balance else @stock_balance := @stock_in - @stock_out end) as total_stock_balance FROM Disbursements d where Vaccine_Id = '$vaccine' and Issued_To_District = '$district_store' and Date_Issued_Timestamp between '$from' and '$to' and Owner = '$owner' order by Unix_Timestamp(str_to_date(Date_Issued,'%m/%d/%Y')) asc) d") -> orderBy("Unix_Timestamp(str_to_date(Date_Issued,'%m/%d/%Y')) desc") -> offset($offset) -> limit($items);
			} else {
				$query -> addComponent('d', 'Disbursements') -> select('{d.Date_Issued},{d.Issued_To_National},{d.Issued_To_Region},{d.Issued_To_District},{d.Quantity},{d.Vaccine_Id},{d.Batch_Number},{d.Voucher_Number},{d.Added_By},{d.Stock_At_Hand},{d.Total_Stock_In},{d.Total_Stock_Out},{d.Total_Stock_Balance}') -> from("(SELECT id,Date_Issued,Issued_To_National,Issued_To_Region,Issued_To_District,Quantity,Vaccine_Id,Batch_Number,Voucher_Number,Added_By,Stock_At_Hand, (case when Issued_By_National='0' then (@stock_out := @stock_out + Quantity) else case when total_stock_balance > 0 then @stock_out :=0 end  end) as total_stock_out, (case when Issued_To_National='0' then (@stock_in := @stock_in + Quantity) else case when total_stock_balance > 0 then @stock_in :=total_stock_balance end end) as total_stock_in, (case when total_stock_balance > 0 then @stock_balance :=total_stock_balance else @stock_balance := @stock_in - @stock_out end) as total_stock_balance FROM Disbursements d where Vaccine_Id = '$vaccine' and Issued_To_District = '$district_store' and Date_Issued_Timestamp between '$from' and '$to' and Owner = '$owner' order by Unix_Timestamp(str_to_date(Date_Issued,'%m/%d/%Y')) asc) d") -> orderBy("Unix_Timestamp(str_to_date(Date_Issued,'%m/%d/%Y')) desc") -> offset($offset) -> limit($items);
			}
		} else if ($regional_store > 0) {
			if ($order == "DESC") {
				$query -> addComponent('d', 'Disbursements') -> select('{d.Date_Issued},{d.Issued_To_National},{d.Issued_To_Region},{d.Issued_To_District},{d.Quantity},{d.Vaccine_Id},{d.Batch_Number},{d.Voucher_Number},{d.Added_By},{d.Stock_At_Hand},{d.Total_Stock_In},{d.Total_Stock_Out},{d.Total_Stock_Balance}') -> from("(SELECT id,Date_Issued,Issued_To_National,Issued_To_Region,Issued_To_District,Quantity,Vaccine_Id,Batch_Number,Voucher_Number,Added_By,Stock_At_Hand, (case when Issued_By_National='0' then (@stock_out := @stock_out + Quantity) else case when total_stock_balance > 0 then @stock_out :=0 end  end) as total_stock_out, (case when Issued_To_National='0' then (@stock_in := @stock_in + Quantity) else case when total_stock_balance > 0 then @stock_in :=total_stock_balance end end) as total_stock_in, (case when total_stock_balance > 0 then @stock_balance :=total_stock_balance else @stock_balance := @stock_in - @stock_out end) as total_stock_balance FROM Disbursements d where Vaccine_Id = '$vaccine' and Issued_To_Region = '$regional_store' and Date_Issued_Timestamp between '$from' and '$to' and Owner = '$owner' order by Unix_Timestamp(str_to_date(Date_Issued,'%m/%d/%Y')) asc) d") -> orderBy("Unix_Timestamp(str_to_date(Date_Issued,'%m/%d/%Y')) desc") -> offset($offset) -> limit($items);
			} else {
				$query -> addComponent('d', 'Disbursements') -> select('{d.Date_Issued},{d.Issued_To_National},{d.Issued_To_Region},{d.Issued_To_District},{d.Quantity},{d.Vaccine_Id},{d.Batch_Number},{d.Voucher_Number},{d.Added_By},{d.Stock_At_Hand},{d.Total_Stock_In},{d.Total_Stock_Out},{d.Total_Stock_Balance}') -> from("(SELECT id,Date_Issued,Issued_To_National,Issued_To_Region,Issued_To_District,Quantity,Vaccine_Id,Batch_Number,Voucher_Number,Added_By,Stock_At_Hand, (case when Issued_By_National='0' then (@stock_out := @stock_out + Quantity) else case when total_stock_balance > 0 then @stock_out :=0 end  end) as total_stock_out, (case when Issued_To_National='0' then (@stock_in := @stock_in + Quantity) else case when total_stock_balance > 0 then @stock_in :=total_stock_balance end end) as total_stock_in, (case when total_stock_balance > 0 then @stock_balance :=total_stock_balance else @stock_balance := @stock_in - @stock_out end) as total_stock_balance FROM Disbursements d where Vaccine_Id = '$vaccine' and Issued_To_Region = '$regional_store' and Date_Issued_Timestamp between '$from' and '$to' and Owner = '$owner' order by Unix_Timestamp(str_to_date(Date_Issued,'%m/%d/%Y')) asc) d") -> orderBy("Unix_Timestamp(str_to_date(Date_Issued,'%m/%d/%Y')) asc") -> offset($offset) -> limit($items);
			}
		} else {

			if ($order == "DESC") {
				$query -> addComponent('d', 'Disbursements') -> select('{d.Date_Issued},{d.Issued_To_National},{d.Issued_To_Region},{d.Issued_To_District},{d.Quantity},{d.Vaccine_Id},{d.Batch_Number},{d.Voucher_Number},{d.Added_By},{d.Stock_At_Hand},{d.Total_Stock_In},{d.Total_Stock_Out},{d.Total_Stock_Balance}') -> from("(SELECT id,Date_Issued,Issued_To_National,Issued_To_Region,Issued_To_District,Quantity,Vaccine_Id,Batch_Number,Voucher_Number,Added_By,Stock_At_Hand, (case when Issued_By_National='0' then (@stock_out := @stock_out + Quantity) else case when total_stock_balance > 0 then @stock_out :=0 end  end) as total_stock_out, (case when Issued_To_National='0' then (@stock_in := @stock_in + Quantity) else case when total_stock_balance > 0 then @stock_in :=total_stock_balance end end) as total_stock_in, (case when total_stock_balance > 0 then @stock_balance :=total_stock_balance else @stock_balance := @stock_in - @stock_out end) as total_stock_balance FROM Disbursements d where Vaccine_Id = '$vaccine' and Date_Issued_Timestamp between '$from' and '$to' and Owner = '$owner' order by Unix_Timestamp(str_to_date(Date_Issued,'%m/%d/%Y')) asc) d") -> orderBy("Unix_Timestamp(str_to_date(Date_Issued,'%m/%d/%Y')) desc") -> offset($offset) -> limit($items);
			} else {
				$query -> addComponent('d', 'Disbursements') -> select('{d.Date_Issued},{d.Issued_To_National},{d.Issued_To_Region},{d.Issued_To_District},{d.Quantity},{d.Vaccine_Id},{d.Batch_Number},{d.Voucher_Number},{d.Added_By},{d.Stock_At_Hand},{d.Total_Stock_In},{d.Total_Stock_Out},{d.Total_Stock_Balance}') -> from("(SELECT id,Date_Issued,Issued_To_National,Issued_To_Region,Issued_To_District,Quantity,Vaccine_Id,Batch_Number,Voucher_Number,Added_By,Stock_At_Hand, (case when Issued_By_National='0' then (@stock_out := @stock_out + Quantity) else case when total_stock_balance > 0 then @stock_out :=0 end  end) as total_stock_out, (case when Issued_To_National='0' then (@stock_in := @stock_in + Quantity) else case when total_stock_balance > 0 then @stock_in :=total_stock_balance end end) as total_stock_in, (case when total_stock_balance > 0 then @stock_balance :=total_stock_balance else @stock_balance := @stock_in - @stock_out end) as total_stock_balance FROM Disbursements d where Vaccine_Id = '$vaccine' and Date_Issued_Timestamp between '$from' and '$to' and Owner = '$owner' order by Unix_Timestamp(str_to_date(Date_Issued,'%m/%d/%Y')) asc) d") -> orderBy("Unix_Timestamp(str_to_date(Date_Issued,'%m/%d/%Y')) asc") -> offset($offset) -> limit($items);
			}
		}
		$disbursements = $query -> execute();

		return $disbursements;
	}

	//Function for getting the total number of national disbursements in a given period!
	public static function getTotalNationalDisbursements($vaccine, $from, $to, $district_store, $regional_store) {
		$owner = "N0";
		if ($district_store > 0) {
			$query = Doctrine_Query::create() -> select("COUNT(*) as Total_Disbursements") -> from("Disbursements") -> where("Issued_To_District = '$district_store' and Vaccine_Id = '$vaccine' and Date_Issued_Timestamp between '$from' and '$to' and Owner = '$owner'") -> orderBy("Date_Issued_Timestamp desc");
		} elseif ($regional_store > 0) {
			$query = Doctrine_Query::create() -> select("COUNT(*) as Total_Disbursements") -> from("Disbursements") -> where("Issued_To_Region = '$regional_store' and Vaccine_Id = '$vaccine' and Date_Issued_Timestamp between '$from' and '$to' and Owner = '$owner'") -> orderBy("Date_Issued_Timestamp desc");
		} else {
			$query = Doctrine_Query::create() -> select("COUNT(*) as Total_Disbursements") -> from("Disbursements") -> where("Vaccine_Id = '$vaccine' and Date_Issued_Timestamp between '$from' and '$to' and Owner = '$owner'") -> orderBy("Date_Issued_Timestamp desc");
		}

		$count = $query -> execute();
		return $count[0] -> Total_Disbursements;
	}

	//Function for getting the total number of regional disbursements in a given period!
	public static function getTotalRegionalDisbursements($region, $vaccine, $from, $to, $district_store, $regional_store) {
		$owner = "R" . $region;
		if ($district_store > 0) {
			$query = Doctrine_Query::create() -> select("COUNT(*) as Total_Disbursements") -> from("Disbursements") -> where("Issued_To_District = '$district_store' and Vaccine_Id = '$vaccine' and Date_Issued_Timestamp between '$from' and '$to' and Owner = '$owner'") -> orderBy("Date_Issued_Timestamp desc");
		} elseif ($regional_store > 0) {
			$query = Doctrine_Query::create() -> select("COUNT(*) as Total_Disbursements") -> from("Disbursements") -> where("Issued_To_Region = '$regional_store' and Vaccine_Id = '$vaccine' and Date_Issued_Timestamp between '$from' and '$to' and Owner = '$owner'") -> orderBy("Date_Issued_Timestamp desc");
		} else {
			$query = Doctrine_Query::create() -> select("COUNT(*) as Total_Disbursements") -> from("Disbursements") -> where("Vaccine_Id = '$vaccine' and Date_Issued_Timestamp between '$from' and '$to' and Owner = '$owner'") -> orderBy("Date_Issued_Timestamp desc");
		}

		$count = $query -> execute();
		return $count[0] -> Total_Disbursements;
	}

	//Function for getting the total number of district disbursements in a given period!
	public static function getTotalDistrictDisbursements($district, $vaccine, $from, $to, $district_store) {
		$owner = "D" . $district;
		if ($district_store > 0) {
			$query = Doctrine_Query::create() -> select("COUNT(*) as Total_Disbursements") -> from("Disbursements") -> where("Issued_To_District = '$district_store' and Vaccine_Id = '$vaccine' and Date_Issued_Timestamp between '$from' and '$to' and Owner = '$owner'") -> orderBy("Date_Issued_Timestamp desc");
		} else {
			$query = Doctrine_Query::create() -> select("COUNT(*) as Total_Disbursements") -> from("Disbursements") -> where("Vaccine_Id = '$vaccine' and Date_Issued_Timestamp between '$from' and '$to' and Owner = '$owner'") -> orderBy("Date_Issued_Timestamp desc");
		}
		$count = $query -> execute();
		return $count[0] -> Total_Disbursements;
	}

	public static function getRegionalDisbursements($region, $vaccine, $from, $to, $offset, $items, $district_store = 0, $regional_store = 0, $order_by, $order, $balance = 0) {
		$owner = "R" . $region;
		$balance_sql = "SET @stock_in = $balance";
		Doctrine_Manager::getInstance() -> getCurrentConnection() -> standaloneQuery($balance_sql) -> execute();
		Doctrine_Manager::getInstance() -> getCurrentConnection() -> standaloneQuery('SET @stock_out = 0;') -> execute();
		Doctrine_Manager::getInstance() -> getCurrentConnection() -> standaloneQuery('SET @stock_balance = 0;') -> execute();
		$query = new Doctrine_RawSql();
		if ($district_store > 0) {
			if ($order == "DESC") {
				$query -> addComponent('d', 'Disbursements') -> select('{d.Date_Issued},{d.Issued_To_National},{d.Issued_To_Region},{d.Issued_To_District},{d.Quantity},{d.Vaccine_Id},{d.Batch_Number},{d.Voucher_Number},{d.Added_By},{d.Stock_At_Hand},{d.Total_Stock_In},{d.Total_Stock_Out},{d.Total_Stock_Balance}') -> from("(SELECT id,Date_Issued,Issued_To_National,Issued_To_Region,Issued_To_District,Quantity,Vaccine_Id,Batch_Number,Voucher_Number,Added_By,Stock_At_Hand, (case when Issued_By_Region='$region' then (@stock_out := @stock_out + Quantity) else case when total_stock_balance > 0 then @stock_out :=0 end  end) as total_stock_out, (case when Issued_To_Region='$region' then (@stock_in := @stock_in + Quantity) else case when total_stock_balance > 0 then @stock_in :=total_stock_balance end end) as total_stock_in, (case when total_stock_balance > 0 then @stock_balance :=total_stock_balance else @stock_balance := @stock_in - @stock_out end) as total_stock_balance FROM Disbursements d where Vaccine_Id = '$vaccine' and Issued_To_District = '$district_store' and Date_Issued_Timestamp between '$from' and '$to' and Owner = '$owner' order by Unix_Timestamp(str_to_date(Date_Issued,'%m/%d/%Y')) asc) d") -> orderBy("Unix_Timestamp(str_to_date(Date_Issued,'%m/%d/%Y')) desc") -> offset($offset) -> limit($items);
			} else {
				$query -> addComponent('d', 'Disbursements') -> select('{d.Date_Issued},{d.Issued_To_National},{d.Issued_To_Region},{d.Issued_To_District},{d.Quantity},{d.Vaccine_Id},{d.Batch_Number},{d.Voucher_Number},{d.Added_By},{d.Stock_At_Hand},{d.Total_Stock_In},{d.Total_Stock_Out},{d.Total_Stock_Balance}') -> from("(SELECT id,Date_Issued,Issued_To_National,Issued_To_Region,Issued_To_District,Quantity,Vaccine_Id,Batch_Number,Voucher_Number,Added_By,Stock_At_Hand, (case when Issued_By_Region='$region' then (@stock_out := @stock_out + Quantity) else case when total_stock_balance > 0 then @stock_out :=0 end  end) as total_stock_out, (case when Issued_To_Region='$region' then (@stock_in := @stock_in + Quantity) else case when total_stock_balance > 0 then @stock_in :=total_stock_balance end end) as total_stock_in, (case when total_stock_balance > 0 then @stock_balance :=total_stock_balance else @stock_balance := @stock_in - @stock_out end) as total_stock_balance FROM Disbursements d where Vaccine_Id = '$vaccine' and Issued_To_District = '$district_store' and Date_Issued_Timestamp between '$from' and '$to' and Owner = '$owner' order by Unix_Timestamp(str_to_date(Date_Issued,'%m/%d/%Y')) asc) d") -> orderBy("Unix_Timestamp(str_to_date(Date_Issued,'%m/%d/%Y')) asc") -> offset($offset) -> limit($items);
			}
		} else if ($regional_store > 0) {
			if ($order == "DESC") {
				$query -> addComponent('d', 'Disbursements') -> select('{d.Date_Issued},{d.Issued_To_National},{d.Issued_To_Region},{d.Issued_To_District},{d.Quantity},{d.Vaccine_Id},{d.Batch_Number},{d.Voucher_Number},{d.Added_By},{d.Stock_At_Hand},{d.Total_Stock_In},{d.Total_Stock_Out},{d.Total_Stock_Balance}') -> from("(SELECT id,Date_Issued,Issued_To_National,Issued_To_Region,Issued_To_District,Quantity,Vaccine_Id,Batch_Number,Voucher_Number,Added_By,Stock_At_Hand, (case when Issued_By_Region='$region' then (@stock_out := @stock_out + Quantity) else case when total_stock_balance > 0 then @stock_out :=0 end  end) as total_stock_out, (case when Issued_To_Region='$region' then (@stock_in := @stock_in + Quantity) else case when total_stock_balance > 0 then @stock_in :=total_stock_balance end end) as total_stock_in, (case when total_stock_balance > 0 then @stock_balance :=total_stock_balance else @stock_balance := @stock_in - @stock_out end) as total_stock_balance FROM Disbursements d where Vaccine_Id = '$vaccine' and Issued_To_Region = '$regional_store' and Date_Issued_Timestamp between '$from' and '$to' and Owner = '$owner' order by Unix_Timestamp(str_to_date(Date_Issued,'%m/%d/%Y')) asc) d") -> orderBy("Unix_Timestamp(str_to_date(Date_Issued,'%m/%d/%Y')) desc") -> offset($offset) -> limit($items);
			} else {
				$query -> addComponent('d', 'Disbursements') -> select('{d.Date_Issued},{d.Issued_To_National},{d.Issued_To_Region},{d.Issued_To_District},{d.Quantity},{d.Vaccine_Id},{d.Batch_Number},{d.Voucher_Number},{d.Added_By},{d.Stock_At_Hand},{d.Total_Stock_In},{d.Total_Stock_Out},{d.Total_Stock_Balance}') -> from("(SELECT id,Date_Issued,Issued_To_National,Issued_To_Region,Issued_To_District,Quantity,Vaccine_Id,Batch_Number,Voucher_Number,Added_By,Stock_At_Hand, (case when Issued_By_Region='$region' then (@stock_out := @stock_out + Quantity) else case when total_stock_balance > 0 then @stock_out :=0 end  end) as total_stock_out, (case when Issued_To_Region='$region' then (@stock_in := @stock_in + Quantity) else case when total_stock_balance > 0 then @stock_in :=total_stock_balance end end) as total_stock_in, (case when total_stock_balance > 0 then @stock_balance :=total_stock_balance else @stock_balance := @stock_in - @stock_out end) as total_stock_balance FROM Disbursements d where Vaccine_Id = '$vaccine' and Issued_To_Region = '$regional_store' and Date_Issued_Timestamp between '$from' and '$to' and Owner = '$owner' order by Unix_Timestamp(str_to_date(Date_Issued,'%m/%d/%Y')) asc) d") -> orderBy("Unix_Timestamp(str_to_date(Date_Issued,'%m/%d/%Y')) asc") -> offset($offset) -> limit($items);
			}
		} else {

			if ($order == "DESC") {
				$query -> addComponent('d', 'Disbursements') -> select('{d.Date_Issued},{d.Issued_To_National},{d.Issued_To_Region},{d.Issued_To_District},{d.Quantity},{d.Vaccine_Id},{d.Batch_Number},{d.Voucher_Number},{d.Added_By},{d.Stock_At_Hand},{d.Total_Stock_In},{d.Total_Stock_Out},{d.Total_Stock_Balance}') -> from("(SELECT id,Date_Issued,Issued_To_National,Issued_To_Region,Issued_To_District,Quantity,Vaccine_Id,Batch_Number,Voucher_Number,Added_By,Stock_At_Hand, (case when Issued_By_Region='$region' then (@stock_out := @stock_out + Quantity) else case when total_stock_balance > 0 then @stock_out :=0 end  end) as total_stock_out, (case when Issued_To_Region='$region' then (@stock_in := @stock_in + Quantity) else case when total_stock_balance > 0 then @stock_in :=total_stock_balance end end) as total_stock_in, (case when total_stock_balance > 0 then @stock_balance :=total_stock_balance else @stock_balance := @stock_in - @stock_out end) as total_stock_balance FROM Disbursements d where Vaccine_Id = '$vaccine' and Date_Issued_Timestamp between '$from' and '$to' and Owner = '$owner' order by Unix_Timestamp(str_to_date(Date_Issued,'%m/%d/%Y')) asc) d") -> orderBy("Unix_Timestamp(str_to_date(Date_Issued,'%m/%d/%Y')) desc") -> offset($offset) -> limit($items);
			} else {
				$query -> addComponent('d', 'Disbursements') -> select('{d.Date_Issued},{d.Issued_To_National},{d.Issued_To_Region},{d.Issued_To_District},{d.Quantity},{d.Vaccine_Id},{d.Batch_Number},{d.Voucher_Number},{d.Added_By},{d.Stock_At_Hand},{d.Total_Stock_In},{d.Total_Stock_Out},{d.Total_Stock_Balance}') -> from("(SELECT id,Date_Issued,Issued_To_National,Issued_To_Region,Issued_To_District,Quantity,Vaccine_Id,Batch_Number,Voucher_Number,Added_By,Stock_At_Hand, (case when Issued_By_Region='$region' then (@stock_out := @stock_out + Quantity) else case when total_stock_balance > 0 then @stock_out :=0 end  end) as total_stock_out, (case when Issued_To_Region='$region' then (@stock_in := @stock_in + Quantity) else case when total_stock_balance > 0 then @stock_in :=total_stock_balance end end) as total_stock_in, (case when total_stock_balance > 0 then @stock_balance :=total_stock_balance else @stock_balance := @stock_in - @stock_out end) as total_stock_balance FROM Disbursements d where Vaccine_Id = '$vaccine' and Date_Issued_Timestamp between '$from' and '$to' and Owner = '$owner' order by Unix_Timestamp(str_to_date(Date_Issued,'%m/%d/%Y')) asc) d") -> orderBy("Unix_Timestamp(str_to_date(Date_Issued,'%m/%d/%Y')) asc") -> offset($offset) -> limit($items);
			}
		}
		$disbursements = $query -> execute();
		return $disbursements;
	}

	public static function getDistrictDisbursements($district, $vaccine, $from, $to, $offset, $items, $order_by, $order, $district_store = 0, $balance = 0) {
		$owner = "D" . $district;
		$balance_sql = "SET @stock_in = $balance";
		Doctrine_Manager::getInstance() -> getCurrentConnection() -> standaloneQuery($balance_sql) -> execute();
		Doctrine_Manager::getInstance() -> getCurrentConnection() -> standaloneQuery('SET @stock_out = 0;') -> execute();
		Doctrine_Manager::getInstance() -> getCurrentConnection() -> standaloneQuery('SET @stock_balance = 0;') -> execute();
		$query = new Doctrine_RawSql();
		if ($district_store > 0) {
			if ($order == "DESC") {
				$query -> addComponent('d', 'Disbursements') -> select('{d.Date_Issued},{d.Issued_To_National},{d.Issued_To_Region},{d.Issued_To_District},{d.Quantity},{d.Vaccine_Id},{d.Batch_Number},{d.Voucher_Number},{d.Added_By},{d.Stock_At_Hand},{d.Total_Stock_In},{d.Total_Stock_Out},{d.Total_Stock_Balance}') -> from("(SELECT id,Date_Issued,Issued_To_National,Issued_To_Region,Issued_To_District,Quantity,Vaccine_Id,Batch_Number,Voucher_Number,Added_By,Stock_At_Hand, (case when Issued_By_District='$district' then (@stock_out := @stock_out + Quantity) else case when total_stock_balance > 0 then @stock_out :=0 end  end) as total_stock_out, (case when Issued_To_District='$district' then (@stock_in := @stock_in + Quantity) else case when total_stock_balance > 0 then @stock_in :=total_stock_balance end end) as total_stock_in, (case when total_stock_balance > 0 then @stock_balance :=total_stock_balance else @stock_balance := @stock_in - @stock_out end) as total_stock_balance FROM Disbursements d where Vaccine_Id = '$vaccine' and Issued_To_District = '$district_store' and Date_Issued_Timestamp between '$from' and '$to' and Owner = '$owner' order by Unix_Timestamp(str_to_date(Date_Issued,'%m/%d/%Y')) asc) d") -> orderBy("Unix_Timestamp(str_to_date(Date_Issued,'%m/%d/%Y')) desc") -> offset($offset) -> limit($items);
			} else {
				$query -> addComponent('d', 'Disbursements') -> select('{d.Date_Issued},{d.Issued_To_National},{d.Issued_To_Region},{d.Issued_To_District},{d.Quantity},{d.Vaccine_Id},{d.Batch_Number},{d.Voucher_Number},{d.Added_By},{d.Stock_At_Hand},{d.Total_Stock_In},{d.Total_Stock_Out},{d.Total_Stock_Balance}') -> from("(SELECT id,Date_Issued,Issued_To_National,Issued_To_Region,Issued_To_District,Quantity,Vaccine_Id,Batch_Number,Voucher_Number,Added_By,Stock_At_Hand, (case when Issued_By_District='$district' then (@stock_out := @stock_out + Quantity) else case when total_stock_balance > 0 then @stock_out :=0 end  end) as total_stock_out, (case when Issued_To_District='$district' then (@stock_in := @stock_in + Quantity) else case when total_stock_balance > 0 then @stock_in :=total_stock_balance end end) as total_stock_in, (case when total_stock_balance > 0 then @stock_balance :=total_stock_balance else @stock_balance := @stock_in - @stock_out end) as total_stock_balance FROM Disbursements d where Vaccine_Id = '$vaccine' and Issued_To_District = '$district_store' and Date_Issued_Timestamp between '$from' and '$to' and Owner = '$owner' order by Unix_Timestamp(str_to_date(Date_Issued,'%m/%d/%Y')) asc) d") -> orderBy("Unix_Timestamp(str_to_date(Date_Issued,'%m/%d/%Y')) asc") -> offset($offset) -> limit($items);
			}
		} else {

			if ($order == "DESC") {
				$query -> addComponent('d', 'Disbursements') -> select('{d.Date_Issued},{d.Issued_To_National},{d.Issued_To_Region},{d.Issued_To_District},{d.Quantity},{d.Vaccine_Id},{d.Batch_Number},{d.Voucher_Number},{d.Added_By},{d.Stock_At_Hand},{d.Total_Stock_In},{d.Total_Stock_Out},{d.Total_Stock_Balance}') -> from("(SELECT id,Date_Issued,Issued_To_National,Issued_To_Region,Issued_To_District,Quantity,Vaccine_Id,Batch_Number,Voucher_Number,Added_By,Stock_At_Hand, (case when Issued_By_District='$district' then (@stock_out := @stock_out + Quantity) else case when total_stock_balance > 0 then @stock_out :=0 end  end) as total_stock_out, (case when Issued_To_District='$district' then (@stock_in := @stock_in + Quantity) else case when total_stock_balance > 0 then @stock_in :=total_stock_balance end end) as total_stock_in, (case when total_stock_balance > 0 then @stock_balance :=total_stock_balance else @stock_balance := @stock_in - @stock_out end) as total_stock_balance FROM Disbursements d where Vaccine_Id = '$vaccine' and Date_Issued_Timestamp between '$from' and '$to' and Owner = '$owner' order by Unix_Timestamp(str_to_date(Date_Issued,'%m/%d/%Y')) asc) d") -> orderBy("Unix_Timestamp(str_to_date(Date_Issued,'%m/%d/%Y')) desc") -> offset($offset) -> limit($items);
			} else {
				$query -> addComponent('d', 'Disbursements') -> select('{d.Date_Issued},{d.Issued_To_National},{d.Issued_To_Region},{d.Issued_To_District},{d.Quantity},{d.Vaccine_Id},{d.Batch_Number},{d.Voucher_Number},{d.Added_By},{d.Stock_At_Hand},{d.Total_Stock_In},{d.Total_Stock_Out},{d.Total_Stock_Balance}') -> from("(SELECT id,Date_Issued,Issued_To_National,Issued_To_Region,Issued_To_District,Quantity,Vaccine_Id,Batch_Number,Voucher_Number,Added_By,Stock_At_Hand, (case when Issued_By_District='$district' then (@stock_out := @stock_out + Quantity) else case when total_stock_balance > 0 then @stock_out :=0 end  end) as total_stock_out, (case when Issued_To_District='$district' then (@stock_in := @stock_in + Quantity) else case when total_stock_balance > 0 then @stock_in :=total_stock_balance end end) as total_stock_in, (case when total_stock_balance > 0 then @stock_balance :=total_stock_balance else @stock_balance := @stock_in - @stock_out end) as total_stock_balance FROM Disbursements d where Vaccine_Id = '$vaccine' and Date_Issued_Timestamp between '$from' and '$to' and Owner = '$owner' order by Unix_Timestamp(str_to_date(Date_Issued,'%m/%d/%Y')) asc) d") -> orderBy("Unix_Timestamp(str_to_date(Date_Issued,'%m/%d/%Y')) asc") -> offset($offset) -> limit($items);
			}
		}
		$disbursements = $query -> execute();
		return $disbursements;
	}

	/*
	 public static function getDistrictDisbursements($district, $vaccine, $from, $to) {
	 $query = Doctrine_Query::create() -> select("Date_Issued,Issued_To_Facility,Issued_To_District,Issued_By_National, Issued_By_Region, Quantity,Vaccine_Id,Batch_Number,Voucher_Number,Added_By,Stock_At_Hand") -> from("Disbursements") -> where("(Issued_By_District = '$district' or Issued_To_District = '$district') and Vaccine_Id = '$vaccine' and Date_Issued_Timestamp between '$from' and '$to'") -> orderBy("Date_Issued_Timestamp desc");
	 $disbursements = $query -> execute();
	 return $disbursements;
	 }
	 */

	//Retrieves all receipts for a particular region in a given period of time
	public static function getRegionalReceipts($region, $vaccine, $from, $to) {
		$owner = "R" . $region;
		$query = Doctrine_Query::create() -> select("Date_Issued,Issued_To_Region,Issued_To_District,Quantity,Vaccine_Id,Batch_Number,Voucher_Number,Added_By") -> from("Disbursements") -> where("Issued_To_Region = '$region' and Vaccine_Id = '$vaccine' and Date_Issued_Timestamp between '$from' and '$to' and Owner = '$owner'") -> orderBy("Date_Issued_Timestamp desc");
		$disbursements = $query -> execute();
		return $disbursements;
	}

	//Gets all receipts for the whole country for a particular period of time
	public static function getNationalReceived($vaccine, $from, $to) {
		$owner = "N0";
		$query = Doctrine_Query::create() -> select("Date_Issued,Issued_To_Region,Issued_To_District,Quantity,Vaccine_Id,Batch_Number,Voucher_Number,Added_By") -> from("Disbursements") -> where("Issued_To_National = '0' and Vaccine_Id = '$vaccine' and Date_Issued_Timestamp between '$from' and '$to' and Owner = '$owner'") -> orderBy("Date_Issued_Timestamp desc");
		$disbursements = $query -> execute();
		return $disbursements;
	}

	//Get Totals of What The National Store has received in a given period.
	public static function getNationalReceiptsTotals($vaccine, $from, $to) {
		$owner = "N0";
		$query = Doctrine_Query::create() -> select("sum(Quantity) as Total") -> from("Disbursements") -> where("Issued_To_National = '0' and Vaccine_Id = '$vaccine' and Date_Issued_Timestamp between '$from' and '$to' and Owner = '$owner'");
		$totals = $query -> execute();
		$accumulated_receipts = $totals[0]['Total'];
		if ($accumulated_receipts > 0) {
			return $accumulated_receipts;
		} else {
			return "0";
		}
	}

	//Get Totals of What The National Store has issued in a given period.
	public static function getNationalIssuesTotals($vaccine, $from, $to) {
		$owner = "N0";
		$query = Doctrine_Query::create() -> select("sum(Quantity) as Total") -> from("Disbursements") -> where("Issued_By_National = '0' and Vaccine_Id = '$vaccine' and Date_Issued_Timestamp between '$from' and '$to' and Owner = '$owner'");
		$totals = $query -> execute();
		$accumulated_issues = $totals[0]['Total'];
		if ($accumulated_issues > 0) {
			return $accumulated_issues;
		} else {
			return "0";
		}
	}

	//Get Totals of What a regional Store has issued in a given period.
	public static function getRegionalIssuesTotals($vaccine, $from, $to, $region) {
		$owner = "R" . $region;
		$query = Doctrine_Query::create() -> select("sum(Quantity) as Total") -> from("Disbursements") -> where("Issued_By_Region = '$region' and Vaccine_Id = '$vaccine' and Date_Issued_Timestamp between '$from' and '$to' and Owner = '$owner'");
		$totals = $query -> execute();
		$accumulated_issues = $totals[0]['Total'];
		if ($accumulated_issues > 0) {
			return $accumulated_issues;
		} else {
			return "0";
		}
	}

	//Get Totals of What This Regional Store has received in a given period.
	public static function getRegionalReceiptsTotals($region, $vaccine, $from, $to) {
		$owner = "R" . $region;
		$query = Doctrine_Query::create() -> select("sum(Quantity) as Total") -> from("Disbursements") -> where("Issued_To_Region = '$region' and Vaccine_Id = '$vaccine' and Date_Issued_Timestamp between '$from' and '$to' and Owner = '$owner'");
		$totals = $query -> execute();
		$accumulated_receipts = $totals[0]['Total'];
		if ($accumulated_receipts > 0) {
			return $accumulated_receipts;
		} else {
			return "0";
		}
	}

	//Get Totals of What This District Store has received in a given period.
	public static function getDistrictReceiptsTotals($district, $vaccine, $from, $to) {
		$owner = "D" . $district;
		$query = Doctrine_Query::create() -> select("sum(Quantity) as Total") -> from("Disbursements") -> where("Issued_To_District = '$district' and Vaccine_Id = '$vaccine' and Date_Issued_Timestamp between '$from' and '$to' and Owner = '$owner'");
		$totals = $query -> execute();
		$accumulated_receipts = $totals[0]['Total'];
		if ($accumulated_receipts > 0) {
			return $accumulated_receipts;
		} else {
			return "0";
		}
	}

	public static function getDistrictReceipts($district, $vaccine, $from, $to) {
		$owner = "D" . $district;
		$query = Doctrine_Query::create() -> select("Date_Issued,Issued_To_Facility,Issued_To_District,Issued_By_National, Issued_By_Region, Quantity,Vaccine_Id,Batch_Number,Voucher_Number,Added_By") -> from("Disbursements") -> where("Issued_To_District = '$district' and Vaccine_Id = '$vaccine' and Date_Issued_Timestamp between '$from' and '$to' and Owner = '$owner'") -> orderBy("Date_Issued_Timestamp desc");
		$disbursements = $query -> execute();
		return $disbursements;
	}

	public static function getNationalPeriodBalance($vaccine, $from) {
		$owner = "N0";
		$last_balance_query = Doctrine_Query::create() -> select("Total_Stock_Balance,Date_Issued") -> from("Disbursements") -> where("Owner = '$owner' and Vaccine_Id = '$vaccine' and Date_Issued_Timestamp < '$from' and total_stock_balance>0") -> orderBy("Date_Issued_Timestamp desc") -> limit("1");
		$last_balance_result = $last_balance_query -> execute();
		$last_date = strtotime($last_balance_result[0] -> Date_Issued);
		$last_balance = $last_balance_result[0] -> Total_Stock_Balance;
		$query = Doctrine_Query::create() -> select("SUM(Quantity) as Totals") -> from("Disbursements") -> where("Issued_By_National = '0' and Vaccine_Id = '$vaccine' and Date_Issued_Timestamp between '$last_date' and '$from' and Owner = '$owner'");
		$issued = $query -> execute(array(), Doctrine::HYDRATE_ARRAY);
		$query2 = Doctrine_Query::create() -> select("SUM(Quantity) as Totals") -> from("Disbursements") -> where("Issued_To_National = '0' and Vaccine_Id = '$vaccine' and Date_Issued_Timestamp between '$last_date' and '$from' and Owner = '$owner'");
		$received = $query2 -> execute(array(), Doctrine::HYDRATE_ARRAY);
		$balance = $received[0]['Totals'] - $issued[0]['Totals'];
		$balance += $last_balance;
		return $balance;
	}

	public static function getRegionalPeriodBalance($region, $vaccine, $from) {
		$owner = "R" . $region;
		$last_balance_query = Doctrine_Query::create() -> select("Total_Stock_Balance,Date_Issued") -> from("Disbursements") -> where("Owner = '$owner' and Vaccine_Id = '$vaccine' and Date_Issued_Timestamp < '$from' and total_stock_balance>0") -> orderBy("Date_Issued_Timestamp desc") -> limit("1");
		$last_balance_result = $last_balance_query -> execute();
		$last_date = strtotime($last_balance_result[0] -> Date_Issued);
		$last_balance = $last_balance_result[0] -> Total_Stock_Balance;
		$query = Doctrine_Query::create() -> select("SUM(Quantity) as Totals") -> from("Disbursements") -> where("Issued_By_Region = '$region' and Vaccine_Id = '$vaccine' and Date_Issued_Timestamp between '$last_date' and '$from' and Owner = '$owner'");
		$issued = $query -> execute(array(), Doctrine::HYDRATE_ARRAY);
		$query2 = Doctrine_Query::create() -> select("SUM(Quantity) as Totals") -> from("Disbursements") -> where("Issued_To_Region = '$region' and Vaccine_Id = '$vaccine' and Date_Issued_Timestamp between '$last_date' and '$from' and Owner = '$owner'");
		$received = $query2 -> execute(array(), Doctrine::HYDRATE_ARRAY);
		$balance = $received[0]['Totals'] - $issued[0]['Totals'];
		$balance += $last_balance;
		return $balance;

	}

	public static function getDistrictPeriodBalance($district, $vaccine, $from) {
		$owner = "D" . $district;
		$last_balance_query = Doctrine_Query::create() -> select("Total_Stock_Balance,Date_Issued") -> from("Disbursements") -> where("Owner = '$owner' and Vaccine_Id = '$vaccine' and Date_Issued_Timestamp < '$from' and total_stock_balance>0") -> orderBy("Date_Issued_Timestamp desc") -> limit("1");
		$last_balance_result = $last_balance_query -> execute();
		$last_date = strtotime($last_balance_result[0] -> Date_Issued);
		$last_balance = $last_balance_result[0] -> Total_Stock_Balance;
		$query = Doctrine_Query::create() -> select("SUM(Quantity) as Totals") -> from("Disbursements") -> where("Issued_By_District = '$district' and Vaccine_Id = '$vaccine' and Date_Issued_Timestamp between '$last_date' and '$from' and Owner = '$owner'");
		$issued = $query -> execute(array(), Doctrine::HYDRATE_ARRAY);
		$query2 = Doctrine_Query::create() -> select("SUM(Quantity) as Totals") -> from("Disbursements") -> where("Issued_To_District = '$district' and Vaccine_Id = '$vaccine' and Date_Issued_Timestamp between '$last_date' and '$from' and Owner = '$owner'");
		$received = $query2 -> execute(array(), Doctrine::HYDRATE_ARRAY);
		$balance = $received[0]['Totals'] - $issued[0]['Totals'];
		$balance += $last_balance;
		return $balance;
	}

	public static function getEarliestDisbursement() {
		$query = Doctrine_Query::create() -> select("Date_Issued") -> from("Disbursements") -> orderBy("Date_Issued_Timestamp") -> limit('1');
		$disbursement_date = $query -> execute(array(), Doctrine::HYDRATE_ARRAY);
		return $disbursement_date[0]['Date_Issued'];
	}

	public static function getDisbursement($id) {
		$query = Doctrine_Query::create() -> select("*") -> from("Disbursements") -> where("id = '$id'") -> limit('1');
		$disbursement = $query -> execute(array(), Doctrine::HYDRATE_ARRAY);
		return $disbursement;
	}

	public static function getDisbursementObject($id) {
		$query = Doctrine_Query::create() -> select("id,Date_Issued,Quantity,Batch_Number,Voucher_Number,Vaccine_Id,Issued_To_Region,Issued_To_District,Issued_To_Facility") -> from("Disbursements") -> where("id = '$id'") -> limit('1');
		$disbursement = $query -> execute();
		return $disbursement;
	}

	//This function lists a summary of all recipients of vaccines from this store (National Store)
	public static function getNationalRecipientTally($vaccine, $from, $to, $offset, $items, $order_by, $order) {
		$owner = "N0";
		$query = Doctrine_Query::create() -> select("Date_Issued,Issued_To_Region,Issued_To_District,sum(Quantity) as Quantity,Vaccine_Id") -> from("Disbursements") -> where("Issued_By_National = '0' and Vaccine_Id = '$vaccine' and Date_Issued_Timestamp between '$from' and '$to' and Owner = '$owner'") -> GroupBy("Issued_To_Region, Issued_To_District") -> offset($offset) -> limit($items) -> orderBy("$order_by $order");
		$result = $query -> execute();
		return $result;
	}

	//This function lists a summary of all recipients of vaccines from this store (Regional Store)
	public static function getRegionalRecipientTally($region, $vaccine, $from, $to, $offset, $items, $order_by, $order) {
		$owner = "R" . $region;
		$query = Doctrine_Query::create() -> select("Date_Issued,Issued_To_Region,Issued_To_District,sum(Quantity) as Quantity,Vaccine_Id") -> from("Disbursements") -> where("Issued_By_Regional = '$region' and Vaccine_Id = '$vaccine' and Date_Issued_Timestamp between '$from' and '$to' and Owner = '$owner'") -> GroupBy("Issued_To_Region, Issued_To_District") -> offset($offset) -> limit($items) -> orderBy("$order_by $order");
		$result = $query -> execute();
		return $result;
	}

	//Function to retrieve the corresponding disbursement entry for a batch
	public static function getBatchEntry($id) {
		$query = Doctrine_Query::create() -> select("*") -> from("Disbursements") -> where("Batch_Id = '$id'") -> limit('1');
		$disbursement = $query -> execute();
		return $disbursement;
	}

}
