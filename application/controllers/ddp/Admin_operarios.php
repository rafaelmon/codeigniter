<?php
class Admin_operarios extends CI_Controller
{
    private $modulo=30;
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
//            $variables['permiso_permiso'] = $this->permisos_model->checkIn($this->user['perfil_id'],$this->modulo);
            $variables['btn'] = $this->user['id'];
//             echo "<pre>".print_r($variables,true)."</pre>";
            $this->load->view('ddp/admin/operarios/listado',$variables);
	}
	
	public function listado()
	{
		if ($this->permisos['Listar'])
                {
                    $id_usuario=$this->user['id'];
                    $this->load->model('usuarios_model','usuarios',true);
                    $usuemp=$this->usuarios->dameUsuarioEmpresaYGR($id_usuario);
                    $id_empresa = $usuemp['id_empresa'];
                    $listado=array();
                    $this->load->model('ddp/ddp_operarios_model','ddp_operarios',true);
                    $start = ($this->input->post("start"))?$this->input->post("start"):0;
                    $limit = ($this->input->post("limit"))?$this->input->post("limit"):10;
                    $sort = $this->input->post("sort");
                    $dir = $this->input->post("dir");
                    if($this->input->post("filtros"))
                    {
                        $filtros=json_decode($this->input->post("filtros"));
                        foreach ($filtros as &$filtro)
                        {
                            if ($filtro=='Todas'||$filtro=='Todos'||$filtro=='-1')
                                $filtro="";
                        }
                        unset ($filtro);
//                        echo "<pre>".print_r($filtros,true)."</pre>";

                    }
                    else
                    {
                        $filtros="";

                    }
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
                    $listado = $this->ddp_operarios->listado($start, $limit, $filtros,$busqueda, $campos, $sort, $dir,$id_empresa);
                    echo $listado;
                }
                else
                    echo -1; //No tiene permisos
                
	}
        
        public function supervisores_combo()
	{
            $id_empresa=$this->input->post("id_empresa");
            $start = ($this->input->post("start"))?$this->input->post("start"):0;
            $limit = ($this->input->post("limit"))?$this->input->post("limit"):10;
            $query=$this->input->post("query");
            $this->load->model("ddp/ddp_operarios_model","ddp_operarios",true);
            $jcode = $this->ddp_operarios->supervisoresCombo($id_empresa,$limit,$start,$query);
            echo $jcode;
	}
       
        public function insert()
	{
            if ($this->permisos['Alta'])		
            {
                $campos_requeridos=array('empresa','legajo','apellido','nombre','id_supervisor');
                $control=true;
                foreach ($campos_requeridos as $campo)
                {
                    if($this->input->post($campo)=="")
                    {
                        $control=false;
                        echo $campo;
                    }
                }
                
                if ($control)
                {
                    //verifico que el nro de legajo no este repetido
                    $this->load->model('ddp/ddp_operarios_model','ddp_operarios',true);
                    $controlLegajo=$this->ddp_operarios->chekLegajo($this->input->post("empresa"),$this->input->post("legajo"));
                    
                    if($controlLegajo)
                    {
                        
                        $datos_operario=array();
                        $supervisores=array();
                        $id_usuario=$this->user['id'];

                        $datos_operario['id_empresa']                   =$this->input->post("empresa");
                        $datos_operario['legajo']                       =$this->input->post("legajo");
                        $datos_operario['nombre']                       =$this->input->post("nombre");
                        $datos_operario['apellido']                     =$this->input->post("apellido");
                        $datos_operario['id_usuario_supervisor']        =$this->input->post("id_supervisor");
                        $datos_operario['habilitado']       =1;
//                        $supervisores= explode(",",$this->input->post("sups"));

                        if($id_nuevo_operario=$this->ddp_operarios->insert($datos_operario))
                        {
//                            foreach ($supervisores as $dato)
//                            {
//                                $rel_op_sup['id_usuario_supervisor']=$dato;
//                                $rel_op_sup['id_operario']=$id_nuevo_operario;
//                                $ins2=$this->ddp_operarios->inser_supervisor($rel_op_sup);
//                                if($ins2)
//                                    $b=1;
//                                else
//                                {
//                                    $b=2;
//                                    break; //error al insertar usuario, salgo del foreach
//                                }
//                            }
                            echo 1;
                       }
                        else
                            echo 3; //error al insertar el documento
                    }
                    else
                        echo -2; //legajo repetido
                }
                else 
                    echo 4; //Faltan campos requeridos
            }
            else
                echo -1; //No tiene permisos
    } 
     public function empresas_combo()
    {
        $this->load->model('empresas_model','empresas',true);
        $var=$this->empresas->dameComboParaOperariosDDP();
        echo $var;	
    }
    public function reporte_excel()
    {
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        
        
        $this->excel->getProperties()
            ->setCreator("Sistema de Mejora Continua (SMC)") // Nombre del autor
            ->setLastModifiedBy("Sistema de Mejora Continua (SMC)") //Ultimo usuario que lo modificó
            ->setTitle("Reporte Administrar TOP") // Titulo
            ->setSubject("Reporte Administrar TOP") //Asunto
            ->setDescription("Reporte Administrar TOP") //Descripción
            ->setKeywords("Reporte Administrar TOP") //Etiquetas
            ->setCategory("Reporte Administrar TOP");
        $getActiveSheet=$this->excel->getActiveSheet();
        
        $estiloCabeceraGrilla = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => True,
                'italic'    => false,
                'strike'    => false,
                'size' =>11,
                'color'     => array(
                    'rgb' => 'FFFFFF'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'rotation' => 0,
                'wrap' => TRUE
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'argb' => 'FFC0504D'
                )
            ),
        );
        $estiloDatosPar = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => True,
                'italic'    => false,
                'strike'    => false,
                'size' =>10,
                'color'     => array(
                    'rgb' => '000000'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'rotation' => 0,
                'wrap' => TRUE
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'argb' => 'FFE5E0EC'
                )
            ),
        );
        $estiloDatosImpar = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => True,
                'italic'    => false,
                'strike'    => false,
                'size' =>10,
                'color'     => array(
                    'rgb' => '000000'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'rotation' => 0,
                'wrap' => TRUE
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'argb' => 'FFDBEEF3'
                )
            ),
        );
        
        $periodo = 1;
        
        $this->load->model('reportes/repo_administrar_top_model','reporte',true);
        $tops = $this->reporte->dameTops($periodo);
        
        $i = 5;
        $n = 0;
        $lastColumn = $this->excel->getActiveSheet()->getHighestColumn();
        
        foreach ($tops as $top)
        {
//            echo "<pre>Id_Area:s".print_r($top,true)."</pre>";die();
            if($n==0)
            {
                foreach ($top as $clave=>$valor)
                {
                    $getActiveSheet->setCellValue($lastColumn.$i, $clave);
                    $getActiveSheet->getStyle($lastColumn.$i)->applyFromArray($estiloCabeceraGrilla);
                    $lastColumn++;
                }
                $n++;
                $i++;
            }
            $getActiveSheet
                ->setCellValue('A'.$i,$top['Usuario'])
                ->setCellValue('B'.$i,$top['Periodo'])
                ->setCellValue('C'.$i,$top['Puesto'])
                ->setCellValue('D'.$i,$top['Gerencia'])
                ->setCellValue('E'.$i,$top['Supervisor'])
                ->setCellValue('F'.$i,$top['Estado'])
                ->setCellValue('G'.$i,$top['Sum. obj.'])
                ->setCellValue('H'.$i,$top['Sum. peso'])
                ->setCellValue('I'.$i,$top['Aprobado'])
                ->setCellValue('J'.$i,$top['Para aprobar']);
            if ($i & 1)
            {
                $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloDatosImpar);
                $getActiveSheet->getStyle('B'.$i)->applyFromArray($estiloDatosImpar);
                $getActiveSheet->getStyle('C'.$i)->applyFromArray($estiloDatosImpar);
                $getActiveSheet->getStyle('D'.$i)->applyFromArray($estiloDatosImpar);
                $getActiveSheet->getStyle('E'.$i)->applyFromArray($estiloDatosImpar);
                $getActiveSheet->getStyle('F'.$i)->applyFromArray($estiloDatosImpar);
                $getActiveSheet->getStyle('G'.$i)->applyFromArray($estiloDatosImpar);
                $getActiveSheet->getStyle('H'.$i)->applyFromArray($estiloDatosImpar);
                $getActiveSheet->getStyle('I'.$i)->applyFromArray($estiloDatosImpar);
                $getActiveSheet->getStyle('J'.$i)->applyFromArray($estiloDatosImpar);
            }
            else
            {
                $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloDatosPar);
                $getActiveSheet->getStyle('B'.$i)->applyFromArray($estiloDatosPar);
                $getActiveSheet->getStyle('C'.$i)->applyFromArray($estiloDatosPar);
                $getActiveSheet->getStyle('D'.$i)->applyFromArray($estiloDatosPar);
                $getActiveSheet->getStyle('E'.$i)->applyFromArray($estiloDatosPar);
                $getActiveSheet->getStyle('F'.$i)->applyFromArray($estiloDatosPar);
                $getActiveSheet->getStyle('G'.$i)->applyFromArray($estiloDatosPar);
                $getActiveSheet->getStyle('H'.$i)->applyFromArray($estiloDatosPar);
                $getActiveSheet->getStyle('I'.$i)->applyFromArray($estiloDatosPar);
                $getActiveSheet->getStyle('J'.$i)->applyFromArray($estiloDatosPar);
            }
            $i++;
        }
        
        $getActiveSheet->getColumnDimension('A')->setAutoSize(true);
        $getActiveSheet->getColumnDimension('B')->setAutoSize(true);
        $getActiveSheet->getColumnDimension('C')->setAutoSize(true);
        $getActiveSheet->getColumnDimension('D')->setAutoSize(true);
        $getActiveSheet->getColumnDimension('E')->setAutoSize(true);
        $getActiveSheet->getColumnDimension('F')->setAutoSize(true);
        $getActiveSheet->getColumnDimension('G')->setAutoSize(true);
        $getActiveSheet->getColumnDimension('H')->setAutoSize(true);
        $getActiveSheet->getColumnDimension('I')->setAutoSize(true);
        $getActiveSheet->getColumnDimension('J')->setAutoSize(true);
            
        $getActiveSheet->getPageSetup()->setFitToWidth(1);
        $getActiveSheet->getPageSetup()->setFitToHeight(0);
        $getActiveSheet->setTitle('Reporte administrar TOPs');
        $getActiveSheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $getActiveSheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        
        $filename = 'Reporte Administrar TOPs.xlsx';
        
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache

        //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
        //if you want to save it as .XLSX Excel 2007 format
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');  
        ob_end_clean();
        //force user to download the Excel file without writing it to server's HD
        $objWriter->save('php://output');
    }
        
}
?>