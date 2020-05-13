<?php
//datos
    $col1=105;
    $col2=40;
    $col3=40;
    $h1=8;
    $h2=6;
    $h3=0.5;
    $tipo_texto='helvetica';
    $texto_label=8;
    $texto_dato=10;
    
    
//    $pdf->SetXY($xIniCuerpo, 80);
    $pdf->SetFillColor($f=225,$f,$f);
    $pdf->SetFont($tipo_texto,'B', 11);
    $pdf->Write($h1+5, 'Competencias a Evaluar','',false,'C',true);
    $pdf->SetFont($tipo_texto,'', 10);
    $pdf->MultiCell($col1, $h1,  'Competencia', 0, 'C', true, 0, '', '',true, 0, false,false, $h1,'M');
    $pdf->SetFont($tipo_texto,'', 8);
    $pdf->MultiCell($col2, $h1,  'Valor Autoevaluado', 0, 'C', true, 0, '', '',true, 0, false,false, $h1,'M');
    $pdf->MultiCell($col3, $h1,  'Valor Supervisor', 0, 'C', true, 1, '', '',true, 0, false,false, $h1,'M');
    $n=1;
    if(is_array($ed['competencias']))
        foreach ($ed['competencias'] as $competencia) 
        {
            $pdf->SetFont($tipo_texto,'B', 9);
            $pdf->MultiCell(7, $h1,$n, 0, 'L', false, 0, '', '',true, 0, false,false, $h1,'M',false);
//            $pdf->SetFont($tipo_texto,'B', 9);
            $pdf->MultiCell($col1+$col2, $h1,$competencia['competencia'], 0, 'L', false, 1, '', '',true, 0, false,false, $h1,'M',false);
            $nn=1;

            if(is_array($competencia['subcompetencia']))
                foreach($competencia['subcompetencia'] as $subcompetencia)
                {
                    $line=array('B' => array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 2, 'color' => array(0, 0, 0)));
                    $pdf->SetFont($tipo_texto,'', 6);
                    $pdf->MultiCell(5,$h1,'', 0, 'L', false, 0, $pdf->getX(), $pdf->getY(),true, 0, false,false, $h1,'T');
                    $pdf->MultiCell(7,$h1,$n.".".$nn, 0, 'L', false, 0, '', '',true, 0, false,false, $h1,'T');
                    $pdf->SetFont($tipo_texto,'', 9);
                    $pdf->MultiCell($col1-11, $h2,$subcompetencia['subcompetencia'], $line, 'L', false, 0, '', '',true, 0, false,false, 0,'T',false);
                    if ($competencia['tipo'] == 1)
                    {
                        $pdf->SetFont($tipo_texto,'', '7');
                        $pdf->MultiCell($col2, $h2, $subcompetencia['valor_u'] , $line, 'C', false, 0, '', '',false, 0, false,false, 0,'T',false);
                        $pdf->MultiCell($col3, $h2, $subcompetencia['valor_s'] , $line, 'C', false, 1, '', '',false, 0, false,false, 0,'T',false);
                        $pdf->Ln(1);
                        $nn++;
                    }
                    else
                    {
                        $pdf->SetFont($tipo_texto,'', '7');
                        $pdf->MultiCell($col2, $h2, '-.-' , $line, 'C', false, 0, '', '',false, 0, false,false, 0,'T',false);
                        $pdf->MultiCell($col3, $h2, $subcompetencia['valor_sc'] , $line, 'C', false, 1, '', '',false, 0, false,false, 0,'T',false);
                        $pdf->Ln(1);
                        $nn++;
                    }
                }
            $pdf->Ln(1);
            $n++;
        }
    $pdf->Ln(1);
    
?>
