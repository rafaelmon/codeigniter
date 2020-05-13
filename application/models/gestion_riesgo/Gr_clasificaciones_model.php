<?php
class Gr_clasificaciones_model extends CI_Model
{
    public function dameCombo()
    {
        $this->db->select('c.id_clasificacion,c.clasificacion');
        $this->db->from('gr_clasificaciones c');
        $this->db->orderby('c.clasificacion');
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