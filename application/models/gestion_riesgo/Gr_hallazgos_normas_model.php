<?php
class Gr_hallazgos_normas_model extends CI_Model
{
        public function insert($datos)
	{
		$this->db->trans_begin();
		
		$this->db->insert('gr_hallazgos_normas',$datos);
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
}	
?>