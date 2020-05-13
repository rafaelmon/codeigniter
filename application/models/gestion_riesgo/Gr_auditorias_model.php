<?php
class Gr_auditorias_model extends CI_Model
{
    public function listado($usuario, $start, $limit, $filtros, $busqueda, $campos,$sort="", $dir="")
    {
//             echo "<pre>". print_r($usuario,true)."<pre>";
            $this->db->select('a.id_auditoria,a.fecha,a.fecha_alta,a.q_usuarios,a.programada,a.realizada,a.id_usuario_alta');
            $this->db->select("concat(p.nombre,' ',p.apellido) as usuario_alta",false);
            $this->db->select("(select GROUP_CONCAT(DISTINCT concat(p.nombre,' ',p.apellido) SEPARATOR ' | ') from gr_auditores_auditorias au
                    inner join sys_usuarios u on u.id_usuario=au.id_usuario_auditor
                    inner join grl_personas p on p.id_persona=u.id_persona
                    where au.id_auditoria=a.id_auditoria)as auditores",false);
            $this->db->select("DATE_FORMAT(a.fecha,'%d/%m/%Y') as fecha", FALSE);
            $this->db->select("DATE_FORMAT(a.fecha_alta,'%d/%m/%Y') as fecha_alta", FALSE);
            $this->db->select("concat(e.abv,' | ',s.sector) as sector",false);
            $this->db->select("(select count(id_hallazgo) from gr_hallazgos_auditorias h where h.id_auditoria=a.id_auditoria) as q_hallazgos",false);
            $this->db->select("(select count(st.id_tarea) as cant from gr_auditorias sa
                                inner join gr_hallazgos_auditorias sha on sha.id_auditoria=sa.id_auditoria
                                inner join gr_tareas st on st.id_herramienta= sha.id_hallazgo and st.id_tipo_herramienta=5
                                where sa.id_auditoria=a.id_auditoria ) as q_tareas",false);
            $this->db->from('gr_auditorias a');
             $this->db->join('gr_usuarios gu','gu.id_usuario = a.id_usuario_alta','inner');
             $this->db->join('sys_usuarios u','u.id_usuario = gu.id_usuario','inner');
             $this->db->join('grl_personas p','p.id_persona = u.id_persona','left');
            $this->db->join('gr_sectores_auditar s','s.id_sector = a.id_sector','inner');
            $this->db->join('grl_empresas e','e.id_empresa = s.id_empresa','inner');
            
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
                        case 'fecha':
                            $campo="o.fecha";
                            break;
                        case 'sector':
                            $campo="concat(e.abv,' | ',s.sector)";
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
//                  $this->db->where_in('pta.id_area',$usuario['areas_inferiores'],FALSE);
                $this->db->where("s.id_sector",$usuario['usuario']['id_empresa']);

            }
        if ($sort!="")
        {
            if ($sort=='id_auditoria')
                $sort="a.".$sort;
            else
                $sort="a.".$sort;
            $this->db->order_by($sort, $dir);
        }
        else
            $this->db->order_by("a.id_auditoria", "desc"); 
        $this->db->limit($limit,$start); 

        $query = $this->db->get();
//        echo $this->db->last_query();
        $num = $this->cantSql('a.id_auditoria',$this->db->last_query());
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
            $this->db->select('count(id_auditoria) as cant',false);
            $this->db->from('gr_auditorias');
            $this->db->where("id_usuario_alta",$datos['id_usuario_alta']);
            $this->db->where("id_sector",$datos['id_sector']);
            $this->db->where("q_usuarios",$datos['q_usuarios']);
            $this->db->where("programada",$datos['programada']);
            $this->db->where("realizada",$datos['realizada']);
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
		
		$this->db->insert('gr_auditorias',$datos);
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
        public function insert_auditor($datos)
	{
		$this->db->trans_begin();
		
		$this->db->insert('gr_auditores_auditorias',$datos);
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
			return true;
		}
	}
         public function verificaAuditor($id_usuario,$id_auditoria)
        {
            $this->db->select('count(a.id_auditoria) as cant',false);
            $this->db->from('gr_auditorias a');
            $this->db->join('gr_auditores_auditorias aa','aa.id_auditoria = a.id_auditoria','inner');
            $this->db->where("a.id_auditoria",$id_auditoria);
            $this->db->where("a.id_usuario_alta",$id_usuario);
            $this->db->or_where("aa.id_usuario_auditor",$id_usuario);
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
         public function verificaAuditorAltaHabilitado($id_usuario,$id_auditoria)
        {
            $this->db->select('count(a.id_auditoria) as cant',false);
            $this->db->from('gr_auditorias a');
            $this->db->join('gr_roles r','r.id_usuario = a.id_usuario_alta','inner');
            $this->db->where("a.id_auditoria",$id_auditoria);
            $this->db->where("a.id_usuario_alta",$id_usuario);
            $this->db->where("r.habilitado",1);
            $query = $this->db->get();
//            echo $this->db->last_query();
            $res = $query->row();
            if ($res->cant > 0)
            {
                    return 1;
            }
            else
                    return 0;
        }
}	
?>