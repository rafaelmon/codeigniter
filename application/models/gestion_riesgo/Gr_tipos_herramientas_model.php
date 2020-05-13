<?php
class Gr_tipos_herramientas_model extends CI_Model
{
        public function dameCombo()
        {
            $this->db->select("id_tipo_herramienta,tipo_herramienta");
            $this->db->from("gr_tipos_herramientas");
            $this->db->where("habilitado",1);
            $this->db->order_by("tipo_herramienta", "asc");
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
        public function dameFiltroTareas()
        {
            $this->db->select("th.id_tipo_herramienta,th.tipo_herramienta");
            $this->db->from("gr_tipos_herramientas th");
            $this->db->join('gr_tareas t','t.id_tipo_herramienta = th.id_tipo_herramienta','inner');
            $this->db->group_by('t.id_tipo_herramienta');
            $this->db->order_by("tipo_herramienta", "asc");
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