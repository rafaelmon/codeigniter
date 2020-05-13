<?php
class Ed_pm_model extends CI_Model
{   
        public function dameCompetenciasPlanPdf($id_evaluacion)
	{
		
            $this->db->select('distinct ec.id_competencia',false);
            $this->db->select('c.competencia');
            $this->db->from('ed_pm pm');
            $this->db->join('ed_ec ec','ec.id_ec = pm.id_ec');
            $this->db->join('ed_competencias c','c.id_competencia = ec.id_competencia');
            $this->db->where("ec.habilitado",1);
            $this->db->where("(ec.s_r1",1);
            $this->db->or_where("ec.s_r2",'1)',false);
            if($id_evaluacion != 0)
            {
                $this->db->where("ec.id_evaluacion",$id_evaluacion);
            }
            $this->db->order_by("ec.id_competencia", "asc");
            
            $query = $this->db->get();
//            echo $this->db->last_query();die();
            $num = $this->cantSql('ec.id_competencia',$this->db->last_query());
            $res = $query->result_array();
            
            if ($num > 0)
            {
                    return $res;
            }
            else
            {
                    return 0;
            }
	}
        
        public function dameSubcompetenciasPlanPdf($id_evaluacion=0,$id_competencia=0)
	{
		
            $this->db->select('ec.id_ec,ec.id_subcompetencia');
            $this->db->select('sc.subcompetencia');
            $this->db->select('pm.accion');
            $this->db->select("DATE_FORMAT(pm.fecha_plazo,'%d/%m/%Y') as plazo", FALSE);
            $this->db->from('ed_pm pm');
            $this->db->join('ed_ec ec','ec.id_ec = pm.id_ec');
            $this->db->join('ed_competencias c','c.id_competencia = ec.id_competencia');
            $this->db->join('ed_subcompetencias sc','ec.id_subcompetencia = sc.id_subcompetencia');
            $this->db->where("ec.habilitado",1);
            $this->db->where("(ec.s_r1",1);
            $this->db->or_where("ec.s_r2",'1)',false);
            if($id_evaluacion != 0)
            {
                $this->db->where("ec.id_evaluacion",$id_evaluacion);
            }
            if($id_competencia != 0)
            {
                $this->db->where("ec.id_competencia",$id_competencia);
            }
            $this->db->order_by("ec.id_subcompetencia", "asc");

            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $this->cantSql('ec.id_subcompetencia',$this->db->last_query());
            $res = $query->result_array();
            
            if ($num > 0)
            {
                    return $res;
            }
            else
            {
                    return 0;
            }
	}
        public function damePpmmPorEd($id_evaluacion=0)
	{
		
            $this->db->select('ec.id_ec,ec.id_subcompetencia');
            $this->db->select('c.competencia');
            $this->db->select('sc.subcompetencia');
            $this->db->select('pm.accion, pm.resp_tarea');
            $this->db->select("DATE_FORMAT(pm.fecha_plazo,'%d/%m/%Y') as plazo", FALSE);
            $this->db->select("fecha_plazo");
            $this->db->from('ed_pm pm');
            $this->db->join('ed_ec ec','ec.id_ec = pm.id_ec');
            $this->db->join('ed_competencias c','c.id_competencia = ec.id_competencia');
            $this->db->join('ed_subcompetencias sc','ec.id_subcompetencia = sc.id_subcompetencia');
            $this->db->where("ec.id_evaluacion",$id_evaluacion);
            $this->db->where("ec.habilitado",1);
            $this->db->where("(ec.s_r1",1);
            $this->db->or_where("ec.s_r2",'1)',false);
            $this->db->order_by("ec.id_subcompetencia", "asc");

            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $this->cantSql('ec.id_subcompetencia',$this->db->last_query());
            $res = $query->result_array();
            
            if ($num > 0)
                return $res;
            else
                return 0;
	}
        
        function cantSql($count,$last_query)
        {
            $sql=  explode('FROM', $last_query);
            $sql=  explode('ORDER BY', $sql[1]);
            $sql=  "SELECT count($count) as cantidad FROM ".$sql[0];
            $query =$this->db->query($sql);
            $res = $query->result();
//            echo $this->db->last_query();die();
            return $res[0]->cantidad;
        }
         public function insert($datos)
        {
            if ($this->db->insert("ed_pm",$datos))
                    return 1;
            else
                    return 0;
        }
        public function update_plan($id,$campo,$valor)
	{
		$this->db->set($campo, $valor);
		$this->db->where('id_ec', $id);
		if(!$this->db->update("ed_pm"))
			return false;
		else
			return true;
	}
        public function checkSiExiste($id_ec)
	{
            $this->db->select('pm.id_ec');
            $this->db->where('id_ec', $id_ec);
            $this->db->from('ed_pm pm');
            $query = $this->db->get();
            $res = $query->result();
            if($query->num_rows()> 0)
                    return true;
            else
                    return false;
	}
        
        public function dameResponsable($id_ec)
	{
            $this->db->select('pm.resp_tarea');
            $this->db->select("CASE
                              WHEN pm.resp_tarea=1 THEN 'Usuario'
                              WHEN pm.resp_tarea=2 THEN 'Supervisor'
                              END as responsable",false);
            $this->db->from('ed_pm pm');
            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $this->cantSql('pm.resp_tarea',$this->db->last_query());
            $res = $query->result_array();
            if ($num > 0)
            {
                return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
            }
            else
                return '({"total":"0","rows":""})';
        }
}
?>
