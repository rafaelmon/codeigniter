<?php
class Perfiles extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		header ("Expires: Thu, 31 Dic 1976 23:59:00 GMT"); //la pagina expira en una fecha pasada
		header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); //ultima actualizacion ahora cuando la cargamos
		header ("Cache-Control: no-cache, must-revalidate"); //no guardar en CACHE
		header ("Pragma: no-cache");
	}
	
	public function index()
	{
		$user=$this->session->userdata(USER_DATA_SESSION);
		if ($user['id']>0)
		{
			$modulo = $this->uri->segment(SEGMENTOS_DOM+2);
			if (!isset($modulo)) $modulo=0;
			$this->load->model('permisos_model','permisos_model',true);
			$variables['permiso'] = $this->permisos_model->checkIn($user['perfil_id'],$modulo);
			$variables['permiso_permiso'] = $this->permisos_model->checkIn($user['perfil_id'],4);
			$this->load->view('admin/perfiles/listado',$variables);
		}
		else
		{
			redirect("admin/admin/index_js","location", 301);
		}
	}
	
	public function listado()
	{
		$user=$this->session->userdata(USER_DATA_SESSION);
		if ($user['id']>0)
		{
			$start = $this->input->post("start");
			$limit = $this->input->post("limit");
			$this->load->model('perfiles_model','perfiles_model',true);
			$jcode = $this->perfiles_model->listado($start,$limit);
			echo $jcode;
		}
		else
		{
			redirect("admin/admin/index_js","location", 301);
		}
	}
	
	public function modificar()
	{
		$user=$this->session->userdata(USER_DATA_SESSION);
		if ($user['id']>0)
		{
			$this->load->model("perfiles_model","perfiles_model",true);
                        $this->load->model("permisos_model","permisos_model",true);
			$permisos = $this->permisos_model->checkIn($user['perfil_id'],5);
			if ($permisos['Modificacion'])
			{
				$id = $this->input->post('id_perfil');
				$datos['perfil'] = $this->input->post("perfil");
				$datos['detalle'] = $this->input->post("detalle");
				if ($datos['perfil'] != "")
				{
					$this->load->model('perfiles_model','perfiles_model',true);
					if ($this->perfiles_model->update($id,$datos))
						echo 1;
					else
						echo 0;
				}
				else
					echo 3;	
			}
			else
				echo 2;
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
			$this->load->model("permisos_model","permisos_model",true);
			$permisos = $this->permisos_model->checkIn($user['perfil_id'],5);
			if ($permisos['Baja'])
			{
				$b = 1;
				$ids = json_decode($this->input->post('ids'));
				if (count($ids)>0)
				{
					$this->load->model('perfiles_model','perfiles_model',true);
					foreach ($ids as $id)
					{
						if ($this->perfiles_model->checkRelacion($id))
						{
							if (!$this->perfiles_model->delete($id))
							{
								$b = 0;
								break;
							}
						}
						else
						{
							$b = 3;
							break;
						}
					}
				}
				else
					$b = 0;
					
				echo $b;		
			}
			else
				echo 2;
		}
		else
		{
			redirect("admin/admin/index_js","location", 301);
		}
	}
	
	public function insertar()
	{
		$user=$this->session->userdata(USER_DATA_SESSION);
		if ($user['id']>0)
		{
			$this->load->model("permisos_model","permisos_model",true);
			$permisos = $this->permisos_model->checkIn($user['perfil_id'],5);
			if ($permisos['Alta'])
			{
				$datos['perfil'] = $this->input->post("perfil");
				$datos['detalle'] = $this->input->post("detalle");
				if ($datos['perfil']&&$datos['detalle'])
				{
					$this->load->model("perfiles_model","perfiles_model",true);
					if ($this->perfiles_model->insert($datos))
						echo 1;
					else
						echo 0;
				}
				else
				{
					echo 2;
				}
			}
			else
				echo 3;
		}
		else
		{
			redirect("admin/admin/index_js","location", 301);
		}
	}
	
	public function listado_permisos()
	{
		$user=$this->session->userdata(USER_DATA_SESSION);
		if ($user['id']>0)
		{
			$this->load->model("permisos_model","permisos_model",true);
			$permisos = $this->permisos_model->checkIn($user['perfil_id'],4);
			if ($permisos['Listar'])
			{
				$perfil_id = $this->input->post("id_perfil");
				$this->load->model("perfiles_model","perfiles_model",true);
				echo $this->perfiles_model->listadoPermisos($perfil_id);	
			}
			else
			{
				echo '({"total":"0","rows":""})';
			}
		}
		else
		{
			redirect("admin/admin/index_js","location", 301);
		}
	}
	
	public function eliminar_modulo_permiso()
	{
		$user=$this->session->userdata(USER_DATA_SESSION);
		if ($user['id']>0)
		{
			$this->load->model("permisos_model","permisos_model",true);
			$permisos = $this->permisos_model->checkIn($user['perfil_id'],4);
			if ($permisos['Baja'])
			{
				$id = $this->input->post("perfil_modulo_id");
				$this->load->model("perfiles_model","perfiles_model",true);
				if ($this->perfiles_model->deletePermiso($id))
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
	
	public function listado_modulos()
	{
		$user=$this->session->userdata(USER_DATA_SESSION);
		if ($user['id']>0)
		{
			$this->load->model("perfiles_model","perfiles_model",true);
			echo $this->perfiles_model->listadoModulos();
		}
	}
	public function listado_modulosxPerfil()
	{
		$user=$this->session->userdata(USER_DATA_SESSION);
		$id_perfil=$this->input->post("id_perfil");
		if ($user['id']>0)
		{
			$this->load->model("perfiles_model","perfiles_model",true);
			echo $this->perfiles_model->listadoModulosXPerfil($id_perfil);
		}
	}
	
	public function agregar_modulo()
	{
		$user=$this->session->userdata(USER_DATA_SESSION);
		if ($user['id']>0)
		{
			$this->load->model("permisos_model","permisos_model",true);
			$permisos = $this->permisos_model->checkIn($user['perfil_id'],4);
			if ($permisos['Alta'])
			{
				$modulo_id = $this->input->post("id_modulo");
				$perfil_id = $this->input->post("id_perfil");
				if ($modulo_id)
				{
					$datos['id_modulo'] = $modulo_id;
					$datos['id_perfil'] = $perfil_id;
					$this->load->model("perfiles_model","perfiles_model",true);
					if ($this->perfiles_model->insertPermiso($datos))
						echo 1;
					else
						echo 0;
				}
				else
					echo 3;
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