<?php
class Ed_fortalezas_model extends CI_Model
{
    public function dameFortalezas($id_ed)
        {
            $this->db->select('f.id_fortaleza,f.fortaleza');
            $this->db->from('ed_fortalezas f');
            $this->db->where('f.id_evaluacion',$id_ed);
            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $query->num_rows();
            $res = $query->result_array();
            if ($num >= 1)
            {
                
                    return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
            }
            else
                    return 0;
              
        }
    
    public function dameFortalezasPdf($id_ed)
    {
        $this->db->select('f.id_fortaleza,f.fortaleza');
        $this->db->from('ed_fortalezas f');
        $this->db->where('f.id_evaluacion',$id_ed);
        $query = $this->db->get();
//            echo $this->db->last_query();
        $num = $query->num_rows();
        $res = $query->result_array();
        if ($num >= 1)
        {

                return $res;
        }
        else
                return 0;

    }
    
    function update($id, $datos)
    {
        $this->db->where("id_fortaleza",$id);
        if ($this->db->update("ed_fortalezas",$datos))
            return true;
        else
            return false;
    }

    public function insert($datos)
    {
        if ($this->db->insert("ed_fortalezas",$datos))
            return true;
        else
            return false;
    }
     public function delete($id_fortaleza,$id_ed)
	{
            $this->db->where("id_fortaleza",$id_fortaleza);
            $this->db->where("id_evaluacion",$id_ed);
            $q = $this->db->delete("ed_fortalezas");
            if ($q)
                return true;
            else
                return false;
	}
}

