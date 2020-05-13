<?php
class Gr_normas_model extends CI_Model
{
	
        public function dameNormasCombo($limit,$start,$filtro,$idNot="")
	{
		
            $this->db->select('np.id_norma_punto,np.punto,np.detalle');
            $this->db->select('n.norma');
            $this->db->select('concat(n.norma," - ",np.punto,") ",np.detalle) as normadetalle',false);
            $this->db->from('gr_normas_puntos np');
            $this->db->join('gr_normas n','n.id_norma = np.id_norma','inner');
//            if($idNot!="")
//                $this->db->where("u.id_usuario !=",$idNot);
             $this->db->where("n.norma like","'%".$filtro."%'",FALSE);
             $this->db->or_where("n.norma like","'%".$filtro."%'",FALSE);
             $this->db->or_where("np.detalle like","'%".$filtro."%'",FALSE);
             
            $this->db->order_by("n.norma", "asc"); 
            $this->db->order_by("np.punto", "asc"); 
            
            $this->db->limit($limit,$start); 
                
            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $this->cantSql('np.id_norma_punto',$this->db->last_query());
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