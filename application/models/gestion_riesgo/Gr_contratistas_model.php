<?php
class Gr_contratistas_model extends CI_Model
{
	public function contratistasCombo($limit,$start,$filtro,$idNot="")
	{
		
            $this->db->select('c.id_contratista');
            $this->db->select('c.contratista');
            $this->db->select('e.abv');
//            $this->db->select("concat('(',e.abv,') ',c.contratista) as contratista",false);
//            $this->db->select('pto.puesto');
            $this->db->from('gr_contratistas c');
            $this->db->join('grl_empresas e','e.id_empresa = c.id_empresa','inner');
//            $this->db->where("u.id_perfil",2);
            
            if($idNot!="")
                $this->db->where_not_in("c.id_contratista",$idNot);
            
            
            $this->db->where("c.habilitado",1);
            $this->db->where("(c.contratista like","'%".$filtro."%')",FALSE);
            
            
            $this->db->order_by("c.contratista", "asc");             
            $this->db->limit($limit=5,$start); 
                
            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $this->cantSql('c.id_contratista',$this->db->last_query());
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
}	
?>