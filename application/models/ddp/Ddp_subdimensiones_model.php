<?php
class Ddp_subdimensiones_model extends CI_Model
{
	 public function damePricipios()
        {
            $this->db->select('*');
            $this->db->from('ddp_subdimensiones');
            $this->db->where("id_dimension",3);
            $query = $this->db->get();
    //        echo $this->db->last_query();
            $res = $query->result_array();
            $num=count($res);
            if ($num > 0)
            {
                    return $res;
            }
            else
                    return 0;

        }
}	
?>