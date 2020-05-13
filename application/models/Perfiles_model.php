<?php
class Perfiles_model extends CI_Model
{
	public function listado($start="",$limit="")
	{
		$this->db->select("sys_perfiles.id_perfil, sys_perfiles.perfil, sys_perfiles.detalle, sys_perfiles.habilitado"); 
//		$this->db->select(""); 
		$this->db->from("sys_perfiles");
//                $this->db->join("","");
		$this->db->order_by("perfil asc");
		$this->db->limit($limit,$start);
		$query = $this->db->get();
		$res = $query->result_array();
		$cant = $this->cantPerfiles();
		if (count($res)>0)
		{
			return '({"total":"'.$cant.'","rows":'.json_encode($res).'})';
		}
		else
			return '({"total":"0","rows":""})';
	}
	
	public function cantPerfiles()
	{
		$this->db->select("count(id_perfil) as cantidad"); 
		$this->db->from("sys_perfiles"); 
		$query = $this->db->get();
		$res = $query->result();
		return $res[0]->cantidad;
	}
	
	public function update($id, $datos)
	{
		$this->db->where("id_perfil",$id);
		if ($this->db->update("sys_perfiles",$datos))
			return true;
		else
			return false;
	}
	
	public function checkRelacion($id)
	{
		$this->db->select("id_usuario");
		$this->db->from("sys_usuarios");
		$this->db->where("id_perfil",$id);
		$query = $this->db->get();
		if ($query->num_rows() == 0)
		{
			return true;
		}
		else
			return false;
	}
	
	public function delete($id)
	{
		$this->db->where("id_perfil",$id);
		$q = $this->db->delete("sys_perfiles");
		if ($q)
			return true;
		else
			return false;
	}
	
	public function insert($datos)
	{
		if ($this->db->insert("sys_perfiles",$datos))
			return true;
		else
			return false;
	}
	
	public function listadoPermisos($perfil_id)
	{
		$this->db->select("p.id_permiso,p.listar,p.alta,p.modificacion,p.baja,p.id_modulo,p.id_perfil,
		                  mb.modulo, pf.perfil");
		$this->db->from("sys_permisos p");
		$this->db->join('sys_modulos_backend mb', 'mb.id_modulo=p.id_modulo', 'inner');
		$this->db->join('sys_perfiles pf', 'pf.id_perfil=p.id_perfil', 'inner');
		$this->db->where('p.id_perfil', $perfil_id);
		$this->db->order_by("mb.modulo", "asc");
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
	
	public function deletePermiso($id)
	{
		$q=$this->db->delete('sys_permisos',array('id_permiso'=>$id));
		if ($q)
			return true;
		else
			return false;
	}
	
	public function listadoModulos()
	{		
		$this->db->select("id_modulo, modulo");
		$this->db->from("sys_modulos_backend");
		$this->db->where('hijos',0);
		$this->db->order_by("padre_id asc, modulo asc");
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
	public function listadoModulosXPerfil($id_perfil)
	{		
		
                $ids="";    
                $this->db->select('m.id_modulo');
		$this->db->from("sys_modulos_backend m");
                $this->db->join('sys_permisos pf', 'pf.id_modulo=m.id_modulo', 'inner');
		$this->db->where('pf.id_perfil',$id_perfil);
		$query = $this->db->get();
		$num = $query->num_rows();
//                 echo $this->db->last_query();
		$res = $query->result();
                if ($num > 0)
		{
                    foreach ($res as $row)
                    {
                        $ids[]=$row->id_modulo;
                    }
                }
//                 echo "<pre>".print_r($ids,false)."</pre>";
                $query->free_result();
                
                $this->db->select("m.id_modulo, m.modulo");
		$this->db->from("sys_modulos_backend m");
		$this->db->where('hijos',0);
                if ($ids!="")
                    $this->db->where_not_in('m.id_modulo',$ids);
		$this->db->order_by("m.padre_id asc, m.modulo asc");
		$query = $this->db->get();
//                 echo $this->db->last_query();
		$num = $query->num_rows();
		$res = $query->result_array();
		if ($num > 0)
		{
			return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
		}
		else
			return '({"total":"0","rows":"'.$ids.'"})';
	}
	
	public function modulosExcluidos($perfil_id)
	{
		$this->db->select("mb.id_modulo");
		$this->db->from("sys_modulos_backend mb"); 
		$this->db->join("sys_perfiles p","p.id_modulo = mb.id_modulo","inner"); 
		$this->db->where("p.id_perfil",$perfil_id);
		$query = $this->db->get();
		$res = $query->result();
		return $res;
	}
	
	public function insertPermiso($datos)
	{
		if ($this->db->insert("sys_permisos",$datos))
			return true;
		else
			return false;
	}
	
}
?>