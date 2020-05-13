<?php
class Gr_tareas_model extends Model
{
    public function listadoParaHerramientas($usuario, $start, $limit, $filtros, $busqueda, $campos,$sort="", $dir="", $herramienta)
    {
            $this->db->select(' t.id_tarea, t.usuario_responsable as id_responsable, t.usuario_alta as id_usuario_alta, t.hallazgo, t.tarea,t.id_estado,t.editada,t.observacion as obs');
            $this->db->select("concat(p.nombre,' ',p.apellido) as usuario_alta",false);
            $this->db->select("concat(p2.nombre,' ',p2.apellido) as usuario_responsable",false);
            $this->db->select("DATE_FORMAT(t.fecha_alta,'%d/%m/%Y') as fecha_alta", FALSE);
            $this->db->select("DATE_FORMAT(t.fecha_vto,'%d/%m/%Y') as fecha_vto", FALSE);
            $this->db->select("DATE_FORMAT(t.fecha_accion,'%d/%m/%Y') as fecha_accion", FALSE);
            $this->db->select(" e.estado as estado",FALSE);
            $this->db->select(" th.abv as fuente",FALSE);
//            $this->db->select('a.area');
            $this->db->select("concat(ee.abv,'-',a.area) as area",false);
            $this->db->select('t.id_grado_crit');
            $this->db->select(" CASE 
                                WHEN t.id_grado_crit=1 THEN 'Critica'
                                WHEN t.id_grado_crit=2 THEN 'Alta'
                                WHEN t.id_grado_crit=3 THEN 'Menor'
                                ELSE '-.-'
                            END as grado_crit",FALSE);
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
            $this->db->where('t.id_herramienta',$herramienta['id']);
            $this->db->where('t.id_tipo_herramienta',$herramienta['id_tipo']);

            if ($filtros!="")
            {
                if (isset($filtros[0]) && $filtros[0]!="")
                    $this->db->where('t.id_estado',$filtros[0]);
                if (isset($filtros[1]) && $filtros[1]!="")
                    $this->db->where('t.id_tipo_herramienta',$filtros[1]);
            }
            if ($busqueda!="" && count($campos)>0)
            {
                foreach ($campos as &$campo)
                {
                    switch ($campo)
                    {
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
                    }
                }
                unset($campo);
//                    echo "<pre>". print_r($campos,true)."<pre>";
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

            }
            if($usuario['gr']!=1)
            {
//                $this->db->where_in('(pto2.id_area',$usuario['areas_inferiores'],FALSE);
//                $this->db->or_where_in('pto.id_area',$usuario['areas_inferiores'].")",FALSE);
                $this->db->where("(pto2.id_area in(".$usuario['areas_inferiores'].") or pto.id_area in (".$usuario['areas_inferiores']."))");

            }
            if ($sort!="")
            {
                if ($sort=='id_tarea')
                    $sort="t.".$sort;
                else
                    $sort="t.".$sort;
                $this->db->order_by($sort, $dir);
            }
            else
                $this->db->order_by("field(t.usuario_responsable,".$usuario['usuario']['id_usuario'].")", "desc"); 
                $this->db->order_by("field(t.id_estado,"."3".")", "desc"); 
                $this->db->order_by("id_tarea", "desc"); 
//    /            $this->db->order_by("t.id_tarea", "asc"); 
            $this->db->limit($limit,$start); 

            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $this->cantSql('t.id_tarea',$this->db->last_query());
            $res = $query->result_array();
            if ($num > 0)
            {
                return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
            }
            else
                return '({"total":"0","rows":""})';
        }
    public function listadoObsoletas($usuario, $start, $limit, $filtros="", $busqueda="", $campos="",$sort="", $dir="")
    {
        $this->db->select('t.id_tarea, t.usuario_responsable as id_responsable, t.usuario_alta as id_usuario_alta, t.hallazgo, t.tarea,t.id_estado,t.editada,t.observacion as obs');
        $this->db->select('t.id_tipo_herramienta as th');
        $this->db->select("concat(p.nombre,' ',p.apellido) as usuario_alta",false);
        $this->db->select("concat(p2.nombre,' ',p2.apellido) as usuario_responsable",false);
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
        
       $this->db->where('t.id_estado',8);

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
    public function x_listadoObsoletas($usuario, $start, $limit, $filtros, $busqueda, $campos,$sort="", $dir="")
    {
            $this->db->select(' t.id_tarea, t.usuario_responsable as id_responsable, t.usuario_alta as id_usuario_alta, t.hallazgo, t.tarea,t.id_estado,t.editada,t.observacion as obs,t.obs_obsoleta as obs_ob');
            $this->db->select("concat(p.nombre,' ',p.apellido) as usuario_alta",false);
            $this->db->select("concat(p2.nombre,' ',p2.apellido) as usuario_responsable",false);
            $this->db->select("DATE_FORMAT(t.fecha_alta,'%d/%m/%Y') as fecha_alta", FALSE);
            $this->db->select("DATE_FORMAT(t.fecha_vto,'%d/%m/%Y') as fecha_vto", FALSE);
            $this->db->select("DATE_FORMAT(t.fecha_accion,'%d/%m/%Y') as fecha_accion", FALSE);
            $this->db->select(" e.estado as estado",FALSE);
            $this->db->select(" th.abv as fuente",FALSE);
//            $this->db->select('a.area');
            $this->db->select("concat(ee.abv,'-',a.area) as area",false);
            $this->db->select('t.id_grado_crit');
            $this->db->select(" CASE 
                                WHEN t.id_grado_crit=1 THEN 'Critica'
                                WHEN t.id_grado_crit=2 THEN 'Alta'
                                WHEN t.id_grado_crit=3 THEN 'Menor'
                                ELSE '-.-'
                            END as grado_crit",FALSE);
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
            $this->db->where('t.id_estado',8); //tareas en estado obsoleto

//            if ($filtros!="")
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
                    }
                }
                unset($campo);
//                    echo "<pre>". print_r($campos,true)."<pre>";
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

            }
            if($usuario['gr']!=1)
            {
//                $this->db->where_in('(pto2.id_area',$usuario['areas_inferiores'],FALSE);
//                $this->db->or_where_in('pto.id_area',$usuario['areas_inferiores'].")",FALSE);
                $this->db->where("(pto2.id_area in(".$usuario['areas_inferiores'].") or pto.id_area in (".$usuario['areas_inferiores']."))");

            }
            if ($sort!="")
            {
                switch ($sort)
                {
                    case 'id_tarea':
                        $ordenar="t.id_tarea";
                        break;
                    case 'grado_crit':
                        $ordenar="field(t.id_grado_crit,2,1,3,0)";
                        break;
                    default:
                        $ordenar="t.id_tarea";

                }
                $this->db->order_by($ordenar, $dir);
            }
        
            else
            {
                $this->db->order_by("field(t.usuario_responsable,".$usuario['usuario']['id_usuario'].")", "desc"); 
                $this->db->order_by("field(t.id_estado,"."3".")", "desc"); 
                $this->db->order_by("id_tarea", "desc"); 
//    /            $this->db->order_by("t.id_tarea", "asc"); 
            }
            $this->db->limit($limit,$start); 

            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $this->cantSql('t.id_tarea',$this->db->last_query());
            $res = $query->result_array();
            if ($num > 0)
            {
                return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
            }
            else
                return '({"total":"0","rows":""})';
        }
    public function listadoParaHerramientasRmc($usuario, $start, $limit, $filtros, $busqueda, $campos,$sort="", $dir="", $herramienta)
    {
        $this->db->select(' t.id_tarea, t.usuario_responsable as id_responsable, t.usuario_alta as id_usuario_alta, t.hallazgo, t.tarea,t.id_estado,t.editada,t.observacion as obs');
        $this->db->select("concat(p.nombre,' ',p.apellido) as usuario_alta",false);
        $this->db->select("concat(p2.nombre,' ',p2.apellido) as usuario_responsable",false);
        $this->db->select("DATE_FORMAT(t.fecha_alta,'%d/%m/%Y') as fecha_alta", FALSE);
        $this->db->select("DATE_FORMAT(t.fecha_vto,'%d/%m/%Y') as fecha_vto", FALSE);
        $this->db->select("DATE_FORMAT(t.fecha_accion,'%d/%m/%Y') as fecha_accion", FALSE);
        $this->db->select(" e.estado as estado",FALSE);
        $this->db->select(" th.abv as fuente",FALSE);
//            $this->db->select('a.area');
        $this->db->select("concat(ee.abv,'-',a.area) as area",false);
        $this->db->select('t.id_grado_crit');
        $this->db->select(" CASE 
                            WHEN t.id_grado_crit=1 THEN 'Critica'
                            WHEN t.id_grado_crit=2 THEN 'Alta'
                            WHEN t.id_grado_crit=3 THEN 'Menor'
                            ELSE '-.-'
                        END as grado_crit",FALSE);
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
        $this->db->where('t.id_herramienta',$herramienta['id']);
        $this->db->where('t.id_tipo_herramienta',$herramienta['id_tipo']);

        if ($filtros!="")
        {
            if (isset($filtros[0]) && $filtros[0]!="")
                $this->db->where('t.id_estado',$filtros[0]);
            if (isset($filtros[1]) && $filtros[1]!="")
                $this->db->where('t.id_tipo_herramienta',$filtros[1]);
        }
        if ($busqueda!="" && count($campos)>0)
        {
            foreach ($campos as &$campo)
            {
                switch ($campo)
                {
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
                }
            }
            unset($campo);
//                    echo "<pre>". print_r($campos,true)."<pre>";
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

        }
        if ($sort!="")
        {
            if ($sort=='id_tarea')
                $sort="t.".$sort;
            else
                $sort="t.".$sort;
            $this->db->order_by($sort, $dir);
        }
        else
            $this->db->order_by("field(t.usuario_responsable,".$usuario['usuario']['id_usuario'].")", "desc"); 
            $this->db->order_by("field(t.id_estado,"."3".")", "desc"); 
            $this->db->order_by("id_tarea", "desc"); 
//    /            $this->db->order_by("t.id_tarea", "asc"); 
        $this->db->limit($limit,$start); 

        $query = $this->db->get();
//            echo $this->db->last_query();
        $num = $this->cantSql('t.id_tarea',$this->db->last_query());
        $res = $query->result_array();
        if ($num > 0)
        {
            return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
        }
        else
            return '({"total":"0","rows":""})';
    }                                      
    public function listadoParaHerramientasCpp($usuario, $start, $limit, $filtros, $busqueda, $campos,$sort="", $dir="", $herramienta)
    {
            $this->db->select(' t.id_tarea, t.usuario_responsable as id_responsable, t.usuario_alta as id_usuario_alta, t.hallazgo, t.tarea,t.id_estado,t.editada,t.observacion as obs');
            $this->db->select(' t.id_herramienta');
            $this->db->select(' t.eficiencia as id_eficiencia');
            $this->db->select("concat(p.nombre,' ',p.apellido) as usuario_alta",false);
            $this->db->select("concat(p2.nombre,' ',p2.apellido) as usuario_responsable",false);
            $this->db->select("DATE_FORMAT(t.fecha_alta,'%d/%m/%Y') as fecha_alta", FALSE);
            $this->db->select("DATE_FORMAT(t.fecha_vto,'%d/%m/%Y') as fecha_vto", FALSE);
            $this->db->select("DATE_FORMAT(t.fecha_accion,'%d/%m/%Y') as fecha_accion", FALSE);
            $this->db->select(" e.estado as estado",FALSE);
            $this->db->select(" th.abv as fuente",FALSE);
//            $this->db->select('a.area');
            $this->db->select("concat(ee.abv,'-',a.area) as area",false);
            $this->db->select('t.id_grado_crit');
            $this->db->select(" CASE 
                                WHEN t.id_grado_crit=1 THEN 'Critica'
                                WHEN t.id_grado_crit=2 THEN 'Alta'
                                WHEN t.id_grado_crit=3 THEN 'Menor'
                                ELSE '-.-'
                            END as grado_crit",FALSE);
	$this->db->select("(select GROUP_CONCAT(id_archivo) from gr_archivos a where a.eliminado=0 and a.id_tarea=t.id_tarea)as archivos", FALSE);
        $this->db->select("(select GROUP_CONCAT(archivo_nom_orig) from gr_archivos a2 where a2.eliminado=0 and a2.id_tarea=t.id_tarea) as archivos_qtip", FALSE);
        $this->db->select("(select GROUP_CONCAT(extension) from gr_archivos a3 where a3.eliminado=0 and a3.id_tarea=t.id_tarea) as archivos_ext", FALSE);
			    		    
            $this->db->select(" CASE 
                                WHEN t.eficiencia is NULL THEN '-.-'
                                WHEN t.eficiencia=0 THEN 'Pendiente'
                                WHEN t.eficiencia=1 THEN 'Eficiente'
                                WHEN t.eficiencia=2 THEN 'Rechazada'
                                ELSE '-.-'
                            END as eficiencia",FALSE);
//            $this->db->select("if (,1,0) as btn_eliminar",false);
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
            
            if(count($herramienta['id']) == 0)
            {
                $herramienta['id'] = 0;
                $this->db->where_in('t.id_herramienta',$herramienta['id']);
                $this->db->where('t.id_tipo_herramienta',$herramienta['id_tipo']);
            }
            else
            {
                $this->db->where_in('t.id_herramienta',$herramienta['id']);
                $this->db->where('t.id_tipo_herramienta',$herramienta['id_tipo']);
                
            }

            if ($filtros!="")
            {
                if (isset($filtros[0]) && $filtros[0]!="")
                    $this->db->where('t.id_estado',$filtros[0]);
                if (isset($filtros[1]) && $filtros[1]!="")
                    $this->db->where('t.id_tipo_herramienta',$filtros[1]);
            }
            if ($busqueda!="" && count($campos)>0)
            {
                foreach ($campos as &$campo)
                {
                    switch ($campo)
                    {
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
                    }
                }
                unset($campo);
//                    echo "<pre>". print_r($campos,true)."<pre>";
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

            }
            if ($sort!="")
            {
                if ($sort=='id_tarea')
                    $sort="t.".$sort;
                else
                    $sort="t.".$sort;
                $this->db->order_by($sort, $dir);
            }
            else
//                $this->db->order_by("field(t.usuario_responsable,".$usuario['usuario']['id_usuario'].")", "desc"); 
//                $this->db->order_by("field(t.id_estado,"."3".")", "desc"); 
                $this->db->order_by("id_tarea", "desc"); 
//    /            $this->db->order_by("t.id_tarea", "asc"); 
            $this->db->limit($limit,$start); 

            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $this->cantSql('t.id_tarea',$this->db->last_query());
            $res = $query->result_array();
            if ($num > 0)
            {
                return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
            }
            else
                return '({"total":"0","rows":""})';
        }
    public function contarTareasCppEvaluadas($id_evento)
    {
            $this->db->select('count(t.id_tarea) as cant');
            $this->db->from('gr_tareas t');
            $this->db->join('cpp_causas c','c.id_causa = t.id_herramienta','inner');
//            $this->db->join('cpp eventos e','e.id_evento = c.id_evento','inner');
            $this->db->where('c.id_evento',$id_evento);
            $this->db->where('t.eficiencia !=0',NULL,false);
            $this->db->where('t.eficiencia is NOT NULL',NULL,FALSE);
            $query = $this->db->get();
//            echo $this->db->last_query();
            $res = $query->result();
            if ($res[0]->cant > 0)
            {
                    return $res[0]->cant;
            }
            else
                    return 0;
        }
    public function listadoParaHerramientaRpyc($usuario, $start, $limit, $filtros, $busqueda, $campos,$sort="", $dir="", $herramienta)
    {
        $this->db->select(' t.id_tarea, t.usuario_responsable as id_responsable, t.usuario_alta as id_usuario_alta, t.hallazgo, t.tarea,t.id_estado,t.editada,t.observacion as obs');
        $this->db->select("concat(p.nombre,' ',p.apellido) as usuario_alta",false);
        $this->db->select("concat(p2.nombre,' ',p2.apellido) as usuario_responsable",false);
        $this->db->select("DATE_FORMAT(t.fecha_alta,'%d/%m/%Y') as fecha_alta", FALSE);
        $this->db->select("DATE_FORMAT(t.fecha_vto,'%d/%m/%Y') as fecha_vto", FALSE);
        $this->db->select("DATE_FORMAT(t.fecha_accion,'%d/%m/%Y') as fecha_accion", FALSE);
        $this->db->select(" e.estado as estado",FALSE);
        $this->db->select(" th.abv as fuente",FALSE);
//            $this->db->select('a.area');
        $this->db->select(" CASE 
                            WHEN rpyc.opcion=1 THEN concat('Usuario:',p3.nombre,' ',p3.apellido) 
                            WHEN rpyc.opcion=2 THEN concat('Operario:',rpyc.operario) 
                            WHEN rpyc.opcion=3 THEN concat('Contratista:',co.contratista) 
                            ELSE '?'
                        END as usuario_relacionado",FALSE);
        $this->db->select("concat(ee.abv,'-',a.area) as area",false);
        $this->db->select('t.id_grado_crit');
        $this->db->select(" CASE 
                            WHEN t.id_grado_crit=1 THEN 'Critica'
                            WHEN t.id_grado_crit=2 THEN 'Alta'
                            WHEN t.id_grado_crit=3 THEN 'Menor'
                            ELSE '-.-'
                        END as grado_crit",FALSE);
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
        $this->db->join('gr_tareas_rpyc rpyc','rpyc.id_tarea = t.id_tarea','inner');
        $this->db->join('sys_usuarios u3','u3.id_usuario = rpyc.id_usuario','left');
        $this->db->join('grl_personas p3','p3.id_persona = u3.id_persona','left');
        $this->db->join('gr_contratistas co','co.id_contratista = rpyc.id_contratista','left');
        $this->db->where('t.id_herramienta',$herramienta['id']);
        $this->db->where('t.id_tipo_herramienta',$herramienta['id_tipo']);

        if ($filtros!="")
        {
            if (isset($filtros[0]) && $filtros[0]!="")
                $this->db->where('t.id_estado',$filtros[0]);
            if (isset($filtros[1]) && $filtros[1]!="")
                $this->db->where('t.id_tipo_herramienta',$filtros[1]);
        }
        if ($busqueda!="" && count($campos)>0)
        {
            foreach ($campos as &$campo)
            {
                switch ($campo)
                {
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
                }
            }
            unset($campo);
//                    echo "<pre>". print_r($campos,true)."<pre>";
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

        }
//            if($usuario['gr']!=1)
//            {
////                $this->db->where_in('(pto2.id_area',$usuario['areas_inferiores'],FALSE);
////                $this->db->or_where_in('pto.id_area',$usuario['areas_inferiores'].")",FALSE);
//                $this->db->where("(pto2.id_area in(".$usuario['areas_inferiores'].") or pto.id_area in (".$usuario['areas_inferiores']."))");
//
//            }
        if ($sort!="")
        {
            if ($sort=='id_tarea')
                $sort="t.".$sort;
            else
                $sort="t.".$sort;
            $this->db->order_by($sort, $dir);
        }
        else
        {
//                $this->db->order_by("field(t.usuario_responsable,".$usuario['usuario']['id_usuario'].")", "desc"); 
//                $this->db->order_by("field(t.id_estado,"."3".")", "desc"); 
            $this->db->order_by("id_tarea", "desc"); 
        }
//    /            $this->db->order_by("t.id_tarea", "asc"); 
        $this->db->limit($limit,$start); 

        $query = $this->db->get();
//            echo $this->db->last_query();
        $num = $this->cantSql('t.id_tarea',$this->db->last_query());
        $res = $query->result_array();
        if ($num > 0)
        {
            return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
        }
        else
            return '({"total":"0","rows":""})';
    }
    public function listadoParaHerramientaCap($start, $limit, $filtros, $busqueda, $campos,$sort="", $dir="", $herramienta)
    {
        $this->db->select(' t.id_tarea, t.usuario_responsable as id_responsable, t.usuario_alta as id_usuario_alta, t.hallazgo, t.tarea,t.id_estado');
        $this->db->select("concat(p.nombre,' ',p.apellido) as usuario_alta",false);
        $this->db->select("concat(p2.nombre,' ',p2.apellido) as usuario_responsable",false);
        $this->db->select("DATE_FORMAT(t.fecha_alta,'%d/%m/%Y') as fecha_alta", FALSE);
        $this->db->select("DATE_FORMAT(t.fecha_vto,'%d/%m/%Y') as fecha_vto", FALSE);
        $this->db->select("DATE_FORMAT(t.fecha_accion,'%d/%m/%Y') as fecha_accion", FALSE);
        $this->db->select(" e.estado as estado",FALSE);
        $this->db->select(" th.abv as fuente",FALSE);
        $this->db->select('t.id_grado_crit');
        $this->db->select(" CASE 
                            WHEN t.id_grado_crit=1 THEN 'Critica'
                            WHEN t.id_grado_crit=2 THEN 'Alta'
                            WHEN t.id_grado_crit=3 THEN 'Menor'
                            ELSE '-.-'
                        END as grado_crit",FALSE);
        $this->db->select("(select GROUP_CONCAT(id_archivo) from gr_archivos a where a.eliminado=0 and a.id_tarea=t.id_tarea) as archivos", FALSE);
        $this->db->select("(select GROUP_CONCAT(archivo_nom_orig) from gr_archivos a where a.eliminado=0 and a.id_tarea=t.id_tarea)as archivos_qtip", FALSE);
        $this->db->select("(select GROUP_CONCAT(extension) from gr_archivos a where a.eliminado=0 and a.id_tarea=t.id_tarea)as archivos_ext", FALSE);
//        $this->db->select("if(ha.id_accion=4,(select GROUP_CONCAT(extension) from gr_archivos a3 where a3.eliminado=0 and a3.id_ha=ha.id and a3.id_tarea=".$id_tarea."),'')as archivos_ext", FALSE);
        $this->db->from('gr_tareas t');
        $this->db->join('gr_estados e','e.id_estado = t.id_estado','inner');
        $this->db->join('sys_usuarios u','u.id_usuario = t.usuario_alta','inner');
        $this->db->join('sys_usuarios u2','u2.id_usuario = t.usuario_responsable','inner');
        $this->db->join('grl_personas p','p.id_persona = u.id_persona','inner');
        $this->db->join('grl_personas p2','p2.id_persona = u2.id_persona','inner');
        $this->db->join('gr_tipos_herramientas th','th.id_tipo_herramienta = t.id_tipo_herramienta','inner');
        $this->db->where('t.id_herramienta',$herramienta['id']);
        $this->db->where('t.id_tipo_herramienta',$herramienta['id_tipo']);

        if ($filtros!="")
        {
            if (isset($filtros[0]) && $filtros[0]!="")
                $this->db->where('t.id_estado',$filtros[0]);
            if (isset($filtros[1]) && $filtros[1]!="")
                $this->db->where('t.id_tipo_herramienta',$filtros[1]);
        }
        if ($busqueda!="" && count($campos)>0)
        {
            foreach ($campos as &$campo)
            {
                switch ($campo)
                {
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
                }
            }
            unset($campo);
//                    echo "<pre>". print_r($campos,true)."<pre>";
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

        }
        if ($sort!="")
        {
            if ($sort=='id_tarea')
                $sort="t.".$sort;
            else
                $sort="t.".$sort;
            $this->db->order_by($sort, $dir);
        }
        else
        {
            $this->db->order_by("id_tarea", "desc"); 
        }
        $this->db->limit($limit,$start); 
        $query = $this->db->get();
//        echo $this->db->last_query();
        $num = $this->cantSql('t.id_tarea',$this->db->last_query());
        if ($num > 0)
        {
            $res = $query->result_array();
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

    public function dameTarea($id)
    {
        $this->db->select('*');
        $this->db->select('concat(th.tipo_herramienta," (",th.abv,")") as th', FALSE);
        $this->db->select("DATE_FORMAT(t.fecha_alta,'%d/%m/%Y') as fecha_alta", FALSE);
        $this->db->select("DATE_FORMAT(t.fecha_vto,'%d/%m/%Y') as fecha_vto", FALSE);
        $this->db->select("DATE_FORMAT(t.fecha_accion,'%d/%m/%Y') as fecha_accion", FALSE);
        $this->db->select('DATEDIFF (now(),t.fecha_vto) as dias_vto',false);
//            $this->db->select("DATE_FORMAT(c.fecha_aprobacion,'%d/%m/%Y') as fecha_aprobacion", FALSE);
        $this->db->from('gr_tareas t');
        $this->db->join('gr_tipos_herramientas th','th.id_tipo_herramienta = t.id_tipo_herramienta','inner');
//            $this->db->join('gr_cierrres c','c.id_tarea = t.id_tarea','left');
        $this->db->where("t.id_tarea",$id);
        $query = $this->db->get();
//        echo $this->db->last_query();
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
    public function dameTarea2($id)
    {
        $this->db->select('id_tarea,id_tipo_herramienta,id_herramienta');
        $this->db->from('gr_tareas t');
        $this->db->where("t.id_tarea",$id);
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
    public function dameTareasRmc($id)
    {
        $this->db->select('t.id_tarea,t.tarea,t.hallazgo');
        $this->db->select("concat(p.nombre,' ',p.apellido,' - ',pto.puesto,'(',ee1.abv,')') as usuario_alta",false);
        $this->db->select("concat(p2.nombre,' ',p2.apellido,' - ',pto2.puesto,'(',ee2.abv,')') as usuario_responsable",false);
        $this->db->select("DATE_FORMAT(t.fecha_alta,'%d/%m/%Y') as fecha_alta", FALSE);
        $this->db->select("DATE_FORMAT(t.fecha_vto,'%d/%m/%Y') as fecha_vto", FALSE);
        $this->db->select("DATE_FORMAT(t.fecha_accion,'%d/%m/%Y') as fecha_accion", FALSE);
        $this->db->select('DATEDIFF (now(),t.fecha_vto) as dias_vto',false);
        $this->db->from('gr_tareas t');
        $this->db->join('sys_usuarios u','u.id_usuario = t.usuario_alta','inner');
        $this->db->join('sys_usuarios u2','u2.id_usuario = t.usuario_responsable','inner');
        $this->db->join('gr_usuarios uu','uu.id_usuario = t.usuario_alta','inner');
        $this->db->join('gr_usuarios uu2','uu2.id_usuario = t.usuario_responsable','inner');
        $this->db->join('gr_puestos pto','pto.id_puesto = uu.id_puesto','inner');
        $this->db->join('gr_puestos pto2','pto2.id_puesto = uu2.id_puesto','inner');
        $this->db->join('gr_areas a','a.id_area = pto2.id_area','inner');
        $this->db->join('grl_personas p','p.id_persona = u.id_persona','inner');
        $this->db->join('grl_personas p2','p2.id_persona = u2.id_persona','inner');
        $this->db->join('gr_organigramas or1','or1.id_organigrama = pto.id_organigrama','inner');
        $this->db->join('grl_empresas ee1','ee1.id_empresa = or1.id_empresa','inner');
         $this->db->join('gr_organigramas or2','or2.id_organigrama = pto2.id_organigrama','inner');
        $this->db->join('grl_empresas ee2','ee2.id_empresa = or2.id_empresa','inner');
        $this->db->where("t.id_tipo_herramienta",1);
        $this->db->where("t.id_herramienta",$id);
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
    public function dameTareasED($id)
    {
        $this->db->select('t.id_tarea,t.tarea,t.hallazgo');
        $this->db->select("concat(p.nombre,' ',p.apellido,' - ',pto.puesto,'(',ee1.abv,')') as usuario_alta",false);
        $this->db->select("concat(p2.nombre,' ',p2.apellido,' - ',pto2.puesto,'(',ee2.abv,')') as usuario_responsable",false);
        $this->db->select("DATE_FORMAT(t.fecha_alta,'%d/%m/%Y') as fecha_alta", FALSE);
        $this->db->select("DATE_FORMAT(t.fecha_vto,'%d/%m/%Y') as fecha_vto", FALSE);
        $this->db->select("DATE_FORMAT(t.fecha_accion,'%d/%m/%Y') as fecha_accion", FALSE);
        $this->db->select('DATEDIFF (now(),t.fecha_vto) as dias_vto',false);
        $this->db->from('gr_tareas t');
        $this->db->join('sys_usuarios u','u.id_usuario = t.usuario_alta','inner');
        $this->db->join('sys_usuarios u2','u2.id_usuario = t.usuario_responsable','inner');
        $this->db->join('gr_usuarios uu','uu.id_usuario = t.usuario_alta','inner');
        $this->db->join('gr_usuarios uu2','uu2.id_usuario = t.usuario_responsable','inner');
        $this->db->join('gr_puestos pto','pto.id_puesto = uu.id_puesto','inner');
        $this->db->join('gr_puestos pto2','pto2.id_puesto = uu2.id_puesto','inner');
        $this->db->join('gr_areas a','a.id_area = pto2.id_area','inner');
        $this->db->join('grl_personas p','p.id_persona = u.id_persona','inner');
        $this->db->join('grl_personas p2','p2.id_persona = u2.id_persona','inner');
        $this->db->join('gr_organigramas or1','or1.id_organigrama = pto.id_organigrama','inner');
        $this->db->join('grl_empresas ee1','ee1.id_empresa = or1.id_empresa','inner');
         $this->db->join('gr_organigramas or2','or2.id_organigrama = pto2.id_organigrama','inner');
        $this->db->join('grl_empresas ee2','ee2.id_empresa = or2.id_empresa','inner');
        $this->db->where("t.id_tipo_herramienta",7);
        $this->db->where("t.id_herramienta",$id);
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
    public function dameTareasCPPPorCausa($id_causa)
    {
        $this->db->select('t.id_tarea,t.tarea,t.hallazgo');
        $this->db->select('e.estado');
        $this->db->select("concat(p.nombre,' ',p.apellido,' - ',pto.puesto,'(',ee1.abv,')') as usuario_alta",false);
        $this->db->select("concat(p2.nombre,' ',p2.apellido,' - ',pto2.puesto,'(',ee2.abv,')') as usuario_responsable",false);
        $this->db->select("DATE_FORMAT(t.fecha_alta,'%d/%m/%Y') as fecha_alta", FALSE);
        $this->db->select("DATE_FORMAT(t.fecha_vto,'%d/%m/%Y') as fecha_vto", FALSE);
        $this->db->select("DATE_FORMAT(t.fecha_accion,'%d/%m/%Y') as fecha_accion", FALSE);
        $this->db->select('DATEDIFF (now(),t.fecha_vto) as dias_vto',false);
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
        $this->db->join('gr_organigramas or1','or1.id_organigrama = pto.id_organigrama','inner');
        $this->db->join('grl_empresas ee1','ee1.id_empresa = or1.id_empresa','inner');
         $this->db->join('gr_organigramas or2','or2.id_organigrama = pto2.id_organigrama','inner');
        $this->db->join('grl_empresas ee2','ee2.id_empresa = or2.id_empresa','inner');
        $this->db->where("t.id_tipo_herramienta",8);
        $this->db->where("t.id_herramienta",$id_causa);
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
    public function checkTareasED($id)
    {
        $this->db->select('t.id_tarea');
        $this->db->from('gr_tareas t');
        $this->db->where("t.id_tipo_herramienta",7);
        $this->db->where("t.id_herramienta",$id);
        $query = $this->db->get();
//            echo $this->db->last_query();
        $num=$query->num_rows();
        if ($num > 0)
        {
                return 1;
        }
        else
                return 0;

    }
    public function contarTareasRmc($id)
    {
        $this->db->select('count(t.id_tarea) as cant');
        $this->db->from('gr_tareas t');
        $this->db->where("t.id_tipo_herramienta",1);
        $this->db->where("t.id_herramienta",$id);
        $query = $this->db->get();
//             $res = $query->result_array();
        $res = $query->row();
        if ($res->cant > 0)
        {
                return $res->cant;
        }
        else
                return 0;

    }
    public function dameTareaPorBcYDR($id,$id_usuario_bc)
    {
        $this->db->select('t.id_tarea');
//            $this->db->select("DATE_FORMAT(t.fecha_alta,'%d/%m/%Y') as fecha_alta", FALSE);
//            $this->db->select("DATE_FORMAT(t.fecha_vto,'%d/%m/%Y') as fecha_vto", FALSE);
//            $this->db->select("DATE_FORMAT(t.fecha_accion,'%d/%m/%Y') as fecha_accion", FALSE);
//            $this->db->select('DATEDIFF (now(),t.fecha_vto) as dias_vto',false);
        $this->db->from('gr_tareas t');
        $this->db->where("t.id_herramienta",$id);
        $this->db->where("t.id_tipo_herramienta",3);//tipo de Herramienta de BAjada en Cascada
        $this->db->where("t.usuario_relacionado",$id_usuario_bc);
        $query = $this->db->get();
         $res = $query->row();
//            $res = $query->row();
        $num=count($res);
        if ($num > 0)
        {
                return $res->id_tarea;
        }
        else
                return 0;

    }
    public function dameTareaPanel($id)
    {
        $this->db->select('*');
        $this->db->select("DATE_FORMAT(t.fecha_alta,'%d/%m/%Y') as fecha_alta", FALSE);
        $this->db->select("DATE_FORMAT(t.fecha_vto,'%d/%m/%Y') as fecha_vto", FALSE);
        $this->db->select("DATE_FORMAT(t.fecha_accion,'%d/%m/%Y') as fecha_accion", FALSE);
        $this->db->from('gr_tareas t');
        $this->db->where("t.id_tarea",$id);
        $query = $this->db->get();
         $res = $query->result_array();
//            $res = $query->row();
        $num=count($res);
        if ($num > 0)
        {
                echo "{success: true, msg: 'Se cargaron los datos con &eacute;xito.', data:".str_replace(']','',str_replace('[','',json_encode($res)))."}";
        }
        else
                return '({"total":"0","rows":""})';

    }
    public function verificarResponsable($id_tarea,$id_responsable)
    {
        $estados=array(1,4,10); //abierto, reabierta, Vencida y observada
        $this->db->select('count(id_tarea) as cant',false);
        $this->db->from('gr_tareas');
        $this->db->where("id_tarea",$id_tarea);
        $this->db->where("usuario_responsable",$id_responsable);
        $this->db->where_in("id_estado",$estados);
        $query = $this->db->get();
//            echo $this->db->last_query();
        $res = $query->row();
        if ($res->cant > 0)
            return 1;
        else
            return 0;

    }
    public function verificarEditor($id_tarea,$id_usuario_alta)
    {
        $estados=array(2,3);
        $this->db->select('count(id_tarea) as cant',false);
        $this->db->from('gr_tareas');
        $this->db->where("id_tarea",$id_tarea);
        $this->db->where("usuario_alta",$id_usuario_alta);
        $this->db->where_in("id_estado",$estados);
        $query = $this->db->get();
//            echo $this->db->last_query();
        $res = $query->row();
        if ($res->cant > 0)
            return 1;
        else
            return 0;
    }
    public function dameVencidasHoy()
    {
        $estados=array(1,2,4,10); //Abierta, Informada,Reabierta, Observada
        $this->db->select('t.id_tarea');
//            $this->db->select('DATEDIFF (now(),t.fecha_vto)',false);
        $this->db->from('gr_tareas t');
        $this->db->where_in("t.id_estado",$estados);
        $this->db->where("t.fecha_vto <",'CURRENT_DATE()',false);
        $this->db->where("estado_vto",0);
        $query = $this->db->get();
        $num = $query->num_rows();
        $res = $query->result_array();
        if ($num > 0)
                return $res;
        else
                return 0;
    }
    public function dameProximasAVencer()
    {
        $estados=array(1,2,4,10); //Abierta, Informada,Reabierta, Observada
        $this->db->select('t.id_tarea');
        $this->db->from('gr_tareas t');
        $this->db->where_in("t.id_estado",$estados);
        $this->db->where("CURRENT_DATE()",'DATE_ADD(DATE_FORMAT(fecha_alta, "%Y-%m-%d"),interval if(mod(DATEDIFF(fecha_vto,DATE_FORMAT(fecha_alta, "%Y-%m-%d")),2)=0,DATEDIFF(fecha_vto,DATE_FORMAT(fecha_alta, "%Y-%m-%d"))/2,(DATEDIFF(fecha_vto,DATE_FORMAT(fecha_alta, "%Y-%m-%d"))-1)/2) day)',false);
        $this->db->where("estado_vto",0);
        $query = $this->db->get();
//        echo $this->db->last_query();
        $num = $query->num_rows();
        $res = $query->result_array();
        if ($num > 0)
                return $res;
        else
                return 0;
    }
    public function dameQVencidasHoy()
    {
        $estados=array(1,2,4,10); //Abierta, Informada,Reabierta, Observada
        $this->db->select('count(t.id_tarea) as cant',false);
//            $this->db->select('DATEDIFF (now(),t.fecha_vto)',false);
        $this->db->from('gr_tareas t');
        $this->db->where_in("t.id_estado",$estados);
        $this->db->where("t.fecha_vto <",'CURRENT_DATE()',false);
        $this->db->where("estado_vto",0);
        $query = $this->db->get();
        $num = $query->num_rows();
        $res = $query->result();
        if ($num > 0)
                return $res[0]->cant;
        else
                return 0;
    }
    public function marcarVencidasHoy()
    {
        $estados=array(1,2,4,10); //Abierta, Informada,Reabierta, Observada
         $this->db->set('estado_vto', 1);
        $this->db->where_in("id_estado",$estados);
        $this->db->where("fecha_vto <",'CURRENT_DATE()',false);
        $this->db->where("estado_vto",0);
        $edit=$this->db->update('gr_tareas');
        $num=$this->db->affected_rows();
//            echo $this->db->last_query();
        if(!$edit)
                return 0;
        else
                return $num;
    }
    public function dameVencidas()
    {
        $estadosVencidos=array(1,2,4,10);
        $this->db->select('t.id_tarea');
        $this->db->select('DATEDIFF (now(),t.fecha_vto) as dias_vto',false);
        $this->db->from('gr_tareas t');
        $this->db->where("t.estado_vto",1);
        $this->db->where_in("t.id_estado",$estadosVencidos);
        $query = $this->db->get();
        $num = $query->num_rows();
        $res = $query->result_array();
        if ($num > 0)
                return $res;
        else
                return 0;
    }
    public function dameResponsablesMorosos()
    {
        $estados=array(1,4);
        $this->db->select('t.usuario_responsable');
        $this->db->distinct();
        $this->db->from('gr_tareas t');
        $this->db->where("t.id_estado",5);//id_estado=5 =>Vencidas
        $this->db->orderby("t.usuario_responsable",'asc');
        $query = $this->db->get();
        $num = $query->num_rows();
        $res = $query->result_array();
        if ($num > 0)
                return $res;
        else
                return 0;
    }
    public function cambiarEstado($arrayTareas,$nuevoEstado)
    {
        $this->db->set('id_estado', $nuevoEstado);
        $this->db->where_in('id_tarea', $arrayTareas);
        $edit=$this->db->update('gr_tareas');
//            echo $this->db->last_query();
        if(!$edit)
                return false;
        else
                return true;

    }
    public function copy($id_tarea)
    {
        $sql="INSERT INTO gr_historial(id_tarea, usuario_alta, usuario_responsable, id_tipo_herramienta, id_estado, hallazgo, tarea, fecha_alta, fecha_vto, fecha_accion, observacion) 
                select id_tarea, usuario_alta, usuario_responsable, id_tipo_herramienta, id_estado, hallazgo, tarea, fecha_alta, fecha_vto, fecha_accion, observacion  from gr_tareas 
                where id_tarea=".$id_tarea;
        $copy=$this->db->query($sql);
//            echo $this->db->last_query();
//            return $copy;
        if(!$copy)
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
        $this->db->where("id_tipo_herramienta",$datos['id_tipo_herramienta']);
        $this->db->where("fecha_vto",$datos['fecha_vto']);
        if(isset($datos['id_herramienta']))
            $this->db->where("id_herramienta",$datos['id_herramienta']);
        if(isset($datos['usuario_relacionado']))
            $this->db->where("usuario_relacionado",$datos['usuario_relacionado']);
        $this->db->where("id_estado",1);
        $this->db->where("TIME_TO_SEC(TIMEDIFF( CURRENT_TIMESTAMP,fecha_alta)) <",20);//que el último insert no haya sido anterior a 20seg
        $query = $this->db->get();
        $res = $query->row();
//            echo $this->db->last_query();
        if ($res->cant > 0)
        {
                return $res->cant;
        }
        else
                return 0;
    }
    public function insert($datos)
    {
        $this->db->trans_begin();

        $this->db->insert('gr_tareas',$datos);
        $insert_id = $this->db->insert_id();
//		echo $this->db->last_query();
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
        public function dameTareasAprobadasPorHerramienta($arrayHerramientas)
        {
            $this->db->select('t.id_tarea');
            $this->db->from('gr_tareas t');
            $this->db->where("t.id_tipo_herramienta",8);
            $this->db->where_in("t.id_herramienta",$arrayHerramientas);
            $query = $this->db->get();
            $res = $query->result_array();
//            echo $this->db->last_query();
            $num=$query->num_rows();
            if ($num > 0)
            {
                    return $res;
            }
            else
                    return 0;
            
        }
        public function controlTareasAprobadas($arrayTareas)
        {
            $estadosNoAprobados=array(1,2,3,4,5,10); //Abierta, Informada,Rechazada,Reabierta,Vencida, Observada
            $this->db->select('t.id_tarea');
            $this->db->from('gr_tareas t');
            $this->db->where_in("t.id_estado ",$estadosNoAprobados);
            $this->db->where_in("t.id_tarea",$arrayTareas);
            $query = $this->db->get();
//            echo $this->db->last_query();
            $num=$query->num_rows();
            if ($num == 0)
            {
                    return 1;
            }
            else
                    return 0;
        }
        
    public function verificaRePostCap($datos)
    {
        $this->db->select('count(id_tarea) as cant',false);
        $this->db->from('gr_tareas');
        $this->db->where("hallazgo",$datos['hallazgo']);
        $this->db->where("tarea",$datos['tarea']);
        if(isset($datos['usuario_alta']))
            $this->db->where("usuario_alta",$datos['usuario_alta']);
        $this->db->where("usuario_responsable",$datos['usuario_responsable']);
        $this->db->where("id_tipo_herramienta",$datos['id_tipo_herramienta']);
        $this->db->where("fecha_vto",$datos['fecha_vto']);
        if(isset($datos['id_herramienta']))
            $this->db->where("id_herramienta",$datos['id_herramienta']);
        if(isset($datos['usuario_relacionado']))
            $this->db->where("usuario_relacionado",$datos['usuario_relacionado']);
        $this->db->where("id_estado",1);
        $this->db->where("TIME_TO_SEC(TIMEDIFF( CURRENT_TIMESTAMP,fecha_alta))/3600 <",24);//que el último insert no haya sido anterior a 24hs
        $query = $this->db->get();
        $res = $query->row();
//            echo $this->db->last_query();
        if ($res->cant > 0)
        {
                return $res->cant;
        }
        else
                return 0;
    }
    public function listadoParaOmc($usuario, $start, $limit, $filtros, $busqueda, $campos,$sort="", $dir="", $herramienta)
    {
            $this->db->select(' t.id_tarea, t.usuario_responsable as id_responsable, t.usuario_alta as id_usuario_alta, t.hallazgo, t.tarea,t.id_estado,t.editada,t.observacion as obs');
            $this->db->select("concat(p.nombre,' ',p.apellido) as usuario_alta",false);
            $this->db->select("concat(p2.nombre,' ',p2.apellido) as usuario_responsable",false);
            $this->db->select("DATE_FORMAT(t.fecha_alta,'%d/%m/%Y') as fecha_alta", FALSE);
            $this->db->select("DATE_FORMAT(t.fecha_vto,'%d/%m/%Y') as fecha_vto", FALSE);
            $this->db->select("DATE_FORMAT(t.fecha_accion,'%d/%m/%Y') as fecha_accion", FALSE);
            $this->db->select(" e.estado as estado",FALSE);
            $this->db->select(" th.abv as fuente",FALSE);
//            $this->db->select('a.area');
            $this->db->select("concat(ee.abv,'-',a.area) as area",false);
            $this->db->select('t.id_grado_crit');
            $this->db->select(" CASE 
                                WHEN t.id_grado_crit=1 THEN 'Critica'
                                WHEN t.id_grado_crit=2 THEN 'Alta'
                                WHEN t.id_grado_crit=3 THEN 'Menor'
                                ELSE '-.-'
                            END as grado_crit",FALSE);
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
            $this->db->where('t.id_herramienta',$herramienta['id']);
            $this->db->where('t.id_tipo_herramienta',$herramienta['id_tipo']);

            if ($filtros!="")
            {
                if (isset($filtros[0]) && $filtros[0]!="")
                    $this->db->where('t.id_estado',$filtros[0]);
                if (isset($filtros[1]) && $filtros[1]!="")
                    $this->db->where('t.id_tipo_herramienta',$filtros[1]);
            }
            if ($busqueda!="" && count($campos)>0)
            {
                foreach ($campos as &$campo)
                {
                    switch ($campo)
                    {
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
                    }
                }
                unset($campo);
//                    echo "<pre>". print_r($campos,true)."<pre>";
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

            }
//            if($usuario['gr']!=1)// COMENTADO POR PEDIDO DE GDR 25/07/2018 
//            {
////                $this->db->where_in('(pto2.id_area',$usuario['areas_inferiores'],FALSE);
////                $this->db->or_where_in('pto.id_area',$usuario['areas_inferiores'].")",FALSE);
//                $this->db->where("(pto2.id_area in(".$usuario['areas_inferiores'].") or pto.id_area in (".$usuario['areas_inferiores']."))");
//
//            }
            if ($sort!="")
            {
                if ($sort=='id_tarea')
                    $sort="t.".$sort;
                else
                    $sort="t.".$sort;
                $this->db->order_by($sort, $dir);
            }
            else
                $this->db->order_by("field(t.usuario_responsable,".$usuario['usuario']['id_usuario'].")", "desc"); 
                $this->db->order_by("field(t.id_estado,"."3".")", "desc"); 
                $this->db->order_by("id_tarea", "desc"); 
//    /            $this->db->order_by("t.id_tarea", "asc"); 
            $this->db->limit($limit,$start); 

            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $this->cantSql('t.id_tarea',$this->db->last_query());
            $res = $query->result_array();
            if ($num > 0)
            {
                return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
            }
            else
                return '({"total":"0","rows":""})';
    }
    public function update($id, $datos)
    {
        $this->db->where("id_tarea",$id);
        if ($this->db->update("gr_tareas",$datos))
        {
//            echo $this->db->last_query();
            return true;
        }
        else
        {
//            echo $this->db->last_query();
            return false;
        }
    }
        
}	
?>