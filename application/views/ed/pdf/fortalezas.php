<?php
//datos
    $col1=184;
    $h1=8;
    $h2=6;
    $tipo_texto='helvetica';
    
    $line=array('BTLR' => array('width' => 0, 'dash' => 0));
    $pdf->SetFillColor(74,206,38);
    $pdf->SetFont($tipo_texto,'B', 11);
    $pdf->MultiCell($col1, $h2,  '', 0, 'C', false, 1, '', '',true, 0, false,false, $h1,'M');
    $pdf->MultiCell($col1, $h1,  'Fortalezas', $line, 'L', true, 1, '', '',true, 0, false,false, $h1,'M');
    $pdf->SetFont($tipo_texto,'', 7);
    
    if(is_array($ed['fortalezas']))
    {
        foreach ($ed['fortalezas'] as $fortaleza) 
            $pdf->MultiCell($col1, $h2,$fortaleza['fortaleza']  , 1, 'L', false, 1, '', '',true, 0, false,false, 0,'M');//$line
    }
    else
            $pdf->MultiCell($col1, $h2,$ed['fortalezas']        , 1, 'L', false, 1, '', '',true, 0, false,false, 0,'M');//$line
    
?>
