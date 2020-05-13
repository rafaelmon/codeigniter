<?php
class Gr_usuarios_model extends CI_Model
{
	
        
        public function dameUsuarioHabilitadoPorId($id)
	{
            $this->db->select('u.id_usuario,u.id_puesto');
            $this->db->select('u2.email,u2.mailing');
            $this->db->select('p.genero');
             $this->db->select('concat(p.nombre," ",p.apellido) as persona',false);
             $this->db->select('concat(p.nombre," ",p.apellido," <",u2.email,">") as para',false);
            $this->db->select('pto.id_puesto_superior,pto.id_area,pto.realiza_omc,pto.gte_corporativo');
            $this->db->select('concat(e.abv," - ",pto.puesto) as puesto',false);
            $this->db->select('o.id_empresa');
            $this->db->select('(select count(su.id_usuario) from gr_usuarios su inner join gr_puestos sp on sp.id_puesto=su.id_puesto where sp.id_puesto_superior=u.id_puesto and su.habilitado=1) as q_dr',false);
            $this->db->select('a.id_area,a.gr');
            $this->db->from('gr_usuarios u');
            $this->db->join('sys_usuarios u2','u2.id_usuario = u.id_usuario','inner');
            $this->db->join('grl_personas p','p.id_persona = u2.id_persona','inner');
            $this->db->join('gr_puestos pto','pto.id_puesto = u.id_puesto','inner');
            $this->db->join('gr_areas a','pto.id_area = a.id_area','inner');
            $this->db->join('gr_organigramas o','o.id_organigrama = pto.id_organigrama','inner');
            $this->db->join('grl_empresas e','e.id_empresa = o.id_empresa','inner');
            $this->db->where("u.id_usuario",$id);
            $this->db->where("u.habilitado",1);
            $query = $this->db->get();
             $res = $query->result_array();
//            $res = $query->row();
            $num=count($res);
            if ($num > 0)
            {
                    return $res[0];
            }
            else
                    return 0;
        }
        public function dameUsuarioPorId($id)
	{
            $this->db->select('u.id_usuario,u.id_puesto,u.habilitado,pto.id_puesto_superior');
            $this->db->select('u2.email,u2.mailing');
            $this->db->select('p.genero');
             $this->db->select('concat(p.nombre," ",p.apellido) as persona',false);
            $this->db->select('concat(e.abv," - ",pto.puesto) as puesto',false);
            $this->db->select('o.id_empresa');
            $this->db->select('pto.realiza_omc');
            $this->db->select('a.id_area,a.gr');
            $this->db->from('gr_usuarios u');
            $this->db->join('sys_usuarios u2','u2.id_usuario = u.id_usuario','inner');
            $this->db->join('grl_personas p','p.id_persona = u2.id_persona','inner');
            $this->db->join('gr_puestos pto','pto.id_puesto = u.id_puesto','inner');
            $this->db->join('gr_areas a','pto.id_area = a.id_area','inner');
            $this->db->join('gr_organigramas o','o.id_organigrama = pto.id_organigrama','inner');
            $this->db->join('grl_empresas e','e.id_empresa = o.id_empresa','inner');
            $this->db->where("u.id_usuario",$id);
            $query = $this->db->get();
             $res = $query->result_array();
//            $res = $query->row();
            $num=count($res);
            if ($num > 0)
            {
                    return $res[0];
            }
            else
                    return 0;
		
	}
        public function dameUsuariosPorPuestos($idPuestos,$idUsuarioNot="")
	{
            $this->db->select('u.id_usuario,u.id_puesto');
            $this->db->select('uu.email,uu.mailing');
             $this->db->select('concat(p.nombre," ",p.apellido) as persona',false);
             $this->db->select('concat(p.nombre," ",p.apellido," <",uu.email,">") as para',false);
            $this->db->select('pp.id_puesto_superior');
            $this->db->from('gr_usuarios u');
            $this->db->join('sys_usuarios uu','uu.id_usuario = u.id_usuario','inner');
            $this->db->join('grl_personas p','p.id_persona = uu.id_persona','inner');
            $this->db->join('gr_puestos pp','pp.id_puesto = u.id_puesto','inner');
            $this->db->where("u.habilitado",1);
            $this->db->where("uu.habilitado",1);
            $this->db->where_in("pp.id_puesto",$idPuestos);
            if ($idUsuarioNot!="")
                $this->db->where("u.id_usuario !=",$idUsuarioNot);
            $this->db->orderby("pp.id_puesto",'asc');
            $query = $this->db->get();
             $res = $query->result_array();
//            $res = $query->row();
            $num=count($res);
            if ($num > 0)
            {
                    return $res;
            }
            else
                    return 0;
		
	}
        public function dameUsuariosGr($id_areas_gr)
	{
            $this->db->select('u.id_usuario');
            $this->db->select('uu.email, uu.mailing');
             $this->db->select('concat(p.nombre," ",p.apellido) as persona',false);
             $this->db->select('concat(p.nombre," ",p.apellido," <",uu.email,">") as para',false);
            $this->db->select('pp.id_puesto_superior');
            $this->db->from('gr_usuarios u');
            $this->db->join('sys_usuarios uu','uu.id_usuario = u.id_usuario','inner');
            $this->db->join('grl_personas p','p.id_persona = uu.id_persona','inner');
            $this->db->join('gr_puestos pp','pp.id_puesto = u.id_puesto','inner');
            $this->db->join('gr_organigramas o','o.id_organigrama = pp.id_organigrama','inner');
            $this->db->join('grl_empresas e','e.id_empresa = o.id_empresa','inner');
            $this->db->where("u.habilitado",1);
            $this->db->where("uu.habilitado",1);
            $this->db->where_in("pp.id_area",$id_areas_gr);
            $this->db->orderby("pp.id_puesto",'asc');
            $query = $this->db->get();
//            echo $this->db->last_query();
             $res = $query->result_array();
//            $res = $query->row();
            $num=count($res);
            if ($num > 0)
            {
                    return $res;
            }
            else
                    return 0;
		
	}
        public function dameUsuariosGrPorEmpresa($id_areas_gr,$id_empresa)
	{
            $this->db->select('u.id_usuario');
            $this->db->select('uu.email,uu.mailing');
             $this->db->select('concat(p.nombre," ",p.apellido) as persona',false);
             $this->db->select('concat(p.nombre," ",p.apellido," <",uu.email,">") as para',false);
            $this->db->select('pp.id_puesto_superior');
            $this->db->from('gr_usuarios u');
            $this->db->join('sys_usuarios uu','uu.id_usuario = u.id_usuario','inner');
            $this->db->join('grl_personas p','p.id_persona = uu.id_persona','inner');
            $this->db->join('gr_puestos pp','pp.id_puesto = u.id_puesto','inner');
            $this->db->join('gr_organigramas o','o.id_organigrama = pp.id_organigrama','inner');
            $this->db->join('grl_empresas e','e.id_empresa = o.id_empresa','inner');
            $this->db->where("u.habilitado",1);
            $this->db->where("uu.habilitado",1);
            $this->db->where("e.id_empresa",$id_empresa);
            $this->db->where_in("pp.id_area",$id_areas_gr);
            $this->db->orderby("pp.id_puesto",'asc');
            $query = $this->db->get();
//            echo $this->db->last_query();
             $res = $query->result_array();
//            $res = $query->row();
            $num=count($res);
            if ($num > 0)
            {
                    return $res;
            }
            else
                    return 0;
		
	}
        public function dameUsuariosInicioBcCombo($limit,$start,$filtro,$idNot="")
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
            
