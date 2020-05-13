<?php
class Admin_tops extends CI_Controller
{
    private $modulo=33;
    private $user;
    private $permisos;
    
        function __construct()
	{
            parent::__construct();
            header ("Expires: Thu, 31 Dic 1976 23:59:00 GMT"); //la pagina expira en una fecha pasada
            header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); //ultima actualizacion ahora cuando la cargamos
            header ("Cache-Control: no-cache, must-revalidate"); //no guardar en CACHE
            header ("Pragma: no-cache");
            $this->user=$this->session->userdata(USER_DATA_SESSION);
            
            if (!($this->user['id']>0)) 
                redirect("admin/admin/index_js","location", 301);
            $this->load->model("permisos_model","permisos_model",true);
            $this->permisos= $this->permisos_model->checkIn($this->user['perfil_id'],$this->modulo);
            
//            $this->load->model('ddp/ddp_tops_model','ddp_tops',true);
//            $id_periodo=1;
//            $this->user['id_top']=$this->ddp_tops->dameIdTop($this->user['id'],$id_periodo);
//            echo "<pre>".print_r($this->user,true)."</pre>";
	}
	
	public function index()
	{
            $modulo = $this->uri->segment(SEGMENTOS_DOM+2);
            if (!isset($modulo)) $modulo=0;
            $this->load->model('permisos_model','permisos_model',true);
            $variables['permiso'] = $this->permisos_model->checkIn($this->user['perfil_id'],$modulo);
//             $this->load->model('ddp/ddp_tops_model','ddp_tops',true);
//             echo "<pre>".print_r($variables,true)."</pre>";
            $this->load->model('ddp/ddp_periodos_model','ddp_periodos',true);
            $variables['periodo']=$this->ddp_periodos->damePeriodoActivo();
//            echo "<pre>".print_r($variables,true)."</pre>";
            $this->load->view('ddp/admin/tops/listado',$variables);
	}
	public function top_usuario()
	{
            $modulo = $this->uri->segment(SEGMENTOS_DOM+2);
            $id_top = $this->input->post("id");
            $this->load->model('ddp/ddp_tops_model','ddp_tops',true);
            $usuario=$this->ddp_tops->dameUsuarioTop($id_top);
            
            if (!isset($modulo)) $modulo=0;
            $this->load->model('permisos_model','permisos_model',true);
            $this->load->model('ddp/ddp_tops_model','ddp_tops',true);
            $variables['permiso'] = $this->permisos_model->checkIn($this->user['perfil_id'],$modulo);
            $top=$this->ddp_tops->dameTopActivaDatosPanelAdmin($id_top);
            $variables['id_top'] = $id_top;
            $variables['usuario'] = $usuario['nomape'];
            $variables['top'] = $top;
//             $this->load->model('ddp/ddp_tops_model','ddp_tops',true);
//            echo "<pre>".print_r($top,true)."</pre>";
//            die();
            
            $this->load->view('ddp/admin/tops/top/listado',$variables);
	}
	
	public function listado()
	{
            if ($this->permisos['Listar'])
            {
                $id_usuario=$this->user['id'];
                $this->load->model('usuarios_model','usuarios',true);
                $usuemp=$this->usuarios->dameUsuarioEmpresaYGR($id_usuario);
                $id_empresa = $usuemp['id_empresa'];
                $id_periodo = ($this->input->post("filtro_id_periodo"))?$this->input->post("filtro_id_periodo"):-1;

                if($id_periodo!=-1)
                {
                    $this->load->model('ddp/ddp_periodos_model','ddp_periodos',true);
                    $periodo=$this->ddp_periodos->damePeriodo($id_periodo);
                }
                else
                    $periodo=-1;

                $listado=array();
                $this->load->model('ddp/ddp_tops_model','ddp_tops',true);
                $start = $this->input->post("start");
                $limit = $this->input->post("limit");
                $sort = $this->input->post("sort");
                $dir = $this->input->post("dir");
                if($this->input->post("filtros"))
                {
                    $filtros=json_decode($this->input->post("filtros"));
                    foreach ($filtros as &$filtro)
                    {
                        if ($filtro=='Todas'||$filtro=='Todos'||$filtro=='-1')
                            $filtro="";
                    }
                    unset ($filtro);
//                        echo "<pre>".print_r($filtros,true)."</pre>";

                }
                else
                {
                    $filtros="";

                }
                $busqueda  = ($this->input->post("query"))?$this->input->post("query"):"";
                $campos="";
                if ($this->input->post("fields"))
                {
                    $campos = str_replace('"', "", $this->input->post("fields"));
                    $campos = str_replace('[', "", $campos);
                    $campos = str_replace(']', "", $campos);
                    $campos = explode(",",$campos);
//                            echo "<pre>".print_r($campos,true)."</pre>";
                }
                $listado = $this->ddp_tops->listado_admin($start, $limit, $filtros,$busqueda, $campos, $sort, $dir,$id_usuario,$periodo,$id_empresa);
//                    echo "<pre>".print_r($listado,true)."</pre>";
                    echo $listado;
                }
                else
                    echo -1; //No tiene permisos
                
	}
    public function listado_dimensiones()
    {
            if ($this->permisos['Listar'])
            {
                $id_usuario=$this->input->post("id_usuario");
//                    $id_periodo=$this->input->post("id_periodo");
//                    $this->load->model('ddp/ddp_tops_model','ddp_tops',true);
//                    $id_top=$this->ddp_tops->dameIdTop($id_usuario,$id_periodo);
                $id_top=$this->input->post("top");

                $usuario['id']= $id_usuario;
                $usuario['id_top']=$id_top;

                $listado=array();
                $this->load->model('ddp/ddp_dimensiones_model','ddp_dimensiones',true);
                $sort = $this->input->post("sort");
                $dir = $this->input->post("dir");
                $listado = $this->ddp_dimensiones->listado_top($usuario,$sort, $dir);
                echo $listado;
            }
            else
                echo -1; //No tiene permisos

    }
    public function listado_historial()
    {
            if ($this->permisos['Listar'])
            {
//                    $usuario['id']=$this->user['id'];
                $id_objetivo=$this->input->post("id_obj");
                $listado=array();
                $this->load->model('ddp/ddp_objetivos_historial1_model','ddp_historial',true);
                $sort = $this->input->post("sort");
                $dir = $this->input->post("dir");
                $listado = $this->ddp_historial->listado($id_objetivo);
                echo $listado;
            }
            else
                echo -1; //No tiene permisos

    }
    public function listado_objetivos()
    {
        if ($this->permisos['Listar'])
        {
//                    $id_usuario=$this->input->post("id_usuario");
//                    $id_periodo=$this->input->post("id_periodo");
//                    $id_top=$this->ddp_tops->dameIdTop($id_usuario,$id_periodo);
            $this->load->model('ddp/ddp_tops_model','ddp_tops',true);
            $id_top=$this->input->post("top");

            if ($id_top=="" || $id_top==NULL)
                exit ('({"total":"0","rows":""})');
            $id_dimension=$this->input->post("id_dimension");
            if ($id_dimension>=0)
            {
//                        $datos['id_usuario']=$id_usuario;
                $datos['id_dimension']=$id_dimension;
                $datos['id_top']=$id_top;
                $listado=array();
                $this->load->model('ddp/ddp_objetivos_model','ddp_obj',true);
                $start = $this->input->post("start");
                $limit = $this->input->post("limit");
                $filtros="";
                $busqueda  = ($this->input->post("query"))?$this->input->post("query"):"";
                $campos="";
                if ($this->input->post("fields"))
                {
                    $campos = str_replace('"', "", $this->input->post("fields"));
                    $campos = str_replace('[', "", $campos);
                    $campos = str_replace(']', "", $campos);
                    $campos = explode(",",$campos);
//                            echo "<pre>".print_r($campos,true)."</pre>";
                }
                $sort = $this->input->post("sort");
                $dir = $this->input->post("dir");



                $listado = $this->ddp_obj->listado_usuario($datos,$start, $limit, $filtros, $busqueda, $campos,$sort="", $dir="");
                echo $listado;
            }
            else
                echo '({"total":"0","rows":""})';

        }
        else
            echo -1; //No tiene permisos

    }
    public function periodos_combo()
    {
        $this->load->model('ddp/ddp_periodos_model','ddp_periodos',true);
        $var=$this->ddp_periodos->dameComboPeriodos();
        echo $var;	
    }
    public function fd_combo()
    {
        $id_dimension=$this->input->post("id_dimension");
        $this->load->model('ddp/ddp_fuentes_model','ddp_fuentes',true);
        $var=$this->ddp_fuentes->dameComboFD($id_dimension);
        echo $var;	
    }
    public function dr_combo()
    {
        $id_usuario=$this->user['id'];
        $id_periodo=$this->input->post("id_periodo");
        $this->load->model('usuarios_model','usuarios',true);
        $var=$this->usuarios->dameDrCombo($id_usuario);
        echo $var;	
    }
        //deshabilitada a pedido del usuario
        /*
        public function excel()
        {
            $id_usuario=$this->user['id'];
            $this->load->model('gestion_riesgo/gr_usuarios_model','gr_usuarios',true);
            $usuario=$this->gr_usuarios->dameUsuarioPorId($id_usuario);
            $filename = 'ReporteAdminTops.xls';
            header('Content-Type:application/vnd.ms-excel'); //mime type
            header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name          
            header('Cache-Control: max-age=0'); //no cache
            $this->load->library('excel');


            $this->excel->getProperties()
                ->setCreator("Sistema de Mejora Continua (SMC)") // Nombre del autor
                ->setLastModifiedBy("Sistema de Mejora Continua (SMC)") //Ultimo usuario que lo modificó
                ->setTitle("ReporteAdminTops") // Titulo
                ->setSubject("ReporteAdminTops") //Asunto
                ->setDescription("ReporteAdminTops") //Descripción
                ->setKeywords("ReporteAdminTops") //Etiquetas
                ->setCategory("ReporteAdminTops");
            $getActiveSheet=$this->excel->getActiveSheet();
            
            $startTableColumn="A";
            
            $estiloTituloReporte = array(
                'font' => array(
                'name'      => 'Calibri',
                'bold'      => true,
                'italic'    => false,
                'strike'    => false,
                'size'      =>16,
                'color'     => array(
                    'rgb' => '000000'
                    )
                ),
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                        'rotation' => 0,
                        'wrap' => TRUE
                    )
            );
            $estiloCabeceraGrilla = array(
                'font' => array(
                    'name'      => 'Calibri',
                    'bold'      => True,
                    'italic'    => false,
                    'strike'    => false,
                    'size' =>11,
                    'color'     => array(
                        'rgb' => 'FFFFFF'
                    )
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                    'rotation' => 0,
                    'wrap' => TRUE
                ),
                'fill' => array(
                    'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array(
                        'argb' => 'FFC0504D'
                    )
                ),
            );
            $estiloDatosPar = array(
                'font' => array(
                    'name'      => 'Calibri',
                    'bold'      => True,
                    'italic'    => false,
                    'strike'    => false,
                    'size' =>10,
                    'color'     => array(
                        'rgb' => '000000'
                    )
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                    'rotation' => 0,
                    'wrap' => TRUE
                ),
                'fill' => array(
                    'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array(
                        'argb' => 'FFE5E0EC'
                    )
                ),
            );
            $estiloDatosImpar = array(
                'font' => array(
                    'name'      => 'Calibri',
                    'bold'      => True,
                    'italic'    => false,
                    'strike'    => false,
                    'size' =>10,
                    'color'     => array(
                        'rgb' => '000000'
                    )
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                    'rotation' => 0,
                    'wrap' => TRUE
                ),
                'fill' => array(
                    'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array(
                        'argb' => 'FFDBEEF3'
                    )
                ),
            );
            
            $setActiveSheetIndex=$this->excel->setActiveSheetIndex(0);
            $setActiveSheetIndex->setCellValue('A1',"Estado gestión TOPs");
            $setActiveSheetIndex->mergeCells('A1:J1');
            $this->excel->getActiveSheet()->getStyle('A1')->applyFromArray($estiloTituloReporte);
            $setActiveSheetIndex->setCellValue('A2',"Fecha y hora: ".date('d-m-Y H:i:s'));
            $setActiveSheetIndex->mergeCells('A2:C2');
            $setActiveSheetIndex->setCellValue('A3',"Descargado por: ".$usuario['persona']);
            $setActiveSheetIndex->mergeCells('A3:C3');
            $periodo = 1;

            $this->load->model('reportes/rep_administrar_top_model','reporte',true);
            $tops = $this->reporte->dameTops($periodo);
            if($tops==0)
                exit ('-2');

            $arrayValueLabels=array('idTop', 'Usuario','Periodo','Estado','Puesto','Gerencia','Supervisor','SumObj','SumPeso');    
            $n = 0;
            $startC=0;
            $startR=5;
            $col=$startC;
            $letterCol=chr($startC+65);
            $row=$startR;
            
            //Defino encabezado de tabla
            $getActiveSheet->setCellValueByColumnAndRow($col,$row, '#');
            $getActiveSheet->getStyle($letterCol.$row)->applyFromArray($estiloCabeceraGrilla);
            $col++;
            $letterCol++;
            foreach ($arrayValueLabels as $label)
            {
                $getActiveSheet->setCellValueByColumnAndRow($col,$row, $label);
                $getActiveSheet->getStyle($letterCol.$row)->applyFromArray($estiloCabeceraGrilla);
                $getActiveSheet->getColumnDimension($letterCol)->setAutoSize(true);
                $col++;
                $letterCol++;
            }
            $col=$startC;
            $letterCol=chr($startC+65);
            $row++;
            
            //Completo el rango de datos
            foreach ($tops as $top)
            {
                $getActiveSheet->setCellValueByColumnAndRow($col,$row, $n+1);
                $col++;
                foreach ($arrayValueLabels as $label)
                {
                    $getActiveSheet->setCellValueByColumnAndRow($col,$row, $top[$label]);
                    $col++;
                }
                $col=$startC;
                $row++;
                $n++;
            }
            $this->excel->setActiveSheetIndex()->getRowDimension(1)->setRowHeight(25);

            $getActiveSheet->getPageSetup()->setFitToWidth(1);
            $getActiveSheet->getPageSetup()->setFitToHeight(0);
            $getActiveSheet->setTitle('AdminTops');
            $getActiveSheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
            $getActiveSheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

            //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
            //if you want to save it as .XLSX Excel 2007 format
            $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
            ob_end_clean();
            //force user to download the Excel file without writing it to server's HD
            $objWriter->save('php://output');
        }
       */
    
    public function editar_supervisor()
    {
        if ($this->permisos['Alta'])
        {
            $id_usuario             = $this->user['id'];
            $id_top                 = $this->input->post("id_top");
            $id_supervisor_nuevo    = $this->input->post("id_supervisor");
            $id_accion              = 8;

            $datos['id_usuario']=$id_usuario;

            //busco el usuario supervisor
            $this->load->model('gestion_riesgo/gr_usuarios_model','gr_usuarios',true);
            $this->load->model('ddp/ddp_tops_model','ddp_tops',true);
            $top = $this->ddp_tops->dameTopPorId($id_top);
//            echo "<pre>" . print_r($top, true) . "</pre>";die();
            $datos['id_top_superior']=NULL;
            //creo la nueva TOP
            if ($id_supervisor_nuevo == 0 || $id_supervisor_nuevo == null)
                exit( "({success: false, error :'No tiene superior definido o este se encuentra deshabilitado del sistema.'})");

            if ($top['id_aprobador'] == $id_supervisor_nuevo)
                exit( "({success: false, error :'El supervisor no puede ser el mismo usuario que el aprobador.'})");

            if ($top['id_supervisor'] == $id_supervisor_nuevo)
                exit( "({success: 'falso', error :''})");            
            
            if ($top['id_usuario'] == $id_supervisor_nuevo)
                exit( "({success: false, error :'El supervisor no puede ser la misma persona que el usuario.'})");
            
            $datos_top['id_supervisor'] = $id_supervisor_nuevo;
            $id_accion                  = 8;

            $update_top = $this->ddp_tops->update($id_top, $datos_top);

            if ($update_top==0)
                exit( "({success: false, error :'Error al crear su TOP...'})");
            else
            {
                //agrego auditoria
                $aud = $this->_auditoria($id_accion, $id_usuario, $id_top,$top['id_supervisor'],$id_supervisor_nuevo);
                if ($aud)
                    echo "({success: true, error : 'La top se modifico correctamente.'})";
                else
                    return "({success: false, error :'Error al insertar auditoria. Comuniquese con el administrador del sistema.'})";
            }
        }
        else
            echo -1; //No tiene permisos
    }
       
    public function listado_auditoria()
    {
        if ($this->permisos['Listar'])
        {
            $id_top = $this->input->post("id_top");
            $listado=array();
            
            $this->load->model('ddp/ddp_tops_auditoria_model','ddp_tops_auditoria',true);
            
            $sort = $this->input->post("sort");
            $dir = $this->input->post("dir");
            $start = $this->input->post("start");
            $limit = $this->input->post("limit");
            
            $listado = $this->ddp_tops_auditoria->listado($start, $limit, $sort, $dir,$id_top);
            echo $listado;
        }
        else
            echo -1; //No tiene permisos
    }
    
    public function editar_aprobador()
    {
        if ($this->permisos['Alta'])
        {
            $id_usuario             = $this->user['id'];
            $id_top                 = $this->input->post("id_top");
            $id_aprobador_nuevo     = $this->input->post("id_aprobador");
            $id_accion              = 9;

            $datos['id_usuario']=$id_usuario;

            //busco el usuario supervisor
            $this->load->model('gestion_riesgo/gr_usuarios_model','gr_usuarios',true);
            $this->load->model('ddp/ddp_tops_model','ddp_tops',true);
            $top = $this->ddp_tops->dameTopPorId($id_top);
//            echo "<pre>" . print_r($top, true) . "</pre>";die();
            $datos['id_top_superior']=NULL;
            //creo la nueva TOP
            if ($id_aprobador_nuevo == 0 || $id_aprobador_nuevo == null)
                exit( "({success: false, error :'No tiene aprobador definido o este se encuentra deshabilitado del sistema.'})");

            if ($top['id_supervisor'] == $id_aprobador_nuevo)
                exit( "({success: false, error :'El aprobador no puede ser el mismo usuario que el supervisor.'})");

            if ($top['id_aprobador'] == $id_aprobador_nuevo)
                exit( "({success: 'falso', error :''})");
            
            if ($top['id_usuario'] == $id_aprobador_nuevo)
                exit( "({success: false, error :'El aprobador no puede ser la misma persona que el usuario.'})");

            $datos_top['id_aprobador']              = $id_aprobador_nuevo;
//            $datos_aud['id_accion']                 = 9;
//            $datos_aud['id_usuario_anterior']       = $top['id_aprobador'];
//            $datos_aud['id_usuario_nuevo']          = $id_aprobador_nuevo;
//            $datos_aud['id_usuario_alta']           = $id_usuario;
//            $datos_aud['id_top']                    = $id_top;
            

            $update_top = $this->ddp_tops->update($id_top, $datos_top);

            if ($update_top==0)
                exit( "({success: false, error :'Error al crear su TOP...'})");
            else
            {
                //agrego auditoria
                $aud = $this->_auditoria($id_accion, $id_usuario, $id_top,$top['id_aprobador'],$id_aprobador_nuevo);
                if ($aud)
                    echo "({success: true, error : 'La top se modifico correctamente.'})";
                else
                    return "({success: false, error :'Error al insertar auditoria. Comuniquese con el administrador del sistema.'})";
            }
        }
        else
            echo -1; //No tiene permisos
    }
    
    public function _auditoria ($id_accion,$id_usuario,$id_top, $id_usuario_anterior,$id_usuario_nuevo)
    {
        $aud = array();
        $aud['id_accion'] = $id_accion;
        $aud['id_usuario_alta'] = $id_usuario;
        $aud['id_top'] = $id_top;
        $this->load->model('ddp/ddp_tops_auditoria_model','ddp_tops_auditoria',true);
        
        switch ($id_accion)
        {
            case 8:
                $this->load->model('usuarios_model','usuarios',true);
                $anterior = $this->usuarios->dameUsuario($id_usuario_anterior);
                $nuevo = $this->usuarios->dameUsuario($id_usuario_nuevo);
                $aud['observacion'] = "Supervisor " . $anterior['nomape'] . " reemplazado por " . $nuevo['nomape'];
                break;
            case 9:
                $this->load->model('usuarios_model','usuarios',true);
                $anterior = $this->usuarios->dameUsuario($id_usuario_anterior);
                $nuevo = $this->usuarios->dameUsuario($id_usuario_nuevo);
                $aud['observacion'] = "Aprobador " . $anterior['nomape'] . " reemplazado por " . $nuevo['nomape'];
                break;
        }
        
        $insert_aud = $this->ddp_tops_auditoria->insert($aud);
        
        if ($insert_aud)
            return true;
        else
            return false;
        
    }
    
    function excel()
    {
        if ($this->permisos['Listar'])
            {
                $id_usuario=$this->user['id'];
                $this->load->model('usuarios_model','usuarios',true);
                $usuemp=$this->usuarios->dameUsuarioEmpresaYGR($id_usuario);
                $id_empresa = $usuemp['id_empresa'];
                $id_periodo = ($this->input->post("filtro_id_periodo"))?$this->input->post("filtro_id_periodo"):-1;

                if($id_periodo!=-1)
                {
                    $this->load->model('ddp/ddp_periodos_model','ddp_periodos',true);
                    $periodo=$this->ddp_periodos->damePeriodo($id_periodo);
                }
                else
                    $periodo=-1;

                
                $this->load->model('ddp/ddp_tops_model','ddp_tops',true);
                $start = 0;
                $limit = 1000;
                $sort = $this->input->post("sort");
                $dir = $this->input->post("dir");
                if($this->input->post("filtros"))
                {
                    $filtros=json_decode($this->input->post("filtros"));
                    foreach ($filtros as &$filtro)
                    {
                        if ($filtro=='Todas'||$filtro=='Todos'||$filtro=='-1')
                            $filtro="";
                    }
                    unset ($filtro);
//                        echo "<pre>".print_r($filtros,true)."</pre>";

                }
                else
                {
                    $filtros="";

                }
                $busqueda  = ($this->input->post("query"))?$this->input->post("query"):"";
                $campos="";
                if ($this->input->post("fields"))
                {
                    $campos = str_replace('"', "", $this->input->post("fields"));
                    $campos = str_replace('[', "", $campos);
                    $campos = str_replace(']', "", $campos);
                    $campos = explode(",",$campos);
//                            echo "<pre>".print_r($campos,true)."</pre>";
                }
                $listado = $this->ddp_tops->listado_excel($start, $limit, $filtros,$busqueda, $campos, $sort, $dir,$id_usuario,$periodo,$id_empresa);

    //        $datos_audit['id_modulo']=$this->modulo;
    //        $datos_audit['id_usuario']=$this->user['id'];
    //        $datos_audit['q_registros']=count($listado);
    //        $params=array($usuario,$filtros, $busqueda, $campos,$sort,$dir);
    //        $datos_audit['params']=json_encode($params);

    //        $this->load->model("auditoria_model","auditoria_model",true);
    //        $this->auditoria_model->guardarPedidoExcel($datos_audit);
    //             echo "<pre>Datos:".print_r($listado,true)."</pre>";
    //             die();

            $filename = 'Listado - Administración TOPs - '.date("YmdHis").'.xls';
            header('Content-Type:application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$filename.'"');
            header('Cache-Control: max-age=0');


            $this->load->library('excel');
            // Se asignan las propiedades del libro
            $this->excel->getProperties()
                ->setCreator(NOMSIS) // Nombre del autor
                ->setLastModifiedBy(NOMSIS) //Ultimo usuario que lo modificó
                ->setTitle($filename) // Titulo
                ->setSubject($filename) //Asunto
                ->setDescription('Listado desde grilla - v1.0 - 20180813') //Descripción
                ->setKeywords('') //Etiquetas
                ->setCategory(''); //Categorias
            $setActiveSheetIndex=$this->excel->setActiveSheetIndex(0);

             $this->excel->getActiveSheet()->setTitle('Listado - Administración TOPs');

              $estiloTituloReporte = array(
                'font' => array(
                'name'      => 'Calibri',
                'bold'      => true,
                'italic'    => false,
                'strike'    => false,
                'size'      =>16,
                'color'     => array(
                    'rgb' => '000000'
                    )
                ),
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                        'rotation' => 0,
                        'wrap' => TRUE
                    )
            );
             $estiloFilaTitulos = array(
                    'font' => array(
                        'name'      => 'Calibri',
                        'bold'      => true,
                        'italic'    => false,
                        'strike'    => false,
                        'size' =>10,
                        'color'     => array(
                            'rgb' => 'FFFFFF'
                        )
                    ),
                    'fill' => array(
                        'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array(
                            'rgb' => 'EED693')//ese color ya esta OK
                    ),
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_NONE
                        )
                    ),
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                        'rotation' => 0,
                        'wrap' => FALSE
                    )
                );
             $estiloFilaPar = array(
                    'font' => array(
                        'name'      => 'Calibri',
                        'bold'      => false,
                        'italic'    => false,
                        'strike'    => false,
                        'size' =>9,
                        'color'     => array(
                            'rgb' => '000000'
                        )
                    ),
                    'fill' => array(
                        'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array(
                            'rgb' => 'FFFFCC')//ese color ya esta OK
                    ),
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_NONE
                        )
                    ),
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                        'rotation' => 0,
                        'wrap' => FALSE
                    )
                );
             $estiloFilaImpar = array(
                    'font' => array(
                        'name'      => 'Calibri',
                        'bold'      => false,
                        'italic'    => false,
                        'strike'    => false,
                        'size' =>9,
                        'color'     => array(
                            'rgb' => '000000'
                        )
                    ),
                    'fill' => array(
                        'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array(
                            'rgb' => 'E5FFCC')//ese color ya esta OK
                    ),
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_NONE
                        )
                    ),
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                        'rotation' => 0,
                        'wrap' => FALSE
                    )
                );

            $setActiveSheetIndex->setCellValue('A1',"Listado - Administración TOPs");
            $setActiveSheetIndex->mergeCells('A1:M1');
            $setActiveSheetIndex->getRowDimension('1')->setRowHeight(30);
            $this->excel->getActiveSheet()->getStyle('A1')->applyFromArray($estiloTituloReporte);
            $setActiveSheetIndex->setCellValue('A2',"Fecha y hora: ");
            $setActiveSheetIndex->mergeCells('A2:B2');
            $setActiveSheetIndex->setCellValue('C2',  date('d-m-Y H:i:s'));
    //        $setActiveSheetIndex->setCellValue('A3',"Descargado por: ");
    //        $setActiveSheetIndex->mergeCells('A3:B3');
    //        $setActiveSheetIndex->setCellValue('C3',$usuario['usuario']['persona']);

            $maxCol=count($listado[0]);
            $maxFil=count($listado);
            //defino celda superior derecha de la tabla de datos
            $iniFil=5;
            $iniCol=0;
            //atributos
            $col=0;
            $fil=0;


            foreach ($listado as $clave => $arreglo) {

                if ($fil==0)
                {
                    foreach ($arreglo as $key => $value) {

                        $columna=PHPExcel_Cell::stringFromColumnIndex($iniCol+$col);
                        $filaAtributo=(string)$iniFil+$fil;
                        $filaValor=(string)$iniFil+$fil+1;
                        $celdaAtributo=$columna.$filaAtributo;
                        $celdaValor=$columna.$filaValor;
                        $setActiveSheetIndex->setCellValue($celdaAtributo,$key);
                        $setActiveSheetIndex->setCellValue($celdaValor,$value);
                        $this->excel->getActiveSheet()->getStyle($celdaAtributo)->applyFromArray($estiloFilaTitulos);
                        $this->excel->getActiveSheet()->getStyle($celdaValor)->applyFromArray($estiloFilaImpar);



                        $col++;
                    }
                    $col=0;
                    $fil=$fil+2;

                }
                else
                {
                    foreach ($arreglo as $key => $value) {

                        $columna=PHPExcel_Cell::stringFromColumnIndex($iniCol+$col);
                        $fila=(string)$iniFil+$fil;
                        $celda=$columna.$fila;
                        $setActiveSheetIndex->setCellValue($celda,$value);

                        if (($fil % 2)==0){
                            $this->excel->getActiveSheet()->getStyle($celda)->applyFromArray($estiloFilaPar);
                        }else{
                            $this->excel->getActiveSheet()->getStyle($celda)->applyFromArray($estiloFilaImpar);
                        }
                        $anchoEstandarMaximo=30;
                        $anchoEstandarMinimo=12;
                        $anchoTexto=strlen($value);

                        if($anchoTexto>$anchoEstandarMaximo) 
                            $setActiveSheetIndex->getColumnDimension($columna)->setWidth($anchoEstandarMaximo);
                        elseif ($anchoTexto<$anchoEstandarMinimo) 
                            $setActiveSheetIndex->getColumnDimension($columna)->setWidth($anchoEstandarMinimo);
                        else
                            $setActiveSheetIndex->getColumnDimension($columna)->setWidth($anchoTexto);

                        $col++;

                    }
                    $col=0;
                    $fil=$fil+1;
                }
            }

    //        $getActiveSheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
            $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
            $objWriter->save('php://output');
        }
        else 
            echo 0;
            
    }
    
    public function eliminar_top()
    {
        if ($this->permisos['Baja'])
        {
            $id_top = $this->input->post("id_top");

            if ($id_top != null && $id_top != 0)
            {
                $this->load->model('ddp/ddp_tops_model','ddp_tops',true);
                $datos=array();

                $datos["habilitado"] = 0;
                $update = $this->ddp_tops->update($id_top,$datos);
                if($update)
                {
                    $id_accion          = 16;
                    $id_usuario         = $this->user['id'];
                    $aud = $this->_auditoria($id_accion, $id_usuario, $id_top,'','');
                    echo 1;
                }
                else
                    echo 0;
            }
            else
                echo 2;
        }
        else
            echo 3;
    }
        
}
?>