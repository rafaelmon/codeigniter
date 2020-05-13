<?php
class Gr_omc_model extends CI_Model
{
    public function listado($usuario, $start, $limit, $filtros, $busqueda, $campos,$sort="", $dir="")
    {
//            echo "<pre>". print_r($usuario,true)."<pre>";
            $this->db->select('o.id_omc,o.id_observador,o.id_acomp1,id_acomp2,o.analisis_riesgo as ar,o.estado');
            $this->db->select("concat(p.nombre,' ',p.apellido) as observador",false);
            $this->db->select("concat(p2.nombre,' ',p2.apellido) as acomp1",false);
            $this->db->select("concat(p2.nombre,' ',p2.apellido) as acomp1",false);
//            $this->db->select("if(id_acomp1 is NULL, concat('Contratista: ',c1.contratista), concat(p2.nombre,' ',p2.apellido)) as acomp1",false);
//            $this->db->select("if(id_acomp2 is NULL, concat('Contratista: ',c2.contratista), concat(p3.nombre,' ',p3.apellido)) as acomp2",false);
            $this->db->select("concat(p3.nombre,' ',p3.apellido) as acomp2",false);
            $this->db->select("DATE_FORMAT(o.fecha_alta,'%d/%m/%Y') as fecha_alta", FALSE);
            $this->db->select("e.abv as empresa",false);
            $this->db->select("s.sitio");
//            $this->db->select("a.gr");
//            $this->db->select("concat(e.abv,' | ',s.sitio) as sitio",false);
            $this->db->select("ss.sector as sector",false);
            $this->db->select("(select count(id_tarea) from gr_tareas t where t.id_herramienta=o.id_omc and t.id_tipo_herramienta=4) as tareas",false);
            $this->db->from('gr_omc o');
            $this->db->join('gr_usuarios gu','gu.id_usuario = o.id_observador','inner');
            $this->db->join('gr_usuarios gu2','gu2.id_usuario = o.id_acomp1','left');
            $this->db->join('gr_usuarios gu3','gu3.id_usuario = o.id_acomp2','left');
            $this->db->join('sys_usuarios u','u.id_usuario = gu.id_usuario','inner');
            $this->db->join('sys_usuarios u2','u2.id_usuario = gu2.id_usuario','left');
            $this->db->join('sys_usuarios u3','u3.id_usuario = gu3.id_usuario','left');
            $this->db->join('grl_personas p','p.id_persona = u.id_persona','left');
            $this->db->join('grl_personas p2','p2.id_persona = u2.id_persona','left');
            $this->db->join('grl_personas p3','p3.id_persona = u3.id_persona','left');
            $this->db->join('gr_puestos pto','pto.id_puesto = gu.id_puesto','inner');
//            $this->db->join('gr_areas a','pto.id_area = a.id_area','inner');
            $this->db->join('gr_sitios s','s.id_sitio = o.id_sitio','inner');
            $this->db->join('gr_sectores ss','ss.id_sector = o.id_sector','inner');
            $this->db->join('grl_empresas e','e.id_empresa = o.id_empresa','left');
//            $this->db->join('gr_contratistas c1','c1.id_contratista = o.id_contratista1','left');
//            $this->db->join('gr_contratistas c2','c2.id_contratista = o.id_contratista2','left');
            
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
                        case 'observador':
                            $campo="concat(p.nombre,' ',p.apellido)";
                            break;
                        case 'acomp1':
                            $campo="concat(p2.nombre,' ',p2.apellido)";
                            break;
                        case 'acomp2':
                            $campo="concat(p3.nombre,' ',p3.apellido)";
                            break;
                        case 'empresa':
                            $campo="concat(e.abv,' ',e.empresa)";
                            break;
                        case 'sitio':
                            $campo="s.sitio";
                            break;
                        case 'sector':
                            $campo="ss.sector";
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
//            if($usuario['usuario']['gr']!=1)
//            {
////                $this->db->where_in('(pto2.id_area',$usuario['areas_inferiores'],FALSE);
////                  $this->db->where_in('pto.id_area',$usuario['areas_inferiores'],FALSE);
//                $this->db->where("(o.id_observador",$usuario['usuario']['id_usuario'],FALSE);
//                $this->db->or_where("o.id_acomp1",$usuario['usuario']['id_usuario'].")",FALSE);
//
//            }
        if ($sort!="")
        {
            switch ($sort)
            {
                case 'id_omc':
                    $campoOrden="o.id_omc";
                    break;
                case 'observador':
                    $campoOrden="concat(p.nombre,' ',p.apellido)";
                    break;
                case 'acomp1':
                    $campoOrden="concat(p2.nombre,' ',p2.apellido)";
                    break;
                case 'empresa':
                    $campoOrden="e.abv";
                    break;
                case 'sitio':
                    $campoOrden="s.sitio";
                    break;
                case 'sector':
                    $campoOrden="ss.sector";
                    break;
                case 'fecha_alta':
                    $campoOrden="o.fecha_alta";
                    break;
                case 'ar':
                    $campoOrden="o.analisis_riesgo";
                    break;
                default:
                    $campoOrden="o.id_omc";
                    
            }
            $this->db->order_by($campoOrden, $dir);
        }
        else
            $this->db->order_by("o.id_omc", "desc"); 
        $this->db->limit($limit,$start); 

        $query = $this->db->get();
//        echo $this->db->last_query();
        $num = $this->cantSql('o.id_omc',$this->db->last_query());
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
         public function verificaRePost($datos)
        {
            $this->db->select('count(id_omc) as cant',false);
            $this->db->from('gr_omc');
            $this->db->where("id_observador",$datos['id_observador']);
            $this->db->where("id_acomp1",$datos['id_acomp1']);
            $this->db->where("id_empresa",$datos['id_empresa']);
            $this->db->where("id_sitio",$datos['id_sitio']);
            $this->db->where("id_sector",$datos['id_sector']);
//            $this->db->where("id_acomp2",$datos['id_acomp2']);
//            $this->db->where("id_contratista1",$datos['id_contratista1']);
//            $this->db->where("id_contratista2",$datos['id_contratista2']);
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
		
		$this->db->insert('gr_omc',$datos);
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
        public function dameOmcDatosMail($id)
	{
            $this->db->select("o.id_omc");
            $this->db->select("concat(p.nombre,' ',p.apellido) as observador",false);
            $this->db->select("DATE_FORMAT(o.fecha_alta,'%d/%m/%Y') as fecha_alta", FALSE);
            $this->db->select("concat(e.abv,' | ',s.sector) as sector",false);
            $this->db->select("concat(p2.nombre,' ',p2.apellido) as acomp1",false);
//            $this->db->select("if(id_acomp1 is NULL, concat('Contratista: ',c1.contratista), concat(p2.nombre,' ',p2.apellido)) as acomp1",false);
//            $this->db->select("if(id_acomp2 is NULL, concat('Contratista: ',c2.contratista), concat(p3.nombre,' ',p3.apellido)) as acomp2",false);
            $this->db->select("if(id_acomp1 is NULL, '', u2.email )as mailacomp1",false);
            $this->db->select("if(id_acomp2 is NULL, '', u3.email )as mailacomp2",false);
            $this->db->from('gr_omc o');
            $this->db->join('gr_usuarios gu','gu.id_usuario = o.id_observador','inner');
            $this->db->join('gr_usuarios gu2','gu2.id_usuario = o.id_acomp1','left');
            $this->db->join('gr_usuarios gu3','gu3.id_usuario = o.id_acomp2','left');
            $this->db->join('sys_usuarios u','u.id_usuario = gu.id_usuario','inner');
            $this->db->join('sys_usuarios u2','u2.id_usuario = gu2.id_usuario','left');
            $this->db->join('sys_usuarios u3','u3.id_usuario = gu3.id_usuario','left');
            $this->db->join('grl_personas p','p.id_persona = u.id_persona','left');
            $this->db->join('grl_personas p2','p2.id_persona = u2.id_persona','left');
            $this->db->join('grl_personas p3','p3.id_persona = u3.id_persona','left');
            $this->db->join('gr_sectores s','s.id_sector = o.id_sector','inner');
             $this->db->join('grl_empresas e','e.id_empresa = o.id_empresa','inner');
             $this->db->join('gr_contratistas c1','c1.id_contratista = o.id_contratista1','left');
            $this->db->join('gr_contratistas c2','c2.id_contratista = o.id_contratista2','left');
            $this->db->where("o.id_omc",$id);
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
         public function verificaObservador($id_usuario,$id_omc)
        {
            $this->db->select('count(id_omc) as cant',false);
            $this->db->from('gr_omc');
            $this->db->where("id_omc",$id_omc);
            $this->db->where("id_observador",$id_usuario);
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
        
        function update($id, $datos)
        {
            $this->db->where("id_omc",$id);
//            $this->db->update("gr_omc",$datos)
            $update=$this->db->update("gr_omc",$datos);
//                    echo $this->db->last_query();
            if ($update)
                return 1;
            else
                return 0;
        }
        
    public function listado_excel($start, $limit, $busqueda, $campos,$sort="", $dir="", $filtros="")
    {
//            echo "<pre>". print_r($usuario,true)."<pre>";
            $this->db->select('o.id_omc');
            $this->db->select("DATE_FORMAT(o.fecha_alta,'%d/%m/%Y') as fecha_alta", FALSE);
            $this->db->select("concat(p.nombre,' ',p.apellido) as observador",false);
            $this->db->select("concat(p2.nombre,' ',p2.apellido) as acomp1",false);
//            $this->db->select("concat(p2.nombre,' ',p2.apellido) as acomp1",false);
//            $this->db->select("if(id_acomp1 is NULL, concat('Contratista: ',c1.contratista), concat(p2.nombre,' ',p2.apellido)) as acomp1",false);
//            $this->db->select("if(id_acomp2 is NULL, concat('Contratista: ',c2.contratista), concat(p3.nombre,' ',p3.apellido)) as acomp2",false);
            $this->db->select("concat(p3.nombre,' ',p3.apellido) as acomp2",false);
//            $this->db->select("e.abv as empresa",false);
            $this->db->select("s.sitio");
//            $this->db->select("a.gr");
//            $this->db->select("concat(e.abv,' | ',s.sitio) as sitio",false);
            $this->db->select("ss.sector as sector",false);
            $this->db->select("(select count(id_tarea) from gr_tareas t where t.id_herramienta=o.id_omc and t.id_tipo_herramienta=4) as tareas",false);
            $this->db->select('o.analisis_riesgo as ar');
            $this->db->select(" CASE 
                                WHEN o.estado=1 THEN 'No Evaluada'
                                WHEN o.estado=2 THEN 'Aprobada'
                                ELSE '?'
                            END as estado",FALSE);
            $this->db->from('gr_omc o');
            $this->db->join('gr_usuarios gu','gu.id_usuario = o.id_observador','inner');
            $this->db->join('gr_usuarios gu2','gu2.id_usuario = o.id_acomp1','left');
            $this->db->join('gr_usuarios gu3','gu3.id_usuario = o.id_acomp2','left');
            $this->db->join('sys_usuarios u','u.id_usuario = gu.id_usuario','inner');
            $this->db->join('sys_usuarios u2','u2.id_usuario = gu2.id_usuario','left');
            $this->db->join('sys_usuarios u3','u3.id_usuario = gu3.id_usuario','left');
            $this->db->join('grl_personas p','p.id_persona = u.id_persona','left');
            $this->db->join('grl_personas p2','p2.id_persona = u2.id_persona','left');
            $this->db->join('grl_personas p3','p3.id_persona = u3.id_persona','left');
            $this->db->join('gr_puestos pto','pto.id_puesto = gu.id_puesto','inner');
//            $this->db->join('gr_areas a','pto.id_area = a.id_area','inner');
            $this->db->join('gr_sitios s','s.id_sitio = o.id_sitio','inner');
            $this->db->join('gr_sectores ss','ss.id_sector = o.id_sector','inner');
            $this->db->join('grl_empresas e','e.id_empresa = o.id_empresa','left');
//            $this->db->join('gr_contratistas c1','c1.id_contratista = o.id_contratista1','left');
//            $this->db->join('gr_contratistas c2','c2.id_contratista = o.id_contratista2','left');
            
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
                        case 'observador':
                            $campo="concat(p.nombre,' ',p.apellido)";
                            break;
                        case 'acomp1':
                            $campo="concat(p2.nombre,' ',p2.apellido)";
                            break;
                        case 'acomp2':
                            $campo="concat(p3.nombre,' ',p3.apellido)";
                            break;
                        case 'empresa':
                            $campo="concat(e.abv,' ',e.empresa)";
                            break;
                        case 'sitio':
                            $campo="s.sitio";
                            break;
                        case 'sector':
                            $campo="ss.sector";
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
//            if($usuario['usuario']['gr']!=1)
//            {
////                $this->db->where_in('(pto2.id_area',$usuario['areas_inferiores'],FALSE);
////                  $this->db->where_in('pto.id_area',$usuario['areas_inferiores'],FALSE);
//                $this->db->where("(o.id_observador",$usuario['usuario']['id_usuario'],FALSE);
//                $this->db->or_where("o.id_acomp1",$usuario['usuario']['id_usuario'].")",FALSE);
//
//            }
        if ($sort!="")
        {
            switch ($sort)
            {
                case 'id_omc':
                    $campoOrden="o.id_omc";
                    break;
                case 'observador':
                    $campoOrden="concat(p.nombre,' ',p.apellido)";
                    break;
                case 'acomp1':
                    $campoOrden="concat(p2.nombre,' ',p2.apellido)";
                    break;
                case 'empresa':
                    $campoOrden="e.abv";
                    break;
                case 'sitio':
                    $campoOrden="s.sitio";
                    break;
                case 'sector':
                    $campoOrden="ss.sector";
                    break;
                case 'fecha_alta':
                    $campoOrden="o.fecha_alta";
                    break;
                case 'ar':
                    $campoOrden="o.analisis_riesgo";
                    break;
                default:
                    $campoOrden="o.id_omc";
                    
            }
            $this->db->order_by($campoOrden, $dir);
        }
        else
            $this->db->order_by("o.id_omc", "desc"); 
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