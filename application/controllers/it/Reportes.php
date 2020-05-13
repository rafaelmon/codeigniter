<?php
class Reportes extends CI_Controller
{
    private $modulo=36;
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
//            echo "<pre>".print_r($this->permisos,true)."</pre>";
	}
	
	public function index()
	{
            $modulo = $this->uri->segment(SEGMENTOS_DOM+2);
            if (!isset($modulo)) $modulo=0;
            $this->load->model('permisos_model','permisos_model',true);
            $variables['permiso'] = $this->permisos_model->checkIn($this->user['perfil_id'],$modulo);
            $variables['permiso_permiso'] = $this->permisos_model->checkIn($this->user['perfil_id'],$this->modulo);
            $this->load->view('reportes/listado',$variables);
	}
        public function listado()
	{
		if ($this->permisos['Listar'])
                {
                    $this->load->model('reportes/rep_reportes_model','rep_reportes',true);
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
                    }
//                    echo "<pre>".print_r($sort,true)."</pre>";
                    $listado = $this->rep_reportes->listado($start, $limit, $busqueda, $campos, $sort, $dir);
                    echo $listado;
                }
                else
                    echo -1; //No tiene permisos
                
	}
        public function download()
        {
            $id_reporte = $this->uri->segment(SEGMENTOS_DOM+2);
            switch ($id_reporte) {
                case 1:$this->generar_excel_tareas(1);break;
                case 2:$this->generar_excel_tareas(2);break;
                case 3:$this->generar_excel_tareas(3);break;
                default:break;
            }
            
        }
        
        public function generar_excel_tareas($id_empresa)
        {
        $filas=array('id_corp','id_gerencia','id_empresa','id_organigrama');
        
        $numeros=array(
            'ua_q_tareas_A'
            ,'ua_q_tareas_A2'
            ,'ua_q_tareas_R'
            ,'ua_q_tareas_V1'
            ,'ua_q_tareas_V2'
            ,'ua_q_tareas_Cm'
            ,'ua_q_tareas_Ct'
            ,'ua_q_tareas_C'
            ,'ua_q_tareas_t'
            ,'ur_q_tareas_A'
            ,'ur_q_tareas_A2'
            ,'ur_q_tareas_R'
            ,'ur_q_tareas_V1'
            ,'ur_q_tareas_V2'
            ,'ur_q_tareas_Cm'
            ,'ur_q_tareas_Ct'
            ,'ur_q_tareas_C'
            ,'ur_q_tareas_t'
        );
        
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        
        
        $this->excel->getProperties()
            ->setCreator("Sistema de Mejora Continua (SMC)") // Nombre del autor
            ->setLastModifiedBy("Sistema de Mejora Continua (SMC)") //Ultimo usuario que lo modificó
            ->setTitle("Reporte Semanal") // Titulo
            ->setSubject("Reporte Semanal") //Asunto
            ->setDescription("Reporte Semanal") //Descripción
            ->setKeywords("Reporte Semanal") //Etiquetas
            ->setCategory("Reporte Semanal");
        
        $getActiveSheet=$this->excel->getActiveSheet();
        
        $mes = date("m");
        $fecha_actual=date("d/m/Y");
        $anio = date("Y");
        
        switch($mes)
        {
            case 1: $nombreMes = 'Enero';break;
            case 2: $nombreMes = 'Febrero';break;
            case 3: $nombreMes = 'Marzo';break;
            case 4: $nombreMes = 'Abril';break;
            case 5: $nombreMes = 'Mayo';break;
            case 6: $nombreMes = 'Junio';break;
            case 7: $nombreMes = 'Julio';break;
            case 8: $nombreMes = 'Agosto';break;
            case 9: $nombreMes = 'Septiembre';break;
            case 10: $nombreMes = 'Octubre';break;
            case 11: $nombreMes = 'Noviembre';break;
            case 12: $nombreMes = 'Diciembre';break;
        }
        
        $estiloTituloReporte = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => true,
                'italic'    => false,
                'strike'    => false,
                'size' =>30,
                'color'     => array(
                    'rgb' => '000000'
                )
            ),
            'alignment' => array(
               'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
               'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
               'rotation' => 0,
               'wrap' => TRUE
            )
         );
        $estiloTitulosCabecera = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => true,
                'italic'    => false,
                'strike'    => false,
                'size' =>12,
                'color'     => array(
                    'rgb' => '000000'
                )
            ),
           'alignment' => array(
               'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
               'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
               'rotation' => 0,
               'wrap' => TRUE
            )
        );
        $estiloDatosCabecera = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => false,
                'italic'    => false,
                'strike'    => false,
                'size' =>12,
                'color'     => array(
                    'rgb' => '000000'
                )
            ),
            'alignment' => array(
               'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
               'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
               'rotation' => 0,
               'wrap' => TRUE
            ),
            'fill' => array(
               'type'  => PHPExcel_Style_Fill::FILL_SOLID,
               'color' => array(
               'argb' => 'FFF2F2F2'
                )
            ),
        );
        $estiloTitulosGrilla = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => True,
                'italic'    => false,
                'strike'    => false,
                'size' =>20,
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
                'argb' => 'FF9BBB59'
                )
            ),
        );
        $estiloSubTitulosGrillaC = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => True,
                'italic'    => false,
                'strike'    => false,
                'size' =>18,
                'color'     => array(
                    'rgb' => '1F497D'
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
                'argb' => 'FFFFFFFF'
                )
            ),
        );
        $estiloSubTitulosGrillaR = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => True,
                'italic'    => false,
                'strike'    => false,
                'size' =>18,
                'color'     => array(
                    'rgb' => '9BBB59'
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
                'argb' => 'FFFFFFFF')
            ),
        );
        $estiloTitulosTotTareas = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => True,
                'italic'    => false,
                'strike'    => false,
                'size' =>8,
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
                'argb' => 'FF808080'
                )
            ),
        );
        $estiloTitulosGrillaPorcentaje = array(
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
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                    'rotation' => 0,
                    'wrap' => TRUE
                ),
                'fill' => array(
                    'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array(
                    'argb' => 'FFFFFFFF'
                    )
                ),
        );
        $estiloPorcentajeTotal = array(
                   'font' => array(
                       'name'      => 'Calibri',
                       'bold'      => True,
                       'italic'    => false,
                       'strike'    => false,
                       'size' =>18,
                       'color'     => array(
                           'rgb' => 'FFFF00'
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
                      'argb' => 'FF000000')
                    ),
            );
            $estiloTituloA = array(
                'font' => array(
                    'name'      => 'Calibri',
                    'bold'      => True,
                    'italic'    => false,
                    'strike'    => false,
                    'size' =>8,
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
                    'argb' => 'FF528ED5'
                    )
                ),
            );
        $estiloTituloA2 = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => True,
                'italic'    => false,
                'strike'    => false,
                'size' =>8,
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
                'argb' => 'FF1F497D'
                )
            ),
        );
        $estiloTituloR = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => True,
                'italic'    => false,
                'strike'    => false,
                'size' =>8,
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
                'argb' => 'FFE46D0A'
                )
            ),
        );
        $estiloTituloV1 = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => True,
                'italic'    => false,
                'strike'    => false,
                'size' =>8,
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
                'argb' => 'FFFF3B3B')
            ),
        );
        $estiloTituloV2 = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => True,
                'italic'    => false,
                'strike'    => false,
                'size' =>8,
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
                'argb' => 'FFFF0000'
                )
            ),
        );
        $estiloTituloCm = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => True,
                'italic'    => false,
                'strike'    => false,
                'size' =>8,
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
                'argb' => 'FF00B050'
                )
            ),
        );
        $estiloTituloCt = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => True,
                'italic'    => false,
                'strike'    => false,
                'size' =>8,
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
                'argb' => 'FF948B54'
                )
            ),
        );
        
        $estiloTituloEmpresas = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => True,
                'italic'    => false,
                'strike'    => false,
                'size' =>16,
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
                    'argb' => 'FFA5A5A5'
                )
            ),
        );
        $estiloGciaCorp = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => false,
                'italic'    => false,
                'strike'    => false,
                'size' =>11,
                'color'     => array(
                    'rgb' => '000000'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'rotation' => 0,
                'wrap' => TRUE
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'argb' => 'FFFFC000'
                )
            ),
        );
        $estiloTituloTotal = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => false,
                'italic'    => false,
                'strike'    => false,
                'size' =>10,
                'color'     => array(
                    'rgb' => 'FFFFFF'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'rotation' => 0,
                'wrap' => TRUE
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'argb' => 'FFA5A5A5'
                )
            ),
        );
        $estiloTitulosAreas = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => True,
                'italic'    => false,
                'strike'    => false,
                'size' =>16,
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
                    'argb' => 'FFA5A5A5'
                )
            ),
        );
        $estiloTitulosAreasNoCorp = array(
                'font' => array(
                    'name'      => 'Calibri',
                    'bold'      => True,
                    'italic'    => false,
                    'strike'    => false,
                    'size' =>12,
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
                        'argb' => 'FFA5A5A5'
                    )
                ),
        );
        $estiloGerenciasCorp = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => True,
                'italic'    => false,
                'strike'    => false,
                'size' =>11,
                'color'     => array(
                    'rgb' => '1F497D'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'rotation' => 0,
                'wrap' => TRUE
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'argb' => 'FFFFC000'
                )
            ),
        );
        $estiloGerencias = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => True,
                'italic'    => false,
                'strike'    => false,
                'size' =>9,
                'color'     => array(
                    'rgb' => '000000'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'rotation' => 0,
                'wrap' => TRUE
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'argb' => 'FFC2D69A'
                )
            ),
        );
        $estiloUsuario = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => True,
                'italic'    => true,
                'strike'    => false,
                'size' =>10,
                'color'     => array(
                    'rgb' => '000000'
                )
                ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'rotation' => 0,
                'wrap' => TRUE
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'argb' => 'FFFFFFFF'
                )
            ),
        );
        //-------------------------------DATOS---------------------------------
        $estiloDatosA = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => True,
                'italic'    => false,
                'strike'    => false,
                'size' =>8,
                'color'     => array(
                    'rgb' => '528ED5'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'rotation' => 0,
                'wrap' => TRUE
            ),
            'borders' => array(
                'outline' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                    'color' => array(
                        'rgb' => 'BFBFBF'
                    )
                )
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                   'argb' => 'FFFFFFFF'
                )
            ),
        );
        $estiloDatosA2 = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => True,
                'italic'    => false,
                'strike'    => false,
                'size' =>8,
                'color'     => array(
                   'rgb' => '1F497D'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'rotation' => 0,
                'wrap' => TRUE
            ),
            'borders' => array(
                'outline' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                    'color' => array(
                        'rgb' => 'BFBFBF'
                    )
                )
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                  'argb' => 'FFFFFFFF'
                )
            ),
        );
        $estiloDatosR = array(
            'font' => array(
               'name'      => 'Calibri',
               'bold'      => True,
               'italic'    => false,
               'strike'    => false,
               'size' =>8,
               'color'     => array(
                   'rgb' => 'F79646'
                  )
              ),
            'alignment' => array(
              'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
              'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
              'rotation' => 0,
              'wrap' => TRUE
            ),
            'borders' => array(
                'outline' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                    'color' => array(
                        'rgb' => 'BFBFBF'
                    )
                )
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                  'argb' => 'FFFFFFFF'
                )
            ),
        );
        $estiloDatosV1 = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => True,
                'italic'    => false,
                'strike'    => false,
                'size' =>8,
                'color'     => array(
                   'rgb' => 'D99695'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'rotation' => 0,
                'wrap' => TRUE
            ),
            'borders' => array(
                'outline' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                    'color' => array(
                        'rgb' => 'BFBFBF'
                    )
                )
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                  'argb' => 'FFFFFFFF'
                )
            ),
        );
        $estiloDatosV2 = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => True,
                'italic'    => false,
                'strike'    => false,
                'size' =>8,
                'color'     => array(
                   'rgb' => 'FF0000'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'rotation' => 0,
                'wrap' => TRUE
            ),
            'borders' => array(
                'outline' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                    'color' => array(
                        'rgb' => 'BFBFBF'
                    )
                )
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                'argb' => 'FFFFFFFF'
                )
            ),
        );
        $estiloDatosCm = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => True,
                'italic'    => false,
                'strike'    => false,
                'size' =>8,
                'color'     => array(
                   'rgb' => '948B54'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'rotation' => 0,
                'wrap' => TRUE
            ),
            'borders' => array(
                'outline' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                    'color' => array(
                        'rgb' => 'BFBFBF'
                    )
                )
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                'argb' => 'FFFFFFFF'
                )
            ),
        );
        $estiloDatosCt = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => True,
                'italic'    => false,
                'strike'    => false,
                'size' =>8,
                'color'     => array(
                    'rgb' => '00B050'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'rotation' => 0,
                'wrap' => TRUE
            ),
            'borders' => array(
                'outline' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                    'color' => array(
                        'rgb' => 'BFBFBF'
                    )
                )
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                'argb' => 'FFFFFFFF'
                )
            ),
        );
        $estiloDatosTot = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => True,
                'italic'    => false,
                'strike'    => false,
                'size' =>8,
                'color'     => array(
                   'rgb' => '7F7F7F'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'rotation' => 0,
                'wrap' => TRUE
            ),
            'borders' => array(
                'outline' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                    'color' => array(
                        'rgb' => 'BFBFBF'
                    )
                )
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'argb' => 'FFFFFFFF'
                )
            ),
        );
        $estiloDatospTot = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => false,
                'italic'    => false,
                'strike'    => false,
                'size' =>8,
                'color'     => array(
                   'rgb' => '7F7F7F'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'rotation' => 0,
                'wrap' => TRUE
            ),
            'borders' => array(
                'outline' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                    'color' => array(
                        'rgb' => 'BFBFBF'
                    )
                )
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                'argb' => 'FFFFFFFF'
                )
            ),
        );
        $estiloDatospCump = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => true,
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
            'borders' => array(
                'outline' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                    'color' => array(
                        'rgb' => 'BFBFBF'
                    )
                )
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'argb' => 'FF00B050'
                )
            ),
        );
        $estiloDatosCorp = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => True,
                'italic'    => false,
                'strike'    => false,
                'size' =>8,
                'color'     => array(
                    'rgb' => '528ED5'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'rotation' => 0,
                'wrap' => TRUE
            ),
            'borders' => array(
                'outline' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                    'color' => array(
                        'rgb' => 'BFBFBF'
                    )
                )
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                'argb' => 'FFFFC000')
            ),
        );
        $estiloDatosGciaGral = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => True,
                'italic'    => false,
                'strike'    => false,
                'size' =>8,
                'color'     => array(
                    'rgb' => '528ED5'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'rotation' => 0,
                'wrap' => TRUE
            ),
            'borders' => array(
                'outline' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                    'color' => array(
                        'rgb' => 'BFBFBF'
                    )
                )
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'argb' => 'FFC2D69A'
                )
            ),
        );
        
        $usuarioC = 'Usuario Creador';
        $usuarioR = 'Usuario Responsable';
        
        $this->excel->getActiveSheet()
            ->setCellValue('A1', 'Reporte de Tareas')
            ->setCellValue('A3', 'Año:')
            ->setCellValue('A4', 'Mes:')
            ->setCellValue('A5', 'Fecha de Reporte:')
            ->setCellValue('C3', $anio)
            ->setCellValue('C4', $nombreMes)
            ->setCellValue('C5', $fecha_actual)
            ->setCellValue('E3', 'TAREAS')
            ->setCellValue('P3', 'TAREAS')
            ->setCellValue('E4', $usuarioC)
            ->setCellValue('P4', $usuarioR)
            ->setCellValue('E5', '% de Cumplimiento Alcanzado->')
            ->setCellValue('P5', '% de Cumplimiento Alcanzado->')
            ->setCellValue('E6', 'A')
            ->setCellValue('F6', 'A2')
            ->setCellValue('G6', 'R')
            ->setCellValue('H6', 'V1')
            ->setCellValue('I6', 'V2')
            ->setCellValue('J6', 'Cm')
            ->setCellValue('K6', 'Ct')
            ->setCellValue('L6', 'Tot.')
            ->setCellValue('M6', '% Tot.')
            ->setCellValue('N6', '% Cump.')
            ->setCellValue('P6', 'A')
            ->setCellValue('Q6', 'A2')
            ->setCellValue('R6', 'R')
            ->setCellValue('S6', 'V1')
            ->setCellValue('T6', 'V2')
            ->setCellValue('U6', 'Cm')
            ->setCellValue('V6', 'Ct')
            ->setCellValue('W6', 'Tot.')
            ->setCellValue('X6', '% Tot.')
            ->setCellValue('Y6', '% Cump.');
        
        $getActiveSheet
            ->mergeCells('A1:Y1')
            ->mergeCells('A3:B3')
            ->mergeCells('A4:B4')
            ->mergeCells('A5:B5')
            ->mergeCells('C3:D3')
            ->mergeCells('C4:D4')
            ->mergeCells('C5:D5')
            ->mergeCells('E3:N3')
            ->mergeCells('E4:N4')
            ->mergeCells('E5:L5')
            ->mergeCells('M5:N5')
            ->mergeCells('P3:Y3')
            ->mergeCells('P4:Y4')
            ->mergeCells('P5:W5')
            ->mergeCells('X5:Y5');
        
        $this->load->model('reportes/meta_reportes_model','meta_reporte',true);
        $usuarios = $this->meta_reporte->dameUsuarios($anio,$mes);
        
        $n=0;            
        $totaltareasuaA=0;
        $totaltareasuaA2=0;
        $totaltareasuaR=0;
        $totaltareasuaV1=0;
        $totaltareasuaV2=0;
        $totaltareasuaCm=0;
        $totaltareasuaCt=0;
        $totaltareasua=0;
        $totaltareasurA=0;
        $totaltareasurA2=0;
        $totaltareasurR=0;
        $totaltareasurV1=0;
        $totaltareasurV2=0;
        $totaltareasurCm=0;
        $totaltareasurCt=0;
        $totaltareasur=0;
        $totalporctareasempresasua = 0;
        $totalporctareasempresasur = 0;
        $totalporctareascorpua = 0;
        $totalporctareascorpur = 0;
        $totaltareasuaAg=0;
        $totaltareasuaA2g=0;
        $totaltareasuaRg=0;
        $totaltareasuaV1g=0;
        $totaltareasuaV2g=0;
        $totaltareasuaCmg=0;
        $totaltareasuaCtg=0;
        $totaltareasuag=0;
        $totaltareasurAg=0;
        $totaltareasurA2g=0;
        $totaltareasurRg=0;
        $totaltareasurV1g=0;
        $totaltareasurV2g=0;
        $totaltareasurCmg=0;
        $totaltareasurCtg=0;
        $totaltareasurg=0;
        $totaltareasuaAgb=0;
        $totaltareasuaA2gb=0;
        $totaltareasuaRgb=0;
        $totaltareasuaV1gb=0;
        $totaltareasuaV2gb=0;
        $totaltareasuaCmgb=0;
        $totaltareasuaCtgb=0;
        $totaltareasuagb=0;
        $totaltareasurAgb=0;
        $totaltareasurA2gb=0;
        $totaltareasurRgb=0;
        $totaltareasurV1gb=0;
        $totaltareasurV2gb=0;
        $totaltareasurCmgb=0;
        $totaltareasurCtgb=0;
        $totaltareasurgb=0;
        
        $arreglo=$this->tabla_dinamica($usuarios,$numeros,$filas);
        $areas=$this->arrayCargaUsuarios($usuarios,$numeros,$filas);
        $gciacorp=$this->armarGerenciasCorp($arreglo,$numeros);
        $gcia=$this->armarGerencias($arreglo,$numeros);
        
        $i = 7;
        $j= $i;
        
        if ($id_empresa == 1)
        { 
            foreach ($arreglo as $empresa)
            {
                if($n==0)
                {
                    $getActiveSheet
                        ->mergeCells('A'.$i.':D'.$i)
                        ->getRowDimension($i)->setRowHeight(27);
                    $getActiveSheet->setCellValue('A'.$i, 'Empresas');
                    $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloTituloEmpresas);
                    
                    foreach($arreglo as $linea)
                    {
                        $totaltareasuaA=$totaltareasuaA+$linea['ua_q_tareas_A'];
                        $totaltareasuaA2=$totaltareasuaA2+$linea['ua_q_tareas_A2'];
                        $totaltareasuaR=$totaltareasuaR+$linea['ua_q_tareas_R'];
                        $totaltareasuaV1=$totaltareasuaV1+$linea['ua_q_tareas_V1'];
                        $totaltareasuaV2=$totaltareasuaV2+$linea['ua_q_tareas_V2'];
                        $totaltareasuaCm=$totaltareasuaCm+$linea['ua_q_tareas_Cm'];
                        $totaltareasuaCt=$totaltareasuaCt+$linea['ua_q_tareas_Ct'];
                        $totaltareasua=$totaltareasua+$linea['ua_q_tareas_t'];
                        $totaltareasurA=$totaltareasurA+$linea['ur_q_tareas_A'];
                        $totaltareasurA2=$totaltareasurA2+$linea['ur_q_tareas_A2'];
                        $totaltareasurR=$totaltareasurR+$linea['ur_q_tareas_R'];
                        $totaltareasurV1=$totaltareasurV1+$linea['ur_q_tareas_V1'];
                        $totaltareasurV2=$totaltareasurV2+$linea['ur_q_tareas_V2'];
                        $totaltareasurCm=$totaltareasurCm+$linea['ur_q_tareas_Cm'];
                        $totaltareasurCt=$totaltareasurCt+$linea['ur_q_tareas_Ct'];
                        $totaltareasur=$totaltareasur+$linea['ur_q_tareas_t'];
                    }
                    $n++;
                    $i++;
                }

                $getActiveSheet->mergeCells('A'.$i.':D'.$i);
                $getActiveSheet->setCellValue('A'.$i, $empresa['nomemp']);
                $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloGciaCorp);
                
                $getActiveSheet
                    ->setCellValue('E'.$i, $empresa['ua_q_tareas_A'])
                    ->setCellValue('F'.$i, $empresa['ua_q_tareas_A2'])
                    ->setCellValue('G'.$i, $empresa['ua_q_tareas_R'])
                    ->setCellValue('H'.$i, $empresa['ua_q_tareas_V1'])
                    ->setCellValue('I'.$i, $empresa['ua_q_tareas_V2'])
                    ->setCellValue('J'.$i, $empresa['ua_q_tareas_Cm'])
                    ->setCellValue('K'.$i, $empresa['ua_q_tareas_Ct'])
                    ->setCellValue('L'.$i, $empresa['ua_q_tareas_t'])
                    ->setCellValue('M'.$i, ($empresa['ua_q_tareas_t']/$totaltareasua))
                    ->setCellValue('P'.$i, $empresa['ur_q_tareas_A'])
                    ->setCellValue('Q'.$i, $empresa['ur_q_tareas_A2'])
                    ->setCellValue('R'.$i, $empresa['ur_q_tareas_R'])
                    ->setCellValue('S'.$i, $empresa['ur_q_tareas_V1'])
                    ->setCellValue('T'.$i, $empresa['ur_q_tareas_V2'])
                    ->setCellValue('U'.$i, $empresa['ur_q_tareas_Cm'])
                    ->setCellValue('V'.$i, $empresa['ur_q_tareas_Ct'])
                    ->setCellValue('W'.$i, $empresa['ur_q_tareas_t'])
                    ->setCellValue('X'.$i, ($empresa['ur_q_tareas_t']/$totaltareasur));
                
                $divisorUA = ($empresa['ua_q_tareas_R']+$empresa['ua_q_tareas_V1']+$empresa['ua_q_tareas_V2']+$empresa['ua_q_tareas_Cm']+$empresa['ua_q_tareas_Ct']);
                $divisorUR = ($empresa['ur_q_tareas_R']+$empresa['ur_q_tareas_V1']+$empresa['ur_q_tareas_V2']+$empresa['ur_q_tareas_Cm']+$empresa['ur_q_tareas_Ct']);
                
                if($divisorUA != 0)
                {
                    $getActiveSheet
                        ->setCellValue('N'.$i, (($empresa['ua_q_tareas_Cm']+$empresa['ua_q_tareas_Ct'])/$divisorUA));
                }
                else
                {
                    $getActiveSheet->setCellValue('N'.$i, 0);
                }
                if($divisorUR != 0)
                    
                {
                    $getActiveSheet
                        ->setCellValue('Y'.$i, (($empresa['ur_q_tareas_Cm']+$empresa['ur_q_tareas_Ct'])/$divisorUR));
                }
                else
                {
                    $getActiveSheet->setCellValue('Y'.$i, 0);
                }
                
                $getActiveSheet->getStyle('E'.$i)->applyFromArray($estiloDatosA);
                $getActiveSheet->getStyle('F'.$i)->applyFromArray($estiloDatosA2);
                $getActiveSheet->getStyle('G'.$i)->applyFromArray($estiloDatosR);
                $getActiveSheet->getStyle('H'.$i)->applyFromArray($estiloDatosV1);
                $getActiveSheet->getStyle('I'.$i)->applyFromArray($estiloDatosV2);
                $getActiveSheet->getStyle('J'.$i)->applyFromArray($estiloDatosCm);
                $getActiveSheet->getStyle('K'.$i)->applyFromArray($estiloDatosCt);
                $getActiveSheet->getStyle('L'.$i)->applyFromArray($estiloDatosTot);
                $getActiveSheet->getStyle('M'.$i)->applyFromArray($estiloDatospTot);
                $getActiveSheet->getStyle('N'.$i)->applyFromArray($estiloDatospCump);
                $getActiveSheet->getStyle('P'.$i)->applyFromArray($estiloDatosA);
                $getActiveSheet->getStyle('Q'.$i)->applyFromArray($estiloDatosA2);
                $getActiveSheet->getStyle('R'.$i)->applyFromArray($estiloDatosR);
                $getActiveSheet->getStyle('S'.$i)->applyFromArray($estiloDatosV1);
                $getActiveSheet->getStyle('T'.$i)->applyFromArray($estiloDatosV2);
                $getActiveSheet->getStyle('U'.$i)->applyFromArray($estiloDatosCm);
                $getActiveSheet->getStyle('V'.$i)->applyFromArray($estiloDatosCt);
                $getActiveSheet->getStyle('W'.$i)->applyFromArray($estiloDatosTot);
                $getActiveSheet->getStyle('X'.$i)->applyFromArray($estiloDatospTot);
                $getActiveSheet->getStyle('Y'.$i)->applyFromArray($estiloDatospCump);
                
                $totalporctareasempresasua = $totalporctareasempresasua + ($empresa['ua_q_tareas_t']/$totaltareasua);
                $totalporctareasempresasur = $totalporctareasempresasur + ($empresa['ur_q_tareas_t']/$totaltareasur);
                $i++;
            }
            $this->excel->getActiveSheet()//totales
                ->mergeCells('A'.$i.':D'.$i)
                ->setCellValue('A'.$i, 'Total')
                ->setCellValue('E'.$i, $totaltareasuaA)
                ->setCellValue('F'.$i, $totaltareasuaA2)
                ->setCellValue('G'.$i, $totaltareasuaR)
                ->setCellValue('H'.$i, $totaltareasuaV1)
                ->setCellValue('I'.$i, $totaltareasuaV2)
                ->setCellValue('J'.$i, $totaltareasuaCm)
                ->setCellValue('K'.$i, $totaltareasuaCt)
                ->setCellValue('L'.$i, $totaltareasua)
                ->setCellValue('M'.$i, $totalporctareasempresasua)
                ->setCellValue('P'.$i, $totaltareasurA)
                ->setCellValue('Q'.$i, $totaltareasurA2)
                ->setCellValue('R'.$i, $totaltareasurR)
                ->setCellValue('S'.$i, $totaltareasurV1)
                ->setCellValue('T'.$i, $totaltareasurV2)
                ->setCellValue('U'.$i, $totaltareasurCm)
                ->setCellValue('V'.$i, $totaltareasurCt)
                ->setCellValue('W'.$i, $totaltareasur)
                ->setCellValue('X'.$i, $totalporctareasempresasur);
            
            $divisorUA = ($totaltareasuaR+$totaltareasuaV1+$totaltareasuaV2+$totaltareasuaCm+$totaltareasuaCt);
            $divisorUR = ($totaltareasurR+$totaltareasurV1+$totaltareasurV2+$totaltareasurCm+$totaltareasurCt);
            if($divisorUA != 0)
            {
                $getActiveSheet
                    ->setCellValue('N'.$i, (($totaltareasuaCm+$totaltareasuaCt)/$divisorUA));
            }
            else
            {
                $getActiveSheet->setCellValue('N'.$i, 0);
            }
            if($divisorUR != 0)
            {
                $getActiveSheet
                    ->setCellValue('Y'.$i, (($totaltareasurCm+$totaltareasurCt)/$divisorUR));
            }
            else
            {
                $getActiveSheet->setCellValue('Y'.$i, 0);
            }
            $getActiveSheet
                ->setCellValue('M5', (($totaltareasuaCm+$totaltareasuaCt)/$divisorUA));
            $getActiveSheet
                ->setCellValue('X5', (($totaltareasurCm+$totaltareasurCt)/$divisorUR));
            $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloTituloTotal);
            $getActiveSheet->getStyle('E'.$i.':M'.$i)->applyFromArray($estiloTituloTotal);
            $getActiveSheet->getStyle('P'.$i.':X'.$i)->applyFromArray($estiloTituloTotal);
            $getActiveSheet->getStyle('N'.$i)->applyFromArray($estiloDatospCump);
            $getActiveSheet->getStyle('Y'.$i)->applyFromArray($estiloDatospCump);
            
            $i = $i+3;
        }
        else
        {
            foreach($gciacorp as $valor)
            {
                $totaltareasuaA=$totaltareasuaA+$valor['ua_q_tareas_A'];
                $totaltareasuaA2=$totaltareasuaA2+$valor['ua_q_tareas_A2'];
                $totaltareasuaR=$totaltareasuaR+$valor['ua_q_tareas_R'];
                $totaltareasuaV1=$totaltareasuaV1+$valor['ua_q_tareas_V1'];
                $totaltareasuaV2=$totaltareasuaV2+$valor['ua_q_tareas_V2'];
                $totaltareasuaCm=$totaltareasuaCm+$valor['ua_q_tareas_Cm'];
                $totaltareasuaCt=$totaltareasuaCt+$valor['ua_q_tareas_Ct'];
                $totaltareasua=$totaltareasua+$valor['ua_q_tareas_t'];
                $totaltareasurA=$totaltareasurA+$valor['ur_q_tareas_A'];
                $totaltareasurA2=$totaltareasurA2+$valor['ur_q_tareas_A2'];
                $totaltareasurR=$totaltareasurR+$valor['ur_q_tareas_R'];
                $totaltareasurV1=$totaltareasurV1+$valor['ur_q_tareas_V1'];
                $totaltareasurV2=$totaltareasurV2+$valor['ur_q_tareas_V2'];
                $totaltareasurCm=$totaltareasurCm+$valor['ur_q_tareas_Cm'];
                $totaltareasurCt=$totaltareasurCt+$valor['ur_q_tareas_Ct'];
                $totaltareasur=$totaltareasur+$valor['ur_q_tareas_t'];
            }
        }
            
        //GERENCIAS CORPORATIVAS
        $getActiveSheet
            ->mergeCells('A'.$i.':D'.$i)
            ->getRowDimension($i)->setRowHeight(27);
        $getActiveSheet
            ->setCellValue('A'.$i, 'Gerencias Corporativas');
        $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloTitulosAreas);
        $i++;
        foreach($gciacorp as $valor)
        {
            $getActiveSheet
                    ->mergeCells('A'.$i.':D'.$i)
                    ->setCellValue('A'.$i,$valor['gerencia'])
                    ->setCellValue('E'.$i,$valor['ua_q_tareas_A'])
                    ->setCellValue('F'.$i,$valor['ua_q_tareas_A2'])
                    ->setCellValue('G'.$i,$valor['ua_q_tareas_R'])
                    ->setCellValue('H'.$i,$valor['ua_q_tareas_V1'])
                    ->setCellValue('I'.$i,$valor['ua_q_tareas_V2'])
                    ->setCellValue('J'.$i,$valor['ua_q_tareas_Cm'])
                    ->setCellValue('K'.$i,$valor['ua_q_tareas_Ct'])
                    ->setCellValue('L'.$i,$valor['ua_q_tareas_t'])
                    ->setCellValue('M'.$i,($valor['ua_q_tareas_t']/$totaltareasua))
                    ->setCellValue('P'.$i,$valor['ur_q_tareas_A'])
                    ->setCellValue('Q'.$i,$valor['ur_q_tareas_A2'])
                    ->setCellValue('R'.$i,$valor['ur_q_tareas_R'])
                    ->setCellValue('S'.$i,$valor['ur_q_tareas_V1'])
                    ->setCellValue('T'.$i,$valor['ur_q_tareas_V2'])
                    ->setCellValue('U'.$i,$valor['ur_q_tareas_Cm'])
                    ->setCellValue('V'.$i,$valor['ur_q_tareas_Ct'])
                    ->setCellValue('W'.$i,$valor['ur_q_tareas_t'])
                    ->setCellValue('X'.$i,($valor['ur_q_tareas_t']/$totaltareasua));
            
            $totalporctareascorpua = $totalporctareascorpua + ($valor['ua_q_tareas_t']/$totaltareasua);
            $totalporctareascorpur = $totalporctareascorpur + ($valor['ur_q_tareas_t']/$totaltareasur);

            $divisorUA = ($valor['ua_q_tareas_R']+$valor['ua_q_tareas_V1']+$valor['ua_q_tareas_V2']+$valor['ua_q_tareas_Cm']+$valor['ua_q_tareas_Ct']);
            $divisorUR = ($valor['ur_q_tareas_R']+$valor['ur_q_tareas_V1']+$valor['ur_q_tareas_V2']+$valor['ur_q_tareas_Cm']+$valor['ur_q_tareas_Ct']);

            if($divisorUA != 0)
            {
                $getActiveSheet
                    ->setCellValue('N'.$i, (($valor['ua_q_tareas_Cm']+$valor['ua_q_tareas_Ct'])/$divisorUA));
            }
            else
            {
                $getActiveSheet->setCellValue('N'.$i, 0);
            }
            if($divisorUR != 0) 
            {
                $getActiveSheet
                    ->setCellValue('Y'.$i, (($valor['ur_q_tareas_Cm']+$valor['ur_q_tareas_Ct'])/$divisorUR));
            }
            else
            {
                $getActiveSheet->setCellValue('Y'.$i, 0);
            }

            $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloGciaCorp);
            $getActiveSheet->getStyle('E'.$i)->applyFromArray($estiloDatosA);
            $getActiveSheet->getStyle('F'.$i)->applyFromArray($estiloDatosA2);
            $getActiveSheet->getStyle('G'.$i)->applyFromArray($estiloDatosR);
            $getActiveSheet->getStyle('H'.$i)->applyFromArray($estiloDatosV1);
            $getActiveSheet->getStyle('I'.$i)->applyFromArray($estiloDatosV2);
            $getActiveSheet->getStyle('J'.$i)->applyFromArray($estiloDatosCm);
            $getActiveSheet->getStyle('K'.$i)->applyFromArray($estiloDatosCt);
            $getActiveSheet->getStyle('L'.$i)->applyFromArray($estiloDatosTot);
            $getActiveSheet->getStyle('M'.$i)->applyFromArray($estiloDatospTot);
            $getActiveSheet->getStyle('N'.$i)->applyFromArray($estiloDatospCump);
            $getActiveSheet->getStyle('P'.$i)->applyFromArray($estiloDatosA);
            $getActiveSheet->getStyle('Q'.$i)->applyFromArray($estiloDatosA2);
            $getActiveSheet->getStyle('R'.$i)->applyFromArray($estiloDatosR);
            $getActiveSheet->getStyle('S'.$i)->applyFromArray($estiloDatosV1);
            $getActiveSheet->getStyle('T'.$i)->applyFromArray($estiloDatosV2);
            $getActiveSheet->getStyle('U'.$i)->applyFromArray($estiloDatosCm);
            $getActiveSheet->getStyle('V'.$i)->applyFromArray($estiloDatosCt);
            $getActiveSheet->getStyle('W'.$i)->applyFromArray($estiloDatosTot);
            $getActiveSheet->getStyle('X'.$i)->applyFromArray($estiloDatospTot);
            $getActiveSheet->getStyle('Y'.$i)->applyFromArray($estiloDatospCump);
            $i++;
        }

        $this->excel->getActiveSheet()//totales
            ->mergeCells('A'.$i.':D'.$i)
            ->setCellValue('A'.$i, 'Total')
            ->setCellValue('E'.$i, $totaltareasuaA)
            ->setCellValue('F'.$i, $totaltareasuaA2)
            ->setCellValue('G'.$i, $totaltareasuaR)
            ->setCellValue('H'.$i, $totaltareasuaV1)
            ->setCellValue('I'.$i, $totaltareasuaV2)
            ->setCellValue('J'.$i, $totaltareasuaCm)
            ->setCellValue('K'.$i, $totaltareasuaCt)
            ->setCellValue('L'.$i, $totaltareasua)
            ->setCellValue('M'.$i, $totalporctareascorpua)
            ->setCellValue('P'.$i, $totaltareasurA)
            ->setCellValue('Q'.$i, $totaltareasurA2)
            ->setCellValue('R'.$i, $totaltareasurR)
            ->setCellValue('S'.$i, $totaltareasurV1)
            ->setCellValue('T'.$i, $totaltareasurV2)
            ->setCellValue('U'.$i, $totaltareasurCm)
            ->setCellValue('V'.$i, $totaltareasurCt)
            ->setCellValue('W'.$i, $totaltareasur)
            ->setCellValue('X'.$i, $totalporctareascorpur);   
        $divisorUA = ($totaltareasuaR+$totaltareasuaV1+$totaltareasuaV2+$totaltareasuaCm+$totaltareasuaCt);
        $divisorUR = ($totaltareasurR+$totaltareasurV1+$totaltareasurV2+$totaltareasurCm+$totaltareasurCt);

        if($divisorUA != 0)
        {
            $getActiveSheet
                ->setCellValue('N'.$i, (($totaltareasuaCm+$totaltareasuaCt)/$divisorUA));
        }
        else
        {
            $getActiveSheet->setCellValue('N'.$i, 0);
        }
        if($divisorUR != 0) 
        {
            $getActiveSheet
                ->setCellValue('Y'.$i, (($totaltareasurCm+$totaltareasurCt)/$divisorUR));
        }
        else
        {
            $getActiveSheet->setCellValue('Y'.$i, 0);
        }
         if($id_empresa != 1)
            {
                $getActiveSheet
                    ->setCellValue('M5', (($totaltareasuaCm+$totaltareasuaCt)/$divisorUA));
                $getActiveSheet
                    ->setCellValue('X5', (($totaltareasurCm+$totaltareasurCt)/$divisorUR));
            }
        $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloTituloTotal);
        $getActiveSheet->getStyle('E'.$i.':M'.$i)->applyFromArray($estiloTituloTotal);
        $getActiveSheet->getStyle('P'.$i.':X'.$i)->applyFromArray($estiloTituloTotal);
        $getActiveSheet->getStyle('N'.$i)->applyFromArray($estiloDatospCump);
        $getActiveSheet->getStyle('Y'.$i)->applyFromArray($estiloDatospCump);
        $i=$i+3;

        foreach($gcia as $linea)//TOTALIZO POR GERENCIAS NO CORPORATIVAS
        {
            if($linea['empresa'] == 2)
            {
                $totaltareasuaAg=$totaltareasuaAg+$linea['ua_q_tareas_A'];
                $totaltareasuaA2g=$totaltareasuaA2g+$linea['ua_q_tareas_A2'];
                $totaltareasuaRg=$totaltareasuaRg+$linea['ua_q_tareas_R'];
                $totaltareasuaV1g=$totaltareasuaV1g+$linea['ua_q_tareas_V1'];
                $totaltareasuaV2g=$totaltareasuaV2g+$linea['ua_q_tareas_V2'];
                $totaltareasuaCmg=$totaltareasuaCmg+$linea['ua_q_tareas_Cm'];
                $totaltareasuaCtg=$totaltareasuaCtg+$linea['ua_q_tareas_Ct'];
                $totaltareasuag=$totaltareasuag+$linea['ua_q_tareas_t'];
                $totaltareasurAg=$totaltareasurAg+$linea['ur_q_tareas_A'];
                $totaltareasurA2g=$totaltareasurA2g+$linea['ur_q_tareas_A2'];
                $totaltareasurRg=$totaltareasurRg+$linea['ur_q_tareas_R'];
                $totaltareasurV1g=$totaltareasurV1g+$linea['ur_q_tareas_V1'];
                $totaltareasurV2g=$totaltareasurV2g+$linea['ur_q_tareas_V2'];
                $totaltareasurCmg=$totaltareasurCmg+$linea['ur_q_tareas_Cm'];
                $totaltareasurCtg=$totaltareasurCtg+$linea['ur_q_tareas_Ct'];
                $totaltareasurg=$totaltareasurg+$linea['ur_q_tareas_t'];
            }
            else
            {
                $totaltareasuaAgb=$totaltareasuaAgb+$linea['ua_q_tareas_A'];
                $totaltareasuaA2gb=$totaltareasuaA2gb+$linea['ua_q_tareas_A2'];
                $totaltareasuaRgb=$totaltareasuaRgb+$linea['ua_q_tareas_R'];
                $totaltareasuaV1gb=$totaltareasuaV1gb+$linea['ua_q_tareas_V1'];
                $totaltareasuaV2gb=$totaltareasuaV2gb+$linea['ua_q_tareas_V2'];
                $totaltareasuaCmgb=$totaltareasuaCmgb+$linea['ua_q_tareas_Cm'];
                $totaltareasuaCtgb=$totaltareasuaCtgb+$linea['ua_q_tareas_Ct'];
                $totaltareasuagb=$totaltareasuagb+$linea['ua_q_tareas_t'];
                $totaltareasurAgb=$totaltareasurAgb+$linea['ur_q_tareas_A'];
                $totaltareasurA2gb=$totaltareasurA2gb+$linea['ur_q_tareas_A2'];
                $totaltareasurRgb=$totaltareasurRgb+$linea['ur_q_tareas_R'];
                $totaltareasurV1gb=$totaltareasurV1gb+$linea['ur_q_tareas_V1'];
                $totaltareasurV2gb=$totaltareasurV2gb+$linea['ur_q_tareas_V2'];
                $totaltareasurCmgb=$totaltareasurCmgb+$linea['ur_q_tareas_Cm'];
                $totaltareasurCtgb=$totaltareasurCtgb+$linea['ur_q_tareas_Ct'];
                $totaltareasurgb=$totaltareasurgb+$linea['ur_q_tareas_t'];
            }
        }
        
        if($id_empresa == 1 || $id_empresa == 2)
        {
        //SALES DE JUJUY
            $getActiveSheet
                ->mergeCells('A'.$i.':D'.$i)
                ->setCellValue('A'.$i, 'Gerencias No Corporativas');
            $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloTitulosAreasNoCorp);
            $i++;

            $getActiveSheet
                ->mergeCells('A'.$i.':D'.$i)
                ->setCellValue('A'.$i, 'Sales de Jujuy');
            $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloTitulosAreasNoCorp);
            $i++;

            foreach($gcia as $valor)
            {
                if($valor['empresa'] == 2)
                {
                    $getActiveSheet
                        ->mergeCells('A'.$i.':D'.$i)
                        ->setCellValue('A'.$i,$valor['gerencia'])
                        ->setCellValue('E'.$i,$valor['ua_q_tareas_A'])
                        ->setCellValue('F'.$i,$valor['ua_q_tareas_A2'])
                        ->setCellValue('G'.$i,$valor['ua_q_tareas_R'])
                        ->setCellValue('H'.$i,$valor['ua_q_tareas_V1'])
                        ->setCellValue('I'.$i,$valor['ua_q_tareas_V2'])
                        ->setCellValue('J'.$i,$valor['ua_q_tareas_Cm'])
                        ->setCellValue('K'.$i,$valor['ua_q_tareas_Ct'])
                        ->setCellValue('L'.$i,$valor['ua_q_tareas_t'])
                        ->setCellValue('M'.$i,($valor['ua_q_tareas_t']/$totaltareasua))
                        ->setCellValue('P'.$i,$valor['ur_q_tareas_A'])
                        ->setCellValue('Q'.$i,$valor['ur_q_tareas_A2'])
                        ->setCellValue('R'.$i,$valor['ur_q_tareas_R'])
                        ->setCellValue('S'.$i,$valor['ur_q_tareas_V1'])
                        ->setCellValue('T'.$i,$valor['ur_q_tareas_V2'])
                        ->setCellValue('U'.$i,$valor['ur_q_tareas_Cm'])
                        ->setCellValue('V'.$i,$valor['ur_q_tareas_Ct'])
                        ->setCellValue('W'.$i,$valor['ur_q_tareas_t'])
                        ->setCellValue('X'.$i,($valor['ur_q_tareas_t']/$totaltareasur));
                $totalporctareasgciaua = $totalporctareasgciaua + ($valor['ua_q_tareas_t']/$totaltareasua);
                $totalporctareasgciaur = $totalporctareasgciaur + ($valor['ur_q_tareas_t']/$totaltareasur);
                $divisorUA = ($valor['ua_q_tareas_R']+$valor['ua_q_tareas_V1']+$valor['ua_q_tareas_V2']+$valor['ua_q_tareas_Cm']+$valor['ua_q_tareas_Ct']);
                $divisorUR = ($valor['ur_q_tareas_R']+$valor['ur_q_tareas_V1']+$valor['ur_q_tareas_V2']+$valor['ur_q_tareas_Cm']+$valor['ur_q_tareas_Ct']);

                if($divisorUA != 0)
                {
                    $getActiveSheet
                        ->setCellValue('N'.$i, (($valor['ua_q_tareas_Cm']+$valor['ua_q_tareas_Ct'])/$divisorUA));
                }
                else
                {
                    $getActiveSheet->setCellValue('N'.$i, 0);
                }
                if($divisorUR != 0) 
                {
                    $getActiveSheet
                        ->setCellValue('Y'.$i, (($valor['ur_q_tareas_Cm']+$valor['ur_q_tareas_Ct'])/$divisorUR));
                }
                else
                {
                    $getActiveSheet->setCellValue('Y'.$i, 0);
                }
                $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloGciaCorp);
                $getActiveSheet->getStyle('E'.$i)->applyFromArray($estiloDatosA);
                $getActiveSheet->getStyle('F'.$i)->applyFromArray($estiloDatosA2);
                $getActiveSheet->getStyle('G'.$i)->applyFromArray($estiloDatosR);
                $getActiveSheet->getStyle('H'.$i)->applyFromArray($estiloDatosV1);
                $getActiveSheet->getStyle('I'.$i)->applyFromArray($estiloDatosV2);
                $getActiveSheet->getStyle('J'.$i)->applyFromArray($estiloDatosCm);
                $getActiveSheet->getStyle('K'.$i)->applyFromArray($estiloDatosCt);
                $getActiveSheet->getStyle('L'.$i)->applyFromArray($estiloDatosTot);
                $getActiveSheet->getStyle('M'.$i)->applyFromArray($estiloDatospTot);
                $getActiveSheet->getStyle('N'.$i)->applyFromArray($estiloDatospCump);
                $getActiveSheet->getStyle('P'.$i)->applyFromArray($estiloDatosA);
                $getActiveSheet->getStyle('Q'.$i)->applyFromArray($estiloDatosA2);
                $getActiveSheet->getStyle('R'.$i)->applyFromArray($estiloDatosR);
                $getActiveSheet->getStyle('S'.$i)->applyFromArray($estiloDatosV1);
                $getActiveSheet->getStyle('T'.$i)->applyFromArray($estiloDatosV2);
                $getActiveSheet->getStyle('U'.$i)->applyFromArray($estiloDatosCm);
                $getActiveSheet->getStyle('V'.$i)->applyFromArray($estiloDatosCt);
                $getActiveSheet->getStyle('W'.$i)->applyFromArray($estiloDatosTot);
                $getActiveSheet->getStyle('X'.$i)->applyFromArray($estiloDatospTot);
                $getActiveSheet->getStyle('Y'.$i)->applyFromArray($estiloDatospCump);
                    $i++;
                }
            }
            $this->excel->getActiveSheet()//totales
                ->mergeCells('A'.$i.':D'.$i)
                ->setCellValue('A'.$i, 'Total')
                ->setCellValue('E'.$i, $totaltareasuaAg)
                ->setCellValue('F'.$i, $totaltareasuaA2g)
                ->setCellValue('G'.$i, $totaltareasuaRg)
                ->setCellValue('H'.$i, $totaltareasuaV1g)
                ->setCellValue('I'.$i, $totaltareasuaV2g)
                ->setCellValue('J'.$i, $totaltareasuaCmg)
                ->setCellValue('K'.$i, $totaltareasuaCtg)
                ->setCellValue('L'.$i, $totaltareasuag)
                ->setCellValue('M'.$i, $totalporctareasgciaua)
                ->setCellValue('P'.$i, $totaltareasurAg)
                ->setCellValue('Q'.$i, $totaltareasurA2g)
                ->setCellValue('R'.$i, $totaltareasurRg)
                ->setCellValue('S'.$i, $totaltareasurV1g)
                ->setCellValue('T'.$i, $totaltareasurV2g)
                ->setCellValue('U'.$i, $totaltareasurCmg)
                ->setCellValue('V'.$i, $totaltareasurCtg)
                ->setCellValue('W'.$i, $totaltareasurg)
                ->setCellValue('X'.$i, $totalporctareasgciaur); 
            $divisorUA = ($totaltareasuaRg+$totaltareasuaV1g+$totaltareasuaV2g+$totaltareasuaCmg+$totaltareasuaCtg);
            $divisorUR = ($totaltareasurRg+$totaltareasurV1g+$totaltareasurV2g+$totaltareasurCmg+$totaltareasurCtg);

            if($divisorUA != 0)
            {
                $getActiveSheet
                    ->setCellValue('N'.$i, (($totaltareasuaCmg+$totaltareasuaCtg)/$divisorUA));
            }
            else
            {
                $getActiveSheet->setCellValue('N'.$i, 0);
            }
            if($divisorUR != 0) 
            {
                $getActiveSheet
                    ->setCellValue('Y'.$i, (($totaltareasurCmg+$totaltareasurCtg)/$divisorUR));
            }
            else
            {
                $getActiveSheet->setCellValue('Y'.$i, 0);
            }
            $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloTituloTotal);
            $getActiveSheet->getStyle('E'.$i.':M'.$i)->applyFromArray($estiloTituloTotal);
            $getActiveSheet->getStyle('P'.$i.':X'.$i)->applyFromArray($estiloTituloTotal);
            $getActiveSheet->getStyle('N'.$i)->applyFromArray($estiloDatospCump);
            $getActiveSheet->getStyle('Y'.$i)->applyFromArray($estiloDatospCump);
            $i=$i+3;
        }
        
        if($id_empresa == 1 || $id_empresa == 3)
        {
            //BORAX ARGENTINA
            $getActiveSheet
                ->mergeCells('A'.$i.':D'.$i)
                ->setCellValue('A'.$i, 'Gerencias No Corporativas');
            $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloTitulosAreasNoCorp);
            $i++;

            $getActiveSheet
                ->mergeCells('A'.$i.':D'.$i)
                ->setCellValue('A'.$i, 'Borax Argentina');
            $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloTitulosAreasNoCorp);
            $i++;
            foreach($gcia as $valor)
            {
                if($valor['empresa'] == 3)
                {
                    $getActiveSheet
                        ->mergeCells('A'.$i.':D'.$i)
                        ->setCellValue('A'.$i,$valor['gerencia'])
                        ->setCellValue('E'.$i,$valor['ua_q_tareas_A'])
                        ->setCellValue('F'.$i,$valor['ua_q_tareas_A2'])
                        ->setCellValue('G'.$i,$valor['ua_q_tareas_R'])
                        ->setCellValue('H'.$i,$valor['ua_q_tareas_V1'])
                        ->setCellValue('I'.$i,$valor['ua_q_tareas_V2'])
                        ->setCellValue('J'.$i,$valor['ua_q_tareas_Cm'])
                        ->setCellValue('K'.$i,$valor['ua_q_tareas_Ct'])
                        ->setCellValue('L'.$i,$valor['ua_q_tareas_t'])
                        ->setCellValue('M'.$i,($valor['ua_q_tareas_t']/$totaltareasua))
                        ->setCellValue('P'.$i,$valor['ur_q_tareas_A'])
                        ->setCellValue('Q'.$i,$valor['ur_q_tareas_A2'])
                        ->setCellValue('R'.$i,$valor['ur_q_tareas_R'])
                        ->setCellValue('S'.$i,$valor['ur_q_tareas_V1'])
                        ->setCellValue('T'.$i,$valor['ur_q_tareas_V2'])
                        ->setCellValue('U'.$i,$valor['ur_q_tareas_Cm'])
                        ->setCellValue('V'.$i,$valor['ur_q_tareas_Ct'])
                        ->setCellValue('W'.$i,$valor['ur_q_tareas_t'])
                        ->setCellValue('X'.$i,($valor['ur_q_tareas_t']/$totaltareasur));
                $totalporctareasgciauab = $totalporctareasgciauab + ($valor['ua_q_tareas_t']/$totaltareasua);
                $totalporctareasgciaurb = $totalporctareasgciaurb + ($valor['ur_q_tareas_t']/$totaltareasur);
                $divisorUA = ($valor['ua_q_tareas_R']+$valor['ua_q_tareas_V1']+$valor['ua_q_tareas_V2']+$valor['ua_q_tareas_Cm']+$valor['ua_q_tareas_Ct']);
                $divisorUR = ($valor['ur_q_tareas_R']+$valor['ur_q_tareas_V1']+$valor['ur_q_tareas_V2']+$valor['ur_q_tareas_Cm']+$valor['ur_q_tareas_Ct']);

                if($divisorUA != 0)
                {
                    $getActiveSheet
                        ->setCellValue('N'.$i, (($valor['ua_q_tareas_Cm']+$valor['ua_q_tareas_Ct'])/$divisorUA));
                }
                else
                {
                    $getActiveSheet->setCellValue('N'.$i, 0);
                }
                if($divisorUR != 0) 
                {
                    $getActiveSheet
                        ->setCellValue('Y'.$i, (($valor['ur_q_tareas_Cm']+$valor['ur_q_tareas_Ct'])/$divisorUR));
                }
                else
                {
                    $getActiveSheet->setCellValue('Y'.$i, 0);
                }
                $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloGciaCorp);
                $getActiveSheet->getStyle('E'.$i)->applyFromArray($estiloDatosA);
                $getActiveSheet->getStyle('F'.$i)->applyFromArray($estiloDatosA2);
                $getActiveSheet->getStyle('G'.$i)->applyFromArray($estiloDatosR);
                $getActiveSheet->getStyle('H'.$i)->applyFromArray($estiloDatosV1);
                $getActiveSheet->getStyle('I'.$i)->applyFromArray($estiloDatosV2);
                $getActiveSheet->getStyle('J'.$i)->applyFromArray($estiloDatosCm);
                $getActiveSheet->getStyle('K'.$i)->applyFromArray($estiloDatosCt);
                $getActiveSheet->getStyle('L'.$i)->applyFromArray($estiloDatosTot);
                $getActiveSheet->getStyle('M'.$i)->applyFromArray($estiloDatospTot);
                $getActiveSheet->getStyle('N'.$i)->applyFromArray($estiloDatospCump);
                $getActiveSheet->getStyle('P'.$i)->applyFromArray($estiloDatosA);
                $getActiveSheet->getStyle('Q'.$i)->applyFromArray($estiloDatosA2);
                $getActiveSheet->getStyle('R'.$i)->applyFromArray($estiloDatosR);
                $getActiveSheet->getStyle('S'.$i)->applyFromArray($estiloDatosV1);
                $getActiveSheet->getStyle('T'.$i)->applyFromArray($estiloDatosV2);
                $getActiveSheet->getStyle('U'.$i)->applyFromArray($estiloDatosCm);
                $getActiveSheet->getStyle('V'.$i)->applyFromArray($estiloDatosCt);
                $getActiveSheet->getStyle('W'.$i)->applyFromArray($estiloDatosTot);
                $getActiveSheet->getStyle('X'.$i)->applyFromArray($estiloDatospTot);
                $getActiveSheet->getStyle('Y'.$i)->applyFromArray($estiloDatospCump);
                $i++;
                }
            }
            $this->excel->getActiveSheet()//imprimo totales
                ->mergeCells('A'.$i.':D'.$i)
                ->setCellValue('A'.$i, 'Total')
                ->setCellValue('E'.$i, $totaltareasuaAgb)
                ->setCellValue('F'.$i, $totaltareasuaA2gb)
                ->setCellValue('G'.$i, $totaltareasuaRgb)
                ->setCellValue('H'.$i, $totaltareasuaV1gb)
                ->setCellValue('I'.$i, $totaltareasuaV2gb)
                ->setCellValue('J'.$i, $totaltareasuaCmgb)
                ->setCellValue('K'.$i, $totaltareasuaCtgb)
                ->setCellValue('L'.$i, $totaltareasuagb)
                ->setCellValue('M'.$i, $totalporctareasgciauab)
                ->setCellValue('P'.$i, $totaltareasurAgb)
                ->setCellValue('Q'.$i, $totaltareasurA2gb)
                ->setCellValue('R'.$i, $totaltareasurRgb)
                ->setCellValue('S'.$i, $totaltareasurV1gb)
                ->setCellValue('T'.$i, $totaltareasurV2gb)
                ->setCellValue('U'.$i, $totaltareasurCmgb)
                ->setCellValue('V'.$i, $totaltareasurCtgb)
                ->setCellValue('W'.$i, $totaltareasurgb)
                ->setCellValue('X'.$i, $totalporctareasgciaurb);            
            $divisorUA = ($totaltareasuaRgb+$totaltareasuaV1gb+$totaltareasuaV2gb+$totaltareasuaCmgb+$totaltareasuaCtgb);
            $divisorUR = ($totaltareasurRgb+$totaltareasurV1gb+$totaltareasurV2gb+$totaltareasurCmgb+$totaltareasurCtgb);

            if($divisorUA != 0)
            {
                $getActiveSheet
                    ->setCellValue('N'.$i, (($totaltareasuaCmgb+$totaltareasuaCtgb)/$divisorUA));
            }
            else
            {
                $getActiveSheet->setCellValue('N'.$i, 0);
            }
            if($divisorUR != 0) 
            {
                $getActiveSheet
                    ->setCellValue('Y'.$i, (($totaltareasurCmgb+$totaltareasurCtgb)/$divisorUR));
            }
            else
            {
                $getActiveSheet->setCellValue('Y'.$i, 0);
            }
            $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloTituloTotal);
            $getActiveSheet->getStyle('E'.$i.':M'.$i)->applyFromArray($estiloTituloTotal);
            $getActiveSheet->getStyle('P'.$i.':X'.$i)->applyFromArray($estiloTituloTotal);
            $getActiveSheet->getStyle('N'.$i)->applyFromArray($estiloDatospCump);
            $getActiveSheet->getStyle('Y'.$i)->applyFromArray($estiloDatospCump);
            $i=$i+3;
        }

