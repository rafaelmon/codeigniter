<?php
class Sys_permiso_acciones_model extends CI_Model
{
    public function damePermisoAcciones($id_modulo,$id_usuario,$id_accion = 0)
    {
        $this->db->select('spa.id_accion'); 
        $this->db->from('sys_permiso_acciones spa'); 
        $this->db->join('sys_acciones sa','sa.id_accion=spa.id_accion','inner');
        $this->db->where('spa.id_usuario',$id_usuario);
        $this->db->where('sa.id_modulo',$id_modulo);
        $this->db->where('spa.habilitado', 1);
        $this->db->where('sa.habilitado', 1);
        if($id_accion != 0)
            $this->db->where('spa.id_accion', $id_accion);
            
//	$this->db->limit(1);
        $query = $this->db->get();
//        echo $this->db->last_query();
        $num = $query->num_rows();
        $res = $query->result_array();
        if ($num > 0)
        {
            if($id_accion != 0)
                return $res[0];
            else
                return $res;
        }
        else
            return 0;
    }
}
?>