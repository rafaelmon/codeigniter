<?php
class Cpp_eventos_model extends CI_Model
{
    public function listado($start, $limit, $sort="", $dir="", $busqueda, $campos,  $filtros="", $id_usuario, $admin=0,$estadosBotones)
    {
//        echo "<pre>".print_r($permiso,true)."</pre>";
        
        $this->db->select('e.id_evento as id, e.descripcion,e.horas_perdidas as hrs,e.id_criticidad,e.id_estado,e.id_usuario_alta');
        $this->db->select("DATE_FORMAT(e.fecha_cierre,'%d/%m/%Y') as fecha_cierre", FALSE);
        $this->db->select("concat(p.nombre,' ',p.apellido) as usuario_alta",false); 
        $this->db->select("DATE_FORMAT(e.verifica_ini,'%d/%m/%Y') as ver_ini", FALSE);
        $this->db->select("DATE_FORMAT(e.verifica_fin,'%d/%m/%Y') as ver_fin", FALSE);
        $this->db->select("concat(DATE_FORMAT(e.verifica_ini,'%d/%m/%Y'),' al ',DATE_FORMAT(e.verifica_fin,'%d/%m/%Y')) as ver_rango",false);
        $this->db->select("DATE_FORMAT(e.fecha_alta,'%d/%m/%Y') as fecha_alta", FALSE);
        $this->db->select('ee.estado,ee.estilo_color');
        $this->db->select("DATE_FORMAT(e.fecha_inicio,'%d/%m/%Y') as fecha_evento", FALSE);
        $this->db->select("(select sum(monto) from cpp_evento_consecuencias where id_evento=e.id_evento)  as monto", FALSE);
        $this->db->select("(select sum(set_monto) from cpp_evento_consecuencias where id_evento=e.id_evento)  as set_monto", FALSE);
        $this->db->select("(select sum(unidades_perdidas) from cpp_evento_consecuencias where id_evento=e.id_evento)  as unidades_perdidas", FALSE);
//        $this->db->select('concat(eq.tag," - ",eq.equipo) as equipo');
        $this->db->select("concat(DATE_FORMAT(e.fecha_inicio,'%d/%m/%Y'),' - ',TIME_FORMAT(e.hora_inicio,'%k:%i')) as fh_ini",false);
        $this->db->select("concat(DATE_FORMAT(e.fecha_fin,'%d/%m/%Y'),' - ',TIME_FORMAT(e.hora_fin,'%k:%i')) as fh_fin",false);
        $this->db->select('pp.producto');
        $this->db->select('s.sector');
        $this->db->select('c.criticidad');
        $this->db->select('(select GROUP_CONCAT(eq.tag," - ",eq.equipo SEPARATOR "; ") from cpp_equipos eq
                          INNER JOIN cpp_evento_equipos cee ON cee.id_equipo = eq.id_equipo
                          where cee.id_evento = e.id_evento
                          group by e.id_evento) as equipo',false);
        $this->db->select("if (CURRENT_DATE()>=e.verifica_ini and e.id_estado=6,1,0) as estado_ver",false);
        
        //******Botones*******
        //btn_eliminar
        if($admin == 1)
            $this->db->select('1 btn_eliminar');
        else
            $this->db->select("if (e.id_usuario_alta = $id_usuario,1,0) as btn_eliminar",false);
        //btn_calificar
            $this->db->select("if (((e.id_estado = 1 or e.id_estado = 2) and e.id_usuario_alta = $id_usuario),1,0) as btn_calificar",false);
        //btn_crit
            $this->db->select("if (e.id_estado = 2, if(".$estadosBotones['btn_crit']." != 0,1, if(e.id_usuario_alta = $id_usuario ,1,0)),0) as btn_crit",false);
        //btn_cerrar
            //$this->db->select("if(e.id_estado = 6, 0,if (e.q_tareas = 0,0,if (e.q_tareas =(select count(st2.id_tarea) from gr_tareas st2 where st2.id_tipo_herramienta = 8 and st2.id_herramienta IN (select ssc2.id_causa from cpp_causas ssc2 where ssc2.id_evento = e.id_evento) and st2.id_estado=9),0,if(e.id_estado != 5,0,if(e.id_usuario_alta != $id_usuario, 0,if((select count(st.id_tarea) from gr_tareas st where st.id_tipo_herramienta = 8 and st.id_herramienta IN (select ssc.id_causa from cpp_causas ssc where ssc.id_evento = e.id_evento) and st.id_estado IN (1,2,3,4,5,10))>0,0,1)))))) as btn_cerrar",false);
            $this->db->select("if(e.id_estado = 6, 0,if (e.q_tareas = 0,0,if(e.id_estado != 5,0,if(e.id_usuario_alta != $id_usuario, 0,if((select count(st.id_tarea) from gr_tareas st where st.id_tipo_herramienta = 8 and st.id_herramienta IN (select ssc.id_causa from cpp_causas ssc where ssc.id_evento = e.id_evento) and st.id_estado IN (1,2,3,4,5,10))=0 and (select count(st.id_tarea) from gr_tareas st where st.id_tipo_herramienta = 8 and st.id_herramienta IN (select ssc.id_causa from cpp_causas ssc where ssc.id_evento = e.id_evento) and st.id_estado = 9) > 0,1,0))))) as btn_cerrar",false);
        //btn_tarea
            $this->db->select("if ((e.id_estado != 5),0, if($id_usuario IN (select si.id_usuario from cpp_investigadores si where si.id_evento = e.id_evento),1,0)) as btn_tarea",false);
        //btn_causa
            $this->db->select("if ((e.id_estado != 4 and e.id_estado != 5),0, if($id_usuario IN (select si.id_usuario from cpp_investigadores si where si.id_evento = e.id_evento),1,0)) as btn_causa",false);
        //btn_monto
            if($estadosBotones['btn_monto'] == 0)
                $this->db->select('0 as btn_monto');
            else
            $this->db->select(" case when e.id_estado IN (".$estadosBotones['btn_monto'].") then 1
                                else 0 end as btn_monto",false);
       //btn_tn
            if($estadosBotones['btn_tn'] == 0)
                $this->db->select('0 as btn_tn');
            else
                $this->db->select(" case when e.id_estado IN (".$estadosBotones['btn_tn'].") then 1
                                    else 0 end as btn_tn",false);
       //btn_ei
            if($estadosBotones['btn_ei'] == 0)
                $this->db->select('0 as btn_ei');
            else
                $this->db->select(" case when e.id_estado IN (".$estadosBotones['btn_ei'].") then 1
                                    else 0 end as btn_ei",false);
        //btn_edit_crit
            if($estadosBotones['btn_edit_crit'] == 0)
                $this->db->select('0 as btn_edit_crit');
            else
                $this->db->select(" case when e.id_estado IN (".$estadosBotones['btn_edit_crit'].") then 1
                                    else 0 end as btn_edit_crit",false);
        //btn_edit_evento
            if($estadosBotones['btn_edit_evento'] == 0)
                $this->db->select('0 as btn_edit_evento');
            else
                $this->db->select(" case when e.id_estado IN (".$estadosBotones['btn_edit_evento'].") then 1
                                    else 0 end as btn_edit_evento",false);
       //btn_cancelar
            if($admin == 1)
            {
                $this->db->select(" case when e.id_estado IN (2,3,4,5) then 1
                                    else 0 end as btn_cancelar",false);
            }
            else
            {
                if($estadosBotones['btn_cancelar'] == 0)
                    $this->db->select('0 as btn_cancelar');
                else
                    $this->db->select(" case when e.id_estado IN (".$estadosBotones['btn_cancelar'].") then 1
                                        else 0 end as btn_cancelar",false);
            }
            
        //btn_verificar_tareas
         $this->db->select("if (e.id_usuario_alta = $id_usuario and CURRENT_DATE()>=e.verifica_ini and e.id_estado=6,1,0) as btn_verificar_tareas",false);
            
        $this->db->from('cpp_eventos e');
        $this->db->join('sys_usuarios u','u.id_usuario = e.id_usuario_alta','inner');
        $this->db->join('grl_personas p','p.id_persona = u.id_persona','inner');
        $this->db->join('cpp_estados ee','ee.id_estado = e.id_estado','inner');
        $this->db->join('cpp_productos pp','pp.id_producto = e.id_producto','left');
        $this->db->join('cpp_sectores s','s.id_sector = e.id_sector','inner');
        $this->db->join('cpp_criticidades c','c.id_criticidad = e.id_criticidad','left');
//        $this->db->join('cpp_evento_equipos cee','cee.id_evento = e.id_evento','inner');
//        $this->db->join('cpp_equipos eq','eq.id_equipo = cee.id_equipo','inner');
        $this->db->where('e.habilitado',1);
        
        if ($busqueda!="" && count($campos)>0)
        {
            foreach ($campos as &$campo)
            {
                switch ($campo)
                {
                    case 'descripcion':
                        $campo="e.".$campo;
                        break;
                    case 'usuario_alta':
                        $campo="concat(p.nombre,' ',p.apellido)";
                        break;
                }
            }
            unset($campo);
            
            if(count($campos)==1)
            {
                $this->db->where($campos[0]. " like","'%".$busqueda."%'",FALSE);
            }
            else
            {
                $n=0;
                foreach ($campos as $campo)
                {
                    if ($n==0)
                        {
                        $this->db->where("(".$campo." like","'%".$busqueda."%'",FALSE);
                        }
                    else
                    {
                        if ($n==count($campos)-1)
                        {
                            $this->db->or_where($campo ." like","'%".$busqueda."%')",FALSE);
                        }
                        else
                        {
                            $this->db->or_where($campo." like","'%".$busqueda."%'",FALSE);
                        }
                    }
                   $n++;     
                }
            }
        }
        if ($filtros!="")
        {
            if ($filtros[0]!="")
                $this->db->where('e.id_criticidad',$filtros[0]);
            if ($filtros[1]!="")
                $this->db->where('e.id_estado',$filtros[1]);
        }
		
        if ($sort!="")
        {
             switch ($sort)
            {
                case 'id':
                    $ordenar="e.id_evento";
                    break;
                case 'producto':
                    $ordenar="pp.producto";
                    break;
                case 'fecha_evento':
                    $ordenar="e.fecha_inicio";
                    break;
                case 'fecha_alta':
                    $ordenar="e.fecha_alta";
                    break;
                case 'ver_rango':
                    $ordenar="e.verifica_ini";
                    break;
                default:
                    $ordenar="e.id_evento";
                    break;

            }
            $this->db->order_by($ordenar, $dir);
        }
        else
        {
            $this->db->order_by('e.fecha_alta','desc');
        }
        
        $this->db->limit($limit,$start); 
        $query = $this->db->get();
//         echo $this->db->last_query();
        $last_query=$this->db->last_query();
        $pasQuery=$this->sqlTxt('e.id_evento',$last_query);
        $num = $this->cantSql($pasQuery);
        $res = $query->result_array();

        if ($num > 0)
        {
            return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
        }
        else
        {
            return '({"total":"0","rows":""})';
        }
    }
    public function listado_repo($start, $limit, $sort="", $dir="", $filtros="")
    {
//        echo "<pre>".print_r($permiso,true)."</pre>";
        //filtro --> (1->
        $this->db->select('e.id_evento as id, e.descripcion,e.horas_perdidas as hrs,e.id_criticidad,e.id_estado,e.id_usuario_alta');
        $this->db->select("concat(p.nombre,' ',p.apellido) as usuario_alta",false); 
        $this->db->select("DATE_FORMAT(e.fecha_alta,'%d/%m/%Y') as fecha_alta", FALSE);
        $this->db->select('ee.estado,ee.estilo_color');
//        $this->db->select("DATE_FORMAT(e.fecha_inicio,'%d/%m/%Y') as fecha_evento", FALSE);
        $this->db->select("(select sum(monto) from cpp_evento_consecuencias where id_evento=e.id_evento)  as monto", FALSE);
        $this->db->select("(select sum(unidades_perdidas) from cpp_evento_consecuencias where id_evento=e.id_evento)  as unidades_perdidas", FALSE);
//        $this->db->select('concat(eq.tag," - ",eq.equipo) as equipo');
        $this->db->select("concat(DATE_FORMAT(e.fecha_inicio,'%d/%m/%Y'),' - ',TIME_FORMAT(e.hora_inicio,'%k:%i')) as fh_ini",false);
        $this->db->select("concat(DATE_FORMAT(e.fecha_fin,'%d/%m/%Y'),' - ',TIME_FORMAT(e.hora_fin,'%k:%i')) as fh_fin",false);
        $this->db->select('pp.producto');
        $this->db->select('s.sector');
        $this->db->select('c.criticidad');
        $this->db->select('(select GROUP_CONCAT(eq.tag," - ",eq.equipo SEPARATOR "; ") from cpp_equipos eq
                          INNER JOIN cpp_evento_equipos cee ON cee.id_equipo = eq.id_equipo
                          where cee.id_evento = e.id_evento
                          group by e.id_evento) as equipo',false);    
        $this->db->from('cpp_eventos e');
        $this->db->join('sys_usuarios u','u.id_usuario = e.id_usuario_alta','inner');
        $this->db->join('grl_personas p','p.id_persona = u.id_persona','inner');
        $this->db->join('cpp_estados ee','ee.id_estado = e.id_estado','inner');
//        $this->db->join('cpp_equipos eq','eq.id_equipo = e.id_equipo','inner');
        $this->db->join('cpp_productos pp','pp.id_producto = e.id_producto','left');
        $this->db->join('cpp_sectores s','s.id_sector = e.id_sector','inner');
        $this->db->join('cpp_criticidades c','c.id_criticidad = e.id_criticidad','left');
        $this->db->where('e.habilitado',1);
        
       
        if ($filtros!="")
        {
            if ($filtros['id_criticidad']!="")
                $this->db->where('e.id_criticidad',$filtros['id_criticidad']);
            if ($filtros['id_estado']!="")
                $this->db->where('e.id_estado',$filtros['id_estado']);
            if ($filtros['f_ini']!="" && $filtros['f_fin']!="")
                $this->db->where("e.fecha_inicio BETWEEN '".$filtros['f_ini']."' AND '".$filtros['f_fin']."'", NULL, FALSE );
            elseif($filtros['f_ini']!="")
            {
                $this->db->where("e.fecha_inicio >=",$filtros['f_ini']);
            }
            if ($filtros['sectores']!="")
                $this->db->where_in('e.id_sector',$filtros['sectores']);
            if ($filtros['equipos']!="")
                $this->db->where_in('e.id_evento',$filtros['equipos']);
        }
		
        if ($sort!="")
        {
             switch ($sort)
            {
                case 'id':
                    $ordenar="e.id_evento";
                    break;
                case 'producto':
                    $ordenar="pp.producto";
                    break;
                case 'fecha_evento':
                    $ordenar="e.fecha_inicio";
                    break;
                case 'fecha_alta':
                    $ordenar="e.fecha_alta";
                    break;
                default:
                    $ordenar="e.fecha_inicio";
                    break;

            }
            $this->db->order_by($ordenar, $dir);
        }
        else
        {
            $this->db->order_by('e.fecha_inicio','desc');
        }
        
        $this->db->limit($limit,$start); 
        $query = $this->db->get();
//         echo $this->db->last_query();
        $q = $query->num_rows();
        if ($q > 0)
        {
            $last_query=$this->db->last_query();
            $pasQuery=$this->sqlTxt('e.id_evento',$last_query);
            $num = $this->cantSql($pasQuery);
            $res = $query->result_array();
            return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
        }
        else
        {
            return '({"total":"0","rows":""})';
        }
    }
    public function dameEventosRepoExcel($sort="",$dir="",$filtros="")
    {
//        echo "<pre>".print_r($permiso,true)."</pre>";
        
        $this->db->select('e.id_evento');
        $this->db->select('e.descripcion as evento');
        $this->db->select('e.horas_perdidas as hrs');
        $this->db->select("concat(p.nombre,' ',p.apellido) as usuario_alta",false); 
        $this->db->select("DATE_FORMAT(e.fecha_alta,'%d/%m/%Y') as fecha_alta", FALSE);
        $this->db->select('ee.estado,ee.estilo_color');
        $this->db->select("DATE_FORMAT(e.fecha_inicio,'%d/%m/%Y') as fecha_evento", FALSE);
        $this->db->select("(select sum(monto) from cpp_evento_consecuencias where id_evento=e.id_evento)  as monto", FALSE);
        $this->db->select("(select sum(unidades_perdidas) from cpp_evento_consecuencias where id_evento=e.id_evento)  as unidades_perdidas", FALSE);
//        $this->db->select('concat(eq.tag," - ",eq.equipo) as equipo');
        $this->db->select('(select GROUP_CONCAT(eq.tag," - ",eq.equipo SEPARATOR "; ") from cpp_equipos eq
                          INNER JOIN cpp_evento_equipos cee ON cee.id_equipo = eq.id_equipo
                          where cee.id_evento = e.id_evento
                          group by e.id_evento) as equipo',false);
        $this->db->select("concat(DATE_FORMAT(e.fecha_inicio,'%d/%m/%Y'),' - ',TIME_FORMAT(e.hora_inicio,'%k:%i')) as fh_ini",false);
        $this->db->select("concat(DATE_FORMAT(e.fecha_fin,'%d/%m/%Y'),' - ',TIME_FORMAT(e.hora_fin,'%k:%i')) as fh_fin",false);
        $this->db->select('pp.producto');
        $this->db->select('s.sector');
        $this->db->select('c.criticidad');
        $this->db->select("(select GROUP_CONCAT(concat(sp.nombre,' ',sp.apellido) SEPARATOR ' | ') from cpp_investigadores si 
                    inner join sys_usuarios su  on su.id_usuario=si.id_usuario
                    inner join grl_personas sp  on sp.id_persona=su.id_persona
                    where si.id_evento=e.id_evento)as ei",false);
        $this->db->from('cpp_eventos e');
        $this->db->join('sys_usuarios u','u.id_usuario = e.id_usuario_alta','inner');
        $this->db->join('grl_personas p','p.id_persona = u.id_persona','inner');
        $this->db->join('cpp_estados ee','ee.id_estado = e.id_estado','inner');
//        $this->db->join('cpp_equipos eq','eq.id_equipo = e.id_equipo','inner');
        $this->db->join('cpp_productos pp','pp.id_producto = e.id_producto','left');
        $this->db->join('cpp_sectores s','s.id_sector = e.id_sector','inner');
        $this->db->join('cpp_criticidades c','c.id_criticidad = e.id_criticidad','left');
        $this->db->where('e.habilitado',1);
        
        if ($filtros!="")
        {
            if ($filtros['id_criticidad']!="")
                $this->db->where('e.id_criticidad',$filtros['id_criticidad']);
            if ($filtros['id_estado']!="")
                $this->db->where('e.id_estado',$filtros['id_estado']);
            if ($filtros['f_ini']!="" && $filtros['f_fin']!="")
                $this->db->where("e.fecha_inicio BETWEEN '".$filtros['f_ini']."' AND '".$filtros['f_fin']."'", NULL, FALSE );
            elseif($filtros['f_ini']!="")
            {
                $this->db->where("e.fecha_inicio >=",$filtros['f_ini']);
            }
            if ($filtros['sectores']!="")
                $this->db->where_in('e.id_sector',$filtros['sectores']);
             if ($filtros['equipos']!="")
                $this->db->where_in('e.id_evento',$filtros['equipos']);
        }
		
        if ($sort!="")
        {
             switch ($sort)
            {
                case 'id':
                    $ordenar="e.id_evento";
                    break;
                case 'producto':
                    $ordenar="pp.producto";
                    break;
                case 'fecha_evento':
                    $ordenar="e.fecha_inicio";
                    break;
                case 'fecha_alta':
                    $ordenar="e.fecha_alta";
                    break;
                default:
                    $ordenar="e.fecha_inicio";
                    break;

            }
            $this->db->order_by($ordenar, $dir);
        }
        else
        {
            $this->db->order_by('e.fecha_inicio','desc');
        }
        
        $this->db->limit(1000,0); 
        $query = $this->db->get();
//         echo $this->db->last_query();
        $num = $query->num_rows();

        if ($num > 0)
        {
            $res = $query->result_array();
            return $res;
        }
        else
        {
            return 0;
        }
    }
    
    function sqlTxt($count,$sql)
    {
        $exploud=  explode('FROM', $sql);
        $exploud=  explode('ORDER BY', $exploud[1]);
        $newSql=  "SELECT count($count) as cantidad FROM ".$exploud[0];
            return $newSql;
    }
    function cantSql($sql)
    {
        $query =$this->db->query($sql);
        $res = $query->result();
        return $res[0]->cantidad;
    }
    
    public function insert($datos)
    {
        $this->db->trans_begin();

        $this->db->insert('cpp_eventos',$datos);
//        echo $this->db->last_query();
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
    function update($id, $datos)
    {
        $this->db->where("id_evento",$id);
        if ($this->db->update("cpp_eventos",$datos))
        {
//            echo $this->db->last_query();
            return true;
        }
        else
        {
//            echo $this->db->last_query();
            return false;
        }
    }
    function updateEstado($id, $id_estado)
    {
        $this->db->set("id_estado",$id_estado);
        $this->db->where("id_evento",$id);
        if ($this->db->update("cpp_eventos"))
            return true;
        else
            return false;
    }
    public function dameEvento($id_evento)
    {
        $this->db->select('e.*');
        $this->db->select("concat(p.nombre,' ',p.apellido) as usuario_alta",false);
        $this->db->select("concat(DATE_FORMAT(e.fecha_inicio,'%d/%m/%Y'),' - ',TIME_FORMAT(e.hora_inicio,'%k:%i')) as fh_inicio",false);
        $this->db->select("concat(DATE_FORMAT(e.fecha_fin,'%d/%m/%Y'),' - ',TIME_FORMAT(e.hora_fin,'%k:%i')) as fh_fin",false);
        $this->db->select('ee.estado');
//        $this->db->select('eq.equipo');
        $this->db->select('pp.producto');
        $this->db->select('s.sector');
        $this->db->select('c.criticidad');
        $this->db->from('cpp_eventos e');
        $this->db->join('sys_usuarios u','u.id_usuario = e.id_usuario_alta','inner');
        $this->db->join('grl_personas p','p.id_persona = u.id_persona','inner');
        $this->db->join('cpp_estados ee','ee.id_estado = e.id_estado','inner');
//        $this->db->join('cpp_equipos eq','eq.id_equipo = e.id_equipo','inner');
        $this->db->join('cpp_productos pp','pp.id_producto = e.id_producto','left');
        $this->db->join('cpp_sectores s','s.id_sector = e.id_sector','inner');
        $this->db->join('cpp_criticidades c','c.id_criticidad = e.id_criticidad','left');
        $this->db->where('e.habilitado',1);
        $this->db->where('e.id_evento',$id_evento);
        $query = $this->db->get();
//         echo $this->db->last_query();
        $res = $query->row_array();
        $num = $query->num_rows();
        if ($num > 0)
            return $res;
        else
            return 0;
    }
    public function verificaEventoCausaTarea($id_evento,$id_causa,$id_tarea,$id_estado)
    {
        $this->db->select('e.id_evento');
        $this->db->from('cpp_eventos e');
        $this->db->join('cpp_causas c','e.id_evento=c.id_evento','inner');
        $this->db->join('gr_tareas t','c.id_causa = t.id_herramienta','inner');
        $this->db->where('e.habilitado',1);
        $this->db->where('e.id_evento',$id_evento);
        $this->db->where('c.id_causa',$id_causa);
        $this->db->where('t.id_tarea',$id_tarea);
        $this->db->where('e.id_estado',$id_estado);
        $this->db->where('t.id_estado',9); //con tarea en estado aprobada
        $this->db->where('t.eficiencia',0); //con tarea en estado aprobada
        $query = $this->db->get();
//         echo $this->db->last_query();
        $num = $query->num_rows();
        if ($num > 0)
            return 1;
        else
            return 0;
    }
    public function dameEventoAEditar($id_evento)
    {
        $this->db->select('e.id_producto,e.id_sector,e.descripcion,e.fecha_inicio,e.fecha_fin,e.hora_inicio,e.hora_fin,e.id_producto,e.horas_perdidas');
        $this->db->from('cpp_eventos e');
        $this->db->where('e.habilitado',1);
        $this->db->where('e.id_evento',$id_evento);
        $query = $this->db->get();
//         echo $this->db->last_query();
        $res = $query->row_array();
        return $res;
    }
    
    public function listado_excel($start, $limit, $sort="", $dir="", $busqueda, $campos,  $filtros="")
    {
        $this->db->select('e.id_evento as id');
        $this->db->select("concat(p.nombre,' ',p.apellido) as usuario_alta",false); 
        $this->db->select("DATE_FORMAT(e.fecha_alta,'%d/%m/%Y') as fecha_alta", FALSE);
        $this->db->select("concat(DATE_FORMAT(e.fecha_inicio,'%d/%m/%Y'),' - ',TIME_FORMAT(e.hora_inicio,'%k:%i')) as fh_ini",false);
        $this->db->select("concat(DATE_FORMAT(e.fecha_fin,'%d/%m/%Y'),' - ',TIME_FORMAT(e.hora_fin,'%k:%i')) as fh_fin",false);
        $this->db->select('ee.estado');
        $this->db->select("DATE_FORMAT(e.fecha_inicio,'%d/%m/%Y') as fecha_evento", FALSE);
        $this->db->select('e.descripcion');
//        $this->db->select('eq.equipo');
         $this->db->select('(select GROUP_CONCAT(eq.tag," - ",eq.equipo SEPARATOR "; ") from cpp_equipos eq
                          INNER JOIN cpp_evento_equipos cee ON cee.id_equipo = eq.id_equipo
                          where cee.id_evento = e.id_evento
                          group by e.id_evento) as equipo',false);  
        $this->db->select('pp.producto');
        $this->db->select('s.sector');
        $this->db->select('IF(c.criticidad IS NULL, " -- ",c.criticidad)');
        $this->db->select('IF(e.horas_perdidas IS NULL," -- ",e.horas_perdidas) as hrs');
        $this->db->select("(select sum(monto) from cpp_evento_consecuencias where id_evento=e.id_evento)  as monto", FALSE);
        $this->db->select("(select sum(unidades_perdidas) from cpp_evento_consecuencias where id_evento=e.id_evento)  as unidades_perdidas", FALSE);
        $this->db->from('cpp_eventos e');
        $this->db->join('sys_usuarios u','u.id_usuario = e.id_usuario_alta','inner');
        $this->db->join('grl_personas p','p.id_persona = u.id_persona','inner');
        $this->db->join('cpp_estados ee','ee.id_estado = e.id_estado','inner');
//        $this->db->join('cpp_equipos eq','eq.id_equipo = e.id_equipo','inner');
        $this->db->join('cpp_productos pp','pp.id_producto = e.id_producto','left');
        $this->db->join('cpp_sectores s','s.id_sector = e.id_sector','inner');
        $this->db->join('cpp_criticidades c','c.id_criticidad = e.id_criticidad','left');
        $this->db->where('e.habilitado',1);
        
        if ($busqueda!="" && count($campos)>0)
        {
            foreach ($campos as &$campo)
            {
                switch ($campo)
                {
                    case 'descripcion':
                        $campo="e.".$campo;
                        break;
                    case 'usuario_alta':
                        $campo="concat(p.nombre,' ',p.apellido)";
                        break;
                }
            }
            unset($campo);
            
            if(count($campos)==1)
            {
                $this->db->where($campos[0]. " like","'%".$busqueda."%'",FALSE);
            }
            else
            {
                $n=0;
                foreach ($campos as $campo)
                {
                    if ($n==0)
                        {
                        $this->db->where("(".$campo." like","'%".$busqueda."%'",FALSE);
                        }
                    else
                    {
                        if ($n==count($campos)-1)
                        {
                            $this->db->or_where($campo ." like","'%".$busqueda."%')",FALSE);
                        }
                        else
                        {
                            $this->db->or_where($campo." like","'%".$busqueda."%'",FALSE);
                        }
                    }
                   $n++;     
                }
            }
        }
        
        if ($filtros!="")
        {
            if ($filtros[0]!="")
                $this->db->where('e.id_criticidad',$filtros[0]);
            if ($filtros[1]!="")
                $this->db->where('e.id_estado',$filtros[1]);
            
            if ($filtros['id_criticidad']!="")
                $this->db->where('e.id_criticidad',$filtros['id_criticidad']);
            if ($filtros['id_estado']!="")
                $this->db->where('e.id_estado',$filtros['id_estado']);
            if ($filtros['f_ini']!="" && $filtros['f_fin']!="")
                $this->db->where("e.fecha_inicio BETWEEN '".$filtros['f_ini']."' AND '".$filtros['f_fin']."'", NULL, FALSE );
            elseif($filtros['f_ini']!="")
            {
                $this->db->where("e.fecha_inicio >=",$filtros['f_ini']);
            }
            if ($filtros['sectores']!="")
                $this->db->where_in('e.id_sector',$filtros['sectores']);
            if ($filtros['equipos']!="")
                $this->db->where_in('e.id_evento',$filtros['equipos']);
            
        }
		
        if ($sort!="")
        {
             switch ($sort)
            {
                case 'id':
                    $ordenar="e.id_evento";
                    break;
                default:
                    $ordenar="e.id_evento";
                    break;

            }
            $this->db->order_by($ordenar, $dir);
        }
        else
        {
            $this->db->order_by('e.fecha_alta','desc');
        }
        
        $this->db->limit($limit,$start); 
        $query = $this->db->get();
//         echo $this->db->last_query();die();
        $num = $query->num_rows();
        $res = $query->result_array();

        if ($num > 0)
            return $res;
        else
            return 0;
    }
    
    function dameQTreas($id_evento)
    {
        $this->db->select("q_tareas");
        $this->db->from('cpp_eventos e');
        $this->db->where('e.id_evento',$id_evento);
        $query=$this->db->get();
        $row = $query->row();
        return $row->q_tareas;
    }
    
    public function controlBtnTarea ($id_evento,$id_usuario)
    {
        $this->db->select("if ((e.id_estado != 5),0, if($id_usuario IN (select si.id_usuario from cpp_investigadores si where si.id_evento = e.id_evento),1,0)) as btn_tarea",false);
        $this->db->from('cpp_eventos e');
        $this->db->where('e.id_evento',$id_evento);
        $query=$this->db->get();
        $row = $query->row();
        return $row->btn_tarea;
    }
    
    public function controlBtnEi ($id_evento,$estadosBotones)
    {
        $this->db->select(" case when e.id_estado IN (".$estadosBotones['btn_ei'].") then 1
                                     else 0 end as btn_ei",false);
        $this->db->from('cpp_eventos e');
        $this->db->where('e.id_evento',$id_evento);
        $query=$this->db->get();
        $row = $query->row();
        return $row->btn_ei;
    }
    public function controlBtnCalificar ($id_evento,$id_usuario)
    {
        $this->db->select("if (((e.id_estado = 1 or e.id_estado = 2) and e.id_usuario_alta = $id_usuario),1,0) as btn_calificar",false);
        $this->db->from('cpp_eventos e');
        $this->db->where('e.id_evento',$id_evento);
        $query=$this->db->get();
        $row = $query->row();
        return $row->btn_calificar;
    }
    public function controlBtnCausa ($id_evento,$id_usuario)
    {
        $this->db->select("if ((e.id_estado != 4 and e.id_estado != 5),0, if($id_usuario IN (select si.id_usuario from cpp_investigadores si where si.id_evento = e.id_evento),1,0)) as btn_causa",false);
        $this->db->from('cpp_eventos e');
        $this->db->where('e.id_evento',$id_evento);
        $query=$this->db->get();
        $row = $query->row();
        return $row->btn_causa;
    }
    public function controlBtnCrit ($id_evento,$id_usuario)
    {
        $this->db->select("if ((e.id_estado = 2 and e.id_usuario_alta = $id_usuario),1,0) as btn_crit",false);
        $this->db->from('cpp_eventos e');
        $this->db->where('e.id_evento',$id_evento);
        $query=$this->db->get();
        $row = $query->row();
        return $row->btn_crit;
    }
    public function controlBtnCerrar ($id_evento,$id_usuario)
    {
        $this->db->select("if (e.q_tareas = 0,0,if ((e.id_estado != 5  and e.id_usuario_alta != $id_usuario), 0,if((select count(st.id_tarea) from gr_tareas st where st.id_tipo_herramienta = 8 and st.id_herramienta IN (select ssc.id_causa from cpp_causas ssc where ssc.id_evento = e.id_evento) and st.id_estado IN (1,2,3,4,5,10))>0,0,1))) as btn_cerrar",false);
        $this->db->from('cpp_eventos e');
        $this->db->where('e.id_evento',$id_evento);
        $query=$this->db->get();
        $row = $query->row();
        return $row->btn_cerrar;
    }
    public function controlBtnCancelar ($id_evento,$estadosBotones)
    {
        $this->db->select(" case when e.id_estado IN (".$estadosBotones['btn_cancelar'].") then 1
                                     else 0 end as btn_cancelar",false);
        $this->db->from('cpp_eventos e');
        $this->db->where('e.id_evento',$id_evento);
        $query=$this->db->get();
        $row = $query->row();
        return $row->btn_cancelar;
    }
    public function controlBtnTn ($id_evento,$estadosBotones)
    {
        $this->db->select(" case when e.id_estado IN (".$estadosBotones['btn_tn'].") then 1
                                     else 0 end as btn_tn",false);
        $this->db->from('cpp_eventos e');
        $this->db->where('e.id_evento',$id_evento);
        $query=$this->db->get();
        $row = $query->row();
        return $row->btn_tn;
    }
    public function controlBtnMonto ($id_evento,$estadosBotones)
    {
        $this->db->select(" case when e.id_estado IN (".$estadosBotones['btn_monto'].") then 1
                                     else 0 end as btn_monto",false);
        $this->db->from('cpp_eventos e');
        $this->db->where('e.id_evento',$id_evento);
        $query=$this->db->get();
        $row = $query->row();
        return $row->btn_monto;
    }
    public function controlBtnEditCrit ($id_evento,$estadosBotones)
    {
        $this->db->select(" case when e.id_estado IN (".$estadosBotones['btn_edit_crit'].") then 1
                                     else 0 end as btn_edit_crit",false);
        $this->db->from('cpp_eventos e');
        $this->db->where('e.id_evento',$id_evento);
        $query=$this->db->get();
        $row = $query->row();
//                 echo $this->db->last_query();
        return $row->btn_edit_crit;
    }
    
    public function dameDatosCriticidadParaForm ($id_evento)
    {
        $this->db->select("e.id_criticidad as criticidadEventoRadios");
        $this->db->from('cpp_eventos e');
        $this->db->where('e.id_evento',$id_evento);
        $query=$this->db->get();
//        $row = $query->row();
        //         echo $this->db->last_query();
        $num = $query->num_rows();
        $res = $query->result_array();
        if ($num > 0)
        {
            return "{ 'success': true, 'msg': 'dada', 'data': ".json_encode($res[0])."}";
        }
        else
            return 0;
    }
    
    public function checkCriticidad($id_evento)
    {
        $this->db->select('id_evento');
        $this->db->from('cpp_eventos');
        $this->db->where('id_evento',$id_evento);
        $this->db->where('id_criticidad IS NULL');
        $query=$this->db->get();      
        $num = $query->num_rows();
//         echo $this->db->last_query();
        if ($num == 0)
            return true;
        else
            return false;
    }
    
    public function controlBtnEditEvento ($id_evento,$estadosBotones)
    {
        $this->db->select(" case when e.id_estado IN (".$estadosBotones['btn_edit_evento'].") then 1
                                     else 0 end as btn_edit_evento",false);
        $this->db->from('cpp_eventos e');
        $this->db->where('e.id_evento',$id_evento);
        $query=$this->db->get();
        $row = $query->row();
        //         echo $this->db->last_query();
        return $row->btn_edit_evento;
    }
    
    public function dameDatosEventoParaForm ($id_evento)
    {
        $this->db->select("e.id_sector as sectoresCombo");
        $this->db->select("IF(e.id_producto IS NULL, 0,1) as productosCheckField");
        $this->db->select("DATE_FORMAT(e.fecha_inicio,'%Y-%m-%d') as fechaInicioField");
        $this->db->select("DATE_FORMAT(e.fecha_fin,'%Y-%m-%d') as fechaFinField");
        $this->db->select("DATE_FORMAT(hora_inicio, '%H:%i') as hmInicioField");
        $this->db->select("DATE_FORMAT(hora_fin, '%H:%i') as hmFinField");
        $this->db->select("e.id_producto as productosCombo");
        $this->db->select("e.descripcion as descripcionEventoField");
        $this->db->select("(select GROUP_CONCAT(DISTINCT ee.id_equipo) from cpp_evento_equipos ee
                    where ee.id_evento = e.id_evento)as equiposSBS",false);
        $this->db->from('cpp_eventos e');
        $this->db->where('e.id_evento',$id_evento);
        $query=$this->db->get();
//        $row = $query->row();
//                 echo $this->db->last_query();
        $num = $query->num_rows();
        $res = $query->result_array();
        if ($num > 0)
        {
            return "{ 'success': true, 'msg': 'dada', 'data': ".json_encode($res[0])."}";
        }
        else
            return 0;
    }
    
    public function dameDescripcionEvento($id_evento) 
    {
        $this->db->select('e.descripcion');
        $this->db->from('cpp_eventos e');
        $this->db->where('e.id_evento',$id_evento);
        $query=$this->db->get();
        $row = $query->row();
        //         echo $this->db->last_query();
        return $row->descripcion;
    }
}
?>