<?php
class Adm_ed extends CI_Controller
{
    private $modulo=39;
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
//            echo "<pre>Origen".print_r($this,true)."</pre>";
	}
	
	public function index()
	{
		$user=$this->session->userdata(USER_DATA_SESSION);
		if ($user['id'] > 0)
		{			
                    $modulo = $this->uri->segment(SEGMENTOS_DOM+2);
                    if (!isset($modulo)) $modulo=0;
                    $this->load->model('permisos_model','permisos_model',true);
                    $variables['permiso'] = $this->permisos_model->checkIn($user['perfil_id'],$modulo);
                    $variables['permiso_permiso'] = $this->permisos_model->checkIn($user['perfil_id'],2);
                    $this->load->view('ed/adm_ed/listado',$variables);
		}
		else
		{
                    redirect("admin/admin/index_js","location", 301);
		}
	}
	
	public function listado()
	{
            if ($this->permisos['Listar'])
            {
                $this->load->model('ed/ed_evaluaciones_model','ed_model',true);
                $start = $this->input->post("start");
                $limit = $this->input->post("limit");
                $sort = $this->input->post("sort");
                $dir = $this->input->post("dir");
                $campos = "";
                if ($this->input->post("fields"))
                {
                    $campos = str_replace('"', "", $this->input->post("fields"));
                    $campos = str_replace('[', "", $campos);
                    $campos = str_replace(']', "", $campos);
                    $campos = explode(",",$campos);
                }
                $busqueda  = ($this->input->post("query"))?$this->input->post("query"):"";
                if(($this->input->post("f_id_periodo")=='Todos')||($this->input->post("f_id_periodo")==-1))
                {
                    $filtro="";
                }
                else
                {
                    $filtro=$this->input->post("f_id_periodo");
                }

                $listado = $this->ed_model->listado_admin($start, $limit, $filtro,$sort,$dir,$busqueda,$campos);
                echo $listado;
            }
            else
                echo -1; //No tiene permisos
          
            
	}
        
        public function combo_periodos()
	{
            $this->load->model('ed/ed_evaluaciones_model','evaluaciones',true);
            $var=$this->evaluaciones->damePeriodosFiltro();
//            echo "<pre>".print_r($var,true)."</pre>";die();
            echo $var;	
	}
        public function generar()
	{
            if ($this->permisos['Alta'])
            {
//                $id_periodo= $this->input->post("periodo");
                $id_periodo= 1;
                $id_estado= 1;
                $this->load->model('ed/ed_evaluaciones_model','evaluaciones',true);
                $this->load->model('usuarios_model','usuarios',true);
                 
                //verifico que ya no esten generadas y lanzadas EEDD para este período (id>1)
                $check=$this->evaluaciones->verificarNoExistan($id_periodo);
                if($check)
                {
                    //traigo todos los usuarios y sus supervisores
                    $usuarios=$this->usuarios->dameUsuarioConSupervisor();
//                     echo "<pre>".print_r($usuarios,true)."</pre>";
                    //limpio la tabla de EEDD generadas anteriores
                     $limpiar=$this->evaluaciones->limpiarGeneradosAnteriores($id_periodo);

                    //inserto 
                     foreach ($usuarios as $usuario)
                     {
                         $dato=$usuario;
                        $dato['id_periodo']=$id_periodo;
                        $dato['id_estado']=$id_estado;
                        $insert=$this->evaluaciones->insert($dato);
//                        echo "<pre>".print_r($dato,true)."</pre>";

                     }
                }
                else
                {
                    echo "({success: false, error :'Ya existen Evaluaciones en curso para el período designado'})";; //
                }
            }
            else
                echo "({success: false, error :'No tiene permisos para realizar la acciòn solicitada'})";; //
	}
        
        public function datosExcel()
	{
            $this->load->library('excel');
            $prob=array('#','Periodo','Nombre','Apellido','Area','Gerencia','Supervisor','Estado','C1-u','C1-s','C2','FyAM','PM','FM','Peso','% Cump');
            $this->load->model('ed/ed_evaluaciones_model','ed_evaluaciones',true);
            $start = $this->input->post("start");
            $sort = $this->input->post("sort");
            $dir = $this->input->post("dir");
            $campos = "";//["id_evaluacion","empleado","area","gerencia","supervisor"];
            if ($this->input->post("campos"))
            {
                $campos = str_replace('"', "", $this->input->post("campos"));
                $campos = str_replace('[', "", $campos);
                $campos = str_replace(']', "", $campos);
                $campos = explode(",",$campos);
            }
            $busqueda  = ($this->input->post("query"))?$this->input->post("query"):"";
            if(($this->input->post("f_id_periodo")=='Todos')||($this->input->post("f_id_periodo")==-1))
            {
                $filtro="";
            }
            else
            {
                $filtro=$this->input->post("f_id_periodo");
            }
            $titulos=$prob;
            $listado['titulos'] = $titulos;
            $listado['datos'] = $this->ed_evaluaciones->listadoExcel($start, $filtro,$sort,$dir,$busqueda,$campos);
            
            $datos_audit['id_modulo']=$this->modulo;
            $datos_audit['id_usuario']=$this->user['id'];
            $datos_audit['q_registros']=count($data);
            $params=array($start, $filtro,$sort,$dir,$busqueda,$campos);
            $datos_audit['params']=json_encode($params);

            $this->load->model("auditoria_model","auditoria_model",true);
            $this->auditoria_model->guardarPedidoExcel($datos_audit);
            
            $estiloTituloProb=array(
                'font' => array(
                    'bold' => true,
                    'size' =>9
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                    'rotation' => 0,
                    'wrap' => TRUE
                    )               
            );
            $estiloDatosProb=array(
                'font' => array(
                    'size' => 10
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                    'rotation' => 0,
                    'wrap' => TRUE
                    )
            );
            $ed['estilos']=$estiloTituloProb;
            $ed['datos']=$listado;
            
//            foreach ($estiloTitulo as $key => $value) {
//                echo $key;
//                echo $value;
//            }
//            echo "<pre>".print_r($listado,true)."</pre>";
//            $this->load->view('/ed/adm_ed/descargarListado',$ed);
//            $this->load->view('/ed/adm_ed/prueba');
            $this->excel($listado,$estiloTituloProb,$estiloDatosProb);//,$estiloTitulo
            
	}
        
        public function excel($listado,$estiloTituloProb="",$estiloDatosProb="")//,$estiloTitulo
        {
            $filename = 'ListadoAdministrarED.xls';
            header('Content-Type:application/vnd.ms-excel'); //mime type
            header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name          
            header('Cache-Control: max-age=0'); //no cache
            $this->load->library('excel');


            $this->excel->getProperties()
                ->setCreator("Sistema de Mejora Continua (SMC)") // Nombre del autor
                ->setLastModifiedBy("Sistema de Mejora Continua (SMC)") //Ultimo usuario que lo modificó
                ->setTitle("Listado Administrar ED") // Titulo
                ->setSubject("Listado Administrar ED") //Asunto
                ->setDescription("Listado Administrar ED") //Descripción
                ->setKeywords("Listado Administrar ED") //Etiquetas
                ->setCategory("Listado Administrar ED");
            $getActiveSheet=$this->excel->getActiveSheet();
            
            if($estiloTituloProb != "" || $estiloTituloProb != null)
            {
                    $estiloTitulo = $estiloTituloProb;
            }
            
            if($estiloDatosProb != "" || $estiloDatosProb != null)
            {
                    $estiloDatos = $estiloDatosProb;
            }

//            echo "<pre>".print_r($listado,true)."</pre>";
//            die();

            $setActiveSheetIndex=$this->excel->setActiveSheetIndex(0);

            $lastColumn = 'A';
//                    $this->excel->getActiveSheet()->getHighestColumn();
            $i = 1;
            $band=0;
            
            foreach ($listado as $reg)
            {
                foreach($reg as $linea)
                {
                    if($band == 0)
                    {
                        $getActiveSheet
                            ->setCellValue($lastColumn.$i,$linea);
                        if($estiloTituloProb != "" || $estiloTituloProb != null)
                        {
                            $getActiveSheet->getStyle($lastColumn.$i)->applyFromArray($estiloTitulo);
                        }
                        $getActiveSheet->getColumnDimension($lastColumn)->setAutoSize(true);
                        $lastColumn++;
                    }
                    if($band == 1)
                    {
                        foreach($linea as $titulo=>$dato)
                        {
                            if ($titulo == 'v_cump')
                            {
                                $getActiveSheet
                                     ->setCellValue($lastColumn.$i,$dato);
                                $getActiveSheet->getStyle($lastColumn.$i)->getNumberFormat()->applyFromArray( 
                                    array( 
                                        'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE
                                    )
                                );
                            }
                            else
                            {
                                $getActiveSheet
                                     ->setCellValue($lastColumn.$i,$dato);
                            }
                            if($estiloDatosProb != "" || $estiloDatosProb != null)
                            {
                                $getActiveSheet->getStyle($lastColumn.$i)->applyFromArray($estiloDatos);
                            }
                            $lastColumn++;
                        }
                        $i++;
                        $lastColumn = 'A';
                    }
                }
                $band=1;
                $i++;
                $lastColumn = 'A';
            }

            $this->excel->setActiveSheetIndex()->getRowDimension(1)->setRowHeight(25);

            $getActiveSheet->getPageSetup()->setFitToWidth(1);
            $getActiveSheet->getPageSetup()->setFitToHeight(0);
            $getActiveSheet->setTitle('Listado Administrar EDs');
            $getActiveSheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
            $getActiveSheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);


            //save it to Excel5 format (excel 2003 .XLS file), chan ge this to 'Excel2007' (and adjust the filename extension, also the header mime type)
            //if you want to save it as .XLSX Excel 2007 format
            $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
            ob_end_clean();
            //force user to download the Excel file without writing it to server's HD
            $objWriter->save('php://output');
        }
        
    public function combo_usuarios()
    {
        $limit=$this->input->post("limit");
        $start=$this->input->post("start");
        $query=$this->input->post("query");
        $id_usuario_supervisor=$this->input->post("id_usuario_supervisor");
        $this->load->model('ed/ed_evaluaciones_alta_model','ed_alta',true);
        $jcode = $this->ed_alta->usuariosCombo($limit,$start,$query,$id_usuario_supervisor);
        echo $jcode;
    }

    public function modificar()
    {
        if ($this->permisos['Modificacion'])
        {
            $id                     = $this->input->post("id_evaluacion");                
            $id_usuario_supervisor  = $this->input->post("id_usuario_supervisor");
//            echo "<pre>".print_r($campos,true)."</pre>";
            $this->load->model('ed/ed_evaluaciones_model','ed_evaluaciones',true);
            $ed = $this->ed_evaluaciones->dameCabeceraEdMail($id);
            
            $id_sup_old = $ed['id_usuario_supervisor'];

            if($id_usuario_supervisor != $id_sup_old)
            {
                if($ed['id_estado'] == 1)
                {
                    if ($id_usuario_supervisor)
                    {
                        $supervisor=$this->dameDatosSupervisor($id_usuario_supervisor);
                        $datos = $supervisor;

        //                echo "<pre>".print_r($campos,true)."</pre>";
                        $this->load->model('usuarios_model','usuarios',true);


                        $contenido['periodo'] = $ed['periodo'];

                        $supervisor_new = $this->usuarios->DameUsuarioPersona($id_usuario_supervisor);
                        $supervisor_new = array_pop($supervisor_new);
                        $contenido['supervisor_new'] = $supervisor['supervisor'];
                        $contenido['puesto_supervisor_new'] = $supervisor['puesto_supervisor'];
                        $contenido['mails'] = $supervisor_new['email'];

                        $empleado = $this->ed_evaluaciones->dameUsuarioEmpleado($id);
                        $empleado = array_pop($empleado);
                        $contenido['empleado'] = $empleado['usuario'];
                        $contenido['mail_empleado'] = $empleado['email'];
                        $contenido['genero_empleado'] = $empleado['genero'];

                        $usuario_editor = $this->usuarios->DameUsuarioPersona($this->user['id']);
                        $usuario_editor = array_pop($usuario_editor);
                        $contenido['usuario_editor'] = $usuario_editor['nomape'];
                        $contenido['mails'] .= ', '.$usuario_editor['email'];
                        $contenido['puesto_editor'] = $usuario_editor['puesto'];

                        $contenido['id_ed'] = $id;

        //                 echo "<pre>".print_r($contenido,true)."</pre>"; die();
                        if ($datos!=0)
                        {
                            if ($this->ed_evaluaciones->update($id,$datos))
                            {
                                $this->_enviar_mail($contenido);
                                echo 1;
                            }
                            else
                                echo 0;
                        }
                        else
                            echo 3;
                    }
                    else
                        echo 0;
                }
                else
                    echo 4;
            }
            else
                echo 5;
        }
        else
                echo 2;
    }
        
    function dameDatosSupervisor($id_supervisor)
    {
        $this->load->model('ed/ed_evaluaciones_alta_model','ed_alta',true);
        $supervisor=$this->ed_alta->dameDatosSupervisor($id_supervisor);
//            echo "<pre>".print_r($supervisor,true)."</pre>";
        if ($supervisor !=0)
            return $supervisor;
        else
            return 0;
    }
    
    function _enviar_mail($data)
    {
//        echo "<pre>1".print_r($data,true)."</pre>";
        $link='www.polidata.com.ar/sdj/admin';
                        
$mail_sup=<<<HTML
<html> 
    <head>
        <title>SMC</title>
        
    </head>
    <body>
    <div style="color:#000000; font-family:Verdana, Arial, Helvetica, sans-serif; text-align:Left; font-size:13px;">
        <pre>
            #persona,

            Informamos que se ha procedido al cambio de supervisor en su evaluación de desempeño del período #periodo y su nuevo evaluador será #sup_new.
            
            <b>ED Nro:</b>
            <em>#id</em>
        
            <b>Perdíodo:</b>
            <em>#periodo</em>
            
            <b>Nuevo Supervisor:</b>
            <em>#sup_new (#puesto)</em>
        
            <b>Cambio realizad por:</b>
            <em>#editor (#puestoEdit)</em>  
            
            Para verificar el cambio ingrese al sistema desde <a href="#link" target="_blank">aqu&iacute;</a>, o copiando y pegando la siguiente direcci&oacute;n en su navegador web <b>Mozilla Firefox</b> o <b>Google Chrome</b>: 
            #link.
            

            Atentamente
            #firma
            
            Importante: Mensaje generado autom&aacute;ticamente, por favor no responder.
            <hr>
                Para: #usuarioPara <br>
                CC  : #usuariosCC1 <br>
           <hr>

        </pre>
        </div>
    </body>
</html>
HTML;
        
        $cuerpo_mail=$mail_sup;
        
        $subject='Edición Evaluación de Desempeño Nro:'.$data['id_ed'];
        
        if($data['genero_empleado'] == 'M')
            $cuerpo_mail=str_replace("#persona"           , 'Estimado '.$data['empleado']  , $cuerpo_mail);
        else
            $cuerpo_mail=str_replace("#persona"           , 'Estimada '.$data['empleado']  , $cuerpo_mail);
        
        $cuerpo_mail=str_replace("#editor"           , $data['usuario_editor']  , $cuerpo_mail);
        
        $cuerpo_mail=str_replace("#puestoEdit"           , $data['puesto_editor']  , $cuerpo_mail);
        
        $cuerpo_mail=str_replace("#id"         , $data['id_ed']  , $cuerpo_mail);
        
        $cuerpo_mail=str_replace("#periodo"         , $data['periodo']   , $cuerpo_mail);
        
        $cuerpo_mail=str_replace("#sup_new"         , $data['supervisor_new']        ,$cuerpo_mail);
        
        $cuerpo_mail=str_replace("#puesto"         , $data['puesto_supervisor_new']        ,$cuerpo_mail);
        if($data['mail_empleado'])
            $cuerpo_mail=str_replace("#usuarioPara"         , $data['mail_empleado']        ,$cuerpo_mail);
        else
            $cuerpo_mail=str_replace("#usuarioPara"         , 'sinmail+smc@salesdejujuy.com'        ,$cuerpo_mail);
        $cuerpo_mail=str_replace("#usuariosCC1"         , $data['mails']        ,$cuerpo_mail);
        
            
        $cuerpo_mail=str_replace("#link"                , $link                  ,$cuerpo_mail);
        $cuerpo_mail=str_replace("#firma"               , htmlentities(NOMSIS,ENT_QUOTES,"UTF-8"),$cuerpo_mail);

        
         $this->load->library('email');
//             $this->email->clear();//para cuando uso un bucle
        $this->email->from(SYS_MAIL, NOMSIS);
        $this->email->message($cuerpo_mail);
        $this->email->subject($subject);
        
//        echo $cuerpo_mail;
        if($data['mail_empleado'])
            $this->email->to($data['mail_empleado']);
        else
            $this->email->to('sinmail+smc@salesdejujuy.com');
        $this->email->cc($data['mails']);
        
        IF (MAILBCC && MAILBCC !="")
            $this->email->bcc(MAILBCC);
//        echo 7;
        if (ENPRODUCCION)
        {
            if($this->email->send())
                return 1;
            else
                return 0;
        }
        else
        {
                return 0;
        }
    }
}
?>