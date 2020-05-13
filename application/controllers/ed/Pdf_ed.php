<?php
class Pdf_ed extends CI_Controller
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
	
	public function ver_ed($id_ed)
        {
            if($id_ed > 0)
            {
                //verifico que el usuario que solicita correponda con e usuario de la ed
                $this->load->model("usuarios_model","usuarios",true);
                $usuario_soicitante=$this->usuarios->dameUsuario($this->user['id']);
                
                $this->load->model("ed/ed_evaluaciones_model","ed_evaluaciones",true);
                $ed=$this->ed_evaluaciones->dameEdParaPdf($id_ed);
                
                
                //verifico que el usuario que solicita correponda con usuario o supervisor o tenga permisos de listar en el módulo admin evaluaciones
                if (($this->user['id'] == $ed['id_usuario']) || ($this->user['id']==$ed['id_usuario_supervisor']))
                {
                    $this->load->model("ed/ed_ec_model","ed_ec",true);
                    $this->load->model("ed/ed_pm_model","ed_pm",true);
                    $this->load->model("ed/ed_fortalezas_model","ed_fortalezas",true);
                    $this->load->model("ed/ed_aam_model","ed_aam",true);
                    $this->load->model("ed/ed_metas_model","ed_metas",true);
                    
                    $pdf_ed['cabecera']=$ed;
                    $competencias=$this->ed_ec->dameCompetenciasParaPdf($id_ed);
                    //busco los datos según que usuario es el que los solicita
                    if ($this->user['id'] == $ed['id_usuario_supervisor'] || ($this->user['id'] == $ed['id_usuario'] && $ed['cierre_s']==1)) //supervisor o usaurio con cierre por parte del supervisor
                    {
                            foreach ($competencias as &$competencia)
                            {
                                $competencia['subcompetencia']=$this->ed_ec->dameSubCompetenciasParaPdf($id_ed,$competencia['id_competencia']);
                            }
                            unset ($competencia);
                            $fortalezas=$this->ed_fortalezas->dameFortalezasPdf($id_ed);
                            $aam=$this->ed_aam->dameAamPdf($id_ed);
                            $metas=$this->ed_metas->dameMetasPdf($id_ed);
                            $comp_pm=$this->ed_pm->dameCompetenciasPlanPdf($id_ed);
                            $t_cump=$this->ed_ec->dameValorCumplimiento($id_ed);
                            $q_comp=$this->ed_ec->dameCantidadCompetencias($id_ed);
                            $cumplimiento=round($t_cump/$q_comp,2).'%';
                            if($comp_pm != 0)
                            {
                                foreach ($comp_pm as &$pm)
                                {
                                    $pm['subcompetencia']=$this->ed_pm->dameSubcompetenciasPlanPdf($id_ed,$pm['id_competencia']);
                                }
                                unset($pm);
                            }
                    }
                    else //usuario o sin cierre por parte del supervisor
                    {
                        foreach ($competencias as &$competencia)
                        {
                            $competencia['subcompetencia']=$this->ed_ec->dameSubCompetenciasParaPdf_enblanco_supervisor($id_ed,$competencia['id_competencia']);
                        }
                        $fortalezas="-.-";
                        $aam="-.-";
                        $metas="-.-";
                        $comp_pm=0;
                        $t_cump=0;
                        $q_comp=0;
                        $cumplimiento="-.-";
                    }
                    $pdf_ed['competencias']=$competencias;
                    $pdf_ed['fortalezas']=($fortalezas!=0)?$fortalezas:"-";
                    $pdf_ed['aam']=($aam!=0)?$aam:"-";
                    $pdf_ed['pm']=($comp_pm!=0)?$comp_pm:"-";
                    $pdf_ed['metas']=($metas!=0)?$metas:"-";
                    $pdf_ed['cumplimiento']=$cumplimiento;
                    $pdf_ed['firmas']=array('u'=>($ed['cierre_u']==1)?$ed['usuario']:'','s'=>($ed['cierre_s']==1)?$ed['supervisor']:'');
                    $pdf_ed['impresa_por']=$usuario_soicitante['nomape'];
//                    echo "<pre>".print_r($pdf_ed,true)."</pre>";die();
                    $this->_generaDocumentoPdf($pdf_ed);
                }
                else
                {
                    echo "<h2>No posee los permisos necesarios para abrir el documento...</h2>";
                }
            }
            else
            {
                echo "<h2>Error generando documento...</h2>";
            }
            
//            if ($this->user['id'] == $ed['id_usuario'])
//            {
//            echo "<pre>".print_r($ed,true)."</pre>";die();
                
//            }
//            else
//                 echo "({success: false, error :'No tiene permisos para realizar la acciòn solicitada'})"; //
        }
	public function ver_ed_admin($id_ed)
        {
            if($id_ed > 0)
            {
                //verifico que el usuario que solicita correponda con e usuario de la ed
                $this->load->model("usuarios_model","usuarios",true);
                $usuario_soicitante=$this->usuarios->dameUsuario($this->user['id']);
                
                $this->load->model("ed/ed_evaluaciones_model","ed_evaluaciones",true);
                $ed=$this->ed_evaluaciones->dameEdParaPdf($id_ed);
                
                $this->load->model("permisos_model","permisos_model",true);
                $permisosModuloAdmin= $this->permisos_model->checkIn($this->user['perfil_id'],39);
                
                //verifico que el usuario tenga permisos de listar en el módulo admin evaluaciones y que la evaluación este en estado confirmada
                if ($ed['id_estado']==3 && $permisosModuloAdmin['Listar'])
                {
                    $this->load->model("ed/ed_ec_model","ed_ec",true);
                    $this->load->model("ed/ed_pm_model","ed_pm",true);
                    $this->load->model("ed/ed_fortalezas_model","ed_fortalezas",true);
                    $this->load->model("ed/ed_aam_model","ed_aam",true);
                    $this->load->model("ed/ed_metas_model","ed_metas",true);
                    
                    $pdf_ed['cabecera']=$ed;
                    $competencias=$this->ed_ec->dameCompetenciasParaPdf($id_ed);
                    //busco los datos según que usuario es el que los solicita
                    foreach ($competencias as &$competencia)
                    {
                        $competencia['subcompetencia']=$this->ed_ec->dameSubCompetenciasParaPdf($id_ed,$competencia['id_competencia']);
                    }
                    unset ($competencia);
                    $fortalezas=$this->ed_fortalezas->dameFortalezasPdf($id_ed);
                    $aam=$this->ed_aam->dameAamPdf($id_ed);
                    $metas=$this->ed_metas->dameMetasPdf($id_ed);
                    $comp_pm=$this->ed_pm->dameCompetenciasPlanPdf($id_ed);
                    $t_cump=$this->ed_ec->dameValorCumplimiento($id_ed);
                    $q_comp=$this->ed_ec->dameCantidadCompetencias($id_ed);
                    $cumplimiento=round($t_cump/$q_comp,2).'%';
                    if($comp_pm != 0)
                    {
                        foreach ($comp_pm as &$pm)
                        {
                            $pm['subcompetencia']=$this->ed_pm->dameSubcompetenciasPlanPdf($id_ed,$pm['id_competencia']);
                        }
                        unset($pm);
                    }
                    $pdf_ed['competencias']=$competencias;
                    $pdf_ed['fortalezas']=($fortalezas!=0)?$fortalezas:"-";
                    $pdf_ed['aam']=($aam!=0)?$aam:"-";
                    $pdf_ed['pm']=($comp_pm!=0)?$comp_pm:"-";
                    $pdf_ed['metas']=($metas!=0)?$metas:"-";
                    $pdf_ed['cumplimiento']=$cumplimiento;
                    $pdf_ed['firmas']=array('u'=>($ed['cierre_u']==1)?$ed['usuario']:'','s'=>($ed['cierre_s']==1)?$ed['supervisor']:'');
                    $pdf_ed['impresa_por']=$usuario_soicitante['nomape'];
//                    echo "<pre>".print_r($pdf_ed,true)."</pre>";die();
                    $this->_generaDocumentoPdf($pdf_ed);
                }
                else
                {
                    echo "<h2>Error verificando permisos o estado del documento...</h2>";
                }
            }
            else
            {
                echo "<h2>Error generando documento...</h2>";
            }
            
//            if ($this->user['id'] == $ed['id_usuario'])
//            {
//            echo "<pre>".print_r($ed,true)."</pre>";die();
                
//            }
//            else
//                 echo "({success: false, error :'No tiene permisos para realizar la acciòn solicitada'})"; //
        }
	public function ver_demo()
        {
            $arrayCabecera=array(
                'id_evaluacion' => 7,
                'id_periodo' => 4,
                'usuario' => 'Usuario Demo',
                'puesto' => 'Puesto Demo',
                'area' => 'Area Demo',
                'empresa' => 'Empresa Demo',
                'supervisor' => 'Supervisor Demo',
                'puesto_supervisor' => 'Puesto Supervisor Demo',
                'area_supervisor' => 'Area Supervisor Demo',
                'empresa_supervisor' => 'Empresa Supervisor Demo',
                'comentario' =>'' ,
                'fecha_cierre_u' => '##/##/####',
                'periodo' => '201#-##',
                'anio' => 2016
            );
                $this->load->model("ed/ed_competencias_model","ed_competencias",true);
                $this->load->model("ed/ed_subcompetencias_model","ed_subcompetencias",true);
                
                $ed['cabecera']=$arrayCabecera;
                $competencias=$this->ed_competencias->dameCompetenciasParaPdf();
                $comp_pm=0;
    //            $comentarios= 
//                if($comp_pm != 0)
//                {
//                    foreach ($comp_pm as &$pm)
//                    {
//                        $pm['subcompetencia']=$this->ed_pm->dameSubcompetenciasPlanPdf($id_ed,$pm['id_competencia']);
//                    }
//                    unset($pm);
//                }
                foreach ($competencias as &$competencia)
                {
                    $competencia['subcompetencia']=$this->ed_subcompetencias->dameSubCompetenciasParaPdf($competencia['id_competencia']);
                }
                unset ($competencia);

                $ed['competencias']=$competencias;
                $ed['fortalezas']=0;
                $ed['aam']=0;
                $ed['pm']=0;
                $ed['metas']=0;
                $ed['marca']='Vista Preliminar';
//                echo "<pre>".print_r($ed,true)."</pre>";die();
                $this->_generaDocumentoPdf($ed);
            }
        
        
        
        function _generaDocumentoPdf($ed)//$id_ed,$save=false,$print=false
        {
            $save=false;
            $print=false;
            
            $this->load->library('Pdf_ed_library');
            $pdf = new Pdf_ed_library('P', 'mm', 'A4', true, 'UTF-8', false);
            
            $variables['pdf']=$pdf;
            $variables['ed']=$ed;
            $variables['print']=$print;
            $variables['output']=($save)?'D':'I';
                /*
                * I: send the file inline to the browser (default). The plug-in is used if available. The name given by name is used when one selects the "Save as" option on the link generating the PDF.
                * D: send to the browser and force a file download with the name given by name.
                * F: save to a local server file with the name given by name.
                * S: return the document as a string (name is ignored).
                * FI: equivalent to F + I option
                * FD: equivalent to F + D option
                * E: return the document as base64 mime multi-part email attachment (RFC 2045)
                */
             
            $variables['logo']=URL_BASE.'images/LogoSalesDeJujuy.png';
       
        
             $this->load->view('ed/pdf/ed',$variables);//,
        }
}
?>