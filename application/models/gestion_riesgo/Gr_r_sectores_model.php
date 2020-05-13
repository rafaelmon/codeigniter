<?php
class Gr_r_sectores_model extends CI_Model
{
    public function dameComboSitiosPorEmpresa($id_th,$id_empresa)
    {
        $this->db->select('s.id_sitio,s.sitio');
        $this->db->from('gr_r_sectores rs');
        $this->db->join('gr_sitios s','s.id_sitio = rs.id_sitio','inner');
        $this->db->where('rs.id_th',$id_th);
        $this->db->where('rs.id_empresa',$id_empresa);
        $this->db->where('s.habilitado',1);
        $this->db->group_by('rs.id_sitio');
        $this->db->orderby('s.sitio');
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
    public function dameComboSectoresPorEmpresaPorSitio($id_th,$id_empresa,$id_sitio)
    {
        $this->db->select('s.id_sector,s.sector');
        $this->db->from('gr_r_sectores rs');
        $this->db->join('gr_sectores s','s.id_sector = rs.id_sector','inner');
        $this->db->where('rs.id_th',$id_th);
        $this->db->where('rs.id_empresa',$id_empresa);
        $this->db->where('rs.id_sitio',$id_sitio);
        $this->db->where('s.habilitado',1);
//        $this->db->group_by('rs.id_sitio');
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