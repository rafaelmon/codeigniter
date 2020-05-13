<?php
    $col1=61;
    $col2=62;
    $h1=5;
    $h2=25;
    $tipo_texto='helvetica';
    
    $line=array('T' => array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 2, 'color' => array(0, 0, 0)));
//    $pdf->MultiCell($col1, $h2,  '', 0, 'C', false, 1, '', '',true, 0, false,false, $h1,'M');
     $pdf->Ln(3);
    $pdf->SetFont($tipo_texto,'', 9);
    $pdf->MultiCell($col1, $h1,   $ed['firmas']['s'], 0, 'C', false, 0, '', '',true, 0, false,false, $h1,'B');
    $pdf->MultiCell($col1, $h1,  '', 0, 'L', false, 0, '', '',true, 0, false,false, $h1,'B');
    $pdf->MultiCell($col1, $h1,   $ed['firmas']['u'], 0, 'C', false, 1, '', '',true, 0, false,false, $h1,'B');
    $pdf->SetFont($tipo_texto,'', 7);
    $pdf->MultiCell($col1, $h1,  $ed['cabecera']['fecha_cierre_s'], 0, 'C', false, 0, '', '',true, 0, false,false, $h1,'B');
    $pdf->MultiCell($col1, $h1,  '', 0, 'L', false, 0, '', '',true, 0, false,false, $h1,'B');
    $pdf->MultiCell($col1, $h1,  $ed['cabecera']['fecha_cierre_u'], 0, 'C', false, 1, '', '',true, 0, false,false, $h1,'B');
    $pdf->SetFont($tipo_texto,'B', 11);
    $pdf->MultiCell($col1, $h1,  'Firma Supervisor', $line, 'C', false, 0, '', '',true, 0, false,false, $h1,'M');
    $pdf->MultiCell($col1, $h1,  '', 0, 'L', false, 0, '', '',true, 0, false,false, $h1,'M');
    $pdf->MultiCell($col1, $h1,  'Firma Evaluado', $line, 'C', false, 0, '', '',true, 0, false,false, $h1,'M');
?>
