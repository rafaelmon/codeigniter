<?php
class Modulos_model extends CI_Model
{
	public function listado($start="",$limit="",$filtro=0,$criterioBusqueda,$camposBusqueda)
	{
		
            $this->db->select('mb.id_modulo,mb.modulo,mb.accion,mb.icono,mb.hijos,mb.orden,mb.menu,mb.habilitado');
            $this->db->select('(select modulo from sys_modulos_backend where id_modulo = mb.padre_id) as padre',false);
            $this->db->from('sys_modulos_backend mb');
            
            if ($filtro>0)
            {
                $this->db->where("mb.padre_id",$filtro);
            }
            
            if ($criterioBusqueda!="" && count($camposBusqueda)>0)
            {
                
                foreach ($camposBusqueda as &$campo)
                {
                    $campo="mb.".$campo;
                }
                unset($campo);
//                echo "<pre>". print_r($camposBusqueda,true)."<pre>";
                //si viene un solo campo de busqueda
                if(count($camposBusqueda)==1)
                    $this->db->where("$camposBusqueda[0] like","'%".$criterioBusqueda."%'",FALSE);
                else
                {
                    $n=0;
                    foreach ($camposBusqueda as $campo)
                    {
                        if ($n==0)
                            $this->db->where("($campo like","'%".$criterioBusqueda."%'",FALSE);
                        else
                        {
                            if ($n==(count($camposBusqueda)-1))
                                $this->db->or_where("$campo like","'%".$criterioBusqueda."%')",FALSE);
                            else
                                $this->db->or_where("$campo like","'%".$criterioBusqueda."%'",FALSE);
                            
                        }
                       $n++;     
                    }//fin foreach
//                    echo "<pre>". print_r($camposBusqueda,true)."<pre>";

                }
                    
            }
            
            $this->db->order_by("id_modulo"); 
            $this->db->limit($limit,$start);
            $query = $this->db->get();
//            echo $this->db->last_query();
            $res = $query->result_array();
//            $cant = $this->cantModulos();
            $cant = $this->cantSql('mb.id_modulo',$this->db->last_query());
            if (count($res)>0)
            {
                    return '({"total":"'.$cant.'","rows":'.json_encode($res).'})';
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
	
	public function cantModulos()
	{
		$this->db->select("count(id_modulo) as cantidad"); 
		$this->db->from("sys_modulos_backend"); 
		$query = $this->db->get();
		$res = $query->result();
		return $res[0]->cantidad;
	}
	
	public function damePadres()
	{
		$this->db->select("id_modulo as id_padre,modulo as padre"); 
		$this->db->from("sys_modulos_backend");
		$this->db->where("hijos","1"); 
		$this->db->where("menu","1");
		$this->db->order_by("modulo"); 
		$query = $this->db->get();
		$res = $query->result_array();
		$cant = $query->num_rows();
		//$add['id']=0;
		//$add['titulo']="ninguno";
		//$resultado[] = $add;
		if ($cant > 0)
		{
			foreach ($res as $r)
			{
				$resultado[] = $r;
			}
			return '({"num":"'.$cant.'","data":'.json_encode($resultado).'})';
		}
		else
		{
			return '({"num":"0","data":""})';
		}
	}
	
	public function update($id,$datos)
	{
		$info['modulo'] = $datos['modulo'];
		$info['accion'] = $datos['accion'];
		$info['icono'] = $datos['icono'];
		$info['orden'] = $datos['orden'];
		
		if ($datos['padre_id']=="" or $datos['padre_id']==NULL)
			$info['padre_id'] = 0;
		elseif (is_numeric($datos['padre_id']))
			$info['padre_id'] = $datos['padre_id'];
			
		$this->db->where("id_modulo",$id);
		$q = $this->db->update("sys_modulos_backend",$info);
		if ($q)
			return true;
		else
			return false;
	}
	
	public function delete($id)
	{
		$this->db->trans_begin();
		
		$this->db->query("DELETE FROM sys_permisos WHERE id_modulo = ".$id);
		$this->db->query("DELETE FROM sys_modulos_backend WHERE id_modulo = ".$id);
		
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
	
	public function tieneHijos($id)
	{
		$this->db->select("id_modulo");
		$this->db->from("sys_modulos_backend");
		$this->db->where("padre_id",$id);
		$query = $this->db->get();
		if ($query->num_rows()>0)
			return true;
		else
			return false;
	}
	
	public function insert($datos)
	{
		if ($datos['padre_id']=="" or $datos['padre_id']==0)
			$padre_id = 0;
		else
			$padre_id = $datos['padre_id'];
		$this->db->set("modulo",$datos['modulo']);
		$this->db->set("accion",$datos['accion']);
		$this->db->set("icono",$datos['icono']);
		$this->db->set("padre_id",$padre_id);
		$this->db->set("orden",$datos['orden']);
		$this->db->set("hijos",($datos['hijos'])?1:0);
		$this->db->set("menu",($datos['menu'])?1:0);
		if ($this->db->insert("sys_modulos_backend"))
			return true;
		else
			return false;
	}
	public function generarMenu($id_perfil,$id_modulo=0)
	{
		$this->db->select("b.*"); 
		$this->db->from("sys_modulos_backend b");
		if($id_modulo!=0)
		{
			$this->db->join("sys_permisos p", "p.id_modulo = b.id_modulo","inner");
			$this->db->where("p.listar",1);
			$this->db->where("p.id_perfil",$id_perfil);
		} 
		$this->db->where("b.padre_id",$id_modulo);
		$this->db->where("b.menu",1);
		$this->db->where("b.habilitado",1);
		$this->db->order_by("b.orden"); 
		$query = $this->db->get();
//                echo $this->db->last_query();
		$res = $query->result_array();
		$cant = $query->num_rows();
		$return=array();
		foreach($res as $k => $v)
		{
			if($v['hijos']==1)
			{
				$v['submenu']=$this->generarMenu($id_perfil,$v['id_modulo']);
			}
			$return[]=$v;
		}
		return $return;
	}
}
?>