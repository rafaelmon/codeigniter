<?php
class Omc extends CI_Controller
{
    private $modulo=21;
    private $user;
    private $usuario;
    private $permisos;
//    private $origen;
    
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
//            $this->load->model("usuarios_model","usuarios",true);
	}
	
	public function index()
	{
            $modulo = $this->uri->segment(SEGMENTOS_DOM+2);
            if (!isset($modulo)) $modulo=0;
            $this->load->model('permisos_model','permisos_model',true);
            $this->load->model('gestion_riesgo/gr_usuarios_model','gr_usuarios',true);
            $variables['permiso'] = $this->permisos_model->checkIn($this->user['perfil_id'],$modulo);
            $permiso_gdr = $this->gr_usuarios->dameUsuarioPermisoGdr($this->user['id']);
//            $variables['permiso_permiso'] = $this->permisos_model->checkIn($this->user['perfil_id'],$this->modulo);
            $variables['btn'] = $this->user['id'];
            $this->load->model("gestion_riesgo/gr_usuarios_model","gr_usuarios",true);
            $usuario= $this->gr_usuarios->dameUsuarioPorId($this->user['id']);
            $variables['btn_nueva'] = $usuario['realiza_omc'];
            $variables['gr']=($permiso_gdr != 0)?1:0;
//             echo "<pre>".print_r($usuario,true)."</pre>";
            $this->load->view('gestion_riesgo/omc/listado',$variables);
	}
	
	public function listado()
	{
		if ($this->permisos['Listar'])
                {
                    $id_usuario=$this->user['id'];
                    $listado=array();
                    $this->load->model('gestion_riesgo/gr_omc_model','gr_omc',true);
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
                    $listado = $this->gr_omc->listado($usuario,$start, $limit, $filtros,$busqueda, $campos, $sort, $dir);
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
                    $id_omc=$this->input->post("id");
                    $herramienta=array();
                    $herramienta['id_tipo']=4;
                    $herramienta['id']=$id_omc;
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
                    $listado = $this->gr_tareas->listadoParaOmc($usuario,$start, $limit, $filtros,$busqueda, $campos, $sort, $dir,$herramienta);
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
            
            if(in_array(4, $areasSuperiores) || in_array(155, $areasSuperiores) )
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
                $datos=array();
                $datos['id_observador']         = $this->user['id'];
                $datos['id_acomp1']             = ($this->input->post("acomp1")=="")?NULL:$this->input->post("acomp1");//&&$this->input->post("contra1")!=""
                $datos['id_acomp2']             = ($this->input->post("acomp2")=="")?NULL:$this->input->post("acomp2");
//                $datos['id_contratista1']       =($this->input->post("contra1")==""&&$this->input->post("acomp1")!="")?NULL:$this->input->post("contra1");
//                $datos['id_acomp2']             =($this->input->post("acomp2")==""&&$this->input->post("contra2")!="")?NULL:$this->input->post("acomp2");
//                $datos['id_contratista2']       =($this->input->post("contra2")==""&&$this->input->post("acomp2")!="")?NULL:$this->input->post("contra2");
                $datos['id_empresa']            = 2;//$this->input->post("empresa");
                $datos['id_sitio']              = $this->input->post("sitio");
                $datos['id_sector']             = $this->input->post("sector");
                $datos['analisis_riesgo']       = ($this->input->post("ar")=="no")?0:1;
//                echo "<pre>".print_r($datos,true)."</pre>";
                $control=true;
                foreach ($datos as $dato)
                {
                    if($dato==""&&$dato!=NULL)
                        $control=false;
                }
                if ($control)
                {
                    //Verifico observador y acompañantes sean gerentes
                    $this->load->model("gestion_riesgo/gr_usuarios_model","gr_usuarios",true);
                    if( $this->gr_usuarios->checkRealizaOMC($datos['id_observador'])!=0 )
                    {
                        if($datos['id_acomp1'] != $datos['id_acomp2'])
                        {
                            //verifico que no sea rePost
                            $this->load->model("gestion_riesgo/gr_omc_model","omc",true);

                            if ($this->omc->verificaRePost($datos)==0)
                            {
                                $insert_id=$this->omc->insert($datos);
                                if($insert_id)
                                {
//                                    if (ENPRODUCCION)
//                                        $this->_preparar_enviar_mail($insert_id,'alta');
                                    echo 1; //todo OK
                                }
                                else
                                    echo 2; //Error al insertar el registro
                            }
                            else
                                echo 0;//Registro repetido (RePost)
                        }
                        else
                            echo 5;//Mismo usuario para campos acompañantes
                    }
                    else
                            echo 4;//El usuario no es gerente
                        
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
         public function sitios_combo()
	{
            $id_th=4;
            $id_empresa=2;//$this->input->post("id");
            $this->load->model('gestion_riesgo/gr_r_sectores_model','r_sectores',true);
            $var=$this->r_sectores->dameComboSitiosPorEmpresa($id_th,$id_empresa);
            echo $var;	
	}
         public function sectores_combo()
	{
            $id_th=4;
            $id_empresa=2;//$this->input->post("id_e");
            $id_sitio=$this->input->post("id_s");
            $this->load->model('gestion_riesgo/gr_r_sectores_model','r_sectores',true);
            $var=$this->r_sectores->dameComboSectoresPorEmpresaPorSitio($id_th,$id_empresa,$id_sitio);
            echo $var;	
	}

        public function combo_acomp()
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
        public function combo_acomp2()
	{
            $limit=$this->input->post("limit");
            $start=$this->input->post("start");
            $query=$this->input->post("query");
            $id_usuario_not=array($this->user['id']);
            $id_usuario_acomp=$this->input->post('id_acomp1')==null?"":$this->input->post('id_acomp1');
            $this->load->model("gestion_riesgo/gr_usuarios_model","gr_usuarios",true);
            $jcode = $this->gr_usuarios->usuariosRealizaOmcCombo($limit,$start,$query,$id_usuario_not,$id_usuario_acomp);
            echo $jcode;
	}
//        public function combo_contra()
//	{
//            $limit=$this->input->post("limit");
//            $start=$this->input->post("start");
//            $query=$this->input->post("query");
//            $id_usuario_not=array($this->user['id']);
//            $this->load->model("gestion_riesgo/gr_contratistas_model","contratistas",true);
//            $jcode = $this->contratistas->contratistasCombo($limit,$start,$query);
//            echo $jcode;
//	}
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
         public function insert_tarea()
	{
            if ($this->permisos['Alta'])		
            {
                $id_usuario=$this->user['id'];
                $id_omc=$this->input->post("id_omc");
                //verifico que sea el usuario observador
                $this->load->model("gestion_riesgo/gr_omc_model","gr_omc",true);
                 if ($this->gr_omc->verificaObservador($id_usuario,$id_omc)==1)
                 {
                    $datos=array();
                    $datos['usuario_alta']          =$id_usuario;
                    $datos['id_tipo_herramienta']   =4; //4=Observación para la mejora continua (OMC)
                    $datos['id_herramienta']        =$id_omc;
                    $datos['usuario_responsable']   =$this->input->post("responsable");
                    $datos['hallazgo']              =$this->input->post("hallazgo");
                    $datos['id_grado_crit']         =$this->input->post("grado_crit");
                    $datos['tarea']                 =$this->input->post("tarea");
                    $datos['fecha_vto']             =$this->input->post("fecha");
                    $datos['id_estado']             =1;
                    //$datos['rpd']                   =$this->input->post("rpd");

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
                                //inserto historial de accion
                                $id_accion=1;
                                $texto="Nueva tarea desde OMC";
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
                $this->load->model('gestion_riesgo/gr_omc_model','gr_omc',true);

                //traigo todos los datos de la tarea
                $tarea=$this->gr_tarea->dameTarea($id_tarea);
                $omc=$this->gr_omc->dameOmcDatosMail($tarea['id_herramienta']);
//                echo "<pre>".print_r($tarea,true)."</pre>";
//                echo "<pre>".print_r($omc,true)."</pre>";

                //traigo todos los datos del usuario alta y del usuario responsable
                $usuarioAlta=$this->gr_usuarios->dameUsuarioPorId($tarea['usuario_alta']);
                $usuarioResp=$this->gr_usuarios->dameUsuarioPorId($tarea['usuario_responsable']);
    //            echo "<pre>".print_r($usuarioAlta,true)."</pre>";
//                echo "<pre>".print_r($usuarioResp,true)."</pre>";
//                echo 'algo'.$usuarioResp;
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
                if ($omc['mailacomp1']!="" && strpos($usuariosCC,$omc['mailacomp1'])== false) 
                {
                        $usuariosCC.=", ".$omc['mailacomp1'];
                }
//                if ($omc['mailacomp2']!="" && strpos($usuariosCC,$omc['mailacomp2'])== false) 
//                {
//                        $usuariosCC.=", ".$omc['mailacomp2'];
//                }

                $usuario=$this->user['nombre']." ".$this->user['apellido'];
                $datos=array();
                
                $datos['accion']=$accion;
                $datos['id_tarea']=$tarea['id_tarea'];
                $datos['tarea']=$tarea['tarea'];
                $datos['hallazgo']=$tarea['hallazgo'];
                $datos['fecha_limite']=$tarea['fecha_vto'];
                $datos['generoResp']=$usuarioResp['genero'];
                $datos['id_omc']=$omc['id_omc'];
                $datos['sector']=$omc['sector'];
                $datos['acomp1']=$omc['acomp1'];
//                $datos['acomp2']=$omc['acomp2'];
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

            De la Observación para la Mejora continua realizada por <b>#usuarioRtte</b> fue #nominado como responsable para resolver la siguiente tarea:
            
            <b>Descripci&oacute;n:</b>
            <em>#hallazgo</em>
            
            <b>Tarea:</b>
            <em>#tarea</em>
                        
            <b>Vencimiento:</b>#fecha_limite
        
            <b>Tipo Herramienta:</b>
            <em>Observaciones de Mejora Continua (OMC)</em>
            
            <b>Nro:</b>#omc
            <b>Acompa&ntilde;ante:</b>#acomp1y2
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
             
             $cuerpo_mail=str_replace("#omc"   ,(1000000+$datos['id_omc']), $cuerpo_mail);
             $cuerpo_mail=str_replace("#sector"   , $datos['sector'], $cuerpo_mail);
             $cuerpo_mail=str_replace("#acomp1y2"   , $datos['acomp1'], $cuerpo_mail);
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
        
        public function grabar_accion($id_accion,$id_tarea,$texto)
        {
            $this->load->model("gestion_riesgo/gr_historial_acciones_model","gr_historial_acciones",true);
            
            $datos['id_usuario']    =$this->user['id'];
            $datos['id_tarea']      =$id_tarea;
            $datos['id_accion']     =$id_accion;
            $datos['texto']         =$texto;
            
            $insert_id=$this->gr_historial_acciones->insert($datos);
            return $insert_id;
            
        }
        
        public function aprobar()
	{
            $id_usuario=$this->user['id'];
            $this->load->model("gestion_riesgo/gr_usuarios_model","gr_usuarios",true);
            $usuario=$this->gr_usuarios->dameUsuarioPorId($this->user['id']);
//            echo "<pre>".print_r($usuario,true)."</pre>";die();
            if ($usuario['gr'] == 1)
            {
                if ($this->permisos['Modificacion'])		
                {
                    $id=$this->input->post("id");
                    if ($id)
                    {
                        $datos['estado']=2;
                        $datos['id_aprobador']=$id_usuario;

                        $this->load->model("gestion_riesgo/gr_omc_model","gr_omc",true);

                        $update=$this->gr_omc->update($id,$datos);
                        if($update==1)
                            echo "({success: true, error :0})";
                        else
                            echo "({success: false, error :'Error al guardar información, por favor comunique este error a sistemas...'})"; //error de permiso,
                        return $update;
                    }
                    else
                        echo  "({success: false, error :'Observacion incorrecta'})"; //error de permiso,
                }
                else
                    echo  "({success: false, error :'Su usuario no tiene el rol correspondiente para la gesti�n que desea realizar'})"; //error de permiso,
            }
            else
                echo  "({success: false, error :'Su usuario no tiene el rol correspondiente para la gesti�n que desea realizar'})"; //error de permiso,
        }
        
    public function excel()
    {
        if ($this->permisos['Listar'])
        {
            $this->load->library('excel');
//            echo $busqueda;
//            echo "<pre>".print_r($campos,true)."</pre>";
            $listado=array();
            $this->load->model('gestion_riesgo/gr_omc_model','gr_omc',true);
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
            
            $datos = $this->gr_omc->listado_excel($start, $limit, $busqueda, $campos, $sort, $dir, $filtros);
            
            $datos_audit['id_modulo']=$this->modulo;
            $datos_audit['id_usuario']=$this->user['id'];
            $datos_audit['q_registros']=count($datos);
            $params=array($start, $limit, $filtros,$busqueda, $campos, $sort, $dir);
            $datos_audit['params']=json_encode($params);

            $this->load->model("auditoria_model","auditoria_model",true);
            $this->auditoria_model->guardarPedidoExcel($datos_audit);
            
            $titulo = 'OMC';
            
            $cabecera = array('#','Fecha','Observador','Acompañante 1','Acompañante 2','Sitio','Sector','Tareas','AR Eficiente?','Estado');
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
//        echo $filename;die();
        header('Content-Type:application/vnd.ms-excel'); //mime type
       header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name          
        header('Cache-Control: max-age=0'); //no cache


        $this->excel->getProperties()
            ->setCreator("Sistema de Mejora Continua (SMC)") // Nombre del autor
            ->setLastModifiedBy("Sistema de Mejora Continua (SMC)") //Ultimo usuario que lo modificó
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