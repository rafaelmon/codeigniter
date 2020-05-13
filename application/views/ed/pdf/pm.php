<?php
//datos
    $col1=184;
    $col2=$col1/2;
    $col3=67;
    $col4=25;
    $h1=8;
    $h2=6;
    $tipo_texto='helvetica';
    
//    $line=array('BTLR' => array('width' => 0, 'dash' => 0));
    $pdf->SetFillColor(233,215,165);
    $pdf->SetFont($tipo_texto,'B', 11);
    $pdf->MultiCell($col1, $h2,  '', 0, 'C', false, 1, '', '',true, 0, false,false, $h1,'M');
    $pdf->MultiCell($col1, $h1,  'Plan de Mejora', 1, 'L', true, 1, '', '',true, 0, false,false, $h1,'M');
    
    $band=0;
    if(is_array($ed['pm']))
    {
        foreach ($ed['pm'] as $pm) 
        {
            if($band==0)
            {
                $pdf->SetFont($tipo_texto,'B', 9);
//                $pdf->MultiCell($col2, $h2,$pm['competencia'], 1, 'L', false, 0, '', '',true, 0, false,false, 0,'M');
                $pdf->MultiCell($col2, $h2,'Competencia', 1, 'C', true, 0, '', '',true, 0, false,false, 0,'M');
                $pdf->MultiCell($col3, $h2,'Descripción PM', 1, 'C', true, 0, '', '',true, 0, false,false, 0,'M');
                $pdf->MultiCell($col4, $h2,'Plazo', 1, 'C', true, 1, '', '',true, 0, false,false, 0,'M');
                $band=1;
            }
//            else
//            {
            $pdf->SetFont($tipo_texto,'B', 9);
            $pdf->MultiCell($col1, $h2,$pm['competencia'], 1, 'L', false, 1, '', '',true, 0, false,false, 0,'M');
//            }
            foreach($pm['subcompetencia'] as $subcompetencia)
            {
                $pdf->SetFont($tipo_texto,'', 7);
                $pdf->MultiCell($col2, $h2,$subcompetencia['subcompetencia'], 1, 'L', false, 0, '', '',true, 0, false,false, $h1,'T',true);
                $pdf->MultiCell($col3, $h2,$subcompetencia['accion'], 1, 'L', false, 0, '', '',true, 0, false,false, $h2,'T',true);
                $pdf->MultiCell($col4, $h2,$subcompetencia['plazo'], 1, 'C', false, 1, '', '',true, 0, false,false, 0,'M',false);
            }
        }
    }
    else
    {
        $pdf->SetFont($tipo_texto,'', 7);
        $pdf->MultiCell($col1, $h2,$ed['pm'], 1, 'L', false, 1, '', '',true, 0, false,false, 0,'M');//$line
    }
?>
