<?php
    $pdf->SetCreator('SMC - DDP');
    $pdf->SetAuthor('autor');
    $pdf->SetTitle($titulo);
    $pdf->SetSubject('Documento');
    $pdf->SetKeywords("PDF, DMS, TOPs, 2019");
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetHeaderMargin(5);
    $pdf->SetFooterMargin(5);
    // set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, 5);

    // set image scale factor
    $pdf->setImageScale(1.25);
    
    
    
    
    $variables['marginT']=10;
    $variables['marginL']=0;
    $variables['marginR']=0;
    $variables['yIniEnc']=10;
    $variables['col1']=41;
    $variables['col3']=27;
    $variables['col4']=35;
    $variables['h1']=9;
    $variables['h2']=12;
    $variables['h3']=9;
    $variables['hPie']=12;
    $variables['ht']= $variables['h1']+ $variables['h2']+ $variables['h3'];
    
    $xIniEnc=12;
    $xIniPie=12;
    $yIniPie=274;
    $xIniCuerpo=0;
    $yIniCuerpo=18;
    $hCuerpo=283;
    $col2=82;
    
    
    
    $variables['styleBarcode'] = array(
                'position' => '',
                'align' => 'R',
                'stretch' => true,
                'fitwidth' => true,
                'cellfitalign' => '',
                'border' => false,
                'hpadding' => 'auto',
                'vpadding' => 'auto',
                'fgcolor' => array(0,0,0),
                'bgcolor' => false, //array(255,255,255),
                'text' => true,
                'font' => 'helvetica',
                'fontsize' => 4,
                'stretchtext' => 4
            );

    $pdf->SetMargins($variables['marginL'], $variables['marginT'], $variables['marginR']);
    
//    for ($i=1;$i<=$paginas;$i++)
//    {
//        $tplIdx = $pdf->importPage($i, '/MediaBox');
////        $tplIdx = $pdf->importPage($i, '/BleedBox');
////        $tplIdx = $pdf->importPage($i, '/TrimBox');
////        $tplIdx = $pdf->importPage($i, '/CropBox');
////        $tplIdx = $pdf->importPage($i, '/ArtBox');
//        $pdf->addPage();
//        $wh=$pdf->getTemplateSize($tplIdx,0,0);
//        
//        if($wh['w']>220)
//    {
        $pdf->addPage();
        $pdf->setPageOrientation('L');
        $variables['xIniEnc']=$xIniEnc+35;
        
//    }
//    else
//    {
//        $pdf->setPageOrientation('P');
//        $variables['xIniEnc']=$xIniEnc;
//        $variables['xIniPie']=$xIniPie;
//        $variables['yIniPie']=$yIniPie;
//        $variables['xIniCuerpo']=$xIniCuerpo;
//        $variables['yIniCuerpo']=$yIniCuerpo;
//        $variables['hCuerpo']=$hCuerpo;
//        $variables['col2']=$col2;
//        $variables['colPie']= ($variables['col1']+ $variables['col2']+ $variables['col3']+$variables['col4'])/3;
//        $variables['col12']= $variables['col1']+ $variables['col2']+ $variables['xIniEnc'];
//        
//    }
        
//        $pdf->useTemplate($tplIdx,$variables['xIniCuerpo'],$variables['yIniCuerpo'],$w=0,$variables['hCuerpo']);
        
//        $variables['i']=$i;
//        $variables['wh']=$wh;
        $datos['top']=$top;
        $this->view('ddp/pdf/top/encabezado',$datos);
//        $this->view('ddp/pdf/top/pie',$variables);
        
        
        
    $x=25;
    $y=35;
    $pdf->SetXY($x, $y);
     $h1=10;
     $h2=25;
     $col1=140;
     $col2=30;
     $col3=90;
     $n=1;
     
     if (is_array($objs))
     {
        foreach ($objs as $objetivo)
        {
            $obj_txt=$objetivo['obj'];
            $fecha=$objetivo['fecha_evaluacion'];
            $comentarios="<b>Fuente de datos:</b> ".$objetivo['fd'] . " - <b>Valor de referencia:</b> " . $objetivo['valor_ref'] . " - <b>Peso:</b> " . $objetivo['peso'] . " - <b>Peso real:</b> " . $objetivo['real1'];

            switch ($objetivo['id_dimension']) {
                case 1:
                       $dimension="OBJETIVO PERSONAL $n";
                       $pdf->SetFillColor($r=226,$g=239,$b=217);
                       break;
                case 2:
                       $pdf->AddPage();
                       $this->view('ddp/pdf/top/encabezado',$datos);
                       $dimension="OBJETIVO PERSONAL HABILIDADES";
                       $pdf->SetFillColor($r=189,$g=214,$b=238);
                       $y=50;
                       $pdf->SetXY($x, $y);
                    break;
                case 3:
                    $dimension="OBJETIVO ORGANIZACIONAL";
                    $pdf->SetFillColor($r=242,$g=198,$b=186);
                    break;

                default:
                    $dimension="OBJETIVO ORGANIZACIONAL";
                    break;
            }

           $pdf->SetFont('helvetica','', '13');
          // writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)
          $pdf->writeHTMLCell($col1,$h1,$x,'','<i><b>'.$dimension.'</b></i>',1,0,1,1,'L');
           $pdf->SetFont('helvetica','', '11');
          $pdf->writeHTMLCell($col2,$h1,'','','<i><b>FECHA REVISION</b></i>',1,0,1,1,'C');
           $pdf->SetFont('helvetica','', '13');
          $pdf->writeHTMLCell($col3,$h1,'','','<i><b>COMENTARIOS</b></i>',1,1,1,1,'C');

          $pdf->SetFont('helvetica','', '11');
          $pdf->writeHTMLCell($col1,$h2,$x,'',$obj_txt,1,0,0,1,'L');
          $pdf->writeHTMLCell($col2,$h2,'','',$fecha,1,0,0,1,'C');
          $pdf->writeHTMLCell($col3,$h2,'','',$comentarios,1,1,0,1,'L');    
          $pdf->Ln(4);

          $n++;

        }
     } else {
        $obj_txt='';
        $fecha='  /    /';
        $comentarios='';
        $dimension="OBJETIVO...";
        $pdf->SetFont('helvetica','', '13');
        $pdf->SetFillColor($r=226,$g=239,$b=217);
       // writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)
       $pdf->writeHTMLCell($col1,$h1,$x,'','<i><b>'.$dimension.'</b></i>',1,0,1,1,'L');
        $pdf->SetFont('helvetica','', '11');
       $pdf->writeHTMLCell($col2,$h1,'','','<i><b>FECHA REVISION</b></i>',1,0,1,1,'C');
        $pdf->SetFont('helvetica','', '13');
       $pdf->writeHTMLCell($col3,$h1,'','','<i><b>COMENTARIOS</b></i>',1,1,1,1,'C');

       $pdf->SetFont('helvetica','', '11');
       $pdf->writeHTMLCell($col1,$h2,$x,'',$obj_txt,1,0,0,1,'L');
       $pdf->writeHTMLCell($col2,$h2,'','',$fecha,1,0,0,1,'C');
       $pdf->writeHTMLCell($col3,$h2,'','',$comentarios,1,1,0,1,'L');    
       $pdf->Ln(4);
     }
     
     
     
        
        
//    }
     $js = <<<EOD
    print();
EOD;

// Add Javascript code
    if($print)
        $pdf->IncludeJS($js);      
    $pdf->Output($nomArchivoPDF,$output);
?>
