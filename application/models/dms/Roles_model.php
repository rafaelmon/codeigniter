<?php
class Roles_model extends CI_Model
{
    
	public function listado($start, $limit, $filtro, $campos,$sort="", $dir="")
	{
		
            $this->db->select('r.id_rol,r.rol,r.detalle,r.habilitado');
            $this->db->from('dms_roles r');
            $this->db->order_by("r.rol", "asc"); 
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
	
        
//        public function dameComboRoles()
//        {
//            $this->db->select("td.id_td,td.td");
//            $this->db->from("dms_tipos_documento td");
//            $this->db->where("td.habilitado",1);
//            $this->db->order_by("td.td", "asc");
//            $query = $this->db->get();
//            $num = $query->num_rows();
//            $res = $query->result_array();
//            if ($num > 0)
//            {
//                    return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
//            }
//            else
//                    return '({"total":"0","rows":""})';
//        }
        
        public function dameRol($id_rol)
	{
            $this->db->select('rol');
            $this->db->from('dms_roles');
            $this->db->where('id_rol',$id_rol); 
            $query = $this->db->get();
//            return $this->db->last_query();
//            $res = $query->result();
            $res = $query->row();
            $num=count($res);
            if ($num > 0)
                return $res->rol; 
            else
                return false; 
		
	}
	
	
}	
?>