            $this->db->where("u.habilitado",1);
            $this->db->where("uu.habilitado",1);
            $this->db->where("p.habilitado",1);
            $this->db->where("(p.nombre like","'%".$filtro."%'",FALSE);
            $this->db->or_where("p.apellido like","'%".$filtro."%'",FALSE);
            $this->db->or_where("concat(p.nombre,' ',p.apellido) like","'%".$filtro."%')",FALSE);
            $subSql="(select distinct sp.id_puesto_superior from gr_puestos sp inner join gr_usuarios su on su.id_puesto=sp.id_puesto inner join sys_usuarios suu on suu.id_usuario=su.id_usuario where sp.id_puesto_superior is not null and su.habilitado=1 and suu.habilitado=1)";
//            $this->db->where_in("pto.id_puesto",$subSql,FALSE);
            $this->db->where("pto.id_puesto in",$subSql,FALSE);
            
            $this->db->order_by("p.nombre", "asc"); 
            $this->db->order_by("p.apellido", "asc"); 
            
            $this->db->limit($limit=5,$start=0); 
            
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
        public function dameUsuarioIdPuesto($id_usuario)
	{
            $this->db->select('p.id_puesto');
            $this->db->from('gr_usuarios u');
            $this->db->join('gr_puestos p','p.id_puesto = u.id_puesto','inner');
            $this->db->where("u.id_usuario",$id_usuario);
            $query = $this->db->get();
            $res = $query->row();
            $num=count($res);
            if ($num > 0)
            {
                    return $res->id_puesto;
            }
            else
                    return 0;
		
	}
        public function dameIdUsuarioSuperior($id_usuario)
	{
            $this->db->select('u2.id_usuario');
            $this->db->from('gr_usuarios u');
            $this->db->join('gr_puestos p','p.id_puesto = u.id_puesto','inner');
            $this->db->join('gr_usuarios u2','u2.id_puesto = p.id_puesto_superior','inner');
            $this->db->join('sys_usuarios su','su.id_usuario = u2.id_usuario','inner');
            $this->db->where("u.id_usuario",$id_usuario);
            $this->db->where("u2.habilitado",1);
            $this->db->where("su.habilitado",1);
            $query = $this->db->get();
            $res = $query->row();
            $num=count($res);
            if ($num > 0)
            {
                    return $res->id_usuario;
            }
            else
                    return 0;
		
	}
        public function dameEmailAreasInferiores($areas)
	{
            $this->db->select('uu.email,uu.mailing');
            $this->db->from('gr_usuarios u');
            $this->db->join('sys_usuarios uu','uu.id_usuario = u.id_usuario','inner');
            $this->db->join('gr_puestos p','p.id_puesto = u.id_puesto','inner');
            $this->db->where("u.habilitado",1);
            $this->db->where("uu.habilitado",1);
            $this->db->where_in("p.id_area",$areas);
            $query = $this->db->get();
//            echo $this->db->last_query();
            $res = $query->result_array();
            $num=count($res);
            if ($num > 0)
            {
                    return $res;
            }
            else
                    return 0;
		
	}
        public function dameArea($id_usuario)
	{
            $this->db->select('a.id_area,a.area,a.gr,a.id_gcia');
            $this->db->from('gr_usuarios u');
            $this->db->join('sys_usuarios uu','uu.id_usuario = u.id_usuario','inner');
            $this->db->join('gr_puestos p','p.id_puesto = u.id_puesto','inner');
            $this->db->join('gr_areas a','a.id_area = p.id_area','inner');
            $this->db->where("u.habilitado",1);
            $this->db->where("uu.habilitado",1);
            $this->db->where("u.id_usuario",$id_usuario);
            $query = $this->db->get();
//            echo $this->db->last_query();
            $res = $query->result_array();
            $num=count($res);
            if ($num > 0)
            {
                    return $res[0];
            }
            else
                    return 0;
		
	}
         public function verificarUsuarioGr($id_usuario,$areas)
        {
            $this->db->select('count(u.id_usuario) as cant',false);
            $this->db->from('gr_usuarios u');
            $this->db->join('gr_puestos p','p.id_puesto = u.id_puesto','inner');
            $this->db->where("u.id_usuario",$id_usuario);
            $this->db->where_in("p.id_area",$areas);
            $query = $this->db->get();
//            echo $this->db->last_query();
            $res = $query->row();
            $num=$res->cant;
            if ($num > 0)
            {
                    return 1;
            }
            else
                    return 0;
        }
         public function verificarSiPerteneceGr($id_usuario)
        {
            $this->db->select('count(u.id_usuario) as cant',false);
            $this->db->from('gr_usuarios u');
            $this->db->join('gr_puestos p','p.id_puesto = u.id_puesto','inner');
            $this->db->join('gr_areas a','a.id_area = p.id_area','inner');
            $this->db->where("u.id_usuario",$id_usuario);
            $this->db->where("a.gr",1);
            $query = $this->db->get();
//            echo $this->db->last_query();
            $res = $query->row();
            $num=$res->cant;
            if ($num > 0)
            {
                    return 1;
            }
            else
                    return 0;
        }
        public function dameDirectReports($id_puesto_superior)
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
            
