<?php
class Usuarios_model extends CI_Model
{
	public function listado($start, $limit, $filtro, $sort="", $dir="")
	{
		
            $this->db->select('u.id_usuario,u.usuario,u.password,u.email,u.habilitado');
            $this->db->select('p.perfil as id_perfil');
            $this->db->select('concat(pp.nombre,", ",pp.apellido) as persona',false);
            $this->db->select('p.perfil as id_perfil');
            $this->db->from('sys_usuarios u');
            $this->db->join('sys_perfiles p','p.id_perfil = u.id_perfil','left');
            $this->db->join('grl_personas pp','pp.id_persona = u.id_persona','left');            

            if ($filtro!="")
            {
                $this->db->where("(pp.nombre like","'%".$filtro."%'",FALSE); 
                $this->db->or_where("pp.apellido like","'%".$filtro."%'",FALSE);
                $this->db->or_where("concat(pp.nombre,' ',pp.apellido) like","'%".$filtro."%'",FALSE); 
                $this->db->or_where("u.usuario like","'%".$filtro."%'",FALSE);
                $this->db->or_where("u.email like","'%".$filtro."%')",FALSE); 
            }
            if ($sort!="")
            {
                 switch ($sort)
                {
                    case 'id_usuario':
                        $ordenar="u.id_usuario";
                        break;
                     case 'persona':
                        $ordenar='concat(pp.nombre,", ",pp.apellido)';
                        break;
                    case 'id_perfil':
                        $ordenar="p.perfil";
                        break;
                    case 'email':
                        $ordenar="u.email";
                        break;
                    default:
                        $ordenar="u.usuario";
                        break;

                }
                $this->db->order_by($ordenar, $dir);
            }
            else
            {
                $this->db->order_by("u.usuario", "asc");
            }
            
                $this->db->limit($limit,$start); 
                
                $query = $this->db->get();

		$num = $this->dameTotalUsuarios($filtro);
		$res = $query->result_array();
		if ($num > 0)
		{
			return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
		}
		else
			return '({"total":"0","rows":""})';
	}
	
