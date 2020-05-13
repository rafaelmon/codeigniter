<?php
class Permisos_model extends CI_Model
{
	public function checkIn($perfil_id,$modulo_id)
	{
		$perfil_id=($perfil_id=="")?0:$perfil_id;
		$modulo_id=($modulo_id=="")?0:$modulo_id;
                $this->db->select('listar, alta, baja, modificacion'); 
		$this->db->from('sys_permisos'); 
                $this->db->join('sys_perfiles','sys_perfiles.id_perfil=sys_permisos.id_perfil','inner');
		$this->db->where('sys_permisos.id_perfil',$perfil_id);
		$this->db->where('sys_permisos.id_modulo',$modulo_id);
		$this->db->where('sys_perfiles.habilitado', 1);
	//	$this->db->limit(1);
		$query = $this->db->get();
//                echo $this->db->last_query();
		if($query->num_rows() == 1)
		{
			$resultado = $query->result();
			$res['Listar'] = $resultado[0]->listar;
			$res['Alta'] = $resultado[0]->alta;
			$res['Baja'] = $resultado[0]->baja;
			$res['Modificacion'] = $resultado[0]->modificacion;
		}
		else
		{
			$res['Listar'] = 0;
			$res['Alta'] = 0;
			$res['Baja'] = 0;
			$res['Modificacion'] = 0;
		}
//                echo "<pre>".print_r($res,true)."</pre>";
		return $res;
	}
	
	public function check_disponible($modulo_id,$usuario_id)
	{
		$this->db->select('id'); 
		$this->db->from('sys_permisos'); 
		$this->db->where('id_usuario = '.$usuario_id);
		$this->db->where('id_modulo = '.$modulo_id);
		$query = $this->db->get();
		if($query->num_rows() == 1)
		{
			return false;
		}
		else
		{
			return true;
		}
	}
}
?>