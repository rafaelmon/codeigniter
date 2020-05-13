<?php
//datos
    $col1=184;
    $col2=40;
    $col3=30;
    $h1=8;
    $h2=6;
    $tipo_texto='helvetica';
    
    $line=array('BTLR' => array('width' => 0, 'dash' => 0));
    $pdf->SetFillColor(0,0,0);
    $pdf->SetTextColor(255,255,255);
    $pdf->SetFont($tipo_texto,'B', 11);
    $pdf->MultiCell($col1, $h1,  '', 0, 'C', false, 1, '', '',true, 0, false,false, $h1,'M',false);
    $pdf->MultiCell($col2, $h1,  'Cumplimiento', $line, 'L', true, 0, '', '',true, 0, false,false, $h1,'M',false);
    $pdf->SetTextColor(0,0,0);
    $pdf->SetFont($tipo_texto,'', 7);
    $pdf->MultiCell($col3, $h1,$ed['cumplimiento'], 1, 'C', false, 1, '', '',true, 0, false,false, $h1,'M',false);//$line
?>
