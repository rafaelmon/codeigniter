<?php
class Areas_rpyc extends CI_Controller
{
    private $modulo=35;
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
//            echo "<pre>".print_r($this->permisos,true)."</pre>";
	}
	
	public function index()
	{
            $modulo = $this->uri->segment(SEGMENTOS_DOM+2);
            if (!isset($modulo)) $modulo=0;
            $this->load->model('permisos_model','permisos_model',true);
            $variables['permiso'] = $this->permisos_model->checkIn($this->user['perfil_id'],$modulo);
            $variables['permiso_permiso'] = $this->permisos_model->checkIn($this->user['perfil_id'],$this->modulo);
            $this->load->view('it/areas_rpcy/listado',$variables);
	}
	
	public function listado()
	{
		if ($this->permisos['Listar'])
                {
                    $this->load->model('gestion_riesgo/gr_areas_rpyc_model','areas_rpyc',true);
                    $start = $this->input->post("start");
                    $limit = $this->input->post("limit");
                    $sort = $this->input->post("sort");
                    $dir = $this->input->post("dir");
                    $busqueda  = ($this->input->post("query"))?$this->input->post("query"):"";
                    $campos="";
                    if ($this->input->post("fields"))
                    {
                        $campos = str_replace('"', "", $this->input->post("fields"));
                        $campos = str_replace('[', "", $campos);
                        $campos = str_replace(']', "", $campos);
                        $campos = explode(",",$campos);
                    }
//                    echo "<pre>".print_r($sort,true)."</pre>";
                    $listado = $this->areas_rpyc->listado($start, $limit, $busqueda, $campos, $sort, $dir);
                    echo $listado;
                }
                else
                    echo -1; //No tiene permisos
                
	}
	
	public function modificar()
	{
            if ($this->permisos['Modificacion'])
            {
                $id_area=$this->input->post("id");
                $campos=json_decode($this->input->post("campo"));
                $valores=json_decode($this->input->post("valor"));
                $this->load->model('gestion_riesgo/gr_areas_rpyc_model','areas_rpyc',true);
                $area= $this->areas_rpyc->dameArea($id_area);
//                echo "<pre>".print_r($area,true)."</pre>";die();
                for($i=0;$i<count($campos);$i++)
                {

                    if($campos[$i]=="area")
                    {
                        if(!$this->areas_rpyc->check_area($area,$valores[$i]))
                        {
                            echo 10;
                            die();
                        }
                        $datos[$campos[$i]]=$valores[$i];
                    }
                    elseif($campos[$i]=="empresa")
                    {
                        if(!$this->areas_rpyc->check_empresa($area,$valores[$i]))
                        {
                            echo 11;
                            die();
                        }
                        $datos['id_empresa']=$valores[$i];
                    }
                    else
                    {
                        $datos[$campos[$i]]=$valores[$i];
                    }
                }
                $b=1;
                if(!$this->areas_rpyc->edit($id_area,$datos))
                {
                    $b=0;
                }
                echo $b;
            }
	}
	
//	public function insert()
//	{
//            
//	}
        public function empresas_combo()
	{
            $this->load->model('empresas_model','empresas',true);
            $var=$this->empresas->dameComboEmpresas();
            echo $var;	
	}
}
?>