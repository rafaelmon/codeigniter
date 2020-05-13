<?php
class Cpp_administradores_model extends CI_Model
{
    public function checkAdministrador($id_usuario)
    {
        $this->db->select('a.id_usuario');
        $this->db->from('cpp_administradores a');
        $this->db->where('a.id_usuario',$id_usuario);
        
        $query = $this->db->get();
//         echo $this->db->last_query();
        $row = $query->row();
        $num = $query->num_rows();
        if ($num > 0)
        {
            return 1;
        }
        else
            return 0;
    }
}	
?>