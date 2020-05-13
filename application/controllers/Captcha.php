<?php
class Captcha extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
            
        
        }
	public function pruebas()
	{
            $this->load->helper('captcha');
            $vals = array(
                            'img_path'	 => 'C:/xampp/htdocs/sca/images/captcha/',
                            'img_url'	 => 'http://localhost/sca/images/captcha/',
                            'expiration' =>  3600//tiempo en segundos 1hr=3600s
                        );
	
            $cap = create_captcha($vals);

            $data = array(
                            'captcha_id'	=> '',
                            'captcha_time'	=> (int)$cap['time'],
                            'ip_address'	=> $this->input->ip_address(),
                            'word'              => $cap['word']
                        );
        
            echo $cap['image'];
            
            $this->load->model('captcha_model','catcha',true);
            $this->catcha->registra($data);
            
//            $post['ip']=$this->input->ip_address();
//            $post['word']=$this->input->post('captcha');
//            
//            $this->catcha->valida($post);
        
        }
}
?>