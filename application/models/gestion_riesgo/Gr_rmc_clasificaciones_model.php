<?php
class Gr_rmc_clasificaciones_model extends CI_Model
{
    public function insert($datos)
	{
		$this->db->trans_begin();
		
		$this->db->insert('gr_rmc_clasificaciones',$datos);
		
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