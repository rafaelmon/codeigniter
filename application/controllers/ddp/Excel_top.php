<?php
class Excel_top extends CI_Controller
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
	}
	
        public function miTopExcel()
        {
            $id_usuario=$this->user['id'];
            $id_top = $this->uri->segment(SEGMENTOS_DOM+2);
            
            $this->load->model('ddp/Ddp_tops_model','tops',true);
            $top=$this->tops->dameTopPorId($id_top);
//            echo "<pre>".print_r($top,true)."</pre>";die();
//            if ($this->user['id'] == $datos['id_usuario'] || $this->user['id'] == $datos['id_supervisor'])
            if ($top==0)
                exit ("({success: false, error :'Top inexistente'})"); 
            else 
            {
                if ( $top['id_usuario']==$id_usuario )
                    $this->_generar($id_top);
                else
                    exit ("({success: false, error :'Su usuario no esta autorizado para descargar el reporte'})"); 
                
            }    
            
        }
        public function topExcel()
        {
            $id_solicitante=$this->user['id'];
            
            //verifico que el usuario sea el supervisor
            $id_top=$this->uri->segment(SEGMENTOS_DOM+2);
            $this->load->model('ddp/ddp_tops_model','ddp_tops',true);
            $top=$this->ddp_tops->dameTopPorId($id_top);
            
            //verifico que el período sea válido
//            $this->load->model('ddp/ddp_periodos_model','periodo',true);
//            $checkPeriodo=$this->periodo->checkPeriodo((int)$id_periodo);
//            if (!$checkPeriodo)
//                    exit ("Error, per&iacute;odo inv&aacute;lido"); 
            
            $id_usuario=$top['id_usuario'];
            //verifico que el usuario este habilitado
            
            $this->load->model('usuarios_model','usuarios',true);
            $checkUsuario=$this->usuarios->checkUsuarioHabilitado((int)$id_usuario);
            if (!$checkUsuario)
                    exit ("Error, usuario inv&aacute;lido o deshabilitado"); 
            
            //verifico que el solicitante sea el supervisor del usuario
            $checkSupervisor=$this->ddp_tops->checkSupervisor($id_solicitante,(int)$id_usuario);
            if (!$checkSupervisor)
                    exit ("Error, usuario sin permisos para descargar el reporte"); 
            
//            $this->load->model('ddp/ddp_tops_model','tops',true);
//            $top=$this->tops->dameTop($id_usuario,$id_periodo);
//            echo "<pre>".print_r($top,true)."</pre>";die();
            
            $id_top = $top['id_top'];
//            if ($this->user['id'] == $datos['id_usuario'] || $this->user['id'] == $datos['id_supervisor'])
            if ( $id_solicitante == $top['id_supervisor'] )
                $this->_generar($id_top);
            else
                exit ("Error, usuario no esta autorizado para descargar el reporte"); 
        }
        public function topExcelDDP()
        {
            //verifico que el usuario tenga permiso para el módulo de administración de TOPS
            $id_solicitante=$this->user['id'];
//             $user=$this->session->userdata(USER_DATA_SESSION);
            
            $this->load->model("permisos_model","permisos_model",true);
            $permisos= $this->permisos_model->checkIn($this->user['perfil_id'],33);
//            echo "<pre>".print_r($permisos,true)."</pre>";
            if ($permisos['Listar'])
            {
                $id_top=$this->uri->segment(SEGMENTOS_DOM+2);
                $this->load->model('ddp/ddp_tops_model','ddp_tops',true);
                $top=$this->ddp_tops->dameTopPorId($id_top);

                //verifico que el período sea válido
    //            $this->load->model('ddp/ddp_periodos_model','periodo',true);
    //            $checkPeriodo=$this->periodo->checkPeriodo((int)$id_periodo);
    //            if (!$checkPeriodo)
    //                    exit ("Error, per&iacute;odo inv&aacute;lido"); 

                $id_usuario=$top['id_usuario'];
                //verifico que el usuario este habilitado

                $this->load->model('usuarios_model','usuarios',true);
                $checkUsuario=$this->usuarios->checkUsuarioHabilitado((int)$id_usuario);
                if (!$checkUsuario)
                        exit ("Error, usuario inv&aacute;lido o deshabilitado"); 

    //            $this->load->model('ddp/ddp_tops_model','tops',true);
    //            $top=$this->tops->dameTop($id_usuario,$id_periodo);
    //            echo "<pre>".print_r($top,true)."</pre>";die();

                $id_top = $top['id_top'];
                $this->_generar($id_top);
            }
            else 
                exit ("Error, usuario sin permisos para descargar el reporte"); 
        }
        
	public function _generar($id_top)
	{
            $this->load->model('ddp/Ddp_excel_model','reporte',true);
            $top_cabecera = $this->reporte->datos_cabecera($id_top);
    //        echo "<pre>".print_r($top_cabecera,true)."</pre>";die();
            $datos['id_top']            = $top_cabecera['id_top'];
            $datos['id_usuario']        = $top_cabecera['id_usuario'];
            $datos['nombre']            = $top_cabecera['nombre'];
            $datos['nomape']            = $top_cabecera['nomape'];
            $datos['puesto']            = $top_cabecera['puesto'];
            $datos['id_supervisor']     = $top_cabecera['id_supervisor'];
            $datos['supervisor']        = $top_cabecera['supervisor'];
            $datos['periodo']           = $top_cabecera['periodo'];
            $datos['fecha_alta']        = $top_cabecera['fecha_alta'];
            $datos['puestosupervisor']  = $top_cabecera['puestosupervisor'];
            $datos['estado_top']        = $top_cabecera['estado'];
            
            $this->load->library('excel');

            // Se asignan las propiedades del libro
            $this->excel->getProperties()
                ->setCreator("Sistema de Mejora Continua (SMC)") // Nombre del autor
                ->setLastModifiedBy("Sistema de Mejora Continua (SMC)") //Ultimo usuario que lo modificó
                ->setTitle("Tarjeta de Objetivos Personales (TOP)") // Titulo
                ->setSubject("Tarjeta de Objetivos Personales (TOP)") //Asunto
                ->setDescription("Tarjeta de Objetivos Personales (TOP)") //Descripción
                ->setKeywords("Tarjeta de Objetivos Personales (TOP)") //Etiquetas
                ->setCategory("Tarjeta de Objetivos Personales (TOP)"); //Categorias

                $estiloTituloReporte = array(
                    'font' => array(
                        'name'      => 'Calibri',
                        'bold'      => true,
                        'italic'    => false,
                        'strike'    => false,
                        'size' =>16,
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
                $estiloTituloDatos = array(
                    'font' => array(
                        'name'      => 'Calibri',
                        'bold'      => true,
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
                    )
                );
                $estiloNombre = array(
                    'font' => array(
                        'name'      => 'Calibri',
                        'bold'      => false,
                        'italic'    => false,
                        'strike'    => false,
                        'size' =>14,
                        'color'     => array(
                            'rgb' => '000000'
                        )
                    ),
                    'borders' => array(
                        'bottom' => array(
                            'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                            'color' => array(
                                'rgb' => '143860'
                            )
                        )
                    ),
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                        'rotation' => 0,
                        'wrap' => TRUE
                    )
                );

                $estiloDatos = array(
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
                    'borders' => array(
                        'bottom' => array(
                            'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                            'color' => array(
                                'rgb' => '143860'
                            )
                        )
                    ),
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                        'rotation' => 0,
                        'wrap' => TRUE
                    )
                );

                $estiloTituloEvaluaciones = array(
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
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN
                        )
                    ),
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                        'rotation' => 0,
                        'wrap' => TRUE
                    )
                );

                $estiloTituloFinanzas = array(
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
                    'fill' => array(
                        'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array(
                            'argb' => 'FF00B050')
                    ),
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN
                        )
                    ),
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                        'rotation' => 0,
                        'wrap' => TRUE
                    )
                );

                $estiloTituloGDR = array(
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
                    'fill' => array(
                        'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array(
                            'argb' => 'FFFF0000')
                    ),
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN
                        )
                    ),
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                        'rotation' => 0,
                        'wrap' => TRUE
                    )
                );

                $estiloTituloValorCompartido = array(
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
                    'fill' => array(
                        'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array(
                            'argb' => 'FF33CCCC')
                    ),
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN
                        )
                    ),
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                        'rotation' => 0,
                        'wrap' => TRUE
                    )
                );

                $estiloTituloDesarrolloDePersonas = array(
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
                    'fill' => array(
                        'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array(
                            'argb' => 'FF993366')
                    ),
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN
                        )
                    ),
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                        'rotation' => 0,
                        'wrap' => TRUE
                    )
                );

                $estiloTituloComunicaciones = array(
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
                    'fill' => array(
                        'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array(
                            'argb' => 'FFBFBFBF')
                    ),
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN
                        )
                    ),
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                        'rotation' => 0,
                        'wrap' => TRUE
                    )
                );

                $estiloTituloPrincipios = array(
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
                    'fill' => array(
                        'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array(
                            'argb' => 'FFFFC000')
                    ),
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN
                        )
                    ),
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                        'rotation' => 0,
                        'wrap' => TRUE
                    )
                );

                $estiloTituloColumnas = new PHPExcel_Style();
                $estiloTituloColumnas->applyFromArray( array(
                'font' => array(
                    'name'      => 'Calibri',
                    'bold'      => true,
                    'italic'    => false,
                    'strike'    => false,
                    'size' =>9,
                    'color'     => array(
                        'rgb' => '000000'
                    )
                ),
                'fill' => array(
                    'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array(
                        'argb' => 'FFFFFF99')
                ),
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                    'rotation' => 0,
                    'wrap' => TRUE
                    )
                ));

                $estiloGrilla = new PHPExcel_Style();
                $estiloGrilla->applyFromArray( array(
                'font' => array(
                    'name'      => 'Calibri',
                    'bold'      => false,
                    'italic'    => false,
                    'strike'    => false,
                    'size' =>9,
                    'color'     => array(
                        'rgb' => '000000'
                    )
                ),
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                    'rotation' => 0,
                    'wrap' => TRUE
                    )
                ));

                $tituloReporte = "TARJETA DE OBJETIVOS PERSONALES (TOP)";
                $tituloNombre = "Nombre de empleado:";
                $tituloPuesto = "Puesto:";
                $tituloSupervisor = "Supervisor:";
                $tituloPeriodo = "Periodo de revisión:";
                $tituloFecha = "Fecha:";
                $tituloEvaluaciones = array("1ª evaluación","2ª evaluación");
                $titulosColumnas = array("Dimensión","Objetivo de la empresa","Objetivo personal","Indicador","Fuente de Datos","Valor de referencia","Peso","% Alcanzado","Peso Real");//,"Peso real","Real","Peso real");
                $titulosDimensiones = array("FINANZAS","GESTIÓN DE RIESGO","VALOR COMPARTIDO","DESARROLLO DE PERSONAS","COMUNICACION E IMAGEN","PRINCIPIOS");

                $this->excel->setActiveSheetIndex(0)
                    ->mergeCells('A1:K1')
                    ->mergeCells('C3:D3')
                    ->mergeCells('C4:D4')
                    ->mergeCells('C5:D5')
                    ->mergeCells('F3:I3')
                    ->mergeCells('F4:I4')
                    ->mergeCells('F5:I5')
                    ->mergeCells('H8:I8')
