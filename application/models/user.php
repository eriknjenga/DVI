<?php
class User extends Doctrine_Record {

	public function setTableDefinition() {
		$this -> hasColumn('Username', 'text');
		$this -> hasColumn('Password', 'varchar', 32);
		$this -> hasColumn('User_Group', 'varchar', 2);
		$this -> hasColumn('Full_Name', 'varchar', 50);
		$this -> hasColumn('District_Province_Id', 'varchar', 10);
		$this -> hasColumn('Disabled', 'varchar', 2);
	}

	public function setUp() {
		$this -> setTableName('users');
		$this -> hasMutator('Password', '_encrypt_password');
		$this -> hasOne('User_Groups as Group', array('local' => 'User_Group', 'foreign' => 'id'));
	}

	protected function _encrypt_password($value) {
		$this -> _set('Password', md5($value));
	}

	public function login($username, $password) {

		$query = Doctrine_Query::create() -> select("id,username,full_name,user_group, district_province_id") -> from("User") -> where("Username = '" . $username . "'");

		$user = $query -> fetchOne();
		if ($user) {

			$user2 = new User();
			$user2 -> Password = $password;

			if ($user -> Password == $user2 -> Password) {
				return $user;
			} else {
				return false;
			}
		} else {
			return false;
		}

	}

	public static function getTotalNumber() {
		$query = Doctrine_Query::create() -> select("COUNT(*) as Total_Users") -> from("User");
		$count = $query -> execute();
		return $count[0] -> Total_Users;
	}

	public function getPagedUsers($offset, $items) {
		$query = Doctrine_Query::create() -> select("*") -> from("User") -> orderBy("Full_Name") -> offset($offset) -> limit($items);
		$users = $query -> execute();
		return $users;
	}

	public static function getUser($id) {
		$query = Doctrine_Query::create() -> select("*") -> from("User") -> where("id = '$id'");
		$user = $query -> execute();
		return $user[0];
	}
		public static function userExists($username){
			if ($u = Doctrine::getTable('User')->findOneByUsername($username)) {
					
						return TRUE;
				}
			else{ return FALSE;}
		}

}
