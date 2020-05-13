<?php
class Gr_roles_model extends CI_Model
{
	public function checkIn($id_usuario)
	{
		$id_usuario=($id_usuario=="")?0:$id_usuario;
                $this->db->select('editor, revisor, aprobador, publicador'); 
		$this->db->from('gr_roles r'); 
                $this->db->join('sys_usuarios u','u.id_usuario = r.id_usuario','inner');
                $this->db->join('grl_personas pp','pp.id_persona = u.id_persona','inner');
		$this->db->where('r.habilitado',1);
		$this->db->where('r.id_usuario',$id_usuario);
		$this->db->where('pp.habilitado',1);
		$this->db->where('u.habilitado',1);
	//	$this->db->limit(1);
		$query = $this->db->get();
//                echo $this->db->last_query();
		if($query->num_rows() == 1)
		{
			$resultado = $query->result();
			$res['id_usuario'] = $id_usuario;
			$res['Editor'] = $resultado[0]->editor;
			$res['Revisor'] = $resultado[0]->revisor;
			$res['Aprobador'] = $resultado[0]->aprobador;
			$res['Publicador'] = $resultado[0]->publicador;
		}
		else
		{
			$res['id_usuario'] = $id_usuario;
			$res['Editor'] = 0;
			$res['Revisor'] = 0;
			$res['Aprobador'] = 0;
			$res['Publicador'] = 0;
		}
//                echo "<pre>".print_r($res,true)."</pre>";
		return $res;
	}
        public function dameAuditoresCombo($limit,$start,$filtro,$idNot="")
	{
		
            $this->db->select('r.id_usuario');
            $this->db->select('u.usuario');
            $this->db->select('concat(pp.nombre," ",pp.apellido) as nomape',false);
            $this->db->select('concat(e.abv," - ",pto.puesto) as puesto',false);
            $this->db->from('gr_roles r');
            $this->db->join('gr_usuarios gru','gru.id_usuario = r.id_usuario','inner');
            $this->db->join('sys_usuarios u','u.id_usuario = gru.id_usuario','inner');
            $this->db->join('grl_personas pp','pp.id_persona = u.id_persona','inner');
            $this->db->join('gr_puestos pto','pto.id_puesto = gru.id_puesto','inner');
            $this->db->join('gr_organigramas o','o.id_organigrama = pto.id_organigrama','inner');
            $this->db->join('grl_empresas e','e.id_empresa = o.id_empresa','inner');
            $this->db->where("r.auditor",1);
            if($idNot!="")
                $this->db->where("u.id_usuario !=",$idNot);
            
            $this->db->where("pp.habilitado",1);
            $this->db->where("u.habilitado",1);
            $this->db->where("(pp.nombre like","'%".$filtro."%'",FALSE);
            $this->db->or_where("pp.apellido like","'%".$filtro."%'",FALSE);
            $this->db->or_where("concat(pp.nombre,' ',pp.apellido) like","'%".$filtro."%')",FALSE);
            $this->db->order_by("pp.nombre", "asc"); 
            $this->db->order_by("pp.apellido", "asc"); 
            
            $this->db->limit($limit,$start); 
                
            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $this->cantSql('r.id_usuario',$this->db->last_query());
            $res = $query->result_array();
            if ($num > 0)
            {
                return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
            }
            else
                return '({"total":"0","rows":""})';
	}
	 function cantSql($count,$last_query)
        {
            $sql=  explode('FROM', $last_query);
            $sql=  explode('ORDER BY', $sql[1]);
            $sql=  "SELECT count($count) as cantidad FROM ".$sql[0];
            $query =$this->db->query($sql);
            $res = $query->result();
            return $res[0]->cantidad;
        }
//	public function check_disponible($modulo_id,$usuario_id)
//	{
//		$this->db->select('id'); 
//		$this->db->from('sys_permisos'); 
//		$this->db->where('id_usuario = '.$usuario_id);
//		$this->db->where('id_modulo = '.$modulo_id);
//		$query = $this->db->get();
//		if($query->num_rows() == 1)
//		{
//			return false;
//		}
//		else
//		{
//			return true;
//		}
//	}
}
?>