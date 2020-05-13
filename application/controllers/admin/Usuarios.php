<?php
class Usuarios extends CI_Controller
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
			$variables['permiso_permiso'] = $this->permisos_model->checkIn($user['perfil_id'],2);
			$this->load->view('admin/usuarios/listado',$variables);
		}
		else
		{
			redirect("admin/admin/index_js","location", 301);
		}
	}
	
	public function listado()
	{
		$this->load->model('usuarios_model','usuarios_model',true);
		$start = $this->input->post("start");
		$limit = $this->input->post("limit");
		$filtro = $this->input->post("query");
		$sort = $this->input->post("sort");
		$dir = $this->input->post("dir");
		$listado = $this->usuarios_model->listado($start, $limit, $filtro, $sort, $dir);
		echo $listado;
	}
	
	
	public function eliminar()
	{
		$user=$this->session->userdata(USER_DATA_SESSION);
		if ($user['id']>0)
		{
			$this->load->model("permisos_model","permisos_model",true);
			$permisos = $this->permisos_model->checkIn($user['perfil_id'],2);
			if ($permisos['Baja'])
			{
				$array=json_decode($_POST["ids"]);
				$this->load->model('usuarios_model','usuarios_model',true);
				$b=1;
				foreach($array as $v)
				{
					if(!$this->usuarios_model->delete($v))
					{	
						$b=0;
					}
				}
				echo $b;
			}
		}
	}
	
	public function modificar()
	{
		$user=$this->session->userdata(USER_DATA_SESSION);
		if ($user['id']>0)
		{
			$this->load->model("permisos_model","permisos_model",true);
			$permisos = $this->permisos_model->checkIn($user['perfil_id'],2);
			if ($permisos['Modificacion'])
			{
				$campos=json_decode($_POST["campos"]);
				$valores=json_decode($_POST["valores"]);
				$this->load->model('usuarios_model','usuarios_model',true);
				for($i=0;$i<count($campos);$i++)
				{
					if($campos[$i]=="password")
					{
						$encriptado=MD5($valores[$i]);
						$datos[$campos[$i]]=$encriptado;
					}
					elseif($campos[$i]=="usuario")
					{
						if(!$this->usuarios_model->check_user($valores[$i],''))
						{
							echo 10;
							die();
						}
						$datos[$campos[$i]]=$valores[$i];
					}
					elseif($campos[$i]=="email")
					{
						if(!$this->usuarios_model->check_email($valores[$i],''))
						{
							echo 11;
							die();
						}
						$datos[$campos[$i]]=$valores[$i];
					}
					else
					{
						$datos[$campos[$i]]=$valores[$i];
					}
				}
				$b=1;
				if(!$this->usuarios_model->edit($_POST["id"],$datos))
				{
					$b=0;
				}
				echo $b;
			}
		}
	}
	
	public function update_user()
	{
		$user=$this->session->userdata(USER_DATA_SESSION);
		if ($user['id'] > 0)
		{
			$this->load->model('permisos_model','permisos_model',true);
			$permiso = $this->permisos_model->checkIn($user['perfil_id'],2);
			if ($permiso['Modificacion'])
			{
				$datos['usuario_id'] = $this->input->post("usuario_id");
				$datos['apellido'] = $this->input->post("apellido");
				$datos['nombre'] = $this->input->post("nombre");
				$datos['usuario'] = $this->input->post("usuario");
				$datos['password'] = $this->input->post("password");
				$datos['email'] = $this->input->post("email");
				$datos['provincia_id'] = $this->input->post("provincia_id");
				if ($datos['provincia_id']=="")
					$datos['provincia_id'] = 0;
				$datos['perfil_id'] = $this->input->post("perfil_id");
				$datos['ciudad'] = $this->input->post("ciudad");
				$datos['domicilio'] = $this->input->post("domicilio");
				$datos['telefono'] = $this->input->post("telefono");
				$datos['estado'] = $this->input->post("estado");
				$datos['tipo'] = $this->input->post("tipo");
				
				
				$datos['pais_id_reside']=0;
				$datos['pais_id_nacimiento']=0;
				$datos['ocupacion_id']=0;
				$datos['fecha_nac']=0;
				$datos['sexo']=0;
				$datos['tipodocu']='';
				$datos['numdoc']=0;
				$datos['codigo_habilitacion']=0;
				$datos['recibe_titulares']=0;
				$datos['IP_reg']=0;
				$datos['usuario_id_aud']=$user['id'];
				
				$vaForm = true;
				if ($datos['apellido']=="")
					$vaForm = false;
				if ($datos['nombre']=="")
					$vaForm = false;
				if ($datos['usuario']=="")
					$vaForm = false;
				if ($datos['usuario_id']=="")
					$vaForm = false;
				if ($datos['email']=="")
					$vaForm = false;
				if ($datos['perfil_id']=="")
					$vaForm = false;
				if ($vaForm)
				{
					$this->load->model("usuarios_model","usuarios_model",true);
					if ($this->usuarios_model->check_user($datos['usuario'],$datos['usuario_id']))
					{
						if ($this->usuario->check_email($datos['email'],$datos['usuario_id']))
						{
							$this->load->model("lector","lector",true);
							if ($this->lector->insert_update($datos))
								echo '1';
							else
								echo '0';
						}
						else
						{
							echo '3';
						}
					}
					else
					{
						echo '2';
					}
				}
				else
				{
					echo '0';
				}
			}
			else
			{
				echo '4';
			}
		}
		else
		{
			redirect("admin/admin/index_js","location", 301);
		}
	}
        
	public function insert()
	{
		$user=$this->session->userdata(USER_DATA_SESSION);
		if ($user['id']>0)
		{
			$this->load->model("permisos_model","permisos_model",true);
			$permisos = $this->permisos_model->checkIn($user['perfil_id'],2);
			if ($permisos['Alta'])
			{
				$datos=array();
				$datos['email']         =$this->input->post("email");
				$datos['usuario']       =$this->input->post("usuario");
				$datos['password']      =md5($this->input->post("password"));
				$datos['id_persona']     =$this->input->post("id_persona");
				$datos['id_perfil']     =$this->input->post("id_perfil");
//				$datos['id_supervisor'] =$this->input->post("id_supervisor");
				$b=1;
				$this->load->model('usuarios_model','usuarios',true);
				if($this->usuarios->check_user($datos['usuario']))
				{
                                    if($this->usuarios->check_email($datos['email']))
                                    {
                                        if($this->usuarios->check_persona($datos['id_persona']))
                                        {
                                                if(!$this->usuarios->insert($datos))
                                                {
                                                        $b=4;
                                                }

                                        }
                                        else
                                            $b=5;
                                    }
                                    else
                                            $b=3;
				}
				else
					$b = 2;
				
				echo $b;
			}
		}
	}
	public function listing_permisos()
	{
		$user=$this->session->userdata(USER_DATA_SESSION);
		if ($user['id'] > 0)
		{
			$this->load->model('usuarios_model','usuarios_model',true);
			$var=$this->usuarios_model->listado_permisos($this->input->post("id"));
			echo $var;	
		}
		else
		{
			redirect("admin/admin/index_js","location", 301);
		}
	}
	public function listing_modulos()
	{
		$user=$this->session->userdata(USER_DATA_SESSION);
		if ($user['id'] > 0)
		{
			$this->load->model('usuarios_model','usuarios_model',true);
			$var=$this->usuarios_model->listado_modulos($this->input->post("id"));
			echo $var;	
		}
		else
		{
			redirect("admin/admin/index_js","location", 301);
		}
	}
	public function agregar_modulo()
	{
		$user=$this->session->userdata(USER_DATA_SESSION);
		if ($user['id']>0)
		{
			$this->load->model("permisos_model","permisos_model",true);
			$permisos = $this->permisos_model->checkIn($user['perfil_id'],9);
			if ($permisos['Alta'])
			{
				$id_modulo=$_POST["id_modulo"];
				$id_usuario=$_POST["id_usuario"];
				$b=1;
				$this->load->model('usuarios_model','usuarios_model',true);
				$this->load->model('permisos_model','permisos_model',true);
				if($this->permisos_model->check_disponible($id_modulo,$id_usuario))
				{
					if(!$this->usuarios_model->agregar_modulo($id_modulo,$id_usuario))
					{
						$b=0;
					}
				}
				else
				{
					$b=2;
				}
				echo $b;
			}
		}
	}
	public function eliminar_modulo()
	{
		$user=$this->session->userdata(USER_DATA_SESSION);
		if ($user['id']>0)
		{
			$this->load->model("permisos_model","permisos_model",true);
			$permisos = $this->permisos_model->checkIn($user['perfil_id'],9);
			if ($permisos['Baja'])
			{
				$id_eliminar=$_POST["id_usuario_modulo"];
				$b=1;
				$this->load->model('usuarios_model','usuarios_model',true);
				if(!$this->usuario->eliminar_modulo($id_eliminar))
				{
					$b=0;
				}
				echo $b;
			}
		}
	}
	public function tiposUsuarios()
	{
		$user=$this->session->userdata(USER_DATA_SESSION);
		if ($user['id']>0)
		{
			$this->load->model("usuarios_model","usuarios_model",true);
			$jcode = $this->usuarios_model->dameTipos();
			echo $jcode;
		}
		else
		{
			redirect("admin/admin/index_js","location", 301);
		}
	}
	public function perfilesUsuarios()
	{
//		$user=$this->session->userdata(USER_DATA_SESSION);
//		if ($user['id']>0)
//		{
			$this->load->model("usuarios_model","usuarios_model",true);
			$jcode = $this->usuarios_model->damePerfiles();
			echo $jcode;
//		}
//		else
//		{
//			redirect("admin/admin/index_js","location", 301);
//		}
	}
	public function personasCombo()
	{
            $limit=$this->input->post("limit");
            $start=$this->input->post("start");
            $query=$this->input->post("query");
            $this->load->model("personas_model","personas",true);
            $jcode = $this->personas->damePersonasComboTpl($limit,$start,$query);
            echo $jcode;
	}
	public function empresasCombo()
	{
            $this->load->model("empresas_model","empresas",true);
            $jcode = $this->empresas->dameComboEmpresas();
            echo $jcode;
	}
	public function gerenciasCombo()
	{
            $id_empresa=$this->input->post("id_empresa");
            $this->load->model("gerencias_model","gerencias",true);
            $jcode = $this->gerencias->dameComboGerencias($id_empresa);
            echo $jcode;
	}
	public function departamentosCombo()
	{
            $id_gerencia=$this->input->post("id_gerencia");
            $this->load->model("departamentos_model","departamentos",true);
            $jcode = $this->departamentos->dameComboDepartamentos($id_gerencia);
            echo $jcode;
	}
	
	
	public function traer_datos_usuario()
	{
		$user=$this->session->userdata(USER_DATA_SESSION);
		if ($user['id']>0)
		{
			$user_id = $user['id'];
			$this->load->model('usuarios_model','usuarios_model',true);
			$jcode = $this->usuarios_model->traer_datos_usuario($user_id);
			echo $jcode;
		}
		else
		{
			redirect("admin/admin/index_js","location", 301);
		}
	}
	
	public function borrar_imagen()
	{
		$user=$this->session->userdata(USER_DATA_SESSION);
		if ($user['id'] > 0)
		{
			$this->load->model('permisos_model','permisos_model',true);
			$permiso = $this->permisos_model->checkIn($user['perfil_id'],23);
			if ($permiso['Modificacion'])
			{
				$usuario_id = $this->input->post("usuario_id");
				$this->load->model("lector","lector",true);
				$imagen = $this->lector->dameImagen($usuario_id);
				if ($this->lector->insertarImagen($usuario_id,""))
				{
					$path_borrar = PATH_BASE_FILE."fotos/avatar_lector/".$imagen;
					if (file_exists($path_borrar))
						unlink($path_borrar);
					echo 1;
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
	
	public function mi_perfil()
	{
		$user=$this->session->userdata(USER_DATA_SESSION);
		if ($user['id'] > 0)
		{
			$var['iidd'] = $user['id'];
			$this->load->view('admin/usuarios/mi_perfil',$var);
		}
		else
		{
			redirect("admin/admin/index_js","location", 301);
		}
	}
	
	public function cambiar_pass()
	{
		$user=$this->session->userdata(USER_DATA_SESSION);
		if ($user['id'] > 0)
		{
			$usuario_id = $user['id']; 
			$actual = $this->input->post("pass");
			$nuevo = $this->input->post("nuevopass");
			$repass = $this->input->post("repass");

			$this->load->model("usuarios_model","usuarios_model",true);
			if ($this->usuarios_model->validaPass($usuario_id,$actual))
			{
				if ($nuevo != "" and $nuevo==$repass)
				{
					if ($this->usuarios_model->changePass($usuario_id, $nuevo))
					{
						echo 1;
					}
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
			echo 2;//5;
	}
	
	public function cambiar_pass_ad()
	{
		$user=$this->session->userdata(USER_DATA_SESSION);
		if ($user['id'] > 0)
		{
			$usuario_id = $user['id']; 
			$actual = $this->input->post("pass");
			$nuevo = $this->input->post("nuevopass");
			$repass = $this->input->post("repass");
			
//			$nombre 		= $this->input->post("nombre");
//			$apellido 		= $this->input->post("apellido");
//			$descripcion 	= $this->input->post("descripcion");
			
			$this->load->model("usuarios_model","sys_usuario",true);
			
			$ret = 0;
			if($nuevo != "" && $actual!="" && $repass!="")
			{
				if ($nuevo==$repass)
				{
					if ($this->sys_usuario->validaPass($usuario_id,$actual))
					{
                                            if ($this->sys_usuario->changePass($usuario_id, $nuevo))
                                                echo 1;
                                            else
                                                echo 9;
					}
					else
                                            echo 2;
				}
				else
					echo 3;
			}
                        else 
                            echo 0;
			
//			if($nuevo == "" xor $ret == 1)
//			{
//				if ($this->usuario->changeDataMin($usuario_id, $nombre, $apellido, $descripcion))
//				{
//					if($ret==1)
//						echo 1;
//					else
//						echo 6;
//				}
//				else
//					echo 0;
//			}
		}
		else
			echo 2;//2; para q no desloguee al depurar . 5 = logout
	}
        public function supervisorCombo()
	{
            $limit=$this->input->post("limit");
            $start=$this->input->post("start");
            $query=$this->input->post("query");
            $this->load->model("personas_model","personas",true);
            $jcode = $this->personas->dameSupervisorCombo($limit,$start,$query);
            echo $jcode;
	}
}
?>