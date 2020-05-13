<?php
class Workflow extends CI_Controller
{
    private $modulo=15;
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
            if (!($this->user['id']>0)) 
                redirect("admin/admin/index_js","location", 301);
            $this->load->model("gestion_riesgo/gr_usuarios_model","gr_usuarios",true);
            $this->user['area']=$this->gr_usuarios->dameArea( $this->user['id']);
                    
            $this->load->model("permisos_model","permisos_model",true);
            $this->permisos= $this->permisos_model->checkIn($this->user['perfil_id'],$this->modulo);
            $this->load->model("gestion_riesgo/gr_roles_model","gr_roles",true);
            $this->roles= $this->gr_roles->checkIn($this->user['id']);
            $this->load->model("usuarios_model","usuarios",true);
            $this->origen= $this->usuarios->dameOrigen($this->user['id']);
            $arr = $this->_buscarGerencia($this->origen['id_area']);
            $this->origen['id_area_gcia']= $arr['id_area'];
//             echo "<pre>Origen".print_r($this->origen,true)."</pre>";
	}
	
	public function index()
	{
            $modulo = $this->uri->segment(SEGMENTOS_DOM+2);
            if (!isset($modulo)) $modulo=0;
            $this->load->model('permisos_model','permisos_model',true);
            $variables['permiso'] = $this->permisos_model->checkIn($this->user['perfil_id'],$modulo);
            $variables['permiso_permiso'] = $this->permisos_model->checkIn($this->user['perfil_id'],$this->modulo);
            $variables['roles'] = $this->roles;
            $this->load->model("gestion_riesgo/gr_usuarios_model","gr_usuarios",true);
            $permiso_gdr = $this->gr_usuarios->dameUsuarioPermisoGdr($this->user['id']);
//            $variables['u_gr']=$this->gr_usuarios->verificarSiPerteneceGr($this->user['id']);
            $variables['u_gr']=($permiso_gdr != 0)?1:0;
//             echo "<pre>".print_r($variables,true)."</pre>";
            $this->load->view('dms/workflow/listado',$variables);
	}
	
	public function listado()
	{
		if ($this->permisos['Listar'])
                {
                    $listado=array();
                    $id_usuario=$this->user['id'];
                    $this->load->model("dms/usuarios_model","usuarios",true);
                    $usuario=$this->usuarios->dameUsuarioCompleto($id_usuario);
                    $usuario['roles']=$this->roles;
//                    echo "<pre>".print_r($usuario,true)."</pre>";
                    $this->load->model('dms/workflows_model','workflows',true);
                    $start = $this->input->post("start");
//                    $limit = $this->input->post("limit");
                    $limit = 25;
                    $sort = $this->input->post("sort");
                    $dir = $this->input->post("dir");
                    
                    switch ($this->input->post("f_id_estado"))
                    {
                        case -1:
                        case 'Todos':
                            $filtro="";
                            break;
                        case '1':
                        case 1:
                            $filtro=1;
                            break;
                        case '2':
                        case 2:
                            $filtro=2;
                            break;
                        case '3':
                        case 3:
                            $filtro=3;
                            break;
                        case '4':
                        case 4:
                            $filtro=4;
                            break;
                        case '5':
                        case 5:
                            $filtro=5;
                            break;
                        default:
                            $filtro="";
                    }
//                    $filtro = ($this->input->post("f_id_estado")&&$this->input->post("f_id_estado")!=-1)?$this->input->post("f_id_estado"):"";
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
                    
//                    if ($this->roles['Editor']==1)
//                    {
//                        $listadoEditor=$this->workflof->dameEditores($start, $limit, $filtro,$busqueda, $campos, $sort, $dir);
//                    }
//                    if ($this->roles['Revisor']==1)
//                    {}
//                    if ($this->roles['Aprobador']==1)
//                    {}
//                    if ($this->roles['Publicador']==1)
//                    {}
                    
                    
                    
                    $listado = $this->workflows->listado($usuario,$start, $limit, $filtro,$busqueda, $campos, $sort, $dir);
                    echo $listado;
                }
                else
                    echo -1; //No tiene permisos
                
	}
	
	public function nuevoDocumento()
	{
            $this->load->model('permisos_model','permisos_model',true);
            $modulo = $this->uri->segment(SEGMENTOS_DOM+2);
            if (!isset($modulo)) $modulo=0;
            $this->load->model('permisos_model','permisos_model',true);
            $variables['permiso'] = $this->permisos_model->checkIn($this->user['perfil_id'],$modulo);
            $variables['permiso_permiso'] = $this->permisos_model->checkIn($this->user['perfil_id'],$this->modulo);
            $variables['roles'] = $this->roles;
//            echo "<pre>".print_r($variables,true)."</pre>";
            $this->load->view('dms/workflow/alta',$variables);
	}
        
        public function _checkEditor($id_documento,$id_editor)
        {
              $id_rol=1;//Editor
            $this->load->model("dms/documentos_model","documentos");
            $documento=$this->documentos->dameDocumento($id_documento);
//            echo "<pre>".print_r($documento,true)."</pre>";
            
            //verifico que corresponda con dms_usuarios_doc
            $this->load->model("dms/usuarios_doc_model","usuariosDoc",true);
            $checkUsuarioDoc=$this->usuariosDoc->checkUsuarioDoc($id_documento,$id_editor,$id_rol);
            
            //verifico que corresponda con dms_documentos
            $checkDocEditor=($documento['id_usuario_editor']==$id_editor)?true:false;
            
            //verifico que la gcia del documento coincida con la del editor
            $this->load->model("usuarios_model","usuarios",true);
            $area_editor=$this->usuarios->dameIdArea($id_editor);
            $gerencia_editor=$this->_buscarGerencia($area_editor['id_area']);
//            echo "<pre>".print_r($gerencia_editor,true)."</pre>";
            $checkGcia=($documento['id_gerencia_origen']==$gerencia_editor['id_area'])?true:false;
            
//            echo ($checkUsuarioDoc&&$checkDocEditor&&$checkGcia);
            return ($checkUsuarioDoc&&$checkDocEditor&&$checkGcia);
            
        }
        
	public function editDocumento()
	{
            $id_documento= $this->input->post("id");
            $id_usuario=$this->user['id'];
            
            if (!$this->_checkEditor($id_documento,$id_usuario))
            {
//                exit ("({success: false, error :'Su usuario no tiene el rol correspondiente en el documento para la gestión que desea realizar'})"); 
                $this->index();

            }
            else
            {
                $modulo = $this->uri->segment(SEGMENTOS_DOM+2);
                $this->load->model("dms/documentos_model","documentos",true);
                $this->load->model("dms/usuarios_doc_model","revisores",true);
    //            $documento=$this->documentos->dameDocumentoParaEditar($id_documento);
    //            $documento['revisores']=$this->revisores->dameRevisores($id_documento);
                $variables['documento']=$id_documento;
                if (!isset($modulo)) $modulo=0;
                $this->load->model('permisos_model','permisos_model',true);
                $variables['permiso'] = $this->permisos_model->checkIn($this->user['perfil_id'],$modulo);
                $variables['permiso_permiso'] = $this->permisos_model->checkIn($this->user['perfil_id'],$this->modulo);
                $variables['roles'] = $this->roles;
    //            echo "<pre>".print_r($variables,true)."</pre>";
                $this->load->view('dms/workflow/alta',$variables);
            }
	}
	public function dameDocumento()
	{
            $id_documento= $this->input->post("id");
            $this->load->model("dms/documentos_model","documentos",true);
            $this->load->model("dms/usuarios_doc_model","revisores",true);
            $documento=$this->documentos->dameDocumentoParaEditar2($id_documento);
            echo $documento;
//            $documento['revisores']=$this->revisores->dameRevisores($id_documento);
//            $variables['documento']=$documento;
//            if (!isset($modulo)) $modulo=0;
//            $this->load->model('permisos_model','permisos_model',true);
//            $variables['permiso'] = $this->permisos_model->checkIn($this->user['perfil_id'],$modulo);
//            $variables['permiso_permiso'] = $this->permisos_model->checkIn($this->user['perfil_id'],$this->modulo);
//            $variables['roles'] = $this->roles;
//            $this->load->view('dms/workflow/alta',$variables);
//            echo "<pre>".print_r($variables,true)."</pre>";
	}
        public function guardar ()
        {
            $edit=$this->input->post('edit');
            if ($edit==0)
            {
                $this->insert();
                
            }
            else
            {
                $this->update();
            }
        }
        public function insert()
	{
            if ($this->roles['Editor'])		
            {
                $campos_requeridos=array('documento','id_td','tipo_wf','aprobador');
                $control=true;
                foreach ($campos_requeridos as $campo)
                {
                    if($this->input->post($campo)=="")
                    {
                        $control=false;
                        echo 'Revisar campo->'.$campo.".";
                    }
                }
                
                if ($control)
                {
                        $datos_documento=array();
                        $datos_usuarios_revisores=array();
                        $datos_usuarios_aprobador=array();
//                        $datos_usuarios_publicadores=array();
//                        $datos_alcance=array();
                        $id_usuario=$this->user['id'];
                        
                        $this->load->model("usuarios_model","usuarios",true);
                        $area_editor=$this->usuarios->dameIdArea($id_usuario);
//                        echo "<pre>".print_r($area_editor,true)."</pre>";
                        
                        $gerencia_editor=$this->_buscarGerencia($area_editor['id_area']);
//                        echo $area_editor['id_area'];
//                        echo "<pre>".print_r($gerencia_editor,true)."</pre>";die();
                        
                        $datos_documento['id_usuario_editor']=$id_usuario;
                        $datos_documento['id_estado']=1; //en borrador
                        $datos_documento['version']=0;
                        
                        $datos_documento['alcance']=1;
                        $codigo['empresa']='SDJ';
                        
                        $datos_documento['id_usuario_aprobador']=$this->input->post("aprobador");
                        $datos_documento['detalle']=$this->input->post("detalle");
                        $datos_documento['documento']=$this->input->post("documento");
                        $datos_documento['id_td'] =$this->input->post("id_td");
                        $datos_documento['tipo_wf']=$this->input->post("tipo_wf");
                        if($this->input->post("tipo_wf")==2)//wf =2 -> wf largo
                        {
                            $datos_usuarios_revisores= explode(",",$this->input->post("revisores"));
//                            $datos_documento['q_revisores']= $this->input->post("qrevisores");
                            $datos_documento['q_revisores']= count($datos_usuarios_revisores);
                        }
                        else
                        {
                            $datos_documento['q_revisores']= 0;
                        }
                        
                        $datos_documento['id_empresa_origen']=$gerencia_editor['id_empresa'];
                        $datos_documento['id_gerencia_origen']=$gerencia_editor['id_area'];
//                        $datos_documento['id_dpto_origen']=($this->origen['id_departamento']=="")?null:$this->origen['id_departamento'];
                        
                        //buscar el último número para el mismo origen
                        $this->load->model("dms/documentos_model","documentos",true);
                        $qDocs=$this->documentos->maxNroPorOrigen($gerencia_editor['id_area']);
                        
                        if ($qDocs && $qDocs!=NULL)
                            $datos_documento['numero']=$qDocs+1;
                        else
                            $datos_documento['numero']=1;
                            
                        //Genero Código de documento
                        $this->load->model("dms/tiposdoc_model","tiposdoc",true);
                        $tdAbv=$this->tiposdoc->dameTdAbv($datos_documento['id_td']);

                        $version= str_repeat("0",2-strlen($datos_documento['version'])).$datos_documento['version'];
                        $numero= str_repeat("0",4-strlen($datos_documento['numero'])).$datos_documento['numero'];

                        $codigo=$tdAbv."-".$gerencia_editor['abv']."-".$codigo['empresa']."-".$numero."-".$version;

                        $datos_documento['codigo'] =$codigo;

                        $datos_usuarios_aprobador= $this->input->post("aprobador");
//                        $datos_usuarios_publicadores= explode(",",$this->input->post("publicadores"));

        //                $id_doc=$this->documentos->insert($datos_documento);
        //                        echo $id_doc;
                        
//                        echo "<pre>".print_r($datos_documento,true)."</pre>";
//                        die();
                        if($id_doc=$this->documentos->insert($datos_documento))
                        {
                            $n=0;
                            //Usuario Editor
                                $usuarios_doc[$n]['id_usuario']=$this->user['id'];
                                $usuarios_doc[$n]['id_documento']=$id_doc;
                                $usuarios_doc[$n]['id_rol']=1;
                                $n++;
                            //Usuarios Revisores
                             if($this->input->post("tipo_wf")==2)//wf =2 -> wf largo
                            {
                                foreach ($datos_usuarios_revisores as $usuarioR)
                                {
                                    $usuarios_doc[$n]['id_usuario']=$usuarioR;
                                    $usuarios_doc[$n]['id_documento']=$id_doc;
                                    $usuarios_doc[$n]['id_rol']=2;
                                    $usuarios_doc[$n]['activo']=1;
                                    $n++;
                                }
                           }
                            //Usuario Aprobador
                                $usuarios_doc[$n]['id_usuario']=$datos_usuarios_aprobador;
                                $usuarios_doc[$n]['id_documento']=$id_doc;
                                $usuarios_doc[$n]['id_rol']=3;
                                $n++;
                            //Usuarios Publicadores
//                            foreach ($datos_usuarios_publicadores as $usuarioP)
//                            {
//                                $usuarios_doc[$n]['id_usuario']=$usuarioP;
//                                $usuarios_doc[$n]['id_documento']=$id_doc;
//                                $usuarios_doc[$n]['id_rol']=4;
//                                $n++;
//                            }

        //                    echo "<pre>".print_r($usuarios_doc,true)."</pre>";
                            $this->load->model('dms/usuarios_doc_model','usuarios_doc',true);
                            $res=0;
                            foreach ($usuarios_doc as $usuarioD)
                            {
                                if($this->usuarios_doc->insert($usuarioD))
                                    $b=1;
                                else
                                {
                                    $b=2;
                                    break; //error al insertar usuario, salgo del foreach
                                }

                            }

                            echo $b;
                        }
                        else
                            echo 3; //error al insertar el documento
                }
                else 
                    echo 4; //Faltan campos requeridos
            }
            else
                echo -1; //No tiene permisos
    }
        public function update()
	{
            
            $id_documento=$this->input->post("id");
            $id_usuario=$this->user['id'];
            $id_rol=1;
            
            $this->load->model("dms/usuarios_doc_model","usuariosDoc",true);
            $checkUsuarioDoc=$this->usuariosDoc->checkUsuarioDoc($id_documento,$id_usuario,$id_rol);
            
            if ($this->roles['Editor'] && $checkUsuarioDoc)		
            {
                $this->load->model("dms/documentos_model","documentos",true);
                $documento=$this->documentos->dameDocumento($id_documento);
//                echo "<pre>".print_r($documento,true)."</pre>";
                
                
                if ($documento!=0)
                {
                    $campos_requeridos=array('documento','id_td','tipo_wf','aprobador');
                    $control=true;
                    foreach ($campos_requeridos as $campo)
                    {
                        if($this->input->post($campo)=="")
                        {
                            $control=false;
                            echo $campo;
                        }
                    }

                    if ($control)
                    {
                            $datos_documento=array();
                            $datos_usuarios_revisores=array();
                            $datos_usuarios_aprobador=array();
    //                        $datos_usuarios_publicadores=array();
    //                        $datos_alcance=array();

//                            $this->load->model("usuarios_model","usuarios",true);
                            $area_editor=$this->usuarios->dameIdArea($documento['id_usuario_editor']);
//    //                        echo "<pre>".print_r($area_editor,true)."</pre>";
//
                            $gerencia_editor=$this->_buscarGerencia($area_editor['id_area']);
//                            echo "<pre>".print_r($gerencia_editor,true)."</pre>";die();

//                            $datos_documento['id_usuario_editor']=$id_usuario;
//                            $datos_documento['id_estado']=1; //en borrador


//                            $datos_documento['alcance']=$this->input->post("alcance");
                            $datos_documento['alcance']=1;
                            $codigo['empresa']='SDJ';
                            
                            $datos_documento['id_usuario_aprobador']=$this->input->post("aprobador");
                            $datos_documento['detalle']=$this->input->post("detalle");
                            $datos_documento['documento']=$this->input->post("documento");
                            $datos_documento['id_td'] =$this->input->post("id_td");
                            $datos_documento['tipo_wf']=$this->input->post("tipo_wf");
                            if($this->input->post("tipo_wf")==2)//wf =2 -> wf largo
                            {
                                $datos_usuarios_revisores= explode(",",$this->input->post("revisores"));
    //                            $datos_documento['q_revisores']= $this->input->post("qrevisores");
                                $datos_documento['q_revisores']= count($datos_usuarios_revisores);
                            }
                            else
                            {
                                $datos_documento['q_revisores']= 0;
                            }

//                            $datos_documento['id_empresa_origen']=$gerencia_editor['id_empresa'];
//                            $datos_documento['id_gerencia_origen']=$gerencia_editor['gcia'];

                            //buscar el último número para el mismo origen
                            $this->load->model("dms/documentos_model","documentos",true);
                            $qDocs=$documento['numero'];
//                            $datos_documento['numero']=$qDocs+1;

                            //Genero Código de documento
                            $this->load->model("dms/tiposdoc_model","tiposdoc",true);
                            $tdAbv=$this->tiposdoc->dameTdAbv($datos_documento['id_td']);

                            $version= str_repeat("0",2-strlen($documento['version'])).$documento['version'];
                            $numero= str_repeat("0",4-strlen($documento['numero'])).$documento['numero'];

//                            $codigo=$tdAbv."-".$gerencia_editor['origen']."-".$numero."-".$version;
                             $codigo=$tdAbv."-".$gerencia_editor['abv']."-".$codigo['empresa']."-".$numero."-".$version;

                            $datos_documento['codigo'] =$codigo;

                            $datos_usuarios_aprobador= $this->input->post("aprobador");
    //                        $datos_usuarios_publicadores= explode(",",$this->input->post("publicadores"));

            //                $id_doc=$this->documentos->insert($datos_documento);
            //                        echo $id_doc;

//                            echo "<pre>".print_r($datos_documento,true)."</pre>";
    //                        die();
                            if($this->documentos->update($id_documento,$datos_documento))
                            {
                                //deshabilito los usuarios Revisores y aprobador para crear los nuevos
                                $this->load->model('dms/usuarios_doc_model','usuarios_doc',true);
                                $deshabilitar['id_documento']=$id_documento;
                                $deshabilitar['id_rol']=array(2,3); //Roles: 2->Editor; 3->Aprobador
                                $this->usuarios_doc->deshabilitarPorRol($deshabilitar);
                                
                                $n=0;
                                //Usuarios Revisores
                                if($this->input->post("tipo_wf")==2)//wf =2 -> wf largo
                                {
                                    foreach ($datos_usuarios_revisores as $usuarioR)
                                    {
                                        $usuarios_doc[$n]['id_usuario']=$usuarioR;
                                        $usuarios_doc[$n]['id_documento']=$id_documento;
                                        $usuarios_doc[$n]['id_rol']=2;
                                        $usuarios_doc[$n]['activo']=1;
                                        $n++;
                                    }
                            }
                                //Usuario Aprobador
                                    $usuarios_doc[$n]['id_usuario']=$datos_usuarios_aprobador;
                                    $usuarios_doc[$n]['id_documento']=$id_documento;
                                    $usuarios_doc[$n]['id_rol']=3;
                                    $n++;
                                //Usuarios Publicadores
    //                            foreach ($datos_usuarios_publicadores as $usuarioP)
    //                            {
    //                                $usuarios_doc[$n]['id_usuario']=$usuarioP;
    //                                $usuarios_doc[$n]['id_documento']=$id_doc;
    //                                $usuarios_doc[$n]['id_rol']=4;
    //                                $n++;
    //                            }

            //                    echo "<pre>".print_r($usuarios_doc,true)."</pre>";
                                $res=0;
                                foreach ($usuarios_doc as $usuarioD)
                                {
                                    if($this->usuarios_doc->insert($usuarioD))
                                        $b=1;
                                    else
                                    {
                                        $b=2;
                                        break; //error al insertar usuario, salgo del foreach
                                    }

                                }

                                echo $b;
                            }
                            else
                                echo 3; //error al insertar el documento
                    }
                    else 
                        echo 4; //Faltan campos requeridos
                    
                }
                else
                    echo -2;
                
            }
            else
                echo -10; //No tiene permisos
    }
