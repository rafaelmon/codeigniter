<?php
    $titulo="Evaluación del Desempeño";
    $periodo="2016-01";
    $usuario="Juan Perez";
    $nomArchivoED="ED".$ed['cabecera']['id_evaluacion'];
    
    $pdf->SetCreator('SMC - ED');
    $pdf->SetTitle($titulo);
    $pdf->SetSubject("Proceso de Evaluación - Período->$periodo - Usuario->$usuario");
    $pdf->SetKeywords("PDF, ED");
    $pdf->SetHeaderData($logo,PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING,$ed);
    $pdf->setPrintHeader(true);
    $pdf->setPrintFooter(true);
    $pdf->SetMargins(12, 40, 12);
    $pdf->SetHeaderMargin(15);
    $pdf->SetFooterMargin(15);
    // set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, 35);
    $variables['ed'] = $ed;
    $variables['titulo'] = $titulo;
    $variables['periodo'] = $periodo;
    $variables['fecha_cierre'] = "03/07/2016";
    $variables['paginas'] = "1";
    
    $variables['marginT'] = 10;
    $variables['marginT'] = 10;
    $variables['marginL'] = 12;
    $variables['marginR'] = 0;
    $variables['yIniEnc'] = 10;
    $variables['col1']=41;
    $variables['col2'] = 82;
    $variables['col3']=27;
    $variables['col4']=35;
    $variables['col5'] = 47;
    $variables['col6'] = 10;
    $variables['col7'] = 82;
    $variables['h1'] = 10;
    $variables['h2'] = 8;
    $variables['h3'] = 9;
    $variables['h4'] = 5;
    $variables['hPie'] =12;
    $variables['ht'] = $variables['h1']+ $variables['h2']+ $variables['h3'];
    
    $xIniEnc=12;
    $xIniCuerpo=12;
    $xIniPie=12;
    $yIniPie=274;
    $yIniCuerpo=40;
    $hCuerpo=283;
//    $col2=82;
    
    
    
    

//    for ($i=1;$i<=$paginas;$i++)
//    {
//        $tplIdx = $pdf->importPage($i, '/MediaBox');
//        $tplIdx = $pdf->importPage($i, '/BleedBox');
//        $tplIdx = $pdf->importPage($i, '/TrimBox');
//        $tplIdx = $pdf->importPage($i, '/CropBox');
//        $tplIdx = $pdf->importPage($i, '/ArtBox');
        $pdf->addPage();
//        $wh=$pdf->getTemplateSize($tplIdx,0,0);
        
        $pdf->setPageOrientation('P');
        $variables['xIniEnc']=$xIniEnc;
        $variables['$xIniCuerpo']=$xIniEnc;
        $variables['xIniPie']=$xIniPie;
        $variables['yIniPie']=$yIniPie;
        $variables['xIniCuerpo']=$xIniCuerpo;
        $variables['yIniCuerpo']=$yIniCuerpo;
        $variables['hCuerpo']=$hCuerpo;
        $variables['colPie']= ($variables['col1']+ $variables['col2']+ $variables['col3']+$variables['col4'])/3;
        $variables['col12']= $variables['col1']+ $variables['col2']+ $variables['xIniEnc'];
//        
//    }
        
//        $pdf->useTemplate($tplIdx,$variables['xIniCuerpo'],$variables['yIniCuerpo'],$w=0,$variables['hCuerpo']);
        
//        $variables['i']=$i;
//        $variables['wh']=$wh;
        $this->view('ed/pdf/usuarios',$variables);
        $this->view('ed/pdf/cuerpo',$variables);
        $this->view('ed/pdf/cumplimiento',$variables);
        $this->view('ed/pdf/fortalezas',$variables);
        $this->view('ed/pdf/aam',$variables);
        $this->view('ed/pdf/pm',$variables);
        $this->view('ed/pdf/metas',$variables);
        $this->view('ed/pdf/comentario',$variables);
        $this->view('ed/pdf/firmas',$variables);
        
//        $pdf->setPage(1);
//        $this->view('ed/pdf/encabezado',$variables);
//        $this->view('ed/pdf/pie',$variables);
//        die();
//    }
$js = <<<EOD
print();
EOD;

// Add Javascript code
//    if($print)
//        $pdf->IncludeJS($js);      
    $pdf->Output($nomArchivoED,$output);
?>
