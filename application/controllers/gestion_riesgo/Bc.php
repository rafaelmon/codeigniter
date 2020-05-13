<?php
class Bc extends CI_Controller
{
    private $modulo=23;
    private $user;
    private $permisos;
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
            $this->load->model("permisos_model","permisos_model",true);
            $this->permisos= $this->permisos_model->checkIn($this->user['perfil_id'],$this->modulo);
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
            $variables['btn_tareas'] = $this->user['id'];
            $usuario=$this->_dameAreas($this->user['id']);
            $variables['btn_gr'] = ($usuario['gr']==1)?1:0;
//             echo "<pre>".print_r($variables,true)."</pre>";
            $this->load->view('gestion_riesgo/bc/listado',$variables);
	}
	
	public function listado()
	{
//             echo "<pre>".print_r($this->permisos['Listar'],true)."</pre>";
		if ($this->permisos['Listar'])
                {
                    $id_usuario=$this->user['id'];
                    $listado=array();
                    $this->load->model('gestion_riesgo/gr_bc_model','gr_bc',true);
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
                    $usuario=$this->_dameAreas($id_usuario);
//                    $usuario=array(1);
                    $listado = $this->gr_bc->listado($usuario,$start, $limit, $filtros,$busqueda, $campos, $sort, $dir);
                    echo $listado;
                }
                else
                    echo -1; //No tiene permisos
                
	}
        public function insert()
	{
            if ($this->permisos['Alta'])		
            {
                $datos=array();
                $datos['id_usuario_alta']   =$this->user['id'];
                $datos['id_usuario_inicio'] =$this->input->post("usuario");
                $datos['descripcion']      =$this->input->post("desc");
                $datos['id_estado']      =1;//pendiente
//                echo "<pre>".print_r($datos,true)."</pre>";
                $control=true;
                foreach ($datos as $dato)
                {
                    if($dato==""||$dato==NULL)
                        $control=false;
                }
                if ($control)
                {
                    //verifico que no sea rePost
                    $this->load->model("gestion_riesgo/gr_bc_model","gr_bc",true);

                    if ($this->gr_bc->verificaRePost($datos)==0)
                    {
                        $insert_id=$this->gr_bc->insert($datos);
                        if($insert_id)
                        {
                            $this->_enviarMailAltaBc($insert_id);
                            echo 1; //todo OK
                        }
                        else
                            echo 2; //Error al insertar el registro
                    }
                    else
                        echo 0;//Registro repetido (RePost)
                        
                }
                else
                    echo 3;//Faltan campos requeridos
                
            }
            else
                    echo -1; //No tiene permisos
	}
        public function aprobar()
	{
            if ($this->permisos['Alta'])		
            {
                $datos=array();
                $id_bc =$this->input->post("id");
                $id_usuario_gr = $this->user['id'];
                
                //busco las areas que integran el área de gestión de riesgo
                $this->load->model('gestion_riesgo/gr_areas_model','gr_areas',true);
                $areasInferiores[]=4;

                for($i=0;$i<10;$i++)
                {
                    $subareasGr=$this->gr_areas->dameAreasInferiores($areasInferiores);                
                    if (count($subareasGr)>1)
                    {
                        foreach ($subareasGr as $area)
                        {
                            $arrayAreas[]=$area['id_area'];
                        }
                        $areasInferiores= array_unique(array_merge($areasInferiores,$arrayAreas));
                    }

                }
                
                
                //verifico que el usaurio que esta editando corresponsa con un usuario de GR o JoseDeCastro y que la BC este en estado pendiente
//                $this->load->model('gestion_riesgo/gr_usuarios_model','gr_usuarios',true);
//                $usuarioGr=$this->gr_usuarios->verificarUsuarioGr($this->user['id'],$areasInferiores);
//                $usuarioGr=true;
                $usuarioGr=$this->_dameAreas($this->user['id']);
                
                if($usuarioGr['gr'])
                {
                    $this->load->model('gestion_riesgo/gr_bc_model','gr_bc',true);
//                    $datos['id_tipo_herramienta']   =$this->input->post("herramienta");
                    $datos['id_usuario_gr']     =$id_usuario_gr;
                    $datos['id_estado']         =2;
                    
                    
                    //calculo profundidad
                    $profundidad=$this->dameProfundidad($id_bc);
//                    echo "<pre>".print_r($profundidad,true)."</pre>";die();
                    $datos['alcance']         =($profundidad['alcance']!=0)?$profundidad['alcance']:'0';
//                    $datos['status']         ='0';
                    
                    //verifico que todos los campos esten completos
                    $control=true;
                    foreach ($datos as $dato)
                    {
                        if($dato=="")
                            $control=false;
                    }
                    if ($control)
                    {

                        $update=$this->gr_bc->update($id_bc,$datos);
                        if($update)
                        {
                            //Inicio la Bajada en cascada
                            $this->iniciar_bc($id_bc);
                            //Envio mail al usuario que debe iniciar la BC
                            $this->_enviarMailInicioBc($id_bc);
                            echo 1; //todo OK
                        }
                        else
                            echo 2; //Error al insertar el registro
                                
                    }
                    else
                        echo 3;//Faltan campos requeridos
                    
                }
                else
                    echo 4;//No es usuario de GR o el RMC ya tiene asignado una criticidad
                
                
            }
            else
                    echo -1; //No tiene permisos
	}
        public function rechazar()
	{
            if ($this->permisos['Baja'])		
            {
                $datos=array();
                $id_usuario_gr = $this->user['id'];
                $id_bc =$this->input->post("id");
                $motivo = ($this->input->post("texto")!=Null)?$this->input->post("texto"):"";
                
                //busco las areas que integran el área de gestión de riesgo
                $this->load->model('gestion_riesgo/gr_areas_model','gr_areas',true);
                $areasInferiores[]=4;

                for($i=0;$i<10;$i++)
                {
                    $subareasGr=$this->gr_areas->dameAreasInferiores($areasInferiores);                
                    if (count($subareasGr)>1)
                    {
                        foreach ($subareasGr as $area)
                        {
                            $arrayAreas[]=$area['id_area'];
                        }
                        $areasInferiores= array_unique(array_merge($areasInferiores,$arrayAreas));
                    }

                }
                
                //verifico que el usaurio que esta editando corresponsa con un usuario de GR o JoseDeCastro y que la BC este en estado pendiente
                $this->load->model("gestion_riesgo/gr_bc_model","gr_bc",true);
                $bc=$this->gr_bc->dameBc($id_bc);
                
//                $this->load->model('gestion_riesgo/gr_usuarios_model','gr_usuarios',true);
//                $usuarioGr=$this->gr_usuarios->verificarUsuarioGr($this->user['id'],$areasInferiores);
//                $usuarioGr=true;
                $usuarioGr=$this->_dameAreas($this->user['id']);
                
                if($usuarioGr['gr'] && $bc['id_estado']==1)
                {
                    $this->load->model('gestion_riesgo/gr_bc_model','gr_bc',true);
//                    $datos['id_tipo_herramienta']   =$this->input->post("herramienta");
                    $datos['id_usuario_gr']     =$id_usuario_gr;
                    $datos['id_estado']         =3;
                    $datos['detalle_rechazo_cancel']   =$motivo;
                    
                    
                    //verifico que todos los campos esten completos
                    $control=true;
                    foreach ($datos as $dato)
                    {
                        if($dato=="")
                            $control=false;
                    }
                    if ($control)
                    {

                        $update=$this->gr_bc->update($id_bc,$datos);
                        if($update)
                        {
                            //Envio mail al usuario que dió de alta avisando que se rechazo
                            $this->_enviarMailRechazarBc($id_bc);
                            echo 1; //todo OK
                        }
                        else
                            echo 2; //Error al insertar el registro
                                
                    }
                    else
                        echo 3;//Faltan campos requeridos
                    
                }
                else
                    echo 4;//No es usuario de GR o el RMC ya cambio de estado
                
                
            }
            else
                    echo -1; //No tiene permisos
	}
        public function cancelar()
	{
            if ($this->permisos['Baja'])		
            {
                $datos=array();
                $id_usuario_gr = $this->user['id'];
                $id_bc =$this->input->post("id");
                $motivo = ($this->input->post("texto")!=Null)?$this->input->post("texto"):"";
                
                //busco las areas que integran el área de gestión de riesgo
                $this->load->model('gestion_riesgo/gr_areas_model','gr_areas',true);
                $areasInferiores[]=4;

                for($i=0;$i<10;$i++)
                {
                    $subareasGr=$this->gr_areas->dameAreasInferiores($areasInferiores);                
                    if (count($subareasGr)>1)
                    {
                        foreach ($subareasGr as $area)
                        {
                            $arrayAreas[]=$area['id_area'];
                        }
                        $areasInferiores= array_unique(array_merge($areasInferiores,$arrayAreas));
                    }

                }
                
                //verifico que el usaurio que esta editando corresponsa con un usuario de GR o JoseDeCastro y que la BC este en estado en curso(2) 
                $this->load->model("gestion_riesgo/gr_bc_model","gr_bc",true);
                $bc=$this->gr_bc->dameBc($id_bc);
                
//                $this->load->model('gestion_riesgo/gr_usuarios_model','gr_usuarios',true);
//                $usuarioGr=$this->gr_usuarios->verificarUsuarioGr($this->user['id'],$areasInferiores);
//                $usuarioGr=true;
                $usuarioGr=$this->_dameAreas($this->user['id']);
                
                if($usuarioGr['gr'] && $bc['id_estado']==2)
                {
                    $this->load->model('gestion_riesgo/gr_bc_model','gr_bc',true);
//                    $datos['id_tipo_herramienta']   =$this->input->post("herramienta");
                    $datos['id_usuario_cancel']     =$id_usuario_gr;
                    $datos['id_estado']         =5;
                    $datos['detalle_rechazo_cancel']   =$motivo;
                    
                    
                    //verifico que todos los campos esten completos
                    $control=true;
                    foreach ($datos as $dato)
                    {
                        if($dato=="")
                            $control=false;
                    }
                    if ($control)
                    {

                        $update=$this->gr_bc->cancelar($id_bc,$datos);
                        if($update)
                        {
                            //Busco todas las tareas pendeintes de esta BC (abiertas y vencidas)
                            $this->load->model("gestion_riesgo/mejoracontinua_model","tareas_model",true);
                            //Actualizo todas las tareas que esten pendientes (Abiertas y vencidas) al estado 7=Cancelada
                            $tareas=$this->tareas_model->dameTareasParaCancelarBC($id_bc);
//                            echo "<pre>".print_r($tareas,true)."</pre>";
                            
                            if($tareas!=0)
                            {
                                foreach ($tareas as $key=>$value)
                                {
//                                    echo $value['id_tarea']."<br>";
                                    //guardo en el historial el estado actual de la tarea
                                    $this->tareas_model->copy($value['id_tarea']);

                                    //cambio estado a Cancelada(7) 
                                    $this->tareas_model->updateGestion($value['id_tarea'],7,'BC cancelada por GR');
                                }
                            }
                            
//                            $this->_enviarMailRechazarBc($id_bc);
                            echo 1; //todo OK
                        }
                        else
                            echo 2; //Error al insertar el registro
                                
                    }
                    else
                        echo 3;//Faltan campos requeridos
                    
                }
                else
                    echo 4;//No es usuario de GR o el RMC ya cambio de estado
                
                
            }
            else
                    echo -1; //No tiene permisos
	}
        
         public function combo_usuarios()
	{
            $limit=$this->input->post("limit");
            $start=$this->input->post("start");
            $query=$this->input->post("query");
            $id_usuario_not=$this->user['id'];
            $this->load->model("gestion_riesgo/gr_usuarios_model","gr_usuarios",true);
            $jcode = $this->gr_usuarios->dameUsuariosInicioBcCombo($limit,$start,$query,$id_usuario_not);
            echo $jcode;
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
            
            if(in_array(4, $areasSuperiores) || in_array(4, $areasInferiores))
                $areas['gr']=1;
            else
                $areas['gr']=0;
                    
//            echo "<pre>".print_r($areas,true)."</pre>";
            
            return $areas;
            
        }
        //Propaga el inicio de la Bajada en cascada, las siguientes propagaciones de tareas se hace desde el módulo de tareas
         public function iniciar_bc($id_bc)
	{
            $this->load->model("gestion_riesgo/gr_bc_model","gr_bc",true);
            $bc=$this->gr_bc->dameBc($id_bc);
//            echo "<pre>bc".print_r($bc,true)."</pre>";
            
            //verifico que el estado de la BC sea en curso:
            if ($bc['id_estado']==2)
            {
                
                $this->load->model("gestion_riesgo/gr_usuarios_model","gr_usuarios",true);
                $usuario=$this->gr_usuarios->dameUsuarioPorId($bc['id_usuario_inicio']);
                $directReports=$this->gr_usuarios->dameDirectReports($usuario['id_puesto']);

//                echo "<pre>usuario".print_r($usuario,true)."</pre>";
//                echo "<pre>directReports".print_r($directReports,true)."</pre>";
//
//                echo "q->".count($directReports);

//                 if(count($directReports)==$usuario['q_dr'] && count($directReports)>0 && $usuario['q_dr']>0)
//                 if(count($directReports)>0)
                if(!empty ($directReports))
                {
                    $this->load->model("gestion_riesgo/gr_tareas_model","gr_tareas",true);
                    foreach ($directReports as $destinatario)
                    {
                        //Datos para alta de Tarea
                        $datos=array();
                        $datos['usuario_alta']          =$bc['id_usuario_alta'];
                        $datos['usuario_relacionado']            =$destinatario['id_usuario'];
                        $datos['id_tipo_herramienta']   =3;// Bajada en cascada
                        $datos['id_herramienta']        =$id_bc;// Bajada en cascada
                        $datos['usuario_responsable']   =$bc['id_usuario_inicio'];
                        $datos['hallazgo']              =$bc['descripcion'];
                        $datos['tarea']                 ='Bajar en cascada lo descripto a '.$destinatario['nomape']." (".$destinatario['puesto'].").";
                        $datos['fecha_vto']             =$bc['fecha_vto_1'];
                        $datos['id_estado']             =1;
//                        echo "<pre>".print_r($datos,true)."</pre>";
                        $directReports=$this->gr_tareas->insert($datos);

                    }
                    return 1; 

                }
                else
                    return 0; //El usuario no tiene Direct Reports
            }
            else
                return 0;
            
	}
        public function arbolBc($id_bc)
        {
            $this->load->model('gestion_riesgo/gr_bc_model','gr_bc',true);
            $bc=$this->gr_bc->dameBc($id_bc);
//            echo "<pre>".print_r($bc,true)."</pre>";
            
            $this->load->model('gestion_riesgo/gr_usuarios_model','gr_usuarios',true);
            $directReports=$this->gr_usuarios->dameArbol($bc['id_usuario_inicio']);
            $arbolBc=$directReports;
            
            $subDr=array();
            $profundidad=0;
            if($directReports!=0)
            {
                foreach ($directReports as $key1=>$dr1)
                {
                    $profundidad=0;
                    $subDr1=$this->gr_usuarios->dameArbol($dr1['id_usuario']);
                    $arbolBc[$key1]['prof']=$profundidad;
                    if($subDr1!=0)
                    {
                        $arbolBc[$key1]['dr']=$subDr1;
                        foreach ($subDr1 as $key2=>$dr2)
                        {   
                            $profundidad=1;
                            $subDr2=$this->gr_usuarios->dameArbol($dr2['id_usuario']);
                            $arbolBc[$key1]['dr'][$key2]['prof']=$profundidad;
                            $arbolBc[$key1]['dr'][$key2]['dr']=$subDr2;
                            if($subDr2!=0)
                            {
                                foreach ($subDr2 as $key3=>$dr3)
                                {   
                                    $profundidad=2;
                                    $arbolBc[$key1]['dr'][$key2]['dr'][$key3]['prof']=$profundidad;
                                    $subDr3=$this->gr_usuarios->dameArbol($dr3['id_usuario']);
                                    $arbolBc[$key1]['dr'][$key2]['dr'][$key3]['dr']=$subDr3;
                                    if($subDr3['q_dr']>0)
                                        echo $subDr3[0]['id_usuario'];
                                }
                            }
                        }
                    }
                    
                }
                
            }
            echo "<pre>".print_r($arbolBc,true)."</pre>";
            
            
            
        }
        
        function probandonoMailing()
        {
            $this->_enviarMailAltaBc(5);
        }
        
        function _enviarMailAltaBc($id_bc)
        {
            $this->load->model('gestion_riesgo/gr_usuarios_model','gr_usuarios',true);
            $this->load->model('gestion_riesgo/gr_bc_model','gr_bc',true);
            $this->load->model('gestion_riesgo/gr_areas_model','gr_areas',true);
            
            $bc=$this->gr_bc->dameBc($id_bc);
//            echo "<pre>".print_r($bc,true)."</pre>";
            $usuarioAlta=$this->gr_usuarios->dameUsuarioPorId($bc['id_usuario_alta']);
//            echo "<pre>Usuario Alta".print_r($usuarioAlta,true)."</pre>";
            $usuarioInicio=$this->gr_usuarios->dameUsuarioPorId($bc['id_usuario_inicio']);
//            echo "<pre>Usuario Inicio".print_r($usuarioInicio,true)."</pre>";
            
            $directReports=$this->gr_usuarios->dameDirectReports($usuarioInicio['id_puesto']);
//            echo "<pre>DiectReports:".print_r($directReports,true)."</pre>";
            
            
            $listDDRR="<br>";
            $tab=  str_repeat("&nbsp;", 20);
            $div1='<span style="color:#122A0A; font-size:13px;">';
            $div2='<span style="color:#585858; font-size:11px;">';
            
             if(!empty($directReports))
            {
                foreach ($directReports as $directReport)
                {
                    $listDDRR.=$div1.$tab."&rarr;"."<b>".$directReport['nomape'].'</b></span>'.$div2.' ('.$directReport['puesto'].")</span><br>";
                }
            }
            else
                $listDDRR="<br>".$tab."--Ninguno--"; //Por ahora lo dejo en blanco
//            echo "<pre>ListDR:".print_r($listDDRR,true)."</pre>";
            
            //busco todas las areas dependientes de GR
            $areasInferiores[]=4; //Gerencia Corportavida Gestión de Riesgo
            for($i=0;$i<1;$i++)
            {
                $subareasGr=$this->gr_areas->dameAreasInferiores($areasInferiores);                
                if (count($subareasGr)>1)
                {
                    foreach ($subareasGr as $area)
                    {
                        $arrayAreas[]=$area['id_area'];
                    }
                    $areasInferiores= array_unique(array_merge($areasInferiores,$arrayAreas));
                }
            }
            $usuariosGr=$this->gr_usuarios->dameUsuariosGr($areasInferiores);
//            echo "<pre>".print_r($usuariosGr,true)."</pre>";
            $arrayEmailsGr=array();
            foreach ($usuariosGr as $email)
            {
                if ($email['mailing']==1)
                    $arrayEmailsGr[]=$email['email'];
            }
            
            $usuarioGGR=''; //Gerente Gestión de Riesgo
            $usuarioRtte=$bc['mailUsuarioAlta'];
            
            unset($arrayEmailsGr[array_search($usuarioGGR,$arrayEmailsGr)]);
            
            
            $datos=array();
            $datos['id_bc']         =$bc['id_bc'];
            $datos['bc_nro']        =3000000+$bc['id_bc'];
            $datos['asunto']        ='Nueva BC Nro: '.$datos['bc_nro'];
            $datos['descipcion']    =$bc['descripcion'];
            $datos['listaDDRR']      =$listDDRR;
            $datos['usuarioAlta']   =$usuarioAlta['persona']." (".$usuarioAlta['puesto'].")";
            $datos['usuarioInicio']   =$usuarioInicio['persona']." (".$usuarioInicio['puesto'].")";
            $datos['usuarioPara']   =$arrayEmailsGr;
            $datos['usuarioRtte']   =$usuarioAlta['email'];
            if (MAILS_ALWAYS_CC!="")
                $datos['usuariosCC']    = array(MAILS_ALWAYS_CC,$usuarioGGR);
            else
                $datos['usuariosCC']    = array($usuarioGGR);
            if (!in_array($usuarioRtte,array_merge($datos['usuarioPara'],$datos['usuariosCC'])))
                array_push($datos['usuariosCC'],$datos['usuarioRtte']);
//            echo "<pre>Datos:".print_r($datos,true)."</pre>";
            
            $link='www.polidata.com.ar/orocobre/admin';
                        
$cuerpo_mail=<<<HTML
<html> 
    <head>
        <title>SMC</title>
        
    </head>
    <body>
    <div style="color:#000000; font-family:Verdana, Arial, Helvetica, sans-serif; text-align:Left; font-size:13px;">
        <pre>
            Srs GR,

            Se ha creado una nueva Bajada en Cascada y se encuentra disponible para su aprobaci&oacute;n:
            
            <b>BC Nro:</b>#bcNro
            <b>Usuario Alta:</b>#usuarioAlta
            <b>Usuario donde se iniciar&aacute;:</b>#usuarioInicio            
            <b>Descripci&oacute;n:</b>
            <em>#descipcionBc</em>
            
            Direct Reports para la 1ra propagaci&oacute;n:#listaDDRR
            
            Puede continuar su gesti&oacute;n ingresando al sistema desde <a href="#link" target="_blank">aqu&iacute;</a>, o copiando y pegando la siguiente direcci&oacute;n en su navegador web <b>Mozilla Firefox</b> o <b>Google Chrome</b>: 
            #link

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

             $subject=$datos['asunto'];
             $cuerpo_mail=str_replace("#descipcionBc"       ,$datos['descipcion'], $cuerpo_mail);
             $cuerpo_mail=str_replace("#listaDDRR"          ,$datos['listaDDRR'], $cuerpo_mail);
             $cuerpo_mail=str_replace("#bcNro"              ,$datos['bc_nro'], $cuerpo_mail);
             $cuerpo_mail=str_replace("#usuarioAlta"        , $datos['usuarioAlta']  , $cuerpo_mail);
             $cuerpo_mail=str_replace("#usuarioInicio"      , $datos['usuarioInicio']  , $cuerpo_mail);
             $cuerpo_mail=str_replace("#txt_usuarioPara"    , implode(", ", $datos['usuarioPara'])  , $cuerpo_mail);
             $cuerpo_mail=str_replace("#txt_usuariosCC"     , implode(", ",$datos['usuariosCC'])   , $cuerpo_mail);
             $cuerpo_mail=str_replace("#usuarioRtte"        , $datos['usuarioRtte']  , $cuerpo_mail);
             $cuerpo_mail=str_replace("#link"               , $link                  ,$cuerpo_mail);
             $cuerpo_mail=str_replace("#firma"              , htmlentities(NOMSIS,ENT_QUOTES,"UTF-8"),$cuerpo_mail);
             
             $this->load->library('email');
             $guardarMail['id_origen']=$id_bc;
             $guardarMail['origen']='BC/enviarMailAltaBc';
//             $this->email->clear();//para cuando uso un bucle
            $this->email->from(SYS_MAIL, NOMSIS);
            $this->email->message($cuerpo_mail);
            $guardarMail['texto']=$cuerpo_mail;
//            $this->email->set_alt_message();//Sets the alternative email message body
//            $this->email->reply_to('gestiondedocumentos@salesdejujuy.com','DNS');
            $this->email->to($datos['usuarioPara']);
            $guardarMail['para']=$datos['usuarioPara'];
            $this->email->cc($datos['usuariosCC']);
            $guardarMail['cc']=$datos['usuariosCC'];
            $this->email->subject($subject);
            $guardarMail['asunto']=$subject;
            IF (MAILBCC && MAILBCC !="")
                $this->email->bcc(MAILBCC);
            $guardarMail['cco']=MAILBCC;
                
            $this->load->model('mailing_model','mailing',true);
            $guardar=$this->mailing->insert($guardarMail);
            
//            echo $cuerpo_mail;
//            $this->email->send();
            if (ENPRODUCCION)
            {
                if($this->email->send())
                    return 0;
                else
                    return 2;
            }
            else
            {
                    return -1;
            }
//            echo $this->email->print_debugger();

        }
        function _enviarMailRechazarBc($id_bc)
        {
            $this->load->model('gestion_riesgo/gr_usuarios_model','gr_usuarios',true);
            $this->load->model('gestion_riesgo/gr_bc_model','gr_bc',true);
            $this->load->model('gestion_riesgo/gr_areas_model','gr_areas',true);
            
            $bc=$this->gr_bc->dameBc($id_bc);
//            echo "<pre>".print_r($bc,true)."</pre>";
            
            //solo continuo si efectivamente el estado de la BC es rechazada
            if($bc['id_estado']==3)
            {
                
                $usuarioAlta=$this->gr_usuarios->dameUsuarioPorId($bc['id_usuario_alta']);
    //            echo "<pre>Usuario Alta".print_r($usuarioAlta,true)."</pre>";
                $usuarioInicio=$this->gr_usuarios->dameUsuarioPorId($bc['id_usuario_inicio']);
    //            echo "<pre>Usuario Inicio".print_r($usuarioInicio,true)."</pre>";

                $usuarioSetGr=$this->gr_usuarios->dameUsuarioPorId($bc['id_usuario_gr']);
    //            echo "<pre>Usuario Inicio".print_r($usuarioInicio,true)."</pre>";

                $directReports=$this->gr_usuarios->dameDirectReports($usuarioInicio['id_puesto']);
    //            echo "<pre>DiectReports:".print_r($directReports,true)."</pre>";


                $listDDRR="<br>";
                $tab=  str_repeat("&nbsp;", 20);
                $div1='<span style="color:#122A0A; font-size:13px;">';
                $div2='<span style="color:#585858; font-size:11px;">';

                if(!empty($directReports))
                {
                    foreach ($directReports as $directReport)
                    {
                        $listDDRR.=$div1.$tab."&rarr;"."<b>".$directReport['nomape'].'</b></span>'.$div2.' ('.$directReport['puesto'].")</span><br>";
                    }
                }
                else
                    $listDDRR="<br>".$tab."--Ninguno--"; //Por ahora lo dejo en blanco
    //            echo "<pre>ListDR:".print_r($listDDRR,true)."</pre>";

                //busco todas las areas dependientes de GR
                $areasInferiores[]=4; //Gerencia Corportavida Gestión de Riesgo
                for($i=0;$i<1;$i++)
                {
                    $subareasGr=$this->gr_areas->dameAreasInferiores($areasInferiores);                
                    if (count($subareasGr)>1)
                    {
                        foreach ($subareasGr as $area)
                        {
                            $arrayAreas[]=$area['id_area'];
                        }
                        $areasInferiores= array_unique(array_merge($areasInferiores,$arrayAreas));
                    }
                }

                $usuarioGGR=''; //Gerente Gestión de Riesgo


                $datos=array();
                $datos['id_bc']                 =$bc['id_bc'];
                $datos['bc_nro']                =3000000+$bc['id_bc'];
                $datos['usuario']               =$usuarioAlta['persona'];
                $datos['usuarioSetGr']          =$usuarioSetGr['persona'];
                $datos['asunto']                ='BC Nro: '.$datos['bc_nro'].' - Rechazada';
                $datos['descipcion']            =$bc['descripcion'];
                $datos['motivo']                =$bc['motivo'];
                $datos['listaDDRR']             =$listDDRR;
                $datos['usuarioAlta']           =$usuarioAlta['persona']." (".$usuarioAlta['puesto'].")";
                $datos['generousuarioAlta']     =$usuarioAlta['genero'];
                $datos['usuarioInicio']         =$usuarioInicio['persona']." (".$usuarioInicio['puesto'].")";
                $datos['usuarioPara']           =$usuarioAlta['email'] ;
                $datos['usuarioRtte']           =$usuarioSetGr['email'];
                
                if (MAILS_ALWAYS_CC!="")
                    $datos['usuariosCC']            = array_unique(array(MAILS_ALWAYS_CC,$usuarioGGR));
                else
                    $datos['usuariosCC']            = array_unique(array($usuarioGGR));
                
                if($datos['usuarioRtte']!=$datos['usuarioPara'] )
                    array_push ($datos['usuariosCC'],$datos['usuarioRtte']);


                $link='www.polidata.com.ar/orocobre/admin';

$cuerpo_mail=<<<HTML
<html> 
<head>
<title>SMC</title>
</head>
<body>
<div style="color:#000000; font-family:Verdana, Arial, Helvetica, sans-serif; text-align:Left; font-size:13px;">
<pre>
#persona,

Le informamos que la Bajada en Cascada de referencia que usted genero como:

<b>BC Nro:</b>#bcNro
<b>Usuario donde se iniciar&aacute;:</b>#usuarioInicio            
<b>Descripci&oacute;n:</b>
<em>#descipcionBc</em>

Direct Reports para la 1ra propagaci&oacute;n:#listaDDRR

Fue rechazada por <b>#usuarioSetGr</b> por el siguiente motivo:<div style="color:#FF0000; font-family:Verdana, Arial, Helvetica, sans-serif; text-align:Left; font-size:13px;">#motivo</div>

Puede continuar su gesti&oacute;n ingresando al sistema desde <a href="#link" target="_blank">aqu&iacute;</a>, o copiando y pegando la siguiente direcci&oacute;n en su navegador web <b>Mozilla Firefox</b> o <b>Google Chrome</b>: 
#link

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
                if ($datos['generousuarioAlta']=='F')
                {
                    $cuerpo_mail=str_replace("#persona"       , 'Estimada '.$datos['usuario'], $cuerpo_mail);
                    $cuerpo_mail=str_replace("#propuesto/a"       , 'propuesta' , $cuerpo_mail);

                }
                else
                {
                    $cuerpo_mail=str_replace("#persona"       , 'Estimado '.$datos['usuario']      , $cuerpo_mail);
                    $cuerpo_mail=str_replace("#propuesto/a"       , 'propuesto' , $cuerpo_mail);

                }

                $subject=$datos['asunto'];
                $cuerpo_mail=str_replace("#descipcionBc"       ,$datos['descipcion'], $cuerpo_mail);
                $cuerpo_mail=str_replace("#usuarioSetGr"       ,($datos['usuarioSetGr']!=$datos['usuario'])?$datos['usuarioSetGr']:'usted mismo', $cuerpo_mail);
                $cuerpo_mail=str_replace("#motivo"             ,$datos['motivo'], $cuerpo_mail);
                $cuerpo_mail=str_replace("#listaDDRR"          ,$datos['listaDDRR'], $cuerpo_mail);
                $cuerpo_mail=str_replace("#bcNro"              ,$datos['bc_nro'], $cuerpo_mail);
                $cuerpo_mail=str_replace("#usuarioAlta"        , $datos['usuarioAlta']  , $cuerpo_mail);
                $cuerpo_mail=str_replace("#usuarioInicio"      , $datos['usuarioInicio']  , $cuerpo_mail);
                $cuerpo_mail=str_replace("#txt_usuarioPara"    , $datos['usuarioPara']  , $cuerpo_mail);
                $cuerpo_mail=str_replace("#txt_usuariosCC"     , implode(", ",$datos['usuariosCC'])   , $cuerpo_mail);
                $cuerpo_mail=str_replace("#usuarioRtte"        , $datos['usuarioRtte']  , $cuerpo_mail);
                $cuerpo_mail=str_replace("#link"               , $link                  ,$cuerpo_mail);
                $cuerpo_mail=str_replace("#firma"              , htmlentities(NOMSIS,ENT_QUOTES,"UTF-8"),$cuerpo_mail);

                $this->load->library('email');
                $guardarMail['id_origen']=$id_bc;
                $guardarMail['origen']='BC/enviarMailRechazaBc';
    //             $this->email->clear();//para cuando uso un bucle
                $this->email->from(SYS_MAIL, NOMSIS);
                $this->email->message($cuerpo_mail);
                $guardarMail['texto']=$cuerpo_mail;
    //            $this->email->set_alt_message();//Sets the alternative email message body
    //            $this->email->reply_to('gestiondedocumentos@salesdejujuy.com','DNS');
                $this->email->to($datos['usuarioPara']);
                $guardarMail['para']=$datos['usuarioPara'];
                $this->email->cc($datos['usuariosCC']);
                $guardarMail['cc']=$datos['usuariosCC'];
                $this->email->subject($subject);
                $guardarMail['asunto']=$subject;
                IF (MAILBCC && MAILBCC !="")
                    $this->email->bcc(MAILBCC);
                $guardarMail['cco']='fmoraiz@gmail.com';

                $this->load->model('mailing_model','mailing',true);
                $guardar=$this->mailing->insert($guardarMail);

//                echo $cuerpo_mail;
    //            $this->email->send();
                if (ENPRODUCCION)
                {
                    if($this->email->send())
                        return 0;
                    else
                        return 2;
                }
                else
                {
                        return -1;
                }
    //            echo $this->email->print_debugger();
            }
            else
                return -1;

        }
        function _enviarMailInicioBc($id_bc)
        {
             $this->load->model('gestion_riesgo/gr_usuarios_model','gr_usuarios',true);
            $this->load->model('gestion_riesgo/gr_bc_model','gr_bc',true);
            $this->load->model('gestion_riesgo/gr_areas_model','gr_areas',true);
            $this->load->model('gestion_riesgo/gr_tareas_model','gr_tareas',true);
            
            $bc=$this->gr_bc->dameBc($id_bc);
//            echo "<pre>".print_r($bc,true)."</pre>";
            $usuarioAlta=$this->gr_usuarios->dameUsuarioPorId($bc['id_usuario_alta']);
//            echo "<pre>Usuario Alta".print_r($usuarioAlta,true)."</pre>";
            $usuarioInicio=$this->gr_usuarios->dameUsuarioPorId($bc['id_usuario_inicio']);
//            echo "<pre>Usuario Inicio".print_r($usuarioInicio,true)."</pre>";
            $usuarioAprobador=$this->gr_usuarios->dameUsuarioPorId($bc['id_usuario_gr']);
//            echo "<pre>Usuario Aprobador".print_r($usuarioInicio,true)."</pre>";
            
            $directReports=$this->gr_usuarios->dameDirectReports($usuarioInicio['id_puesto']);
            
            if(!empty($directReports))
            {
                if ($usuarioInicio['q_dr']==1)
                    $listDDRR="La siguiente tarea fue generadas para que usted confirme (cierre) o rechace (no aplica) seg&uacute;n su Direct Report:<br>";
                else    
                    $listDDRR="Las siguientes tareas fueron generadas para que usted confirme (cierre) o rechace (no aplica) seg&uacute;n cada uno de sus Direct Reports:<br>";
                $tab=  str_repeat("&nbsp;", 20);
//                $tab=  str_repeat(" ", 20);
                $div1='<span style="color:#122A0A; font-size:13px;">';
                $div2='<span style="color:#585858; font-size:11px;">';
                $div3='<span style="color:#045FB4; font-size:13px; text-align:right; width:100px">';


                foreach ($directReports as &$directReport)
                {
                    $tareaNro=$this->gr_tareas->dameTareaPorBcYDR($id_bc,$directReport['id_usuario']);
                    $directReport['id_tarea']=$tareaNro;
                    $line="";
                    $line.=$div3.$tab."&rarr;Tarea Nro: ".$tareaNro."-&gt; </span>";
                    $line.=$div1."<b>".$directReport['nomape'].'</b></span>';
                    $line.=$div2.' ('.$directReport['puesto'].")</span>";
                    $listDDRR.=$line."<br>";
                }
                unset ($directReport);
            }
            else
                    $listDDRR="";
            
//            echo "<pre>DiectReports:".print_r($directReports,true)."</pre>";
//            echo "<pre>ListDR:".print_r($listDDRR,true)."</pre>";
            
            //busco todas las areas dependientes de GR
            $areasInferiores[]=4; //Gerencia Corportavida Gestión de Riesgo
            for($i=0;$i<1;$i++)
            {
                $subareasGr=$this->gr_areas->dameAreasInferiores($areasInferiores);                
                if (count($subareasGr)>1)
                {
                    foreach ($subareasGr as $area)
                    {
                        $arrayAreas[]=$area['id_area'];
                    }
                    $areasInferiores= array_unique(array_merge($areasInferiores,$arrayAreas));
                }
            }
            $usuariosGr=$this->gr_usuarios->dameUsuariosGr($areasInferiores);
//            echo "<pre>".print_r($usuariosGr,true)."</pre>";
            $arrayEmailsGr=array();
            foreach ($usuariosGr as $email)
            {
                $arrayEmailsGr[]=$email['email'];
            }
//            echo "<pre>".print_r($arrayEmailsGr,true)."</pre>";
            //cambiar por funcion que traiga integrantes del área de GR de cada empresa solo al nivel de estos usuarios
//            switch ($rmc['id_empresa'])
//            {
//                case 1:
//                   $arrayEmailsGr=array('pedro.musso@boraxargentina.com','natalia.maizares@boraxargentina.com','iceballos@salesdejujuy.com','jbarroso@salesdejujuy.com','dcachullani@salesdejujuy.com','mramirez@salesdejujuy.com');
//                   break;
//               case 2:
//                   $arrayEmailsGr=array('iceballos@salesdejujuy.com','jbarroso@salesdejujuy.com','dcachullani@salesdejujuy.com','mramirez@salesdejujuy.com');
//                   break;
//               case 3:
//                   $arrayEmailsGr=array('pedro.musso@boraxargentina.com','natalia.maizares@boraxargentina.com');
//                   break;
//               default :
//                   $arrayEmailsGr=array('pedro.musso@boraxargentina.com','natalia.maizares@boraxargentina.com','iceballos@salesdejujuy.com','jbarroso@salesdejujuy.com','dcachullani@salesdejujuy.com','mramirez@salesdejujuy.com');
//            }
            
            $usuarioGGR=''; //Gerente Gestión de Riesgo
            $usuarioRtte=$usuarioAlta['email'];
            
            unset($arrayEmailsGr[array_search($usuarioGGR,$arrayEmailsGr)]);
            
            
            $datos=array();
            $guardarMail=array();
            $datos['id_bc']         =$bc['id_bc'];
            $datos['bc_nro']        =3000000+$bc['id_bc'];
            $datos['asunto']        ='Inicio BC Nro: '.$datos['bc_nro'];
            $datos['usuario']       =$usuarioInicio['persona'];
            $datos['descipcion']    =$bc['descripcion'];
            $datos['listaDDRR']      =$listDDRR;
            $datos['usuarioAlta']   =$usuarioAlta['persona']." (".$usuarioAlta['puesto'].")";
            $datos['usuarioInicio']   =$usuarioInicio['persona']." (".$usuarioInicio['puesto'].")";
            $datos['usuarioAprobador']   =$usuarioAprobador['persona']." (".$usuarioAprobador['puesto'].")";
            $datos['generoUsuarioInicio']   =$usuarioInicio['genero'];
            $datos['usuarioPara']   =$usuarioInicio['email'];
            $datos['usuarioRtte']   =$usuarioAlta['email'];
            $datos['usuariosCC']    = $arrayEmailsGr;
            if(in_array($datos['usuarioPara'], $datos['usuariosCC']))
                unset ($datos['usuariosCC'][$datos['usuarioPara']]);
            
            if (!in_array($usuarioRtte,$datos['usuariosCC']) && $datos['usuarioRtte']!=$datos['usuarioPara'])
                array_push($datos['usuariosCC'],$datos['usuarioRtte']);
//            echo "<pre>Datos:".print_r($datos,true)."</pre>";
            
            $link='www.polidata.com.ar/orocobre/admin';
                        
$cuerpo_mail=<<<HTML
<html> 
    <head>
        <title>SMC</title>
        
    </head>
    <body>
    <div style="color:#000000; font-family:Verdana, Arial, Helvetica, sans-serif; text-align:Left; font-size:13px;">
        <pre>
            #persona,

            Usted fue #propuesto/a para iniciar un proceso de Bajada en Cascada
            
            <b>BC Nro:</b>#bcNro
            <b>Usuario Alta:</b>#usuarioAlta
            <b>Aprobada por:</b>#usuarioGrAprobador            
            <b>Descripci&oacute;n:</b>
            <em>#descipcionBc</em>
            
            #listaDDRR
            
            Puede continuar su gesti&oacute;n ingresando al sistema desde <a href="#link" target="_blank">aqu&iacute;</a>, o copiando y pegando la siguiente direcci&oacute;n en su navegador web <b>Mozilla Firefox</b> o <b>Google Chrome</b>: 
            #link

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
            if ($datos['generoUsuarioInicio']=='F')
             {
                $cuerpo_mail=str_replace("#persona"       , 'Estimada '.$datos['usuario'], $cuerpo_mail);
                $cuerpo_mail=str_replace("#propuesto/a"       , 'propuesta' , $cuerpo_mail);
                 
             }
             else
             {
                $cuerpo_mail=str_replace("#persona"       , 'Estimado '.$datos['usuario']      , $cuerpo_mail);
                $cuerpo_mail=str_replace("#propuesto/a"       , 'propuesto' , $cuerpo_mail);
                 
             }

             $subject=$datos['asunto'];
             $cuerpo_mail=str_replace("#descipcionBc"       ,$datos['descipcion'], $cuerpo_mail);
             $cuerpo_mail=str_replace("#listaDDRR"          ,$datos['listaDDRR'], $cuerpo_mail);
             $cuerpo_mail=str_replace("#bcNro"              ,$datos['bc_nro'], $cuerpo_mail);
             $cuerpo_mail=str_replace("#usuarioAlta"        , $datos['usuarioAlta']  , $cuerpo_mail);
             $cuerpo_mail=str_replace("#usuarioGrAprobador" , $datos['usuarioAprobador']  , $cuerpo_mail);
             $cuerpo_mail=str_replace("#usuarioInicio"      , $datos['usuarioAlta']  , $cuerpo_mail);
             $cuerpo_mail=str_replace("#txt_usuarioPara"    , $datos['usuarioPara']  , $cuerpo_mail);
             $cuerpo_mail=str_replace("#txt_usuariosCC"     , implode(", ",$datos['usuariosCC'])   , $cuerpo_mail);
             $cuerpo_mail=str_replace("#usuarioRtte"        , $datos['usuarioRtte']  , $cuerpo_mail);
             $cuerpo_mail=str_replace("#link"               , $link                  ,$cuerpo_mail);
             $cuerpo_mail=str_replace("#firma"              , htmlentities(NOMSIS,ENT_QUOTES,"UTF-8"),$cuerpo_mail);
             
             $this->load->library('email');
            $guardarMail['id_origen']=$id_bc;
            $guardarMail['origen']='BC/enviarMailInicioBc';
//             $this->email->clear();//para cuando uso un bucle
            $this->email->from(SYS_MAIL, NOMSIS);
            $this->email->message($cuerpo_mail);
            $guardarMail['texto']=$cuerpo_mail;
//            $this->email->set_alt_message();//Sets the alternative email message body
//            $this->email->reply_to('gestiondedocumentos@salesdejujuy.com','DNS');
            $this->email->to($datos['usuarioPara']);
            $guardarMail['para']=$datos['usuarioPara'];
            $this->email->cc($datos['usuariosCC']);
            $guardarMail['cc']=$datos['usuariosCC'];
            $this->email->subject($subject);
            $guardarMail['asunto']=$subject;
            IF (MAILBCC && MAILBCC !="")
                $this->email->bcc(MAILBCC);
            $guardarMail['cco']='fmoraiz@gmail.com';
//            echo $cuerpo_mail;
            
            $this->load->model('mailing_model','mailing',true);
            $guardar=$this->mailing->insert($guardarMail);
            
//            $this->email->send();
            if (ENPRODUCCION)
            {
                if($this->email->send())
                    return 0;
                else
                    return 2;
            }
            else
            {
                    return -1;
            }
//            echo $this->email->print_debugger();

        }
        function actualizarAvanceTodasBc()
        {
            $estados=array(1,2); //Pendiente y en curso
//            $idsbbcc=array();
            $this->load->model('gestion_riesgo/gr_bc_model','gr_bc',true);
            $bbcc=$this->gr_bc->dameBBCC($estados);
            
            foreach ($bbcc as $key=>$value)
            {
                $bc=$this->gr_bc->dameBc($value['id_bc']);
                $datos['alcance']=0;
                $datos['status']=0;
                $datos['id_estado']=$bc['id_estado'];
                //contadores se envía por referencia
                $this->_calcularProfundidad($value['id_bc'],$bc['id_usuario_inicio'],$datos);
                
                if ($datos['alcance']==$datos['status'] && ($datos['alcance']+$datos['status'])!=0)
                    $datos['id_estado']=4; //si se cumplio la BC cambio el estado a terminada
                
                $this->gr_bc->updateStatus($value['id_bc'],$datos);
                
            }
        }
        function actualizarAvanceBc($id_bc)
        {
            $datos['alcance']=0;
            $datos['status']=0;
            $this->load->model('gestion_riesgo/gr_bc_model','gr_bc',true);
            $bc=$this->gr_bc->dameBc($id_bc);
//            echo "<pre>BC:".print_r($bc,true)."</pre>";
            //contadores se envía por referencia
            $this->_calcularProfundidad($id_bc,$bc['id_usuario_inicio'],$datos);
            
            if ($datos['alcance']==$datos['status'] && ($datos['alcance']+$datos['status'])!=0)
                $datos['id_estado']=4; //si se cumplio la BC cambio el estado a terminada
            
//            echo "<pre>Contadores:".print_r($datos,true)."</pre>";
            $this->gr_bc->updateStatus($id_bc,$datos);
        }
        function dameProfundidad($id_bc)
        {
            $datos['alcance']=0;
            $datos['status']=0;
            $this->load->model('gestion_riesgo/gr_bc_model','gr_bc',true);
            $bc=$this->gr_bc->dameBc($id_bc);
//            echo "<pre>BC:".print_r($bc,true)."</pre>";
            //contadores se envía por referencia
            $this->_calcularProfundidad($id_bc,$bc['id_usuario_inicio'],$datos);
            
            return $datos;
            
        }
        function _calcularProfundidad($id_bc,$id_usuario_inicio,&$contadores)
        {
//            echo "<br>";
//            global $contador;
            $this->load->model('gestion_riesgo/gr_bc_model','gr_bc',true);
            $personas=$this->gr_bc->dameUsuariosInferiores($id_bc,$id_usuario_inicio);
//            echo $id_usuario_inicio."->";
//            echo "<pre>Personas:".print_r($personas,true)."</pre>";
//            foreach ($personas as $key=>$value)
//                    echo $value['id_usuario'].",";
            foreach ($personas as $key=>$value)
            {
                
                if ($value['id_estado']!=6)
                {
//                    echo "<pre>Sigue".print_r($value,true)."</pre>";
                    $contadores['alcance']++;
                    if ($value['id_estado']==2)
                        $contadores['status']++;
                    if ($value['q_dr']!=0)
                        $this->_calcularProfundidad($id_bc,$value['id_usuario'],$contadores);
                }
                
            }
//            return $contadores;
            
        }
}
?>