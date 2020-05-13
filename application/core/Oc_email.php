<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Oc_email {
    
    function __construct()
    {
    }

    function enviar($mail)
    {
        $CI =& get_instance();
        $CI->load->model('sys_mailing','mailing',true);
        echo $CI->load->library('email');
        echo '<pre>'.print_r($mail,true)."</pre>";
    }
}
?>
