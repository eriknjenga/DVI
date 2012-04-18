<?php

class Orders_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> library('pagination');

	}

	public function index() {
		$this -> view_orders();
	}

	public function view_orders() {
		$data['title'] = "Order Management::All Orders";
		//get the type of user accessing the module
		$identifier = $this -> session -> userdata('user_identifier');
		if ($identifier == 'national_officer') {
			$this -> get_national_orders();
		} else if ($identifier == 'provincial_officer') {
			$this -> get_regional_orders();
		} else if ($identifier == 'district_officer') {
			$this -> get_district_orders();
		}

	}

	public function get_national_orders($offset = 0) {
		$items_per_page = 20;
		$number_of_orders = Vaccine_Orders::getTotalNumber();
		$orders = Vaccine_Orders::getPagedOrders($offset, $items_per_page);
		if ($number_of_orders > $items_per_page) {
			$config['base_url'] = base_url() . "orders_management/get_national_orders/";
			$config['total_rows'] = $number_of_orders;
			$config['per_page'] = $items_per_page;
			$config['uri_segment'] = 3;
			$config['num_links'] = 5;
			$this -> pagination -> initialize($config);
			$data['pagination'] = $this -> pagination -> create_links();
		}

		$data['orders'] = $orders;
		$data['title'] = "Orders Management::All My Orders";
		$data['content_view'] = "view_national_orders_view";
		$data['quick_link'] = "vaccine_orders";
		$this -> base_params($data);
	}

	public function get_regional_orders($offset = 0) {
		$items_per_page = 20;
		$region = $this -> session -> userdata('district_province_id');
		$number_of_orders = Vaccine_Orders::getTotalRegionalNumber($region);
		$orders = Vaccine_Orders::getPagedRegionalOrders($region, $offset, $items_per_page);
		if ($number_of_orders > $items_per_page) {
			$config['base_url'] = base_url() . "orders_management/get_regional_orders/";
			$config['total_rows'] = $number_of_orders;
			$config['per_page'] = $items_per_page;
			$config['uri_segment'] = 3;
			$config['num_links'] = 5;
			$this -> pagination -> initialize($config);
			$data['pagination'] = $this -> pagination -> create_links();
		}

		$data['orders'] = $orders;
		$data['title'] = "Orders Management::All My Orders";
		$data['content_view'] = "view_orders_view";
		$data['quick_link'] = "vaccine_orders";
		$this -> base_params($data);
	}

	public function get_district_orders($offset = 0) {
		$items_per_page = 20;
		$district = $this -> session -> userdata('district_province_id');
		$number_of_orders = Vaccine_Orders::getTotalDistrictNumber($district);
		$orders = Vaccine_Orders::getPagedDistrictOrders($district, $offset, $items_per_page);
		if ($number_of_orders > $items_per_page) {
			$config['base_url'] = base_url() . "orders_management/get_district_orders/";
			$config['total_rows'] = $number_of_orders;
			$config['per_page'] = $items_per_page;
			$config['uri_segment'] = 3;
			$config['num_links'] = 5;
			$this -> pagination -> initialize($config);
			$data['pagination'] = $this -> pagination -> create_links();
		}

		$data['orders'] = $orders;
		$data['title'] = "Orders Management::All My Orders";
		$data['content_view'] = "view_orders_view";
		$data['quick_link'] = "vaccine_orders";
		$this -> base_params($data);
	}

	private function base_params($data) {
		$data['vaccines'] = Vaccines::getAll_Minified();
		$data['scripts'] = array("jquery-ui.js", "tab.js");
		$data['styles'] = array("jquery-ui.css", "tab.css");
		$data['link'] = "orders_management";
		$this -> load -> view('template', $data);

	}

	public function new_order() {
		$data['title'] = "Orders Management::New Order";
		$data['content_view'] = "add_order_view";
		$data['quick_link'] = "new_order";
		$data['vaccinces'] = Vaccines::getAll();
		$this -> base_params($data);
	}

	public function approve($order_id) {
		$order_details = Vaccine_Orders::getDetails($order_id);
		$data['title'] = "Orders Management::Approve Order";
		$data['content_view'] = "approve_order_view";
		$data['order'] = $order_details;
		$this -> base_params($data);
	}

	public function save_approval() {
		$approved_quantity = $this -> input -> post('approved_quantity');
		$pickup_date = $this -> input -> post('pickup_date');
		$order_id = $this -> input -> post('order_id');
		$approved_order = Vaccine_Orders::getDetails($order_id);
		$approved_order -> Approved = '1';
		$approved_order -> Accepted_Quantity = $approved_quantity;
		$approved_order -> Pickup_Date = $pickup_date;
		$approved_order -> Accepted_By = $this -> session -> userdata('user_id');
		$approved_order -> Order_Accepted_On = date('U');
		$approved_order -> save();
		redirect("orders_management");
	}

	public function save_order() {
		$vaccines = $this -> input -> post('vaccine');
		$quantities = $this -> input -> post('quantity');
		$other = $this -> input -> post('other_items');
		$counter = 0;
		$identifier = $this -> session -> userdata('user_identifier');
		$district_order = false;
		$region_order = false;
		if ($identifier == 'district_officer') {
			$district_order = true;
		}
		if ($identifier == 'provincial_officer') {
			$region_order = true;
		}
		echo $identifier;
		//Save the orders of the vaccines
		foreach ($vaccines as $vaccine) {
			$vaccine_order = new Vaccine_Orders();
			if ($district_order) {
				$vaccine_order -> District = $this -> session -> userdata('district_province_id');
			}
			if ($region_order) {

				$vaccine_order -> Region = $this -> session -> userdata('district_province_id');
			}
			$vaccine_order -> Vaccine = $vaccine;
			$vaccine_order -> Quantity = $quantities[$counter];
			$vaccine_order -> Active = '1';
			$vaccine_order -> Approved = '0';
			$vaccine_order -> Made_By = $this -> session -> userdata('user_id');
			$vaccine_order -> Order_Made_On = date('U');
			$vaccine_order -> save();
			$counter++;
		}
		//Save the order for other items if any other items have been ordered
		if (strlen($other) > 0) {
			$other_order = new Other_Orders();
			if ($district_order) {
				$other_order -> District = $this -> session -> userdata('district_province_id');
			}
			if ($region_order) {
				$other_order -> Region = $this -> session -> userdata('district_province_id');
			}
			$other_order -> Items_Ordered = $other;
			$other_order -> Active = '1';
			$other_order -> Made_By = $this -> session -> userdata('user_id');
			$other_order -> Timestamp = date('U');
			$other_order -> save();
		}

	}

}
