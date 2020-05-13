<?php
class Vto_mef_model extends CI_Model
{
    public function dameEstadoSiguiente($id_estado_actual,$id_accion)
    {
        $this->db->select('vm.id_estado_sig');
        $this->db->from('vto_mef vm');
        $this->db->where('vm.id_estado_actual',$id_estado_actual);
        $this->db->where('vm.id_accion',$id_accion);
        $query = $this->db->get();
//        echo $this->db->last_query();
        $res = $query->row();
        $num = $query->num_rows();
        if ($num != 0)
        {
            return $res->id_estado_sig;
        }
        else
        {
            return 0;
        }
    }
}	
?>