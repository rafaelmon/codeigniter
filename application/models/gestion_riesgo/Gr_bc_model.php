<?php
class Gr_bc_model extends CI_Model
{
    public function listado($usuario, $start, $limit, $filtros, $busqueda, $campos,$sort="", $dir="")
    {
//            echo"<pre>".print_r($usuario,true)."</pre>";
            $this->db->select('b.id_bc, b.id_usuario_alta,b.id_estado,b.detalle_rechazo_cancel');
            $this->db->select('b.descripcion as descr',false);
            $this->db->select("concat(p.nombre,' ',p.apellido) as usuario_alta",false);
            $this->db->select('concat(e.abv," - ",pto.puesto) as puesto1',false);
            $this->db->select("concat(p2.nombre,' ',p2.apellido) as usuario_inicio",false);
            $this->db->select('concat(e2.abv," - ",pto2.puesto) as puesto2',false);
            $this->db->select("DATE_FORMAT(b.fecha_alta,'%d/%m/%Y') as fecha_alta", FALSE);
            $this->db->select("DATE_FORMAT(b.fecha_set_gr,'%d/%m/%Y') as fecha_set_gr", FALSE);
            $this->db->select('b.alcance,b.status');
            $this->db->select('ROUND((b.status/b.alcance*100),0) as cump');
            $this->db->select("(select count(t.id_tarea) from gr_tareas t where t.id_herramienta=b.id_bc and t.id_tipo_herramienta=3) as tareas",false);
            $this->db->select(" CASE 
                                WHEN b.id_estado=1 THEN 'Pendiente'
                                WHEN b.id_estado=2 THEN 'En Curso'
                                WHEN b.id_estado=3 THEN 'Rechazada'
                                WHEN b.id_estado=4 THEN 'Cumplida'
                                WHEN b.id_estado=5 THEN 'Cancelada'
                                ELSE '?'
                            END as estado",FALSE);
            $this->db->from('gr_bc b');
            $this->db->join('gr_usuarios gu','gu.id_usuario = b.id_usuario_alta','inner');
            $this->db->join('gr_usuarios gu2','gu2.id_usuario = b.id_usuario_inicio','inner');
            $this->db->join('sys_usuarios u','u.id_usuario = gu.id_usuario','inner');
            $this->db->join('sys_usuarios u2','u2.id_usuario = gu2.id_usuario','left');
            $this->db->join('grl_personas p','p.id_persona = u.id_persona','left');
            $this->db->join('grl_personas p2','p2.id_persona = u2.id_persona','left');
            $this->db->join('gr_puestos pto','pto.id_puesto = gu.id_puesto','inner');
            $this->db->join('gr_puestos pto2','pto2.id_puesto = gu2.id_puesto','inner');
            $this->db->join('gr_organigramas o','o.id_organigrama = pto.id_organigrama','inner');
            $this->db->join('gr_organigramas o2','o2.id_organigrama = pto2.id_organigrama','inner');
            $this->db->join('grl_empresas e','e.id_empresa = o.id_empresa','inner');
            $this->db->join('grl_empresas e2','e2.id_empresa = o2.id_empresa','inner');
            
//             if ($filtros!="")
//            {
//                if (isset($filtros[0]) && $filtros[0]!="")
//                    $this->db->where('t.id_estado',$filtros[0]);
//                if (isset($filtros[1]) && $filtros[1]!="")
//                    $this->db->where('t.id_tipo_herramienta',$filtros[1]);
//            }
            if ($busqueda!="" && count($campos)>0)
            {
                foreach ($campos as &$campo)
                {
                    switch ($campo)
                    {
                        case 'usuario_alta':
                            $campo="concat(p.nombre,' ',p.apellido)";
                            break;
                        case 'usuario_inicio':
                            $campo="concat(p2.nombre,' ',p2.apellido)";
                            break;
//                        default :
//                            unset($campo);
//                            break;
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
            if($usuario['gr']!=1)
            {
//                $this->db->where_in('(pto2.id_area',$usuario['areas_inferiores'],FALSE);
//                  $this->db->where_in('pto.id_area',$usuario['areas_inferiores'],FALSE);
                $this->db->where("pto.id_area in(".$usuario['areas_inferiores'].")");
                $this->db->or_where("b.id_usuario_inicio",$usuario['usuario']['id_usuario']);

            }
        if ($sort!="")
        {
            if ($sort=='id_bc')
                $sort="b.".$sort;
            else
                $sort="b.".$sort;
            $this->db->order_by($sort, $dir);
        }
        else
            $this->db->order_by("b.id_bc", "desc"); 
        $this->db->limit($limit,$start); 

        $query = $this->db->get();
//        echo $this->db->last_query();
        $num = $this->cantSql('b.id_bc',$this->db->last_query());
        $res = $query->result_array();
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
            $this->db->select('count(id_bc) as cant',false);
            $this->db->from('gr_bc');
            $this->db->where("id_usuario_alta",$datos['id_usuario_alta']);
            $this->db->where("id_usuario_inicio",$datos['id_usuario_inicio']);
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
		
		$this->db->insert('gr_bc',$datos);
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
            $this->db->set('fecha_set_gr', 'now()',false);
            $this->db->where('id_bc', $id);
            $edit=$this->db->update('gr_bc', $datos);
//            echo $this->db->last_query();
            if(!$edit)
                    return false;
            else
                    return true;
	}
        public function cancelar($id,$datos)
	{
            $this->db->set('fecha_set_cancel', 'now()',false);
            $this->db->where('id_bc', $id);
            $edit=$this->db->update('gr_bc', $datos);
//            echo $this->db->last_query();
            if(!$edit)
                    return false;
            else
                    return true;
	}
        public function updateStatus($id,$datos)
	{
            $this->db->where('id_bc', $id);
            $edit=$this->db->update('gr_bc', $datos);
//            echo $this->db->last_query();
            if(!$edit)
                    return false;
            else
                    return true;
	}
        
        public function dameBc($id)
	{
            $this->db->select("b.id_bc,b.id_usuario_alta,b.descripcion,b.id_usuario_inicio,b.id_estado,b.id_usuario_gr");
            $this->db->select("b.detalle_rechazo_cancel as motivo",false);
            $this->db->select("concat(p.nombre,' ',p.apellido) as usuarioAlta",false);
            $this->db->select('u.email as mailUsuarioAlta',false);
            $this->db->select("concat(p2.nombre,' ',p2.apellido) as usuarioInicio",false);
            $this->db->select('u2.email as mailUsuarioInicio',false);
            $this->db->select("concat(p3.nombre,' ',p3.apellido) as usuarioAprobador",false);
            $this->db->select("DATE_FORMAT(b.fecha_alta,'%d/%m/%Y') as fecha_alta", FALSE);
            $this->db->select("DATE_FORMAT(b.fecha_set_gr,'%d/%m/%Y %h:%i') as fecha_set_gr", FALSE);
            $this->db->select("DATE_FORMAT((select DATE_ADD(b.fecha_set_gr, INTERVAL 10 DAY)),'%Y/%m/%d') as fecha_vto_1", FALSE);
            $this->db->select("DATE_FORMAT((select DATE_ADD(CURRENT_TIMESTAMP, INTERVAL 10 DAY)),'%Y/%m/%d') as fecha_vto_n", FALSE);
            $this->db->select(" CASE 
                                WHEN b.id_estado=1 THEN 'Pendiente'
                                WHEN b.id_estado=2 THEN 'En Curso'
                                WHEN b.id_estado=3 THEN 'Rechazada'
                                WHEN b.id_estado=4 THEN 'Terminada'
                                ELSE '?'
                            END as estado",FALSE);
            $this->db->from('gr_bc b');
            $this->db->join('gr_usuarios gu','gu.id_usuario = b.id_usuario_alta','inner');
            $this->db->join('gr_usuarios gu2','gu2.id_usuario = b.id_usuario_inicio','left');
            $this->db->join('gr_usuarios gu3','gu3.id_usuario = b.id_usuario_gr','left');
            $this->db->join('sys_usuarios u','u.id_usuario = gu.id_usuario','inner');
            $this->db->join('sys_usuarios u2','u2.id_usuario = gu2.id_usuario','left');
            $this->db->join('sys_usuarios u3','u3.id_usuario = gu3.id_usuario','left');
            $this->db->join('grl_personas p','p.id_persona = u.id_persona','left');
            $this->db->join('grl_personas p2','p2.id_persona = u2.id_persona','left');
            $this->db->join('grl_personas p3','p3.id_persona = u3.id_persona','left');
            $this->db->where("b.id_bc",$id);
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
        public function dameBBCC($estados)
	{
            $this->db->select("b.id_bc");
            $this->db->from('gr_bc b');
            $this->db->where_in("b.id_estado",$estados);
            $query = $this->db->get();
             $res = $query->result_array();
//            $res = $query->row();
            $num=count($res);
            if ($num > 0)
            {
                    return $res;
            }
            else
                    return 0;
		
	}
        public function dameTareasInicio($id)
	{
//            $this->db->select("b.id_bc,b.id_usuario_alta,b.descripcion,b.id_usuario_inicio,b.id_estado");
            $this->db->select("t.id_tarea,t.usuario_alta,t.usuario_responsable,t.usuario_relacionado,t.id_estado");
            $this->db->select("concat(p.nombre,' ',p.apellido) as usuarioBC",false);
//            $this->db->select('concat(p.nombre," ",p.apellido," <",u.email,">") as mailUsuarioAlta',false);
//            $this->db->select("concat(p2.nombre,' ',p2.apellido) as usuarioInicio",false);
//            $this->db->select('u2.email as mailUsuarioInicio',false);
//            $this->db->select("concat(p3.nombre,' ',p3.apellido) as usuarioAprobador",false);
//            $this->db->select("DATE_FORMAT(b.fecha_alta,'%d/%m/%Y') as fecha_alta", FALSE);
//            $this->db->select("DATE_FORMAT(b.fecha_set_gr,'%d/%m/%Y %h:%i') as fecha_set_gr", FALSE);
//            $this->db->select("DATE_FORMAT((select DATE_ADD(b.fecha_set_gr, INTERVAL 10 DAY)),'%Y/%m/%d') as fecha_vto_1", FALSE);
//            $this->db->select(" CASE 
//                                WHEN b.id_estado=1 THEN 'Pendiente'
//                                WHEN b.id_estado=2 THEN 'En Curso'
//                                WHEN b.id_estado=3 THEN 'Rechazada'
//                                WHEN b.id_estado=4 THEN 'Terminada'
//                                ELSE '?'
//                            END as estado",FALSE);
            $this->db->from('gr_bc b');
            $this->db->join('gr_tareas t','t.id_herramienta = b.id_bc','inner');
            $this->db->join('gr_usuarios gu','gu.id_usuario = t.usuario_relacionado','inner');
            $this->db->join('gr_usuarios gu2','gu2.id_usuario = b.id_usuario_inicio','left');
            $this->db->join('gr_usuarios gu3','gu3.id_usuario = b.id_usuario_gr','left');
            $this->db->join('sys_usuarios u','u.id_usuario = gu.id_usuario','inner');
            $this->db->join('sys_usuarios u2','u2.id_usuario = gu2.id_usuario','left');
            $this->db->join('sys_usuarios u3','u3.id_usuario = gu3.id_usuario','left');
            $this->db->join('grl_personas p','p.id_persona = u.id_persona','left');
            $this->db->join('grl_personas p2','p2.id_persona = u2.id_persona','left');
            $this->db->join('grl_personas p3','p3.id_persona = u3.id_persona','left');
            $this->db->where("b.id_bc",$id);
            $this->db->where("t.id_tipo_herramienta",3);
            $query = $this->db->get();
             $res = $query->result_array();
//            $res = $query->row();
            $num=count($res);
            if ($num > 0)
            {
                    return $res;
            }
            else
                    return 0;
		
	}
        public function calculaProfundidad($id_bc)
        {
            
        }
        public function dameUsuariosInferiores($id_bc,$id_usuario_superior)
	{
            $this->db->select('u.id_usuario');
             $this->db->select('pto.id_puesto');
             $this->db->select('concat(e.abv," - ",pto.puesto) as puesto',false);
            $this->db->select('concat(p.nombre," ",p.apellido) as nomape',false);
            $this->db->select('(select count(su.id_usuario) from gr_usuarios su inner join gr_puestos spto on spto.id_puesto=su.id_puesto where spto.id_puesto_superior=pto.id_puesto and su.habilitado=1) as q_dr',false);
            $this->db->select('t.id_tarea');
            $this->db->select('t.id_estado');
            $this->db->from('gr_usuarios u');
            
            $this->db->join('sys_usuarios uu','uu.id_usuario = u.id_usuario','inner');
            $this->db->join('grl_personas p','p.id_persona = uu.id_persona','inner');
            $this->db->join('gr_puestos pto','pto.id_puesto = u.id_puesto','inner');
            $this->db->join('gr_usuarios usup','usup.id_puesto = pto.id_puesto_superior','inner');
            $this->db->join('gr_organigramas o','o.id_organigrama = pto.id_organigrama','inner');
            $this->db->join('gr_tareas t',"t.usuario_relacionado=u.id_usuario and t.id_herramienta = $id_bc and t.id_tipo_herramienta=3",'left');
            $this->db->join('grl_empresas e','e.id_empresa = o.id_empresa','inner');
            
            $this->db->where("u.habilitado",1);
            $this->db->where("uu.habilitado",1);
            $this->db->where("p.habilitado",1);
            $this->db->where("usup.id_usuario",$id_usuario_superior);
            
            $this->db->order_by("u.id_usuario", "asc"); 
            
            $query = $this->db->get();
//            echo $this->db->last_query();
//             $num = $this->cantSql('u.id_usuario',$this->db->last_query());
            $res = $query->result_array();
            $num=count($res);
                if ($num > 0)
                {
                        return $res;
                }
                else
                        return 0;
		
	}
        
        
}	
?>