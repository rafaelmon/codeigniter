<?php
class Ddp_dimensiones_model extends CI_Model
{
	public function listado_top($usuario,$sort="", $dir="")
	{
		
            $this->db->select('d.id_dimension,d.dimension,d.abv,d.css_bc,d.orden,d.habilitado');
            $this->db->select("(select count(distinct(so.id_objetivo)) 
                    from ddp_objetivos so
                    inner join ddp_tops st on st.id_top=so.id_top 
                        where st.id_top=".$usuario['id_top']."
                        and so.id_dimension=d.id_dimension
                    ) as q_obj",false);
            
            $this->db->select("(select sum(so.peso) 
                    from ddp_objetivos so 
                    inner join ddp_tops st on st.id_top=so.id_top 
                        where st.id_top=".$usuario['id_top']."
                        and so.id_dimension=d.id_dimension
                    ) as s_pesos",false);
            $this->db->select("(select sum(so.real1) 
                    from ddp_objetivos so 
                    inner join ddp_tops st on st.id_top=so.id_top 
                        where st.id_top=".$usuario['id_top']."
                        and so.id_dimension=d.id_dimension
                    ) as s_real1",false);
            $this->db->select("(select sum(((so.peso*so.real1)/100)) 
                    from ddp_objetivos so 
                    inner join ddp_tops st on st.id_top=so.id_top 
                        where st.id_top=".$usuario['id_top']."
                        and so.id_dimension=d.id_dimension
                    ) as s_pesoreal",false);
            $this->db->from('ddp_dimensiones d');
            $this->db->where('d.habilitado',1);
//            $this->db->join('grl_empresas e','e.id_empresa = g.id_empresa','inner');
            if ($sort !="" && $dir!="")
                $this->db->order_by($sort, $dir);
            else
                $this->db->order_by("d.orden", "asc");
            $this->db->limit(25,0); 
            $query = $this->db->get();
            $num = $query->num_rows();
//            echo $this->db->last_query();die();
            $res = $query->result_array();
            if ($num > 0)
            {
                return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
            }
            else
                return '({"total":"0","rows":""})';
	}
	
//        function cantSql($count,$last_query)
//        {
//            $sql=  explode('FROM', $last_query);
//            $sql=  explode('ORDER BY', $sql[1]);
//            $sql=  "SELECT count($count) as cantidad FROM ".$sql[0];
//            $query =$this->db->query($sql);
//            $res = $query->result();
//            return $res[0]->cantidad;
//        }
        
        
        public function dameComboDimensiones($limit="",$start="",$query="")
        {
            $this->db->select("d.id_dimension");
            $this->db->select("concat(d.dimension,' (',d.abv,')') as dimension",false);
            $this->db->from("ddp_dimensiones d");
            $this->db->where("d.habilitado",1);
            $this->db->where("d.id_dimension !=",6);
            
            if($query!="")
            {
                $this->db->where_like("d.abv",$query);
                $this->db->or_where_like("d.dimension",$query);
            }
            if($limit!="")
                 $this->db->limit($limit,$start); 
            
            $this->db->order_by("d.dimension", "asc");
            $query = $this->db->get();
            $num = $query->num_rows();
            $res = $query->result_array();
            if ($num > 0)
            {
                    return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
            }
            else
                    return '({"total":"0","rows":""})';
        }
        public function dameArrayIdsDim()
        {
            $this->db->select("d.id_dimension,d.dimension");
            $this->db->from("ddp_dimensiones d");
            $this->db->where("d.habilitado",1);
            $this->db->order_by("d.id_dimension", "asc");
            $query = $this->db->get();
            $num = $query->num_rows();
            $res = $query->result_array();
            if ($num > 0)
            {
                return $res;
            }
            else
                return 0;
            
        }
    
    public function dame_q_obj_dimension ($id_dimension="")
    {
        $this->db->select('d.q_obj');
        $this->db->from('ddp_dimensiones d');
        $this->db->where("d.id_dimension",$id_dimension);
        $query = $this->db->get();
//        echo $this->db->last_query();
        $res = $query->result_array();
        $num=count($res);
        if ($num == 1)
            return $res[0]['q_obj'];
        else
            return 0;
        
    }
    public function dameTotalObj ()
    {
        $this->db->select('sum(d.q_obj) as q_obj');
        $this->db->from('ddp_dimensiones d');
        $this->db->where("d.habilitado",1);
        $query = $this->db->get();
//        echo $this->db->last_query();
        $res = $query->result_array();
        $num=count($res);
        if ($num == 1)
            return $res[0]['q_obj'];
        else
            return 0;
        
    }
}	

?>