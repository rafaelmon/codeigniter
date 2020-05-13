<?php
    $col1=184;
    $h1=8;
    $h2=6;
    $tipo_texto='helvetica';
    
    $pdf->SetFillColor(233,215,165);
    $pdf->SetFont($tipo_texto,'B', 11);
    $pdf->MultiCell($col1, $h2,  '', 0, 'C', false, 1, '', '',true, 0, false,false, $h1,'M');
    $pdf->MultiCell($col1, $h1,  'Comentario del Usuario', 1, 'L', true, 1, '', '',true, 0, false,false, $h1,'M');
    $pdf->SetFont($tipo_texto,'', 7);
    $pdf->MultiCell($col1, $h1,  $ed['cabecera']['comentario_usuario'], 1, 'L', false, 1, '', '',true, 0, false,false, $h1,'M');
?>
