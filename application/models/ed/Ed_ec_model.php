<?php
class Ed_ec_model extends CI_Model
{   
        public function listadoEvaluacion($start, $limit, $busqueda, $campos, $sort, $dir,$id_evaluacion)
	{
		
            $this->db->select('ec.id_ec,ec.id_evaluacion,ec.id_competencia,ec.id_subcompetencia');
            $this->db->select('ec.u_r1,ec.u_r2,ec.u_r3,ec.u_r4,ec.s_r1,ec.s_r2,ec.s_r3,ec.s_r4');
            $this->db->select('c.competencia');
            $this->db->select('sc.subcompetencia');
            $this->db->from('ed_ec ec');
            $this->db->join('ed_competencias c','ec.id_competencia = c.id_competencia');
            $this->db->join('ed_subcompetencias sc','ec.id_subcompetencia = sc.id_subcompetencia');
            $this->db->where("ec.habilitado",1);
            if($id_evaluacion != 0)
            {
                $this->db->where("ec.id_evaluacion",$id_evaluacion);
            }
//            if ($busqueda!="" && count($campos)>0)
//            {
//                foreach ($campos as &$campo)
//                {
//                    switch($campo)
//                    {
//                        case 'empleado':
//                            $campo = "concat(pe.nombre,', ',pe.apellido)";
//                            break;
//                        case 'id_evaluacion':
//                            $campo = "e.".$campo;
//                            break;
//                        case 'periodo':
//                            $campo = "p.".$campo;
//                            break;
//                    }
//
//                }
//                 unset($campo);
//                //si viene un solo campo de busqueda
//                if(count($campos)==1)
//                {
//                    if ($campos[0] == "e.id_evaluacion")
//                        $this->db->where($campos[0]." =",$busqueda,FALSE);
//                    else
//                        $this->db->where($campos[0]. " like","'%".$busqueda."%'",FALSE);
//                }
//                else
//                {
//                    $n=0;
//                    foreach ($campos as $campo)
//                    {
//                        if ($n==0)
//                            {
//                            $this->db->where("(".$campo." like","'%".$busqueda."%'",FALSE);
//                            }
//                        else
//                        {
//                            if ($n==count($campos)-1)
//                            {
//                                $this->db->or_where($campo ." like","'%".$busqueda."%')",FALSE);
//                            }
//                            else
//                            {
//                                $this->db->or_where($campo." like","'%".$busqueda."%'",FALSE);
//                            }
//                        }
//                       $n++;     
//                    }
//                }
//            }
            $this->db->order_by("ec.id_ec", "asc"); 
            $this->db->limit($limit,$start); 

            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $this->cantSql('ec.id_ec',$this->db->last_query());
            $res = $query->result_array();
            
            if ($num > 0)
            {
                    return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
            }
            else
            {
                    return '({"total":"0","rows":""})';
            }
//            echo $this->db->last_query();
	}
        public function dameCompetencias($id_evaluacion,$tipo)
	{
		
            $this->db->select('ec.id_ec,ec.id_evaluacion,ec.id_competencia,ec.id_subcompetencia');
            $this->db->select('ec.u_r1,ec.u_r2,ec.u_r3,ec.u_r4');
            $this->db->select('ec.s_r1,ec.s_r2,ec.s_r3,ec.s_r4');
            $this->db->select('ec.s_rc as valor');
            $this->db->select('c.competencia');
            $this->db->select('sc.subcompetencia');
            $this->db->select(" CASE 
                                WHEN ec.s_r1=1 THEN 25
                                WHEN ec.s_r2=1 THEN 50
                                WHEN ec.s_r3=1 THEN 100
                                WHEN ec.s_r4=1 THEN 120
                                ELSE 0
                            END as st_cump",FALSE);
            $this->db->from('ed_ec ec');
            $this->db->join('ed_competencias c','ec.id_competencia = c.id_competencia');
            $this->db->join('ed_subcompetencias sc','ec.id_subcompetencia = sc.id_subcompetencia');
            $this->db->where("ec.habilitado",1);
            $this->db->where("c.tipo",$tipo);
            if($id_evaluacion != 0)
            {
                $this->db->where("ec.id_evaluacion",$id_evaluacion);
            }
            $this->db->order_by("ec.id_ec", "asc"); 

            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $this->cantSql('ec.id_ec',$this->db->last_query());
            $res = $query->result_array();
            $total_cumplimiento=0;
            
            foreach ($res as $key)
            {
                $total_cumplimiento+=$key['st_cump'];
            }
            
            
            if ($num > 0)
            {
                    return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
            }
            else
            {
                    return '({"total":"0","rows":""})';
            }
//            echo $this->db->last_query();
	}
        public function dameCantidadCompetencias($id_evaluacion)
	{
            $this->db->select("COUNT(id_ec) q_comp",FALSE);
            $this->db->from('ed_ec ec');
            $this->db->join('ed_competencias c','ec.id_competencia = c.id_competencia');
            $this->db->where("ec.habilitado",1);
            $this->db->where("ec.id_evaluacion",$id_evaluacion);
            $this->db->where("c.tipo",1);
            $query = $this->db->get();
//            echo $this->db->last_query();
            $res = $query->row();
            return $res->q_comp;
	}
        public function dameValorCumplimiento($id_evaluacion)
	{
            $this->db->select("SUM( CASE 
                                WHEN ec.s_r1=1 THEN 25
                                WHEN ec.s_r2=1 THEN 50
                                WHEN ec.s_r3=1 THEN 100
                                WHEN ec.s_r4=1 THEN 120
                                ELSE 0
                            END) t_cump",FALSE);
            $this->db->from('ed_ec ec');
             $this->db->join('ed_competencias c','ec.id_competencia = c.id_competencia');
            $this->db->where("ec.habilitado",1);
            $this->db->where("ec.id_evaluacion",$id_evaluacion);
            $this->db->where("c.tipo",1);
            $query = $this->db->get();
//            echo $this->db->last_query();
            $res = $query->row();
            return $res->t_cump;
	}
        public function dameCompetenciasParaPdf($id_evaluacion)
	{
            $this->db->select('distinct ec.id_competencia',false);
            $this->db->select('c.competencia,c.tipo');
            $this->db->from('ed_ec ec');
            $this->db->join('ed_competencias c','c.id_competencia = ec.id_competencia');
//            $this->db->where("ec.habilitado",1);
            $this->db->where("ec.id_evaluacion",$id_evaluacion);
            $this->db->order_by("ec.id_ec", "asc"); 
            $query = $this->db->get();
            $num = $this->cantSql('ec.id_ec',$this->db->last_query());
            $res = $query->result_array();
            if ($num > 0)
                    return $res;
            else
                    return 0;
	}
        public function dameSubCompetenciasParaPdf($id_evaluacion,$id_competencia)
	{
            $this->db->select('distinct ec.id_subcompetencia',false);
            $this->db->select('sc.subcompetencia,sc.obligatoria');
            $this->db->select(" CASE 
                                WHEN concat(ec.u_r1,ec.u_r2,ec.u_r3,ec.u_r4)=1000 THEN 'Inferior a lo esperado'
                                WHEN concat(ec.u_r1,ec.u_r2,ec.u_r3,ec.u_r4)=0100 THEN 'Necesita Mejorar'
                                WHEN concat(ec.u_r1,ec.u_r2,ec.u_r3,ec.u_r4)=0010 THEN 'Bueno'
                                WHEN concat(ec.u_r1,ec.u_r2,ec.u_r3,ec.u_r4)=0001 THEN 'Destacado'
                                WHEN concat(ec.u_r1,ec.u_r2,ec.u_r3,ec.u_r4)=0000 THEN 'No Responde'
                                ELSE 'Error!'
                            END as valor_u",FALSE);
            $this->db->select(" CASE 
                                WHEN concat(ec.s_r1,ec.s_r2,ec.s_r3,ec.s_r4)=1000 THEN 'Inferior a lo esperado'
                                WHEN concat(ec.s_r1,ec.s_r2,ec.s_r3,ec.s_r4)=0100 THEN 'Necesita Mejorar'
                                WHEN concat(ec.s_r1,ec.s_r2,ec.s_r3,ec.s_r4)=0010 THEN 'Bueno'
                                WHEN concat(ec.s_r1,ec.s_r2,ec.s_r3,ec.s_r4)=0001 THEN 'Destacado'
                                WHEN concat(ec.s_r1,ec.s_r2,ec.s_r3,ec.s_r4)=0000 THEN 'No Responde'
                                ELSE 'Error!'
                            END as valor_s",FALSE);
            $this->db->select('ec.s_rc as valor_sc');
            $this->db->from('ed_ec ec');
//            $this->db->join('ed_competencias c','c.id_competencia = ec.id_competencia');
            $this->db->join('ed_subcompetencias sc','sc.id_subcompetencia = ec.id_subcompetencia');
//            $this->db->where("ec.habilitado",1);
            $this->db->where("ec.id_evaluacion",$id_evaluacion);
            $this->db->where("ec.id_competencia",$id_competencia);
            $this->db->order_by("ec.id_ec", "asc"); 
            $query = $this->db->get();
            $num = $this->cantSql('ec.id_ec',$this->db->last_query());
            $res = $query->result_array();
            if ($num > 0)
                    return $res;
            else
                    return 0;
	}
        public function dameSubCompetenciasParaPdf_enblanco_supervisor($id_evaluacion,$id_competencia)
	{
            $this->db->select('distinct ec.id_subcompetencia',false);
            $this->db->select('sc.subcompetencia,sc.obligatoria');
            $this->db->select(" CASE 
                                WHEN concat(ec.u_r1,ec.u_r2,ec.u_r3,ec.u_r4)=1000 THEN 'Inferior a lo esperado'
                                WHEN concat(ec.u_r1,ec.u_r2,ec.u_r3,ec.u_r4)=0100 THEN 'Necesita Mejorar'
                                WHEN concat(ec.u_r1,ec.u_r2,ec.u_r3,ec.u_r4)=0010 THEN 'Bueno'
                                WHEN concat(ec.u_r1,ec.u_r2,ec.u_r3,ec.u_r4)=0001 THEN 'Destacado'
                                WHEN concat(ec.u_r1,ec.u_r2,ec.u_r3,ec.u_r4)=0000 THEN 'No Responde'
                                ELSE 'Error!'
                            END as valor_u",FALSE);
            $this->db->select("'-.-' as valor_s",false);
            $this->db->from('ed_ec ec');
//            $this->db->join('ed_competencias c','c.id_competencia = ec.id_competencia');
            $this->db->join('ed_subcompetencias sc','sc.id_subcompetencia = ec.id_subcompetencia');
//            $this->db->where("ec.habilitado",1);
            $this->db->where("ec.id_evaluacion",$id_evaluacion);
            $this->db->where("ec.id_competencia",$id_competencia);
            $this->db->order_by("ec.id_ec", "asc"); 
            $query = $this->db->get();
            $num = $this->cantSql('ec.id_ec',$this->db->last_query());
            $res = $query->result_array();
            if ($num > 0)
                    return $res;
            else
                    return 0;
	}
        public function dameCompetenciasPlan($id_evaluacion=0)
	{
		
            $this->db->select('ec.id_ec,ec.id_evaluacion,ec.id_competencia,ec.id_subcompetencia');
            $this->db->select('ec.u_r1,ec.u_r2,ec.u_r3,ec.u_r4');
            $this->db->select('ec.s_r1,ec.s_r2,ec.s_r3,ec.s_r4');
            $this->db->select('c.competencia');
            $this->db->select('sc.subcompetencia');
            $this->db->select('pm.accion,pm.resp_tarea');
            $this->db->select("CASE
                              WHEN pm.resp_tarea=1 THEN 'Usuario'
                              WHEN pm.resp_tarea=2 THEN 'Supervisor'
                              END as responsable",false);
            $this->db->select("DATE_FORMAT(pm.fecha_plazo,'%Y-%m-%d') as plazo", FALSE);
            $this->db->from('ed_ec ec');
            $this->db->join('ed_competencias c','ec.id_competencia = c.id_competencia');
            $this->db->join('ed_subcompetencias sc','ec.id_subcompetencia = sc.id_subcompetencia');
            $this->db->join('ed_pm pm','pm.id_ec = ec.id_ec','left');
            $this->db->where("ec.habilitado",1);
            $this->db->where("(ec.s_r1",1,false);
            $this->db->or_where("ec.s_r2",'1)',false);
            $this->db->where("ec.habilitado",1);
            if($id_evaluacion != 0)
            {
                $this->db->where("ec.id_evaluacion",$id_evaluacion);
            }
            $this->db->order_by("ec.id_ec", "asc"); 

            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $this->cantSql('ec.id_ec',$this->db->last_query());
            $res = $query->result_array();
            
            if ($num > 0)
            {
                    return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
            }
            else
            {
                    return '({"total":"0","rows":""})';
            }
//            echo $this->db->last_query();
	}
        
        public function dameCompetenciasPlanPdf($id_evaluacion)
	{
            $this->db->select('distinct ec.id_competencia',false);
//            $this->db->select('ec.id_competencia');
            $this->db->select('c.competencia');
            $this->db->from('ed_ec ec');
            $this->db->join('ed_competencias c','ec.id_competencia = c.id_competencia');
            $this->db->where("ec.habilitado",1);
            $this->db->where("(ec.s_r1",1);
            $this->db->or_where("ec.s_r2",'1)',false);
            if($id_evaluacion != 0)
            {
                $this->db->where("ec.id_evaluacion",$id_evaluacion);
            }
            $this->db->order_by("ec.id_competencia", "asc");
//            $this->db->group_by("ec.id_competencia");
            
            $query = $this->db->get();
//            echo $this->db->last_query();die();
            $num = $this->cantSql('ec.id_ec',$this->db->last_query());
            $res = $query->result_array();
            
            if ($num > 0)
            {
                    return $res;
            }
            else
            {
                    return 0;
            }
//            echo $this->db->last_query();die();
	}
        public function dameSubcompetenciasPlanPdf($id_evaluacion=0,$id_competencia=0)
	{
		
            $this->db->select('ec.id_subcompetencia');
            $this->db->select('sc.subcompetencia');
            $this->db->from('ed_ec ec');
            $this->db->join('ed_competencias c','ec.id_competencia = c.id_competencia');
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
            $num = $this->cantSql('ec.id_ec',$this->db->last_query());
            $res = $query->result_array();
            
            if ($num > 0)
            {
                    return $res;
            }
            else
            {
                    return 0;
            }
//            echo $this->db->last_query();die();
	}
        
        function cantSql($count,$last_query)
        {
            $sql=  explode('FROM', $last_query);
            $sql=  explode('ORDER BY', $sql[1]);
            $sql=  "SELECT count($count) as cantidad FROM ".$sql[0];
            $query =$this->db->query($sql);
//            echo $this->db->last_query();
            $res = $query->result();
            return $res[0]->cantidad;
        }
        public function insert($datos)
        {
            if ($this->db->insert("ed_ec",$datos))
                    return 1;
            else
                    return 0;
        }
        public function check_respuesta($id,$datos)
	{
		$this->db->where('id_ec', $id);
		if(!$this->db->update("ed_ec", $datos))
			return false;
		else
			return true;
	}
        public function update_comp_cuant($id,$valor)
	{
		$this->db->set('s_rc', $valor);
		$this->db->where('id_ec', $id);
		if(!$this->db->update("ed_ec"))
			return false;
		else
			return true;
	}
}
?>