//                    ->mergeCells('H9:I9')
                    ->mergeCells('J8:K8');

                $fecha = date('d/m/Y', strtotime($datos['fecha_alta']));
                $grilla = $this->reporte->datos_grilla($datos['id_top']);

                $i = 10; //Numero de fila donde comienza a rellenar
                $flag = 0;
                $bandera = 0;
                $columna=PHPExcel_Cell::stringFromColumnIndex(0);
                $celdadimension = $i;
                foreach ($grilla as $fila) 
                {
                    if ($bandera == 0)
                    {
                        if($flag == 1)
                        {
                            if($dimension != $fila['id_dimension'])
                            {
                                $this->excel->getActiveSheet()->setSharedStyle($estiloGrilla, 'G'.$i.':I'.$i);
                                $this->excel->getActiveSheet()->setSharedStyle($estiloGrilla, 'B'.$celdadimension.':I'.($i-1));

                                $this->excel->setActiveSheetIndex(0)        
                                    ->setCellValue('G'.$i, $peso)
                                    ->setCellValue('I'.$i, $pesoreal1);
                                $i = $i + 2;

                                $pesot = $pesot + $peso;
                                $pesoreal1t = $pesoreal1t + $pesoreal1;

                                $peso = 0;
                                $pesoreal1 = 0;
                                switch ($dimension) 
                                {
                                    case 1:
                                        $this->excel->setActiveSheetIndex(0)
                                            ->setCellValue($columna.$celdadimension, $titulosDimensiones[0]);
                                        $this->excel->getActiveSheet()->getStyle($columna.$celdadimension)->applyFromArray($estiloTituloFinanzas);
                                        break;
                                    case 2:
                                        $this->excel->setActiveSheetIndex(0)
                                            ->setCellValue($columna.$celdadimension, $titulosDimensiones[1]);
                                        $this->excel->getActiveSheet()->getStyle($columna.$celdadimension)->applyFromArray($estiloTituloGDR);
                                        break;
                                    case 3:
                                        $this->excel->setActiveSheetIndex(0)
                                            ->setCellValue($columna.$celdadimension, $titulosDimensiones[2]);
                                        $this->excel->getActiveSheet()->getStyle($columna.$celdadimension)->applyFromArray($estiloTituloValorCompartido);
                                        break;
                                    case 4:
                                        $this->excel->setActiveSheetIndex(0)
                                            ->setCellValue($columna.$celdadimension, $titulosDimensiones[3]);
                                        $this->excel->getActiveSheet()->getStyle($columna.$celdadimension)->applyFromArray($estiloTituloDesarrolloDePersonas);
                                        break;
                                    case 5:
                                        $this->excel->setActiveSheetIndex(0)
                                            ->setCellValue($columna.$celdadimension, $titulosDimensiones[4]);
                                        $this->excel->getActiveSheet()->getStyle($columna.$celdadimension)->applyFromArray($estiloTituloComunicaciones);
                                        break;
                                }
                                $celdadimension = $i;
                            }
                            $dimension = $fila['id_dimension'];
                            $bandera = 1;
                        }
                        else
                        {
                            $dimension = $fila['id_dimension'];
                            $bandera = 1;
                            $flag = 1;
                        }
                        
                    }
                    
                    if ($dimension == $fila['id_dimension'])
                    {
                        $columna++;
                        $this->excel->setActiveSheetIndex(0)
                            ->setCellValue($columna.$i, $fila['oe']);
                        $columna++;
                        $this->excel->setActiveSheetIndex(0)
                            ->setCellValue($columna.$i, $fila['op']);
                        $columna++;
                        $this->excel->setActiveSheetIndex(0)
                            ->setCellValue($columna.$i, $fila['indicador']);
                        $columna++;
                        $this->excel->setActiveSheetIndex(0)
                            ->setCellValue($columna.$i, $fila['fd']);
                        $columna++;
                        $this->excel->setActiveSheetIndex(0)
                            ->setCellValue($columna.$i, $fila['valor_ref']);
                        $columna++;
                        $this->excel->setActiveSheetIndex(0)
                            ->setCellValue($columna.$i, $fila['peso']);
                        $columna++;
                        $this->excel->setActiveSheetIndex(0)
                            ->setCellValue($columna.$i, $fila['real1']);
                        $columna++;
                            //->setCellValue('J'.$i, $fila['real2']);
                            if ($fila['real1'] != 0)
                            {
                                $this->excel->setActiveSheetIndex(0)
                                    ->setCellValue($columna.$i, (($fila['peso']*$fila['real1'])/100));
                                $pesoreal1 = $pesoreal1 + (($fila['peso']*$fila['real1'])/100);
                            }
                            /*if ($fila['real2'] != 0)
                            {
                                $this->excel->setActiveSheetIndex(0)
                                ->setCellValue('K'.$i, ($fila['peso']*$fila['real2']));
                                $pesoreal2fi = $pesoreal2fi + (($fila['peso']*$fila['real1'])/100);
                            }*/
                            $peso = $peso + $fila["peso"];
//                            $real1 = $real1 + $fila['real1'];
                            /*$real2fi = $real2fi + $fila['real2'];*/
                            $i++;
                        $columna=PHPExcel_Cell::stringFromColumnIndex(0);
                    }
                    else
                    {
                        $this->excel->getActiveSheet()->setSharedStyle($estiloGrilla, 'G'.$i.':I'.$i);
                        $this->excel->getActiveSheet()->setSharedStyle($estiloGrilla, 'B'.$celdadimension.':I'.($i-1));
                        $this->excel->setActiveSheetIndex(0)
                            ->mergeCells($columna.$celdadimension.':'.$columna.($i-1));
                        
                        $this->excel->setActiveSheetIndex(0)        
                            ->setCellValue('G'.$i, $peso)
//                            ->setCellValue('H'.$i, $real1fi)
                            ->setCellValue('I'.$i, $pesoreal1);
                        $i = $i + 2;
                        
                        $pesot = $pesot + $peso;
                        $pesoreal1t = $pesoreal1t + $pesoreal1;
                        
                        $peso = 0;
                        $pesoreal1 = 0;
                        switch ($dimension) 
                        {
                            case 1:
                                $this->excel->setActiveSheetIndex(0)
                                    ->setCellValue($columna.$celdadimension, $titulosDimensiones[0]);
                                $this->excel->getActiveSheet()->getStyle($columna.$celdadimension)->applyFromArray($estiloTituloFinanzas);
                                break;
                            case 2:
                                $this->excel->setActiveSheetIndex(0)
                                    ->setCellValue($columna.$celdadimension, $titulosDimensiones[1]);
                                $this->excel->getActiveSheet()->getStyle($columna.$celdadimension)->applyFromArray($estiloTituloGDR);
                                break;
                            case 3:
                                $this->excel->setActiveSheetIndex(0)
                                    ->setCellValue($columna.$celdadimension, $titulosDimensiones[2]);
                                $this->excel->getActiveSheet()->getStyle($columna.$celdadimension)->applyFromArray($estiloTituloValorCompartido);
                                break;
                            case 4:
                                $this->excel->setActiveSheetIndex(0)
                                    ->setCellValue($columna.$celdadimension, $titulosDimensiones[3]);
                                $this->excel->getActiveSheet()->getStyle($columna.$celdadimension)->applyFromArray($estiloTituloDesarrolloDePersonas);
                                break;
                            case 5:
                                $this->excel->setActiveSheetIndex(0)
                                    ->setCellValue($columna.$celdadimension, $titulosDimensiones[4]);
                                $this->excel->getActiveSheet()->getStyle($columna.$celdadimension)->applyFromArray($estiloTituloComunicaciones);
                                break;
                        }

                        $columna++;
                        $this->excel->setActiveSheetIndex(0)
                            ->setCellValue($columna.$i, $fila['oe']);
                        $columna++;
                        $this->excel->setActiveSheetIndex(0)
                            ->setCellValue($columna.$i, $fila['op']);
                        $columna++;
                        $this->excel->setActiveSheetIndex(0)
                            ->setCellValue($columna.$i, $fila['indicador']);
                        $columna++;
                        $this->excel->setActiveSheetIndex(0)
                            ->setCellValue($columna.$i, $fila['fd']);
                        $columna++;
                        $this->excel->setActiveSheetIndex(0)
                            ->setCellValue($columna.$i, $fila['valor_ref']);
                        $columna++;
                        $this->excel->setActiveSheetIndex(0)
                            ->setCellValue($columna.$i, $fila['peso']);
                        $columna++;
                        $this->excel->setActiveSheetIndex(0)
                            ->setCellValue($columna.$i, $fila['real1']);
                        $columna++;
                            //->setCellValue('J'.$i, $fila['real2']);
                        if ($fila['real1'] != 0)
                        {
                            $this->excel->setActiveSheetIndex(0)
                                ->setCellValue($columna.$i, (($fila['peso']*$fila['real1'])/100));
                            $pesoreal1 = $pesoreal1 + (($fila['peso']*$fila['real1'])/100);
                        }
                        /*if ($fila['real2'] != 0)
                        {
                            $this->excel->setActiveSheetIndex(0)
                            ->setCellValue('K'.$i, ($fila['peso']*$fila['real2']));
                            $pesoreal2fi = $pesoreal2fi + (($fila['peso']*$fila['real1'])/100);
                        }*/
                        $peso = $peso + $fila["peso"];
//                            $real1 = $real1 + $fila['real1'];
                        /*$real2fi = $real2fi + $fila['real2'];*/
                        $celdadimension = $i;
                        $i++;
                        $columna=PHPExcel_Cell::stringFromColumnIndex(0);
                        $dimension = $fila['id_dimension'];
                        $bandera = 0;
                    }
                }
                $this->excel->getActiveSheet()->setSharedStyle($estiloGrilla, 'G'.$i.':I'.$i);
                $this->excel->getActiveSheet()->setSharedStyle($estiloGrilla, 'B'.$celdadimension.':I'.($i-1));
                $this->excel->setActiveSheetIndex(0)
                    ->mergeCells($columna.$celdadimension.':'.$columna.($i-1));

                $this->excel->setActiveSheetIndex(0)        
                    ->setCellValue('G'.$i, $peso)
