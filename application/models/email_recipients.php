<?php

class Email_Recipients extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Name', 'varchar', 100);
		$this -> hasColumn('Email', 'varchar', 50);
	}

	public function setUp() {
		$this -> setTableName('email_recipients');
	}

	public function getAll() {
		$query = Doctrine_Query::create() -> select("*") -> from("Email_Recipients");
		$recipients = $query -> execute();
		return $recipients;
	}

}
