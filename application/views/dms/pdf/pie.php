<?php

            $pdf->SetXY($xIniPie,$yIniPie);
            $pdf->SetFont('helvetica', '', '9');
            //    $pdf->MultiCell($w, $h, $txt, $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($colPie, $hPie, 'Editó: '.$edito, 1, 'C', true, 0, '', '', true, 0, false,false, $hPie,'M');
            $pdf->MultiCell($colPie, $hPie, 'Revisó: '.$reviso, 1, 'C', true, 0, '', '', true, 0, false,false, $hPie,'M');
            $pdf->MultiCell($colPie, $hPie, 'Aprobó: '.$aprobo, 1, 'C', true, 1, '', '', true, 0, false,false, $hPie,'M');
            $yBarcode=$pdf->getY()-2;
            
            $pdf->SetFont('helvetica', '', '6');
            $pdf->SetX($xIniPie+($colPie * 2)+25);
//          $pdf->Write($h, $txt, $link, $fill, $align, $ln, $stretch, $firstline, $firstblock, $maxh, $wadj, $margin);
            $pdf->Write(0, 'Impreso el: '.date("d-m-Y  G:i")."hr", '', 0, 'L', false, 0, false, false, 2);
            
//            $pdf->write1DBarcode($code,      $type, $x       ,$y,          $w, $h, $xres, $style,   $align);
            $pdf->write1DBarcode($barcode, 'I25+', $xIniPie, $yBarcode,$colPie, 8, 0.4, $styleBarcode, 'N');
            
            //Marca de Agua
//                $pdf->Image($marcaAgua, 120, 105, '', '', '', '', '', false, 300);
//                $pdf->Rotate($angle, $x, $y)
//                $pdf->Text($x, $y, $txt, $fstroke, $fclip, $ffill, $border, $ln, $align, $fill, $link, $stretch)
//                $pdf->MultiCell($w, $h, $txt, $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);

                
                switch ($estado)
                {
                    case 1:
                    case 2:
                        $pdf->SetAlpha(0.3);
                        $pdf->StartTransform();
                        $pdf->Rotate(45, 70, 110);
                        $pdf->SetFont('helvetica', '', '20');
                        $pdf->SetXY(72, 5);
                        $pdf->MultiCell(50, 10,'Vista Preliminar', 1, 'C', 0, 0, '', '', true, 0, false,false, $ht,'M');
                        $pdf->SetXY(70, 7);
                        $pdf->StopTransform();
                        $pdf->SetFont('helveticaB', '', '12');
                         $pdf->SetFillColor(255,128,36);
                         $pdf->SetTextColor(0,0,0);
                        $pdf->MultiCell(60, 4,'Documento en borrador', 0, 'C', 1, 0, '', '', true, 0);
                        break;
                    case 3:
                        $pdf->SetAlpha(0.3);
                        $pdf->StartTransform();
                        $pdf->Rotate(45, 70, 110);
                        $pdf->SetFont('helvetica', '', '20');
                        $pdf->SetXY(72, 5);
                        $pdf->MultiCell(50, 10,'Vista Preliminar', 1, 'C', 0, 0, '', '', true, 0, false,false, $ht,'M');
                        $pdf->SetXY(65, 7);
                        $pdf->StopTransform();
                        $pdf->SetFont('helveticaB', '', '12');
                        $pdf->SetFillColor(255,128,36);
                        $pdf->SetTextColor(0,0,0);
                        $pdf->MultiCell(60, 4,'Documento en revisión', 0, 'C', 1, 0, '', '', true, 0);
                        break;
                    case 4:
                        $pdf->SetAlpha(0.3);
                        $pdf->StartTransform();
                        $pdf->Rotate(45, 70, 110);
                        $pdf->SetFont('helvetica', '', '20');
                        $pdf->SetXY(72, 5);
                        $pdf->MultiCell(50, 10,'Vista Preliminar', 1, 'C', 0, 0, '', '', true, 0, false,false, $ht,'M');
                        $pdf->SetXY(65, 7);
                        $pdf->StopTransform();
                        $pdf->SetFont('helveticaB', '', '12');
                        $pdf->SetFillColor(255,128,36);
                        $pdf->SetTextColor(0,0,0);
                        $pdf->MultiCell(60, 4,'Documento en aprobación', 0, 'C', 1, 0, '', '', true, 0);
                        break;
                    case 5:
                        $pdf->SetAlpha(0.3);
                        $pdf->StartTransform();
                        $pdf->Rotate(45, 70, 110);
                        $pdf->SetFont('helvetica', '', '20');
                        $pdf->SetXY(72, 5);
                        $pdf->MultiCell(50, 10,'Vista Preliminar', 1, 'C', 0, 0, '', '', true, 0, false,false, $ht,'M');
                        $pdf->SetXY(65, 7);
                        $pdf->StopTransform();
                        $pdf->SetFont('helveticaB', '', '12');
                        $pdf->SetFillColor(255,128,36);
                        $pdf->SetTextColor(0,0,0);
                        $pdf->MultiCell(60, 4,'Documento a publicar', 0, 'C', 1, 0, '', '', true, 0);
                        break;
                    case 6:
                        break;
                    case 7:
                        $pdf->SetAlpha(0.7);
                        $pdf->StartTransform();
                        $pdf->Rotate(45, 70, 110);
                        $pdf->SetFont('helvetica', '', '20');
                        $pdf->SetXY(72, 5);
                        $pdf->SetTextColor(255);
                        $pdf->SetFillColor(255,0,0);
                        $pdf->MultiCell(50, 10,'Documento Obsoleto', 0, 'C', 1, 0, '', '', true, 0, false,false, $ht,'M');
                        $pdf->StopTransform();
                        $pdf->SetXY($col12+30, 20);
                        $pdf->SetFont('helveticaB', '', '20');
                        $pdf->SetAlpha(0.6);
                        $pdf->SetTextColor(255,0,0);
                        $pdf->MultiCell(30, 10,str_repeat('/',14), 0, 'C', 0, 0);
                        $pdf->SetXY($xIniEnc+55, 5);
                        $pdf->SetFont('helveticaB', '', '12');
//                        $pdf->MultiCell(60, 10,'Documento Obsoleto', 0, 'C', 1, 0, '', '', true, 0, false,false, 10,'M');
                        break;
            }

?>
