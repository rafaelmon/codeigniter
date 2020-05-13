<?php
class Downloads extends CI_Controller
{

    function __construct()
        {
                parent::__construct();
                header ("Expires: Thu, 27 Mar 1980 23:59:00 GMT"); //la pagina expira en una fecha pasada
                header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); //ultima actualizacion ahora cuando la cargamos
                header ("Cache-Control: no-cache, must-revalidate"); //no guardar en CACHE
                header ("Pragma: no-cache");
                header ("Expires: -1");
//                header("Content-type: application/msword"); //or yours?
//                header("Content-Transfer-Encoding: binary");
//                header("Cache-Control: post-check=0, pre-check=0");
//                 header("Content-Type: application/force-download");
                 if(!$this->session->userdata(USER_DATA_SESSION))
                    redirect("admin/admin/index_js","location", 301);
        }

        //descarga Plantilla del modullo DMS
        public function dms_plantilla($id_plantilla)
	{

            $user=$this->session->userdata(USER_DATA_SESSION);
            $this->load->model("permisos_model","permisos_model",true);
            $permisos = $this->permisos_model->checkIn($user['perfil_id'],14);
            //verifico si el usuario tiene permisos para listar el modulo de plantillas
            //mejorar con permisos del rol de editor en dms
            if ($permisos['Listar'])
            {
                header ("Content-type: application/msword; charset=UTF-8");
                header('Content-Disposition: inline');
//                header('Content-Disposition: attachment; filename="downloaded.pdf"');
                $archivo=PATH_DMS_PLANTILLAS.md5("PLT".$id_plantilla.".dot").".dot";
//                echo $archivo; die();
                readfile($archivo);
                
            }
            else
                redirect("admin/admin/index_js","location", 301);
	
        }
        //Descarga archivos anterirores que entes estaban en dropbox
        public function dms_dbx($id_archivo)
	{
            

            $user=$this->session->userdata(USER_DATA_SESSION);
            $this->load->model("permisos_model","permisos_model",true);
            $permisos = $this->permisos_model->checkIn($user['perfil_id'],18);
            //verifico si el usuario tiene permisos para listar el modulo de plantillas
            //mejorar con permisos del rol de editor en dms
            if ($permisos['Listar'])
            {
//                $id_archivo=$this->input->post("id");
                
                $this->load->model('dms/dropbox_model','dropbox',true);
                $archivo = $this->dropbox->dameArchivo($id_archivo);
//                echo "archivo:<pre>".print_r($archivo,true)."</pre>";
                
//                $numero= str_repeat("0",3-strlen($arch['id_archivo'])).$arch['id_archivo'];
//                $nom_nuevo_archivo ='DBX'.$numero.md5($arch['id_archivo']).".".$ext;
                
                if($archivo)
                {
                    header ("Content-type: $archivo ['mime_type']; charset=UTF-8");
                    header('Content-Disposition: inline; filename="'.$archivo['archivo_nom'].'"');
    //                header('Content-Disposition: attachment; filename="downloaded.pdf"');
                    $archivo=PATH_DMS_DROPBOX.$archivo['archivo'];
    //                echo $archivo; die();
                    readfile($archivo);
                }
                else
                    echo "<h2><b>Error: Archivo no encontrado</b></h2>";
                
            }
            else
                redirect("admin/admin/index_js","location", 301);
	
        }
        
       

}

?>