//                            ->setCellValue('H'.$i, $real1fi)
                    ->setCellValue('I'.$i, $pesoreal1);
                

                $pesot = $pesot + $peso;
                $pesoreal1t = $pesoreal1t + $pesoreal1;
                $this->excel->setActiveSheetIndex(0)
                    ->setCellValue($columna.$celdadimension, $titulosDimensiones[5]);
                $this->excel->getActiveSheet()->getStyle($columna.$celdadimension)->applyFromArray($estiloTituloPrincipios);
                $i = $i + 2;

                // Se agregan los titulos del reporte
                $this->excel->setActiveSheetIndex(0)
                    ->setCellValue('A1',$tituloReporte) // Titulo del reporte
                    ->setCellValue('C3',$datos['nombre'])
                    ->setCellValue('F3',$datos['puesto'])
                    ->setCellValue('C4',$datos['supervisor'])
                    ->setCellValue('F4',$datos['puestosupervisor'])
                    ->setCellValue('F5',$datos['periodo'])
                    ->setCellValue('C5',$fecha)
                    ->setCellValue('B3',$tituloNombre)
                    ->setCellValue('E3',$tituloPuesto)
                    ->setCellValue('E4',$tituloPuesto)
                    ->setCellValue('B4',$tituloSupervisor)
                    ->setCellValue('E5',$tituloPeriodo)  //Titulo de las columnas
                    ->setCellValue('B5',$tituloFecha)
                    /*->setCellValue('H8',$tituloEvaluaciones[0])
                    ->setCellValue('J8',$tituloEvaluaciones[1])*/
                    ->setCellValue('H8','Evaluación Anual')
                    ->setCellValue('A9',$titulosColumnas[0])
                    ->setCellValue('B9',$titulosColumnas[1])
                    ->setCellValue('C9',$titulosColumnas[2])
                    ->setCellValue('D9',$titulosColumnas[3])
                    ->setCellValue('E9',$titulosColumnas[4])
                    ->setCellValue('F9',$titulosColumnas[5])
                    ->setCellValue('G9',$titulosColumnas[6])
                    ->setCellValue('H9',$titulosColumnas[7])
                    ->setCellValue('I9',$titulosColumnas[8])
                    /*->setCellValue('J9',$titulosColumnas[9])
                    ->setCellValue('K9',$titulosColumnas[10])*/
                    ->setCellValue('F'.$i,'TOTAL: ')
                    ->setCellValue('G'.$i,$pesot)
