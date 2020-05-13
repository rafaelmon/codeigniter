<?php
class Observaciones_model extends CI_Model
{
    
	public function dameObsPorDocumento($id, $start, $limit,$sort="", $dir="")
	{
		
            $this->db->select('o.id_obs,o.id_usuario,o.obs');
            $this->db->select('concat(p.nombre,", ",p.apellido) as persona',false);
            $this->db->select("DATE_FORMAT(o.fecha_alta,'%d/%m/%Y') as fecha", FALSE);
            $this->db->from('dms_observaciones o');
            $this->db->join('sys_usuarios u','u.id_usuario = o.id_usuario','inner');
            $this->db->join('grl_personas p','p.id_persona = u.id_persona','left');
            $this->db->where('o.id_documento',$id);
            $this->db->where('o.obs_wf',1);//solo los que pertenecen al proceso de WF
            
            if ($sort!="")
            {
                $sort="o.".$sort;
                $this->db->order_by($sort, $dir);
            }
            else
                $this->db->order_by("o.id_obs", "asc"); 
            $this->db->limit($limit,$start); 
                
            $query = $this->db->get();
            $num = $this->cantSql('o.id_obs',$this->db->last_query());
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
        
	public function insert($datos)
	{
            
            $this->db->trans_begin();
		
		$this->db->insert('dms_observaciones',$datos);
		
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
        
}	
?>