<?php
class Gr_sectores_model extends CI_Model
{
    public function dameCombo($id_empresa)
    {
        $this->db->select('s.id_sector,sector');
        $this->db->from('gr_sectores s');
        $this->db->where('s.habilitado',1);
        $this->db->where('s.id_empresa',$id_empresa);
        $this->db->orderby('s.sector');
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