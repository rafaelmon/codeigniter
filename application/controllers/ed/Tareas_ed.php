<?php
class Tareas_ed extends CI_Controller
{
    private $modulo=43;
    private $user;
    private $permisos;
    private $roles;
    private $origen;
    
        function __construct()
	{
            parent::__construct();
            header ("Expires: Thu, 31 Dic 1976 23:59:00 GMT"); //la pagina expira en una fecha pasada
            header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); //ultima actualizacion ahora cuando la cargamos
            header ("Cache-Control: no-cache, must-revalidate"); //no guardar en CACHE
            header ("Pragma: no-cache");
            $this->user=$this->session->userdata(USER_DATA_SESSION);
//           echo "<pre>".print_r(!($this->user['id'])>0,true)."</pre>";
            if (!($this->user['id']>0)) 
                redirect("admin/admin/index_js","location", 301);
            $this->load->model("permisos_model","permisos_model",true);
            $this->permisos= $this->permisos_model->checkIn($this->user['perfil_id'],$this->modulo);
            $this->load->model("gestion_riesgo/gr_roles_model","gr_roles",true);
            $this->roles= $this->gr_roles->checkIn($this->user['id']);
            $this->load->model("usuarios_model","usuarios",true);
            $this->origen= $this->usuarios->dameOrigen($this->user['id']);
	}
	
	public function index()
	{
            $modulo = $this->uri->segment(SEGMENTOS_DOM+2);
            if (!isset($modulo)) $modulo=0;
            $this->load->model('permisos_model','permisos_model',true);
            $variables['permiso'] = $this->permisos_model->checkIn($this->user['perfil_id'],$modulo);
//            $variables['permiso_permiso'] = $this->permisos_model->checkIn($this->user['perfil_id'],$this->modulo);
            $variables['btn'] = $this->user['id'];
            $this->load->model("gestion_riesgo/gr_usuarios_model","gr_usuarios",true);
            $variables['u_gr']=$this->gr_usuarios->verificarSiPerteneceGr($this->user['id']);
//             echo "<pre>".print_r($variables,true)."</pre>";
            $this->load->view('ed/tareas/listado',$variables);
	}
	
	public function listado()
	{
		if ($this->permisos['Listar'])
                {
                    $id_usuario=$this->user['id'];
                    $listado=array();
                    $this->load->model('gestion_riesgo/mejoracontinua_model','mejoracontinua',true);
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
                    //mantengo usuario solo para ordenar
                    $usuario=$this->_dameAreas($id_usuario);
                    $listado = $this->mejoracontinua->listadoParaED($usuario,$start, $limit, $filtros,$busqueda, $campos, $sort, $dir);
                    
                    echo $listado;
                }
                else
                    echo -1; //No tiene permisos
                
	}
	public function _dameAreas($id_usuario)
        {
//            $id_usuario=$this->user['id'];
            $areas=array();
            $this->load->model('gestion_riesgo/gr_usuarios_model','gr_usuarios',true);
            $usuario=$this->gr_usuarios->dameUsuarioPorId($id_usuario);
            $areas['usuario']=$usuario;
            
            $id_area=$usuario['id_area'];
            $arrayAreas=array();
            $areasInferiores[]=$id_area;
            $this->load->model('gestion_riesgo/gr_areas_model','gr_areas',true);
            
            //Areas Inferiores: recorro el nro máximo de niveles posible para traer las áreas inferiores
            for($i=0;$i<10;$i++)
            {
                $as=$this->gr_areas->dameAreasInferiores($areasInferiores);                
                if (count($as)>1)
                {
                    foreach ($as as $area)
                    {
                        $arrayAreas[]=$area['id_area'];
                    }
                    $areasInferiores= array_unique(array_merge($areasInferiores,$arrayAreas));
                }
                    
            }
            $areas['areas_inferiores']=  str_replace("'","",implode(",",$areasInferiores));
            
            //Areas Superiores: vos subiendo por la herencia hasta no encontrar padre
            $areasSuperiores=array();
            $areasSuperiores[]=$id_area;
            while ($this->gr_areas->dameAreaSuperior(end($areasSuperiores))!="")
            {
                $areasSuperiores[]=$this->gr_areas->dameAreaSuperior(end($areasSuperiores));
            }
            $areas['areas_superiores']=  str_replace("'","",implode(",",$areasSuperiores));
            
            if(in_array(4, $areasSuperiores))
                $areas['gr']=1;
            else
                $areas['gr']=0;
                    
//            echo "<pre>".print_r($areas,true)."</pre>";
            
            return $areas;
            
        }
	public function historial_cambios()
	{
		if ($this->permisos['Listar'])
                {
                    $id_usuario=$this->user['id'];
                    $listado=array();
                    $this->load->model('gestion_riesgo/gr_historial_model','historial',true);
                    $id_tarea = $this->input->post("id");
                    $start = $this->input->post("start");
                    $limit = $this->input->post("tampagina");
                    $listado = $this->historial->listado($id_tarea,$start, $limit);
                    echo $listado;
                }
                else
                    echo -1; //No tiene permisos
                
	}
	public function historial_acciones()
	{
		if ($this->permisos['Listar'])
                {
                    $id_usuario=$this->user['id'];
                    $listado=array();
                    $this->load->model('gestion_riesgo/gr_historial_acciones_model','historial_acciones',true);
                    $id_tarea = $this->input->post("id");
                    $start = $this->input->post("start");
                    $limit = $this->input->post("tampagina");
                    $listado = $this->historial_acciones->listado($id_tarea,$start, $limit);
                    echo $listado;
                }
                else
                    echo -1; //No tiene permisos
                
	}
         public function filtro_estados()
        {
            $this->load->model("gestion_riesgo/gr_estados_model","gr_estados",true);
            $estados=array(1,2,3,4,9);
            echo $this->gr_estados->dameCombo($estados);
            
        }
        function excel()
        {
            $id_usuario=$this->user['id'];
            $usuario=$this->_dameAreas($id_usuario);
//             echo "<pre>Datos:".print_r($usuario,true)."</pre>";die();
            $sort = $this->input->post("sort");
//             echo "<pre>Datos:".print_r($sort,true)."</pre>";die();
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
            $busqueda  = ($this->input->post("busqueda"))?$this->input->post("busqueda"):"";
            $campos="";
            if ($this->input->post("campos"))
            {
                $campos = str_replace('"', "", $this->input->post("campos"));
                $campos = str_replace('[', "", $campos);
                $campos = str_replace(']', "", $campos);
                $campos = explode(",",$campos);
//                            echo "<pre>".print_r($campos,true)."</pre>";
            }
            $this->load->model('gestion_riesgo/mejoracontinua_model','mejoracontinua',true);
            $listado = $this->mejoracontinua->listado_excel_ed($usuario,$filtros, $busqueda, $campos,$sort,$dir);
            
            
            $datos_audit['id_modulo']=$this->modulo;
            $datos_audit['id_usuario']=$this->user['id'];
            $datos_audit['q_registros']=count($data);
            $params=array($usuario,$filtros, $busqueda, $campos,$sort,$dir);
            $datos_audit['params']=json_encode($params);

            $this->load->model("auditoria_model","auditoria_model",true);
            $this->auditoria_model->guardarPedidoExcel($datos_audit);
//             echo "<pre>Datos:".print_r($listado,true)."</pre>";
//             die();
                        
            $filename="ListadoTareas.xls";
            header('Content-Type:application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$filename.'"');
            header('Cache-Control: max-age=0');
            
                
            $this->load->library('excel');
            // Se asignan las propiedades del libro
            $this->excel->getProperties()
                ->setCreator(NOMSIS) // Nombre del autor
                ->setLastModifiedBy(NOMSIS) //Ultimo usuario que lo modificó
                ->setTitle("Listado de Tareas") // Titulo
                ->setSubject("Listado de Tareas") //Asunto
                ->setDescription("Listado de Tareas") //Descripción
                ->setKeywords("Tareas") //Etiquetas
                ->setCategory("Tareas"); //Categorias
            $setActiveSheetIndex=$this->excel->setActiveSheetIndex(0);
            
             $this->excel->getActiveSheet()->setTitle("Listado de Tareas");
            
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
            
            $setActiveSheetIndex->setCellValue('A1',"Listado de Tareas");
            $setActiveSheetIndex->mergeCells('A1:M1');
            $this->excel->getActiveSheet()->getStyle('A1')->applyFromArray($estiloTituloReporte);
            $setActiveSheetIndex->setCellValue('A2',"Fecha y hora: ");
            $setActiveSheetIndex->mergeCells('A2:B2');
            $setActiveSheetIndex->setCellValue('C2',  date('d-m-Y H:i:s'));
            $setActiveSheetIndex->setCellValue('A3',"Descargado por: ");
            $setActiveSheetIndex->mergeCells('A3:B3');
            $setActiveSheetIndex->setCellValue('C3',$usuario['usuario']['persona']);

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
            $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
            $objWriter->save('php://output');
        }
}

?>