<?php
class Mejora_continua extends CI_Controller
{
    private $modulo=20;
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
            if (!($this->user['id']>0)) 
                redirect("admin/admin/index_js","location", 301);
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
            $this->load->model('sys_permiso_acciones_model','sys_permiso_acciones',true);
            $this->load->model('gestion_riesgo/gr_usuarios_model','gr_usuarios',true);
            $variables['permiso'] = $this->permisos_model->checkIn($this->user['perfil_id'],$modulo);
            $permiso_acciones = $this->sys_permiso_acciones->damePermisoAcciones($modulo,$this->user['id']);
            $permiso_gdr = $this->gr_usuarios->dameUsuarioPermisoGdr($this->user['id']);
            //echo $permiso_gdr;
            $variables['revision'] = 0;
            if ($permiso_acciones!=0)
            {
                foreach ($permiso_acciones as $atrib)
                {
                    if ($atrib['id_accion'] == 1)//pregunto si tiene id de permiso 1(editar revision)
                        $variables['revision'] = 1;
                }
            }
//             echo "<pre>".print_r($permiso_acciones,true)."</pre>";
//             die();
            $variables['btn'] = $this->user['id'];
            $variables['btn_obsoleto'] = ($permiso_gdr != 0)?1:0;
            $this->load->model("gestion_riesgo/gr_usuarios_model","gr_usuarios",true);
//            $variables['u_gr']=$this->gr_usuarios->verificarSiPerteneceGr($this->user['id']);
//             echo "<pre>".print_r($variables,true)."</pre>";
            $this->load->view('gestion_riesgo/mejora_continua/listado',$variables);
	}
	
	public function listado()
	{
		if ($this->permisos['Listar'])
                {
                    $id_usuario=$this->user['id'];
                    $listado=array();
                    $this->load->model('gestion_riesgo/mejoracontinua_model','mejoracontinua',true);
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
                    //mantengo usuario solo para ordenar
                    $usuario=$this->_dameAreas($id_usuario);
                    $listado = $this->mejoracontinua->listado($usuario,$start, $limit, $filtros,$busqueda, $campos, $sort, $dir);
                    
                    echo $listado;
                }
                else
                    echo -1; //No tiene permisos
                
	}
	public function listadoArchivosCerrar()
	{
		if ($this->permisos['Listar'])
                {
                    $id_usuario=$this->user['id'];
                    $id_tarea = $this->input->post("id_tarea");
                    $this->load->model('gestion_riesgo/gr_archivos_model','gr_archivos',true);
                    
//                    $start = $this->input->post("start");
//                    $limit = $this->input->post("limit");
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
                    $listado = $this->gr_archivos->listadoTarea($id_tarea);
                    
                    echo $listado;
                }
                else
                    echo -1; //No tiene permisos
                
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
        public function combo_responsables()
	{
            $limit=$this->input->post("limit");
            $start=$this->input->post("start");
            $query=$this->input->post("query");
            $id_usuario_not=$this->user['id'];
            $this->load->model("gestion_riesgo/mejoracontinua_model","mejoracontinua",true);
            $jcode = $this->mejoracontinua->usuariosCombo($limit,$start,$query,$id_usuario_not);
            echo $jcode;
	}
	
        public function insert()
	{
            if ($this->permisos['Alta'])		
            {
                $datos=array();
                $datos['usuario_alta']          =$this->user['id'];
                $datos['id_tipo_herramienta']   =$this->input->post("herramienta");
                $datos['usuario_responsable']   =$this->input->post("responsable");
                $datos['hallazgo']              =$this->input->post("hallazgo");
                $datos['id_grado_crit']         =$this->input->post("grado_crit");
                $datos['tarea']                 =$this->input->post("tarea");
                $datos['fecha_vto']             =$this->input->post("fecha");
                //$datos['rpd']                   =$this->input->post("rpd");
                $datos['id_estado']             =1;
//                 echo "<pre>".print_r($datos,true)."</pre>";die();
                $control=true;
                foreach ($datos as $dato)
                {
                    if($dato=="")
                        $control=false;
                }
                if ($control)
                {
                    //verifico que no sea rePost
                    $this->load->model("gestion_riesgo/mejoracontinua_model","mejoracontinua",true);

                    if ($this->mejoracontinua->verificaRePost($datos)==0)
                    {
                        $insert_id=$this->mejoracontinua->insert($datos);
                        if($insert_id)
                        {
                            //inserto historial de accion
                            $id_accion=1;
                            $texto="";
                            $this->grabar_accion($id_accion, $insert_id, $texto);
                            $this->_preparar_enviar_mail($insert_id,'alta');
                            echo 1; //todo OK
                        }
                        else
                            echo 2; //Error al insertar el registro
                    }
                    else
                        echo 0;//Registro repetido (RePost)
                }
                else
                    echo 3;//Faltan campos requeridos
                
            }
            else
                    echo -1; //No tiene permisos
	}
        public function tarea_edit()
	{
            if ($this->permisos['Alta'])		
            {
                $datos=array();
                $id_tarea =$this->input->post("id_tarea");
                
                //verifico que el usaurio que esta editando corresponsa con el creador y que la tarea este en estado rechazado=3
                $this->load->model('gestion_riesgo/gr_tareas_model','gr_tarea',true);
                $editor=$this->gr_tarea->verificarEditor($id_tarea,$this->user['id']);
                
                if($editor)
                {
//                    $datos['id_tipo_herramienta']   =$this->input->post("herramienta");
                    $datos['usuario_responsable']   =$this->input->post("responsable");
                    $datos['hallazgo']              =$this->input->post("hallazgo");
                    $datos['id_grado_crit']         =$this->input->post("grado_crit");
                    $datos['tarea']                 =$this->input->post("tarea");
                    $datos['fecha_vto']             =$this->input->post("fecha");
                    $datos['id_estado']             =4; //reabierta
                    $datos['editada']               =1; //Si
                    
                    //verifico que todos los campos esten completos
                    $control=true;
                    foreach ($datos as $dato)
                    {
                        if($dato=="")
                            $control=false;
                    }
                    if ($control)
                    {
                        //verifico que no sea rePost
                        $this->load->model("gestion_riesgo/mejoracontinua_model","mejoracontinua",true);

                        if ($this->mejoracontinua->verificaRePost($datos)==0)
                        {
                            //guardo en acciones que campos fueron editados
                            $tarea=$this->gr_tarea->dameTarea($id_tarea);
                            $camposModificados=array();
                            if($tarea['usuario_responsable']!=$datos['usuario_responsable'])$camposModificados[]='Usuario Responsable';
                            if($tarea['hallazgo']!=$datos['hallazgo'])$camposModificados[]='Hallazgo';
                            if($tarea['tarea']!=$datos['tarea'])$camposModificados[]='Tarea';
                            if($tarea['id_grado_crit']!=$datos['id_grado_crit'])$camposModificados[]='Grado Criticidad';
                            if($tarea['fecha_vto']!=date('d/m/Y',strtotime($datos['fecha_vto'])))$camposModificados[]='Fecha Vencimiento';
                            //if($tarea['revision']!=$datos['revision'])$camposModificados[]='Revision';
                            if(count($camposModificados)>0)
                                $txtEditados="Datos modificados:".implode("; ", $camposModificados);
                            else
                                $txtEditados="Datos modificados:-ninguno-";
                            //copia en historial para guardar datos anteriores a la edición
                            $copia=$this->mejoracontinua->copy($id_tarea);
                            if($copia)
                            {
                                $update=$this->mejoracontinua->update($id_tarea,$datos);
                                if($update)
                                {
                                    //guardo en el historia que la tarea fue reabierta
                                    $this->load->model('gestion_riesgo/gr_historial_model','gr_historial',true);
                                    $datosHist['id_estado']=4; //estado Reabiert
                                    $datosHist['id_tarea']=$id_tarea;
                                    $this->gr_historial->insertReabiertas($datosHist);
                                    
                                    //Actualizo la fecha de acción
                                    $this->mejoracontinua->updateGestion($id_tarea,$datos['id_estado']);
                                    
                                    //inserto historial de accion
                                    $id_accion=3;
                                    $texto=$txtEditados;
                                    $this->grabar_accion($id_accion, $id_tarea, $texto);
                                    
                                        if(ENPRODUCCION)
                                            $this->_preparar_enviar_mail($id_tarea,'editada');
                                    echo 1; //todo OK
                                }
                                else
                                    echo 2; //Error al insertar el registro
                                
                            }
                            else
                                echo 3; //error al copiar en historial
                        }
                        else
                            echo 4;//Registro repetido (RePost)
                    }
                    else
                        echo 5;//Faltan campos requeridos
                    
                }
                else
                    echo 0;//No es editor, la tarea no esta rechazada
                
                
                
            }
            else
                    echo -1; //No tiene permisos
	}
        function _verificoResponsable($id_tarea,$id_responsable)
        {
            $this->load->model('gestion_riesgo/gr_tareas_model','gr_tarea',true);
            return $this->gr_tarea->verificarResponsable($id_tarea,$id_responsable);
        }
        function _verificoEditor($id_tarea,$id_usuario)
        {
            $this->load->model('gestion_riesgo/gr_tareas_model','gr_tarea',true);
            return $this->gr_tarea->verificarEditor($id_tarea,$id_usuario);
        }
        public function cerrar_AbrirForm()
	{
//            $modulo = $this->uri->segment(SEGMENTOS_DOM+2);
            $id_tarea =$this->input->post("id");
            //verifico si la tarea existe
            $this->load->model('gestion_riesgo/gr_tareas_model','gr_tareas',true);
            $tarea=$this->gr_tareas->dameTarea2($id_tarea);
//            echo "<pre>".print_r($tarea,true)."</pre>";
            
            $this->load->model('gestion_riesgo/gr_cierres_model','gr_cierres',true);
            $cierre=$this->gr_cierres->verificaCierrePorTarea($id_tarea);
            $tarea['cierre']=$cierre;
            if ($tarea!=0)
            {
                // verifico que sea el responsable
                $id_usuario=$this->user['id'];
                if ($this->_verificoResponsable($id_tarea,$id_usuario))
                {
//                    if (!isset($modulo)) $modulo=$this->$modulo;
                    $variables['permiso'] = $this->permisos;
                    $variables['roles'] = $this->roles;
                    $variables['tarea'] = $tarea;
                    $variables['cap'] = ($tarea['id_tipo_herramienta']==9)?1:0;
//                    echo "<pre>".print_r($variables,true)."</pre>";
                    $this->load->view('gestion_riesgo/mejora_continua/cerrar/listado_cerrar',$variables);
                    
                }
                else 
                    echo 0;//Usuario sin permisos u operacion ya gestionada
            }
            else 
                echo -1;
	}
        public function cerrar_clicBtnCerrar()
	{
            $id_tarea =$this->input->post("id");
            //verifico si la tarea existe
            $this->load->model('gestion_riesgo/gr_tareas_model','gr_tareas',true);
            $tarea=$this->gr_tareas->dameTarea2($id_tarea);
            if ($tarea!=0)
            {
                // verifico que sea el responsable
                $id_usuario=$this->user['id'];
                if ($this->_verificoResponsable($id_tarea,$id_usuario))
                    echo 1; //todo OK
                else 
                    echo 0;//Usuario sin permisos u operacion ya gestionada
            }
	}
        public function cierre_dameDatos()
        {
            $this->load->model('permisos_model','permisos_model',true);
            $modulo = $this->uri->segment(SEGMENTOS_DOM+2);
            $id_tarea =$this->input->post("id");
            //verifico si la tarea existe
            $this->load->model('gestion_riesgo/gr_tareas_model','gr_tareas',true);
            $tarea=$this->gr_tareas->dameTarea2($id_tarea);
            if ($tarea!=0)
            {
                // verifico que sea el responsable
                $id_usuario=$this->user['id'];
                if ($this->_verificoResponsable($id_tarea,$id_usuario))
                {
                    $this->load->model('gestion_riesgo/gr_cierres_model','gr_cierres',true);
                    $cierre=$this->gr_cierres->dameCierrePorTareaParaForm($id_tarea);
                    echo $cierre;
                }
            }
        }
        public function cerrar_guardar()
	{
            $this->load->model('permisos_model','permisos_model',true);
            $modulo = $this->uri->segment(SEGMENTOS_DOM+2);
            $id_tarea =$this->input->post("id");
            //verifico si la tarea existe
            $this->load->model('gestion_riesgo/gr_tareas_model','gr_tareas',true);
            $tarea=$this->gr_tareas->dameTarea2($id_tarea);
            if ($tarea!=0)
            {
                // verifico que sea el responsable
                $id_usuario=$this->user['id'];
                if ($this->_verificoResponsable($id_tarea,$id_usuario))
                {
                    //verifico si ya tiene id_cierre
                    $this->load->model('gestion_riesgo/gr_cierres_model','gr_cierres',true);
                    $cierre=$this->gr_cierres->dameCierrePorTarea($id_tarea);
//                    $datos['texto']=$this->input->post("txt");
                    $datos['texto']="<txt>".$this->input->post("txt")."</txt>";
                    $datos['id_tarea']=$id_tarea;
                    $datos['id_usuario_cerro']=$id_usuario;
                    $datos['fecha_ultima_mod']=date("Y-m-d H:i:s");
                    
                    if($tarea['id_tipo_herramienta']==9)
                    {
//                        $valor=($this->input->post("continua")==2)?1:0;
//                        $this->load->model('cap/cap_responsables_model','cap_responsables',true);
//                        $updateCont=$this->cap_responsables->updateContinua($id_tarea,$valor);
                          $datos['continua']=($this->input->post("continua")==1)?1:0;;
                        
                    }
                    
                    if($cierre!=0)
                        $guardar=$this->gr_cierres->edit($cierre['id_cierre'],$datos);
                    else
                        $guardar=$this->gr_cierres->insert($datos);
                    echo 1;
                }
                else 
                    echo 0;//Usuario sin permisos u operacion ya gestionada
            }
	}
        public function cerrar_confirmaCerrar()
	{
            if ($this->permisos['Modificacion'])		
            {
                //Verifico que ese usuario sea el responsable
                $id_usuario=$this->user['id'];
                $id_tarea =$this->input->post("id");
                
                if ($this->_verificoResponsable($id_tarea,$id_usuario))
                {
                    //verifico campos obligatorios
                    if ($this->input->post("txt")=="")
                         exit(-1);
                    
                    $this->load->model('gestion_riesgo/gr_tareas_model','gr_tareas',true);
                    $tarea=$this->gr_tareas->dameTarea($id_tarea);
                    if($tarea['id_tipo_herramienta']==9) //tarea de capacitación
                    {
                        //veriifico que tenga por lo menos un archivo adjunto
                        $this->load->model("gestion_riesgo/gr_archivos_model","gr_archivos",true);
                        $qArchivos=$this->gr_archivos->cuentaArchivosPorTarea($id_tarea);
                        if ($qArchivos==0)
                         exit(-2);
                        // verifico que tenga por lo menos un participante cargado
                        $this->load->model("cap/cap_participantes_model","cap_participantes",true);
                        $qParticipantes=$this->cap_participantes->cuentaParticipantesPorTarea($id_tarea);
                        if ($qParticipantes==0)
                         exit(-3);
                    }
                    
                    
                    //tomo id_tarea y cambio estado=2
                    $estado =2;
                    $this->load->model("gestion_riesgo/mejoracontinua_model","mejoracontinua",true);
                        if($this->mejoracontinua->updateGestion($id_tarea,$estado))
                        {
                           //guardo los datos de cierre
                           //verifico si ya tiene id_cierre
                            $tarea=$this->gr_tareas->dameTarea($id_tarea);
                            $this->load->model('gestion_riesgo/gr_cierres_model','gr_cierres',true);
                            $cierre=$this->gr_cierres->dameCierrePorTarea($id_tarea);
                            $datos['texto']="<txt>".$this->input->post("txt")."</txt>";
                            $datos['id_tarea']=$id_tarea;
                            $datos['id_usuario_cerro']=$id_usuario;
                            $datos['fecha_cierre']=date("Y-m-d H:i:s");
                            
                            if($tarea['id_tipo_herramienta']==9) //tarea de capacitación
                            {
                                $datos['continua']=($this->input->post("continua")==1)?1:0;
                            }
                            
                            if($cierre!=0)
                                $guardar=$this->gr_cierres->edit($cierre['id_cierre'],$datos);
                            else
                                $guardar=$this->gr_cierres->insert($datos); 
                            
                            //guardo en el historia que la tarea fue cerrada
                            $this->load->model('gestion_riesgo/gr_historial_model','gr_historial',true);
                            $datosHist['id_estado']=2; //estado Cerrado
                            $datosHist['id_tarea']=$id_tarea;
                            $datosHist['usuario_responsable']=$id_usuario;
                            $datosHist['fecha_accion']=date("Y-m-d H:i:s");
                            $datosHist['observacion']=str_replace('<br mce_bogus="1">',"", $datos['texto']);
                            $this->gr_historial->insert($datosHist);
                            
                            //inserto historial de accion
                            $id_accion=4;
                            $texto=$this->input->post("txt");
                            $id_ha=$this->grabar_accion($id_accion, $id_tarea, $texto);
                            //verifico si se subieron archivos al informar la tarea y les grabo el id_historial_accion
                            $this->load->model("gestion_riesgo/gr_archivos_model","gr_archivos",true);
                            $this->gr_archivos->edit_ha($id_tarea,$id_ha);
                            
                            $this->_preparar_enviar_mail($id_tarea,'cerrada');
                            echo 1; //todo OK
                        }
                        else
                            echo 2; //Error al insertar el registro
                }
                else 
                    echo 0;//Usuario sin permisos u operacion ya gestionada
                
            }
            else
                    echo -1; //No tiene permisos
	}
        public function aprobar()
	{
            if ($this->permisos['Modificacion'])		
            {
                //Verifico que ese usuario sea el responsable (COMPLETAR)
                $id_usuario=$this->user['id'];
                $id_tarea =$this->input->post("id");
                
                if ($this->_verificoEditor($id_tarea,$id_usuario))
                {
                    //tomo id_tarea y cambio estado=2
                    $estado =9;
                    $this->load->model("gestion_riesgo/mejoracontinua_model","mejoracontinua",true);
                        if($this->mejoracontinua->updateGestion($id_tarea,$estado))
                        {
                            $this->load->model('gestion_riesgo/gr_cierres_model','gr_cierres',true);
                            $cierre=$this->gr_cierres->dameCierrePorTarea($id_tarea);
                            if($cierre!=0)
                            {
                                if( $cierre['continua']==1)
                                {
                                    $this->load->model('gestion_riesgo/gr_tareas_model','gr_tareas',true);
                                    $tarea=$this->gr_tareas->dameTarea($id_tarea);
                                    //si la tarea es de capacitacion (id_tipo_herramienta=9) y en la marca de continua=1 entonces genero una nueva tarea
                                    if($tarea['id_tipo_herramienta']==9)
                                    {
                                        $copia=$this->mejoracontinua->copyTareaQueContinua($id_tarea);
                                        $datos['id_tarea_nueva']=$copia;
                                    }
                                }
                                $datos['id_usuario_cerro']=$id_usuario;
                                $datos['fecha_aprobacion']=date("Y-m-d H:i:s");
                                $guardar=$this->gr_cierres->edit($cierre['id_cierre'],$datos);
                            }
                            //guardo en el historia que la tarea fue cerrada
                            $this->load->model('gestion_riesgo/gr_historial_model','gr_historial',true);
                            $datosHist['id_estado']=$estado; //estado Cerrado
                            $datosHist['id_tarea']=$id_tarea;
                            $datosHist['usuario_alta']=$id_usuario;
                            $datosHist['fecha_accion']=date("Y-m-d H:i:s");
                            $datosHist['observacion']='';
                            $this->gr_historial->insert($datosHist);
                            
                            //cambio el estado_vto=0 por si la tarea estaba vencida
                            $dato['estado_vto']=0;
                            $this->mejoracontinua->update($id_tarea,$dato);
                            //inserto historial de accion
                            $id_accion=6;
                            $texto="";
                            $this->grabar_accion($id_accion, $id_tarea, $texto);
                                                        
                           $this->_preparar_enviar_mail($id_tarea,'aprobada');
                           echo 1; //todo OK
                        }
                        else
                            echo 2; //Error al insertar el registro
                }
                else 
                    echo 0;//Usuario sin permisos u operacion ya gestionada
                
            }
            else
                    echo -1; //No tiene permisos
	}
        public function rechazar()
	{
            if ($this->permisos['Modificacion'])		
            {
                //Verifico que ese usuario sea el responsable (COMPLETAR)
                $id_usuario=$this->user['id'];
                $id_tarea =$this->input->post("id");
                $obs   =$this->input->post("texto");
                
                //verifico que la tarea no de sea de BC (id_tipo_herramienta) por que en ese caso no se puede rechazar
                $this->load->model("gestion_riesgo/gr_tareas_model","gr_tareas",true);
                $tarea=$this->gr_tareas->dameTarea($id_tarea);
                if ($tarea['id_tipo_herramienta']!=3)
                {
                    if ($this->_verificoResponsable($id_tarea,$id_usuario))
                    {
                        //tomo id_tarea y cambio estado=3
                        $estado =3;
                        $this->load->model("gestion_riesgo/mejoracontinua_model","mejoracontinua",true);
                            if($this->mejoracontinua->updateGestion($id_tarea,$estado,$obs))
                            {
                                //cambio el estado_vto=0 por si la tarea estaba vencida
                                $dato['estado_vto']=0;
                                $this->mejoracontinua->update($id_tarea,$dato);
                                //inserto historial de accion
                                $id_accion=2;
                                $texto=$obs;
                                $this->grabar_accion($id_accion, $id_tarea, $texto);
                                $this->_preparar_enviar_mail($id_tarea,'rechazada');
                                echo 1; //todo OK

                            }
                            else
                                echo 2; //Error al insertar el registro
                    }
                    else 
                        echo 0;//Usuario sin permisos u operacion ya gestionada
                }
                else
                    echo 3;
            }
            else
                    echo -1; //No tiene permisos
	}
        public function observar()
	{
            if ($this->permisos['Modificacion'])		
            {
                //Verifico que ese usuario sea el responsable (COMPLETAR)
                $id_usuario=$this->user['id'];
                $id_tarea =$this->input->post("id");
                $obs   =$this->input->post("texto");
                
                //verifico que la tarea no de sea de BC (id_tipo_herramienta) por que en ese caso no se puede rechazar
                $this->load->model("gestion_riesgo/gr_tareas_model","gr_tareas",true);
                $tarea=$this->gr_tareas->dameTarea($id_tarea);
                if ($this->_verificoEditor($id_tarea,$id_usuario))
                {
                    //tomo id_tarea y cambio estado=3
                    $estado =10;
                    $this->load->model("gestion_riesgo/mejoracontinua_model","mejoracontinua",true);
                        if($this->mejoracontinua->updateGestion($id_tarea,$estado,$obs))
                        {
                            //inserto historial de accion
                            $id_accion=5;
                            $texto=$obs;
                            $this->grabar_accion($id_accion, $id_tarea, $texto);
                            $this->_preparar_enviar_mail($id_tarea,'observada');
                            echo 1; //todo OK

                        }
                        else
                            echo 2; //Error al insertar el registro
                }
                else 
                    echo 0;//Usuario sin permisos u operacion ya gestionada
            }
            else
                    echo -1; //No tiene permisos
	}
        public function hacer_obsoleta()
	{
            if ($this->permisos['Modificacion'])		
            {
                //Verifico que ese usuario sea de GR
                $id_usuario=$this->user['id'];
                
                $this->load->model("gestion_riesgo/gr_usuarios_model","gr_usuarios",true);
                $usuario_gr=$this->gr_usuarios->verificarSiPerteneceGr($this->user['id']);
                
//                if ($usuario_gr)--> Lo cambio para que solo Ricardo Luna pueda hacer obsoletas las tareas
                if ($id_usuario==39 || $id_usuario==18  || $this->user['id']==97 || $this->user['id']==101)
                {
                    $id_tarea =$this->input->post("id");
                    $obs   =$this->input->post("texto");
                    
                    $this->load->model("gestion_riesgo/mejoracontinua_model","mejoracontinua",true);
                    //copia en historial para guardar datos anteriores
                    $copia=$this->mejoracontinua->copy($id_tarea);
                            
                    //tomo id_tarea, cambio estado=8 y guardo el usuario y el texto
                    $estado =8;
                    if($this->mejoracontinua->setObsoleta($id_tarea,$id_usuario,$obs))
                    {
//                        $this->_preparar_enviar_mail($id_tarea,'obsoleta');
                        //guardo en el historia que la tarea fue enviada a obsoleta
                        $this->load->model('gestion_riesgo/gr_historial_model','gr_historial',true);
                        $datosHist['id_estado']=$estado; //estado Reabiert
                        $datosHist['id_tarea']=$id_tarea;
                        $datosHist['observacion']=$obs;
                        $this->gr_historial->insertObsoleto($datosHist);
                        
                        //inserto historial de accion
                        $id_accion=9;
                        $texto=$obs;
                        $this->grabar_accion($id_accion, $id_tarea, $texto);
                        
                        echo 1; //todo OK

                    }
                    else
                        echo 2; //Error al insertar el registro
                }
                else
                        echo -1; //No tiene permisos
                    
                }
	}
	
        function _enviarMail($datos)
        {
            $link='www.polidata.com.ar/sdj/admin';
                        
$mail_alta=<<<HTML
<html> 
    <head>
        <title>SMC</title>
        
    </head>
    <body>
    <div style="color:#000000; font-family:Verdana, Arial, Helvetica, sans-serif; text-align:Left; font-size:13px;">
        <pre>
            #persona,

            Usted fue #nominado por <b>#usuarioRtte</b> como responsable para resolver la siguiente tarea:
            
            <b>Descripci&oacute;n:</b>
            <em>#hallazgo</em>
            
            <b>Tarea:</b>
            <em>#tarea</em>
                        
            <b>Vencimiento:</b>
            #fecha_limite
            
            <b>Tipo de Herramienta:</b>
            #tipo_herramienta
            
            Puede confirmar su gesti&oacute;n o rechazarla ingresando al sistema desde <a href="#link" target="_blank">aqu&iacute;</a>, o copiando y pegando la siguiente direcci&oacute;n en su navegador web <b>Mozilla Firefox</b> o <b>Google Chrome</b>: 
            #link
            

            Atentamente
            #firma
            
            Importante: Mensaje generado autom&aacute;ticamente, por favor no responder.
            <hr>
              Para: #usuarioPara <br>
              CC  : #usuariosCC
           <hr>

        </pre>
        </div>
    </body>
</html>
HTML;

$mail_rechazo=<<<HTML
<html> 
    <head>
        <title>SMC</title>
        
    </head>
    <body>
    <div style="color:#000000; font-family:Verdana, Arial, Helvetica, sans-serif; text-align:Left; font-size:13px;">
        <pre>
            #persona,

            La tar&eacute;a de referencia que ust&eacute;d asigno a <b>#usuarioRtte</b>  fue rechazada por el usuario por el siguiente motivo:
            
            <b>Motivo del Rechazo:</b>
            <em>#motivo</em>
            
            <b>Descripci&oacute;n:</b>
            <em>#hallazgo</em>
            
            <b>Tarea:</b>
            <em>#tarea</em>
                        
            <b>Vencimiento:</b>
            #fecha_limite
            
            <b>Tipo de Herramienta:</b>
            #tipo_herramienta
            
            
            Puede editar y reabrirla ingresando al sistema desde <a href="#link" target="_blank">aqu&iacute;</a>, o copiando y pegando la siguiente direcci&oacute;n en su navegador web <b>Mozilla Firefox</b> o <b>Google Chrome</b>: 
            #link
            

            Atentamente
            #firma
            
            Importante: Mensaje generado autom&aacute;ticamente, por favor no responder.
            <hr>
              Para: #usuarioPara <br>
              CC  : #usuariosCC
           <hr>

        </pre>
        </div>
    </body>
</html>
HTML;
$mail_cerrada=<<<HTML
<html> 
    <head>
        <title>SMC</title>
        
    </head>
    <body>
    <div style="color:#000000; font-family:Verdana, Arial, Helvetica, sans-serif; text-align:Left; font-size:13px;">
        <pre>
            #persona,

            La tar&eacute;a de referencia que ust&eacute;d asigno a <b>#usuarioRtte</b> fue finalizada e informada por el usuario y se encuentra disponible para su aprobaci&oacute;n.
            
            <b>Fecha en que se informa:</b>
            #fecha_cierre
        
            <b>Descripci&oacute;n de la resoluci&oacute;n:</b>
            <em>#txt_cierre</em>
        
            
            <b>Tarea:</b>
            <em>#tarea</em>
            
            <b>Hallazgo:</b>
            <em>#hallazgo</em>
                        
            <b>Vencimiento:</b>
            #fecha_limite
            
            <b>Tipo de Herramienta:</b>
            #tipo_herramienta
            

            Atentamente
            #firma
            
            Importante: Mensaje generado autom&aacute;ticamente, por favor no responder.
            <hr>
              Para: #usuarioPara <br>
              CC  : #usuariosCC
           <hr>

        </pre>
        </div>
    </body>
</html>
HTML;
$mail_cerrada_cap=<<<HTML
<html> 
<head>
<title>SMC</title>

</head>
<body>
<div style="color:#000000; font-family:Verdana, Arial, Helvetica, sans-serif; text-align:Left; font-size:13px;">
<pre>
#persona,

La tar&eacute;a de referencia que ust&eacute;d asigno a <b>#usuarioRtte</b> fue finalizada e informada por el usuario y se encuentra disponible para su aprobaci&oacute;n.

<b>Fecha en que se informa:</b>
#fecha_cierre

<b>Descripci&oacute;n de la resoluci&oacute;n:</b>
<em>#txt_cierre</em>


<b>Tarea:</b>
<em>#tarea</em>

<b>Hallazgo:</b>
<em>#hallazgo</em>

<b>Vencimiento:</b>
#fecha_limite

<b>Tipo de Herramienta:</b>
#tipo_herramienta

<b>Personas capacitadas:</b>
#cuadro_cap


Atentamente
#firma

Importante: Mensaje generado autom&aacute;ticamente, por favor no responder.
<hr>
  Para: #usuarioPara <br>
  CC  : #usuariosCC
<hr>

</pre>
</div>
</body>
</html>
HTML;
$mail_aprobar=<<<HTML
<html> 
    <head>
        <title>SMC</title>
        
    </head>
    <body>
    <div style="color:#000000; font-family:Verdana, Arial, Helvetica, sans-serif; text-align:Left; font-size:13px;">
        <pre>
            #persona,

            La tar&eacute;a de referencia que ust&eacute;d informo a <b>#usuarioRtte</b> ha sido aprobada.
            
            <b>Fecha de aprobaci&oacute;n:</b>
            #fecha_aprobacion
        
            <b>Fecha en que se informo:</b>
            #fecha_cierre
        
            <b>Descripci&oacute;n de la resoluci&oacute;n:</b>
            <em>#txt_cierre</em>
        
            
            <b>Tarea:</b>
            <em>#tarea</em>
            
            <b>Hallazgo:</b>
            <em>#hallazgo</em>
                        
            <b>Vencimiento:</b>
            #fecha_limite
            
            <b>Tipo de Herramienta:</b>
            #tipo_herramienta
            

            Atentamente
            #firma
            
            Importante: Mensaje generado autom&aacute;ticamente, por favor no responder.
            <hr>
              Para: #usuarioPara <br>
              CC  : #usuariosCC
           <hr>

        </pre>
        </div>
    </body>
</html>
HTML;
$mail_observada=<<<HTML
<html> 
    <head>
        <title>SMC</title>
        
    </head>
    <body>
    <div style="color:#000000; font-family:Verdana, Arial, Helvetica, sans-serif; text-align:Left; font-size:13px;">
        <pre>
            #persona,

            La tar&eacute;a de referencia que ust&eacute;d informo a <b>#usuarioRtte</b> ha sido observada y devuelta.
            
            <b>Fecha de observaciòn:</b>
            #fecha_observacion
        
            <b>Motivo por el que se observa el informe de la tarea:</b>
            <em>#txt_obs</em>
        
            <b>Fecha en que se informo:</b>
            #fecha_cierre
        
            <b>Descripci&oacute;n de la resoluci&oacute;n:</b>
            <em>#txt_cierre</em>
        
            
            <b>Tarea:</b>
            <em>#tarea</em>
            
            <b>Hallazgo:</b>
            <em>#hallazgo</em>
                        
            <b>Vencimiento:</b>
            #fecha_limite
            
            <b>Tipo de Herramienta:</b>
            #tipo_herramienta
            

            Atentamente
            #firma
            
            Importante: Mensaje generado autom&aacute;ticamente, por favor no responder.
            <hr>
              Para: #usuarioPara <br>
              CC  : #usuariosCC
           <hr>

        </pre>
        </div>
    </body>
</html>
HTML;

            switch ($datos['accion']) 
            {
                case 'alta':
                    $cuerpo_mail=$mail_alta;
                    $subject='Nueva Tarea Nro: '.$datos['id_tarea'];
                    break;
                case 'editada':
                    $cuerpo_mail=$mail_alta;
                    $subject='Tarea Nro: '.$datos['id_tarea']. ' - Reabierta';
                    break;
                case 'rechazada':
                    $cuerpo_mail=$mail_rechazo;
                    $cuerpo_mail=str_replace("#motivo"        , $datos['motivo'] ,$cuerpo_mail);
                    $subject='Tarea Nro: '.$datos['id_tarea'].' - Rechazada';
                    break;
                case 'cerrada':
                    if($datos['id_tipo_herramienta']==9) //si el tipo de herramienta es 9=Capacitaciones
                    {
                        $this->load->model('cap/cap_participantes_model','cap_participantes',true);
                        $participantes=$this->cap_participantes->dameParticipantesPorTarea($datos['id_tarea']);
                        $tabla="";
                        if ($participantes!=0)
                        {
                            $tabla='<table  border="0">
                                <tr>
                                    <th style="width:20px;background-color:black;color:white"><b>#</b></th>
                                    <th style="width:200px;background-color:black;color:white"><b>Nombre y Apellido</b></th>
                                    <th style="width:120px;background-color:black;color:white"><b>Fecha Cap</b></th>
                                </tr>';
                            $n=0;
                            foreach ($participantes as $value)
                            {
                                $tabla.='<tr>
                                            <td style="width:20px;background-color:grey;color:white;text-align:center;font-size:11px">'.++$n.'</td>
                                            <td>'.$value['persona'].'</td>
                                            <td  style="text-align:center;">'.$value['fecha'].'</td>
                                        </tr>';
                            }
                            $tabla.='</table>';

                        }

                        $cuerpo_mail=$mail_cerrada_cap;
                        $subject='Tarea Nro: '.$datos['id_tarea'].' - Informada';
                        $cuerpo_mail=str_replace("#fecha_cierre"  , $datos['fecha_cierre']     ,$cuerpo_mail);
                        $cuerpo_mail=str_replace("#txt_cierre"    , $datos['txt_cierre']     ,$cuerpo_mail);
                        $cuerpo_mail=str_replace("#cuadro_cap"    , $tabla     ,$cuerpo_mail);
                        
                    }
                    else
                    {
                        $cuerpo_mail=$mail_cerrada;
                        $subject='Tarea Nro: '.$datos['id_tarea'].' - Informada';
                        $cuerpo_mail=str_replace("#fecha_cierre"  , $datos['fecha_cierre']     ,$cuerpo_mail);
                        $cuerpo_mail=str_replace("#txt_cierre"    , $datos['txt_cierre']     ,$cuerpo_mail);
                    }
                    break;
                case 'observada':
                    $cuerpo_mail=$mail_observada;
                    $subject='Tarea Nro: '.$datos['id_tarea'].' - Observada';
                    $cuerpo_mail=str_replace("#fecha_observacion"  , $datos['fecha_obs']     ,$cuerpo_mail);
                    $cuerpo_mail=str_replace("#fecha_cierre"  , $datos['fecha_cierre']     ,$cuerpo_mail);
                    $cuerpo_mail=str_replace("#txt_obs"     , $datos['txt_obs']     ,$cuerpo_mail);
                    $cuerpo_mail=str_replace("#txt_cierre"    , $datos['txt_cierre']     ,$cuerpo_mail);
                    break;
                case 'aprobada':
                    $cuerpo_mail=$mail_aprobar;
                    $subject='Tarea Nro: '.$datos['id_tarea'].' - Aprobada';
                    $cuerpo_mail=str_replace("#fecha_aprobacion"  , $datos['fecha_aprobacion']     ,$cuerpo_mail);
                    $cuerpo_mail=str_replace("#fecha_cierre"  , $datos['fecha_cierre']     ,$cuerpo_mail);
                    $cuerpo_mail=str_replace("#txt_cierre"    , $datos['txt_cierre']     ,$cuerpo_mail);
                    break;
                
            }
                 
             if ($datos['generoUsuarioPara']=='F')
             {
                $cuerpo_mail=str_replace("#persona"       , 'Estimada '.$datos['usuario'], $cuerpo_mail);
                $cuerpo_mail=str_replace("#nominado"       , 'nominada ' , $cuerpo_mail);
                 
             }
             else
             {
                $cuerpo_mail=str_replace("#persona"       , 'Estimado '.$datos['usuario']      , $cuerpo_mail);
                $cuerpo_mail=str_replace("#nominado"       , 'nominado ' , $cuerpo_mail);
                 
             }
             
             $cuerpo_mail=str_replace("#usuarioPara"   , $datos['usuarioPara']  , $cuerpo_mail);
             $cuerpo_mail=str_replace("#usuariosCC"    , implode(",",$datos['usuariosCC'])  , $cuerpo_mail);
             $cuerpo_mail=str_replace("#usuarioRtte"   , $datos['usuarioRtte']  , $cuerpo_mail);
             $cuerpo_mail=str_replace("#tarea"         , $datos['tarea']        ,$cuerpo_mail);
             $cuerpo_mail=str_replace("#hallazgo"      , $datos['hallazgo']     ,$cuerpo_mail);
             $cuerpo_mail=str_replace("#fecha_limite"  , $datos['fecha_limite'] ,$cuerpo_mail);
             $cuerpo_mail=str_replace("#tipo_herramienta"  , $datos['tipo_herramienta'] ,$cuerpo_mail);
             $cuerpo_mail=str_replace("#link"          , $link                  ,$cuerpo_mail);
             $cuerpo_mail=str_replace("#firma"         , htmlentities(NOMSIS,ENT_QUOTES,"UTF-8"),$cuerpo_mail);
             
             $this->load->library('email');
//             $this->email->clear();//para cuando uso un bucle
            $this->email->from(SYS_MAIL, NOMSIS);
            $this->email->message($cuerpo_mail);
//            $this->email->set_alt_message();//Sets the alternative email message body
//            $this->email->reply_to('gestiondedocumentos@salesdejujuy.com','DNS');
            $this->email->to($datos['usuarioPara']);
            $this->email->cc($datos['usuariosCC']);
            $this->email->subject($subject);
            IF (MAILBCC && MAILBCC !="")
                $this->email->bcc(MAILBCC);
            if($datos['adjuntos']!=0)
            {
                $n=0;
                $kb=0;
                foreach($datos['adjuntos'] as $archivo)
                {
                    $n++;
                    $kb+=$archivo['tam'];

                }
                if($n<=5 && $kb<=5120000)
                {
                    foreach($datos['adjuntos'] as $archivo)
                    {
                        $this->email->attach($archivo['archivo']);
                    }
                    
                }
            }
            if (ENPRODUCCION)
            {
                if($this->email->send())
                    return 0;
                else
                    return 2;
            }
            else
            {
//                    echo $cuerpo_mail;
//                    die();
                    return 0;
            }
//            $this->email->print_debugger();

        }
        
        public function enviar_manual($id)
        {
//            $accion='alta';
//            $accion='editada';
            $accion='cerrada';
//            $accion='observada';
//            $accion='rechazada';
            $this->_preparar_enviar_mail($id,$accion);
        }
        
        public function _preparar_enviar_mail($id_tarea="",$accion)
        {
            if($id_tarea!="")
            {
                
                $this->load->model('gestion_riesgo/gr_usuarios_model','gr_usuarios',true);
                $this->load->model('gestion_riesgo/gr_tareas_model','gr_tarea',true);

                //traigo todos los datos de la tarea
                $tarea=$this->gr_tarea->dameTarea($id_tarea);
//                echo "<pre>".print_r($tarea,true)."</pre>";die();

                //traigo todos los datos del usuario alta y del usuario responsable
                $usuarioAlta=$this->gr_usuarios->dameUsuarioPorId($tarea['usuario_alta']);
                $usuarioResp=$this->gr_usuarios->dameUsuarioPorId($tarea['usuario_responsable']);
//                echo "<pre>".print_r($usuarioAlta,true)."</pre>";
//                echo "<pre>".print_r($usuarioResp,true)."</pre>";
                $idSupervisor=$this->_dameSuperiorHabilitado($tarea['usuario_responsable']);
                
                if($idSupervisor==0)
                {
//                    $usuarioSup=''; 
                    $usuarioSup['email']='errorSuervisorUsuarioResp+smc@salesdejujuy.com';
                }
                else
                {
                    $usuarioSup=$this->gr_usuarios->dameUsuarioPorId($idSupervisor); 
                }
                
                $this->load->model('gestion_riesgo/gr_puestos_model','gr_puestos',true);

                $usuario=$this->user['nombre']." ".$this->user['apellido'];
                $datos=array();
                
                $datos['accion']=$accion;
                $datos['id_tarea']=$tarea['id_tarea'];
                $datos['tarea']=$tarea['tarea'];
                $datos['hallazgo']=$tarea['hallazgo'];
                $datos['fecha_limite']=$tarea['fecha_vto'];
                $datos['tipo_herramienta']=$tarea['th'];
                $datos['id_tipo_herramienta']=$tarea['id_tipo_herramienta'];
                $datos['adjuntos']=0;
                switch ($accion)
                {
                    case 'alta':
                    case 'editada':
                        $datos['usuario']=$usuarioResp['persona'];
                        $datos['usuarioPara']=$usuarioResp['email'];
                        $arrayMailsCc=array($usuarioAlta['email'],$usuarioSup['email']);
                        $arrayMailsCc=array_diff($arrayMailsCc,array($datos['usuarioPara']));
                        $datos['usuariosCC']=array_filter(array_unique($arrayMailsCc));
                        $datos['usuarioRtte']=$usuarioAlta['persona'];
                        $datos['generoUsuarioPara']=$usuarioResp['genero'];
                        $datos['motivo']="";
                        break;
                    case 'observada':
                        $this->load->model('gestion_riesgo/gr_cierres_model','gr_cierre',true);
                        $this->load->model('gestion_riesgo/gr_historial_acciones_model','gr_historial_acciones',true);
                        $cierre=$this->gr_cierre->dameCierrePorTarea($id_tarea);
                        $historial=$this->gr_historial_acciones->dameUltimaObservadaPorTarea($id_tarea);
                        $datos['usuario']=$usuarioResp['persona'];
                        $datos['usuarioPara']=$usuarioResp['email'];
                        $arrayMailsCc=array($usuarioAlta['email'],$usuarioSup['email']);
                        $arrayMailsCc=array_diff($arrayMailsCc,array($datos['usuarioPara']));
                        $datos['usuariosCC']=array_filter(array_unique($arrayMailsCc));
                        $datos['usuarioRtte']=$usuarioAlta['persona'];
                        $datos['generoUsuarioPara']=$usuarioResp['genero'];
                        $datos['motivo']="";
                        $datos['adjuntos']=0;
                        $datos['fecha_cierre']=$cierre['fecha_cierre'];
                        $datos['fecha_obs']=$historial['fecha_obs'];
                        $datos['txt_cierre']=$cierre['texto'];
                        $datos['txt_obs']=$historial['texto'];
                        break;
                    case 'aprobada':
                        $this->load->model('gestion_riesgo/gr_cierres_model','gr_cierre',true);
                        $cierre=$this->gr_cierre->dameCierrePorTarea($id_tarea);
                        $datos['usuario']=$usuarioResp['persona'];
                        $datos['usuarioPara']=$usuarioResp['email'];
                        $arrayMailsCc=array($usuarioAlta['email'],$usuarioSup['email']);
                        $arrayMailsCc=array_diff($arrayMailsCc,array($datos['usuarioPara']));
                        $datos['usuariosCC']=array_filter(array_unique($arrayMailsCc));
                        $datos['usuarioRtte']=$usuarioAlta['persona'];
                        $datos['generoUsuarioPara']=$usuarioResp['genero'];
                        $datos['motivo']="";
                        $datos['adjuntos']=0;
                        $datos['fecha_cierre']=$cierre['fecha_cierre'];
                        $datos['fecha_aprobacion']=$cierre['fecha_aprobacion'];
                        $datos['txt_cierre']=$cierre['texto'];
                        break;
                    case 'rechazada':
                    case 'cerrada':
                        $this->load->model('gestion_riesgo/gr_cierres_model','gr_cierre',true);
                        $cierre=$this->gr_cierre->dameCierrePorTarea($id_tarea);
                        $this->load->model('gestion_riesgo/gr_archivos_model','gr_archivos',true);
                        $archivos=$this->gr_archivos->dameListadoPorTareaParaMail($id_tarea);
                        
                        $datos['usuario']=$usuarioAlta['persona'];
                        $datos['usuarioPara']=$usuarioAlta['email'];
                        $arrayMailsCc=array($usuarioResp['email'],$usuarioSup['email']);
                        $arrayMailsCc=array_diff($arrayMailsCc,array($datos['usuarioPara']));
                        $datos['usuariosCC']=array_filter(array_unique($arrayMailsCc));
                        $datos['usuarioRtte']=$usuarioResp['persona'];
                        $datos['generoUsuarioPara']=$usuarioAlta['genero'];
                        $datos['motivo']=$tarea['observacion'];
                        $datos['adjuntos']=$archivos;
                        $datos['fecha_cierre']=$cierre['fecha_cierre'];
                        $datos['txt_cierre']=$cierre['texto'];
                        break;
//                    case 'editado':
//                        break;
                }
//                echo "<pre>".print_r($datos,true)."</pre>";die();
                $this->_enviarMail($datos);
            }
        }
        public function combo_herramientas()
        {
            $this->load->model("gestion_riesgo/gr_tipos_herramientas_model","gr_tipos_herramientas",true);
            echo $this->gr_tipos_herramientas->dameCombo();
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
        public function combo_estados()
        {
            $this->load->model("gestion_riesgo/gr_estados_model","gr_estados",true);
            $estados=array(1,2,3,4,9);
            echo $this->gr_estados->dameCombo($estados);
            
        }
        public function traer_tarea()
        {
            $id_tarea = $this->input->post("id_tarea");
            $this->load->model('gestion_riesgo/gr_tareas_model','gr_tarea',true);
            $tarea=$this->gr_tarea->dameTareaPanel($id_tarea);
            echo $tarea;
        }
        
        
        public function _test()
        {
            $id_tarea=24;
//            $accion='alta';
            $accion='rechazada';
            
            if($id_tarea!="")
            {
                $this->load->model('gestion_riesgo/gr_usuarios_model','gr_usuarios',true);
                $this->load->model('gestion_riesgo/gr_tareas_model','gr_tarea',true);

                //traigo todos los datos de la tarea
                $tarea=$this->gr_tarea->dameTarea($id_tarea);
    //            echo "<pre>".print_r($tarea,true)."</pre>";

                //traigo todos los datos del usuario alta y del usuario responsable
                $usuarioAlta=$this->gr_usuarios->dameUsuarioPorId($tarea['usuario_alta']);
                $usuarioResp=$this->gr_usuarios->dameUsuarioPorId($tarea['usuario_responsable']);
    //            echo "<pre>".print_r($usuarioAlta,true)."</pre>";
    //            echo "<pre>".print_r($usuarioResp,true)."</pre>";


                $id_puesto_superior=$usuarioResp['id_puesto_superior'];
                $destinatariosId=array();
                $puestosId=array();

                $this->load->model('gestion_riesgo/gr_puestos_model','gr_puestos',true);

                //Subo por el organigrama tomando todos los id_puesto de los superiores
                do
                {
                    $puestosId[]=$id_puesto_superior;
                    $id_puesto_superior=$this->gr_puestos->damePuestoSuperior($id_puesto_superior);

                }while ($id_puesto_superior!=0);
                
                //agrego id del usuario que dio de alta para que este incluido en copia así no lo repte en caso de que pertenezca a la cadena de mando
                //Omito este paso en el caso de que corresponsa a una tarea rechazada ya qu ese invierten los destinatarios
                if($accion!='rechazada')
                {
                    $puestosId[]=$usuarioAlta['id_puesto'];
                    $idUsuarioNot=$usuarioResp['id_usuario'];
                }
                else
                {
                    $puestosId[]=$usuarioResp['id_puesto'];
                    $idUsuarioNot=$usuarioAlta['id_usuario'];
                    
                }

                $superiores=$this->gr_usuarios->dameUsuariosPorPuestos($puestosId,$idUsuarioNot);

    //            echo "<pre>".print_r($superiores,true)."</pre>";

                $usuariosCC="";
                $n=0;
                foreach($superiores as $superior)
                {
                    if ($n==0)
                    {
                        if($superior['mailing']==1)
                            $usuariosCC.=$superior['email'];
                    }
                    else
                    {
                        if($superior['mailing']==1)
                            $usuariosCC.=", ".$superior['email'];
                    }
                    $n++;
                }

                $usuario=$this->user['nombre']." ".$this->user['apellido'];
                $datos=array();
                $datos['accion']=$accion;
                $datos['id_tarea']=$tarea['id_tarea'];
                $datos['tarea']=$tarea['tarea'];
                $datos['hallazgo']=$tarea['hallazgo'];
                $datos['fecha_limite']=$tarea['fecha_vto'];
                switch ($accion)
                {
                    case 'alta':
                        $datos['usuario']=$usuarioResp['persona'];
                        $datos['usuariosCC']=$usuariosCC;
                        $datos['usuarioPara']=$usuarioResp['email'];
                        $datos['usuarioRtte']=$usuarioAlta['persona'];
                         $datos['motivo']="";
                        break;
                    case 'rechazada':
                        $datos['usuario']=$usuarioAlta['persona'];
                        $datos['usuariosCC']=$usuariosCC;
                        $datos['usuarioPara']=$usuarioAlta['email'];
                        $datos['usuarioRtte']=$usuarioResp['persona'];
                         $datos['motivo']=$tarea['observacion'];
                        break;
                    case 'editado':
                        break;
                }

                
//                echo "<pre>".print_r($datos,true)."</pre>";
                $this->_enviarMail($datos);
            }
        }
        public function _test2($id_usuario)
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
                    
            echo "<pre>".print_r($areas,true)."</pre>";
            
            
        }
        public function _test3($id_tarea)
        {
            $this->load->model("gestion_riesgo/mejoracontinua_model","mejoracontinua",true);
            $copia=$this->mejoracontinua->copy($id_tarea);
            echo $copia;
        }
        public function reporte1()
        {
            $this->load->model('reports_model','reports',true);
            $xml=$this->reports->sp_prueba1();
            echo $xml;
                       
        }
        public function propagarBc($id_tarea)
	{
            $this->load->model('gestion_riesgo/gr_tareas_model','gr_tareas',true);
            $tarea=$this->gr_tareas->dameTarea($id_tarea);
//            echo "<pre>".print_r($tarea,true)."</pre>";
            $this->load->model("gestion_riesgo/gr_bc_model","gr_bc",true);
            $bc=$this->gr_bc->dameBc($tarea['id_herramienta']);
//            echo "<pre>bc".print_r($bc,true)."</pre>";
            
            //verifico que el estado de la BC sea en curso:
            if ($bc['id_estado']==2)
            {
                
                $this->load->model("gestion_riesgo/gr_usuarios_model","gr_usuarios",true);
                $usuario=$this->gr_usuarios->dameUsuarioPorId($tarea['usuario_relacionado']);
                $directReports=$this->gr_usuarios->dameDirectReports($usuario['id_puesto']);

//                echo "<pre>usuario".print_r($usuario,true)."</pre>";
//                echo "<pre>directReports".print_r($directReports,true)."</pre>";
//
//                echo "q->".count($directReports);

//                if(count($directReports)==$usuario['q_dr'] && count($directReports)>0 && $usuario['q_dr']>0)
//                if(count($directReports)>0)
                if(!empty ($directReports))
                {
                    $this->load->model("gestion_riesgo/gr_tareas_model","gr_tareas",true);
                    foreach ($directReports as $destinatario)
                    {
                        //Datos para alta de Tarea
                        $datos=array();
                        $datos['usuario_alta']          =$tarea['usuario_responsable'];
                        $datos['usuario_relacionado']   =$destinatario['id_usuario'];
                        $datos['id_tipo_herramienta']   =3;// Bajada en cascada
                        $datos['id_herramienta']        =$bc['id_bc'];// Bajada en cascada
                        $datos['usuario_responsable']   =$tarea['usuario_relacionado'];
                        $datos['hallazgo']              =$bc['descripcion'];
                        $datos['tarea']                 ='Bajar en cascada lo descripto a '.$destinatario['nomape']." (".$destinatario['puesto'].").";
                        $datos['fecha_vto']             =$bc['fecha_vto_n'];
                        $datos['id_estado']             =1;
                        $datos['id_tarea_padre']        =$tarea['id_tarea'];
//                        echo "<pre>".print_r($datos,true)."</pre>";
                        $insert=$this->gr_tareas->insert($datos);

                    }
                    sleep(3);
                    $this->_enviarMailPropagaBc($id_tarea);
                    sleep(3);
                    return 1;

                }
                else
                    return 0; //El usuario no tiene Direct Reports
            }
            else
                return 0;
            
	}
        public function enviarManualMailPropagaBc($id_tarea)
        {
            $acc=$this->_enviarMailPropagaBc($id_tarea);
            echo $acc;
        }
        function _enviarMailPropagaBc($id_tarea)
        {
             $this->load->model('gestion_riesgo/gr_usuarios_model','gr_usuarios',true);
            $this->load->model('gestion_riesgo/gr_bc_model','gr_bc',true);
            $this->load->model('gestion_riesgo/gr_areas_model','gr_areas',true);
            $this->load->model('gestion_riesgo/gr_tareas_model','gr_tareas',true);
            
            $tarea=$this->gr_tareas->dameTarea($id_tarea); //--->corresponde a la tarea que se esta cerrando
//            echo "<pre>".print_r($tarea,true)."</pre>";
            
            $usuarioAltaTarea=$this->gr_usuarios->dameUsuarioPorId($tarea['usuario_alta']);
            
            $id_bc=$tarea['id_herramienta'];
            $bc=$this->gr_bc->dameBc($tarea['id_herramienta']);
//            echo "<pre>".print_r($bc,true)."</pre>";
            
            $usuarioAltaBc=$this->gr_usuarios->dameUsuarioPorId($bc['id_usuario_alta']);
//            echo "<pre>Usuario Alta".print_r($usuarioAltaBc,true)."</pre>";
            $usuarioPara=$this->gr_usuarios->dameUsuarioPorId($tarea['usuario_relacionado']);
//            echo "<pre>Usuario Inicio".print_r($usuarioPara,true)."</pre>";
            
            $usuarioInicioBc=$this->gr_usuarios->dameUsuarioPorId($bc['id_usuario_inicio']);
//            echo "<pre>Usuario Inicio".print_r($usuarioPara,true)."</pre>";
            
            $usuarioAprobador=$this->gr_usuarios->dameUsuarioPorId($bc['id_usuario_gr']);
//            echo "<pre>Usuario Aprobador".print_r($usuarioInicio,true)."</pre>";
            $usuarioResponsable=$this->gr_usuarios->dameUsuarioPorId($tarea['usuario_responsable']);
//            echo "<pre>Usuario Responsable".print_r($usuarioResponsable,true)."</pre>";
//            echo "<pre>".print_r($usuariosCC,true)."</pre>";
            
            $directReports=$this->gr_usuarios->dameDirectReports($usuarioPara['id_puesto']);
            //solo continúo si el usuario tiene personal a cargo
            if(!empty($directReports))
            {
                //Busco todos los superiores del usuario_bc quien va a ser el responsable de la nueva tarea
                $id_puesto_superior=$usuarioPara['id_puesto_superior'];
                $usuariosCC=array();
                if($id_puesto_superior!="")
                {
                    $destinatariosId=array();
                    $puestosId=array();

                    $this->load->model('gestion_riesgo/gr_puestos_model','gr_puestos',true);

                    //Subo por el organigrama tomando todos los id_puesto de los superiores
                    do
                    {
                        $puestosId[]=$id_puesto_superior;
                        $id_puesto_superior=$this->gr_puestos->damePuestoSuperior($id_puesto_superior);

                    }while ($id_puesto_superior!=0);

                    //agrego id del usuario que dio de alta para que este incluido en copia así no lo repte en caso de que pertenezca a la cadena de mando
                    $puestosId[]=$usuarioAltaBc['id_puesto'];
                    $idUsuarioNot=$usuarioResponsable['id_usuario'];

                    $superiores=$this->gr_usuarios->dameUsuariosPorPuestos($puestosId,$idUsuarioNot);

        //            echo "<pre>".print_r($superiores,true)."</pre>";

                    foreach($superiores as $superior)
                    {
                        if($superior['mailing']==1)
                            $usuariosCC[]=$superior['email'];
                    }
                }
                else
                {
                    if (MAILS_ALWAYS_CC!="")
                        $usuariosCC=array(MAILS_ALWAYS_CC);
                    else
                        $usuariosCC=array();
                }
                
                //invierto el orden del array para que quede mejor ordenado
                $usuariosCC=array_reverse($usuariosCC);
                
                if ($usuarioPara['q_dr']==1)
                    $listDDRR="La siguiente tarea fue generadas para que usted confirme (cierre) o rechace (no aplica) seg&uacute;n su Direct Report:<br>";
                else    
                    $listDDRR="Las siguientes tareas fueron generadas para que usted confirme (cierre) o rechace (no aplica) seg&uacute;n cada uno de sus Direct Reports:<br>";
                $tab=  str_repeat("&nbsp;", 20);
                $div1='<span style="color:#122A0A; font-size:13px;">';
                $div2='<span style="color:#585858; font-size:11px;">';
                $div3='<span style="color:#045FB4; font-size:13px; text-align:right; width:100px">';

                //Busco las tareas de cada DR y armo una lista
                foreach ($directReports as &$directReport)
                {
                    $tareaNro=$this->gr_tareas->dameTareaPorBcYDR($id_bc,$directReport['id_usuario']);
                    $directReport['id_tarea']=$tareaNro;
                    $line="";
                    $line.=$div3.$tab."&rarr;Tarea Nro: ".$tareaNro."-&gt; </span>";
                    $line.=$div1."<b>".$directReport['nomape'].'</b></span>';
                    $line.=$div2.' ('.$directReport['puesto'].")</span>";
                    $listDDRR.=$line."<br>";
                }
                unset ($directReport);
    //            echo "<pre>DiectReports:".print_r($directReports,true)."</pre>";
    //            echo "<pre>ListDR:".print_r($listDDRR,true)."</pre>";

                //busco todas las areas dependientes de GR
                $areasInferiores[]=4; //Gerencia Corportavida Gestión de Riesgo
                for($i=0;$i<1;$i++)
                {
                    $subareasGr=$this->gr_areas->dameAreasInferiores($areasInferiores);                
                    if (count($subareasGr)>1)
                    {
                        foreach ($subareasGr as $area)
                        {
                            $arrayAreas[]=$area['id_area'];
                        }
                        $areasInferiores= array_unique(array_merge($areasInferiores,$arrayAreas));
                    }
                }
                $usuariosGr=$this->gr_usuarios->dameUsuariosGr($areasInferiores);
    //            echo "<pre>".print_r($usuariosGr,true)."</pre>";
                $arrayEmailsGr=array();
                foreach ($usuariosGr as $email)
                {
                    if($email['mailing']==1)
                        $arrayEmailsGr[]=$email['email'];
                }

                $datos=array();
                $guardarMail=array();
                $datos['id_bc']                 =$bc['id_bc'];
                $datos['bc_nro']                =3000000+$bc['id_bc'];
                $datos['asunto']                ='Propagar BC Nro: '.$datos['bc_nro'];
                $datos['usuario']               =$usuarioPara['persona'];
                $datos['descipcion']            =$bc['descripcion'];
                $datos['listaDDRR']             =$listDDRR;
                $datos['usuarioAlta']           =$usuarioAltaBc['persona']." (".$usuarioAltaBc['puesto'].")";
                $datos['usuarioInicioBc']       =$usuarioInicioBc['persona']." (".$usuarioInicioBc['puesto'].")";
                $datos['usuarioAprobador']      =$usuarioAprobador['persona']." (".$usuarioAprobador['puesto'].")";
                $datos['usuarioResponsable']    =$usuarioResponsable['persona'];
                $datos['generoUsuarioInicio']   =$usuarioPara['genero'];
                $datos['usuarioPara']           =$usuarioPara['email'];
                $datos['usuarioRtte']           =$usuarioResponsable['email'];

                if (!empty($usuariosCC))
                    $datos['usuariosCC']    = array_merge($usuariosCC,$arrayEmailsGr);
                else
                    $datos['usuariosCC']    = $arrayEmailsGr;
                
                if(in_array($datos['usuarioPara'], $datos['usuariosCC']))
                    unset ($datos['usuariosCC'][$datos['usuarioPara']]);
                if (!in_array($datos['usuarioRtte'],$datos['usuariosCC']) && $datos['usuarioRtte']!=$datos['usuarioPara'])
                    array_push($datos['usuariosCC'],$datos['usuarioRtte']);
                
                $datos['usuariosCC']    = array_unique($datos['usuariosCC']);
    //            echo "<pre>Datos:".print_r($datos,true)."</pre>";

                $link='www.polidata.com.ar/sdj/admin';

$cuerpo_mail=<<<HTML
<html> 
    <head>
        <title>SMC</title>

    </head>
    <body>
    <div style="color:#000000; font-family:Verdana, Arial, Helvetica, sans-serif; text-align:Left; font-size:13px;">
        <pre>
            #persona,

            Ya puede continuar con la Bajada en cascada recibida de #usuarioResponsable

            <b>BC Nro:</b>#bcNro
            <b>Usuario Alta:</b>#usuarioAlta
            <b>Aprobada por:</b>#usuarioGrAprobador            
            <b>Iniciada en:</b>#usuarioInicioBc            
            <b>Descripci&oacute;n:</b>
            <em>#descipcionBc</em>

            #listaDDRR

            Puede continuar su gesti&oacute;n ingresando al sistema desde <a href="#link" target="_blank">aqu&iacute;</a>, o copiando y pegando la siguiente direcci&oacute;n en su navegador web <b>Mozilla Firefox</b> o <b>Google Chrome</b>: 
            #link

            Atentamente
            #firma

            Importante: Mensaje generado autom&aacute;ticamente, por favor no responder.
            <hr>
            <code>
            Para: #txt_usuarioPara <br>
            CC  : #txt_usuariosCC
            </code>
        <hr>

        </pre>
        </div>
    </body>
</html>
HTML;
                if ($datos['generoUsuarioInicio']=='F')
                {
                    $cuerpo_mail=str_replace("#persona"       , 'Estimada '.$datos['usuario'], $cuerpo_mail);
                    $cuerpo_mail=str_replace("#propuesto/a"       , 'propuesta' , $cuerpo_mail);

                }
                else
                {
                    $cuerpo_mail=str_replace("#persona"       , 'Estimado '.$datos['usuario']      , $cuerpo_mail);
                    $cuerpo_mail=str_replace("#propuesto/a"       , 'propuesto' , $cuerpo_mail);

                }

                $subject=$datos['asunto'];
                $cuerpo_mail=str_replace("#descipcionBc"       ,$datos['descipcion'], $cuerpo_mail);
                $cuerpo_mail=str_replace("#listaDDRR"          ,$datos['listaDDRR'], $cuerpo_mail);
                $cuerpo_mail=str_replace("#bcNro"              ,$datos['bc_nro'], $cuerpo_mail);
                $cuerpo_mail=str_replace("#usuarioAlta"        , $datos['usuarioAlta']  , $cuerpo_mail);
                $cuerpo_mail=str_replace("#usuarioGrAprobador" , $datos['usuarioAprobador']  , $cuerpo_mail);
                $cuerpo_mail=str_replace("#usuarioInicioBc"    , $datos['usuarioInicioBc']  , $cuerpo_mail);
                $cuerpo_mail=str_replace("#usuarioResponsable" , $datos['usuarioResponsable']  , $cuerpo_mail);
                $cuerpo_mail=str_replace("#txt_usuarioPara"    , $datos['usuarioPara']  , $cuerpo_mail);
                $cuerpo_mail=str_replace("#txt_usuariosCC"     , implode(", ",$datos['usuariosCC'])   , $cuerpo_mail);
                $cuerpo_mail=str_replace("#usuarioRtte"        , $datos['usuarioRtte']  , $cuerpo_mail);
                $cuerpo_mail=str_replace("#link"               , $link                  ,$cuerpo_mail);
                $cuerpo_mail=str_replace("#firma"              , htmlentities(NOMSIS,ENT_QUOTES,"UTF-8"),$cuerpo_mail);

                $this->load->library('email');
                $guardarMail['id_origen']=$id_bc;
                $guardarMail['origen']='BC/enviarMailInicioBc';
    //             $this->email->clear();//para cuando uso un bucle
                $this->email->from(SYS_MAIL, NOMSIS);
                $this->email->message($cuerpo_mail);
                $guardarMail['texto']=$cuerpo_mail;
    //            $this->email->set_alt_message();//Sets the alternative email message body
    //            $this->email->reply_to('gestiondedocumentos@salesdejujuy.com','DNS');
                $this->email->to($datos['usuarioPara']);
                $guardarMail['para']=$datos['usuarioPara'];
                $this->email->cc($datos['usuariosCC']);
                $guardarMail['cc']=$datos['usuariosCC'];
                $this->email->subject($subject);
                $guardarMail['asunto']=$subject;
                IF (MAILBCC && MAILBCC !="")
                    $this->email->bcc(MAILBCC);
                $guardarMail['cco']='fmoraiz@gmail.com';
//                echo $cuerpo_mail;die();

                $this->load->model('mailing_model','mailing',true);
                $guardar=$this->mailing->insert($guardarMail);

    //            $this->email->send();
                if (ENPRODUCCION)
                {
                    if($this->email->send())
                        return 0;
                    else
                        return 2;
                }
                else
                {
                        return -1;
                }
    //            echo $this->email->print_debugger();
            }
            else
                    return -1;

        }
//         function actualizarAvanceBc($id_bc)
//        {
//            $datos['alcance']=0;
//            $datos['status']=0;
//            $this->load->model('gestion_riesgo/gr_bc_model','gr_bc',true);
//            $bc=$this->gr_bc->dameBc($id_bc);
//            $this->_calcularProfundidadBC($id_bc,$bc['id_usuario_inicio'],$datos);
//            
//            if ($datos['alcance']==$datos['status'] && ($datos['alcance']+$datos['status'])!=0)
//                $datos['id_estado']=4; //si se cumplio la BC cambio el estado a terminada
//            $this->gr_bc->updateStatus($id_bc,$datos);
//        }
        function _calcularProfundidadBC($id_bc,$id_usuario_inicio,&$contadores)
        {
            global $contador;
            $this->load->model('gestion_riesgo/gr_bc_model','gr_bc',true);
            $personas=$this->gr_bc->dameUsuariosInferiores($id_bc,$id_usuario_inicio);
            foreach ($personas as $key=>$value)
            {
                
                if ($value['id_estado']!=6)
                {
                    $contadores['alcance']++;
                    if ($value['id_estado']==2)
                        $contadores['status']++;
                    if ($value['q_dr']!=0)
                        $this->_calcularProfundidadBC($id_bc,$value['id_usuario'],$contadores);
                }
                
            }
            
        }
        function excel()
        {
            $id_usuario=$this->user['id'];
            $usuario=$this->_dameAreas($id_usuario);
//             echo "<pre>Datos:".print_r($usuario,true)."</pre>";die();
            $sort = $this->input->post("sort");
//             echo "<pre>Datos:".print_r($sort,true)."</pre>";die();
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
            $busqueda  = ($this->input->post("busqueda"))?$this->input->post("busqueda"):"";
            $campos="";
            if ($this->input->post("campos"))
            {
                $campos = str_replace('"', "", $this->input->post("campos"));
                $campos = str_replace('[', "", $campos);
                $campos = str_replace(']', "", $campos);
                $campos = explode(",",$campos);
//                            echo "<pre>".print_r($campos,true)."</pre>";
            }
            $this->load->model('gestion_riesgo/mejoracontinua_model','mejoracontinua',true);
            $listado = $this->mejoracontinua->listado_excel($usuario,$filtros, $busqueda, $campos,$sort,$dir);
            
            $datos_audit['id_modulo']=$this->modulo;
            $datos_audit['id_usuario']=$this->user['id'];
            $datos_audit['q_registros']=count($listado);
            $params=array($usuario,$filtros, $busqueda, $campos,$sort,$dir);
            $datos_audit['params']=json_encode($params);

            $this->load->model("auditoria_model","auditoria_model",true);
            $this->auditoria_model->guardarPedidoExcel($datos_audit);
//             echo "<pre>Datos:".print_r($listado,true)."</pre>";
//             die();
                        
            $filename = 'Listado - Tareas - '.date("YmdHis").'.xls';
            header('Content-Type:application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$filename.'"');
            header('Cache-Control: max-age=0');
            
                
            $this->load->library('excel');
            // Se asignan las propiedades del libro
            $this->excel->getProperties()
                ->setCreator(NOMSIS) // Nombre del autor
                ->setLastModifiedBy(NOMSIS) //Ultimo usuario que lo modificó
                ->setTitle($filename) // Titulo
                ->setSubject($filename) //Asunto
                ->setDescription('Listado desde grilla - v1.0 - 20180813') //Descripción
                ->setKeywords('') //Etiquetas
                ->setCategory(''); //Categorias
            $setActiveSheetIndex=$this->excel->setActiveSheetIndex(0);
            
             $this->excel->getActiveSheet()->setTitle('Listado - Tareas.xls');
            
              $estiloTituloReporte = array(
                'font' => array(
                'name'      => 'Calibri',
                'bold'      => true,
                'italic'    => false,
                'strike'    => false,
                'size'      =>16,
                'color'     => array(
                    'rgb' => '000000'
                    )
                ),
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                        'rotation' => 0,
                        'wrap' => TRUE
                    )
            );
             $estiloFilaTitulos = array(
                    'font' => array(
                        'name'      => 'Calibri',
                        'bold'      => true,
                        'italic'    => false,
                        'strike'    => false,
                        'size' =>10,
                        'color'     => array(
                            'rgb' => 'FFFFFF'
                        )
                    ),
                    'fill' => array(
                        'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array(
                            'rgb' => 'EED693')//ese color ya esta OK
                    ),
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_NONE
                        )
                    ),
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                        'rotation' => 0,
                        'wrap' => FALSE
                    )
                );
             $estiloFilaPar = array(
                    'font' => array(
                        'name'      => 'Calibri',
                        'bold'      => false,
                        'italic'    => false,
                        'strike'    => false,
                        'size' =>9,
                        'color'     => array(
                            'rgb' => '000000'
                        )
                    ),
                    'fill' => array(
                        'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array(
                            'rgb' => 'FFFFCC')//ese color ya esta OK
                    ),
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_NONE
                        )
                    ),
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                        'rotation' => 0,
                        'wrap' => FALSE
                    )
                );
             $estiloFilaImpar = array(
                    'font' => array(
                        'name'      => 'Calibri',
                        'bold'      => false,
                        'italic'    => false,
                        'strike'    => false,
                        'size' =>9,
                        'color'     => array(
                            'rgb' => '000000'
                        )
                    ),
                    'fill' => array(
                        'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array(
                            'rgb' => 'E5FFCC')//ese color ya esta OK
                    ),
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_NONE
                        )
                    ),
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                        'rotation' => 0,
                        'wrap' => FALSE
                    )
                );
            
            $setActiveSheetIndex->setCellValue('A1',"Listado de Tareas");
            $setActiveSheetIndex->mergeCells('A1:M1');
            $this->excel->getActiveSheet()->getStyle('A1')->applyFromArray($estiloTituloReporte);
            $setActiveSheetIndex->setCellValue('A2',"Fecha y hora: ");
            $setActiveSheetIndex->mergeCells('A2:B2');
            $setActiveSheetIndex->setCellValue('C2',  date('d-m-Y H:i:s'));
            $setActiveSheetIndex->setCellValue('A3',"Descargado por: ");
            $setActiveSheetIndex->mergeCells('A3:B3');
            $setActiveSheetIndex->setCellValue('C3',$usuario['usuario']['persona']);

            $maxCol=count($listado[0]);
            $maxFil=count($listado);
            //defino celda superior derecha de la tabla de datos
            $iniFil=5;
            $iniCol=0;
            //atributos
            $col=0;
            $fil=0;
            
            
            foreach ($listado as $clave => $arreglo) {
                
                if ($fil==0)
                {
                    foreach ($arreglo as $key => $value) {

                        $columna=PHPExcel_Cell::stringFromColumnIndex($iniCol+$col);
                        $filaAtributo=(string)$iniFil+$fil;
                        $filaValor=(string)$iniFil+$fil+1;
                        $celdaAtributo=$columna.$filaAtributo;
                        $celdaValor=$columna.$filaValor;
                        $setActiveSheetIndex->setCellValue($celdaAtributo,$key);
                        $setActiveSheetIndex->setCellValue($celdaValor,$value);
                        $this->excel->getActiveSheet()->getStyle($celdaAtributo)->applyFromArray($estiloFilaTitulos);
                        $this->excel->getActiveSheet()->getStyle($celdaValor)->applyFromArray($estiloFilaImpar);
                        
                        
                        
                        $col++;
                    }
                    $col=0;
                    $fil=$fil+2;
                    
                }
                else
                {
                    foreach ($arreglo as $key => $value) {

                        $columna=PHPExcel_Cell::stringFromColumnIndex($iniCol+$col);
                        $fila=(string)$iniFil+$fil;
                        $celda=$columna.$fila;
                        $setActiveSheetIndex->setCellValue($celda,$value);
                        
                        if (($fil % 2)==0){
                            $this->excel->getActiveSheet()->getStyle($celda)->applyFromArray($estiloFilaPar);
                        }else{
                            $this->excel->getActiveSheet()->getStyle($celda)->applyFromArray($estiloFilaImpar);
                        }
                        $anchoEstandarMaximo=30;
                        $anchoEstandarMinimo=12;
                        $anchoTexto=strlen($value);
                        
                        if($anchoTexto>$anchoEstandarMaximo) 
                            $setActiveSheetIndex->getColumnDimension($columna)->setWidth($anchoEstandarMaximo);
                        elseif ($anchoTexto<$anchoEstandarMinimo) 
                            $setActiveSheetIndex->getColumnDimension($columna)->setWidth($anchoEstandarMinimo);
                        else
                            $setActiveSheetIndex->getColumnDimension($columna)->setWidth($anchoTexto);
                        
                        $col++;

                    }
                    $col=0;
                    $fil=$fil+1;
                }
            }
            $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
            $objWriter->save('php://output');
        }
        public function delete_archivo_cerrar()
	{
            if ($this->permisos['Alta'])		
            {
                $datos=array();
                $id_archivo =$this->input->post("id");
                $id_tarea =$this->input->post("id_tarea");
                
                //verifico que el usaurio que esta eliminando el archivo sea el responsable
                $this->load->model('gestion_riesgo/gr_tareas_model','gr_tarea',true);
                $responsable=$this->gr_tarea->verificarResponsable($id_tarea,$this->user['id']);
                
                if($responsable)
                {
//                    //elimino el archivo
                         $this->load->model("gestion_riesgo/gr_archivos_model","gr_archivos",true);
                         $delete=$this->gr_archivos->delete($id_archivo);
                        echo 1;
                    
                    
                }
                else
                    echo 0;//No es el responsable
                
                
                
            }
            else
                    echo -1; //No tiene permisos
	}
        public function delete_archivos_cerrar()
	{
            if ($this->permisos['Alta'])		
            {
                $datos=array();
                $id_archivo =$this->input->post("id");
                $id_tarea =$this->input->post("id_tarea");
                
                //verifico que el usaurio que esta eliminando el archivo sea el responsable
                $this->load->model('gestion_riesgo/gr_tareas_model','gr_tarea',true);
                $responsable=$this->gr_tarea->verificarResponsable($id_tarea,$this->user['id']);
                
                if($responsable)
                {
//                    //elimino el archivo
                         $this->load->model("gestion_riesgo/gr_archivos_model","gr_archivos",true);
                         $delete=$this->gr_archivos->deleteAll($id_tarea);
                        echo 1;
                    
                    
                }
                else
                    echo 0;//No es el responsable
                
                
                
            }
            else
                    echo -1; //No tiene permisos
	}
        public function grabar_accion($id_accion,$id_tarea,$texto){
            $this->load->model("gestion_riesgo/gr_historial_acciones_model","gr_historial_acciones",true);
            
            $datos['id_usuario']    =$this->user['id'];
            $datos['id_tarea']      =$id_tarea;
            $datos['id_accion']     =$id_accion;
            $datos['texto']         =$texto;
            
            $insert_id=$this->gr_historial_acciones->insert($datos);
            return $insert_id;
            
        }
        
    public function dato_revision ()
    {
        $id_tarea=$this->input->post("id");
     
        $this->load->model('gestion_riesgo/mejoracontinua_model','mejoracontinua',true);
        $revision=$this->mejoracontinua->dameRevisionParaForm($id_tarea);
//            echo "<pre>" . print_r($criticidad, true) . "</pre>";die();
        
        echo $revision;	
    }
    
    public function update_revision ()
    {
        $id_accion = 1;
        $this->load->model('sys_permiso_acciones_model','sys_permiso_acciones',true);
        $permiso_acciones = $this->sys_permiso_acciones->damePermisoAcciones($this->modulo,$this->user['id'],$id_accion);
//            $variables['permiso_permiso'] = $this->permisos_model->checkIn($this->user['perfil_id'],$this->modulo);
        if($permiso_acciones["id_accion"] != 0)
        {
            if ($this->permisos['Modificacion'])		
            {
                $id_tarea       = $this->input->post("id_tarea");
                $datos["rpd"]   = $this->input->post("rpd");
    //            echo $datos["rpd"];
                if($datos["rpd"] != "" )
                {
                    $this->load->model('gestion_riesgo/mejoracontinua_model','mejoracontinua',true);
                    $update=$this->mejoracontinua->update($id_tarea,$datos);
                    if($update)
                    {
                        $id_accion=10;
                        $texto="Edito revisión para la dirección";
                        $this->grabar_accion($id_accion, $id_tarea, $texto);
                        echo 1;
                    }
                    else
                        echo 4;
                }
                else         
                    echo 2;
            }
            else
                echo 3;
        }
        else
            echo 3;
    }
     function _dameSuperiorHabilitado($id_usuario)
    {
        $this->load->model('usuarios_model','usuarios',true);
        $idSupervisor=$this->usuarios->dameIdUsuarioSuperior($id_usuario);
        if($idSupervisor != 0)
        {
            $checkHabilitado=$this->usuarios->checkUsuarioHabilitado($idSupervisor);
            if($checkHabilitado)
                return $idSupervisor;
            else
                $this->_dameSuperiorHabilitado($idSupervisor);
        }
        else
            return 0;
    }
}
?>