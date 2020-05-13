<?php
class Rmc extends CI_Controller
{
    private $modulo=22;
    private $user;
    private $permisos;
    private $origen;
    
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
            $variables['btn_tareas'] = $this->user['id'];
            $usuario=$this->_dameAreas($this->user['id']);
            $variables['btn_gr'] = ($usuario['usuario']['gr']==1)?1:0;
//             echo "<pre>".print_r($variables,true)."</pre>";die();
            $this->load->view('gestion_riesgo/rmc/listado',$variables);
	}
	
	public function listado()
	{
		if ($this->permisos['Listar'])
                {
                    $id_usuario=$this->user['id'];
                    $listado=array();
                    $this->load->model('gestion_riesgo/gr_rmc_model','gr_rmc',true);
                    $start = $this->input->post("start");
                    $limit = $this->input->post("limit");
                    $sort = $this->input->post("sort");
                    $dir = $this->input->post("dir");
                    
                    if($this->input->post("filtros"))
                    {
                        $filtros=json_decode($this->input->post("filtros"));
//                        echo "<pre>".print_r($filtros,true)."</pre>";
                        if ($filtros[0]=='Todas'||$filtros[0]=='Todos'||$filtros[0]=='-1')
                            $arrayFiltros['id_clasificacion'] = 0;
                        else
                            $arrayFiltros['id_clasificacion'] = $filtros[0];
                        
                        if($filtros[1]=="" || $filtros[1] == null)
                            $arrayFiltros["f_desde"] = "";
                        else
                            $arrayFiltros["f_desde"] = $filtros[1];
                        
                        if($filtros[2]=="" || $filtros[2] == null)
                            $arrayFiltros["f_hasta"] = "";
                        else
                            $arrayFiltros["f_hasta"] = $filtros[2];
                        
                        if($filtros[3]=="Todos" || $filtros[3] == null)
                            $arrayFiltros["id_estado_inv"] = "";
                        else
                            $arrayFiltros["id_estado_inv"] = $filtros[3];
                        
                        if($filtros[4]=="Todos" || $filtros[4] == null)
                            $arrayFiltros["id_criticidad"] = "";
                        else
                            $arrayFiltros["id_criticidad"] = $filtros[4];
                    }
                    else
                    {
                        $arrayFiltros="";

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

                    $usuario="";
//                    echo "<pre>".print_r($arrayFiltros,true)."</pre>";
                    $listado = $this->gr_rmc->listado($usuario,$start, $limit, $arrayFiltros,$busqueda, $campos, $sort, $dir);
                    echo $listado;
                }
                else
                    echo -1; //No tiene permisos
                
	}
	public function listado_tareas()
	{
		if ($this->permisos['Listar'])
                {
                    $id_usuario=$this->user['id'];
                    $id_rmc=$this->input->post("id");
                    $herramienta=array();
                    $herramienta['id_tipo']=1;
                    $herramienta['id']=$id_rmc;
                    $listado=array();
                    $this->load->model('gestion_riesgo/gr_tareas_model','gr_tareas',true);
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
//                    $listado = $this->fcatedras->listado             ($start, $limit, $campos, $busqueda, $sort, $dir,$filtros);
                    $listado = $this->gr_tareas->listadoParaHerramientasRmc($usuario,$start, $limit, $filtros,$busqueda, $campos, $sort, $dir,$herramienta);
                    echo $listado;
                }
                else
                    echo -1; //No tiene permisos
                
	}
	public function listado_archivos()
	{
		if ($this->permisos['Listar'])
                {
                    $id_usuario=$this->user['id'];
                    $id_rmc=$this->input->post("id");
                    $herramienta=array();
                    $this->load->model('gestion_riesgo/gr_archivos_rmc_model','gr_archivos_rmc',true);
                    $start = $this->input->post("start");
                    $limit = 8;
                    $sort = $this->input->post("sort");
                    $dir = $this->input->post("dir");
                    $listado=array();
                    $listado = $this->gr_archivos_rmc->listado($id_rmc,$sort, $dir,$limit,$start);
                    echo $listado;
                }
                else
                    echo -1; //No tiene permisos
                
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
                    
//            echo "<pre>".print_r($areas,true)."</pre>";die();
            
            return $areas;
            
        }
	
        public function insert()
	{
            if ($this->permisos['Alta'])		
            {
                $datos=array();
                $datos['id_usuario_alta']       = $this->user['id'];
                $datos['id_sector']             = $this->input->post("sector");
                $datos['descripcion']           = $this->input->post("desc");
                $validar                        = $this->input->post("validar_observacion");
                $observacion_sector             = $this->input->post("observacion_sector");
//                echo "<pre>".print_r($datos,true)."</pre>";
                $control=true;
                foreach ($datos as $dato)
                {
                    if($dato==""||$dato==NULL)
                        $control=false;
                }
                $control = ($validar == 1 && ($observacion_sector == "" || $observacion_sector == NULL)?false:$control);
                if ($control)
                {
                    $datos['observacion_sector'] = $this->input->post("observacion_sector");
                    //verifico que no sea rePost
                    $this->load->model("gestion_riesgo/gr_rmc_model","rmc",true);

                    if ($this->rmc->verificaRePost($datos)==0)
                    {
                        $insert_id=$this->rmc->insert($datos);
                        if($insert_id)
                        {
                            $this->_enviarMailAltaRmc($insert_id);
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
        
        public function setCriticidad()
	{
            if ($this->permisos['Alta'])		
            {
                $datos=array();
                $datos2=array();
                $id_rmc =$this->input->post("id_rmc");
                $id_usuario_set_crit = $this->user['id'];
                $clas=explode(";",$this->input->post("clas"));
                foreach ($clas as &$value)
                {
                    $datos2['id_rmc']=$id_rmc;
                    $datos2['id_clasificacion']=$value;
                    $value=$datos2;
                }
                unset ($value);
//                echo "<pre>".print_r($clas,true)."</pre>";die();
                
                //busco las areas que integran el área de gestión de riesgo
//                $this->load->model('gestion_riesgo/gr_areas_model','gr_areas',true);
//                $areasInferiores[]=4;
//
//                for($i=0;$i<10;$i++)
//                {
//                    $subareasGr=$this->gr_areas->dameAreasInferiores($areasInferiores);                
//                    if (count($subareasGr)>1)
//                    {
//                        foreach ($subareasGr as $area)
//                        {
//                            $arrayAreas[]=$area['id_area'];
//                        }
//                        $areasInferiores= array_unique(array_merge($areasInferiores,$arrayAreas));
//                    }
//
//                }
//                
//                
//                //verifico que el usaurio que esta editando corresponsa con un usuario de GR o JoseDeCastro y que el RMC no tenga asignado criticidad
//                $this->load->model('gestion_riesgo/gr_usuarios_model','gr_usuarios',true);
//                $usuarioGr=$this->gr_usuarios->verificarUsuarioGr($this->user['id'],$areasInferiores);
//                $usuarioGr=true;
                $usuarioGr = $this->_dameAreas($id_usuario_set_crit);
                
                if($usuarioGr['usuario']['gr'] == 1)
                {
                    $this->load->model('gestion_riesgo/gr_rmc_model','gr_rmc',true);
//                    $datos['id_tipo_herramienta']   =$this->input->post("herramienta");
                    $datos['id_usuario_set_crit']   =$id_usuario_set_crit;
                    $datos['id_criticidad']         =$this->input->post("id_crit");
                    $datos['id_investigador1']      =($this->input->post("inv1")!="")?$this->input->post("inv1"):NULL;
                    $datos['id_investigador2']      =($this->input->post("inv2")!="")?$this->input->post("inv2"):NULL;
                    if(($datos['id_criticidad'] ==3)||($datos['id_criticidad'] ==4))
                        $datos['id_estado_inv'] = 0;
                    else
                        $datos['id_estado_inv'] = 1;
                    
//                    $datos['id_estado_inv']        =(($datos['id_criticidad'] ==3)||($datos['id_criticidad'] ==4))?0:1;//si criticidad=Baja(3)->0 sino Abierta(1)
                    
                    
                    //verifico que todos los campos esten completos
                    $control=true;
//                    foreach ($datos as $dato)
//                    {
//                        if($dato=="")
//                            $control=false;
//                    }
                    if ($control)
                    {
                        //actualizo tabla rmc y tabla rmc_clasificaciones
                        
                        $this->load->model('gestion_riesgo/gr_rmc_clasificaciones_model','gr_rmc_clasificaciones',true);
                        foreach ($clas as $value)
                        {
                            $insert=$this->gr_rmc_clasificaciones->insert($value);
                        }
                        $update=$this->gr_rmc->update($id_rmc,$datos);
                        if($insert&&$update)
                        {
                            //Envio mail a los Investigadores asignados si la criticidad es Crítica o Alta (1,2)
                            if($datos['id_criticidad']==1 || $datos['id_criticidad']==2)
                            {
                                $inv = array();
                                $inv[0] = $datos['id_investigador1'];
                                $inv[2] = $datos['id_investigador2'];
                                //if(ENPRODUCCION)
                                    $this->_enviarMailInvestigadores($id_rmc,$inv);
                                
                            }
                            echo 1; //todo OK
                        }
                        else
                            echo 2; //Error al insertar el registro
                                
                    }
                    else
                        echo 3;//Faltan campos requeridos
                    
                }
                else
                    echo 4;//No es usuario de GR o el RMC ya tiene asignado una criticidad
                
                
            }
            else
                    echo -1; //No tiene permisos
	}
         public function empresas_combo()
	{
            $this->load->model('empresas_model','empresas',true);
            $var=$this->empresas->dameComboParaSectores();
            echo $var;	
	}
         public function sectores_combo()
	{
            $id_empresa=2;
            $this->load->model('gestion_riesgo/gr_sectores_papelera_model','sectores',true);
            $var=$this->sectores->dameCombo($id_empresa);
            echo $var;	
	}
//         public function subsectores_combo()
//	{
//            $id_sector=$this->input->post("id");
//            $this->load->model('gestion_riesgo/gr_subsectores_model','subsectores',true);
//            $var=$this->subsectores->dameCombo($id_sector);
//            echo $var;	
//	}
        public function combo_inv()
	{
            $limit=$this->input->post("limit");
            $start=$this->input->post("start");
            $query=$this->input->post("query");
            $inv2=($this->input->post("idNot")!="")?$this->input->post("idNot"):0;
            //agrego ademas del usuario logein el usuario ya seleccionado en otro combo
//            $id_usuario_not=array($this->user['id'],$inv2);
            $id_usuario_not=array($inv2);
            $this->load->model("usuarios_model","usuarios",true);
            $jcode = $this->usuarios->usuariosCombo($limit,$start,$query,$id_usuario_not);
            echo $jcode;
	}
        
        public function combo_responsables()
	{
            $limit=$this->input->post("limit");
            $start=$this->input->post("start");
            $query=$this->input->post("query");
            $id_usuario_not=$this->user['id'];
            $this->load->model("usuarios_model","usuarios",true);
            $jcode = $this->usuarios->usuariosCombo($limit,$start,$query,$id_usuario_not);
            echo $jcode;
	}
        
        public function combo_clasificaciones()
	{
            $limit=$this->input->post("limit");
            $start=$this->input->post("start");
            $query=$this->input->post("query");
            $id_usuario_not=$this->user['id'];
            $this->load->model("gestion_riesgo/gr_clasificaciones_model","gr_clasificaciones",true);
            $jcode = $this->gr_clasificaciones->dameCombo($limit,$start,$query,$id_usuario_not);
            echo $jcode;
	}
        
        public function insert_tarea()
	{
            if ($this->permisos['Alta'])		
            {
                $id_usuario=$this->user['id'];
                $id_rmc=$this->input->post("id_rmc");
                //verifico que sea el usuario observador
                $this->load->model("gestion_riesgo/gr_rmc_model","gr_rmc",true);
                 if ($this->gr_rmc->verificaInvesigador($id_usuario,$id_rmc)==1)
                 {
                    $datos=array();
                    $datos['usuario_alta']          =$id_usuario;
                    $datos['id_tipo_herramienta']   =1; //1=Reporte para la mejora Continua (rmc)
                    $datos['id_herramienta']        =$id_rmc;
                    $datos['usuario_responsable']   =$this->input->post("responsable");
                    $datos['hallazgo']              =$this->input->post("hallazgo");
                    $datos['id_grado_crit']         =$this->input->post("grado_crit");
                    $datos['tarea']                 =$this->input->post("tarea");
                    $datos['fecha_vto']             =$this->input->post("fecha");
//                    $datos['rpd']                   =$this->input->post("rpd");
                    $datos['id_estado']             =1;

                    $control=true;
                    foreach ($datos as $dato)
                    {
                        if($dato=="")
                            $control=false;
                    }
                    if ($control)
                    {
                        //verifico que no sea rePost
                        $this->load->model("gestion_riesgo/gr_tareas_model","gr_tareas",true);

                        if ($this->gr_tareas->verificaRePost($datos)==0)
                        {
                            $insert_id=$this->gr_tareas->insert($datos);
                            if($insert_id)
                            {
                                //Si es la primera cambio el estado de la inv a cerrada y grabo la fecha de cierre
                                $q_tareas=$this->gr_tareas->contarTareasRmc($id_rmc);
//                                echo "<pre>".print_r($q_tareas,true)."</pre>";die();
                                if ($q_tareas==1)
                                {
                                    $cerrar=$this->gr_rmc->cerrarInvestigacion($id_rmc);
                                }
                                //inserto historial de accion
                                $id_accion=1;
                                $texto="Nueva tarea desde RI";
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
                     echo -2;//No tiene permisos
            }
            else
                    echo -1; //No tiene permisos
	}
        
        public function _preparar_enviar_mail($id_tarea="",$accion="alta")
        {
            if($id_tarea!="")
            {
                
                $this->load->model('gestion_riesgo/gr_usuarios_model','gr_usuarios',true);
                $this->load->model('gestion_riesgo/gr_tareas_model','gr_tarea',true);
                $this->load->model('gestion_riesgo/gr_rmc_model','gr_rmc',true);

                //traigo todos los datos de la tarea
                $tarea=$this->gr_tarea->dameTarea($id_tarea);
//                echo "<pre>".print_r($tarea,true)."</pre>";
//                echo "<pre>".print_r($rmc,true)."</pre>";
                
            
                $rmc=$this->gr_rmc->dameRmc($tarea['id_herramienta']);

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
                    if($superior['mailing']==0)
                        continue;
                    if ($n==0)
                    {
                        $usuariosCC.=$superior['email'];
                    }
                    else
                    {
                        $usuariosCC.=", ".$superior['email'];
                    }
                    $n++;
                }
                if ($rmc['mailInv1']!="" && strpos($usuariosCC,$rmc['mailInv1'])== false) 
                {
                        $usuariosCC.=", ".$rmc['mailInv1'];
                }
                if ($rmc['mailInv2']!="" && strpos($usuariosCC,$rmc['mailInv2'])== false) 
                {
                        $usuariosCC.=", ".$rmc['mailInv2'];
                }

                $usuario=$this->user['nombre']." ".$this->user['apellido'];
                $datos=array();
                
                $datos['accion']=$accion;
                $datos['id_tarea']=$tarea['id_tarea'];
                $datos['tarea']=$tarea['tarea'];
                $datos['hallazgo']=$tarea['hallazgo'];
                $datos['fecha_limite']=$tarea['fecha_vto'];
                $datos['generoResp']=$usuarioResp['genero'];
                $datos['id_rmc']=$rmc['id_rmc'];
                $datos['grado']=$rmc['grado'];
                $datos['sector']=$rmc['sector'];
                $datos['inv1']=$rmc['inv1'];
                $datos['inv2']=$rmc['inv2'];
                $datos['descipcionRmc']=$rmc['descripcion'];
                
                switch ($accion)
                {
                    case 'alta':
                    case 'editada':
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
//                    case 'editado':
//                        break;
                }

                
                $this->_enviarMail($datos);
            }
        }
        function enviarManual()
        {
            $id_rmc=1875;
            $this->_enviarMailAltaRmc($id_rmc);
            echo "Mail enviado...";
        }
        function _enviarMailAltaRmc($id_rmc)
        {
            $this->load->model('gestion_riesgo/gr_usuarios_model','gr_usuarios',true);
            $this->load->model('gestion_riesgo/gr_rmc_model','gr_rmc',true);
            $this->load->model('gestion_riesgo/gr_areas_model','gr_areas',true);
            $this->load->model('usuarios_model','usuarios',true);
            
            $rmc=$this->gr_rmc->dameRmc($id_rmc);
            $usuarioAlta=$this->gr_usuarios->dameUsuarioPorId($rmc['id_usuario_alta']);

            $gerente= array();
            $resultado= array();
//            echo count($gerente);
            $resultado=$this->_dameGerente($this->user['id'],$gerente);//($this->user['id']);
//            echo $gerente;
//            echo "<pre>111".print_r($resultado,true)."</pre>";
            $mailGte = array();
            foreach($resultado as $atrib=>$usuarioGte)
            {
                $mailGte[] = $usuarioGte['email'];
            }
//            echo "<pre>111".print_r($mailGte,true)."</pre>"; //$mailGerente['mail'];
            $arrayEmailsGr=array('gestionderiesgos@salesdejujuy.com');

            
            $usuarioGGR=''; //Gerente Gestión de Riesgo
            $usuarioRtte=$usuarioAlta['email'];
//            die();

            
            $datos=array();
            $datos['id_rmc']        =$rmc['id_rmc'];
            $datos['descipcion']    =$rmc['descripcion'];
            $datos['sector']        =$rmc['sector'];
            $datos['usuarioAlta']   =$usuarioAlta['persona']." (".$usuarioAlta['puesto'].")";
//            $datos['usuarioPara']   =htmlentities($emailsGr);
            $datos['usuarioPara']   =$arrayEmailsGr;
            $datos['usuarioRtte']   =$usuarioAlta['email'];
            
            if (MAIL_GG!="")
                $datos['usuariosCC']    = (in_array($usuarioRtte,$datos['usuarioPara']))?array(MAIL_GG):array(MAIL_GG,$usuarioRtte);
            else
            {
                if (!in_array($usuarioRtte,$datos['usuarioPara']))
                    $datos['usuariosCC'] = array($usuarioRtte);
            }//:array($usuarioGGR,$usuarioRtte);
            
            
            $datos['usuariosCC']    = array_diff($datos['usuariosCC'],$datos['usuarioPara']);
            $datos['usuariosCC']    = array_merge($datos['usuariosCC'],$mailGte);
            $datos['usuariosCC']    = array_unique($datos['usuariosCC']);
            
            $link='www.polidata.com.ar/sdj/admin';
                        
$cuerpo_mail=<<<HTML
<html> 
    <head>
        <title>SMC</title>
        
    </head>
    <body>
    <div style="color:#000000; font-family:Verdana, Arial, Helvetica, sans-serif; text-align:Left; font-size:13px;">
        <pre>
            Srs GR,

            Se ha creado un nuevo Reporte de Incidente (RI) y se encuentra disponible para asignarle criticidad:
            
            <b>RI Nro: </b>#rmcNro
            <b>Usuario Alta: </b>#usuarioAlta
            <b>Sector asociado: </b>#sector            
            <b>Descripci&oacute;n: </b>
            <em>#descipcionRmc</em>          
                        
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

             $subject='Nuevo RI Nro: '.(2000000+$datos['id_rmc']);
             $cuerpo_mail=str_replace("#descipcionRmc"   ,$datos['descipcion'], $cuerpo_mail);
             $cuerpo_mail=str_replace("#rmcNro"   ,(2000000+$datos['id_rmc']), $cuerpo_mail);
             $cuerpo_mail=str_replace("#sector"   , $datos['sector'], $cuerpo_mail);
             $cuerpo_mail=str_replace("#usuarioAlta"   , $datos['usuarioAlta']  , $cuerpo_mail);
             $cuerpo_mail=str_replace("#txt_usuarioPara"   , implode(", ", $datos['usuarioPara'])  , $cuerpo_mail);
             $cuerpo_mail=str_replace("#txt_usuariosCC"    , implode(", ",$datos['usuariosCC'])   , $cuerpo_mail);
             $cuerpo_mail=str_replace("#usuarioRtte"   , $datos['usuarioRtte']  , $cuerpo_mail);
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
//            echo $cuerpo_mail;
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
//                echo -1;
                    return -1;
            }
//            echo $this->email->print_debugger();

        }
        function _enviarMailinvestigadores($id_rmc,$inv)
        {
            $this->load->model('gestion_riesgo/gr_usuarios_model','gr_usuarios',true);
            $this->load->model('gestion_riesgo/gr_rmc_model','gr_rmc',true);
            $this->load->model('gestion_riesgo/gr_areas_model','gr_areas',true);
            
            $rmc=$this->gr_rmc->dameRmc($id_rmc);
            $usuarioAlta=$this->gr_usuarios->dameUsuarioPorId($rmc['id_usuario_alta']);
            
            if ($rmc['id_criticidad']==1 || $rmc['id_criticidad']==2)
            {
                
                $areasInferiores[]=4;

                for($i=0;$i<10;$i++)
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

    //            $areasGr=  str_replace("'","",implode(",",$areasInferiores));

                $usuariosGr=$this->gr_usuarios->dameUsuariosGrPorEmpresa($areasInferiores,$rmc['id_empresa']);
                $arrayEmailsGr=array();
                if($usuariosGr != 0)
                {
                    foreach ($usuariosGr as $email)
                    {
                        if($email['mailing']==1)
                            $arrayEmailsGr[]=$email['email'];
                    }
                }
                    
    //            $emailsGr=  str_replace("'","",implode(",",$arrayEmailsGr));
                
                $gerente = array();
                $gerentes = $this->_dameGerente($inv,$gerente);
                $mailGteMant = array();
                foreach($gerentes as $atrib=>$usuarioGte)
                {
                    if($usuarioGte['id_usuario'] == GTE_MANT)
                    {
                        $mailGteMant[] = $usuarioGte['email'];
                        break;
                    }
                }
//                echo "<pre>".print_r($mailGteMant,true)."</pre>";
                
//            switch ($rmc['id_empresa'])
//            {
//                case 1:
//                   $usuarioGG=array(" ivan.gomez@boraxargentina.com");
//                   break;
//               case 2:
//                   $usuarioGG=array();
//                   break;
//               case 3:
//                   $usuarioGG=array(" ivan.gomez@boraxargentina.com");
//                   break;
//               default :
//                   $usuarioGG=array(" ivan.gomez@boraxargentina.com");
//                   
//            }

                $usuarioGGR=''; //Gerente Gestión de Riesgo
                $usuarioGPyGGR=array(MAILS_ALWAYS_CC,$usuarioGGR); 

                $usuarioRtte=$usuarioAlta['email'];

    //             echo "<pre>".print_r($usuariosGr,true)."</pre>";

                $datos=array();
                $datos['id_rmc']        =$rmc['id_rmc'];
                $datos['grado']         =$rmc['grado'];
                $datos['usuarioSetCrit']=$rmc['usuarioSetCrit'];
                $datos['vto_inv']       =$rmc['vto_inv'];
                $datos['inv1']          =$rmc['inv1'];
                $datos['inv2']          =$rmc['inv2'];
                $conector                = (strtoupper(substr($datos['inv2'],0,1))=='I')?' e ':' y ';
                $datos['descipcion']    =$rmc['descripcion'];
                $datos['sector']        =$rmc['sector'];
                $datos['usuarioAlta']   =$usuarioAlta['persona']." (".$usuarioAlta['puesto'].")";
    //            $datos['usuarioPara']   =htmlentities($emailsGr);
                $datos['usuarioPara']   =array($rmc['mailInv1'],$rmc['mailInv2']);
                $datos['usuarioRtte']   =$usuariosGr;
                $datos['usuarioRtte']   =$usuarioAlta['email'];
                $datos['usuariosCC']   = array_diff(array_merge($usuarioGPyGGR,$arrayEmailsGr,$mailGteMant),$datos['usuarioPara']);

                $link='www.polidata.com.ar/sdj/admin';
                        
$cuerpo_mail=<<<HTML
<html> 
    <head>
        <title>SMC</title>
        
    </head>
    <body>
    <div style="color:#000000; font-family:Verdana, Arial, Helvetica, sans-serif; text-align:Left; font-size:13px;">
        <pre>
            Estimados #inv1y2,

            Del Reporte de Incidente (RI) se los ha nominado como investigadores.
            
            <b>RI Nro:</b>#rmcNro
            <b>Grado:</b>#grado
            <b>Grado establecido por:</b>#usuarioSetCrit
            <b>Fecha de Vencimiento (plazo):</b>#vto_inv
            <b>Usuario que origino el RI:</b>#usuarioAlta
            <b>Sector asociado:</b>#sector            
            <b>Descripci&oacute;n:</b>
            <em>#descipcionRmc</em>          
                        
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

                $subject='Investigadores RI Nro: '.(2000000+$datos['id_rmc']);
                $cuerpo_mail=str_replace("#inv1y2"   , $datos['inv1'].$conector.$datos['inv2']  , $cuerpo_mail);
                $cuerpo_mail=str_replace("#grado"   , $datos['grado']  , $cuerpo_mail);
                $cuerpo_mail=str_replace("#usuarioSetCrit"   , $datos['usuarioSetCrit']  , $cuerpo_mail);
                $cuerpo_mail=str_replace("#vto_inv"   , $datos['vto_inv']  , $cuerpo_mail);
                $cuerpo_mail=str_replace("#descipcionRmc"   ,$datos['descipcion'], $cuerpo_mail);
                $cuerpo_mail=str_replace("#rmcNro"   ,(2000000+$datos['id_rmc']), $cuerpo_mail);
                $cuerpo_mail=str_replace("#sector"   , $datos['sector'], $cuerpo_mail);
                $cuerpo_mail=str_replace("#usuarioAlta"   , $datos['usuarioAlta']  , $cuerpo_mail);
    //             $cuerpo_mail=str_replace("#usuarioPara"   , $datos['usuarioPara']  , $cuerpo_mail);
    //             $cuerpo_mail=str_replace("#usuariosCC"    , $datos['usuariosCC']   , $cuerpo_mail);
                $cuerpo_mail=str_replace("#txt_usuarioPara"   , implode(", ", $datos['usuarioPara'])  , $cuerpo_mail);
                $cuerpo_mail=str_replace("#txt_usuariosCC"    , implode(", ",$datos['usuariosCC'])   , $cuerpo_mail);
                $cuerpo_mail=str_replace("#usuarioRtte"   , $datos['usuarioRtte']  , $cuerpo_mail);
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
//                echo $cuerpo_mail;
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

            Del Reporte de Incidente (RI) y la investigación realizada, fue #nominado como responsable para resolver la siguiente tarea:
            
            <b>Descripci&oacute;n:</b>
            <em>#hallazgo</em>
            
            <b>Tarea:</b>
            <em>#tarea</em>
                        
            <b>Vencimiento:</b>#fecha_limite
            
            <b>Tipo de Herramienta:</b>
            <em>Reporte de Incidente(RI)</em>
        
            <b>RI Nro:</b>#rmcNro
            <b>Grado:</b>#grado
            <b>Sector asociado:</b>#sector
            <b>Investigadores:</b>#inv1y2
            <b>Descripci&oacute;n RI:</b>
            <em>#descipcionRmc</em>
            
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
                
            }
             
             if ($datos['generoResp']=='F')
             {
                $cuerpo_mail=str_replace("#persona"       , 'Estimada '.$datos['usuario']      , $cuerpo_mail);
                $cuerpo_mail=str_replace("#nominado"       , 'nominada' , $cuerpo_mail);
             }
             else
             {
                $cuerpo_mail=str_replace("#persona"       , 'Estimado '.$datos['usuario']      , $cuerpo_mail);
                $cuerpo_mail=str_replace("#nominado"       , 'nominado' , $cuerpo_mail);
             }   
             
             $cuerpo_mail=str_replace("#rmcNro"   ,(2000000+$datos['id_rmc']), $cuerpo_mail);
             $cuerpo_mail=str_replace("#grado"   , $datos['grado']  , $cuerpo_mail);
             $cuerpo_mail=str_replace("#sector"   , $datos['sector'], $cuerpo_mail);
             $cuerpo_mail=str_replace("#inv1y2"   , $datos['inv1']." y ".$datos['inv2'], $cuerpo_mail);
             $cuerpo_mail=str_replace("#usuarioPara"   , $datos['usuarioPara']  , $cuerpo_mail);
             $cuerpo_mail=str_replace("#usuariosCC"    , $datos['usuariosCC']   , $cuerpo_mail);
             $cuerpo_mail=str_replace("#usuarioRtte"   , $datos['usuarioRtte']  , $cuerpo_mail);
             $cuerpo_mail=str_replace("#hallazgo"      , $datos['hallazgo']     ,$cuerpo_mail);
             $cuerpo_mail=str_replace("#descipcionRmc" , $datos['descipcionRmc']     ,$cuerpo_mail);
             $cuerpo_mail=str_replace("#tarea"         , $datos['tarea']        ,$cuerpo_mail);
             $cuerpo_mail=str_replace("#fecha_limite"  , $datos['fecha_limite'] ,$cuerpo_mail);
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
//            echo $cuerpo_mail;
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
        public function probando_sp($id_area)
        {
            $this->load->model('gestion_riesgo/gr_areas_model','gr_areas',true);
            $areas=$this->gr_areas->sp_areasInferiores($id_area);
//             echo "<pre>".print_r($areas,true)."</pre>";
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
        
        public function excel()
    {
        if ($this->permisos['Listar'])
        {
            $this->load->library('excel');
            $this->load->model('gestion_riesgo/gr_rmc_model','gr_rmc',true);
            $start = 0;
            $limit = 1000;
            $sort = $this->input->post("sort");
            $dir = $this->input->post("dir");
            $campos = "";
            if ($this->input->post("fields"))
            {
                $campos = str_replace('"', "", $this->input->post("fields"));
                $campos = str_replace('[', "", $campos);
                $campos = str_replace(']', "", $campos);
                $campos = explode(",",$campos);
            }
            $busqueda  = ($this->input->post("query"))?$this->input->post("query"):"";
//            echo $busqueda;
//            echo "<pre>".print_r($campos,true)."</pre>";
            if($this->input->post("filtros"))
            {
                $filtros=json_decode($this->input->post("filtros"));
                if ($filtros[0]=='Todas'||$filtros[0]=='Todos'||$filtros[0]=='-1')
                    $arrayFiltros['id_clasificacion'] = 0;
                else
                    $arrayFiltros['id_clasificacion'] = $filtros[0];

                if($filtros[1]=="" || $filtros[1] == null)
                    $arrayFiltros["f_desde"] = "";
                else
                    $arrayFiltros["f_desde"] = $filtros[1];

                if($filtros[2]=="" || $filtros[2] == null)
                    $arrayFiltros["f_hasta"] = "";
                else
                    $arrayFiltros["f_hasta"] = $filtros[2];
                
                if($filtros[3]=="Todos" || $filtros[3] == null)
                            $arrayFiltros["id_estado_inv"] = "";
                        else
                            $arrayFiltros["id_estado_inv"] = $filtros[3];
                        
                if($filtros[4]=="Todos" || $filtros[4] == null)
                        $arrayFiltros["id_criticidad"] = "";
                    else
                        $arrayFiltros["id_criticidad"] = $filtros[4];
//                        echo "<pre>".print_r($arrayFiltros,true)."</pre>";
            }
            else
            {
                $arrayFiltros="";

            }
                                                
            $datos = $this->gr_rmc->listado_excel($start, $limit,$arrayFiltros,$busqueda,$campos,$sort,$dir); 
            $datos_audit['id_modulo']=$this->modulo;
            $datos_audit['id_usuario']=$this->user['id'];
            $datos_audit['q_registros']=count($datos);
            $params=array($start, $limit,$arrayFiltros,$busqueda,$campos,$sort,$dir);
            $datos_audit['params']=json_encode($params);

            $this->load->model("auditoria_model","auditoria_model",true);
            $this->auditoria_model->guardarPedidoExcel($datos_audit);
            
            $titulo = 'RI';
            $cabecera = array('#','Fecha Alta','Usuario Alta','Descripción','Sector','Criticidad','Fecha Criticidad','Clasificación','Vencimiento Investigación','Estado','1er Investigador','2do Investigador','Tareas');
            $estiloCabecera=array(
                'font' => array(
                    'bold' => true,
                    'size' =>9
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                    'rotation' => 0,
                    'wrap' => TRUE
                )               
             );
            $estiloDatos=array(
                'font' => array(
                    'size' => 10
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                    'rotation' => 0,
                    'wrap' => TRUE
                )
            );
            $estiloTitulo=array(
                'font' => array(
                    'size' => 14,
                    'bold' => true
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                    'rotation' => 0,
                    'wrap' => TRUE
                )
            );
            

            $this->_generar_excel_listado($datos,$cabecera,$titulo,$estiloCabecera,$estiloDatos,$estiloTitulo);
        }
        else
            echo -1; //No tiene permisos
    }
        
    public function _generar_excel_listado($datos,$cabecera,$titulo="",$estiloCabecera="",$estiloDatos="",$estiloTitulo="")//,$estiloTitulo
    {
        
        $filename = ($titulo != "")?'Listado - '.$titulo.' - '.date("YmdHis").'.xls':'Listado.xls';
        header('Content-Type:application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name          
        header('Cache-Control: max-age=0'); //no cache


        $this->excel->getProperties()
            ->setCreator("Sistema de Mejroa Continua") // Nombre del autor
            ->setLastModifiedBy($this->user['nombre']."-".$this->user['apellido']) //Ultimo usuario que lo modificó
            ->setTitle($filename) // Titulo
            ->setSubject($filename) //Asunto
            ->setDescription('Listado desde grilla - v1.0 - 20180813') //Descripción
            ->setKeywords('') //Etiquetas
            ->setCategory('');
        $getActiveSheet=$this->excel->getActiveSheet();

        $setActiveSheetIndex=$this->excel->setActiveSheetIndex(0);
        $lastColumn = 'A';
        $i = 1;
        if ($titulo != "")
        {
            $cont = count($cabecera);
            $o = 1;
            while ($o != $cont)
            {
                $lastColumn++;
                $o++;
            }
//                $lastColumn = $lastColumn + $cont;
            $getActiveSheet->mergeCells('A'.$i.':'.$lastColumn.$i);
            $getActiveSheet->setCellValue('A'.$i,'Listado - '.$titulo);
            if($estiloTitulo != "")
            {
                $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloTitulo);
            }
            $i = $i + 2;
            $lastColumn = 'A';
        }

        foreach($cabecera as $row)
        {
            $getActiveSheet->setCellValue($lastColumn.$i,$row);
            if($estiloCabecera != "" || $estiloCabecera != null)
            {
                $getActiveSheet->getStyle($lastColumn.$i)->applyFromArray($estiloCabecera);
            }
            $lastColumn++;
        }
        $i++;
        $lastColumn = 'A';

        foreach ($datos as $reg)
        {
            foreach($reg as $linea)
            {
                $getActiveSheet->setCellValue($lastColumn.$i,$linea);
                $getActiveSheet->getColumnDimension($lastColumn)->setAutoSize(true);
                if($estiloDatos != "" || $estiloDatos != null)
                {
                    $getActiveSheet->getStyle($lastColumn.$i)->applyFromArray($estiloDatos);
                }
                $lastColumn++;
            }
            $i++;
            $lastColumn = 'A';
        }

        $getActiveSheet->getPageSetup()->setFitToWidth(1);
        $getActiveSheet->setTitle(($titulo != "")?'Listado - '.$titulo.'.xls':'Listado.xls');
//            $getActiveSheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $getActiveSheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);


        //save it to Excel5 format (excel 2003 .XLS file), chan ge this to 'Excel2007' (and adjust the filename extension, also the header mime type)
        //if you want to save it as .XLSX Excel 2007 format
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
        ob_end_clean();
        //force user to download the Excel file without writing it to server's HD
        $objWriter->save('php://output');
    }
    //$usuario = array_pop($usuario);//Extrae el último elemento del final del array al tener un solo elemento se lo utiliza para sacarle un nivel al array
    
    
    public function pruebaGerente()
    {
////        $this->load->model('usuarios_model','usuarios',true);
////        $resultado = $this->_dameGerente(243);
////        $id_usuario = array(219);//,243);//243);
//        $gerente = array();
//        $resultado = $this->_dameGerente(219,$gerente);
//        echo "<pre>4564".print_r($resultado,true)."</pre>";die();
    }    
    
    public function _dameGerente($id_usuarios,$gerente)
    {
        $usuarios  = $this->usuarios->dameUsuarioSupervisor($id_usuarios);
        $id_usuarios=null;
        
        if($usuarios != 0)
        {
            $i = 0;
            foreach($usuarios as $atrib=>$usuario)
            {
                if($usuario['gte'] == 1)
                {
                    $gerente[$usuario['id_usuario']]= $usuario;
                }
                else
                {
                    $id_usuarios[$i]=$usuario['id_usuario'];
                }
            }
            
            if($id_usuarios == null)
            {
                return $gerente;
            }
            else
            {
                return $this->_dameGerente($id_usuarios,$gerente);
            }
        }
        else
            if(count($gerente) > 0)
                return $gerente;
            else
                return $usuarios;
    }
}
?>