<?php
class Publicados extends CI_Controller
{
    private $modulo=16;
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
            
            $this->load->model('usuarios_model','usuarios',true);
            $usuario=$this->usuarios->dameUsuarioEmpresaYGR($this->user['id']);
             
            $this->load->model("dms/dms_permisos_model","dms_permisos",true);
            $usuario['permiso_dms']=$this->dms_permisos->checkIn($this->user['id']);
            $this->load->model("gestion_riesgo/gr_usuarios_model","gr_usuarios",true);
            $permiso_gdr = $this->gr_usuarios->dameUsuarioPermisoGdr($this->user['id']);
            
            //verifico que el usuario sea el editor o pertenezca a GR
            $variables['permiso_gr'] = ($permiso_gdr != 0)?1:0;
            $variables['permiso_btn_col_acc']=($variables['permiso_gr']==1 || $usuario['permiso_dms']['Editor']==1)?1:0;
            $variables['permiso_btn_row_acc'] =  $this->user['id'];
//            $variables['permiso_gr'] = ($usuario['gr']==1)?1:0;
//            echo "<pre>".print_r($variables,true)."</pre>";
            $this->load->view('dms/publicados/listado',$variables);
	}
	
	public function listado()
	{
		if ($this->permisos['Listar'])
                {
                    $id_usuario=$this->user['id'];
                    $this->load->model('usuarios_model','usuarios',true);
                    $usuario=$this->usuarios->dameUsuarioEmpresaYGR($id_usuario); 
//                    echo "<pre>".print_r($usuario,true)."</pre>";die(); 
                    
                    $this->load->model('dms/documentos_model','documentos',true);
                    $start = $this->input->post("start");
                    $limit = ($this->input->post("limit")&&$this->input->post("limit")<=TAM_PAGINA)?$this->input->post("limit"):TAM_PAGINA;
                    $sort = $this->input->post("sort");
                    $dir = $this->input->post("dir");
                    $filtros['id_td'] = ($this->input->post("filtro_id_td")&&$this->input->post("filtro_id_td")!=-1&&$this->input->post("filtro_id_td")!='Todos')?$this->input->post("filtro_id_td"):"";
                    $filtros['id_gcia'] = ($this->input->post("filtro_id_gcia")&&$this->input->post("filtro_id_gcia")!=-1&&$this->input->post("filtro_id_gcia")!='Todas')?$this->input->post("filtro_id_gcia"):"";
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
                    $listado = $this->documentos->publicados($start, $limit, $filtros,$busqueda, $campos, $sort, $dir,$usuario);
                    echo $listado;
                }
                else
                    echo -1; //No tiene permisos
                
	}
	
//	public function modificar()
//	{
//            if ($this->permisos['Modificacion'])
//            {
//                    $campo=json_decode($_POST["campo"]);
//                    $valor=json_decode($_POST["valor"]);
//                    $this->load->model('dms/documentos_model','documentos',true);
////                    if($campo[0]=="gerencia")
////                        $datos[$campo[0]]=$valor[0];
////                    if($campo[0]=="abv")
////                        $datos[$campo[0]]=strtoupper($valor[0]);
////                    if($campo[0]=="empresa")
////                    {
////                        $campo[0]='id_empresa';
////                        $datos[$campo[0]]=$valor[0];
////                    }
//                            
//                    if(!$this->documentos->edit($_POST["id"],$datos))
//                        echo 0;
//                    else
//                        echo 1;
//            }
//	}
	
