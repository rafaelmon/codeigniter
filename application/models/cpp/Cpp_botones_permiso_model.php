<?php
class Cpp_botones_permiso_model extends CI_Model
{
    public function dameEstados($id_gcia,$boton)
    {
        $this->db->select('bp.id_estado');
        $this->db->from('cpp_botones_permiso bp');
        $this->db->where('bp.id_gcia',$id_gcia);
        switch ($boton) {
            case 'btn_monto':
                $this->db->where('bp.btn_monto',1);
                break;
            case 'btn_tn':
                $this->db->where('bp.btn_tn',1);
                break;
            case 'btn_ei':
                $this->db->where('bp.btn_ei',1);
                break;
            case 'btn_cancelar':
                $this->db->where('bp.btn_cancelar',1);
                break;
            case 'btn_crit':
                $this->db->where('bp.btn_crit',1);
                break;
            case 'btn_edit_crit':
                $this->db->where('bp.btn_edit_crit',1);
                break;
            case 'btn_edit_evento':
                $this->db->where('bp.btn_edit_evento',1);
                break;
            default:
                $this->db->where('1',0);
                break;
        }
        $query = $this->db->get();
//         echo $this->db->last_query();
        $num = $query->num_rows();
        $res = $query->result_array();
       if ($num > 0)
        {
           foreach  ($res as $key=>$value)
            {
                $estados[]   = $value['id_estado'];
            }
            return $estados;
        }
        else
        {
            return 0;
        }
    }
    
    public function damePermisos($id_gcia)
    {
        $this->db->select('bp.id_estado,bp.btn_monto,bp.btn_tn,bp.btn_ei,bp.btn_cancelar,bp.btn_crit,bp.btn_edit_crit,bp.btn_edit_evento');
        $this->db->from('cpp_botones_permiso bp');
        $this->db->where('bp.id_gcia',$id_gcia);
        $this->db->where('(bp.btn_monto',1,false);
        $this->db->or_where('bp.btn_ei',1);
        $this->db->or_where('bp.btn_cancelar',1);
        $this->db->or_where('bp.btn_crit',1);
        $this->db->or_where('bp.btn_edit_crit',1);
        $this->db->or_where('bp.btn_edit_evento',1);
        $this->db->or_where("'bp.btn_tn'", 1 .")",false);
//        switch ($boton) {
//            case 'btn_monto':
//                $this->db->where('bp.btn_monto',1);
//                break;
//            case 'btn_tn':
//                $this->db->where('bp.btn_tn',1);
//                break;
//            case 'btn_ei':
//                $this->db->where('bp.btn_ei',1);
//                break;
//            case 'btn_cancelar':
//                $this->db->where('bp.btn_cancelar',1);
//                break;
//            case 'btn_crit':
//                $this->db->where('bp.btn_crit',1);
//                break;
//            case 'btn_edit_crit':
//                $this->db->where('bp.btn_edit_crit',1);
//                break;
//            case 'btn_edit_evento':
//                $this->db->where('bp.btn_edit_evento',1);
//                break;
//            default:
//                $this->db->where('1',0);
//                break;
//        }
        $query = $this->db->get();
//         echo $this->db->last_query();
        $num = $query->num_rows();
        $res = $query->result_array();
       if ($num > 0)
        {
           foreach  ($res as $key=>$value)
            {
                $estados[]   = $value['id_estado'];
            }
//            return $estados;
            return $res;
        }
        else
        {
            return 0;
        }
    }
}	
?>