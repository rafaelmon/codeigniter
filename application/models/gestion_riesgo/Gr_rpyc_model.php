<?php
class gr_rpyc_model extends CI_Model
{
    public function listado($usuario, $start, $limit, $filtros, $busqueda, $campos,$sort="", $dir="")
    {
		
            $this->db->select('r.id_rpyc,r.id_usuario_alta,r.q_usuarios,r.q_contratistas,r.programada,r.realizada,r.tareas,r.sectores');
            $this->db->select("concat(p.nombre,' ',p.apellido) as usuarioAlta",false);
//            $this->db->select("concat(p3.nombre,' ',p3.apellido) as acomp2",false);
            $this->db->select("DATE_FORMAT(r.fecha_alta,'%d/%m/%Y') as fecha_alta", FALSE);
//            $this->db->select("concat(e.abv,' | ',s.area) as area",false);
            $this->db->select("IF (r.tareas=0,'No','Si') as ttareas",false);
//            $this->db->select("(SELECT GROUP_CONCAT(DISTINCT aa.area SEPARATOR '; ') from gr_rel_areas_rpyc srs
//                    inner join gr_areas_rpyc aa on aa.id_area=srs.id_area
//                    where srs.id_rpyc=r.id_rpyc)as areas",false);
            $this->db->from('gr_rpyc r');
            $this->db->join('gr_usuarios gu','gu.id_usuario = r.id_usuario_alta','inner');
            $this->db->join('sys_usuarios u','u.id_usuario = gu.id_usuario','inner');
            $this->db->join('grl_personas p','p.id_persona = u.id_persona','left');
            $this->db->join('gr_puestos pto','pto.id_puesto = gu.id_puesto','inner');
//            $this->db->join('gr_areas s','s.id_area = r.id_area','inner');
//            $this->db->join('grl_empresas e','e.id_empresa = s.id_empresa','inner');
            
//             if ($filtros!="")
//            {
//                if (isset($filtros[0]) && $filtros[0]!="")
//                    $this->db->where('t.id_estado',$filtros[0]);
//                if (isset($filtros[1]) && $filtros[1]!="")
//                    $this->db->where('t.id_tipo_herramienta',$filtros[1]);
//            }
            if ($busqueda!="" && count($campos)>0)
            {
                foreach ($campos as &$campo)
                {
                    switch ($campo)
                    {
                        case 'usuario_alta':
                            $campo="concat(p.nombre,' ',p.apellido)";
                            break;
                        case 'areas':
//                            $campo="concat(e.abv,' | ',s.area)";
                            $campo='sectores';
//                                "(SELECT GROUP_CONCAT(DISTINCT aa.area SEPARATOR ' ') from gr_rel_areas_rpyc srs
//                    inner join gr_areas_rpyc ss on aa.id_area=srs.id_area
//                    where srs.id_rpyc=r.id_rpyc)";
                            break;
//                        default :
//                            unset($campo);
//                            break;
                    }
                }
                unset($campo);
    //                echo "<pre>". print_r($campos,true)."<pre>";
                //si viene un solo campo de busqueda
                if(count($campos)==1)
                    $this->db->where("$campos[0] like","'%".$busqueda."%'",FALSE);
                else
                {
                    $n=0;
                    foreach ($campos as $campo)
                    {
                        if ($n==0)
                            $this->db->where("($campo like","'%".$busqueda."%'",FALSE);
                        else
                        {
                            if ($n==count($campos)-1)
                                $this->db->or_where("$campo like","'%".$busqueda."%')",FALSE);
                            else
                                $this->db->or_where("$campo like","'%".$busqueda."%'",FALSE);


                        }
                    $n++;     

                    }

                }

            }//fin if de Busqueda
//            if($usuario['gr']!=1)
//            {
////                $this->db->where_in('(pto2.id_area',$usuario['areas_inferiores'],FALSE);
////                  $this->db->where_in('ptr.id_area',$usuario['areas_inferiores'],FALSE);
//                $this->db->where("ptr.id_area in(".$usuario['areas_inferiores'].")");
//
//            }
        if ($sort!="")
        {
            switch ($sort)
            {
                case 'id_rpyc':
                case 'fecha_alta':
                    $ordenar="r.".$sort;
                    break;
                case 'usuario_alta':
                    $ordenar="concat(p.nombre,p.apellido)";
                    break;
                case 'q_contratistas':
                    $ordenar="r.q_contratistas";
                    break;
                case 'q_usuarios':
                    $ordenar="r.q_usuarios";
                    break;
                case 'tareas':
                    $ordenar="tareas";
                    break;
                default:
                    $ordenar="r.id_rpyc";
            }
            $this->db->order_by($ordenar, $dir);
        }
        else
            $this->db->order_by("r.id_rpyc", "desc"); 
        $this->db->limit($limit,$start); 

        $query = $this->db->get();
//        echo $this->db->last_query();
        $num = $this->cantSql('r.id_rpyc',$this->db->last_query());
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
//            echo $this->db->last_query();
            $res = $query->result();
            return $res[0]->cantidad;
        }
         public function verificaUsuarioAlta($id_usuario,$id_rpyc)
        {
            $this->db->select('count(id_rpyc) as cant',false);
            $this->db->from('gr_rpyc');
            $this->db->where("id_rpyc",$id_rpyc);
            $this->db->where("id_usuario_alta",$id_usuario);
            $query = $this->db->get();
//            echo $this->db->last_query();
            $res = $query->row();
            $num=count($res);
            if ($num > 0)
            {
                    return 1;
            }
            else
                    return 0;
        }
         public function verificaRePost($datos)
        {
            $this->db->select('count(id_rpyc) as cant',false);
            $this->db->from('gr_rpyc');
            $this->db->where("id_usuario_alta",$datos['id_usuario_alta']);
            $this->db->where("q_usuarios",$datos['q_usuarios']);
            $this->db->where("q_contratistas",$datos['q_contratistas']);
            $this->db->where("TIME_TO_SEC(TIMEDIFF( CURRENT_TIMESTAMP,fecha_alta)) <",10);//que el último insert no haya sido anterior a 10seg
            $query = $this->db->get();
//            echo $this->db->last_query();
            $res = $query->row();
            $num=count($res);
            if ($num > 0)
            {
                    return $res->cant;
            }
            else
                    return 0;
        }
        public function insert($datos)
	{
		$this->db->trans_begin();
		
		$this->db->insert('gr_rpyc',$datos);
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
			return $last;
		}
	}
         public function dameRpycDatosMail($id)
	{
            $this->db->select("r.id_rpyc");
//            $this->db->select("concat(p.nombre,' ',p.apellido) as observador",false);
            $this->db->select("DATE_FORMAT(r.fecha_alta,'%d/%m/%Y') as fecha_alta", FALSE);
            $this->db->select("(SELECT GROUP_CONCAT(DISTINCT aa.area SEPARATOR '; ') from gr_rel_areas_rpyc srs
                    inner join gr_areas_rpyc aa on aa.id_area=srs.id_area
                    where srs.id_rpyc=r.id_rpyc)as areas",false);
            $this->db->from('gr_rpyc r');
            $this->db->where("r.id_rpyc",$id);
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
        public function update($id,$datos)
	{
            $this->db->where('id_rpyc', $id);
            $edit=$this->db->update('gr_rpyc', $datos);
//            echo $this->db->last_query();
            if(!$edit)
                    return false;
            else
                    return true;
		
	}
        
        public function dameSectores($areas)
	{
            $this->db->select("GROUP_CONCAT(DISTINCT aa.area SEPARATOR '; ') sectores",false);
            $this->db->from('gr_areas_rpyc aa');
            $this->db->where_in("aa.id_area",$areas);
            $query = $this->db->get();
            $res = $query->result_array();
//            $res = $query->row();
//            $num=count($res);
//            if ($num > 0)
//            {
                    return $res;
//            }
//            else
//                    return 0;
		
	}
        
    public function listado_excel($usuario, $start, $limit, $filtros, $busqueda, $campos,$sort="", $dir="")
    {
		
            $this->db->select('r.id_rpyc');
            $this->db->select("DATE_FORMAT(r.fecha_alta,'%d/%m/%Y') as fecha_alta", FALSE);
            $this->db->select("concat(p.nombre,' ',p.apellido) as usuarioAlta",false);
            $this->db->select('r.sectores,r.q_usuarios,r.q_contratistas');
//            $this->db->select("concat(p3.nombre,' ',p3.apellido) as acomp2",false);
//            $this->db->select("concat(e.abv,' | ',s.area) as area",false);
            $this->db->select("IF (r.tareas=0,'No','Si') as ttareas",false);
//            $this->db->select("(SELECT GROUP_CONCAT(DISTINCT aa.area SEPARATOR '; ') from gr_rel_areas_rpyc srs
//                    inner join gr_areas_rpyc aa on aa.id_area=srs.id_area
//                    where srs.id_rpyc=r.id_rpyc)as areas",false);
            $this->db->from('gr_rpyc r');
            $this->db->join('gr_usuarios gu','gu.id_usuario = r.id_usuario_alta','inner');
            $this->db->join('sys_usuarios u','u.id_usuario = gu.id_usuario','inner');
            $this->db->join('grl_personas p','p.id_persona = u.id_persona','left');
            $this->db->join('gr_puestos pto','pto.id_puesto = gu.id_puesto','inner');
//            $this->db->join('gr_areas s','s.id_area = r.id_area','inner');
//            $this->db->join('grl_empresas e','e.id_empresa = s.id_empresa','inner');
            
//             if ($filtros!="")
//            {
//                if (isset($filtros[0]) && $filtros[0]!="")
//                    $this->db->where('t.id_estado',$filtros[0]);
//                if (isset($filtros[1]) && $filtros[1]!="")
//                    $this->db->where('t.id_tipo_herramienta',$filtros[1]);
//            }
            if ($busqueda!="" && count($campos)>0)
            {
                foreach ($campos as &$campo)
                {
                    switch ($campo)
                    {
                        case 'usuario_alta':
                            $campo="concat(p.nombre,' ',p.apellido)";
                            break;
                        case 'areas':
//                            $campo="concat(e.abv,' | ',s.area)";
                            $campo='sectores';
//                                "(SELECT GROUP_CONCAT(DISTINCT aa.area SEPARATOR ' ') from gr_rel_areas_rpyc srs
//                    inner join gr_areas_rpyc ss on aa.id_area=srs.id_area
//                    where srs.id_rpyc=r.id_rpyc)";
                            break;
//                        default :
//                            unset($campo);
//                            break;
                    }
                }
                unset($campo);
    //                echo "<pre>". print_r($campos,true)."<pre>";
                //si viene un solo campo de busqueda
                if(count($campos)==1)
                    $this->db->where("$campos[0] like","'%".$busqueda."%'",FALSE);
                else
                {
                    $n=0;
                    foreach ($campos as $campo)
                    {
                        if ($n==0)
                            $this->db->where("($campo like","'%".$busqueda."%'",FALSE);
                        else
                        {
                            if ($n==count($campos)-1)
                                $this->db->or_where("$campo like","'%".$busqueda."%')",FALSE);
                            else
                                $this->db->or_where("$campo like","'%".$busqueda."%'",FALSE);


                        }
                    $n++;     

                    }

                }

            }//fin if de Busqueda
//            if($usuario['gr']!=1)
//            {
////                $this->db->where_in('(pto2.id_area',$usuario['areas_inferiores'],FALSE);
////                  $this->db->where_in('ptr.id_area',$usuario['areas_inferiores'],FALSE);
//                $this->db->where("ptr.id_area in(".$usuario['areas_inferiores'].")");
//
//            }
        if ($sort!="")
        {
            switch ($sort)
            {
                case 'id_rpyc':
                case 'fecha_alta':
                    $ordenar="r.".$sort;
                    break;
                case 'usuario_alta':
                    $ordenar="concat(p.nombre,p.apellido)";
                    break;
                case 'q_contratistas':
                    $ordenar="r.q_contratistas";
                    break;
                case 'q_usuarios':
                    $ordenar="r.q_usuarios";
                    break;
                case 'tareas':
                    $ordenar="tareas";
                    break;
                default:
                    $ordenar="r.id_rpyc";
            }
            $this->db->order_by($ordenar, $dir);
        }
        else
            $this->db->order_by("r.id_rpyc", "desc"); 
        $this->db->limit($limit,$start); 

        $query = $this->db->get();
//         echo $this->db->last_query();
        $num = $query->num_rows();
        $res = $query->result_array();

        if ($num > 0)
            return $res;
        else
            return 0;
    }
        
}	
?>