            $this->db->where("u.habilitado",1);
            $this->db->where("uu.habilitado",1);
            $this->db->where("p.habilitado",1);
            $this->db->where("u.habilitado",1);
            $this->db->where("pto.id_puesto_superior",$id_puesto_superior);
            
            $this->db->order_by("p.nombre", "asc"); 
            $this->db->order_by("p.apellido", "asc"); 
            
            $query = $this->db->get();
//            echo $this->db->last_query();
//             $num = $this->cantSql('u.id_usuario',$this->db->last_query());
            $res = $query->result_array();
            $num=count($res);
                if ($num > 0)
                {
                        return $res;
                }
                else
                        return 0;
		
	}
        public function dameArbolInferior($id_usuario_inicio,$id_bc)
	{
            $this->db->select('u.id_usuario');
            $this->db->select('concat(p.nombre," ",p.apellido) as nomape',false);
            $this->db->select('pto.id_puesto');
            $this->db->select('pto.id_puesto_superior');
            $this->db->select("(select id_estado from gr_tareas t where t.id_tipo_herramienta=3 and id_herramienta=$id_bc and usuario_relacionado=u.id_usuario) as estado",false);
            $this->db->select('NULL as dr',false);
            
            
            $this->db->from('gr_usuarios u');
            
            $this->db->join('sys_usuarios uu','uu.id_usuario = u.id_usuario','inner');
            $this->db->join('grl_personas p','p.id_persona = uu.id_persona','inner');
            $this->db->join('gr_puestos pto','pto.id_puesto = u.id_puesto','inner');
            $this->db->join('gr_puestos pto2','pto2.id_puesto = pto.id_puesto_superior','inner');
            $this->db->join('gr_usuarios u2','u2.id_puesto = pto2.id_puesto','inner');
            
            $this->db->where("u.habilitado",1);
//            $this->db->where("uu.habilitado",1);
            $this->db->where("u2.id_usuario",$id_usuario_inicio);
            
            $this->db->order_by("id_usuario", "asc"); 
            
            $query = $this->db->get();
//            echo $this->db->last_query();
//             $num = $this->cantSql('u.id_usuario',$this->db->last_query());
            $res = $query->result_array();
            $num=count($res);
                if ($num > 0)
                {
                        return $res;
                }
                else
                        return 0;
		
	}
        public function dameListadoInferior($id_usuario_inicio)
	{
            $this->db->select('u.id_usuario');
            $this->db->from('gr_usuarios u');
            
            $this->db->join('sys_usuarios uu','uu.id_usuario = u.id_usuario','inner');
            $this->db->join('grl_personas p','p.id_persona = uu.id_persona','inner');
            $this->db->join('gr_puestos pto','pto.id_puesto = u.id_puesto','inner');
            $this->db->join('gr_puestos pto2','pto2.id_puesto = pto.id_puesto_superior','inner');
            $this->db->join('gr_usuarios u2','u2.id_puesto = pto2.id_puesto','inner');
            
            $this->db->where("u.habilitado",1);
//            $this->db->where("uu.habilitado",1);
            $this->db->where("u2.id_usuario",$id_usuario_inicio);
            
            $this->db->order_by("id_usuario", "asc"); 
            
            $query = $this->db->get();
//            echo $this->db->last_query();
//             $num = $this->cantSql('u.id_usuario',$this->db->last_query());
            $res = $query->result_array();
            $num=count($res);
                if ($num > 0)
                {
                        return $res;
                }
                else
                        return 0;
		
	}
        public function dameArbol ($id_usuario_inicio)
        {
            $this->db->select('u.id_usuario');
            $this->db->select('pto.puesto as puesto',false);
            $this->db->select('e.abv as empresa',false);
            $this->db->select('concat(p.nombre," ",p.apellido) as nomape',false);
            $this->db->select('(select count(su.id_usuario) from gr_usuarios su inner join gr_puestos sp on sp.id_puesto=su.id_puesto where sp.id_puesto_superior=u.id_puesto and su.habilitado=1) as q_dr',false);
            $this->db->from('gr_usuarios u');
            $this->db->join('sys_usuarios uu','uu.id_usuario = u.id_usuario','inner');
            $this->db->join('grl_personas p','p.id_persona = uu.id_persona','inner');
            $this->db->join('gr_puestos pto','pto.id_puesto = u.id_puesto','inner');
            $this->db->join('gr_usuarios u2','u2.id_puesto = pto.id_puesto_superior','inner');
            $this->db->join('gr_organigramas o','o.id_organigrama = pto.id_organigrama','inner');
            $this->db->join('grl_empresas e','e.id_empresa = o.id_empresa','inner');
            $this->db->where("u.habilitado",1);
            $this->db->where("uu.habilitado",1);
            $this->db->where("p.habilitado",1);
            $this->db->where("u.habilitado",1);
            $this->db->where("u2.id_usuario",$id_usuario_inicio);
            $query = $this->db->get();
//            echo $this->db->last_query();
//             $num = $this->cantSql('u.id_usuario',$this->db->last_query());
            $res = $query->result_array();
            $num=count($res);
                if ($num > 0)
                {
                        return $res;
                }
                else
                        return 0;
            
        }
        public function checkRealizaOMC($id_usuario)
	{
            $this->db->select('u.id_usuario');
            $this->db->from('gr_usuarios u');
            $this->db->join('gr_puestos p','p.id_puesto = u.id_puesto','inner');
            $this->db->where("u.id_usuario",$id_usuario);
            $this->db->where("u.habilitado",1);
            $this->db->where("p.realiza_omc",1);
            $query = $this->db->get();
            $res = $query->row();
            $num=count($res);
            if ($num > 0)
            {
                    return 1;
            }
            else
                    return 0;
		
	}
         public function usuariosRealizaOmcCombo($limit,$start,$filtro,$idNot="",$id_usuario_acomp="")
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
            if($id_usuario_acomp!="")
                $this->db->where_not_in("u.id_usuario",$id_usuario_acomp);
            
            
            
            $this->db->where("pp.habilitado",1);
            $this->db->where("u.habilitado",1);
            $this->db->where("uu.habilitado",1);
