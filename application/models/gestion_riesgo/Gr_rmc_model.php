<?php
class Gr_rmc_model extends CI_Model
{
    public function listado($usuario, $start, $limit, $arrayFiltros = "", $busqueda, $campos,$sort="", $dir="")
    {
//            echo"<pre>".print_r($usuario,true)."</pre>";
            $this->db->select('r.id_rmc, r.id_usuario_alta,r.id_estado_inv, r.id_criticidad,r.id_investigador1,r.id_investigador2,r.observacion_sector');
            $this->db->select('r.descripcion as descr',false);
            $this->db->select("concat(p.nombre,' ',p.apellido) as usuario_alta",false);
            $this->db->select("concat(p2.nombre,' ',p2.apellido) as investigador1",false);
            $this->db->select("concat(p3.nombre,' ',p3.apellido) as investigador2",false);
            $this->db->select("DATE_FORMAT(r.fecha_alta,'%d/%m/%Y') as fecha_alta", FALSE);
            $this->db->select("DATE_FORMAT(r.fecha_set_crit,'%d/%m/%Y') as fecha_set_crit", FALSE);
            $this->db->select("DATE_FORMAT(r.fecha_vto_inv,'%d/%m/%Y') as fecha_vto_inv", FALSE);
            $this->db->select("concat(e.abv,' | ',s.sector) as sector",false);
            $this->db->select("(select count(t.id_tarea) from gr_tareas t where t.id_herramienta=r.id_rmc and t.id_tipo_herramienta=1) as tareas",false);
            $this->db->select("(select count(a.id_archivo) from gr_archivos_rmc a where a.id_rmc=r.id_rmc and a.eliminado=0) as archivos",false);
            $this->db->select(" CASE 
                                WHEN r.id_criticidad=1 THEN 'Crítico'
                                WHEN r.id_criticidad=2 THEN 'Alto'
                                WHEN r.id_criticidad=3 THEN 'Menor'
                                WHEN r.id_criticidad=4 THEN 'Fuera del Alcance de Gestion'
                                ELSE '?'
                            END as criticidad",FALSE);
            $this->db->select(" CASE 
                                WHEN r.id_estado_inv=1 THEN 'Abierta'
                                WHEN r.id_estado_inv=2 THEN 'Cerrada'
                                WHEN r.id_estado_inv=3 THEN 'Vencida'
                                ELSE '?'
                            END as estado",FALSE);
            $this->db->select("(select GROUP_CONCAT(DISTINCT clasificacion SEPARATOR '; ') from 
                                gr_rmc gr
                                inner join gr_rmc_clasificaciones grc on gr.id_rmc=grc.id_rmc
                                inner join gr_clasificaciones gc on grc.id_clasificacion=gc.id_clasificacion
                                where gr.id_rmc=r.id_rmc) as clasificacion",false);
            $this->db->select("(select GROUP_CONCAT(DISTINCT clasificacion SEPARATOR '<br> ') from 
                                gr_rmc gr
                                inner join gr_rmc_clasificaciones grc on gr.id_rmc=grc.id_rmc
                                inner join gr_clasificaciones gc on grc.id_clasificacion=gc.id_clasificacion
                                where gr.id_rmc=r.id_rmc) as clasificaciont",false);
            $this->db->from('gr_rmc r');
            $this->db->join('gr_usuarios gu','gu.id_usuario = r.id_usuario_alta','inner');
            $this->db->join('gr_usuarios gu2','gu2.id_usuario = r.id_investigador1','left');
            $this->db->join('gr_usuarios gu3','gu3.id_usuario = r.id_investigador2','left');
            $this->db->join('sys_usuarios u','u.id_usuario = gu.id_usuario','inner');
            $this->db->join('sys_usuarios u2','u2.id_usuario = gu2.id_usuario','left');
            $this->db->join('sys_usuarios u3','u3.id_usuario = gu3.id_usuario','left');
            $this->db->join('grl_personas p','p.id_persona = u.id_persona','left');
            $this->db->join('grl_personas p2','p2.id_persona = u2.id_persona','left');
            $this->db->join('grl_personas p3','p3.id_persona = u3.id_persona','left');
            $this->db->join('gr_puestos pto','pto.id_puesto = gu.id_puesto','inner');
            $this->db->join('gr_sectores_papelera s','s.id_sector = r.id_sector','inner');
            $this->db->join('grl_empresas e','e.id_empresa = s.id_empresa','inner');
            $this->db->where("r.habilitado",1);

            if ($busqueda!="" && count($campos)>0)
            {
                foreach ($campos as &$campo)
                {
                    switch ($campo)
                    {
                        case 'usuario_alta':
                            $campo="concat(p.nombre,' ',p.apellido)";
                            break;
                        case 'investigador1':
                            $campo="concat(p2.nombre,' ',p2.apellido)";
                            break;
                        case 'investigador2':
                            $campo="concat(p3.nombre,' ',p3.apellido)";
                            break;
                        case 'sector':
                            $campo="concat(e.abv,' | ',s.sector)";
                            break;
                        case 'descr':
                            $campo="r.descripcion";
                            break;
                        case 'fecha_set_crit':
                            $campo="r.fecha_set_crit";
                            break;
                        default :
                            $campo='r.'.$campo;
                            break;
                    }
                }
                unset($campo);
    //                echo "<pre>". print_r($campos,true)."<pre>";
                //si viene un solo campo de busqueda
                if(count($campos)==1)
                    $this->db->where("$campos[0] like","'%".$busqueda."%'",FALSE);
                else
                {
                    $n=0;
                    foreach ($campos as $campo)
                    {
                        if ($n==0)
                            $this->db->where("($campo like","'%".$busqueda."%'",FALSE);
                        else
                        {
                            if ($n==count($campos)-1)
                                $this->db->or_where("$campo like","'%".$busqueda."%')",FALSE);
                            else
                                $this->db->or_where("$campo like","'%".$busqueda."%'",FALSE);


                        }
                    $n++;     

                    }

                }

            }//fin if de Busqueda
            
            if($arrayFiltros != "")
            {
                if($arrayFiltros['id_clasificacion'] != "")
                {
                    $this->db->join('gr_rmc_clasificaciones grcla','grcla.id_rmc = r.id_rmc','left');
                    $this->db->join('gr_clasificaciones cla','cla.id_clasificacion = grcla.id_clasificacion','left');
                    $this->db->where_in('grcla.id_clasificacion',$arrayFiltros['id_clasificacion']);
                }
                
                if ($arrayFiltros['f_desde']!="" && $arrayFiltros['f_hasta']!="")
                    $this->db->where("r.fecha_alta BETWEEN '".$arrayFiltros['f_desde']."' AND '".$arrayFiltros['f_hasta']."'", NULL, FALSE );
                else
                {
                    if ($arrayFiltros['f_desde']!="" && $arrayFiltros['f_hasta']=="")
                        $this->db->where("r.fecha_alta >=",$arrayFiltros['f_desde']);
                    else
                    {
                        if($arrayFiltros['f_hasta']!="" && $arrayFiltros['f_desde']=="")
                            $this->db->where("r.fecha_alta <=",$arrayFiltros['f_hasta']);
                    }
                }
                
                if($arrayFiltros['id_estado_inv'] != "")
                    $this->db->where('r.id_estado_inv',$arrayFiltros['id_estado_inv']);
                
                if($arrayFiltros['id_criticidad'] != "")
                    $this->db->where('r.id_criticidad',$arrayFiltros['id_criticidad']);
            }//Fin filtros
           
//            $empresas=array(2,3);
//            if($usuario['gr']!=1)
//            {
////                $this->db->where_in('(pto2.id_area',$usuario['areas_inferiores'],FALSE);
////                  $this->db->where_in('pto.id_area',$usuario['areas_inferiores'],FALSE);
////                $this->db->where("pto.id_area in(".$usuario['areas_inferiores'].")");
//                if($usuario['usuario']['id_empresa']==1)
//                    $this->db->where_in("s.id_empresa",$empresas);
//                else
//                    $this->db->where("s.id_empresa",$usuario['usuario']['id_empresa']);
//                $this->db->or_where("r.id_usuario_alta",$usuario['usuario']['id_usuario']);
//                $this->db->or_where("r.id_investigador1",$usuario['usuario']['id_usuario']);
//                $this->db->or_where("r.id_investigador2",$usuario['usuario']['id_usuario']);
//
//            }
        if ($sort!="")
        {
            switch ($sort)
            {
                case 'id_rmc':
                case 'fecha_alta':
                case 'fecha_set_crit':
                case 'fecha_vto_inv':
                    $ordenar="r.".$sort;
                    break;
                case 'usuario_alta':
                    $ordenar="concat(p.nombre,p.apellido)";
                    break;
                 case 'sector':
                    $ordenar="concat(e.abv,s.sector)";
                    break;
                 case 'criticidad':
                    $ordenar="r.id_criticidad";
                    break;
                 case 'estado':
                    $ordenar="r.id_estado_inv";
                    break;
                 case 'tareas':
                    $ordenar="(select count(t.id_tarea) from gr_tareas t where t.id_herramienta=r.id_rmc and t.id_tipo_herramienta=1)";
                    break;
                default:
                    $ordenar="r.id_rmc";
            }
            $this->db->order_by($ordenar, $dir);
        }
        else
            $this->db->order_by("r.id_rmc", "desc"); 
        $this->db->limit($limit,$start); 

        $query = $this->db->get();
//        echo $this->db->last_query();
        $res = $query->result_array();
        $num = $this->cantSql('r.id_rmc',$this->db->last_query());
        if ($num > 0)
        {
            return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
        }
        else
            return '({"total":"0","rows":""})';
    }
	
        function cantSql($count,$last_query)
        {
            $sql=  explode('FROM', $last_query);
            $sql=  explode('ORDER BY', $sql[1]);
            $sql=  "SELECT count($count) as cantidad FROM ".$sql[0];
            $query =$this->db->query($sql);
            $res = $query->result();
            return $res[0]->cantidad;
        }
         public function verificaRePost($datos)
        {
            $this->db->select('count(id_rmc) as cant',false);
            $this->db->from('gr_rmc');
            $this->db->where("id_usuario_alta",$datos['id_usuario_alta']);
            $this->db->where("id_sector",$datos['id_sector']);
            $this->db->where("descripcion",$datos['descripcion']);
            $this->db->where("TIME_TO_SEC(TIMEDIFF( CURRENT_TIMESTAMP,fecha_alta)) <",10);//que el último insert no haya sido anterior a 10seg
            $query = $this->db->get();
//            echo $this->db->last_query();
            $res = $query->row();
            $num=count($res);
            if ($num > 0)
            {
                    return $res->cant;
            }
            else
                    return 0;
        }
        public function insert($datos)
	{
		$this->db->trans_begin();
		
		$this->db->insert('gr_rmc',$datos);
		$last=$this->db->insert_id();
		
		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			return false;
		}
		else
		{
			$this->db->trans_commit();
			return $last;
		}
	}
        public function update($id,$datos)
	{
            $this->db->set('fecha_set_crit', 'now()',false);
            switch ($datos['id_criticidad'] )
            {
                case 1:
                    $this->db->set('fecha_vto_inv', "ADDTIME(now(),'48:00:00.0')",false); //crítica 48hrs
                    break;
                case 2:
                    $this->db->set('fecha_vto_inv', "ADDTIME(now(),'72:00:00.0')",false); //alta 72Hrs
                    break;
            }
            $this->db->where('id_rmc', $id);
            $edit=$this->db->update('gr_rmc', $datos);
//            echo $this->db->last_query();
            if(!$edit)
                    return false;
            else
                    return true;
	}
        public function cerrarInvestigacion($id)
	{
            $this->db->set('id_estado_inv',2);// id_estado_inv=2 => Cerrada
            $this->db->set('fecha_cierre','now()',false);// id_estado_inv=2 => Cerrada
            $this->db->where('id_rmc', $id);
            $edit=$this->db->update('gr_rmc');
//            echo $this->db->last_query();
            if(!$edit)
                    return false;
            else
                    return true;
		
	}
        public function dameRmc($id)
	{
            $this->db->select("r.id_rmc,r.id_usuario_alta,r.descripcion,r.id_criticidad");
            $this->db->select("e.id_empresa");
            $this->db->select("concat(p.nombre,' ',p.apellido) as usuario_alta",false);
            $this->db->select('concat(p.nombre," ",p.apellido," <",u.email,">") as mailUsuarioAlta',false);
            $this->db->select("concat(p2.nombre,' ',p2.apellido) as inv1",false);
            $this->db->select('u2.email as mailInv1',false);
            $this->db->select("concat(p3.nombre,' ',p3.apellido) as inv2",false);
            $this->db->select('u3.email as mailInv2',false);
            $this->db->select("concat(p4.nombre,' ',p4.apellido) as usuarioSetCrit",false);
            $this->db->select("DATE_FORMAT(r.fecha_alta,'%d/%m/%Y') as fecha_alta", FALSE);
            $this->db->select("concat(e.abv,' | ',s.sector) as sector",false);
//            $this->db->select("DATE_FORMAT(addtime(r.fecha_set_crit,'24:00:00'),'%d/%m/%Y %H:%i') as vto_inv",false);
            $this->db->select(" CASE 
                                WHEN r.id_criticidad=1 THEN 'Crítico'
                                WHEN id_criticidad=2 THEN 'Alto'
                                WHEN id_criticidad=3 THEN 'Menor'
                                ELSE ''
                            END as grado",FALSE);
            $this->db->select(" CASE 
                                WHEN r.id_criticidad=1 THEN DATE_FORMAT(addtime(r.fecha_set_crit,'24:00:00'),'%d/%m/%Y %H:%i')
                                WHEN id_criticidad=2 THEN DATE_FORMAT(addtime(r.fecha_set_crit,'72:00:00'),'%d/%m/%Y %H:%i')
                                WHEN id_criticidad=3 THEN ''
                                ELSE ''
                            END as vto_inv",FALSE);
            $this->db->from('gr_rmc r');
            $this->db->join('gr_usuarios gu','gu.id_usuario = r.id_usuario_alta','inner');
            $this->db->join('gr_usuarios gu2','gu2.id_usuario = r.id_investigador1','left');
            $this->db->join('gr_usuarios gu3','gu3.id_usuario = r.id_investigador2','left');
            $this->db->join('gr_usuarios gu4','gu4.id_usuario = r.id_usuario_set_crit','left');
            $this->db->join('sys_usuarios u','u.id_usuario = gu.id_usuario','inner');
            $this->db->join('sys_usuarios u2','u2.id_usuario = gu2.id_usuario','left');
            $this->db->join('sys_usuarios u3','u3.id_usuario = gu3.id_usuario','left');
            $this->db->join('sys_usuarios u4','u4.id_usuario = gu4.id_usuario','left');
            $this->db->join('grl_personas p','p.id_persona = u.id_persona','left');
            $this->db->join('grl_personas p2','p2.id_persona = u2.id_persona','left');
            $this->db->join('grl_personas p3','p3.id_persona = u3.id_persona','left');
            $this->db->join('grl_personas p4','p4.id_persona = u4.id_persona','left');
            $this->db->join('gr_sectores_papelera s','s.id_sector = r.id_sector','inner');
            $this->db->join('grl_empresas e','e.id_empresa = s.id_empresa','inner');
            $this->db->where("r.habilitado",1);
            $this->db->where("r.id_rmc",$id);
            $query = $this->db->get();
             $res = $query->result_array();
//            $res = $query->row();
            $num=count($res);
            if ($num > 0)
            {
                    return $res[0];
            }
            else
                    return 0;
		
	}
        
         public function verificaInvesigador($id_usuario,$id_rmc)
        {
            $this->db->select('count(id_rmc) as cant',false);
            $this->db->from('gr_rmc');
            $this->db->where("id_rmc",$id_rmc);
            $this->db->where("(id_investigador1",$id_usuario);
            $this->db->or_where("id_investigador2",$id_usuario.")",FALSE);
            $query = $this->db->get();
//            echo $this->db->last_query();
            $res = $query->row();
            $num=count($res);
            if ($num > 0)
            {
                    return 1;
            }
            else
                    return 0;
        }
        public function dameRmcConInvVencidasHoy()
	{
            $estados_crit=array(1,2); //Crítica, Alta
            $this->db->select('r.id_rmc');
//            $this->db->select('DATEDIFF (now(),t.fecha_vto)',false);
            $this->db->from('gr_rmc r');
            $this->db->where("r.habilitado",1);
            $this->db->where("r.id_estado_inv",1);//Investigacion abierta
            $this->db->where_in("r.id_criticidad",$estados_crit);
            $this->db->where("r.fecha_vto_inv <",'CURRENT_DATE()',false);
            $query = $this->db->get();
            $num = $query->num_rows();
            $res = $query->result_array();
            if ($num > 0)
                    return $res;
            else
                    return 0;
	}
        public function dameCerradasAyer()
	{
            $estados_crit=array(1,2); //Crítica, Alta
            $this->db->select('r.id_rmc');
            $this->db->select('s.id_empresa');
            $this->db->select("concat(p.nombre,' ',p.apellido,' - ',pto.puesto,' (',ee1.abv,')') as usuario_alta",false);
            $this->db->select("concat(p2.nombre,' ',p2.apellido,' - ',pto2.puesto,' (',ee2.abv,')') as inv1",false);
            $this->db->select("concat(p3.nombre,' ',p3.apellido,' - ',pto3.puesto,' (',ee3.abv,')') as inv2",false);
            $this->db->select("concat(e.abv,' | ',s.sector) as sector",false);
            $this->db->select('r.descripcion as descr',false);
            $this->db->select("DATE_FORMAT(r.fecha_alta,'%d/%m/%Y %H:%i') as fecha_alta", FALSE);
            $this->db->select(" CASE 
                                WHEN r.id_criticidad=1 THEN concat('Crítico (',p4.nombre,' ',p4.apellido,' - ',DATE_FORMAT(r.fecha_set_crit,'%d/%m/%Y'),')')
                                WHEN id_criticidad=2 THEN concat ('Alto (',p4.nombre,' ',p4.apellido,' - ',DATE_FORMAT(r.fecha_set_crit,'%d/%m/%Y'),')')
                                WHEN id_criticidad=3 THEN concat('Menor (',p4.nombre,' ',p4.apellido,' - ',DATE_FORMAT(r.fecha_set_crit,'%d/%m/%Y'),')')
                                ELSE ''
                            END as grado",FALSE);
            $this->db->from('gr_rmc r');
             $this->db->join('gr_usuarios gu','gu.id_usuario = r.id_usuario_alta','inner');
            $this->db->join('gr_usuarios gu2','gu2.id_usuario = r.id_investigador1','left');
            $this->db->join('gr_usuarios gu3','gu3.id_usuario = r.id_investigador2','left');
            $this->db->join('gr_usuarios gu4','gu4.id_usuario = r.id_usuario_set_crit','left');
            $this->db->join('sys_usuarios u','u.id_usuario = gu.id_usuario','inner');
            $this->db->join('sys_usuarios u2','u2.id_usuario = gu2.id_usuario','left');
            $this->db->join('sys_usuarios u3','u3.id_usuario = gu3.id_usuario','left');
            $this->db->join('sys_usuarios u4','u4.id_usuario = gu4.id_usuario','left');
            $this->db->join('grl_personas p','p.id_persona = u.id_persona','left');
            $this->db->join('grl_personas p2','p2.id_persona = u2.id_persona','left');
            $this->db->join('grl_personas p3','p3.id_persona = u3.id_persona','left');
            $this->db->join('grl_personas p4','p4.id_persona = u4.id_persona','left');
             $this->db->join('gr_puestos pto','pto.id_puesto = gu.id_puesto','inner');
            $this->db->join('gr_puestos pto2','pto2.id_puesto = gu2.id_puesto','inner');
            $this->db->join('gr_puestos pto3','pto3.id_puesto = gu3.id_puesto','inner');
            $this->db->join('gr_organigramas or1','or1.id_organigrama = pto.id_organigrama','inner');
            $this->db->join('grl_empresas ee1','ee1.id_empresa = or1.id_empresa','inner');
            $this->db->join('gr_organigramas or2','or2.id_organigrama = pto2.id_organigrama','inner');
            $this->db->join('grl_empresas ee2','ee2.id_empresa = or2.id_empresa','inner');
            $this->db->join('gr_organigramas or3','or3.id_organigrama = pto3.id_organigrama','inner');
            $this->db->join('grl_empresas ee3','ee3.id_empresa = or3.id_empresa','inner');
            $this->db->join('gr_sectores_papelera s','s.id_sector = r.id_sector','inner');
            $this->db->join('grl_empresas e','e.id_empresa = s.id_empresa','inner');
            $this->db->where("r.habilitado",1);
            $this->db->where("r.id_estado_inv",2);//Investigacion abierta
            $this->db->where_in("r.id_criticidad",$estados_crit);
            $this->db->where("DATEDIFF(CURRENT_DATE(),r.fecha_cierre)",'1',false);
            
            $this->db->where("r.id_rmc !=",851);//Temporal....borrar cuando sistemas confirme!!!!!
            
            $query = $this->db->get();
            $num = $query->num_rows();
            $res = $query->result_array();
            if ($num > 0)
                    return $res;
            else
                    return 0;
	}
        public function dameRmcCerrada($id_rmc)
	{
            $estados_crit=array(1,2); //Crítica, Alta
            $this->db->select('r.id_rmc');
            $this->db->select('s.id_empresa');
            $this->db->select("concat(p.nombre,' ',p.apellido,' - ',pto.puesto,' (',ee1.abv,')') as usuario_alta",false);
            $this->db->select("concat(p2.nombre,' ',p2.apellido,' - ',pto2.puesto,' (',ee2.abv,')') as inv1",false);
            $this->db->select("concat(p3.nombre,' ',p3.apellido,' - ',pto3.puesto,' (',ee3.abv,')') as inv2",false);
            $this->db->select("concat(e.abv,' | ',s.sector) as sector",false);
            $this->db->select('r.descripcion as descr',false);
            $this->db->select("DATE_FORMAT(r.fecha_alta,'%d/%m/%Y %H:%i') as fecha_alta", FALSE);
            $this->db->select(" CASE 
                                WHEN r.id_criticidad=1 THEN concat('Crítico (',p4.nombre,' ',p4.apellido,' - ',DATE_FORMAT(r.fecha_set_crit,'%d/%m/%Y'),')')
                                WHEN id_criticidad=2 THEN concat ('Alto (',p4.nombre,' ',p4.apellido,' - ',DATE_FORMAT(r.fecha_set_crit,'%d/%m/%Y'),')')
                                WHEN id_criticidad=3 THEN concat('Menor (',p4.nombre,' ',p4.apellido,' - ',DATE_FORMAT(r.fecha_set_crit,'%d/%m/%Y'),')')
                                ELSE ''
                            END as grado",FALSE);
            $this->db->from('gr_rmc r');
             $this->db->join('gr_usuarios gu','gu.id_usuario = r.id_usuario_alta','inner');
            $this->db->join('gr_usuarios gu2','gu2.id_usuario = r.id_investigador1','left');
            $this->db->join('gr_usuarios gu3','gu3.id_usuario = r.id_investigador2','left');
            $this->db->join('gr_usuarios gu4','gu4.id_usuario = r.id_usuario_set_crit','left');
            $this->db->join('sys_usuarios u','u.id_usuario = gu.id_usuario','inner');
            $this->db->join('sys_usuarios u2','u2.id_usuario = gu2.id_usuario','left');
            $this->db->join('sys_usuarios u3','u3.id_usuario = gu3.id_usuario','left');
            $this->db->join('sys_usuarios u4','u4.id_usuario = gu4.id_usuario','left');
            $this->db->join('grl_personas p','p.id_persona = u.id_persona','left');
            $this->db->join('grl_personas p2','p2.id_persona = u2.id_persona','left');
            $this->db->join('grl_personas p3','p3.id_persona = u3.id_persona','left');
            $this->db->join('grl_personas p4','p4.id_persona = u4.id_persona','left');
             $this->db->join('gr_puestos pto','pto.id_puesto = gu.id_puesto','inner');
            $this->db->join('gr_puestos pto2','pto2.id_puesto = gu2.id_puesto','inner');
            $this->db->join('gr_puestos pto3','pto3.id_puesto = gu3.id_puesto','inner');
            $this->db->join('gr_organigramas or1','or1.id_organigrama = pto.id_organigrama','inner');
            $this->db->join('grl_empresas ee1','ee1.id_empresa = or1.id_empresa','inner');
            $this->db->join('gr_organigramas or2','or2.id_organigrama = pto2.id_organigrama','inner');
            $this->db->join('grl_empresas ee2','ee2.id_empresa = or2.id_empresa','inner');
            $this->db->join('gr_organigramas or3','or3.id_organigrama = pto3.id_organigrama','inner');
            $this->db->join('grl_empresas ee3','ee3.id_empresa = or3.id_empresa','inner');
            $this->db->join('gr_sectores_papelera s','s.id_sector = r.id_sector','inner');
            $this->db->join('grl_empresas e','e.id_empresa = s.id_empresa','inner');
            $this->db->where("r.habilitado",1);
            $this->db->where("r.id_rmc",$id_rmc);
            $this->db->where("r.id_estado_inv",2);//Investigacion cerrada
            $this->db->where_in("r.id_criticidad",$estados_crit);
            $this->db->where("DATEDIFF(CURRENT_DATE(),r.fecha_cierre)>=",'1',false);
            $query = $this->db->get();
            $num = $query->num_rows();
            $res = $query->result_array();
            if ($num > 0)
                    return $res[0];
            else
                    return 0;
	}
        public function dameRmcConInvVencidas()
	{
            $estados_crit=array(1,2); //Crítica, Alta
            $this->db->select('r.id_rmc');
            $this->db->select('DATEDIFF (now(),r.fecha_vto_inv) as dias_vto',false);
            $this->db->from('gr_rmc r');
            $this->db->where("r.habilitado",1);
            $this->db->where("r.id_estado_inv",3);//Investigacion abierta
            $this->db->where_in("r.id_criticidad",$estados_crit);
            $this->db->where("r.fecha_vto_inv <",'CURRENT_DATE()',false);
            $query = $this->db->get();
            $num = $query->num_rows();
            $res = $query->result_array();
            if ($num > 0)
                    return $res;
            else
                    return 0;
	}
        public function vencerRmc()
        {
//            $sql=("update gr_rmc set id_estado_inv=3 where id_estado_inv=1 and fecha_vto_inv<CURRENT_DATE();");
            $this->db->set('id_estado_inv', 3); //Vencida
            $this->db->where('id_estado_inv', 1); //Abierta
            $this->db->where('fecha_vto_inv <', 'CURRENT_DATE()',false); //las que vencieron ayer
            $edit=$this->db->update('gr_rmc');
//            echo $this->db->last_query();
            if(!$edit)
                    return 0;
            else
                    return 1;
		
        }
        
        public function listado_excel( $start, $limit, $arrayFiltros = "", $busqueda, $campos,$sort="", $dir="")
        {
//            echo"<pre>".print_r($usuario,true)."</pre>";
            $this->db->select('r.id_rmc');
            $this->db->select("DATE_FORMAT(r.fecha_alta,'%d/%m/%Y') as fecha_alta", FALSE);
            $this->db->select("concat(p.nombre,' ',p.apellido) as usuario_alta",false);
            $this->db->select('r.descripcion as descr',false);
            $this->db->select("concat(e.abv,' | ',s.sector) as sector",false);
            $this->db->select(" CASE 
                                WHEN r.id_criticidad=1 THEN 'Crítico'
                                WHEN r.id_criticidad=2 THEN 'Alto'
                                WHEN r.id_criticidad=3 THEN 'Menor'
                                WHEN r.id_criticidad=4 THEN 'Fuera del Alcance de Gestion'
                                ELSE '?'
                            END as criticidad",FALSE);
            $this->db->select("DATE_FORMAT(r.fecha_set_crit,'%d/%m/%Y') as fecha_set_crit", FALSE);
            $this->db->select("(select GROUP_CONCAT(DISTINCT clasificacion SEPARATOR '; ') from 
                                gr_rmc gr
                                inner join gr_rmc_clasificaciones grc on gr.id_rmc=grc.id_rmc
                                inner join gr_clasificaciones gc on grc.id_clasificacion=gc.id_clasificacion
                                where gr.id_rmc=r.id_rmc) as clasificacion",false);
            $this->db->select("DATE_FORMAT(r.fecha_vto_inv,'%d/%m/%Y') as fecha_vto_inv", FALSE);
            $this->db->select(" CASE 
                                WHEN r.id_estado_inv=1 THEN 'Abierta'
                                WHEN r.id_estado_inv=2 THEN 'Cerrada'
                                WHEN r.id_estado_inv=3 THEN 'Vencida'
                                ELSE '?'
                            END as estado",FALSE);
            $this->db->select("concat(p2.nombre,' ',p2.apellido) as investigador1",false);
            $this->db->select("concat(p3.nombre,' ',p3.apellido) as investigador2",false);
            $this->db->select("(select count(t.id_tarea) from gr_tareas t where t.id_herramienta=r.id_rmc and t.id_tipo_herramienta=1) as tareas",false);
            $this->db->from('gr_rmc r');
            $this->db->join('gr_usuarios gu','gu.id_usuario = r.id_usuario_alta','inner');
            $this->db->join('gr_usuarios gu2','gu2.id_usuario = r.id_investigador1','left');
            $this->db->join('gr_usuarios gu3','gu3.id_usuario = r.id_investigador2','left');
            $this->db->join('sys_usuarios u','u.id_usuario = gu.id_usuario','inner');
            $this->db->join('sys_usuarios u2','u2.id_usuario = gu2.id_usuario','left');
            $this->db->join('sys_usuarios u3','u3.id_usuario = gu3.id_usuario','left');
            $this->db->join('grl_personas p','p.id_persona = u.id_persona','left');
            $this->db->join('grl_personas p2','p2.id_persona = u2.id_persona','left');
            $this->db->join('grl_personas p3','p3.id_persona = u3.id_persona','left');
            $this->db->join('gr_puestos pto','pto.id_puesto = gu.id_puesto','inner');
            $this->db->join('gr_sectores_papelera s','s.id_sector = r.id_sector','inner');
            $this->db->join('grl_empresas e','e.id_empresa = s.id_empresa','inner');
            $this->db->where("r.habilitado",1);
            
            if ($busqueda!="" && count($campos)>0)
            {
                foreach ($campos as &$campo)
                {
                    switch ($campo)
                    {
                        case 'usuario_alta':
                            $campo="concat(p.nombre,' ',p.apellido)";
                            break;
                        case 'investigador1':
                            $campo="concat(p2.nombre,' ',p2.apellido)";
                            break;
                        case 'investigador2':
                            $campo="concat(p3.nombre,' ',p3.apellido)";
                            break;
                        case 'sector':
                            $campo="concat(e.abv,' | ',s.sector)";
                            break;
                        case 'descr':
                            $campo="r.descripcion";
                            break;
                        case 'fecha_set_crit':
                            $campo="r.fecha_set_crit";
                            break;
                        case 'clasificacion':
                            $campo="cla.clasificacion";
                            break;
                    }
                }
                unset($campo);
    //                echo "<pre>". print_r($campos,true)."<pre>";
                //si viene un solo campo de busqueda
                if(count($campos)==1)
                    $this->db->where("$campos[0] like","'%".$busqueda."%'",FALSE);
                else
                {
                    $n=0;
                    foreach ($campos as $campo)
                    {
                        if ($n==0)
                            $this->db->where("($campo like","'%".$busqueda."%'",FALSE);
                        else
                        {
                            if ($n==count($campos)-1)
                                $this->db->or_where("$campo like","'%".$busqueda."%')",FALSE);
                            else
                                $this->db->or_where("$campo like","'%".$busqueda."%'",FALSE);
                        }
                    $n++;     
                    }
                }
            }//fin if de Busqueda
            
            if($arrayFiltros != "")
            {
                if($arrayFiltros['id_clasificacion'] != "")
                {
                    $this->db->join('gr_rmc_clasificaciones grcla','grcla.id_rmc = r.id_rmc','left');
                    $this->db->join('gr_clasificaciones cla','cla.id_clasificacion = grcla.id_clasificacion','left');
                    $this->db->where_in('grcla.id_clasificacion',$arrayFiltros['id_clasificacion']);
                }
                
                if ($arrayFiltros['f_desde']!="" && $arrayFiltros['f_hasta']!="")
                    $this->db->where("r.fecha_alta BETWEEN '".$arrayFiltros['f_desde']."' AND '".$arrayFiltros['f_hasta']."'", NULL, FALSE );
                else
                {
                    if ($arrayFiltros['f_desde']!="" && $arrayFiltros['f_hasta']=="")
                        $this->db->where("r.fecha_alta >=",$arrayFiltros['f_desde']);
                    else
                    {
                        if($arrayFiltros['f_hasta']!="" && $arrayFiltros['f_desde']=="")
                            $this->db->where("r.fecha_alta <=",$arrayFiltros['f_hasta']);
                    }
                }
                
                if($arrayFiltros['id_estado_inv'] != "")
                    $this->db->where('r.id_estado_inv',$arrayFiltros['id_estado_inv']);
                
                if($arrayFiltros['id_criticidad'] != "")
                    $this->db->where('r.id_criticidad',$arrayFiltros['id_criticidad']);
            }//Fin filtros
           
        if ($sort!="")
        {
            switch ($sort)
            {
                case 'id_rmc':
                case 'fecha_alta':
                case 'fecha_set_crit':
                case 'fecha_vto_inv':
                    $ordenar="r.".$sort;
                    break;
                case 'usuario_alta':
                    $ordenar="concat(p.nombre,p.apellido)";
                    break;
                 case 'sector':
                    $ordenar="concat(e.abv,s.sector)";
                    break;
                 case 'criticidad':
                    $ordenar="r.id_criticidad";
                    break;
                 case 'estado':
                    $ordenar="r.id_estado_inv";
                    break;
                 case 'tareas':
                    $ordenar="(select count(t.id_tarea) from gr_tareas t where t.id_herramienta=r.id_rmc and t.id_tipo_herramienta=1)";
                    break;
                default:
                    $ordenar="r.id_rmc";
            }
            $this->db->order_by($ordenar, $dir);
        }
        else
            $this->db->order_by("r.id_rmc", "desc"); 
        
        $this->db->limit($limit,$start); 
        $query = $this->db->get();
//         echo $this->db->last_query();
        $num = $query->num_rows();
        $res = $query->result_array();

        if ($num > 0)
            return $res;
        else
            return 0;
    }
}	
?>