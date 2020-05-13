<?php
class Usuarios_doc_model extends CI_Model
{
	
        
        public function insert($datos)
	{
		$this->db->trans_begin();
		
		$this->db->insert('dms_usuarios_doc',$datos);
//                echo $this->db->last_query();
		$last=$this->db->insert_id();
		
		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			return false;
		}
		else
		{
			$this->db->trans_commit();
			return true;
		}
	}
        public function checkUsuarioDoc($id_documento,$id_usuario, $id_rol="")
        {
            $this->db->select('ud.id_usuario');
            $this->db->from('dms_usuarios_doc ud');
            $this->db->join('sys_usuarios u','u.id_usuario = ud.id_usuario','inner');
            $this->db->join('grl_personas p','p.id_persona = u.id_persona','inner');
            $this->db->where("ud.id_documento",$id_documento);
            $this->db->where("ud.id_usuario",$id_usuario);
            if($id_rol!="")
                $this->db->where("ud.id_rol",$id_rol);
            $this->db->where("ud.habilitado",1);
            $this->db->where("u.habilitado",1);
            $this->db->where("p.habilitado",1);
            $query = $this->db->get();
//            echo $this->db->last_query();
		if($query->num_rows() >= 1)
		{
			return true;
		}
		else
		{
			return false;
		}
        }
        public function dameDestinatariosMailing($id_documento, $id_rol)
        {
            $this->db->select('u.email');
            $this->db->from('dms_usuarios_doc ud');
            $this->db->join('sys_usuarios u','u.id_usuario = ud.id_usuario','inner');
            $this->db->join('grl_personas p','p.id_persona = u.id_persona','inner');
            $this->db->where("ud.id_documento",$id_documento);
            $this->db->where("ud.id_rol",$id_rol);
            $this->db->where("ud.habilitado",1);
            $this->db->where("u.habilitado",1);
            $this->db->where("p.habilitado",1);
            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $query->num_rows();
            $res = $query->result_array();
            $destinatarios=array();
            foreach ($res as $email)
                $destinatarios[]=$email['email'];
                
            if ($num > 0)
            {
                    return $destinatarios;
            }
            else
                    return 0;
        }
        public function dameUsuariosDoc($id_documento)
        {
            $this->db->select('*');
            $this->db->from('dms_usuarios_doc ud');
            $this->db->where("ud.id_documento",$id_documento);
            $this->db->where("ud.habilitado",1);
            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $query->num_rows();
            $res = $query->result_array();
//            $destinatarios=array();
//            foreach ($res as $email)
//                $destinatarios[]=$email['email'];
                
            if ($num > 0)
            {
                    return $res;
            }
            else
                    return 0;
        }
        public function dameEmailParticipantes($id_documento)
        {
            $this->db->select('u.email');
            $this->db->from('dms_usuarios_doc ud');
            $this->db->join('sys_usuarios u','u.id_usuario = ud.id_usuario','inner');
            $this->db->join('grl_personas p','p.id_persona = u.id_persona','inner');
            $this->db->where("ud.id_documento",$id_documento);
            $this->db->where("ud.habilitado",1);
            $this->db->where("u.habilitado",1);
            $this->db->where("p.habilitado",1);
            $this->db->where("u.mailing",1);
            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $query->num_rows();
            $res = $query->result_array();
            $destinatarios=array();
            foreach ($res as $email)
                $destinatarios[]=$email['email'];
                
            if ($num > 0)
            {
                    return $destinatarios;
            }
            else
                    return 0;
        }
        public function dameRevisores($id_documento)
        {
            $this->db->select('ud.id_usuario');
            $this->db->from('dms_usuarios_doc ud');
            $this->db->where("ud.id_documento",$id_documento);
            $this->db->where("ud.id_rol",2);
            $this->db->where("ud.habilitado",1);
            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $query->num_rows();
            $res = $query->result_array();
            $revisores=array();
            foreach ($res as $revisor)
                $revisores[]=$revisor['id_usuario'];
                
            if ($num > 0)
            {
                    return $revisores;
            }
            else
                    return 0;
        }
        public function dameNombreRevisores($id_documento)
        {
            $this->db->select('concat(u.nombre,", ",u.apellido) as revisor');
            $this->db->from('dms_usuarios_doc ud');
            $this->db->join('sys_usuarios u','u.id_usuario = ud.id_usuario','inner');
            $this->db->join('grl_personas p','p.id_persona = u.id_persona','inner');
            $this->db->where("ud.id_documento",$id_documento);
            $this->db->where("ud.id_rol",2);
            $this->db->where("ud.habilitado",1);
            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $query->num_rows();
            $res = $query->result_array();
            $revisores=array();
            foreach ($res as $revisor)
                $revisores[]=$revisor['revisor'];
                
            if ($num > 0)
            {
                    return $revisores;
            }
            else
                    return 0;
        }
        public function deshabilitar($datos)
	{
                $update['habilitado']=0;
		$this->db->where('id_usuario', $datos['id_usuario']);
		$this->db->where('id_documento', $datos['id_documento']);
		$this->db->where('id_rol', $datos['id_rol']);
                $deshabilitar=$this->db->update('dms_usuarios_doc', $update);
//                echo $this->db->last_query();
		if(!$deshabilitar)
			return false;
		else
			return true;
	}
        public function desactivar($datos)
	{
                $update['activo']=0;
		$this->db->where('id_usuario', $datos['id_usuario']);
		$this->db->where('id_documento', $datos['id_documento']);
		$this->db->where('id_rol', $datos['id_rol']);
                $desactivar=$this->db->update('dms_usuarios_doc', $update);
//                echo $this->db->last_query();
		if(!$desactivar)
			return false;
		else
			return true;
	}
        public function activar($id_documento)
	{
                $update['activo']=1;
                $roles=array(2,3);
		$this->db->where('id_documento', $id_documento);
		$this->db->where('habilitado', 1);
		$this->db->where_in('id_rol', $roles);
		if(!$this->db->update('dms_usuarios_doc', $update))
			return false;
		else
			return true;
	}
        public function deshabilitarPorRol($datos)
	{
                $update['habilitado']=0;
		$this->db->where('id_documento', $datos['id_documento']);
		$this->db->where_in('id_rol', $datos['id_rol']);
		if(!$this->db->update('dms_usuarios_doc', $update))
			return false;
		else
			return true;
	}
        
}	
?>