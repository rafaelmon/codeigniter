<?php

class Home extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		header ("Expires: Thu, 31 Dic 1976 23:59:00 GMT"); //la pagina expira en una fecha pasada
		header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); //ultima actualizacion ahora cuando la cargamos
		header ("Cache-Control: no-cache, must-revalidate"); //no guardar en CACHE
		header ("Pragma: no-cache");	
	}
	
	public function index($resLog=0)
	{
//                $this->load->library('user_agent');
//                $agent = $this->agent->browser();
//                echo $agent;
		$user=$this->session->userdata(USER_DATA_SESSION);
//		$user_agent=$this->session->userdata('user_agent');
//                echo "<pre>".print_r($user_agent,true)."</pre>";
		if ($user['id']>0)
		{
			/*header*/
			$this->load->view('admin/include/header');
			/********/
			
                        $this->load->model("modulos_model","modulos_model",true);
			$menu = $this->modulos_model->generarMenu($user['perfil_id']);
			$arg['menu']=$menu;
			$arg['resLog'] = $resLog;
			
			$this->load->model('permisos_model','permisos_model',true);
			
                        /*panel*/	
			$this->load->view('admin/home/panel',$arg);
			/********/
			/*footer*/
			$this->load->view('admin/include/footer');
			/********/
		}
		else
		{
                    if($this -> input -> post('username') and $this -> input -> post('password'))
                    { 
                        $this->_verificar($this -> input -> post('username'),$this -> input -> post('password'));
                    }
                    else
                    {
			$variables['error'] = "";
			$this->load->view('admin/loginlayout',$variables);
                    }
                    
                    
		}
	}
        function _verificar($usuario=false,$pass=false)
	{
		$data = array();
		if($usuario and $pass)
		{ // Verificamos si llega mediante post el username
		
			$username = $usuario;
			$username = str_replace('"',"",$username);
			$username = str_replace("'","",$username);
			$username = str_replace(";","",$username);
			$username = str_replace(",","",$username);
			$username = str_replace("@","",$username);
			$password = $pass;
			$this->load-> model('common_model','common_model',true);
                        //Llamamos a la funci�n login dentro del modelo common mandando los argumentos password y username
			$result = $this -> common_model -> login($username, $password); 
		
			if($result)
			{ //login exitoso
				$sess_array = array();
				foreach($result as $row)
				{
					$sess_array = array(
						'id' => $row -> id_usuario,
						'perfil_id' => $row -> id_perfil,
						'perfil_nomb' => $row -> perfil_nomb,
						'usuario' => $row -> usuario,
						'nombre' => $row -> nombre,
						'apellido' => $row -> apellido,
						'email' => $row -> email
					);
					$this -> session -> set_userdata(USER_DATA_SESSION, $sess_array); //Iniciamos una sesion con los datos obtenidos de la base.
				}
				$actualizo = $this -> common_model -> ultimoAcceso($sess_array['id']);
				
					/*Bloque de Auditor�a*/
					$this->load->model("auditoria_model","auditoria_model",true);
					$datos['id_usuario'] = $sess_array['id'];
					$datos['usuario_nombre'] = $sess_array['nombre']." ".$sess_array['apellido'];
					$datos['id_aa'] = 1;
					$datos['ip_origen'] = $_SERVER['REMOTE_ADDR'];
					$datos['fecha'] = date("Y-m-d H:i:s");
					$this->auditoria_model->cargarAccion($datos);
					/*Fin bloque*/
					
					redirect('admin/', 'refresh');
					echo "{success: true}";
			}
			else
			{ // La validaci�n falla
				$data['error'] = 'Error...Datos Incorrectos'; //Error que sera enviado a la vista en forma de arreglo
				$this->load-> view('admin/loginlayout', $data); //Cargamos el mensaje de error en la vista.
			}
		}
		else
		{
			$data['error'] = 'Error...Datos Incorrectos';
			$this->load-> view('admin/loginlayout',$data);
		}
	}
}