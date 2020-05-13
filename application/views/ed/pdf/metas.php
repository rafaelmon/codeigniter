<?php
//datos
    $col1=184;
    $col2=159;
    $col3=25;
    $h1=8;
    $h2=6;
    $tipo_texto='helvetica';
    
//    $line=array('BTLR' => array('width' => 0, 'dash' => 0));
    $pdf->SetFillColor(233,215,165);
    $pdf->SetFont($tipo_texto,'B', 11);
    $pdf->MultiCell($col1, $h2,  '', 0, 'C', false, 1, '', '',true, 0, false,false, $h1,'M');
    $pdf->MultiCell($col2, $h1,  'Metas', 1, 'L', true, 0, '', '',true, 0, false,false, $h1,'M');
    $pdf->MultiCell($col3, $h1,  'Plazo', 1, 'L', true, 1, '', '',true, 0, false,false, $h1,'M');
    
    if(is_array($ed['metas']))
    {
        foreach ($ed['metas'] as $meta) 
        {
            $pdf->SetFont($tipo_texto,'', 7);
            $pdf->MultiCell($col2, $h2,$meta['meta'], 1, 'L', false, 0, '', '',true, 0, false,false, 0,'M');//$line
            $pdf->MultiCell($col3, $h2,$meta['plazo'], 1, 'L', false, 1, '', '',true, 0, false,false, 0,'M');//$line
        }
    }
    else
    {
        $pdf->SetFont($tipo_texto,'', 7);
        $pdf->MultiCell($col2, $h2,$ed['metas'], 1, 'L', false, 0, '', '',true, 0, false,false, 0,'M');//$line
        $pdf->MultiCell($col3, $h2,$ed['metas'], 1, 'L', false, 1, '', '',true, 0, false,false, 0,'M');//$line
    }
?>
