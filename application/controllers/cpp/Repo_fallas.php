<?php
class Repo_fallas extends CI_Controller
{
    private $modulo=51;
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
        $this->load->view('cpp/repo_fallas/listado',$variables);
    }

    public function listado()
    {
        if ($this->permisos['Listar'])
        {
            $this->load->model('cpp/cpp_eventos_model','eventos',true);
            $start = $this->input->post("start");
            $limit = $this->input->post("limit");
            $sort = $this->input->post("sort");
            $dir = $this->input->post("dir");
//            echo $busqueda;
//            echo "<pre>".print_r($campos,true)."</pre>";
            if($this->input->post("filtros"))
                $arrayFiltros=$this->generaArrayFiltros($this->input->post("filtros"));
            else
                $arrayFiltros="";
//            echo "<pre>".print_r($arrayFiltros,true)."</pre>";die();
            
            $listado = $this->eventos->listado_repo($start, $limit, $sort,$dir,$arrayFiltros);
            echo $listado;
        }
        else
            echo -1; //No tiene permisos
    }
    public function generaArrayFiltros($filtros)
    {
        $filtros=json_decode($filtros);
        foreach ($filtros as &$filtro)
        {
            if ($filtro=='Todas'||$filtro=='Todos'||$filtro=='-1')
                $filtro="";
        }
        unset ($filtro);
        if($filtros[4]!="")
            $filtros[4]=explode(",",$filtros[4]);
        if($filtros[5]!="")
        {
            $filtros[5]=explode(",",$filtros[5]);
            //busco los eventos que contienen esos equipos
            $this->load->model('cpp/cpp_evento_equipos_model','evento_equipos',true);
            $eventosPorFiltroEquipos=$this->evento_equipos->dameEventosPorEquipos($filtros[5]);
            $arrayEventosPorEquipos="";
            if (count($eventosPorFiltroEquipos)>0 && $eventosPorFiltroEquipos!="")
            {
                foreach ($eventosPorFiltroEquipos as $exe)
                {
                   $arrayEventosPorEquipos[]=$exe['id_evento'];
                }
                $filtros[5]=$arrayEventosPorEquipos;
            }
            else
                $filtros[5]=-1;
        }

        $arrayFiltros=array(
            'id_criticidad' =>$filtros[0],
            'id_estado'     =>$filtros[1],
            'f_ini'         =>$filtros[2],
            'f_fin'         =>$filtros[3],
            'sectores'      =>$filtros[4],
            'equipos'       =>$filtros[5]
        );
        
        return $arrayFiltros;
    }
    public function filtroCriticidad()
    {
        $start = $this->input->post("start");
        $limit = $this->input->post("limit");
        $this->load->model('cpp/cpp_criticidades_model','criticidades',true);
        $var=$this->criticidades->dameCombo($start,$limit);
        echo $var;
    }
    
    public function filtroEstado()
    {
        $start = $this->input->post("start");
        $limit = $this->input->post("limit");
        $this->load->model('cpp/cpp_estados_model','estados',true);
        $var=$this->estados->dameCombo($start,$limit);
        echo $var;
    }
     public function filtroSectores()
    {
        $start = $this->input->post("start");
        $limit = $this->input->post("limit");
        $busqueda = $this->input->post("query");
        $this->load->model('cpp/cpp_sectores_model','sectores',true);
        $var=$this->sectores->dameCombo($start,$limit,$busqueda);
        echo $var;	
    }
     public function filtroEquipos()
    {
        $start = $this->input->post("start");
        $limit = $this->input->post("limit");
        if($this->input->post("query")=="f_all")
            $busqueda = "";
        else    
            $busqueda = $this->input->post("query");
        $this->load->model('cpp/cpp_evento_equipos_model','equipos',true);
        $var=$this->equipos->dameComboEquipos($start,$limit,$busqueda);
        echo $var;	
    }
    public function genera_excel()
    {
        if ($this->permisos['Listar'])
        {
            $sort = $this->input->post("sort");
            $dir = $this->input->post("dir");
              if($this->input->post("filtros"))
                $arrayFiltros=$this->generaArrayFiltros($this->input->post("filtros"));
            else
                $arrayFiltros="";

            $this->load->model('cpp/cpp_eventos_model','eventos',true);
            $this->load->model('cpp/cpp_evento_consecuencias_model','consecuencias',true);
            $this->load->model('cpp/cpp_causas_model','causas',true);
            $this->load->model('gestion_riesgo/gr_tareas_model','tareas',true);
            $this->load->model('usuarios_model','usuarios',true);
    //         $this->load->library('excel');

            $eventos=$this->eventos->dameEventosRepoExcel($sort,$dir,$arrayFiltros);
            
            $datos_audit['id_modulo']=$this->modulo;
            $datos_audit['id_usuario']=$this->user['id'];
            $datos_audit['q_registros']=count($data);
            $params=array($sort,$dir,$arrayFiltros);
            $datos_audit['params']=json_encode($params);

            $this->load->model("auditoria_model","auditoria_model",true);
            $this->auditoria_model->guardarPedidoExcel($datos_audit);
            
            if ($eventos!=0)
            {
                $usuario=$this->usuarios->dameUsuario($this->user['id']);

                foreach ($eventos as $key=>&$value)
                {
                    $consecuencias=$this->consecuencias->dameConsecuenciasPorEventoParaExcel($value['id_evento']);
                    $value['consecuencias']=$consecuencias;
                    $causas=$this->causas->dameCausasPorEventoParaExcel($value['id_evento']);
                    if ($causas!=0)
                    {
                        foreach ($causas as $key2=>&$value2)
                        {
                            $tipo_herramienta=8;
                            $tareas=$this->tareas->dameTareasCPPPorCausa($value2['id_causa']);
                            $value2['tareas']=$tareas;
                        }
                    }
                    $value['causas']=$causas;
                }
        //        $var='id_evento';

                $datosexcel['titulo']='titulo';
                $datosexcel['usuario'] = $usuario['nomape']." - ".$usuario['puesto']." - ".$usuario['area'];
                $datosexcel['filtros']=($arrayFiltros!="")?$this->input->post("filtros"):"ninguno";
                $datosexcel['datos']=$eventos;
    //            echo "<pre>".print_r($datosexcel,true)."</pre>";die();

                $this->load->view('cpp/repo_fallas/excel',$datosexcel);
            }
            else {
                echo -1;
            }
        }
        else
            echo -1; //No tiene permisos
    }
    public function genera_excel_demo()
    {
        if ($this->permisos['Listar'])
        {
            $sort = "";
            $dir = "";
            $filtrosPost='["Todas","Todas","","","",""]';
            if(0==0)
            {
                $filtros=json_decode($filtrosPost);
                foreach ($filtros as &$filtro)
                {
                    if ($filtro=='Todas'||$filtro=='Todos'||$filtro=='-1')
                        $filtro="";
                }
                unset ($filtro);

                $arrayFiltros=array(
                    'id_criticidad' =>$filtros[0],
                    'id_estado'     =>$filtros[1],
                    'f_ini'         =>$filtros[2],
                    'f_fin'         =>$filtros[3],
                    'sectores'      =>(count(explode(",",$filtros[4])==0))?"":explode(",",$filtros[4]),
                    'equipos'      =>(count(explode(",",$filtros[5])==0))?"":explode(",",$filtros[5])
                );
            }
            else
            {
                $arrayFiltros="";
            }

            $this->load->model('cpp/cpp_eventos_model','eventos',true);
            $this->load->model('cpp/cpp_evento_consecuencias_model','consecuencias',true);
            $this->load->model('cpp/cpp_causas_model','causas',true);
            $this->load->model('gestion_riesgo/gr_tareas_model','tareas',true);
            $this->load->model('usuarios_model','usuarios',true);
    //         $this->load->library('excel');

            $eventos=$this->eventos->dameEventosRepoExcel($sort,$dir,$arrayFiltros);
            
            if ($eventos!=0)
            {
                $usuario=$this->usuarios->dameUsuario($this->user['id']);

                foreach ($eventos as $key=>&$value)
                {
                    $consecuencias=$this->consecuencias->dameConsecuenciasPorEventoParaExcel($value['id_evento']);
                    $value['consecuencias']=$consecuencias;
                    $causas=$this->causas->dameCausasPorEventoParaExcel($value['id_evento']);
                    if ($causas!=0)
                    {
                        foreach ($causas as $key2=>&$value2)
                        {
                            $tipo_herramienta=8;
                            $tareas=$this->tareas->dameTareasCPPPorCausa($value2['id_causa']);
                            $value2['tareas']=$tareas;
                        }
                    }
                    $value['causas']=$causas;
                }
        //        $var='id_evento';

                $datosexcel['titulo']='titulo';
                $datosexcel['usuario'] = $usuario['nomape']." - ".$usuario['puesto']." - ".$usuario['area'];
                $datosexcel['filtros']=($arrayFiltros!="")?$filtrosPost:"ninguno";
                $datosexcel['datos']=$eventos;
//                echo "<pre>".print_r($datosexcel,true)."</pre>";die();

                $this->load->view('cpp/repo_fallas/excel',$datosexcel);
            }
            else {
                echo -2;
            }
        }
        else
            echo -1; //No tiene permisos
    }
}
?>