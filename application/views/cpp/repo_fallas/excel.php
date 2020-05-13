<?php
$date=date("Ymd");
$time=date("His");
$filename = $date."ReporteFallas.xlsx";

$this->load->library('excel');
$objPHPExcel= new PHPExcel();

$objPHPExcel->getProperties()
            ->setCreator("Sistema de Mejora Continua (SMC)") // Nombre del autor
            ->setLastModifiedBy("SMC") //Ultimo usuario que lo modificó
            ->setTitle('Reporte de Fallas - Módulo CPP') // Titulo
            ->setSubject('') //Asunto
            ->setDescription('Usuario: '.$usuario) //Descripción
            ->setKeywords('Filtros: '.$filtros) //Etiquetas
            ->setCategory('Reportes Dinámicos');

$objPHPExcel->setActiveSheetIndex(0);

$tablaEventosConsecuencias= array(
    array('atrib'=>'#'                              ,'data'=>'id_evento',   'width'=>5),
    array('atrib'=>'Fecha Inicio'                   ,'data'=>'fh_ini',      'width'=>22),
    array('atrib'=>'Fecha Termino'                  ,'data'=>'fh_fin',      'width'=>22),
    array('atrib'=>'Hs'                             ,'data'=>'hrs',         'width'=>10),
    array('atrib'=>'Evento'                         ,'data'=>'evento',      'width'=>50),
    array('atrib'=>'Criticidad'                     ,'data'=>'criticidad',  'width'=>12),
    array('atrib'=>'Estado'                         ,'data'=>'estado',      'width'=>15),
    array('atrib'=>'Sector'                         ,'data'=>'sector',      'width'=>50),
    array('atrib'=>'Equipo'                         ,'data'=>'equipo',      'width'=>50),
    array('atrib'=>'Usuario Alta'                   ,'data'=>'usuario_alta','width'=>25),
    
    array('atrib'=>'consecuencias'                      ,'data'=>array(
        array('atrib'=>'Tipo de Consecuencia'           ,'data'=>'consecuencia',        'width'=>30),
        array('atrib'=>'Consecuencia'                   ,'data'=>'descripcion',         'width'=>70),
        array('atrib'=>'Pérdida de Producción (Ton)'    ,'data'=>'unidades_perdidas',   'width'=>30),
        array('atrib'=>'Monto perdido (US$)'            ,'data'=>'monto',               'width'=>22)
        )
    )
);
//function countColumns($array,&$n)
//{
//    foreach ($array as $key=>$value)
//    {   
//        if(is_array($value['data']))
//            countColumns($value['data'],$n);
//        else  
//            $n++;
//    }
//    return $n;
//};
function countRows($arrayDatos,$arrayEstruc,&$n)
{
    foreach ($arrayDatos as $claveDato=>$valorDato)
    {
        $key="";
        foreach ($arrayEstruc as $claveEstruc=>$valorEstruc)
        {
            if (is_array($valorEstruc['data']))
                $key = $valorEstruc['atrib'];
        } 
        if($key!="")
        {
            if (is_array($valorDato[$key]))
            {
                countRows($valorDato[$key],$valorEstruc['data'],$n);
            }
            else 
                $n++;
        }
        else 
            $n++;
    } 
}
//$n=0;
//$totalCol=countColumns($tablaEventosConsecuencias,$n);
//echo $totalCol;
//echo $n;
//die();
$tablaEventosCausas= array(
    array('atrib'=>'#'                              ,'data'=>'id_evento',   'width'=>5),
    array('atrib'=>'Evento'                         ,'data'=>'evento',      'width'=>50),
    array('atrib'=>'Equipo Investigador'            ,'data'=>'ei',          'width'=>30),
    array('atrib'=>'causas'                         ,'data'=>array(
        array('atrib'=>'Area Causante'                  ,'data'=>'ac',              'width'=>30),
        array('atrib'=>'Causa Inmediata'                ,'data'=>'causa_inmediata', 'width'=>40),
        array('atrib'=>'Causa Raiz'                     ,'data'=>'causa_raiz',      'width'=>40),
        array('atrib'=>'tareas'                         ,'data'=>array(
            array('atrib'=>'Tarea'                          ,'data'=>'tarea',               'width'=>40),
            array('atrib'=>'Responsable Tarea'              ,'data'=>'usuario_responsable', 'width'=>25),
            array('atrib'=>'Estado Tarea'                   ,'data'=>'estado',              'width'=>15),
            array('atrib'=>'Fecha Término Tarea'            ,'data'=>'fecha_vto',           'width'=>20),
                )
            )
        )
    )
);

$objDrawing = new PHPExcel_Worksheet_Drawing();
$objDrawing->setName('Logo');
$objDrawing->setDescription('Logo');
$objDrawing->setPath(PATH_BASE.'images/CabeceraControldeFallas.jpg');
//$objDrawing->setHeight(120);
//$objDrawing->setWidth(700);
//$objDrawing->setOffsetX(1100);
//$objDrawing->setOffsetY(110);
$objDrawing->setCoordinates('F1');

$objDrawing1 = new PHPExcel_Worksheet_Drawing();
$objDrawing1->setName('Logo');
$objDrawing1->setDescription('Logo');
$objDrawing1->setPath(PATH_BASE.'images/CabeceraControldeFallas.jpg');
$objDrawing1->setCoordinates('C1');
//

$col_left=0;
$row_ini=6;

