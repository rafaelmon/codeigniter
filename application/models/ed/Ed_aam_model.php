<?php
class Ed_aam_model extends CI_Model
{
    public function dameAam($id_ed)
        {
            $this->db->select('a.id_aam,a.aam');
            $this->db->from('ed_aam a');
            $this->db->where('a.id_evaluacion',$id_ed);
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
    public function dameAamPdf($id_ed)
    {
        $this->db->select('a.id_aam,a.aam');
        $this->db->from('ed_aam a');
        $this->db->where('a.id_evaluacion',$id_ed);
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
        $this->db->where("id_aam",$id);
        if ($this->db->update("ed_aam",$datos))
            return true;
        else
            return false;
    }

    public function insert($datos)
    {
        if ($this->db->insert("ed_aam",$datos))
            return true;
        else
            return false;
    }
     public function delete($id_aam,$id_ed)
	{
            $this->db->where("id_aam",$id_aam);
            $this->db->where("id_evaluacion",$id_ed);
            $q = $this->db->delete("ed_aam");
            if ($q)
                return true;
            else
                return false;
	}
}

