<?php
class Calificaciones extends CI_Controller
{
    private $modulo=43;
    private $user;
    private $permisos;
    private $roles;
	function __construct()
	{
		parent::__construct();
		header ("Expires: Thu, 31 Dic 1976 23:59:00 GMT"); //la pagina expira en una fecha pasada
		header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); //ultima actualizacion ahora cuando la cargamos
		header ("Cache-Control: no-cache, must-revalidate"); //no guardar en CACHE
		header ("Pragma: no-cache");
                $this->user=$this->session->userdata(USER_DATA_SESSION);
                if (!($this->user['id']>0)) 
                    redirect("admin/admin/index_js","location", 301);
                $this->load->model("permisos_model","permisos_model",true);
                $this->permisos= $this->permisos_model->checkIn($this->user['perfil_id'],$this->modulo);
	}
	
	public function index()
	{
            $modulo = $this->uri->segment(SEGMENTOS_DOM+2);
            if (!isset($modulo)) $modulo=0;
            $this->load->model('permisos_model','permisos_model',true);
            $variables['permiso'] = $this->permisos_model->checkIn($this->user['perfil_id'],$modulo);
            $this->load->view('ed/calificaciones/listado',$variables);
	}
	
	public function listado()
	{
             if ($this->permisos['Listar'])
            {
		$this->load->model('ed/ed_calificaciones_model','ed_calificaciones',true);
		$start = $this->input->post("start");
		$limit = $this->input->post("limit");
		$id_periodo = $this->input->post("f_id_periodo");
		$listado = $this->ed_calificaciones->listado($start, $limit,$id_periodo);
		echo $listado;
            }
            else
                echo "({success: false, error :'No tiene permisos para realizar la acciòn solicitada'})"; //
	}
        public function combo_periodos()
	{
            $this->load->model('ed/ed_evaluaciones_model','ed_evaluaciones',true);
//            echo 'entra';
            $var=$this->ed_evaluaciones->damePeriodosFiltro();
//            echo 'sale';die();
            echo $var;	
	}
        
        public function modificar()
	{
            if ($this->permisos['Modificacion'])
            {
                $this->load->model('ed/ed_calificaciones_model','ed_calificaciones',true);
                $campos=json_decode($_POST["campos"]);
                $valores=json_decode($_POST["valores"]);
                for($i=0;$i<count($campos);$i++)
                {
                        if($campos[$i]=="calificacion")
                        {
                            $datos[$campos[$i]]=$valores[$i];
                        }
                        elseif ($campos[$i]=="valor")
                        {
                            $datos[$campos[$i]]=$valores[$i];
                        }
                }
                $b=1;
                if(!$this->ed_calificaciones->edit($_POST["id"],$datos))
                {
                    $b=0;
                }
                echo $b;
            }
            else
                echo "({success: false, error :'No tiene permisos para realizar la acciòn solicitada'})"; 
	}
        
        public function insertar()
	{
            if ($this->permisos['Alta'])
            {
                $datos['id_periodo'] = $this->input->post("periodo");
                $datos['calificacion'] = $this->input->post("calificacion");
                $datos['valor'] = $this->input->post("valor");
                $datos['habilitado'] = 1;
                if ($datos['calificacion'] != '')
                {
                    if($datos['valor'] != '')
                    {
                        if($datos['id_periodo'] != 'Todos')
                        {
                            $this->load->model('ed/ed_calificaciones_model','ed_calificaciones',true);	
                            if ($this->ed_calificaciones->insert($datos))
                                echo 1;
                            else
                                echo 0;
                        }
                        else
                            echo 2;
                    }
                    else
                        echo 3;
                }
                else
                    echo 4;
            }
            else
                echo 5;//"({success: false, error :'No tiene permisos para realizar la acciòn solicitada'})"; 
	}
}
?>