//	public function insert()
//	{
//            if ($this->permisos['Alta'])		
//            {
//                $datos=array();
//                $datos['documento']      =$_POST["documento"];
//                $datos['detalle']        =  strtoupper($_POST["detalle"]);
//                $datos['version']          =$_POST["id_td"];
//                $datos['archivo']        =$_POST["archivo"];
//                $this->load->model('dms/documentos_model','documentos',true);
//                //antes de insertar verifico que no exita el registro
//                if($this->documentos->insert($datos))
//                    echo 1; //todo OK
//                else
//                    echo 2; //Error al insertar el registro
//            }
//	}
        public function combo_td()
	{
            $id_usuario=$this->user['id'];
            $this->load->model('usuarios_model','usuarios',true);
            $usuario=$this->usuarios->dameUsuarioEmpresaYGR($id_usuario);
            $this->load->model('dms/documentos_model','documentos',true);
            $var=$this->documentos->dameComboTDPublicados($usuario);
            echo $var;	
	}
        public function combo_gerencias()
	{
//            $id_usuario=$this->user['id'];
//            $this->load->model('usuarios_model','usuarios',true);
//            $usuario=$this->usuarios->dameUsuarioEmpresaYGR($id_usuario);//-->quito para que todos puedan ver todos los documentos
            $this->load->model('dms/documentos_model','documentos',true);
            $var=$this->documentos->dameComboGerenciasPublicados();
            echo $var;	
	}
        function _checkuser($id_documento, $id_usuario){
            
            //Controla que el usuario peretenezca al alcance del documento
            $id_usuario=$this->user['id'];
            $this->load->model('dms/documentos_model','documentos',true);
            $documento=$this->documentos->dameDocumento($id_documento);
//             echo "<pre>".print_r($documento,true)."</pre>";
            
            $this->load->model('usuarios_model','usuarios',true);
            $usuario=$this->usuarios->dameUsuarioEmpresaYGR($id_usuario);
//             echo "<pre>".print_r($usuario,true)."</pre>";die();
            
            //Alcance: 0-> ORO, 1-> SDJ, 2->BRX
            //Empresa: 1-> ORO, 2-> SDJ, 3-> BRX
            if ($usuario['gr']==1 || !(($documento['alcance']==1 && $usuario['id_empresa']==3)||($documento['alcance']==2 && $usuario['id_empresa']==2)))
                return 1;
            else
                return 0;
        }
        function preview($id_documento)
        {
//            $id_usuario=$this->user['id'];//-->quito para que todos puedan ver todos los documentos
            
//            if ($this->_checkuser($id_documento,$id_usuario))
                $this->_generaDocumentoPdf($id_documento,0);
//            else{
//                $jsonData = "<p><h3>Error: Su usuario no tiene permisos sobre el documento solicitado</h3></p>"; //error de permisos, el susuario no esta como integrante habilitado del wf para este documento		
//                    echo $jsonData;
//            }
        }
        function descargar($id_documento)
        {
//            $id_usuario=$this->user['id'];//-->quito para que todos puedan ver todos los documentos
//            if ($this->_checkuser($id_documento,$id_usuario))
                $this->_generaDocumentoPdf($id_documento,1);
//            else{
//                $jsonData = "<p><h3>Error: Su usuario no tiene permisos sobre el documento solicitado</h3></p>"; //error de permisos, el susuario no esta como integrante habilitado del wf para este documento		
//                    echo $jsonData;
//            }
        }
