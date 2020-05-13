<?php
class Tops_sup extends CI_Controller
{
    private $modulo=31;
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
            
            $this->load->view('ddp/tops_sup/listado',$variables);
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
            $this->load->view('ddp/tops_sup/top/listado',$variables);
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
                    $listado = $this->ddp_tops->listado_supervisor($start, $limit, $filtros,$busqueda, $campos, $sort, $dir,$id_usuario,$periodo);
                    echo $listado;
                }
                else
                    echo -1; //No tiene permisos
                
	}
//	public function listado_dimensiones()
//	{
//		if ($this->permisos['Listar'])
//                {
//                    $id_usuario=$this->input->post("id_usuario");
////                    $id_periodo=$this->input->post("id_periodo");
////                    $this->load->model('ddp/ddp_tops_model','ddp_tops',true);
////                    $id_top=$this->ddp_tops->dameIdTop($id_usuario,$id_periodo);
//                    $id_top=$this->input->post("top");
//                    
//                    $usuario['id']= $id_usuario;
//                    $usuario['id_top']=$id_top;
//                    
//                    $listado=array();
//                    $this->load->model('ddp/ddp_dimensiones_model','ddp_dimensiones',true);
//                    $sort = $this->input->post("sort");
//                    $dir = $this->input->post("dir");
//                    $listado = $this->ddp_dimensiones->listado_top($usuario,$sort, $dir);
//                    echo $listado;
//                }
//                else
//                    echo -1; //No tiene permisos
//                
//	}
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
        public function fd_combo()
	{
            $id_dimension=$this->input->post("id_dimension");
            $this->load->model('ddp/ddp_fuentes_model','ddp_fuentes',true);
            $var=$this->ddp_fuentes->dameComboFD($id_dimension);
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
        public function datos_obj_ev1 ()
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
                $objetivo_form=$this->ddp_objetivos->dameObjetivoParaFormEditSupTop_ev1($id_objetivo);
                echo $objetivo_form;	
            }
	}
        public function nueva_top()
	{
		if ($this->permisos['Alta'])
                {
                    $datos['id_usuario']=$this->user['id'];
//                    $datos['id_periodo']=$this->input->post("id_periodo");
                    $datos['id_periodo']=$this->input->post("id_periodo");
                    $datos['id_estado']=1;
                    
                    //busco el usuario superior
                    $this->load->model('gestion_riesgo/gr_usuarios_model','gr_usuarios',true);
                    $id_superior=$this->gr_usuarios->dameIdUsuarioSuperior($datos['id_usuario']);
                    $datos['id_supervisor']=$id_superior;
                    
                    $id_puesto=$this->gr_usuarios->dameUsuarioIdPuesto($datos['id_usuario']);
                    $datos['id_puesto']=$id_puesto;


                    $this->load->model('ddp/ddp_tops_model','ddp_tops',true);
                    //creo la nueva TOP
                    
                    $top = $this->ddp_tops->insert($datos);
                    if ($top==0)
                        exit( "({success: false, error :'Error al crear su TOP...'})");
                    
                    $principios=array('Integridad','Respeto','Honestidad','Proactividad','Compromiso');
                    $q_principios=count($principios);
                    
                    $datos_objs['id_top']=$top;
                    $datos_objs['id_dimension']=6;//id_dimensio=6 => Principios
                    $datos_objs['id_usuario_alta']=$this->user['id'];
                    $datos_objs['aceptado']=1;
                    $datos_objs['id_estado']=6;
                    $datos_objs['op']='';
                    $datos_objs['indicador']='';
                    $datos_objs['fd']='';
                    $datos_objs['valor_ref']='';
                    $datos_objs['peso']=4;
                    
                    $this->load->model('ddp/ddp_objetivos_model','ddp_obj',true);
                    $q_objs=0;
                    foreach ($principios as $principio)
                    {
                        $datos_objs['oe']=$principio;
                        $insert=$this->ddp_obj->insert($datos_objs);
                    
                        $q_objs+=$insert;
                    }
                    
                    if ($q_objs==$q_principios)
                        echo "({success: true, error :null})";
                    else
                        echo "({success: false, error :'Error al configurar su TOP...'})";
                    
                    

                }
                else
                    echo -1; //No tiene permisos
                
	}
        public function edit_obj()
	{
            if ($this->permisos['Alta'])
            {
                $id_usuario=$this->user['id'];
                $campos_requeridos=array('obj');
                $control=0;
                foreach ($campos_requeridos as $campo)
                {
                    if ($this->input->post($campo)=="")
                        exit("4");
                }
                
                
                $id_objetivo=$this->input->post("id");
                $this->load->model('ddp/ddp_objetivos_model','ddp_obj',true);
                
                if($id_objetivo!=0)
                {
                    //Modifico Objetivo y guardo en el historial
                    $objetivo=$this->ddp_obj->dameObjetivoEditado($id_objetivo);
//                    echo "<pre>" . print_r($objetivo, true) . "</pre>";die();
                    // $objetivo['peso']=$this->formatFloat($objetivo['peso']);

//                    $datos['oe']=$this->input->post("obj_e");
                    $datos['obj']=$this->input->post("obj");
                    $datos['indicador']=$this->input->post("indicador");
                    $datos['fd']=$this->input->post("fd");
                    $datos['valor_ref']=$this->input->post("valor_ref");
//                    $datos['peso']=  $this->formatFloat($this->input->post("peso"));
                    $datos['fecha_evaluacion']=$this->input->post("fecha_evaluacion");
                    $datos['fecha_evaluacion']= substr($datos['fecha_evaluacion'], 0, 10);
                    $datos['fecha_evaluacion']= date("d/m/Y",  strtotime($datos['fecha_evaluacion']));
                    $diff=  array_diff($datos, $objetivo);
//                    echo "<pre>" . print_r($diff, true) . "</pre>";
                    if (count($diff)>0 )
                    {
                        if(array_key_exists("fecha_evaluacion", $diff))
                            $diff['fecha_evaluacion']   = $this->input->post("fecha_evaluacion");
                        $datos['fecha_evaluacion']=$this->input->post("fecha_evaluacion");
                        $ok=$this->ddp_obj->update($id_objetivo, $datos);
                        $datos_historial=$diff;
                        $datos_historial['id_editor']=$id_usuario;
                        $datos_historial['id_objetivo']=$id_objetivo;
                        $this->load->model('ddp/ddp_objetivos_historial1_model','ddp_historial',true);
                        $insert=$this->ddp_historial->insert($datos_historial);
                        //antes de estado el objetivo
                        $id_accion=2;
                        $gestion=$this->gestion($id_usuario,$id_objetivo,$id_accion);
                         if(substr($gestion,11,4)=='true')
                            $ok=1;
                         else
                            $ok=3;
                        
                    }
                    else
                        $ok=2;

                }
                else {
                        $ok=0;
                    
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
            if(substr($gestion,11,4)=='true')
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
                
                 $this->load->model('ddp/ddp_mef_model','ddp_mef',true);
                $mef=$this->ddp_mef->dameEstadoAccion($objetivo['id_estado'],$id_accion);
                
                //verifico que la dimension del objetivo no sea "Principios"
                if ($objetivo['id_dimension']==6)
                    exit( "({success: false, error :'Objetivo no editable...'})");
                if ($objetivo['id_supervisor']==$id_usuario && $mef['actor']==2)
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
                     echo  2;
                
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
           $id_accion=2;
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
    
    public function _auditoria ($id_accion,$id_usuario,$id_top, $id_objetivo)
    {
        $aud = array();
        $aud['id_accion'] = $id_accion;
        $aud['id_usuario_alta'] = $id_usuario;
        $aud['id_top'] = $id_top;
        $aud['observacion'] = "Objetivo nro: " . $id_objetivo;
        
        $this->load->model('ddp/ddp_tops_auditoria_model','ddp_tops_auditoria',true);
        $insert_aud = $this->ddp_tops_auditoria->insert($aud);
        
        if ($insert_aud)
            return true;
        else
            return false;
        
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
}
?>