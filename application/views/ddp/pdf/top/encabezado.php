<?php
//datos
    $x=25;
    $y=10;
    $pdf->SetXY($x, $y);
    $pdf->SetFont('helvetica','', '18');
//    $xLogo=$pdf->GetX()+3;
//    $yLogo=$pdf->GetY()+1;
    $pdf->SetFillColor($r=255,$g=217,$b=102);
//    $pdf->MultiCell($w, $h, $txt, $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
//    $pdf->MultiCell(140, 5,'<i>TARJETA DE OBJETIVOS PERSONALES </i>', 'T', 'LS', true, false, '', '', true, 0, false,false, $ht,'T');
//    $pdf->MultiCell(140, 5, 'PERIODO 2019-2020 ', 'T', 'R', true, true, '', '', true, 0, false,false, $ht,'T');
    
    
    // writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)
    $pdf->writeHTMLCell(155,7,'','','<i><b>TARJETA DE OBJETIVOS PERSONALES </b></i>','T',0,1,0,'L');
    $pdf->writeHTMLCell(105,7,'','',"<i><b>PERIODO ".$top['periodo']."  </b></i>",'T',0,1,0,'R');
    
    $pdf->Ln(10);
    $x=25;
    $pdf->SetX($x);
    $pdf->SetFont('helvetica','', '10');
    
     $h=5;
     
     $col1=90;
     $col1_1=18;
     $col1_2=$col1-$col1_1;
     
     $col2=85;
     $col2_1=25;
     $col2_2=$col2-$col2_1;
     
     
     $col3=$col2;
     $col3_1=25;
     $col3_2=$col3-$col3_1;
     
    // MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0,$valign, $fitcell)
    $pdf->SetFillColor(0,0,0);
    $pdf->SetTextColor($r=255,$g=217,$b=102);
     $pdf->SetFont('helvetica','', 8);
    $pdf->MultiCell($col1_1,$h,'<i><b>NOMBRE:</b></i>'  ,'L','L',1,0,'','',true,0,true,true,$h);
    $pdf->SetFont('helvetica','', 10);
    $pdf->MultiCell($col1_2,$h,$top['usuario']          ,'R','L',1,0,'','',true,0,false,true,$h,'M');
//    $pdf->MultiCell($col1_2,$h,$top['usuario'],'C',0,0,0,'C');
    
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica','', 8);
    $pdf->MultiCell($col2_1,$h,'<i><b>SUPERVISOR:</b></i>'  ,'L','L',0,0,'','',true,0,true,true,$h);
    $pdf->SetFont('helvetica','', 10);
    $pdf->MultiCell($col2_2,$h,$top['supervisor']          ,'R','L',0,0,'','',true,0,false,true,$h,'M');
//    $pdf->writeHTMLCell($col2_1,$h,'','','<i><b>SUPERVISOR:</b></i>','LR',0,0,0,'L');
//    $pdf->writeHTMLCell($col2_2,$h,'','',$top['supervisor'],'B',0,0,0,'C');
    
    $pdf->SetFont('helvetica','', 8);
    $pdf->MultiCell($col3_1,$h,'<i><b>APROBADOR:</b></i>'  ,'L','L',0,0,'','',true,0,true,true,$h);
    $pdf->SetFont('helvetica','', 10);
    $pdf->MultiCell($col3_2,$h,$top['aprobador']          ,'R','L',0,1,'','',true,0,false,true,$h,'M');
    $pdf->SetX($x);
//    $pdf->writeHTMLCell($col3_1,$h,'','','<i><b>APROBADOR:</b></i>',0,0,0,0,'L');
//    $pdf->writeHTMLCell($col3_2,$h,'','',$top['aprobador'],'B',0,1,0,'C');
    
    $pdf->SetTextColor(175, 175, 175);
    $pdf->SetFont('helvetica','', 8);
    $pdf->MultiCell($col1_1,$h,'','LB','L',1,0,'','',true,0,true,true,$h);
    $pdf->MultiCell($col1_2,$h,$top['puesto_u']  ,'RB','L',1,0,'','',false,0,false,true,$h,'',true);
    
    $pdf->SetTextColor(175, 175, 175);
    $pdf->MultiCell($col2_1,$h,'','LB','L',0,0,'','',true,0,true,true,$h);
    $pdf->MultiCell($col2_2,$h,$top['puesto_s']  ,'RB','L',0,0,'','',true,0,false,true,$h,'',true);
    
    $pdf->MultiCell($col3_1,$h,'' ,'LB','L',0,0,'','',true,0,true,true,$h);
    $pdf->MultiCell($col3_2,$h,$top['puesto_a']  ,'RB','L',0,1,'','',true,0,false,true,$h,'',true);
    $pdf->SetTextColor(0, 0, 0);
//    $pdf->writeHTMLCell($col1,$h,'','',$top['puesto'],'LRB',0,0,0,'C');
//    $pdf->writeHTMLCell($col2,$h,'','',$top['puesto'],'LRB',0,0,0,'C');
//    $pdf->writeHTMLCell($col3,$h,'','',$top['puesto'],'LRB',0,0,0,'C');
    
    
//    $pdf->Image($logo, $xLogo, $yLogo, 34, 26   , ''      , ''      , ''       , false        , 300     , ''        , false       , false         , 0         , false        , false        , false);

?>
