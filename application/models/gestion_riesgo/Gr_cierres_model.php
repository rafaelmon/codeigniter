<?php
class Gr_cierres_model extends CI_Model
{
    
        
        public function dameCierre($id_cierre)
	{
            $this->db->select('*');
            $this->db->from('gr_cierres c');
            $this->db->where("c.id_cierre",$id_cierre);
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
        public function dameCierrePorTarea($id_tarea)
	{
            $this->db->select('*');
            $this->db->from('gr_cierres c');
            $this->db->where("c.id_tarea",$id_tarea);
            $this->db->order_by('c.id_cierre', 'desc');
            $this->db->limit(1,0); 
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
        public function verificaCierrePorTarea($id_tarea)
	{
            $this->db->select('c.id_cierre');
            $this->db->from('gr_cierres c');
            $this->db->where("c.id_tarea",$id_tarea);
            $query = $this->db->get();
             $res = $query->result_array();
//            $res = $query->row();
            $num=count($res);
            if ($num > 0)
            {
                    return 1;
            }
            else
                    return 0;
		
	}
        public function dameCierrePorTareaParaForm($id_tarea)
	{
            $this->db->select('c.texto as textoCerrarTareaTM');
            $this->db->select('if(c.continua=1,1,2) as continue_cap');
            $this->db->from('gr_cierres c');
            $this->db->where("c.id_tarea",$id_tarea);
            $query = $this->db->get();
             $res = $query->result_array();
//            $res = $query->row();
            $num=count($res);
            if ($num > 0)
            {
                 return "{ 'success': true, 'msg': 'ok', 'data': ".json_encode($res[0])."}";
            }
            else
                return 0;
		
	}
        
        public function insert($datos)
        {
            $this->db->trans_begin();
            $this->db->insert('gr_cierres',$datos);
            $insert_id = $this->db->insert_id();    
//            echo $this->db->last_query();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE)
            {
                    $this->db->trans_rollback();
                    return false;
            }
            else
            {
                    $this->db->trans_commit();
                    return $insert_id;
            }
        }
        public function edit($id,$datos)
	{
            $this->db->where('id_cierre', $id);
            if(!$this->db->update('gr_cierres', $datos))
                    return false;
            else
                    return true;
	}
        
        
        
}	
?>