//            $this->db->where("pto.realiza_omc",1);
            $this->db->where("(pp.nombre like","'%".$filtro."%'",FALSE);
            $this->db->or_where("pp.apellido like","'%".$filtro."%'",FALSE);
            $this->db->or_where("concat(pp.nombre,' ',pp.apellido) like","'%".$filtro."%')",FALSE);
            
//            $this->db->order_by("pp.nombre", "asc"); 
//            $this->db->order_by("pp.apellido", "asc"); 
            $this->db->order_by("concat(pp.nombre,' ',pp.apellido)", "asc"); 
            
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
        
        public function dameUsuarioPermisoGdr($id_usuario)
	{
            $this->db->select('u.id_usuario');
            $this->db->from('gr_usuarios u');
            $this->db->where("u.id_usuario",$id_usuario);
            $this->db->where("u.gdr",1);
            $query = $this->db->get();
//            echo $this->db->last_query();
            $res = $query->row();
            $num=count($res);
            if ($num > 0)
            {
                    return $res->id_usuario;
            }
            else
                    return 0;
		
	}
        
        public function listado($start, $limit, $filtro, $sort="", $dir="")
	{
		
            $this->db->select('u.id_usuario,u.gdr');
            $this->db->select('p.puesto');
            $this->db->select('concat(pp.nombre,", ",pp.apellido) as persona',false);
            $this->db->from('gr_usuarios u');
            $this->db->join('sys_usuarios su','su.id_usuario = u.id_usuario','inner');
            $this->db->join('grl_personas pp','pp.id_persona = su.id_persona','inner');            
            $this->db->join('gr_puestos p','p.id_puesto = u.id_puesto','inner');            
            $this->db->where("u.habilitado",1); 
            $this->db->where("su.habilitado",1); 
            $this->db->where("pp.habilitado",1); 

            if ($filtro!="")
            {
                $this->db->where("(pp.nombre like","'%".$filtro."%'",FALSE); 
                $this->db->or_where("pp.apellido like","'%".$filtro."%'",FALSE);
                $this->db->or_where("concat(pp.nombre,' ',pp.apellido) like","'%".$filtro."%')",FALSE); 
                $this->db->or_where("p.puesto like","'%".$filtro."%'",FALSE);
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
                    default:
                        $ordenar="u.id_usuario";
                        break;

                }
                $this->db->order_by($ordenar, $dir);
            }
            else
            {
                $this->db->order_by("concat(pp.nombre,", ",pp.apellido)", "asc");
            }
            
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

        
}	
?>