//        function preview_cc($id_documento)
//        {
//            echo "<h2>Impresi&oacute;n con copia controlada!!!</h2>";
//        }
        function imprime($id_documento)
        {
//            $id_usuario=$this->user['id'];//-->quito para que todos puedan ver todos los documentos
//            if ($this->_checkuser($id_documento,$id_usuario))
                $this->_generaDocumentoPdf($id_documento,0,1);
//            else{
//                $jsonData = "<p><h3>Error: Su usuario no tiene permisos sobre el documento solicitado</h3></p>"; //error de permisos, el susuario no esta como integrante habilitado del wf para este documento		
//                    echo $jsonData;
//            }
        }
        function _generaDocumentoPdf($id_documento,$save=false,$print=false)
        {
//            $nro= "PDF".str_repeat('0',7-strlen($id_documento)).$id_documento."_";
            
            $this->load->model("dms/documentos_model","documentos",true);
            $documento=$this->documentos->dameDocumento($id_documento);
            
//            $this->load->model("empresas_model","empresas",true);
//            $logo=$this->empresas->damelogo($documento['id_empresa_origen']);
            $logo=PATH_DMS_IMAGES.'logo_'.((int)$documento['alcance']+1).'.jpg';
//            $variables['logo']=(int)$documento['alcance'];
            
            $this->load->model("dms/gestiones_model","gestiones",true);
            $editor=$this->gestiones->DameEditor($id_documento);
            $aprobador=$this->gestiones->DameAprobador($id_documento);
            if ($documento['tipo_wf']==1) //tipo_wf==1 => WF Corto
                $revisores=$aprobador;
            else
                $revisores=$this->gestiones->DameRevisores($id_documento);
             $this->load->library('Pdf_wf');
            $pdf = new Pdf_wf('P', 'mm', 'A4', true, 'UTF-8', false);
//            $archivo=PATH_DMS_DOCS.$nro.md5(KEYMD5."$id_documento").".pdf";
            $archivo=PATH_DMS_DOCS.$this->_nombreArchivo($id_documento,'PDF');
//            $ArchivoDMS=  str_replace('.pdf', '_dms.pdf', $archivo);
            $ArchivoDMS=  $documento['codigo'].".pdf";
            $pageCount = $pdf->setSourceFile($archivo);
            //Open the pdf as a text file and count the number of times "/Page" occurs.
//            function count_pages($pdfname) {
//                $pdftext = file_get_contents($pdfname);
//                $num = preg_match_all("/\/Page\W/", $pdftext, $dummy);
//                return $num;
//            }
//            echo count_pages($archivo);
            
            $variables['pdf']=$pdf;
             $variables['print']=$print;
             $variables['nomArchivoDMS']=$ArchivoDMS;
            /*
            * I: send the file inline to the browser (default). The plug-in is used if available. The name given by name is used when one selects the "Save as" option on the link generating the PDF.
            * D: send to the browser and force a file download with the name given by name.
            * F: save to a local server file with the name given by name.
            * S: return the document as a string (name is ignored).
            * FI: equivalent to F + I option
            * FD: equivalent to F + D option
            * E: return the document as base64 mime multi-part email attachment (RFC 2045)
            */
             
            $variables['output']=($save)?'D':'I';
//            $variables['logo']=URL_BASE.'images/'.$logo;
            $variables['logo']=$logo;
            $variables['marcaAgua']=PATH_DMS_IMAGES.'ma1.png';
            $variables['titulo']=$documento['documento'];
            $variables['codigo']=$documento['codigo'];
            $variables['vigencia']=($documento['fecha_public']==null)?"":$documento['fecha_public'];
            $variables['paginas']=$pageCount;
            $variables['barcode']=1000000+$documento['id_documento'];
            $variables['estado']=$documento['id_estado'];
            $variables['edito']=$editor;
            $variables['reviso']=$revisores;
            $variables['aprobo']=$aprobador;
            
             $this->load->view('dms/pdf/documento',$variables);
        }
        function _nombreArchivo($id_documento,$tipo)
        {
            $qceros=7;
            $t='PDF';
            $ext='.pdf';
            $nro= str_repeat('0',$qceros-strlen($id_documento)).$id_documento;
            $nombre=$nro.$t.md5(KEYMD5."$id_documento").$ext;
            return $nombre;
        }
        function obsoleto()
        {
            
            $id_usuario=$this->user['id'];
            $id_documento = $this->input->post("id");
            if ($id_documento)
            {
                //verifico que el usuario sea el editor o pertenezca a GR
                $this->load->model('dms/documentos_model','documentos',true);
                $documento=$this->documentos->dameDocumento($id_documento);
    //             echo "<pre>".print_r($documento,true)."</pre>";
            
                $this->load->model('usuarios_model','usuarios',true);
                $usuario=$this->usuarios->dameUsuarioEmpresaYGR($id_usuario);
//                echo "<pre>".print_r($usuario,true)."</pre>";die();
                
                if($usuario['gr']==1 || $documento['id_usuario_editor']==$id_usuario)
                {
                    $obsoleto=$this->hacerObsoleto($id_documento);
                    if ($obsoleto==1)
                        echo "({success: true, error :0})";
                    elseif ($obsoleto==0)
                        echo "({success: false, error :'Error al guardar información, por favor comunique este error a sistemas...'})"; //error de permiso,
                    else
                        echo  "({success: false, error :'Su usuario no tiene el rol correspondiente para la gestión que desea realizar'})"; //error de permiso,
                }
                else
                    echo  "({success: false, error :'Su usuario no tiene el rol correspondiente para la gestión que desea realizar'})"; //error de permiso,
                    
            }
            else 
                echo  "({success: false, error :'No se ha podido identificar el documento, por favor comunique este error a sistemas...'})"; //error para recibir id_documento,
        }
        function hacerObsoleto($id_documento)
        {
            
            $id_usuario=$this->user['id'];
            $this->load->model("dms/documentos_model","documentos",true);
            $documento=$this->documentos->dameDocumento($id_documento);
//                echo "<pre>".print_r($documento,true)."</pre>";

            $this->load->model('usuarios_model','usuarios',true);
            $usuario=$this->usuarios->dameUsuarioEmpresaYGR($id_usuario);
//                echo "<pre>".print_r($usuario,true)."</pre>";

                //verifico que el usuario sea el editor o pertenezca a GR
                if ($usuario['gr']==1 || $documento['id_usuario_editor']==$id_usuario)
                {
                    $datos_documento['id_usuario_obsoleto']=$id_usuario;
                    $datos_documento['id_estado']=7;
                    $datos_documento['obsoleto']=1;
                    $datos_documento['fecha_obsoleto']=date("Y-m-d");
                    $datos_documento['habilitado']=0;
                    //Si se actualizo correctamente actualizo gestiones
                    if($this->documentos->update($documento['id_documento'],$datos_documento))
                    {
                        $this->load->model("usuarios_model","usuarios",true);
                        $area_usuario=$this->usuarios->dameIdArea($id_usuario);

                        $gerencia_usuario=$this->_buscarGerencia($area_usuario['id_area']);
    //                    echo "<pre>".print_r($gerencia_usuario,true)."</pre>";

                        $datos_gestion=array();
                        $datos_gestion['id_documento']=$documento['id_documento'];
                        $datos_gestion['version']=$documento['version'];
                        $datos_gestion['id_usuario']=$id_usuario;
                        $datos_gestion['id_rol']=($documento['id_usuario_editor']==$id_usuario)?1:5;//si es el editor como editor sino como rolde GR;
                        $datos_gestion['id_tg']=8;
                        $datos_gestion['ciclo_wf']=$documento['ciclos_wf'];
                        $datos_gestion['id_gerencia']=$gerencia_usuario['gcia'];
                        $datos_gestion['detalle']='Documento obsoleto';
            //            echo"<pre>".print_r($datosGestion,true)."</pre>";
                        $this->load->model("dms/gestiones_model","gestiones",true);
                        $this->gestiones->insert($datos_gestion);

                        return 1;
                    }
                    else 
                        return 0;
                }
                else
                    return -1; //sin permisos
        }
        function nuevaVersion()
       {
            
            $id_usuario=$this->user['id'];
            $id_documento = ($this->input->post("id"))?$this->input->post("id"):0;
            
            $this->load->model('dms/documentos_model','documentos',true);
            //verifico que el documento exista y que no este marcado en_nv
            $documento=$this->documentos->dameDocumentoPublicadoUObsoleto($id_documento);
//            echo "<pre>".print_r($documento,true)."</pre>";die();
            
            if ($documento!=0)
            {
                $this->load->model("gestion_riesgo/gr_areas_model","areas",true);
                if($documento['en_nv']==0)
                {
                    if($documento['transferido']==0)
                    {
                        $this->load->model('usuarios_model','usuarios',true);
                        $usuario=$this->usuarios->dameUsuarioEmpresaYGR($id_usuario);
        //                echo "<pre>".print_r($usuario,true)."</pre>";die();

                        //verifico que el usuario sea el editor o pertenezca a GR
                        if($usuario['gr']==1 || $documento['id_usuario_editor']==$id_usuario)
                        {

                            //Creo un documento nuevo y marco el actual en estado de nueva version (en_nv)
                            $dato['en_nv']=1;
                            $dato['fecha_nv']=date("Y-m-d");
                            $marca=$this->documentos->update($id_documento,$dato);
                            if ($marca)
                            {
                                //creo nuevo documento a partir del anterior
                                $nuevo=$this->crearNuevaVersion($id_documento);

                                if ($nuevo!=0)
                                {
                                     //agrego en dms_gestiones
                                    $area_usuario=$this->usuarios->dameIdArea($id_usuario);
                        //            echo "<pre>".print_r($area_editor,true)."</pre>";

                                    $gerencia_usuario=$this->_buscarGerencia($area_usuario['id_area']);
                        //            echo "<pre>".print_r($gerencia_editor,true)."</pre>";

                                    $datosGestion=array();
                                    $datosGestion['id_documento']=$id_documento;
                                    $datosGestion['id_usuario']=$id_usuario;
                                    $datosGestion['id_rol']=($documento['id_usuario_editor']==$id_usuario)?1:5;//si es el editor como editor sino como rolde GR
                                    $datosGestion['id_tg']=9; //id_tg=9=>inicio nueva version
                                    $datosGestion['ciclo_wf']=$documento['ciclos_wf'];
                                    $datosGestion['id_gerencia']=$gerencia_usuario['gcia'];
                        //            echo"<pre>".print_r($datosGestion,true)."</pre>";
                                    $this->load->model("dms/gestiones_model","gestiones",true);
                                    $this->gestiones->insert($datosGestion);

                                    echo "({success: true, error :0, id:".$nuevo."})";
                                }

                                else
                                    echo  "({success: false, error :'Su usuario no tiene el rol correspondiente para la gesti&oacute;n que desea realizar'})"; //error de permiso,

                            }
                            else
                                echo  "({success: false, error :'Error inesperado por favor comunique al departamento de IT'})"; //error al actualizar nv,
                        }
                        else
                            echo  "({success: false, error :'Su usuario no tiene el rol correspondiente para la gesti&oacute;n que desea realizar'})"; //error de permiso,
                    }
                    else
                        echo  "({success: false, error :'Documento ya transferido previamente y en gestión.'})";
                }
                else 
                {
                    $gerencia_origen = $this->areas->dameGerencia($documento['id_gerencia_origen']);
                    echo  "({success: false, error :'Documento con trámite en gestión por <b>" . $gerencia_origen['area'] . "</b>. Para transferir debe antes resolver la bandeja de trabajo.'})";
                }
            }
            else 
                echo  "({success: false, error :'No se ha podido identificar el documento, por favor comunique este error a sistemas...'})"; //error para recibir id_documento,
        }
        function crearNuevaVersion($id_documento)
        {
            
            $id_usuario=$this->user['id'];
            $this->load->model("dms/documentos_model","documentos",true);
            $documento=$this->documentos->dameDocumentoPublicado($id_documento);
//                echo "<pre>".print_r($documento,true)."</pre>";die();

            $this->load->model('usuarios_model','usuarios',true);
            $usuario=$this->usuarios->dameUsuarioEmpresaYGR($id_usuario);
//                echo "<pre>".print_r($usuario,true)."</pre>";die();

                //verifico que el usuario sea el editor o pertenezca a GR
                if ($usuario['gr']==1 || $documento['id_usuario_editor']==$id_usuario)
                {
                    $datos_documento['id_td']=$documento['id_td'];
                    $datos_documento['id_estado']=1;
                    $datos_documento['id_usuario_editor']=$documento['id_usuario_editor'];
                    $datos_documento['id_usuario_aprobador']=$documento['id_usuario_aprobador'];
                    $datos_documento['id_empresa_origen']=$documento['id_empresa_origen'];
                    $datos_documento['id_gerencia_origen']=$documento['id_gerencia_origen'];
                    $datos_documento['alcance']=$documento['alcance'];
                    $datos_documento['tipo_wf']=$documento['tipo_wf'];
                    $datos_documento['documento']=$documento['documento'];
                    $datos_documento['version']=$documento['version']+1;
                    $datos_documento['codigo']=  substr($documento['codigo'], 0,strlen($documento['codigo'])-2).str_repeat("0",2-strlen($datos_documento['version'])).$datos_documento['version'];
                    $datos_documento['detalle']=$documento['detalle'];
                    $datos_documento['numero']=$documento['numero'];
                    $datos_documento['padre_id']=$documento['id_documento'];
                    $datos_documento['q_revisores']=$documento['q_revisores'];
                    $datos_documento['en_wf']=1;
                    $datos_documento['habilitado']=1;
//                     echo "<pre>".print_r($datos_documento,true)."</pre>";
                    //Si se actualizo correctamente actualizo gestiones
                    $id_doc_nuevo=$this->documentos->insert($datos_documento);
//                    $id_doc_nuevo=1;
                     if($id_doc_nuevo)
                    {
                         //si el doc se inserto OK continuo con la tabla usuario_doc
                         
                         $this->load->model('dms/usuarios_doc_model','usuarios_doc',true);
                         $usuarios_doc=$this->usuarios_doc->dameUsuariosDoc($id_documento);
//                          echo "<pre>".print_r($usuarios_doc,true)."</pre>";
                         
                         foreach ($usuarios_doc as $user)
                         {
                             $datos_usuario=array();
                             $datos_usuario['id_usuario']=$user['id_usuario'];
                             $datos_usuario['id_documento']=$id_doc_nuevo;
                             $datos_usuario['id_rol']=$user['id_rol'];
                             $datos_usuario['id_padre']=$user['id_ud'];
                             $datos_usuario['activo']=($user['id_rol']==2 || $user['id_rol']==3)?1:0;
                             $datos_usuario['habilitado']=1;
//                             echo "<pre>".print_r($datos_usuario,true)."</pre>";
                             $this->usuarios_doc->insert($datos_usuario);
                         }
                        $this->load->model("usuarios_model","usuarios",true);
                        $area_usuario=$this->usuarios->dameIdArea($id_usuario);

                        $gerencia_usuario=$this->_buscarGerencia($area_usuario['id_area']);
    //                    echo "<pre>".print_r($gerencia_usuario,true)."</pre>";

                        $datos_gestion=array();
                        $datos_gestion['id_documento']=$id_doc_nuevo;
                        $datos_gestion['version']=$datos_documento['version'];
                        $datos_gestion['id_usuario']=$id_usuario;
                        $datos_gestion['id_rol']=($documento['id_usuario_editor']==$id_usuario)?1:5;//si es el editor como editor sino como rolde GR;
                        $datos_gestion['id_tg']=9;
                        $datos_gestion['ciclo_wf']=1;
                        $datos_gestion['id_gerencia']=$gerencia_usuario['gcia'];
            //            echo"<pre>".print_r($datosGestion,true)."</pre>";
                        $this->load->model("dms/gestiones_model","gestiones",true);
                        $this->gestiones->insert($datos_gestion);

                        return $id_doc_nuevo;
                    }
                    else 
                        return 0;
                }
                else
                    return -1; //sin permisos
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
                if($area['gcia']==0)                    
                    return $this->_buscarGerencia($area['id_area_padre']);
                else
                    
                    return $area;
////                
            }
            else 
                return 0;
        }
        
            public function excel()
    {
        if ($this->permisos['Listar'])
        {
            $this->load->library('excel');
            $this->load->model('gestion_riesgo/gr_rmc_model','gr_rmc',true);
            $start = 0;
            $limit = 1000;
            $sort = $this->input->post("sort");
            $dir = $this->input->post("dir");
            $campos = "";
            if ($this->input->post("fields"))
            {
                $campos = str_replace('"', "", $this->input->post("fields"));
                $campos = str_replace('[', "", $campos);
                $campos = str_replace(']', "", $campos);
                $campos = explode(",",$campos);
            }
            $busqueda  = ($this->input->post("query"))?$this->input->post("query"):"";
//            echo $busqueda;
//            echo "<pre>".print_r($campos,true)."</pre>";
            if($this->input->post("filtros"))
            {
                $filtros=json_decode($this->input->post("filtros"));
                if ($filtros[0]=='Todas'||$filtros[0]=='Todos'||$filtros[0]=='-1')
                    $arrayFiltros['id_clasificacion'] = 0;
                else
                    $arrayFiltros['id_clasificacion'] = $filtros[0];

                if($filtros[1]=="" || $filtros[1] == null)
                    $arrayFiltros["f_desde"] = "";
                else
                    $arrayFiltros["f_desde"] = $filtros[1];

                if($filtros[2]=="" || $filtros[2] == null)
                    $arrayFiltros["f_hasta"] = "";
                else
                    $arrayFiltros["f_hasta"] = $filtros[2];
                
                if($filtros[3]=="Todos" || $filtros[3] == null)
                            $arrayFiltros["id_estado_inv"] = "";
                        else
                            $arrayFiltros["id_estado_inv"] = $filtros[3];
                        
                if($filtros[4]=="Todos" || $filtros[4] == null)
                        $arrayFiltros["id_criticidad"] = "";
                    else
                        $arrayFiltros["id_criticidad"] = $filtros[4];
//                        echo "<pre>".print_r($arrayFiltros,true)."</pre>";
            }
            else
            {
                $arrayFiltros="";

            }
                                                
            $datos = $this->gr_rmc->listado_excel($start, $limit,$arrayFiltros,$busqueda,$campos,$sort,$dir); 
            $datos_audit['id_modulo']=$this->modulo;
            $datos_audit['id_usuario']=$this->user['id'];
            $datos_audit['q_registros']=count($datos);
            $params=array($start, $limit,$arrayFiltros,$busqueda,$campos,$sort,$dir);
            $datos_audit['params']=json_encode($params);

            $this->load->model("auditoria_model","auditoria_model",true);
            $this->auditoria_model->guardarPedidoExcel($datos_audit);
            
            $titulo = 'RI';
            $cabecera = array('#','Fecha Alta','Usuario Alta','Descripción','Sector','Criticidad','Fecha Criticidad','Clasificación','Vencimiento Investigación','Estado','1er Investigador','2do Investigador','Tareas');
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
    
    public function listado_excel()
    {
        if ($this->permisos['Listar'])
        {
            $id_usuario=$this->user['id'];
            $this->load->model('usuarios_model','usuarios',true);
            $usuario=$this->usuarios->dameUsuarioEmpresaYGR($id_usuario); 

            $this->load->model('dms/documentos_model','documentos',true);
            $start = 0;
            $limit = 1000;
            $sort = $this->input->post("sort");
            $dir = $this->input->post("dir");
            $filtros['id_td'] = ($this->input->post("filtro_id_td")&&$this->input->post("filtro_id_td")!=-1&&$this->input->post("filtro_id_td")!='Todos')?$this->input->post("filtro_id_td"):"";
            $filtros['id_gcia'] = ($this->input->post("filtro_id_gcia")&&$this->input->post("filtro_id_gcia")!=-1&&$this->input->post("filtro_id_gcia")!='Todas')?$this->input->post("filtro_id_gcia"):"";
//            echo 'lalala'.$filtros['id_td'];
//            echo $filtros['id_gcia'];die();
            
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
            $listado = $this->documentos->publicados_excel($start, $limit, $filtros,$busqueda, $campos, $sort, $dir,$usuario);
//            echo "<pre>".print_r($listado,true)."</pre>";
//            echo $listado;
            $this->_generar_excel($listado,"Documentos Publicados");
        }
        else
            echo -1; //No tiene permisos

    }
        
    
        
    public function _generar_excel($data,$titulo="")
    {
        $this->load->library('excel');
        $filename = ($titulo != "")?'Listado - '.$titulo.'.xls':'Listado.xls';
        header('Content-Type:application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name          
        header('Cache-Control: max-age=0'); //no cache
        
        $this->excel->getProperties()
            ->setCreator("Sistema de Mejroa Continua") // Nombre del autor
            ->setLastModifiedBy($this->user['nombre']."-".$this->user['apellido']) //Ultimo usuario que lo modificó
            ->setTitle(($titulo != "")?'Listado - '.$titulo.'.xls':'Listado.xls') // Titulo
            ->setSubject('Listado') //Asunto
            ->setDescription('Listado desde grilla - v1.0 - 20180716 - '.date("YmdHis")) //Descripción
            ->setKeywords('') //Etiquetas
            ->setCategory('');
        
        $getActiveSheet=$this->excel->getActiveSheet();
        //Datos
        $titulos=array();
        $n_columnas=0;
        foreach ($data[0] as $key=>$value)
        {
            $titulos[]=$key;
            $size_col= max(strlen($key)+5,strlen($value)+5,10);
            $getActiveSheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($n_columnas))->setWidth($size_col);
            $n_columnas++;
        }
        array_unshift($data,$titulos);
//        echo "<pre>data".print_r($size_cols,true)."</pre>";die();
        $colString = PHPExcel_Cell::stringFromColumnIndex($n_columnas);
        
        //Estilos
        $styleArray = array(
                'font' => array(
                    'bold' => true,
                    'color' => array('rgb' => '#000000')
                    ),
                'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,),
                //'borders' => array('top' => array('style' => PHPExcel_Style_Border::BORDER_THIN,),),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
                    'rotation' => 90,
                    'startcolor' => array(
                        'argb' => '#E6E6E6',),
                    'endcolor' => array(
                        'argb' => '#E6E6E6',)
                    ,)
            ,);
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
        
        $getActiveSheet->getStyle('A3:'.$colString.'3')->applyFromArray($styleArray);
        
//        $getActiveSheet->getColumnDimension('C')->setAutoSize(true);
        
        $getActiveSheet->fromArray($data, null, 'A3');
        
        $getActiveSheet->mergeCells('A1:'.$colString.'1');
        $getActiveSheet->setCellValue('A1','Listado - '.$titulo);
        $getActiveSheet->getStyle('A1')->applyFromArray($estiloTitulo);
        //save it to Excel5 format (excel 2003 .XLS file), chan ge this to 'Excel2007' (and adjust the filename extension, also the header mime type)
        //if you want to save it as .XLSX Excel 2007 format
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
        ob_end_clean();
        //force user to download the Excel file without writing it to server's HD
        $objWriter->save('php://output');
    }
    
    public function combo_gerencias_transferir()
    {
        $id_gerencia_not    = $this->input->post("id_g");
        $query              = $this->input->post("query");
        $id_usuario=$this->user['id'];

        $this->load->model('gestion_riesgo/gr_areas_model','gr_areas',true);
        $jcode=$this->gr_areas->combo_gerencias_transferir_doc($id_gerencia_not, $query);

        echo $jcode;
    }
    public function combo_editores_transferencia()
    {
        $id_gerencia    = $this->input->post("id_g");
        $id_documento   = $this->input->post("id");
        $query          = $this->input->post("query");

        $this->load->model('dms/documentos_model','documentos',true);
        $documento=$this->documentos->dameDocumento($id_documento);
        
        $this->load->model('dms/workflows_model','workflows',true);
        $jcode = $this->workflows->dameComboEditoresPorGerencia($id_gerencia,'','',$query,$documento['id_usuario_aprobador']);

        echo $jcode;
    }
    
    public function transferir_documento()
    {
        $id_usuario     =$this->user['id'];
        $this->load->model('usuarios_model','usuarios',true);
        $usuario=$this->usuarios->dameUsuarioEmpresaYGR($id_usuario);
        if($usuario['gr']==1)
        {
            $id_documento   = ($this->input->post("id_documento"))?$this->input->post("id_documento"):0;
            $id_gcia        = ($this->input->post("id_gerencia"))?$this->input->post("id_gerencia"):0;
            $id_editor      = ($this->input->post("id_editor"))?$this->input->post("id_editor"):0;

            if( $id_documento!=0 && $id_gcia!=0 && $id_editor!=0)
            {
                $this->load->model('dms/documentos_model','documentos',true);
                //verifico que el documento exista y que no este marcado en_nv
                $documento=$this->documentos->dameDocumentoPublicadoUObsoleto($id_documento);
                if ($documento!=0)
                {
                    $this->load->model("gestion_riesgo/gr_areas_model","areas",true);
                    if($documento['en_nv']== 0)
                    {
                        if($documento['transferido']== 0)
                        {
                            //marco el actual en estado de nueva version (en_nv) y transferido
                            $dato['fecha_nv']=date("Y-m-d");
                            $dato['transferido']=1;
                            $dato['id_usuario_transfirio']=$id_usuario;
                            $marca=$this->documentos->update($id_documento,$dato);
                            if ($marca)
                            {
                                $transferido=$this->_crearNuevaVersionTrasferida($id_documento,$id_gcia, $id_editor);
                                if ($transferido!=0)
                                {
                                    $documento_nuevo=$this->documentos->dameDocumento($transferido);
                                    $gerencia_nueva=$this->areas->dameGerencia($documento['id_gerencia_origen']);
                                    //agrego en dms_gestiones
                                    $area_usuario=$this->usuarios->dameIdArea($id_usuario);
                        //            echo "<pre>".print_r($area_editor,true)."</pre>";

                                    $gerencia_usuario=$this->_buscarGerencia($area_usuario['id_area']);
                        //            echo "<pre>".print_r($gerencia_editor,true)."</pre>";
                                    $datosGestion=array();
                                    $datosGestion['id_documento']=$id_documento;
                                    $datosGestion['id_usuario']=$id_usuario;
                                    $datosGestion['id_rol']=($documento['id_usuario_editor']==$id_usuario)?1:5;//si es el editor como editor sino como rolde GR
                                    $datosGestion['id_tg']=10; //id_tg=10=>inicio nueva version Transferido
                                    $datosGestion['ciclo_wf']=$documento['ciclos_wf'];
                                    $datosGestion['id_gerencia']=$gerencia_usuario['gcia'];
                                    $datos_gestion['detalle']= 'Documento transferido a '.$gerencia_nueva['area'].' con código: '.$documento_nuevo['codigo'];
                        //            echo"<pre>".print_r($datosGestion,true)."</pre>";
                                    $this->load->model("dms/gestiones_model","gestiones",true);
                                    $this->gestiones->insert($datosGestion);
                                    echo "({success: true, error :0, id:".$transferido."})";
                                }
                                else
                                    echo  "({success: false, error :'Error al transferir el documento'})"; 
                            }
                             else
                                echo  "({success: false, error :'Error inesperado por favor comunique al departamento de IT'})"; //error al actualizar nv,
                        }
                        else
                            echo  "({success: false, error :'Documento ya transferido previamente y en gestión.'})";
                    }
                    else 
                    {
                        $gerencia_origen = $this->areas->dameGerencia($documento['id_gerencia_origen']);
                        echo  "({success: false, error :'Documento con trámite en gestión por <b>" . $gerencia_origen['area'] . "</b>. Para transferir debe antes resolver la bandeja de trabajo.'})";
                    }
                }
                else 
                    echo  "({success: false, error :'No se ha podido identificar el documento, por favor comunique este error a sistemas...'})"; //error para recibir id_documento,
            }
            else
                echo "({success: false, error :'Faltan completar campos requeridos.'})";; // faltan datos requeridos
        }
        else
            echo "({success: false, error :'No tiene los permisos necesarios para realizar esta acción.'})";; //No tiene permisos
    }
    
    function _crearNuevaVersionTrasferida($id_documento,$id_gcia, $id_editor)
    {

        $id_usuario=$this->user['id'];
        $this->load->model("dms/documentos_model","documentos",true);
        $documento=$this->documentos->dameDocumentoPublicado($id_documento);
//                echo "<pre>".print_r($documento,true)."</pre>";die();

        $this->load->model('usuarios_model','usuarios',true);
        $usuario=$this->usuarios->dameUsuarioEmpresaYGR($id_usuario);
//                echo "<pre>".print_r($usuario,true)."</pre>";die();

            //verifico que el usuario pertenezca a GR
            if ($usuario['gr']==1)
            {
                $datos_documento['id_td']=$documento['id_td'];
                $datos_documento['id_estado']=1;
                $datos_documento['id_usuario_editor']=$id_editor;
                $datos_documento['id_usuario_aprobador']=$documento['id_usuario_aprobador'];
                $datos_documento['id_empresa_origen']=$documento['id_empresa_origen'];
                $datos_documento['id_gerencia_origen']=$id_gcia;
                $datos_documento['alcance']=$documento['alcance'];
                $datos_documento['tipo_wf']=$documento['tipo_wf'];
                $datos_documento['documento']=$documento['documento'];
//                    $datos_documento['version']=$documento['version']+1;
                $datos_documento['version']=0;
                $datos_documento['detalle']=$documento['detalle'];
                $datos_documento['padre_id']=$documento['id_documento'];
                $datos_documento['q_revisores']=$documento['q_revisores'];
                $datos_documento['en_wf']=1;
                $datos_documento['habilitado']=1;

                $this->load->model("gestion_riesgo/gr_areas_model","areas",true);
                $nueva_gcia=$this->areas->dameGerencia($id_gcia);


                //buscar el último número para el mismo origen
                $this->load->model("dms/documentos_model","documentos",true);
                $qDocs=$this->documentos->maxNroPorOrigen($id_gcia);
                if ($qDocs && $qDocs!=NULL)
                        $datos_documento['numero']=$qDocs+1;
                    else
                        $datos_documento['numero']=1;
                //Genero Código de documento
                $this->load->model("dms/tiposdoc_model","tiposdoc",true);
                $tdAbv=$this->tiposdoc->dameTdAbv($datos_documento['id_td']);   
                $numero= str_repeat("0",4-strlen($datos_documento['numero'])).$datos_documento['numero'];
                $version= "00";
                $codigo=$tdAbv."-".$nueva_gcia['abv']."-".'SDJ'."-".$numero."-".$version;
                $datos_documento['codigo'] =$codigo;



//                     echo "<pre>".print_r($datos_documento,true)."</pre>";
                //Si se actualizo correctamente actualizo gestiones
                $id_doc_nuevo=$this->documentos->insert($datos_documento);
//                    $id_doc_nuevo=1;
                 if($id_doc_nuevo)
                {
                     //si el doc se inserto OK continuo con la tabla usuario_doc

                     $this->load->model('dms/usuarios_doc_model','usuarios_doc',true);
                     $usuarios_doc=$this->usuarios_doc->dameUsuariosDoc($id_documento);
//                          echo "<pre>".print_r($usuarios_doc,true)."</pre>";

                     foreach ($usuarios_doc as $user)
                     {
                         $datos_usuario=array();
                         $datos_usuario['id_usuario']=($user['id_rol'] == 1?$id_editor:$user['id_usuario']);
                         $datos_usuario['id_documento']=$id_doc_nuevo;
                         $datos_usuario['id_rol']=$user['id_rol'];
                         $datos_usuario['id_padre']=$user['id_ud'];
                         $datos_usuario['activo']=($user['id_rol']==2 || $user['id_rol']==3)?1:0;
                         $datos_usuario['habilitado']=1;
//                             echo "<pre>".print_r($datos_usuario,true)."</pre>";
                         $this->usuarios_doc->insert($datos_usuario);
                     }
                    $this->load->model("usuarios_model","usuarios",true);
                    $area_usuario=$this->usuarios->dameIdArea($id_usuario);

                    $gerencia_usuario=$this->_buscarGerencia($area_usuario['id_area']);
                    
                    
                    $gerencia_anterior=$this->areas->dameGerencia($documento['id_gerencia_origen']);
                    
//                    echo $documento['id_gerencia_origen'];
//                    echo "<pre>".print_r($gerencia_anterior,true)."</pre>";
                    $datos_gestion=array();
                    $datos_gestion['id_documento']=$id_doc_nuevo;
                    $datos_gestion['version']=$datos_documento['version'];
                    $datos_gestion['id_usuario']=$id_usuario;
                    $datos_gestion['id_rol']=($documento['id_usuario_editor']==$id_usuario)?1:5;//si es el editor como editor sino como rol de GR;
                    $datos_gestion['id_tg']=10;
                    $datos_gestion['ciclo_wf']=1;
                    $datos_gestion['id_gerencia']=$gerencia_usuario['gcia'];
                    $datos_gestion['detalle']= 'Documento transferido de '.$gerencia_anterior['area'].' con código: '.$documento['codigo'];
//                    echo"<pre>".print_r($datos_gestion,true)."</pre>";
                    $this->load->model("dms/gestiones_model","gestiones",true);
                    $this->gestiones->insert($datos_gestion);

                    return $id_doc_nuevo;
                }
                else 
                    return 0;
            }
            else
                return -1; //sin permisos
    }
}
?>