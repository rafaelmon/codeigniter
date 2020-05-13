<?php
class Mi_ed extends CI_Controller
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
            $variables['id_ed'] = $this->input->post("id");
            $this->load->model("ed/ed_evaluaciones_model","evaluaciones",true);
            $this->load->model('ed/ed_ec_model','ed_ec_model',true);
            $ed=$this->evaluaciones->dameCabeceraEd($variables['id_ed']);
            
            //verifico que la ed este en estado editable
            if($ed['id_estado']==1 || $ed['id_estado']==2)
            {
                $t_cump=$this->ed_ec_model->dameValorCumplimiento($variables['id_ed']);
                $q_comp=$this->ed_ec_model->dameCantidadCompetencias($variables['id_ed']);
            
                    $variables['nom_usuario']   = $ed['usuario'];
                    $variables['periodo']       = $ed['periodo'];
                    $variables['t_cump']        = $t_cump;
                    $variables['max_cump']      = $q_comp*100;
                    $variables['cierre_s']      = $ed['cierre_s'];
                    $this->load->view('ed/mis_ed/mi_ed/listado',$variables);
            }
            else
                $this->load->view('ed/mis_ed/listado',$variables);
	}
	
	public function listado()
	{
            if ($this->permisos['Listar'])
            {
		$id_usuario=$this->user['id'];
                $id_evaluacion=$this->input->post("id");
//                echo "<pre>".print_r($this,true)."</pre>";    
                    if ($id_evaluacion=="" || $id_evaluacion==NULL)
                        exit ('({"total":"0","rows":""})');
                    
                    $this->load->model('ed/ed_ec_model','ed_ec_model',true);
                    $start = $this->input->post("start");
                    $limit = $this->input->post("limit");
                    $sort = $this->input->post("sort");
                    $dir = $this->input->post("dir");
                    
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
                    $listado = $this->ed_ec_model->listadoEvaluacion($start, $limit, $busqueda, $campos, $sort, $dir,$id_evaluacion);
                    echo $listado;
            }
            else
                echo "({success: false, error :'No tiene permisos para realizar la acci�n solicitada'})"; //
	}
        public function chek_radio()
	{
            $valor=0;
            if ($_POST['value']=="true"){
                    $valor=1;
            }
            $campo=$_POST['campo'];
            $id=$_POST['id'];
            
            
            $b=1;
            
            $datos['u_r1']=0;
            $datos['u_r2']=0;
            $datos['u_r3']=0;
            $datos['u_r4']=0;
            if($valor==1)
            {
                switch ($campo)
                {
                    case 'u_r1':$datos['u_r1']=1;break;
                    case 'u_r2':$datos['u_r2']=1;break;
                    case 'u_r3':$datos['u_r3']=1;break;
                    case 'u_r4':$datos['u_r4']=1;break;
                }
                
            }
            
            $this->load->model('ed/ed_ec_model','ed_ec',true);
            if(!$this->ed_ec->check_respuesta($id,$datos))
            {
                    $b=0;
            }
            echo $b;
	}
	 public function preview($id_ed)
        {
            $this->_generaDocumentoPdf($id_ed);
        }
        
        function _generaDocumentoPdf($id_ed)//$id_ed,$save=false,$print=false
        {
//            $id_ed = 1;
            $save=false;
            $print=false;
//            $nro= "PDF".str_repeat('0',7-strlen($id_documento)).$id_documento."_";
            
            $this->load->model("ed/ed_evaluaciones_model","evaluaciones",true);
            $ed=$this->evaluaciones->dameEd($id_ed);
//            echo $ed;
//            echo "<pre>". print_r($ed,true)."<pre>";
//            $logo=$this->empresas->damelogo($documento['id_empresa_origen']);
//            $logo=PATH_DMS_IMAGES.'logo_'.((int)$documento['alcance']+1).'.jpg';
//            $variables['logo']=(int)$documento['alcance'];
            
            $this->load->library('Pdf_wf');
            $pdf = new Pdf_wf('P', 'mm', 'A4', true, 'UTF-8', false);
//            $archivo=PATH_DMS_DOCS.$nro.md5(KEYMD5."$id_documento").".pdf";
//            $archivo=PATH_DMS_DOCS.$this->_nombreArchivo($id_documento,'PDF');
//            $ArchivoDMS=  str_replace('.pdf', '_dms.pdf', $archivo);
            $ArchivoED=  'Evaluaci�n de Desempe�o_.pdf';//'.$ed['usuario']."
//            $pageCount = $pdf->setSourceFile($archivo);
            //Open the pdf as a text file and count the number of times "/Page" occurs.
//            function count_pages($pdfname) {
//                $pdftext = file_get_contents($pdfname);
//                $num = preg_match_all("/\/Page\W/", $pdftext, $dummy);
//                return $num;
//            }
//            echo count_pages($archivo);
            
            $variables['pdf']=$pdf;
            $variables['print']=$print;
            $variables['nomArchivoED']=$ArchivoED;
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
//            $variables['logo']=$logo;
//            $variables['marcaAgua']=PATH_DMS_IMAGES.'ma1.png';
            $variables['titulo']='Evaluaci�n de Desempe�o';//.$ed['usuario']
//            $variables['codigo']=$documento['codigo'];
//            $variables['vigencia']=($documento['fecha_public']==null)?"":$documento['fecha_public'];
//            $variables['paginas']=$pageCount;
//            $variables['barcode']=1000000+$documento['id_documento'];
        foreach ($ed as $row) 
        {
            $variables['usuario']=$row['usuario'];
            $variables['supervisor']=$row['supervisor'];
            $variables['puesto']=$row['puesto'];
            $variables['anio']=$row['anio'];
            $variables['periodo']=$row['periodo'];
            break;
        }
        $competencias = array();
        $subcompetencias = array();
        foreach ($ed as $row) 
        {
            $competencias[$row['id_competencia']]=$row['competencia'];
//            $resultado[$row['id_competencia']]['id_subcompetencia']=$value['subcompetencia'];
        }
        foreach ($ed as $row) 
        {
            $subcompetencias[$row['id_competencia']][$row['id_subcompetencia']]=$row['subcompetencia'];
        }
        foreach($ed as $linea)
        {
            $resultados[$linea['id_subcompetencia']]['u_r1']=$linea['u_r1'];
            $resultados[$linea['id_subcompetencia']]['u_r2']=$linea['u_r2'];
            $resultados[$linea['id_subcompetencia']]['u_r3']=$linea['u_r3'];
            $resultados[$linea['id_subcompetencia']]['u_r4']=$linea['u_r4'];
            $resultados[$linea['id_subcompetencia']]['s_r1']=$linea['s_r1'];
            $resultados[$linea['id_subcompetencia']]['s_r2']=$linea['s_r2'];
            $resultados[$linea['id_subcompetencia']]['s_r3']=$linea['s_r3'];
            $resultados[$linea['id_subcompetencia']]['s_r4']=$linea['s_r4'];
        }
        $variables['competencias']=$competencias;
        $variables['subcompetencias']=$subcompetencias;
        $variables['resultados']=$resultados;
//        foreach($resultados as $clave)
//        {
//            foreach($clave as $valor=>$valor1)
//            {
//                echo $valor1;
//            }
//            
//        }
//        echo "<pre>". print_r($resultados,true)."<pre>";
        
             $this->load->view('ed/pdf/documento',$variables);//,
        }
}
?>