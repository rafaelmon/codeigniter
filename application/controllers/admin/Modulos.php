<?php
class Modulos extends CI_Controller
{
	function __construct()
	{
		parent::__construct(); 
		header ("Expires: Thu, 27 Mar 1980 23:59:00 GMT"); //la pagina expira en una fecha pasada
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
		if ($user['id']>0)
		{
			/*header*/
			//$this->load->view('admin/include/header');
			/********/
			
			$modulo = $this->uri->segment(SEGMENTOS_DOM+2);
			if (!isset($modulo)) $modulo=0;
			$this->load->model('permisos_model','permisos_model',true);
			$variables['permiso'] = $this->permisos_model->checkIn($user['perfil_id'],$modulo);
			$this->load->view('admin/modulos/listado',$variables);
			
			/*footer*/
			//$this->load->view('admin/include/footer');
			/********/
		}
		else
		{
			redirect("admin/admin/index_js","location", 301);
		}
	}
	
	public function padres()
	{
		$this->load->model('modulos_model','modulos_model',true);
		$jcode = $this->modulos_model->damePadres();
		echo $jcode;
	}
	
	public function listado()
	{
		$user=$this->session->userdata(USER_DATA_SESSION);
		if ($user['id']>0)
		{
                    $start = $this->input->post("start");
                    $limit = $this->input->post("limit");
                    $query = $this->input->post("query");
                    $filtro = 0;
                    if($this->input->post("filtro_padre_id"))
                            $filtro = $this->input->post("filtro_padre_id");
                    $criterioBusqueda  = ($this->input->post("query"))?$this->input->post("query"):"";
                    $camposBusqueda="";
                    if ($this->input->post("fields"))
                    {
                        $camposBusqueda = str_replace('"', "", $this->input->post("fields"));
                        $camposBusqueda = str_replace('[', "", $camposBusqueda);
                        $camposBusqueda = str_replace(']', "", $camposBusqueda);
                        $camposBusqueda = explode(",",$camposBusqueda);
    //                            echo "<pre>".print_r($campos,true)."</pre>";
                    }
			$this->load->model('modulos_model','modulos_model',true);
			$jcode = $this->modulos_model->listado($start,$limit,$filtro,$criterioBusqueda,$camposBusqueda);
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
			$this->load->model("permisos_model","permisos_model",true);
			$permisos = $this->permisos_model->checkIn($user['perfil_id'],3);
			if ($permisos['Modificacion'])
			{
				$id = $_POST['id_modulo'];
				$campos = json_decode($_POST['campos']);
				$valores = json_decode($_POST['valores']);
				if (count($campos)>0)
				{
					foreach ($campos as $indice=>$camp)
					{
						$datos[$camp] = $valores[$indice];
					}
					$this->load->model('modulos_model','modulos_model',true);
					if ($this->modulos_model->update($id,$datos))
						echo 1;
					else
						echo 0;
				}
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
	
	public function borrar()
	{
		$user=$this->session->userdata(USER_DATA_SESSION);
		if ($user['id']>0)
		{
			$this->load->model("permisos_model","permisos_model",true);
			$permisos = $this->permisos_model->checkIn($user['perfil_id'],3);
			if ($permisos['Baja'])
			{
				$b = 1;
				$ids = json_decode($_POST['ids']);
				if (count($ids)>0)
				{
					$this->load->model('modulos_model','modulos_model',true);
					foreach ($ids as $id)
					{
						if (!$this->modulos_model->tieneHijos($id))
						{
							if (!$this->modulos_model->delete($id))
								$b = 0;
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
			$permisos = $this->permisos_model->checkIn($user['perfil_id'],3);
			if ($permisos['Alta'])
			{
				$datos['modulo'] = $this->input->post("modulo");
				$datos['accion'] = $this->input->post("accion");
				$datos['icono'] = $this->input->post("icono");
				$datos['padre_id'] = $this->input->post("padre_id");
				$datos['orden'] = $this->input->post("orden");
				$datos['hijos'] = ($this->input->post("hijos")=="true")?1:0;
				$datos['menu'] = ($this->input->post("menu")=="true")?1:0;
				if ($datos['modulo'] and $datos['orden'])
				{
					$this->load->model("modulos_model","modulos_model",true);
					if ($this->modulos_model->insert($datos))
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

}
?>