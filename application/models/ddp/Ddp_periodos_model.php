<?php
class Ddp_periodos_model extends CI_Model
{
        
        public function dameComboPeriodos()
        {
            $this->db->select("p.id_periodo,p.periodo");
            $this->db->from("ddp_periodos p");
//            $this->db->where("p.activo",1);
            $this->db->order_by("p.periodo", "asc");
            $query = $this->db->get();
            $num = $query->num_rows();
            $res = $query->result_array();
            if ($num > 0)
            {
                    return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
            }
            else
                    return '({"total":"0","rows":""})';
        }
        public function damePeriodoActivo()
        {
            $this->db->select("p.id_periodo,p.periodo");
            $this->db->from("ddp_periodos p");
            $this->db->where("p.activo",1);
            $this->db->order_by("p.periodo", "asc");
            $query = $this->db->get();
            $num = $query->num_rows();
            $res = $query->result_array();
            if ($num > 0)
            {
                   return $res[0];
        }
        else
                return -1;
        }
        public function damePeriodo($id)
        {
            $this->db->select("p.id_periodo,p.periodo,p.activo");
            $this->db->from("ddp_periodos p");
            $this->db->where("p.id_periodo",$id);
            $this->db->order_by("p.periodo", "asc");
            $query = $this->db->get();
            $num = $query->num_rows();
            $res = $query->result_array();
            if ($num > 0)
            {
                   return $res[0];
        }
        else
                return -1;
        }
        public function checkPeriodo($id)
	{
		$this->db->select('id_periodo');
		$this->db->from('ddp_periodos');
                $this->db->where('id_periodo',$id);
		$query=$this->db->get();
		if($query->num_rows()==0)
                    return false;
		else
                    return true;
	}
}	
?>