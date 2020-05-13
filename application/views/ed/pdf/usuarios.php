<?php
//datos
    $col1=92;
    $col2=20;
    $col3=$col1-$col2;
    $h1=8;
    $h2=6;
    $tipo_texto='helvetica';
    $texto_label=7;
    $texto_dato=8;
    $pdf->SetFillColor(238,199,0);
    $pdf->SetXY($xIniCuerpo, $yIniCuerpo);
    $xLogo=$pdf->GetX()+3;
    $yLogo=$pdf->GetY()+10;
    $pdf->SetFont('helvetica','', '10');
    $pdf->MultiCell($col1, $h1,  'Usuario', 'LRTB', 'C', true, 0, '', '',TRUE, 0, false,false, $h1,'M');
    $pdf->MultiCell($col1, $h1,  'Supervisor', 'LRTB', 'C', true, 1, '', '',TRUE, 0, false,false, $h1,'M');
    
    $dato='Nombre:';
    $dato_usuario=$ed['cabecera']['usuario'];
    $dato_supervisor=$ed['cabecera']['supervisor'];
    $pdf->SetFont($tipo_texto,'', $texto_label);
    $pdf->MultiCell($col2, $h2,  $dato, 'L', 'M', false, 0, '', '',TRUE, 0, false,false, $h2,'M');
    $pdf->SetFont($tipo_texto,'', 10);
    $pdf->MultiCell($col3, $h2,  $dato_usuario, 'R', 'L', false, 0,'' , '',TRUE, 0, false,false, $h2,'M');
    $pdf->SetFont($tipo_texto,'', $texto_label);
    $pdf->MultiCell($col2, $h2,  $dato, 'L', 'L', false, 0, '', '',TRUE, 0, false,false, $h2,'M');
    $pdf->SetFont($tipo_texto,'', 10);
    $pdf->MultiCell($col3, $h2,  $dato_supervisor, 'R', 'L', false, 1, '', '',TRUE, 0, false,false, $h2,'M');
    
    $dato='Puesto:';
    $dato_usuario=$ed['cabecera']['puesto'];
    $dato_supervisor=$ed['cabecera']['puesto_supervisor'];
    $pdf->SetFont($tipo_texto,'', $texto_label);
    $pdf->MultiCell($col2, $h2,  $dato, 'L', 'M', false, 0, '', '',TRUE, 0, false,false, $h2,'M');
    $pdf->SetFont($tipo_texto,'', $texto_dato);
    $pdf->MultiCell($col3, $h2,  $dato_usuario, 'R', 'L', false, 0,'' , '',TRUE, 0, false,false, $h2,'M');
    $pdf->SetFont($tipo_texto,'', $texto_label);
    $pdf->MultiCell($col2, $h2,  $dato, 'L', 'L', false, 0, '', '',TRUE, 0, false,false, $h2,'M');
    $pdf->SetFont($tipo_texto,'', $texto_dato);
    $pdf->MultiCell($col3, $h2,  $dato_supervisor, 'R', 'L', false, 1, '', '',TRUE, 0, false,false, $h2,'M');
    
    $dato='Area:';
    $dato_usuario=$ed['cabecera']['area'];
    $dato_supervisor=$ed['cabecera']['area_supervisor'];
    $pdf->SetFont($tipo_texto,'', $texto_label);
    $pdf->MultiCell($col2, $h2,  $dato, 'LB', 'M', false, 0, '', '',TRUE, 0, false,false, $h2,'M');
    $pdf->SetFont($tipo_texto,'', $texto_dato);
    $pdf->MultiCell($col3, $h2,  $dato_usuario, 'RB', 'L', false, 0,'' , '',TRUE, 0, false,false, $h2,'M');
    $pdf->SetFont($tipo_texto,'', $texto_label);
    $pdf->MultiCell($col2, $h2,  $dato, 'LB', 'LB', false, 0, '', '',TRUE, 0, false,false, $h2,'M');
    $pdf->SetFont($tipo_texto,'', $texto_dato);
    $pdf->MultiCell($col3, $h2,  $dato_supervisor, 'RB', 'L', false, 1, '', '',TRUE, 0, false,false, $h2,'M');
?>
