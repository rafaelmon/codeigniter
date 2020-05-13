<?php
class Gr_sectores_rpyc_model extends CI_Model
{
    public function dameCombo($query,$id_empresa)
    {
        $this->db->select('s.id_sector,sector');
        $this->db->from('gr_sectores_rpyc s');
        $this->db->where('s.habilitado',1);
        $this->db->where('s.id_empresa',$id_empresa);
        $this->db->like('s.sector',$query);
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
    public function insert($datos)
    {
        $this->db->trans_begin();

        $this->db->insert('gr_sectores_rpyc',$datos);
        $last=$this->db->insert_id();

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
                $this->db->trans_rollback();
                return false;
        }
        else
        {
                $this->db->trans_commit();
                return true;
        }
    }
        
}	

?>