//                    ->setCellValue('H'.$i,$real1total)
                    ->setCellValue('I'.$i,$pesoreal1t);
                /*->setCellValue('J'.$i,$real2total)
                ->setCellValue('K'.$i,$pesoreal2total); */       

                $this->excel->getActiveSheet()->setSharedStyle($estiloGrilla, 'F'.$i.':I'.$i);
                $this->excel->getActiveSheet()->getStyle('A1')->applyFromArray($estiloTituloReporte);

                 $this->excel->getActiveSheet()->getStyle('B3:B5')->applyFromArray($estiloTituloDatos);
                 $this->excel->getActiveSheet()->getStyle('C3:D3')->applyFromArray($estiloNombre);
                 $this->excel->getActiveSheet()->getStyle('C4:D4')->applyFromArray($estiloDatos);
                 $this->excel->getActiveSheet()->getStyle('C5:D5')->applyFromArray($estiloDatos);
                 $this->excel->getActiveSheet()->getStyle('F3:I3')->applyFromArray($estiloDatos);
                 $this->excel->getActiveSheet()->getStyle('F4:I4')->applyFromArray($estiloDatos);
                 $this->excel->getActiveSheet()->getStyle('F5:I5')->applyFromArray($estiloDatos);
                 $this->excel->getActiveSheet()->getStyle('E3:E5')->applyFromArray($estiloTituloDatos);
                 $this->excel->getActiveSheet()->getStyle('H8:I8')->applyFromArray($estiloTituloEvaluaciones);
