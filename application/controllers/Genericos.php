<?php
class Genericos extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		header ("Expires: Thu, 27 Mar 1980 23:59:00 GMT"); //la pagina expira en una fecha pasada
		header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); //ultima actualizacion ahora cuando la cargamos
		header ("Cache-Control: no-cache, must-revalidate"); //no guardar en CACHE
		header ("Pragma: no-cache");
	}
	
	public function habilitar()
	{
		$user=$this->session->userdata(USER_DATA_SESSION);
		if ($user['id']>0)
		{
			$valor=0;
			if ($_POST['value']=="true"){
				$valor=1;
			}
			$tabla=$_POST['tabla'];
			$campo_id=$_POST['campo_id'];
			$campo=$_POST['campo'];
			$id=$_POST['id'];
			
			
			$b=1;
			$this->load->model('genericos_model','genericos_model',true);
			if(!$this->genericos_model->habilitar($tabla,$campo,$valor,$campo_id,$id))
			{
				$b=0;
			}
			echo $b;
		}
		else
		{
			redirect("admin/admin/index_js","location", 301);
		}
	}
	public function borrar()
	{
		$user=$this->session->userdata(USER_DATA_SESSION);
		if ($user['id']>0)
		{
			$campo_id=$this->input->post('campo_id');
			$ids=$this->input->post('ids');
			$tabla=$this->input->post('tabla');		
			$ids=json_decode($ids);
			$b=1;
			$this->load->model('genericos_model','genericos_model',true);
			if(!$this->genericos_model->borrar($tabla,$campo_id,$ids))
			{
				$b=0;
			}
			echo $b;
		}
		else
		{
			redirect("admin/admin/index_js","location", 301);
		}
	}
	
	public function paises()
	{
		$user=$this->session->userdata(USER_DATA_SESSION);
		if ($user['id']>0)
		{
			$this->load->model('genericos_model','genericos_model',true);
			$jcode = $this->genericos_model->damePaises();
			echo $jcode;
		}
	}
	
	public function provincias()
	{
		$user=$this->session->userdata(USER_DATA_SESSION);
		if ($user['id']>0)
		{
			$this->load->model('genericos_model','genericos_model',true);
			$pais_id = $this->input->post("id_pais");
			if (!$pais_id)
				$pais_id = 0;
			$jcode = $this->genericos_model->dameProvincias($pais_id);
			echo $jcode;
		}
	}
	public function tipos_documentos()
	{
		$user=$this->session->userdata(USER_DATA_SESSION);
		if ($user['id']>0)
		{
			$this->load->model('genericos_model','genericos_model',true);
			$jcode = $this->genericos_model->dameTiposDocumento();
			echo $jcode;
		}
	}
        public function sesion()
        {
            $user=$this->user=$this->session->userdata(USER_DATA_SESSION);
//            echo "<pre>".print_r($user,true)."</pre>";
           if (!($this->user['id']>0))  { 
               //Sesion Expirada
                echo 0; 
                $this->session->sess_destroy();
            } 
            else {
                //Sesion Activa
                echo 1;
            }
            
//            if (!($this->user['id']>0)) 
        }
        public function libreria()
        {
            $this->load->library('db_constants_library');
            $vars=$this->db_constants_library->set();
        }
	
}
?>