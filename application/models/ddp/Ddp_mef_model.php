<?php
class Ddp_mef_model extends CI_Model
{
        
         public function dameEstadoAccion($id_estado,$id_accion,$id_estado_top=1)
        {
            $this->db->select('*');
            $this->db->from('ddp_mef m');
            $this->db->where("m.id_estado",$id_estado);
            $this->db->where("m.id_accion",$id_accion);
            $this->db->where("m.id_estado_top",$id_estado_top);
            $query = $this->db->get();
//            echo $this->db->last_query();
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