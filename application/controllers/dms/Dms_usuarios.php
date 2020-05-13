<?php
class Dms_usuarios extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		header ("Expires: Thu, 31 Dic 1976 23:59:00 GMT"); //la pagina expira en una fecha pasada
		header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); //ultima actualizacion ahora cuando la cargamos
		header ("Cache-Control: no-cache, must-revalidate"); //no guardar en CACHE
		header ("Pragma: no-cache");
                $user=$this->session->userdata(USER_DATA_SESSION);
                    if (!($user['id']>0)) 
                        redirect("admin/admin/index_js","location", 301);
	}
	
	public function index()
	{
		$user=$this->session->userdata(USER_DATA_SESSION);
		if ($user['id'] > 0)
		{			
			$modulo = $this->uri->segment(SEGMENTOS_DOM+2);
			if (!isset($modulo)) $modulo=0;
			$this->load->model('permisos_model','permisos_model',true);
			$variables['permiso'] = $this->permisos_model->checkIn($user['perfil_id'],$modulo);
			$variables['permiso_permiso'] = $this->permisos_model->checkIn($user['perfil_id'],12);
			$this->load->view('dms/config/usuarios/listado',$variables);
		}
		else
		{
			redirect("admin/admin/index_js","location", 301);
		}
	}
	
	public function listado()
	{
		$this->load->model('dms/dms_usuarios_model','usuarios',true);
		$start = $this->input->post("start");
		$limit = $this->input->post("limit");
		$filtro = $this->input->post("query");
		$sort = $this->input->post("sort");
		$dir = $this->input->post("dir");
		$listado = $this->usuarios->listado($start, $limit, $filtro, $sort, $dir);
		echo $listado;
	}
	
	public function usuariosRolesCombo()
	{
            $limit=$this->input->post("limit");
            $start=$this->input->post("start");
            $query=$this->input->post("query");
            $this->load->model("dms/dms_usuarios_model","usuarios",true);
            $jcode = $this->usuarios->dameUsuariosPersonasComboTpl($limit,$start,$query);
            echo $jcode;
	}
	
	public function agregar_usuario()
	{
		$user=$this->session->userdata(USER_DATA_SESSION);
		if ($user['id']>0)
		{
			$this->load->model("permisos_model","permisos_model",true);
			$permisos = $this->permisos_model->checkIn($user['perfil_id'],12);
			if ($permisos['Alta'])
			{
                            $datos=array();
                            $datos['id_usuario']       =$this->input->post("id_usuario");
                            $b=1;
                            $this->load->model('dms/dms_usuarios_model','usuarios',true);
                                if($this->usuarios->check_user($datos['id_usuario']))
                                {
                                    if(!$this->usuarios->insert($datos))
                                    {
                                            $b=0;//error insert
                                    }
                                    else
                                            $b=1;//insert ok
                                }
                                else 
                                    $b=2; //id_usuario ya presente en gr_roles

                            echo $b;
			}
                        else
                            echo 3; //error permisos
		}
	}
        public function eliminar_permiso()
	{
		$user=$this->session->userdata(USER_DATA_SESSION);
		if ($user['id']>0)
		{
			$this->load->model("permisos_model","permisos_model",true);
			$permisos = $this->permisos_model->checkIn($user['perfil_id'],12);
			if ($permisos['Baja'])
			{
				$id = $this->input->post("id_permiso");
				$this->load->model("gestion_riesgo/gr_roles_model","gr_roles",true);
				if ($this->gr_roles->deletePermiso($id))
					echo 1;
				else
					echo 0;
			}
			else
				echo 2;
		}
		else
		{
			redirect("admin/admin/index_js","location", 301);
		}
	}
}
?>