<?php
class Mejoracontinua_model extends CI_Model
{
    public function listado($usuario, $start, $limit, $filtros, $busqueda, $campos,$sort="", $dir="")
    {
        $this->db->select('t.id_tarea, t.usuario_responsable as id_responsable, t.usuario_alta as id_usuario_alta, t.hallazgo, t.tarea,t.id_estado,t.editada,t.observacion as obs');
        $this->db->select('t.id_tipo_herramienta as th');
        $this->db->select("concat(p.nombre,' ',p.apellido) as usuario_alta",false);
        $this->db->select("concat(p2.nombre,' ',p2.apellido) as usuario_responsable",false);
        $this->db->select("DATE_FORMAT(t.fecha_alta,'%d/%m/%Y') as fecha_alta", FALSE);
        $this->db->select("DATE_FORMAT(t.fecha_vto,'%d/%m/%Y') as fecha_vto", FALSE);
        $this->db->select("DATE_FORMAT(t.fecha_accion,'%d/%m/%Y') as fecha_accion", FALSE);
        $this->db->select("e.estado as estado",FALSE);
//        $this->db->select('a.area');
        $this->db->select("concat(ee.abv,'-',a.area) as area",false);
        $this->db->select('t.id_grado_crit');
//        $this->db->select("concat_WS(' ',th.abv,COALESCE(t.id_herramienta,'')) as fuente",FALSE);
        $this->db->select(" CASE 
                                WHEN t.id_tipo_herramienta=8 and t.id_herramienta is not null
                                    THEN (select concat_WS(' ',th.abv,sc.id_evento) from cpp_causas sc where sc.id_causa=t.id_herramienta) 
                                ELSE (concat_WS(' ',th.abv,COALESCE(t.id_herramienta,'')))
                            END as fuente",FALSE);
        $this->db->select(" CASE 
                                WHEN t.id_grado_crit=1 THEN 'Critica'
                                WHEN t.id_grado_crit=2 THEN 'Alta'
                                WHEN t.id_grado_crit=3 THEN 'Menor'
                                ELSE '-.-'
                            END as grado_crit",FALSE);
        $this->db->select(" CASE 
                                WHEN t.rpd = 1 THEN 'Si'
                                WHEN t.rpd = 2 THEN 'No'
                                ELSE '-.-'
                            END as rpd",FALSE);
        $this->db->select('t.estado_vto as id_estado_vto',false);
        $this->db->select(" CASE 
                                WHEN t.estado_vto=0 THEN ''
                                WHEN t.estado_vto=1 THEN 'Vencida'
                                ELSE '-.-'
                            END as estado_vto",FALSE);
        $this->db->select('c.texto as txt_cierre',FALSE);
        $this->db->select("DATE_FORMAT(c.fecha_cierre,'%d/%m/%Y') as fecha_cierre", FALSE);
        $this->db->select("DATE_FORMAT(c.fecha_aprobacion,'%d/%m/%Y') as fecha_aprobada", FALSE);
        $this->db->from('gr_tareas t');
        $this->db->join('gr_estados e','e.id_estado = t.id_estado','inner');
        $this->db->join('sys_usuarios u','u.id_usuario = t.usuario_alta','inner');
        $this->db->join('sys_usuarios u2','u2.id_usuario = t.usuario_responsable','inner');
        $this->db->join('gr_usuarios uu','uu.id_usuario = t.usuario_alta','inner');
        $this->db->join('gr_usuarios uu2','uu2.id_usuario = t.usuario_responsable','inner');
        $this->db->join('gr_puestos pto','pto.id_puesto = uu.id_puesto','inner');
        $this->db->join('gr_puestos pto2','pto2.id_puesto = uu2.id_puesto','inner');
        $this->db->join('gr_areas a','a.id_area = pto2.id_area','inner');
        $this->db->join('grl_personas p','p.id_persona = u.id_persona','inner');
        $this->db->join('grl_personas p2','p2.id_persona = u2.id_persona','inner');
        $this->db->join('gr_organigramas or1','or1.id_organigrama = pto2.id_organigrama','inner');
        $this->db->join('grl_empresas ee','ee.id_empresa = or1.id_empresa','inner');
        $this->db->join('gr_tipos_herramientas th','th.id_tipo_herramienta = t.id_tipo_herramienta','inner');
        $this->db->join('gr_cierres c','c.id_tarea = t.id_tarea','left');
        
        $estados=array(1,2,3,4,5,9,10);
        $this->db->where_in('t.id_estado',$estados);

        if ($filtros!="")
        {
            if (isset($filtros[0]) && $filtros[0]!="")
                $this->db->where('t.id_estado',$filtros[0]);
            if (isset($filtros[1]) && $filtros[1]!="")
                $this->db->where('t.id_tipo_herramienta',$filtros[1]);
            if (isset($filtros[2]) && $filtros[2]!="" && $filtros[2]==1)
                $this->db->where('t.estado_vto',1,false);
            if (isset($filtros[3]) && $filtros[3]!="" && $filtros[3]==1)
            {
                $this->db->where('(t.usuario_responsable',$usuario['usuario']['id_usuario'],false);
                $this->db->or_where('t.usuario_alta',$usuario['usuario']['id_usuario'].")",false);
                
            }
            if (isset($filtros[4]) && $filtros[4] > 0)
            {
                $this->db->where('t.rpd',$filtros[4]);
            }
        }
        if ($busqueda!="" && count($campos)>0)
        {
            foreach ($campos as &$campo)
            {
                switch ($campo)
                {
                    case 'id_tarea':
                    case 'hallazgo':
                    case 'tarea':
                        $campo="t.".$campo;
                        break;
                    case 'usuario_alta':
                        $campo="concat(p.nombre,' ',p.apellido)";
                        break;
                    case 'usuario_responsable':
                        $campo="concat(p2.nombre,' ',p2.apellido)";
                        break;
                    case 'fuente':
                        $campo="concat_WS(' ',th.abv,COALESCE(t.id_herramienta,''))";
                        break;
                }
            }
            unset($campo);
//                echo "<pre>". print_r($campos,true)."<pre>";
            //si viene un solo campo de busqueda
            if(count($campos)==1)
                if ($campos[0]=='t.id_tarea')
                    $this->db->where($campos[0],$busqueda);
                else
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

        }
//        if($usuario['gr']!=1)
//        {
////            $this->db->where_in('(pto2.id_area',$usuario['areas_inferiores'],FALSE);
////            $this->db->or_where_in('pto.id_area',$usuario['areas_inferiores'].")",FALSE);
//              $this->db->where("(pto2.id_area in(".$usuario['areas_inferiores'].") or pto.id_area in (".$usuario['areas_inferiores']."))");
//          
//        }
        if ($sort!="")
        {
            switch ($sort)
            {
                case 'id_tarea':
                case 'fecha_alta':
                case 'fecha_accion':
                case 'fecha_vto':
                case 'estado_vto':
                    $ordenar="t.".$sort;
                    break;
                case 'area':
                    $ordenar="concat(ee.abv,'-',a.area)";
                    break;
                case 'usuario_alta':
                    $ordenar="concat(p.nombre,' ',p.apellido)";
                    break;
                case 'usuario_responsable':
                    $ordenar="concat(p2.nombre,' ',p2.apellido)";
                    break;
                case 'estado':
                    $ordenar="e.estado";
                    break;
                case 'grado_crit':
                    $ordenar="field(t.id_grado_crit,2,1,3,0)";
                    break;
                default:
                    $ordenar="t.".$sort;

            }
            $this->db->order_by($ordenar, $dir);
        }
        else
        {
            $secuenciaUR="'".$usuario['usuario']['id_usuario']."-1'";
            $secuenciaUR.=",'".$usuario['usuario']['id_usuario']."-4'";
            $secuenciaUR.=",'".$usuario['usuario']['id_usuario']."-10'";
            $this->db->order_by("field(concat(t.usuario_responsable,'-',t.id_estado),".$secuenciaUR.")", "desc"); 
            $secuenciaUA="'".$usuario['usuario']['id_usuario']."-2'";
            $secuenciaUA.=",'".$usuario['usuario']['id_usuario']."-3'";
            $this->db->order_by("field(concat(t.usuario_alta,'-',t.id_estado),".$secuenciaUA.")", "desc"); 
//            $this->db->order_by("field(t.id_estado,"."1,4,5,3,2".")", "desc"); 
//            $this->db->order_by("field(t.usuario_responsable,".$usuario['usuario']['id_usuario'].")", "desc"); 
//            $this->db->order_by("id_tarea", "desc"); 
        }
//            $this->db->order_by("t.id_tarea", "asc"); 
        $this->db->limit($limit,$start); 

        $query = $this->db->get();
//        echo $this->db->last_query();
        $num = $this->cantSql('t.id_tarea',$this->db->last_query());
//        $num = 25;
        $res = $query->result_array();
        if ($num > 0)
        {
            return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
        }
        else
            return '({"total":"0","rows":""})';
    }
    public function listadoParaED($usuario, $start, $limit, $filtros, $busqueda, $campos,$sort="", $dir="")
    {
        $this->db->select('t.id_tarea, t.usuario_responsable as id_responsable, t.usuario_alta as id_usuario_alta, t.hallazgo, t.tarea,t.id_estado,t.editada,t.observacion as obs');
        $this->db->select('t.id_tipo_herramienta as th');
        $this->db->select("concat(p.apellido,' ',p.nombre) as usuario_alta",false);
        $this->db->select("concat(p2.apellido,' ',p2.nombre) as usuario_responsable",false);
        $this->db->select("DATE_FORMAT(t.fecha_alta,'%d/%m/%Y') as fecha_alta", FALSE);
        $this->db->select("DATE_FORMAT(t.fecha_vto,'%d/%m/%Y') as fecha_vto", FALSE);
        $this->db->select("DATE_FORMAT(t.fecha_accion,'%d/%m/%Y') as fecha_accion", FALSE);
        $this->db->select("e.estado as estado",FALSE);
        $this->db->select("concat_WS(' ',th.abv,COALESCE(t.id_herramienta,'')) as fuente",FALSE);
//        $this->db->select('a.area');
        $this->db->select("concat(ee.abv,'-',a.area) as area",false);
        $this->db->select('t.id_grado_crit');
        $this->db->select(" CASE 
                                WHEN t.id_grado_crit=1 THEN 'Critica'
                                WHEN t.id_grado_crit=2 THEN 'Alta'
                                WHEN t.id_grado_crit=3 THEN 'Menor'
                                ELSE '-.-'
                            END as grado_crit",FALSE);
        $this->db->select('t.estado_vto as id_estado_vto',false);
        $this->db->select(" CASE 
                                WHEN t.estado_vto=0 THEN ''
                                WHEN t.estado_vto=1 THEN 'Vencida'
                                ELSE '-.-'
                            END as estado_vto",FALSE);
        $this->db->select('c.texto as txt_cierre',FALSE);
        $this->db->select("DATE_FORMAT(c.fecha_cierre,'%d/%m/%Y') as fecha_cierre", FALSE);
        $this->db->select("DATE_FORMAT(c.fecha_aprobacion,'%d/%m/%Y') as fecha_aprobada", FALSE);
        $this->db->from('gr_tareas t');
        $this->db->join('gr_estados e','e.id_estado = t.id_estado','inner');
        $this->db->join('sys_usuarios u','u.id_usuario = t.usuario_alta','inner');
        $this->db->join('sys_usuarios u2','u2.id_usuario = t.usuario_responsable','inner');
        $this->db->join('gr_usuarios uu','uu.id_usuario = t.usuario_alta','inner');
        $this->db->join('gr_usuarios uu2','uu2.id_usuario = t.usuario_responsable','inner');
        $this->db->join('gr_puestos pto','pto.id_puesto = uu.id_puesto','inner');
        $this->db->join('gr_puestos pto2','pto2.id_puesto = uu2.id_puesto','inner');
        $this->db->join('gr_areas a','a.id_area = pto2.id_area','inner');
        $this->db->join('grl_personas p','p.id_persona = u.id_persona','inner');
        $this->db->join('grl_personas p2','p2.id_persona = u2.id_persona','inner');
        $this->db->join('gr_organigramas or1','or1.id_organigrama = pto2.id_organigrama','inner');
        $this->db->join('grl_empresas ee','ee.id_empresa = or1.id_empresa','inner');
        $this->db->join('gr_tipos_herramientas th','th.id_tipo_herramienta = t.id_tipo_herramienta','inner');
        $this->db->join('gr_cierres c','c.id_tarea = t.id_tarea','left');
        
        $this->db->where('t.id_tipo_herramienta',7);
        
        $estados=array(1,2,3,4,5,9,10);
        $this->db->where_in('t.id_estado',$estados);

        if ($filtros!="")
        {
            if (isset($filtros[0]) && $filtros[0]!="")
                $this->db->where('t.id_estado',$filtros[0]);
            if (isset($filtros[1]) && $filtros[1]!="" && $filtros[1]==1)
                $this->db->where('t.estado_vto',1,false);
            if (isset($filtros[2]) && $filtros[2]!="" && $filtros[2]==1)
            {
                $this->db->where('(t.usuario_responsable',$usuario['usuario']['id_usuario'],false);
                $this->db->or_where('t.usuario_alta',$usuario['usuario']['id_usuario'].")",false);
                
            }
        }
        if ($busqueda!="" && count($campos)>0)
        {
            foreach ($campos as &$campo)
            {
                switch ($campo)
                {
                    case 'id_tarea':
                    case 'hallazgo':
                    case 'tarea':
                        $campo="t.".$campo;
                        break;
                    case 'usuario_alta':
                        $campo="concat(p.apellido,' ',p.nombre)";
                        break;
                    case 'usuario_responsable':
                        $campo="concat(p2.apellido,' ',p2.nombre)";
                        break;
                    case 'fuente':
                        $campo="concat_WS(' ',th.abv,COALESCE(t.id_herramienta,''))";
                        break;
                }
            }
            unset($campo);
//                echo "<pre>". print_r($campos,true)."<pre>";
            //si viene un solo campo de busqueda
            if(count($campos)==1)
                if ($campos[0]=='t.id_tarea')
                    $this->db->where($campos[0],$busqueda);
                else
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

        }
//        if($usuario['gr']!=1)
//        {
////            $this->db->where_in('(pto2.id_area',$usuario['areas_inferiores'],FALSE);
////            $this->db->or_where_in('pto.id_area',$usuario['areas_inferiores'].")",FALSE);
//              $this->db->where("(pto2.id_area in(".$usuario['areas_inferiores'].") or pto.id_area in (".$usuario['areas_inferiores']."))");
//          
//        }
        if ($sort!="")
        {
            switch ($sort)
            {
                case 'id_tarea':
                case 'fecha_alta':
                case 'fecha_accion':
                case 'fecha_vto':
                case 'estado_vto':
                    $ordenar="t.".$sort;
                    break;
                case 'area':
                    $ordenar="concat(ee.abv,'-',a.area)";
                    break;
                case 'usuario_alta':
                    $ordenar="concat(p.apellido,' ',p.nombre)";
                    break;
                case 'usuario_responsable':
                    $ordenar="concat(p2.apellido,' ',p2.nombre)";
                    break;
                case 'estado':
                    $ordenar="e.estado";
                    break;
                case 'grado_crit':
                    $ordenar="field(t.id_grado_crit,2,1,3,0)";
                    break;
                default:
                    $ordenar="t.".$sort;

            }
            $this->db->order_by($ordenar, $dir);
        }
        else
        {
            $secuenciaUR="'".$usuario['usuario']['id_usuario']."-1'";
            $secuenciaUR.=",'".$usuario['usuario']['id_usuario']."-4'";
            $secuenciaUR.=",'".$usuario['usuario']['id_usuario']."-10'";
            $this->db->order_by("field(concat(t.usuario_responsable,'-',t.id_estado),".$secuenciaUR.")", "desc"); 
            $secuenciaUA="'".$usuario['usuario']['id_usuario']."-2'";
            $secuenciaUA.=",'".$usuario['usuario']['id_usuario']."-3'";
            $this->db->order_by("field(concat(t.usuario_alta,'-',t.id_estado),".$secuenciaUA.")", "desc"); 
//            $this->db->order_by("field(t.id_estado,"."1,4,5,3,2".")", "desc"); 
//            $this->db->order_by("field(t.usuario_responsable,".$usuario['usuario']['id_usuario'].")", "desc"); 
//            $this->db->order_by("id_tarea", "desc"); 
        }
//            $this->db->order_by("t.id_tarea", "asc"); 
        $this->db->limit($limit,$start); 

        $query = $this->db->get();
//        echo $this->db->last_query();
        $num = $this->cantSql('t.id_tarea',$this->db->last_query());
//        $num = 25;
        $res = $query->result_array();
        if ($num > 0)
        {
            return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
        }
        else
            return '({"total":"0","rows":""})';
    }
    public function listado_excel($usuario,$filtros, $busqueda, $campos,$sort="", $dir="")
    {
        $limit=1000;
        $start=0;
        $this->db->select('t.id_tarea as Nro', FALSE);
        $this->db->select("DATE_FORMAT(t.fecha_alta,'%d/%m/%Y') as FechaAlta", FALSE);
        $this->db->select("concat_WS(' ',th.abv,COALESCE(t.id_herramienta,'')) as Fuente",FALSE);
        $this->db->select('t.hallazgo as DetalleHallazgo', FALSE); 
        $this->db->select(" CASE 
                                WHEN t.id_grado_crit=1 THEN 'Critica'
                                WHEN t.id_grado_crit=2 THEN 'Alta'
                                WHEN t.id_grado_crit=3 THEN 'Menor'
                                ELSE '-.-'
                            END as gradoCrit",FALSE);
        $this->db->select('t.tarea as Tarea', FALSE);
        $this->db->select("DATE_FORMAT(t.fecha_vto,'%d/%m/%Y') as FechaLimite", FALSE);
        $this->db->select("e.estado as EstadoActual",FALSE);
        $this->db->select("concat(p.nombre,' ',p.apellido) as UsuarioSolicitante",false);
        $this->db->select("concat(p2.nombre,' ',p2.apellido) as UsuarioResponsable",false);
        $this->db->select("concat(ee.abv,'-',a.area) as AreaResponsable",false);
        $this->db->select("DATE_FORMAT(t.fecha_accion,'%d/%m/%Y') as FechaAccion", FALSE);
//        $this->db->select('a.area');
        $this->db->select('t.observacion as Observaciones', FALSE);
        $this->db->from('gr_tareas t');
        $this->db->join('gr_estados e','e.id_estado = t.id_estado','inner');
        $this->db->join('sys_usuarios u','u.id_usuario = t.usuario_alta','inner');
        $this->db->join('sys_usuarios u2','u2.id_usuario = t.usuario_responsable','inner');
        $this->db->join('gr_usuarios uu','uu.id_usuario = t.usuario_alta','inner');
        $this->db->join('gr_usuarios uu2','uu2.id_usuario = t.usuario_responsable','inner');
        $this->db->join('gr_puestos pto','pto.id_puesto = uu.id_puesto','inner');
        $this->db->join('gr_puestos pto2','pto2.id_puesto = uu2.id_puesto','inner');
        $this->db->join('gr_areas a','a.id_area = pto2.id_area','inner');
        $this->db->join('grl_personas p','p.id_persona = u.id_persona','inner');
        $this->db->join('grl_personas p2','p2.id_persona = u2.id_persona','inner');
        $this->db->join('gr_organigramas or1','or1.id_organigrama = pto2.id_organigrama','inner');
        $this->db->join('grl_empresas ee','ee.id_empresa = or1.id_empresa','inner');
        $this->db->join('gr_tipos_herramientas th','th.id_tipo_herramienta = t.id_tipo_herramienta','inner');
        
        $estados=array(1,2,3,4,5,9,10);
        $this->db->where_in('t.id_estado',$estados);

        if ($filtros!="")
        {
            if (isset($filtros[0]) && $filtros[0]!="")
                $this->db->where('t.id_estado',$filtros[0]);
            if (isset($filtros[1]) && $filtros[1]!="")
                $this->db->where('t.id_tipo_herramienta',$filtros[1]);
            if (isset($filtros[2]) && $filtros[2]!="" && $filtros[2]==1)
                $this->db->where('t.estado_vto',1,false);
            if (isset($filtros[3]) && $filtros[3]!="" && $filtros[3]==1)
            {
                $this->db->where('(t.usuario_responsable',$usuario['usuario']['id_usuario'],false);
                $this->db->or_where('t.usuario_alta',$usuario['usuario']['id_usuario'].")",false);
                
            }
            if (isset($filtros[4]) && $filtros[4] > 0)
            {
                $this->db->where('t.rpd',$filtros[4]);
            }
        }
        if ($busqueda!="" && count($campos)>0)
        {
            foreach ($campos as &$campo)
            {
                switch ($campo)
                {
                    case 'id_tarea':
                    case 'hallazgo':
                    case 'tarea':
                        $campo="t.".$campo;
                        break;
                    case 'usuario_alta':
                        $campo="concat(p.nombre,' ',p.apellido)";
                        break;
                    case 'usuario_responsable':
                        $campo="concat(p2.nombre,' ',p2.apellido)";
                        break;
                    case 'fuente':
                        $campo="concat_WS(' ',th.abv,COALESCE(t.id_herramienta,''))";
                        break;
                }
            }
            unset($campo);
//                echo "<pre>". print_r($campos,true)."<pre>";
            //si viene un solo campo de busqueda
            if(count($campos)==1)
                if ($campos[0]=='t.id_tarea')
                    $this->db->where($campos[0],$busqueda);
                else
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

        }
//        if($usuario['gr']!=1)
//        {
////            $this->db->where_in('(pto2.id_area',$usuario['areas_inferiores'],FALSE);
////            $this->db->or_where_in('pto.id_area',$usuario['areas_inferiores'].")",FALSE);
//              $this->db->where("(pto2.id_area in(".$usuario['areas_inferiores'].") or pto.id_area in (".$usuario['areas_inferiores']."))");
//          
//        }
        if ($sort!="")
        {
            switch ($sort)
            {
                case 'id_tarea':
                case 'fecha_alta':
                case 'fecha_accion':
                case 'fecha_vto':
                    $ordenar="t.".$sort;
                    break;
                case 'area':
                    $ordenar="concat(ee.abv,'-',a.area)";
                    break;
                case 'usuario_alta':
                    $ordenar="concat(p.nombre,' ',p.apellido)";
                    break;
                case 'usuario_responsable':
                    $ordenar="concat(p2.nombre,' ',p2.apellido)";
                    break;
                case 'estado':
                    $ordenar="e.estado";
                    break;
                case 'grado_crit':
                    $ordenar="field(t.id_grado_crit,2,1,3,0)";
                    break;
                default:
                    $ordenar="t.".$sort;

            }
            $this->db->order_by($ordenar, $dir);
        }
        else
        {
            $this->db->order_by("field(t.usuario_responsable,".$usuario['usuario']['id_usuario'].")", "desc"); 
            $this->db->order_by("field(t.id_estado,"."3".")", "desc"); 
            $this->db->order_by("id_tarea", "desc"); 
        }
//            $this->db->order_by("t.id_tarea", "asc"); 
        $this->db->limit($limit,$start); 

        $query = $this->db->get();
//        echo $this->db->last_query();
        $num = $this->db->count_all_results();
//        $num = 25;
        $res = $query->result_array();
        if ($num > 0)
        {
            return $res;
        }
        else
            return 0;
    }
    public function listado_excel_ed($usuario,$filtros, $busqueda, $campos,$sort="", $dir="")
    {
        $limit=1000;
        $start=0;
        $this->db->select('t.id_tarea as Nro', FALSE);
        $this->db->select("DATE_FORMAT(t.fecha_alta,'%d/%m/%Y') as FechaAlta", FALSE);
        $this->db->select("concat_WS(' ',th.abv,COALESCE(t.id_herramienta,'')) as Fuente",FALSE);
        $this->db->select('t.hallazgo as DetalleHallazgo', FALSE); 
        $this->db->select(" CASE 
                                WHEN t.id_grado_crit=1 THEN 'Critica'
                                WHEN t.id_grado_crit=2 THEN 'Alta'
                                WHEN t.id_grado_crit=3 THEN 'Menor'
                                ELSE '-.-'
                            END as gradoCrit",FALSE);
        $this->db->select('t.tarea as Tarea', FALSE);
        $this->db->select("DATE_FORMAT(t.fecha_vto,'%d/%m/%Y') as FechaLimite", FALSE);
        $this->db->select("e.estado as EstadoActual",FALSE);
        $this->db->select("concat(p.apellido,' ',p.nombre) as UsuarioSolicitante",false);
        $this->db->select("concat(p2.apellido,' ',p2.nombre) as UsuarioResponsable",false);
        $this->db->select("concat(ee.abv,'-',a.area) as AreaResponsable",false);
        $this->db->select("DATE_FORMAT(t.fecha_accion,'%d/%m/%Y') as FechaAccion", FALSE);
//        $this->db->select('a.area');
        $this->db->select('t.observacion as Observaciones', FALSE);
        $this->db->from('gr_tareas t');
        $this->db->join('gr_estados e','e.id_estado = t.id_estado','inner');
        $this->db->join('sys_usuarios u','u.id_usuario = t.usuario_alta','inner');
        $this->db->join('sys_usuarios u2','u2.id_usuario = t.usuario_responsable','inner');
        $this->db->join('gr_usuarios uu','uu.id_usuario = t.usuario_alta','inner');
        $this->db->join('gr_usuarios uu2','uu2.id_usuario = t.usuario_responsable','inner');
        $this->db->join('gr_puestos pto','pto.id_puesto = uu.id_puesto','inner');
        $this->db->join('gr_puestos pto2','pto2.id_puesto = uu2.id_puesto','inner');
        $this->db->join('gr_areas a','a.id_area = pto2.id_area','inner');
        $this->db->join('grl_personas p','p.id_persona = u.id_persona','inner');
        $this->db->join('grl_personas p2','p2.id_persona = u2.id_persona','inner');
        $this->db->join('gr_organigramas or1','or1.id_organigrama = pto2.id_organigrama','inner');
        $this->db->join('grl_empresas ee','ee.id_empresa = or1.id_empresa','inner');
        $this->db->join('gr_tipos_herramientas th','th.id_tipo_herramienta = t.id_tipo_herramienta','inner');
        $this->db->where('t.id_tipo_herramienta',7);
        
        $estados=array(1,2,3,4,5);
        $this->db->where_in('t.id_estado',$estados);

        if ($filtros!="")
        {
            if (isset($filtros[0]) && $filtros[0]!="")
                $this->db->where('t.id_estado',$filtros[0]);
             if (isset($filtros[1]) && $filtros[1]!="" && $filtros[1]==1)
                $this->db->where('t.estado_vto',1,false);
             if (isset($filtros[2]) && $filtros[2]!="" && $filtros[2]==1)
            {
                $this->db->where('(t.usuario_responsable',$usuario['usuario']['id_usuario'],false);
                $this->db->or_where('t.usuario_alta',$usuario['usuario']['id_usuario'].")",false);
                
            }
        }
        if ($busqueda!="" && count($campos)>0)
        {
            foreach ($campos as &$campo)
            {
                switch ($campo)
                {
                    case 'id_tarea':
                    case 'hallazgo':
                    case 'tarea':
                        $campo="t.".$campo;
                        break;
                    case 'usuario_alta':
                        $campo="concat(p.apellido,' ',p.nombre)";
                        break;
                    case 'usuario_responsable':
                        $campo="concat(p2.apellido,' ',p2.nombre)";
                        break;
                    case 'fuente':
                        $campo="concat_WS(' ',th.abv,COALESCE(t.id_herramienta,''))";
                        break;
                }
            }
            unset($campo);
//                echo "<pre>". print_r($campos,true)."<pre>";
            //si viene un solo campo de busqueda
            if(count($campos)==1)
                if ($campos[0]=='t.id_tarea')
                    $this->db->where($campos[0],$busqueda);
                else
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

        }
//        if($usuario['gr']!=1)
//        {
////            $this->db->where_in('(pto2.id_area',$usuario['areas_inferiores'],FALSE);
////            $this->db->or_where_in('pto.id_area',$usuario['areas_inferiores'].")",FALSE);
//              $this->db->where("(pto2.id_area in(".$usuario['areas_inferiores'].") or pto.id_area in (".$usuario['areas_inferiores']."))");
//          
//        }
        if ($sort!="")
        {
            switch ($sort)
            {
                case 'id_tarea':
                case 'fecha_alta':
                case 'fecha_accion':
                case 'fecha_vto':
                    $ordenar="t.".$sort;
                    break;
                case 'area':
                    $ordenar="concat(ee.abv,'-',a.area)";
                    break;
                case 'usuario_alta':
                    $ordenar="concat(p.nombre,' ',p.apellido)";
                    break;
                case 'usuario_responsable':
                    $ordenar="concat(p2.nombre,' ',p2.apellido)";
                    break;
                case 'estado':
                    $ordenar="e.estado";
                    break;
                case 'grado_crit':
                    $ordenar="field(t.id_grado_crit,2,1,3,0)";
                    break;
                default:
                    $ordenar="t.".$sort;

            }
            $this->db->order_by($ordenar, $dir);
        }
        else
        {
            $this->db->order_by("field(t.usuario_responsable,".$usuario['usuario']['id_usuario'].")", "desc"); 
            $this->db->order_by("field(t.id_estado,"."3".")", "desc"); 
            $this->db->order_by("id_tarea", "desc"); 
        }
//            $this->db->order_by("t.id_tarea", "asc"); 
        $this->db->limit($limit,$start); 

        $query = $this->db->get();
//        echo $this->db->last_query();
        $num = $this->db->count_all_results();
//        $num = 25;
        $res = $query->result_array();
        if ($num > 0)
        {
            return $res;
        }
        else
            return 0;
    }
	
        function cantSql($count,$last_query)
        {
            $sql=  explode('FROM', $last_query);
            $sql=  explode('ORDER BY', $sql[1]);
            $sql=  "SELECT count($count) as cantidad FROM ".$sql[0];
//            $sql= str_replace('INNER JOIN gr_tipos_herramientas th ON th.id_tipo_herramienta = t.id_tipo_herramienta','', $sql);
            $sql= str_replace('INNER JOIN grl_empresas ee ON ee.id_empresa = or1.id_empresa','', $sql);
            $sql= str_replace('INNER JOIN gr_organigramas or1 ON or1.id_organigrama = pto2.id_organigrama','', $sql);
//            $sql= str_replace('INNER JOIN grl_personas p2 ON p2.id_persona = u2.id_persona', '', $sql);
//            $sql= str_replace('INNER JOIN grl_personas p ON p.id_persona = u.id_persona', '', $sql);
            $sql= str_replace('INNER JOIN gr_areas a ON a.id_area = pto2.id_area','', $sql);
//            echo $sql;
            $query =$this->db->query($sql);
//            echo $this->db->last_query();
            $res = $query->result();
            return $res[0]->cantidad;
//            return 25;
        }
        public function usuariosCombo($limit,$start,$filtro,$idNot="")
	{
		
//            $this->db->select('p.id_usuario');
            $this->db->select('u.id_usuario,uu.usuario');
//            $this->db->select('pto.puesto');
            $this->db->select('concat(pto.puesto," - ",e.abv) as puesto',false);
            $this->db->select('concat(pp.nombre," ",pp.apellido) as nomape',false);
            $this->db->from('gr_usuarios u');
            $this->db->join('sys_usuarios uu','uu.id_usuario = u.id_usuario','inner');
            $this->db->join('grl_personas pp','pp.id_persona = uu.id_persona','inner');
            $this->db->join('gr_puestos pto','pto.id_puesto = u.id_puesto','inner');
            $this->db->join('gr_organigramas or1','or1.id_organigrama = pto.id_organigrama','inner');
            $this->db->join('grl_empresas e','e.id_empresa = or1.id_empresa','inner');
//            $this->db->where("u.id_perfil",2);
            
            if($idNot!="")
                $this->db->where("u.id_usuario !=",$idNot);
            
            
            $this->db->where("pp.habilitado",1);
            $this->db->where("u.habilitado",1);
            $this->db->where("uu.habilitado",1);
            $this->db->where("(pp.nombre like","'%".$filtro."%'",FALSE);
            $this->db->or_where("pp.apellido like","'%".$filtro."%'",FALSE);
            $this->db->or_where("concat(pp.nombre,' ',pp.apellido) like","'%".$filtro."%')",FALSE);
            
            
            $this->db->order_by("pp.nombre", "asc"); 
            $this->db->order_by("pp.apellido", "asc"); 
            
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
        public function insert($datos)
	{
            
            $this->db->trans_begin();
		
		$this->db->insert('gr_tareas',$datos);
//                echo $this->db->last_query();
                $insert_id = $this->db->insert_id();
		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			return false;
		}
		else
		{
			$this->db->trans_commit();
			return $insert_id;
		}
		
	}
        public function update($id,$datos)
	{
            $this->db->where('id_tarea', $id);
            $edit=$this->db->update('gr_tareas', $datos);
//            echo $this->db->last_query();
            if(!$edit)
                    return false;
            else
                    return true;
		
	}
        public function copy($id_tarea)
	{
            $sql="INSERT INTO gr_historial(id_tarea, usuario_alta, usuario_responsable, id_tipo_herramienta, id_estado, hallazgo, tarea, fecha_alta, fecha_vto, estado_vto, fecha_accion, observacion) 
                    select id_tarea, usuario_alta, usuario_responsable, id_tipo_herramienta, id_estado, hallazgo, tarea, fecha_alta, fecha_vto, estado_vto, fecha_accion, observacion  from gr_tareas 
                    where id_tarea=".$id_tarea;
            $copy=$this->db->query($sql);
//            echo $this->db->last_query();
//            return $copy;
            if(!$copy)
                    return false;
            else
                    return true;
		
	}
        public function copyTareaQueContinua($id_tarea)
	{
            $sql="INSERT INTO gr_tareas(usuario_alta, usuario_responsable, id_tipo_herramienta, id_herramienta, id_estado, hallazgo, id_grado_crit, tarea, fecha_vto, estado_vto, fecha_accion, observacion, editada, usuario_relacionado, id_tarea_padre, tipo, rpd) 
                                select  usuario_alta, usuario_responsable, id_tipo_herramienta, id_herramienta,         1, hallazgo, id_grado_crit, tarea, fecha_vto, estado_vto, fecha_accion, observacion, editada, usuario_relacionado, id_tarea,       tipo, rpd  from gr_tareas 
                    where id_tarea=".$id_tarea." limit 1";
            $copy=$this->db->query($sql);
//            echo $this->db->last_query();
//            return $copy;
            if(!$copy)
                    return 0;
            else
                    return $this->db->insert_id();
		
	}
        public function updateGestion($id,$estado,$obs="")
	{
            $this->db->set('fecha_accion', 'now()',false);
            $this->db->set('id_estado', $estado);
            if ($obs!="")
                $this->db->set('observacion', $obs);
                
            $this->db->where('id_tarea', $id);
            $edit=$this->db->update('gr_tareas');
//            echo $this->db->last_query();
            if(!$edit)
                    return false;
            else
                    return true;
		
	}
        public function setObsoleta($id,$id_usuario,$obs="")
	{
            $this->db->set('id_estado', 8);
            $this->db->set('fecha_obsoleta', 'now()',false);
            $this->db->set('usuario_obsoleta', $id_usuario);
            if ($obs!="")
                $this->db->set('obs_obsoleta', $obs);
                
            $this->db->where('id_tarea', $id);
            $edit=$this->db->update('gr_tareas');
//            echo $this->db->last_query();
            if(!$edit)
                    return false;
            else
                    return true;
		
	}
        public function verificaRePost($datos)
        {
            $this->db->select('count(id_tarea) as cant',false);
            $this->db->from('gr_tareas');
            $this->db->where("hallazgo",$datos['hallazgo']);
            $this->db->where("tarea",$datos['tarea']);
            if(isset($datos['usuario_alta']))
                $this->db->where("usuario_alta",$datos['usuario_alta']);
            $this->db->where("usuario_responsable",$datos['usuario_responsable']);
            $this->db->where("id_estado",1);
            if(isset($datos['usuario_relacionado']))
                $this->db->where("usuario_relacionado",$datos['usuario_relacionado']);
            $query = $this->db->get();
            $res = $query->row();
            $num=count($res);
            if ($num > 0)
            {
                    return $res->cant;
            }
            else
                    return 0;
        }
        public function dameTareasParaCancelarBC($id_bc)
        {
            $estados=array(1,5);
            $this->db->select('t.id_tarea');
            $this->db->from('gr_tareas t');
            $this->db->where('id_tipo_herramienta',3);
            $this->db->where('id_herramienta',$id_bc);
            $this->db->where_in('id_estado',$estados);
             $query = $this->db->get();
//        echo $this->db->last_query();
            $res = $query->result_array();
            $num = count($res);
            if ($num > 0)
            {
                    return $res;
            }
            else
                    return 0;
        }
        
    public function dameRevisionParaForm ($id_tarea)
    {
        $this->db->select("t.rpd as tareaRevisionRadios");
        $this->db->from('gr_tareas t');
        $this->db->where('t.id_tarea',$id_tarea);
        $query=$this->db->get();
//        $row = $query->row();
//                 echo $this->db->last_query();
        $num = $query->num_rows();
        $res = $query->result_array();
        if ($num > 0)
        {
            return "{ 'success': true, 'msg': 'dada', 'data': ".json_encode($res[0])."}";
        }
        else
            return 0;
    }
}	
?>