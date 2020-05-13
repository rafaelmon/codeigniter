<?php
class Capacitaciones extends CI_Controller
{
    private $modulo=53;
    private $modulo_tareas=20;
    private $user;
    private $permisos;
    private $permisos_tareas;
    
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
        $this->load->view('cap/capacitaciones/listado',$variables);
    }
    
    public function listado()
    {
        if ($this->permisos['Listar'])
        {
            $start          = $this->input->post("start");
            $limit          = $this->input->post("limit");
            $sort           = $this->input->post("sort");
            $dir            = $this->input->post("dir");
            
            $campos = "";
            if ($this->input->post("fields"))
            {
                $campos = str_replace('"', "", $this->input->post("fields"));
                $campos = str_replace('[', "", $campos);
                $campos = str_replace(']', "", $campos);
                $campos = explode(",",$campos);
            }
            $busqueda  = ($this->input->post("query"))?$this->input->post("query"):"";
            
            $this->load->model('cap/cap_capacitaciones_model','cap_capacitaciones',true);
            $listado = $this->cap_capacitaciones->listado($start, $limit, $sort,$dir,$busqueda,$campos);
            echo $listado;
            
        }
    }
    public function listadoPartCerrar()
    {
         $this->permisos_tareas= $this->permisos_model->checkIn($this->user['perfil_id'],$this->modulo_tareas);
        if ($this->permisos_tareas['Listar'])
        {
            $id_tarea      =$this->input->post("id_tarea");
            $start          = $this->input->post("start");
            $limit          = $this->input->post("limit");
            $sort           = $this->input->post("sort");
            $dir            = $this->input->post("dir");
            
            
            $this->load->model('cap/cap_participantes_model','cap_participantes',true);
            $listado = $this->cap_participantes->listadoParaFormInformarTarea($id_tarea,$start, $limit, $sort,$dir);
            echo $listado;
            
        }
    }
    
    public function insert()
    {
        if ($this->permisos['Alta'])
        {          
            $datos["titulo"]            = $this->input->post("titulo");
            $datos["descripcion"]       = $this->input->post("descripcion");
            $datos["id_usuario_alta"]   = $this->user['id'];
            $this->load->model('cap/cap_capacitaciones_model','cap_capacitaciones',true);
            
            $control=true;
            foreach ($datos as $atributo=>$dato)
            {
                if($dato=="")
                {
                    $control=false;
                }
            }
            
            if ($control)
            {
                $insert=$this->cap_capacitaciones->insert($datos);
                if($insert)
                    echo 1;
                else 
                    echo 0;
            }
            else
                echo 2;            
        }
        else
            echo 3;
    }
    
    public function combo_responsables()
    {
        if ($this->permisos['Listar'])
        {
            $limit=$this->input->post("limit");
            $start=$this->input->post("start");
            $query=$this->input->post("query");
//            $id_usuario_not=$this->user['id'];
            $this->load->model("usuarios_model","usuarios",true);
            $jcode = $this->usuarios->usuariosCombo($limit,$start,$query);//,$id_usuario_not
            echo $jcode;
        }
    }
    
    public function setResponsables()
    {
        if ($this->permisos['Modificacion'])
        {
            $datos['fecha_vto']             = $this->input->post("fecha");
            $datos['id_herramienta']        = $this->input->post("id_capacitacion");
            $datos['id_grado_crit']         = $this->input->post("id_criticidad");
            $datos['usuario_responsable']   = $this->input->post("id_usuario");
//            $datos["rpd"]                   = $this->input->post("rpd");
            $datos['usuario_alta']          = $this->user['id'];
            $datos['id_tipo_herramienta']   = 9;
            $datos['id_estado']             = 1;
            $id_capacitacion                = $this->input->post("id_capacitacion");
            $id_usuario_alta                = $this->user['id'];
            
            $control=true;
            foreach ($datos as $dato)
            {
                if($dato=="")
                    $control=false;
            }
            
            if ($control)
            {
                $this->load->model('cap/cap_capacitaciones_model','cap_capacitaciones',true); 
                $capacitacion = $this->cap_capacitaciones->dameCapacitacion($id_capacitacion);

                $datos['hallazgo']              = $capacitacion[0]['titulo'];
                $datos['tarea']                 = 'Capacitar a su personal respecto de '.$capacitacion[0]['descripcion'];
                
                $this->load->model('gestion_riesgo/gr_tareas_model','gr_tareas',true);
                $cant = $this->gr_tareas->verificaRePostCap($datos);
                if($cant == 0)
                {
    //                echo "<pre>".print_r($aceptados,true)."</pre>";

                    $this->load->model('gestion_riesgo/mejoracontinua_model','mejora_continua',true);
                    
                    if($datos['usuario_responsable'] == $id_usuario_alta)
                    {
                        if($capacitacion[0]["id_usuario_alta"] != $id_usuario_alta)
                        {   
                            $datos['usuario_alta'] = $capacitacion[0]["id_usuario_alta"];
//                            echo "<pre>".print_r($datos,true)."</pre>";die();
                            $id_accion = 11;
                            $respuesta = $this->_insertTarea($datos,$id_capacitacion,$id_usuario_alta,$id_accion);
                            echo $respuesta;
                        }   
                        else
                            echo 7;
                    }
                    else
                    {
                        $id_accion = 1;
                        $respuesta = $this->_insertTarea($datos,$id_capacitacion,$id_usuario_alta,$id_accion);
                        echo $respuesta;
                    }
                }
                else
                    echo 6;
            }
            else 
                echo 2;
        }
        else
            echo 3;
    }
    
    public function listadoTareas()
    {
        if ($this->permisos['Listar'])
        {
            $start              = $this->input->post("start");
            $limit              = $this->input->post("limit");
            $sort               = $this->input->post("sort");
            $dir                = $this->input->post("dir");
            $id_capacitacion    = $this->input->post("id_capacitacion");
            
            $campos = "";
            if ($this->input->post("fields"))
            {
                $campos = str_replace('"', "", $this->input->post("fields"));
                $campos = str_replace('[', "", $campos);
                $campos = str_replace(']', "", $campos);
                $campos = explode(",",$campos);
            }
            $busqueda  = ($this->input->post("query"))?$this->input->post("query"):"";
            $filtros="";
            $herramienta['id']=$id_capacitacion;
            $herramienta['id_tipo']=9;
            
            $this->load->model('gestion_riesgo/gr_tareas_model','gr_tareas',true);
            
            $listado = $this->gr_tareas->listadoParaHerramientaCap($start, $limit, $filtros, $busqueda, $campos,$sort, $dir, $herramienta);
            echo $listado;
        }
    }
    
    public function _preparar_enviar_mail($id_tarea="",$accion)
    {
        if($id_tarea!="")
        {

            $this->load->model('gestion_riesgo/gr_usuarios_model','gr_usuarios',true);
            $this->load->model('gestion_riesgo/gr_tareas_model','gr_tarea',true);

            //traigo todos los datos de la tarea
            $this->load->model('gestion_riesgo/gr_tareas_model','gr_tarea',true);
            $tarea=$this->gr_tarea->dameTarea($id_tarea);
//                echo "<pre>".print_r($tarea,true)."</pre>";die();

            //traigo todos los datos del usuario alta y del usuario responsable
            $usuarioAlta=$this->gr_usuarios->dameUsuarioPorId($tarea['usuario_alta']);
            $usuarioResp=$this->gr_usuarios->dameUsuarioPorId($tarea['usuario_responsable']);
//                echo "<pre>".print_r($usuarioAlta,true)."</pre>";
//                echo "<pre>".print_r($usuarioResp,true)."</pre>";


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
            if($accion!='rechazada' && $accion!='cerrada')
            {
                $puestosId[]=$usuarioAlta['id_puesto'];
                $idUsuarioNot=$usuarioResp['id_usuario'];
            }
            else
            {
                $puestosId[]=$usuarioResp['id_puesto'];
                $idUsuarioNot=$usuarioAlta['id_usuario'];

            }
            /*//--->eliminado el 26/11/15 por pedido A.Aleman (#28)
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
            if($usuariosCC!="")
                $usuariosCC.=", gestionderiesgos@salesdejujuy.com";
            else
                $usuariosCC="gestionderiesgos@salesdejujuy.com";
            */
            $usuario=$this->user['nombre']." ".$this->user['apellido'];
            $datos=array();

            $datos['accion']=$accion;
            $datos['id_tarea']=$tarea['id_tarea'];
            $datos['tarea']=$tarea['tarea'];
            $datos['hallazgo']=$tarea['hallazgo'];
            $datos['fecha_limite']=$tarea['fecha_vto'];
            $datos['tipo_herramienta']=$tarea['th'];
            $datos['adjuntos']=0;
            switch ($accion)
            {
                case 'alta':
                case 'editada':
                    $datos['usuario']=$usuarioResp['persona'];
//                        $datos['usuariosCC']=$usuariosCC;//--->eliminado el 26/11/15 por pedido A.Aleman (#28)
                    $datos['usuariosCC']="";
                    $datos['usuarioPara']=$usuarioResp['email'];
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
//                        $datos['usuariosCC']=$usuariosCC;//--->eliminado el 26/11/15 por pedido A.Aleman (#28)
                    $datos['usuariosCC']="";
                    $datos['usuarioPara']=$usuarioResp['email'];
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
//                        $datos['usuariosCC']=$usuariosCC;//--->eliminado el 26/11/15 por pedido A.Aleman (#28)
                    $datos['usuariosCC']="";
                    $datos['usuarioPara']=$usuarioResp['email'];
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
//                        $datos['usuariosCC']=$usuariosCC; //--->eliminado el 26/11/15 por pedido A.Aleman (#28)
                    $datos['usuariosCC']="";
                    $datos['usuarioPara']=$usuarioAlta['email'];
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
//                echo "<pre>".print_r($datos,true)."</pre>";
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
                $cuerpo_mail=$mail_cerrada;
                $subject='Tarea Nro: '.$datos['id_tarea'].' - Informada';
                $cuerpo_mail=str_replace("#fecha_cierre"  , $datos['fecha_cierre']     ,$cuerpo_mail);
                $cuerpo_mail=str_replace("#txt_cierre"    , $datos['txt_cierre']     ,$cuerpo_mail);
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
         $cuerpo_mail=str_replace("#usuariosCC"    , $datos['usuariosCC']   , $cuerpo_mail);
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
//            $this->email->cc($datos['usuariosCC']);//--->eliminado el 26/11/15 por pedido A.Aleman (#28)
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
//            echo $cuerpo_mail;die();
        if (ENPRODUCCION)
        {
            if($this->email->send())
                return 0;
            else
                return 2;
        }
        else
        {
                return 0;
        }
//            $this->email->print_debugger();

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
    
    public function combo_participantes()
    {
        if ($this->permisos['Listar'])
        {
            $limit=$this->input->post("limit");
            $start=$this->input->post("start");
            $query=$this->input->post("query");
            $id_usuario_not=$this->user['id'];
            $this->load->model("personas_model","personas",true);
            $jcode = $this->personas->dameCombo($limit,$start,$query,$id_usuario_not);
            echo $jcode;
        }
    }
    public function combo_participantes_form_cerrar()
    {
        $this->permisos_tareas= $this->permisos_model->checkIn($this->user['perfil_id'],$this->modulo_tareas);
        if ($this->permisos_tareas['Listar'])
        {
            $id_tarea =$this->input->post("id");
            $limit=$this->input->post("limit");
            $start=$this->input->post("start");
            $query=$this->input->post("query");
//            $id_usuario_not=$this->user['id'];
            $this->load->model("personas_model","personas",true);
            $jcode = $this->personas->dameComboParaFormCerrar($id_tarea,$limit,$start,$query);
            echo $jcode;
        }
        else
            echo 0;
    }
    function _verificoResponsable($id_tarea,$id_responsable)
    {
        $this->load->model('gestion_riesgo/gr_tareas_model','gr_tarea',true);
        return $this->gr_tarea->verificarResponsable($id_tarea,$id_responsable);
    }
    function _verificoPersona($id_persona)
    {
        $this->load->model('personas_model','personas_model',true);
        return $this->personas_model->checkPersonaPorId($id_persona);
    }
    public function add_participante()
    {
        $id_tarea =$this->input->post("id_tarea");
        $this->load->model('gestion_riesgo/gr_tareas_model','gr_tareas',true);
        $tarea=$this->gr_tareas->dameTarea2($id_tarea);
        if ($tarea!=0)
        {
            $id_usuario=$this->user['id'];
                if ($this->_verificoResponsable($id_tarea,$id_usuario))
                {
                    
                    $tipo=$this->input->post("tipo");
                    $id_persona=$this->input->post("part");
                    $fecha =$this->input->post("fecha");
                    $fecha_txt=str_replace("-","",substr($fecha,0,10));
//                    echo $fecha_txt;
//                    echo "<br>";
//                    echo date('Ymd');
//                    die();
                    if(!$this->_verificoPersona($id_persona))
                        exit ('-2');
                    if($fecha=="" || str_replace("-","",substr($fecha,0,10)) > date('Ymd'))
                        exit ('-3');
                    $tarea=$this->gr_tareas->dameTarea($id_tarea);
//                        echo "<pre>".print_r($tarea,true)."</pre>";
                    $this->load->model('cap/cap_participantes_model','cap_participantes',true);
                    $datos["tipo"]                  = $tipo;
                    $datos["id_persona"]            = $id_persona;
                    $datos["id_tarea"]              = $id_tarea;
                    $datos["id_capacitacion"]       = $tarea['id_herramienta'];
                    $datos["id_usuario_alta"]       = $id_usuario;
                    $datos["fecha_cap"]             = $fecha;
                    $insert=$this->cap_participantes->insert($datos);
                    if($insert)
                        echo 1;
                    else 
                        echo 0;
                }
        }
        else
            echo -1;
    }
    public function delete_participante()
    {
        $this->permisos_tareas= $this->permisos_model->checkIn($this->user['perfil_id'],$this->modulo_tareas);
        if ($this->permisos_tareas['Baja'])
        {
            $id_tarea =$this->input->post("id_tarea");
            $this->load->model('gestion_riesgo/gr_tareas_model','gr_tareas',true);
            $tarea=$this->gr_tareas->dameTarea2($id_tarea);
            if ($tarea!=0)
            {
                $id_usuario=$this->user['id'];
                    if ($this->_verificoResponsable($id_tarea,$id_usuario))
                    {
                        $id=$this->input->post("id");
                        $this->load->model('cap/cap_participantes_model','cap_participantes',true);
                        $delete=$this->cap_participantes->delete($id,$id_tarea);
                        if($delete)
                            echo 1;
                        else 
                            echo 0;
                    }
            }
            else
                echo -1;
        }
        else 
            echo 0;
    }
    public function delete_participantes_all()
    {
        $this->permisos_tareas= $this->permisos_model->checkIn($this->user['perfil_id'],$this->modulo_tareas);
        if ($this->permisos_tareas['Baja'])
        {
            $id_tarea =$this->input->post("id_tarea");
            $this->load->model('gestion_riesgo/gr_tareas_model','gr_tareas',true);
            $tarea=$this->gr_tareas->dameTarea2($id_tarea);
            if ($tarea!=0)
            {
                $id_usuario=$this->user['id'];
                    if ($this->_verificoResponsable($id_tarea,$id_usuario))
                    {
                        $this->load->model('cap/cap_participantes_model','cap_participantes',true);
                        $delete=$this->cap_participantes->delete_all($id_tarea);
                        if($delete)
                            echo 1;
                        else 
                            echo 0;
                    }
            }
            else
                echo -1;
        }
        else
            echo 0;
    }
    
    public function _insertTarea($datos,$id_capacitacion,$id_usuario_alta,$id_accion)
    {
        $this->load->model('gestion_riesgo/mejoracontinua_model','mejora_continua',true);
        
        $insert_id = $this->mejora_continua->insert($datos);           
        if($insert_id)
        {
            $texto="Nueva Tarea CAP";
            $this->grabar_accion($id_accion, $insert_id, $texto); 
            return 1;
        }
        else
            return 4;
    }
    
    public function listadoParticipantes()
    {
        if ($this->permisos['Listar'])
        {
            $start              = $this->input->post("start");
            $limit              = $this->input->post("limit");
            $sort               = $this->input->post("sort");
            $dir                = $this->input->post("dir");
            $id_cap             = $this->input->post("id_capacitacion");
            $id_tarea           = $this->input->post("id_tarea");

            $campos = "";
            if ($this->input->post("fields"))
            {
                $campos = str_replace('"', "", $this->input->post("fields"));
                $campos = str_replace('[', "", $campos);
                $campos = str_replace(']', "", $campos);
                $campos = explode(",",$campos);
            }
            $busqueda  = ($this->input->post("query"))?$this->input->post("query"):"";
            
            $this->load->model('cap/cap_participantes_model','cap_participantes',true);
            
            $listado = $this->cap_participantes->listadoParticipantesPorTareaoCapacitacion($start, $limit, $busqueda, $campos,$sort, $dir,$id_cap,$id_tarea);
            echo $listado;
        }
    }
    
    public function listadoParticipantesTareas()
    {
        if ($this->permisos['Listar'])
        {
            $start              = $this->input->post("start");
            $limit              = $this->input->post("limit");
            $sort               = $this->input->post("sort");
            $dir                = $this->input->post("dir");
            $id_tarea           = $this->input->post("id_tarea");
            
            $campos = "";
            if ($this->input->post("fields"))
            {
                $campos = str_replace('"', "", $this->input->post("fields"));
                $campos = str_replace('[', "", $campos);
                $campos = str_replace(']', "", $campos);
                $campos = explode(",",$campos);
            }
            $busqueda  = ($this->input->post("query"))?$this->input->post("query"):"";
            
            $this->load->model('cap/cap_participantes_model','cap_participantes',true);
            
            $listado = $this->cap_participantes->listadoParticipantesPorTarea($start, $limit, $busqueda, $campos,$sort, $dir,$id_tarea);
            echo $listado;
        }
    }
}
?>