<?php
class alta_eedd extends CI_Controller
{
    private $modulo=41;
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
            $modulo = $this->uri->segment(SEGMENTOS_DOM+2);
            if (!isset($modulo)) $modulo=0;
            $this->load->model('permisos_model','permisos_model',true);
            $variables['permiso'] = $this->permisos_model->checkIn($this->user['perfil_id'],$modulo);
            $this->load->view('ed/alta_ed/listado',$variables);
	}
	
	public function listado()
	{
            if ($this->permisos['Listar'])
            {
		$this->load->model('ed/ed_evaluaciones_alta_model','ed_alta',true);
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
		$listado = $this->ed_alta->listado($start, $limit, $filtro,$sort,$dir,$busqueda,$campos);
		echo $listado;
            }
            else
                echo "({success: false, error :'No tiene permisos para realizar la acciòn solicitada'})"; //
	}
        
        public function combo_periodos()
	{
            $this->load->model('ed/ed_evaluaciones_alta_model','ed_alta',true);
            $var=$this->ed_alta->damePeriodosFiltro();
            echo $var;	
	}
        public function generar()
	{
            if ($this->permisos['Alta'])
            {
//                $id_periodo= $this->input->post("periodo");
                $tipo=$this->input->post("tipo");
                $semestre=$this->input->post("semestre");
                $anio=$this->input->post("anio");
                $peridodo=$anio."-0".$semestre;
                //verifico si el período ya existe sino lo inserto
                $this->load->model('ed/ed_periodos_model','ed_periodos',true);
                $id_periodo=$this->ed_periodos->damePeriodoPorAnioSemestre($anio,$semestre);
                if($id_periodo==0)
                {
                    $datos_periodo['semestre']=$semestre;
                    $datos_periodo['anio']=$anio;
                    $datos_periodo['periodo']=$peridodo;
                    $id_periodo=$this->ed_periodos->insert($datos_periodo);
                }
                
                $this->load->model('ed/ed_evaluaciones_alta_model','ed_alta',true);
                $this->load->model('usuarios_model','usuarios',true);
                //diferencio entre el tipo de alta 1=alta masiva, 2=alta individual
                if($tipo==1)
                {
                    $id_empresa=$this->input->post("empresa");

                    //verifico que ya no esten generadas y lanzadas EEDD para este período (id>1)
    //                $check=$this->ed_evaluaciones->verificarNoExistan($id_periodo);
                    $check=true;
                    if($check)
                    {
                        //traigo todos los usuarios y sus supervisores
                        $usuarios=$this->usuarios->dameUsuariosConSupervisorPorEmpresa($id_empresa);
//                         echo "<pre>".print_r($usuarios,true)."</pre>";

                        //Traigo los usuarios que ya estan verificados y con ED en la grilla
                        $idUsuariosConEDVerificada=$this->ed_alta->dameUsuariosVerificados();
    //                     echo "<pre>".print_r($usuariosConEDVerificada,true)."</pre>";

                        //limpio la tabla de EEDD generadas anteriores
    //                     $limpiar=$this->ed_alta->limpiarGeneradosAnteriores($id_periodo);
                         $eliminar=$this->ed_alta->deleteNoVerificados();
//                        if ($idUsuariosConEDVerificada==0)
//                            $idUsuariosConEDVerificada=array();
                        //inserto 
                         foreach ($usuarios as $usuario)
                         {
                            $dato=$usuario;
                            $dato['id_periodo']=$id_periodo;
                            if (!in_array($usuario['id_usuario'], $idUsuariosConEDVerificada))
                                $insert=$this->ed_alta->insert($dato);
    //                        echo "<pre>".print_r($dato,true)."</pre>";
                         }
                        //verifico si ya existe en la tabla de evaluaciones y lo marco como duplicado
                        $this->verificarDuplicados();
                        echo 1;

                    }
                    else
                    {
                        echo "({success: false, msg:'Ya existen Evaluaciones en curso para el período designado'})";//anulado por ahora
                    }
                }
                elseif($tipo==2)
                {
                    $id_usuario=$this->input->post("usuario");
//                    echo "<pre>".print_r($usuarios,true)."</pre>";
                    
                    //verifico que el usuario todavía no este en la grilla para el mismo período
                    $checkUsuario=$this->ed_alta->verificarUsuario($id_usuario,$id_periodo);
                    
                    if (!$checkUsuario)
                    {
                        $usuario=$this->usuarios->dameUsuarioParaEd($id_usuario);
                        $dato=$usuario;
                        $dato['id_periodo']=$id_periodo;
//                        echo "<pre>".print_r($dato,true)."</pre>";die();
                        $insert=$this->ed_alta->insert($dato);
                        if ($insert)
                            echo "({success: true, msg:'Evaluación creada correctamente!'})";
                        else
                            echo "({success: false, msg:'Error generando registro'})";
                        
                    }
                    else
                        echo "({success: false, msg: 'La ED que intenta crear ya se encuentra en la grilla'})";; //
                        
                    
                }
                else
                    echo "({success: false, msg: 'No se reconoce el tipo de acción'})";; //
                    
                
            }
            else
                echo "({success: false, msg:'No tiene permisos para realizar la acciòn solicitada'})"; //
	}
        public function eliminar()
        {
            if ($this->permisos['Baja'])
            {
                $id= $this->input->post("id");
                $this->load->model('ed/ed_evaluaciones_alta_model','ed_alta',true);
                $eliminar=$this->ed_alta->delete($id);
                if ($eliminar)
                    echo "({success: true, msg:'Registro eliminado correctamente'})";
                else
                    echo "({success: false, msg:'Erro al eliminar registro'})";
                    
                    
            }
            else
                echo "({success: false, msg:'No tiene permisos para realizar la acciòn solicitada'})";
        }
        public function eliminarNoVerificados()
        {
            if ($this->permisos['Baja'])
            {
                $id= $this->input->post("id");
                $this->load->model('ed/ed_evaluaciones_alta_model','ed_alta',true);
                $eliminar=$this->ed_alta->deleteNoVerificados();
                if ($eliminar)
                    echo "({success: true, msg:'Registros eliminados correctamente'})";
                else
                    echo "({success: false, msg:'Erro al eliminar registros'})";
                    
                    
            }
            else
                echo "({success: false, msg:'No tiene permisos para realizar la acciòn solicitada'})";
        }
        public function combo_usuarios()
	{
            $limit=$this->input->post("limit");
            $start=$this->input->post("start");
            $query=$this->input->post("query");
            $this->load->model('ed/ed_evaluaciones_alta_model','ed_alta',true);
            $jcode = $this->ed_alta->usuariosCombo($limit,$start,$query);
            echo $jcode;
	}
        public function modificar()
	{
            if ($this->permisos['Modificacion'])
            {
                $id = $_POST['id_alta'];
                $this->load->model('ed/ed_evaluaciones_alta_model','ed_alta',true);
                $alta=$this->ed_alta->dameAltaPorId($id);
                
                $campos = json_decode($_POST['campos']);
                $valores = json_decode($_POST['valores']);
                if (count($campos)>0)
                {
                    foreach ($campos as $indice=>$camp)
                    {
                        switch ($camp)
                        {
                            case 'supervisor':
                                if ($alta['id_usuario']!=$valores[$indice])
                                {
                                    $supervisor=$this->dameDatosSupervisor($valores[$indice]);
                                    $datos = $supervisor;
                                }
                                else
                                    $datos=0;
                                break;
                            default :
                                $datos[$camp] = $valores[$indice];
                        }
                    }
                    $this->load->model('ed/ed_evaluaciones_alta_model','ed_alta',true);
                    
                    if ($datos!=0)
                    {
                        if ($this->ed_alta->update($id,$datos))
                        {
                            $this->verificarDuplicados();
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
                    echo 2;
	}
        function dameDatosSupervisor($id_supervisor)
        {
                    $this->load->model('ed/ed_evaluaciones_alta_model','ed_alta',true);
                    $supervisor=$this->ed_alta->dameDatosSupervisor($id_supervisor);
//                    echo "<pre>".print_r($supervisor,true)."</pre>";
                    if ($supervisor !=0)
                        return $supervisor;
                    else
                        return 0;
        }
        public function empresas_combo()
	{
            $this->load->model('empresas_model','empresas',true);
            $var=$this->empresas->dameComboEmpresas();
            echo $var;	
	}
//        public function iniciar()
//        {
//             if ($this->permisos['Alta'])
//            {
//                $this->verificarDuplicados();
//                $this->load->model('ed/ed_evaluaciones_alta_model','ed_alta',true);
////                $this->load->model('ed/ed_evaluacionesa_model','ed_alta',true);
////                $this->load->model('usuarios_model','usuarios',true);
//                $copiar=$this->ed_alta->copiarVerificados();
//                $eliminar=$this->ed_alta->deleteVerificados();
//                echo "({success: true, msg:'Evaluaciones iniciadas correctamente'})";
//            }
//            else
//                echo "({success: false, error :'No tiene permisos para realizar la acciòn solicitada'})"; //
//            
//        }
        public function iniciar()
        {
             //verifico duplicados
            $this->verificarDuplicados();
             
            
            $this->load->model('ed/ed_evaluaciones_alta_model','ed_alta',true);
            $this->load->model('ed/ed_evaluaciones_model','ed_evaluaciones',true);
            $verificar=$this->ed_alta->controlarDuplicados();
            
            if($verificar!=0)
            {
//                $marcar=$this->ed_alta->marcarDuplicado($verificar);
                echo "({success: false, msg:'Existen Evaluaciones ya inciadas'})"; //
                
            }
            else
            {
                $idsVerificados=$this->ed_alta->dameIdsVerificadasYNoDuplicados();
//                echo "<pre>".print_r($idsVerificados,true)."</pre>";die();
                if($idsVerificados!=0)
                {
                    $x=0;
                    $y=0;
                    foreach ($idsVerificados as $id)
                    {
                        $alta=$this->ed_alta->dameAltaParaIniciar($id);
//                        echo "<pre>".print_r($alta,true)."</pre>";die();
                        if ($alta!=0)
                        {
                            if ($alta['id_usuario_supervisor']!=NULL && $alta['id_usuario_supervisor']!=0 && $alta['id_usuario_supervisor']!='' && $alta['id_usuario']!=$alta['id_usuario_supervisor'])
                            {
                                $alta['id_estado']=1;
                                $alta['id_avance']=1;
                                $id_ed=$this->ed_evaluaciones->insert($alta);
                                $insertar_cuestionario=$this->generarCuestionario($id_ed);
                                $eliminar=$this->ed_alta->delete($id);
                                $x++;
                            }
                            else
                                $y++;

                        }
                    }
//                    $this->generarCuestionarios($ids_ed);                
//                    $eliminar=$this->ed_alta->deleteVerificados();
                    echo "({success: true, msg:'$x Evaluaciones iniciadas correctamente y $y Evaluaciones sin iniciar por incosistencias'})";
                    
                }
                else
                    echo "({success: false, msg:'No hay registros seleccionados para iniciar'})"; //
            }
            //si al verificar encuentro duplicados los marco en la tabla ed_evaluaciones_alta
             
//                    echo "<pre>".print_r($supervisor,true)."</pre>";

         }
         public function verificarDuplicados()
         {
            $this->load->model('ed/ed_evaluaciones_alta_model','ed_alta',true);
            $verificar=$this->ed_alta->verificarParaIniciar();
//            echo "<pre>".print_r($verificar,true)."</pre>";
            //si al verificar encuentro duplicados los marco en la tabla ed_evaluaciones_alta
            $marcar=$this->ed_alta->marcarDuplicado($verificar);
             
         }
        public function generarCuestionarios ($ids_evaluaciones)
        {
            $this->load->model('ed/ed_subcompetencias_model','subcompetencias',true);
            $this->load->model('ed/ed_ec_model','ed_ec',true);
            $preguntas=$this->subcompetencias->dameCompetenciasSubcompetencias();
            $inserts=0;
            if (is_array($ids_evaluaciones))
            {
                foreach ($ids_evaluaciones as $id_evaluacion)
                {
                    foreach ($preguntas as $pregunta)
                    {
                        $datos_ec=$pregunta;
                        $datos_ec['id_evaluacion']=$id_evaluacion;
                        $inserts+=$this->ed_ec->insert($datos_ec);
    //                    echo "<pre>".print_r($datos_ec,true)."</pre>";
                    }

                }
                return $inserts; //devuelvo la cantidad de inserciones realizadas! 
            }
            else 
                return 0;
                
        }
        public function generarCuestionario ($id_ed)
        {
            $this->load->model('ed/ed_subcompetencias_model','subcompetencias',true);
            $this->load->model('ed/ed_ec_model','ed_ec',true);
            $preguntas=$this->subcompetencias->dameCompetenciasSubcompetencias();
            foreach ($preguntas as $pregunta)
            {
                $datos_ec=$pregunta;
                $datos_ec['id_evaluacion']=$id_ed;
                $insert=$this->ed_ec->insert($datos_ec);
            }
            return $insert;

                
        }
}
?>