<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class User_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> library('pagination');
	}

	public function index() {
		$this -> login_view();
	}

	public function login_view() {
		$data['title'] = "Login";
		$this -> load -> view('login_view', $data);
	}

	public function listing($offset = 0) {
		$items_per_page = 20;
		$number_of_users = User::getTotalNumber();
		$users = User::getPagedUsers($offset, $items_per_page);
		if ($number_of_users > $items_per_page) {
			$config['base_url'] = base_url() . "user_management/listing/";
			$config['total_rows'] = $number_of_users;
			$config['per_page'] = $items_per_page;
			$config['uri_segment'] = 3;
			$config['num_links'] = 5;
			$this -> pagination -> initialize($config);
			$data['pagination'] = $this -> pagination -> create_links();
		}

		$data['users'] = $users;
		$data['title'] = "User Management::All System Users";
		$data['module_view'] = "view_users_view";
		$this -> base_params($data);
	}

	public function add() {
		$data['title'] = "User Management::Add New User";
		$data['module_view'] = "add_user_view";
		$data['groups'] = User_Groups::getAllGroups();
		$data['districts'] = Districts::getAllDistricts();
		$data['regions'] = Regions::getAllRegions();
		$this -> base_params($data);
	}

	public function edit_user($id) {
		$user = User::getUser($id);
		$data['user'] = $user;
		$data['title'] = "User Management::Edit " . $user -> Full_Name . "'s Details";
		$data['title'] = "User Management::Add New User";
		$data['module_view'] = "add_user_view";
		$data['groups'] = User_Groups::getAllGroups();
		$data['districts'] = Districts::getAllDistricts();
		$data['regions'] = Regions::getAllRegions();
		$this -> base_params($data);
	}

	public function save() {
		$user_id = $this -> input -> post("user_id");
		$valid = false;
		if ($user_id > 0) {
			//The user is editing! Modify the validation
			$user = User::getUser($user_id);
			$valid = $this -> _submit_validate($user);
		} else {
			$valid = $this -> _submit_validate();
			$user = new User();
		}
		if ($valid) {
			$name = $this -> input -> post("name");
			$username = $this -> input -> post("username");
			$password = "123456";
			$user_group = $this -> input -> post("user_group");
			$region = $this -> input -> post("region");
			$district = $this -> input -> post("district");
			$user -> Full_Name = $name;
			$user -> Password = $password;
			$user -> User_Group = $user_group;
			$user -> Username = $username;
			$user -> Disabled = '0';
			if (strlen($district) > 0) {
				$user -> District_Province_Id = $district;
			} else if (strlen($region) > 0) {
				$user -> District_Province_Id = $region;
			} else {
				$user -> District_Province_Id = '';
			}
			$user -> save();

			redirect("user_management/listing");
		} else {
			$this -> add();
		}
	}

	public function login_submit() {
		$user = new User();

		$password = $this -> input -> post('password');
		$username = $this -> input -> post('username');
		$returned_user = $user -> login($username, $password);
		//If user successfully logs in, proceed here
		if ($returned_user) {
			//Create basic data to be saved in the session
			$session_data = array('user_id' => $returned_user['id'], 'user_group' => $returned_user['User_Group'], 'user_identifier' => $returned_user -> Group -> Identifier, 'user_group_name' => $returned_user -> Group -> Name, 'username' => $returned_user['Username'], 'full_name' => $returned_user['Full_Name'], 'district_province_id' => $returned_user['District_Province_Id']);
			//Save this data in the session
			$this -> session -> set_userdata($session_data);
			//Retrieve Menus accessible to this user
			$rights = User_Access::getAccessRights($returned_user['User_Group']);
			//Create array that will hold all the accessible menus in the session
			$menus = array();
			$counter = 0;
			foreach ($rights as $right) {
				$menus[$counter] = array("menu_text" => $right -> Menus -> Menu_Text, "menu_url" => $right -> Menus -> Menu_Url);
				$counter++;
			}
			//Save this menus array in the session
			$this -> session -> set_userdata(array("menus" => $menus));
			redirect('home_controller');
		} else {
			$data['title'] = "Login::Credentials Error";
			$this -> load -> view("login_view", $data);
		}
	}
	public function change_availability($code, $availability) {
		$user = User::getUser($code);
		$user -> Disabled = $availability;
		$user -> save();
		redirect("user_management/listing");
	}
	private function _submit_validate($user = false) {
		// validation rules
		$this -> form_validation -> set_rules('name', 'Full Name', 'trim|required|min_length[2]|max_length[50]');
		$this -> form_validation -> set_rules('username', 'Username', 'trim|required|min_length[6]|max_length[50]');
		$this -> form_validation -> set_rules('user_group', 'User Group', 'trim|required|min_length[1]|max_length[50]');
		$temp_validation = $this -> form_validation -> run();
		if ($temp_validation) {
			//If the user is editing, if the username changes, check whether the new username exists!
			if ($user) {
				if($user->Username != $this->input->post('username')){
					$this -> form_validation -> set_rules('username', 'Username', 'trim|required|callback_unique_username');
				}
			} else {
				$this -> form_validation -> set_rules('username', 'Username', 'trim|required|callback_unique_username');
			}

			return $this -> form_validation -> run();
		} else {
			return $temp_validation;
		}

	}

	public function unique_username($usr) {
		$exists = User::userExists($usr);
		if ($exists) {
			$this -> form_validation -> set_message('unique_username', 'The Username already exists. Enter another one.');
			return FALSE;
		} else {
			return TRUE;
		}

	} 

	function logout() {
		$this -> session -> sess_destroy();
		redirect('user_management');
	}

	private function base_params($data) {
		$data['scripts'] = array("jquery-ui.js", "tab.js");
		$data['styles'] = array("jquery-ui.css", "tab.css");
		$data['content_view'] = "admin_view";
		$data['quick_link'] = "user_management";
		$data['link'] = "system_administration";
		$this -> load -> view('template', $data);

	}

}
