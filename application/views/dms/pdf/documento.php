<?php
    $pdf->SetCreator('SMC - DMS');
    $pdf->SetAuthor('autor');
    $pdf->SetTitle($titulo);
    $pdf->SetSubject('Documento');
    $pdf->SetKeywords("PDF, DMS, $codigo, $vigencia");
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
    
    for ($i=1;$i<=$paginas;$i++)
    {
        $tplIdx = $pdf->importPage($i, '/MediaBox');
//        $tplIdx = $pdf->importPage($i, '/BleedBox');
//        $tplIdx = $pdf->importPage($i, '/TrimBox');
//        $tplIdx = $pdf->importPage($i, '/CropBox');
//        $tplIdx = $pdf->importPage($i, '/ArtBox');
        $pdf->addPage();
        $wh=$pdf->getTemplateSize($tplIdx,0,0);
        
        if($wh['w']>220)
    {
        $pdf->setPageOrientation('L');
        $variables['xIniEnc']=$xIniEnc+35;
        $variables['xIniPie']=$xIniPie+35;
        $variables['yIniPie']=$yIniPie-87;
        $variables['xIniCuerpo']=$xIniCuerpo+10;
        $variables['yIniCuerpo']=$yIniCuerpo-8;
        $variables['hCuerpo']=$hCuerpo-65;
        $variables['col2']=$col2+20;
        $variables['colPie']= ($variables['col1']+ $variables['col2']+ $variables['col3']+$variables['col4'])/3;
        $variables['col12']= $variables['col1']+ $variables['col2']+ $variables['xIniEnc'];
    }
    else
    {
        $pdf->setPageOrientation('P');
        $variables['xIniEnc']=$xIniEnc;
        $variables['xIniPie']=$xIniPie;
        $variables['yIniPie']=$yIniPie;
        $variables['xIniCuerpo']=$xIniCuerpo;
        $variables['yIniCuerpo']=$yIniCuerpo;
        $variables['hCuerpo']=$hCuerpo;
        $variables['col2']=$col2;
        $variables['colPie']= ($variables['col1']+ $variables['col2']+ $variables['col3']+$variables['col4'])/3;
        $variables['col12']= $variables['col1']+ $variables['col2']+ $variables['xIniEnc'];
        
    }
        
        $pdf->useTemplate($tplIdx,$variables['xIniCuerpo'],$variables['yIniCuerpo'],$w=0,$variables['hCuerpo']);
        
        $variables['i']=$i;
        $variables['wh']=$wh;
        $this->view('dms/pdf/encabezado',$variables);
        $this->view('dms/pdf/pie',$variables);
    }
     $js = <<<EOD
    print();
EOD;

// Add Javascript code
    if($print)
        $pdf->IncludeJS($js);      
    $pdf->Output($nomArchivoDMS,$output);
?>
