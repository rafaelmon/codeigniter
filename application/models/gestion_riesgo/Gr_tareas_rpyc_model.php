<?php
class Gr_tareas_rpyc_model extends CI_Model
{
        public function insert($datos)
	{
		$this->db->trans_begin();
		
		$this->db->insert('gr_tareas_rpyc',$datos);
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
        public function dameTarea($id_tarea,$id_rpyc)
	{
//            $this->db->select('*');
            $this->db->select(" CASE 
                                WHEN tr.opcion=1 THEN concat('Usuario:',p.nombre,' ',p.apellido) 
                                WHEN tr.opcion=2 THEN concat('Operario:',tr.operario) 
                                WHEN tr.opcion=3 THEN concat('Contratista:',co.contratista) 
                                ELSE '?'
                            END as detector",FALSE);
            $this->db->from('gr_tareas_rpyc tr');
             $this->db->join('sys_usuarios u','u.id_usuario = tr.id_usuario','left');
            $this->db->join('grl_personas p','p.id_persona = u.id_persona','left');
            $this->db->join('gr_contratistas co','co.id_contratista = tr.id_contratista','left');
            $this->db->where("tr.id_tarea",$id_tarea);
            $this->db->where("tr.id_rpyc",$id_rpyc);
            $query = $this->db->get();
             $res = $query->result_array();
//            $res = $query->row();
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