<?php
class Tiposdoc extends CI_Controller
{
    private $modulo=11;
    private $user;
    private $permisos;
    
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
            $variables['permiso_permiso'] = $this->permisos_model->checkIn($this->user['perfil_id'],$this->modulo);
            $this->load->view('dms/config/td/listado',$variables);
	}
	
	public function listado()
	{
		if ($this->permisos['Listar'])
                {
                    $this->load->model('dms/tiposdoc_model','tiposdoc',true);
                    $start = $this->input->post("start");
                    $limit = $this->input->post("limit");
                    $sort = $this->input->post("sort");
                    $dir = $this->input->post("dir");
                    $filtro  = ($this->input->post("query"))?$this->input->post("query"):"";
                    $campos="";
                    if ($this->input->post("fields"))
                    {
                        $campos = str_replace('"', "", $this->input->post("fields"));
                        $campos = str_replace('[', "", $campos);
                        $campos = str_replace(']', "", $campos);
                        $campos = explode(",",$campos);
    //                            echo "<pre>".print_r($campos,true)."</pre>";
                    }
                    $listado = $this->tiposdoc->listado($start, $limit, $filtro, $campos, $sort, $dir);
                    echo $listado;
                }
                else
                    echo -1; //No tiene permisos
                
	}
	
	public function modificar()
	{
            if ($this->permisos['Modificacion'])
            {
                    $campo=json_decode($_POST["campo"]);
                    $valor=json_decode($_POST["valor"]);
                    $this->load->model('dms/tiposdoc_model','tiposdoc',true);
                    if($campo[0]=="td")
                    {
                        $datos['td']=$valor[0];
                        if(!$this->tiposdoc->check_td($datos))
                            $datos[$campo[0]]=$valor[0];
                        else
                        {
                            echo 2;
                            die();
                        }
                    }
                    if($campo[0]=="detalle")
                        $datos[$campo[0]]=$valor[0];
                    if($campo[0]=="abv")
                        $datos[$campo[0]]=strtoupper($valor[0]);
                            
                    if(!$this->tiposdoc->edit($_POST["id"],$datos))
                        echo 0;
                    else
                        echo 1;
            }
	}
	
	public function insert()
	{
            if ($this->permisos['Alta'])		
            {
                $datos=array();
                $datos['td']      =$_POST["td"];
                $datos['abv']           =  strtoupper($_POST["abv"]);
                $datos['detalle']    =$_POST["detalle"];
                $this->load->model('dms/tiposdoc_model','tiposdoc',true);
                //antes de insertar verifico que no exita el registro
                if(!$this->tiposdoc->check_td($datos))
                {
                    if($this->tiposdoc->insert($datos))
                        echo 1; //todo OK
                    else
                        echo 2; //Error al insertar el registro
                }
                else
                        echo 0; //Error - Ya existe registro
            }
	}
}
        
?>