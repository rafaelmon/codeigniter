<?php
class Admin extends CI_Controller {

	function Admin()
	{
		parent::__construct();	
                echo "algo";
	}
	
	function index()
	{
		header ("Expires: Thu, 31 Dic 1976 23:59:00 GMT"); //la pagina expira en una fecha pasada
		header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); //ultima actualizacion ahora cuando la cargamos
		header ("Cache-Control: no-cache, must-revalidate"); //no guardar en CACHE
		header ("Pragma: no-cache"); 
		$user=$this->session->userdata(USER_DATA_SESSION);
		if ($user)
		{
			if ($user['id']>0)
			{
				redirect('admin/home', 'refresh');
			}
		}
		else
		{
			$variables['error'] = "";
			$this->load->view('admin/loginlayout',$variables);
		}
	}
	
	public function index_js()
	{
		$this->load->view('admin/refresh_ajax');
	}
	
	public function verificar()
	{
		$data = array();
		if($this -> input -> post('username') and $this -> input -> post('password'))
		{ // Verificamos si llega mediante post el username
		
			$username = $this -> input -> post('username');
			$username = str_replace('"',"",$username);
			$username = str_replace("'","",$username);
			$username = str_replace(";","",$username);
			$username = str_replace(",","",$username);
			$username = str_replace("@","",$username);
			$password = $this -> input -> post('password');
			$this->load-> model('common_model','common_model',true);
                        //Llamamos a la función login dentro del modelo common mandando los argumentos password y username
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
					$this -> session -> set_userdata(USER_DATA_SESSION, $sess_array); //Iniciamos una sesi�n con los datos obtenidos de la base.
				}
				$actualizo = $this -> common_model -> ultimoAcceso($sess_array['id']);
				
					/*Bloque de Auditoría*/
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
			{ // La validación falla
				$data['error'] = 'Nombre de usuario / Password Incorrecto'; //Error que ser� enviado a la vista en forma de arreglo
				$this->load-> view('admin/loginlayout', $data); //Cargamos el mensaje de error en la vista.
//				echo "{success: false, errors: { reason: '".$data['error']."' }}";
			}
		}
		else
		{
			$data['error'] = 'Error...Datos Incorrectos';
			$this->load-> view('admin/loginlayout',$data);
		}
	}
	
	public function salir()
	{
		$user=$this->session->userdata(USER_DATA_SESSION);
		$usuario_id = $user['id'];
		if ($user)
		{
			$this->session->unset_userdata(USER_DATA_SESSION);
			
			/*Bloque de Auditor�a*/
			$this->load->model("auditoria_model","auditoria_model",true);
			$datos['id_usuario'] = $usuario_id;
			$datos['usuario_nombre'] = $user['nombre']." ".$user['apellido'];
			$datos['id_aa'] = 2;
			$datos['ip_origen'] = $_SERVER['REMOTE_ADDR'];
			$datos['fecha'] = date("Y-m-d H:i:s");
			$this->auditoria_model->cargarAccion($datos);
			/*Fin bloque*/			
		}
		
		//redirect('admin/admin', 'refresh');
		header("location: ".site_url('admin'));
		die();
	}
}
?>