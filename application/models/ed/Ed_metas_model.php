<?php
class Ed_metas_model extends CI_Model
{
    public function dameMetas($id_ed)
    {
        $this->db->select('m.id_meta,m.meta');
        $this->db->select("DATE_FORMAT(m.fecha_plazo,'%d/%m/%Y') as plazo", FALSE);
        $this->db->from('ed_metas m');
        $this->db->where('m.id_evaluacion',$id_ed);
        $query = $this->db->get();
//        echo $this->db->last_query();
        $num = $query->num_rows();
        $res = $query->result_array();
        if ($num >= 1)
        {

                return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
        }
        else
                return 0;
    }
    public function dameMetasPorEd($id_ed)
    {
        $this->db->select('m.id_meta,m.meta');
        $this->db->select("DATE_FORMAT(m.fecha_plazo,'%d/%m/%Y') as plazo", FALSE);
        $this->db->select("fecha_plazo");
        $this->db->from('ed_metas m');
        $this->db->where('m.id_evaluacion',$id_ed);
        $query = $this->db->get();
//        echo $this->db->last_query();
        $num = $query->num_rows();
        $res = $query->result_array();
        if ($num >= 1)
        {

               return $res;
        }
        else
                return 0;

    }
    
    public function dameMetasPdf($id_ed)
    {
        $this->db->select('m.id_meta,m.meta');
        $this->db->select("DATE_FORMAT(m.fecha_plazo,'%d/%m/%Y') as plazo", FALSE);
        $this->db->from('ed_metas m');
        $this->db->where('m.id_evaluacion',$id_ed);
        $query = $this->db->get();
//            echo $this->db->last_query();
        $num = $query->num_rows();
        $res = $query->result_array();
        if ($num >= 1)
            return $res;
        else
            return 0;
    }
    
    function update($id, $datos)
    {
        $this->db->where("id_meta",$id);
        if ($this->db->update("ed_metas",$datos))
            return true;
        else
            return false;
    }

    public function insert($datos)
    {
        $insert=$this->db->insert("ed_metas",$datos);
//        echo $this->db->last_query();
        if ($insert)
            return true;
        else
            return false;
    }
     public function delete($id_meta,$id_ed)
	{
            $this->db->where("id_meta",$id_meta);
            $this->db->where("id_evaluacion",$id_ed);
            $q = $this->db->delete("ed_metas");
            if ($q)
                return true;
            else
                return false;
	}
}

