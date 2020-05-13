<?php
class Usuarios extends CI_Controller
{
    private $modulo=56;
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
//        $variables['editar'] = $this->_control_edicion();
        $variables['usuario'] = $this->user['id'];
//        echo "<pre>Origen".print_r($variables,true)."</pre>";
        $this->load->view('gestion_riesgo/usuarios/listado',$variables);
    }

    public function listado()
    {
        if ($this->permisos['Listar'])
        {
            $this->load->model('gestion_riesgo/gr_usuarios_model','gr_usuarios',true);
            $start = $this->input->post("start");
            $limit = $this->input->post("limit");
            $filtro = $this->input->post("query");
            $sort = $this->input->post("sort");
            $dir = $this->input->post("dir");
            $listado = $this->gr_usuarios->listado($start, $limit, $filtro, $sort, $dir);
            echo $listado;
        }
    }
}
?>