//                 $this->excel->getActiveSheet()->getStyle('J8:K8')->applyFromArray($estiloTituloEvaluaciones);
                 $this->excel->getActiveSheet()->setSharedStyle($estiloTituloColumnas, "A9:I9"); 
                 $this->excel->setActiveSheetIndex()->getRowDimension(1)->setRowHeight(22);
                 $this->excel->setActiveSheetIndex(0)->getColumnDimension('A')->setAutoSize(TRUE);
                 $this->excel->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth('30');
                 $this->excel->setActiveSheetIndex(0)->getColumnDimension('C')->setWidth('30');
                 $this->excel->setActiveSheetIndex(0)->getColumnDimension('D')->setWidth('30');
                 $this->excel->setActiveSheetIndex(0)->getColumnDimension('E')->setWidth('30');
                 $this->excel->setActiveSheetIndex(0)->getColumnDimension('F')->setWidth('30');
//                 $this->excel->setActiveSheetIndex(0)->getColumnDimension('H')->setWidth('20');

                  // Se manda el archivo al navegador web, con el nombre que se indica, en formato 2007
                $filename="TOP".$datos['periodo']."-".$datos['nomape'].".xls";
                header('Content-Type:application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="'.$filename.'"');
                header('Cache-Control: max-age=0');

                $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
                $objWriter->save('php://output');
    }
        
	public function generar_x()
	{
            $this->load->library('excel');
            //activate worksheet number 1
            $this->excel->setActiveSheetIndex(0);
            //name the worksheet
            $this->excel->getActiveSheet()->setTitle('test worksheet');
            //set cell A1 content with some text
            $this->excel->getActiveSheet()->setCellValue('A1', 'This is just some text value');
            //change the font size
            $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
            //make the font become bold
            $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
            //merge cell A1 until D1
            $this->excel->getActiveSheet()->mergeCells('A1:D1');
            //set aligment to center for that merged cell (A1 to D1)
            $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $filename='just_some_random_name.xls'; //save our workbook as this file name
            header('Content-Type: application/vnd.ms-excel'); //mime type
            header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
            header('Cache-Control: max-age=0'); //no cache

            //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
            //if you want to save it as .XLSX Excel 2007 format
            $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
            //force user to download the Excel file without writing it to server's HD
            $objWriter->save('php://output');
	}
        
        
	
	
}
?>