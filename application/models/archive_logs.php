<?php
 
class Archive_Logs extends Doctrine_Record{
public function setTableDefinition() {
$this->hasColumn('Archive_Date', 'varchar',20); 
$this->hasColumn('Timestamp', 'varchar',32); 
}
public function setUp() {
$this->setTableName('archive_logs');
}

public function archiveExists($date){
$query = Doctrine_Query::create()->select("id")->from("Archive_Logs")->where("Archive_Date = '".$date."'"); 
$log = $query->fetchOne();
return $log;
}

public static function getEarliestArchive(){ 
	$query = Doctrine_Query::create()->select("Archive_Date")->from("Archive_Logs")->orderBy("Unix_Timestamp(str_to_date(Archive_Date,'%m/%d/%Y'))")->limit('1');
	$archive_date = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
	 
	return $archive_date[0]['Archive_Date'];
}
}