<?php
//datos

    $pdf->SetXY($xIniEnc, $yIniEnc);
    $pdf->SetFont('helvetica','', '13');
    $xLogo=$pdf->GetX()+3;
    $yLogo=$pdf->GetY()+1;
    $pdf->MultiCell($col1, $ht,  '', 1, 'L', false, 0, '', '', true, 0, false,false, $ht,'M');
//    $pdf->MultiCell($w, $h, $txt, $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    $pdf->MultiCell($col2, $ht, $titulo, 1, 'C', 0, 0, '', '', true, 0, false,false, $ht,'M');
//    $pdf->MultiCell($col2, $ht, $logo, 1, 'C', 0, 0, '', '', true, 0, false,false, $ht,'M');
    $pdf->SetFont('helvetica', '', '10');
    $pdf->SetFillColor($f=225,$f,$f);
    $xSig=$pdf->GetX();
    $pdf->MultiCell($col3, $h1, 'Código', 1, 'L', true, 0, '', '', true, 0, false,false, $h1,'M');
    $pdf->SetFont('helvetica', '', '7');
    $pdf->MultiCell($col4, $h1, $codigo, 1, 'C', false, 1, '', '', true,4,false,false,$h1,'M',true);
    $pdf->SetFont('helvetica', '', '10');
//     $pdf->SetX($col12);

    $pdf->MultiCell($col3, $h2, 'Fecha de vigencia', 1, 'L', true, 0, $xSig, '', true, 0, false,false, $h2,'M');
    $pdf->MultiCell($col4, $h2, $vigencia, 1, 'C', false, 1, '', '', true,1,false,false,$h2,'M');

    $pdf->MultiCell($col3, $h3, 'Página', 1, 'L', true, 0, $xSig, '', true, 0, false,false, $h3,'M');
    $pdf->MultiCell($col4, $h3, "$i de ".$paginas, 1, 'C', false, 1, '', '', true, 0, false,false, $h3,'M');
//     $pdf->SetXY($marginL+10, $marginT+10);
    $pdf->SetXY(10, 10);
    
    $pdf->Image($logo, $xLogo, $yLogo, 34, 26   , ''      , ''      , ''       , false        , 300     , ''        , false       , false         , 0         , false        , false        , false);
       // Image($file, $x='' , $y='' ,$w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false)
//    $pdf->MultiCell(30, 3,  $wh['w']."--".$wh['h'], 1, 'L', false, 0, 0, 0, true, 0, false,false, '','M');

?>
