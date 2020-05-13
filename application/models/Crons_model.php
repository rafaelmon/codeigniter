<?php
class Crons_model extends CI_Model
{
	
	
	public function insert($datos)
	{
		$this->db->trans_begin();
		
		$this->db->insert('sys_crons',$datos);
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
        public function verificaCronAlertMorosos()
        {
            $this->db->select('count(id_cron) as cant',false);
            $this->db->from('sys_crons');
            $this->db->where("cron",'bots/alert_morosos');
            $this->db->where("DATEDIFF( CURDATE( ) , fecha )=",0,false);
            $query = $this->db->get();
//            echo $this->db->last_query();
            $res = $query->result();
            return $res[0]->cant;
        }
        public function verificaCronAlertProxVto()
        {
            $this->db->select('count(id_cron) as cant',false);
            $this->db->from('sys_crons');
            $this->db->where("cron",'bots/alert_proxvto');
            $this->db->where("DATEDIFF( CURDATE( ) , fecha )=",0,false);
            $query = $this->db->get();
//            echo $this->db->last_query();
            $res = $query->result();
            return $res[0]->cant;
        }
	public function verificaCronDocPublicados()
        {
            $this->db->select('count(id_cron) as cant',false);
            $this->db->from('sys_crons');
            $this->db->where("cron",'bots/documentos_publicados');
            $this->db->where("DATEDIFF( CURDATE( ) , fecha )=",0,false);
            $query = $this->db->get();
//            echo $this->db->last_query();
            $res = $query->result();
            return $res[0]->cant;
        }
       
	
}	
?>