//	public function modificar()
//	{
//            if ($this->permisos['Modificacion'])
//            {
//                    $campo=json_decode($_POST["campo"]);
//                    $valor=json_decode($_POST["valor"]);
//                    $this->load->model('dms/documentos_model','documentos',true);
////                    if($campo[0]=="gerencia")
////                        $datos[$campo[0]]=$valor[0];
////                    if($campo[0]=="abv")
////                        $datos[$campo[0]]=strtoupper($valor[0]);
////                    if($campo[0]=="empresa")
////                    {
////                        $campo[0]='id_empresa';
////                        $datos[$campo[0]]=$valor[0];
////                    }
//                            
//                    if(!$this->documentos->edit($_POST["id"],$datos))
//                        echo 0;
//                    else
//                        echo 1;
//            }
//	}
	
	public function grabar_obs()
	{
            if ($this->permisos['Alta'])		
            {
                $datos=array();
                
                $datos['id_usuario']      =$this->user['id'];
                $datos['id_documento']    =  $this->input->post("id_documento");
                $datos['obs']             =$this->input->post("texto");
                $datos['obs_wf']          =1;
                $this->load->model('dms/observaciones_model','observaciones',true);
                //antes de insertar verifico que no exita el registro
                if($this->observaciones->insert($datos))
                    echo 1; //todo OK
                else
                    echo 2; //Error al insertar el registro
            }
	}
        public function combo_twf()
	{
            $tw[0]=array('tipo_wf'=>1,'wf'=>'Corto');
            $tw[1]=array('tipo_wf'=>2,'wf'=>'Largo');            
            $var='({"total":2,"rows":'.json_encode($tw).'})';
            echo $var;	
	}
        public function combo_td()
	{
            $this->load->model('dms/tiposdoc_model','tiposdoc',true);
            $var=$this->tiposdoc->dameComboTD();
            echo $var;	
	}
        public function combo_estados()
	{
            $this->load->model('dms/dms_estados_model','estados',true);
            $var=$this->estados->dameComboFiltroWf();
            echo $var;	
	}
        public function combo_td_publicados()
	{
            $this->load->model('dms/documentos_model','documentos',true);
            $var=$this->documentos->dameComboTDPublicados();
            echo $var;	
	}
        public function combo_revisores()
	{
            $limit=$this->input->post("limit");
            $start=$this->input->post("start");
            $query=$this->input->post("query");
            $this->load->model("dms/workflows_model","workflows",true);
            $jcode = $this->workflows->usuariosCombo($limit,$start,$query,'Revisor');
            echo $jcode;
	}
        public function combo_delegar()
	{
            $id_documento=$this->input->post("id_documento");
            $id_usuario=$this->user['id'];
            
            $this->load->model('dms/documentos_model','documentos',true);
            $documento=$this->documentos->dameDocumento($id_documento);
            
            $limit=$this->input->post("limit");
            $start=$this->input->post("start");
            $query=$this->input->post("query");
            $this->load->model("dms/workflows_model","workflows",true);
            $jcode = $this->workflows->dameComboEditoresPorGerencia($documento['id_gerencia_origen'],$limit,$start,$query,$documento['id_usuario_editor']);
            echo $jcode;
	}
        public function combo_aprobadores()
	{
            $limit=$this->input->post("limit");
            $start=$this->input->post("start");
            $query=$this->input->post("query");
            $this->load->model("dms/workflows_model","workflows",true);
            $jcode = $this->workflows->usuariosCombo($limit,$start,$query,'Aprobador');
            echo $jcode;
	}
        public function combo_publicadores()
	{
            $limit=$this->input->post("limit");
            $start=$this->input->post("start");
            $query=$this->input->post("query");
            $this->load->model("dms/workflows_model","workflows",true);
            $jcode = $this->workflows->usuariosCombo($limit,$start,$query,'Publicador');
            echo $jcode;
	}
        public function empresas_combo()
	{
            $this->load->model('departamentos_model','departamentos',true);
            $var=$this->departamentos->dameComboEmpresas();
            echo $var;	
	}
        public function gerencias_combo()
	{
            $id_empresa= $this->input->post("id_empresa");
            $this->load->model('departamentos_model','departamentos',true);
            $var=$this->departamentos->dameComboGerencias($id_empresa);
            echo $var;	
	}
        public function upload()
        {
            if ($this->roles['Editor'])		
            {
                if ($this->permisos['Modificacion'])
                {
                    $id_documento=$this->input->post("id_documento");
                    $id_usuario=$this->user['id'];
                    if ($this->_checkEditor($id_documento,$id_usuario))
                    {
                        $this->load->model("dms/documentos_model","documento",true);
                        $doc=$this->documento->dameDocumento($id_documento);
                        if ($doc['id_estado']!=1 && $doc['id_estado']!=2)
                        {
                            exit ("({success: false, error :'Estado del documento incompatible'})"); 
                        }

                        $id_rol_actual=1;//Editor
                        //verifico que sea el ediutor del documento;
                        $this->load->model("dms/usuarios_doc_model","usuariosDoc",true);
                        $checkUsuarioDoc=$this->usuariosDoc->checkUsuarioDoc($id_documento,$id_usuario,$id_rol_actual);
                        if (!$checkUsuarioDoc)
                        {
                            exit ("({success: false, error :'Su usuario no tiene el rol correspondiente en el documento para la gestión que desea realizar'})"); 
                        }
    //                    $tipo=$this->input->post("col");
                        $tipo='PDF';
    //                    if ($tipo=='PDF')
    //                    {
    //                        $pre='PDF';
    //                        $ext='.pdf';
                            $type='pdf|PDF';
                            $campoBD='archivo';
    //                    }
    //                    elseif ($tipo=='Fuente') 
    //                    {
    //                        $pre='AFX';
    //                        $ext='.doc';
    //                        $type='doc|DOC|docx|DOCX';
    //                        $campoBD='archivo_fuente';
    //                    
    //                    }
    //                    $nro= $pre.str_repeat('0',7-strlen($id_documento)).$id_documento."_";
    //                      $this->load->helper('string');
    //                    $nombre_archivo=$nro.md5(KEYMD5.$id_documento).$ext;
                        $nombre_archivo=$this->_nombreArchivo($id_documento, $tipo);
                        $upload=$this->_do_upload('archivo',$nombre_archivo,$type);
    //                    echo "<pre>".print_r($upload,true)."</pre>";
                        //si hubo errores al subir el archivo
                            if (array_key_exists('error',$upload))
                            {
                                 if(strpos($upload['error'],'The filetype you are attempting to upload is not allowed')!==false)		
                                    $jsonData = "({success: false, error :'Se ha producido un error. Verifique el tipo de archivo seleccionado'})"; //error el archivo no corresponde al tipo permitido
                                 elseif (strpos($upload['error'], 'The file you are attempting to upload is larger than')!==false)		
                                    $jsonData = "({success: false, error :'Se ha producido un error. Verifique el tamaño del archivo seleccionado'})"; //error el archivo es muy pesado
                                 else
                                    $jsonData = "({success: false, error :'".(strpos($upload['error'], 'The file you are attempting to upload is larger than')!==false)."'})"; //error al subir el archivo
                                 echo $jsonData;
                            }
                            else
                            {
                                //preparo datos para insertar en la base de datos
                                $datos[$campoBD]            =$upload['upload_data']['file_name'];
                                $datos[$campoBD.'_nom_orig']=$upload['upload_data']['client_name'];
        //                                    $datos['id_documento']        = $id_documento;

                                    $this->load->model("dms/documentos_model","documentos",true);
                                    if ($this->documentos->update($id_documento,$datos))
                                    {
                                        $jsonData = "({success: true, error :0})"; 		
                                        echo $jsonData;
                                    }
                                    else
                                    {
                                        $jsonData = "({success: false, error :2})"; //error al insertar en base de datos		
                                        echo $jsonData;
                                        //----->>>COMPLETAR ELIMINANDO EL ARCHIVO SUBIDO<<<<<--------------
                                    }

                            }
                        
                    }
                    else
                    {
                        $jsonData = "({success: false, error :'Editor Check inválido'})"; //error de permiso, no tiene rol Editor		
                            echo $jsonData;
                    }
                }
            }//fin  if ($this->roles['Editor'])	
            else {
                $jsonData = "({success: false, error :-1})"; //error de permiso, no tiene rol Editor		
                    echo $jsonData;
            }
        }
        function _do_upload($field_name,$nombre_archivo,$type='pdf|PDF')
	{
		$config['upload_path']  = './uploads/dms/documentos/';
		$config['allowed_types']= $type;
		$config['max_size']	= '5120'; //5MB Max
		$config['remove_spaces']= true;
                $config['overwrite'] = true;
		$config['encrypt_name'] = false;
		$config['file_name'] =$nombre_archivo;
		$this->load->library('upload', $config);
	
		if ( ! $this->upload->do_upload($field_name))
		{
			$error = array('error' => $this->upload->display_errors());
			return $error;
		}	
		else
		{
			$data = array('upload_data' => $this->upload->data());
			return $data;
		}
	}
        function preview($id_documento)
        {
            //Controla que el usuario tenga permisos para ver el documento
            $id_usuario=$this->user['id'];
//             echo "<pre>".print_r($this->user,true)."</pre>";die();
            $this->load->model("dms/usuarios_doc_model","usuarios_doc",true);
            $check=$this->usuarios_doc->checkUsuarioDoc($id_documento,$id_usuario);
            if ($check || $this->user['area']['gr']==1)
            {
                
    //            $id_documento=$this->input->post("documento");
    //            $id_documento=1;
                $this->_generaDocumentoPdf($id_documento,0);
            }
            else{
                $jsonData = "<p><h3>Error: Su usuario no tiene permisos sobre el documento solicitado</h3></p>"; //error de permisos, el susuario no esta como integrante habilitado del wf para este documento		
                    echo $jsonData;
            }
                
            
            
        }
        function _generaDocumentoPdf($id_documento,$save=false,$print=false)
        {
            $this->load->model("dms/documentos_model","documentos",true);
            $documento=$this->documentos->dameDocumento($id_documento);
//            echo "<pre>".print_r($documento,true)."</pre>";die();
//            $this->load->model("empresas_model","empresas",true);
//            $logo=$this->empresas->damelogo($documento['id_empresa_origen']);
//            echo "<pre>".print_r($logo,true)."</pre>";die();
            
            $this->load->model("dms/gestiones_model","gestiones",true);
            $editor=$this->gestiones->DameEditor($id_documento);
            $aprobador=$this->gestiones->DameAprobador($id_documento);
            if ($documento['tipo_wf']==1) //tipo_wf==1 => WF Corto
                $revisores=$aprobador;
            else
                $revisores=$this->gestiones->DameRevisores($id_documento);
            
            $this->load->library('Pdf_wf');
            $pdf = new Pdf_wf('P', 'mm', 'A4', true, 'UTF-8', false);
//            $archivo=PATH_DMS_DOCS.$nro.md5(KEYMD5."$id_documento").".pdf";
            $archivo=PATH_DMS_DOCS.$this->_nombreArchivo($id_documento,'PDF');
            $ArchivoDMS=  str_replace('.pdf', '_dms.pdf', $archivo);
            $pageCount = $pdf->setSourceFile($archivo);
            //Open the pdf as a text file and count the number of times "/Page" occurs.
//            function count_pages($pdfname) {
//                $pdftext = file_get_contents($pdfname);
//                $num = preg_match_all("/\/Page\W/", $pdftext, $dummy);
//                return $num;
//            }
//            echo count_pages($archivo);
            
            $variables['pdf']=$pdf;
            
            //datos
            $variables['nomArchivoDMS']=$ArchivoDMS;
            $variables['output']=($save)?'F':'I';
//            $variables['logo']=URL_BASE.'images/'.$logo;
            $variables['logo']=PATH_DMS_IMAGES.'logo_'.((int)$documento['alcance']+1).'.jpg';
//            $variables['logo']=(int)$documento['alcance'];
            $variables['marcaAgua']=PATH_DMS_IMAGES.'ma1.png';
            $variables['titulo']=$documento['documento'];
            $variables['codigo']=$documento['codigo'];
            $variables['vigencia']=($documento['fecha_public']==null)?"":$documento['fecha_public'];
            $variables['estado']=$documento['id_estado'];
            $variables['paginas']=$pageCount;
            $variables['barcode']=1000000+$documento['id_documento'];
            $variables['print']=$print;
            $variables['edito']=$editor;
            $variables['reviso']=$revisores;
            $variables['aprobo']=$aprobador;
            
             $this->load->view('dms/pdf/documento',$variables);
            
            
        }
        function pruebaManual($id_documento, $id_gestion)
        {
            $estado_rol=array(1=>1,2=>1,3=>2,4=>3,5=>4,6=>1); //id_estado y el rol que puede actuar sobre cada estado
            $id_usuario=$this->user['id'];
            
            $this->load->model("dms/documentos_model","documentos");
            $documento=$this->documentos->dameDocumento($id_documento);
            $id_estado_actual=$documento['id_estado'];
            
            $id_rol_actual=$estado_rol[$id_estado_actual];
            
            switch ($documento['tipo_wf'])
            {
                //wf corto
                case 1:
                    $idEstadoActual_idEstadoSiguiente=array(1=>4,2=>4,4=>5,5=>6,6=>7);
                    $idGestionActual_idRolSiguiente=array(2=>3,4=>4,5=>'todos',6=>1);
                    break;
                //wf largo
                case 2:
                    $idEstadoActual_idEstadoSiguiente=array(1=>3,2=>3,3=>4,4=>5,5=>6,6=>7);
                    $idGestionActual_idRolSiguiente=array(2=>2,3=>3,4=>4,5=>'todos',6=>1);
                    break;
            }
            $id_estado_siguiente=$idEstadoActual_idEstadoSiguiente[$id_estado_actual];
            $id_rol_siguiente=$idGestionActual_idRolSiguiente[$id_gestion];
            
            $this->load->model("dms/roles_model");
            $rol_actual=$this->roles_model->dameRol($id_rol_actual);
           
            //verifico si tiene rol habilitado para la gestion que desea realizar
            if(!$this->roles[$rol_actual])
               echo "({success: false, error :'Su usuario no tiene el rol correspondiente para la gestión que desea realizar'})"; //error de permiso, no tiene el rol correspondiente
            
            //verifico si tiene rol en el documento para la gestion que desea realizar salvo que sea publicador
            if($id_rol_actual!=4)
            {
                $this->load->model("dms/usuarios_doc_model","usuariosDoc",true);
                $checkUsuarioDoc=$this->usuariosDoc->checkUsuarioDoc($id_documento,$id_usuario,$id_rol_actual);
                if (!$checkUsuarioDoc)
                    echo "({success: false, error :'Su usuario no tiene el rol correspondiente en el documento para la gestión que desea realizar'})"; //error de permiso, no tiene el rol correspondiente en el documento
            }
           
            //verifico que el documento este en el estado que corresponde
            $estado_gestion=array(1=>2,2=>2,3=>3,4=>4, 5=>5); //id_estado vs la gestiòn  que se pueden realizar sobre ese estado
            
            $gestion_actual=$estado_gestion[$documento['id_estado']];
            
            if($gestion_actual!=$id_gestion)
                echo "({success: false, error :'El estado del documento ha cambiado antes de su gestión'})"; //error el estado del documento no concuerda con la gestion solicitada
           
            
            //agrego en dms_gestiones
            $this->load->model("usuarios_model","usuarios",true);
            $area_usuario=$this->usuarios->dameIdArea($id_usuario);
//            echo "<pre>".print_r($area_editor,true)."</pre>";

            $gerencia_usuario=$this->_buscarGerencia($area_usuario['id_area']);
//            echo "<pre>".print_r($gerencia_editor,true)."</pre>";
            
            
            $datosGestion=array();
            $datosGestion['id_documento']=$id_documento;
            $datosGestion['id_usuario']=$id_usuario;
            $datosGestion['id_rol']=$id_rol_actual;
            $datosGestion['id_tg']=$id_gestion;
            $datosGestion['ciclo_wf']=$documento['ciclos_wf'];
            $datosGestion['id_gerencia']=$gerencia_usuario['gcia'];
//            echo"<pre>".print_r($datosGestion,true)."</pre>";
            $this->load->model("dms/gestiones_model","gestiones",true);
//            $this->gestiones->insert($datosGestion);
            
            //Si la gestion es REVISION verifico si es la última 
//            if($id_gestion==3)
//            {
//                $q_rev=$this->gestiones->contarRevisiones($id_documento);
//                if($q_rev<$documento['q_revisores'])
//                {
//                    $pendientes=$documento['q_revisores']-$q_rev;
//                    return "({success: true, error :0, revisores:$pendientes})"; //salgo notificando la cantidad de revisores pendiente
//                }
//                
//            }
                
            //cambio ultimo estado en dms_documentos
            $datos=array();
            $datos['id_estado']=$id_estado_siguiente;
            
            //si esta Publicando ademas saco la marca en_wf
            if($id_estado_siguiente==6)
            {
                $datos['en_wf']=0;
                $datos['fecha_public']=date("Y-m-d");
            }
//            $this->documentos->update($id_documento,$datos);
            
            //Preparo Mail
            $this->load->model("dms/documentos_model","documentos",true);
            $this->load->model("dms/dms_usuarios_model","dms_usuarios",true);
            $this->load->model("dms/usuarios_doc_model","usuarios_doc",true);
            $mail['documento']=$this->documentos->dameDatosParaMailing($id_documento);

             //busco destinatarios para mail
            //Editor, Revisores u Aprobador
            echo $id_rol_siguiente."<br>";
            if($id_rol_siguiente<=3)
            {
                $mail['destinatarios']=$this->usuarios_doc->dameDestinatariosMailing($id_documento,$id_rol_siguiente); 
                $mail['publicadores']=$this->dms_usuarios->dameEmailPublicadoresHabilitados($mail['documento']['alcance']);//Publicadores
            }
            //Publicadores o todos
            else
            {
                //si el documento esta siendo publicado => envio mail a todos los integrantes
                if($id_estado_siguiente==6 && $id_rol_siguiente=='todos')
                {
//                    $this->load->model("dms/usuarios_doc_model","usuarios_doc",true);
                    $mail['destinatarios']=$this->usuarios_doc->dameEmailParticipantes($id_documento,$id_rol_siguiente);  //todos los que participaron
                    $mail['publicadores']=$this->dms_usuarios->dameEmailPublicadoresHabilitados($mail['documento']['alcance']);//Publicadores
                }
                //A los publicadores
                else
                {
                    $mail['destinatarios']=$this->dms_usuarios->dameEmailPublicadoresHabilitados($mail['documento']['alcance']);//Publicadores
                    $mail['publicadores']=$this->dms_usuarios->dameEmailPublicadoresHabilitados($mail['documento']['alcance']);//Publicadores
                }
                    
            }
                
            //envio mail
            $envio=0;
//             echo"<pre>".print_r($mail,true)."</pre>";
            $envio=$this->_enviarMail($mail);
           
                echo "({success: true, error :0, email :$envio})"; //error el estado del documento no concuerda con la gestion solicitada
            
        }
        //funcion para Liberar, Revisar y Aprobar
        function gestion($id_documento, $id_gestion)
        {
            $estado_rol=array(1=>1,2=>1,3=>2,4=>3,5=>4); //id_estado y el rol que puede actuar sobre cada estado
            $id_usuario=$this->user['id'];
            
            $this->load->model("dms/documentos_model","documentos");
            $documento=$this->documentos->dameDocumento($id_documento);
            $id_estado_actual=$documento['id_estado'];
            
            $id_rol_actual=$estado_rol[$id_estado_actual];
            
            switch ($documento['tipo_wf'])
            {
                //wf corto
                case 1:
                    $idEstadoActual_idEstadoSiguiente=array(1=>4,2=>4,4=>5,5=>6,6=>7);
                    $idGestionActual_idRolSiguiente=array(2=>3,4=>4,5=>'todos',6=>1);
                    break;
                //wf largo
                case 2:
                    $idEstadoActual_idEstadoSiguiente=array(1=>3,2=>3,3=>4,4=>5,5=>6,6=>7);
                    $idGestionActual_idRolSiguiente=array(2=>2,3=>3,4=>4,5=>'todos',6=>1);
                    break;
            }
            $id_estado_siguiente=$idEstadoActual_idEstadoSiguiente[$id_estado_actual];
            $id_rol_siguiente=$idGestionActual_idRolSiguiente[$id_gestion];
            
            $this->load->model("dms/roles_model");
            $rol_actual=$this->roles_model->dameRol($id_rol_actual);
           
            //verifico si tiene rol habilitado para la gestion que desea realizar
            if(!$this->roles[$rol_actual])
               return "({success: false, error :'Su usuario no tiene el rol correspondiente para la gestión que desea realizar'})"; //error de permiso, no tiene el rol correspondiente
            
            //verifico si tiene rol en el documento para la gestion que desea realizar salvo que sea publicador
            if($id_rol_actual!=4)
            {
                $this->load->model("dms/usuarios_doc_model","usuariosDoc",true);
                $checkUsuarioDoc=$this->usuariosDoc->checkUsuarioDoc($id_documento,$id_usuario,$id_rol_actual);
                if (!$checkUsuarioDoc)
                    return "({success: false, error :'Su usuario no tiene el rol correspondiente en el documento para la gestión que desea realizar'})"; //error de permiso, no tiene el rol correspondiente en el documento
            }
           
            //verifico que el documento este en el estado que corresponde
            $estado_gestion=array(1=>2,2=>2,3=>3,4=>4, 5=>5); //id_estado vs la gestiòn  que se pueden realizar sobre ese estado
            
            $gestion_actual=$estado_gestion[$documento['id_estado']];
            
            if($gestion_actual!=$id_gestion)
                return "({success: false, error :'El estado del documento ha cambiado antes de su gestión'})"; //error el estado del documento no concuerda con la gestion solicitada
           
            
            //agrego en dms_gestiones
            $this->load->model("usuarios_model","usuarios",true);
            $area_usuario=$this->usuarios->dameIdArea($id_usuario);
//            echo "<pre>".print_r($area_editor,true)."</pre>";

            $gerencia_usuario=$this->_buscarGerencia($area_usuario['id_area']);
//            echo "<pre>".print_r($gerencia_editor,true)."</pre>";
            
            
            $datosGestion=array();
            $datosGestion['id_documento']=$id_documento;
            $datosGestion['id_usuario']=$id_usuario;
            $datosGestion['id_rol']=$id_rol_actual;
            $datosGestion['id_tg']=$id_gestion;
            $datosGestion['ciclo_wf']=$documento['ciclos_wf'];
            $datosGestion['id_gerencia']=$gerencia_usuario['gcia'];
//            echo"<pre>".print_r($datosGestion,true)."</pre>";
            $this->load->model("dms/gestiones_model","gestiones",true);
            $this->gestiones->insert($datosGestion);
            
            //Si la gestion es REVISION verifico si es la última 
            if($id_gestion==3)
            {
                $q_rev=$this->gestiones->contarRevisiones($id_documento);
                if($q_rev<$documento['q_revisores'])
                {
                    $pendientes=$documento['q_revisores']-$q_rev;
                    return "({success: true, error :0, revisores:$pendientes})"; //salgo notificando la cantidad de revisores pendiente
                }
                
            }
                
            //cambio ultimo estado en dms_documentos
            $datos=array();
            $datos['id_estado']=$id_estado_siguiente;
            
            //si esta Publicando ademas saco la marca en_wf y guardo fecha y usuario publicador
            if($id_estado_siguiente==6)
            {
                $datos['en_wf']=0;
                $datos['fecha_public']=date("Y-m-d");
                $datos['id_usuario_public']=$id_usuario;
            }
            $this->documentos->update($id_documento,$datos);
            
            //Preparo Mail
            $this->load->model("dms/documentos_model","documentos",true);
            $this->load->model("dms/dms_usuarios_model","dms_usuarios",true);
            $this->load->model("dms/usuarios_doc_model","usuarios_doc",true);
            $mail['documento']=$this->documentos->dameDatosParaMailing($id_documento);

             //busco destinatarios para mail
            //Editor, Revisores u Aprobador
            if($id_rol_siguiente<=3)
            {
                $mail['destinatarios']=$this->usuarios_doc->dameDestinatariosMailing($id_documento,$id_rol_siguiente); 
                $mail['publicadores']=$this->dms_usuarios->dameEmailPublicadoresHabilitados($mail['documento']['alcance']);//Publicadores
            }
            //Publicadores o todos
            else
            {
                //si el documento esta siendo publicado => envio mail a todos los integrantes
                if($id_estado_siguiente==6 && $id_rol_siguiente=='todos')
                {
                    $this->load->model("dms/usuarios_doc_model","usuarios_doc",true);
                    $mail['destinatarios']=$this->usuarios_doc->dameEmailParticipantes($id_documento,$id_rol_siguiente);  //todos los que participaron
                    $mail['publicadores']=$this->dms_usuarios->dameEmailPublicadoresHabilitados($mail['documento']['alcance']);//Publicadores
                }
                else
                {
                    $this->load->model("usuarios_model","usuarios",true);
                    $mail['destinatarios']=$this->dms_usuarios->dameEmailPublicadoresHabilitados($mail['documento']['alcance']);//Publicadores
                    $mail['publicadores']=$this->dms_usuarios->dameEmailPublicadoresHabilitados($mail['documento']['alcance']);//Publicadores
                }
                    
            }
                
            //envio mail
            $envio=0;
             
            $envio=$this->_enviarMail($mail);
           
                return "({success: true, error :0, email :$envio})"; //error el estado del documento no concuerda con la gestion solicitada
           
        }//fin gestion()
 
