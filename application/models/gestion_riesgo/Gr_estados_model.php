<?php
class Gr_estados_model extends CI_Model
{
    public function dameCombo()
    {
        $this->db->select('e.id_estado,estado');
        $this->db->from('gr_estados e');
//        if ($estados!="")
//            $this->db->where_in('e.id_estado',$estados);
        $this->db->where('e.habilitado',1);
            
        $this->db->orderby('e.estado');
        $query = $this->db->get();
        $res = $query->result_array();
        $num=count($res);
        if ($num > 0)
            {
                    return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
            }
            else
                    return '({"total":"0","rows":""})';

    }

        
}	
?>