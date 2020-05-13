<?php
class Archivos extends CI_Controller
{
    private $user;
    
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
	}
	
	function nodosDropBox()
        {
            $directorio='C:\Users\fm\Documents\OroCobre\DropBox\Raiz';
            $url='https://www.dropbox.com/sh/ee8y39qeixhi4nv/Xhy79OzMMb';
            $this->load->helper('file');
            
//            $filenames=get_filenames($directorio);
//            echo "get_filenames()";
//            echo "<pre>".print_r($filenames,true)."</pre>";
            
            $filenames=get_dir_file_info($directorio,FALSE);
//            echo "get_dir_file_info()";
            
            foreach ($filenames as &$data)
            {
                unset($data['server_path']);
                unset($data['date']);
                $data['relative_path'] = str_replace('C:\Users\fm\Documents\OroCobre\DropBox\Raiz\\',"", $data['relative_path']);
                $data['relative_path'] = substr( $data['relative_path'],0,-1);
                $data['size'] =  round($data['size']/1024,2) ;
                $relative_path2 = explode("\\", $data['relative_path'] );
                $data['niveles'] = count($relative_path2);
                $data['nivel1'] = $relative_path2[0];
                $data['nivel2'] = ($data['niveles']==1)?"":$relative_path2[1];
            }
            
            $header=array('name'=>'Nombre','size'=>'Size (Kb)','relative_path'=>'Path relativo','niveles'=>'Niveles','nivel1'=>'Directorio','nivel2'=>'Subdirectorio');
            array_unshift($filenames,$header);
//            echo "<pre>".print_r($filenames,true)."</pre>";
//            
//            $mimetype=get_mime_by_extension('C:\Users\fm\Documents\OroCobre\DropBox\Raiz\00 Generales\Documentos\DC-SDG01-01 InducciónGeneral.pdf');
//            echo "get_mime_by_extension(DC-SDG01-01 InducciónGeneral.pdf): ".$mimetype;
            
//            $filenames=get_dir_file_info($directorio);
//            echo "get_dir_file_info('path/to/directory/')";
//            echo "<pre>".print_r($filenames,true)."</pre>";
            
            header("Content-Disposition: attachment; filename=\"demo2.xls\"");
            header("Content-Type: application/vnd.ms-excel;");
            header("Pragma: no-cache");
            header("Expires: 0");
            $out = fopen("php://output", 'w');
            foreach ($filenames as $data)
            {
                fputcsv($out, $data,"\t");
            }
//            fclose($out);
            
        }
        public function upload_cerrar_tarea()
	{
            $id_tarea = $this->input->post("id");
    //				list($anio,$mes,$dia)=explode('-',date('Y-m-d'));

            $ruta_absoluta = PATH_TAREAS_FILES.$id_tarea;
            if (!is_dir($ruta_absoluta))
                mkdir($ruta_absoluta);                                
            $ruta_absoluta .= "/cierre";
            if (!is_dir($ruta_absoluta))
                mkdir($ruta_absoluta);                                

            if ($_FILES['file'][ 'size' ] < 5242880) //maximo 5MB
            {
                //bypaseo
                $nombre = trim(str_replace(" ","_", $_FILES [ 'file' ][ 'name']));
                $extensiones_upload = explode(".", $nombre);
                $size=$_FILES['file'][ 'size' ];
//                echo "<pre>".print_r($extensiones_upload,true)."</pre>";
                foreach ($extensiones_upload as $key=>&$value)
                {
                    $value=strtolower($value);
                }
//                echo "<pre>".print_r($extensiones_upload,true)."</pre>";
                unset($value);

                $extensiones_no_permitidas = array("php","php5");

                if(count(array_intersect($extensiones_no_permitidas, $extensiones_upload))==0)
                {
                    $nombreFinal = $id_tarea."_".date("YmdHis").".".end($extensiones_upload);
                    $destino_final=trim($ruta_absoluta . '/' . $nombreFinal);
                    if (move_uploaded_file( $_FILES [ 'file' ][ 'tmp_name' ],$destino_final))
                    {
                        //Si todo bien hasta aqui guardo todo en la BD
                         $this->load->model("gestion_riesgo/gr_archivos_model","gr_archivos",true);
                         $datos_upload['id_tarea']=$id_tarea;
                         $datos_upload['archivo_nom_orig']=$nombre;
                         $datos_upload['archivo']=$nombreFinal;
                         $datos_upload['extension']=end($extensiones_upload);
                         $datos_upload['tam']=$size;
                         $insert=$this->gr_archivos->insert($datos_upload);
                        echo '{success:true, res:'.json_encode(1).'}';
                    }
                    else
                        echo '{success:false, res:'.json_encode(500).'}';// error en los directorios
                }
                else 
                    echo '{success:false, res:'.json_encode(5).'}'; //Extension no permitida
            }
            else
                echo '{success:false, res:'.json_encode(5).'}'; //archivo demasiado pesado
	}
        public function upload_ri()
	{
            $id_rmc = $this->input->post("id");
            $ruta_absoluta = PATH_RI_FILES.$id_rmc;
            if (!is_dir($ruta_absoluta))
                mkdir($ruta_absoluta);                                

            if ($_FILES['file'][ 'size' ] < 5242880) //maximo 5MB
            {
                //bypaseo
                $nombre = trim(str_replace(" ","_", $_FILES [ 'file' ][ 'name']));
                $extensiones_upload = explode(".", $nombre);
                $size=$_FILES['file'][ 'size' ];
//                echo "<pre>".print_r($extensiones_upload,true)."</pre>";
                foreach ($extensiones_upload as $key=>&$value)
                {
                    $value=strtolower($value);
                }
//                echo "<pre>".print_r($extensiones_upload,true)."</pre>";
                unset($value);

                $extensiones_no_permitidas = array("php","php5");

                if(count(array_intersect($extensiones_no_permitidas, $extensiones_upload))==0)
                {
                    $nombreFinal = $id_rmc."_".date("YmdHis").".".end($extensiones_upload);
                    $destino_final=trim($ruta_absoluta . '/' . $nombreFinal);
                    if (move_uploaded_file( $_FILES [ 'file' ][ 'tmp_name' ],$destino_final))
                    {
                        //Si todo bien hasta aqui guardo todo en la BD
                         $this->load->model("gestion_riesgo/gr_archivos_rmc_model","gr_archivos_rmc",true);
                         $datos_upload['id_rmc']=$id_rmc;
                         $datos_upload['titulo']=$this->input->post("titulo");
                         $datos_upload['descripcion']=($this->input->post("desc")=="")?NULL:$this->input->post("desc");
                         $datos_upload['archivo_nom_orig']=$nombre;
                         $datos_upload['archivo']=$nombreFinal;
                         $datos_upload['extension']=end($extensiones_upload);
                         $datos_upload['tam']=$size;
                         $datos_upload['usuario_upload']=$this->user['id'];
                         $insert=$this->gr_archivos_rmc->insert($datos_upload);
                        echo '{success:true, res:'.json_encode(1).'}';
                    }
                    else
                        echo '{success:false, res:'.json_encode(500).'}';// error en los directorios
                }
                else 
                    echo '{success:false, res:'.json_encode(5).'}'; //Extension no permitida
            }
            else
                echo '{success:false, res:'.json_encode(5).'}'; //archivo demasiado pesado
	}
        function _do_upload($field_name,$nombre_archivo,$type='pdf|PDF')
	{
		$config['upload_path']  = './uploads/dms/documentos/';
		$config['allowed_types']= $type;
		$config['max_size']	= '2048'; //2MB Max
		$config['remove_spaces']= true;
                $config['overwrite'] = true;
		$config['encrypt_name'] = false;
		$config['file_name'] =$nombre_archivo;
		$this->load->library('upload', $config);
	
		if ( ! $this->upload->do_upload($field_name))
		{
			$error = array('error' => $this->upload->display_errors());
			return $error;
		}	
		else
		{
			$data = array('upload_data' => $this->upload->data());
			return $data;
		}
	}
        function cierre()
        {
//            $id_archivo=2;
            $id_archivo = $this->uri->segment(SEGMENTOS_DOM+1);
//            echo $id_archivo;
//            echo '<br>';die();
            $this->load->model("gestion_riesgo/gr_archivos_model","gr_archivos",true);
            $archivo=$this->gr_archivos->dameArchivo($id_archivo);
            if ($archivo!=0)
            {
                $id_tarea=$archivo['id_tarea'];

    //            echo "<pre>".print_r($archivo,true)."</pre>";
    //            die();
                $path=PATH_TAREAS_FILES.$archivo['id_tarea'].'/cierre/';
                $archivo_path=$path.$archivo['archivo'];
    //            echo $archivo_path;

                if(file_exists($archivo_path))
                {
//                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
//                    $mimeType=finfo_file($finfo, $archivo_path);
//                    finfo_close($finfo);
//                    header ("Content-type:".$mimeType."; charset=UTF-8");
                    header ('Content-Type: application/octet-stream; charset=UTF-8');
                    header('Content-Disposition: attachment filename='.$archivo['archivo_nom_orig']);
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
        function ri()
        {
//            $id_archivo=2;
            $id_archivo = $this->uri->segment(SEGMENTOS_DOM+1);
//            echo $id_archivo;
//            echo '<br>';die();
            $this->load->model("gestion_riesgo/gr_archivos_rmc_model","gr_archivos_rmc",true);
            $archivo=$this->gr_archivos_rmc->dameArchivo($id_archivo);
//            echo "<pre>".print_r($archivo,true)."</pre>";
//            die();
            if ($archivo!=0)
            {
                $id_rmc=$archivo['id_rmc'];

                $path = PATH_RI_FILES.$id_rmc.'/';
                $archivo_path=$path.$archivo['archivo'];
//                echo $archivo_path;die();

                if(file_exists($archivo_path))
                {
//                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
//                    $mimeType=finfo_file($finfo, $archivo_path);
//                    finfo_close($finfo);
//                    header ("Content-type:".$mimeType."; charset=UTF-8");
                    header ('Content-Type: application/octet-stream; charset=UTF-8');
                    header('Content-Disposition: attachment filename='.$archivo['archivo_nom_orig']);
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
        function cap()
        {
//            $id_archivo=2;
            $id_archivo = $this->uri->segment(SEGMENTOS_DOM+1);
//            echo $id_archivo;
//            echo '<br>';die();
            $this->load->model("gestion_riesgo/gr_archivos_model","gr_archivos",true);
            $archivo=$this->gr_archivos->dameArchivo($id_archivo);
            if ($archivo!=0)
            {
                $id_tarea=$archivo['id_tarea'];
                $path=PATH_TAREAS_FILES.$archivo['id_tarea'].'/cierre/';
                $archivo_path=$path.$archivo['archivo'];

                if(file_exists($archivo_path))
                {
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
//    function encodeURIComponent($string) {
//        $result = "";
//        for ($i = 0; $i < strlen($string); $i++) {
//            $result .= $this->encodeURIComponentbycharacter(urlencode($string[$i]));
//        }
//        return $result;
//    }
//

//    function encodeURIComponentbycharacter($char) {
//        if ($char == "+") { return "%20"; }
//        if ($char == "%21") { return "!"; }
//        if ($char == "%27") { return '"'; }
//        if ($char == "%28") { return "("; }
//        if ($char == "%29") { return ")"; }
//        if ($char == "%2A") { return "*"; }
//        if ($char == "%7E") { return "~"; }
//        if ($char == "%80") { return "%E2%82%AC"; }
//        if ($char == "%81") { return "%C2%81"; }
//        if ($char == "%82") { return "%E2%80%9A"; }
//        if ($char == "%83") { return "%C6%92"; }
//        if ($char == "%84") { return "%E2%80%9E"; }
//        if ($char == "%85") { return "%E2%80%A6"; }
//        if ($char == "%86") { return "%E2%80%A0"; }
//        if ($char == "%87") { return "%E2%80%A1"; }
//        if ($char == "%88") { return "%CB%86"; }
//        if ($char == "%89") { return "%E2%80%B0"; }
//        if ($char == "%8A") { return "%C5%A0"; }
//        if ($char == "%8B") { return "%E2%80%B9"; }
//        if ($char == "%8C") { return "%C5%92"; }
//        if ($char == "%8D") { return "%C2%8D"; }
//        if ($char == "%8E") { return "%C5%BD"; }
//        if ($char == "%8F") { return "%C2%8F"; }
//        if ($char == "%90") { return "%C2%90"; }
//        if ($char == "%91") { return "%E2%80%98"; }
//        if ($char == "%92") { return "%E2%80%99"; }
//        if ($char == "%93") { return "%E2%80%9C"; }
//        if ($char == "%94") { return "%E2%80%9D"; }
//        if ($char == "%95") { return "%E2%80%A2"; }
//        if ($char == "%96") { return "%E2%80%93"; }
//        if ($char == "%97") { return "%E2%80%94"; }
//        if ($char == "%98") { return "%CB%9C"; }
//        if ($char == "%99") { return "%E2%84%A2"; }
//        if ($char == "%9A") { return "%C5%A1"; }
//        if ($char == "%9B") { return "%E2%80%BA"; }
//        if ($char == "%9C") { return "%C5%93"; }
//        if ($char == "%9D") { return "%C2%9D"; }
//        if ($char == "%9E") { return "%C5%BE"; }
//        if ($char == "%9F") { return "%C5%B8"; }
//        if ($char == "%A0") { return "%C2%A0"; }
//        if ($char == "%A1") { return "%C2%A1"; }
//        if ($char == "%A2") { return "%C2%A2"; }
//        if ($char == "%A3") { return "%C2%A3"; }
//        if ($char == "%A4") { return "%C2%A4"; }
//        if ($char == "%A5") { return "%C2%A5"; }
//        if ($char == "%A6") { return "%C2%A6"; }
//        if ($char == "%A7") { return "%C2%A7"; }
//        if ($char == "%A8") { return "%C2%A8"; }
//        if ($char == "%A9") { return "%C2%A9"; }
//        if ($char == "%AA") { return "%C2%AA"; }
//        if ($char == "%AB") { return "%C2%AB"; }
//        if ($char == "%AC") { return "%C2%AC"; }
//        if ($char == "%AD") { return "%C2%AD"; }
//        if ($char == "%AE") { return "%C2%AE"; }
//        if ($char == "%AF") { return "%C2%AF"; }
//        if ($char == "%B0") { return "%C2%B0"; }
//        if ($char == "%B1") { return "%C2%B1"; }
//        if ($char == "%B2") { return "%C2%B2"; }
//        if ($char == "%B3") { return "%C2%B3"; }
//        if ($char == "%B4") { return "%C2%B4"; }
//        if ($char == "%B5") { return "%C2%B5"; }
//        if ($char == "%B6") { return "%C2%B6"; }
//        if ($char == "%B7") { return "%C2%B7"; }
//        if ($char == "%B8") { return "%C2%B8"; }
//        if ($char == "%B9") { return "%C2%B9"; }
//        if ($char == "%BA") { return "%C2%BA"; }
//        if ($char == "%BB") { return "%C2%BB"; }
//        if ($char == "%BC") { return "%C2%BC"; }
//        if ($char == "%BD") { return "%C2%BD"; }
//        if ($char == "%BE") { return "%C2%BE"; }
//        if ($char == "%BF") { return "%C2%BF"; }
//        if ($char == "%C0") { return "%C3%80"; }
//        if ($char == "%C1") { return "%C3%81"; }
//        if ($char == "%C2") { return "%C3%82"; }
//        if ($char == "%C3") { return "%C3%83"; }
//        if ($char == "%C4") { return "%C3%84"; }
//        if ($char == "%C5") { return "%C3%85"; }
//        if ($char == "%C6") { return "%C3%86"; }
//        if ($char == "%C7") { return "%C3%87"; }
//        if ($char == "%C8") { return "%C3%88"; }
//        if ($char == "%C9") { return "%C3%89"; }
//        if ($char == "%CA") { return "%C3%8A"; }
//        if ($char == "%CB") { return "%C3%8B"; }
//        if ($char == "%CC") { return "%C3%8C"; }
//        if ($char == "%CD") { return "%C3%8D"; }
//        if ($char == "%CE") { return "%C3%8E"; }
//        if ($char == "%CF") { return "%C3%8F"; }
//        if ($char == "%D0") { return "%C3%90"; }
//        if ($char == "%D1") { return "%C3%91"; }
//        if ($char == "%D2") { return "%C3%92"; }
//        if ($char == "%D3") { return "%C3%93"; }
//        if ($char == "%D4") { return "%C3%94"; }
//        if ($char == "%D5") { return "%C3%95"; }
//        if ($char == "%D6") { return "%C3%96"; }
//        if ($char == "%D7") { return "%C3%97"; }
//        if ($char == "%D8") { return "%C3%98"; }
//        if ($char == "%D9") { return "%C3%99"; }
//        if ($char == "%DA") { return "%C3%9A"; }
//        if ($char == "%DB") { return "%C3%9B"; }
//        if ($char == "%DC") { return "%C3%9C"; }
//        if ($char == "%DD") { return "%C3%9D"; }
//        if ($char == "%DE") { return "%C3%9E"; }
//        if ($char == "%DF") { return "%C3%9F"; }
//        if ($char == "%E0") { return "%C3%A0"; }
//        if ($char == "%E1") { return "%C3%A1"; }
//        if ($char == "%E2") { return "%C3%A2"; }
//        if ($char == "%E3") { return "%C3%A3"; }
//        if ($char == "%E4") { return "%C3%A4"; }
//        if ($char == "%E5") { return "%C3%A5"; }
//        if ($char == "%E6") { return "%C3%A6"; }
//        if ($char == "%E7") { return "%C3%A7"; }
//        if ($char == "%E8") { return "%C3%A8"; }
//        if ($char == "%E9") { return "%C3%A9"; }
//        if ($char == "%EA") { return "%C3%AA"; }
//        if ($char == "%EB") { return "%C3%AB"; }
//        if ($char == "%EC") { return "%C3%AC"; }
//        if ($char == "%ED") { return "%C3%AD"; }
//        if ($char == "%EE") { return "%C3%AE"; }
//        if ($char == "%EF") { return "%C3%AF"; }
//        if ($char == "%F0") { return "%C3%B0"; }
//        if ($char == "%F1") { return "%C3%B1"; }
//        if ($char == "%F2") { return "%C3%B2"; }
//        if ($char == "%F3") { return "%C3%B3"; }
//        if ($char == "%F4") { return "%C3%B4"; }
//        if ($char == "%F5") { return "%C3%B5"; }
//        if ($char == "%F6") { return "%C3%B6"; }
//        if ($char == "%F7") { return "%C3%B7"; }
//        if ($char == "%F8") { return "%C3%B8"; }
//        if ($char == "%F9") { return "%C3%B9"; }
//        if ($char == "%FA") { return "%C3%BA"; }
//        if ($char == "%FB") { return "%C3%BB"; }
//        if ($char == "%FC") { return "%C3%BC"; }
//        if ($char == "%FD") { return "%C3%BD"; }
//        if ($char == "%FE") { return "%C3%BE"; }
//        if ($char == "%FF") { return "%C3%BF"; }
//        return $char;
//    }
}
?>