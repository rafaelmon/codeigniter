<?php
class Tops_apr extends CI_Controller
{
    private $modulo=57;
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

//            $this->load->model('ddp/ddp_tops_model','ddp_tops',true);
//            $id_periodo=1;
//            $this->user['id_top']=$this->ddp_tops->dameIdTop($this->user['id'],$id_periodo);
//            echo "<pre>".print_r($this->user,true)."</pre>";
    }

    public function index()
    {
        $modulo = $this->uri->segment(SEGMENTOS_DOM+2);
        if (!isset($modulo)) $modulo=0;
        $this->load->model('permisos_model','permisos_model',true);
        $variables['permiso'] = $this->permisos_model->checkIn($this->user['perfil_id'],$modulo);
//             $this->load->model('ddp/ddp_tops_model','ddp_tops',true);
//             echo "<pre>".print_r($variables,true)."</pre>";
        $this->load->model('ddp/ddp_periodos_model','ddp_periodos',true);
        $variables['periodo']=$this->ddp_periodos->damePeriodoActivo();

        $this->load->view('ddp/tops_apr/listado',$variables);
    }
    public function top_usuario()
    {
        $modulo = $this->uri->segment(SEGMENTOS_DOM+2);
        $id_top = $this->input->post("id");
        $this->load->model('ddp/ddp_tops_model','ddp_tops',true);
        $usuario=$this->ddp_tops->dameUsuarioTop($id_top);

        if (!isset($modulo)) $modulo=0;
        $this->load->model('permisos_model','permisos_model',true);
        $variables['permiso'] = $this->permisos_model->checkIn($this->user['perfil_id'],$modulo);
//            $top_activa=$this->ddp_tops->topActiva($this->user['id']);

        $top=$this->ddp_tops->dameTopActivaDatosPanelParaSupervisor($id_top);
        $variables['top'] = $top;

        $variables['id_top'] = $id_top;
        $variables['usuario'] = $usuario['nomape'];
//             $this->load->model('ddp/ddp_tops_model','ddp_tops',true);
//             echo "<pre>".print_r($variables,true)."</pre>";
        $this->load->view('ddp/tops_apr/top/listado',$variables);
    }

    public function listado()
    {
            if ($this->permisos['Listar'])
            {
                $id_usuario=$this->user['id'];
                $id_periodo = ($this->input->post("filtro_id_periodo"))?$this->input->post("filtro_id_periodo"):-1;

                if($id_periodo!=-1)
                {
                    $this->load->model('ddp/ddp_periodos_model','ddp_periodos',true);
                    $periodo=$this->ddp_periodos->damePeriodo($id_periodo);
                }
                else
                    $periodo=-1;

                $listado=array();
                $this->load->model('ddp/ddp_tops_model','ddp_tops',true);
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
                $listado = $this->ddp_tops->listado_aprobador($start, $limit, $filtros,$busqueda, $campos, $sort, $dir,$id_usuario,$periodo);
                echo $listado;
            }
            else
                echo -1; //No tiene permisos

    }
    public function listado_historial()
    {
            if ($this->permisos['Listar'])
            {
//                    $usuario['id']=$this->user['id'];
                $id_objetivo=$this->input->post("id_obj");
                $listado=array();
                $this->load->model('ddp/ddp_objetivos_historial1_model','ddp_historial',true);
                $sort = $this->input->post("sort");
                $dir = $this->input->post("dir");
                $listado = $this->ddp_historial->listado($id_objetivo);
                echo $listado;
            }
            else
                echo -1; //No tiene permisos

    }
    public function listado_objetivos()
    {
            if ($this->permisos['Listar'])
            {
//                    $id_usuario=$this->input->post("id_usuario");
//                    $id_periodo=$this->input->post("id_periodo");
//                    $id_top=$this->ddp_tops->dameIdTop($id_usuario,$id_periodo);
                $this->load->model('ddp/ddp_tops_model','ddp_tops',true);
                $id_top=$this->input->post("top");

                if ($id_top=="" || $id_top==NULL)
                    exit ('({"total":"0","rows":""})');
                $id_dimension=$this->input->post("id_dimension");
                if ($id_dimension>=0)
                {
//                        $datos['id_usuario']=$id_usuario;
                    $datos['id_dimension']=$id_dimension;
                    $datos['id_top']=$id_top;
                    $listado=array();
                    $this->load->model('ddp/ddp_objetivos_model','ddp_obj',true);
                    $start = $this->input->post("start");
                    $limit = $this->input->post("limit");
                    $filtros="";
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
                    $sort = $this->input->post("sort");
                    $dir = $this->input->post("dir");


                    $listado = $this->ddp_obj->listado_usuario($datos,$start, $limit, $filtros, $busqueda, $campos,$sort="", $dir="");
                    echo $listado;
                }
                else
                    echo '({"total":"0","rows":""})';

            }
            else
                echo -1; //No tiene permisos

    }
    public function periodos_combo()
    {
        $this->load->model('ddp/ddp_periodos_model','ddp_periodos',true);
        $var=$this->ddp_periodos->dameComboPeriodos();
        echo $var;	
    }
    public function dr_combo()
    {
        $id_usuario=$this->user['id'];
        $id_periodo=$this->input->post("id_periodo");
        $this->load->model('usuarios_model','usuarios',true);
        $var=$this->usuarios->dameDrCombo($id_usuario);
        echo $var;	
    }

    public function datos_obj ()
    {
        $id_usuario=$this->user['id'];
        $id_objetivo=$this->input->post("id");
        //verifico que el objetivo corresponda al usuario o su superioor
        $this->load->model('ddp/ddp_objetivos_model','ddp_objetivos',true);
        $usuarios=$this->ddp_objetivos->dameUsuarios($id_objetivo);
        $objetivo=$this->ddp_objetivos->dameObjetivo($id_objetivo);
//            echo "<pre>" . print_r($usuarios, true) . "</pre>";
//            echo "<pre>" . print_r($objetivo, true) . "</pre>";
        if ($id_usuario!=$usuarios['id_supervisor'])
             exit( "({success: false, error :'Usted no tiene permisos para realizar esta acción...'})");
        else
        {
            $objetivo_form=$this->ddp_objetivos->dameObjetivoParaFormEditSupTop($id_objetivo);
            echo $objetivo_form;	
        }
    }
               
        
    function aprobar()
    {
        if ($this->permisos['Listar'])
        {
            $id_usuario=$this->user['id'];
            $id_top=$this->input->post("id");
            $id_accion=13;
//                echo "<pre>" . print_r($id_top, true) . "</pre>";die();
            $this->load->model('ddp/ddp_tops_model','ddp_tops',true);
            $top=$this->ddp_tops->dameTopPorId($id_top);
            if ($top['id_estado']==2)
            {
                $dato['id_estado']=3;
                $aprobar=$this->ddp_tops->update($id_top,$dato);
                if ($aprobar)
                {
                    $this->_auditoria($id_accion,$id_usuario,$id_top);
                    echo "({success: true, error :''})";

                }
                else
                 echo "({success: false, error :'Error actualizando estado TOP'})";
            } else 
                echo "({success: false, error :'Error... Estado TOP incorrecto'})";
        } else 
            echo "({success: false, error :'El usuario no tiene permisos para realizar la acción solicitada'})";
    }
    function rechazar()
    {
        if ($this->permisos['Listar'])
        {
            $id_usuario=$this->user['id'];
            $id_top=$this->input->post("id");
            $id_accion=14;
//                echo "<pre>" . print_r($id_top, true) . "</pre>";die();
            $this->load->model('ddp/ddp_tops_model','ddp_tops',true);
            $top=$this->ddp_tops->dameTopPorId($id_top);
            if ($top['id_estado']==2)
            {
                $dato['id_estado']=1;
                $rechazar=$this->ddp_tops->update($id_top,$dato);
                if ($rechazar)
                {
                    $this->load->model('ddp/ddp_objetivos_model','ddp_objetivos',true);
                    $rechazar_obj=$this->ddp_objetivos->rechazarObjsTop($id_top);
                    if ($rechazar_obj)
                    {
                        $this->_auditoria($id_accion,$id_usuario,$id_top);
                        echo "({success: true, error :''})";
                    }
                    else
                        echo "({success: false, error :'Error actualizando estado objetivos'})";
                }
                else
                 echo "({success: false, error :'Error actualizando estado TOP'})";
            } else 
                echo "({success: false, error :'Error... Estado TOP incorrecto'})";
        } else 
            echo "({success: false, error :'El usuario no tiene permisos para realizar la acción solicitada'})";
    }
       
    function gestion($id_usuario,$id_objetivo,$id_accion,$id_estado_top=1)
    {
        //busco el objetivo
        $this->load->model('ddp/ddp_objetivos_model','ddp_obj',true);
        $objetivo=$this->ddp_obj->dameObjetivo($id_objetivo);
//            echo "<pre>" . print_r($objetivo, true) . "</pre>";
        if ($objetivo==0)
            exit( "({success: false, error :'Error al buscar el objetivo...'})");
        else
        {
            //verifico que el usuario sea o el creador o el supervisor de este objetivo
             if ($objetivo['id_usuario_alta']!=$id_usuario && $objetivo['id_supervisor']!=$id_usuario)
            {
                exit( "({success: false, error :'El usuario no tiene relación con el objetivo'})");
            }
            else
            {
                //verifico que la accion solicitada corresponda con el usuario
                $this->load->model('ddp/ddp_mef_model','ddp_mef',true);
                $estadoAccion=$this->ddp_mef->dameEstadoAccion($objetivo['id_estado'],$id_accion,$id_estado_top);
//                    echo "<pre>" . print_r($estadoAccion, true) . "</pre>";
                //si estadoAccion[actor]==1 => el usuario debe ser el editor sino debe ser el supervisor
                if (($estadoAccion['actor']==1 && $objetivo['id_usuario_alta']==$id_usuario)||($estadoAccion['actor']==2 &&$objetivo['id_supervisor']==$id_usuario))
                {
                    //usuario correcto
                    //cambio el objetivo al siguiente estado y guardo en auditoria
                    $dato['id_estado']=$estadoAccion['id_nuevo_estado'];
                    $cambiarEstado=$this->ddp_obj->update($id_objetivo,$dato);
                    if ($cambiarEstado)
                    {
                        return "({success: true, error :''})";
                    }
                    else
                        return "({success: false, error :'Error al cambiar de estado el objetivo'})";

                }
                else
                {
                    //usuario incorrecto
                    return "({success: false, error :'El usuario no tiene permisos para realizar la acción solicitada'})";
                }
            }
        }
    }
    public function listado_auditoria()
    {
        if ($this->permisos['Listar'])
        {
            $id_top = $this->input->post("id_top");
            $listado=array();

            $this->load->model('ddp/ddp_tops_auditoria_model','ddp_tops_auditoria',true);

            $sort = $this->input->post("sort");
            $dir = $this->input->post("dir");
            $start = $this->input->post("start");
            $limit = $this->input->post("limit");

            $listado = $this->ddp_tops_auditoria->listado($start, $limit, $sort, $dir,$id_top);
            echo $listado;
        }
        else
            echo -1; //No tiene permisos
    }
    public function _auditoria ($id_accion,$id_usuario,$id_top)
    {
        $aud = array();
        $aud['id_accion']       = $id_accion;
        $aud['id_usuario_alta'] = $id_usuario;
        $aud['id_top']          = $id_top;
        $aud['observacion']     = "";
        
        $this->load->model('ddp/ddp_tops_auditoria_model','ddp_tops_auditoria',true);
        $insert_aud = $this->ddp_tops_auditoria->insert($aud);
        
        if ($insert_aud)
            return true;
        else
            return false;
        
    }
    
       
}
?>