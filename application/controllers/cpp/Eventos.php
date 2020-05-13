<?php
class Eventos extends CI_Controller
{
    private $modulo=48;
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
        $this->permisos = $this->permisos_model->checkIn($this->user['perfil_id'],$this->modulo);
//            echo "<pre>Origen".print_r($this,true)."</pre>";
    }

    public function index()
    {
        $modulo = $this->uri->segment(SEGMENTOS_DOM+2);
        if (!isset($modulo)) $modulo=0;
        $this->load->model('permisos_model','permisos_model',true);
//        $variables['permiso'] = $this->permisos_model->checkIn($this->user['perfil_id'],$modulo);
        $variables['permiso'] = $this->permisos;
//        $variables['editar'] = $this->_control_edicion();
        $variables['usuario'] = $this->user['id'];
//        echo "<pre>Origen".print_r($variables,true)."</pre>";
        $this->load->view('cpp/eventos/listado',$variables);
    }

    public function listado()
    {
        if ($this->permisos['Listar'])
        {
            $this->load->model('cpp/cpp_eventos_model','eventos',true);
            $this->load->model('cpp/cpp_botones_permiso_model','botones',true);
            $this->load->model('usuarios_model','usuarios',true);
            $this->load->model('cpp/cpp_administradores_model','administradores',true);
            
            $start = $this->input->post("start");
            $limit = $this->input->post("limit");
            $sort = $this->input->post("sort");
            $dir = $this->input->post("dir");
            
            $campos = "";
            $id_usuario = $this->user['id'];
            $admin=$this->administradores->checkAdministrador($id_usuario);
            $id_gcia=$this->usuarios->dameGerenciaUsuario($id_usuario);
            
//            $estadosBotonMonto=$this->botones->dameEstados($id_gcia,'btn_monto');
//            $estadosBotonTn=$this->botones->dameEstados($id_gcia,'btn_tn');
//            $estadosBotonEi=$this->botones->dameEstados($id_gcia,'btn_ei');
//            $estadosBotonCancelar=$this->botones->dameEstados($id_gcia,'btn_cancelar');
//            $estadosBotonCrit=$this->botones->dameEstados($id_gcia,'btn_crit');
//            $estadosBotonEditCrit=$this->botones->dameEstados($id_gcia,'btn_edit_crit');
//            $estadosBotonEditEvento=$this->botones->dameEstados($id_gcia,'btn_edit_evento');
//            
//            $estadosBotones['btn_monto']=($estadosBotonMonto == 0)?0:implode(',',$estadosBotonMonto);
//            $estadosBotones['btn_tn']=($estadosBotonTn == 0)?0:implode(',',$estadosBotonTn);
//            $estadosBotones['btn_ei']=($estadosBotonEi==0)?0:implode(',',$estadosBotonEi);
//            $estadosBotones['btn_cancelar']=($estadosBotonCancelar==0)?0:implode(',',$estadosBotonCancelar);
//            $estadosBotones['btn_crit']=($estadosBotonCrit==0)?0:implode(',',$estadosBotonCrit);
//            $estadosBotones['btn_edit_crit']=($estadosBotonEditCrit==0)?0:implode(',',$estadosBotonEditCrit);
//            $estadosBotones['btn_edit_evento']=($estadosBotonEditEvento==0)?0:implode(',',$estadosBotonEditEvento);
            
            $estados=$this->botones->damePermisos($id_gcia);
            $estadosBotones['btn_monto'] = array();
            $estadosBotones['btn_tn'] = array();
            $estadosBotones['btn_ei'] = array();
            $estadosBotones['btn_cancelar'] = array();
            $estadosBotones['btn_crit'] = array();
            $estadosBotones['btn_edit_crit'] = array();
            $estadosBotones['btn_edit_evento'] = array();
            if ($estados != 0)
            {
                foreach($estados as $estado)
                {
                    if($estado['btn_monto'] == 1)
                        array_push($estadosBotones['btn_monto'], $estado['id_estado']);
                    if($estado['btn_tn'] == 1)
                        array_push($estadosBotones['btn_tn'], $estado['id_estado']);
                    if($estado['btn_ei'] == 1)
                        array_push($estadosBotones['btn_ei'], $estado['id_estado']);
                    if($estado['btn_cancelar'] == 1)
                        array_push($estadosBotones['btn_cancelar'], $estado['id_estado']);
                    if($estado['btn_crit'] == 1)
                        array_push($estadosBotones['btn_crit'], $estado['id_estado']);
                    if($estado['btn_edit_crit'] == 1)
                        array_push($estadosBotones['btn_edit_crit'], $estado['id_estado']);
                    if($estado['btn_edit_evento'] == 1)
                        array_push($estadosBotones['btn_edit_evento'], $estado['id_estado']);

                }
            }
            $estadosBotones['btn_monto']        = (count($estadosBotones['btn_monto']) == 0)?0:implode(',',$estadosBotones['btn_monto']);
            $estadosBotones['btn_tn']           = (count($estadosBotones['btn_tn']) == 0)?0:implode(',',$estadosBotones['btn_tn']);
            $estadosBotones['btn_ei']           = (count($estadosBotones['btn_ei']) == 0)?0:implode(',',$estadosBotones['btn_ei']);
            $estadosBotones['btn_cancelar']     = (count($estadosBotones['btn_cancelar']) == 0)?0:implode(',',$estadosBotones['btn_cancelar']);
            $estadosBotones['btn_crit']         = (count($estadosBotones['btn_crit']) == 0)?0:implode(',',$estadosBotones['btn_crit']);
            $estadosBotones['btn_edit_crit']    = (count($estadosBotones['btn_edit_crit']) == 0)?0:implode(',',$estadosBotones['btn_edit_crit']);
            $estadosBotones['btn_edit_evento']  = (count($estadosBotones['btn_edit_evento']) == 0)?0:implode(',',$estadosBotones['btn_edit_evento']);
            
//            echo $id_gcia;
//            echo "<pre>".print_r($estadosBotones,true)."</pre>";die();
            if ($this->input->post("fields"))
            {
                $campos = str_replace('"', "", $this->input->post("fields"));
                $campos = str_replace('[', "", $campos);
                $campos = str_replace(']', "", $campos);
                $campos = explode(",",$campos);
            }
            $busqueda  = ($this->input->post("query"))?$this->input->post("query"):"";
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
            
            $listado = $this->eventos->listado($start, $limit, $sort,$dir,$busqueda,$campos,$filtros,$id_usuario,$admin,$estadosBotones);
            echo $listado;
        }
        else
            echo -1; //No tiene permisos
    }
    
    public function listado_cons()
    {
        if ($this->permisos['Listar'])
        {
            $id_evento =$this->input->post('evento');
            $this->load->model('cpp/cpp_evento_consecuencias_model','ec_model',true);
            $start = $this->input->post("start");
            $limit = $this->input->post("limit");
            $sort = $this->input->post("sort");
            $dir = $this->input->post("dir");
            $listado = $this->ec_model->listado($id_evento,$start, $limit, $sort,$dir);
            echo $listado;
        }
        else
            echo -1; //No tiene permisos
    }
    
    public function listado_invest()
    {
        if ($this->permisos['Listar'])
        {
            $id_evento =$this->input->post('evento');
            $this->load->model('cpp/cpp_investigadores_model','investigadores',true);
            $start = $this->input->post("start");
            $limit = $this->input->post("limit");
            $sort = $this->input->post("sort");
            $dir = $this->input->post("dir");
            $listado = $this->investigadores->listado($id_evento,$start, $limit, $sort,$dir);
            echo $listado;
        }
        else
            echo -1; //No tiene permisos
    }
    public function listado_audit()
    {
        if ($this->permisos['Listar'])
        {
            $id_evento =$this->input->post('evento');
            $this->load->model('cpp/cpp_auditoria_evento_model','ae_model',true);
            $start = $this->input->post("start");
            $limit = $this->input->post("limit");
            $sort = $this->input->post("sort");
            $dir = $this->input->post("dir");
            $listado = $this->ae_model->listado($id_evento,$start, $limit, $sort,$dir);
            echo $listado;
        }
        else
            echo -1; //No tiene permisos
    }
    public function listado_tareas()
    {
        if ($this->permisos['Listar'])
        {
            $id_usuario=$this->user['id'];
            $id_evento=$this->input->post("evento");
            $this->load->model("cpp/cpp_causas_model","causas",true);
            $this->load->model("cpp/cpp_eventos_model","eventos",true);
            $arrayCausas=$this->causas->dameCausasPorIdEvento($id_evento);
//            echo "<pre>".print_r($arrayCausas,true)."</pre>";
            
            if($arrayCausas != 0)
            {
                foreach ($arrayCausas as $key=>$value)
                {
                   $causasIds[]=$value['id_causa'];
                }
                $herramienta=array();
                $herramienta['id']=$causasIds;
            
                $herramienta['id_tipo']=8;
    //            echo "<pre>".print_r($herramienta,true)."</pre>";
                $listado=array();
                $this->load->model('gestion_riesgo/gr_tareas_model','gr_tareas',true);
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
                $usuario=$this->_dameAreas($id_usuario);
//                echo "<pre>".print_r($herramienta,true)."</pre>";
                $listado = $this->gr_tareas->listadoParaHerramientasCpp($usuario,$start, $limit, $filtros,$busqueda, $campos, $sort, $dir,$herramienta);
                echo $listado;
            }
            else
                echo '({"total":"0","rows":""})';
        }
        else
            echo -1; //No tiene permisos

    }
    public function combo_sectores()
    {
        $start = $this->input->post("start");
        $limit = $this->input->post("limit");
        $busqueda = $this->input->post("query");
        $this->load->model('cpp/cpp_sectores_model','sectores',true);
        $var=$this->sectores->dameCombo($start,$limit,$busqueda);
        echo $var;	
    }
    public function combo_equipos()
    {
        $start = $this->input->post("start");
        $limit = $this->input->post("limit");
        $busqueda = $this->input->post("query");
        $this->load->model('cpp/cpp_equipos_model','equipos',true);
        $var=$this->equipos->dameCombo($start,$limit,$busqueda);
        echo $var;	
    }
    public function combo_productos()
    {
        $start = $this->input->post("start");
        $limit = $this->input->post("limit");
        $busqueda = $this->input->post("query");
        $this->load->model('cpp/cpp_productos_model','productos',true);
        $var=$this->productos->dameCombo($start,$limit,$busqueda);
        echo $var;	
    }
    public function combo_consecuencias()
    {
        $start = $this->input->post("start");
        $limit = $this->input->post("limit");
        $busqueda = $this->input->post("query");
        $this->load->model('cpp/cpp_consecuencias_model','consecuencias',true);
        $var=$this->consecuencias->dameCombo($start,$limit,$busqueda);
        echo $var;
    }
    public function combo_investigadores()
    {
        $id_evento = $this->input->post('evento');
        $this->load->model('cpp/cpp_investigadores_model','investigadores',true);
        $investigadores=$this->investigadores->dameInvestigadores($id_evento);
        if ($investigadores!=0)
        {
            foreach ($investigadores as $key=>$value)
            {
                $arrayIdNot[]=$value['id_usuario'];
            }
        }
        else
            $arrayIdNot="";
            
        $start = $this->input->post("start");
        $limit = $this->input->post("limit");
        $busqueda = $this->input->post("query");
        $id_usuario_not=array($this->user['id']);
        $this->load->model('cpp/cpp_investigadores_model','investigadores',true);
        $var=$this->investigadores->investigadoresCombo($start,$limit,$busqueda,$arrayIdNot);
        echo $var;	
    }
    
    public function insert()
    {
        if ($this->permisos['Alta'])
        {
            
            $id_evento                  = $this->input->post('id'); 
            $datos['id_sector']     = $this->input->post('sector');
            $datos['fecha_inicio']      = $this->input->post('fecha_inicio');
            $datos['hora_inicio']       = $this->input->post('hora_inicio');
            $datos['fecha_fin']         = $this->input->post('fecha_fin');
            $datos['hora_fin']          = $this->input->post('hora_fin');
//            $datos['id_equipo']         = $this->input->post('equipo');
            $equipo                     = explode(',', $this->input->post('equipo'));
            $datos['descripcion']       = $this->input->post("descripcion");
            $datos['id_producto']   = ($this->input->post("producto")==NULL)?NULL:$this->input->post("producto");
            
            $fecha_i = date_create($datos['fecha_inicio']);
            $arrayHora_i=explode(':', $datos['hora_inicio']);
            $fecha_i->setTime($arrayHora_i[0],$arrayHora_i[1]);
            $ts_i=date_timestamp_get($fecha_i);

            $fecha_f = date_create($datos['fecha_fin']);
            $arrayHora_f=explode(':', $datos['hora_fin']);
            $fecha_f->setTime($arrayHora_f[0],$arrayHora_f[1]);
            $ts_f=date_timestamp_get($fecha_f);

            $hrs_perdidas=($ts_f-$ts_i)/3600;
            $this->load->model('cpp/cpp_eventos_model','eventos',true);

//            echo "<pre>".print_r($datos,true)."</pre>";die();
            $control=true;
            foreach ($datos as $atributo=>$dato)
            {
                if($dato=="")
                    $control=false;
            }

            if ($control || count($equipo) > 0)
            {
                if($id_evento == 0)
                {
    //                echo 'nuevo';
                    $datos['id_usuario_alta']   = $this->user['id'];
                    $datos['id_estado']         = 1;

                    if($hrs_perdidas>0)
                    {
                        $datos['horas_perdidas']= $hrs_perdidas;
            //            echo "<pre>".print_r($datos,true)."</pre>";die();

                        $insert=$this->eventos->insert($datos);
                        $data['id_evento'] = $insert;
                        if ($insert)
                        {
                            $this->load->model('cpp/cpp_evento_equipos_model','evento_equipos',true);
                            foreach  ($equipo as $key=>$value)
                            {
                                $data['id_equipo']   = $value;
                                $evento_equipos[]=$data;
                            }
//                            echo "<pre>".print_r($evento_equipos,true)."</pre>";die();
                            $i = 0;
                            $ok = array();
                            foreach  ($evento_equipos as $key=>$value)
                            {
                                if ($this->evento_equipos->insert($value))
                                    $ok[$i] = 1;
                                else
                                    $ok[$i] =0;
                                $i++;
                            }
                            //$this->_auditoria($insert,0,1,1);
                            if(in_array(0, $ok))
                                echo 0;
                            else
                                echo 1;
                        }
                        else
                            echo 0;
                    }
                    else
                        echo -1; //
                }
                else
                {
                    if($hrs_perdidas>0)
                    {
                        $datos['horas_perdidas']= $hrs_perdidas;
                        $eventoAEditar=$this->eventos->dameEventoAEditar($id_evento);

                        $update=$this->eventos->update($id_evento,$datos);
                        if ($update)
                        {
                            $this->load->model('cpp/cpp_evento_equipos_model','evento_equipos',true);

                            $delete = $this->evento_equipos->delete($id_evento);
                            if($delete)
                            {
                                $data['id_evento'] = $id_evento;
                                foreach  ($equipo as $key=>$value)
                                {
                                    $data['id_equipo']   = $value;
                                    $evento_equipos[]=$data;
                                }

                                $i = 0;
                                $ok = array();
                                foreach  ($evento_equipos as $key=>$value)
                                {
                                    if ($this->evento_equipos->insert($value))
                                        $ok[$i] = 1;
                                    else
                                        $ok[$i] = 0;
        //                                $control=$control+1;
                                    $i++;
                                }
                                if(in_array(0, $ok))
                                    echo 0;
                                else
                                {
                                    $datos['hora_inicio'] = date_format($fecha_i, 'H:i:s');
                                    $datos['hora_fin'] = date_format($fecha_f, 'H:i:s');
                                    $datos['fecha_inicio'] = str_replace('T', ' ',$datos['fecha_inicio']);
                                    $datos['fecha_fin'] = str_replace('T', ' ',$datos['fecha_fin']);

                                    $horas = number_format($datos['horas_perdidas'], 2, '.','');
                                    $datos['horas_perdidas'] = $horas;
                                    $diff =  array_diff($eventoAEditar,$datos);
                                    if($diff>0)
                                    {
                                        
                                        $this->_mef($id_evento,9,$diff);
                                    }
                                    echo 1;
                                }

                            }
                            else
                                echo 0;
                        }
                        else
                            echo 0;
                    }
                    else
                        echo -1; 
                }
            }
            else
                echo 2;
        }
        else
            echo "({success: false, error :'No tiene permisos para realizar la acción solicitada'})"; //
    }
    
    public function insert_cons()
    {
        if ($this->permisos['Alta'])
        {
            $id_evento                  = $this->input->post('evento');
            $id_accion                  = 2;
            $datos['id_usuario_alta']   = $this->user['id'];
            $datos['id_evento']         = $id_evento;
            $datos['id_consecuencia']   = $this->input->post('consecuencia');
            $datos['descripcion']       = $this->input->post("descripcion");
            
//            echo "<pre>".print_r($resultado,true)."</pre>";die();
            $this->load->model('cpp/cpp_eventos_model','eventos',true);
            $btn_calificar = $this->eventos->controlBtnCalificar($id_evento,$this->user['id']);
 
            if($btn_calificar == 1)
            {
                $control = true;
                foreach ($datos as $dato)
                {
                    if($dato=="")
                        $control=false;
                }
                if ($control)
                {
                        $this->load->model('cpp/cpp_evento_consecuencias_model','ec_model',true);	
                        if ($this->ec_model->insert($datos))
                        {
                            $this->_mef($id_evento,$id_accion);
                             echo "({success: true, msg:'Datos guardados...'})";
                        }
                        else
                             echo "({success: false, msg:'Error al guardar la información'})";
                }
                else
                     echo "({success: false, msg:'Verifique campos obligatorios...'})";
            }
            else
                echo "({success: false, error :'No tiene permisos para realizar la acción solicitada'})"; //
        }
        else
            echo "({success: false, error :'No tiene permisos para realizar la acción solicitada'})"; //
    }
    
    public function insert_ei()
    {
        if ($this->permisos['Alta'])
        {
            $id_evento                  = $this->input->post('evento');
            $ei                         = explode(',', $this->input->post('lista'));
            $id_accion                  = 4;
            $datos['id_usuario_alta']   = $this->user['id'];
            $datos['id_evento']         = $id_evento;
            
            $this->load->model('usuarios_model','usuarios',true);
            $this->load->model('cpp/cpp_botones_permiso_model','botones',true);
            $this->load->model('cpp/cpp_investigadores_model','investigadores',true);

            $id_gcia=$this->usuarios->dameGerenciaUsuario($this->user['id']);
 //           echo $id_gcia;
            $estadosBotonEi=$this->botones->dameEstados($id_gcia,'btn_ei');
            $estadosBotones['btn_ei']=($estadosBotonEi==0)?0:implode(',',$estadosBotonEi);
            
            if($estadosBotones['btn_ei'] != 0)
            {
                foreach  ($ei as $key=>$value)
                {
                    $datos['id_usuario']   = $value;
                    $datosDatos[]=$datos;
                }

                $investigadores=$this->investigadores->dameInvestigadores($id_evento);
                if ($investigadores!=0)
                {
                    foreach ($investigadores as $key=>$value)
                    {
                        $arrayInvActuales[]=$value['id_usuario'];
                    }
        //            $repetidos=array_intersect($arrayInv,$ei);
                    $ei=  array_diff($ei, $arrayInvActuales);
                }

                if (count($ei)!=0)
                {
                    $this->load->model('cpp/cpp_eventos_model','eventos',true);
                    $btn_ei = $this->eventos->controlBtnEi($id_evento,$estadosBotones);
                    
                    if($btn_ei == 1)
                    {
                        $control=0;
                        $idParaMails = array();
                        foreach  ($datosDatos as $key=>$value)
                        {
                            $idParaMails[$control] = $value['id_usuario'];
                            if ($this->investigadores->insert($value))
                                $control=$control+1;
                        }
                        if (count($ei)==$control)
                        {
                            $this->load->model('usuarios_model','usuarios',true);
                            $usuarioAlta = $this->usuarios->DameUsuarioPersona($this->user['id']);
                            $usuarioAlta = array_pop($usuarioAlta);
                            
                            $contenido['nombreUsuarioAlta'] = $usuarioAlta['nomape'];
                            $contenido['emailUsuarioAlta'] = $usuarioAlta['email'];
                            $mailUA[0] = $usuarioAlta['email'];
                            $i=0;
                            
                            foreach ($idParaMails as $valor) 
                            {
                                $habilitado = $this->_dameSupervisorHabilitado($valor);
                                $habilitado = array_pop($habilitado);
                                $supervisoresEmail[$i] = $habilitado['email'];
                                $usuarioInv = $this->usuarios->DameUsuarioInvestigador($valor);
                                $usuarioInv = array_pop($usuarioInv);
                                $investigadoresEmail[$i] = $usuarioInv['email'];
                                if($i == 0)
                                    $contenido['investigador'] = '<tr><td>'.$usuarioInv['nomape'].'</td><td>'.$usuarioInv['fecha_alta'].'</td></tr>';
                                else
                                    $contenido['investigador'] .= '<tr><td>'.$usuarioInv['nomape'].'</td><td>'.$usuarioInv['fecha_alta'].'</td></tr>';
                                    
                                
                                $i++;
                            }
                            
                            //echo "<pre>".print_r($investigadores,true)."</pre>";
                            foreach ($investigadores as $key=>$value)
                            {
                                $contenido['investigador'] .= '<tr><td>'.$value['nomape'].'</td><td>'.$value['fecha_alta'].'</td></tr>';
                            }
                            
                            $supervisoresEmail = array_merge($supervisoresEmail,$mailUA);
                            $supervisoresEmail = array_unique($supervisoresEmail);
                            $supervisoresEmail = array_diff($supervisoresEmail,$investigadoresEmail);
                            
                            foreach($supervisoresEmail as $sup)
                            {
                                
                                if($contenido['emails'] == null)
                                    $contenido['emails'] = $sup;
                                else
                                    $contenido['emails'] .=", ".$sup;
                                
                            }
                            
                            foreach($investigadoresEmail as $invest)
                            {
                                
                                if($contenido['emailsInv'] == null)
                                    $contenido['emailsInv'] = $invest;
                                else
                                    $contenido['emailsInv'] .= ", ".$invest;
                                
                            }
                            
                            $contenido['investigadoresEmail'] = $investigadoresEmail;
                            $contenido['cant_inv'] = count($investigadoresEmail);
                            $contenido['emailsCopia'] = $supervisoresEmail;
                            $contenido['descripcion'] = $btn_ei = $this->eventos->dameDescripcionEvento($id_evento);
                            $contenido['id_evento'] = $id_evento;
                            $this->_mef($id_evento,$id_accion);
//                            echo "<pre>".print_r($contenido,true)."</pre>";
                            $res = $this->_enviarMailInvestigadores($contenido);
                            echo "({success: true, msg:'Datos guardados...'})";
                        }
                        else
                             echo "({success: false, msg:'Error contando registros'})";
                    }
                    else
                        echo "({success: false, error :'Error al guardar la información, por favor comuniquese con el administrador del sistema.'})";
                }
                else
                     echo "({success: false, msg:'Verifique seleccionados'})";
            }
            else
                echo "({success: false, error :'No tiene permisos para realizar la acción solicitada'})"; //
        }
        else
            echo "({success: false, error :'No tiene permisos para realizar la acción solicitada'})"; //
    }
    
    public function insert_tarea()
    {
        if ($this->permisos['Alta'])		
        {
            $id_usuario=$this->user['id'];
            $id_causa=$this->input->post("id_causa");
            $this->load->model("cpp/cpp_causas_model","causas",true);
            $this->load->model("cpp/cpp_eventos_model","eventos",true);
            $causa= $this->causas->dameCausa($id_causa);
            $evento= $this->eventos->dameEvento($causa['id_evento']);
             if ($evento!=0)
             {
                $datos=array();
                $datos['usuario_alta']          =$id_usuario;
                $datos['id_tipo_herramienta']   =8; //8=CPP
                $datos['id_herramienta']        =$id_causa;
                $datos['usuario_responsable']   =$this->input->post("responsable");
                $datos['hallazgo']              =$causa['causa_raiz'];
                $datos['id_grado_crit']         =$evento['id_criticidad'];
                $datos['tarea']                 =$this->input->post("tarea");
                $datos['fecha_vto']             =$this->input->post("fecha");
                $datos['id_estado']             =1;

                $control=true;
                foreach ($datos as $dato)
                {
                    if($dato=="")
                        $control=false;
                }
                if ($control)
                {
                    $this->load->model('cpp/cpp_causas_model','causas',true);
                    $id_evento = $this->causas->dameIdEventoDeCausa($id_causa);

                    $this->load->model('cpp/cpp_eventos_model','eventos',true);
                    $btn_tarea = $this->eventos->controlBtnTarea($id_evento,$id_usuario);
                    
                    if($btn_tarea == 1)
                    {
                        //verifico que no sea rePost
                        $this->load->model("gestion_riesgo/gr_tareas_model","gr_tareas",true);

                        if ($this->gr_tareas->verificaRePost($datos)==0)
                        {
                            $insert_id=$this->gr_tareas->insert($datos);
                            if($insert_id)
                            {
                                //inserto historial de accion
                                $id_accion=1;
                                $texto="Nueva tarea desde CPP";
                                $this->grabar_accion($id_accion, $insert_id, $texto);
                                
                                //grabo en auditoría de CPP
                                $id_accion=10; //10=Registra MC
                                $obs='Tarea Nro:'.$insert_id;
                                $this->_mef($id_evento,$id_accion,$obs);
                                
                                $q_tareas = $this->eventos->dameQTreas($id_evento);
                                $data['q_tareas'] = $q_tareas+1;
                                $update = $this->eventos->update($id_evento,$data);
                                
                                if($update)
                                {
                                    $this->_preparar_enviar_mail($insert_id,'alta');
                                    echo 1;
                                }
                                else
                                    echo 2;
                            }
                            else
                                echo 2; //Error al insertar el registro
                        }
                        else
                            echo 0;//Registro repetido (RePost)
                    }
                    else
                        echo 2;
                }
                else
                    echo 3;//Faltan campos requeridos
             }
             else
                 echo -2;//No tiene permisos
        }
        else
                echo -1; //No tiene permisos
    }
    public function verifica_tarea()
    {
        if ($this->permisos['Alta'])		
        {
            $id_usuario=$this->user['id'];
            $id_evento=$this->input->post("id_evento");
            $id_causa=$this->input->post("id_causa");
            $id_tarea=$this->input->post("id_tarea");
            
            $this->load->model("cpp/cpp_eventos_model","eventos",true);
            $id_estado=6;//Cierre preliminar
            $verificaEventoCausaTarea=$this->eventos->verificaEventoCausaTarea($id_evento,$id_causa,$id_tarea,$id_estado);
            
            $this->load->model("cpp/cpp_causas_model","causas",true);
            $causa= $this->causas->dameCausa($id_causa);
            $evento= $this->eventos->dameEvento($causa['id_evento']);
            
            if($evento['id_usuario_alta']==$id_usuario)
            {
                if ($verificaEventoCausaTarea!=0)
                {
                    $this->load->model('gestion_riesgo/gr_tareas_model','gr_tarea',true);
                    $datos['eficiencia']=1;
                    $datos['usuario_eficiencia']=$id_usuario;
                    $datos['fecha_eficiencia']=date("Y-m-d H:i:s");
                    $update=$this->gr_tarea->update($id_tarea,$datos);

                    if($update)
                    {
                        $id_accion=11;
                        $obs='Verificada tarea nro:'.$id_tarea;
                        $this->_mef($id_evento,$id_accion,$obs);

                        $q_tareasVerificadas=$this->gr_tarea->contarTareasCppEvaluadas($id_evento);
                        if($evento['q_tareas']==$q_tareasVerificadas)
                        {
                            $id_accion=13;
                            $obs='Se verifica evento';
                            $this->_mef($id_evento,$id_accion,$obs);
                        }
                        echo 1;//todo ok
                    }
                    else
                        echo -3;//error al actualizar tareas
                }
                 else
                     echo -2;//error al verificar datos
            }
            else
                echo -4;
        }
        else
            echo -1; //No tiene permisos
    }
    public function rechazar_tarea()
    {
        if ($this->permisos['Alta'])		
        {
            $id_usuario=$this->user['id'];
            $id_evento=$this->input->post("id_evento");
            $id_causa=$this->input->post("id_causa");
            $id_tarea=$this->input->post("id_tarea");
            
            $this->load->model("cpp/cpp_eventos_model","eventos",true);
            $id_estado=6;//Cierre preliminar
            $verificaEventoCausaTarea=$this->eventos->verificaEventoCausaTarea($id_evento,$id_causa,$id_tarea,$id_estado);
            
            $this->load->model("cpp/cpp_causas_model","causas",true);
            $causa= $this->causas->dameCausa($id_causa);
            $evento= $this->eventos->dameEvento($causa['id_evento']);
            
            if($evento['id_usuario_alta']==$id_usuario)
            {
                if ($verificaEventoCausaTarea!=0)
                {
                    $this->load->model('gestion_riesgo/gr_tareas_model','gr_tarea',true);
                    $datos['eficiencia']=2;
                    $datos['usuario_eficiencia']=$id_usuario;
                    $datos['fecha_eficiencia']=date("Y-m-d H:i:s");
                    $update=$this->gr_tarea->update($id_tarea,$datos);

                    if($update)
                    {
                        $datos_evento['fecha_cierre']=NULL;
                        $datos_evento['espera_ini']= NULL;
                        $datos_evento['espera_fin']= NULL;
                        $datos_evento['verifica_ini']= NULL;
                        $datos_evento['verifica_fin']= NULL;
//                      echo "<pre>".print_r($datos,true)."</pre>";die();
                        $update=$this->eventos->update($id_evento,$datos_evento);
                        
                        $id_accion=12;
                        $obs='Rechazada tarea nro:'.$id_tarea;
                        $this->_mef($id_evento,$id_accion,$obs);
                        echo 1;//todo ok
                    }
                    else
                        echo -3;//error al actualizar tareas
                }
                 else
                     echo -2;//error al verificar datos
            }
            else
                echo -4;
        }
        else
            echo -1; //No tiene permisos
    }
