<?php
class Dropbox extends CI_Controller
{
    private $modulo=18;
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
            $modulo = $this->uri->segment(SEGMENTOS_DOM+2);
            if (!isset($modulo)) $modulo=0;
            $this->load->model('permisos_model','permisos_model',true);
            $variables['permiso'] = $this->permisos_model->checkIn($this->user['perfil_id'],$modulo);
            $variables['permiso_permiso'] = $this->permisos_model->checkIn($this->user['perfil_id'],$this->modulo);
            $this->load->view('dms/dropbox/listado',$variables);
	}
        public function arbol()
        {
             $this->load->model('dms/dropbox_model','dropbox',true);
            $directorios=$this->dropbox->dameDirectoriosRaiz();
//            echo "directorios:<pre>".print_r($directorios,true)."</pre>";
            
            $arbol=array();
            $i=0;
            foreach ($directorios as $dir)
            {
                $children=array();
                $j=0;
                $arbol[$i]['text']=$dir['dir'];
                
//                echo "directorios:<pre>".print_r($subdirectorios,true)."</pre>";
                
                if($dir['hijos']==0)
                    $arbol[$i]['leaf']=true;
                else
                {
                    $arbol[$i]['leaf']=false;
                    $subdirectorios=$this->dropbox->dameSubDirectorios($dir['id_directorio']);
                     foreach ($subdirectorios as $sub)
                    {
                        $children[$j]['text']=$sub['dir'];
                        $children[$j]['leaf']=true;
                        $children[$j]['iconCls']="folder_ico";
                        $children[$j]['id']="sub-".$sub['id_directorio'];
                        $j++;
                    }
                    $arbol[$i]['children']=$children;
                }
                    
                
                $i++;
            }
//            echo "directorios:<pre>".print_r($arbol,true)."</pre>";
            echo json_encode($arbol);
        }
        public function archivos()
        {
            $id_directorio = explode('-', $this->input->post("id"));
            $id_directorio = array_pop($id_directorio);
            $start = $this->input->post("start");
            $limit = $this->input->post("limit");
            $filtro = "";
            $busqueda = "";
            $campos = "";
            $sort = $this->input->post("sort");
            $dir = $this->input->post("dir");
            $this->load->model('dms/dropbox_model','dropbox',true);
            $listado = $this->dropbox->dameArchivosPorDirectorio($start, $limit, $filtro,$busqueda, $campos, $sort, $dir,$id_directorio);
            echo $listado;
        }
	
//	public function listado()
//	{
//		if ($this->permisos['Listar'])
//                {
//                    $this->load->model('dms/documentos_model','documentos',true);
//                    $start = $this->input->post("start");
//                    $limit = $this->input->post("limit");
//                    $sort = $this->input->post("sort");
//                    $dir = $this->input->post("dir");
//                    $filtro = ($this->input->post("filtro_id_td")&&$this->input->post("filtro_id_td")!=-1)?$this->input->post("filtro_id_td"):"";
//                    $busqueda  = ($this->input->post("query"))?$this->input->post("query"):"";
//                    $campos="";
//                    if ($this->input->post("fields"))
//                    {
//                        $campos = str_replace('"', "", $this->input->post("fields"));
//                        $campos = str_replace('[', "", $campos);
//                        $campos = str_replace(']', "", $campos);
//                        $campos = explode(",",$campos);
//    //                            echo "<pre>".print_r($campos,true)."</pre>";
//                    }
//                    $listado = $this->documentos->publicados($start, $limit, $filtro,$busqueda, $campos, $sort, $dir);
//                    echo $listado;
//                }
//                else
//                    echo -1; //No tiene permisos
//                
//	}
        
        //temporal para manipular archivos
        public function _procesar()
	{
            $this->load->helper('file');
            $baseIniPath='C:/xampp/htdocs/polidata/orocobre/uploads/dms/dropbox/Raiz/';
            $baseFinPath='C:/xampp/htdocs/polidata/orocobre/uploads/dms/dropbox/';
            
            $this->load->model('dms/dropbox_model','dropbox',true);
            $archivo=$this->dropbox->dameArchivos();
//            echo "archivo:<pre>".print_r($archivo,true)."</pre>";
            $directorios=$this->dropbox->dameDirectorios();
//            echo "directorios:<pre>".print_r($directorios,true)."</pre>";
            
            
            
            $archivosConDirectorio=$this->dropbox->dameArchivosConDirectorio();
            foreach ($archivosConDirectorio as &$arch)
            {
                $arch['archivo_nom']=utf8_decode($arch['archivo_nom']);
                $arch['dir']=utf8_decode($arch['dir']);
                $arch['sub']=utf8_decode($arch['sub']);
                if($arch['dir']!="")
                    $arch['path']=$arch['dir']."/".$arch['sub']."/".$arch['archivo_nom'];
                else
                    $arch['path']=$arch['sub']."/".$arch['archivo_nom'];
                
                $ext=  explode('.', $arch['archivo_nom']);
                $ext=  array_pop($ext);
                $arch['ext']=$ext;
                $numero= str_repeat("0",3-strlen($arch['id_archivo'])).$arch['id_archivo'];
                $archivo = $baseIniPath.$arch['path'];
                $arch['mime_type']=get_mime_by_extension($archivo);
                $arch['date']=get_file_info($archivo);
                $arch['date']=$arch['date']['date'];
                $nom_nuevo_archivo ='DBX'.$numero.md5($arch['id_archivo']).".".$ext;
                $nuevo_archivo = $baseFinPath.$nom_nuevo_archivo;
                
                $datos['ext']=$arch['ext'];
                $datos['archivo']=$nom_nuevo_archivo;
                $datos['fecha']=$arch['date'];
                $datos['mime_type']=$arch['mime_type'];
                $this->dropbox->update_archivos($arch['id_archivo'],$datos);
                
                if (!copy($archivo, $nuevo_archivo)) {
                    echo "Error al copiar $archivo...\n";
                }
                
            }
            
        }

}
?>