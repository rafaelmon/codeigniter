<?php
class Ed_periodos_model extends CI_Model
{
        public function damePeriodoPorAnioSemestre($anio,$semestre)
	{
            $this->db->select('id_periodo');
            $this->db->from('ed_periodos');
            $this->db->where('anio',$anio);
            $this->db->where('semestre',$semestre);
            $query = $this->db->get();
            $res = $query->result();
//            echo $this->db->last_query();
            $num = $query->num_rows();
            if ($num==0)
            {
                    return 0;
            }
            else
                return $res[0]->id_periodo;
	}
        public function insert($datos)
	{
            if ($this->db->insert("ed_periodos",$datos))
            {
                $insert_id = $this->db->insert_id();
                return $insert_id;
            }
            else
                return false;
	}
}	
?>