//    function pruebaMail($id_tarea)
//    {
//        $this->_preparar_enviar_mail($id_tarea);
//    }
    public function _preparar_enviar_mail($id_tarea="",$accion="alta")
        {
            if($id_tarea!="")
            {
                
                $this->load->model('gestion_riesgo/gr_usuarios_model','gr_usuarios',true);
                $this->load->model('gestion_riesgo/gr_tareas_model','gr_tarea',true);
                $this->load->model("cpp/cpp_eventos_model","eventos",true);

                //traigo todos los datos de la tarea
                $this->load->model('gestion_riesgo/gr_tareas_model','gr_tarea',true);
                $tarea=$this->gr_tarea->dameTarea($id_tarea);
//                echo "<pre>".print_r($tarea,true)."</pre>";
                $this->load->model('cpp/cpp_causas_model','causas',true);
                //traigo todos los datos del evento
                $id_evento = $this->causas->dameIdEventoDeCausa($tarea['id_herramienta']);
                $evento= $this->eventos->dameEvento($id_evento);
//                echo "<pre>".print_r($evento,true)."</pre>";

                //traigo todos los datos del usuario alta y del usuario responsable
                $usuarioAlta=$this->gr_usuarios->dameUsuarioPorId($tarea['usuario_alta']);
                $usuarioResp=$this->gr_usuarios->dameUsuarioPorId($tarea['usuario_responsable']);
                $puestosId[]=$usuarioResp['id_puesto'];
//                $idUsuarioNot=$usuarioAlta['id_usuario'];
                    
                $usuario=$this->user['nombre']." ".$this->user['apellido'];
                $datos=array();
                
                $datos['accion']=$accion;
                $datos['id_tarea']=$tarea['id_tarea'];
                $datos['tarea']=$tarea['tarea'];
                $datos['hallazgo']=$tarea['hallazgo'];
                $datos['fecha_limite']=$tarea['fecha_vto'];
                $datos['tipo_herramienta']=$tarea['th'];
                $datos['adjuntos']=0;
                $datos['usuario']=$usuarioResp['persona'];
                $datos['usuariosCC']="";
                $datos['usuarioPara']=$usuarioResp['email'];
                $datos['usuarioRtte']=$usuarioAlta['persona'];
                $datos['generoUsuarioPara']=$usuarioResp['genero'];
                $datos['motivo']="";
                $datos['evento']=$evento;
//                echo "<pre>".print_r($datos,true)."</pre>";
                $this->_enviarMail($datos);
            }
        }
        function _enviarMail($datos)
        {
            $link='www.polidata.com.ar/sdj/admin';
                        
$mail_alta=<<<HTML
<html> 
    <head>
        <title>SMC</title>
        
    </head>
    <body>
    <div style="color:#000000; font-family:Verdana, Arial, Helvetica, sans-serif; text-align:Left; font-size:13px;">
        <pre>
            #persona,

            Usted fue #nominado por <b>#usuarioRtte</b> como responsable para resolver la siguiente tarea:
            
            <b>Descripci&oacute;n:</b>
            <em>#hallazgo</em>
            
            <b>Tarea:</b>
            <em>#tarea</em>
                        
            <b>Vencimiento:</b>
            #fecha_limite
            
            <b>Tipo de Herramienta:</b>
            #tipo_herramienta
        
            <b>Evento Nro:</b>#eventoNro
            <b>Fecha Inicio:</b>#fechaIni
            <b>Fecha Fin:</b>#fechaFin
            <b>Producto:</b>#producto
            <b>Descripción:</b>
            <em>#descripcion</em>
            
            Puede confirmar su gesti&oacute;n o rechazarla ingresando al sistema desde <a href="#link" target="_blank">aqu&iacute;</a>, o copiando y pegando la siguiente direcci&oacute;n en su navegador web <b>Mozilla Firefox</b> o <b>Google Chrome</b>: 
            #link
            

            Atentamente
            #firma
            
            Importante: Mensaje generado autom&aacute;ticamente, por favor no responder.
            <hr>
              Para: #usuarioPara <br>
              CC  : #usuariosCC
           <hr>

        </pre>
        </div>
    </body>
</html>
HTML;



            $cuerpo_mail=$mail_alta;
            $subject='Nueva Tarea Nro: '.$datos['id_tarea'].", desde CPP";
                 
             if ($datos['generoUsuarioPara']=='F')
             {
                $cuerpo_mail=str_replace("#persona"       , 'Estimada '.$datos['usuario'], $cuerpo_mail);
                $cuerpo_mail=str_replace("#nominado"       , 'nominada ' , $cuerpo_mail);
                 
             }
             else
             {
                $cuerpo_mail=str_replace("#persona"       , 'Estimado '.$datos['usuario']      , $cuerpo_mail);
                $cuerpo_mail=str_replace("#nominado"       , 'nominado ' , $cuerpo_mail);
                 
             }
             
             $cuerpo_mail=str_replace("#usuarioPara"   , $datos['usuarioPara']  , $cuerpo_mail);
             $cuerpo_mail=str_replace("#usuariosCC"    , $datos['usuariosCC']   , $cuerpo_mail);
             $cuerpo_mail=str_replace("#usuarioRtte"   , $datos['usuarioRtte']  , $cuerpo_mail);
             $cuerpo_mail=str_replace("#tarea"         , $datos['tarea']        ,$cuerpo_mail);
             $cuerpo_mail=str_replace("#hallazgo"      , $datos['hallazgo']     ,$cuerpo_mail);
             $cuerpo_mail=str_replace("#fecha_limite"  , $datos['fecha_limite'] ,$cuerpo_mail);
             $cuerpo_mail=str_replace("#tipo_herramienta"  , $datos['tipo_herramienta'] ,$cuerpo_mail);
             $cuerpo_mail=str_replace("#link"          , $link                  ,$cuerpo_mail);
             $cuerpo_mail=str_replace("#link"          , $link                  ,$cuerpo_mail);
             $cuerpo_mail=str_replace("#firma"         , htmlentities(NOMSIS,ENT_QUOTES,"UTF-8"),$cuerpo_mail);
             
             //Datos del evento
             $cuerpo_mail=str_replace("#eventoNro",     $datos['evento']['id_evento'],$cuerpo_mail);
             $cuerpo_mail=str_replace("#fechaIni",      $datos['evento']['fh_inicio'],$cuerpo_mail);
             $cuerpo_mail=str_replace("#fechaFin",      $datos['evento']['fh_fin'],$cuerpo_mail);
             $cuerpo_mail=str_replace("#producto",      $datos['evento']['producto'],$cuerpo_mail);
             $cuerpo_mail=str_replace("#descripcion",   $datos['evento']['descripcion'],$cuerpo_mail);
             
             
             $this->load->library('email');
//             $this->email->clear();//para cuando uso un bucle
            $this->email->from(SYS_MAIL, NOMSIS);
            $this->email->message($cuerpo_mail);
//            $this->email->set_alt_message();//Sets the alternative email message body
//            $this->email->reply_to('gestiondedocumentos@salesdejujuy.com','DNS');
            $this->email->to($datos['usuarioPara']);
//            $this->email->cc($datos['usuariosCC']);//--->eliminado el 26/11/15 por pedido A.Aleman (#28)
            $this->email->subject($subject);
            IF (MAILBCC && MAILBCC !="")
                $this->email->bcc(MAILBCC);
//            if($datos['adjuntos']!=0)
//            {
//                $n=0;
//                $kb=0;
//                foreach($datos['adjuntos'] as $archivo)
//                {
//                    $n++;
//                    $kb+=$archivo['tam'];
//
//                }
//                if($n<=5 && $kb<=5120000)
//                {
//                    foreach($datos['adjuntos'] as $archivo)
//                    {
//                        $this->email->attach($archivo['archivo']);
//                    }
//                    
//                }
//            }
            if (ENPRODUCCION)
            {
                if($this->email->send())
                    return 0;
                else
                    return 2;
            }
//            else
//            {
//                    echo $cuerpo_mail;
//                    return 0;
//            }
//            $this->email->print_debugger();

        }
     public function grabar_accion($id_accion,$id_tarea,$texto)
    {
        $this->load->model("gestion_riesgo/gr_historial_acciones_model","gr_historial_acciones",true);

        $datos['id_usuario']    =$this->user['id'];
        $datos['id_tarea']      =$id_tarea;
        $datos['id_accion']     =$id_accion;
        $datos['texto']         =$texto;

        $insert_id=$this->gr_historial_acciones->insert($datos);
        return $insert_id;
            
    }
    private function _mef($id_evento,$id_accion,$obs=null)
    {
        $this->load->model('cpp/cpp_eventos_model','evento',true);
        $evento=$this->evento->dameEvento($id_evento);
        $datos['id_usuario']   = $this->user['id'];
        $this->load->model('cpp/cpp_workflow_model','wf_model',true);
        $id_estado_actual=$evento['id_estado'];
        $id_estado_siguiente=$this->wf_model->dameEstadoSiguiente($id_estado_actual,$id_accion);
        if ($id_estado_siguiente!=0)
        {
            $this->_auditoria($id_evento,$id_estado_actual,$id_accion,$id_estado_siguiente,$obs);
            $update=$this->evento->updateEstado($id_evento,$id_estado_siguiente);
        }
    }
    private function _auditoria($id_evento,$id_estado_actual,$id_accion,$id_estado_siguiente,$obs=null)
    {
        $this->load->model('cpp/cpp_eventos_model','evento',true);
        $this->load->model('cpp/cpp_auditoria_evento_model','auditoria_evento',true);
        $datos['id_evento']             = $id_evento;
        $datos['id_usuario_accion']     = $this->user['id'];
        $datos['id_estado_anterior']    = ($id_estado_actual==0)?NULL:$id_estado_actual;
        $datos['id_estado_siguiente']   = $id_estado_siguiente;
        $datos['id_accion']             = $id_accion;
        $datos['obs']                   =  is_array($obs)?implode(" - ", $obs):$obs;
        $insert=$this->auditoria_evento->insert($datos);
    }
    
    public function set_criticidad()
    {
        if ($this->permisos['Modificacion'])
        {
            $id_evento                      = $this->input->post('id_evento');
            $datos['id_usuario_set_crit']   = $this->user['id'];
            $datos['id_criticidad']         = $this->input->post('id_criticidad');
            $datos['fecha_set_crit']        = date("Y-m-d H:i:s");
            
//            echo "<pre>".print_r($datos,true)."</pre>";die();
            if ($id_evento != "")
            {
                $this->load->model('cpp/cpp_eventos_model','eventos',true);
                $control=true;
                foreach ($datos as $dato)
                {
                    if($dato=="")
                        $control=false;
                }
                if ($control)
                {
                    $crit = $this->eventos->checkCriticidad($id_evento);
                    
                    if($crit)
                    {
                        $id_accion                      = 8;
                        $this->load->model('usuarios_model','usuarios',true);
                        $this->load->model('cpp/cpp_botones_permiso_model','botones',true);
                        $id_gcia=$this->usuarios->dameGerenciaUsuario($this->user['id']);
                        $estadosBotonEditCrit=$this->botones->dameEstados($id_gcia,'btn_edit_crit');

                        $estadosBotones['btn_edit_crit']=($estadosBotonEditCrit == 0)?0:implode(',',$estadosBotonEditCrit);

                        $btn_edit_crit = $this->eventos->controlBtnEditCrit($id_evento,$estadosBotones);
                        if($btn_edit_crit == 1)
                        {
                            $this->load->model('cpp/cpp_eventos_model','eventos',true);	
                                if ($this->eventos->update($id_evento,$datos))
                                {
                                    $this->_mef($id_evento,$id_accion);
                                    echo "({success: true, msg:'Datos guardados...'})";
                                }
                                else
                                    echo "({success: false, msg:'Error al guardar la información'})";
                        }
                        else
                            echo "({success: false, error :'No tiene permisos para realizar la acción solicitada'})";           
                    }
                    else
                    {
                        $id_accion                      = 3;
                        $btn_crit = $this->eventos->controlBtnCrit($id_evento,$this->user['id']);

                        if($btn_crit == 1)
                        {

                                $this->load->model('cpp/cpp_eventos_model','eventos',true);	
                                if ($this->eventos->update($id_evento,$datos))
                                {
                                    $this->_mef($id_evento,$id_accion);
                                    echo "({success: true, msg:'Datos guardados...'})";
                                }
                                else
                                    echo "({success: false, msg:'Error al guardar la información'})";
                        }
                        else
                            echo "({success: false, error :'No tiene permisos para realizar la acción solicitada'})"; //
                    }
                }
                else
                    echo "({success: false, msg:'Verifique campos obligatorios...'})";
            }
            else
                echo "({success: false, msg:'Error al guardar la información'})";
        }
        else
            echo "({success: false, error :'No tiene permisos para realizar la acción solicitada'})"; //
    }
    

    
    public function update_monto()
    {
        if ($this->permisos['Modificacion'])
        {
            $id_ec                          = $this->input->post('id_ec');
            $datos['id_usuario_set_monto']  = $this->user['id'];
            $datos['monto']                 = $this->input->post('monto');
            $datos['fecha_set_monto']       = date("Y-m-d H:i:s");
            $datos['set_monto']             = 1;
//            echo "<pre>".print_r($datos,true)."</pre>";die();
            $this->load->model('cpp/cpp_botones_permiso_model','botones',true);
            $this->load->model('usuarios_model','usuarios',true);
            
            $id_gcia=$this->usuarios->dameGerenciaUsuario($this->user['id']);
            
            $estadosBotonMonto=$this->botones->dameEstados($id_gcia,'btn_monto');
                             
            $estadosBotones['btn_monto']=($estadosBotonMonto == 0)?0:implode(',',$estadosBotonMonto);
            
            if($estadosBotones['btn_monto'] != 0)
            {
                $control=true;
                foreach ($datos as $dato)
                {
                    if($dato=="")
                        $control=false;
                }
                if ($control)
                {

                    $this->load->model('cpp/cpp_eventos_model','eventos',true);
                    $this->load->model('cpp/cpp_evento_consecuencias_model','evento_consecuencias',true);
                    $id_evento = $this->evento_consecuencias->dameIdEventoDeConsecuencia($id_ec);
                    $btn_monto = $this->eventos->controlBtnMonto($id_evento,$estadosBotones);

                    if($btn_monto == 1)
                    {
                        $this->load->model('cpp/cpp_evento_consecuencias_model','ec_model',true);	
                        if ($this->ec_model->update($id_ec,$datos))
                        {
                            echo "({success: true, msg:'Datos guardados...'})";
                        }
                        else
                             echo "({success: false, msg:'Error al guardar la información'})";
                    }
                    else
                         echo "({success: false, error :'No tiene permisos para realizar la acción solicitada'})"; //
                }
                else
                     echo "({success: false, msg:'Verifique campos obligatorios...'})";
            }
            else
                echo "({success: false, error :'No tiene permisos para realizar la acción solicitada'})"; //
        }
        else
            echo "({success: false, error :'No tiene permisos para realizar la acción solicitada'})"; //
    }
    
    public function update_toneladas()
    {
        if ($this->permisos['Modificacion'])
        {
            $id_ec                              = $this->input->post('id_ec');
            $datos['id_usuario_set_toneladas']  = $this->user['id'];
            $datos['unidades_perdidas']         = $this->input->post('unidades_perdidas');
            $datos['fecha_set_toneladas']       = date("Y-m-d H:i:s");
//            $datos['set_toneladas']             = 1;
//            echo "<pre>".print_r($datos,true)."</pre>";die();
            
            $this->load->model('cpp/cpp_botones_permiso_model','botones',true);
            $this->load->model('usuarios_model','usuarios',true);
            
            $id_gcia=$this->usuarios->dameGerenciaUsuario($this->user['id']);
            
            $estadosBotonTn=$this->botones->dameEstados($id_gcia,'btn_tn');
            $estadosBotones['btn_tn']=($estadosBotonTn == 0)?0:implode(',',$estadosBotonTn);
            
            if($estadosBotones['btn_tn'] != 0)
            {
                $control=true;
                foreach ($datos as $dato)
                {
                    if($dato=="")
                        $control=false;
                }
                if ($control)
                {

                    $this->load->model('cpp/cpp_eventos_model','eventos',true);
                    $this->load->model('cpp/cpp_evento_consecuencias_model','ec_model',true);	
                    $id_evento = $this->ec_model->dameIdEventoDeConsecuencia($id_ec);
                    $btn_tn = $this->eventos->controlBtnTn($id_evento,$estadosBotones);

                    if($btn_tn == 1)
                    {
                        if ($this->ec_model->update($id_ec,$datos))
                        {
                            echo "({success: true, msg:'Datos guardados...'})";
                        }
                        else
                             echo "({success: false, msg:'Error al guardar la información'})";
                    }
                    else
                        echo "({success: false, error :'No tiene permisos para realizar la acción solicitada'})"; //
                }
                else
                     echo "({success: false, msg:'Verifique campos obligatorios...'})";
            }
            else
                echo "({success: false, error :'No tiene permisos para realizar la acción solicitada'})"; //
        }
        else
            echo "({success: false, error :'No tiene permisos para realizar la acción solicitada'})"; //
    }
    
    public function combo_areas_causante()
    {
        $start = $this->input->post("start");
        $limit = $this->input->post("limit");
        $busqueda = $this->input->post("query");
        $this->load->model('cpp/cpp_areas_causante_model','areas_causante',true);
        $var=$this->areas_causante->dameCombo($start,$limit,$busqueda);
        echo $var;	
    }
    
    public function insert_causa()
        {
        if ($this->permisos['Alta'])
        {
            $datos['id_ac']             = $this->input->post('id_ac');
            $datos['id_usuario_alta']   = $this->user['id'];
            $datos['id_evento']         = $this->input->post('id_evento');
            $datos['causa_raiz']        = $this->input->post('causa_raiz');
            $datos['causa_inmediata']   = $this->input->post('causa_inmediata');
            $id_accion                  = 5;
            $id_evento                  = $datos['id_evento'];
//            echo "<pre>".print_r($datos,true)."</pre>";die();
            $this->load->model('cpp/cpp_eventos_model','eventos',true);
            $btn_causa = $this->eventos->controlBtnCausa($id_evento,$this->user['id']);
 
            if($btn_causa == 1)
            {
                $control=true;
                foreach ($datos as $dato)
                {
                    if($dato=="")
                        $control=false;
                }
                if ($control)
                {
                    $this->load->model('cpp/cpp_causas_model','causas',true);
                    $insert_id=$this->causas->insert($datos);
                    if ($insert_id)
                    {
                        $obs='Causa Nro:'.$insert_id;
                        $this->_mef($id_evento,$id_accion,$obs);
                        echo "({success: true, msg:'Datos guardados...'})";
                    }
                    else
                        echo "({success: false, msg:'Error al guardar la información'})";
                }
                else
                     echo "({success: false, msg:'Verifique campos obligatorios...'})";
            }
            else
                "({success: false, error :'No tiene permisos para realizar la acción solicitada'})"; //
        }
        else
            echo "({success: false, error :'No tiene permisos para realizar la acción solicitada'})"; //
    }
    
    public function cancelarEvento()
    {
        if ($this->permisos['Modificacion'])
        {
            $id_accion                  = 7;
            $id_evento = $this->input->post('id_evento');
//            echo $id_evento;
            if ($id_evento != 0 && $id_evento != null)
            {
                $this->load->model('usuarios_model','usuarios',true);
                $this->load->model('cpp/cpp_botones_permiso_model','botones',true);
		$id_usuario=$this->user['id'];
                $id_gcia=$this->usuarios->dameGerenciaUsuario($id_usuario);

                $estadosBotonCancelar=$this->botones->dameEstados($id_gcia,'btn_cancelar');
                $estadosBotones['btn_cancelar']=($estadosBotonCancelar==0)?0:implode(',',$estadosBotonCancelar);

                if($estadosBotones['btn_cancelar'] != 0)
                {
                    $this->load->model('cpp/cpp_eventos_model','eventos',true);
                    $btn_cancelar = $this->eventos->controlBtnCancelar($id_evento,$estadosBotones);

                    if($btn_cancelar == 1)
                    {
                        $this->_mef($id_evento,$id_accion);
                        echo "({success: true, msg: 0})";
                    }
                    else
                        echo "({success: false, msg :'No tiene permisos para realizar la acción solicitada'})";
                }
                else
                    echo "({success: false, msg :'No tiene permisos para realizar la acción solicitada'})";
            }
            else
                echo "({success: false, msg :'Evento igual a 0'})";
        }
        else
            echo "({success: false, msg :'No tiene permisos para realizar la acción solicitada'})";
    }
    
    public function cerrarEvento()
    {
        if ($this->permisos['Modificacion'])
        {
            $id_accion                  = 6;
            $id_evento = $this->input->post('id_evento');
            $dias_espera = $this->input->post('dias_esp');
            if ($dias_espera<=0 || $dias_espera>365)
                exit ("({success: false, msg :'Los días de espera deben estar entre 1 y 365'})");
                
            $dias_verificacion = $this->input->post('dias_ver');
            if ($dias_verificacion<=0 || $dias_verificacion>365)
                exit ("({success: false, msg :'Los días para la verificación deben estar entre 1 y 365'})");
//            echo $id_evento;
            if ($id_evento != 0 && $id_evento != null)
            {
                $this->load->model('cpp/cpp_eventos_model','eventos',true);
                $btn_cerrar = $this->eventos->controlBtnCerrar($id_evento,$this->user['id']);

                if($btn_cerrar == 1)
                {   
                    
                    $obs='DíasEsp-DíasVer='.$dias_espera."-".$dias_verificacion;
                    $objetoHoy=date_create(date("Y-m-d H:i:s"));
                     $calculoFechaIni_1=date_add(clone $objetoHoy, date_interval_create_from_date_string("1 days"));
                    $dias_espera=(int)$dias_espera-1;
                     $calculoFechaFin_1=date_add(clone $calculoFechaIni_1, date_interval_create_from_date_string($dias_espera." days"));
                    $calculoFechaIni_2=date_add(clone $calculoFechaFin_1, date_interval_create_from_date_string("1 days"));
                    $dias_verificacion=(int)$dias_verificacion-1;
                    $calculoFechaFin_2=date_add(clone $calculoFechaIni_2, date_interval_create_from_date_string($dias_verificacion." days"));
                    
                    
                    $datos['fecha_cierre']=date_format($objetoHoy,"Y-m-d H:i:s");
                    $datos['espera_ini']= date_format($calculoFechaIni_1,"Y-m-d");
                    $datos['espera_fin']= date_format($calculoFechaFin_1,"Y-m-d");
                    $datos['verifica_ini']= date_format($calculoFechaIni_2,"Y-m-d");
                    $datos['verifica_fin']= date_format($calculoFechaFin_2,"Y-m-d");
//                    echo "<pre>".print_r($datos,true)."</pre>";die();
                    $update=$this->eventos->update($id_evento,$datos);
                    
                    $this->_mef($id_evento,$id_accion,$obs);
                    
                    echo "({success: true, msg: 'Cierre preliminar realizado!'})";
                }
                else
                    echo "({success: false, msg :'No tiene permisos para realizar la acción solicitada'})";
            }
            else
                echo "({success: false, msg :'Error vinculando Evento'})";
        }
        else
            echo "({success: false, msg :'No tiene permisos para realizar la acción solicitada'})";
    }
    
    public function listado_causas()
    {
        if ($this->permisos['Listar'])
        {
            $id_evento =$this->input->post('evento');
            $this->load->model('cpp/cpp_causas_model','causas',true);
            $start = $this->input->post("start");
            $limit = $this->input->post("limit");
            $sort = $this->input->post("sort");
            $dir = $this->input->post("dir");
            $listado = $this->causas->listado($id_evento,$start, $limit, $sort,$dir);
            echo $listado;
        }
        else
            echo -1; //No tiene permisos
    }
    public function combo_responsables()
    {
        $limit=$this->input->post("limit");
        $start=$this->input->post("start");
        $query=$this->input->post("query");
        $id_usuario_not=$this->user['id'];
        $this->load->model("usuarios_model","usuarios",true);
        $jcode = $this->usuarios->usuariosComboCPP($limit,$start,$query,$id_usuario_not);
        echo $jcode;
    }
    
    public function eliminarEvento()
    {
        if ($this->permisos['Baja'])
        {
            $id_evento = $this->input->post('id_evento');
            $datos['habilitado'] = 0;
//            echo $id_evento;
            if ($id_evento != 0 && $id_evento != null)
            {
                $this->load->model('cpp/cpp_eventos_model','eventos',true);
//                $this->eventos->update($id_evento,$datos);
                if($this->eventos->update($id_evento,$datos))
                    echo "({success: true, msg: 0})";
                else
                    echo "({success: false, msg:'Error al guardar la información'})";
            }
            else
                echo "({success: false, msg :'Evento igual a 0'})";
        }
        else
            echo "({success: false, msg :'No tiene permisos para realizar la acción solicitada'})";
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

//            echo "<pre>".print_r($areas,true)."</pre>";die();

        return $areas;
            
    }
    public function filtroCriticidad()
    {
        $start = $this->input->post("start");
        $limit = $this->input->post("limit");
        $this->load->model('cpp/cpp_criticidades_model','criticidades',true);
        $var=$this->criticidades->dameCombo($start,$limit);
        echo $var;
    }
    
    public function filtroEstado()
    {
        $start = $this->input->post("start");
        $limit = $this->input->post("limit");
        $this->load->model('cpp/cpp_estados_model','estados',true);
        $var=$this->estados->dameCombo($start,$limit);
        echo $var;
    }
	        
    public function datos_excel()
    {
        if ($this->permisos['Listar'])
        {
            $this->load->library('excel');
            $this->load->model('cpp/cpp_eventos_model','eventos',true);
            $start = 0;
            $limit = 1000;
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
//            echo $busqueda;
//            echo "<pre>".print_r($campos,true)."</pre>";
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

            $datos = $this->eventos->listado_excel($start, $limit, $sort,$dir,$busqueda,$campos,$filtros); 
            $titulo = 'Eventos';
            $cabecera = array('#','usuario Alta','Fecha Alta','Inicio','Fin','Estado','Fecha Evento','Descripción','Equipo','Producto','Sector','Criticidad','Hrs','U$D','TN');
            $estiloCabecera=array(
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
            $estiloDatos=array(
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
            $estiloTitulo=array(
                'font' => array(
                    'size' => 14,
                    'bold' => true
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                    'rotation' => 0,
                    'wrap' => TRUE
                )
            );

            $this->generar_excel_listado($datos,$cabecera,$titulo,$estiloCabecera,$estiloDatos,$estiloTitulo);
        }
        else
            echo -1; //No tiene permisos
    }
        
    public function generar_excel_listado($datos,$cabecera,$titulo="",$estiloCabecera="",$estiloDatos="",$estiloTitulo="")//,$estiloTitulo
    {
        $filename = ($titulo != "")?'Listado - '.$titulo.'.xls':'Listado.xls';
        header('Content-Type:application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name          
        header('Cache-Control: max-age=0'); //no cache


        $this->excel->getProperties()
            ->setCreator("Sistema de Mejora Continua (SMC)") // Nombre del autor
            ->setLastModifiedBy("Sistema de Mejora Continua (SMC)") //Ultimo usuario que lo modificó
            ->setTitle(($titulo != "")?'Listado - '.$titulo.'.xls':'Listado.xls') // Titulo
            ->setSubject(($titulo != "")?'Listado - '.$titulo.'.xls':'Listado.xls') //Asunto
            ->setDescription(($titulo != "")?'Listado - '.$titulo.'.xls':'Listado.xls') //Descripción
            ->setKeywords(($titulo != "")?'Listado - '.$titulo.'.xls':'Listado.xls') //Etiquetas
            ->setCategory(($titulo != "")?'Listado - '.$titulo.'.xls':'Listado.xls');
        $getActiveSheet=$this->excel->getActiveSheet();

        $setActiveSheetIndex=$this->excel->setActiveSheetIndex(0);
        $lastColumn = 'A';
        $i = 1;
        if ($titulo != "")
        {
            $cont = count($cabecera);
            $o = 1;
            while ($o != $cont)
            {
                $lastColumn++;
                $o++;
            }
//                $lastColumn = $lastColumn + $cont;
            $getActiveSheet->mergeCells('A'.$i.':'.$lastColumn.$i);
            $getActiveSheet->setCellValue('A'.$i,'Listado - '.$titulo);
            if($estiloTitulo != "")
            {
                $getActiveSheet->getStyle('A'.$i)->applyFromArray($estiloTitulo);
            }
            $i = $i + 2;
            $lastColumn = 'A';
        }

        foreach($cabecera as $row)
        {
            $getActiveSheet->setCellValue($lastColumn.$i,$row);
            if($estiloCabecera != "" || $estiloCabecera != null)
            {
                $getActiveSheet->getStyle($lastColumn.$i)->applyFromArray($estiloCabecera);
            }
            $lastColumn++;
        }
        $i++;
        $lastColumn = 'A';

        foreach ($datos as $reg)
        {
            foreach($reg as $linea)
            {
                $getActiveSheet->setCellValue($lastColumn.$i,$linea);
                $getActiveSheet->getColumnDimension($lastColumn)->setAutoSize(true);
                if($estiloDatos != "" || $estiloDatos != null)
                {
                    $getActiveSheet->getStyle($lastColumn.$i)->applyFromArray($estiloDatos);
                }
                $lastColumn++;
            }
            $i++;
            $lastColumn = 'A';
        }

        $getActiveSheet->getPageSetup()->setFitToWidth(1);
        $getActiveSheet->setTitle(($titulo != "")?'Listado - '.$titulo.'.xls':'Listado.xls');
//            $getActiveSheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $getActiveSheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);


        //save it to Excel5 format (excel 2003 .XLS file), chan ge this to 'Excel2007' (and adjust the filename extension, also the header mime type)
        //if you want to save it as .XLSX Excel 2007 format
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
        ob_end_clean();
        //force user to download the Excel file without writing it to server's HD
        $objWriter->save('php://output');
    }
    
    public function datos_crit ()
    {
        $id_evento=$this->input->post("id");
     
        $this->load->model('cpp/cpp_eventos_model','cpp_eventos',true);
        $criticidad=$this->cpp_eventos->dameDatosCriticidadParaForm($id_evento);
//            echo "<pre>" . print_r($criticidad, true) . "</pre>";die();
        
        echo $criticidad;	
    }
    
    public function datos_evento ()
    {
        $id_evento=$this->input->post("id");
     
        $this->load->model('cpp/cpp_eventos_model','cpp_eventos',true);
        $evento=$this->cpp_eventos->dameDatosEventoParaForm($id_evento);
//            echo "<pre>" . print_r($criticidad, true) . "</pre>";die();
        
        echo $evento;	
    }
    
    function _dameSupervisorHabilitado($id_usuario)
    {
        $this->load->model('usuarios_model','usuarios',true);
            
        $supervisor=$this->usuarios->dameSupervisor($id_usuario);
//        echo "<pre>".print_r($supervisor,true)."</pre>";
        if($supervisor != 0)
        {
            $checkEditor=$this->usuarios->checkUsuarioHabilitado($supervisor[0]['id_usuario']);
            if($checkEditor)
                return $supervisor;
            else
                $this->_dameSupervisorHabilitado($supervisor[0]['id_usuario']);
        }
        else
            return 0;
//        echo "<pre>".print_r($supervisor,true)."</pre>";
    }
    
    
    function _enviarMailInvestigadores($data)
    {
//        echo "<pre>1".print_r($data,true)."</pre>";
        $link='www.polidata.com.ar/sdj/admin';
                        
$mail_inv=<<<HTML
<html> 
    <head>
        <title>SMC</title>
        
    </head>
    <body>
    <div style="color:#000000; font-family:Verdana, Arial, Helvetica, sans-serif; text-align:Left; font-size:13px;">
        <pre>
            #intro,

            #numero designado por #Nominador para ser investigador del siguiente evento:
            
            <b>Evento Nro:</b>
            <em>#id</em>
            
            <b>Descripci&oacute;n:</b>
            <em>#descripcion</em>
        
            <b>Investigador/es:</b>
            <em><table>#investigador</table></em>
            
            Por favor, complete su gesti&oacute;n ingresando al sistema desde <a href="#link" target="_blank">aqu&iacute;</a>, o copiando y pegando la siguiente direcci&oacute;n en su navegador web <b>Mozilla Firefox</b> o <b>Google Chrome</b>: 
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
        
        $cuerpo_mail=$mail_inv;
        
        $subject='Investigador nominado para evento Nro'.$data['id_evento'];
        
        if($data['cant_inv'] > 1)
        {
            $cuerpo_mail=str_replace("#intro"           , 'Estimados usuarios'  , $cuerpo_mail);
            $cuerpo_mail=str_replace("#numero"           , 'Fueron '  , $cuerpo_mail);
        }
        else
        {
            $cuerpo_mail=str_replace("#intro"           , 'Estimado usuario ', $cuerpo_mail);
            $cuerpo_mail=str_replace("#numero"           , 'Usted fue '  , $cuerpo_mail);
        }
            
        
        $cuerpo_mail=str_replace("#Nominador"           , $data['nombreUsuarioAlta']  , $cuerpo_mail);
        
        $cuerpo_mail=str_replace("#usuarioPara"         , $data['emailsInv']  , $cuerpo_mail);
        
        $cuerpo_mail=str_replace("#usuariosCC1"         , $data['emails']   , $cuerpo_mail);
        
        $cuerpo_mail=str_replace("#id"                  , $data['id_evento']     ,$cuerpo_mail);
        
        $cuerpo_mail=str_replace("#descripcion"         , $data['descripcion']        ,$cuerpo_mail);
        
        $cuerpo_mail=str_replace("#investigador"         , $data['investigador']        ,$cuerpo_mail);
        
            
        $cuerpo_mail=str_replace("#link"                , $link                  ,$cuerpo_mail);
        $cuerpo_mail=str_replace("#firma"               , htmlentities(NOMSIS,ENT_QUOTES,"UTF-8"),$cuerpo_mail);

        
         $this->load->library('email');
//             $this->email->clear();//para cuando uso un bucle
        $this->email->from(SYS_MAIL, NOMSIS);
        $this->email->message($cuerpo_mail);
        $this->email->subject($subject);
        
//        echo $cuerpo_mail;
        $this->email->to($data['investigadoresEmail']);
        $this->email->cc($data['emailsCopia']);
        
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