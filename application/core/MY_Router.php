<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class MY_Router extends CI_Router 
{ 
    function MY_Router()
    {
        parent::__construct();
    }
 
    function _validate_request($segments)
    {
        return parent::_validate_request($segments);
    }
}

?>