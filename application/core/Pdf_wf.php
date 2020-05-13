<?php

require_once dirname(__FILE__) . '/tcpdf/tcpdf.php';
require_once dirname(__FILE__) . '/fpdi/fpdi.php';

class Pdf_wf extends FPDI 
{
    function __construct()
    {
        parent::__construct();
    }
    
    
}

?>