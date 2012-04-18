<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends MY_Controller {
    function __construct()
    {
        parent::__construct();
    }

/**
 * Index Page for this controller.
 *
 * Maps to the following URL
 * 		http://example.com/index.php/welcome
 *	- or -
 * 		http://example.com/index.php/welcome/index
 *	- or -
 * Since this controller is set as the default controller in
 * config/routes.php, it's displayed at http://example.com/
 *
 * So any other public methods not prefixed with an underscore will
 * map to /index.php/welcome/<method_name>
 * @see http://codeigniter.com/user_guide/general/urls.html
 */
public function index()
{
$user = new models\user();
 $user->setUsername("njenga");
 $user->setPassword("njenga");
 $user->setUserGroup("1");
 $this->em->persist($user);
$this->em->flush();
$this->load->view('welcome_message');
}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */