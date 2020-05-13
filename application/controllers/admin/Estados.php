<?php
class Estados extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		header ("Expires: Thu, 27 Mar 1980 23:59:00 GMT"); //la pagina expira en una fecha pasada
		header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); //ultima actualizacion ahora cuando la cargamos
		header ("Cache-Control: no-cache, must-revalidate"); //no guardar en CACHE
		header ("Pragma: no-cache");
	}
	
	public function listado()
	{
		$user=$this->session->userdata(USER_DATA_SESSION);
		if ($user['id'] > 0)
		{
			$this->load->model("estados_model","estados_model",true);
			echo $this->estados_model->dameListado();
		}
		else
		{
			redirect("admin/admin/index_js","location", 301);
		}
	}
}
?>