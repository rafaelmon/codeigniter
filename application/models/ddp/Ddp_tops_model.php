<?php
class Ddp_tops_model extends CI_Model
{
    public function listado($start, $limit, $filtros, $busqueda, $campos,$sort="", $dir="")
    {
//            
    }
//    public function listado_supervisor($start, $limit, $filtros, $busqueda, $campos,$sort="", $dir="",$id_usuario)
//    {
//        $this->db->select('t.id_top,t.habilitado');
//        $this->db->select('e.estado');
//        $this->db->select('p.periodo');
//        $this->db->select('pto.puesto');
//         $this->db->select("concat(pp.nombre,' ',pp.apellido) as usuario",false);
//         $this->db->select("DATE_FORMAT(t.fecha_alta,'%d/%m/%Y') as fecha_alta", FALSE);
//         $this->db->select("(select count(distinct(so.id_objetivo)) 
//                    from ddp_objetivos so
//                    where so.id_top=t.id_top
//                    ) as q_obj",false);
//            $this->db->select("(select sum(so.peso) 
//                    from ddp_objetivos so 
//                    where so.id_top=t.id_top
//                    ) as s_pesos",false);
//        $this->db->select("(select count(distinct(so.id_objetivo)) 
//                    from ddp_objetivos so
//                    where so.id_top=t.id_top
//                    and so.id_estado in(2,3)) as q_obj_sup",false);
//        $this->db->select("(select count(distinct(so.id_objetivo)) 
//                    from ddp_objetivos so
//                    where so.id_top=t.id_top
//                    and so.aceptado=1) as q_obj_aprob",false);
//        $this->db->from('ddp_tops t');
//        $this->db->join('ddp_periodos p','p.id_periodo = t.id_periodo','inner');
//        $this->db->join('ddp_estados_top e','e.id_estado = t.id_estado','inner');
//        $this->db->join('sys_usuarios u','u.id_usuario = t.id_usuario','inner');
//        $this->db->join('grl_personas pp','pp.id_persona = u.id_persona','inner');
//        $this->db->join('gr_usuarios gu','gu.id_usuario = u.id_usuario','inner');
//        $this->db->join('gr_puestos pto','pto.id_puesto = gu.id_puesto','inner');
//        $this->db->join('gr_areas a','a.id_area = pto.id_area','inner');
//        $this->db->where("t.id_supervisor",$id_usuario);
//        $query = $this->db->get();
////        echo $this->db->last_query();
//        $res = $query->result_array();
//        $num=count($res);
//        if ($num > 0)
//        {
//            return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
//        }
//        else
//            return '({"total":"0","rows":""})';
////            
//    }
    public function listado_supervisor($start, $limit, $filtros, $busqueda, $campos,$sort="", $dir="",$id_usuario,$periodo)
    {
        $this->db->select("concat(p.nombre,' ',p.apellido) as usuario",false);
        $this->db->select('pto.puesto');
        $this->db->select('t.id_top');
        $this->db->select('t.id_estado');
        $this->db->select(" CASE 
                                WHEN e.estado!='' THEN e.estado
                                ELSE 'Pendiente'
                            END as estado",FALSE);
        $this->db->select(" CASE 
                                WHEN pp.periodo!='' THEN pp.periodo
                                ELSE 'Pendiente'
                            END as periodo",FALSE);
        $this->db->select("DATE_FORMAT(t.fecha_alta,'%d/%m/%Y') as fecha_alta", FALSE);
        $this->db->select("(select count(distinct(so.id_objetivo)) 
                    from ddp_objetivos so
                    where so.id_top=t.id_top
                    ) as q_obj",false);
        $this->db->select("(select sum(so.peso) 
                    from ddp_objetivos so 
                    where so.id_top=t.id_top
                    ) as s_pesos",false);
        $this->db->select("(select count(distinct(so.id_objetivo)) 
                    from ddp_objetivos so
                    where so.id_top=t.id_top
                    and so.id_estado in(2,3)) as q_obj_sup",false);
        $this->db->select("(select count(distinct(so.id_objetivo)) 
                    from ddp_objetivos so
                    where so.id_top=t.id_top
                    and so.aceptado=1) as q_obj_aprob",false);
        $this->db->select("(select sum(((so.peso*so.real1)/100)) 
                    from ddp_objetivos so 
                    where so.id_top=t.id_top
                    ) as s_pesoreal",false);
        $this->db->from('ddp_tops t');
        $this->db->join('sys_usuarios u','u.id_usuario = t.id_usuario','inner');
        $this->db->join('grl_personas p','p.id_persona = u.id_persona','inner');
        $this->db->join('gr_usuarios gu','gu.id_usuario = u.id_usuario','inner');
        $this->db->join('gr_puestos pto','pto.id_puesto = gu.id_puesto','inner');
        $this->db->join('ddp_periodos pp','pp.id_periodo = t.id_periodo','left');
        $this->db->join('ddp_estados_top e','e.id_estado = t.id_estado','left');
        
        $this->db->where("t.habilitado",1);
        $this->db->where("t.id_supervisor",$id_usuario);

        if ($periodo!=-1)
        {
            if ($periodo['activo']==1)
            {
                $this->db->where("(t.id_periodo=".$periodo['id_periodo']." or t.id_periodo='')");
            }
            else
                $this->db->where("t.id_periodo",$periodo['id_periodo']);
        }
        switch ($sort)
        {
            case 'estado':
                $this->db->order_by("estado", $dir); 
                break;
            case 'usuario':
                $this->db->order_by("concat(p.nombre,' ',p.apellido)", $dir); 
                break;
            case 'periodo':
                $this->db->order_by("periodo", $dir); 
                break;
        }
        $query = $this->db->get();
//        echo $this->db->last_query();
        $res = $query->result_array();
        $num=count($res);
        if ($num > 0)
        {
            return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
        }
        else
            return '({"total":"0","rows":""})';
//            
    }
    public function listado_aprobador($start, $limit, $filtros, $busqueda, $campos,$sort="", $dir="",$id_usuario,$periodo)
    {
        $this->db->select("concat(p.nombre,' ',p.apellido) as usuario",false);
        $this->db->select('t.id_supervisor');
        $this->db->select('t.id_estado');
        $this->db->select("concat(ps.nombre,' ',ps.apellido) as supervisor",false);
        $this->db->select('pto.puesto');
        $this->db->select('t.id_top');
        $this->db->select(" CASE 
                                WHEN e.estado!='' THEN e.estado
                                ELSE 'Pendiente'
                            END as estado",FALSE);
        $this->db->select(" CASE 
                                WHEN pp.periodo!='' THEN pp.periodo
                                ELSE 'Pendiente'
                            END as periodo",FALSE);
        $this->db->select("DATE_FORMAT(t.fecha_alta,'%d/%m/%Y') as fecha_alta", FALSE);
        $this->db->select("(select count(distinct(so.id_objetivo)) 
                    from ddp_objetivos so
                    where so.id_top=t.id_top
                    ) as q_obj",false);
        $this->db->select("(select sum(so.peso) 
                    from ddp_objetivos so 
                    where so.id_top=t.id_top
                    ) as s_pesos",false);
        $this->db->select("(select count(distinct(so.id_objetivo)) 
                    from ddp_objetivos so
                    where so.id_top=t.id_top
                    and so.id_estado in(2,3)) as q_obj_sup",false);
        $this->db->select("(select count(distinct(so.id_objetivo)) 
                    from ddp_objetivos so
                    where so.id_top=t.id_top
                    and so.aceptado=1) as q_obj_aprob",false);
        $this->db->select("(select sum(((so.peso*so.real1)/100)) 
                    from ddp_objetivos so 
                    where so.id_top=t.id_top
                    ) as s_pesoreal",false);
        $this->db->from('ddp_tops t');
        $this->db->join('sys_usuarios u','u.id_usuario = t.id_usuario','inner');
        $this->db->join('grl_personas p','p.id_persona = u.id_persona','inner');
        $this->db->join('gr_usuarios gu','gu.id_usuario = u.id_usuario','inner');
        $this->db->join('gr_puestos pto','pto.id_puesto = gu.id_puesto','inner');
        $this->db->join('ddp_periodos pp','pp.id_periodo = t.id_periodo','left');
        $this->db->join('ddp_estados_top e','e.id_estado = t.id_estado','left');
        $this->db->join('sys_usuarios us','us.id_usuario = t.id_supervisor','inner');
        $this->db->join('grl_personas ps','ps.id_persona = us.id_persona','inner');
        $this->db->where("t.habilitado",1);
        $this->db->where("t.id_aprobador",$id_usuario);
        $this->db->where("t.id_usuario !=",$id_usuario, false);
        $this->db->where("t.id_supervisor !=",$id_usuario, false);

        if ($periodo!=-1)
        {
            if ($periodo['activo']==1)
            {
                $this->db->where("(t.id_periodo=".$periodo['id_periodo']." or t.id_periodo='')");
            }
            else
                $this->db->where("t.id_periodo",$periodo['id_periodo']);
        }
        switch ($sort)
        {
            case 'estado':
                $this->db->order_by("estado", $dir); 
                break;
            case 'usuario':
                $this->db->order_by("concat(p.nombre,' ',p.apellido)", $dir); 
                break;
            case 'periodo':
                $this->db->order_by("periodo", $dir); 
                break;
            case 'supervisor':
                $this->db->order_by("3", $dir); 
                break;
            default:
                $this->db->order_by("3", 'asc'); 
                break;
        }
        $query = $this->db->get();
//        echo $this->db->last_query();
        $res = $query->result_array();
        $num=count($res);
        if ($num > 0)
        {
            return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
        }
        else
            return '({"total":"0","rows":""})';
//            
    }
    public function listado_admin($start, $limit, $filtros, $busqueda, $campos,$sort="", $dir="",$id_usuario,$periodo,$id_empresa=0)
    {
         
        $this->db->select("concat(gp.nombre, ' ', gp.apellido) as usuario",false);
        $this->db->select("concat(gp1.nombre, ' ', gp1.apellido) as supervisor",false);
        $this->db->select("concat(gp2.nombre, ' ', gp2.apellido) as aprobador",false);
        $this->db->select('pto.puesto');
        $this->db->select('a.id_organigrama');
        $this->db->select('gcia.area as gerencia');
        $this->db->select('dt.id_top, dt.id_supervisor, dt.id_aprobador');
        $this->db->select('dt.id_estado');
        $this->db->select(" CASE 
                                WHEN de.estado!='' THEN de.estado
                                ELSE 'Pendiente'
                            END as estado",FALSE);
//        $this->db->select('p.periodo');
//        if ($id_periodo!=-1)
        $this->db->select(" CASE 
                                WHEN p.periodo!='' THEN p.periodo
                                ELSE 'Pendiente'
                            END as periodo",FALSE);
        $this->db->select("DATE_FORMAT(dt.fecha_alta,'%d/%m/%Y') as fecha_alta", FALSE);
        $this->db->select("(select count(distinct(so.id_objetivo)) 
                    from ddp_objetivos so
                    where so.id_top=dt.id_top
                    ) as q_obj",false);
        $this->db->select("(select sum(so.peso) 
                    from ddp_objetivos so 
                    where so.id_top=dt.id_top
                    ) as s_pesos",false);
        $this->db->select("(select count(distinct(so.id_objetivo)) 
                    from ddp_objetivos so
                    where so.id_top=dt.id_top
                    and so.id_estado in(2,3)) as q_obj_sup",false);
        $this->db->select("(select count(distinct(so.id_objetivo)) 
                    from ddp_objetivos so
                    where so.id_top=dt.id_top
                    and so.aceptado=1) as q_obj_aprob",false);
        $this->db->select("(select sum(((so.peso*so.real1)/100)) 
                    from ddp_objetivos so 
                    where so.id_top=dt.id_top
                    ) as s_pesoreal",false);
        
        $this->db->from('ddp_tops dt');
        $this->db->join('sys_usuarios su','su.id_usuario = dt.id_usuario','inner');
        $this->db->join('grl_personas gp','gp.id_persona = su.id_persona','inner');
        $this->db->join('gr_usuarios gu','gu.id_usuario = dt.id_usuario','inner');
        $this->db->join('gr_puestos pto','pto.id_puesto = gu.id_puesto','inner');
        $this->db->join('gr_organigramas gor','pto.id_organigrama = gor.id_organigrama','inner');
        $this->db->join('gr_areas a','a.id_area = pto.id_area','inner');
        $this->db->join('gr_areas gcia','gcia.id_area = a.id_gcia','inner');
        $this->db->join('grl_empresas ge','ge.id_empresa = gor.id_empresa','inner');
        $this->db->join('sys_usuarios su1','su1.id_usuario = dt.id_supervisor','inner');
        $this->db->join('grl_personas gp1','gp1.id_persona = su1.id_persona','inner');
        $this->db->join('ddp_estados_top de','de.id_estado = dt.id_estado','left');
        $this->db->join('ddp_periodos p',' p.id_periodo = dt.id_periodo','left');
        
        $this->db->join('sys_usuarios su2','su2.id_usuario = dt.id_aprobador','inner');
        $this->db->join('grl_personas gp2','gp2.id_persona = su2.id_persona','inner');
//        $this->db->join('ddp_estados_top e','e.id_estado = t.id_estado','left');
        
        $empresa = array(1,2);
        $this->db->where("dt.habilitado",1);
        $this->db->where_in("ge.id_empresa",$empresa);
//        $this->db->where("pp.habilitado",1);
//        $this->db->where("gu.habilitado",1);
//        switch ($id_empresa)
//        {
//            case 1:
//                break;
//            case 2:
//                $this->db->where("ge.id_empresa",2);
//                break;
//            case 3:
//                $this->db->where("ge.id_empresa",3);
//                break;
//        }
//        $this->db->where("gu_sup.id_usuario",$id_usuario);
//        if ($periodo!=-1)
//        {
//            if ($periodo['activo']==1)
//            {
//                $this->db->where("(t.id_periodo=".$periodo['id_periodo']." or t.id_periodo='')");
//            }
//            else
//                $this->db->where("t.id_periodo",$periodo['id_periodo']);
//        }
        switch ($sort)
        {
            case 'id_top':
                $this->db->order_by("id_top", $dir); 
                break;
            case 'estado':
                $this->db->order_by("estado", $dir); 
                break;
            case 'usuario':
                $this->db->order_by("concat(gp.nombre,' ',gp.apellido)", $dir); 
                break;
            case 'periodo':
                $this->db->order_by("periodo", $dir); 
                break;
            case 'gerencia':
                $this->db->order_by("gcia.area", $dir); 
                break;
            case 'supervisor':
                $this->db->order_by("concat(gp1.nombre,' ',gp1.apellido)", $dir); 
                break;
            case 'aprobador':
                $this->db->order_by("concat(gp2.nombre, ' ', gp2.apellido)", $dir); 
                break;
        }
         if ($busqueda!="" && count($campos)>0)
            {
                foreach ($campos as &$campo)
                {
                    switch ($campo)
                    {
                        case 'usuario':
                            $campo="concat(gp.nombre,' ',gp.apellido)";
                            break;
                        case 'supervisor':
                            $campo="concat(gp.nombre,' ',gp1.apellido)";
                            break;
                        case 'puesto':
                            $campo="pto.puesto";
                            break;
                        case 'gerencia':
                            $campo="gcia.area";
                            break;
                        case 'aprobador':
                            $campo="concat(gp2.nombre, ' ', gp2.apellido)";
                            break;
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
                    
            }
        $this->db->limit($limit,$start); 
        $query = $this->db->get();
//        echo $this->db->last_query();
        $res = $query->result_array();
        $num=count($res);
         $num = $this->cantSql('su.id_usuario',$this->db->last_query());
        if ($num > 0)
        {
            return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
        }
        else
            return '({"total":"0","rows":""})';
//            
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
    public function insert($datos)
    {

        $this->db->trans_begin();

            $this->db->insert('ddp_tops',$datos);
            $last=$this->db->insert_id();
//            echo $this->db->last_query();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE)
            {
                    $this->db->trans_rollback();
                    return 0;
            }
            else
            {
                    $this->db->trans_commit();
                    return $last;
            }

    }
    public function inser_dimensiones($datos)
    {

        $this->db->trans_begin();

            $this->db->insert('ddp_r_fd_dimensiones',$datos);

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
    public function dameTopPorId($id_top)
    {
        $this->db->select('*');
        $this->db->from('ddp_tops t');
        $this->db->join('ddp_periodos p','p.id_periodo = t.id_periodo','inner');
        $this->db->where("t.id_top",$id_top);
        $query = $this->db->get();
//        echo $this->db->last_query();
        $res = $query->result_array();
        $num=count($res);
        if ($num == 1)
        {
                return $res[0];
        }
        else
                return 0;
        
    }
    public function dameTopPorIdParaPDF($id_top)
    {
        $this->db->select('t.id_top');
        $this->db->select('p.periodo');
        $this->db->select('t.id_estado');
        $this->db->select('et.estado');
        $this->db->select("concat(gp.nombre, ' ', gp.apellido) as usuario",false);
        $this->db->select('pto.puesto as puesto_u',false);
        $this->db->select("concat(gp1.nombre, ' ', gp1.apellido) as supervisor",false);
        $this->db->select('pto1.puesto as puesto_s',false);
        $this->db->select("concat(gp2.nombre, ' ', gp2.apellido) as aprobador",false);
        $this->db->select('pto2.puesto as puesto_a',false);
        $this->db->from('ddp_tops t');
        $this->db->join('ddp_periodos p','p.id_periodo = t.id_periodo','inner');
        $this->db->join('ddp_estados_top et','et.id_estado = t.id_estado','inner');
        $this->db->join('sys_usuarios u','u.id_usuario = t.id_usuario','inner');
        $this->db->join('grl_personas gp','gp.id_persona = u.id_persona','inner');
        $this->db->join('gr_usuarios gu','gu.id_usuario = u.id_usuario','inner');
        $this->db->join('gr_puestos pto','pto.id_puesto = gu.id_puesto','inner');
        $this->db->join('sys_usuarios su1','su1.id_usuario = t.id_supervisor','inner');
        $this->db->join('gr_usuarios gu1','gu1.id_usuario = su1.id_usuario','inner');
        $this->db->join('gr_puestos pto1','pto1.id_puesto = gu1.id_puesto','inner');
        $this->db->join('grl_personas gp1','gp1.id_persona = su1.id_persona','inner');
        $this->db->join('sys_usuarios su2','su2.id_usuario = t.id_aprobador','inner');
        $this->db->join('gr_usuarios gu2','gu2.id_usuario = su2.id_usuario','inner');
        $this->db->join('gr_puestos pto2','pto2.id_puesto = gu2.id_puesto','inner');
        $this->db->join('grl_personas gp2','gp2.id_persona = su2.id_persona','inner');
        $this->db->where("t.id_top",$id_top);
        $query = $this->db->get();
//        echo $this->db->last_query();
        $res = $query->result_array();
        $num=count($res);
        if ($num == 1)
        {
                return $res[0];
        }
        else
                return 0;
        
    }
    public function dameTopPorIdYUsuario($id_top,$id_usuario)
    {
        $this->db->select('*');
        $this->db->from('ddp_tops t');
        $this->db->join('ddp_periodos p','p.id_periodo = t.id_periodo','inner');
        $this->db->where("t.id_top",$id_top);
        $this->db->where("t.id_usuario",$id_usuario);
        $query = $this->db->get();
//        echo $this->db->last_query();
        $res = $query->result_array();
        $num=count($res);
        if ($num == 1)
        {
            return $res[0];
        }
        else
            return 0;
    }
    public function dameTopPorObjetivo($id_obetivo)
    {
        $this->db->select('t.*');
        $this->db->from('ddp_tops t');
        $this->db->join('ddp_objetivos o','o.id_top = t.id_top','inner');
        $this->db->where("o.id_objetivo",$id_obetivo);
        $query = $this->db->get();
//        echo $this->db->last_query();
        $res = $query->result_array();
        $num=count($res);
        if ($num == 1)
        {
                return $res[0];
        }
        else
                return 0;
        
    }
    public function dameUsuarioTop($id_top)
    {
        $this->db->select("concat(pp.nombre,' ',pp.apellido) as nomape",false);
        $this->db->from('ddp_tops t');
        $this->db->where("t.id_top",$id_top);
        $this->db->join('sys_usuarios u','u.id_usuario = t.id_usuario','inner');
        $this->db->join('grl_personas pp','pp.id_persona = u.id_persona','inner');
        $query = $this->db->get();
//        echo $this->db->last_query();
        $res = $query->result_array();
        $num=count($res);
        if ($num == 1)
        {
                return $res[0];
        }
        else
                return 0;
        
    }
    public function dameTop($id_usuario,$id_periodo)
    {
        $this->db->select('*');
        $this->db->from('ddp_tops t');
        $this->db->join('ddp_periodos p','p.id_periodo = t.id_periodo','inner');
        $this->db->where("t.id_usuario",$id_usuario);
        $this->db->where("t.id_periodo",$id_periodo);
        $this->db->where("t.id_estado",4);
        $query = $this->db->get();
//        echo $this->db->last_query();
        $res = $query->result_array();
        $num=count($res);
        if ($num == 1)
        {
                return $res[0];
        }
        else
                return 0;
        
    }
    public function topActiva($id_usuario)
    {
        $this->db->select('t.id_top');
        $this->db->from('ddp_tops t');
        $this->db->join('ddp_periodos p','p.id_periodo = t.id_periodo','inner');
        $this->db->where("t.id_usuario",$id_usuario);
        $this->db->where("p.activo",1);
        $query = $this->db->get();
//        echo $this->db->last_query();
        $res = $query->row();
        $num=count($res);
        if ($num == 1)
        {
                return 1;
        }
        else
                return 0;
        
    }
    public function dameTopActivaDeUsuario($id_usuario)
    {
        $this->db->select('*');
        $this->db->from('ddp_tops t');
        $this->db->join('ddp_periodos p','p.id_periodo = t.id_periodo','inner');
        $this->db->where("t.id_usuario",$id_usuario);
        $this->db->where("p.activo",1);
        $query = $this->db->get();
//        echo $this->db->last_query();
        $res = $query->result_array();
        $num=count($res);
        if ($num == 1)
        {
                return $res[0];
        }
        else
                return 0;
        
    }
    public function dameTopActivaDatosPanel($id_usuario)
    {
        $this->db->select('t.id_top');
        $this->db->select('t.id_periodo');
        $this->db->select('t.id_estado');
        $this->db->select("case t.id_estado
                when 1 then 0
                when 2 then 1
                when 3 then 1
                when 4 then 1
                else 0
                end as activeTab",false);
        $this->db->select('if(t.id_estado=1,1,0) as btn_cerrar',false);
        $this->db->select('if(t.id_estado=1,1,0) as btn_altaObj',false);
        $this->db->select('if(t.id_estado=2,1,0) as btn_cerrar_1ev',false);
        $this->db->select('if(t.id_estado=3,1,0) as btn_cerrar_2ev',false);
        $this->db->select('if(t.id_estado<=4,1,0) as spa_obj',false);
        $this->db->select('if(t.id_estado>=2,1,0) as spa_1raEv',false);
        $this->db->select('0 as spa_2daEv',false);
//        $this->db->select('if(t.id_estado>=3,1,0) as spa_2daEv',false);
        $this->db->select("if(t.id_estado>2,'\" (cerrada)\"','\"\"') as spa_1raEv_estado",false);
        $this->db->select("concat(gp.nombre, ' ', gp.apellido) as txt_usuario",false);
        $this->db->select("concat(gp1.nombre, ' ', gp1.apellido) as txt_supervisor",false);
        $this->db->select("su1.id_usuario as id_supervisor",false);
        $this->db->from('ddp_tops t');
        $this->db->join('ddp_periodos p','p.id_periodo = t.id_periodo','inner');
        $this->db->join('sys_usuarios su','su.id_usuario = t.id_usuario','inner');
        $this->db->join('grl_personas gp','gp.id_persona = su.id_persona','inner');
        $this->db->join('sys_usuarios su1','su1.id_usuario = t.id_supervisor','inner');
        $this->db->join('grl_personas gp1','gp1.id_persona = su1.id_persona','inner');
        $this->db->where("t.id_usuario",$id_usuario);
        $this->db->where("p.activo",1);
        $query = $this->db->get();
//        echo $this->db->last_query();
        $res = $query->row();
        $num=count($res);
        if ($num == 1)
        {
                return $res;
        }
        else
                return 0;
        
    }
    public function dameTopActivaDatosPanelAdmin($id_top)
    {
        $this->db->select('t.id_top');
        $this->db->select('t.id_periodo');
        $this->db->select('t.id_estado');
        $this->db->select("case t.id_estado
                when 1 then 0
                when 2 then 0
                when 3 then 1
                when 4 then 1
                else 0
                end as activeTab",false);
        $this->db->select('if(t.id_estado=1,1,0) as btn_cerrar',false);
        $this->db->select('if(t.id_estado=1,1,0) as btn_altaObj',false);
        $this->db->select('if(t.id_estado=3,1,0) as btn_cerrar_eval',false);
        $this->db->select('if(t.id_estado<=4,1,0) as spa_obj',false);
        $this->db->select('if(t.id_estado>=3,1,0) as spa_eval',false);
        $this->db->select('0 as spa_2daEv',false);
//        $this->db->select("concat(gp1.nombre, ' ', gp1.apellido) as txt_supervisor",false);
//        $this->db->select('if(t.id_estado>=3,1,0) as spa_2daEv',false);
        $this->db->select("if(t.id_estado>3,'\" (cerrada)\"','\"\"') as spa_eval_estado",false);
        $this->db->from('ddp_tops t');
        $this->db->join('ddp_periodos p','p.id_periodo = t.id_periodo','inner');
//         $this->db->join('sys_usuarios su1','su1.id_usuario = t.id_supervisor','inner');
//        $this->db->join('grl_personas gp1','gp1.id_persona = su1.id_persona','inner');
        $this->db->where("t.id_top",$id_top);
        $this->db->where("p.activo",1);
        $query = $this->db->get();
//        echo $this->db->last_query();
        $res = $query->row();
        $num=count($res);
        if ($num == 1)
        {
                return $res;
        }
        else
                return 0;
        
    }
    public function dameTopActivaDatosPanelParaSupervisor($id_top)
    {
        $this->db->select('t.id_top');
        $this->db->select('t.id_periodo');
        $this->db->select('t.id_estado');
        $this->db->select("case t.id_estado
                when 1 then 0
                when 2 then 0
                when 3 then 1
                when 4 then 1
                else 0
                end as activeTab",false);
        $this->db->select('if(t.id_estado<=2,1,0) as spa_obj',false);
        $this->db->select('if(t.id_estado>=3,1,0) as spa_eval',false);
        $this->db->select("if(t.id_estado>2,'\" (cerrada)\"','\"\"') as spa_eval_estado",false);
        $this->db->from('ddp_tops t');
        $this->db->join('ddp_periodos p','p.id_periodo = t.id_periodo','inner');
        $this->db->where("t.id_top",$id_top);
        $this->db->where("p.activo",1);
        $query = $this->db->get();
//        echo $this->db->last_query();
        $res = $query->row();
        $num=count($res);
        if ($num == 1)
        {
                return $res;
        }
        else
                return 0;
        
    }
    public function dameIdTop($id_usuario,$id_periodo)
    {
        $this->db->select('t.id_top');
        $this->db->from('ddp_tops t');
        $this->db->join('ddp_periodos p','p.id_periodo = t.id_periodo','inner');
        $this->db->where("t.id_usuario",$id_usuario);
        $this->db->where("t.id_periodo",$id_periodo);
        $query = $this->db->get();
//        echo $this->db->last_query();
        $res = $query->result_array();
        $num=count($res);
        if ($num == 1)
        {
                return $res[0]['id_top'];
        }
        else
                return 0;
        
    }
     public function update($id, $datos)
    {
            $this->db->where("id_top",$id);
            $update=$this->db->update("ddp_tops",$datos);
//                echo $update;
//                echo $this->db->last_query();
            if ($update)
                return 1;
            else
                return 0;
    }
    
    public function checkSupervisor($id_supervisor,$id_usuario)
    {
            $this->db->select("id_top"); 
            $this->db->from("ddp_tops");
            $this->db->where('id_usuario',$id_usuario);
            $this->db->where('id_supervisor',$id_supervisor);
            $query=$this->db->get();
            if($query->num_rows()==0)
                return false;
            else
                return true;
    }
    
    public function listado_mis_tops($start, $limit,$busqueda, $campos, $sort, $dir,$id_usuario)
    {
        $this->db->select("concat(gp1.nombre, ' ', gp1.apellido) as supervisor",false);
        $this->db->select("concat(gp2.nombre, ' ', gp2.apellido) as aprobador",false);
        $this->db->select('pto.puesto');
        $this->db->select('a.id_organigrama');
        $this->db->select('gcia.area as gerencia');
        $this->db->select('dt.id_top, dt.id_supervisor, dt.id_aprobador');
        $this->db->select('dt.id_estado');
        $this->db->select(" CASE 
                                WHEN de.estado!='' THEN de.estado
                                ELSE 'Pendiente'
                            END as estado",FALSE);
//        $this->db->select('p.periodo');
//        if ($id_periodo!=-1)
        $this->db->select(" CASE 
                                WHEN p.periodo!='' THEN p.periodo
                                ELSE 'Pendiente'
                            END as periodo",FALSE);
        $this->db->select("DATE_FORMAT(dt.fecha_alta,'%d/%m/%Y') as fecha_alta", FALSE);
        $this->db->select("(select count(distinct(so.id_objetivo)) 
                    from ddp_objetivos so
                    where so.id_top=dt.id_top
                    ) as q_obj",false);
        $this->db->select("(select sum(so.peso) 
                    from ddp_objetivos so 
                    where so.id_top=dt.id_top
                    ) as s_pesos",false);
        $this->db->select("(select count(distinct(so.id_objetivo)) 
                    from ddp_objetivos so
                    where so.id_top=dt.id_top
                    and so.id_estado in(2,3)) as q_obj_sup",false);
        $this->db->select("(select count(distinct(so.id_objetivo)) 
                    from ddp_objetivos so
                    where so.id_top=dt.id_top
                    and so.aceptado=1) as q_obj_aprob",false);
        $this->db->select("(select sum(((so.peso*so.real1)/100)) 
                    from ddp_objetivos so 
                    where so.id_top=dt.id_top
                    ) as s_pesoreal",false);
        
        $this->db->from('ddp_tops dt');
//        $this->db->join('sys_usuarios su','su.id_usuario = dt.id_usuario','inner');
//        $this->db->join('grl_personas gp','gp.id_persona = su.id_persona','inner');
//        $this->db->join('gr_usuarios gu','gu.id_usuario = dt.id_usuario','inner');
        $this->db->join('gr_puestos pto','pto.id_puesto = dt.id_puesto','inner');
//        $this->db->join('gr_organigramas gor','pto.id_organigrama = gor.id_organigrama','inner');
        $this->db->join('gr_areas a','a.id_area = pto.id_area','inner');
        $this->db->join('gr_areas gcia','gcia.id_area = a.id_gcia','inner');
//        $this->db->join('grl_empresas ge','ge.id_empresa = gor.id_empresa','inner');
        $this->db->join('sys_usuarios su1','su1.id_usuario = dt.id_supervisor','inner');
        $this->db->join('grl_personas gp1','gp1.id_persona = su1.id_persona','inner');
        $this->db->join('ddp_estados_top de','de.id_estado = dt.id_estado','left');
        $this->db->join('ddp_periodos p',' p.id_periodo = dt.id_periodo','left');
        
        $this->db->join('sys_usuarios su2','su2.id_usuario = dt.id_aprobador','inner');
        $this->db->join('grl_personas gp2','gp2.id_persona = su2.id_persona','inner');
//        $this->db->join('ddp_estados_top e','e.id_estado = t.id_estado','left');
        
        //$empresa = array(1,2);
        $this->db->where("dt.habilitado",1);
        $this->db->where("dt.id_usuario",$id_usuario);
        //$this->db->where_in("ge.id_empresa",$empresa);
//        $this->db->where("pp.habilitado",1);
//        $this->db->where("gu.habilitado",1);
//        switch ($id_empresa)
//        {
//            case 1:
//                break;
//            case 2:
//                $this->db->where("ge.id_empresa",2);
//                break;
//            case 3:
//                $this->db->where("ge.id_empresa",3);
//                break;
//        }
//        $this->db->where("gu_sup.id_usuario",$id_usuario);
//        if ($periodo!=-1)
//        {
//            if ($periodo['activo']==1)
//            {
//                $this->db->where("(t.id_periodo=".$periodo['id_periodo']." or t.id_periodo='')");
//            }
//            else
//                $this->db->where("t.id_periodo",$periodo['id_periodo']);
//        }
        switch ($sort)
        {
            case 'id_top':
                $this->db->order_by("id_top", $dir); 
                break;
            case 'estado':
                $this->db->order_by("estado", $dir); 
                break;
            case 'usuario':
                $this->db->order_by("concat(gp.nombre,' ',gp.apellido)", $dir); 
                break;
            case 'periodo':
                $this->db->order_by("periodo", $dir); 
                break;
            case 'gerencia':
                $this->db->order_by("gcia.area", $dir); 
                break;
            case 'supervisor':
                $this->db->order_by("concat(gp1.nombre,' ',gp1.apellido)", $dir); 
                break;
        }
         if ($busqueda!="" && count($campos)>0)
            {
                foreach ($campos as &$campo)
                {
                    switch ($campo)
                    {
                        case 'usuario':
                            $campo="concat(gp.nombre,' ',gp.apellido)";
                            break;
                        case 'supervisor':
                            $campo="concat(gp.nombre,' ',gp1.apellido)";
                            break;
                        case 'puesto':
                            $campo="pto.puesto";
                            break;
                        case 'gerencia':
                            $campo="gcia.area";
                            break;
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
                    
            }
        $this->db->limit($limit,$start); 
        $query = $this->db->get();
//        echo $this->db->last_query();
        $res = $query->result_array();
        $num=count($res);
         $num = $this->cantSql('dt.id_top',$this->db->last_query());
        if ($num > 0)
        {
            return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
        }
        else
            return '({"total":"0","rows":""})';
//            
    }
    
    public function dameTopPorPeriodoYUsuario($id_usuario,$id_periodo)
    {
        $this->db->select('*');
        $this->db->from('ddp_tops t');
        $this->db->join('ddp_periodos p','p.id_periodo = t.id_periodo','inner');
        $this->db->where("t.id_periodo",$id_periodo);
        $this->db->where("t.id_usuario",$id_usuario);
        $this->db->where("t.habilitado",1);
        $query = $this->db->get();
//        echo $this->db->last_query();
        $res = $query->result_array();
        $num=count($res);
        if ($num >= 1)
            return 1;
        else
            return 0;
    }
    
    public function listado_excel($start, $limit, $filtros, $busqueda, $campos,$sort="", $dir="",$id_usuario,$periodo,$id_empresa=0)
    {
         
        $this->db->select("dt.id_top as '#'");
        $this->db->select(" CASE 
                                WHEN p.periodo!='' THEN p.periodo
                                ELSE 'Pendiente'
                            END as Periodo",FALSE);
        $this->db->select(" CASE 
                                WHEN de.estado!='' THEN de.estado
                                ELSE 'Pendiente'
                            END as Estado",FALSE);
        $this->db->select("concat(gp.nombre, ' ', gp.apellido) as Usuario",false);
        $this->db->select('pto.puesto as Puesto');
        $this->db->select('gcia.area as Gerencia');
        $this->db->select("concat(gp1.nombre, ' ', gp1.apellido) as Supervisor",false);
        $this->db->select("concat(gp2.nombre, ' ', gp2.apellido) as Aprobador",false);
//        $this->db->select('p.periodo');
//        if ($id_periodo!=-1)
        $this->db->select("(select count(distinct(so.id_objetivo)) 
                    from ddp_objetivos so
                    where so.id_top=dt.id_top
                    ) as 'Sum. obj.'",false);
        $this->db->select("(select sum(so.peso) 
                    from ddp_objetivos so 
                    where so.id_top=dt.id_top
                    ) as 'Sum. peso'",false);
        $this->db->select("(select count(distinct(so.id_objetivo)) 
                    from ddp_objetivos so
                    where so.id_top=dt.id_top
                    and so.id_estado in(2,3)) as q_obj_sup",false);
        $this->db->select("(select sum(((so.peso*so.real1)/100)) 
                    from ddp_objetivos so 
                    where so.id_top=dt.id_top
                    ) as 'Sum. peso real'",false);
        
        $this->db->from('ddp_tops dt');
        $this->db->join('sys_usuarios su','su.id_usuario = dt.id_usuario','inner');
        $this->db->join('grl_personas gp','gp.id_persona = su.id_persona','inner');
        $this->db->join('gr_usuarios gu','gu.id_usuario = dt.id_usuario','inner');
        $this->db->join('gr_puestos pto','pto.id_puesto = gu.id_puesto','inner');
        $this->db->join('gr_organigramas gor','pto.id_organigrama = gor.id_organigrama','inner');
        $this->db->join('gr_areas a','a.id_area = pto.id_area','inner');
        $this->db->join('gr_areas gcia','gcia.id_area = a.id_gcia','inner');
        $this->db->join('grl_empresas ge','ge.id_empresa = gor.id_empresa','inner');
        $this->db->join('sys_usuarios su1','su1.id_usuario = dt.id_supervisor','inner');
        $this->db->join('grl_personas gp1','gp1.id_persona = su1.id_persona','inner');
        $this->db->join('ddp_estados_top de','de.id_estado = dt.id_estado','left');
        $this->db->join('ddp_periodos p',' p.id_periodo = dt.id_periodo','left');
        
        $this->db->join('sys_usuarios su2','su2.id_usuario = dt.id_aprobador','inner');
        $this->db->join('grl_personas gp2','gp2.id_persona = su2.id_persona','inner');
//        $this->db->join('ddp_estados_top e','e.id_estado = t.id_estado','left');
        
        $empresa = array(1,2);
        $this->db->where("dt.habilitado",1);
        $this->db->where_in("ge.id_empresa",$empresa);

        switch ($sort)
        {
            case 'id_top':
                $this->db->order_by("id_top", $dir); 
                break;
            case 'estado':
                $this->db->order_by("estado", $dir); 
                break;
            case 'usuario':
                $this->db->order_by("concat(gp.nombre,' ',gp.apellido)", $dir); 
                break;
            case 'periodo':
                $this->db->order_by("periodo", $dir); 
                break;
            case 'gerencia':
                $this->db->order_by("gcia.area", $dir); 
                break;
            case 'supervisor':
                $this->db->order_by("concat(gp1.nombre,' ',gp1.apellido)", $dir); 
                break;
            case 'aprobador':
                $this->db->order_by("concat(gp2.nombre, ' ', gp2.apellido)", $dir); 
                break;
        }
         if ($busqueda!="" && count($campos)>0)
            {
                foreach ($campos as &$campo)
                {
                    switch ($campo)
                    {
                        case 'usuario':
                            $campo="concat(gp.nombre,' ',gp.apellido)";
                            break;
                        case 'supervisor':
                            $campo="concat(gp.nombre,' ',gp1.apellido)";
                            break;
                        case 'puesto':
                            $campo="pto.puesto";
                            break;
                        case 'gerencia':
                            $campo="gcia.area";
                            break;
                        case 'aprobador':
                            $campo="concat(gp2.nombre, ' ', gp2.apellido)";
                            break;
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
                    
            }
        $this->db->limit($limit,$start); 
        $query = $this->db->get();
//        echo $this->db->last_query();
        $res = $query->result_array();
        $num=count($res);
         $num = $this->cantSql('su.id_usuario',$this->db->last_query());
        if ($num > 0)
        {
            return $res;
        }
        else
            return 0;
//            
    }
    
}
?>