<?php
class Top extends CI_Controller
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
            $this->load->model('ddp/Ddp_tops_model','tops',true);
	}
	
        public function miTop()
        {
            $id_usuario=$this->user['id'];
            $id_top = $this->uri->segment(SEGMENTOS_DOM+2);
            $top=$this->tops->dameTopPorId($id_top);
//            echo "<pre>".print_r($top,true)."</pre>";die();
//            if ($this->user['id'] == $datos['id_usuario'] || $this->user['id'] == $datos['id_supervisor'])
            if ($top==0 || $top==NULL)
                exit ("({success: false, error :'Top inexistente'})"); 
            else 
            {
                if ( $top['id_usuario']==$id_usuario || $top['id_supervisor']==$id_usuario || $top['id_aprobador']==$id_usuario)
                    $this->_generaDocumentoPdf($id_top);
                else
                    exit ("({success: false, error :'Su usuario no esta autorizado para descargar el reporte'})"); 
                
            }    
            
        }
        
	 function _generaDocumentoPdf($id_top,$save=false,$print=false)
        {
//            $nro= "PDF".str_repeat('0',7-strlen($id_documento)).$id_documento."_";
             $this->load->model('ddp/ddp_objetivos_model','ddp_obj',true);
            $top=$this->tops->dameTopPorIdParaPDF($id_top);
            $objetivos=$this->ddp_obj->dameObjetivosTop($id_top);
//             echo "<pre>".print_r($objetivos,true)."</pre>";die();
            $logo=PATH_DMS_IMAGES.'logo_2.jpg';
//            $variables['logo']=(int)$documento['alcance'];
            
            $this->load->library('Pdf_wf');
            $pdf = new Pdf_wf('P', 'mm', 'A4', true, 'UTF-8', false);
//            $archivo=PATH_DMS_DOCS.$nro.md5(KEYMD5."$id_documento").".pdf";
//            $ArchivoDMS=  str_replace('.pdf', '_dms.pdf', $archivo);
            $nombrePDF= "TOP_".str_replace(" ","",$top['periodo']."_".$top['usuario']).".pdf";
            $pageCount = 2;
            //Open the pdf as a text file and count the number of times "/Page" occurs.
//            function count_pages($pdfname) {
//                $pdftext = file_get_contents($pdfname);
//                $num = preg_match_all("/\/Page\W/", $pdftext, $dummy);
//                return $num;
//            }
//            echo count_pages($archivo);
            
            $variables['pdf']=$pdf;
             $variables['print']=$print;
             $variables['nomArchivoPDF']=$nombrePDF;
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
            $variables['titulo']=$nombrePDF;
           
            $variables['top']=$top;
            $variables['objs']=$objetivos;
             $this->load->view('ddp/pdf/top/documento',$variables);
        }
        
        
	
	
}
?>