//        function delegar()
//        {
//              //verifico que tenga por lo menos un rol
//              if ($this->roles['Editor'] || $this->roles['Revisor'] || $this->roles['Aprobador'] || $this->roles['Publicador'])
//              {
//                    $id_documento=$this->input->post("id_documento");
//                    $this->load->model('dms/documentos_model','documentos',true);
////                    $estado=$this->documentos->dameUltimoEstado($id_documento);
//                    $documento=$this->documentos->dameDocumento($id_documento);
//
//                    $id_estado_doc=$documento['id_estado'];
//
//                    $id_usuario=$this->user['id'];
//
//                    $id_usuario_nuevo=$this->input->post("id_delegado");
//                    $usuario_nuevo=$this->input->post("delegado");
//
//                    $estado_rol=array(1=>1,2=>1,3=>2,4=>3,5=>4);
//    //                    echo"<pre>".print_r($this->user,true)."</pre>";
//                    $id_rol=$estado_rol[$id_estado_doc];
//
//                    //verifico si posee ese rol en el documento
//                    $this->load->model("dms/usuarios_doc_model","usuariosDoc",true);
//                    if ($this->usuariosDoc->checkUsuarioDoc($id_documento,$id_usuario,$id_rol))
//                    {
//                        //verifico que el documento este en ese estado
//                            $this->load->model("dms/documentos_model","documentos",true);
//                            $estado=$this->documentos->dameUltimoEstado($id_documento);
//    //                            echo"<pre>".print_r($estado,true)."</pre>";
//                            if ($estado['id_estado']==$id_estado_doc)
//                            {
//                                 
//                                //deshabilito el usuario que delego
//                                $datos['id_documento']=$id_documento;
//                                $datos['id_usuario']=$id_usuario;
//                                $datos['id_rol']=$id_rol;
//                                $this->usuariosDoc->deshabilitar($datos);
//
//                                //inserto el nuevo usuario
//                                $datos['id_usuario']=$id_usuario_nuevo;
//                                $datos['id_padre']=$id_usuario;
//                                $this->usuariosDoc->insert($datos);
//
//                                //si es editor o aprobador actualizo la tabla documentos
//                                if($id_rol==1 || $id_rol==3)
//                                {
//                                    if($id_rol==1)
//                                        $update['id_usuario_editor']=$id_usuario_nuevo;
//                                    else 
//                                        $update['id_usuario_aprobador']=$id_usuario_nuevo;
//                                    $this->documentos->update($id_documento,$update);
//                                }
//                                
//                                //Grabo en gestiones
//                                $datosGestion['id_documento']=$id_documento;
//                                $datosGestion['id_usuario']=$id_usuario;
//                                $datosGestion['id_rol']=$id_rol;
//                                $datosGestion['id_tg']=7;
//                                $datosGestion['id_gerencia']=$this->origen['id_gerencia'];
//                                $datosGestion['detalle']='Usuario delegado:'.$usuario_nuevo;
//    //                            echo"<pre>".print_r($datosGestion,true)."</pre>";
//                                $this->load->model("dms/gestiones_model","gestiones",true);
//                                $this->gestiones->insert($datosGestion);
//                                
//                                //Busco mail destinatario y envio mail
//                                
//                                $jsonData = "({success: true, error :0})"; 		
//                                echo $jsonData;
//                            }
//                            else
//                            {
//                            $jsonData = "({success: false, error :'Antes de su delegacion el documento a cambiado de estado'})"; //error no concuerda el estado del doc		
//                            echo $jsonData;
//                            }
//                    }
//                    else
//                    {
//                        $jsonData = "({success: false, error :'Usted no tiene permiso asignado para delegar este documento'})"; //error de permiso, no tiene el rol correspondiente en el documento		
//                        echo $jsonData;
//                    }
//            }
//              else 
//              {
//                    $jsonData = "({success: false, error :-1})"; //error de permiso, no tiene el rol correspondiente		
//                    echo $jsonData;
//              }
//        }
        function delete()
        {
              //verifico si es editor
              if ($this->roles['Editor'])
              {
                    $id_documento=$this->input->post("id");
                    $id_usuario=$this->user['id'];
//                    $tipo=$this->input->post("col");
//                    echo"<pre>".print_r($this->user,true)."</pre>";
//                    
                    //verifico si es editor del documento
                    $this->load->model("dms/usuarios_doc_model","usuariosDoc",true);
                    if ($this->_checkEditor($id_documento,$id_usuario))
                    {
                        //verifico que el documento este en estado borrador o rechazado (1 y 2)
                         $this->load->model("dms/documentos_model","documentos",true);
                         $estado=$this->documentos->dameUltimoEstado($id_documento);
//                            echo"<pre>".print_r($estado,true)."</pre>";
                         if ($estado['id_estado']==1 || $estado['id_estado']==2)
                         {
                            //actualizo BD y borro el archivo físico
//                            if ($tipo=='PDF')
//                            {
                                $datos['archivo']="";
                                $datos['archivo_nom_orig']="";
//                                $pre='PDF';
//                                $ext='.pdf';
                                $campoBD='archivo';
                                
//                            }
//                            elseif($tipo=='Fuente')
//                            {
//                                $datos['archivo_fuente']="";
//                                $datos['archivo_fuente_nom_orig']="";
////                                $pre='AFX';
////                                $ext='.doc';
//                                $campoBD='archivo_fuente';
//
//                            }
//                            else
//                                exit('({success: false, error :4})');
                            $this->documentos->update($id_documento,$datos);
                            
//                            $nro= $pre.str_repeat('0',7-strlen($id_documento)).$id_documento."_"; 
                            $archivo=PATH_DMS_DOCS.$this->_nombreArchivo($id_documento);
                            if(unlink($archivo))
                            {
                                $jsonData = "({success: true, error :0})"; 		
                                echo $jsonData;
                            }
                            else
                            {
                                $jsonData = "({success: false, error :4})"; //error al intentar eliminar el archivo		
                                echo $jsonData;
                            }
                         }
                         else{
                            $jsonData = "({success: false, error :3})"; //error no concuerda el estado del doc para la accion a realizar		
                            echo $jsonData;
                        }
                    }
                    else
                    {
                        $jsonData = "({success: false, error :2})"; //error de permiso, no tiene el rol correspondiente en el documento		
                        echo $jsonData;
                    }
              }
              else 
              {
                    $jsonData = "({success: false, error :1})"; //error de permiso, no tiene el rol correspondiente		
                    echo $jsonData;
              }
        }
        function rechazar()
        {
            if ($this->roles['Revisor']||$this->roles['Aprobador']||$this->roles['Publicador'])
              {
                    $id_documento=$this->input->post("id");
                    $texto=$this->input->post("texto");
                    $id_usuario=$this->user['id'];
//                    echo"<pre>".print_r($this->user,true)."</pre>";
//                    echo"<pre>".print_r($this->roles,true)."</pre>";die();
                    $estado_rol=array(1=>1,2=>1,3=>2,4=>3,5=>4); //id_estado y el rol que puede actuar sobre cada estado

                    $this->load->model("dms/documentos_model","documentos");
//                    $estado=$this->documentos->dameUltimoEstado($id_documento);
                    $documento=$this->documentos->dameDocumento($id_documento);
                    $id_estado_actual=$documento['id_estado'];
                    $id_rol_actual=$estado_rol[$id_estado_actual];
                    //verifico si es editor del documento
                    $this->load->model("dms/usuarios_doc_model","usuariosDoc",true);
                    if ($this->usuariosDoc->checkUsuarioDoc($id_documento,$id_usuario,$id_rol_actual) || ($id_estado_actual==5 && $this->roles['Publicador']))
                    {
                        //verifico que el documento este en estado en Revisión, En Aprobación o A Publicar (3,4,5)
//                         $estado=$this->documentos->dameUltimoEstado($id_documento);
//                            echo"<pre>".print_r($this->origen,true)."</pre>";
                         if ($id_estado_actual==3||$id_estado_actual==4||$id_estado_actual==5)
                         {
                            //rechazo el documento: cambio ultimo estado y grabo en dms_gestiones
                            $datos['id_estado']=2;
                            $datos['ciclos_wf']=$documento['ciclos_wf']+1;
                            $this->documentos->update($id_documento,$datos);
                            
                            $this->load->model("usuarios_model","usuarios",true);
                            $area_usuario=$this->usuarios->dameIdArea($id_usuario);
    //                        echo "<pre>".print_r($area_editor,true)."</pre>";
                        
                            $gerencia_usuario=$this->_buscarGerencia($area_usuario['id_area']);
    //                        echo "<pre>".print_r($gerencia_editor,true)."</pre>";
                            
                            $datosGestion=array();
                            $datosGestion['id_documento']=$id_documento;
                            $datosGestion['id_usuario']=$id_usuario;
                            $datosGestion['id_rol']=$id_rol_actual;
                            $datosGestion['id_tg']=6;
                            $datosGestion['id_gerencia']=$gerencia_usuario['gcia']; //$this->origen['id_gerencia'];
                            $datosGestion['ciclo_wf']=$documento['ciclos_wf'];
                            $datosGestion['detalle']=$texto;
//                            echo"<pre>".print_r($datosGestion,true)."</pre>";
                            $this->load->model("dms/gestiones_model","gestiones",true);
                            $this->gestiones->insert($datosGestion);
                            
                            //revierto la revision de los usuarios?
                            
                            if(ENPRODUCCION)
                            {
                                $mail['documento']=$this->documentos->dameDatosParaMailing($id_documento);
                                $this->load->model("dms/usuarios_doc_model","usuarios_doc",true);
                                $this->load->model("usuarios_model","usuarios",true);
                                $mail['documento']=$this->documentos->dameDatosParaMailing($id_documento);
                                $mail['destinatarios']=$this->usuarios_doc->dameDestinatariosMailing($id_documento,1); 
                                $mail['cc']=$this->usuarios->dameEmailUsuario($id_usuario); 
//                                echo"<pre>".print_r($mail,true)."</pre>";die();
                                $envio=$this->_enviarMail($mail);
//                                echo $envio;
                                $jsonData = "({success: true, error :0})"; 		
                                
                            }
                            else
                            {
                                $jsonData = "({success: true, error :0})"; 		
                            }
                             echo $jsonData;
                         }
                         else{
                            $jsonData = "({success: false, error :'Antes de su gestión el documento a cambiado de estado'})"; //error no concuerda el estado del doc para la accion a realizar		
                            echo $jsonData;
                        }
                    }
                    else
                    {
                        $jsonData = "({success: false, error :'Usted no tiene permiso asignado en este documento para realizar la taréa'})"; //error de permiso, no tiene el rol correspondiente en el documento		
                        echo $jsonData;
                    }
                  
              }
              else {
                $jsonData = "({success: false, error :'Usted no tiene permiso asignado para rechazar este documento'})"; //error de permiso, no tiene el rol correspondiente		
                echo $jsonData;
                
            }
            
        }
        
        function liberar()
        {
            $liberar=2;
            $id_documento=$this->input->post("id");
            $this->load->model('dms/usuarios_doc_model','usuarios_doc',true);
            $this->load->model("dms/documentos_model","documentos");
            $documento=$this->documentos->dameDocumento($id_documento);
            $activando_usuarios=true;
            //si es >=2do ciclo o si el tipo_wf es largo activo los usuarios que pudieran estar desactivados en caso de que el usuario no haya editado
            if($documento['ciclos_wf']>1 && $documento['tipo_wf']==2 )
                $activando_usuarios=$this->usuarios_doc->activar($id_documento);
            if ($activando_usuarios)
            {
                //Verifico  antes de liberar que haya subido el archivo
    //            echo"<pre>".print_r($documento,true)."</pre>";die();
                if ($documento['archivo']==NULL)
                        echo "({success: false, error :'Por favor antes de liberar aguegue el documento PDF'})";
                else
                {
                        $gestion=$this->gestion($id_documento, $liberar);
                        echo $gestion;
                }
                
            }
            else
                echo "({success: false, error :'Error en la estructura de usuarios, por favor informe a Sistemas o pruebe editando el Flujo de Trabajo'})";
                
           
        }
        function delegar()
        {
            $delegar=7;
            $id_usuario=$this->user['id'];
            $id_documento=$this->input->post("id_documento");
            $id_nuevo_editor=$this->input->post("id_editor");
            if($id_documento=="" || $id_nuevo_editor=="")
            {
                echo "({success: false, error :'Error en la transmisión de datos'})";
            }
            else
            {
                //verifico que el que esta delegando pertenezca a GR
                $this->load->model("gestion_riesgo/gr_usuarios_model","gr_usuarios",true);
                $u_gr=$this->gr_usuarios->verificarSiPerteneceGr($this->user['id']);
//                echo "<pre>".print_r($u_gr,true)."</pre>";die();
                
                if ($u_gr)
                {
                    $this->load->model("usuarios_model","usuarios",true);
                    $area_editor=$this->usuarios->dameIdArea($id_nuevo_editor);
                    $gerencia_editor=$this->_buscarGerencia($area_editor['id_area']);

                    //verifico que el nuevo editor pertenezca a la misma gerencia que el documento
                    $this->load->model("dms/documentos_model","documentos");
                    $documento=$this->documentos->dameDocumento($id_documento);
    //                echo "<pre>".print_r($documento,true)."</pre>";


                    if($documento['id_gerencia_origen']==$gerencia_editor['id_area'])
                    {
                        //valido que el estado del documento sea en borrador o rechazado
                        if ($documento['id_estado']==1 || $documento['id_estado']==2)
                        {
                            $usuarioGR=$this->usuarios->dameUsuario($id_usuario);
                            $usuasrioNuevoEditor=$this->usuarios->dameUsuario($id_nuevo_editor);
                            $usuarioViejoEditor=$this->usuarios->dameUsuario($documento['id_usuario_editor']);

                            //deshabilito y desactivo el editor anterior de la tabla de usuarios_doc
                            $datos_anterior['id_usuario']=$documento['id_usuario_editor'];
                            $datos_anterior['id_documento']=$id_documento;
                            $datos_anterior['id_rol']=1; //editor

                            $this->load->model('dms/usuarios_doc_model','usuarios_doc',true);
                            $desactivar=$this->usuarios_doc->desactivar($datos_anterior);
                            $deshabilitar=$this->usuarios_doc->deshabilitar($datos_anterior);

                            //cambio el editor en la tabla de documentos
                            $dato_nuevo['id_usuario_editor']=$id_nuevo_editor; //editor
                            $actualizar=$this->documentos->update($id_documento,$dato_nuevo);

                            //agrego el nuevo editor en usuarios_doc
                            $usuarios_doc['id_usuario']=$id_nuevo_editor;
                            $usuarios_doc['id_documento']=$id_documento;
                            $usuarios_doc['id_rol']=1;
                            $usuarios_doc['activo']=1;
                            $insert=$this->usuarios_doc->insert($usuarios_doc);

                            //guardo en el historial
                             $datosGestion=array();
                            $datosGestion['id_documento']=$id_documento;
                            $datosGestion['id_usuario']=$id_usuario;
                            $datosGestion['id_rol']=5; //usuario GR
                            $datosGestion['id_tg']=7; //Delegar
                            $datosGestion['id_gerencia']=$documento['id_gerencia_origen']; //$this->origen['id_gerencia'];
                            $datosGestion['ciclo_wf']=$documento['ciclos_wf'];
                            $datosGestion['detalle']="Documento delegado del editor ".$usuarioViejoEditor['nomape']." al editor ".$usuasrioNuevoEditor['nomape']." por el usuario ".$usuarioGR['nomape'];
    //                            echo"<pre>".print_r($datosGestion,true)."</pre>";
                            $this->load->model("dms/gestiones_model","gestiones",true);
                            $this->gestiones->insert($datosGestion);

                            echo "({success: true, error :''})";
                        }
                        else
                            echo "({success: false, error :'Error, el documento no se encuentra en el estado'})";


                    }
                    else
                        echo "({success: false, error :'Error, el usuario no pertenece a la gerencia del documento'})";
                }
                else
                    echo "({success: false, error :'Error, acción solo autorizada a personal de GR'})";
            }
                
        }
        function aprobar()
        {
           $aprobar=4;
            $id_documento=$this->input->post("id");
           $gestion=$this->gestion($id_documento, $aprobar);
//           $this->load->model("dms/documentos_model","documentos");
//           $documento=$this->documentos->dameDocumento($id_documento);
           echo $gestion;
           
        }
        function revisado()
        {
           $revisar=3;
           $id_documento=$this->input->post("id");
           $id_usuario=$this->user['id'];
           //verifico que el usuario no haya ya revisado el documento en este ciclo
           $this->load->model("dms/gestiones_model","gestiones",true);
            $yaReviso=$this->gestiones->checkRevisor($id_usuario,$id_documento);
            if($yaReviso)
                echo "({success: false, error :'Usted Ya reviso este documento'})"; //error el estado del documento no concuerda con la gestion solicitada
            else
            {
                //desactivo el usuario revisor
                $this->load->model("dms/usuarios_doc_model","usuarios_doc",true);
                $datos['id_usuario']=$id_usuario;
                $datos['id_documento']=$id_documento;
                $datos['id_rol']=2;
                $this->usuarios_doc->desactivar($datos);
                $gestion=$this->gestion($id_documento, $revisar);
                echo $gestion;
            }
           
        }
        function publicar()
        {
            $publicar=5;
            $id_documento=$this->input->post("id");
            $this->load->model('dms/documentos_model','documentos',true);
            $documento=$this->documentos->dameDocumentoAPublicar($id_documento);
//            echo "<pre>".print_r($documento,true)."</pre>";
            
            //verifico si proviene de un documento padre que aún este publicado
            if ($documento['padre_id']!=0 && $documento['padre_id']!=NULL)
            {
                //busco el documento padre
                $documento_padre=$this->documentos->dameDocumentoPublicado($documento['padre_id']);
//                echo "<pre>".print_r($documento_padre,true)."</pre>";
                if ($documento_padre!=0)
                {
                    //envio el documento a obsoleto
                    $obsoleto=$this->_hacerObsoleto($documento_padre['id_documento']);
                    if ($obsoleto==0)
                    {
                        echo "({success: false, error :'Error al guardar información del documento obsoleto, por favor comunique este incidente a sistemas...'})";
                    }
                    else
                    {
//                        return "({success: true,;
                        $gestion=$this->gestion($id_documento, $publicar);
                        if (substr($gestion,11,4)=='true')
                            $jsonData ="({success: true, msg: 'El documento ha sido publicado correctamente, y su version anterior, el documento c&oacute;digo ".$documento_padre['codigo']." fue enviado a obsoleto'})"; 	
                        else
                            $jsonData ="({success: false, error: 'Error al publicar, por favor comunique este incidente a sistemas...'})";
                        echo $jsonData;
                    }

                }
                else
                {
                    //cuando el documento halla sido pasado a obsoleto mientras el anterior estaba dentro del WF
                    $gestion=$this->gestion($id_documento, $publicar);
                    if (substr($gestion,11,4)=='true')
                        $jsonData ="({success: true, msg: 'El documento ha sido publicado correctamente, y su versi&oacute;n anterior ya se encuentra en documentos obsoletos'})"; 	
                    else
                        $jsonData ="({success: false, error: 'Error al publicar nueva versi&oacute;n, por favor comunique este incidente a sistemas...'})";
                    echo $jsonData;
                    
                }
            }
            else
            {
                $gestion=$this->gestion($id_documento, $publicar);
                if (substr($gestion,11,4)=='true')
                    $jsonData ="({success: true, msg: 'El documento ha sido publicado correctamente'})"; 	
                else
                    $jsonData ="({success: false, error: 'Error al publicar, por favor comunique este incidente a sistemas...'})";
                echo $jsonData;
//                echo $gestion;
            }
        }
        
        function _nombreArchivo($id_documento,$tipo="")
        {
            $qceros=7;
//            switch ($tipo)
//            {
//                case 'PDF':
                    $t='PDF';
                    $ext='.pdf';
//                    break;
//                case 'Fuente':
//                    $t='AFX';
//                    $ext='.doc';
//                    break;
//            }
            
            $nro= str_repeat('0',$qceros-strlen($id_documento)).$id_documento;
            $nombre=$nro.$t.md5(KEYMD5."$id_documento").$ext;
            return $nombre;
        }
        
        function _enviarMail($datos)
        {
            $link='www.polidata.com.ar/sdj/admin';
                        
$cuerpo_mail=<<<HTML
<html> 
<head>
<title>SMC</title>
<style type="text/css">
td{
    vertical-align: top;
}
</style>
</head>
<body>
<div style="color:#000000; font-family:Verdana, Arial, Helvetica, sans-serif; text-align:Left; font-size:13px;">
<pre>
#para

#texto
<br>
<table  border="0">
<tr>
<td style="width:150px;background-color:black;color:white"><b>Código</b></td>
<td style="width: 5px;">:</td>
<td>#codigo_doc</td>
</tr>
<tr>
<td style="width: 150px;"><b>Título</b></td>
<td style="width: 5px;">:</td>
<td>#titulo_doc</td>
</tr>
<tr>
<td style="width: 150px;"><b>Identificador</b></td>
<td style="width: 5px;">:</td>
<td>#id_doc</td>
</tr>
<tr>
<td style="width: 150px;"><b>Alcance</b></td>
<td style="width: 5px;">:</td>
<td>#alcance_doc</td>
</tr>
<tr>
<td style="width: 150px;"><b>Editor</b></td>
<td style="width: 5px;">:</td>
<td>#editor_doc</td>
</tr>
<tr>
<td style="width: 150px;"><b>Revisor/es</b></td>
<td style="width: 5px;">:</td>
<td>#revisores_doc</td>
</tr>
<tr>
<td style="width: 150px;"><b>Aprobador</b></td>
<td style="width: 5px;">:</td>
<td>#aprobador_doc</td>
</tr>
<tr>
<td style="width: 150px;"><b>Descripci&oacute;n:</b></td>
<td style="width: 5px;">:</td>
<td>#descripcion_doc</td>
</tr>
</table>

#pie
#link.

Atentamente
#firma

Importante: Mensaje generado autom&aacute;ticamente, por favor no responder.
 <hr>
<code>
Para: #txt_usuarioPara <br>
CC  : #txt_usuariosCC
</code>
<hr>
</pre>
</div>
</body>
</html>
HTML;
            $cc=array();
            if (MAILS_ALWAYS_CC!="")
                $cc[]=MAILS_ALWAYS_CC;
            $para=array();
            $txt_pie='Por favor, complete su gesti&oacute;n ingresando al sistema desde <a href="#link" target="_blank">aqu&iacute;</a>, o copiando y pegando la siguiente direcci&oacute;n en su navegador web <b>Mozilla Firefox</b> o <b>Google Chrome</b>: ';
            $txt_pie_publicado='Puede consultarlo, descargarlo o imprimirlo ingresando al sistema desde <a href="#link" target="_blank">aqu&iacute;</a>, o copiando y pegando la siguiente direcci&oacute;n en su navegador web <b>Mozilla Firefox</b> o <b>Google Chrome</b>: ';
//            if ($datos['documento']['tipo_wf']==1) //wf corto
//            {
//                
//            }
             $cuerpo_mail=str_replace("#titulo_doc"         ,$datos['documento']['titulo']             ,$cuerpo_mail);
             $cuerpo_mail=str_replace("#codigo_doc"         ,$datos['documento']['codigo']             ,$cuerpo_mail);
             $cuerpo_mail=str_replace("#id_doc"             ,$datos['documento']['id_documento']       ,$cuerpo_mail);
            $cuerpo_mail=str_replace("#alcance_doc",'Sales de Jujuy',$cuerpo_mail);
             switch ($datos['documento']['id_estado']){
                 case 1://En Borrador
                    $para=$datos['destinatarios'];
                    $cuerpo_mail=str_replace("#para",'Estimado usuario,',$cuerpo_mail);
                    $cuerpo_mail=str_replace("#texto",'El siguiente documento se encuentra disponible para su gestión en su bandeja de trabajo:',$cuerpo_mail);
                    $cuerpo_mail=str_replace("#pie",$txt_pie,$cuerpo_mail);
                    break;
                 case 2: //Rechazado
                    $para=$datos['destinatarios'];
                    $cuerpo_mail=str_replace("#para",'Estimado '.$datos['documento']['editor_nom'].',',$cuerpo_mail);
                    $cuerpo_mail=str_replace("#texto",'El siguiente documento ha sido rechazado por '.$datos['cc']['nomape'].' y se encuentra disponible para ser editado en su bandeja de trabajo:',$cuerpo_mail);
                    $cuerpo_mail=str_replace("#pie",$txt_pie,$cuerpo_mail);
                    $cc[]=$datos['cc']['email'];
                    break;
                 case 3: //En Revision
                    $para=$datos['destinatarios'];
                    $cuerpo_mail=str_replace("#para",'Estimado/s '.$datos['documento']['revisores_nom'].',',$cuerpo_mail);
                    $cuerpo_mail=str_replace("#texto",'El siguiente documento se encuentra disponible para su revisión en su bandeja de trabajo:',$cuerpo_mail);
                    $cuerpo_mail=str_replace("#pie",$txt_pie,$cuerpo_mail);
                    break;
                 case 4: //En Aprobación
                    $para=$datos['destinatarios'];
                    $cuerpo_mail=str_replace("#para",'Estimado '.$datos['documento']['aprobador_nom'].',',$cuerpo_mail);
                    $cuerpo_mail=str_replace("#texto",'El siguiente documento se encuentra disponible para su aprobación en su bandeja de trabajo:',$cuerpo_mail);
                    $cuerpo_mail=str_replace("#pie",$txt_pie,$cuerpo_mail);
                    break;
                 case 5: //A Publicar
                    $para=$datos['destinatarios'];
                    $cuerpo_mail=str_replace("#para",'Estimado publicadores,',$cuerpo_mail);
                    $cuerpo_mail=str_replace("#texto",'El siguiente documento se encuentra disponible para su gestión en su bandeja de trabajo:',$cuerpo_mail);
                    $cuerpo_mail=str_replace("#pie",$txt_pie,$cuerpo_mail);
                    $cc[]=('gestionderiesgos@salesdejujuy.com');
                    break;
                 case 6: //Publicado 
                    $cuerpo_mail=str_replace("#para",'Estimado usuario,',$cuerpo_mail);
                    $cuerpo_mail=str_replace("#texto",'Se ha publicado el siguiente nuevo documento:',$cuerpo_mail);
                    $cuerpo_mail=str_replace("#pie",$txt_pie_publicado,$cuerpo_mail);
                    $para=array('personalsalesdejujuy@salesdejujuy.com');
                    break;
                 case 7: //Obsoleto
                    break;
               
                 
             }
             $cuerpo_mail=str_replace("#editor_doc"             ,$datos['documento']['editor']       ,$cuerpo_mail);
             $cuerpo_mail=str_replace("#revisores_doc"             ,$datos['documento']['revisores']       ,$cuerpo_mail);
             $cuerpo_mail=str_replace("#aprobador_doc"             ,$datos['documento']['aprobador']       ,$cuerpo_mail);
             $cuerpo_mail=str_replace("#descripcion_doc"    ,$datos['documento']['detalle']             ,$cuerpo_mail);
             $cuerpo_mail=str_replace("#link"               ,$link                                     ,$cuerpo_mail);
             $cuerpo_mail=str_replace("#firma"              ,htmlentities(NOMSIS,ENT_QUOTES,"UTF-8")   ,$cuerpo_mail);
             $cuerpo_mail=str_replace("#txt_usuarioPara"   ,implode(", ",$para), $cuerpo_mail);
             //Compara array $cc con array $para y devuelve los valores de $cc que no estén presentes en $para.
             $cc=array_diff($cc,$para);
             $cuerpo_mail=str_replace("#txt_usuariosCC"    ,implode(",",$cc), $cuerpo_mail);
//             $this->email->clear();//para cuando uso un bucle
             $this->load->library('email');
             $this->email->to($para);
//             $this->email->to('fmoraiz@gmail.com');
             $this->email->cc($cc);
             $this->email->from(SYS_MAIL, NOMSIS);
             $this->email->subject('Documento "'.$datos['documento']['estado'].'" Cod:'.$datos['documento']['codigo']);
             $this->email->message($cuerpo_mail);
//            $this->email->set_alt_message();//Sets the alternative email message body
//            $this->email->reply_to('gestiondedocumentos@salesdejujuy.com','DNS');
            IF (MAILBCC && MAILBCC !="")
                $this->email->bcc(MAILBCC);
            if (ENPRODUCCION)
            {
                if($this->email->send())
                {
                    
//                    echo $cuerpo_mail;
//                    $this->email->print_debugger();
                    return 0;
                }
                else
                    return 2;
            }
            else
            {
                return 0;
            }

        }
        
        public function test($id_empresa){
            
//            $id_empresa=1;
            $id_usuario=$this->user['id'];
            $id_rol=1;
            
            $this->load->model("gestion_riesgo/gr_areas_model","areas",true);
            
                $areas=$this->areas->dameComboGerenciasPorOrganigrama($id_empresa);
            
            echo"<pre>".print_r($areas,true)."</pre>";
//            echo '$q_rev:'.$q_rev;
        }
        
        function _buscarGerencia($id_area)
        {
            $this->load->model("gestion_riesgo/gr_areas_model","areas",true);
            $area=array();
            $area=$this->areas->dameGerencia($id_area);
//                echo"<pre>Buscar".print_r($area,true)."</pre>";
            //verifico que el area exista
            if ($area!=0)
            {
                if($area['gcia']==0)                    
                    return $this->_buscarGerencia($area['id_area_padre']);
                else
                    
                    return $area;
////                
            }
            else 
                return 0;
        }
        public function listado_obs()
	{
		if ($this->permisos['Listar'])
                {
                    $this->load->model('dms/observaciones_model','obs',true);                    
                    $id_documento = $this->input->post("id");
                    $start = $this->input->post("start");
                    $limit = $this->input->post("limit");
                    $sort = $this->input->post("sort");
                    $dir = $this->input->post("dir");
                    $listado = $this->obs->dameObsPorDocumento($id_documento,$start, $limit, $sort, $dir);
                    echo $listado;
                }
                else
                    echo -1; //No tiene permisos
                
	}
        function _hacerObsoleto($id_documento)
        {
            
            $id_usuario=$this->user['id'];
            $this->load->model("dms/documentos_model","documentos",true);
            $documento=$this->documentos->dameDocumento($id_documento);
//                echo "<pre>".print_r($documento,true)."</pre>";

            $this->load->model('usuarios_model','usuarios',true);
            $usuario=$this->usuarios->dameUsuarioEmpresaYGR($id_usuario);
//                echo "<pre>".print_r($usuario,true)."</pre>";

                //verifico que el usuario sea el editor o pertenezca a GR
                if ($usuario['gr']==1 || $documento['id_usuario_editor']==$id_usuario)
                {
                    $datos_documento['id_usuario_obsoleto']=$id_usuario;
                    $datos_documento['id_estado']=7;
                    $datos_documento['obsoleto']=1;
                    $datos_documento['fecha_obsoleto']=date("Y-m-d");
                    $datos_documento['habilitado']=0;
                    //Si se actualizo correctamente actualizo gestiones
                    if($this->documentos->update($documento['id_documento'],$datos_documento))
                    {
                        $this->load->model("usuarios_model","usuarios",true);
                        $area_usuario=$this->usuarios->dameIdArea($id_usuario);

                        $gerencia_usuario=$this->_buscarGerencia($area_usuario['id_area']);
    //                    echo "<pre>".print_r($gerencia_usuario,true)."</pre>";

                        $datos_gestion=array();
                        $datos_gestion['id_documento']=$documento['id_documento'];
                        $datos_gestion['version']=$documento['version'];
                        $datos_gestion['id_usuario']=$id_usuario;
                        $datos_gestion['id_rol']=($documento['id_usuario_editor']==$id_usuario)?1:5;//si es el editor como editor sino como rolde GR;
                        $datos_gestion['id_tg']=8;
                        $datos_gestion['ciclo_wf']=$documento['ciclos_wf'];
                        $datos_gestion['id_gerencia']=$gerencia_usuario['gcia'];
                        $datos_gestion['detalle']='Documento obsoleto';
            //            echo"<pre>".print_r($datosGestion,true)."</pre>";
                        $this->load->model("dms/gestiones_model","gestiones",true);
                        $this->gestiones->insert($datos_gestion);

                        return 1;
                    }
                    else 
                        return 0;
                }
                else
                    return -1; //sin permisos
        }
}
?>