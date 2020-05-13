<?php
class Auditoria extends CI_Controller
{
    private $modulo=25;
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
            if (!($this->user['id']>0)) 
                redirect("admin/admin/index_js","location", 301);
            $this->load->model("permisos_model","permisos_model",true);
            $this->permisos= $this->permisos_model->checkIn($this->user['perfil_id'],$this->modulo);
            $this->load->model("dms/dms_permisos_model","dms_permisos",true);
            $this->roles= $this->dms_permisos->checkIn($this->user['id']);
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
            $this->load->view('gestion_riesgo/auditoria/listado',$variables);
	}
	
	public function listado()
	{
		if ($this->permisos['Listar'])
                {
                    $id_usuario=$this->user['id'];
                    $listado=array();
                    $this->load->model('gestion_riesgo/gr_auditorias_model','gr_auditorias',true);
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
//                    $usuario=array(1);
                    $listado = $this->gr_auditorias->listado($usuario,$start, $limit, $filtros,$busqueda, $campos, $sort, $dir);
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
                    
//            echo "<pre>".print_r($areas,true)."</pre>";
            
            return $areas;
            
        }
        public function combo_auditores()
	{
            $id_usuario_not=$this->user['id'];
            $limit=$this->input->post("limit");
            $start=$this->input->post("start");
            $query=$this->input->post("query");
            $this->load->model("gestion_riesgo/gr_roles_model","gr_roles",true);
            $jcode = $this->gr_roles->dameAuditoresCombo($limit,$start,$query,$id_usuario_not);
            echo $jcode;
	}
        public function combo_normas()
	{
            $limit=$this->input->post("limit");
            $start=$this->input->post("start");
            $query=$this->input->post("query");
            $this->load->model("gestion_riesgo/gr_normas_model","gr_normas",true);
            $jcode = $this->gr_normas->dameNormasCombo($limit,$start,$query);
            echo $jcode;
	}
        
	public function listado_hallazgos()
	{
		if ($this->permisos['Listar'])
                {
                    $id_usuario=$this->user['id'];
                    $id_auditoria=$this->input->post("id");
                    $this->load->model('gestion_riesgo/gr_hallazgos_auditorias_model','gr_hallazgos_auditorias',true);
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
                    $listado = $this->gr_hallazgos_auditorias->listado_auditoria($id_auditoria,$start, $limit, $filtros,$busqueda, $campos, $sort, $dir);
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
                    $id_hallazgo=$this->input->post("id");
                    $herramienta=array();
                    $herramienta['id_tipo']=5;
                    $herramienta['id']=$id_hallazgo;
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
                    $listado = $this->gr_tareas->listadoParaHerramientas($usuario,$start, $limit, $filtros,$busqueda, $campos, $sort, $dir,$herramienta);
                    echo $listado;
                }
                else
                    echo -1; //No tiene permisos
                
	}
	
        public function insert()
	{
            if ($this->permisos['Alta'])		
            {
                $datos=array();
                $datos['id_sector']         =$this->input->post("sector");
                $datos['id_usuario_alta']   =$this->user['id'];
                $datos['fecha']             =$this->input->post("fecha");
                $datos['q_usuarios']        =$this->input->post("q_usuarios");
                $datos['programada']        =($this->input->post("programada")=="no")?0:1;
                $datos['realizada']         =($this->input->post("realizada")=="no")?0:1;
                
                $datos_usuarios_auditores= explode(",",$this->input->post("auditores"));
                $q_auditores= count($datos_usuarios_auditores);
                
                $control=true;
                foreach ($datos as $dato)
                {
                    if(($dato==""||$dato==NULL)&&$dato!=0)
                    {
                        echo "<pre>".print_r($datos,true)."</pre>";die();
                        echo $dato;
                        $control=false;
                        
                    }
                }
                if ($control)
                {
                    //Verifico que los acompañantes sean distintos
                    if($q_auditores<=2 || $q_auditores>0)
                    {
                        //verifico que no sea rePost
                        $this->load->model("gestion_riesgo/gr_auditorias_model","gr_auditorias",true);

                        if ($this->gr_auditorias->verificaRePost($datos)==0)
                        {
                            $insert_id=$this->gr_auditorias->insert($datos);
//                            echo $insert_id;
                            if($insert_id)
                            {
    //                            $this->_preparar_enviar_mail($insert_id,'alta');
                                foreach ($datos_usuarios_auditores as $usuario)
                                {
                                    $datos_auditor['id_usuario_auditor']=$usuario;
                                    $datos_auditor['id_auditoria']=$insert_id;
                                    if($this->gr_auditorias->insert_auditor($datos_auditor))
                                        $b=1; //todo OK
                                    else
                                    {
                                        $b=2;
                                        break; //error al insertar usuario, salgo del foreach
                                    }

                                }
                                echo $b;
                            }
                            else
                                echo 2; //Error al insertar el registro
                        }
                        else
                            echo 0;//Registro repetido (RePost)
                    }
                    else
                            echo 4;//Mínimo 1 y Máximo 3 investigadores
                        
                }
                else
                    echo 3;//Faltan campos requeridos
                
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
            $id_empresa=$this->input->post("id");
            $this->load->model('gestion_riesgo/gr_sectores_auditar_model','sectores_auditar',true);
            $var=$this->sectores_auditar->dameCombo($id_empresa);
            echo $var;	
	}
//         public function subsectores_combo()
//	{
//            $id_sector=$this->input->post("id");
//            $this->load->model('gestion_riesgo/gr_subsectores_model','subsectores',true);
//            $var=$this->subsectores->dameCombo($id_sector);
//            echo $var;	
//	}
         public function insert_hallazgo()
	{
            if ($this->permisos['Alta'])		
            {
                $id_usuario=$this->user['id'];
                $id_auditoria=$this->input->post("id_auditoria");
                //verifico que sea el usuario observador
                $this->load->model("gestion_riesgo/gr_auditorias_model","gr_auditorias",true);
                 if ($this->gr_auditorias->verificaAuditorAltaHabilitado($id_usuario,$id_auditoria)==1)
                 {
                    $datos=array();
                    $datos['id_usuario_alta']       =$id_usuario;
                    $datos['hallazgo']              =$this->input->post("hallazgo");
                    $datos['id_auditoria']          =$id_auditoria;
                    
                    $normas_puntos= explode(",",$this->input->post("normas"));

                    $control=true;
                    foreach ($datos as $dato)
                    {
                        if($dato=="")
                            $control=false;
                    }
                    if ($control)
                    {
                        //verifico que no sea rePost
                        $this->load->model("gestion_riesgo/gr_hallazgos_auditorias_model","gr_hallazgos_auditorias",true);

                        if ($this->gr_hallazgos_auditorias->verificaRePost($datos)==0)
                        {
                            $insert_id=$this->gr_hallazgos_auditorias->insert($datos);
                            if($insert_id)
                            {
//                                $this->_preparar_enviar_mail($insert_id);
                                $this->load->model("gestion_riesgo/gr_hallazgos_normas_model","gr_hallazgos_normas",true);
                                foreach ($normas_puntos as $norma)
                                {
                                    $datos2['id_hallazgo']=$insert_id;
                                    $datos2['id_norma_punto']=$norma;
                                    $this->gr_hallazgos_normas->insert($datos2);
                                }
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
        public function insert_tarea()
	{
            if ($this->permisos['Alta'])		
            {
                $id_usuario=$this->user['id'];
                $id_hallazgo=$this->input->post("id_hallazgo");
                
                $this->load->model("gestion_riesgo/gr_hallazgos_auditorias_model","gr_hallazgos_auditorias",true);
                $hallazgo=$this->gr_hallazgos_auditorias->dameHallazgo($id_hallazgo);
//                echo "<pre>".print_r($hallazgo,true)."</pre>";die();
                $this->load->model("gestion_riesgo/gr_auditorias_model","gr_auditorias_model",true);
                //verifico que sea el usuario observador
                 if ($this->gr_auditorias_model->verificaAuditorAltaHabilitado($id_usuario,$hallazgo['id_auditoria'])==1)
                 {
                    $datos=array();
                    $datos['usuario_alta']          =$id_usuario;
                    $datos['id_tipo_herramienta']   =5; //5=Acciones resultantes de auditorias(ARA)
                    $datos['id_herramienta']        =$id_hallazgo;
                    $datos['usuario_responsable']   =$this->input->post("responsable");
                    $datos['hallazgo']              =$hallazgo['hallazgo'];
                    $datos['tarea']                 =$this->input->post("tarea");
                    $datos['fecha_vto']             =$this->input->post("fecha");
                    $datos['id_estado']             =1;
                    
                    switch ($this->input->post("tipoTarea"))
                    {
                        case '1':
                            $datos['tipo']          =1;
                            break;
                        case '2':
                            $datos['tipo']          =2;
                            break;
                        default :
                            $datos['tipo']          =NULL;
                            break;
                    }

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
//                                $this->_preparar_enviar_mail($insert_id,'alta');
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
         public function _enviar_manual($id)
        {
            $accion='alta';
//            $accion='editada';
//            $accion='rechazada';
            $this->_preparar_enviar_mail($id,$accion);
        }
        
         public function _preparar_enviar_mail($id_tarea="",$accion)
        {
            if($id_tarea!="")
            {
                
                $this->load->model('gestion_riesgo/gr_usuarios_model','gr_usuarios',true);
                $this->load->model('gestion_riesgo/gr_tareas_model','gr_tarea',true);
                $this->load->model('gestion_riesgo/gr_auditoria_model','gr_auditoria',true);

                //traigo todos los datos de la tarea
                $tarea=$this->gr_tarea->dameTarea($id_tarea);
                $auditoria=$this->gr_auditoria->dameAuditoriaDatosMail($tarea['id_herramienta']);
//                echo "<pre>".print_r($tarea,true)."</pre>";
//                echo "<pre>".print_r($auditoria,true)."</pre>";

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
                        $usuariosCC.=$superior['email'];
                    else
                        $usuariosCC.=", ".$superior['email'];
                    $n++;
                }
                if ($auditoria['mailacomp1']!="" && strpos($usuariosCC,$auditoria['mailacomp1'])== false) 
                {
                        $usuariosCC.=", ".$auditoria['mailacomp1'];
                }
                if ($auditoria['mailacomp2']!="" && strpos($usuariosCC,$auditoria['mailacomp2'])== false) 
                {
                        $usuariosCC.=", ".$auditoria['mailacomp2'];
                }

                $usuario=$this->user['nombre']." ".$this->user['apellido'];
                $datos=array();
                
                $datos['accion']=$accion;
                $datos['id_tarea']=$tarea['id_tarea'];
                $datos['tarea']=$tarea['tarea'];
                $datos['hallazgo']=$tarea['hallazgo'];
                $datos['fecha_limite']=$tarea['fecha_vto'];
                $datos['generoResp']=$usuarioResp['genero'];
                $datos['id_auditoria']=$auditoria['id_auditoria'];
                $datos['sector']=$auditoria['sector'];
                $datos['acomp1']=$auditoria['acomp1'];
                $datos['acomp2']=$auditoria['acomp2'];
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
        function _enviarMail($datos)
        {
            $link='www.polidata.com.ar/orocobre/admin';
                        
$mail_alta=<<<HTML
<html> 
    <head>
        <title>SMC</title>
        
    </head>
    <body>
    <div style="color:#000000; font-family:Verdana, Arial, Helvetica, sans-serif; text-align:Left; font-size:13px;">
        <pre>
            #persona,

            De la Observación para la Mejora continua realizada por <b>#usuarioRtte</b> fue #nominado como responsable para resolver la siguiente tarea:
            
            <b>Descripci&oacute;n:</b>
            <em>#hallazgo</em>
            
            <b>Tarea:</b>
            <em>#tarea</em>
                        
            <b>Vencimiento:</b>#fecha_limite
            
            <b>OMC Nro:</b>#auditoria
            <b>Acompa&ntilde;antes:</b>#acomp1y2
            <b>Sector:</b>#sector
            
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
             
             $cuerpo_mail=str_replace("#auditoria"   ,(1000000+$datos['id_auditoria']), $cuerpo_mail);
             $cuerpo_mail=str_replace("#sector"   , $datos['sector'], $cuerpo_mail);
             $cuerpo_mail=str_replace("#acomp1y2"   , $datos['acomp1']." y ".$datos['acomp2'], $cuerpo_mail);
             $cuerpo_mail=str_replace("#usuarioPara"   , $datos['usuarioPara']  , $cuerpo_mail);
             $cuerpo_mail=str_replace("#usuariosCC"    , $datos['usuariosCC']   , $cuerpo_mail);
             $cuerpo_mail=str_replace("#usuarioRtte"   , $datos['usuarioRtte']  , $cuerpo_mail);
             $cuerpo_mail=str_replace("#hallazgo"      , $datos['hallazgo']     ,$cuerpo_mail);
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
        
}
?>