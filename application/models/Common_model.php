<?php
Class Common_model extends CI_Model
{
	
	public function login($username, $password)
	{
        // Esta funci�n recibe como par�metros el nombre de usuario y password
		$this -> db -> select('u.id_usuario, u.id_perfil,  u.email, u.usuario');
		$this -> db -> select('p.perfil as perfil_nomb');
		$this -> db -> select('pp.apellido, pp.nombre');
		$this -> db -> from('sys_usuarios u');
		$this -> db -> join('sys_perfiles p','p.id_perfil = u.id_perfil','inner');
		$this -> db -> join('grl_personas pp','pp.id_persona = u.id_persona','inner');
		$this -> db -> where('u.usuario = ' . "'" . $username . "'");
		$this -> db -> where('u.habilitado = 1');
		$this -> db -> where('u.password = ' . "'" . MD5($password) . "'");
		$this -> db -> limit(1);

		$query = $this -> db -> get();
//                echo $this->db->last_query();

		if($query -> num_rows() == 1)
		{
			return $query->result();
                        // Existen nombre de usuario y contraseña.
		}
		else
		{
			return false;
                        // No existe nombre de usuario o contraseña.
		}

	}
	
	public function ultimoAcceso($id)
	{
		$datos = array('ultimo_acceso' => date('Y-m-d H:i:s'));
		$this -> db -> update('sys_usuarios',$datos,'id_usuario = '.$id);
				
	}
}
?>