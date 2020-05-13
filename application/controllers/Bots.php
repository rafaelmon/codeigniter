<?php
class Bots extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }
    public function comput_vtos()
    {
        //-----------VENCIMIENTO DE TAREAS----------//
        $this->load->model('gestion_riesgo/gr_tareas_model','gr_tarea',true);
        $qVencidasHoy   = $this->gr_tarea->marcarVencidasHoy();
        if ($qVencidasHoy!=0)
        {
            $arrayVencidasHoy    =$this->gr_tarea->dameVencidasHoy();
            $this->load->model("gestion_riesgo/gr_historial_acciones_model","gr_historial_acciones",true);
            $id_accion=7;
            foreach($arrayVencidasHoy as $vencida)
            {
                $tareasVencidas[]=$vencida['id_tarea'];
                //inserto historial de accion
                $datos['id_tarea']      =$vencida['id_tarea'];
                $datos['id_accion']     =$id_accion;
//              $datos['texto']         =$texto;
                $insert=$this->gr_historial_acciones->insert($datos);
            }        
            $update=$qVencidasHoy.'Tarea/s Vencida/s'.implode(",", $tareasVencidas);
        }
        else
            $update='Hoy no hay tareas vencidas';
        
        //Registro la ejecución del cron
        $datosCron['cron']='bots/comput_vtos(tareas)';
        $datosCron['detalle']=$update;
        $datosCron['echo']=$update;
        $this->load->model('crons_model','crons',true);
        $this->crons->insert($datosCron);
        
            
    }
    public function comput_vtos_old()
    {
        //-----------VENCIMIENTO DE TAREAS----------//
        $this->load->model('gestion_riesgo/gr_tareas_model','gr_tarea',true);
        $this->load->model('gestion_riesgo/gr_historial_model','gr_historial',true);
        $vencidasHoy    =$this->gr_tarea->dameVencidasHoy();
        $arrayVencidasHoy=array();
        
        if ($vencidasHoy!=0)
        {
            $id_estado=5;
            $datosHist['id_estado']=$id_estado;
            foreach ($vencidasHoy as $value)
            {
                $arrayVencidasHoy[]=$value['id_tarea'];
                $datosHist['id_tarea']=$value['id_tarea'];
                $this->gr_historial->insertVencidas($datosHist);
            }
            $update=$this->gr_tarea->cambiarEstado($arrayVencidasHoy,$id_estado);
            
                       
            
        }
        else
            $update='Hoy no hay tareas vencidas';
        
        //Registro la ejecución del cron
        $datos['cron']='bots/comput_vtos(tareas)';
        $datos['detalle']='TareasVencidasHoy: '.implode(",", $arrayVencidasHoy);
        $datos['echo']=$update;
        $this->load->model('crons_model','crons',true);
        $this->crons->insert($datos);
        
            
    }
    public function comput_vto_inv()
    {
        //-----------VENCIMIENTO DE INVESTIGACION rmc----------//
        $this->load->model('gestion_riesgo/gr_rmc_model','gr_rmc',true);
        $invVencidasHoy    =$this->gr_rmc->dameRmcConInvVencidasHoy();
        
//        echo "<pre>".print_r($invVencidasHoy,true)."</pre>";
        
        $arrayInvVencidasHoy=array();
        $vencer=0;
        if ($invVencidasHoy!=0)
        {
            $vencer = $this->gr_rmc->vencerRmc();
            foreach ($invVencidasHoy as $value)
            {
                $arrayInvVencidasHoy[]=$value['id_rmc'];
            }
            
        }
        else
            $arrayInvVencidasHoy[]='0';
        
        $datos['cron']='bots/comput_vtos(INV-RI)';
        $datos['detalle']='RIVencidosHoy: '.implode(",", $arrayInvVencidasHoy);
        $this->load->model('crons_model','crons',true);
        $datos['echo']=$vencer;
        $this->crons->insert($datos);
        
//        echo "<pre>".print_r($datos,true)."</pre>";
        
        
            
    }
    public function alert_morosos()
    {
        $this->load->model('gestion_riesgo/gr_tareas_model','gr_tarea',true);
        $this->load->model('crons_model','crons',true);
        $cron=$this->crons->verificaCronAlertMorosos();
        $datos['cron']='bots/alert_morosos';
        if ($cron==0)
        {
            $tareasVencidas=$this->gr_tarea->dameVencidas();
            if ($tareasVencidas!=0)
            {
                $arrayIdVencidas=array();
                foreach ($tareasVencidas as $value)
                {
                    if ($value['dias_vto']==1 || ($value['dias_vto']%7)==0)
                        $arrayIdVencidas[]=$value['id_tarea'];
                }
                if (!empty($arrayIdVencidas))
                {
                    foreach ($arrayIdVencidas as $value)
                    {
                            $send_mail=$this->_preparar_enviar_mail($value);
                            //Registro la ejecución del cron
                            $datos['detalle']='Tarea Nro '.$value.' de ('.implode(",", $arrayIdVencidas).')';
                            $datos['echo']=$send_mail;
                            $this->crons->insert($datos);
                            sleep(4);
                    }
                }
                else
                {
                    
                    $datos['detalle']='No hay Tareas para Alertar con 1 o %7 dias';
                    $datos['echo']='0';
                    $this->crons->insert($datos);
                }
            }
            else
            {
                $datos['detalle']='No hay Tareas Vencidas';
                $datos['echo']='0';
                $this->crons->insert($datos);

            }
        }
        else
        {
            $datos['detalle']='Cron ya ejecutado...';
            $datos['echo']='0';
            $this->crons->insert($datos);
            die();
        }
            
    }