	public function dameTotalUsuarios($filtro)
	{
		$sql = "select count(u.id_usuario) as cantidad from sys_usuarios u ";
		$sql .= "inner join grl_personas pp on pp.id_persona = u.id_persona";
		if ($filtro!="")
		{
			$sql .= " where (pp.apellido like '%$filtro%' or pp.nombre like '%$filtro%' or u.usuario like '%$filtro%' or u.email like '%$filtro%')";
		}
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0)
		{
			$res = $query->result_array();
			return $res[0]['cantidad'];
		}
		else
			return 0;
	}
	
	public function delete($id)
	{
		$q=$this->db->delete('sys_usuarios',array('id'=>$id));
		if ($q)
		{
			return true;
		}
		else
			return false;
	}
	
	public function edit($id,$datos)
	{
		$this->db->where('id_usuario', $id);
		if(!$this->db->update('sys_usuarios', $datos))
			return false;
		else
			return true;
	}
	
	public function insert($datos)
	{
		$this->db->trans_begin();
		
		$this->db->insert('sys_usuarios',$datos);
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
	
	public function check_user($usuario,$id="")
	{
		$this->db->select('id_usuario');
		$this->db->from('sys_usuarios');
		$this->db->where('usuario',$usuario);
		if($id<>"")
		{
			$this->db->where('id_usuario !=',$id);
		}
		$query=$this->db->get();
		if($query->num_rows()>0)
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	public function checkUsuarioHabilitado($id)
	{
            $this->db->select('u.id_usuario');
            $this->db->from('sys_usuarios u');
            $this->db->join('grl_personas p','p.id_persona = u.id_persona','inner');
            $this->db->join('gr_usuarios gu','gu.id_usuario = u.id_usuario','inner');
            $this->db->where('u.id_usuario',$id);
            $this->db->where('u.habilitado',1);
            $this->db->where('p.habilitado',1);
            $this->db->where('gu.habilitado',1);
            $query=$this->db->get();
            if($query->num_rows()==0)
                return false;
            else
                return true;
	}
        public function checkSupervisor($id_supervisor,$id_usuario)
	{
		$this->db->select("u.id_usuario"); 
		$this->db->from("sys_usuarios u");
                $this->db->join('gr_usuarios gru', 'gru.id_usuario=u.id_usuario', 'inner');
                $this->db->join('gr_puestos pto', 'pto.id_puesto=gru.id_puesto', 'inner');
                $this->db->join('gr_puestos pto_sup', 'pto_sup.id_puesto=pto.id_puesto_superior', 'inner');
                $this->db->join('gr_usuarios gru_sup', 'gru_sup.id_puesto=pto_sup.id_puesto', 'inner');
                $this->db->where('u.id_usuario',$id_usuario);
                $this->db->where('gru_sup.id_usuario',$id_supervisor);
		$query=$this->db->get();
		if($query->num_rows()==0)
                    return false;
		else
                    return true;
	}
	public function check_email($email,$id="")
	{
		$this->db->select('id_usuario');
		$this->db->from('sys_usuarios');
		$this->db->where('email',$email);
		if($id<>"")
		{
			$this->db->where('id_usuario !=',$id);
		}
		$query=$this->db->get();
		if($query->num_rows()>0)
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	public function check_persona($id)
	{
		$this->db->select('id_persona');
		$this->db->from('sys_usuarios');
		$this->db->where('id_persona',$id);
		$query=$this->db->get();
		if($query->num_rows()>0)
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	public function listado_permisos($id)
	{
		$this->db->select("p.*,b.titulo as modulo");
		$this->db->from("sys_permisos p");
		$this->db->join('sys_modulos_backend b', 'b.id_modulo=p.id_modulo', 'inner');
		$this->db->where('p.id_usuario', $id);
		$this->db->order_by("b.modulo", "asc");
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
	public function listado_modulos()
	{
		$this->db->select("*, modulo as modulo");
		$this->db->from("sys_modulos_backend");
		//$this->db->where('menu !=',0);
		//$this->db->where('nombre !=', $nombre);
		$this->db->order_by("titulo", "asc");
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
	public function agregar_modulo($id_modulo,$id_usuario)
	{
		$data = array(
			'id_usuario' => $id_usuario ,
			'id_modulo' => $id_modulo ,
			);
		if($this->db->insert('sys_permisos', $data))
			return true;
		else
			return false;
	}
	public function eliminar_modulo($id_eliminar)
	{
		$q=$this->db->delete('sys_permisos',array('id_modulo'=>$id_eliminar));
		if ($q)
			return true;
		else
			return false;
	}
	
	public function damePerfiles()
	{
		$this->db->select("id_perfil, perfil"); 
		$this->db->from("sys_perfiles");
		$this->db->order_by("perfil");
		$query = $this->db->get();
		$num = $query->num_rows();
		$resultado = $query->result_array();
		if ($num > 0)
		{
			return '({"num":"'.$num.'","data":'.json_encode($resultado).'})';
		}
		else
			return '({"num":"0","data":""})';
	}
	public function dameIdArea($id_usuario)
	{
		$this->db->select("pto.id_area"); 
		$this->db->from("sys_usuarios u");
                $this->db->join('gr_usuarios gru', 'gru.id_usuario=u.id_usuario', 'inner');
                $this->db->join('gr_puestos pto', 'pto.id_puesto=gru.id_puesto', 'inner');
                $this->db->where('u.id_usuario',$id_usuario);
		$query = $this->db->get();
//                echo $this->db->last_query();
		$num = $query->num_rows();
		$resultado = $query->result_array();
		if ($num > 0)
		{
			return $resultado[0];
		}
		else
			return 0;
	}
	public function dameUsuarioCompleto($id_usuario)
	{
		$this->db->select("u.id_usuario"); 
		$this->db->select("pto.id_puesto"); 
		$this->db->select("a.id_area"); 
		$this->db->select("a.gr"); 
		$this->db->select("e.id_empresa"); 
		$this->db->from("sys_usuarios u");
                $this->db->join('gr_usuarios gru', 'gru.id_usuario=u.id_usuario', 'inner');
                $this->db->join('gr_puestos pto', 'pto.id_puesto=gru.id_puesto', 'inner');
                $this->db->join('gr_areas a', 'a.id_area=pto.id_area', 'inner');
                $this->db->join('gr_organigramas o', 'o.id_organigrama=a.id_organigrama', 'inner');
                $this->db->join('grl_empresas e', 'e.id_empresa=o.id_empresa', 'inner');
                $this->db->where('u.id_usuario',$id_usuario);
		$query = $this->db->get();
		$num = $query->num_rows();
		$resultado = $query->result_array();
		if ($num > 0)
		{
			return $resultado[0];
		}
		else
			return 0;
	}
	public function dameUsuarioParaEd($id_usuario)
	{
		$this->db->select("u.id_usuario"); 
                $this->db->select('concat(p.nombre," ",p.apellido) as usuario',false);
		$this->db->select("pto.id_puesto"); 
		$this->db->select("pto.puesto"); 
		$this->db->select("a.id_area"); 
		$this->db->select("a.area"); 
		$this->db->select("e.id_empresa"); 
		$this->db->select("e.empresa");
                
//                $this->db->select('u.id_supervisor as id_usuario_supervisor',false);
//                $this->db->select('concat(p2.nombre," ",p2.apellido) as supervisor',false);
//                $this->db->select("pto2.id_puesto as id_puesto_supervisor",false); 
//                $this->db->select("pto2.puesto as puesto_supervisor",false); 
//                $this->db->select("a2.id_area as id_area_supervisor",false); 
//                $this->db->select("a2.area as area_supervisor",false); 
//                $this->db->select("e2.id_empresa as id_empresa_supervisor",false); 
//                $this->db->select("e2.empresa as empresa_supervisor",false); 
                
                $this->db->select("if(u2.habilitado=0,NULL,u2.id_usuario)  as id_usuario_supervisor",false); 
                $this->db->select('if(u2.habilitado=0,NULL,concat(p2.nombre," ",p2.apellido)) as supervisor',false);
		$this->db->select("if(u2.habilitado=0,NULL,pto2.id_puesto) as id_puesto_supervisor",false); 
		$this->db->select("if(u2.habilitado=0,NULL,pto2.puesto) as puesto_supervisor",false); 
		$this->db->select("if(u2.habilitado=0,NULL,a2.id_area) as id_area_supervisor",false); 
		$this->db->select("if(u2.habilitado=0,NULL,a2.area) as area_supervisor",false); 
		$this->db->select("if(u2.habilitado=0,NULL,e2.id_empresa) as id_empresa_supervisor",false); 
		$this->db->select("if(u2.habilitado=0,NULL,e2.empresa) as empresa_supervisor",false); 
                
		$this->db->from("sys_usuarios u");
                $this->db->join('grl_personas p','p.id_persona = u.id_persona','inner');
                $this->db->join('gr_usuarios gru', 'gru.id_usuario=u.id_usuario', 'inner');
                $this->db->join('gr_puestos pto', 'pto.id_puesto=gru.id_puesto', 'inner');
                $this->db->join('gr_areas a', 'a.id_area=pto.id_area', 'inner');
                $this->db->join('gr_organigramas o', 'o.id_organigrama=a.id_organigrama', 'inner');
                $this->db->join('grl_empresas e', 'e.id_empresa=o.id_empresa', 'inner');
                
                
                $this->db->join('sys_usuarios u2','u2.id_usuario = u.id_supervisor','left');
                $this->db->join('grl_personas p2','p2.id_persona = u2.id_persona','left');
                $this->db->join('gr_usuarios gru2', 'gru2.id_usuario=u2.id_usuario', 'left');
                $this->db->join('gr_puestos pto2', 'pto2.id_puesto=gru2.id_puesto', 'left');
                $this->db->join('gr_areas a2', 'a2.id_area=pto2.id_area', 'left');
                $this->db->join('gr_organigramas o2', 'o2.id_organigrama=a2.id_organigrama', 'left');
                $this->db->join('grl_empresas e2', 'e2.id_empresa=o2.id_empresa', 'left');
                
                
                $this->db->where('u.id_usuario',$id_usuario);
                $this->db->where('u.habilitado',1);
                $this->db->where('gru.habilitado',1);
                $this->db->limit(1,0); 
		$query = $this->db->get();
//                echo $this->db->last_query();
		$num = $query->num_rows();
		$res = $query->result_array();
		if ($num > 0)
                    return $res[0];
		else
                    return 0;
	}
	public function dameUsuariosConSupervisor($id_usuario)
	{
		$this->db->select("u.id_usuario as id_usuario",false); 
                $this->db->select('concat(p.nombre," ",p.apellido) as usuario',false);
		$this->db->select("pto.id_puesto"); 
		$this->db->select("pto.puesto"); 
		$this->db->select("a.id_area"); 
		$this->db->select("a.area"); 
		$this->db->select("e.id_empresa"); 
		$this->db->select("e.empresa");
                
		$this->db->select("u_sup.id_usuario as id_usuario_supervisor",false); 
                $this->db->select('concat(p_sup.nombre," ",p_sup.apellido) as supervisor',false);
		$this->db->select("pto_sup.id_puesto as id_puesto_supervisor",false); 
		$this->db->select("pto_sup.puesto as puesto_supervisor",false); 
		$this->db->select("a_sup.id_area as id_area_supervisor",false); 
		$this->db->select("a_sup.area as area_supervisor",false); 
		$this->db->select("e_sup.id_empresa as id_empresa_supervisor",false); 
		$this->db->select("e_sup.empresa as empresa_supervisor",false); 
                
                
		$this->db->from("sys_usuarios u");
                $this->db->join('grl_personas p','p.id_persona = u.id_persona','inner');
                $this->db->join('gr_usuarios gru', 'gru.id_usuario=u.id_usuario', 'inner');
                $this->db->join('gr_puestos pto', 'pto.id_puesto=gru.id_puesto', 'inner');
                $this->db->join('gr_areas a', 'a.id_area=pto.id_area', 'inner');
                $this->db->join('gr_organigramas o', 'o.id_organigrama=a.id_organigrama', 'inner');
                $this->db->join('grl_empresas e', 'e.id_empresa=o.id_empresa', 'inner');
                
                $this->db->join('gr_puestos pto_sup', 'pto_sup.id_puesto=pto.id_puesto_superior', 'inner');
                $this->db->join('gr_areas a_sup', 'a_sup.id_area=pto_sup.id_area', 'inner');
                $this->db->join('gr_organigramas o_sup', 'o_sup.id_organigrama=a_sup.id_organigrama', 'inner');
                $this->db->join('grl_empresas e_sup', 'e_sup.id_empresa=o_sup.id_empresa', 'inner');
                $this->db->join('gr_usuarios gru_sup', 'gru_sup.id_puesto=pto_sup.id_puesto', 'inner');
                $this->db->join('sys_usuarios u_sup', 'u_sup.id_usuario=gru_sup.id_usuario', 'inner');
                $this->db->join('grl_personas p_sup','p_sup.id_persona = u_sup.id_persona','inner');
                
                $this->db->where('u.id_usuario',$id_usuario);
                $this->db->where('u.habilitado',1);
                $this->db->where('gru.habilitado',1);
                $this->db->order_by("u.id_usuario",'asc');
//                $this->db->limit(10,0); 
		$query = $this->db->get();
//                echo $this->db->last_query();
		$num = $query->num_rows();
		$res = $query->result_array();
		if ($num > 0)
		{
			return $res[0];
		}
		else
			return 0;
	}
	public function dameUsuariosConSupervisorPorEmpresa($id_empresa_usuario=0)
	{
		$this->db->select("u.id_usuario as id_usuario",false); 
                $this->db->select('concat(p.nombre," ",p.apellido) as usuario',false);
		$this->db->select("pto.id_puesto"); 
		$this->db->select("pto.puesto"); 
		$this->db->select("a.id_area"); 
		$this->db->select("a.area"); 
		$this->db->select("e.id_empresa"); 
		$this->db->select("e.empresa");
                
//                $this->db->select("(select u_sup.id_usuario as id_usuario_supervisor, "
//                        . "concat(p_sup.nombre,' ',p_sup.apellido) as supervisor, "
//                        . "pto_sup.id_puesto as id_puesto_supervisor, "
//                        . "a_sup.area as area_supervisor, "
//                        . "e_sup.id_empresa as id_empresa_supervisor, "
//                        . "e_sup.empresa as empresa_supervisor "
//                        . "from sys_usuarios su "
//                        . "where su.id_usuario=u.id_supervisor "
//                            . "and su.habilitado=1)"
//                        ,false);
                
		$this->db->select("if(u_sup.habilitado=0,NULL,u_sup.id_usuario)  as id_usuario_supervisor",false); 
                $this->db->select('if(u_sup.habilitado=0,NULL,concat(p_sup.nombre," ",p_sup.apellido)) as supervisor',false);
		$this->db->select("if(u_sup.habilitado=0,NULL,pto_sup.id_puesto) as id_puesto_supervisor",false); 
		$this->db->select("if(u_sup.habilitado=0,NULL,pto_sup.puesto) as puesto_supervisor",false); 
		$this->db->select("if(u_sup.habilitado=0,NULL,a_sup.id_area) as id_area_supervisor",false); 
		$this->db->select("if(u_sup.habilitado=0,NULL,a_sup.area) as area_supervisor",false); 
		$this->db->select("if(u_sup.habilitado=0,NULL,e_sup.id_empresa) as id_empresa_supervisor",false); 
		$this->db->select("if(u_sup.habilitado=0,NULL,e_sup.empresa) as empresa_supervisor",false); 
                
                
		$this->db->from("sys_usuarios u");
                $this->db->join('grl_personas p','p.id_persona = u.id_persona','inner');
                $this->db->join('gr_usuarios gru', 'gru.id_usuario=u.id_usuario', 'inner');
                $this->db->join('gr_puestos pto', 'pto.id_puesto=gru.id_puesto', 'inner');
                $this->db->join('gr_areas a', 'a.id_area=pto.id_area', 'inner');
                $this->db->join('gr_organigramas o', 'o.id_organigrama=a.id_organigrama', 'inner');
                $this->db->join('grl_empresas e', 'e.id_empresa=o.id_empresa', 'inner');
                
                
                $this->db->join('sys_usuarios u_sup','u_sup.id_usuario = u.id_supervisor','left');
                $this->db->join('grl_personas p_sup','p_sup.id_persona = u_sup.id_persona','left');
                $this->db->join('gr_usuarios gru_sup', 'gru_sup.id_usuario=u_sup.id_usuario', 'left');
                $this->db->join('gr_puestos pto_sup', 'pto_sup.id_puesto=gru_sup.id_puesto', 'left');
                $this->db->join('gr_areas a_sup', 'a_sup.id_area=pto_sup.id_area', 'left');
                $this->db->join('gr_organigramas o_sup', 'o_sup.id_organigrama=a_sup.id_organigrama', 'left');
                $this->db->join('grl_empresas e_sup', 'e_sup.id_empresa=o_sup.id_empresa', 'left');
                
//                $this->db->join('gr_puestos pto_sup', 'pto_sup.id_puesto=pto.id_puesto_superior', 'inner');
//                $this->db->join('gr_areas a_sup', 'a_sup.id_area=pto_sup.id_area', 'inner');
//                $this->db->join('gr_organigramas o_sup', 'o_sup.id_organigrama=a_sup.id_organigrama', 'inner');
//                $this->db->join('grl_empresas e_sup', 'e_sup.id_empresa=o_sup.id_empresa', 'inner');
//                $this->db->join('gr_usuarios gru_sup', 'gru_sup.id_puesto=pto.id_puesto_superior', 'inner');
//                $this->db->join('sys_usuarios u_sup', 'u_sup.id_usuario=gru_sup.id_usuario', 'inner');
//                $this->db->join('grl_personas p_sup','p_sup.id_persona = u_sup.id_persona','inner');
                
                $this->db->where('u.habilitado',1);
                $this->db->where('gru.habilitado',1);
                $this->db->where('gru_sup.habilitado',1);
                if ($id_empresa_usuario!=0 && $id_empresa_usuario!=-1)
                    $this->db->where('e.id_empresa',$id_empresa_usuario);
                $this->db->order_by("u.id_usuario",'asc');
//                $this->db->limit(10,0); 
		$query = $this->db->get();
//                echo $this->db->last_query();
		$num = $query->num_rows();
		$res = $query->result_array();
		if ($num > 0)
		{
			return $res;
		}
		else
			return 0;
	}
	public function dameUsuario($id_usuario)
	{
		$this->db->select("u.id_usuario"); 
		$this->db->select("pto.id_puesto"); 
		$this->db->select("a.id_area"); 
		$this->db->select("a.area"); 
		$this->db->select("a.gr"); 
		$this->db->select("e.id_empresa");
                $this->db->select('concat(p.nombre," ",p.apellido) as nomape',false);
                $this->db->select('concat(e.abv," - ",pto.puesto) as puesto',false);
		$this->db->from("sys_usuarios u");
                $this->db->join('grl_personas p','p.id_persona = u.id_persona','inner');
                $this->db->join('gr_usuarios gru', 'gru.id_usuario=u.id_usuario', 'inner');
                $this->db->join('gr_puestos pto', 'pto.id_puesto=gru.id_puesto', 'inner');
                $this->db->join('gr_areas a', 'a.id_area=pto.id_area', 'inner');
                $this->db->join('gr_organigramas o', 'o.id_organigrama=a.id_organigrama', 'inner');
                $this->db->join('grl_empresas e', 'e.id_empresa=o.id_empresa', 'inner');
                $this->db->where('u.id_usuario',$id_usuario);
		$query = $this->db->get();
		$num = $query->num_rows();
		$resultado = $query->result_array();
		if ($num > 0)
		{
			return $resultado[0];
		}
		else
			return 0;
	}
	public function dameUsuarioEmpresaYGR($id_usuario)
	{
		$this->db->select("e.id_empresa"); 
		$this->db->select("e.abv"); 
		$this->db->select("a.gr"); 
		$this->db->from("sys_usuarios u");
                $this->db->join('gr_usuarios gru', 'gru.id_usuario=u.id_usuario', 'inner');
                $this->db->join('gr_puestos pto', 'pto.id_puesto=gru.id_puesto', 'inner');
                $this->db->join('gr_areas a', 'a.id_area=pto.id_area', 'inner');
                $this->db->join('gr_organigramas o', 'o.id_organigrama=pto.id_organigrama', 'inner');
                $this->db->join('grl_empresas e', 'e.id_empresa=o.id_empresa', 'inner');
                $this->db->where('u.id_usuario',$id_usuario);
		$query = $this->db->get();
		$num = $query->num_rows();
		$resultado = $query->result_array();
		if ($num > 0)
		{
			return $resultado[0];
		}
		else
			return 0;
	}
	
	public function dameOrigen($id_usuario)
	{
		$this->db->select("o.id_empresa"); 
		$this->db->select("pto.id_area"); 
		$this->db->from("sys_usuarios u");
                $this->db->join('gr_usuarios gru', 'gru.id_usuario=u.id_usuario', 'inner');
                $this->db->join('gr_puestos pto', 'pto.id_puesto=gru.id_puesto', 'inner');
                $this->db->join('gr_areas a', 'a.id_area=pto.id_area', 'inner');
                $this->db->join('gr_organigramas o', 'o.id_organigrama=a.id_organigrama', 'inner');
                $this->db->where('u.id_usuario',$id_usuario);
		$query = $this->db->get();
		$num = $query->num_rows();
		$resultado = $query->result_array();
		if ($num > 0)
		{
			return $resultado[0];
		}
		else
			return 0;
	}
	public function dameOrigenAbv($id_usuario)
	{
		$this->db->select("e.abv as empresa, g.abv as gerencia,d.abv as departamento"); 
		$this->db->from("sys_usuarios u");
                $this->db->join('grl_departamentos d', 'd.id_departamento=u.id_departamento', 'left');
                $this->db->join('grl_gerencias g', 'g.id_gerencia=u.id_gerencia', 'inner');
                $this->db->join('grl_empresas e', 'e.id_empresa=g.id_empresa', 'inner');
                $this->db->where('u.id_usuario',$id_usuario);
		$query = $this->db->get();
		$num = $query->num_rows();
		$resultado = $query->result_array();
		if ($num > 0)
		{
			return $resultado[0];
		}
		else
			return 0;
	}
	
	public function traer_datos_usuario($user_id)
	{
		$this->db->select("u.usuario as usuarioFieldEditor, u.email as emailFieldEditor, u.id_perfil as perfilFieldEditor");
		$this->db->select("p.apellido as apellidoUsrFieldEditor, p.nombre as nombreUsrFieldEditor");
		$this->db->from("sys_usuarios u");
                $this->db->join('grl_personas p','p.id_persona = u.id_persona','inner');
		$this->db->where('u.id_usuario',$user_id);
		$query = $this->db->get();
		$num = $query->num_rows();
		$res = $query->result_array();
		if ($num > 0)
		{
			echo "{success: true, msg: 'Se cargaron los datos con &eacute;xito.', data:".str_replace(']','',str_replace('[','',json_encode($res)))."}";
			//die();
		}
		else
			return '({"total":"0","rows":""})';
	}
	
	public function validaPass($usuario_id, $pass)
	{
		$sql = "select id_usuario from sys_usuarios where id_usuario = ".$usuario_id." and password = md5('$pass') and habilitado=1";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0)
		{
			return true; 
		}
		else
			return false;
	}
	
	public function changePass($usuario_id, $pass)
	{
		$sql = "update sys_usuarios set password = md5('$pass') where id_usuario = ".$usuario_id;
		if ($this->db->query($sql))
			return true;
		else
			return false; 
				
	}
	
	public function changeDataMin($usuario_id, $nombre, $apellido, $descripcion)
	{
		$sql = "update sys_usuarios set nombre='$nombre', apellido='$apellido' where id_usuario = ".$usuario_id;		
		if ($this->db->query($sql))
		{
			return true;
		}
		else
			return false; 
	}
        public function dameEmailPublicadoresHabilitados()
        {
            $this->db->select('u.email');
            $this->db->from('sys_usuarios u');
            $this->db->join('grl_personas p','p.id_persona = u.id_persona','inner');
            $this->db->join('gr_roles pp','pp.id_usuario = u.id_usuario','inner');
            $this->db->where("pp.publicador",1);
            $this->db->where("pp.habilitado",1);
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
        public function dameEmailUsuario($id_usuario)
        {
            $this->db->select('u.email');
            $this->db->select('concat(p.nombre," ",p.apellido) as nomape',false);
            $this->db->from('sys_usuarios u');
            $this->db->join('grl_personas p','p.id_persona = u.id_persona','inner');
            $this->db->where("u.habilitado",1);
            $this->db->where("p.habilitado",1);
            $this->db->where("u.id_usuario",$id_usuario);
            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $query->num_rows();
            $res = $query->result_array();
            if ($num > 0)
            {
                    return $res[0];
            }
            else
                    return 0;
        }
        public function usuariosCombo($limit=5,$start=0,$filtro,$idNot="")
	{
		
//            $this->db->select('p.id_usuario');
            $this->db->select('u.id_usuario,uu.usuario');
//            $this->db->select('pto.puesto');
            $this->db->select('concat(e.abv," - ",pto.puesto) as puesto',false);
            $this->db->select('concat(pp.nombre," ",pp.apellido) as nomape',false);
            $this->db->from('gr_usuarios u');
            $this->db->join('sys_usuarios uu','uu.id_usuario = u.id_usuario','inner');
            $this->db->join('grl_personas pp','pp.id_persona = uu.id_persona','inner');
            $this->db->join('gr_puestos pto','pto.id_puesto = u.id_puesto','inner');
            $this->db->join('gr_organigramas or1','or1.id_organigrama = pto.id_organigrama','inner');
            $this->db->join('grl_empresas e','e.id_empresa = or1.id_empresa','inner');
//            $this->db->where("u.id_perfil",2);
            
            if($idNot!="")
                $this->db->where_not_in("u.id_usuario",$idNot);
            
            
            $this->db->where("pp.habilitado",1);
            $this->db->where("u.habilitado",1);
            $this->db->where("uu.habilitado",1);
            $this->db->where("(pp.nombre like","'%".$filtro."%'",FALSE);
            $this->db->or_where("pp.apellido like","'%".$filtro."%'",FALSE);
            $this->db->or_where("concat(pp.nombre,' ',pp.apellido) like","'%".$filtro."%')",FALSE);
            
            
            $this->db->order_by("pp.nombre", "asc"); 
            $this->db->order_by("pp.apellido", "asc"); 
            
            $this->db->limit($limit,$start); 
                
            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $this->cantSql('u.id_usuario',$this->db->last_query());
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
        
        //combo de usuarios a cargo
        public function dameDrCombo($id_jefe,$limit=5,$start=0,$filtro="",$idNot="")
	{
		
            $this->db->select('u.id_usuario,uu.usuario');
            $this->db->select('concat(e.abv," - ",pto.puesto) as puesto',false);
            $this->db->select('concat(pp.nombre," ",pp.apellido) as nomape',false);
            $this->db->from('gr_usuarios u');
            $this->db->join('sys_usuarios uu','uu.id_usuario = u.id_usuario','inner');
            $this->db->join('grl_personas pp','pp.id_persona = uu.id_persona','inner');
            $this->db->join('gr_puestos pto','pto.id_puesto = u.id_puesto','inner');
            $this->db->join('gr_puestos pto_s','pto_s.id_puesto = pto.id_puesto_superior','inner');
            $this->db->join('gr_usuarios u_s','u_s.id_puesto = pto_s.id_puesto','inner');
            $this->db->join('gr_organigramas or1','or1.id_organigrama = pto.id_organigrama','inner');
            $this->db->join('grl_empresas e','e.id_empresa = or1.id_empresa','inner');
            $this->db->where("u_s.id_usuario",$id_jefe);
            
            if($idNot!="")
                $this->db->where_not_in("u.id_usuario",$idNot);
            
            
            $this->db->where("pp.habilitado",1);
            $this->db->where("u.habilitado",1);
            $this->db->where("uu.habilitado",1);
            if ($filtro!="")
            {
                $this->db->where("(pp.nombre like","'%".$filtro."%'",FALSE);
                $this->db->or_where("pp.apellido like","'%".$filtro."%'",FALSE);
                $this->db->or_where("concat(pp.nombre,' ',pp.apellido) like","'%".$filtro."%')",FALSE);
            }
            
            
            $this->db->order_by("pp.nombre", "asc"); 
            $this->db->order_by("pp.apellido", "asc"); 
            
            $this->db->limit($limit,$start); 
                
            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $this->cantSql('u.id_usuario',$this->db->last_query());
            $res = $query->result_array();
            if ($num > 0)
            {
                return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
            }
            else
                return '({"total":"0","rows":""})';
	}
        
        public function dameGerenciaUsuario ($id_usuario)
        {
            $this->db->select('ga.id_gcia');
            $this->db->from('sys_usuarios su');
            $this->db->join('gr_usuarios gu','gu.id_usuario = su.id_usuario','inner');
            $this->db->join('gr_puestos gp','gp.id_puesto = gu.id_puesto','inner');
            $this->db->join('gr_areas ga', 'ga.id_area=gp.id_area', 'inner');
            $this->db->where('su.id_usuario',$id_usuario);
            $query=$this->db->get();
            $row = $query->row();
            return $row->id_gcia;
        }
        
        public function usuariosComboCPP($limit=5,$start=0,$filtro,$idNot="")
	{
		
//            $this->db->select('p.id_usuario');
            $this->db->select('u.id_usuario,uu.usuario');
//            $this->db->select('pto.puesto');
            $this->db->select('concat(e.abv," - ",pto.puesto) as puesto',false);
            $this->db->select('concat(pp.nombre," ",pp.apellido) as nomape',false);
            $this->db->from('gr_usuarios u');
            $this->db->join('sys_usuarios uu','uu.id_usuario = u.id_usuario','inner');
            $this->db->join('grl_personas pp','pp.id_persona = uu.id_persona','inner');
            $this->db->join('gr_puestos pto','pto.id_puesto = u.id_puesto','inner');
            $this->db->join('gr_organigramas or1','or1.id_organigrama = pto.id_organigrama','inner');
            $this->db->join('grl_empresas e','e.id_empresa = or1.id_empresa','inner');
//            $this->db->where("u.id_perfil",2);
            
            if($idNot!="")
                $this->db->where_not_in("u.id_usuario",$idNot);
            
            $empresa_id = array(1,2);
            $this->db->where("pp.habilitado",1);
            $this->db->where("u.habilitado",1);
            $this->db->where("uu.habilitado",1);
            $this->db->where("(pp.nombre like","'%".$filtro."%'",FALSE);
            $this->db->or_where("pp.apellido like","'%".$filtro."%'",FALSE);
            $this->db->or_where("concat(pp.nombre,' ',pp.apellido) like","'%".$filtro."%')",FALSE);
            $this->db->where_in("e.id_empresa",$empresa_id);
            
            $this->db->order_by("pp.nombre", "asc"); 
            $this->db->order_by("pp.apellido", "asc"); 
            
            $this->db->limit($limit,$start); 
                
            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $this->cantSql('u.id_usuario',$this->db->last_query());
            $res = $query->result_array();
            if ($num > 0)
            {
                return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
            }
            else
                return '({"total":"0","rows":""})';
	}
        
        public function dameSupervisor($id_usuario)
	{
		$this->db->select("su1.id_usuario,su1.email,p1.genero");
                $this->db->select('concat(p1.nombre," ",p1.apellido) as nomape',false);
		$this->db->from("sys_usuarios u");
                $this->db->join('grl_personas p','p.id_persona = u.id_persona','inner');
                $this->db->join('gr_usuarios gu', 'gu.id_usuario=u.id_usuario', 'inner');
                $this->db->join('gr_puestos gp', 'gp.id_puesto=gu.id_puesto', 'inner');
                $this->db->join('gr_usuarios gu1', 'gu1.id_puesto=gp.id_puesto_superior', 'inner');
                $this->db->join('sys_usuarios su1', 'su1.id_usuario=gu1.id_usuario', 'inner');
                $this->db->join('grl_personas p1','p1.id_persona = su1.id_persona','inner');
                $this->db->where('u.id_usuario',$id_usuario);
		$query = $this->db->get();
//                echo $this->db->last_query();
		$num = $query->num_rows();
		$resultado = $query->result_array();
		if ($num > 0)
                    return $resultado;
		else
                    return 0;
	}
        public function dameIdUsuarioSuperior($id_usuario)
	{
		$this->db->select("u2.id_usuario");
		$this->db->from("sys_usuarios u");
                $this->db->join('gr_usuarios gu', 'gu.id_usuario=u.id_usuario', 'inner');
                $this->db->join('grl_personas p','p.id_persona = u.id_persona','inner');
                $this->db->join('gr_puestos pto', 'pto.id_puesto=gu.id_puesto', 'inner');
                $this->db->join('gr_usuarios gu2', 'gu2.id_puesto=pto.id_puesto_superior', 'inner');
                $this->db->join('sys_usuarios u2', 'u2.id_usuario=gu2.id_usuario', 'inner');
                $this->db->where('u.id_usuario',$id_usuario);
//                $this->db->where('u2.habilitado',1);
//                $this->db->where('gu.habilitado',1);
//                $this->db->where('p.habilitado',1);
		$query = $this->db->get();
//                echo $this->db->last_query();
                $res = $query->row();
                $num=count($res);
		if ($num == 1)
                    return $res->id_usuario;
		else
                    return 0;
	}
        
        public function DameUsuarioPersona($id_usuario)
	{
            $this->db->select("u.email");
            $this->db->select('concat(p.nombre," ",p.apellido) as nomape',false);
            $this->db->select("p.genero");
            $this->db->select("gp.puesto");
            $this->db->from("sys_usuarios u");
            $this->db->join('grl_personas p','p.id_persona = u.id_persona','inner');
            $this->db->join('gr_usuarios gu','gu.id_usuario = u.id_usuario','inner');
            $this->db->join('gr_puestos gp','gp.id_puesto = gu.id_puesto','inner');
            $this->db->where('u.id_usuario',$id_usuario);
            $query = $this->db->get();
            $num = $query->num_rows();
            $res = $query->result_array();
            if ($num > 0)
                return $res;
            else
                return 0;
            }
            
        public function DameUsuarioInvestigador($id_usuario)
	{
            $this->db->select("u.email");
            $this->db->select('concat(p.nombre," ",p.apellido) as nomape',false);
            $this->db->select("DATE_FORMAT(ci.fecha_alta,'%d/%m/%Y') as fecha_alta", FALSE);
            $this->db->from("sys_usuarios u");
            $this->db->join('grl_personas p','p.id_persona = u.id_persona','inner');
            $this->db->join('gr_usuarios gu','gu.id_usuario = u.id_usuario','inner');
            $this->db->join('cpp_investigadores ci','ci.id_usuario = u.id_usuario','inner');
            $this->db->where('ci.id_usuario',$id_usuario);
            $query = $this->db->get();
            $num = $query->num_rows();
            $res = $query->result_array();
            if ($num > 0)
                return $res;
            else
                return 0;
        }

        public function dameUsuarioSupervisor($id_usuario)
        {
            $this->db->select('su.*');
            $this->db->from('sys_usuarios u');
            $this->db->join('sys_usuarios su','su.id_usuario = u.id_supervisor','inner');
            $this->db->where_in('u.id_usuario',$id_usuario);
            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $query->num_rows();
            $res = $query->result_array();
            if ($num > 0)
                return $res;
            else
                return 0;
        }
}	

?>