//      CARGA DE USUARIOS
        $this->excel->getActiveSheet()
            ->mergeCells('A'.$i.':D'.($i+2))
            ->setCellValue('A'.$i, 'Usuarios');
        $this->excel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($estiloTitulosAreas);
        $i = $i + 2;

        foreach($areas as $area)
        {
            if($id_empresa != 1)//suma el total de tareas dependiendo si el reporte es para ORO o no
            {
                $totales=$this->totalizaPorGerenciaCorp($usuarios,$id_empresa,$numeros,$area['id']);
                $i++;
                foreach($totales as $total)
                {
                    $this->excel->getActiveSheet()
                        ->mergeCells('A'.$i.':D'.$i)
                        ->setCellValue('A'.$i, $area['gerencia'])
                        ->setCellValue('E'.$i, $total['ua_q_tareas_A'])
                        ->setCellValue('F'.$i, $total['ua_q_tareas_A2'])
                        ->setCellValue('G'.$i, $total['ua_q_tareas_R'])
                        ->setCellValue('H'.$i, $total['ua_q_tareas_V1'])
                        ->setCellValue('I'.$i, $total['ua_q_tareas_V2'])
                        ->setCellValue('J'.$i, $total['ua_q_tareas_Cm'])
                        ->setCellValue('K'.$i, $total['ua_q_tareas_Ct'])
                        ->setCellValue('L'.$i, $total['ua_q_tareas_t'])
                        ->setCellValue('M'.$i, ($total['ua_q_tareas_t']/$totaltareasua))
                        ->setCellValue('P'.$i, $total['ur_q_tareas_A'])
                        ->setCellValue('Q'.$i, $total['ur_q_tareas_A2'])
                        ->setCellValue('R'.$i, $total['ur_q_tareas_V1'])
                        ->setCellValue('S'.$i, $total['ur_q_tareas_V2'])
                        ->setCellValue('T'.$i, $total['ur_q_tareas_R'])
                        ->setCellValue('U'.$i, $total['ur_q_tareas_Cm'])
                        ->setCellValue('V'.$i, $total['ur_q_tareas_Ct'])
                        ->setCellValue('W'.$i, $total['ur_q_tareas_t'])
                        ->setCellValue('X'.$i, ($total['ur_q_tareas_t']/$totaltareasua));
                    
                    $divisorUA = ($total['ua_q_tareas_R']+$total['ua_q_tareas_V1']+$total['ua_q_tareas_V2']+$total['ua_q_tareas_Cm']+$total['ua_q_tareas_Ct']);
                    $divisorUR = ($total['ur_q_tareas_R']+$total['ur_q_tareas_V1']+$total['ur_q_tareas_V2']+$total['ur_q_tareas_Cm']+$total['ur_q_tareas_Ct']);

                    if($divisorUA != 0)
                    {
                        $getActiveSheet
                            ->setCellValue('N'.$i, (($total['ua_q_tareas_Cm']+$total['ua_q_tareas_Ct'])/$divisorUA));
                    }
                    else
                    {
                        $getActiveSheet->setCellValue('N'.$i, 0);
                    }
                    if($divisorUR != 0) 
                    {
                        $getActiveSheet
                            ->setCellValue('Y'.$i, (($total['ur_q_tareas_Cm']+$total['ur_q_tareas_Ct'])/$divisorUR));
                    }
                    else
                    {
                        $getActiveSheet->setCellValue('Y'.$i, 0);
                    }
                    $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloGerenciasCorp);
                    $getActiveSheet->getStyle('E'.$i.':M'.$i)->applyFromArray($estiloDatosCorp);
                    $getActiveSheet->getStyle('P'.$i.':X'.$i)->applyFromArray($estiloDatosCorp);
                    $getActiveSheet->getStyle('N'.$i)->applyFromArray($estiloDatospCump);
                    $getActiveSheet->getStyle('Y'.$i)->applyFromArray($estiloDatospCump);
                    $i++;
                }
            }
            else
            {
                $i++;
                $this->excel->getActiveSheet()
                    ->mergeCells('A'.$i.':D'.$i)
                    ->setCellValue('A'.$i, $area['gerencia'])
                    ->setCellValue('E'.$i, $area['ua_q_tareas_A'])
                    ->setCellValue('F'.$i, $area['ua_q_tareas_A2'])
                    ->setCellValue('G'.$i, $area['ua_q_tareas_R'])
                    ->setCellValue('H'.$i, $area['ua_q_tareas_V1'])
                    ->setCellValue('I'.$i, $area['ua_q_tareas_V2'])
                    ->setCellValue('J'.$i, $area['ua_q_tareas_Cm'])
                    ->setCellValue('K'.$i, $area['ua_q_tareas_Ct'])
                    ->setCellValue('L'.$i, $area['ua_q_tareas_t'])
                    ->setCellValue('M'.$i, ($area['ua_q_tareas_t']/$totaltareasua))
                    ->setCellValue('P'.$i, $area['ur_q_tareas_A'])
                    ->setCellValue('Q'.$i, $area['ur_q_tareas_A2'])
                    ->setCellValue('R'.$i, $area['ur_q_tareas_V1'])
                    ->setCellValue('S'.$i, $area['ur_q_tareas_V2'])
                    ->setCellValue('T'.$i, $area['ur_q_tareas_R'])
                    ->setCellValue('U'.$i, $area['ur_q_tareas_Cm'])
                    ->setCellValue('V'.$i, $area['ur_q_tareas_Ct'])
                    ->setCellValue('W'.$i, $area['ur_q_tareas_t'])
                    ->setCellValue('X'.$i, ($area['ur_q_tareas_t']/$totaltareasua));

                $divisorUA = ($area['ua_q_tareas_R']+$area['ua_q_tareas_V1']+$area['ua_q_tareas_V2']+$area['ua_q_tareas_Cm']+$area['ua_q_tareas_Ct']);
                $divisorUR = ($area['ur_q_tareas_R']+$area['ur_q_tareas_V1']+$area['ur_q_tareas_V2']+$area['ur_q_tareas_Cm']+$area['ur_q_tareas_Ct']);

                if($divisorUA != 0)
                {
                    $getActiveSheet
                        ->setCellValue('N'.$i, (($area['ua_q_tareas_Cm']+$area['ua_q_tareas_Ct'])/$divisorUA));
                }
                else
                {
                    $getActiveSheet->setCellValue('N'.$i, 0);
                }
                if($divisorUR != 0) 
                {
                    $getActiveSheet
                        ->setCellValue('Y'.$i, (($area['ur_q_tareas_Cm']+$area['ur_q_tareas_Ct'])/$divisorUR));
                }
                else
                {
                    $getActiveSheet->setCellValue('Y'.$i, 0);
                }
                $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloGerenciasCorp);
                $getActiveSheet->getStyle('E'.$i.':M'.$i)->applyFromArray($estiloDatosCorp);
                $getActiveSheet->getStyle('P'.$i.':X'.$i)->applyFromArray($estiloDatosCorp);
                $getActiveSheet->getStyle('N'.$i)->applyFromArray($estiloDatospCump);
                $getActiveSheet->getStyle('Y'.$i)->applyFromArray($estiloDatospCump);
                $i++;
            }
            
            foreach($usuarios as $usuario)
            {
                if($id_empresa == 1)
                {
                    if($area['id'] == $usuario['id_corp'])
                    {
                        if(!($area['id'] == 17 || $area['id'] == 52))
                        {
                            $this->excel->getActiveSheet()
                                ->mergeCells('A'.$i.':D'.$i)
                                ->setCellValue('A'.$i, $usuario['usuario'])
                                ->setCellValue('E'.$i,$usuario['ua_q_tareas_A'])
                                ->setCellValue('F'.$i,$usuario['ua_q_tareas_A2'])
                                ->setCellValue('G'.$i,$usuario['ua_q_tareas_R'])
                                ->setCellValue('H'.$i,$usuario['ua_q_tareas_V1'])
                                ->setCellValue('I'.$i,$usuario['ua_q_tareas_V2'])
                                ->setCellValue('J'.$i,$usuario['ua_q_tareas_Cm'])
                                ->setCellValue('K'.$i,$usuario['ua_q_tareas_Ct'])
                                ->setCellValue('L'.$i,$usuario['ua_q_tareas_t'])
                                ->setCellValue('M'.$i,($usuario['ua_q_tareas_t']/$totaltareasua))
                                ->setCellValue('P'.$i,$usuario['ur_q_tareas_A'])
                                ->setCellValue('Q'.$i,$usuario['ur_q_tareas_A2'])
                                ->setCellValue('R'.$i,$usuario['ur_q_tareas_R'])
                                ->setCellValue('S'.$i,$usuario['ur_q_tareas_V1'])
                                ->setCellValue('T'.$i,$usuario['ur_q_tareas_V2'])
                                ->setCellValue('U'.$i,$usuario['ur_q_tareas_Cm'])
                                ->setCellValue('V'.$i,$usuario['ur_q_tareas_Ct'])
                                ->setCellValue('W'.$i,$usuario['ur_q_tareas_t'])
                                ->setCellValue('X'.$i,($usuario['ur_q_tareas_t']/$totaltareasur));
                            $divisorUA = ($usuario['ua_q_tareas_R']+$usuario['ua_q_tareas_V1']+$usuario['ua_q_tareas_V2']+$usuario['ua_q_tareas_Cm']+$usuario['ua_q_tareas_Ct']);
                            $divisorUR = ($usuario['ur_q_tareas_R']+$usuario['ur_q_tareas_V1']+$usuario['ur_q_tareas_V2']+$usuario['ur_q_tareas_Cm']+$usuario['ur_q_tareas_Ct']);

                            if($divisorUA != 0)
                            {
                                $getActiveSheet
                                    ->setCellValue('N'.$i, (($usuario['ua_q_tareas_Cm']+$usuario['ua_q_tareas_Ct'])/$divisorUA));
                            }
                            else
                            {
                                $getActiveSheet->setCellValue('N'.$i, 0);
                            }
                            if($divisorUR != 0) 
                            {
                                $getActiveSheet
                                    ->setCellValue('Y'.$i, (($usuario['ur_q_tareas_Cm']+$usuario['ur_q_tareas_Ct'])/$divisorUR));
                            }
                            else
                            {
                                $getActiveSheet->setCellValue('Y'.$i, 0);
                            }
                            $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloUsuario);
                            $getActiveSheet->getStyle('E'.$i)->applyFromArray($estiloDatosA);
                            $getActiveSheet->getStyle('F'.$i)->applyFromArray($estiloDatosA2);
                            $getActiveSheet->getStyle('G'.$i)->applyFromArray($estiloDatosR);
                            $getActiveSheet->getStyle('H'.$i)->applyFromArray($estiloDatosV1);
                            $getActiveSheet->getStyle('I'.$i)->applyFromArray($estiloDatosV2);
                            $getActiveSheet->getStyle('J'.$i)->applyFromArray($estiloDatosCm);
                            $getActiveSheet->getStyle('K'.$i)->applyFromArray($estiloDatosCt);
                            $getActiveSheet->getStyle('L'.$i)->applyFromArray($estiloDatosTot);
                            $getActiveSheet->getStyle('M'.$i)->applyFromArray($estiloDatospTot);
                            $getActiveSheet->getStyle('N'.$i)->applyFromArray($estiloDatospCump);
                            $getActiveSheet->getStyle('P'.$i)->applyFromArray($estiloDatosA);
                            $getActiveSheet->getStyle('Q'.$i)->applyFromArray($estiloDatosA2);
                            $getActiveSheet->getStyle('R'.$i)->applyFromArray($estiloDatosR);
                            $getActiveSheet->getStyle('S'.$i)->applyFromArray($estiloDatosV1);
                            $getActiveSheet->getStyle('T'.$i)->applyFromArray($estiloDatosV2);
                            $getActiveSheet->getStyle('U'.$i)->applyFromArray($estiloDatosCm);
                            $getActiveSheet->getStyle('V'.$i)->applyFromArray($estiloDatosCt);
                            $getActiveSheet->getStyle('W'.$i)->applyFromArray($estiloDatosTot);
                            $getActiveSheet->getStyle('X'.$i)->applyFromArray($estiloDatospTot);
                            $getActiveSheet->getStyle('Y'.$i)->applyFromArray($estiloDatospCump);
                            $i++;
                        }
                    }
                }
                else
                {
                    if($id_empresa ==2)
                    {
                        if($usuario['id_org'] == 1 || $usuario['id_org'] == 2 ||$usuario['id_org'] == 3)
                        {
                            if($area['id'] == $usuario['id_corp'])
                            {
                                if(!($area['id'] == 17 || $area['id'] == 52))
                                {
                                    $this->excel->getActiveSheet()
                                        ->mergeCells('A'.$i.':D'.$i)
                                        ->setCellValue('A'.$i, $usuario['usuario'])
                                        ->setCellValue('E'.$i,$usuario['ua_q_tareas_A'])
                                        ->setCellValue('F'.$i,$usuario['ua_q_tareas_A2'])
                                        ->setCellValue('G'.$i,$usuario['ua_q_tareas_R'])
                                        ->setCellValue('H'.$i,$usuario['ua_q_tareas_V1'])
                                        ->setCellValue('I'.$i,$usuario['ua_q_tareas_V2'])
                                        ->setCellValue('J'.$i,$usuario['ua_q_tareas_Cm'])
                                        ->setCellValue('K'.$i,$usuario['ua_q_tareas_Ct'])
                                        ->setCellValue('L'.$i,$usuario['ua_q_tareas_t'])
                                        ->setCellValue('M'.$i,($usuario['ua_q_tareas_t']/$totaltareasua))
                                        ->setCellValue('P'.$i,$usuario['ur_q_tareas_A'])
                                        ->setCellValue('Q'.$i,$usuario['ur_q_tareas_A2'])
                                        ->setCellValue('R'.$i,$usuario['ur_q_tareas_R'])
                                        ->setCellValue('S'.$i,$usuario['ur_q_tareas_V1'])
                                        ->setCellValue('T'.$i,$usuario['ur_q_tareas_V2'])
                                        ->setCellValue('U'.$i,$usuario['ur_q_tareas_Cm'])
                                        ->setCellValue('V'.$i,$usuario['ur_q_tareas_Ct'])
                                        ->setCellValue('W'.$i,$usuario['ur_q_tareas_t'])
                                        ->setCellValue('X'.$i,($usuario['ur_q_tareas_t']/$totaltareasur));
                                    $divisorUA = ($usuario['ua_q_tareas_R']+$usuario['ua_q_tareas_V1']+$usuario['ua_q_tareas_V2']+$usuario['ua_q_tareas_Cm']+$usuario['ua_q_tareas_Ct']);
                                    $divisorUR = ($usuario['ur_q_tareas_R']+$usuario['ur_q_tareas_V1']+$usuario['ur_q_tareas_V2']+$usuario['ur_q_tareas_Cm']+$usuario['ur_q_tareas_Ct']);

                                    if($divisorUA != 0)
                                    {
                                        $getActiveSheet
                                            ->setCellValue('N'.$i, (($usuario['ua_q_tareas_Cm']+$usuario['ua_q_tareas_Ct'])/$divisorUA));
                                    }
                                    else
                                    {
                                        $getActiveSheet->setCellValue('N'.$i, 0);
                                    }
                                    if($divisorUR != 0) 
                                    {
                                        $getActiveSheet
                                            ->setCellValue('Y'.$i, (($usuario['ur_q_tareas_Cm']+$usuario['ur_q_tareas_Ct'])/$divisorUR));
                                    }
                                    else
                                    {
                                        $getActiveSheet->setCellValue('Y'.$i, 0);
                                    }
                                    $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloUsuario);
                                    $getActiveSheet->getStyle('E'.$i)->applyFromArray($estiloDatosA);
                                    $getActiveSheet->getStyle('F'.$i)->applyFromArray($estiloDatosA2);
                                    $getActiveSheet->getStyle('G'.$i)->applyFromArray($estiloDatosR);
                                    $getActiveSheet->getStyle('H'.$i)->applyFromArray($estiloDatosV1);
                                    $getActiveSheet->getStyle('I'.$i)->applyFromArray($estiloDatosV2);
                                    $getActiveSheet->getStyle('J'.$i)->applyFromArray($estiloDatosCm);
                                    $getActiveSheet->getStyle('K'.$i)->applyFromArray($estiloDatosCt);
                                    $getActiveSheet->getStyle('L'.$i)->applyFromArray($estiloDatosTot);
                                    $getActiveSheet->getStyle('M'.$i)->applyFromArray($estiloDatospTot);
                                    $getActiveSheet->getStyle('N'.$i)->applyFromArray($estiloDatospCump);
                                    $getActiveSheet->getStyle('P'.$i)->applyFromArray($estiloDatosA);
                                    $getActiveSheet->getStyle('Q'.$i)->applyFromArray($estiloDatosA2);
                                    $getActiveSheet->getStyle('R'.$i)->applyFromArray($estiloDatosR);
                                    $getActiveSheet->getStyle('S'.$i)->applyFromArray($estiloDatosV1);
                                    $getActiveSheet->getStyle('T'.$i)->applyFromArray($estiloDatosV2);
                                    $getActiveSheet->getStyle('U'.$i)->applyFromArray($estiloDatosCm);
                                    $getActiveSheet->getStyle('V'.$i)->applyFromArray($estiloDatosCt);
                                    $getActiveSheet->getStyle('W'.$i)->applyFromArray($estiloDatosTot);
                                    $getActiveSheet->getStyle('X'.$i)->applyFromArray($estiloDatospTot);
                                    $getActiveSheet->getStyle('Y'.$i)->applyFromArray($estiloDatospCump);
                                    $i++;
                                }
                            }
                        }
                    }
                    if($id_empresa == 3)
                    {
                        if($usuario['id_org'] == 1 || $usuario['id_org'] == 4 ||$usuario['id_org'] == 5)
                        {
                            if($area['id'] == $usuario['id_corp'])
                            {
                                if(!($area['id'] == 17 || $area['id'] == 52))
                                {

                                    $this->excel->getActiveSheet()
                                        ->mergeCells('A'.$i.':D'.$i)
                                        ->setCellValue('A'.$i, $usuario['usuario'])
                                        ->setCellValue('E'.$i,$usuario['ua_q_tareas_A'])
                                        ->setCellValue('F'.$i,$usuario['ua_q_tareas_A2'])
                                        ->setCellValue('G'.$i,$usuario['ua_q_tareas_R'])
                                        ->setCellValue('H'.$i,$usuario['ua_q_tareas_V1'])
                                        ->setCellValue('I'.$i,$usuario['ua_q_tareas_V2'])
                                        ->setCellValue('J'.$i,$usuario['ua_q_tareas_Cm'])
                                        ->setCellValue('K'.$i,$usuario['ua_q_tareas_Ct'])
                                        ->setCellValue('L'.$i,$usuario['ua_q_tareas_t'])
                                        ->setCellValue('M'.$i,($usuario['ua_q_tareas_t']/$totaltareasua))
                                        ->setCellValue('P'.$i,$usuario['ur_q_tareas_A'])
                                        ->setCellValue('Q'.$i,$usuario['ur_q_tareas_A2'])
                                        ->setCellValue('R'.$i,$usuario['ur_q_tareas_R'])
                                        ->setCellValue('S'.$i,$usuario['ur_q_tareas_V1'])
                                        ->setCellValue('T'.$i,$usuario['ur_q_tareas_V2'])
                                        ->setCellValue('U'.$i,$usuario['ur_q_tareas_Cm'])
                                        ->setCellValue('V'.$i,$usuario['ur_q_tareas_Ct'])
                                        ->setCellValue('W'.$i,$usuario['ur_q_tareas_t'])
                                        ->setCellValue('X'.$i,($usuario['ur_q_tareas_t']/$totaltareasur));
                                    $divisorUA = ($usuario['ua_q_tareas_R']+$usuario['ua_q_tareas_V1']+$usuario['ua_q_tareas_V2']+$usuario['ua_q_tareas_Cm']+$usuario['ua_q_tareas_Ct']);
                                    $divisorUR = ($usuario['ur_q_tareas_R']+$usuario['ur_q_tareas_V1']+$usuario['ur_q_tareas_V2']+$usuario['ur_q_tareas_Cm']+$usuario['ur_q_tareas_Ct']);

                                    if($divisorUA != 0)
                                    {
                                        $getActiveSheet
                                            ->setCellValue('N'.$i, (($usuario['ua_q_tareas_Cm']+$usuario['ua_q_tareas_Ct'])/$divisorUA));
                                    }
                                    else
                                    {
                                        $getActiveSheet->setCellValue('N'.$i, 0);
                                    }
                                    if($divisorUR != 0) 
                                    {
                                        $getActiveSheet
                                            ->setCellValue('Y'.$i, (($usuario['ur_q_tareas_Cm']+$usuario['ur_q_tareas_Ct'])/$divisorUR));
                                    }
                                    else
                                    {
                                        $getActiveSheet->setCellValue('Y'.$i, 0);
                                    }
                                    $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloUsuario);
                                    $getActiveSheet->getStyle('E'.$i)->applyFromArray($estiloDatosA);
                                    $getActiveSheet->getStyle('F'.$i)->applyFromArray($estiloDatosA2);
                                    $getActiveSheet->getStyle('G'.$i)->applyFromArray($estiloDatosR);
                                    $getActiveSheet->getStyle('H'.$i)->applyFromArray($estiloDatosV1);
                                    $getActiveSheet->getStyle('I'.$i)->applyFromArray($estiloDatosV2);
                                    $getActiveSheet->getStyle('J'.$i)->applyFromArray($estiloDatosCm);
                                    $getActiveSheet->getStyle('K'.$i)->applyFromArray($estiloDatosCt);
                                    $getActiveSheet->getStyle('L'.$i)->applyFromArray($estiloDatosTot);
                                    $getActiveSheet->getStyle('M'.$i)->applyFromArray($estiloDatospTot);
                                    $getActiveSheet->getStyle('N'.$i)->applyFromArray($estiloDatospCump);
                                    $getActiveSheet->getStyle('P'.$i)->applyFromArray($estiloDatosA);
                                    $getActiveSheet->getStyle('Q'.$i)->applyFromArray($estiloDatosA2);
                                    $getActiveSheet->getStyle('R'.$i)->applyFromArray($estiloDatosR);
                                    $getActiveSheet->getStyle('S'.$i)->applyFromArray($estiloDatosV1);
                                    $getActiveSheet->getStyle('T'.$i)->applyFromArray($estiloDatosV2);
                                    $getActiveSheet->getStyle('U'.$i)->applyFromArray($estiloDatosCm);
                                    $getActiveSheet->getStyle('V'.$i)->applyFromArray($estiloDatosCt);
                                    $getActiveSheet->getStyle('W'.$i)->applyFromArray($estiloDatosTot);
                                    $getActiveSheet->getStyle('X'.$i)->applyFromArray($estiloDatospTot);
                                    $getActiveSheet->getStyle('Y'.$i)->applyFromArray($estiloDatospCump);
                                    $i++;
                                }
                            }
                        }
                    }
                }
            }
            
            if($id_empresa == 1)
            {
                if(array_key_exists('gciaLinea', $area))
                {
                    foreach($area['gciaLinea'] as $ger)
                    {
                       $this->excel->getActiveSheet()
                            ->mergeCells('A'.$i.':D'.$i)
                            ->setCellValue('A'.$i, $ger['gerenciaLinea'])
                            ->setCellValue('E'.$i, $ger['ua_q_tareas_A'])
                            ->setCellValue('F'.$i, $ger['ua_q_tareas_A2'])
                            ->setCellValue('G'.$i, $ger['ua_q_tareas_R'])
                            ->setCellValue('H'.$i, $ger['ua_q_tareas_V1'])
                            ->setCellValue('I'.$i, $ger['ua_q_tareas_V2'])
                            ->setCellValue('J'.$i, $ger['ua_q_tareas_Cm'])
                            ->setCellValue('K'.$i, $ger['ua_q_tareas_Ct'])
                            ->setCellValue('L'.$i, $ger['ua_q_tareas_t'])
                            ->setCellValue('M'.$i, ($ger['ua_q_tareas_t']/$totaltareasua))
                            ->setCellValue('P'.$i, $ger['ur_q_tareas_A'])
                            ->setCellValue('Q'.$i, $ger['ur_q_tareas_A2'])
                            ->setCellValue('R'.$i, $ger['ur_q_tareas_V1'])
                            ->setCellValue('S'.$i, $ger['ur_q_tareas_V2'])
                            ->setCellValue('T'.$i, $ger['ur_q_tareas_R'])
                            ->setCellValue('U'.$i, $ger['ur_q_tareas_Cm'])
                            ->setCellValue('V'.$i, $ger['ur_q_tareas_Ct'])
                            ->setCellValue('W'.$i, $ger['ur_q_tareas_t'])
                            ->setCellValue('X'.$i, ($ger['ur_q_tareas_t']/$totaltareasua));
                        $divisorUA = ($ger['ua_q_tareas_R']+$ger['ua_q_tareas_V1']+$ger['ua_q_tareas_V2']+$ger['ua_q_tareas_Cm']+$ger['ua_q_tareas_Ct']);
                        $divisorUR = ($ger['ur_q_tareas_R']+$ger['ur_q_tareas_V1']+$ger['ur_q_tareas_V2']+$ger['ur_q_tareas_Cm']+$ger['ur_q_tareas_Ct']);

                        if($divisorUA != 0)
                        {
                            $getActiveSheet
                                ->setCellValue('N'.$i, (($ger['ua_q_tareas_Cm']+$ger['ua_q_tareas_Ct'])/$divisorUA));
                        }
                        else
                        {
                            $getActiveSheet->setCellValue('N'.$i, 0);
                        }
                        if($divisorUR != 0) 
                        {
                            $getActiveSheet
                                ->setCellValue('Y'.$i, (($ger['ur_q_tareas_Cm']+$ger['ur_q_tareas_Ct'])/$divisorUR));
                        }
                        else
                        {
                            $getActiveSheet->setCellValue('Y'.$i, 0);
                        }
                        $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloGerencias);
                        $getActiveSheet->getStyle('E'.$i.':M'.$i)->applyFromArray($estiloDatosGciaGral);
                        $getActiveSheet->getStyle('P'.$i.':X'.$i)->applyFromArray($estiloDatosGciaGral);
                        $getActiveSheet->getStyle('N'.$i)->applyFromArray($estiloDatospCump);
                        $getActiveSheet->getStyle('Y'.$i)->applyFromArray($estiloDatospCump);
                        $i++;
                        foreach($usuarios as $usuario)
                        {
                            if($ger['id'] == $usuario['id_gcia'])
                            {
                                $this->excel->getActiveSheet()
                                    ->mergeCells('A'.$i.':D'.$i)
                                    ->setCellValue('A'.$i, $usuario['usuario'])
                                    ->setCellValue('E'.$i,$usuario['ua_q_tareas_A'])
                                    ->setCellValue('F'.$i,$usuario['ua_q_tareas_A2'])
                                    ->setCellValue('G'.$i,$usuario['ua_q_tareas_R'])
                                    ->setCellValue('H'.$i,$usuario['ua_q_tareas_V1'])
                                    ->setCellValue('I'.$i,$usuario['ua_q_tareas_V2'])
                                    ->setCellValue('J'.$i,$usuario['ua_q_tareas_Cm'])
                                    ->setCellValue('K'.$i,$usuario['ua_q_tareas_Ct'])
                                    ->setCellValue('L'.$i,$usuario['ua_q_tareas_t'])
                                    ->setCellValue('M'.$i,($usuario['ua_q_tareas_t']/$totaltareasua))
                                    ->setCellValue('P'.$i,$usuario['ur_q_tareas_A'])
                                    ->setCellValue('Q'.$i,$usuario['ur_q_tareas_A2'])
                                    ->setCellValue('R'.$i,$usuario['ur_q_tareas_R'])
                                    ->setCellValue('S'.$i,$usuario['ur_q_tareas_V1'])
                                    ->setCellValue('T'.$i,$usuario['ur_q_tareas_V2'])
                                    ->setCellValue('U'.$i,$usuario['ur_q_tareas_Cm'])
                                    ->setCellValue('V'.$i,$usuario['ur_q_tareas_Ct'])
                                    ->setCellValue('W'.$i,$usuario['ur_q_tareas_t'])
                                    ->setCellValue('X'.$i,($usuario['ur_q_tareas_t']/$totaltareasur));
                                $divisorUA = ($usuario['ua_q_tareas_R']+$usuario['ua_q_tareas_V1']+$usuario['ua_q_tareas_V2']+$usuario['ua_q_tareas_Cm']+$usuario['ua_q_tareas_Ct']);
                                $divisorUR = ($usuario['ur_q_tareas_R']+$usuario['ur_q_tareas_V1']+$usuario['ur_q_tareas_V2']+$usuario['ur_q_tareas_Cm']+$usuario['ur_q_tareas_Ct']);

                                if($divisorUA != 0)
                                {
                                    $getActiveSheet
                                        ->setCellValue('N'.$i, (($usuario['ua_q_tareas_Cm']+$usuario['ua_q_tareas_Ct'])/$divisorUA));
                                }
                                else
                                {
                                    $getActiveSheet->setCellValue('N'.$i, 0);
                                }
                                if($divisorUR != 0) 
                                {
                                    $getActiveSheet
                                        ->setCellValue('Y'.$i, (($usuario['ur_q_tareas_Cm']+$usuario['ur_q_tareas_Ct'])/$divisorUR));
                                }
                                else
                                {
                                    $getActiveSheet->setCellValue('Y'.$i, 0);
                                }
                                $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloUsuario);
                                $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloUsuario);
                                $getActiveSheet->getStyle('E'.$i)->applyFromArray($estiloDatosA);
                                $getActiveSheet->getStyle('F'.$i)->applyFromArray($estiloDatosA2);
                                $getActiveSheet->getStyle('G'.$i)->applyFromArray($estiloDatosR);
                                $getActiveSheet->getStyle('H'.$i)->applyFromArray($estiloDatosV1);
                                $getActiveSheet->getStyle('I'.$i)->applyFromArray($estiloDatosV2);
                                $getActiveSheet->getStyle('J'.$i)->applyFromArray($estiloDatosCm);
                                $getActiveSheet->getStyle('K'.$i)->applyFromArray($estiloDatosCt);
                                $getActiveSheet->getStyle('L'.$i)->applyFromArray($estiloDatosTot);
                                $getActiveSheet->getStyle('M'.$i)->applyFromArray($estiloDatospTot);
                                $getActiveSheet->getStyle('N'.$i)->applyFromArray($estiloDatospCump);
                                $getActiveSheet->getStyle('P'.$i)->applyFromArray($estiloDatosA);
                                $getActiveSheet->getStyle('Q'.$i)->applyFromArray($estiloDatosA2);
                                $getActiveSheet->getStyle('R'.$i)->applyFromArray($estiloDatosR);
                                $getActiveSheet->getStyle('S'.$i)->applyFromArray($estiloDatosV1);
                                $getActiveSheet->getStyle('T'.$i)->applyFromArray($estiloDatosV2);
                                $getActiveSheet->getStyle('U'.$i)->applyFromArray($estiloDatosCm);
                                $getActiveSheet->getStyle('V'.$i)->applyFromArray($estiloDatosCt);
                                $getActiveSheet->getStyle('W'.$i)->applyFromArray($estiloDatosTot);
                                $getActiveSheet->getStyle('X'.$i)->applyFromArray($estiloDatospTot);
                                $getActiveSheet->getStyle('Y'.$i)->applyFromArray($estiloDatospCump);
                                $i++;
                            }
                        }
                    }
                }
            }
            else
            {
                if($id_empresa == 1 || ($id_empresa == $area['id_empresa']&&$id_empresa==2))
                {
                    if(array_key_exists('gciaLinea', $area))
                    {
                        foreach($area['gciaLinea'] as $ger)
                        {
                           $this->excel->getActiveSheet()
                                ->mergeCells('A'.$i.':D'.$i)
                                ->setCellValue('A'.$i, $ger['gerenciaLinea'])
                                ->setCellValue('E'.$i, $ger['ua_q_tareas_A'])
                                ->setCellValue('F'.$i, $ger['ua_q_tareas_A2'])
                                ->setCellValue('G'.$i, $ger['ua_q_tareas_R'])
                                ->setCellValue('H'.$i, $ger['ua_q_tareas_V1'])
                                ->setCellValue('I'.$i, $ger['ua_q_tareas_V2'])
                                ->setCellValue('J'.$i, $ger['ua_q_tareas_Cm'])
                                ->setCellValue('K'.$i, $ger['ua_q_tareas_Ct'])
                                ->setCellValue('L'.$i, $ger['ua_q_tareas_t'])
                                ->setCellValue('M'.$i, ($ger['ua_q_tareas_t']/$totaltareasua))
                                ->setCellValue('P'.$i, $ger['ur_q_tareas_A'])
                                ->setCellValue('Q'.$i, $ger['ur_q_tareas_A2'])
                                ->setCellValue('R'.$i, $ger['ur_q_tareas_V1'])
                                ->setCellValue('S'.$i, $ger['ur_q_tareas_V2'])
                                ->setCellValue('T'.$i, $ger['ur_q_tareas_R'])
                                ->setCellValue('U'.$i, $ger['ur_q_tareas_Cm'])
                                ->setCellValue('V'.$i, $ger['ur_q_tareas_Ct'])
                                ->setCellValue('W'.$i, $ger['ur_q_tareas_t'])
                                ->setCellValue('X'.$i, ($ger['ur_q_tareas_t']/$totaltareasua));
                            $divisorUA = ($ger['ua_q_tareas_R']+$ger['ua_q_tareas_V1']+$ger['ua_q_tareas_V2']+$ger['ua_q_tareas_Cm']+$ger['ua_q_tareas_Ct']);
                            $divisorUR = ($ger['ur_q_tareas_R']+$ger['ur_q_tareas_V1']+$ger['ur_q_tareas_V2']+$ger['ur_q_tareas_Cm']+$ger['ur_q_tareas_Ct']);

                            if($divisorUA != 0)
                            {
                                $getActiveSheet
                                    ->setCellValue('N'.$i, (($ger['ua_q_tareas_Cm']+$ger['ua_q_tareas_Ct'])/$divisorUA));
                            }
                            else
                            {
                                $getActiveSheet->setCellValue('N'.$i, 0);
                            }
                            if($divisorUR != 0) 
                            {
                                $getActiveSheet
                                    ->setCellValue('Y'.$i, (($ger['ur_q_tareas_Cm']+$ger['ur_q_tareas_Ct'])/$divisorUR));
                            }
                            else
                            {
                                $getActiveSheet->setCellValue('Y'.$i, 0);
                            }
                            $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloGerencias);
                            $getActiveSheet->getStyle('E'.$i.':M'.$i)->applyFromArray($estiloDatosGciaGral);
                            $getActiveSheet->getStyle('P'.$i.':X'.$i)->applyFromArray($estiloDatosGciaGral);
                            $getActiveSheet->getStyle('N'.$i)->applyFromArray($estiloDatospCump);
                            $getActiveSheet->getStyle('Y'.$i)->applyFromArray($estiloDatospCump);
                            $i++;
                            foreach($usuarios as $usuario)
                            {
                                if($ger['id'] == $usuario['id_gcia'])
                                {
                                    $this->excel->getActiveSheet()
                                        ->mergeCells('A'.$i.':D'.$i)
                                        ->setCellValue('A'.$i, $usuario['usuario'])
                                        ->setCellValue('E'.$i,$usuario['ua_q_tareas_A'])
                                        ->setCellValue('F'.$i,$usuario['ua_q_tareas_A2'])
                                        ->setCellValue('G'.$i,$usuario['ua_q_tareas_R'])
                                        ->setCellValue('H'.$i,$usuario['ua_q_tareas_V1'])
                                        ->setCellValue('I'.$i,$usuario['ua_q_tareas_V2'])
                                        ->setCellValue('J'.$i,$usuario['ua_q_tareas_Cm'])
                                        ->setCellValue('K'.$i,$usuario['ua_q_tareas_Ct'])
                                        ->setCellValue('L'.$i,$usuario['ua_q_tareas_t'])
                                        ->setCellValue('M'.$i,($usuario['ua_q_tareas_t']/$totaltareasua))
                                        ->setCellValue('P'.$i,$usuario['ur_q_tareas_A'])
                                        ->setCellValue('Q'.$i,$usuario['ur_q_tareas_A2'])
                                        ->setCellValue('R'.$i,$usuario['ur_q_tareas_R'])
                                        ->setCellValue('S'.$i,$usuario['ur_q_tareas_V1'])
                                        ->setCellValue('T'.$i,$usuario['ur_q_tareas_V2'])
                                        ->setCellValue('U'.$i,$usuario['ur_q_tareas_Cm'])
                                        ->setCellValue('V'.$i,$usuario['ur_q_tareas_Ct'])
                                        ->setCellValue('W'.$i,$usuario['ur_q_tareas_t'])
                                        ->setCellValue('X'.$i,($usuario['ur_q_tareas_t']/$totaltareasur));
                                    $divisorUA = ($usuario['ua_q_tareas_R']+$usuario['ua_q_tareas_V1']+$usuario['ua_q_tareas_V2']+$usuario['ua_q_tareas_Cm']+$usuario['ua_q_tareas_Ct']);
                                    $divisorUR = ($usuario['ur_q_tareas_R']+$usuario['ur_q_tareas_V1']+$usuario['ur_q_tareas_V2']+$usuario['ur_q_tareas_Cm']+$usuario['ur_q_tareas_Ct']);

                                    if($divisorUA != 0)
                                    {
                                        $getActiveSheet
                                            ->setCellValue('N'.$i, (($usuario['ua_q_tareas_Cm']+$usuario['ua_q_tareas_Ct'])/$divisorUA));
                                    }
                                    else
                                    {
                                        $getActiveSheet->setCellValue('N'.$i, 0);
                                    }
                                    if($divisorUR != 0) 
                                    {
                                        $getActiveSheet
                                            ->setCellValue('Y'.$i, (($usuario['ur_q_tareas_Cm']+$usuario['ur_q_tareas_Ct'])/$divisorUR));
                                    }
                                    else
                                    {
                                        $getActiveSheet->setCellValue('Y'.$i, 0);
                                    }
                                    $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloUsuario);
                                    $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloUsuario);
                                    $getActiveSheet->getStyle('E'.$i)->applyFromArray($estiloDatosA);
                                    $getActiveSheet->getStyle('F'.$i)->applyFromArray($estiloDatosA2);
                                    $getActiveSheet->getStyle('G'.$i)->applyFromArray($estiloDatosR);
                                    $getActiveSheet->getStyle('H'.$i)->applyFromArray($estiloDatosV1);
                                    $getActiveSheet->getStyle('I'.$i)->applyFromArray($estiloDatosV2);
                                    $getActiveSheet->getStyle('J'.$i)->applyFromArray($estiloDatosCm);
                                    $getActiveSheet->getStyle('K'.$i)->applyFromArray($estiloDatosCt);
                                    $getActiveSheet->getStyle('L'.$i)->applyFromArray($estiloDatosTot);
                                    $getActiveSheet->getStyle('M'.$i)->applyFromArray($estiloDatospTot);
                                    $getActiveSheet->getStyle('N'.$i)->applyFromArray($estiloDatospCump);
                                    $getActiveSheet->getStyle('P'.$i)->applyFromArray($estiloDatosA);
                                    $getActiveSheet->getStyle('Q'.$i)->applyFromArray($estiloDatosA2);
                                    $getActiveSheet->getStyle('R'.$i)->applyFromArray($estiloDatosR);
                                    $getActiveSheet->getStyle('S'.$i)->applyFromArray($estiloDatosV1);
                                    $getActiveSheet->getStyle('T'.$i)->applyFromArray($estiloDatosV2);
                                    $getActiveSheet->getStyle('U'.$i)->applyFromArray($estiloDatosCm);
                                    $getActiveSheet->getStyle('V'.$i)->applyFromArray($estiloDatosCt);
                                    $getActiveSheet->getStyle('W'.$i)->applyFromArray($estiloDatosTot);
                                    $getActiveSheet->getStyle('X'.$i)->applyFromArray($estiloDatospTot);
                                    $getActiveSheet->getStyle('Y'.$i)->applyFromArray($estiloDatospCump);
                                    $i++;
                                }
                            }
                        }
                    }
                }
                if($id_empresa == 1 || ($id_empresa == $area['id_empresa']&&$id_empresa==3))
                {
                    if(array_key_exists('gciaLinea', $area))
                    {
                        foreach($area['gciaLinea'] as $ger)
                        {
                           $this->excel->getActiveSheet()
                                ->mergeCells('A'.$i.':D'.$i)
                                ->setCellValue('A'.$i, $ger['gerenciaLinea'])
                                ->setCellValue('E'.$i, $ger['ua_q_tareas_A'])
                                ->setCellValue('F'.$i, $ger['ua_q_tareas_A2'])
                                ->setCellValue('G'.$i, $ger['ua_q_tareas_R'])
                                ->setCellValue('H'.$i, $ger['ua_q_tareas_V1'])
                                ->setCellValue('I'.$i, $ger['ua_q_tareas_V2'])
                                ->setCellValue('J'.$i, $ger['ua_q_tareas_Cm'])
                                ->setCellValue('K'.$i, $ger['ua_q_tareas_Ct'])
                                ->setCellValue('L'.$i, $ger['ua_q_tareas_t'])
                                ->setCellValue('M'.$i, ($ger['ua_q_tareas_t']/$totaltareasua))
                                ->setCellValue('P'.$i, $ger['ur_q_tareas_A'])
                                ->setCellValue('Q'.$i, $ger['ur_q_tareas_A2'])
                                ->setCellValue('R'.$i, $ger['ur_q_tareas_V1'])
                                ->setCellValue('S'.$i, $ger['ur_q_tareas_V2'])
                                ->setCellValue('T'.$i, $ger['ur_q_tareas_R'])
                                ->setCellValue('U'.$i, $ger['ur_q_tareas_Cm'])
                                ->setCellValue('V'.$i, $ger['ur_q_tareas_Ct'])
                                ->setCellValue('W'.$i, $ger['ur_q_tareas_t'])
                                ->setCellValue('X'.$i, ($ger['ur_q_tareas_t']/$totaltareasua));
                            $divisorUA = ($ger['ua_q_tareas_R']+$ger['ua_q_tareas_V1']+$ger['ua_q_tareas_V2']+$ger['ua_q_tareas_Cm']+$ger['ua_q_tareas_Ct']);
                            $divisorUR = ($ger['ur_q_tareas_R']+$ger['ur_q_tareas_V1']+$ger['ur_q_tareas_V2']+$ger['ur_q_tareas_Cm']+$ger['ur_q_tareas_Ct']);

                            if($divisorUA != 0)
                            {
                                $getActiveSheet
                                    ->setCellValue('N'.$i, (($ger['ua_q_tareas_Cm']+$ger['ua_q_tareas_Ct'])/$divisorUA));
                            }
                            else
                            {
                                $getActiveSheet->setCellValue('N'.$i, 0);
                            }
                            if($divisorUR != 0) 
                            {
                                $getActiveSheet
                                    ->setCellValue('Y'.$i, (($ger['ur_q_tareas_Cm']+$ger['ur_q_tareas_Ct'])/$divisorUR));
                            }
                            else
                            {
                                $getActiveSheet->setCellValue('Y'.$i, 0);
                            }
                            $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloGerencias);
                            $getActiveSheet->getStyle('E'.$i.':M'.$i)->applyFromArray($estiloDatosGciaGral);
                            $getActiveSheet->getStyle('P'.$i.':X'.$i)->applyFromArray($estiloDatosGciaGral);
                            $getActiveSheet->getStyle('N'.$i)->applyFromArray($estiloDatospCump);
                            $getActiveSheet->getStyle('Y'.$i)->applyFromArray($estiloDatospCump);
                            $i++;
                            foreach($usuarios as $usuario)
                            {
                                if($ger['id'] == $usuario['id_gcia'])
                                {
                                    $this->excel->getActiveSheet()
                                        ->mergeCells('A'.$i.':D'.$i)
                                        ->setCellValue('A'.$i, $usuario['usuario'])
                                        ->setCellValue('E'.$i,$usuario['ua_q_tareas_A'])
                                        ->setCellValue('F'.$i,$usuario['ua_q_tareas_A2'])
                                        ->setCellValue('G'.$i,$usuario['ua_q_tareas_R'])
                                        ->setCellValue('H'.$i,$usuario['ua_q_tareas_V1'])
                                        ->setCellValue('I'.$i,$usuario['ua_q_tareas_V2'])
                                        ->setCellValue('J'.$i,$usuario['ua_q_tareas_Cm'])
                                        ->setCellValue('K'.$i,$usuario['ua_q_tareas_Ct'])
                                        ->setCellValue('L'.$i,$usuario['ua_q_tareas_t'])
                                        ->setCellValue('M'.$i,($usuario['ua_q_tareas_t']/$totaltareasua))
                                        ->setCellValue('P'.$i,$usuario['ur_q_tareas_A'])
                                        ->setCellValue('Q'.$i,$usuario['ur_q_tareas_A2'])
                                        ->setCellValue('R'.$i,$usuario['ur_q_tareas_R'])
                                        ->setCellValue('S'.$i,$usuario['ur_q_tareas_V1'])
                                        ->setCellValue('T'.$i,$usuario['ur_q_tareas_V2'])
                                        ->setCellValue('U'.$i,$usuario['ur_q_tareas_Cm'])
                                        ->setCellValue('V'.$i,$usuario['ur_q_tareas_Ct'])
                                        ->setCellValue('W'.$i,$usuario['ur_q_tareas_t'])
                                        ->setCellValue('X'.$i,($usuario['ur_q_tareas_t']/$totaltareasur));
                                    $divisorUA = ($usuario['ua_q_tareas_R']+$usuario['ua_q_tareas_V1']+$usuario['ua_q_tareas_V2']+$usuario['ua_q_tareas_Cm']+$usuario['ua_q_tareas_Ct']);
                                    $divisorUR = ($usuario['ur_q_tareas_R']+$usuario['ur_q_tareas_V1']+$usuario['ur_q_tareas_V2']+$usuario['ur_q_tareas_Cm']+$usuario['ur_q_tareas_Ct']);

                                    if($divisorUA != 0)
                                    {
                                        $getActiveSheet
                                            ->setCellValue('N'.$i, (($usuario['ua_q_tareas_Cm']+$usuario['ua_q_tareas_Ct'])/$divisorUA));
                                    }
                                    else
                                    {
                                        $getActiveSheet->setCellValue('N'.$i, 0);
                                    }
                                    if($divisorUR != 0) 
                                    {
                                        $getActiveSheet
                                            ->setCellValue('Y'.$i, (($usuario['ur_q_tareas_Cm']+$usuario['ur_q_tareas_Ct'])/$divisorUR));
                                    }
                                    else
                                    {
                                        $getActiveSheet->setCellValue('Y'.$i, 0);
                                    }
                                    $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloUsuario);
                                    $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloUsuario);
                                    $getActiveSheet->getStyle('E'.$i)->applyFromArray($estiloDatosA);
                                    $getActiveSheet->getStyle('F'.$i)->applyFromArray($estiloDatosA2);
                                    $getActiveSheet->getStyle('G'.$i)->applyFromArray($estiloDatosR);
                                    $getActiveSheet->getStyle('H'.$i)->applyFromArray($estiloDatosV1);
                                    $getActiveSheet->getStyle('I'.$i)->applyFromArray($estiloDatosV2);
                                    $getActiveSheet->getStyle('J'.$i)->applyFromArray($estiloDatosCm);
                                    $getActiveSheet->getStyle('K'.$i)->applyFromArray($estiloDatosCt);
                                    $getActiveSheet->getStyle('L'.$i)->applyFromArray($estiloDatosTot);
                                    $getActiveSheet->getStyle('M'.$i)->applyFromArray($estiloDatospTot);
                                    $getActiveSheet->getStyle('N'.$i)->applyFromArray($estiloDatospCump);
                                    $getActiveSheet->getStyle('P'.$i)->applyFromArray($estiloDatosA);
                                    $getActiveSheet->getStyle('Q'.$i)->applyFromArray($estiloDatosA2);
                                    $getActiveSheet->getStyle('R'.$i)->applyFromArray($estiloDatosR);
                                    $getActiveSheet->getStyle('S'.$i)->applyFromArray($estiloDatosV1);
                                    $getActiveSheet->getStyle('T'.$i)->applyFromArray($estiloDatosV2);
                                    $getActiveSheet->getStyle('U'.$i)->applyFromArray($estiloDatosCm);
                                    $getActiveSheet->getStyle('V'.$i)->applyFromArray($estiloDatosCt);
                                    $getActiveSheet->getStyle('W'.$i)->applyFromArray($estiloDatosTot);
                                    $getActiveSheet->getStyle('X'.$i)->applyFromArray($estiloDatospTot);
                                    $getActiveSheet->getStyle('Y'.$i)->applyFromArray($estiloDatospCump);
                                    $i++;
                                }
                            }
                        }
                    }

                }
            }
        }
        
        $this->excel->setActiveSheetIndex()->getRowDimension(1)->setRowHeight(35);
        
        $getActiveSheet->getStyle('A1')->applyFromArray($estiloTituloReporte);
        $getActiveSheet->getStyle('A3')->applyFromArray($estiloTitulosCabecera);
        $getActiveSheet->getStyle('A4')->applyFromArray($estiloTitulosCabecera);
        $getActiveSheet->getStyle('A5')->applyFromArray($estiloTitulosCabecera);
        $getActiveSheet->getStyle('C3')->applyFromArray($estiloDatosCabecera);
        $getActiveSheet->getStyle('C4')->applyFromArray($estiloDatosCabecera);
        $getActiveSheet->getStyle('C5')->applyFromArray($estiloDatosCabecera);
        $getActiveSheet->getStyle('E3')->applyFromArray($estiloTitulosGrilla);
        $getActiveSheet->getStyle('E4')->applyFromArray($estiloSubTitulosGrillaC);
        $getActiveSheet->getStyle('E5')->applyFromArray($estiloTitulosGrillaPorcentaje);
        $getActiveSheet->getStyle('E6:E7')->applyFromArray($estiloTituloA);
        $getActiveSheet->getStyle('F6:F7')->applyFromArray($estiloTituloA2);
        $getActiveSheet->getStyle('G6:G7')->applyFromArray($estiloTituloR);
        $getActiveSheet->getStyle('H6:H7')->applyFromArray($estiloTituloV1);
        $getActiveSheet->getStyle('I6:I7')->applyFromArray($estiloTituloV2);
        $getActiveSheet->getStyle('J6:J7')->applyFromArray($estiloTituloCt);
        $getActiveSheet->getStyle('K6:K7')->applyFromArray($estiloTituloCm);
        $getActiveSheet->getStyle('L6:L7')->applyFromArray($estiloTitulosTotTareas);
        $getActiveSheet->getStyle('M6:M7')->applyFromArray($estiloTitulosTotTareas);
        $getActiveSheet->getStyle('M5')->applyFromArray($estiloPorcentajeTotal);
        $getActiveSheet->getStyle('N6:N7')->applyFromArray($estiloTitulosTotTareas);
        $getActiveSheet->getStyle('P3')->applyFromArray($estiloTitulosGrilla);
        $getActiveSheet->getStyle('P4')->applyFromArray($estiloSubTitulosGrillaR);
        $getActiveSheet->getStyle('P5')->applyFromArray($estiloTitulosGrillaPorcentaje);
        $getActiveSheet->getStyle('X5')->applyFromArray($estiloPorcentajeTotal);
        $getActiveSheet->getStyle('P6:P7')->applyFromArray($estiloTituloA);
        $getActiveSheet->getStyle('Q6:Q7')->applyFromArray($estiloTituloA2);
        $getActiveSheet->getStyle('R6:R7')->applyFromArray($estiloTituloR);
        $getActiveSheet->getStyle('S6:S7')->applyFromArray($estiloTituloV1);
        $getActiveSheet->getStyle('T6:T7')->applyFromArray($estiloTituloV2);
        $getActiveSheet->getStyle('U6:U7')->applyFromArray($estiloTituloCt);
        $getActiveSheet->getStyle('V6:V7')->applyFromArray($estiloTituloCm);
        $getActiveSheet->getStyle('W6:W7')->applyFromArray($estiloTitulosTotTareas);
        $getActiveSheet->getStyle('X6:X7')->applyFromArray($estiloTitulosTotTareas);
        $getActiveSheet->getStyle('Y6:Y7')->applyFromArray($estiloTitulosTotTareas);
        
        $getActiveSheet->getStyle('M'.$j.':M'.$i)->getNumberFormat()->applyFromArray( 
            array( 
                'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00
            )
        );
        $getActiveSheet->getStyle('X'.$j.':X'.$i)->getNumberFormat()->applyFromArray( 
            array( 
                'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00
            )
        );
        $getActiveSheet->getStyle('N'.$j.':N'.$i)->getNumberFormat()->applyFromArray( 
            array( 
                'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00
            )
        );
        $getActiveSheet->getStyle('Y'.$j.':Y'.$i)->getNumberFormat()->applyFromArray( 
            array( 
                'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00
            )
        );
        $getActiveSheet->getStyle('M5')->getNumberFormat()->applyFromArray( 
            array( 
                'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00
            )
        );
        $getActiveSheet->getStyle('X5')->getNumberFormat()->applyFromArray( 
            array( 
                'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00
            )
        );
        
        $this->excel->setActiveSheetIndex()->getRowDimension(3)->setRowHeight(26.25);
        $this->excel->setActiveSheetIndex()->getRowDimension(4)->setRowHeight(23.25);
        $this->excel->setActiveSheetIndex()->getRowDimension(5)->setRowHeight(21.75);
        
        $this->excel->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth('12');
        $this->excel->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth('12');
        $this->excel->setActiveSheetIndex(0)->getColumnDimension('C')->setWidth('12');
        $this->excel->setActiveSheetIndex(0)->getColumnDimension('D')->setWidth('12');
        $this->excel->setActiveSheetIndex(0)->getColumnDimension('E')->setWidth('6');
        $this->excel->setActiveSheetIndex(0)->getColumnDimension('F')->setWidth('6');
        $this->excel->setActiveSheetIndex(0)->getColumnDimension('G')->setWidth('6');
        $this->excel->setActiveSheetIndex(0)->getColumnDimension('H')->setWidth('6');
        $this->excel->setActiveSheetIndex(0)->getColumnDimension('I')->setWidth('6');
        $this->excel->setActiveSheetIndex(0)->getColumnDimension('J')->setWidth('6');
        $this->excel->setActiveSheetIndex(0)->getColumnDimension('K')->setWidth('6');
        $this->excel->setActiveSheetIndex(0)->getColumnDimension('L')->setWidth('8');
        $this->excel->setActiveSheetIndex(0)->getColumnDimension('M')->setWidth('8');
        $this->excel->setActiveSheetIndex(0)->getColumnDimension('N')->setWidth('8');
        $this->excel->setActiveSheetIndex(0)->getColumnDimension('O')->setWidth('4');
        $this->excel->setActiveSheetIndex(0)->getColumnDimension('P')->setWidth('6');
        $this->excel->setActiveSheetIndex(0)->getColumnDimension('Q')->setWidth('6');
        $this->excel->setActiveSheetIndex(0)->getColumnDimension('R')->setWidth('6');
        $this->excel->setActiveSheetIndex(0)->getColumnDimension('S')->setWidth('6');
        $this->excel->setActiveSheetIndex(0)->getColumnDimension('T')->setWidth('6');
        $this->excel->setActiveSheetIndex(0)->getColumnDimension('U')->setWidth('6');
        $this->excel->setActiveSheetIndex(0)->getColumnDimension('V')->setWidth('6');
        $this->excel->setActiveSheetIndex(0)->getColumnDimension('W')->setWidth('8');
        $this->excel->setActiveSheetIndex(0)->getColumnDimension('X')->setWidth('8');
        $this->excel->setActiveSheetIndex(0)->getColumnDimension('Y')->setWidth('8');
        
        $this->excel->getActiveSheet()->freezePane('E7');
        
        switch($id_empresa)
        {
            case 1: 
                $filename='ORO_Reporte_Tareas';
                break;
            case 2: 
                $filename='SDJ_Reporte_Tarea';
                break;
            case 3: 
                $filename='BRX_Reporte_Tareas';
                break;
        }
        
        
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache

        //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
        //if you want to save it as .XLSX Excel 2007 format
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
        ob_end_clean();
        //force user to download the Excel file without writing it to server's HD
        $objWriter->save('php://output');
    }
    
    function tabla_dinamica ($array,$numeros,$filas)
    {        
        $resultado=array();
        foreach ($array as $key=>$value)
        {
            if (!array_key_exists($value[$filas[2]], $resultado))
            {
                $resultado[$value[$filas[2]]]['nomemp']=$value['nomemp'];
                $resultado[$value[$filas[2]]]['gciaCorp'][$value[$filas[0]]]['gerencia']=$value['gerencia_corp'];
                $resultado[$value[$filas[2]]]['gciaCorp'][$value[$filas[0]]]['id_empresa']=$value['id_empresa'];
                $resultado[$value[$filas[2]]]['gciaCorp'][$value[$filas[0]]]['id']=$value[$filas[0]];
                if ( ($value[$filas[3]]!=1) && ($value[$filas[3]]!=2) && ($value[$filas[3]]!=4))
                {
                    $resultado[$value[$filas[2]]]['gciaCorp'][$value[$filas[0]]]['gciaLinea'][$value[$filas[1]]]['gerenciaLinea']=$value['gerencia'];
                    $resultado[$value[$filas[2]]]['gciaCorp'][$value[$filas[0]]]['gciaLinea'][$value[$filas[1]]]['id']=$value['id_area'];
                    $resultado[$value[$filas[2]]]['gciaCorp'][$value[$filas[0]]]['gciaLinea'][$value[$filas[1]]]['id_empresa']=$value['id_empresa'];
                }
                foreach ($numeros as $numero=>$valorNumero)
                {
                    $resultado[$value[$filas[2]]][$valorNumero]=$value[$valorNumero];
                    $resultado[$value[$filas[2]]]['gciaCorp'][$value[$filas[0]]][$valorNumero]=$value[$valorNumero];
                    if ( ($value[$filas[3]]!=1) && ($value[$filas[3]]!=2) && ($value[$filas[3]]!=4) )
                    {
                        $resultado[$value[$filas[2]]]['gciaCorp'][$value[$filas[0]]]['gciaLinea'][$value[$filas[1]]][$valorNumero]=$value[$valorNumero];
                    }
                }
            }
            else
            {
                foreach ($numeros as $numero=>$valorNumero)
                {
                    $resultado[$value[$filas[2]]][$valorNumero]+=$value[$valorNumero];
                }
                if (!array_key_exists($value[$filas[0]],$resultado[$value[$filas[2]]]['gciaCorp']))
                {
                    $resultado[$value[$filas[2]]]['gciaCorp'][$value[$filas[0]]]['gerencia']=$value['gerencia_corp'];
                    $resultado[$value[$filas[2]]]['gciaCorp'][$value[$filas[0]]]['id_empresa']=$value['id_empresa'];
                    $resultado[$value[$filas[2]]]['gciaCorp'][$value[$filas[0]]]['id']=$value[$filas[0]];
                    if ( ($value[$filas[3]]!=1) && ($value[$filas[3]]!=2) && ($value[$filas[3]]!=4) )
                    {
                        $resultado[$value[$filas[2]]]['gciaCorp'][$value[$filas[0]]]['gciaLinea'][$value[$filas[1]]]['gerenciaLinea']=$value['gerencia'];
                        $resultado[$value[$filas[2]]]['gciaCorp'][$value[$filas[0]]]['gciaLinea'][$value[$filas[1]]]['id']=$value['id_area'];
                        $resultado[$value[$filas[2]]]['gciaCorp'][$value[$filas[0]]]['gciaLinea'][$value[$filas[1]]]['id_empresa']=$value['id_empresa'];
                    }
                    foreach ($numeros as $numero=>$valorNumero)
                    {
                        $resultado[$value[$filas[2]]]['gciaCorp'][$value[$filas[0]]][$valorNumero]=$value[$valorNumero];
                        if ( ($value[$filas[3]]!=1) && ($value[$filas[3]]!=2) && ($value[$filas[3]]!=4) )
                        {
                            $resultado[$value[$filas[2]]]['gciaCorp'][$value[$filas[0]]]['gciaLinea'][$value[$filas[1]]][$valorNumero]=$value[$valorNumero];
                        }
                    }
                }
                else
                {
                    foreach ($numeros as $numero=>$valorNumero)
                    {
                         $resultado[$value[$filas[2]]]['gciaCorp'][$value[$filas[0]]][$valorNumero]+=$value[$valorNumero];
                    }
                    if ( ($value[$filas[3]]!=1) && ($value[$filas[3]]!=2) && ($value[$filas[3]]!=4) )
                    {
                        if (!array_key_exists($value[$filas[1]],$resultado[$value[$filas[2]]]['gciaCorp'][$value[$filas[0]]]['gciaLinea']))
                        {
                            $resultado[$value[$filas[2]]]['gciaCorp'][$value[$filas[0]]]['gciaLinea'][$value[$filas[1]]]['gerenciaLinea']=$value['gerencia'];
                            $resultado[$value[$filas[2]]]['gciaCorp'][$value[$filas[0]]]['gciaLinea'][$value[$filas[1]]]['id']=$value['id_area'];
                            $resultado[$value[$filas[2]]]['gciaCorp'][$value[$filas[0]]]['gciaLinea'][$value[$filas[1]]]['id_empresa']=$value['id_empresa'];
                            foreach ($numeros as $numero=>$valorNumero)
                            {
                                $resultado[$value[$filas[2]]]['gciaCorp'][$value[$filas[0]]]['gciaLinea'][$value[$filas[1]]][$valorNumero]=$value[$valorNumero];
                            }
                        }
                        else
                        {
                            foreach ($numeros as $numero=>$valorNumero)
                            {
                                $resultado[$value[$filas[2]]]['gciaCorp'][$value[$filas[0]]]['gciaLinea'][$value[$filas[1]]][$valorNumero]+=$value[$valorNumero];
                            }
                        }
                    }
                }
            }   
        }
        return $resultado;
    }
    
    public function excel()
    {
        $id_empresa = 1;
        $filas=array('id_corp','id_gerencia','id_empresa','id_organigrama');
        
        $numeros=array(
            'ua_q_tareas_A'
            ,'ua_q_tareas_A2'
            ,'ua_q_tareas_R'
            ,'ua_q_tareas_V1'
            ,'ua_q_tareas_V2'
            ,'ua_q_tareas_Cm'
            ,'ua_q_tareas_Ct'
            ,'ua_q_tareas_C'
            ,'ua_q_tareas_t'
            ,'ur_q_tareas_A'
            ,'ur_q_tareas_A2'
            ,'ur_q_tareas_R'
            ,'ur_q_tareas_V1'
            ,'ur_q_tareas_V2'
            ,'ur_q_tareas_Cm'
            ,'ur_q_tareas_Ct'
            ,'ur_q_tareas_C'
            ,'ur_q_tareas_t'
        );
        
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        
        
        $this->excel->getProperties()
            ->setCreator("Sistema de Mejora Continua (SMC)") // Nombre del autor
            ->setLastModifiedBy("Sistema de Mejora Continua (SMC)") //Ultimo usuario que lo modificó
            ->setTitle("Reporte Semanal") // Titulo
            ->setSubject("Reporte Semanal") //Asunto
            ->setDescription("Reporte Semanal") //Descripción
            ->setKeywords("Reporte Semanal") //Etiquetas
            ->setCategory("Reporte Semanal");
        
        $getActiveSheet=$this->excel->getActiveSheet();
        
        $mes = date("m");
        $fecha_actual=date("d/m/Y");
        $anio = date("Y");
        
        switch($mes)
        {
            case 1: $nombreMes = 'Enero';break;
            case 2: $nombreMes = 'Febrero';break;
            case 3: $nombreMes = 'Marzo';break;
            case 4: $nombreMes = 'Abril';break;
            case 5: $nombreMes = 'Mayo';break;
            case 6: $nombreMes = 'Junio';break;
            case 7: $nombreMes = 'Julio';break;
            case 8: $nombreMes = 'Agosto';break;
            case 9: $nombreMes = 'Septiembre';break;
            case 10: $nombreMes = 'Octubre';break;
            case 11: $nombreMes = 'Noviembre';break;
            case 12: $nombreMes = 'Diciembre';break;
        }
        
        $estiloTituloReporte = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => true,
                'italic'    => false,
                'strike'    => false,
                'size' =>30,
                'color'     => array(
                    'rgb' => '000000'
                )
            ),
            'alignment' => array(
               'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
               'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
               'rotation' => 0,
               'wrap' => TRUE
            )
         );
        $estiloTitulosCabecera = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => true,
                'italic'    => false,
                'strike'    => false,
                'size' =>12,
                'color'     => array(
                    'rgb' => '000000'
                )
            ),
           'alignment' => array(
               'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
               'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
               'rotation' => 0,
               'wrap' => TRUE
            )
        );
        $estiloDatosCabecera = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => false,
                'italic'    => false,
                'strike'    => false,
                'size' =>12,
                'color'     => array(
                    'rgb' => '000000'
                )
            ),
            'alignment' => array(
               'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
               'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
               'rotation' => 0,
               'wrap' => TRUE
            ),
            'fill' => array(
               'type'  => PHPExcel_Style_Fill::FILL_SOLID,
               'color' => array(
               'argb' => 'FFF2F2F2'
                )
            ),
        );
        $estiloTitulosGrilla = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => True,
                'italic'    => false,
                'strike'    => false,
                'size' =>20,
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
                'argb' => 'FF9BBB59'
                )
            ),
        );
        $estiloSubTitulosGrillaC = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => True,
                'italic'    => false,
                'strike'    => false,
                'size' =>18,
                'color'     => array(
                    'rgb' => '1F497D'
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
                'argb' => 'FFFFFFFF'
                )
            ),
        );
        $estiloSubTitulosGrillaR = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => True,
                'italic'    => false,
                'strike'    => false,
                'size' =>18,
                'color'     => array(
                    'rgb' => '9BBB59'
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
                'argb' => 'FFFFFFFF')
            ),
        );
        $estiloTitulosTotTareas = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => True,
                'italic'    => false,
                'strike'    => false,
                'size' =>8,
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
                'argb' => 'FF808080'
                )
            ),
        );
        $estiloTitulosGrillaPorcentaje = array(
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
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                    'rotation' => 0,
                    'wrap' => TRUE
                ),
                'fill' => array(
                    'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array(
                    'argb' => 'FFFFFFFF'
                    )
                ),
        );
        $estiloPorcentajeTotal = array(
                   'font' => array(
                       'name'      => 'Calibri',
                       'bold'      => True,
                       'italic'    => false,
                       'strike'    => false,
                       'size' =>18,
                       'color'     => array(
                           'rgb' => 'FFFF00'
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
                      'argb' => 'FF000000')
                    ),
            );
            $estiloTituloA = array(
                'font' => array(
                    'name'      => 'Calibri',
                    'bold'      => True,
                    'italic'    => false,
                    'strike'    => false,
                    'size' =>8,
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
                    'argb' => 'FF528ED5'
                    )
                ),
            );
        $estiloTituloA2 = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => True,
                'italic'    => false,
                'strike'    => false,
                'size' =>8,
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
                'argb' => 'FF1F497D'
                )
            ),
        );
        $estiloTituloR = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => True,
                'italic'    => false,
                'strike'    => false,
                'size' =>8,
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
                'argb' => 'FFE46D0A'
                )
            ),
        );
        $estiloTituloV1 = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => True,
                'italic'    => false,
                'strike'    => false,
                'size' =>8,
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
                'argb' => 'FFFF3B3B')
            ),
        );
        $estiloTituloV2 = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => True,
                'italic'    => false,
                'strike'    => false,
                'size' =>8,
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
                'argb' => 'FFFF0000'
                )
            ),
        );
        $estiloTituloCm = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => True,
                'italic'    => false,
                'strike'    => false,
                'size' =>8,
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
                'argb' => 'FF00B050'
                )
            ),
        );
        $estiloTituloCt = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => True,
                'italic'    => false,
                'strike'    => false,
                'size' =>8,
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
                'argb' => 'FF948B54'
                )
            ),
        );
        
        $estiloTituloEmpresas = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => True,
                'italic'    => false,
                'strike'    => false,
                'size' =>16,
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
                    'argb' => 'FFA5A5A5'
                )
            ),
        );
        $estiloGciaCorp = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => false,
                'italic'    => false,
                'strike'    => false,
                'size' =>11,
                'color'     => array(
                    'rgb' => '000000'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'rotation' => 0,
                'wrap' => TRUE
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'argb' => 'FFFFC000'
                )
            ),
        );
        $estiloTituloTotal = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => false,
                'italic'    => false,
                'strike'    => false,
                'size' =>10,
                'color'     => array(
                    'rgb' => 'FFFFFF'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'rotation' => 0,
                'wrap' => TRUE
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'argb' => 'FFA5A5A5'
                )
            ),
        );
        $estiloTitulosAreas = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => True,
                'italic'    => false,
                'strike'    => false,
                'size' =>16,
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
                    'argb' => 'FFA5A5A5'
                )
            ),
        );
        $estiloTitulosAreasNoCorp = array(
                'font' => array(
                    'name'      => 'Calibri',
                    'bold'      => True,
                    'italic'    => false,
                    'strike'    => false,
                    'size' =>12,
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
                        'argb' => 'FFA5A5A5'
                    )
                ),
        );
        $estiloGerenciasCorp = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => True,
                'italic'    => false,
                'strike'    => false,
                'size' =>11,
                'color'     => array(
                    'rgb' => '1F497D'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'rotation' => 0,
                'wrap' => TRUE
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'argb' => 'FFFFC000'
                )
            ),
        );
        $estiloGerencias = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => True,
                'italic'    => false,
                'strike'    => false,
                'size' =>9,
                'color'     => array(
                    'rgb' => '000000'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'rotation' => 0,
                'wrap' => TRUE
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'argb' => 'FFC2D69A'
                )
            ),
        );
        $estiloUsuario = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => True,
                'italic'    => true,
                'strike'    => false,
                'size' =>10,
                'color'     => array(
                    'rgb' => '000000'
                )
                ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'rotation' => 0,
                'wrap' => TRUE
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'argb' => 'FFFFFFFF'
                )
            ),
        );
        //-------------------------------DATOS---------------------------------
        $estiloDatosA = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => True,
                'italic'    => false,
                'strike'    => false,
                'size' =>8,
                'color'     => array(
                    'rgb' => '528ED5'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'rotation' => 0,
                'wrap' => TRUE
            ),
            'borders' => array(
                'outline' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                    'color' => array(
                        'rgb' => 'BFBFBF'
                    )
                )
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                   'argb' => 'FFFFFFFF'
                )
            ),
        );
        $estiloDatosA2 = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => True,
                'italic'    => false,
                'strike'    => false,
                'size' =>8,
                'color'     => array(
                   'rgb' => '1F497D'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'rotation' => 0,
                'wrap' => TRUE
            ),
            'borders' => array(
                'outline' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                    'color' => array(
                        'rgb' => 'BFBFBF'
                    )
                )
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                  'argb' => 'FFFFFFFF'
                )
            ),
        );
        $estiloDatosR = array(
            'font' => array(
               'name'      => 'Calibri',
               'bold'      => True,
               'italic'    => false,
               'strike'    => false,
               'size' =>8,
               'color'     => array(
                   'rgb' => 'F79646'
                  )
              ),
            'alignment' => array(
              'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
              'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
              'rotation' => 0,
              'wrap' => TRUE
            ),
            'borders' => array(
                'outline' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                    'color' => array(
                        'rgb' => 'BFBFBF'
                    )
                )
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                  'argb' => 'FFFFFFFF'
                )
            ),
        );
        $estiloDatosV1 = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => True,
                'italic'    => false,
                'strike'    => false,
                'size' =>8,
                'color'     => array(
                   'rgb' => 'D99695'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'rotation' => 0,
                'wrap' => TRUE
            ),
            'borders' => array(
                'outline' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                    'color' => array(
                        'rgb' => 'BFBFBF'
                    )
                )
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                  'argb' => 'FFFFFFFF'
                )
            ),
        );
        $estiloDatosV2 = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => True,
                'italic'    => false,
                'strike'    => false,
                'size' =>8,
                'color'     => array(
                   'rgb' => 'FF0000'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'rotation' => 0,
                'wrap' => TRUE
            ),
            'borders' => array(
                'outline' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                    'color' => array(
                        'rgb' => 'BFBFBF'
                    )
                )
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                'argb' => 'FFFFFFFF'
                )
            ),
        );
        $estiloDatosCm = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => True,
                'italic'    => false,
                'strike'    => false,
                'size' =>8,
                'color'     => array(
                   'rgb' => '948B54'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'rotation' => 0,
                'wrap' => TRUE
            ),
            'borders' => array(
                'outline' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                    'color' => array(
                        'rgb' => 'BFBFBF'
                    )
                )
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                'argb' => 'FFFFFFFF'
                )
            ),
        );
        $estiloDatosCt = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => True,
                'italic'    => false,
                'strike'    => false,
                'size' =>8,
                'color'     => array(
                    'rgb' => '00B050'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'rotation' => 0,
                'wrap' => TRUE
            ),
            'borders' => array(
                'outline' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                    'color' => array(
                        'rgb' => 'BFBFBF'
                    )
                )
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                'argb' => 'FFFFFFFF'
                )
            ),
        );
        $estiloDatosTot = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => True,
                'italic'    => false,
                'strike'    => false,
                'size' =>8,
                'color'     => array(
                   'rgb' => '7F7F7F'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'rotation' => 0,
                'wrap' => TRUE
            ),
            'borders' => array(
                'outline' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                    'color' => array(
                        'rgb' => 'BFBFBF'
                    )
                )
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'argb' => 'FFFFFFFF'
                )
            ),
        );
        $estiloDatospTot = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => false,
                'italic'    => false,
                'strike'    => false,
                'size' =>8,
                'color'     => array(
                   'rgb' => '7F7F7F'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'rotation' => 0,
                'wrap' => TRUE
            ),
            'borders' => array(
                'outline' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                    'color' => array(
                        'rgb' => 'BFBFBF'
                    )
                )
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                'argb' => 'FFFFFFFF'
                )
            ),
        );
        $estiloDatospCump = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => true,
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
            'borders' => array(
                'outline' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                    'color' => array(
                        'rgb' => 'BFBFBF'
                    )
                )
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'argb' => 'FF00B050'
                )
            ),
        );
        $estiloDatosCorp = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => True,
                'italic'    => false,
                'strike'    => false,
                'size' =>8,
                'color'     => array(
                    'rgb' => '528ED5'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'rotation' => 0,
                'wrap' => TRUE
            ),
            'borders' => array(
                'outline' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                    'color' => array(
                        'rgb' => 'BFBFBF'
                    )
                )
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                'argb' => 'FFFFC000')
            ),
        );
        $estiloDatosGciaGral = array(
            'font' => array(
                'name'      => 'Calibri',
                'bold'      => True,
                'italic'    => false,
                'strike'    => false,
                'size' =>8,
                'color'     => array(
                    'rgb' => '528ED5'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'rotation' => 0,
                'wrap' => TRUE
            ),
            'borders' => array(
                'outline' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                    'color' => array(
                        'rgb' => 'BFBFBF'
                    )
                )
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'argb' => 'FFC2D69A'
                )
            ),
        );
        
        $usuarioC = 'Usuario Creador';
        $usuarioR = 'Usuario Responsable';
        
        $this->excel->getActiveSheet()
            ->setCellValue('A1', 'Reporte de Tareas')
            ->setCellValue('A3', 'Año:')
            ->setCellValue('A4', 'Mes:')
            ->setCellValue('A5', 'Fecha de Reporte:')
            ->setCellValue('C3', $anio)
            ->setCellValue('C4', $nombreMes)
            ->setCellValue('C5', $fecha_actual)
            ->setCellValue('E3', 'TAREAS')
            ->setCellValue('P3', 'TAREAS')
            ->setCellValue('E4', $usuarioC)
            ->setCellValue('P4', $usuarioR)
            ->setCellValue('E5', '% de Cumplimiento Alcanzado->')
            ->setCellValue('P5', '% de Cumplimiento Alcanzado->')
            ->setCellValue('E6', 'A')
            ->setCellValue('F6', 'A2')
            ->setCellValue('G6', 'R')
            ->setCellValue('H6', 'V1')
            ->setCellValue('I6', 'V2')
            ->setCellValue('J6', 'Cm')
            ->setCellValue('K6', 'Ct')
            ->setCellValue('L6', 'Tot.')
            ->setCellValue('M6', '% Tot.')
            ->setCellValue('N6', '% Cump.')
            ->setCellValue('P6', 'A')
            ->setCellValue('Q6', 'A2')
            ->setCellValue('R6', 'R')
            ->setCellValue('S6', 'V1')
            ->setCellValue('T6', 'V2')
            ->setCellValue('U6', 'Cm')
            ->setCellValue('V6', 'Ct')
            ->setCellValue('W6', 'Tot.')
            ->setCellValue('X6', '% Tot.')
            ->setCellValue('Y6', '% Cump.');
        
        $getActiveSheet
            ->mergeCells('A1:Y1')
            ->mergeCells('A3:B3')
            ->mergeCells('A4:B4')
            ->mergeCells('A5:B5')
            ->mergeCells('C3:D3')
            ->mergeCells('C4:D4')
            ->mergeCells('C5:D5')
            ->mergeCells('E3:N3')
            ->mergeCells('E4:N4')
            ->mergeCells('E5:L5')
            ->mergeCells('M5:N5')
            ->mergeCells('P3:Y3')
            ->mergeCells('P4:Y4')
            ->mergeCells('P5:W5')
            ->mergeCells('X5:Y5');
        
        $this->load->model('reportes/meta_reportes_model','meta_reporte',true);
        $usuarios = $this->meta_reporte->dameUsuarios($anio,$mes);
        
        $n=0;            
        $totaltareasuaA=0;
        $totaltareasuaA2=0;
        $totaltareasuaR=0;
        $totaltareasuaV1=0;
        $totaltareasuaV2=0;
        $totaltareasuaCm=0;
        $totaltareasuaCt=0;
        $totaltareasua=0;
        $totaltareasurA=0;
        $totaltareasurA2=0;
        $totaltareasurR=0;
        $totaltareasurV1=0;
        $totaltareasurV2=0;
        $totaltareasurCm=0;
        $totaltareasurCt=0;
        $totaltareasur=0;
        $totalporctareasempresasua = 0;
        $totalporctareasempresasur = 0;
        $totalporctareascorpua = 0;
        $totalporctareascorpur = 0;
        $totaltareasuaAg=0;
        $totaltareasuaA2g=0;
        $totaltareasuaRg=0;
        $totaltareasuaV1g=0;
        $totaltareasuaV2g=0;
        $totaltareasuaCmg=0;
        $totaltareasuaCtg=0;
        $totaltareasuag=0;
        $totaltareasurAg=0;
        $totaltareasurA2g=0;
        $totaltareasurRg=0;
        $totaltareasurV1g=0;
        $totaltareasurV2g=0;
        $totaltareasurCmg=0;
        $totaltareasurCtg=0;
        $totaltareasurg=0;
        $totaltareasuaAgb=0;
        $totaltareasuaA2gb=0;
        $totaltareasuaRgb=0;
        $totaltareasuaV1gb=0;
        $totaltareasuaV2gb=0;
        $totaltareasuaCmgb=0;
        $totaltareasuaCtgb=0;
        $totaltareasuagb=0;
        $totaltareasurAgb=0;
        $totaltareasurA2gb=0;
        $totaltareasurRgb=0;
        $totaltareasurV1gb=0;
        $totaltareasurV2gb=0;
        $totaltareasurCmgb=0;
        $totaltareasurCtgb=0;
        $totaltareasurgb=0;
        
        $arreglo=$this->tabla_dinamica($usuarios,$numeros,$filas);
        $areas=$this->arrayCargaUsuarios($usuarios,$numeros,$filas);
        $gciacorp=$this->armarGerenciasCorp($arreglo,$numeros);
        $gcia=$this->armarGerencias($arreglo,$numeros);
        
        $i = 7;
        $j= $i;
        
        if ($id_empresa == 1)
        { 
            foreach ($arreglo as $empresa)
            {
                if($n==0)
                {
                    $getActiveSheet
                        ->mergeCells('A'.$i.':D'.$i)
                        ->getRowDimension($i)->setRowHeight(27);
                    $getActiveSheet->setCellValue('A'.$i, 'Empresas');
                    $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloTituloEmpresas);
                    
                    foreach($arreglo as $linea)
                    {
                        $totaltareasuaA=$totaltareasuaA+$linea['ua_q_tareas_A'];
                        $totaltareasuaA2=$totaltareasuaA2+$linea['ua_q_tareas_A2'];
                        $totaltareasuaR=$totaltareasuaR+$linea['ua_q_tareas_R'];
                        $totaltareasuaV1=$totaltareasuaV1+$linea['ua_q_tareas_V1'];
                        $totaltareasuaV2=$totaltareasuaV2+$linea['ua_q_tareas_V2'];
                        $totaltareasuaCm=$totaltareasuaCm+$linea['ua_q_tareas_Cm'];
                        $totaltareasuaCt=$totaltareasuaCt+$linea['ua_q_tareas_Ct'];
                        $totaltareasua=$totaltareasua+$linea['ua_q_tareas_t'];
                        $totaltareasurA=$totaltareasurA+$linea['ur_q_tareas_A'];
                        $totaltareasurA2=$totaltareasurA2+$linea['ur_q_tareas_A2'];
                        $totaltareasurR=$totaltareasurR+$linea['ur_q_tareas_R'];
                        $totaltareasurV1=$totaltareasurV1+$linea['ur_q_tareas_V1'];
                        $totaltareasurV2=$totaltareasurV2+$linea['ur_q_tareas_V2'];
                        $totaltareasurCm=$totaltareasurCm+$linea['ur_q_tareas_Cm'];
                        $totaltareasurCt=$totaltareasurCt+$linea['ur_q_tareas_Ct'];
                        $totaltareasur=$totaltareasur+$linea['ur_q_tareas_t'];
                    }
                    $n++;
                    $i++;
                }

                $getActiveSheet->mergeCells('A'.$i.':D'.$i);
                $getActiveSheet->setCellValue('A'.$i, $empresa['nomemp']);
                $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloGciaCorp);
                
                $getActiveSheet
                    ->setCellValue('E'.$i, $empresa['ua_q_tareas_A'])
                    ->setCellValue('F'.$i, $empresa['ua_q_tareas_A2'])
                    ->setCellValue('G'.$i, $empresa['ua_q_tareas_R'])
                    ->setCellValue('H'.$i, $empresa['ua_q_tareas_V1'])
                    ->setCellValue('I'.$i, $empresa['ua_q_tareas_V2'])
                    ->setCellValue('J'.$i, $empresa['ua_q_tareas_Cm'])
                    ->setCellValue('K'.$i, $empresa['ua_q_tareas_Ct'])
                    ->setCellValue('L'.$i, $empresa['ua_q_tareas_t'])
                    ->setCellValue('M'.$i, ($empresa['ua_q_tareas_t']/$totaltareasua))
                    ->setCellValue('P'.$i, $empresa['ur_q_tareas_A'])
                    ->setCellValue('Q'.$i, $empresa['ur_q_tareas_A2'])
                    ->setCellValue('R'.$i, $empresa['ur_q_tareas_R'])
                    ->setCellValue('S'.$i, $empresa['ur_q_tareas_V1'])
                    ->setCellValue('T'.$i, $empresa['ur_q_tareas_V2'])
                    ->setCellValue('U'.$i, $empresa['ur_q_tareas_Cm'])
                    ->setCellValue('V'.$i, $empresa['ur_q_tareas_Ct'])
                    ->setCellValue('W'.$i, $empresa['ur_q_tareas_t'])
                    ->setCellValue('X'.$i, ($empresa['ur_q_tareas_t']/$totaltareasur));
                
                $divisorUA = ($empresa['ua_q_tareas_R']+$empresa['ua_q_tareas_V1']+$empresa['ua_q_tareas_V2']+$empresa['ua_q_tareas_Cm']+$empresa['ua_q_tareas_Ct']);
                $divisorUR = ($empresa['ur_q_tareas_R']+$empresa['ur_q_tareas_V1']+$empresa['ur_q_tareas_V2']+$empresa['ur_q_tareas_Cm']+$empresa['ur_q_tareas_Ct']);
                
                if($divisorUA != 0)
                {
                    $getActiveSheet
                        ->setCellValue('N'.$i, (($empresa['ua_q_tareas_Cm']+$empresa['ua_q_tareas_Ct'])/$divisorUA));
                }
                else
                {
                    $getActiveSheet->setCellValue('N'.$i, 0);
                }
                if($divisorUR != 0)
                    
                {
                    $getActiveSheet
                        ->setCellValue('Y'.$i, (($empresa['ur_q_tareas_Cm']+$empresa['ur_q_tareas_Ct'])/$divisorUR));
                }
                else
                {
                    $getActiveSheet->setCellValue('Y'.$i, 0);
                }
                
                $getActiveSheet->getStyle('E'.$i)->applyFromArray($estiloDatosA);
                $getActiveSheet->getStyle('F'.$i)->applyFromArray($estiloDatosA2);
                $getActiveSheet->getStyle('G'.$i)->applyFromArray($estiloDatosR);
                $getActiveSheet->getStyle('H'.$i)->applyFromArray($estiloDatosV1);
                $getActiveSheet->getStyle('I'.$i)->applyFromArray($estiloDatosV2);
                $getActiveSheet->getStyle('J'.$i)->applyFromArray($estiloDatosCm);
                $getActiveSheet->getStyle('K'.$i)->applyFromArray($estiloDatosCt);
                $getActiveSheet->getStyle('L'.$i)->applyFromArray($estiloDatosTot);
                $getActiveSheet->getStyle('M'.$i)->applyFromArray($estiloDatospTot);
                $getActiveSheet->getStyle('N'.$i)->applyFromArray($estiloDatospCump);
                $getActiveSheet->getStyle('P'.$i)->applyFromArray($estiloDatosA);
                $getActiveSheet->getStyle('Q'.$i)->applyFromArray($estiloDatosA2);
                $getActiveSheet->getStyle('R'.$i)->applyFromArray($estiloDatosR);
                $getActiveSheet->getStyle('S'.$i)->applyFromArray($estiloDatosV1);
                $getActiveSheet->getStyle('T'.$i)->applyFromArray($estiloDatosV2);
                $getActiveSheet->getStyle('U'.$i)->applyFromArray($estiloDatosCm);
                $getActiveSheet->getStyle('V'.$i)->applyFromArray($estiloDatosCt);
                $getActiveSheet->getStyle('W'.$i)->applyFromArray($estiloDatosTot);
                $getActiveSheet->getStyle('X'.$i)->applyFromArray($estiloDatospTot);
                $getActiveSheet->getStyle('Y'.$i)->applyFromArray($estiloDatospCump);
                
                $totalporctareasempresasua = $totalporctareasempresasua + ($empresa['ua_q_tareas_t']/$totaltareasua);
                $totalporctareasempresasur = $totalporctareasempresasur + ($empresa['ur_q_tareas_t']/$totaltareasur);
                $i++;
            }
            $this->excel->getActiveSheet()//totales
                ->mergeCells('A'.$i.':D'.$i)
                ->setCellValue('A'.$i, 'Total')
                ->setCellValue('E'.$i, $totaltareasuaA)
                ->setCellValue('F'.$i, $totaltareasuaA2)
                ->setCellValue('G'.$i, $totaltareasuaR)
                ->setCellValue('H'.$i, $totaltareasuaV1)
                ->setCellValue('I'.$i, $totaltareasuaV2)
                ->setCellValue('J'.$i, $totaltareasuaCm)
                ->setCellValue('K'.$i, $totaltareasuaCt)
                ->setCellValue('L'.$i, $totaltareasua)
                ->setCellValue('M'.$i, $totalporctareasempresasua)
                ->setCellValue('P'.$i, $totaltareasurA)
                ->setCellValue('Q'.$i, $totaltareasurA2)
                ->setCellValue('R'.$i, $totaltareasurR)
                ->setCellValue('S'.$i, $totaltareasurV1)
                ->setCellValue('T'.$i, $totaltareasurV2)
                ->setCellValue('U'.$i, $totaltareasurCm)
                ->setCellValue('V'.$i, $totaltareasurCt)
                ->setCellValue('W'.$i, $totaltareasur)
                ->setCellValue('X'.$i, $totalporctareasempresasur);
            
            $divisorUA = ($totaltareasuaR+$totaltareasuaV1+$totaltareasuaV2+$totaltareasuaCm+$totaltareasuaCt);
            $divisorUR = ($totaltareasurR+$totaltareasurV1+$totaltareasurV2+$totaltareasurCm+$totaltareasurCt);
            if($divisorUA != 0)
            {
                $getActiveSheet
                    ->setCellValue('N'.$i, (($totaltareasuaCm+$totaltareasuaCt)/$divisorUA));
            }
            else
            {
                $getActiveSheet->setCellValue('N'.$i, 0);
            }
            if($divisorUR != 0)
            {
                $getActiveSheet
                    ->setCellValue('Y'.$i, (($totaltareasurCm+$totaltareasurCt)/$divisorUR));
            }
            else
            {
                $getActiveSheet->setCellValue('Y'.$i, 0);
            }
            $getActiveSheet
                ->setCellValue('M5', (($totaltareasuaCm+$totaltareasuaCt)/$divisorUA));
            $getActiveSheet
                ->setCellValue('X5', (($totaltareasurCm+$totaltareasurCt)/$divisorUR));
            $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloTituloTotal);
            $getActiveSheet->getStyle('E'.$i.':M'.$i)->applyFromArray($estiloTituloTotal);
            $getActiveSheet->getStyle('P'.$i.':X'.$i)->applyFromArray($estiloTituloTotal);
            $getActiveSheet->getStyle('N'.$i)->applyFromArray($estiloDatospCump);
            $getActiveSheet->getStyle('Y'.$i)->applyFromArray($estiloDatospCump);
            
            $i = $i+3;
        }
        else
        {
            foreach($gciacorp as $valor)
            {
                $totaltareasuaA=$totaltareasuaA+$valor['ua_q_tareas_A'];
                $totaltareasuaA2=$totaltareasuaA2+$valor['ua_q_tareas_A2'];
                $totaltareasuaR=$totaltareasuaR+$valor['ua_q_tareas_R'];
                $totaltareasuaV1=$totaltareasuaV1+$valor['ua_q_tareas_V1'];
                $totaltareasuaV2=$totaltareasuaV2+$valor['ua_q_tareas_V2'];
                $totaltareasuaCm=$totaltareasuaCm+$valor['ua_q_tareas_Cm'];
                $totaltareasuaCt=$totaltareasuaCt+$valor['ua_q_tareas_Ct'];
                $totaltareasua=$totaltareasua+$valor['ua_q_tareas_t'];
                $totaltareasurA=$totaltareasurA+$valor['ur_q_tareas_A'];
                $totaltareasurA2=$totaltareasurA2+$valor['ur_q_tareas_A2'];
                $totaltareasurR=$totaltareasurR+$valor['ur_q_tareas_R'];
                $totaltareasurV1=$totaltareasurV1+$valor['ur_q_tareas_V1'];
                $totaltareasurV2=$totaltareasurV2+$valor['ur_q_tareas_V2'];
                $totaltareasurCm=$totaltareasurCm+$valor['ur_q_tareas_Cm'];
                $totaltareasurCt=$totaltareasurCt+$valor['ur_q_tareas_Ct'];
                $totaltareasur=$totaltareasur+$valor['ur_q_tareas_t'];
            }
        }
            
        //GERENCIAS CORPORATIVAS
        $getActiveSheet
            ->mergeCells('A'.$i.':D'.$i)
            ->getRowDimension($i)->setRowHeight(27);
        $getActiveSheet
            ->setCellValue('A'.$i, 'Gerencias Corporativas');
        $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloTitulosAreas);
        $i++;
        foreach($gciacorp as $valor)
        {
            $getActiveSheet
                    ->mergeCells('A'.$i.':D'.$i)
                    ->setCellValue('A'.$i,$valor['gerencia'])
                    ->setCellValue('E'.$i,$valor['ua_q_tareas_A'])
                    ->setCellValue('F'.$i,$valor['ua_q_tareas_A2'])
                    ->setCellValue('G'.$i,$valor['ua_q_tareas_R'])
                    ->setCellValue('H'.$i,$valor['ua_q_tareas_V1'])
                    ->setCellValue('I'.$i,$valor['ua_q_tareas_V2'])
                    ->setCellValue('J'.$i,$valor['ua_q_tareas_Cm'])
                    ->setCellValue('K'.$i,$valor['ua_q_tareas_Ct'])
                    ->setCellValue('L'.$i,$valor['ua_q_tareas_t'])
                    ->setCellValue('M'.$i,($valor['ua_q_tareas_t']/$totaltareasua))
                    ->setCellValue('P'.$i,$valor['ur_q_tareas_A'])
                    ->setCellValue('Q'.$i,$valor['ur_q_tareas_A2'])
                    ->setCellValue('R'.$i,$valor['ur_q_tareas_R'])
                    ->setCellValue('S'.$i,$valor['ur_q_tareas_V1'])
                    ->setCellValue('T'.$i,$valor['ur_q_tareas_V2'])
                    ->setCellValue('U'.$i,$valor['ur_q_tareas_Cm'])
                    ->setCellValue('V'.$i,$valor['ur_q_tareas_Ct'])
                    ->setCellValue('W'.$i,$valor['ur_q_tareas_t'])
                    ->setCellValue('X'.$i,($valor['ur_q_tareas_t']/$totaltareasua));
            
            $totalporctareascorpua = $totalporctareascorpua + ($valor['ua_q_tareas_t']/$totaltareasua);
            $totalporctareascorpur = $totalporctareascorpur + ($valor['ur_q_tareas_t']/$totaltareasur);

            $divisorUA = ($valor['ua_q_tareas_R']+$valor['ua_q_tareas_V1']+$valor['ua_q_tareas_V2']+$valor['ua_q_tareas_Cm']+$valor['ua_q_tareas_Ct']);
            $divisorUR = ($valor['ur_q_tareas_R']+$valor['ur_q_tareas_V1']+$valor['ur_q_tareas_V2']+$valor['ur_q_tareas_Cm']+$valor['ur_q_tareas_Ct']);

            if($divisorUA != 0)
            {
                $getActiveSheet
                    ->setCellValue('N'.$i, (($valor['ua_q_tareas_Cm']+$valor['ua_q_tareas_Ct'])/$divisorUA));
            }
            else
            {
                $getActiveSheet->setCellValue('N'.$i, 0);
            }
            if($divisorUR != 0) 
            {
                $getActiveSheet
                    ->setCellValue('Y'.$i, (($valor['ur_q_tareas_Cm']+$valor['ur_q_tareas_Ct'])/$divisorUR));
            }
            else
            {
                $getActiveSheet->setCellValue('Y'.$i, 0);
            }

            $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloGciaCorp);
            $getActiveSheet->getStyle('E'.$i)->applyFromArray($estiloDatosA);
            $getActiveSheet->getStyle('F'.$i)->applyFromArray($estiloDatosA2);
            $getActiveSheet->getStyle('G'.$i)->applyFromArray($estiloDatosR);
            $getActiveSheet->getStyle('H'.$i)->applyFromArray($estiloDatosV1);
            $getActiveSheet->getStyle('I'.$i)->applyFromArray($estiloDatosV2);
            $getActiveSheet->getStyle('J'.$i)->applyFromArray($estiloDatosCm);
            $getActiveSheet->getStyle('K'.$i)->applyFromArray($estiloDatosCt);
            $getActiveSheet->getStyle('L'.$i)->applyFromArray($estiloDatosTot);
            $getActiveSheet->getStyle('M'.$i)->applyFromArray($estiloDatospTot);
            $getActiveSheet->getStyle('N'.$i)->applyFromArray($estiloDatospCump);
            $getActiveSheet->getStyle('P'.$i)->applyFromArray($estiloDatosA);
            $getActiveSheet->getStyle('Q'.$i)->applyFromArray($estiloDatosA2);
            $getActiveSheet->getStyle('R'.$i)->applyFromArray($estiloDatosR);
            $getActiveSheet->getStyle('S'.$i)->applyFromArray($estiloDatosV1);
            $getActiveSheet->getStyle('T'.$i)->applyFromArray($estiloDatosV2);
            $getActiveSheet->getStyle('U'.$i)->applyFromArray($estiloDatosCm);
            $getActiveSheet->getStyle('V'.$i)->applyFromArray($estiloDatosCt);
            $getActiveSheet->getStyle('W'.$i)->applyFromArray($estiloDatosTot);
            $getActiveSheet->getStyle('X'.$i)->applyFromArray($estiloDatospTot);
            $getActiveSheet->getStyle('Y'.$i)->applyFromArray($estiloDatospCump);
            $i++;
        }

        $this->excel->getActiveSheet()//totales
            ->mergeCells('A'.$i.':D'.$i)
            ->setCellValue('A'.$i, 'Total')
            ->setCellValue('E'.$i, $totaltareasuaA)
            ->setCellValue('F'.$i, $totaltareasuaA2)
            ->setCellValue('G'.$i, $totaltareasuaR)
            ->setCellValue('H'.$i, $totaltareasuaV1)
            ->setCellValue('I'.$i, $totaltareasuaV2)
            ->setCellValue('J'.$i, $totaltareasuaCm)
            ->setCellValue('K'.$i, $totaltareasuaCt)
            ->setCellValue('L'.$i, $totaltareasua)
            ->setCellValue('M'.$i, $totalporctareascorpua)
            ->setCellValue('P'.$i, $totaltareasurA)
            ->setCellValue('Q'.$i, $totaltareasurA2)
            ->setCellValue('R'.$i, $totaltareasurR)
            ->setCellValue('S'.$i, $totaltareasurV1)
            ->setCellValue('T'.$i, $totaltareasurV2)
            ->setCellValue('U'.$i, $totaltareasurCm)
            ->setCellValue('V'.$i, $totaltareasurCt)
            ->setCellValue('W'.$i, $totaltareasur)
            ->setCellValue('X'.$i, $totalporctareascorpur);   
        $divisorUA = ($totaltareasuaR+$totaltareasuaV1+$totaltareasuaV2+$totaltareasuaCm+$totaltareasuaCt);
        $divisorUR = ($totaltareasurR+$totaltareasurV1+$totaltareasurV2+$totaltareasurCm+$totaltareasurCt);

        if($divisorUA != 0)
        {
            $getActiveSheet
                ->setCellValue('N'.$i, (($totaltareasuaCm+$totaltareasuaCt)/$divisorUA));
        }
        else
        {
            $getActiveSheet->setCellValue('N'.$i, 0);
        }
        if($divisorUR != 0) 
        {
            $getActiveSheet
                ->setCellValue('Y'.$i, (($totaltareasurCm+$totaltareasurCt)/$divisorUR));
        }
        else
        {
            $getActiveSheet->setCellValue('Y'.$i, 0);
        }
         if($id_empresa != 1)
            {
                $getActiveSheet
                    ->setCellValue('M5', (($totaltareasuaCm+$totaltareasuaCt)/$divisorUA));
                $getActiveSheet
                    ->setCellValue('X5', (($totaltareasurCm+$totaltareasurCt)/$divisorUR));
            }
        $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloTituloTotal);
        $getActiveSheet->getStyle('E'.$i.':M'.$i)->applyFromArray($estiloTituloTotal);
        $getActiveSheet->getStyle('P'.$i.':X'.$i)->applyFromArray($estiloTituloTotal);
        $getActiveSheet->getStyle('N'.$i)->applyFromArray($estiloDatospCump);
        $getActiveSheet->getStyle('Y'.$i)->applyFromArray($estiloDatospCump);
        $i=$i+3;

        foreach($gcia as $linea)//TOTALIZO POR GERENCIAS NO CORPORATIVAS
        {
            if($linea['empresa'] == 2)
            {
                $totaltareasuaAg=$totaltareasuaAg+$linea['ua_q_tareas_A'];
                $totaltareasuaA2g=$totaltareasuaA2g+$linea['ua_q_tareas_A2'];
                $totaltareasuaRg=$totaltareasuaRg+$linea['ua_q_tareas_R'];
                $totaltareasuaV1g=$totaltareasuaV1g+$linea['ua_q_tareas_V1'];
                $totaltareasuaV2g=$totaltareasuaV2g+$linea['ua_q_tareas_V2'];
                $totaltareasuaCmg=$totaltareasuaCmg+$linea['ua_q_tareas_Cm'];
                $totaltareasuaCtg=$totaltareasuaCtg+$linea['ua_q_tareas_Ct'];
                $totaltareasuag=$totaltareasuag+$linea['ua_q_tareas_t'];
                $totaltareasurAg=$totaltareasurAg+$linea['ur_q_tareas_A'];
                $totaltareasurA2g=$totaltareasurA2g+$linea['ur_q_tareas_A2'];
                $totaltareasurRg=$totaltareasurRg+$linea['ur_q_tareas_R'];
                $totaltareasurV1g=$totaltareasurV1g+$linea['ur_q_tareas_V1'];
                $totaltareasurV2g=$totaltareasurV2g+$linea['ur_q_tareas_V2'];
                $totaltareasurCmg=$totaltareasurCmg+$linea['ur_q_tareas_Cm'];
                $totaltareasurCtg=$totaltareasurCtg+$linea['ur_q_tareas_Ct'];
                $totaltareasurg=$totaltareasurg+$linea['ur_q_tareas_t'];
            }
            else
            {
                $totaltareasuaAgb=$totaltareasuaAgb+$linea['ua_q_tareas_A'];
                $totaltareasuaA2gb=$totaltareasuaA2gb+$linea['ua_q_tareas_A2'];
                $totaltareasuaRgb=$totaltareasuaRgb+$linea['ua_q_tareas_R'];
                $totaltareasuaV1gb=$totaltareasuaV1gb+$linea['ua_q_tareas_V1'];
                $totaltareasuaV2gb=$totaltareasuaV2gb+$linea['ua_q_tareas_V2'];
                $totaltareasuaCmgb=$totaltareasuaCmgb+$linea['ua_q_tareas_Cm'];
                $totaltareasuaCtgb=$totaltareasuaCtgb+$linea['ua_q_tareas_Ct'];
                $totaltareasuagb=$totaltareasuagb+$linea['ua_q_tareas_t'];
                $totaltareasurAgb=$totaltareasurAgb+$linea['ur_q_tareas_A'];
                $totaltareasurA2gb=$totaltareasurA2gb+$linea['ur_q_tareas_A2'];
                $totaltareasurRgb=$totaltareasurRgb+$linea['ur_q_tareas_R'];
                $totaltareasurV1gb=$totaltareasurV1gb+$linea['ur_q_tareas_V1'];
                $totaltareasurV2gb=$totaltareasurV2gb+$linea['ur_q_tareas_V2'];
                $totaltareasurCmgb=$totaltareasurCmgb+$linea['ur_q_tareas_Cm'];
                $totaltareasurCtgb=$totaltareasurCtgb+$linea['ur_q_tareas_Ct'];
                $totaltareasurgb=$totaltareasurgb+$linea['ur_q_tareas_t'];
            }
        }
        
        if($id_empresa == 1 || $id_empresa == 2)
        {
        //SALES DE JUJUY
            $getActiveSheet
                ->mergeCells('A'.$i.':D'.$i)
                ->setCellValue('A'.$i, 'Gerencias No Corporativas');
            $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloTitulosAreasNoCorp);
            $i++;

            $getActiveSheet
                ->mergeCells('A'.$i.':D'.$i)
                ->setCellValue('A'.$i, 'Sales de Jujuy');
            $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloTitulosAreasNoCorp);
            $i++;

            foreach($gcia as $valor)
            {
                if($valor['empresa'] == 2)
                {
                    $getActiveSheet
                        ->mergeCells('A'.$i.':D'.$i)
                        ->setCellValue('A'.$i,$valor['gerencia'])
                        ->setCellValue('E'.$i,$valor['ua_q_tareas_A'])
                        ->setCellValue('F'.$i,$valor['ua_q_tareas_A2'])
                        ->setCellValue('G'.$i,$valor['ua_q_tareas_R'])
                        ->setCellValue('H'.$i,$valor['ua_q_tareas_V1'])
                        ->setCellValue('I'.$i,$valor['ua_q_tareas_V2'])
                        ->setCellValue('J'.$i,$valor['ua_q_tareas_Cm'])
                        ->setCellValue('K'.$i,$valor['ua_q_tareas_Ct'])
                        ->setCellValue('L'.$i,$valor['ua_q_tareas_t'])
                        ->setCellValue('M'.$i,($valor['ua_q_tareas_t']/$totaltareasua))
                        ->setCellValue('P'.$i,$valor['ur_q_tareas_A'])
                        ->setCellValue('Q'.$i,$valor['ur_q_tareas_A2'])
                        ->setCellValue('R'.$i,$valor['ur_q_tareas_R'])
                        ->setCellValue('S'.$i,$valor['ur_q_tareas_V1'])
                        ->setCellValue('T'.$i,$valor['ur_q_tareas_V2'])
                        ->setCellValue('U'.$i,$valor['ur_q_tareas_Cm'])
                        ->setCellValue('V'.$i,$valor['ur_q_tareas_Ct'])
                        ->setCellValue('W'.$i,$valor['ur_q_tareas_t'])
                        ->setCellValue('X'.$i,($valor['ur_q_tareas_t']/$totaltareasur));
                $totalporctareasgciaua = $totalporctareasgciaua + ($valor['ua_q_tareas_t']/$totaltareasua);
                $totalporctareasgciaur = $totalporctareasgciaur + ($valor['ur_q_tareas_t']/$totaltareasur);
                $divisorUA = ($valor['ua_q_tareas_R']+$valor['ua_q_tareas_V1']+$valor['ua_q_tareas_V2']+$valor['ua_q_tareas_Cm']+$valor['ua_q_tareas_Ct']);
                $divisorUR = ($valor['ur_q_tareas_R']+$valor['ur_q_tareas_V1']+$valor['ur_q_tareas_V2']+$valor['ur_q_tareas_Cm']+$valor['ur_q_tareas_Ct']);

                if($divisorUA != 0)
                {
                    $getActiveSheet
                        ->setCellValue('N'.$i, (($valor['ua_q_tareas_Cm']+$valor['ua_q_tareas_Ct'])/$divisorUA));
                }
                else
                {
                    $getActiveSheet->setCellValue('N'.$i, 0);
                }
                if($divisorUR != 0) 
                {
                    $getActiveSheet
                        ->setCellValue('Y'.$i, (($valor['ur_q_tareas_Cm']+$valor['ur_q_tareas_Ct'])/$divisorUR));
                }
                else
                {
                    $getActiveSheet->setCellValue('Y'.$i, 0);
                }
                $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloGciaCorp);
                $getActiveSheet->getStyle('E'.$i)->applyFromArray($estiloDatosA);
                $getActiveSheet->getStyle('F'.$i)->applyFromArray($estiloDatosA2);
                $getActiveSheet->getStyle('G'.$i)->applyFromArray($estiloDatosR);
                $getActiveSheet->getStyle('H'.$i)->applyFromArray($estiloDatosV1);
                $getActiveSheet->getStyle('I'.$i)->applyFromArray($estiloDatosV2);
                $getActiveSheet->getStyle('J'.$i)->applyFromArray($estiloDatosCm);
                $getActiveSheet->getStyle('K'.$i)->applyFromArray($estiloDatosCt);
                $getActiveSheet->getStyle('L'.$i)->applyFromArray($estiloDatosTot);
                $getActiveSheet->getStyle('M'.$i)->applyFromArray($estiloDatospTot);
                $getActiveSheet->getStyle('N'.$i)->applyFromArray($estiloDatospCump);
                $getActiveSheet->getStyle('P'.$i)->applyFromArray($estiloDatosA);
                $getActiveSheet->getStyle('Q'.$i)->applyFromArray($estiloDatosA2);
                $getActiveSheet->getStyle('R'.$i)->applyFromArray($estiloDatosR);
                $getActiveSheet->getStyle('S'.$i)->applyFromArray($estiloDatosV1);
                $getActiveSheet->getStyle('T'.$i)->applyFromArray($estiloDatosV2);
                $getActiveSheet->getStyle('U'.$i)->applyFromArray($estiloDatosCm);
                $getActiveSheet->getStyle('V'.$i)->applyFromArray($estiloDatosCt);
                $getActiveSheet->getStyle('W'.$i)->applyFromArray($estiloDatosTot);
                $getActiveSheet->getStyle('X'.$i)->applyFromArray($estiloDatospTot);
                $getActiveSheet->getStyle('Y'.$i)->applyFromArray($estiloDatospCump);
                    $i++;
                }
            }
            $this->excel->getActiveSheet()//totales
                ->mergeCells('A'.$i.':D'.$i)
                ->setCellValue('A'.$i, 'Total')
                ->setCellValue('E'.$i, $totaltareasuaAg)
                ->setCellValue('F'.$i, $totaltareasuaA2g)
                ->setCellValue('G'.$i, $totaltareasuaRg)
                ->setCellValue('H'.$i, $totaltareasuaV1g)
                ->setCellValue('I'.$i, $totaltareasuaV2g)
                ->setCellValue('J'.$i, $totaltareasuaCmg)
                ->setCellValue('K'.$i, $totaltareasuaCtg)
                ->setCellValue('L'.$i, $totaltareasuag)
                ->setCellValue('M'.$i, $totalporctareasgciaua)
                ->setCellValue('P'.$i, $totaltareasurAg)
                ->setCellValue('Q'.$i, $totaltareasurA2g)
                ->setCellValue('R'.$i, $totaltareasurRg)
                ->setCellValue('S'.$i, $totaltareasurV1g)
                ->setCellValue('T'.$i, $totaltareasurV2g)
                ->setCellValue('U'.$i, $totaltareasurCmg)
                ->setCellValue('V'.$i, $totaltareasurCtg)
                ->setCellValue('W'.$i, $totaltareasurg)
                ->setCellValue('X'.$i, $totalporctareasgciaur); 
            $divisorUA = ($totaltareasuaRg+$totaltareasuaV1g+$totaltareasuaV2g+$totaltareasuaCmg+$totaltareasuaCtg);
            $divisorUR = ($totaltareasurRg+$totaltareasurV1g+$totaltareasurV2g+$totaltareasurCmg+$totaltareasurCtg);

            if($divisorUA != 0)
            {
                $getActiveSheet
                    ->setCellValue('N'.$i, (($totaltareasuaCmg+$totaltareasuaCtg)/$divisorUA));
            }
            else
            {
                $getActiveSheet->setCellValue('N'.$i, 0);
            }
            if($divisorUR != 0) 
            {
                $getActiveSheet
                    ->setCellValue('Y'.$i, (($totaltareasurCmg+$totaltareasurCtg)/$divisorUR));
            }
            else
            {
                $getActiveSheet->setCellValue('Y'.$i, 0);
            }
            $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloTituloTotal);
            $getActiveSheet->getStyle('E'.$i.':M'.$i)->applyFromArray($estiloTituloTotal);
            $getActiveSheet->getStyle('P'.$i.':X'.$i)->applyFromArray($estiloTituloTotal);
            $getActiveSheet->getStyle('N'.$i)->applyFromArray($estiloDatospCump);
            $getActiveSheet->getStyle('Y'.$i)->applyFromArray($estiloDatospCump);
            $i=$i+3;
        }
        
        if($id_empresa == 1 || $id_empresa == 3)
        {
            //BORAX ARGENTINA
            $getActiveSheet
                ->mergeCells('A'.$i.':D'.$i)
                ->setCellValue('A'.$i, 'Gerencias No Corporativas');
            $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloTitulosAreasNoCorp);
            $i++;

            $getActiveSheet
                ->mergeCells('A'.$i.':D'.$i)
                ->setCellValue('A'.$i, 'Borax Argentina');
            $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloTitulosAreasNoCorp);
            $i++;
            foreach($gcia as $valor)
            {
                if($valor['empresa'] == 3)
                {
                    $getActiveSheet
                        ->mergeCells('A'.$i.':D'.$i)
                        ->setCellValue('A'.$i,$valor['gerencia'])
                        ->setCellValue('E'.$i,$valor['ua_q_tareas_A'])
                        ->setCellValue('F'.$i,$valor['ua_q_tareas_A2'])
                        ->setCellValue('G'.$i,$valor['ua_q_tareas_R'])
                        ->setCellValue('H'.$i,$valor['ua_q_tareas_V1'])
                        ->setCellValue('I'.$i,$valor['ua_q_tareas_V2'])
                        ->setCellValue('J'.$i,$valor['ua_q_tareas_Cm'])
                        ->setCellValue('K'.$i,$valor['ua_q_tareas_Ct'])
                        ->setCellValue('L'.$i,$valor['ua_q_tareas_t'])
                        ->setCellValue('M'.$i,($valor['ua_q_tareas_t']/$totaltareasua))
                        ->setCellValue('P'.$i,$valor['ur_q_tareas_A'])
                        ->setCellValue('Q'.$i,$valor['ur_q_tareas_A2'])
                        ->setCellValue('R'.$i,$valor['ur_q_tareas_R'])
                        ->setCellValue('S'.$i,$valor['ur_q_tareas_V1'])
                        ->setCellValue('T'.$i,$valor['ur_q_tareas_V2'])
                        ->setCellValue('U'.$i,$valor['ur_q_tareas_Cm'])
                        ->setCellValue('V'.$i,$valor['ur_q_tareas_Ct'])
                        ->setCellValue('W'.$i,$valor['ur_q_tareas_t'])
                        ->setCellValue('X'.$i,($valor['ur_q_tareas_t']/$totaltareasur));
                $totalporctareasgciauab = $totalporctareasgciauab + ($valor['ua_q_tareas_t']/$totaltareasua);
                $totalporctareasgciaurb = $totalporctareasgciaurb + ($valor['ur_q_tareas_t']/$totaltareasur);
                $divisorUA = ($valor['ua_q_tareas_R']+$valor['ua_q_tareas_V1']+$valor['ua_q_tareas_V2']+$valor['ua_q_tareas_Cm']+$valor['ua_q_tareas_Ct']);
                $divisorUR = ($valor['ur_q_tareas_R']+$valor['ur_q_tareas_V1']+$valor['ur_q_tareas_V2']+$valor['ur_q_tareas_Cm']+$valor['ur_q_tareas_Ct']);

                if($divisorUA != 0)
                {
                    $getActiveSheet
                        ->setCellValue('N'.$i, (($valor['ua_q_tareas_Cm']+$valor['ua_q_tareas_Ct'])/$divisorUA));
                }
                else
                {
                    $getActiveSheet->setCellValue('N'.$i, 0);
                }
                if($divisorUR != 0) 
                {
                    $getActiveSheet
                        ->setCellValue('Y'.$i, (($valor['ur_q_tareas_Cm']+$valor['ur_q_tareas_Ct'])/$divisorUR));
                }
                else
                {
                    $getActiveSheet->setCellValue('Y'.$i, 0);
                }
                $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloGciaCorp);
                $getActiveSheet->getStyle('E'.$i)->applyFromArray($estiloDatosA);
                $getActiveSheet->getStyle('F'.$i)->applyFromArray($estiloDatosA2);
                $getActiveSheet->getStyle('G'.$i)->applyFromArray($estiloDatosR);
                $getActiveSheet->getStyle('H'.$i)->applyFromArray($estiloDatosV1);
                $getActiveSheet->getStyle('I'.$i)->applyFromArray($estiloDatosV2);
                $getActiveSheet->getStyle('J'.$i)->applyFromArray($estiloDatosCm);
                $getActiveSheet->getStyle('K'.$i)->applyFromArray($estiloDatosCt);
                $getActiveSheet->getStyle('L'.$i)->applyFromArray($estiloDatosTot);
                $getActiveSheet->getStyle('M'.$i)->applyFromArray($estiloDatospTot);
                $getActiveSheet->getStyle('N'.$i)->applyFromArray($estiloDatospCump);
                $getActiveSheet->getStyle('P'.$i)->applyFromArray($estiloDatosA);
                $getActiveSheet->getStyle('Q'.$i)->applyFromArray($estiloDatosA2);
                $getActiveSheet->getStyle('R'.$i)->applyFromArray($estiloDatosR);
                $getActiveSheet->getStyle('S'.$i)->applyFromArray($estiloDatosV1);
                $getActiveSheet->getStyle('T'.$i)->applyFromArray($estiloDatosV2);
                $getActiveSheet->getStyle('U'.$i)->applyFromArray($estiloDatosCm);
                $getActiveSheet->getStyle('V'.$i)->applyFromArray($estiloDatosCt);
                $getActiveSheet->getStyle('W'.$i)->applyFromArray($estiloDatosTot);
                $getActiveSheet->getStyle('X'.$i)->applyFromArray($estiloDatospTot);
                $getActiveSheet->getStyle('Y'.$i)->applyFromArray($estiloDatospCump);
                $i++;
                }
            }
            $this->excel->getActiveSheet()//imprimo totales
                ->mergeCells('A'.$i.':D'.$i)
                ->setCellValue('A'.$i, 'Total')
                ->setCellValue('E'.$i, $totaltareasuaAgb)
                ->setCellValue('F'.$i, $totaltareasuaA2gb)
                ->setCellValue('G'.$i, $totaltareasuaRgb)
                ->setCellValue('H'.$i, $totaltareasuaV1gb)
                ->setCellValue('I'.$i, $totaltareasuaV2gb)
                ->setCellValue('J'.$i, $totaltareasuaCmgb)
                ->setCellValue('K'.$i, $totaltareasuaCtgb)
                ->setCellValue('L'.$i, $totaltareasuagb)
                ->setCellValue('M'.$i, $totalporctareasgciauab)
                ->setCellValue('P'.$i, $totaltareasurAgb)
                ->setCellValue('Q'.$i, $totaltareasurA2gb)
                ->setCellValue('R'.$i, $totaltareasurRgb)
                ->setCellValue('S'.$i, $totaltareasurV1gb)
                ->setCellValue('T'.$i, $totaltareasurV2gb)
                ->setCellValue('U'.$i, $totaltareasurCmgb)
                ->setCellValue('V'.$i, $totaltareasurCtgb)
                ->setCellValue('W'.$i, $totaltareasurgb)
                ->setCellValue('X'.$i, $totalporctareasgciaurb);            
            $divisorUA = ($totaltareasuaRgb+$totaltareasuaV1gb+$totaltareasuaV2gb+$totaltareasuaCmgb+$totaltareasuaCtgb);
            $divisorUR = ($totaltareasurRgb+$totaltareasurV1gb+$totaltareasurV2gb+$totaltareasurCmgb+$totaltareasurCtgb);

            if($divisorUA != 0)
            {
                $getActiveSheet
                    ->setCellValue('N'.$i, (($totaltareasuaCmgb+$totaltareasuaCtgb)/$divisorUA));
            }
            else
            {
                $getActiveSheet->setCellValue('N'.$i, 0);
            }
            if($divisorUR != 0) 
            {
                $getActiveSheet
                    ->setCellValue('Y'.$i, (($totaltareasurCmgb+$totaltareasurCtgb)/$divisorUR));
            }
            else
            {
                $getActiveSheet->setCellValue('Y'.$i, 0);
            }
            $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloTituloTotal);
            $getActiveSheet->getStyle('E'.$i.':M'.$i)->applyFromArray($estiloTituloTotal);
            $getActiveSheet->getStyle('P'.$i.':X'.$i)->applyFromArray($estiloTituloTotal);
            $getActiveSheet->getStyle('N'.$i)->applyFromArray($estiloDatospCump);
            $getActiveSheet->getStyle('Y'.$i)->applyFromArray($estiloDatospCump);
            $i=$i+3;
        }

