<?php
class SeparacionSales extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		header ("Expires: Thu, 27 Mar 1980 23:59:00 GMT"); //la pagina expira en una fecha pasada
		header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); //ultima actualizacion ahora cuando la cargamos
		header ("Cache-Control: no-cache, must-revalidate"); //no guardar en CACHE
		header ("Pragma: no-cache");
                $user=$this->session->userdata(USER_DATA_SESSION);
                if (!($user['id']>0)) 
                    redirect("admin/admin/index_js","location", 301);
	}
	
	public function eliminarDocumentosBorax()
	{
            $this->load->model('separacion_model','separacion',true);
            $alcance = 2;
            $documentos = $this->separacion->dameDocumentos($alcance);
            
//            $id_documento = 31;
//            $archivo = "0000031PDF10778a7146a07e4a862f6c645417e6cc.pdf";
//            $path = PATH_DMS_DOCS.$archivo;
//            
//            if($deleteDocumento == true)
//                unlink($path);
            
//            echo $archivo;
//            echo "<pre>".print_r($documentos,true)."</pre>";
            $resultado = array();
            $i = 0;
            if ($documentos != 0)
            {
                foreach($documentos as $array)
                {
                    foreach($array as $atrib=>$valor)
                    {
                        $deleteObservaciones = $this->separacion->deleteObservaciones($valor);
                        if($deleteObservaciones == true)
                        {
                            $resultado[$i]['id_documento'] = $valor;
                            $resultado[$i]['deleteObservaciones'] = true;
                            $deleteGestiones = $this->separacion->deleteGestiones($valor);
                            if($deleteGestiones == true)
                            {
                                $resultado[$i]['deleteGestiones'] = true;
                                $deleteUsuariosDoc = $this->separacion->deleteUsuariosDoc($valor);
                                if ($deleteUsuariosDoc == true)
                                {
                                    $resultado[$i]['deleteUsuariosDoc'] = true;
                                    $archivo = $this->separacion->dameArchivo($valor);
                                    if($archivo != 0)
                                    {
                                        $path = PATH_DMS_DOCS.$archivo;
                                        if (file_exists($path))
                                        {
                                            unlink($path);
                                            $resultado[$i]['archivoDelete'] = $path;
                                        }
                                    }
                                    $deleteDocumento = $this->separacion->deleteDocumento($valor);
                                    if ($deleteDocumento)
                                        $resultado[$i]['deleteDocumento'] = true;
                                    else
                                        $resultado[$i]['deleteDocumento'] = false;
                                }
                                else
                                    $resultado[$i]['deleteUsuariosDoc'] = false;
                            }
                            else
                                $resultado[$i]['deleteGestiones'] = false;
                        }
                        else 
                        {
                            $resultado[$i]['id_documento'] = $valor;
                            $resultado[$i]['deleteObservaciones'] = false;
                        }
                    }
                    $i++;
                }
                echo "<pre>".print_r($resultado,true)."</pre>";
            }
        }
        
        public function pruebaEliminar()
        {
            $path = PATH_DMS_DOCS.'prueba.pdf';
            unlink($path);
        }
        
        public function eliminarTareasBorax()
        {
            $this->load->model('separacion_model','separacion',true);
            $id_empresa = 3;
            $tareas = $this->separacion->dameTareas($id_empresa);
//            echo "<pre>".print_r($tareas,true)."</pre>";
            
            
            $resultado = array();
            $i = 0;
            if ($tareas != 0)
            {
                foreach($tareas as $array)
                {
                    foreach($array as $atrib=>$valor)
                    {
                        $deleteHistorial = $this->separacion->deleteHistorial($valor);
                        if($deleteHistorial == true)
                        {
                            $resultado[$i]['id_tarea'] = $valor;
                            $resultado[$i]['deleteHistorial'] = true;
                            $deleteHistorialAcciones = $this->separacion->deleteHistorialAcciones($valor);
                            if($deleteHistorialAcciones == true)
                            {
                                $resultado[$i]['deleteHistorialAcciones'] = true;
                                $deleteTareasRPyC = $this->separacion->deleteTareasRPyC($valor);
                                if ($deleteTareasRPyC == true)
                                {
                                    $resultado[$i]['deleteTareasRPyC'] = true;
                                    $deleteCierres = $this->separacion->deleteCierres($valor);
                                    if ($deleteCierres == true)
                                    {
                                        $resultado[$i]['deleteCierres'] = true;
                                        $archivos = $this->separacion->dameArchivosTareas($valor);
                                        if($archivos != 0)
                                        {
                                            $j = 0;
                                            $pathFolderCierre = PATH_TAREAS_FILES.$valor.'/cierre/';
                                            $pathFolder = PATH_TAREAS_FILES.$valor;
                                            foreach ($archivos as $linea)
                                            {
                                                foreach ($linea as $name=>$value)
                                                {
                                                    $path = PATH_TAREAS_FILES.$valor.'/cierre/'.$value;
                                                    if (file_exists($path))
                                                    {
                                                        unlink($path);
                                                        $resultado[$i][$j]['archivoDelete'] = $path;
                                                    }
                                                }
                                                $j++;
                                            }
                                            $resultado[$i]['folderDelete'] = $pathFolder;
                                            rmdir($pathFolderCierre);
                                            rmdir($pathFolder);
                                        }
                                        $deleteArchivos = $this->separacion->deleteArchivos($valor);
                                        if ($deleteArchivos == true)
                                        {
                                            $resultado[$i]['deleteArchivos'] = true;
                                            $deleteTarea = $this->separacion->deleteTarea($valor);
                                            if($deleteTarea == true)
                                                $resultado[$i]['deleteTarea'] = true;
                                            else
                                                $resultado[$i]['deleteTarea'] = false;
                                        }
                                        else
                                            $resultado[$i]['deleteArchivos'] = false;
                                    }
                                    else
                                        $resultado[$i]['deleteCierres'] = false;
                                }
                                else
                                    $resultado[$i]['deleteTareasRPyC'] = false;
                            }
                            else
                                $resultado[$i]['deleteHistorialAcciones'] = false;
                        }
                        else
                        {
                            $resultado[$i]['id_tarea'] = $valor;
                            $resultado[$i]['deleteHistorial'] = false;
                        }
                    }
                    $i++;
                }
                echo "<pre>".print_r($resultado,true)."</pre>";
            }
        }
}
?>