//    public function alert_morosos_inv()
//    {
//        $this->load->model('gestion_riesgo/gr_rmc_model','gr_rmc',true);
//        $invVencidas    =$this->gr_rmc->dameRmcConInvVencidas();
//        
//        echo "<pre>".print_r($invVencidas,true)."</pre>";
//        $arrayInvVencidas=array();
//        $vencer=0;
//        if ($invVencidas!=0)
//        {
//            $vencer = $this->gr_rmc->vencerRmc();
//            foreach ($invVencidas as $value)
//            {
////                if ($value['dias_vto']==1 || ($value['dias_vto']%3)==0)
//                if ($value['dias_vto']>=1)
//                    $arrayInvVencidas[]=$value['id_rmc'];
//            }
//            
//        }
//        else
//            $arrayInvVencidas[]='0';
//        
//        echo "<pre>".print_r($arrayInvVencidas,true)."</pre>";
//        
//    }
    public function enviar_manual()
    {
        $id_tarea=16877;
        $mail=$this->_preparar_enviar_mail($id_tarea,'proximo_vencimiento');
        echo $mail;
    }
    public function _preparar_enviar_mail($id_tarea="",$accion="")
    {
        if($id_tarea!="")
        {

            $this->load->model('gestion_riesgo/gr_usuarios_model','gr_usuarios',true);
            $this->load->model('gestion_riesgo/gr_tareas_model','gr_tarea',true);

            //traigo todos los datos de la tarea
            $tarea=$this->gr_tarea->dameTarea($id_tarea);
//            echo "<pre>".print_r($tarea,true)."</pre>";

            //traigo todos los datos del usuario alta, del usuario responsable y del supervisor del usuario responsable
            $usuarioAlta=$this->gr_usuarios->dameUsuarioPorId($tarea['usuario_alta']);
            $usuarioResp=$this->gr_usuarios->dameUsuarioPorId($tarea['usuario_responsable']); 
//            echo "<pre>".print_r($usuarioResp,true)."</pre>";
            $supervisor=$this->_dameSupervisorHabilitado($tarea['usuario_responsable']);
            $usuarioSup=$this->gr_usuarios->dameUsuarioPorId($supervisor[0]['id_usuario']); 

            if($supervisor==0)
                $usuarioSup['email']='errorSuervisorUsuarioResp+smc@salesdejujuy.com';

            $datos['id_tarea']=$tarea['id_tarea'];
            $datos['tarea']=$tarea['tarea'];
            $datos['hallazgo']=$tarea['hallazgo'];
            $datos['fecha_limite']=$tarea['fecha_vto'];
            $datos['tipo_herramienta']=$tarea['th'];
            $datos['dias_vto']=$tarea['dias_vto'];
            $datos['usuario']=$usuarioResp['persona'];
            $datos['solicitante']=$usuarioAlta['persona'];
            $datos['supervisor']=$usuarioSup['persona'];
            
            if($usuarioResp['habilitado']==0)
            {
                $arrayMailsPara=array($usuarioResp['email'],$usuarioSup['email']);
                $datos['usuarioPara']=array_filter(array_unique($arrayMailsPara));
                $datos['usuariosCC']=$usuarioAlta['email'];
            }
            else
            {
                $datos['usuarioPara']=$usuarioResp['email'];
                $arrayMailsCc=array($usuarioAlta['email'],$usuarioSup['email']);
                $datos['usuariosCC']=array_filter(array_unique($arrayMailsCc));
            }
            
            if($usuarioAlta['habilitado']==0)
            {
                $supervisorUsAlta=$this->_dameSupervisorHabilitado($tarea['usuario_alta']);
                if ($supervisorUsAlta!=0)
                {
                    $usuarioSupAlta=$this->gr_usuarios->dameUsuarioPorId($supervisorUsAlta[0]['id_usuario']); 
                    $datos['usuariosCC']=array_filter(array_unique(array_push($datos['usuariosCC'],$usuarioSupAlta['email'])));
                }
                else 
                {
                    $datos['usuariosCC'][]='errorSuervisorUsuarioAlta+smc@salesdejujuy.com';
                    $datos['usuariosCC']=array_filter(array_unique($datos['usuariosCC']));
                }
            }
            
            $datos['generoResp']=$usuarioResp['genero'];
            $datos['usuarioRtte']=$usuarioAlta['persona'];
            $datos['motivo']="";
            $send_mail=$this->_enviarMail($datos,$accion);
            return $send_mail;
        }
        return 0;
    }
        
        function _enviarMail($datos,$accion="")
        {
            $link='www.polidata.com.ar/sdj/admin';
                        
$mail_vto=<<<HTML
<html> 
    <head>
        <title>SMC</title>
        
    </head>
    <body>
    <div style="color:#000000; font-family:Verdana, Arial, Helvetica, sans-serif; text-align:Left; font-size:13px;">
        <pre>
            #persona,

            #recordatorio tarea de referencia asignada a usted por <b>#usuarioRtte</b> se encuentra vencida #dias_vto:
            
            <b>Descripci&oacute;n:</b>
            <em>#hallazgo</em>
            
            <b>Tarea:</b>
            <em>#tarea</em>
                        
            <b>Vencimiento:</b>
            #fecha_limite
            
            <b>Tipo de Herramienta:</b>
            #tipo_herramienta
            
            Por favor, complete su gesti&oacute;n ingresando al sistema desde <a href="#link" target="_blank">aqu&iacute;</a>, o copiando y pegando la siguiente direcci&oacute;n en su navegador web <b>Mozilla Firefox</b> o <b>Google Chrome</b>: 
            #link.
            

            Atentamente
            #firma
            
            Importante: Mensaje generado autom&aacute;ticamente, por favor no responder.
            <hr>
            Asunto:#asunto</br>
              Para: #usuarioPara </br>
                CC: #usuariosCC </br>
           <hr>

        </pre>
        </div>
    </body>
</html>
HTML;
$mail_proxvto=<<<HTML
<html> 
    <head>
        <title>SMC</title>
        
    </head>
    <body>
    <div style="color:#000000; font-family:Verdana, Arial, Helvetica, sans-serif; text-align:Left; font-size:13px;">
        <pre>
            #persona,

            #recordatorio tarea de referencia asignada a usted por <b>#usuarioRtte</b> se encuentra próxima al vencimiento
            
            <b>Descripci&oacute;n:</b>
            <em>#hallazgo</em>
            
            <b>Tarea:</b>
            <em>#tarea</em>
                        
            <b>Vencimiento:</b>
            #fecha_limite
            
            <b>Tipo de Herramienta:</b>
            #tipo_herramienta
            
            Por favor, complete su gesti&oacute;n ingresando al sistema desde <a href="#link" target="_blank">aqu&iacute;</a>, o copiando y pegando la siguiente direcci&oacute;n en su navegador web <b>Mozilla Firefox</b> o <b>Google Chrome</b>: 
            #link.
            

            Atentamente
            #firma
            
            Importante: Mensaje generado autom&aacute;ticamente, por favor no responder.
            <hr>
              Asunto:#asunto </br>
                Para: #usuarioPara </br>
                  CC: #usuariosCC </br>
              
           <hr>

        </pre>
        </div>
    </body>
</html>
HTML;
            switch ($accion) 
            {
                case "":
                case "vencimiento":
                    $cuerpo_mail=$mail_vto;
                    $subject='Tarea Nro: '.$datos['id_tarea'].' - Vencida';
                    if ($datos['dias_vto']>1)
                    {
                        $cuerpo_mail=str_replace("#recordatorio"  , 'Le recordamos que la' ,$cuerpo_mail);
                        $cuerpo_mail=str_replace("#dias_vto"  , 'hace '.$datos['dias_vto'].' d&iacute;as' ,$cuerpo_mail);

                    }
                    else
                    {
                        $cuerpo_mail=str_replace("#recordatorio"  , "La" ,$cuerpo_mail);
                        $cuerpo_mail=str_replace("#dias_vto"  , "" ,$cuerpo_mail);
                    }
                    break;
                case "proximo_vencimiento":
                    $cuerpo_mail=$mail_proxvto;
                    $cuerpo_mail=str_replace("#recordatorio"  , "Le informamos que la" ,$cuerpo_mail);
                    $subject='Tarea Nro: '.$datos['id_tarea'].' - Vencimiento Próximo';
                    break;

                default:
                    break;
            }
            
            if ($datos['generoResp']=='F')
                $cuerpo_mail=str_replace("#persona"       , 'Estimada '.$datos['usuario']      , $cuerpo_mail);
             else
                $cuerpo_mail=str_replace("#persona"       , 'Estimado '.$datos['usuario']      , $cuerpo_mail);
            
            $cuerpo_mail=str_replace("#asunto"       ,$subject, $cuerpo_mail);
            $cuerpo_mail=str_replace("#usuarioPara"   , $datos['usuarioPara']  , $cuerpo_mail);
            $cuerpo_mail=str_replace("#usuariosCC"    , implode(",",$datos['usuariosCC'])  , $cuerpo_mail);
            $cuerpo_mail=str_replace("#usuarioRtte"   , $datos['usuarioRtte']  , $cuerpo_mail);
            $cuerpo_mail=str_replace("#hallazgo"      , $datos['hallazgo']     ,$cuerpo_mail);
            $cuerpo_mail=str_replace("#tarea"         , $datos['tarea']        ,$cuerpo_mail);
            $cuerpo_mail=str_replace("#fecha_limite"  , $datos['fecha_limite'] ,$cuerpo_mail);
            $cuerpo_mail=str_replace("#tipo_herramienta"  , $datos['tipo_herramienta'] ,$cuerpo_mail);
            $cuerpo_mail=str_replace("#link"          , $link                  ,$cuerpo_mail);
            $cuerpo_mail=str_replace("#firma"         , htmlentities(NOMSIS,ENT_QUOTES,"UTF-8"),$cuerpo_mail);
             
                
            
             $this->load->library('email');
//             $this->email->clear();//para cuando uso un bucle
            $this->email->from(SYS_MAIL, NOMSIS);
            $this->email->message($cuerpo_mail);
            $this->email->subject($subject);
//            $this->email->set_alt_message();//Sets the alternative email message body
            
            $this->email->to($datos['usuarioPara']);
            $this->email->cc($datos['usuariosCC']);
            IF (MAILBCC && MAILBCC !="")
                $this->email->bcc(MAILBCC);
            
//            echo $cuerpo_mail;
//            die();
//            return 0;
            if (ENPRODUCCION)
            {
                if($this->email->send())
                    return 1;
                else
                    return 0;
            }
            else
            {
                    return 0;
            }

        }
        public function _adjunto()
        {
            
            $this->load->library('email');
            $this->email->clear(TRUE);
            $this->email->from(SYS_MAIL, NOMSIS);
            $this->email->message('Esto es una prueba de tareas automáticas por cronjobs');
            $this->email->to('');
            $this->email->subject('Probando adjunto');
            $this->email->attach(PATH_REPO_FILES.'/prueba1.xlsx');
            $this->email->send();
            echo $this->email->print_debugger();
        }
        
        public function _nuevomail()
        {
            $this->load->library('oc_email');
            $mail['to']='fmoraiz@gmail.com';
            $mail['cc']='fmoraiz+cc@gmail.com';
            $mail['cco']='fmoraiz+cco@gmail.com';
            $mail['asunto']='asunto';
            $mail['adjunto']='adjunto';
            $mail['texto']='texto del msg';
            $this->oc_email->enviar($mail);
        }
        public function _repo()
        {
            $this->load->model('reports_model','reports',true);
            $xml=$this->reports->sp_prueba1();
            echo $xml;
                       
        }
        public function _subareas($id_area)
        {
            $areasInferiores[]=$id_area;
            $this->load->model('gestion_riesgo/gr_areas_model','gr_areas',true);
            
            //Areas Inferiores: recorro el nro máximo de niveles posible para traer las áreas inferiores
            for($i=0;$i<1;$i++)
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
//            echo "<pre>".print_r($areasInferiores,TRUE),"</pre>";
            $areas=  str_replace("'","",implode(",",$areasInferiores));
            
            $this->load->model('gestion_riesgo/gr_usuarios_model','gr_usuarios',true);
//            echo $areas;
            
            $em=$this->gr_usuarios->dameEmailAreasInferiores($areasInferiores);
            $arrayEmails=array();
            foreach ($em as $email)
            {
                if($email['mailing']==1)
                    $arrayEmails[]=$email['email'];
            }
//            $emails= array_unique(array_merge($areasInferiores,$arrayAreas));
            $emails=  str_replace("'","",implode(",",$arrayEmails));
//            echo "<br>";
//            echo $emails;
            
            
            
        }
        public function _deltaPass()
        {
$mail_vto=<<<HTML
<html> 
    <head>
        <title>SMC</title>
        
    </head>
    <body>
    <div style="color:#000000; font-family:Verdana, Arial, Helvetica, sans-serif; text-align:Left; font-size:13px;">
        <pre>
            #persona,

            Por motivos de seguridad su contraseña provisoria a sido modificada y su nueva contraseña es: #nuevopass
            
            Por favor, ingrese al sistema y desde la configuracion de su perfil cambiela por una personal.
            
            Atentamente
            #firma
            
            Importante: Mensaje generado autom&aacute;ticamente, por favor no responder.          

        </pre>
        </div>
    </body>
</html>
HTML;


            $cuerpo_mail=$mail_vto;
            $subject='Tarea Nro: '.$datos['id_tarea'].' - Vencida';
            
            if ($datos['generoResp']=='F')
                $cuerpo_mail=str_replace("#persona"       , 'Estimada '.$datos['usuario']      , $cuerpo_mail);
             else
                $cuerpo_mail=str_replace("#persona"       , 'Estimado '.$datos['usuario']      , $cuerpo_mail);
            
            $cuerpo_mail=str_replace("#usuarioPara"   , $datos['usuarioPara']  , $cuerpo_mail);
            $cuerpo_mail=str_replace("#usuariosCC"    , $datos['usuariosCC']   , $cuerpo_mail);
            $cuerpo_mail=str_replace("#usuarioRtte"   , $datos['usuarioRtte']  , $cuerpo_mail);
            $cuerpo_mail=str_replace("#hallazgo"      , $datos['hallazgo']     ,$cuerpo_mail);
            $cuerpo_mail=str_replace("#tarea"         , $datos['tarea']        ,$cuerpo_mail);
            $cuerpo_mail=str_replace("#fecha_limite"  , $datos['fecha_limite'] ,$cuerpo_mail);
            $cuerpo_mail=str_replace("#link"          , $link                  ,$cuerpo_mail);
            $cuerpo_mail=str_replace("#firma"         , htmlentities(NOMSIS,ENT_QUOTES,"UTF-8"),$cuerpo_mail);
             
            if ($datos['dias_vto']>1)
            {
                $cuerpo_mail=str_replace("#recordatorio"  , 'Le recordamos que la' ,$cuerpo_mail);
                $cuerpo_mail=str_replace("#dias_vto"  , 'hace '.$datos['dias_vto'].' d&iacute;as' ,$cuerpo_mail);
                
            }
            else
            {
                $cuerpo_mail=str_replace("#recordatorio"  , "La" ,$cuerpo_mail);
                $cuerpo_mail=str_replace("#dias_vto"  , "" ,$cuerpo_mail);
            }
                
            
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
            if (ENPRODUCCION)
            {
                if($this->email->send())
                    return $this->email->print_debugger();
                else
                    return 0;
            }
            else
            {
                    return 0;
            }            
            
        }
        public function recalcular_bc($id_bc)
        {
            if(isset($id_bc) && $id_bc!="")
            {
                $this->load->model('gestion_riesgo/gr_bc_model','gr_bc',true);
                $this->load->model('gestion_riesgo/gr_usuarios_model','gr_usuarios',true);
//                $this->load->model('gestion_riesgo/gr_areas_model','gr_areas',true);
//                $this->load->model('gestion_riesgo/gr_tareas_model','gr_tareas',true);
                
                $bc=$this->gr_bc->dameBc($id_bc);
                echo "<pre>".print_r($bc,true)."</pre>";
                
                $arbol=$this->_dameArbolDeUsuarios($bc['id_usuario_inicio']);
                echo "<pre>".print_r($arbol,true)."</pre>";
            }
        }
        function _dameArbolDeUsuarios($id_usuario_inicio)
        {
            $id_bc=1;
            $arbol=array();
            $this->load->model('gestion_riesgo/gr_usuarios_model','gr_usuarios',true);
            
            $ciclo1=$this->gr_usuarios->dameArbolInferior($id_usuario_inicio,$id_bc);
            $q=0;
            $qVencidas=0;
            $qNoAplica=0;
            $qCerradas=0;
            $qAbiertas=0;
            $qPendientes=0; //que todavía no recibieron la BC
            $x=0;
            foreach ($ciclo1 as &$c1)
            {
                $q++;
                switch ($c1['estado'])
                {
                    case 1:
                        $qAbiertas++;
                        break;
                    case 2:
                        $qCerradas++;
                        break;
                    case 5:
                        $qVencidas++;
                        break;
                    case 6:
                        $qNoAplica++;
                        break;
                    case "":
                        $qPendientes++;
                        break;
                }
                $c1['n']=$q;
                $c1['dr']=$this->gr_usuarios->dameArbolInferior($c1['id_usuario'],$id_bc);
                if($c1['dr']!=0 && $c1['estado']!=6)
                {
                    foreach ($c1['dr'] as &$c2)
                    {
                        $q++;
                        switch ($c2['estado'])
                        {
                            case 1:
                                $qAbiertas++;
                                break;
                            case 2:
                                $qCerradas++;
                                break;
                            case 5:
                                $qVencidas++;
                                break;
                            case 6:
                                $qNoAplica++;
                                break;
                            case "":
                                $qPendientes++;
                                break;
                        }
                        $c2['n']=$q;
                        $c2['dr']=$this->gr_usuarios->dameArbolInferior($c2['id_usuario'],$id_bc);
                        
                        if($c2['dr']!=0 && $c2['estado']!=6)
                        {
                            foreach ($c2['dr'] as &$c3)
                            {
                                $q++;
                                switch ($c3['estado'])
                                {
                                    case 1:
                                        $qAbiertas++;
                                        break;
                                    case 2:
                                        $qCerradas++;
                                        break;
                                    case 5:
                                        $qVencidas++;
                                        break;
                                    case 6:
                                        $qNoAplica++;
                                        break;
                                    case "":
                                        $qPendientes++;
                                        break;
                                }
                                $c3['n']=$q;
                                $c3['dr']=$this->gr_usuarios->dameArbolInferior($c3['id_usuario'],$id_bc);
                                if($c3['dr']!=0 && $c3['estado']!=6)
                                {
                                    foreach ($c3['dr'] as &$c4)
                                    {
                                        $q++;
                                        switch ($c4['estado'])
                                        {
                                            case 1:
                                                $qAbiertas++;
                                                break;
                                            case 2:
                                                $qCerradas++;
                                                break;
                                            case 5:
                                                $qVencidas++;
                                                break;
                                            case 6:
                                                $qNoAplica++;
                                                break;
                                            case "":
                                                $qPendientes++;
                                                break;
                                        }
                                        $c4['n']=$q;
                                        $c4['dr']=$this->gr_usuarios->dameArbolInferior($c4['id_usuario'],$id_bc);
                                         if($c4['dr']!=0 && $c4['estado']!=6)
                                        {
                                            foreach ($c4['dr'] as &$c5)
                                            {
                                                $q++;
                                                switch ($c5['estado'])
                                                {
                                                    case 1:
                                                        $qAbiertas++;
                                                        break;
                                                    case 2:
                                                        $qCerradas++;
                                                        break;
                                                    case 5:
                                                        $qVencidas++;
                                                        break;
                                                    case 6:
                                                        $qNoAplica++;
                                                        break;
                                                    case "":
                                                        $qPendientes++;
                                                        break;
                                                }
                                                $c5['n']=$q;
                                                $c5['dr']=$this->gr_usuarios->dameArbolInferior($c5['id_usuario'],$id_bc);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                    
            }
            unset($c1);
            unset($c2);
            unset($c3);
            unset($c4);
            unset($c5);
            
            $arbol['alcance']=$q;
            $arbol['status']=$qAbiertas+$qCerradas+$qVencidas+$qNoAplica;
            $arbol['pendientes']=$qPendientes;
            $arbol['abiertas']=$qAbiertas;
            $arbol['cerradas']=$qCerradas;
            $arbol['vencidas']=$qVencidas;
            $arbol['no_aplica']=$qNoAplica;
            $arbol['nodos']=$ciclo1;
//            $this->recorro($ciclo1);
            return $arbol;
        }
        //----->en desarrollo<-----
        function recorro($matriz)
        {
            $q=0;
            echo "<pre>Matriz".print_r($matriz,true)."</pre>";
            foreach($matriz as $key=>$value)
            {
                if (is_array($value))
                {  
                    //si es un array sigo recorriendo
                    echo "<pre>Array".print_r($value,true)."</pre>";
//                    if ($value['estado']==6)
//                        exit();
                    $this->recorro($value);
                }
                else
                {  
                    //si es un elemento lo muestro
                    echo "<pre>Nodo".print_r($value,true)."</pre>";
                        echo '<br>';
                }
            }

        } 
        function _dameUsuariosInferiores($id_usuario_inicio)
        {
            $listado=array();
            $this->load->model('gestion_riesgo/gr_usuarios_model','gr_usuarios',true);
            
            $ciclo1=$this->gr_usuarios->dameListadoInferior($id_usuario_inicio);
            $listado=$ciclo1;
            $q=0;
            foreach ($ciclo1 as &$c1)
            {
                $q++;
                $c1['n']=$q;
                $c1['dr']=$this->gr_usuarios->dameListadoInferior($c1['id_usuario']);
                
                if($c1['dr']!=0)
                {
                    foreach ($c1['dr'] as &$c2)
                    {
                        $q++;
                        $c2['n']=$q;
                        $c2['dr']=$this->gr_usuarios->dameListadoInferior($c2['id_usuario']);
                        
                        if($c2['dr']!=0)
                        {
                            foreach ($c2['dr'] as &$c3)
                            {
                                $q++;
                                $c3['n']=$q;
                                $c3['dr']=$this->gr_usuarios->dameListadoInferior($c3['id_usuario']);
                                if($c3['dr']!=0)
                                {
                                    foreach ($c3['dr'] as &$c4)
                                    {
                                        $q++;
                                        $c4['n']=$q;
                                        $c4['dr']=$this->gr_usuarios->dameListadoInferior($c4['id_usuario']);
                                         if($c4['dr']!=0)
                                        {
                                            foreach ($c4['dr'] as &$c5)
                                            {
                                                $q++;
                                                $c5['n']=$q;
                                                $c5['dr']=$this->gr_usuarios->dameListadoInferior($c5['id_usuario']);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                    
            }
            unset($c1);
            unset($c2);
            unset($c3);
            unset($c4);
            unset($c5);
            
            $arbol['profundidad']=$q;
            $arbol['nodos']=$ciclo1;
            return $arbol;
        }
        //define la genrencia de cada area a partir de su area padre y el campo gcia
        function definirGerencias()
        {
            $this->load->model("gestion_riesgo/gr_areas_model","areas",true);
            $areas=$this->areas->dameTodasAreas();
//            echo "<pre>Array".print_r($areas,true)."</pre>";die();
            $n=1;
            foreach ($areas as $area)
            {
                echo $n++."<br>";
//                echo "<pre>Area".print_r($area,true)."</pre>";
                $gcia=$this->_buscarGerencia($area['id_area']);
//                echo "<pre>Gcia".print_r($gcia,true)."</pre>";
                $dato['id_gcia']=$gcia['id_area'];
                $this->areas->update($area['id_area'],$dato);
            }
            
        }
        function _buscarGerencia($id_area)
        {
            $this->load->model("gestion_riesgo/gr_areas_model","areas",true);
            $area=array();
            $area=$this->areas->dameGerencia($id_area);
//                echo"<pre>Buscar".print_r($area,true)."</pre>";
            //verifico que el area exista
            if ($area!=0)
            {
                if($area['gcia']==0 && $area['id_area_padre']!=NULL)                    
                    return $this->_buscarGerencia($area['id_area_padre']);
                else
                    
                    return $area;
////                
            }
            else 
                return 0;
        }
        function mailRmcCerradas()
        {
            $this->load->model("gestion_riesgo/gr_rmc_model","rmc_model",true);
            $this->load->model("gestion_riesgo/gr_tareas_model","gr_tareas",true);
            $rrmmcc=$this->rmc_model->dameCerradasAyer();
//            echo"<pre>".print_r($rrmmcc,true)."</pre>";
            if($rrmmcc!=0)
            {
                    $this->load->model('crons_model','crons',true);
                foreach ($rrmmcc as $key=>$value)
                {
//                    echo"<pre>".print_r($value,true)."</pre>";
                    $tareas=$this->gr_tareas->dameTareasRmc($value['id_rmc']);
//                    echo"<pre>".print_r($tareas,true)."</pre>";
                    $datos['rmc']=$value;
                    $datos['tareas']=$tareas;
                    $mail=$this->_enviarMailRmcCerrado($datos);
                    //grabo en envío en la tabla crons
                    $datos2['cron']='bots/mailRmcCerradas';
                    $datos2['detalle']='RI-Cerrada: '.$value['id_rmc'];
                    $datos2['echo']=$mail;
                    $this->crons->insert($datos2);
                    sleep(10);
                    //
                }
            }
        }
        function mailManualRmcCerrada($id_rmc)
        {
            $this->load->model("gestion_riesgo/gr_rmc_model","rmc_model",true);
            $this->load->model("gestion_riesgo/gr_tareas_model","gr_tareas",true);
            
            //verifico que el RI este cerrada
            $rmc=$this->rmc_model->dameRmcCerrada($id_rmc);
//            echo"<pre>".print_r($rmc,true)."</pre>";die();
            if($rmc!=0)
            {
                    $this->load->model('crons_model','crons',true);
//                    echo"<pre>".print_r($value,true)."</pre>";
                    $tareas=$this->gr_tareas->dameTareasRmc($rmc['id_rmc']);
//                    echo"<pre>".print_r($tareas,true)."</pre>";
                    $datos['rmc']=$rmc;
                    $datos['tareas']=$tareas;
                    $mail=$this->_enviarMailRmcCerrado($datos);
                    //grabo en envío en la tabla crons
                    $datos2['cron']='bots/mailRmcCerradas';
                    $datos2['detalle']='RI-Cerrada: '.$rmc['id_rmc'];
                    $datos2['echo']=$mail;
                    $this->crons->insert($datos2);
                    sleep(4);
                    //
            }
        }
        //http://www.polidata.com.ar/orocobre/bots/mailManualRmcCerrada/851
        function _enviarMailRmcCerrado($datos)
        {
            extract($datos);
//            echo"<pre>".print_r($rmc,true)."</pre>";

$cuerpoMail=<<<HTML
<html> 
<head>
<title>SMC</title>
<style type="text/css">
td{
    vertical-align: top;
}
</style>
</head>
<body>
<div style="color:#000000; font-family:Verdana, Arial, Helvetica, sans-serif; text-align:Left; font-size:13px;">
<pre>
Comunicamos el cierre de la investigación del siguiente Reporte de Incidente (RI):

<table  border="0">
<tr>
<td style="width:150px;background-color:black;color:white"><b>RI Nro</b></td>
<td style="width: 5px;">:</td>
<td>#rmc_nro</td>
</tr>
<tr>
<td><b>Usuario alta</b></td>
<td style="width: 5px;">:</td>
<td>#rmc_usuario_alta</td>
</tr>
<tr>
<td><b>Investigadores</b></td>
<td style="width: 5px;">:</td>
<td>#rmc_investigadores</td>
</tr>
<tr>
<td><b>Sector involucrado</b></td>
<td style="width: 5px;">:</td>
<td>#rmc_sector</td>
</tr>
<tr>
<tr>
<td><b>Grado establecido</b></td>
<td style="width: 5px;">:</td>
<td>#rmc_grado</td>
</tr>
<td><b>Descripción</b></td>
<td style="width: 5px;">:</td>
<td>#rmc_desc</td>
</tr>
</table>

La tareas creadas hasta la fecha son:
            
#tablaTareas       

Atentamente
#firma

</pre>
</div>
</body>
</html>        
HTML;

$tablaTareas=<<<TAREAS
<table  border="0">
#tr
</table>          
TAREAS;
$tarea=<<<TAREA
<tr>
<td style="width: 10px;background-color:black;color:white"><b>#n)</b></td>
<td style="width: 100px;"><b><b> Tarea Nro</b> </b></td>
<td style="width: 5px;"><b>:</b></td>
<td >#tarea_nro       <b>Vencimiento:</b> #tarea_vto       <b>Responsable:</b> #tarea_resp</b></td>
</tr>
<tr>
<td style="width: 10px;"></td>
<td><b> Hallazgo</b></td>
<td style="width: 5px;">:</td>
<td>#tarea_hallazgo</td>
</tr>
<tr>
<td style="width: 10px;"></td>
<td><b> Tarea</b></td>
<td style="width: 5px;">:</td>
<td>#tarea_tarea</td>
</tr>
<tr>
<td colspan="4" style="border-bottom: 1px dashed black;"> </td>
</tr>
#tr
TAREA;

            $n=1;
            foreach ($tareas as $key=>$value)
            {
                $tr=$tarea;
                $tr=str_replace("#n", $n, $tr);
                $tr=str_replace("#tarea_nro", $value['id_tarea'], $tr);
                $tr=str_replace("#tarea_vto", $value['fecha_vto'], $tr);
                $tr=str_replace("#tarea_resp", $value['usuario_responsable'], $tr);
                $tr=str_replace("#tarea_hallazgo", $value['hallazgo'], $tr);
                $tr=str_replace("#tarea_tarea", $value['tarea'], $tr);
                $tablaTareas=str_replace('#tr',$tr,$tablaTareas);
                $n++;
            }
            $tablaTareas=str_replace('#tr','',$tablaTareas);

            $subject='RI Nro: '.$datos['rmc']['id_rmc'].' - Cerrada';
            
            $cuerpoMail=str_replace("#rmc_nro", 2000000+$rmc['id_rmc'], $cuerpoMail);
            
            $cuerpoMail=str_replace("#rmc_usuario_alta", $rmc['usuario_alta'], $cuerpoMail);
//            $cuerpoMail=str_replace("#rmc_fecha_alta", $rmc['fecha_alta'], $cuerpoMail);
            
            
            $conector=(strtoupper(substr($rmc['inv2'],0,1))=='I')?' e ':' y ';
            $cuerpoMail=str_replace("#rmc_investigadores"   , $rmc['inv1'].$conector.$rmc['inv2']  , $cuerpoMail);
            
            $cuerpoMail=str_replace("#rmc_sector"   , $rmc['sector'],$cuerpoMail);
            $cuerpoMail=str_replace("#rmc_sector"   , $rmc['sector'],$cuerpoMail);
            $cuerpoMail=str_replace("#rmc_grado"   , $rmc['grado'],$cuerpoMail);
            $cuerpoMail=str_replace("#rmc_desc"   , $rmc['descr'],$cuerpoMail);
            $cuerpoMail=str_replace("#tablaTareas "   , $tablaTareas,$cuerpoMail);
            
            $cuerpoMail=str_replace("#firma", htmlentities(NOMSIS,ENT_QUOTES,"UTF-8"),$cuerpoMail);
            
             $this->load->library('email');
//             $this->email->clear();//para cuando uso un bucle
            $this->email->from(SYS_MAIL, NOMSIS);
            $this->email->message($cuerpoMail);
            $this->email->subject($subject);
//            $this->email->set_alt_message();//Sets the alternative email message body
       	    $this->email->to('personalsalesdejujuy@salesdejujuy.com');
             $cc='gestionderiesgos@salesdejujuy.com';
            
            
            if (MAILS_ALWAYS_CC!="")
                $this->email->cc($cc.",".MAILS_ALWAYS_CC);
            else
                $this->email->cc($cc);
            IF (MAILBCC && MAILBCC !="")
                $this->email->bcc(MAILBCC);
            
//            echo $cuerpoMail."<br><hr>";
            if (ENPRODUCCION)
            {
                if($this->email->send())
                    return 1;
                else
                    return 0;
            }
            else
            {
                    return -1;
            }

        }
        function probar_cronjob()
        {
            $this->load->library('email');
            $this->email->from(SYS_MAIL, NOMSIS);
            $this->email->message("Prueba Ok  --". date("F j, Y, g:i a"));
            $this->email->subject('Probando CronJobs');
            $this->email->to('fmoraiz@gmail.com');
            $this->email->send();
            echo "--ok--";

        }
        
        function atualizarRepoMeta()
        {
            //1er mes Marzo2014=1
            $meses=array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45);
            
            $this->load->model("gestion_riesgo/gr_reportes","gr_reportes",true);
            
//                ob_start();
            foreach ($meses as $mes)
            {
                $m=$mes;
                switch ($m)
                {
                    case 1:$this->gr_reportes->actualizar(2014,3);break;
                    case 2:$this->gr_reportes->actualizar(2014,4);break;
                    case 3:$this->gr_reportes->actualizar(2014,5);break;
                    case 4:$this->gr_reportes->actualizar(2014,6);break;
                    case 5:$this->gr_reportes->actualizar(2014,7);break;
                    case 6:$this->gr_reportes->actualizar(2014,8);break;
                    case 7:$this->gr_reportes->actualizar(2014,9);break;
                    case 8:$this->gr_reportes->actualizar(2014,10);break;
		    case 9:$this->gr_reportes->actualizar(2014,11);break;
		    case 10:$this->gr_reportes->actualizar(2014,12);break;
		    case 11:$this->gr_reportes->actualizar(2015,1);break;
		    case 12:$this->gr_reportes->actualizar(2015,2);break;
		    case 13:$this->gr_reportes->actualizar(2015,3);break;
		    case 14:$this->gr_reportes->actualizar(2015,4);break;
		    case 15:$this->gr_reportes->actualizar(2015,5);break;
		    case 16:$this->gr_reportes->actualizar(2015,6);break;
		    case 17:$this->gr_reportes->actualizar(2015,7);break;
		    case 18:$this->gr_reportes->actualizar(2015,8);break;
		    case 19:$this->gr_reportes->actualizar(2015,9);break;
		    case 20:$this->gr_reportes->actualizar(2015,10);break;
		    case 21:$this->gr_reportes->actualizar(2015,11);break;
		    case 22:$this->gr_reportes->actualizar(2015,12);break;
		    case 23:$this->gr_reportes->actualizar(2016,1);break;
		    case 24:$this->gr_reportes->actualizar(2016,2);break;
		    case 25:$this->gr_reportes->actualizar(2016,3);break;
		    case 26:$this->gr_reportes->actualizar(2016,4);break;
		    case 27:$this->gr_reportes->actualizar(2016,5);break;
		    case 28:$this->gr_reportes->actualizar(2016,6);break;
		    case 29:$this->gr_reportes->actualizar(2016,7);break;
		    case 30:$this->gr_reportes->actualizar(2016,8);break;
		    case 31:$this->gr_reportes->actualizar(2016,9);break;
		    case 32:$this->gr_reportes->actualizar(2016,10);break;
		    case 33:$this->gr_reportes->actualizar(2016,11);break;
		    case 34:$this->gr_reportes->actualizar(2016,12);break;
		    case 35:$this->gr_reportes->actualizar(2017,1);break;
		    case 36:$this->gr_reportes->actualizar(2017,2);break;
		    case 37:$this->gr_reportes->actualizar(2017,3);break;
		    case 38:$this->gr_reportes->actualizar(2017,4);break;
		    case 39:$this->gr_reportes->actualizar(2017,5);break;
		    case 40:$this->gr_reportes->actualizar(2017,6);break;
		    case 41:$this->gr_reportes->actualizar(2017,7);break;
		    case 42:$this->gr_reportes->actualizar(2017,8);break;
		    case 43:$this->gr_reportes->actualizar(2017,9);break;
		    case 44:$this->gr_reportes->actualizar(2017,10);break;
		    case 45:$this->gr_reportes->actualizar(2017,11);break;
		    case 46:$this->gr_reportes->actualizar(2017,12);break;
                }
//                echo "Actualizado:".$m." T=".date('G:i:s')."<br>";
//                flush();
            }
//            ob_end_flush(); 
        }
        
        public function vto_proximo()
        {
            $this->load->model('crons_model','crons',true);
            $cron=$this->crons->verificaCronAlertProxVto();
            $datosCron['cron']='bots/alert_proxvto';
            if ($cron==0)
            {
                $this->load->model('gestion_riesgo/gr_tareas_model','gr_tarea',true);
                $tareas=$this->gr_tarea->dameProximasAVencer();
    //            echo "<pre>".print_r($tareas,true)."</pre>"; 
                $this->load->model('crons_model','crons',true);
                 if (!empty($tareas) && $tareas!=0)
                {
                     $arrayTareas="";
                     foreach ($tareas as $value) 
                    {
                         $arrayTareas[]=$value['id_tarea'];
                    }
                    foreach ($arrayTareas as $idTarea) 
                    {
                        $send_mail=$this->_preparar_enviar_mail($idTarea,'proximo_vencimiento');
                        //Registro la ejecución del cron
                        $datosCron['detalle']='Tarea Nro '.$idTarea;
                        $datosCron['echo']=$send_mail;
                        $this->crons->insert($datosCron);
                    }
                }
                else
                {

                    $datosCron['detalle']='No hay Tareas próximas a vener';
                    $datosCron['echo']='null';
                    $this->crons->insert($datosCron);
                }

            }
            else
            {
                $datosCron['detalle']='Cron ya ejecutado...';
                $datosCron['echo']='0';
                $this->crons->insert($datosCron);
                die();
            }
        }

    function documentos_publicados()
    {
        $this->load->model('dms/documentos_model','documentos',true);
        $this->load->model('usuarios_model','usuarios',true);
        
        $publicados=$this->documentos->listadoDocumentosFechaAlerta();
        $this->load->model('crons_model','crons',true);
        $cron=$this->crons->verificaCronDocPublicados();
        $datosCron['cron']='bots/documentos_publicados';
//        echo "<pre>".print_r($publicados,true)."</pre>";
        if($cron == 0)
        {
            if($publicados != 0)
            {
                foreach($publicados as $atrib=>$documento)
                {
                    $datos=$this->documentos->dameDatosDocumentoParaMail($documento['id_documento']);

                    $data = array_pop($datos);
    //                echo "<pre>".print_r($data,true)."</pre>";
                    $checkEditor=$this->usuarios->checkUsuarioHabilitado($data['id_usuario_editor']);

                    if(!$checkEditor)
                    {
                        $habilitado = $this->_dameSupervisorHabilitado($data['id_usuario_editor']);
    //                    echo "<pre>".print_r($habilitado,true)."</pre>";
                        $habilitado = array_pop($habilitado);
    //                    echo "<pre>".print_r($habilitado,true)."</pre>";die();
                        if($habilitado == 0)
                        {
                            $data['id_usuario_editor'] = null;
                            $data['email_editor'] = MAIL_DEFECTO_DMS;

                            if($data['email_revisores'] != null)
                            {
                                $mailEditor[0] = $data['email_editor'];
                                $mailAprobador[0] = $data['email_aprobador'];
                                $mails = explode(', ',$data['email_revisores']);
                                $mails = array_diff($mails,$mailAprobador);
                                $mails = array_diff($mails,$mailEditor);
                                $data['email_revisores'] = implode(', ', $mails);
                            }

                            $send_mail=$this->_enviarMailDocumentosPublicados($data);
                            $datosCron['detalle']='Documento Nro '.$documento['id_documento'];
                            $datosCron['echo']=$send_mail;
                            $this->crons->insert($datosCron);
//                            echo $send_mail;
                            sleep(10);
                        }
                        else
                        {
                            $data['editorPro'] = $habilitado['nomape'];
                            $data['email_editor'] = $habilitado['email'];
                            $data['generoEditor'] = $habilitado['genero'];
    //                            echo "<pre>1".print_r($habilitado,true)."</pre>";
                            if($data['email_revisores'] != null)
                            {
                                $mailEditor[0] = $data['email_editor'];
                                $mailAprobador[0] = $data['email_aprobador'];
                                $mails = explode(', ',$data['email_revisores']);
                                $mails = array_diff($mails,$mailAprobador);
                                $mails = array_diff($mails,$mailEditor);
                                $data['email_revisores'] = implode(', ', $mails);
                            }
                            $send_mail=$this->_enviarMailDocumentosPublicados($data);
                            $datosCron['detalle']='Documento Nro '.$documento['id_documento'];
                            $datosCron['echo']=$send_mail;
                            $this->crons->insert($datosCron);
                            sleep(10);
                        }
                    }
                    else
                    {
                        if($data['email_revisores'] != null)
                        {
                            $mailEditor[0] = $data['email_editor'];
                            $mailAprobador[0] = $data['email_aprobador'];
                            $mails = explode(', ',$data['email_revisores']);
                            $mails = array_diff($mails,$mailAprobador);
                            $mails = array_diff($mails,$mailEditor);
                            $data['email_revisores'] = implode(', ', $mails);
                        }
                        $send_mail=$this->_enviarMailDocumentosPublicados($data);
                        $datosCron['detalle']='Documento Nro '.$documento['id_documento'];
                        $datosCron['echo']=$send_mail;
                        $this->crons->insert($datosCron);
                        sleep(10);
                    }
                }
            }
            else
            {
                $datosCron['detalle']='Sin documentos vencidos';
                $datosCron['echo']='0';
                $this->crons->insert($datosCron);
                die();
            }
        }
        else
        {
            $datosCron['detalle']='Cron ya ejecutado...';
            $datosCron['echo']='0';
            $this->crons->insert($datosCron);
            die();
        }
        
    }
    
    function _enviarMailDocumentosPublicados($data)
    {
//        echo "<pre>1".print_r($data,true)."</pre>";
        $link='www.polidata.com.ar/sdj/admin';
                        
$mail_dcto=<<<HTML
<html> 
    <head>
        <title>SMC</title>
        
    </head>
    <body>
    <div style="color:#000000; font-family:Verdana, Arial, Helvetica, sans-serif; text-align:Left; font-size:13px;">
        <pre>
            #persona,

            El documento con c&oacute;digo #codigo cumpli&oacute; un año de su publicaci&oacute;n. Por favor controle que el contenido del mismo este vigente.
            
            <b>Editor:</b>
            <em>#editor</em>
            
            <b>Revisor/es:</b>
            <em>#revisor</em>
                        
            <b>Aprobador:</b>
            #aprobador
            
            Por favor, controle dicho documento ingresando al sistema desde <a href="#link" target="_blank">aqu&iacute;</a>, o copiando y pegando la siguiente direcci&oacute;n en su navegador web <b>Mozilla Firefox</b> o <b>Google Chrome</b>: 
            #link.
            

            Atentamente
            #firma
            
            Importante: Mensaje generado autom&aacute;ticamente, por favor no responder.
            <hr>
                Para: #usuarioPara <br>
                CC  : #usuariosCC1 <br>
                CC  : #usuariosCC2
           <hr>

        </pre>
        </div>
    </body>
</html>
HTML;
        
        $cuerpo_mail=$mail_dcto;
        
        $subject='El documento '.$data['codigo'].' ha cumplido 1 año desde su publicación.';
        
        if($data['id_usuario_editor'] != null)
        {
            if($data['editorPro'])
            {
                if ($data['generoEditor']=='F')
                    $cuerpo_mail=str_replace("#persona"         , 'Estimada '.$data['editorPro']      , $cuerpo_mail);
                else
                    $cuerpo_mail=str_replace("#persona"         , 'Estimado '.$data['editorPro']      , $cuerpo_mail);
            }
            else
            {
                if ($data['generoEditor']=='F')
                    $cuerpo_mail=str_replace("#persona"         , 'Estimada '.$data['editor']      , $cuerpo_mail);
                else
                    $cuerpo_mail=str_replace("#persona"         , 'Estimado '.$data['editor']      , $cuerpo_mail);
            }
                
        }
        else
            $cuerpo_mail=str_replace("#persona"         , 'Estimado/s personal de GDR '      , $cuerpo_mail);
            
        $cuerpo_mail=str_replace("#usuarioPara"         , $data['email_editor']  , $cuerpo_mail);
        if ($data['email_revisores'] != null)
            $cuerpo_mail=str_replace("#usuariosCC1"     , $data['email_revisores']   , $cuerpo_mail);
        else 
            $cuerpo_mail=str_replace("#usuariosCC1"     , '-.-'   , $cuerpo_mail);
            
        if($data['email_aprobador'] != $data['email_editor'])
            $cuerpo_mail=str_replace("#usuariosCC2"         , $data['email_aprobador']   , $cuerpo_mail);
        else
            $cuerpo_mail=str_replace("#usuariosCC2"     , '-.-'   , $cuerpo_mail);
        
        $cuerpo_mail=str_replace("#codigo"              , $data['codigo']     ,$cuerpo_mail);
        
        $cuerpo_mail=str_replace("#editor"              , $data['editor']        ,$cuerpo_mail);
            
        $cuerpo_mail=str_replace("#aprobador"           , $data['aprobador'] ,$cuerpo_mail);
        
        if ($data['revisores'] != null)
            $cuerpo_mail=str_replace("#revisor"       , $data['revisores'] ,$cuerpo_mail);
        else
            $cuerpo_mail=str_replace("#revisor"       , '-.-' ,$cuerpo_mail);
            
        $cuerpo_mail=str_replace("#link"                , $link                  ,$cuerpo_mail);
        $cuerpo_mail=str_replace("#firma"               , htmlentities(NOMSIS,ENT_QUOTES,"UTF-8"),$cuerpo_mail);

        
         $this->load->library('email');
//             $this->email->clear();//para cuando uso un bucle
        $this->email->from(SYS_MAIL, NOMSIS);
        $this->email->message($cuerpo_mail);
        $this->email->subject($subject);
//            $this->email->set_alt_message();//Sets the alternative email message body
        if ($data['email_revisores'] != null)
        {
            $usuariosCC =  $data['email_revisores'];
            $usuariosCC .=", ".$data['email_aprobador'];
        }
        else
        {
            if($data['email_aprobador'] != $data['email_editor'])
                $usuariosCC = $data['email_aprobador'];
        }
        
//        echo $cuerpo_mail;
        $this->email->to($data['email_editor']);
        $this->email->cc($usuariosCC);
        IF (MAILBCC && MAILBCC !="")
            $this->email->bcc(MAILBCC);
//        echo 7;
        if (ENPRODUCCION)
        {
            if($this->email->send())
                return 1;
            else
                return 0;
        }
        else
        {
                return 0;
        }
    }
    
    function _dameSupervisorHabilitado($id_usuario)
    {
        $this->load->model('usuarios_model','usuarios',true);
            
        $supervisor=$this->usuarios->dameSupervisor($id_usuario);
//        echo "<pre>".print_r($supervisor,true)."</pre>";
        if($supervisor != 0)
        {
            $checkEditor=$this->usuarios->checkUsuarioHabilitado($supervisor[0]['id_usuario']);
            if($checkEditor)
                return $supervisor;
            else
                $this->_dameSupervisorHabilitado($supervisor[0]['id_usuario']);
        }
        else
            return 0;
//        echo "<pre>".print_r($supervisor,true)."</pre>";
    }
    function procesaVencimientos()
    {
$recuerda_txt=<<<HTML
<html> 
<head>
    <title>SMC</title>

</head>
<body>
<div style="color:#000000; font-family:Verdana, Arial, Helvetica, sans-serif; text-align:Left; font-size:13px;">
    <pre>
        Aviso de vencimiento próximo 
        
        Fecha:<b>#fecha</b>
        Titulo:<b>#titulo</b>
        Descripción:<b>#descripcion</b>
        Responsable: <b>#u_responsable</b>
        Generado por:<b>#u_alta</b>


        Puede continuar la gestión de este evento ingresando al sistema desde <a href="#link" target="_blank">aqu&iacute;</a>, o copiando y pegando la siguiente direcci&oacute;n en su navegador web <b>Mozilla Firefox</b> o <b>Google Chrome</b>: 
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
    }
}


?>
