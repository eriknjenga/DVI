<?php

class Alert_Management extends MY_Controller {
function __construct()
{
parent::__construct();

}

public function index()
{
$this->view_alerts();
}
public function new_alert(){
$data['title'] = "Alert Management::Add New Alert";
$data['content_view'] = "add_alert_view";
$this->base_params($data);
}
public function view_alerts(){ 
$data['title'] = "Alert Management::All Alerts";
$data['content_view'] = "view_alerts_view";
$this->base_params($data);
}
private function base_params($data){
$data['vaccines'] = Vaccines::getAll_Minified();
$data['scripts'] = array("jquery-ui.js","tab.js");
$data['styles'] = array("jquery-ui.css","tab.css");
$data['link'] = "alerts";
$this->load->view('template',$data);

}
}