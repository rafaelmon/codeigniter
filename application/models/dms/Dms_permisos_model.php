<?php
class Dms_permisos_model extends CI_Model
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