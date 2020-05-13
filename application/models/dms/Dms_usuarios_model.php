<?php
class Dms_usuarios_model extends CI_Model
{
	public function listado($start, $limit, $filtro, $sort="", $dir="")
	{
		
            $this->db->select('dms.id_permiso,dms.id_usuario,dms.editor,dms.revisor,dms.aprobador,dms.publicador,dms.auditor,dms.habilitado');
            $this->db->select('concat(pp.nombre,", ",pp.apellido) as usuario',false);
            $this->db->select('e.abv as empresa',false);
            $this->db->select('pto.puesto');
            $this->db->select('a.area');
            $this->db->from('gr_roles dms');
            $this->db->join('sys_usuarios u','u.id_usuario = dms.id_usuario','left');
            $this->db->join('grl_personas pp','pp.id_persona = u.id_persona','left');
            $this->db->join('gr_usuarios gru','gru.id_usuario = u.id_usuario','inner');
            $this->db->join('gr_puestos pto','pto.id_puesto= gru.id_puesto','inner');
            $this->db->join('gr_organigramas o','o.id_organigrama= pto.id_organigrama','inner');
            $this->db->join('grl_empresas e','e.id_empresa = o.id_empresa','inner');
            $this->db->join('gr_areas a','a.id_area = pto.id_area','inner');
//            $this->db->join('grl_departamentos d','d.id_departamento = u.id_departamento','left');
            
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
                    case 'id_permiso':
                    case 'editor':
                    case 'revisor':
                    case 'aprobador':
                    case 'publicador':
                    case 'auditor':
                    case 'habilitado':
                        $ordenar="dms.".$sort;
                        break;
                    case 'usuario':
                        $ordenar="concat(pp.nombre,pp.apellido)";
                        break;
                     case 'empresa':
                        $ordenar="e.abv";
                        break;
                     case 'area':
                        $ordenar="a.area";
                        break;
                    default:
                        $ordenar="dms.id_permiso";
                }
                $this->db->order_by($ordenar, $dir);
            }
            else
                $this->db->order_by("dms.id_permiso", "desc"); 
            
                $this->db->limit($limit,$start); 
                
                $query = $this->db->get();
//                echo $this->db->last_query();
//		$num = $this->dameTotalUsuarios($filtro);
                $num = $this->cantSql('dms.id_usuario',$this->db->last_query());
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
	public function dameUsuariosPersonasComboTpl($limit,$start,$filtro)
	{
            
            $this->db->select('u.id_usuario');
            $this->db->select('concat(e.abv," - ",pto.puesto) as puesto',false);
            $this->db->select('concat(p.nombre," ",p.apellido) as nomape',false);
            $this->db->from('gr_usuarios u');
            $this->db->join('sys_usuarios uu','uu.id_usuario = u.id_usuario','inner');
            $this->db->join('grl_personas p','p.id_persona = uu.id_persona','inner');
            $this->db->join('gr_puestos pto','pto.id_puesto = u.id_puesto','inner');
            $this->db->join('gr_organigramas o','o.id_organigrama = pto.id_organigrama','inner');
            $this->db->join('grl_empresas e','e.id_empresa = o.id_empresa','inner');
		
//            $this->db->select('u.id_usuario,u.usuario');
//            $this->db->select('concat(p.nombre," ",p.apellido) as nomape',false);
//            $this->db->from('sys_usuarios u');
//            $this->db->join('grl_personas p','p.id_persona = u.id_persona','inner');
             $this->db->where("u.habilitado",1);
            $this->db->where("uu.habilitado",1);
            $this->db->where("p.habilitado",1);
            $this->db->where("(uu.usuario like","'%".$filtro."%'",FALSE);
            $this->db->or_where("p.nombre like","'%".$filtro."%'",FALSE);
            $this->db->or_where("p.apellido like","'%".$filtro."%'",FALSE);
            $this->db->or_where("concat(p.nombre,' ',p.apellido) like","'%".$filtro."%')",FALSE);
            
            $this->db->order_by("p.nombre", "asc"); 
            $this->db->order_by("p.apellido", "asc"); 
            
            $this->db->limit($limit,$start); 
                
            $query = $this->db->get();
            $num = $this->cantSql('u.id_usuario',$this->db->last_query());
            $res = $query->result_array();
            if ($num > 0)
            {
                return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
            }
            else
                return '({"total":"0","rows":""})';
	}
        public function insert($datos)
	{
		$this->db->trans_begin();
		
		$this->db->insert('gr_roles',$datos);
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
        public function check_user($id)
	{
		$this->db->select('id_usuario');
		$this->db->from('gr_roles');
                $this->db->where('id_usuario',$id);
		$query=$this->db->get();
		if($query->num_rows()>0)
                    return false;
		else
                    return true;
	}
        public function deletePermiso($id)
	{
		$q=$this->db->delete('gr_roles',array('id_permiso'=>$id));
		if ($q)
			return true;
		else
			return false;
	}
        public function dameEmailPublicadoresHabilitados($alcance)
        {
            $this->db->select('u.email');
            $this->db->from('gr_roles p');
            $this->db->join('sys_usuarios u','u.id_usuario = p.id_usuario','inner');
            $this->db->join('gr_usuarios gu','gu.id_usuario = p.id_usuario','inner');
            $this->db->join('gr_puestos pto','pto.id_puesto = gu.id_puesto','inner');
            $this->db->join('grl_personas pp','pp.id_persona = u.id_persona','inner');
            $this->db->where("p.habilitado",1);
            $this->db->where("p.publicador",1);
            $this->db->where("u.habilitado",1);
            $this->db->where("pp.habilitado",1);
            $this->db->where("u.mailing",1);
            $org=array();
            switch ($alcance)
            {
                case 0: //Orocobre
                    $org=array(1,2,3,4,5);
                    break;
                case 1: //Sales de Jujuy
                    $org=array(2,3);
                    break;
                case 2: //Borax
                    $org=array(4,5);
                    break;
            }
            $this->db->where_in("pto.id_organigrama",$org);
            
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
	
}	
?>