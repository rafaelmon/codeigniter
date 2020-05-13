<?php
class Gr_puestos_model extends CI_Model
{
    public function damePuestoSuperior($id)
    {
        $this->db->select('p.id_puesto_superior');
        $this->db->from('gr_puestos p');
        $this->db->where("p.id_puesto",$id);
        $query = $this->db->get();
        $res = $query->row();
        $num=count($res);
        if ($num > 0)
        {
                return $res->id_puesto_superior;
        }
        else
                return 0;

    }
        
}	
?>