$styleArrayTitulos = array(
    'alignment' => array(
        'horizontal'    =>'center',
        'vertical'      =>'center',
    ),
    'font'  => array(
        'bold'  => true,
        'color' => array('rgb' => 'FFFFFF'),
        'size'  => 10,
        'name'  => 'Verdana'
    ),
    'fill' => array(
        'type' =>'solid',
        'color' => array('rgb' => '000000')
    )
);
$styleArrayDatos = array(
    'alignment' => array(
        'horizontal'    =>'center',
        'vertical'      =>'top',
    ),
    'font'  => array(
        'bold'  => false,
        'color' => array('rgb' => '000000'),
        'size'  => 10,
        'name'  => 'Verdana'
    ),
    'fill' => array(
        'type' =>'none',
        'color' => array('rgb' => 'FFFFFF')
    )
);
$styleArrayBordes = array(
    'borders' => array(
        'allborders' => array(
            'style' => 'thin'
        )
    )
);
function titulos(&$col,&$row,&$sheet,$array,$styleArray)
{
    foreach ($array as $key=>$value)
    {
        if (is_array($value['data']))
           titulos($col,$row,$sheet,$value['data'],$styleArray);
        else
        {
            $sheet->setCellValueByColumnAndRow($col, $row, $value['atrib']);
            $cell = $sheet->getCellByColumnAndRow($col, $row);
            $colIndex = $cell->getColumn();
            $cellIndex = $cell->getCoordinate();
//             echo "<pre>".print_r($value['data'],true)."</pre>";die();
            //if($colIndex != 'B')
            $sheet->getStyle($colIndex)->getAlignment()->setWrapText(true);
            $sheet->getColumnDimension($colIndex)->setWidth($value['width']);
            $sheet->getStyle($cellIndex)->applyFromArray($styleArray);
            $col ++;
        }
    }
};
function setStyleCell($sheet,$col,$row,$styleArray)
{
    $cell = $sheet->getCellByColumnAndRow($col, $row);
    $colIndex = $cell->getColumn();
    $cellIndex = $cell->getCoordinate();
    $sheet->getStyle($cellIndex)->applyFromArray($styleArray);
}
function setBordersStyleRange($sheet,$col_ini,$row_ini,$col_end,$row_end,$styleArray)
{
    
    $col_cell_ini = PHPExcel_Cell::stringFromColumnIndex($col_ini);
    $col_cell_end = PHPExcel_Cell::stringFromColumnIndex($col_end);
    $sheet->getStyle("$col_cell_ini{$row_ini}:$col_cell_end{$row_end}")->applyFromArray($styleArray);
}
function datos(&$col,&$row,&$sheet,$arrayEstruc,$arrayDatos,$styleArray)
{
    $col_ini=$col;
    foreach ($arrayDatos as $claveDato=>$valorDato)
    {
        $col=$col_ini;
        $qfilas=0;
        $arrayRegistro=array();
        $arrayRegistro[]=$valorDato;
        countRows($arrayRegistro,$arrayEstruc,$qfilas);
                
        foreach ($arrayEstruc as $claveEstruc=>$valorEstruc)
        {
            if (is_array($valorEstruc['data']))
            {
                if ($valorDato[$valorEstruc['atrib']] != 0 )
                {
                    $n=count($valorDato[$valorEstruc['atrib']]);
                    datos($col,$row,$sheet,$valorEstruc['data'],$valorDato[$valorEstruc['atrib']],$styleArray);
                }
            }
            else
            {
                
                $data=$valorDato[$valorEstruc['data']];
                $count=0;
                $sheet->setCellValueByColumnAndRow($col, $row,$data);
                setStyleCell($sheet,$col,$row,$styleArray);
                if($qfilas>1)
                {
                    $row2 =$row+$qfilas-1;
                    $col_txt=PHPExcel_Cell::stringFromColumnIndex($col);
                    $sheet->mergeCells("$col_txt{$row}:$col_txt{$row2}");
                }
                $col++;
            }
        }
        $row++;
    }
    $row--;
};


$sheet = $objPHPExcel->getSheet(0);
$sheet->setTitle('Eventos y Consecuencias');
$objDrawing->setWorksheet($sheet);
$col=$col_left;
$row=$row_ini;
titulos($col,$row,$sheet,$tablaEventosConsecuencias,$styleArrayTitulos);
$col_total=$col-1;
$col=$col_left;
$row++;
datos($col,$row,$sheet,$tablaEventosConsecuencias,$datos,$styleArrayDatos);
setBordersStyleRange($sheet,$col_left,$row_ini,$col_total,$row,$styleArrayBordes);

$objPHPExcel->createSheet(NULL, "causas");
$sheet2 = $objPHPExcel->setActiveSheetIndex(1);
$objPHPExcel->getActiveSheet()->setTitle('Causas y Remediación');
//$sheet2->setTitle('Causas y Remediación');
$objDrawing1->setWorksheet($sheet2);
$sheet2 = $objPHPExcel->getSheet(1);
$col=$col_left;
$row=$row_ini;
titulos($col,$row,$sheet2,$tablaEventosCausas,$styleArrayTitulos);
$col_total=$col-1;
$col=$col_left;
$row++;
datos($col,$row,$sheet2,$tablaEventosCausas,$datos,$styleArrayDatos);
setBordersStyleRange($sheet2,$col_left,$row_ini,$col_total,$row,$styleArrayBordes);

$objPHPExcel->setActiveSheetIndex(0);
header('Content-Type:application/vnd.ms-excel'); //mime type
header('Content-Type:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); //mime type
header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name          
header('Cache-Control: max-age=0'); //no cache
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
//$objWriter->save(str_replace(__FILE__,'repo.xlsx',__FILE__));
$objWriter->save('php://output');
?>