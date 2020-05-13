<?php
class Tareas_obsoletas extends CI_Controller
{
    private $modulo=32;
    private $user;
    private $permisos;
    private $roles;
    private $origen;
    
        function __construct()
	{
            parent::__construct();
            header ("Expires: Thu, 31 Dic 1976 23:59:00 GMT"); //la pagina expira en una fecha pasada
            header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); //ultima actualizacion ahora cuando la cargamos
            header ("Cache-Control: no-cache, must-revalidate"); //no guardar en CACHE
            header ("Pragma: no-cache");
            $this->user=$this->session->userdata(USER_DATA_SESSION);
//           echo "<pre>".print_r(!($this->user['id'])>0,true)."</pre>";
//            if (!($this->user['id']>0)) 
//                redirect("admin/admin/index_js","location", 301);
            $this->load->model("permisos_model","permisos_model",true);
            $this->permisos= $this->permisos_model->checkIn($this->user['perfil_id'],$this->modulo);
            $this->load->model("gestion_riesgo/gr_roles_model","gr_roles",true);
            $this->roles= $this->gr_roles->checkIn($this->user['id']);
            $this->load->model("usuarios_model","usuarios",true);
            $this->origen= $this->usuarios->dameOrigen($this->user['id']);
	}
	
	public function index()
	{
            $modulo = $this->uri->segment(SEGMENTOS_DOM+2);
            if (!isset($modulo)) $modulo=0;
            $this->load->model('permisos_model','permisos_model',true);
            $variables['permiso'] = $this->permisos_model->checkIn($this->user['perfil_id'],$modulo);
//            $variables['permiso_permiso'] = $this->permisos_model->checkIn($this->user['perfil_id'],$this->modulo);
            $variables['btn'] = $this->user['id'];
//             echo "<pre>".print_r($variables,true)."</pre>";
            $this->load->view('gestion_riesgo/tareas_obsoletas/listado',$variables);
	}
	
	public function listado()
	{
		if ($this->permisos['Listar'])
                {
                    $id_usuario=$this->user['id'];
                    $listado=array();
                    $this->load->model('gestion_riesgo/gr_tareas_model','tareas',true);
                    $start = $this->input->post("start");
                    $limit = $this->input->post("limit");
                    $sort = $this->input->post("sort");
                    $dir = $this->input->post("dir");
                    if($this->input->post("filtros"))
                    {
                        $filtros=json_decode($this->input->post("filtros"));
                        foreach ($filtros as &$filtro)
                        {
                            if ($filtro=='Todas'||$filtro=='Todos'||$filtro=='-1')
                                $filtro="";
                        }
                        unset ($filtro);
//                        echo "<pre>".print_r($filtros,true)."</pre>";

                    }
                    else
                    {
                        $filtros="";

                    }
                    $busqueda  = ($this->input->post("query"))?$this->input->post("query"):"";
                    $campos="";
                    if ($this->input->post("fields"))
                    {
                        $campos = str_replace('"', "", $this->input->post("fields"));
                        $campos = str_replace('[', "", $campos);
                        $campos = str_replace(']', "", $campos);
                        $campos = explode(",",$campos);
    //                            echo "<pre>".print_r($campos,true)."</pre>";
                    }
                    $usuario=$this->_dameAreas($id_usuario);
                    $listado = $this->tareas->listadoObsoletas($usuario,$start, $limit, $filtros,$busqueda, $campos, $sort, $dir);
                    echo $listado;
                }
                else
                    echo -1; //No tiene permisos
                
	}
	public function historial()
	{
		if ($this->permisos['Listar'])
                {
                    $id_usuario=$this->user['id'];
                    $listado=array();
                    $this->load->model('gestion_riesgo/gr_historial_model','historial',true);
                    $id_tarea = $this->input->post("id");
                    $start = $this->input->post("start");
                    $limit = $this->input->post("tampagina");
//                    $sort = $this->input->post("sort");
//                    $dir = $this->input->post("dir");
//                    if($this->input->post("filtros"))
//                    {
//                        $filtros=json_decode($this->input->post("filtros"));
//                        foreach ($filtros as &$filtro)
//                        {
//                            if ($filtro=='Todas'||$filtro=='Todos'||$filtro=='-1')
//                                $filtro="";
//                        }
//                        unset ($filtro);
////                        echo "<pre>".print_r($filtros,true)."</pre>";
//
//                    }
//                    else
//                    {
//                        $filtros="";
//
//                    }
//                    $busqueda  = ($this->input->post("query"))?$this->input->post("query"):"";
//                    $campos="";
//                    if ($this->input->post("fields"))
//                    {
//                        $campos = str_replace('"', "", $this->input->post("fields"));
//                        $campos = str_replace('[', "", $campos);
//                        $campos = str_replace(']', "", $campos);
//                        $campos = explode(",",$campos);
//    //                            echo "<pre>".print_r($campos,true)."</pre>";
//                    }
//                    $usuario=$this->_dameAreas($id_usuario);
//                    $listado = $this->fcatedras->listado             ($start, $limit, $campos, $busqueda, $sort, $dir,$filtros);
                    $listado = $this->historial->listado($id_tarea,$start, $limit);
                    echo $listado;
                }
                else
                    echo -1; //No tiene permisos
                
	}
        public function filtro_herramientas()
        {
            $this->load->model("gestion_riesgo/gr_tipos_herramientas_model","gr_tipos_herramientas",true);
            echo $this->gr_tipos_herramientas->dameFiltroTareas();
        }
         public function _dameAreas($id_usuario)
        {
//            $id_usuario=$this->user['id'];
            $areas=array();
            $this->load->model('gestion_riesgo/gr_usuarios_model','gr_usuarios',true);
            $usuario=$this->gr_usuarios->dameUsuarioPorId($id_usuario);
            $areas['usuario']=$usuario;
            
            $id_area=$usuario['id_area'];
            $arrayAreas=array();
            $areasInferiores[]=$id_area;
            $this->load->model('gestion_riesgo/gr_areas_model','gr_areas',true);
            
            //Areas Inferiores: recorro el nro máximo de niveles posible para traer las áreas inferiores
            for($i=0;$i<10;$i++)
            {
                $as=$this->gr_areas->dameAreasInferiores($areasInferiores);                
                if (count($as)>1)
                {
                    foreach ($as as $area)
                    {
                        $arrayAreas[]=$area['id_area'];
                    }
                    $areasInferiores= array_unique(array_merge($areasInferiores,$arrayAreas));
                }
                    
            }
            $areas['areas_inferiores']=  str_replace("'","",implode(",",$areasInferiores));
            
            //Areas Superiores: vos subiendo por la herencia hasta no encontrar padre
            $areasSuperiores=array();
            $areasSuperiores[]=$id_area;
            while ($this->gr_areas->dameAreaSuperior(end($areasSuperiores))!="")
            {
                $areasSuperiores[]=$this->gr_areas->dameAreaSuperior(end($areasSuperiores));
            }
            $areas['areas_superiores']=  str_replace("'","",implode(",",$areasSuperiores));
            
            if(in_array(4, $areasSuperiores))
                $areas['gr']=1;
            else
                $areas['gr']=0;
                    
//            echo "<pre>".print_r($areas,true)."</pre>";
            
            return $areas;
            
        }
    public function historial_cambios()
    {
        if ($this->permisos['Listar'])
        {
            $id_usuario=$this->user['id'];
            $listado=array();
            $this->load->model('gestion_riesgo/gr_historial_model','historial',true);
            $id_tarea = $this->input->post("id");
            $start = $this->input->post("start");
            $limit = $this->input->post("tampagina");
            $listado = $this->historial->listado($id_tarea,$start, $limit);
            echo $listado;
        }
        else
            echo -1; //No tiene permisos

    }
    public function historial_acciones()
    {
        if ($this->permisos['Listar'])
        {
            $id_usuario=$this->user['id'];
            $listado=array();
            $this->load->model('gestion_riesgo/gr_historial_acciones_model','historial_acciones',true);
            $id_tarea = $this->input->post("id");
            $start = $this->input->post("start");
            $limit = $this->input->post("tampagina");
            $listado = $this->historial_acciones->listado($id_tarea,$start, $limit);
            echo $listado;
        }
        else
            echo -1; //No tiene permisos

    }   
}
?>