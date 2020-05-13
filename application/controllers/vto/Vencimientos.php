<?php
class Vencimientos extends CI_Controller
{
    private $modulo=55;
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
        $this->load->view('vto/vencimientos/listado',$variables);
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
            
            $this->load->model('vto/vto_vencimientos_model','vto_vencimientos',true);
            $listado = $this->vto_vencimientos->listado($start, $limit, $sort,$dir,$busqueda,$campos);
            echo $listado;
            
        }
    }
            
    
    
    public function insert()
    {
        if ($this->permisos['Alta'])
        {          
            $datos["vencimiento"]               = $this->input->post("vencimiento");
            $datos["descripcion"]               = $this->input->post("descripcion");
            $datos["id_usuario_responsable"]    = $this->input->post("id_usuario_responsable");
            $datos["fecha_vencimiento"]         = $this->input->post("fecha_vto");
            $datos["dias_avisos"]               = $this->input->post("dias_avisos");
            $datos["q_avisos"]                  = $this->input->post("q_avisos");
            $datos["rpd"]                       = $this->input->post("rpd");
            $datos["id_estado"]                 = 1;
            $datos["id_usuario_alta"]           = $this->user['id'];
            
//            echo "<pre>Origen".print_r($datos,true)."</pre>";die();
            if ($_FILES['file'][ 'size' ] < 5242880) //Maximo 5MB
            {
                $this->load->model('vto/vto_vencimientos_model','vto_vencimientos',true);

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
                    $today = new DateTime(date('Y-m-d'));
                    $fecha = new DateTime(date("Y-m-d",  strtotime($datos['fecha_vencimiento'])));
                    $diff = date_diff($fecha,  $today);

                    $dias = $diff->format('%a');
//                    echo $dias.'///';
//                    echo $datos["dias_avisos"].'///';
                    if($datos["dias_avisos"] <= $dias)
                    {
                        if($datos["dias_avisos"] >= $datos["q_avisos"])
                        {
                            $insert=$this->vto_vencimientos->insert($datos);
                            if($insert)
                            {
                                //envio mail
                                $this->preparaMail($insert,'alta');
                                $fechaAlta = $this->vto_vencimientos->dameMesAnioFechaAlta($insert);
                                $anio = $fechaAlta['anio'];  
                                $mes = $fechaAlta['mes'];  
                                $ruta_absoluta = PATH_VTO_FILES."$anio";
                                if (!is_dir($ruta_absoluta))
                                    mkdir($ruta_absoluta);
                                $ruta_absoluta .= "/$mes";
                                if (!is_dir($ruta_absoluta))
                                    mkdir($ruta_absoluta);
    //                            echo $ruta_absoluta;
                                $nombre = trim(str_replace(" ","_", $_FILES [ 'file' ][ 'name']));
                                $extensiones_upload = explode(".", $nombre);
                                foreach ($extensiones_upload as $key=>&$value)
                                {
                                    $value=strtolower($value);
                                }
                //                echo "<pre>".print_r($extensiones_upload,true)."</pre>";
                                unset($value);
                                $extensiones_no_permitidas = array("php","php5","msi","exe","bat");

                                if(count(array_intersect($extensiones_no_permitidas, $extensiones_upload))==0)
                                {
                                    $id_vto = 10000+$insert;
                                    $nombre_archivo="vto_".$id_vto.md5(KEYMD5."$insert").".".end($extensiones_upload);
                                    $destino_final=trim($ruta_absoluta . '/' . $nombre_archivo);
                                    if (move_uploaded_file( $_FILES [ 'file' ][ 'tmp_name' ],$destino_final))
                                    {
                                        $archivo['archivo']=$nombre;
    //                                        echo $archivo['archivo'];
                                        $update = $this->vto_vencimientos->update($insert,$archivo);
                                        if($update)
                                            echo "({success: true, msg:'1'})";
                                        else
                                            echo "({success: true, msg:'6'})";
                                    }
                                    else
                                        echo "({success: true, msg:'8'})";
                                }
                                else 
                                    echo "({success: true, msg:'7'})";
                            }
                            else 
                                echo "({success: true, msg:'0'})";
                        }
                        else
                             echo "({success: true, msg:'4'})";
                    }
                    else
                        echo "({success: true, msg:'9'})";
                }
                else
                    echo "({success: true, msg:'2'})";
            }
            else
                echo "({success: true, msg:'5'})";
        }
        else
            echo "({success: true, msg:'3'})";
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
    
    public function eliminarVencimiento()
    {
        if ($this->permisos['Modificacion'])
        {
            $id_vencimiento             = $this->input->post("id_vencimiento");
            $datos['fecha_accion']      = date('Y-m-d H:i:s');
            $datos['id_usuario_accion'] = $this->user['id'];
            $id_accion                  = 4;
            $id_estado_actual           = $this->input->post("id_estado_actual");
            
            $this->load->model('vto/vto_mef_model','vto_mef',true);
            $estado_sig = $this->vto_mef->dameEstadoSiguiente($id_estado_actual,$id_accion);
            
            if($estado_sig != 0)
            {
                $datos['id_estado'] = $estado_sig;
                $this->load->model('vto/vto_vencimientos_model','vto_vencimientos',true);

                $update = $this->vto_vencimientos->update($id_vencimiento,$datos);
                if($update)
                    echo 1;
                else
                    echo 2;
            }
            else
                echo 3;
        }
    }
    
    public function cerrarVencimiento()
    {
        if ($this->permisos['Modificacion'])
        {
            $id_vencimiento             = $this->input->post("id_vencimiento");
            $datos['comentario']        = $this->input->post("comentario");
            $renueva                    = $this->input->post("renovacion");
            $datos['fecha_accion']      = date('Y-m-d H:i:s');
            $datos['id_usuario_accion'] = $this->user['id'];
            $id_estado_actual           = $this->input->post("id_estado_actual");
            
            $this->load->model('vto/vto_vencimientos_model','vto_vencimientos',true);
           
                if($renueva == true)
                {
                    $this->load->model('vto/vto_mef_model','vto_mef',true);
                    if ($_FILES['file'][ 'size' ] < 5242880) //Maximo 5MB
                    {
                        $id_accion = 2;
                        $estado_sig = $this->vto_mef->dameEstadoSiguiente($id_estado_actual,$id_accion);

                        if ($estado_sig != 0)
                        {
                            $datos['id_estado'] = $estado_sig;
                            $data['id_usuario_alta']    = $this->user['id'];
                            $data['id_estado']          = 1;
                            $data['fecha_vencimiento']  = $this->input->post("fecha_vencimiento");
                            $data['id_vto_anterior']    = $id_vencimiento;
                            $data['dias_avisos']        = $this->input->post("dias_avisos");
                            $data['q_avisos']           = $this->input->post("q_avisos");
                            $control=true;
                            foreach ($datos as $atributo=>$dato)
                            {
                                if($dato=="")
                                {
                                    $control=false;
                                }
                            }
                            foreach ($data as $atributo=>$dato)
                            {
                                if($dato=="")
                                {
                                    $control=false;
                                }
                            }
                            if ($control)
                            {
                                $update = $this->vto_vencimientos->update($id_vencimiento,$datos);
                                if($update)
                                {                                    
                                    $today = new DateTime(date('Y-m-d'));
                                    $fecha = new DateTime(date("Y-m-d",  strtotime($data['fecha_vencimiento'])));
                                    $diff = date_diff($fecha,  $today);

                                    $dias = $diff->format('%a');
                //                    echo $dias.'///';
                //                    echo $datos["dias_avisos"].'///';
                                    if($data["dias_avisos"] <= $dias)
                                    {
                                        if($data["dias_avisos"] >= $data["q_avisos"])
                                        {

                                        $vto = $this->vto_vencimientos->dameVencimientoParaRenovacion($id_vencimiento);
                                        $data = array_merge($data,$vto);
                                        $insert=$this->vto_vencimientos->insert($data);
                                        if($insert)
                                        {
                                            $fechaAlta = $this->vto_vencimientos->dameMesAnioFechaAlta($insert);
                                            $anio = $fechaAlta['anio'];  
                                            $mes = $fechaAlta['mes'];  
                                            $ruta_absoluta = PATH_VTO_FILES."$anio";
                                            if (!is_dir($ruta_absoluta))
                                                mkdir($ruta_absoluta);
                                            $ruta_absoluta .= "/$mes";
                                            if (!is_dir($ruta_absoluta))
                                                mkdir($ruta_absoluta);
                                            $nombre = trim(str_replace(" ","_", $_FILES [ 'file' ][ 'name']));
                                            $extensiones_upload = explode(".", $nombre);
                                            foreach ($extensiones_upload as $key=>&$value)
                                            {
                                                $value=strtolower($value);
                                            }
                            //                echo "<pre>".print_r($extensiones_upload,true)."</pre>";
                                            unset($value);
                                            $extensiones_no_permitidas = array("php","php5","msi","exe","bat");

                                            if(count(array_intersect($extensiones_no_permitidas, $extensiones_upload))==0)
                                            {
                                                $id_vto = 10000+$insert;
                                                $nombre_archivo="vto_".$id_vto.md5(KEYMD5."$insert").".".end($extensiones_upload);
                                                $destino_final=trim($ruta_absoluta . '/' . $nombre_archivo);
                                                if (move_uploaded_file( $_FILES [ 'file' ][ 'tmp_name' ],$destino_final))
                                                {
                                                    $archivo['archivo']=$nombre;
            //                                        echo $archivo['archivo'];
                                                    $update = $this->vto_vencimientos->update($insert,$archivo);
                                                    if($update)
                                                    {
                                                        //envio mail
                                                        $this->preparaMail($insert,'renueva');
                                                        echo "({success: true, msg:'3'})";
                                                    }
                                                    else
                                                        echo "({success: true, msg:'4'})";
                                                }
                                                else
                                                    echo "({success: true, msg:'8'})";
                                            }
                                            else 
                                                echo "({success: true, msg:'7'})";
                                        }
                                        else 
                                            echo "({success: true, msg:'4'})";
                                    }
                                        else
                                            echo "({success: true, msg:'9'})";
                                    }
                                    else
                                        echo "({success: true, msg:'11'})";
                                }
                                else
                                    echo "({success: true, msg:'2'})";
                            }
                            else
                                echo "({success: true, msg:'10'})";
                        }
                        else
                            echo "({success: true, msg:'5'})";
                    }
                    else
                        echo "({success: true, msg:'6'})";
                }
                else
                {
                    $id_accion = 1;
                    $estado_sig = $this->vto_mef->dameEstadoSiguiente($id_estado_actual,$id_accion);

                    if ($estado_sig != 0)
                    {
                        $datos['id_estado'] = $estado_sig;
                        $update = $this->vto_vencimientos->update($id_vencimiento,$datos);
                        if($update)
                            echo "({success: true, msg:'1'})";
                        else
                            echo "({success: true, msg:'2'})";
                    }
                    else
                        echo "({success: true, msg:'5'})";
                
                }
            
        }
    }
    
    function preview()
    {
//            $id_archivo=2;
        $id_vencimiento = $this->uri->segment(SEGMENTOS_DOM+2);
//            echo $id_vencimiento;
//            echo '<br>';die();
        $this->load->model("vto/vto_vencimientos_model","vto_vencimientos",true);
        $archivo=$this->vto_vencimientos->dameArchivo($id_vencimiento);
//        echo $archivo;
        if ($archivo!="")
        {
//            echo 'entra';
            $extensiones_upload = explode(".", $archivo);
            $id_vto = 10000+$id_vencimiento;
            $nombre_archivo="vto_".$id_vto.md5(KEYMD5."$id_vencimiento").".".end($extensiones_upload);
//            echo $nombre_archivo;
            $fechaAlta = $this->vto_vencimientos->dameMesAnioFechaAlta($id_vencimiento);
            $anio = $fechaAlta['anio'];  
            $mes = $fechaAlta['mes'];  
            $path = PATH_VTO_FILES."$anio/"."$mes/";
            $archivo_path=$path.$nombre_archivo;
//            echo $archivo_path;
            if(file_exists($archivo_path))
            {
//                echo 'entra';
                $finfo = finfo_open(FILEINFO_MIME);
                $mimeType=finfo_file($finfo, $archivo_path);
//                    echo $mimeType; die();
//                    finfo_close($finfo);
                header ("Content-type:".$mimeType."; charset=UTF-8");
//                    header ('Content-Type: application/octet-stream; charset=UTF-8');
//                     header ("Content-type: application/pdf; charset=UTF-8");
                header('Content-Disposition: inline');
//                    header('Content-Disposition: attachment filename='.$archivo['archivo_nom_orig']);
//                    header('Content-Disposition: attachment; filename="downloaded.pdf"');
//                    header ("Expires: 0");
                readfile($archivo_path);
            }
            else {
                echo "<h2><b>Error:</b> Archivo no encontrado!</h2>";
            }
        }
        else
            return "Error localizando archivo!";

    }
    function preparaMail($id_vto,$accion)
    {
        $datos_mail=array();
        $this->load->model('vto/vto_vencimientos_model','vto_vencimientos',true);
        $vto = $this->vto_vencimientos->dameVtoDatosMail($id_vto);
//        echo "<pre>".print_r($vto,true)."</pre>";
        $link='www.polidata.com.ar/sdj/admin';
$alta_txt=<<<HTML
<html> 
<head>
    <title>SMC</title>

</head>
<body>
<div style="color:#000000; font-family:Verdana, Arial, Helvetica, sans-serif; text-align:Left; font-size:13px;">
    <pre>
        Nuevo vencimiento registrado
        
        Fecha:<b>#fecha</b>
        Titulo:<b>#titulo</b>
        Descripci&oacute;n:<b>#descripcion</b>
        Responsable: <b>#u_responsable</b>
        Generado por:<b>#u_alta</b>


        Puede continuar la gesti&oacute;n de este evento ingresando al sistema desde <a href="#link" target="_blank">aqu&iacute;</a>, o copiando y pegando la siguiente direcci&oacute;n en su navegador web <b>Mozilla Firefox</b> o <b>Google Chrome</b>: 
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

$renueva_txt=<<<HTML
<html> 
<head>
    <title>SMC</title>

</head>
<body>
<div style="color:#000000; font-family:Verdana, Arial, Helvetica, sans-serif; text-align:Left; font-size:13px;">
    <pre>
        Se ha renovado el siguiente vencimiento
        
        Nueva fecha:<b>#fecha</b>
        Titulo:<b>#titulo</b>
        Descripci&oacute;n:<b>#descripcion</b>
        Responsable: <b>#u_responsable</b>
        Renovado por:<b>#u_alta</b>


        Puede continuar la gesti&oacute;n de este evento ingresando al sistema desde <a href="#link" target="_blank">aqu&iacute;</a>, o copiando y pegando la siguiente direcci&oacute;n en su navegador web <b>Mozilla Firefox</b> o <b>Google Chrome</b>: 
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
    switch ($accion) 
    {
        case 'alta':
            $datos_mail['texto']=$alta_txt;
            $datos_mail['asunto']='Alta nuevo Vencimiento';
            break;
        case 'renueva':
            $datos_mail['texto']=$renueva_txt;
            $datos_mail['asunto']='Vencimiento renovado';
            break;
        default:
            break;
    }
     $datos_mail['texto']=str_replace("#fecha"          , $vto['fecha_vto'],            $datos_mail['texto']);
     $datos_mail['texto']=str_replace("#titulo"         , $vto['vencimiento'],          $datos_mail['texto']);
     $datos_mail['texto']=str_replace("#descripcion"    , $vto['descripcion'],          $datos_mail['texto']);
     $datos_mail['texto']=str_replace("#u_responsable"  , $vto['usuario_responsable'],  $datos_mail['texto']);
     $datos_mail['texto']=str_replace("#u_alta"         , $vto['usuario_alta'],         $datos_mail['texto']);
     $datos_mail['texto']=str_replace("#usuarioPara"    , $vto['email_responsable'],    $datos_mail['texto']);
     $datos_mail['texto']=str_replace("#usuariosCC"     , $vto['email_alta'],           $datos_mail['texto']);
     $datos_mail['texto']=str_replace("#link"           , $link,                        $datos_mail['texto']);
     $datos_mail['texto']=str_replace("#firma"          , htmlentities(NOMSIS,ENT_QUOTES,"UTF-8"),$datos_mail['texto']);
     
     
    $datos_mail['usuarioPara']=$vto['email_responsable'];
    $datos_mail['usuariosCC']=$vto['email_alta'];
     
//     echo "<pre>".print_r($datos_mail,true)."</pre>";
    $this->_enviarMail($datos_mail);
     
    }
    function _enviarMail($datos)
    {
        
        $this->load->library('email');
//        $this->email->clear();//para cuando uso un bucle
        $this->email->from(SYS_MAIL, NOMSIS);
        $this->email->message($datos['texto']);
        $this->email->subject($datos['asunto']);
        $this->email->to($datos['usuarioPara']);
        $this->email->cc($datos['usuariosCC']);
        IF (MAILBCC && MAILBCC !="")
            $this->email->bcc(MAILBCC);
//         echo "<pre>".print_r($datos,true)."</pre>";
//        die();
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
}
?>