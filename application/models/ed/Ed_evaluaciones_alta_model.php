<?php
class Ed_evaluaciones_alta_model extends CI_Model
{
	public function listado($start, $limit, $filtro, $sort="",$dir,$busqueda,$campos)
	{
		
            $this->db->select('ea.id_alta,ea.id_usuario,ea.id_usuario_supervisor,ea.id_periodo,ea.verificado,ea.duplicado');
            $this->db->select('ea.usuario,ea.area,ea.supervisor,ea.empresa');
            $this->db->select('p.periodo');
            $this->db->select('gcia.area as gerencia',false);
            
//            $this->db->select('concat(pp.nombre,", ",pp.apellido) as usuario',false);
//            $this->db->select('a.area',false);
//            $this->db->select('concat(pp_sup.nombre,", ",pp_sup.apellido) as supervisor',false);
//            $this->db->select('em.id_empresa, em.empresa',false);
            
            $this->db->from('ed_evaluaciones_alta ea');
            $this->db->join('ed_periodos p','p.id_periodo = ea.id_periodo');
            
//            $this->db->join('sys_usuarios u','u.id_usuario = ea.id_usuario');
//            $this->db->join('sys_usuarios u_sup','u_sup.id_usuario=ea.id_usuario_supervisor');
//            $this->db->join('grl_personas pp','pp.id_persona = u.id_persona');
//            $this->db->join('grl_personas pp_sup','pp_sup.id_persona = u_sup.id_persona');
//            $this->db->join('gr_usuarios gru', 'gru.id_usuario=u.id_usuario', 'inner');
//            $this->db->join('gr_puestos pto', 'pto.id_puesto=gru.id_puesto', 'inner');
            $this->db->join('gr_areas a', 'a.id_area=ea.id_area', 'inner');
            $this->db->join('gr_areas gcia', 'gcia.id_area=a.id_gcia', 'left');
//            $this->db->join('gr_organigramas o', 'o.id_organigrama=a.id_organigrama', 'inner');
//            $this->db->join('grl_empresas em', 'em.id_empresa=o.id_empresa', 'inner');
            
            if ($filtro!="")
            {
                $this->db->where("ea.id_periodo =",$filtro); 
            }
            if ($busqueda!="" && count($campos)>0)
            {
                foreach ($campos as &$campo)
                {
                    switch($campo)
                    {
                        case 'id_alta':
                            $campo = "ea.id_alta";
                            break;
                        case 'usuario':
                            $campo = "ea.usuario";
                            break;
                        case 'supervisor':
                            $campo = "ea.supervisor";
                            break;
                        case 'area':
                            $campo = "ea.area";
                            break;
                        case 'gerencia':
                            $campo = "gcia.area";
                            break;
                        case 'empresa':
                            $campo = "ea.empresa";
                            break;
                        case 'duplicado':
                            $campo = "ea.duplicado";
                            break;
                    }

                }
                 unset($campo);
                //si viene un solo campo de busqueda
                if(count($campos)==1)
                {
                    if ($campos[0] == "ea.id_alta")
                        $this->db->where($campos[0]." =",$busqueda,FALSE);
                    else
                        $this->db->where($campos[0]. " like","'%".$busqueda."%'",FALSE);
                }
                else
                {
                    $n=0;
                    foreach ($campos as $campo)
                    {
                        if ($n==0)
                            {
                            $this->db->where("(".$campo." like","'%".$busqueda."%'",FALSE);
                            }
                        else
                        {
                            if ($n==count($campos)-1)
                            {
                                $this->db->or_where($campo ." like","'%".$busqueda."%')",FALSE);
                            }
                            else
                            {
                                $this->db->or_where($campo." like","'%".$busqueda."%'",FALSE);
                            }
                        }
                       $n++;     
                    }
                }
            }
            if ($sort!="")
            {
                switch ($sort)
                {
                    case 'id_alta':
                        $ordenar="ea.id_alta";
                        break;
                    case 'area':
                        $ordenar="ea.area";
                        break;
                    case 'usuario':
                        $ordenar="ea.usuario";
                        break;
                    case 'supervisor':
                        $ordenar="ea.supervisor";
                        break;
                    case 'gerencia':
                        $ordenar="gcia.area";
                        break;
                    case 'empresa':
                        $ordenar="ea.empresa";
                        break;
                    case 'verificado':
                        $ordenar="ea.verificado";
                        break;
                    case 'duplicado':
                        $ordenar="ea.duplicado";
                        break;
                    default:
                       $ordenar="ea.id_alta";

                }
                $this->db->order_by($ordenar, $dir);
            }
            else {
                $this->db->order_by("ea.id_alta", "asc"); 
            }
            
            $this->db->limit($limit,$start); 
            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $this->cantSql('ea.id_alta',$this->db->last_query());
            $res = $query->result_array();
            if ($num > 0)
            {
                    return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
            }
            else
            {
                    return '({"total":"0","rows":""})';
            }
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
        
        
        function update($id, $datos)
        {
            $this->db->where("id_alta",$id);
            if ($this->db->update("ed_evaluaciones_alta",$datos))
                return true;
            else
                return false;
        }
        function marcarDuplicadoenCero()
        {
            $this->db->set('duplicado', 0); 
            if ($this->db->update("ed_evaluaciones_alta"))
                return true;
            else
                return false;
        }
        function marcarDuplicado($ids)
        {
            //antes de empezar marco todos en 0
            $this->marcarDuplicadoenCero();        
            $this->db->set('duplicado', 1); 
            $this->db->where_in("id_alta",$ids);
            if ($this->db->update("ed_evaluaciones_alta"))
                return true;
            else
                return false;
        }
        
        public function insert($datos)
	{
            $insert=$this->db->insert("ed_evaluaciones_alta",$datos);
//            echo $this->db->last_query();
            if ($insert)
                return true;
            else
                return false;
	}
       
        public function limpiarGeneradosAnteriores($id_periodo)
	{
            $this->db->where('id_periodo', $id_periodo);
            $q=$this->db->delete('ed_evaluaciones_alta'); 
            if ($q)
            {
                return true;
            }
            else
                return false;
	}
        public function delete($id)
	{
            $this->db->where("id_alta",$id);
            $q = $this->db->delete("ed_evaluaciones_alta");
            if ($q)
                return true;
            else
                return false;
	}
        public function deleteNoVerificados()
	{
            $verificados=0;
            $this->db->where("verificado",$verificados);
            $q = $this->db->delete("ed_evaluaciones_alta");
            if ($q)
                return true;
            else
                return false;
	}
        public function deleteVerificados()
	{
            $verificados=1;
            $this->db->where("verificado",$verificados);
            $q = $this->db->delete("ed_evaluaciones_alta");
            if ($q)
                return true;
            else
                return false;
	}
        public function dameUsuariosVerificados()
        {
            $this->db->select('ea.id_usuario');
            $this->db->from('ed_evaluaciones_alta ea');
            $this->db->where("verificado",1);
            $query = $this->db->get();
            $num = $query->num_rows();
            $res = $query->result_array();
            $array=array();
            if ($num > 0)
            {
                foreach($res as $value)
                    $array[]=$value['id_usuario'];
                return $array;
            }
            else
                return $array;
        }
        public function usuariosCombo($limit,$start,$filtro,$idNot="")
	{
		
            $this->db->select('u.id_usuario');
            $this->db->select('u.usuario');
            $this->db->select('concat(pp.nombre," ",pp.apellido) as nomape',false);
            $this->db->select('concat(e.abv," - ",pto.puesto) as puesto',false);
            $this->db->from('sys_usuarios u');
            $this->db->join('gr_usuarios gru','gru.id_usuario = u.id_usuario','inner');
            $this->db->join('grl_personas pp','pp.id_persona = u.id_persona','inner');
            $this->db->join('gr_puestos pto','pto.id_puesto = gru.id_puesto','inner');
            $this->db->join('gr_organigramas o','o.id_organigrama = pto.id_organigrama','inner');
            $this->db->join('grl_empresas e','e.id_empresa = o.id_empresa','inner');
            
            if($idNot!="")
                $this->db->where("u.id_usuario !=",$idNot);
            
            $this->db->where("pp.habilitado",1);
            $this->db->where("u.habilitado",1);
            $this->db->where("gru.habilitado",1);
            $this->db->where("(pp.nombre like","'%".$filtro."%'",FALSE);
            $this->db->or_where("pp.apellido like","'%".$filtro."%'",FALSE);
            $this->db->or_where("concat(pp.nombre,' ',pp.apellido) like","'%".$filtro."%')",FALSE);
            $this->db->where('(pto.id_organigrama',2);
            $this->db->or_where('pto.id_organigrama = 3)');
            
            
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
        public function dameDatosSupervisor($id_supervisor)
	{
		$this->db->select("u_sup.id_usuario as id_usuario_supervisor",false); 
                $this->db->select('concat(p_sup.nombre," ",p_sup.apellido) as supervisor',false);
		$this->db->select("pto_sup.id_puesto as id_puesto_supervisor",false); 
		$this->db->select("pto_sup.puesto as puesto_supervisor",false); 
		$this->db->select("a_sup.id_area as id_area_supervisor",false); 
		$this->db->select("a_sup.area as area_supervisor",false); 
//		$this->db->select("e_sup.id_empresa as id_empresa_supervisor",false); 
		$this->db->select("e_sup.empresa as empresa_supervisor",false); 
                
                
		$this->db->from("sys_usuarios u_sup");
                $this->db->join('grl_personas p_sup','p_sup.id_persona = u_sup.id_persona','inner');
                $this->db->join('gr_usuarios gru_sup', 'gru_sup.id_usuario=u_sup.id_usuario', 'inner');
                $this->db->join('gr_puestos pto_sup', 'pto_sup.id_puesto=gru_sup.id_puesto', 'inner');
                $this->db->join('gr_areas a_sup', 'a_sup.id_area=pto_sup.id_area', 'inner');
                $this->db->join('gr_organigramas o_sup', 'o_sup.id_organigrama=a_sup.id_organigrama', 'inner');
                $this->db->join('grl_empresas e_sup', 'e_sup.id_empresa=o_sup.id_empresa', 'inner');
                
                $this->db->where('u_sup.habilitado',1);
                $this->db->where('p_sup.habilitado',1);
                $this->db->where('u_sup.id_usuario',$id_supervisor);
//                $this->db->limit(10,0); 
		$query = $this->db->get();
		$num = $query->num_rows();
		$res = $query->result_array();
		if ($num > 0)
		{
			return $res[0];
		}
		else
			return 0;
	}
        public function dameTodasVerificadas()
        {
            $this->db->select('ea.id_usuario,ea.usuario,ea.id_puesto,ea.puesto,ea.id_area,ea.area,ea.id_empresa,ea.empresa');
            $this->db->select('ea.id_usuario_supervisor,ea.supervisor,ea.id_puesto_supervisor,ea.puesto_supervisor,ea.id_area_supervisor,ea.area_supervisor,ea.id_empresa_supervisor,ea.empresa_supervisor');
            $this->db->select('ea.id_periodo,ea.fecha_alta');
            $this->db->from('ed_evaluaciones_alta ea');
            $this->db->order_by("ea.id_alta", "asc"); 
            $this->db->where('ea.verificado',1);
        }
        public function dameAltaParaIniciar($id)
        {
            $this->db->select('ea.id_usuario,ea.usuario,ea.id_puesto,ea.puesto,ea.id_area,ea.area,ea.id_empresa,ea.empresa');
            $this->db->select('ea.id_usuario_supervisor,ea.supervisor,ea.id_puesto_supervisor,ea.puesto_supervisor,ea.id_area_supervisor,ea.area_supervisor,ea.id_empresa_supervisor,ea.empresa_supervisor');
            $this->db->select('ea.id_periodo');
            $this->db->from('ed_evaluaciones_alta ea');
            $this->db->where('ea.id_alta',$id);
            $this->db->where('ea.verificado',1);
            $query = $this->db->get();
            $num = $query->num_rows();
            $res = $query->result_array();
            if ($num > 0)
		{
			return $res[0];
		}
		else
			return 0;
            
        }
        public function dameAltaPorId($id)
        {
            $this->db->select('*');
            $this->db->from('ed_evaluaciones_alta ea');
            $this->db->where('ea.id_alta',$id);
            $query = $this->db->get();
            $num = $query->num_rows();
            $res = $query->result_array();
            if ($num > 0)
		{
			return $res[0];
		}
		else
			return 0;
            
        }
        public function dameIdsVerificadasYNoDuplicados()
        {
            $this->db->select('ea.id_alta');
            $this->db->from('ed_evaluaciones_alta ea');
            $this->db->order_by("ea.id_alta", "asc"); 
            $this->db->where('ea.verificado',1);
            $this->db->where('ea.duplicado',0);
            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $query->num_rows();
            $res = $query->result_array();
            if ($num > 0)
            {
                foreach($res as $value)
                    $array[]=$value['id_alta'];
                return $array;
            }
            else
                return 0;
        }
        public function verificarParaIniciar()
        {
            //Selecciona todos los registros de alta que ya tengan ED para el mismo usuario-supervisor-periodo
            $this->db->select('ea.id_alta');
//            $this->db->select('ea.id_usuario_supervisor');
            $this->db->from('ed_evaluaciones_alta ea');
            $this->db->where('ea.id_usuario in (select id_usuario from ed_evaluaciones e where e.id_usuario=ea.id_usuario and e.id_usuario_supervisor=ea.id_usuario_supervisor and e.id_periodo=ea.id_periodo)');
            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $query->num_rows();
            $res = $query->result_array();
            if ($num > 0)
            {
                foreach($res as $value)
                    $array[]=$value['id_alta'];
                return $array;
            }
            else
                return 0;
        }
        public function controlarDuplicados()
        {
            //Selecciona todos los registros de alta que ya tengan ED para el mismo usuario-supervisor-periodo
            $this->db->select('ea.id_alta');
//            $this->db->select('ea.id_usuario_supervisor');
            $this->db->from('ed_evaluaciones_alta ea');
            $this->db->where('verificado',1);
            $this->db->where('duplicado',1);
            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $query->num_rows();
            $res = $query->result_array();
            if ($num > 0)
            {
                foreach($res as $value)
                    $array[]=$value['id_alta'];
                return $array;
            }
            else
                return 0;
        }
        public function verificarUsuario($id_usuario,$id_periodo)
        {
            $this->db->select('ea.id_alta');
            $this->db->from('ed_evaluaciones_alta ea');
            $this->db->where('ea.id_usuario',$id_usuario);
            $this->db->where('ea.id_periodo',$id_periodo);
            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $query->num_rows();
            if ($num > 0)
                return 1;
            else
                return 0;
        }
        public function copiarVerificados()
        {
            $sql="INSERT INTO ed_evaluaciones (id_usuario,usuario,id_puesto,puesto,id_area,area,id_empresa,empresa,id_usuario_supervisor,supervisor,id_puesto_supervisor,puesto_supervisor,id_area_supervisor,area_supervisor,id_empresa_supervisor,empresa_supervisor,id_periodo,fecha_alta,id_estado,id_avance)"; 
            $sql .=" SELECT ea.id_usuario,ea.usuario,ea.id_puesto,ea.puesto,ea.id_area,ea.area,ea.id_empresa,ea.empresa,ea.id_usuario_supervisor,ea.supervisor,ea.id_puesto_supervisor,ea.puesto_supervisor,ea.id_area_supervisor,ea.area_supervisor,ea.id_empresa_supervisor,ea.empresa_supervisor,ea.id_periodo,ea.fecha_alta,1,1"; 
            $sql .=" from ed_evaluaciones_alta ea";
            $sql .=" where ea.verificado=1 and duplicado=0";
            $copy=$this->db->query($sql);
//            echo $this->db->last_query();
//            return $copy;
            if(!$copy)
                    return false;
            else
                    return true;
		
        }
}	
?>