<?php
class Mis_ed extends CI_Controller
{
    private $modulo=38;
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
    }

    public function index()
    {
        $variables['permiso'] = $this->permisos;
        $this->load->view('ed/mis_ed/listado',$variables);
    }

    public function listado()
    {
        if ($this->permisos['Listar'])
        {
            $this->load->model('ed/ed_evaluaciones_model','ed_evaluaciones',true);
            $id_usuario = $this->user['id'];
            $start = $this->input->post("start");
            $limit = $this->input->post("limit");
            $filtro = "";
            $sort = $this->input->post("sort");
            $campos = "";
            if ($this->input->post("fields"))
            {
                $campos = str_replace('"', "", $this->input->post("fields"));
                $campos = str_replace('[', "", $campos);
                $campos = str_replace(']', "", $campos);
                $campos = explode(",",$campos);
            }
            $busqueda  = ($this->input->post("query"))?$this->input->post("query"):"";
            $listado = $this->ed_evaluaciones->listado($id_usuario, $start, $limit, $filtro, $sort,$busqueda,$campos);
            echo $listado;
        }
        else
            echo "({success: false, error :'No tiene permisos para realizar la acciòn solicitada'})"; //
    }
    public function cerrar()
    {
        if ($this->permisos['Listar'])
        {
            $id_usuario = $this->user['id'];
            $id_ed = $this->input->post("id");
//		$id_ed = 2;
            $this->load->model('ed/ed_evaluaciones_model','ed_evaluaciones',true);
            $ed=$this->ed_evaluaciones->dameCabeceraEd($id_ed);
//                echo "<pre>". print_r($ed,true)."<pre>";
            if($ed!=false)
            {
                $tipo = $this->input->post("tipo");// tipo=1->Autoevaluación =2->Evaluación de persona a cargo
                if($tipo==1 && $ed['id_usuario']==$id_usuario) //cierre autoevaluación
                {
                    //verifico que ya se encuentre cerrarda por el supervisor
                    if($ed['cierre_s']==1)
                    {
                        //verifico que no este cerrada por el usuario
                        if($ed['cierre_u']==0)
                        {
                            //controlo que esten completas las competencias cualitativas oblicatorias
                            $control1=$this->ed_evaluaciones->controlCierre_Competencias_cual_usuario($id_ed);
                            if(!$control1)
                                exit ("({success: false, error :'Error, faltan completar competencias cualitativas...'})");
                            //cierro la ed como usuario
                            $txt_comentario=$this->input->post("txt");
                            $cerrar=$this->ed_evaluaciones->cerrarAutoevaluacion($id_ed,$txt_comentario);
                            $genera_tareas=$this->_generar_tareas($id_ed);
                            echo $genera_tareas;
                            if($genera_tareas==1)
                            {
                                $this->_enviarMailCierreUsuario($id_ed);
                            }
                            echo "({success: true, error:'', msg:'Autoevaluación cerrada correctamente...'})";
                        }
                        else
                            exit ("({success: false, error :'Error, evaluacion ya cerrada por el usuario...'})");
                            
                    }
                    else
                        echo "({success: false, error :'La evaluación aún no se encuentra cerrada por su supervisor'})";


                }
                elseif($tipo==2 && $ed['id_usuario_supervisor']==$id_usuario)//cierre evauación supervisada
                {
                    //controlo que esten completas las competencias cualitativas oblicatorias
                    $control1=$this->ed_evaluaciones->controlCierre_Competencias_cual($id_ed);
                    if(!$control1)
                        exit ("({success: false, error :'Error, faltan completar competencias cualitativas...'})");
                    //controlo que esten completas las competencias cuantitativas oblicatorias
                    $control2=$this->ed_evaluaciones->controlCierre_Competencias_cuant($id_ed);
                    if(!$control2)
                        exit ("({success: false, error :'Error, faltan completar competencias cuantitativas...'})");
                    //controlo que exista por lo menos 1 fortaleza y 1 aspecto a mejorar
                    $control3=$this->ed_evaluaciones->controlCierre_fortalezas($id_ed);
                    if(!$control3)
                        exit ("({success: false, error :'Error, complete al menos una fortaleza'})");
                    $control4=$this->ed_evaluaciones->controlCierre_am($id_ed);
                    if(!$control4)
                        exit ("({success: false, error :'Error, complete al menos un aspecto a mejorar'})");
                    //Controlo que esten completos el/los planes de mejoras sobre las competencias que correspondan
                    $control5=$this->ed_evaluaciones->controlCierre_pm($id_ed);
                    if(!$control5)
                        exit ("({success: false, error :'Error, complete todos los campos en la seccion de planes de mejora'})");
                    //controlo que exista por lo menos 1 fijacion de metas
                    $control6=true;
//                    $control6=$this->ed_evaluaciones->controlCierre_fm($id_ed);
//                    if(!$control6)
//                        exit ("({success: false, error :'Error, defina al menos una meta en el campo de fijación de metas'})");




                    if($control1&&$control2&&$control3&&$control4&&$control5&&$control6)
                    {
                        //cierro la ed como supervisor
                         $cerrar=$this->ed_evaluaciones->cerrarEvaluacionSupervisada($id_ed);
                         $this->_enviarMailCierreSupervisor($id_ed);
                        echo "({success: true, error:'', msg:'Evaluación cerrada correctamente...'})";

                    }
                }
                else //error de tipo o permisos
                {
                    echo "({success: false, error :'Error verificando permisos...'})";
                }
            }
            else
                echo "({success: false, error :'Error identidicando el registro seleccionado...'})"; //
        }
        else
            echo "({success: false, error :'No tiene permisos para realizar la acciòn solicitada'})"; //
    }
    function _enviarMailCierreUsuario($id_ed)
    {
         $this->load->model('ed/ed_evaluaciones_model','ed_evaluaciones',true);
         $ed=$this->ed_evaluaciones->dameCabeceraEdMail($id_ed);
//         echo "<pre>". print_r($ed,true)."<pre>";
         $this->load->model("gestion_riesgo/gr_tareas_model","gr_tareas",true);
         $tareas=$this->gr_tareas->dameTareasED($id_ed);
//         echo "<pre>". print_r($tareas,true)."<pre>";
//         die();
        $link='www.polidata.com.ar/sdj/admin';

$txt_mail_cerrar=<<<HTML
<html> 
<head>
    <title>SMC</title>

</head>
<body>
<div style="color:#000000; font-family:Verdana, Arial, Helvetica, sans-serif; text-align:Left; font-size:13px;">
    <pre>
        #para,

        Informamos la finalización de la Evaluación de Desempeño correspondiente al per&iacute;odo <b>#periodo</b>.
        
        <table  border="0">
        <tr>
        <td style="width:150px;background-color:black;color:white"><b>ED Nro</b></td>
        <td style="width: 5px;">:</td>
        <td>#ed_nro</td>
        </tr>
        <tr>
        <td><b>Usuario</b></td>
        <td style="width: 5px;">:</td>
        <td>#ed_usuario</td>
        </tr>
        <tr>
        <td><b>Supervisor</b></td>
        <td style="width: 5px;">:</td>
        <td>#ed_supervisor</td>
        </tr>
        <tr>
        <td><b>Fecha Cierre Supervisor</b></td>
        <td style="width: 5px;">:</td>
        <td>#ed_cierre_s</td>
        </tr>
        <tr>
        <td><b>Fecha Cierre Usuario</b></td>
        <td style="width: 5px;">:</td>
        <td>#ed_cierre_u</td>
        </tr>
        <tr>
        <td><b>Comentario del usuario</b></td>
        <td style="width: 5px;">:</td>
        <td>#ed_comentario_u</td>
        </tr>
        </table>
        
        #tareas  
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

$txt_tablaTareas=<<<TABLATAREAS

        Las tareas generadas automáticamente desde esta evaluación por los planes de mejora y/o la fijación de metas son las siguientes:        

        <table  border="0">
        #tr
        </table>          
TABLATAREAS;


$txt_filaTarea=<<<FILATAREA
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
FILATAREA;
         if ($tareas!=0)
         {
            $n=1;
            foreach ($tareas as $key=>$value)
            {
                $tr=$txt_filaTarea;
                $tr=str_replace("#n", $n, $tr);
                $tr=str_replace("#tarea_nro", $value['id_tarea'], $tr);
                $tr=str_replace("#tarea_vto", $value['fecha_vto'], $tr);
                $tr=str_replace("#tarea_resp", $value['usuario_responsable'], $tr);
                $tr=str_replace("#tarea_hallazgo", $value['hallazgo'], $tr);
                $tr=str_replace("#tarea_tarea", $value['tarea'], $tr);
                $txt_tablaTareas=str_replace('#tr',$tr,$txt_tablaTareas);
                $n++;
            }
            $txt_tablaTareas=str_replace('#tr','',$txt_tablaTareas);
             
         }

        $cuerpo_mail=$txt_mail_cerrar;
//        if ($ed['genero_s']=='F')
            $cuerpo_mail=str_replace("#para"       , 'Estimados '.$ed['supervisor']." y ".$ed['usuario'] , $cuerpo_mail);
//        else
//           $cuerpo_mail=str_replace("#para"       , 'Estimado '.$ed['supervisor']      , $cuerpo_mail);

        $cuerpo_mail=str_replace("#ed_nro",'0000'.$id_ed,$cuerpo_mail);
        $cuerpo_mail=str_replace("#ed_usuario", $ed['usuario'],$cuerpo_mail);
        $cuerpo_mail=str_replace("#ed_supervisor", $ed['supervisor'],$cuerpo_mail);
        $cuerpo_mail=str_replace("#ed_cierre_s", $ed['fecha_cierre_s'],$cuerpo_mail);
        $cuerpo_mail=str_replace("#ed_cierre_u", $ed['fecha_cierre_u'],$cuerpo_mail);
        $cuerpo_mail=str_replace("#ed_comentario_u", $ed['comentario_usuario'],$cuerpo_mail);
        
        $cuerpo_mail=str_replace("#periodo"         , $ed['periodo'],$cuerpo_mail);
//        $cuerpo_mail=str_replace("#usuarioED"     , $ed['usuario'],$cuerpo_mail);
//        $cuerpo_mail=str_replace("#supervisor"      , $ed['supervisor'],$cuerpo_mail);
//        $cuerpo_mail=str_replace("#link"            , $link,$cuerpo_mail);
        $cuerpo_mail=str_replace("#usuarioPara"     , $ed['mail_s'].", ".$ed['mail_u'],$cuerpo_mail);
        $cuerpo_mail=str_replace("#usuariosCC"      , '',$cuerpo_mail);
        $cuerpo_mail=str_replace("#firma"           , htmlentities(NOMSIS,ENT_QUOTES,"UTF-8"),$cuerpo_mail);
        if ($tareas!=0)
            $cuerpo_mail=str_replace("#tareas"   ,$txt_tablaTareas,$cuerpo_mail);
        else
            $cuerpo_mail=str_replace("#tareas"   ,'',$cuerpo_mail);
//        echo $cuerpo_mail;
//        die();
        $subject='ED: '.$ed['periodo'].' - Confirmada';


        $this->load->library('email');
        $this->email->from(SYS_MAIL, NOMSIS);
        $this->email->message($cuerpo_mail);
        $this->email->to(array($ed['mail_s'],$ed['mail_u']));
//        $this->email->cc($ed['mail_s']);
        $this->email->subject($subject);
        IF (MAILBCC && MAILBCC !="")
            $this->email->bcc(MAILBCC);
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
        
    }
    function _enviarMailCierreSupervisor($id_ed)
    {
         $this->load->model('ed/ed_evaluaciones_model','ed_evaluaciones',true);
         $ed=$this->ed_evaluaciones->dameCabeceraEdMail($id_ed);
//         echo "<pre>". print_r($ed,true)."<pre>";
        $link='www.polidata.com.ar/sdj/admin';

$txt_mail_cerrar=<<<HTML
<html> 
<head>
    <title>SMC</title>

</head>
<body>
<div style="color:#000000; font-family:Verdana, Arial, Helvetica, sans-serif; text-align:Left; font-size:13px;">
    <pre>
        #para,

        Informamos que su evaluaci&oacute;n de Desempeño correspondiente al per&iacute;odo <b>#periodo</b> fue evaluada y finalizada por su supervisor <b>#supervisor</b>.

        Puede continuar su gesti&oacute;n ingresando al sistema desde <a href="#link" target="_blank">aqu&iacute;</a>, o copiando y pegando la siguiente direcci&oacute;n en su navegador web <b>Mozilla Firefox</b> o <b>Google Chrome</b>: 
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
        $cuerpo_mail=$txt_mail_cerrar;
        if ($ed['genero_u']=='F')
            $cuerpo_mail=str_replace("#para"       , 'Estimada '.$ed['usuario'], $cuerpo_mail);
        else
           $cuerpo_mail=str_replace("#para"       , 'Estimado '.$ed['usuario']      , $cuerpo_mail);

        $cuerpo_mail=str_replace("#periodo"         , $ed['periodo'],$cuerpo_mail);
        $cuerpo_mail=str_replace("#supervisor"      , $ed['supervisor'],$cuerpo_mail);
        $cuerpo_mail=str_replace("#link"            , $link,$cuerpo_mail);
        $cuerpo_mail=str_replace("#usuarioPara"     , $ed['mail_u'],$cuerpo_mail);
        $cuerpo_mail=str_replace("#usuariosCC"      , $ed['mail_s'],$cuerpo_mail);
        $cuerpo_mail=str_replace("#firma"           , htmlentities(NOMSIS,ENT_QUOTES,"UTF-8"),$cuerpo_mail);
//        echo $cuerpo_mail;

        $subject='ED: '.$ed['periodo'].' - Cerrada';


        $this->load->library('email');
        $this->email->from(SYS_MAIL, NOMSIS);
        $this->email->message($cuerpo_mail);
        $this->email->to($ed['mail_u']);
        $this->email->cc($ed['mail_s']);
        $this->email->subject($subject);
        IF (MAILBCC && MAILBCC !="")
            $this->email->bcc(MAILBCC);
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
        
    }
    function _generar_tareas($id_ed)
    {
        $id_usuario = $this->user['id'];
        $this->load->model('ed/ed_evaluaciones_model','ed_evaluaciones',true);
        $this->load->model("gestion_riesgo/gr_tareas_model","gr_tareas",true);
        $checkTareas=$this->gr_tareas->checkTareasED($id_ed);
        if($checkTareas==0)
        {
            $ed=$this->ed_evaluaciones->dameCabeceraEd($id_ed);
//            echo "<pre>". print_r($ed,true)."<pre>";
            //busco planes de mejora
            $this->load->model("ed/ed_pm_model","ed_pm",true);
            $ppmm=$this->ed_pm->damePpmmPorEd($id_ed);
    //        echo "<pre>". print_r($ppmm,true)."<pre>";

            //busco si existen fijación de metas
            $this->load->model("ed/ed_metas_model","ed_metas",true);
            $metas=$this->ed_metas->dameMetasPorEd($id_ed);
    //        echo "<pre>". print_r($metas,true)."<pre>";

            //tareas
            $datos=array();
            $tareas=array();

            $datos['usuario_alta']          =$ed['id_usuario_supervisor'];
            $datos['id_estado']              =1;
            $datos['id_grado_crit']         =3;
            $datos['id_tipo_herramienta']   =7; //7=Evaluacion de desempeño (ED)
            $datos['id_herramienta']        =$id_ed;

            foreach ($ppmm as $pm)
            {
                if ($pm['resp_tarea']==1)
                    $datos['usuario_responsable']   =$ed['id_usuario'];
                else
                    $datos['usuario_responsable']   =$ed['id_usuario_supervisor'];

                $datos['hallazgo']              ="Evaluación de Desempeño ".$ed['periodo'].":<b>".$pm['competencia']."</b>: ".$pm['subcompetencia'];
                $datos['tarea']                 =$pm['accion'];
                $datos['fecha_vto']             =$pm['fecha_plazo'];
                $tareas[]=$datos;

            }
            foreach ($metas as $meta)
            {
                $datos['usuario_responsable']   =$ed['id_usuario'];
                $datos['hallazgo']              ="Meta estabecida por <b>".$ed['supervisor']."</b> en la Evaluación de Desempeño del período ".$ed['periodo'];
                $datos['tarea']                 =$meta['meta'];
                $datos['fecha_vto']             =$meta['fecha_plazo'];
                $tareas[]=$datos;

            }
//            echo "<pre>". print_r($tareas,true)."<pre>";

            $this->load->model("gestion_riesgo/gr_historial_acciones_model","gr_historial_acciones",true);
             foreach ($tareas as $tarea)
            {
                 //inserto tarea
                $insert_id=$this->gr_tareas->insert($tarea);

                 //guardo historial
                $id_accion=1;
                $texto="Nueva tarea desde ED ".$ed['periodo']." de <b>".$ed['usuario']."</b> supervisada por <b>".$ed['supervisor']."</b>";
                $datos2['id_usuario']    =$ed['id_usuario_supervisor']; 
                $datos2['id_tarea']      =$insert_id;
                $datos2['id_accion']     =$id_accion;
                $datos2['texto']         =$texto;
                $insert_id=$this->gr_historial_acciones->insert($datos2);
             }
             return 1;
        }
        else
            return 0;
        
    }
        
        
}
?>