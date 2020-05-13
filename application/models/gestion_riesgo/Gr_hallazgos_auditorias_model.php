<?php
class Gr_hallazgos_auditorias_model extends CI_Model
{
    public function listado_auditoria($id_auditoria, $start, $limit, $filtros, $busqueda, $campos,$sort="", $dir="")
    {
//             echo "<pre>". print_r($usuario,true)."<pre>";
            $this->db->select('h.id_hallazgo,h.hallazgo');
            $this->db->select("(select count(id_tarea) from gr_tareas t where t.id_herramienta=h.id_hallazgo and t.id_tipo_herramienta=5) as q_tareas",false);
//            $this->db->select("(select GROUP_CONCAT(DISTINCT concat(p.nombre,' ',p.apellido) SEPARATOR ' | ') from gr_auditores_auditorias au
//                    inner join sys_usuarios u on u.id_usuario=au.id_usuario_auditor
//                    inner join grl_personas p on p.id_persona=u.id_persona
//                    where au.id_auditoria=a.id_auditoria)as auditores",false);
//            $this->db->select("DATE_FORMAT(a.fecha_alta,'%d/%m/%Y') as fecha_alta", FALSE);
            $this->db->from('gr_hallazgos_auditorias h');
//            $this->db->join('grl_empresas e','e.id_empresa = s.id_empresa','inner');
            $this->db->where('h.id_auditoria',$id_auditoria);
            
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
//            if($usuario['gr']!=1)
//            {
////                $this->db->where_in('(pto2.id_area',$usuario['areas_inferiores'],FALSE);
////                  $this->db->where_in('pta.id_area',$usuario['areas_inferiores'],FALSE);
//                $this->db->where("s.id_sector",$usuario['usuario']['id_empresa']);
//
//            }
        if ($sort!="")
        {
            if ($sort=='h.id_hallazgo')
                $sort="h.".$sort;
            else
                $sort="h.".$sort;
            $this->db->order_by($sort, $dir);
        }
        else
            $this->db->order_by("h.id_hallazgo", "desc"); 
        $this->db->limit($limit,$start); 

        $query = $this->db->get();
//        echo $this->db->last_query();
        $num = $this->cantSql('h.id_hallazgo',$this->db->last_query());
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
            $this->db->select('count(id_hallazgo) as cant',false);
            $this->db->from('gr_hallazgos_auditorias');
            $this->db->where("id_auditoria",$datos['id_auditoria']);
            $this->db->where("hallazgo",$datos['hallazgo']);
            $this->db->where("id_usuario_alta",$datos['id_usuario_alta']);
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
		
		$this->db->insert('gr_hallazgos_auditorias',$datos);
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
        public function dameHallazgo($id)
        {
            $this->db->select('*');
            $this->db->from('gr_hallazgos_auditorias');
            $this->db->where("id_hallazgo",$id);
            $query = $this->db->get();
            $res = $query->result_array();
            $num=count($res);
            if ($num > 0)
            {
                    return $res[0];
            }
            else
                    return 0;
            
        }
}	
?>