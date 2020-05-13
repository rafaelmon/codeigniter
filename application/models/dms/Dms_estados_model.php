<?php
class Dms_estados_model extends CI_Model
{
        public function dameComboFiltroWf()
        {
            $estados_wf=array(1,2,3,4,5);
            $this->db->select("ed.id_estado,ed.estado");
            $this->db->from("dms_estados_documento ed");
            $this->db->where("ed.habilitado",1);
            $this->db->where_in("ed.id_estado",$estados_wf);
            $this->db->order_by("ed.id_estado", "asc");
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
}	
?>