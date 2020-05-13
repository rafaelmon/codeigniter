<?php
class Mitop extends CI_Controller
{
    private $modulo=29;
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
//**********BORRAR ***************************************************************************************************
//            $this->user['id']=17;
            
            if (!($this->user['id']>0)) 
                redirect("admin/admin/index_js","location", 301);
            $this->load->model("permisos_model","permisos_model",true);
            $this->permisos= $this->permisos_model->checkIn($this->user['perfil_id'],$this->modulo);
            
            $this->load->model('ddp/ddp_tops_model','ddp_tops',true);
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
//            $variables['permiso_permiso'] = $this->permisos_model->checkIn($this->user['perfil_id'],$this->modulo);
             $this->load->model('ddp/ddp_tops_model','ddp_tops',true);
            $top_activa=$this->ddp_tops->topActiva($this->user['id']);
            //$top=$this->ddp_tops->dameTopActivaDatosPanel($this->user['id']);
            $variables['btn_nuevaTop'] = $top_activa;
            //$variables['top'] = $top;
            $this->load->model('gestion_riesgo/gr_usuarios_model','gr_usuarios',true);
            $id_supervisor=$this->gr_usuarios->dameIdUsuarioSuperior($this->user['id']);
            $supervisor=$this->gr_usuarios->dameUsuarioPorId($id_supervisor);
            $variables['supervisor'] = $supervisor['persona'];
            $variables['id_supervisor'] = $id_supervisor;
            $id_aprobador=$this->gr_usuarios->dameIdUsuarioSuperior($id_supervisor);
            $variables['id_aprobador'] = $id_aprobador;
            //$supervisor=$this->gr_usuarios->dameUsuarioPorId($id_aprobador);
//             echo "<pre>".print_r($variables,true)."</pre>";
            $this->load->view('ddp/mistops/listadoMisTops',$variables);
	}
	
	public function listado()
	{
		if ($this->permisos['Listar'])
                {
                    $id_usuario=$this->user['id'];
                    $listado=array();
                    $this->load->model('ddp/ddp_objetivos_empresas_model','ddp_objetivos_empresa',true);
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
                    $listado = $this->ddp_objetivos_empresa->listado($start, $limit, $filtros,$busqueda, $campos, $sort, $dir);
                    echo $listado;
                }
                else
                    echo -1; //No tiene permisos
                
	}
	public function listado_dimensiones()
	{
		if ($this->permisos['Listar'])
                {
                    $usuario['id']=$this->user['id'];
                    $usuario['id_top']=$this->user['id_top'];
                    $listado=array();
                    $this->load->model('ddp/ddp_dimensiones_model','ddp_dimensiones',true);
                    $sort = $this->input->post("sort");
                    $dir = $this->input->post("dir");
                    $listado = $this->ddp_dimensiones->listado_top($usuario,$sort, $dir);
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
                    $datos['id_usuario']=$this->user['id'];
                    $datos['id_dimension']=$this->input->post("id_dimension");
                $datos['id_top']= $this->input->post('id_top');
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
                    echo -1; //No tiene permisos
                
	}
        public function periodos_combo()
	{
            $this->load->model('ddp/ddp_periodos_model','ddp_periodos',true);
            $var=$this->ddp_periodos->dameComboPeriodos();
            echo $var;	
	}
        public function fd_combo()
	{
            $id_dimension=$this->input->post("id_dimension");
            $this->load->model('ddp/ddp_fuentes_model','ddp_fuentes',true);
            $var=$this->ddp_fuentes->dameComboFD($id_dimension);
            echo $var;	
	}
       
        public function datos_obj ()
	{
            $id_usuario=$this->user['id'];
            $id_objetivo=$this->input->post("id");
            //verifico que el objetivo corresponda al usuario o su superioor
            $this->load->model('ddp/ddp_objetivos_model','ddp_objetivos',true);
            $usuarios=$this->ddp_objetivos->dameUsuarios($id_objetivo);
//            echo "<pre>" . print_r($usuarios, true) . "</pre>";
            if ($id_usuario!=$usuarios['id_usuario'] && $id_usuario!=$usuarios['id_supervisor'])
                 exit( "({success: false, error :'Usted no tiene permisos para realizar esta acción...'})");
            else
            {
                $objetivo_form=$this->ddp_objetivos->dameObjetivoParaForm($id_objetivo);
                echo $objetivo_form;	
            }
	}
        
        public function nueva_top()
	{
            if ($this->permisos['Alta'])
            {
                $id_usuario     =$this->user['id'];
                $id_supervisor  = $this->input->post("id_supervisor");
                $id_aprobador   = $this->input->post("id_aprobador");
                $id_periodo     = $this->input->post("id_periodo");

                $datos['id_usuario']    = $id_usuario;
                $datos['id_periodo']    = $id_periodo;
                $datos['id_estado']     = 1;
                $id_accion              = 10;

                //busco el usuario supervisor
                $this->load->model('gestion_riesgo/gr_usuarios_model','gr_usuarios',true);
                //$id_supervisor=$this->gr_usuarios->dameIdUsuarioSuperior($datos['id_usuario']);
                $id_puesto=$this->gr_usuarios->dameUsuarioIdPuesto($datos['id_usuario']);

                $datos['id_puesto']     = $id_puesto;
                $datos['id_supervisor'] = $id_supervisor;
                $datos['id_aprobador']  = $id_aprobador;

                $datos['id_top_superior']=NULL;
                //creo la nueva TOP
                if ($datos['id_supervisor'] == 0)
                    exit( "({success: false, error :'No tiene superior definido o este se encuentra deshabilitado del sistema.'})");

                if ($datos['id_aprobador'] == 0)
                    exit( "({success: false, error :'No tiene aprobador definido o este se encuentra deshabilitado del sistema.'})");

                if ($datos['id_aprobador'] == $datos['id_supervisor'])
                    exit( "({success: false, error :'El supervisor y el aprobador no pueden ser la misma persona.'})");

                $this->load->model('ddp/ddp_tops_model','ddp_tops',true);

                $existe = $this->ddp_tops->dameTopPorPeriodoYUsuario($id_usuario,$id_periodo);
                if ($existe == 1)
                    exit( "({success: 'falso', error :'Ya existe una TOP generada para el periodo seleccionado.'})");

                $top = $this->ddp_tops->insert($datos);

                if ($top==0)
                    exit( "({success: false, error :'Error al crear su TOP...'})");
                else
                {
                //agrego los objetivos de la dimension Principios
                    $this->load->model('ddp/ddp_subdimensiones_model','ddp_subdimensiones',true);
                    $principios=$this->ddp_subdimensiones->damePricipios();
//                         echo "<pre>" . print_r($principios, true) . "</pre>";
                    $q_principios=count($principios);

                    $datos_objs['id_top']=$top;
                    $datos_objs['id_dimension']=3;//id_dimension3 => Organizacional
                    $datos_objs['id_usuario_alta']=$this->user['id'];
    //                        $datos_objs['obj']='';
                    $datos_objs['indicador']='';
                    $datos_objs['fd']='';
                    $datos_objs['valor_ref']='';
                    $datos_objs['id_estado']=14; //comienzan en el estado de aprobados
    //
                    $this->load->model('ddp/ddp_objetivos_model','ddp_obj',true);
                    $q_objs=0;
                    foreach ($principios as $principio)
                    {
                        $datos_objs['obj']=$principio['subdimension'];
                        $datos_objs['peso']=$principio['peso'];
    //                        echo "<pre>" . print_r($datos_objs, true) . "</pre>";continue;
                        $insert=$this->ddp_obj->insert($datos_objs);
                        $q_objs+=$insert;
                    }
                    
                    $aud = $this->_auditoria($id_accion, $id_usuario, $top);
                    if ($aud)
                        echo "({success: true, error :null})";
                    else
                       echo  "({success: false, error :'Error al insertar auditoria. Comuniquese con el administrador del sistema.'})";
                        
                    
                }
            }
            else
                echo -1; //No tiene permisos
                
	}
        
        public function insert_obj()
	{
            if ($this->permisos['Alta'])
            {
                $id_top = $this->input->post("id_top");
                $id_usuario=$this->user['id'];
                $id_objetivo=$this->input->post("id");
                $this->load->model('ddp/ddp_objetivos_model','ddp_obj',true);
                
                if ($this->input->post('id_dimension')==6)
                    exit( "({success: false, error :'Objetivo no editable...'})");
                
                //edito objetivo
                if($id_objetivo!=0)
                {
                    $campos_requeridos=array('obj','fecha_evaluacion');
                    foreach ($campos_requeridos as $campo)
                    {
                        if ($this->input->post($campo)=="")
                            exit( "({success: false, error :'Faltan campos requeridos...'})");
                    }
                    
                    $id_accion=2;
                    //Modifico Objetivo y guardo en el historial
                    $objetivo=$this->ddp_obj->dameObjetivoEditado($id_objetivo);
                    $objetivoCompleto=$this->ddp_obj->dameObjetivo($id_objetivo);
//                    echo "<pre>" . print_r($objetivo, true) . "</pre>";
//                    
//                    
                    //verifico que la TOP se encuentre en estado 1
                    $top=$this->ddp_tops->dameTopPorId($objetivoCompleto['id_top']);
                    if ($top['id_estado']!=1)
                        exit( "({success: false, error :'Hay una inconsistencia con el estado de su TOP, por favor comuníquese con el administrador...'})");
                    else 
                    {
                        
                        //verifico que la accion y el estado del objetivo concuerde con la mef con actor=1: Editor
                        if (!$this->verificar_editor_mef($id_objetivo,$id_accion))
                            exit( "({success: false, error :'No se puede modificar un objetivo delegado al supervisor...'})");

                        else
                        {
                            if ( $objetivoCompleto['id_usuario_alta']!=$id_usuario)
                                exit( "({success: false, error :'Usuario sin permisos...'})");
                            if ( $objetivoCompleto['id_usuario_alta']!=$id_usuario)
                                exit( "({success: false, error :'Usuario sin permisos...'})");

                            //$objetivo['peso']=$this->formatFloat($objetivo['peso']);

                            //$datos['oe']=$this->input->post("obj_e");
                            $datos['obj']=$this->input->post("obj");
                            $datos['indicador']=$this->input->post("indicador");
                            $datos['fd']=$this->input->post("fd");
                            $datos['valor_ref']=$this->input->post("valor_ref");
                            //$datos['peso']=  20;
                            $datos['fecha_evaluacion']= $this->input->post("fecha_evaluacion");
                            $datos['fecha_evaluacion']= substr($datos['fecha_evaluacion'], 0, 10);
                            $datos['fecha_evaluacion']= date("d/m/Y",  strtotime($datos['fecha_evaluacion']));
//                            echo "<pre>" . print_r($datos, true) . "</pre>";
//                            echo "<pre>" . print_r($objetivo, true) . "</pre>";

                            $diff=  array_diff($datos, $objetivo);
//                            echo "<pre>" . print_r($diff, true) . "</pre>";die();

                            if (count($diff)>0 )
                            {
                                if(array_key_exists("fecha_evaluacion", $diff))
                                    $diff['fecha_evaluacion']   = $this->input->post("fecha_evaluacion");
                                $datos['fecha_evaluacion']  = $this->input->post("fecha_evaluacion");
                                $ok=$this->ddp_obj->update($id_objetivo, $datos);
                                $datos_historial=$diff;
                                $datos_historial['id_editor']=$id_usuario;
                                $datos_historial['id_objetivo']=$id_objetivo;
                                $this->load->model('ddp/ddp_objetivos_historial1_model','ddp_historial',true);
                                $insert=$this->ddp_historial->insert($datos_historial);
                                $gestion=$this->gestion($id_usuario,$id_objetivo,$id_accion);
                                $ok=1;
                            }
                            else
                                $ok=2;
                        }
                    }
                }
                //Insert nuevo objetivo
                else
                {
                    $campos_requeridos=array('id_dimension','obj','fecha_evaluacion');
                    foreach ($campos_requeridos as $campo)
                    {
                        if ($this->input->post($campo)=="")
                            exit( "({success: false, error :'Faltan campos requeridos...'})");
                    }
                    
                    $datos['id_dimension']=$this->input->post("id_dimension");
                    $datos['obj']=$this->input->post("obj");
                    $datos['id_top']=$id_top;
                    $datos['id_usuario_alta']=$id_usuario;
                    $datos['indicador']=$this->input->post("indicador");
                    $datos['fd']=$this->input->post("fd");
                    $datos['valor_ref']=$this->input->post("valor_ref");
//                    $datos['peso']=  $this->formatFloat($this->input->post("peso"));
                    $datos['fecha_evaluacion']=$this->input->post("fecha_evaluacion");
//                    echo "<pre>" . print_r($datos, true) . "</pre>";//die();
                    $ok = $this->ddp_obj->insert($datos);
                    if ($ok > 0)
                    {
                        //aca
                        $id_accion = 15;
                        $alta_aud = $this->_auditoria($id_accion, $id_usuario, $id_top, $id_objetivo);
                        if($alta_aud)
                            $ok = 1;
                        else
                            $ok = 3;
                    }
                }
                echo $ok;
            }
            else
                echo -1; //No tiene permisos
                
	}
         function formatFloat($num)
        {
            $num=floatval($num);
            $num=  round($num,2);
            $num=number_format($num,2,'.','');
            
            return $num;
        }
        public function _modificar_obj()
        {
            if ($this->permisos['Modificacion'])
            {
                $dato=array();
                $id_usuario=$this->user['id'];
                $id_accion=2;
                
                $datos['id_usuario_alta']=$id_usuario;
                //verifo que el usuario corresponsa con el OP que qquiere modificar
                
                
                $id_obj=$this->input->post("id");
                //verifico que el objetivo exista y corresponda con el editor
                $this->load->model('ddp/ddp_objetivos_model','ddp_obj',true);
                $objetivo=$this->ddp_obj->dameObjetivo($id_obj);
//                echo "<pre>" . print_r($objetivo, true) . "</pre>";
                
                //verifico que la dimension del objetivo no sea "Principios"
                if ($objetivo['id_dimension']==6)
                    exit( "({success: false, error :'Objetivo no editable...'})");
                if ($objetivo['id_usuario_alta']==$id_usuario)
                {
                    $campo=$this->input->post("campo");
                    $valor=$this->input->post("valor");
                    
                    $datos_historial['oe']=NULL;
                    $datos_historial['op']=NULL;
                    $datos_historial['indicador']=NULL;
                    $datos_historial['fd']=NULL;
                    $datos_historial['valor_ref']=NULL;
                    $datos_historial['peso']=NULL;

                    switch ($campo)
                    {
                        case 'op':
                            $dato['op']=$valor;
                            $datos_historial['op']=$valor;
                            break;
                        case 'oe':
                            $dato['oe']=$valor;
                            $datos_historial['oe']=$valor;
                            break;
                        case 'indicador':
                            $dato['indicador']=$valor;
                            $datos_historial['indicador']=$valor;
                            break;
                        case 'fd':
                            $dato['fd']=$valor;
                            $datos_historial['fd']=$valor;
                            break;
                        case 'valor_ref':
                            $dato['valor_ref']=$valor;
                            $datos_historial['valor_ref']=$valor;
                            break;
                        case 'peso':
                            $dato['peso']=$this->formatFloat($valor);
                            $datos_historial['peso']=$this->formatFloat($valor);
                            break;
                    }

                    //guardo edision en el historial
                    $datos_historial['id_editor']=$id_usuario;
                    $datos_historial['id_objetivo']=$id_obj;
//                    echo "<pre>" . print_r($datos_historial, true) . "</pre>";die();
                    $this->load->model('ddp/ddp_objetivos_historial1_model','ddp_historial',true);
                    $insert=$this->ddp_historial->insert($datos_historial);
                    
                    if($insert)
                    {
                        $update=$this->ddp_obj->update($id_obj, $dato);
                        $gestion=$this->gestion($id_usuario,$id_obj,$id_accion);
                        echo $update;
                    }
                    else
                        echo 0;

                }
                else
                     echo  "({success: false, error :Usuario sin permisos para editar el documento...'})";
                
            }
            else
                echo -1; //No tiene permisos
        }
        function eval1()
        {
             if ($this->permisos['Modificacion'])
            {
                $id_obj=$this->input->post("id");
                $real=$this->input->post("real1");
                $id_usuario=$this->user['id'];
                $id_accion=6;
                $id_estado_top=2;
                $this->load->model('ddp/ddp_tops_model','ddp_tops',true);
                $top=  $this->ddp_tops->dameTopPorObjetivo($id_obj);
                
                $dato['real1']=$real;

                //guardo edision en el historial
                $datos_historial['id_editor']=$id_usuario;
                $datos_historial['id_estado_top']=$top['id_estado'];
                $datos_historial['id_objetivo']=$id_obj;
                $datos_historial['real1']=$dato['real1'];
//                    echo "<pre>" . print_r($datos_historial, true) . "</pre>";die();
                $this->load->model('ddp/ddp_objetivos_historial1_model','ddp_historial',true);
                $insert=$this->ddp_historial->insert($datos_historial);

                if($insert)
                {
                    $this->load->model('ddp/ddp_objetivos_model','ddp_obj',true);
                    $update=$this->ddp_obj->update($id_obj, $dato);
                    if ($update)
                    {
                        $gestion=$this->gestion($id_usuario,$id_obj,$id_accion,$id_estado_top);
                    }
                    else
                        $gestion="({success: false, error :Error al actualizar el valor definido...'})";
                }
                else
                    $gestion="({success: false, error :Error al insertar historial...'})";
                
            }
            else
                $gestion="({success: false, error :Usuario sin permisos para editar el documento...'})";
            echo $gestion;
        }
//        public function _modificar_obj_1raEv($id_obj,$real)
//        {
//            if ($this->permisos['Modificacion'])
//            {
//                $dato=array();
//                $id_usuario=$this->user['id'];
//                $id_accion=2;
//                
//                //verifico que el objetivo exista y corresponda con el editor
//                $this->load->model('ddp/ddp_objetivos_model','ddp_obj',true);
//                $objetivo=$this->ddp_obj->dameObjetivo($id_obj);
//                $this->load->model('ddp/ddp_tops_model','ddp_tops',true);
//                $top=  $this->ddp_tops->dameTopPorObjetivo($id_obj);
////                echo "<pre>" . print_r($objetivo, true) . "</pre>";
//                
//                //verifo que el usuario corresponsa con el Obj que quiere modificar
//                if ($objetivo['id_usuario_alta']==$id_usuario || $objetivo['id_supervisor']==$id_usuario)
//                {
//                    $dato['real1']=$real;
//                    $dato['id_estado']=;
//                  
//                    //guardo edision en el historial
//                    $datos_historial['id_editor']=$id_usuario;
//                    $datos_historial['id_estado_top']=$top['id_estado'];
//                    $datos_historial['id_objetivo']=$id_obj;
//                    $datos_historial['real1']=$dato['real1'];
////                    echo "<pre>" . print_r($datos_historial, true) . "</pre>";die();
//                    $this->load->model('ddp/ddp_objetivos_historial1_model','ddp_historial',true);
//                    $insert=$this->ddp_historial->insert($datos_historial);
//                    
//                    if($insert)
//                    {
//                        $update=$this->ddp_obj->update($id_obj, $dato);
//                        $gestion=$this->gestion($id_usuario,$id_obj,$id_accion);
//                        echo $update;
//                    }
//                    else
//                        echo 0;
//
//                }
//                else
//                     echo  "({success: false, error :'Usuario sin permisos para editar el documento...'})";
//                
//            }
//            else
//                echo -1; //No tiene permisos
//        }
        
         function eliminar()
        {
            $id_usuario=$this->user['id'];
            $id_obj=$this->input->post("id");
            //verifico que el usuario sea el editor del objetivo
            $this->load->model('ddp/ddp_objetivos_model','ddp_obj',true);
            $objetivo=$this->ddp_obj->dameObjetivo($id_obj);
            if ($objetivo['id_usuario_alta']!=$id_usuario)
                exit("({success: false, error :'Usuario sin permisos para eliminar el documento...'})");
            else
            {
                //verifico que el objetivo este en estado 1:En Borrador, 4:Propuesto al usuario y 5:Editado por el usuario
                if ($objetivo['id_estado']==1 || $objetivo['id_estado']==4 || $objetivo['id_estado']==5)
                {
                    //elimino de la tabla de objetivos y del historial
                    $this->load->model('ddp/ddp_objetivos_model','ddp_obj',true);
                    $delete=$this->ddp_obj->delete($id_obj);
                    if ($delete)
                        echo 1;
                    else
                        echo 0;
                        
                    
                }
                else
                    exit("({success: false, error :'El objetivo debe estar en un estado editable por el propietario'})");
                
            }
                
           
        }
         function liberar()
        {
            $id_usuario=$this->user['id'];
            $id_obj=$this->input->post("id");
            $id_accion=1;
            $gestion=$this->gestion($id_usuario,$id_obj,$id_accion);
            echo $gestion;
           
        }
         function liberar_eval1()
        {
            $id_usuario=$this->user['id'];
            $id_obj=$this->input->post("id");
            $id_accion=1;
            $id_estado_top=2;
            $gestion=$this->gestion($id_usuario,$id_obj,$id_accion,$id_estado_top);
            echo $gestion;
           
        }
        
        function aprobar()
        {
            $id_usuario=$this->user['id'];
            $id_obj=$this->input->post("id");
            $id_accion=3;
            $id_estado_top=1;
            $gestion=$this->gestion($id_usuario,$id_obj,$id_accion,$id_estado_top);
            if(substr($gestion,11,4)=='true');
            {
                $datos_objs['aceptado']=1;
                $datos_objs['fecha_aceptado']=date('Y-m-d G:i:s');
                $datos_objs['usuario_aprobador']=$id_usuario;
                $this->load->model('ddp/ddp_objetivos_model','ddp_obj',true);
                $this->ddp_obj->update($id_obj,$datos_objs);
            }
            echo $gestion;
        }
        
         function aprobar_eval1()
        {
            $id_usuario=$this->user['id'];
            $id_obj=$this->input->post("id");
            $id_accion=3;
            $id_estado_top=2;
            $gestion=$this->gestion($id_usuario,$id_obj,$id_accion,$id_estado_top);
            echo $gestion;
        }
         function cerrar_mitop()
        {
            $id_usuario = $this->user['id'];
            $id_top     = $this->input->post("id");
            $id_accion  = 11;
            //verifico que el usuario sea el dueño de la TOP
            $this->load->model('ddp/ddp_tops_model','ddp_tops',true);
//            $top=  $this->ddp_tops->dameTopActivaDeUsuario($id_usuario);
            $top=  $this->ddp_tops->dameTopPorId($id_top);
//            echo "<pre>" . print_r($top, true) . "</pre>";die();
            if ($top['id_usuario']!=$id_usuario)
                exit( "({success: false, error :'Error al verificar el propietario de la TOP...'})");
            else
            {
                //verifico que la TOP este en estado 1 = Definiendo objetivos
                 if ($top['id_estado']!=1)
                    exit( "({success: false, error :'Error: Su TOP no se encuentra bien adecuada, por favor informe a sistemas'})");
                else 
                {
                    //controlo que la top tenga la cantidad de objetivos que correspponde
                    $control=$this->controlarQObjTop($id_top);
                    
                    if (!$control)
                        exit( "({success: false, error :'Error en la cantidad mínima de objetivos. Debe especificar: <ul><li>&bull;3 objetivos <b>Que</b>, </li><li>&bull;1 Objetivo <b>Como</b> y </li><li>&bull;1 Objetivos <b>Organizacional</b></li></ul>'})");
                    else 
                    {
                        //verifico que todos los objetivos esten aprobados (id_estado=6)
                        $todosAprobados=$this->ddp_obj->verificaTodosAprobados($top['id_top']);
                         if ($todosAprobados!=0)
                            exit( "({success: false, error :'Error: Tiene ".$todosAprobados." objetivos aún sin aprobar. Por favor gestion sus objetivos con su supervisor'})");
                        else 
                        {
                            //verifico que todos los objetivos tengan peso !=0
                            $ObjSinPeso=$this->ddp_obj->verificaTodosConPeso($top['id_top']);
                             if ($ObjSinPeso!=0)
                                exit( "({success: false, error :'Error: Tiene ".$ObjSinPeso." objetivos sin asignarles peso'})");
                             else
                             {
                                //cierro la top pasandola al estado 2 = 1ra Evaluación
                                $datos['id_estado']=2;
                                $cerrarTop=$this->ddp_tops->update($top['id_top'],$datos);
                                if ($cerrarTop==0)
                                    exit( "({success: false, error :'Error actualizando registro'})");
                                    
                                else
                                {
                                    $aud = $this->_auditoria($id_accion, $id_usuario, $id_top);
                                    if ($aud)
                                        exit( "({success: true, error :''})");
                                    else
                                        exit("({success: false, error :'Error al insertar auditoria. Comuniquese con el administrador del sistema.'})");
                                }
                             }

                        }
                    }
                }
                
            }
           
        }
         function cerrar_eval1()
        {
            $id_usuario = $this->user['id'];
            $id_top     = $this->input->post("id");
            $id_accion  = 12;
            //verifico que el usuario sea el dueño de la TOP
            $this->load->model('ddp/ddp_tops_model','ddp_tops',true);
//            $top=  $this->ddp_tops->dameTopActivaDeUsuario($id_usuario);
            $top=  $this->ddp_tops->dameTopPorId($id_top);
//            echo "<pre>" . print_r($top, true) . "</pre>";die();
            if ($top['id_usuario']!=$id_usuario)
                exit( "({success: false, error :'Error al verificar el propietario de la TOP...'})");
            else
            {
                //verifico que la TOP este en estado 2 = Evaluación
                 if ($top['id_estado']!=3)
                    exit( "({success: false, error :'Error: Su TOP no se encuentra en el estado correcto'})");
                else 
                {
                    $this->load->model('ddp/ddp_objetivos_model','ddp_obj',true);
                    //verifico que todos los objetivos esten con 1ra ev aprobada (id_estado=13)
                    $todosAprobados=$this->ddp_obj->verificaTodosAprobadosEv1($top['id_top']);
                     if ($todosAprobados!=0)
                        exit( "({success: false, error :'Error: Tiene ".$todosAprobados." objetivos aún sin aprobar'})");
                    else 
                    {
                        //verifico que todos los objetivos tengan peso !=0
                        $ObjSinPeso=$this->ddp_obj->verificaTodosConPeso($top['id_top']);
                        if ($ObjSinPeso!=0)
                           exit( "({success: false, error :'Error: Tiene ".$ObjSinPeso." objetivos sin asignarles peso'})");
                        else
                        {
                            //cierro la top pasandola al estado 4 = Terminada
                            $datos['id_estado']=4;
                            $cerrarTop=$this->ddp_tops->update($top['id_top'],$datos);
                            if ($cerrarTop==0)
                                exit( "({success: false, error :'Error actualizando registro'})");
                            else
                            {
                                $aud = $this->_auditoria($id_accion, $id_usuario, $id_top);
                                if ($aud)
                                    exit( "({success: true, error :''})");
                                else
                                    exit("({success: false, error :'Error al insertar auditoria. Comuniquese con el administrador del sistema.'})");
                            }

                        }

                    }
                }
                
            }
           
        }
        function gestion($id_usuario,$id_objetivo,$id_accion,$id_estado_top=1)//
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
                            $aud = $this->_auditoria($id_accion, $id_usuario, $objetivo['id_top'], $id_objetivo);
                            if ($aud)
                                return "({success: true, error :''})";
                            else
                                return "({success: false, error :'Error al insertar auditoria. Comuniquese con el administrador del sistema.'})";
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
//        function mef($id_usuario,$id_objetivo,$id_accion,$id_estado_top=1)
//        {
//            //busco el objetivo
//            $this->load->model('ddp/ddp_objetivos_model','ddp_obj',true);
//            $objetivo=$this->ddp_obj->dameObjetivo($id_objetivo);
////            echo "<pre>" . print_r($objetivo, true) . "</pre>";
//            $proceso['succes']=false;
//            $proceso['error']="desconocido";
//            if ($objetivo==0)
//            {
//                $proceso['succes']=false;
//                $proceso['error']="Error al buscar el objetivo...";
//            }
//            else
//            {
//                //verifico que el usuario sea o el creador o el supervisor de este objetivo
//                 if ($objetivo['id_usuario_alta']!=$id_usuario && $objetivo['id_supervisor']!=$id_usuario)
//                {
//                    $proceso['succes']=false;
//                    $proceso['error']="El usuario no tiene relación con el objetivo";
//                }
//                else
//                {
//                    //verifico que la accion solicitada corresponda con el tipo de usuario (editor o supervisor)
//                    $this->load->model('ddp/ddp_mef_model','ddp_mef',true);
//                    $estadoAccion=$this->ddp_mef->dameEstadoAccion($objetivo['id_estado'],$id_accion,$id_estado_top);
////                    echo "<pre>" . print_r($estadoAccion, true) . "</pre>";
//                    //si estadoAccion[actor]==1 => el usuario debe ser el editor sino debe ser el supervisor
//                    if (($estadoAccion['actor']==1 && $objetivo['id_usuario_alta']==$id_usuario)||($estadoAccion['actor']==2 &&$objetivo['id_supervisor']==$id_usuario))
//                    {
//                        //usuario correcto
//                        //cambio el objetivo al siguiente estado y guardo en auditoria
//                        $dato['id_estado']=$estadoAccion['id_nuevo_estado'];
//                        $cambiarEstado=$this->ddp_obj->update($id_objetivo,$dato);
//                        if ($cambiarEstado)
//                        {
//                            $proceso['succes']=true;
//                            $proceso['error']="";
//                        }
//                        else
//                        {
//                            $proceso['succes']=false;
//                            $proceso['error']="Error al cambiar de estado el objetivo";
//                        }
//                            
//                    }
//                    else
//                    {
//                        //usuario incorrecto
//                        $proceso['succes']=false;
//                        $proceso['error']="El usuario no tiene permisos para realizar la acción solicitada";
//                    }
//                }
//            }
//            return $proceso;
//        }
    function verificar_editor_mef($id_objetivo,$id_accion)
    {
        $actor=1;//Editor
        $this->load->model('ddp/ddp_objetivos_model','ddp_obj',true);
        $this->load->model('ddp/ddp_mef_model','ddp_mef',true);
        $objetivo=$this->ddp_obj->dameObjetivo($id_objetivo);
        $mef=$this->ddp_mef->dameEstadoAccion($objetivo['id_estado'],$id_accion);
//            echo "<pre>" . print_r($mef, true) . "</pre>";   
            if ($mef['actor']==$actor )
                return true;
            else
               return false;

    }
    
    function controlar_q_obj()
    {
        $id_dimension   = $this->input->post("id_dimension");
        $id_top         = $this->input->post("id_top");
        
        $this->load->model('ddp/ddp_dimensiones_model','ddp_dimensiones',true);
        $this->load->model('ddp/ddp_objetivos_model','ddp_obj',true);
        
        $q_obj_dim = $this->ddp_dimensiones->dame_q_obj_dimension($id_dimension);
        $q_obj = $this->ddp_obj->dame_q_obj($id_dimension,$id_top);
//        echo $q_obj;
//        echo $q_obj_dim;
        if ($q_obj_dim > $q_obj)
            echo 1;
        else
            echo 0;
    }
    function controlarQObjTop($id_top)
    {
        
        $this->load->model('ddp/ddp_dimensiones_model','ddp_dim',true);
        $this->load->model('ddp/ddp_objetivos_model','ddp_obj',true);
        
        $qObjetivos     = $this->ddp_dim->dameTotalObj();
        $qObjetivosTop  = $this->ddp_obj->dameTotalObjTop($id_top);
        if ($qObjetivos == $qObjetivosTop)
            return true;
        else
            return false;
    }
    
    public function combo_supervisor()
    {
        $limit=$this->input->post("limit");
        $start=$this->input->post("start");
        $query=$this->input->post("query");
        $id_usuario_not=array($this->user['id']);
        //$id_usuario_acomp=array($this->user['id_acomp1']==null?"":$this->user['id_acomp1']);
        $this->load->model("gestion_riesgo/gr_usuarios_model","gr_usuarios",true);
        $jcode = $this->gr_usuarios->usuariosRealizaOmcCombo($limit,$start,$query,$id_usuario_not);
        echo $jcode;
    }
    
    public function listadoMisTops()
    {
        if ($this->permisos['Listar'])
        {
            $id_usuario=$this->user['id'];

//            $id_periodo = ($this->input->post("filtro_id_periodo"))?$this->input->post("filtro_id_periodo"):-1;

//            if($id_periodo!=-1)
//            {
//                $this->load->model('ddp/ddp_periodos_model','ddp_periodos',true);
//                $periodo=$this->ddp_periodos->damePeriodo($id_periodo);
//            }
//            else
//                $periodo=-1;

            $listado=array();
            $this->load->model('ddp/ddp_tops_model','ddp_tops',true);
            $start = $this->input->post("start");
            $limit = $this->input->post("limit");
            $sort = $this->input->post("sort");
            $dir = $this->input->post("dir");
//            if($this->input->post("filtros"))
//            {
//                $filtros=json_decode($this->input->post("filtros"));
//                foreach ($filtros as &$filtro)
//                {
//                    if ($filtro=='Todas'||$filtro=='Todos'||$filtro=='-1')
//                        $filtro="";
//                }
//                unset ($filtro);
////                        echo "<pre>".print_r($filtros,true)."</pre>";
//
//            }
//            else
//            {
//                $filtros="";
//
//            }
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
            $listado = $this->ddp_tops->listado_mis_tops($start, $limit,$busqueda, $campos, $sort, $dir,$id_usuario);//$filtros,$periodo);
//                    echo "<pre>".print_r($listado,true)."</pre>";
            echo $listado;
        }
        else
            echo -1; //No tiene permisos

    }
    
    public function mi_top()
    {
        $modulo = $this->uri->segment(SEGMENTOS_DOM+2);
        $id_top = $this->input->post("id_top");
//        echo $id_top;
        $this->load->model('ddp/ddp_tops_model','ddp_tops',true);
        $usuario=$this->ddp_tops->dameUsuarioTop($id_top);

        if (!isset($modulo)) $modulo=0;
        $this->load->model('permisos_model','permisos_model',true);
        $this->load->model('ddp/ddp_tops_model','ddp_tops',true);
        $variables['permiso'] = $this->permisos_model->checkIn($this->user['perfil_id'],$modulo);
        $top=$this->ddp_tops->dameTopActivaDatosPanelAdmin($id_top);
//        $algo = $top;
//            echo "<pre>".print_r($algo,true)."</pre>";die();
//        echo $top;
        $variables['id_top'] = $id_top;
        $variables['usuario'] = $usuario['nomape'];
        $variables['top'] = $top;
//        $variables['txt_supervisor'] = $top['txt_supervisor'];
        $this->load->model('gestion_riesgo/gr_usuarios_model','gr_usuarios',true);
        $id_supervisor=$this->gr_usuarios->dameIdUsuarioSuperior($this->user['id']);
        $supervisor=$this->gr_usuarios->dameUsuarioPorId($id_supervisor);
        $variables['supervisor'] = $supervisor['persona'];
        $variables['id_supervisor'] = $id_supervisor;
        $id_aprobador=$this->gr_usuarios->dameIdUsuarioSuperior($id_supervisor);
        $variables['id_aprobador'] = $id_aprobador;
//        $variables['id_top'] = $id_top;
//             $this->load->model('ddp/ddp_tops_model','ddp_tops',true);
//            die();

        $this->load->view('ddp/mistops/top/listadoMiTop',$variables);
    }
    
    public function _auditoria ($id_accion,$id_usuario,$id_top, $id_objetivo=0)
    {
        $aud = array();
        $aud['id_accion'] = $id_accion;
        $aud['id_usuario_alta'] = $id_usuario;
        $aud['id_top'] = $id_top;
        if ($id_accion == 1 || $id_accion == 2 || $id_accion == 3 || $id_accion == 6) 
                $aud['observacion'] = "Objetivo nro: " . $id_objetivo;
        
        $this->load->model('ddp/ddp_tops_auditoria_model','ddp_tops_auditoria',true);
        $insert_aud = $this->ddp_tops_auditoria->insert($aud);
        
        if ($insert_aud)
            return true;
        else
            return false;
        
    }
       
}
?>