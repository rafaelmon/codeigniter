<?php
class Participantes extends CI_Controller
{
    private $modulo=54;
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
//            echo "<pre>Origen".print_r($this,true)."</pre>";
    }
    
    public function index()
    {
        $modulo = $this->uri->segment(SEGMENTOS_DOM+2);
        if (!isset($modulo)) $modulo=0;
        $this->load->model('permisos_model','permisos_model',true);
        $variables['permiso'] = $this->permisos_model->checkIn($this->user['perfil_id'],$modulo);
//        $variables['editar'] = $this->_control_edicion();
        $variables['usuario'] = $this->user['id'];
//        echo "<pre>Origen".print_r($variables,true)."</pre>";
        $this->load->view('cap/participantes/listado',$variables);
    }
    
    public function listadoParticipantes()
    {
        if ($this->permisos['Listar'])
        {
            $start              = $this->input->post("start");
            $limit              = $this->input->post("limit");
            $sort               = $this->input->post("sort");
            $dir                = $this->input->post("dir");
            
            $campos = "";
            if ($this->input->post("fields"))
            {
                $campos = str_replace('"', "", $this->input->post("fields"));
                $campos = str_replace('[', "", $campos);
                $campos = str_replace(']', "", $campos);
                $campos = explode(",",$campos);
            }
            $busqueda  = ($this->input->post("query"))?$this->input->post("query"):"";
            
            $this->load->model('cap/cap_participantes_model','cap_participantes',true);
            
            $listado = $this->cap_participantes->listadoParticipantes($start, $limit, $busqueda, $campos,$sort, $dir);
            echo $listado;
        }
    }
    
     public function listadoCapacitaciones()
    {
        if ($this->permisos['Listar'])
        {
            $start              = $this->input->post("start");
            $limit              = $this->input->post("limit");
            $sort               = $this->input->post("sort");
            $dir                = $this->input->post("dir");
            $id_persona         = $this->input->post("id_persona");
            
            $campos = "";
            if ($this->input->post("fields"))
            {
                $campos = str_replace('"', "", $this->input->post("fields"));
                $campos = str_replace('[', "", $campos);
                $campos = str_replace(']', "", $campos);
                $campos = explode(",",$campos);
            }
            $busqueda  = ($this->input->post("query"))?$this->input->post("query"):"";
            
            $this->load->model('cap/cap_participantes_model','cap_participantes',true);
            
            $listado = $this->cap_participantes->listadoCapacitacionesPorParticipante($start, $limit, $busqueda, $campos,$sort, $dir, $id_persona);
            echo $listado;
        }
            
    }
}
?>