<?php
class Ddp_acciones_model extends CI_Model
{
        
        public function dameAccion($id_accion)
        {
            $this->db->select('a.accion');
            $this->db->from('ddp_acciones a');
            $this->db->where("e.id_accion",$id_accion);
            $this->db->where("e.habilitado",1);
            $query = $this->db->get();
    //        echo $this->db->last_query();
            $res = $query->result_array();
            $num=count($res);
            if ($num == 1)
            {
                    return $res[0];
            }
            else
                    return 0;

        }
        public function dameComboEstados()
        {
//            $this->db->select("p.id_periodo,p.periodo");
//            $this->db->from("ddp_periodos p");
//            $this->db->where("p.activo",1);
//            $this->db->order_by("p.periodo", "asc");
//            $query = $this->db->get();
//            $num = $query->num_rows();
//            $res = $query->result_array();
//            if ($num > 0)
//            {
//                    return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
//            }
//            else
//                    return '({"total":"0","rows":""})';
        }
}	
?>