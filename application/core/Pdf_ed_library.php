<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


require_once dirname(__FILE__) . '/tcpdf/tcpdf.php';

class Pdf_ed_library extends TCPDF 
{
    private $ed;
    
    function __construct()
    {
        parent::__construct();
    }
    //Redefino los métodos de ->Page header
    public function header() 
    {
        $col1=41;
        $col2 = 82;
        $col3=27;
        $col4=35; 
        $h1 = 10;
        $h2 = 8;
        $h3 = 9;
        $ht=$h1+$h2+$h3;
        $this->ed = $this->header_user_data;
        $this->SetXY(12, 10);
        $this->SetFont('helvetica','', '13');
        $xLogo=$this->GetX()+3;
        $yLogo=$this->GetY()+10;
        $this->MultiCell($col1, $ht,  '', 1, 'L', false, 0, '', '', true, 0, false,false, $ht,'M');
        $this->MultiCell($col2, $ht, "Evaluación del Desempeño", 1, 'C', 0, 0, '', '', true, 0, false,false, $ht,'M');
        $this->SetFont('helvetica', '', '10');
        $this->SetFillColor($f=225,$f,$f);
        $xSig=$this->GetX();
        $this->MultiCell($col3, $h1, ' Período', 1, 'L', true, 0, '', '', true, 0, false,false, $h1,'M');
        $this->SetFont('helvetica', '', '10');
        $this->MultiCell($col4, $h1, $this->ed ['cabecera']['periodo'], 1, 'C', false, 1, '', '', true,0,false,false,$h1,'M',true);
        $this->MultiCell($col3, $h2, ' Fecha cierre', 1, 'L', true, 0, $xSig, '', true, 0, false,false, $h2,'M');
        $this->MultiCell($col4, $h2, $this->ed['cabecera']['fecha_cierre_u'], 1, 'C', false, 1, '', '', true,1,false,false,$h2,'M');
        $this->MultiCell($col3, $h3, ' Página', 1, 'L', true, 0, $xSig, '', true, 0, false,false, $h3,'M');
        $paginas=(string)$this->getAliasNumPage()." de ".(string)$this->getAliasNbPages();
//        $paginas="1 de 2";
//        $paginas=str_replace(" ","",$paginas);
//        $paginas=str_replace("de"," de ",$paginas);
//        $paginas=preg_replace('/[^\p{L}\s]/u','', $paginas);
//        $paginas=  substr($paginas, 1, 10);
        $this->MultiCell($col4, $h3, $paginas, 1, 'C', false, 1, '', '', true, 0, false,false, $h3,'M');
        $this->Image($this->header_logo, 13, 19, 36, 10, '', '', '', false, 300, '', false, false, 0, false, false, false);
        
        if($this->ed['marca'] != '' || $this->ed['cabecera']['cierre_u'] == 0)
        {
            $this->ed['marca'] = 'Documento Borrador';
            $this->SetAlpha(0.5);
            $this->StartTransform();
            $this->Rotate(45, 70, 110);
            $this->SetFont('helvetica', '', '20');
            $this->SetXY(72, 5);
            $this->SetFillColor(255,0,0);
            $this->MultiCell(50, 10,$this->ed['marca'], 1, 'C', true, 0, '', '', true, 0, false,false, 0,'M');//$this->ed ['cabecera']['marca']
            $this->SetXY(65, 7);
            $this->StopTransform();
            $this->SetAlpha(1);
        }
    }

    // Page footer
    public function Footer() {
//        $ed = $this->header_user_data;
        // Position at 15 mm from bottom
        $this->SetXY(12, -15);
        $this->SetFillColor($f=225,$f,$f);
        $colPie=92;
        $hPie=8;
        // Set font
        $this->SetFont('helvetica', '', '9');
            $styleBarcode = array(
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
            
            //    $this->MultiCell($w, $h, $txt, $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $this->MultiCell($colPie, $hPie, 'Usuario: '.$this->ed['cabecera']['usuario']       , 1, 'C', true, 0, '', '', true, 0, false,false, $hPie,'M');
            $this->MultiCell($colPie, $hPie, 'Supervisor: '.$this->ed['cabecera']['supervisor'] , 1, 'C', true, 1, '', '', true, 0, false,false, $hPie,'M');
            $yBarcode=$this->getY()-2;
            $xBarcode=12;
            $this->setX(140);
            $this->SetFont('helvetica', '', '6');
            $this->Write(0, 'Impreso: '.date("d-m-Y  G:i")."hrs, por ".$this->ed['impresa_por'], '', 0, 'R', false, 0, false, false, 2);
//            $pdf->write1DBarcode($barcode                                       , 'I25+', $xIniPie, $yBarcode,$colPie, 8, 0.4, $styleBarcode, 'N');
            $this->write1DBarcode(6000000+$this->ed['cabecera']['id_evaluacion'], 'I25+', $xBarcode,$yBarcode,$colPie, 8, 0.4, $styleBarcode, 'N');
            
//            $this->write1DBarcode($code,      $type, $x       ,$y,          $w, $h, $xres, $style,   $align);
            
    }
    public function controlMargen(){
        if($this->getY()>200)
            {
                $this->AddPage();
                $margins=$this->getMargins();
                $this->SetXY($margins['left'], $margins['top']);
            }
    }
    
}

/* End of file Pdf.php */
/* Location: ./application/libraries/pdf.php */  
?>