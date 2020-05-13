<?php
class Rpyc extends CI_Controller
{
    private $modulo=24;
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
            $this->load->model("usuarios_model","usuarios",true);
//             echo "<pre>".print_r($this->roles,true)."</pre>";
            
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
            $this->load->view('gestion_riesgo/rpyc/listado',$variables);
	}
	
	public function listado()
	{
            if ($this->permisos['Listar'])
            {
                $id_usuario=$this->user['id'];
                $listado=array();
                $this->load->model('gestion_riesgo/gr_rpyc_model','gr_rpyc',true);
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
                $listado = $this->gr_rpyc->listado($usuario,$start, $limit, $filtros,$busqueda, $campos, $sort, $dir);
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
                    $id_rpyc=$this->input->post("id");
                    $herramienta=array();
                    $herramienta['id_tipo']=2;
                    $herramienta['id']=$id_rpyc;
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
                    $listado = $this->gr_tareas->listadoParaHerramientaRpyc($usuario,$start, $limit, $filtros,$busqueda, $campos, $sort, $dir,$herramienta);
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
	
        public function insert()
	{
            if ($this->permisos['Alta'])		
            {
                $this->load->model("gestion_riesgo/gr_rpyc_model","rpyc",true);
                $datos=array();
                $datos2=array();
                $datos['id_usuario_alta']   =$this->user['id'];
                $datos['q_usuarios']        =$this->input->post("usuarios");
                $datos['q_contratistas']    =$this->input->post("contratistas");
                $datos['programada']        =($this->input->post("programada")=="no")?0:1;
                $datos['realizada']        =($this->input->post("realizada")=="no")?0:1;
                
                $sectores_id=explode(";",$this->input->post("areas"));
                $sectores = $this->rpyc->dameSectores($sectores_id);
                $datos['sectores']=$sectores[0]['sectores'];
                
                $control=true;
                foreach ($datos as $dato)
                {
                    if($dato==""&&$dato!=NULL)
                        $control=false;
                }
                if(!($datos['q_usuarios']==0 && $datos['q_contratistas']==0))
                {
                    if ($control)
                    {
                        //verifico que no sea rePost
                        

                        if ($this->rpyc->verificaRePost($datos)==0)
                        {
    //                        echo "<pre>".print_r($datos,true)."</pre>";die();
                            $insert_id=$this->rpyc->insert($datos);
                            if($insert_id)
                            {
                                $areas=explode(";",$this->input->post("areas"));
                                foreach ($areas as &$value)
                                {
                                    $datos2['id_rpyc']=$insert_id;
                                    $datos2['id_area']=$value;
                                    $value=$datos2;
                                }
                                unset ($value);
                                $this->load->model('gestion_riesgo/gr_rel_areas_rpyc_model','gr_rel_areas_rpyc_model',true);
                                foreach ($areas as $value)
                                {
                                    $insert=$this->gr_rel_areas_rpyc_model->insert($value);
                                }
    //                            echo "<pre>".print_r($areas,true)."</pre>";die();
    //                            $this->_preparar_enviar_mail($insert_id,'alta');
                                echo 1; //todo OK
                            }
                            else
                                echo 4; //Error al insertar el registro
                        }
                        else
                            echo 0;//Registro repetido (RePost)

                    }
                    else
                        echo 3;//Faltan campos requeridos
                    
                }
                else
                    echo 2; //Cantida de usuarios y cantidad de contratistas =0
                
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
        public function combo_contra()
	{
            $limit=$this->input->post("limit");
            $start=$this->input->post("start");
            $query=$this->input->post("query");
            $id_usuario_not=array($this->user['id']);
            $this->load->model("gestion_riesgo/gr_contratistas_model","contratistas",true);
            $jcode = $this->contratistas->contratistasCombo($limit,$start,$query);
            echo $jcode;
	}
         
         public function combo_areas()
	{
            $id_empresa=$this->input->post("id");
            $query=$this->input->post("query");
            $this->load->model('gestion_riesgo/gr_areas_rpyc_model','areas',true);
            $var=$this->areas->dameCombo($query,$id_empresa);
//            $this->load->model('gestion_riesgo/gr_areas_rpyc_model','areas',true);
//            $var=$this->areas->dameCombo($query,$id_empresa);
            echo $var;	
	}
//         public function subareas_combo()
//	{
//            $id_area=$this->input->post("id");
//            $this->load->model('gestion_riesgo/gr_subareas_model','subareas',true);
//            $var=$this->subareas->dameCombo($id_area);
//            echo $var;	
//	}
       
         public function combo_responsables()
	{
            $limit=$this->input->post("limit");
            $start=$this->input->post("start");
            $query=$this->input->post("query");
//            $id_usuario_not=$this->user['id'];
            $id_usuario_not=0;
            $this->load->model("usuarios_model","usuarios",true);
            $jcode = $this->usuarios->usuariosCombo($limit,$start,$query,$id_usuario_not);
            echo $jcode;
	}
         public function insert_tarea()
	{
            if ($this->permisos['Alta'])		
            {
                $id_usuario=$this->user['id'];
                $id_rpyc=$this->input->post("id_rpyc");
                if ($id_rpyc)
                {
                    //verifico que sea el usuario observador
                    $this->load->model("gestion_riesgo/gr_rpyc_model","gr_rpyc",true);
                    if ($this->gr_rpyc->verificaUsuarioAlta($id_usuario,$id_rpyc)==1)
                    {
                        $datos=array();
                        $datos['usuario_alta']          =$id_usuario;
                        $datos['id_tipo_herramienta']   =2; //2=Reunión de Participación y Consulta (RPyC)
                        $datos['id_herramienta']        =$id_rpyc;
                        $datos['usuario_responsable']   =$this->input->post("responsable");
                        $datos['hallazgo']              =$this->input->post("hallazgo");
                        $datos['id_grado_crit']         =$this->input->post("grado_crit");
                        $datos['tarea']                 =$this->input->post("tarea");
                        $datos['fecha_vto']             =$this->input->post("fecha");
//                        $datos['rpd']                   =$this->input->post("rpd");
                        $datos['id_estado']              =1;
//                        echo "<pre>".print_r($datos,true)."</pre>";
                        $control=true;
                        foreach ($datos as $dato=>$valor)
                        {
//                            echo $dato."=>".$valor."<br>";
                            if($valor == "")
                                $control=false;
                        }
                        $datos['usuario_relacionado']   =($this->input->post("opcion")!="1")?0:$this->input->post("usuario");
                        if ($control)
                        {
                            //verifico que no sea rePost
                            $this->load->model("gestion_riesgo/gr_tareas_model","gr_tareas",true);

                            if ($this->gr_tareas->verificaRePost($datos)==0)
                            {
                                $insert_id=$this->gr_tareas->insert($datos);
                                if($insert_id)
                                {
                                    //marco el campo de tarear a 1=Si
                                    $tareas['tareas']=1;
                                    $this->gr_rpyc->update($id_rpyc,$tareas);
                                    
                                    $this->load->model("gestion_riesgo/gr_tareas_rpyc_model","gr_tareas_rpyc",true);
                                    $datos2['id_tarea']       =$insert_id;
                                    $datos2['id_rpyc']        =$id_rpyc;
                                    $datos2['opcion']          =$this->input->post("opcion");
                                    $datos2['id_usuario']     =($this->input->post("usuario")=="")?NULL:$this->input->post("usuario");
                                    $datos2['operario']       =($this->input->post("operario")=="")?NULL:$this->input->post("operario");
                                    $datos2['id_contratista'] =($this->input->post("contratista")=="")?NULL:$this->input->post("contratista");
                                    $this->gr_tareas_rpyc->insert($datos2);
                                    
                                    //inserto historial de accion
                                    $id_accion=1;
                                    $texto="Nueva tarea desde RPyC";
                                    $this->grabar_accion($id_accion, $insert_id, $texto);
                                    
                                    $this->_preparar_enviar_mail($insert_id);
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
                else echo -3; //No es POST
            }
            else
                    echo -1; //No tiene permisos
	}
         public function _preparar_enviar_mail($id_tarea="")
        {
            if($id_tarea!="")
            {
                
                $this->load->model('gestion_riesgo/gr_usuarios_model','gr_usuarios',true);
                $this->load->model('gestion_riesgo/gr_tareas_model','gr_tarea',true);
                $this->load->model('gestion_riesgo/gr_rpyc_model','gr_rpyc',true);
                $this->load->model('gestion_riesgo/gr_tareas_rpyc_model','gr_tarea_rpyc',true);

                //traigo todos los datos de la tarea
                $tarea=$this->gr_tarea->dameTarea($id_tarea);
                $rpyc=$this->gr_rpyc->dameRpycDatosMail($tarea['id_herramienta']);
                $tareaRpyc=$this->gr_tarea_rpyc->dameTarea($id_tarea,$rpyc['id_rpyc']);
//                echo "<pre>".print_r($tarea,true)."</pre>";
//                echo "<pre>".print_r($rpyc,true)."</pre>";
//                echo "<pre>".print_r($tareaRpyc,true)."</pre>";

                //traigo todos los datos del usuario alta y del usuario responsable
                $usuarioAlta=$this->gr_usuarios->dameUsuarioPorId($tarea['usuario_alta']);
                $usuarioResp=$this->gr_usuarios->dameUsuarioPorId($tarea['usuario_responsable']);
                $usuarioDetector=$this->gr_usuarios->dameUsuarioPorId($tarea['usuario_relacionado']);
    //            echo "<pre>".print_r($usuarioAlta,true)."</pre>";
    //            echo "<pre>".print_r($usuarioResp,true)."</pre>";
//                echo "<pre>".print_r($usuarioDetector,true)."</pre>";


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
                    $puestosId[]=$usuarioAlta['id_puesto'];
                    $idUsuarioNot=$usuarioResp['id_usuario'];

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
                if ($tarea['usuario_relacionado']!=NULL && $usuarioDetector['email']!="" && strpos($usuariosCC,$usuarioDetector['email'])== false) 
                {
                    if($usuarioResp['email']!=$usuarioDetector['email'])
                        $usuariosCC.=", ".$usuarioDetector['email'];
                }
                
                
                
                $usuario=$this->user['nombre']." ".$this->user['apellido'];
                $datos=array();
                
                $datos['id_tarea']=$tarea['id_tarea'];
                $datos['tarea']=$tarea['tarea'];
                $datos['hallazgo']=$tarea['hallazgo'];
                $datos['fecha_limite']=$tarea['fecha_vto'];
                $datos['generoResp']=$usuarioResp['genero'];
                $datos['id_rpyc']=$rpyc['id_rpyc'];
                $datos['areas']=$rpyc['areas'];
                $datos['detector']=$tareaRpyc['detector'];
                $datos['usuario']=$usuarioResp['persona'];
                $datos['usuariosCC']=$usuariosCC;
                $datos['usuarioPara']=$usuarioResp['email'];
                $datos['usuarioRtte']=$usuarioAlta['persona'];
                $datos['motivo']="";                
                $this->_enviarMail($datos);
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

            #msg
            
            <b>Descripci&oacute;n:</b>
            <em>#hallazgo</em>
            
            <b>Tarea:</b>
            <em>#tarea</em>
                        
            <b>Vencimiento:</b>#fecha_limite
        
            <b>Tipo de Herramienta:</b>
            <em>Reuni&oacute;n de Participaci&oacute;n y Consulta (RPyC)</em>
            
            <b>Nro:</b>#rpyc
            <b>Sector/es:</b>#areas
            <b>Hallazgo identificado por:</b>#detector
            
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

            $cuerpo_mail=$mail_alta;
            $subject='Nueva Tarea Nro: '.$datos['id_tarea'];
             
             if($datos['usuarioRtte']!=$datos['usuario'])
             {
                $msg="De la Reunión de Participáción y Consulta realizada y cargada en el sistema por <b>#usuarioRtte</b>, fue #nominado como responsable para resolver la siguiente tarea:";
                $cuerpo_mail=str_replace("#msg"       , $msg , $cuerpo_mail);
             }
             else
             {
                $msg="De la Reunión de Participáción y Consulta realizada y cargada en el sistema por usted, fue #nominado como responsable para resolver la siguiente tarea:";
                $cuerpo_mail=str_replace("#msg"       , $msg , $cuerpo_mail);
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
             
             $cuerpo_mail=str_replace("#rpyc"           ,(4000000+$datos['id_rpyc'])    , $cuerpo_mail);
             $cuerpo_mail=str_replace("#areas"       , $datos['areas']            , $cuerpo_mail);
             $cuerpo_mail=str_replace("#detector"       , $datos['detector']            , $cuerpo_mail);
             $cuerpo_mail=str_replace("#usuarioPara"    , $datos['usuarioPara']         , $cuerpo_mail);
             $cuerpo_mail=str_replace("#usuariosCC"     , $datos['usuariosCC']          , $cuerpo_mail);
             $cuerpo_mail=str_replace("#usuarioRtte"    , $datos['usuarioRtte']         , $cuerpo_mail);
             $cuerpo_mail=str_replace("#hallazgo"       , $datos['hallazgo']            ,$cuerpo_mail);
             $cuerpo_mail=str_replace("#tarea"          , $datos['tarea']               ,$cuerpo_mail);
             $cuerpo_mail=str_replace("#fecha_limite"   , $datos['fecha_limite']        ,$cuerpo_mail);
             $cuerpo_mail=str_replace("#link"           , $link                         ,$cuerpo_mail);
             $cuerpo_mail=str_replace("#firma"          , htmlentities(NOMSIS,ENT_QUOTES,"UTF-8"),$cuerpo_mail);
             
             $this->load->library('email');
//             $this->email->clear();//para cuando uso un bucle
            $this->email->from(SYS_MAIL, NOMSIS);
            $this->email->message($cuerpo_mail);
//            $this->email->set_alt_message();//Sets the alternative email message body
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
            $id_usuario=$this->user['id'];
            $listado=array();
            $this->load->model('gestion_riesgo/gr_rpyc_model','gr_rpyc',true);
            $start = 0;
            $limit = 1000;
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
            $datos = $this->gr_rpyc->listado_excel($usuario,$start, $limit, $filtros,$busqueda, $campos, $sort, $dir);
            
            $datos_audit['id_modulo']=$this->modulo;
            $datos_audit['id_usuario']=$this->user['id'];
            $datos_audit['q_registros']=count($datos);
            $params=array($usuario,$start, $limit, $filtros,$busqueda, $campos, $sort, $dir);
            $datos_audit['params']=json_encode($params);

            $this->load->model("auditoria_model","auditoria_model",true);
            $this->auditoria_model->guardarPedidoExcel($datos_audit);
            
            $titulo = 'RPyC';
            
            $cabecera = array('#','Fecha','Usuario Alta','Áreas que participaron','Usuarios','Contratistas','Tareas');
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
        
}
?>