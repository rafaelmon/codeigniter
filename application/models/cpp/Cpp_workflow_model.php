<?php
class Cpp_workflow_model extends CI_Model
{
    
    public function dameEstadoSiguiente($id_estado_actual,$id_accion)
    {
        $this->db->select('w.id_estado_siguiente');
        $this->db->from('cpp_workflow w');
        $this->db->where('w.id_estado_actual',$id_estado_actual);
        $this->db->where('w.id_accion',$id_accion);
        $query = $this->db->get();
//        echo $this->db->last_query();
        $res = $query->row();
        $num = $query->num_rows();
        if ($num > 0)
        {
            return $res->id_estado_siguiente;
        }
        else
            return 0;

    }
}	
?>