//      CARGA DE USUARIOS
        $this->excel->getActiveSheet()
            ->mergeCells('A'.$i.':D'.($i+2))
            ->setCellValue('A'.$i, 'Usuarios');
        $this->excel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($estiloTitulosAreas);
        $i = $i + 2;

        foreach($areas as $area)
        {
            if($id_empresa != 1)//suma el total de tareas dependiendo si el reporte es para ORO o no
            {
                $totales=$this->totalizaPorGerenciaCorp($usuarios,$id_empresa,$numeros,$area['id']);
                $i++;
                foreach($totales as $total)
                {
                    $this->excel->getActiveSheet()
                        ->mergeCells('A'.$i.':D'.$i)
                        ->setCellValue('A'.$i, $area['gerencia'])
                        ->setCellValue('E'.$i, $total['ua_q_tareas_A'])
                        ->setCellValue('F'.$i, $total['ua_q_tareas_A2'])
                        ->setCellValue('G'.$i, $total['ua_q_tareas_R'])
                        ->setCellValue('H'.$i, $total['ua_q_tareas_V1'])
                        ->setCellValue('I'.$i, $total['ua_q_tareas_V2'])
                        ->setCellValue('J'.$i, $total['ua_q_tareas_Cm'])
                        ->setCellValue('K'.$i, $total['ua_q_tareas_Ct'])
                        ->setCellValue('L'.$i, $total['ua_q_tareas_t'])
                        ->setCellValue('M'.$i, ($total['ua_q_tareas_t']/$totaltareasua))
                        ->setCellValue('P'.$i, $total['ur_q_tareas_A'])
                        ->setCellValue('Q'.$i, $total['ur_q_tareas_A2'])
                        ->setCellValue('R'.$i, $total['ur_q_tareas_V1'])
                        ->setCellValue('S'.$i, $total['ur_q_tareas_V2'])
                        ->setCellValue('T'.$i, $total['ur_q_tareas_R'])
                        ->setCellValue('U'.$i, $total['ur_q_tareas_Cm'])
                        ->setCellValue('V'.$i, $total['ur_q_tareas_Ct'])
                        ->setCellValue('W'.$i, $total['ur_q_tareas_t'])
                        ->setCellValue('X'.$i, ($total['ur_q_tareas_t']/$totaltareasua));
                    
                    $divisorUA = ($total['ua_q_tareas_R']+$total['ua_q_tareas_V1']+$total['ua_q_tareas_V2']+$total['ua_q_tareas_Cm']+$total['ua_q_tareas_Ct']);
                    $divisorUR = ($total['ur_q_tareas_R']+$total['ur_q_tareas_V1']+$total['ur_q_tareas_V2']+$total['ur_q_tareas_Cm']+$total['ur_q_tareas_Ct']);

                    if($divisorUA != 0)
                    {
                        $getActiveSheet
                            ->setCellValue('N'.$i, (($total['ua_q_tareas_Cm']+$total['ua_q_tareas_Ct'])/$divisorUA));
                    }
                    else
                    {
                        $getActiveSheet->setCellValue('N'.$i, 0);
                    }
                    if($divisorUR != 0) 
                    {
                        $getActiveSheet
                            ->setCellValue('Y'.$i, (($total['ur_q_tareas_Cm']+$total['ur_q_tareas_Ct'])/$divisorUR));
                    }
                    else
                    {
                        $getActiveSheet->setCellValue('Y'.$i, 0);
                    }
                    $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloGerenciasCorp);
                    $getActiveSheet->getStyle('E'.$i.':M'.$i)->applyFromArray($estiloDatosCorp);
                    $getActiveSheet->getStyle('P'.$i.':X'.$i)->applyFromArray($estiloDatosCorp);
                    $getActiveSheet->getStyle('N'.$i)->applyFromArray($estiloDatospCump);
                    $getActiveSheet->getStyle('Y'.$i)->applyFromArray($estiloDatospCump);
                    $i++;
                }
            }
            else
            {
                $i++;
                $this->excel->getActiveSheet()
                    ->mergeCells('A'.$i.':D'.$i)
                    ->setCellValue('A'.$i, $area['gerencia'])
                    ->setCellValue('E'.$i, $area['ua_q_tareas_A'])
                    ->setCellValue('F'.$i, $area['ua_q_tareas_A2'])
                    ->setCellValue('G'.$i, $area['ua_q_tareas_R'])
                    ->setCellValue('H'.$i, $area['ua_q_tareas_V1'])
                    ->setCellValue('I'.$i, $area['ua_q_tareas_V2'])
                    ->setCellValue('J'.$i, $area['ua_q_tareas_Cm'])
                    ->setCellValue('K'.$i, $area['ua_q_tareas_Ct'])
                    ->setCellValue('L'.$i, $area['ua_q_tareas_t'])
                    ->setCellValue('M'.$i, ($area['ua_q_tareas_t']/$totaltareasua))
                    ->setCellValue('P'.$i, $area['ur_q_tareas_A'])
                    ->setCellValue('Q'.$i, $area['ur_q_tareas_A2'])
                    ->setCellValue('R'.$i, $area['ur_q_tareas_V1'])
                    ->setCellValue('S'.$i, $area['ur_q_tareas_V2'])
                    ->setCellValue('T'.$i, $area['ur_q_tareas_R'])
                    ->setCellValue('U'.$i, $area['ur_q_tareas_Cm'])
                    ->setCellValue('V'.$i, $area['ur_q_tareas_Ct'])
                    ->setCellValue('W'.$i, $area['ur_q_tareas_t'])
                    ->setCellValue('X'.$i, ($area['ur_q_tareas_t']/$totaltareasua));

                $divisorUA = ($area['ua_q_tareas_R']+$area['ua_q_tareas_V1']+$area['ua_q_tareas_V2']+$area['ua_q_tareas_Cm']+$area['ua_q_tareas_Ct']);
                $divisorUR = ($area['ur_q_tareas_R']+$area['ur_q_tareas_V1']+$area['ur_q_tareas_V2']+$area['ur_q_tareas_Cm']+$area['ur_q_tareas_Ct']);

                if($divisorUA != 0)
                {
                    $getActiveSheet
                        ->setCellValue('N'.$i, (($area['ua_q_tareas_Cm']+$area['ua_q_tareas_Ct'])/$divisorUA));
                }
                else
                {
                    $getActiveSheet->setCellValue('N'.$i, 0);
                }
                if($divisorUR != 0) 
                {
                    $getActiveSheet
                        ->setCellValue('Y'.$i, (($area['ur_q_tareas_Cm']+$area['ur_q_tareas_Ct'])/$divisorUR));
                }
                else
                {
                    $getActiveSheet->setCellValue('Y'.$i, 0);
                }
                $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloGerenciasCorp);
                $getActiveSheet->getStyle('E'.$i.':M'.$i)->applyFromArray($estiloDatosCorp);
                $getActiveSheet->getStyle('P'.$i.':X'.$i)->applyFromArray($estiloDatosCorp);
                $getActiveSheet->getStyle('N'.$i)->applyFromArray($estiloDatospCump);
                $getActiveSheet->getStyle('Y'.$i)->applyFromArray($estiloDatospCump);
                $i++;
            }
            
            foreach($usuarios as $usuario)
            {
                if($id_empresa == 1)
                {
                    if($area['id'] == $usuario['id_corp'])
                    {
                        if(!($area['id'] == 17 || $area['id'] == 52))
                        {
                            $this->excel->getActiveSheet()
                                ->mergeCells('A'.$i.':D'.$i)
                                ->setCellValue('A'.$i, $usuario['usuario'])
                                ->setCellValue('E'.$i,$usuario['ua_q_tareas_A'])
                                ->setCellValue('F'.$i,$usuario['ua_q_tareas_A2'])
                                ->setCellValue('G'.$i,$usuario['ua_q_tareas_R'])
                                ->setCellValue('H'.$i,$usuario['ua_q_tareas_V1'])
                                ->setCellValue('I'.$i,$usuario['ua_q_tareas_V2'])
                                ->setCellValue('J'.$i,$usuario['ua_q_tareas_Cm'])
                                ->setCellValue('K'.$i,$usuario['ua_q_tareas_Ct'])
                                ->setCellValue('L'.$i,$usuario['ua_q_tareas_t'])
                                ->setCellValue('M'.$i,($usuario['ua_q_tareas_t']/$totaltareasua))
                                ->setCellValue('P'.$i,$usuario['ur_q_tareas_A'])
                                ->setCellValue('Q'.$i,$usuario['ur_q_tareas_A2'])
                                ->setCellValue('R'.$i,$usuario['ur_q_tareas_R'])
                                ->setCellValue('S'.$i,$usuario['ur_q_tareas_V1'])
                                ->setCellValue('T'.$i,$usuario['ur_q_tareas_V2'])
                                ->setCellValue('U'.$i,$usuario['ur_q_tareas_Cm'])
                                ->setCellValue('V'.$i,$usuario['ur_q_tareas_Ct'])
                                ->setCellValue('W'.$i,$usuario['ur_q_tareas_t'])
                                ->setCellValue('X'.$i,($usuario['ur_q_tareas_t']/$totaltareasur));
                            $divisorUA = ($usuario['ua_q_tareas_R']+$usuario['ua_q_tareas_V1']+$usuario['ua_q_tareas_V2']+$usuario['ua_q_tareas_Cm']+$usuario['ua_q_tareas_Ct']);
                            $divisorUR = ($usuario['ur_q_tareas_R']+$usuario['ur_q_tareas_V1']+$usuario['ur_q_tareas_V2']+$usuario['ur_q_tareas_Cm']+$usuario['ur_q_tareas_Ct']);

                            if($divisorUA != 0)
                            {
                                $getActiveSheet
                                    ->setCellValue('N'.$i, (($usuario['ua_q_tareas_Cm']+$usuario['ua_q_tareas_Ct'])/$divisorUA));
                            }
                            else
                            {
                                $getActiveSheet->setCellValue('N'.$i, 0);
                            }
                            if($divisorUR != 0) 
                            {
                                $getActiveSheet
                                    ->setCellValue('Y'.$i, (($usuario['ur_q_tareas_Cm']+$usuario['ur_q_tareas_Ct'])/$divisorUR));
                            }
                            else
                            {
                                $getActiveSheet->setCellValue('Y'.$i, 0);
                            }
                            $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloUsuario);
                            $getActiveSheet->getStyle('E'.$i)->applyFromArray($estiloDatosA);
                            $getActiveSheet->getStyle('F'.$i)->applyFromArray($estiloDatosA2);
                            $getActiveSheet->getStyle('G'.$i)->applyFromArray($estiloDatosR);
                            $getActiveSheet->getStyle('H'.$i)->applyFromArray($estiloDatosV1);
                            $getActiveSheet->getStyle('I'.$i)->applyFromArray($estiloDatosV2);
                            $getActiveSheet->getStyle('J'.$i)->applyFromArray($estiloDatosCm);
                            $getActiveSheet->getStyle('K'.$i)->applyFromArray($estiloDatosCt);
                            $getActiveSheet->getStyle('L'.$i)->applyFromArray($estiloDatosTot);
                            $getActiveSheet->getStyle('M'.$i)->applyFromArray($estiloDatospTot);
                            $getActiveSheet->getStyle('N'.$i)->applyFromArray($estiloDatospCump);
                            $getActiveSheet->getStyle('P'.$i)->applyFromArray($estiloDatosA);
                            $getActiveSheet->getStyle('Q'.$i)->applyFromArray($estiloDatosA2);
                            $getActiveSheet->getStyle('R'.$i)->applyFromArray($estiloDatosR);
                            $getActiveSheet->getStyle('S'.$i)->applyFromArray($estiloDatosV1);
                            $getActiveSheet->getStyle('T'.$i)->applyFromArray($estiloDatosV2);
                            $getActiveSheet->getStyle('U'.$i)->applyFromArray($estiloDatosCm);
                            $getActiveSheet->getStyle('V'.$i)->applyFromArray($estiloDatosCt);
                            $getActiveSheet->getStyle('W'.$i)->applyFromArray($estiloDatosTot);
                            $getActiveSheet->getStyle('X'.$i)->applyFromArray($estiloDatospTot);
                            $getActiveSheet->getStyle('Y'.$i)->applyFromArray($estiloDatospCump);
                            $i++;
                        }
                    }
                }
                else
                {
                    if($id_empresa ==2)
                    {
                        if($usuario['id_org'] == 1 || $usuario['id_org'] == 2 ||$usuario['id_org'] == 3)
                        {
                            if($area['id'] == $usuario['id_corp'])
                            {
                                if(!($area['id'] == 17 || $area['id'] == 52))
                                {
                                    $this->excel->getActiveSheet()
                                        ->mergeCells('A'.$i.':D'.$i)
                                        ->setCellValue('A'.$i, $usuario['usuario'])
                                        ->setCellValue('E'.$i,$usuario['ua_q_tareas_A'])
                                        ->setCellValue('F'.$i,$usuario['ua_q_tareas_A2'])
                                        ->setCellValue('G'.$i,$usuario['ua_q_tareas_R'])
                                        ->setCellValue('H'.$i,$usuario['ua_q_tareas_V1'])
                                        ->setCellValue('I'.$i,$usuario['ua_q_tareas_V2'])
                                        ->setCellValue('J'.$i,$usuario['ua_q_tareas_Cm'])
                                        ->setCellValue('K'.$i,$usuario['ua_q_tareas_Ct'])
                                        ->setCellValue('L'.$i,$usuario['ua_q_tareas_t'])
                                        ->setCellValue('M'.$i,($usuario['ua_q_tareas_t']/$totaltareasua))
                                        ->setCellValue('P'.$i,$usuario['ur_q_tareas_A'])
                                        ->setCellValue('Q'.$i,$usuario['ur_q_tareas_A2'])
                                        ->setCellValue('R'.$i,$usuario['ur_q_tareas_R'])
                                        ->setCellValue('S'.$i,$usuario['ur_q_tareas_V1'])
                                        ->setCellValue('T'.$i,$usuario['ur_q_tareas_V2'])
                                        ->setCellValue('U'.$i,$usuario['ur_q_tareas_Cm'])
                                        ->setCellValue('V'.$i,$usuario['ur_q_tareas_Ct'])
                                        ->setCellValue('W'.$i,$usuario['ur_q_tareas_t'])
                                        ->setCellValue('X'.$i,($usuario['ur_q_tareas_t']/$totaltareasur));
                                    $divisorUA = ($usuario['ua_q_tareas_R']+$usuario['ua_q_tareas_V1']+$usuario['ua_q_tareas_V2']+$usuario['ua_q_tareas_Cm']+$usuario['ua_q_tareas_Ct']);
                                    $divisorUR = ($usuario['ur_q_tareas_R']+$usuario['ur_q_tareas_V1']+$usuario['ur_q_tareas_V2']+$usuario['ur_q_tareas_Cm']+$usuario['ur_q_tareas_Ct']);

                                    if($divisorUA != 0)
                                    {
                                        $getActiveSheet
                                            ->setCellValue('N'.$i, (($usuario['ua_q_tareas_Cm']+$usuario['ua_q_tareas_Ct'])/$divisorUA));
                                    }
                                    else
                                    {
                                        $getActiveSheet->setCellValue('N'.$i, 0);
                                    }
                                    if($divisorUR != 0) 
                                    {
                                        $getActiveSheet
                                            ->setCellValue('Y'.$i, (($usuario['ur_q_tareas_Cm']+$usuario['ur_q_tareas_Ct'])/$divisorUR));
                                    }
                                    else
                                    {
                                        $getActiveSheet->setCellValue('Y'.$i, 0);
                                    }
                                    $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloUsuario);
                                    $getActiveSheet->getStyle('E'.$i)->applyFromArray($estiloDatosA);
                                    $getActiveSheet->getStyle('F'.$i)->applyFromArray($estiloDatosA2);
                                    $getActiveSheet->getStyle('G'.$i)->applyFromArray($estiloDatosR);
                                    $getActiveSheet->getStyle('H'.$i)->applyFromArray($estiloDatosV1);
                                    $getActiveSheet->getStyle('I'.$i)->applyFromArray($estiloDatosV2);
                                    $getActiveSheet->getStyle('J'.$i)->applyFromArray($estiloDatosCm);
                                    $getActiveSheet->getStyle('K'.$i)->applyFromArray($estiloDatosCt);
                                    $getActiveSheet->getStyle('L'.$i)->applyFromArray($estiloDatosTot);
                                    $getActiveSheet->getStyle('M'.$i)->applyFromArray($estiloDatospTot);
                                    $getActiveSheet->getStyle('N'.$i)->applyFromArray($estiloDatospCump);
                                    $getActiveSheet->getStyle('P'.$i)->applyFromArray($estiloDatosA);
                                    $getActiveSheet->getStyle('Q'.$i)->applyFromArray($estiloDatosA2);
                                    $getActiveSheet->getStyle('R'.$i)->applyFromArray($estiloDatosR);
                                    $getActiveSheet->getStyle('S'.$i)->applyFromArray($estiloDatosV1);
                                    $getActiveSheet->getStyle('T'.$i)->applyFromArray($estiloDatosV2);
                                    $getActiveSheet->getStyle('U'.$i)->applyFromArray($estiloDatosCm);
                                    $getActiveSheet->getStyle('V'.$i)->applyFromArray($estiloDatosCt);
                                    $getActiveSheet->getStyle('W'.$i)->applyFromArray($estiloDatosTot);
                                    $getActiveSheet->getStyle('X'.$i)->applyFromArray($estiloDatospTot);
                                    $getActiveSheet->getStyle('Y'.$i)->applyFromArray($estiloDatospCump);
                                    $i++;
                                }
                            }
                        }
                    }
                    if($id_empresa == 3)
                    {
                        if($usuario['id_org'] == 1 || $usuario['id_org'] == 4 ||$usuario['id_org'] == 5)
                        {
                            if($area['id'] == $usuario['id_corp'])
                            {
                                if(!($area['id'] == 17 || $area['id'] == 52))
                                {

                                    $this->excel->getActiveSheet()
                                        ->mergeCells('A'.$i.':D'.$i)
                                        ->setCellValue('A'.$i, $usuario['usuario'])
                                        ->setCellValue('E'.$i,$usuario['ua_q_tareas_A'])
                                        ->setCellValue('F'.$i,$usuario['ua_q_tareas_A2'])
                                        ->setCellValue('G'.$i,$usuario['ua_q_tareas_R'])
                                        ->setCellValue('H'.$i,$usuario['ua_q_tareas_V1'])
                                        ->setCellValue('I'.$i,$usuario['ua_q_tareas_V2'])
                                        ->setCellValue('J'.$i,$usuario['ua_q_tareas_Cm'])
                                        ->setCellValue('K'.$i,$usuario['ua_q_tareas_Ct'])
                                        ->setCellValue('L'.$i,$usuario['ua_q_tareas_t'])
                                        ->setCellValue('M'.$i,($usuario['ua_q_tareas_t']/$totaltareasua))
                                        ->setCellValue('P'.$i,$usuario['ur_q_tareas_A'])
                                        ->setCellValue('Q'.$i,$usuario['ur_q_tareas_A2'])
                                        ->setCellValue('R'.$i,$usuario['ur_q_tareas_R'])
                                        ->setCellValue('S'.$i,$usuario['ur_q_tareas_V1'])
                                        ->setCellValue('T'.$i,$usuario['ur_q_tareas_V2'])
                                        ->setCellValue('U'.$i,$usuario['ur_q_tareas_Cm'])
                                        ->setCellValue('V'.$i,$usuario['ur_q_tareas_Ct'])
                                        ->setCellValue('W'.$i,$usuario['ur_q_tareas_t'])
                                        ->setCellValue('X'.$i,($usuario['ur_q_tareas_t']/$totaltareasur));
                                    $divisorUA = ($usuario['ua_q_tareas_R']+$usuario['ua_q_tareas_V1']+$usuario['ua_q_tareas_V2']+$usuario['ua_q_tareas_Cm']+$usuario['ua_q_tareas_Ct']);
                                    $divisorUR = ($usuario['ur_q_tareas_R']+$usuario['ur_q_tareas_V1']+$usuario['ur_q_tareas_V2']+$usuario['ur_q_tareas_Cm']+$usuario['ur_q_tareas_Ct']);

                                    if($divisorUA != 0)
                                    {
                                        $getActiveSheet
                                            ->setCellValue('N'.$i, (($usuario['ua_q_tareas_Cm']+$usuario['ua_q_tareas_Ct'])/$divisorUA));
                                    }
                                    else
                                    {
                                        $getActiveSheet->setCellValue('N'.$i, 0);
                                    }
                                    if($divisorUR != 0) 
                                    {
                                        $getActiveSheet
                                            ->setCellValue('Y'.$i, (($usuario['ur_q_tareas_Cm']+$usuario['ur_q_tareas_Ct'])/$divisorUR));
                                    }
                                    else
                                    {
                                        $getActiveSheet->setCellValue('Y'.$i, 0);
                                    }
                                    $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloUsuario);
                                    $getActiveSheet->getStyle('E'.$i)->applyFromArray($estiloDatosA);
                                    $getActiveSheet->getStyle('F'.$i)->applyFromArray($estiloDatosA2);
                                    $getActiveSheet->getStyle('G'.$i)->applyFromArray($estiloDatosR);
                                    $getActiveSheet->getStyle('H'.$i)->applyFromArray($estiloDatosV1);
                                    $getActiveSheet->getStyle('I'.$i)->applyFromArray($estiloDatosV2);
                                    $getActiveSheet->getStyle('J'.$i)->applyFromArray($estiloDatosCm);
                                    $getActiveSheet->getStyle('K'.$i)->applyFromArray($estiloDatosCt);
                                    $getActiveSheet->getStyle('L'.$i)->applyFromArray($estiloDatosTot);
                                    $getActiveSheet->getStyle('M'.$i)->applyFromArray($estiloDatospTot);
                                    $getActiveSheet->getStyle('N'.$i)->applyFromArray($estiloDatospCump);
                                    $getActiveSheet->getStyle('P'.$i)->applyFromArray($estiloDatosA);
                                    $getActiveSheet->getStyle('Q'.$i)->applyFromArray($estiloDatosA2);
                                    $getActiveSheet->getStyle('R'.$i)->applyFromArray($estiloDatosR);
                                    $getActiveSheet->getStyle('S'.$i)->applyFromArray($estiloDatosV1);
                                    $getActiveSheet->getStyle('T'.$i)->applyFromArray($estiloDatosV2);
                                    $getActiveSheet->getStyle('U'.$i)->applyFromArray($estiloDatosCm);
                                    $getActiveSheet->getStyle('V'.$i)->applyFromArray($estiloDatosCt);
                                    $getActiveSheet->getStyle('W'.$i)->applyFromArray($estiloDatosTot);
                                    $getActiveSheet->getStyle('X'.$i)->applyFromArray($estiloDatospTot);
                                    $getActiveSheet->getStyle('Y'.$i)->applyFromArray($estiloDatospCump);
                                    $i++;
                                }
                            }
                        }
                    }
                }
            }
            
            if($id_empresa == 1)
            {
                if(array_key_exists('gciaLinea', $area))
                {
                    foreach($area['gciaLinea'] as $ger)
                    {
                       $this->excel->getActiveSheet()
                            ->mergeCells('A'.$i.':D'.$i)
                            ->setCellValue('A'.$i, $ger['gerenciaLinea'])
                            ->setCellValue('E'.$i, $ger['ua_q_tareas_A'])
                            ->setCellValue('F'.$i, $ger['ua_q_tareas_A2'])
                            ->setCellValue('G'.$i, $ger['ua_q_tareas_R'])
                            ->setCellValue('H'.$i, $ger['ua_q_tareas_V1'])
                            ->setCellValue('I'.$i, $ger['ua_q_tareas_V2'])
                            ->setCellValue('J'.$i, $ger['ua_q_tareas_Cm'])
                            ->setCellValue('K'.$i, $ger['ua_q_tareas_Ct'])
                            ->setCellValue('L'.$i, $ger['ua_q_tareas_t'])
                            ->setCellValue('M'.$i, ($ger['ua_q_tareas_t']/$totaltareasua))
                            ->setCellValue('P'.$i, $ger['ur_q_tareas_A'])
                            ->setCellValue('Q'.$i, $ger['ur_q_tareas_A2'])
                            ->setCellValue('R'.$i, $ger['ur_q_tareas_V1'])
                            ->setCellValue('S'.$i, $ger['ur_q_tareas_V2'])
                            ->setCellValue('T'.$i, $ger['ur_q_tareas_R'])
                            ->setCellValue('U'.$i, $ger['ur_q_tareas_Cm'])
                            ->setCellValue('V'.$i, $ger['ur_q_tareas_Ct'])
                            ->setCellValue('W'.$i, $ger['ur_q_tareas_t'])
                            ->setCellValue('X'.$i, ($ger['ur_q_tareas_t']/$totaltareasua));
                        $divisorUA = ($ger['ua_q_tareas_R']+$ger['ua_q_tareas_V1']+$ger['ua_q_tareas_V2']+$ger['ua_q_tareas_Cm']+$ger['ua_q_tareas_Ct']);
                        $divisorUR = ($ger['ur_q_tareas_R']+$ger['ur_q_tareas_V1']+$ger['ur_q_tareas_V2']+$ger['ur_q_tareas_Cm']+$ger['ur_q_tareas_Ct']);

                        if($divisorUA != 0)
                        {
                            $getActiveSheet
                                ->setCellValue('N'.$i, (($ger['ua_q_tareas_Cm']+$ger['ua_q_tareas_Ct'])/$divisorUA));
                        }
                        else
                        {
                            $getActiveSheet->setCellValue('N'.$i, 0);
                        }
                        if($divisorUR != 0) 
                        {
                            $getActiveSheet
                                ->setCellValue('Y'.$i, (($ger['ur_q_tareas_Cm']+$ger['ur_q_tareas_Ct'])/$divisorUR));
                        }
                        else
                        {
                            $getActiveSheet->setCellValue('Y'.$i, 0);
                        }
                        $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloGerencias);
                        $getActiveSheet->getStyle('E'.$i.':M'.$i)->applyFromArray($estiloDatosGciaGral);
                        $getActiveSheet->getStyle('P'.$i.':X'.$i)->applyFromArray($estiloDatosGciaGral);
                        $getActiveSheet->getStyle('N'.$i)->applyFromArray($estiloDatospCump);
                        $getActiveSheet->getStyle('Y'.$i)->applyFromArray($estiloDatospCump);
                        $i++;
                        foreach($usuarios as $usuario)
                        {
                            if($ger['id'] == $usuario['id_gcia'])
                            {
                                $this->excel->getActiveSheet()
                                    ->mergeCells('A'.$i.':D'.$i)
                                    ->setCellValue('A'.$i, $usuario['usuario'])
                                    ->setCellValue('E'.$i,$usuario['ua_q_tareas_A'])
                                    ->setCellValue('F'.$i,$usuario['ua_q_tareas_A2'])
                                    ->setCellValue('G'.$i,$usuario['ua_q_tareas_R'])
                                    ->setCellValue('H'.$i,$usuario['ua_q_tareas_V1'])
                                    ->setCellValue('I'.$i,$usuario['ua_q_tareas_V2'])
                                    ->setCellValue('J'.$i,$usuario['ua_q_tareas_Cm'])
                                    ->setCellValue('K'.$i,$usuario['ua_q_tareas_Ct'])
                                    ->setCellValue('L'.$i,$usuario['ua_q_tareas_t'])
                                    ->setCellValue('M'.$i,($usuario['ua_q_tareas_t']/$totaltareasua))
                                    ->setCellValue('P'.$i,$usuario['ur_q_tareas_A'])
                                    ->setCellValue('Q'.$i,$usuario['ur_q_tareas_A2'])
                                    ->setCellValue('R'.$i,$usuario['ur_q_tareas_R'])
                                    ->setCellValue('S'.$i,$usuario['ur_q_tareas_V1'])
                                    ->setCellValue('T'.$i,$usuario['ur_q_tareas_V2'])
                                    ->setCellValue('U'.$i,$usuario['ur_q_tareas_Cm'])
                                    ->setCellValue('V'.$i,$usuario['ur_q_tareas_Ct'])
                                    ->setCellValue('W'.$i,$usuario['ur_q_tareas_t'])
                                    ->setCellValue('X'.$i,($usuario['ur_q_tareas_t']/$totaltareasur));
                                $divisorUA = ($usuario['ua_q_tareas_R']+$usuario['ua_q_tareas_V1']+$usuario['ua_q_tareas_V2']+$usuario['ua_q_tareas_Cm']+$usuario['ua_q_tareas_Ct']);
                                $divisorUR = ($usuario['ur_q_tareas_R']+$usuario['ur_q_tareas_V1']+$usuario['ur_q_tareas_V2']+$usuario['ur_q_tareas_Cm']+$usuario['ur_q_tareas_Ct']);

                                if($divisorUA != 0)
                                {
                                    $getActiveSheet
                                        ->setCellValue('N'.$i, (($usuario['ua_q_tareas_Cm']+$usuario['ua_q_tareas_Ct'])/$divisorUA));
                                }
                                else
                                {
                                    $getActiveSheet->setCellValue('N'.$i, 0);
                                }
                                if($divisorUR != 0) 
                                {
                                    $getActiveSheet
                                        ->setCellValue('Y'.$i, (($usuario['ur_q_tareas_Cm']+$usuario['ur_q_tareas_Ct'])/$divisorUR));
                                }
                                else
                                {
                                    $getActiveSheet->setCellValue('Y'.$i, 0);
                                }
                                $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloUsuario);
                                $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloUsuario);
                                $getActiveSheet->getStyle('E'.$i)->applyFromArray($estiloDatosA);
                                $getActiveSheet->getStyle('F'.$i)->applyFromArray($estiloDatosA2);
                                $getActiveSheet->getStyle('G'.$i)->applyFromArray($estiloDatosR);
                                $getActiveSheet->getStyle('H'.$i)->applyFromArray($estiloDatosV1);
                                $getActiveSheet->getStyle('I'.$i)->applyFromArray($estiloDatosV2);
                                $getActiveSheet->getStyle('J'.$i)->applyFromArray($estiloDatosCm);
                                $getActiveSheet->getStyle('K'.$i)->applyFromArray($estiloDatosCt);
                                $getActiveSheet->getStyle('L'.$i)->applyFromArray($estiloDatosTot);
                                $getActiveSheet->getStyle('M'.$i)->applyFromArray($estiloDatospTot);
                                $getActiveSheet->getStyle('N'.$i)->applyFromArray($estiloDatospCump);
                                $getActiveSheet->getStyle('P'.$i)->applyFromArray($estiloDatosA);
                                $getActiveSheet->getStyle('Q'.$i)->applyFromArray($estiloDatosA2);
                                $getActiveSheet->getStyle('R'.$i)->applyFromArray($estiloDatosR);
                                $getActiveSheet->getStyle('S'.$i)->applyFromArray($estiloDatosV1);
                                $getActiveSheet->getStyle('T'.$i)->applyFromArray($estiloDatosV2);
                                $getActiveSheet->getStyle('U'.$i)->applyFromArray($estiloDatosCm);
                                $getActiveSheet->getStyle('V'.$i)->applyFromArray($estiloDatosCt);
                                $getActiveSheet->getStyle('W'.$i)->applyFromArray($estiloDatosTot);
                                $getActiveSheet->getStyle('X'.$i)->applyFromArray($estiloDatospTot);
                                $getActiveSheet->getStyle('Y'.$i)->applyFromArray($estiloDatospCump);
                                $i++;
                            }
                        }
                    }
                }
            }
            else
            {
                if($id_empresa == 1 || ($id_empresa == $area['id_empresa']&&$id_empresa==2))
                {
                    if(array_key_exists('gciaLinea', $area))
                    {
                        foreach($area['gciaLinea'] as $ger)
                        {
                           $this->excel->getActiveSheet()
                                ->mergeCells('A'.$i.':D'.$i)
                                ->setCellValue('A'.$i, $ger['gerenciaLinea'])
                                ->setCellValue('E'.$i, $ger['ua_q_tareas_A'])
                                ->setCellValue('F'.$i, $ger['ua_q_tareas_A2'])
                                ->setCellValue('G'.$i, $ger['ua_q_tareas_R'])
                                ->setCellValue('H'.$i, $ger['ua_q_tareas_V1'])
                                ->setCellValue('I'.$i, $ger['ua_q_tareas_V2'])
                                ->setCellValue('J'.$i, $ger['ua_q_tareas_Cm'])
                                ->setCellValue('K'.$i, $ger['ua_q_tareas_Ct'])
                                ->setCellValue('L'.$i, $ger['ua_q_tareas_t'])
                                ->setCellValue('M'.$i, ($ger['ua_q_tareas_t']/$totaltareasua))
                                ->setCellValue('P'.$i, $ger['ur_q_tareas_A'])
                                ->setCellValue('Q'.$i, $ger['ur_q_tareas_A2'])
                                ->setCellValue('R'.$i, $ger['ur_q_tareas_V1'])
                                ->setCellValue('S'.$i, $ger['ur_q_tareas_V2'])
                                ->setCellValue('T'.$i, $ger['ur_q_tareas_R'])
                                ->setCellValue('U'.$i, $ger['ur_q_tareas_Cm'])
                                ->setCellValue('V'.$i, $ger['ur_q_tareas_Ct'])
                                ->setCellValue('W'.$i, $ger['ur_q_tareas_t'])
                                ->setCellValue('X'.$i, ($ger['ur_q_tareas_t']/$totaltareasua));
                            $divisorUA = ($ger['ua_q_tareas_R']+$ger['ua_q_tareas_V1']+$ger['ua_q_tareas_V2']+$ger['ua_q_tareas_Cm']+$ger['ua_q_tareas_Ct']);
                            $divisorUR = ($ger['ur_q_tareas_R']+$ger['ur_q_tareas_V1']+$ger['ur_q_tareas_V2']+$ger['ur_q_tareas_Cm']+$ger['ur_q_tareas_Ct']);

                            if($divisorUA != 0)
                            {
                                $getActiveSheet
                                    ->setCellValue('N'.$i, (($ger['ua_q_tareas_Cm']+$ger['ua_q_tareas_Ct'])/$divisorUA));
                            }
                            else
                            {
                                $getActiveSheet->setCellValue('N'.$i, 0);
                            }
                            if($divisorUR != 0) 
                            {
                                $getActiveSheet
                                    ->setCellValue('Y'.$i, (($ger['ur_q_tareas_Cm']+$ger['ur_q_tareas_Ct'])/$divisorUR));
                            }
                            else
                            {
                                $getActiveSheet->setCellValue('Y'.$i, 0);
                            }
                            $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloGerencias);
                            $getActiveSheet->getStyle('E'.$i.':M'.$i)->applyFromArray($estiloDatosGciaGral);
                            $getActiveSheet->getStyle('P'.$i.':X'.$i)->applyFromArray($estiloDatosGciaGral);
                            $getActiveSheet->getStyle('N'.$i)->applyFromArray($estiloDatospCump);
                            $getActiveSheet->getStyle('Y'.$i)->applyFromArray($estiloDatospCump);
                            $i++;
                            foreach($usuarios as $usuario)
                            {
                                if($ger['id'] == $usuario['id_gcia'])
                                {
                                    $this->excel->getActiveSheet()
                                        ->mergeCells('A'.$i.':D'.$i)
                                        ->setCellValue('A'.$i, $usuario['usuario'])
                                        ->setCellValue('E'.$i,$usuario['ua_q_tareas_A'])
                                        ->setCellValue('F'.$i,$usuario['ua_q_tareas_A2'])
                                        ->setCellValue('G'.$i,$usuario['ua_q_tareas_R'])
                                        ->setCellValue('H'.$i,$usuario['ua_q_tareas_V1'])
                                        ->setCellValue('I'.$i,$usuario['ua_q_tareas_V2'])
                                        ->setCellValue('J'.$i,$usuario['ua_q_tareas_Cm'])
                                        ->setCellValue('K'.$i,$usuario['ua_q_tareas_Ct'])
                                        ->setCellValue('L'.$i,$usuario['ua_q_tareas_t'])
                                        ->setCellValue('M'.$i,($usuario['ua_q_tareas_t']/$totaltareasua))
                                        ->setCellValue('P'.$i,$usuario['ur_q_tareas_A'])
                                        ->setCellValue('Q'.$i,$usuario['ur_q_tareas_A2'])
                                        ->setCellValue('R'.$i,$usuario['ur_q_tareas_R'])
                                        ->setCellValue('S'.$i,$usuario['ur_q_tareas_V1'])
                                        ->setCellValue('T'.$i,$usuario['ur_q_tareas_V2'])
                                        ->setCellValue('U'.$i,$usuario['ur_q_tareas_Cm'])
                                        ->setCellValue('V'.$i,$usuario['ur_q_tareas_Ct'])
                                        ->setCellValue('W'.$i,$usuario['ur_q_tareas_t'])
                                        ->setCellValue('X'.$i,($usuario['ur_q_tareas_t']/$totaltareasur));
                                    $divisorUA = ($usuario['ua_q_tareas_R']+$usuario['ua_q_tareas_V1']+$usuario['ua_q_tareas_V2']+$usuario['ua_q_tareas_Cm']+$usuario['ua_q_tareas_Ct']);
                                    $divisorUR = ($usuario['ur_q_tareas_R']+$usuario['ur_q_tareas_V1']+$usuario['ur_q_tareas_V2']+$usuario['ur_q_tareas_Cm']+$usuario['ur_q_tareas_Ct']);

                                    if($divisorUA != 0)
                                    {
                                        $getActiveSheet
                                            ->setCellValue('N'.$i, (($usuario['ua_q_tareas_Cm']+$usuario['ua_q_tareas_Ct'])/$divisorUA));
                                    }
                                    else
                                    {
                                        $getActiveSheet->setCellValue('N'.$i, 0);
                                    }
                                    if($divisorUR != 0) 
                                    {
                                        $getActiveSheet
                                            ->setCellValue('Y'.$i, (($usuario['ur_q_tareas_Cm']+$usuario['ur_q_tareas_Ct'])/$divisorUR));
                                    }
                                    else
                                    {
                                        $getActiveSheet->setCellValue('Y'.$i, 0);
                                    }
                                    $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloUsuario);
                                    $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloUsuario);
                                    $getActiveSheet->getStyle('E'.$i)->applyFromArray($estiloDatosA);
                                    $getActiveSheet->getStyle('F'.$i)->applyFromArray($estiloDatosA2);
                                    $getActiveSheet->getStyle('G'.$i)->applyFromArray($estiloDatosR);
                                    $getActiveSheet->getStyle('H'.$i)->applyFromArray($estiloDatosV1);
                                    $getActiveSheet->getStyle('I'.$i)->applyFromArray($estiloDatosV2);
                                    $getActiveSheet->getStyle('J'.$i)->applyFromArray($estiloDatosCm);
                                    $getActiveSheet->getStyle('K'.$i)->applyFromArray($estiloDatosCt);
                                    $getActiveSheet->getStyle('L'.$i)->applyFromArray($estiloDatosTot);
                                    $getActiveSheet->getStyle('M'.$i)->applyFromArray($estiloDatospTot);
                                    $getActiveSheet->getStyle('N'.$i)->applyFromArray($estiloDatospCump);
                                    $getActiveSheet->getStyle('P'.$i)->applyFromArray($estiloDatosA);
                                    $getActiveSheet->getStyle('Q'.$i)->applyFromArray($estiloDatosA2);
                                    $getActiveSheet->getStyle('R'.$i)->applyFromArray($estiloDatosR);
                                    $getActiveSheet->getStyle('S'.$i)->applyFromArray($estiloDatosV1);
                                    $getActiveSheet->getStyle('T'.$i)->applyFromArray($estiloDatosV2);
                                    $getActiveSheet->getStyle('U'.$i)->applyFromArray($estiloDatosCm);
                                    $getActiveSheet->getStyle('V'.$i)->applyFromArray($estiloDatosCt);
                                    $getActiveSheet->getStyle('W'.$i)->applyFromArray($estiloDatosTot);
                                    $getActiveSheet->getStyle('X'.$i)->applyFromArray($estiloDatospTot);
                                    $getActiveSheet->getStyle('Y'.$i)->applyFromArray($estiloDatospCump);
                                    $i++;
                                }
                            }
                        }
                    }
                }
                if($id_empresa == 1 || ($id_empresa == $area['id_empresa']&&$id_empresa==3))
                {
                    if(array_key_exists('gciaLinea', $area))
                    {
                        foreach($area['gciaLinea'] as $ger)
                        {
                           $this->excel->getActiveSheet()
                                ->mergeCells('A'.$i.':D'.$i)
                                ->setCellValue('A'.$i, $ger['gerenciaLinea'])
                                ->setCellValue('E'.$i, $ger['ua_q_tareas_A'])
                                ->setCellValue('F'.$i, $ger['ua_q_tareas_A2'])
                                ->setCellValue('G'.$i, $ger['ua_q_tareas_R'])
                                ->setCellValue('H'.$i, $ger['ua_q_tareas_V1'])
                                ->setCellValue('I'.$i, $ger['ua_q_tareas_V2'])
                                ->setCellValue('J'.$i, $ger['ua_q_tareas_Cm'])
                                ->setCellValue('K'.$i, $ger['ua_q_tareas_Ct'])
                                ->setCellValue('L'.$i, $ger['ua_q_tareas_t'])
                                ->setCellValue('M'.$i, ($ger['ua_q_tareas_t']/$totaltareasua))
                                ->setCellValue('P'.$i, $ger['ur_q_tareas_A'])
                                ->setCellValue('Q'.$i, $ger['ur_q_tareas_A2'])
                                ->setCellValue('R'.$i, $ger['ur_q_tareas_V1'])
                                ->setCellValue('S'.$i, $ger['ur_q_tareas_V2'])
                                ->setCellValue('T'.$i, $ger['ur_q_tareas_R'])
                                ->setCellValue('U'.$i, $ger['ur_q_tareas_Cm'])
                                ->setCellValue('V'.$i, $ger['ur_q_tareas_Ct'])
                                ->setCellValue('W'.$i, $ger['ur_q_tareas_t'])
                                ->setCellValue('X'.$i, ($ger['ur_q_tareas_t']/$totaltareasua));
                            $divisorUA = ($ger['ua_q_tareas_R']+$ger['ua_q_tareas_V1']+$ger['ua_q_tareas_V2']+$ger['ua_q_tareas_Cm']+$ger['ua_q_tareas_Ct']);
                            $divisorUR = ($ger['ur_q_tareas_R']+$ger['ur_q_tareas_V1']+$ger['ur_q_tareas_V2']+$ger['ur_q_tareas_Cm']+$ger['ur_q_tareas_Ct']);

                            if($divisorUA != 0)
                            {
                                $getActiveSheet
                                    ->setCellValue('N'.$i, (($ger['ua_q_tareas_Cm']+$ger['ua_q_tareas_Ct'])/$divisorUA));
                            }
                            else
                            {
                                $getActiveSheet->setCellValue('N'.$i, 0);
                            }
                            if($divisorUR != 0) 
                            {
                                $getActiveSheet
                                    ->setCellValue('Y'.$i, (($ger['ur_q_tareas_Cm']+$ger['ur_q_tareas_Ct'])/$divisorUR));
                            }
                            else
                            {
                                $getActiveSheet->setCellValue('Y'.$i, 0);
                            }
                            $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloGerencias);
                            $getActiveSheet->getStyle('E'.$i.':M'.$i)->applyFromArray($estiloDatosGciaGral);
                            $getActiveSheet->getStyle('P'.$i.':X'.$i)->applyFromArray($estiloDatosGciaGral);
                            $getActiveSheet->getStyle('N'.$i)->applyFromArray($estiloDatospCump);
                            $getActiveSheet->getStyle('Y'.$i)->applyFromArray($estiloDatospCump);
                            $i++;
                            foreach($usuarios as $usuario)
                            {
                                if($ger['id'] == $usuario['id_gcia'])
                                {
                                    $this->excel->getActiveSheet()
                                        ->mergeCells('A'.$i.':D'.$i)
                                        ->setCellValue('A'.$i, $usuario['usuario'])
                                        ->setCellValue('E'.$i,$usuario['ua_q_tareas_A'])
                                        ->setCellValue('F'.$i,$usuario['ua_q_tareas_A2'])
                                        ->setCellValue('G'.$i,$usuario['ua_q_tareas_R'])
                                        ->setCellValue('H'.$i,$usuario['ua_q_tareas_V1'])
                                        ->setCellValue('I'.$i,$usuario['ua_q_tareas_V2'])
                                        ->setCellValue('J'.$i,$usuario['ua_q_tareas_Cm'])
                                        ->setCellValue('K'.$i,$usuario['ua_q_tareas_Ct'])
                                        ->setCellValue('L'.$i,$usuario['ua_q_tareas_t'])
                                        ->setCellValue('M'.$i,($usuario['ua_q_tareas_t']/$totaltareasua))
                                        ->setCellValue('P'.$i,$usuario['ur_q_tareas_A'])
                                        ->setCellValue('Q'.$i,$usuario['ur_q_tareas_A2'])
                                        ->setCellValue('R'.$i,$usuario['ur_q_tareas_R'])
                                        ->setCellValue('S'.$i,$usuario['ur_q_tareas_V1'])
                                        ->setCellValue('T'.$i,$usuario['ur_q_tareas_V2'])
                                        ->setCellValue('U'.$i,$usuario['ur_q_tareas_Cm'])
                                        ->setCellValue('V'.$i,$usuario['ur_q_tareas_Ct'])
                                        ->setCellValue('W'.$i,$usuario['ur_q_tareas_t'])
                                        ->setCellValue('X'.$i,($usuario['ur_q_tareas_t']/$totaltareasur));
                                    $divisorUA = ($usuario['ua_q_tareas_R']+$usuario['ua_q_tareas_V1']+$usuario['ua_q_tareas_V2']+$usuario['ua_q_tareas_Cm']+$usuario['ua_q_tareas_Ct']);
                                    $divisorUR = ($usuario['ur_q_tareas_R']+$usuario['ur_q_tareas_V1']+$usuario['ur_q_tareas_V2']+$usuario['ur_q_tareas_Cm']+$usuario['ur_q_tareas_Ct']);

                                    if($divisorUA != 0)
                                    {
                                        $getActiveSheet
                                            ->setCellValue('N'.$i, (($usuario['ua_q_tareas_Cm']+$usuario['ua_q_tareas_Ct'])/$divisorUA));
                                    }
                                    else
                                    {
                                        $getActiveSheet->setCellValue('N'.$i, 0);
                                    }
                                    if($divisorUR != 0) 
                                    {
                                        $getActiveSheet
                                            ->setCellValue('Y'.$i, (($usuario['ur_q_tareas_Cm']+$usuario['ur_q_tareas_Ct'])/$divisorUR));
                                    }
                                    else
                                    {
                                        $getActiveSheet->setCellValue('Y'.$i, 0);
                                    }
                                    $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloUsuario);
                                    $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloUsuario);
                                    $getActiveSheet->getStyle('E'.$i)->applyFromArray($estiloDatosA);
                                    $getActiveSheet->getStyle('F'.$i)->applyFromArray($estiloDatosA2);
                                    $getActiveSheet->getStyle('G'.$i)->applyFromArray($estiloDatosR);
                                    $getActiveSheet->getStyle('H'.$i)->applyFromArray($estiloDatosV1);
                                    $getActiveSheet->getStyle('I'.$i)->applyFromArray($estiloDatosV2);
                                    $getActiveSheet->getStyle('J'.$i)->applyFromArray($estiloDatosCm);
                                    $getActiveSheet->getStyle('K'.$i)->applyFromArray($estiloDatosCt);
                                    $getActiveSheet->getStyle('L'.$i)->applyFromArray($estiloDatosTot);
                                    $getActiveSheet->getStyle('M'.$i)->applyFromArray($estiloDatospTot);
                                    $getActiveSheet->getStyle('N'.$i)->applyFromArray($estiloDatospCump);
                                    $getActiveSheet->getStyle('P'.$i)->applyFromArray($estiloDatosA);
                                    $getActiveSheet->getStyle('Q'.$i)->applyFromArray($estiloDatosA2);
                                    $getActiveSheet->getStyle('R'.$i)->applyFromArray($estiloDatosR);
                                    $getActiveSheet->getStyle('S'.$i)->applyFromArray($estiloDatosV1);
                                    $getActiveSheet->getStyle('T'.$i)->applyFromArray($estiloDatosV2);
                                    $getActiveSheet->getStyle('U'.$i)->applyFromArray($estiloDatosCm);
                                    $getActiveSheet->getStyle('V'.$i)->applyFromArray($estiloDatosCt);
                                    $getActiveSheet->getStyle('W'.$i)->applyFromArray($estiloDatosTot);
                                    $getActiveSheet->getStyle('X'.$i)->applyFromArray($estiloDatospTot);
                                    $getActiveSheet->getStyle('Y'.$i)->applyFromArray($estiloDatospCump);
                                    $i++;
                                }
                            }
                        }
                    }

                }
            }
        }
        
        $this->excel->setActiveSheetIndex()->getRowDimension(1)->setRowHeight(35);
        
        $getActiveSheet->getStyle('A1')->applyFromArray($estiloTituloReporte);
        $getActiveSheet->getStyle('A3')->applyFromArray($estiloTitulosCabecera);
        $getActiveSheet->getStyle('A4')->applyFromArray($estiloTitulosCabecera);
        $getActiveSheet->getStyle('A5')->applyFromArray($estiloTitulosCabecera);
        $getActiveSheet->getStyle('C3')->applyFromArray($estiloDatosCabecera);
        $getActiveSheet->getStyle('C4')->applyFromArray($estiloDatosCabecera);
        $getActiveSheet->getStyle('C5')->applyFromArray($estiloDatosCabecera);
        $getActiveSheet->getStyle('E3')->applyFromArray($estiloTitulosGrilla);
        $getActiveSheet->getStyle('E4')->applyFromArray($estiloSubTitulosGrillaC);
        $getActiveSheet->getStyle('E5')->applyFromArray($estiloTitulosGrillaPorcentaje);
        $getActiveSheet->getStyle('E6:E7')->applyFromArray($estiloTituloA);
        $getActiveSheet->getStyle('F6:F7')->applyFromArray($estiloTituloA2);
        $getActiveSheet->getStyle('G6:G7')->applyFromArray($estiloTituloR);
        $getActiveSheet->getStyle('H6:H7')->applyFromArray($estiloTituloV1);
        $getActiveSheet->getStyle('I6:I7')->applyFromArray($estiloTituloV2);
        $getActiveSheet->getStyle('J6:J7')->applyFromArray($estiloTituloCt);
        $getActiveSheet->getStyle('K6:K7')->applyFromArray($estiloTituloCm);
        $getActiveSheet->getStyle('L6:L7')->applyFromArray($estiloTitulosTotTareas);
        $getActiveSheet->getStyle('M6:M7')->applyFromArray($estiloTitulosTotTareas);
        $getActiveSheet->getStyle('M5')->applyFromArray($estiloPorcentajeTotal);
        $getActiveSheet->getStyle('N6:N7')->applyFromArray($estiloTitulosTotTareas);
        $getActiveSheet->getStyle('P3')->applyFromArray($estiloTitulosGrilla);
        $getActiveSheet->getStyle('P4')->applyFromArray($estiloSubTitulosGrillaR);
        $getActiveSheet->getStyle('P5')->applyFromArray($estiloTitulosGrillaPorcentaje);
        $getActiveSheet->getStyle('X5')->applyFromArray($estiloPorcentajeTotal);
        $getActiveSheet->getStyle('P6:P7')->applyFromArray($estiloTituloA);
        $getActiveSheet->getStyle('Q6:Q7')->applyFromArray($estiloTituloA2);
        $getActiveSheet->getStyle('R6:R7')->applyFromArray($estiloTituloR);
        $getActiveSheet->getStyle('S6:S7')->applyFromArray($estiloTituloV1);
        $getActiveSheet->getStyle('T6:T7')->applyFromArray($estiloTituloV2);
        $getActiveSheet->getStyle('U6:U7')->applyFromArray($estiloTituloCt);
        $getActiveSheet->getStyle('V6:V7')->applyFromArray($estiloTituloCm);
        $getActiveSheet->getStyle('W6:W7')->applyFromArray($estiloTitulosTotTareas);
        $getActiveSheet->getStyle('X6:X7')->applyFromArray($estiloTitulosTotTareas);
        $getActiveSheet->getStyle('Y6:Y7')->applyFromArray($estiloTitulosTotTareas);
        
        $getActiveSheet->getStyle('M'.$j.':M'.$i)->getNumberFormat()->applyFromArray( 
            array( 
                'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00
            )
        );
        $getActiveSheet->getStyle('X'.$j.':X'.$i)->getNumberFormat()->applyFromArray( 
            array( 
                'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00
            )
        );
        $getActiveSheet->getStyle('N'.$j.':N'.$i)->getNumberFormat()->applyFromArray( 
            array( 
                'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00
            )
        );
        $getActiveSheet->getStyle('Y'.$j.':Y'.$i)->getNumberFormat()->applyFromArray( 
            array( 
                'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00
            )
        );
        $getActiveSheet->getStyle('M5')->getNumberFormat()->applyFromArray( 
            array( 
                'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00
            )
        );
        $getActiveSheet->getStyle('X5')->getNumberFormat()->applyFromArray( 
            array( 
                'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00
            )
        );
        
        $this->excel->setActiveSheetIndex()->getRowDimension(3)->setRowHeight(26.25);
        $this->excel->setActiveSheetIndex()->getRowDimension(4)->setRowHeight(23.25);
        $this->excel->setActiveSheetIndex()->getRowDimension(5)->setRowHeight(21.75);
        
        $this->excel->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth('12');
        $this->excel->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth('12');
        $this->excel->setActiveSheetIndex(0)->getColumnDimension('C')->setWidth('12');
        $this->excel->setActiveSheetIndex(0)->getColumnDimension('D')->setWidth('12');
        $this->excel->setActiveSheetIndex(0)->getColumnDimension('E')->setWidth('6');
        $this->excel->setActiveSheetIndex(0)->getColumnDimension('F')->setWidth('6');
        $this->excel->setActiveSheetIndex(0)->getColumnDimension('G')->setWidth('6');
        $this->excel->setActiveSheetIndex(0)->getColumnDimension('H')->setWidth('6');
        $this->excel->setActiveSheetIndex(0)->getColumnDimension('I')->setWidth('6');
        $this->excel->setActiveSheetIndex(0)->getColumnDimension('J')->setWidth('6');
        $this->excel->setActiveSheetIndex(0)->getColumnDimension('K')->setWidth('6');
        $this->excel->setActiveSheetIndex(0)->getColumnDimension('L')->setWidth('8');
        $this->excel->setActiveSheetIndex(0)->getColumnDimension('M')->setWidth('8');
        $this->excel->setActiveSheetIndex(0)->getColumnDimension('N')->setWidth('8');
        $this->excel->setActiveSheetIndex(0)->getColumnDimension('O')->setWidth('4');
        $this->excel->setActiveSheetIndex(0)->getColumnDimension('P')->setWidth('6');
        $this->excel->setActiveSheetIndex(0)->getColumnDimension('Q')->setWidth('6');
        $this->excel->setActiveSheetIndex(0)->getColumnDimension('R')->setWidth('6');
        $this->excel->setActiveSheetIndex(0)->getColumnDimension('S')->setWidth('6');
        $this->excel->setActiveSheetIndex(0)->getColumnDimension('T')->setWidth('6');
        $this->excel->setActiveSheetIndex(0)->getColumnDimension('U')->setWidth('6');
        $this->excel->setActiveSheetIndex(0)->getColumnDimension('V')->setWidth('6');
        $this->excel->setActiveSheetIndex(0)->getColumnDimension('W')->setWidth('8');
        $this->excel->setActiveSheetIndex(0)->getColumnDimension('X')->setWidth('8');
        $this->excel->setActiveSheetIndex(0)->getColumnDimension('Y')->setWidth('8');
        
        $this->excel->getActiveSheet()->freezePane('E7');
        
        switch($id_empresa)
        {
            case 1: 
                $filename='ORO_Reporte_Tareas';
                break;
            case 2: 
                $filename='SDJ_Reporte_Tareas';
                break;
            case 3: 
                $filename='BRX_Reporte_Tareas';
                break;
        }
        
        
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache

        //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
        //if you want to save it as .XLSX Excel 2007 format
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
        ob_end_clean();
        //force user to download the Excel file without writing it to server's HD
        $objWriter->save('php://output');
    }
    
    public function armarGerenciasCorp($arreglo,$numeros)
    {
        $gciacorp = array();
        foreach($arreglo as $gerencias)
        {
            foreach($gerencias['gciaCorp'] as $clave=>$valor)
            {
                if(!array_key_exists($valor['id'],$gciacorp))
                {
                    $gciacorp[$valor['id']]['gerencia']=$valor['gerencia'];
                    $gciacorp[$valor['id']]['id']=$valor['id'];
                    $gciacorp[$valor['id']]['id_empresa']=$valor['id_empresa'];
                    foreach($numeros as $numero=>$atributo)
                    {
                        $gciacorp[$valor['id']][$atributo]=$valor[$atributo];
                    }
                }
                else
                {
                     foreach($numeros as $numero=>$atributo)
                    {
                        $gciacorp[$valor['id']][$atributo]+=$valor[$atributo];
                    }
                }

            }
        }
        return $gciacorp;
    }
    
    public function armarGerencias($arreglo,$numeros)
    {
        $gcia = array();
        foreach($arreglo as $gerencias)
        {
            foreach($gerencias['gciaCorp'] as $clave=>$valor)
            {
                if(array_key_exists('gciaLinea',$valor))
                {
                    foreach($valor['gciaLinea'] as $value)
                    {
                        if(!array_key_exists($value['id'],$gcia))
                        {
                            $gcia[$value['id']]['gerencia']=$value['gerenciaLinea'];
                            $gcia[$value['id']]['empresa']=$value['id_empresa'];
                            foreach($numeros as $numero=>$atributo)
                            {
                                $gcia[$value['id']][$atributo]=$value[$atributo];
                            }
                        }
                        else
                        {
                             foreach($numeros as $atributo)
                            {
                                $gcia[$value['id']][$atributo]+=$value[$atributo];
                            }
                        }
                    }
                }


            }
        }
        return $gcia;
    }
    
    function arrayCargaUsuarios ($array,$numeros,$filas)
    {        
        $resultado=array();
        foreach ($array as $key=>$value)
        {
            if (!array_key_exists($value[$filas[0]],$resultado))
            {
                $resultado[$value[$filas[0]]]['gerencia']=$value['gerencia_corp'];
                $resultado[$value[$filas[0]]]['id']=$value[$filas[0]];
                $resultado[$value[$filas[0]]]['id_empresa']=$value['id_empresa'];
                if ( ($value[$filas[3]]!=1) && ($value[$filas[3]]!=2) && ($value[$filas[3]]!=4) )
                {
                    $resultado[$value[$filas[0]]]['gciaLinea'][$value[$filas[1]]]['gerenciaLinea']=$value['gerencia'];
                    $resultado[$value[$filas[0]]]['gciaLinea'][$value[$filas[1]]]['id']=$value['id_gerencia'];
                    $resultado[$value[$filas[0]]]['gciaLinea'][$value[$filas[1]]]['id_empresa']=$value['id_empresa'];
                }
                foreach ($numeros as $numero=>$valorNumero)
                {
                    $resultado[$value[$filas[0]]][$valorNumero]=$value[$valorNumero];
                    if ( ($value[$filas[3]]!=1) && ($value[$filas[3]]!=2) && ($value[$filas[3]]!=4) )
                    {
                        $resultado[$value[$filas[0]]]['gciaLinea'][$value[$filas[1]]][$valorNumero]=$value[$valorNumero];
                    }
                }
            }
            else
            {
                foreach ($numeros as $numero=>$valorNumero)
                {
                    $resultado[$value[$filas[0]]][$valorNumero]+=$value[$valorNumero];
                }
                if ( ($value[$filas[3]]!=1) && ($value[$filas[3]]!=2) && ($value[$filas[3]]!=4) )
                {
                    if (!array_key_exists($value[$filas[1]],$resultado[$value[$filas[0]]]['gciaLinea']))
                    {
                        $resultado[$value[$filas[0]]]['gciaLinea'][$value[$filas[1]]]['gerenciaLinea']=$value['gerencia'];
                        $resultado[$value[$filas[0]]]['gciaLinea'][$value[$filas[1]]]['id']=$value['id_gerencia'];
                        $resultado[$value[$filas[0]]]['gciaLinea'][$value[$filas[1]]]['id_empresa']=$value['id_empresa'];
                        foreach ($numeros as $numero=>$valorNumero)
                        {
                            $resultado[$value[$filas[0]]]['gciaLinea'][$value[$filas[1]]][$valorNumero]=$value[$valorNumero];
                        }
                    }
                    else
                    {
                        foreach ($numeros as $numero=>$valorNumero)
                        {
                            $resultado[$value[$filas[0]]]['gciaLinea'][$value[$filas[1]]][$valorNumero]+=$value[$valorNumero];
                        }
                    }
                }
            } 
        }
        return $resultado;
    }
    
    public function armarGciasPorEmpresa($arreglo,$id_empresa,$numeros,$filas)
    {
        $resultado=array();
        
        foreach($arreglo as $gerencias)
        {
            foreach($gerencias['gciaCorp'] as $clave=>$valor)
            {
                if($valor['id']==1 || $valor['id_empresa']==$id_empresa)
                {
                    $resultado[$valor['id']]['gerencia']=$valor['gerencia'];
                    $resultado[$valor['id']]['id']=$valor['id'];
                    $resultado[$valor['id']]['id_empresa']=$valor['id_empresa'];
                    foreach($numeros as $numero=>$atributo)
                    {
                        $resultado[$valor['id']][$atributo]=$valor[$atributo];
                    }
                }

            }
        }
        return $resultado;
    }
    public function totalizaPorGerenciaCorp($arreglo,$id_empresa,$numeros,$id_ger)
    {
        $resultado=array();
        
        switch($id_empresa)
        {
            case 2:
                $organigramas = array(1,2,3);
                break;
            case 3:
                $organigramas = array(1,4,5);
                break;
            default:
                break;
        }
        
        foreach($arreglo as $usuario)
        {
            if($usuario['id_org']==$organigramas[0] || $usuario['id_org']==$organigramas[1] || $usuario['id_org']==$organigramas[2])
            {
                if($usuario['id_corp']==$id_ger)
                {
                    if (!array_key_exists($id_ger,$resultado))
                    {
                        foreach($numeros as $numero=>$atributo)
                        {
                            $resultado[$id_ger][$atributo]=$usuario[$atributo];
                        }
                    }
                    else
                    {
                        foreach($numeros as $numero=>$atributo)
                        {
                            $resultado[$id_ger][$atributo]+=$usuario[$atributo];
                        }
                    }
                    
                }
            }
        }
        return $resultado;
    }
    
	
	
}
?>