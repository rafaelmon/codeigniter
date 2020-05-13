<?php
class Competencias extends CI_Controller
{
    private $modulo=40;
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
            $this->load->view('ed/competencias/listado',$variables);
	}
	
	public function listado()
	{
             if ($this->permisos['Listar'])
            {
		$this->load->model('ed/ed_competencias_model','competencias',true);
		$start = $this->input->post("start");
		$limit = $this->input->post("limit");
		$listado = $this->competencias->listado($start, $limit);
		echo $listado;
            }
            else
                echo "({success: false, error :'No tiene permisos para realizar la acci�n solicitada'})"; //
	}
        public function listado_sub()
	{
             if ($this->permisos['Listar'])
            {
                $this->load->model('ed/ed_subcompetencias_model','ed_subcompetencias',true);
                $start = $this->input->post("start");
                $limit = $this->input->post("limit");
                $id_competencia = $this->input->post("id");
                $listado = $this->ed_subcompetencias->listado($id_competencia,$start, $limit);
                echo $listado;
            }
            else
                echo "({success: false, error :'No tiene permisos para realizar la acci�n solicitada'})"; //
        }
        public function modificar()
	{
            if ($this->permisos['Modificacion'])
            {
                $id = $this->input->post('id_competencia');
                $datos['competencia'] = $this->input->post("competencia");
                if ($datos['competencia'] != "")
                {
                        $this->load->model('ed/ed_competencias_model','competencias',true);
                        if ($this->competencias->update($id,$datos))
                                echo 1;
                        else
                                echo 0;
                }
                else
                        echo 3;	
            }
            else
                echo "({success: false, error :'No tiene permisos para realizar la acci�n solicitada'})"; //
	}
        public function modificar_sub()
	{
            if ($this->permisos['Modificacion'])
            {
                $id = $this->input->post('id_subcompetencia');
                $datos['competencia'] = $this->input->post("subcompetencia");
                if ($datos['subcompetencia'] != "")
                {
                        $this->load->model('ed/ed_subcompetencias_model','subcompetencias',true);
                        if ($this->subcompetencias->update($id,$datos))
                                echo 1;
                        else
                                echo 0;
                }
                else
                        echo 3;	
            }
            else
                echo "({success: false, error :'No tiene permisos para realizar la acci�n solicitada'})"; //
	}
        
        public function insertar()
	{
            if ($this->permisos['Alta'])
            {
                $datos['competencia'] = $this->input->post("competencia");
                $datos['tipo'] = $this->input->post("tipo");
                $datos['habilitado'] = 1;
                if ($datos['competencia'] != '')
                {
                    $this->load->model('ed/ed_competencias_model','competencias',true);	
                    if ($this->competencias->insert($datos))
                        echo 1;
                    else
                        echo 0;
                }
                else
                    echo 2;
            }
            else
                echo "({success: false, error :'No tiene permisos para realizar la acci�n solicitada'})"; //
	}
        public function insertar_sub()
	{
            if ($this->permisos['Alta'])
            {
                $datos['subcompetencia'] = $this->input->post("subcompetencia");
                $datos['id_competencia'] = $this->input->post("id_competencia");
                $datos['obligatoria'] = $this->input->post("tipo");
                $datos['habilitado'] = 1;
//                if ($datos['id_competencia'] != NULL)
//                {
                    if ($datos['subcompetencia'] != '')
                    {
                        $this->load->model('ed/ed_subcompetencias_model','subcompetencias',true);	
                        if ($this->subcompetencias->insert($datos))
                            echo 1;
                        else
                            echo 0;
                    }
                    else
                    {
                        echo 2;
                    }
//                }
//                else
//                {
//                    echo 4; //
//                }
            }
            else
                echo "({success: false, error :'No tiene permisos para realizar la acci�n solicitada'})"; //
	}
}
?>