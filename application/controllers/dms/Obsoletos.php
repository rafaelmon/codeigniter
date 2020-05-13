<?php
class Obsoletos extends CI_Controller
{
    private $modulo=17;
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
             $this->load->model("gestion_riesgo/gr_usuarios_model","gr_usuarios",true);
            $this->user['area']=$this->gr_usuarios->dameArea( $this->user['id']);
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
            $this->load->view('dms/obsoletos/listado',$variables);
	}
	
	public function listado()
	{
		if ($this->permisos['Listar'])
                {
                    $this->load->model('dms/documentos_model','documentos',true);
                    $start = $this->input->post("start");
                    $limit = $this->input->post("limit");
                    $sort = $this->input->post("sort");
                    $dir = $this->input->post("dir");
                    $filtro = ($this->input->post("filtro_id_td")&&$this->input->post("filtro_id_td")!=-1)?$this->input->post("filtro_id_td"):"";
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
                    $listado = $this->documentos->obsoletos($start, $limit, $filtro,$busqueda, $campos, $sort, $dir);
                    echo $listado;
                }
                else
                    echo -1; //No tiene permisos
                
	}
	
        public function combo_td()
	{
            $this->load->model('dms/documentos_model','documentos',true);
            $var=$this->documentos->dameComboTDObsoletos();
            echo $var;	
	}
         function preview($id_documento)
        {
            //Controla que el usuario tenga permisos para ver el documento
            $id_usuario=$this->user['id'];
            $this->load->model("dms/usuarios_doc_model","usuarios_doc",true);
            $check=$this->usuarios_doc->checkUsuarioDoc($id_documento,$id_usuario);
            if ($check || $this->user['area']['gr']==1)
            {
                
    //            $id_documento=$this->input->post("documento");
    //            $id_documento=1;
                $this->_generaDocumentoPdf($id_documento,0);
            }
            else{
                $jsonData = "<p><h3>Error: Su usuario no tiene permisos sobre el documento solicitado</h3></p>"; //error de permisos, el susuario no esta como integrante habilitado del wf para este documento		
                    echo $jsonData;
            }
        }
        function _generaDocumentoPdf($id_documento,$save=false,$print=false)
        {
            $this->load->model("dms/documentos_model","documentos",true);
            $documento=$this->documentos->dameDocumentoObsoleto($id_documento);
//            echo "<pre>".print_r($documento,true)."</pre>";die();
            if ($documento)
            {
                $this->load->model("dms/gestiones_model","gestiones",true);
                $editor=$this->gestiones->DameEditor($id_documento);
                $aprobador=$this->gestiones->DameAprobador($id_documento);
                if ($documento['tipo_wf']==1) //tipo_wf==1 => WF Corto
                    $revisores=$aprobador;
                else
                    $revisores=$this->gestiones->DameRevisores($id_documento);

                $this->load->library('Pdf_wf');
                $pdf = new Pdf_wf('P', 'mm', 'A4', true, 'UTF-8', false);
                $archivo=PATH_DMS_DOCS.$this->_nombreArchivo($id_documento,'PDF');
                $ArchivoDMS=  str_replace('.pdf', '_dms.pdf', $archivo);
                $pageCount = $pdf->setSourceFile($archivo);

                $variables['pdf']=$pdf;

                //datos
                $variables['nomArchivoDMS']=$ArchivoDMS;
                $variables['output']=($save)?'F':'I';
                $variables['logo']=PATH_DMS_IMAGES.'logo_'.((int)$documento['alcance']+1).'.jpg';
                $variables['marcaAgua']=PATH_DMS_IMAGES.'ma1.png';
                $variables['titulo']=$documento['documento'];
                $variables['codigo']=$documento['codigo'];
                $variables['vigencia']=($documento['fecha_public']==null)?"":$documento['fecha_public'];
                $variables['estado']=$documento['id_estado'];
                $variables['paginas']=$pageCount;
                $variables['barcode']=1000000+$documento['id_documento'];
                $variables['print']=$print;
                $variables['edito']=$editor;
                $variables['reviso']=$revisores;
                $variables['aprobo']=$aprobador;
                $this->load->view('dms/pdf/documento',$variables);
            }
            else{
                $jsonData = "<p><h3>Error: Documento no encontrado...</h3></p>";		
                    echo $jsonData;
            }
        }
        function _nombreArchivo($id_documento,$tipo="")
        {
            $qceros=7;
            $t='PDF';
            $ext='.pdf';
            
            $nro= str_repeat('0',$qceros-strlen($id_documento)).$id_documento;
            $nombre=$nro.$t.md5(KEYMD5."$id_documento").$ext;
            return $nombre;
        }
}
?>