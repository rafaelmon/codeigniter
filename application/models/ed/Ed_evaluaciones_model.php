<?php
class Ed_evaluaciones_model extends CI_Model
{
	public function listado($id_usuario, $start, $limit, $filtro, $sort="",$busqueda,$campos)
	{
		
            $this->db->select('e.id_evaluacion,e.id_usuario,e.id_usuario_supervisor,e.id_periodo,e.id_estado,e.habilitado');
            $this->db->select('e.cierre_u,e.cierre_s');
            $this->db->select('p.periodo,p.semestre,p.anio');
            $this->db->select('es.estado');
            $this->db->select('av.avance');
            $this->db->select('concat(pp.nombre,", ",pp.apellido) as supervisor',false);
            $this->db->select('concat(pe.nombre,", ",pe.apellido) as usuario',false);
//            $this->db->select('if(e.id_usuario='.$id_usuario.',"u","s") as uos',false);
             $this->db->select(" CASE 
                                WHEN e.id_usuario=$id_usuario THEN '1'
                                WHEN e.id_usuario_supervisor=$id_usuario THEN '2'
                                ELSE 'Error'
                            END as tipo",FALSE);
            $this->db->select("DATE_FORMAT(e.fecha_cierre_u,'%d/%m/%Y') as fecha_cierre_u", FALSE);
            $this->db->select("DATE_FORMAT(e.fecha_cierre_s,'%d/%m/%Y') as fecha_cierre_s", FALSE);
            $this->db->from('ed_evaluaciones e');
            $this->db->join('ed_estados es','e.id_estado = es.id_estado');
            $this->db->join('ed_avances av','e.id_avance = av.id_avance');
            $this->db->join('ed_periodos p','e.id_periodo = p.id_periodo');
            $this->db->join('sys_usuarios u','e.id_usuario_supervisor = u.id_usuario');
            $this->db->join('grl_personas pp','u.id_persona = pp.id_persona');
            $this->db->join('sys_usuarios uu','e.id_usuario = uu.id_usuario');
            $this->db->join('grl_personas pe','uu.id_persona = pe.id_persona');
            $this->db->where("e.habilitado",1);
            if($id_usuario != 0)
            {
                $this->db->where("(e.id_usuario",$id_usuario);
                $this->db->or_where("e.id_usuario_supervisor = ".$id_usuario.")");
            }
            if ($filtro!="")
            {
                $this->db->where("e.id_periodo =",$filtro); 
            }
            if ($busqueda!="" && count($campos)>0)
            {
                foreach ($campos as &$campo)
                {
                    switch($campo)
                    {
                        case 'empleado':
                            $campo = "concat(pe.nombre,', ',pe.apellido)";
                            break;
                        case 'id_evaluacion':
                            $campo = "e.".$campo;
                            break;
                        case 'periodo':
                            $campo = "p.".$campo;
                            break;
                    }

                }
                 unset($campo);
                //si viene un solo campo de busqueda
                if(count($campos)==1)
                {
                    if ($campos[0] == "e.id_evaluacion")
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
            $this->db->order_by("field(e.id_usuario,".$id_usuario.")", "desc"); 
            $this->db->order_by("p.periodo", "desc"); 
//            $this->db->order_by("p.periodo", "asc"); 
            $this->db->limit($limit,$start); 

            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $this->cantSql('e.id_evaluacion',$this->db->last_query());
            $res = $query->result_array();
            
            if ($num > 0)
            {
                foreach ($res as &$row)
                {
                    $row['a_c2']=$this->controlCierre_Competencias_cuant($row['id_evaluacion']);
                    $row['a_fyam']=$this->controlCierre_fortalezas($row['id_evaluacion']);
                    $row['a_pm']=$this->controlCierre_pm($row['id_evaluacion']);
                    $row['a_fm']=$this->controlCierre_fm($row['id_evaluacion']);
                    $row['v_peso']=$this->dameValorCumplimiento($row['id_evaluacion']);
                    $row['v_cump']=  round($row['v_peso']/($this->dameCantidadCompetencias($row['id_evaluacion'])*100)*100,0)."%";
                    if($row['id_usuario']==$id_usuario) //autoevaluación
                    {
                        $row['a_c1']=$this->controlCierre_Competencias_cual_usuario($row['id_evaluacion']);
                        if($row['cierre_u']!=1 && $row['cierre_s']==1 && $row['a_c1']==1)
                            $row['btn_cerrar']=1;
                        else
                            $row['btn_cerrar']=0;
                    }
                    else//supervisor evaluando
                    {
                        $row['a_c1']=$this->controlCierre_Competencias_cual($row['id_evaluacion']);
//                        if($row['a_c1']+$row['a_c2']+$row['a_fyam']+$row['a_pm']+$row['a_fm']==5 && $row['cierre_s']!=1)
                        if($row['a_c1']+$row['a_c2']+$row['a_fyam']+$row['a_pm']==4 && $row['cierre_s']!=1)
                            $row['btn_cerrar']=1;
                        else
                            $row['btn_cerrar']=0;
                        
                    }
                }
                unset($row);
                return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
            }
            else
            {
                return '({"total":"0","rows":""})';
            }
//            echo $this->db->last_query();
	}
	public function dameCabeceraEd($id_evaluacion)
	{
            $this->db->select('e.id_usuario, e.usuario,e.cierre_u,e.id_estado');
            $this->db->select('e.id_usuario_supervisor, e.supervisor,e.cierre_s,e.fecha_cierre_s');
            $this->db->select('p.periodo');
            $this->db->from('ed_evaluaciones e');
            $this->db->join('ed_periodos p','p.id_periodo = e.id_periodo');
            $this->db->where("e.id_evaluacion",$id_evaluacion);
            $query = $this->db->get();
            $num = $query->num_rows();
            $res = $query->result_array();
            if ($num > 0)
            {
                return $res[0];
            }
            else
            {
                return false;
            }
	}
	public function dameCabeceraEdMail($id_evaluacion)
	{
            $this->db->select('e.id_usuario, e.usuario,e.cierre_u,e.id_estado,e.comentario_usuario');
            $this->db->select('e.id_usuario_supervisor, e.supervisor,e.cierre_s');
            $this->db->select("DATE_FORMAT(e.fecha_cierre_u,'%d/%m/%Y') as fecha_cierre_u", FALSE);
            $this->db->select("DATE_FORMAT(e.fecha_cierre_s,'%d/%m/%Y') as fecha_cierre_s", FALSE);
            $this->db->select('p.periodo');
            $this->db->select('us.email as mail_s');
            $this->db->select('uu.email as mail_u');
            $this->db->select('pu.genero as genero_u');
            $this->db->select('ps.genero as genero_s');
            $this->db->from('ed_evaluaciones e');
            $this->db->join('ed_periodos p','p.id_periodo = e.id_periodo');
            $this->db->join('sys_usuarios us','e.id_usuario_supervisor = us.id_usuario');
            $this->db->join('sys_usuarios uu','e.id_usuario = uu.id_usuario');
            $this->db->join('grl_personas pu','uu.id_persona = pu.id_persona');
            $this->db->join('grl_personas ps','us.id_persona = ps.id_persona');
            $this->db->where("e.id_evaluacion",$id_evaluacion);
            $query = $this->db->get();
            $num = $query->num_rows();
            $res = $query->result_array();
            if ($num > 0)
            {
                return $res[0];
            }
            else
            {
                return false;
            }
	}
	public function listado_admin($start, $limit, $filtro, $sort="",$dir,$busqueda,$campos)
	{
            $this->db->select('e.id_evaluacion,e.id_usuario,e.id_usuario_supervisor,e.id_periodo,e.id_estado,e.habilitado,e.supervisor');
            $this->db->select('p.periodo,p.semestre,p.anio');
            $this->db->select('es.estado');
            $this->db->select('concat(pp.nombre,", ",pp.apellido) as empleado',false);
            $this->db->select('a.area',false);
            $this->db->select('gcia.area as gerencia',false);
            $this->db->from('ed_evaluaciones e');
            $this->db->join('ed_estados es','es.id_estado = e.id_estado');
            $this->db->join('ed_periodos p','p.id_periodo = e.id_periodo');
            $this->db->join('sys_usuarios u','u.id_usuario = e.id_usuario');
            $this->db->join('grl_personas pp','pp.id_persona = u.id_persona');
            $this->db->join('gr_usuarios gru', 'gru.id_usuario=u.id_usuario', 'inner');
            $this->db->join('gr_puestos pto', 'pto.id_puesto=gru.id_puesto', 'inner');
            $this->db->join('gr_areas a', 'a.id_area=pto.id_area', 'inner');
            $this->db->join('gr_areas gcia', 'gcia.id_area=a.id_gcia', 'left');
            $this->db->join('gr_organigramas o', 'o.id_organigrama=a.id_organigrama', 'inner');
            $this->db->join('grl_empresas em', 'em.id_empresa=o.id_empresa', 'inner');
            $this->db->where("e.habilitado",1);
            
            if ($filtro!="")
            {
                $this->db->where("e.id_periodo =",$filtro); 
            }
            if ($busqueda!="" && count($campos)>0)
            {
                foreach ($campos as &$campo)
                {
                    switch($campo)
                    {
                        case 'id_evaluacion':
                            $campo = "e.id_evaluacion";
                            break;
                        case 'empleado':
                            $campo = "concat(pp.nombre,' ',pp.apellido)";
                            break;
                        case 'supervisor':
                            $campo = "e.supervisor";
                            break;
                        case 'area':
                            $campo = "a.area";
                            break;
                        case 'gerencia':
                            $campo = "gcia.area";
                            break;
                    }

                }
                 unset($campo);
                //si viene un solo campo de busqueda
                if(count($campos)==1)
                {
                    if ($campos[0] == "e.id_evaluacion")
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
                    case 'id_evaluacion':
                        $ordenar="e.id_evaluacion";
                        break;
                    case 'area':
                        $ordenar="a.area";
                        break;
                    case 'usuario':
                        $ordenar="concat(pp.nombre,' ',pp.apellido)";
                        break;
                    case 'supervisor':
                        $ordenar="e.supervisor";
                        break;
                    case 'gerencia':
                        $ordenar="gcia.area";
                        break;
                    case 'estado':
                        $ordenar="es.estado";
                        break;
                    default:
                       $ordenar="e.id_evaluacion";

                }
                $this->db->order_by($ordenar, $dir);
            }
            else {
                $this->db->order_by("e.id_evaluacion", "asc"); 
            }
            
            $this->db->limit($limit,$start); 
            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $this->cantSql('e.id_evaluacion',$this->db->last_query());
            $res = $query->result_array();
            if ($num > 0)
            {
                foreach ($res as &$row)
                {
                    $row['a_c2']=$this->controlCierre_Competencias_cuant($row['id_evaluacion']);
                    $row['a_fyam']=$this->controlCierre_fortalezas($row['id_evaluacion']);
                    $row['a_pm']=$this->controlCierre_pm($row['id_evaluacion']);
                    $row['a_fm']=$this->controlCierre_fm($row['id_evaluacion']);
                    $row['v_peso']=$this->dameValorCumplimiento($row['id_evaluacion']);
                    $row['v_cump']=  round($row['v_peso']/($this->dameCantidadCompetencias($row['id_evaluacion'])*100)*100,0)."%";
                    $row['a_c1_u']=$this->controlCierre_Competencias_cual_usuario($row['id_evaluacion']);
                    $row['a_c1_s']=$this->controlCierre_Competencias_cual($row['id_evaluacion']);
                }
                unset($row);
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
//            echo $sql;die();
            $query =$this->db->query($sql);
            $res = $query->result();
            return $res[0]->cantidad;
        }
        
        function damePeriodosFiltro()
        {
            $this->db->select('id_periodo, periodo');
            $this->db->from('ed_periodos');
            $this->db->where('habilitado',1);
            $query = $this->db->get();
            $num = $query->num_rows();
            $res = $query->result_array();
//            echo $this->db->last_query();die();
            if ($num > 0)
            {
                    return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
            }
            else
                    return '({"total":"0","rows":""})';
        }
        
        function dameCompetencias($start, $limit, $sort,$busqueda,$campos)
        {
            $this->db->select('id_competencia, competencia,habilitado');
            $this->db->from('ed_competencias');
//            $this->db->where('habilitado',1);
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
        
        function update($id, $datos)
        {
            $this->db->where("id_evaluacion",$id);
            if ($this->db->update("ed_evaluaciones",$datos))
                return true;
            else
                return false;
        }
        function cerrarAutoevaluacion($id,$comentario="")
        {
            $this->db->set('id_estado',3);//cerrada
            $this->db->set('cierre_u',1);
            $this->db->set('fecha_cierre_u',date("Y-m-d H:i:s"));
            $this->db->set('comentario_usuario',$comentario);
            $this->db->where("id_evaluacion",$id);
            $this->db->where("cierre_u",0);
            if ($this->db->update("ed_evaluaciones"))
                return true;
            else
                return false;
        }
        function cerrarEvaluacionSupervisada($id)
        {
            $this->db->set('id_estado',2);//cerrada
            $this->db->set('cierre_s',1);
            $this->db->set('fecha_cierre_s',date("Y-m-d H:i:s"));
            $this->db->where("id_evaluacion",$id);
            $this->db->where("cierre_s",0);
            if ($this->db->update("ed_evaluaciones"))
                return true;
            else
                return false;
        }
        
        public function insert($datos)
	{
            if ($this->db->insert("ed_evaluaciones",$datos))
            {
                $last=$this->db->insert_id();
                return $last;
            }
            else
                return false;
	}
        public function verificarNoExistan($id_periodo)
	{
            $this->db->select('id_evaluacion');
            $this->db->from('ed_evaluaciones');
            $this->db->where('habilitado',1);
            $this->db->where('id_estado >',1);
            $this->db->where('id_periodo', $id_periodo);
            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $query->num_rows();
            if ($num==0)
            {
                    return true;
            }
            else
                    return false;
	}
        public function limpiarGeneradosAnteriores($id_periodo)
	{
            $this->db->where('id_periodo', $id_periodo);
            $this->db->where('id_estado', 1);
            $q=$this->db->delete('ed_evaluaciones'); 
		if ($q)
		{
			return true;
		}
		else
			return false;
	}
        
        public function dameEd($id_ed)
        {
            $this->db->select('ee.id_evaluacion,usuario,supervisor,puesto,p.periodo,p.anio,ee.id_periodo');
            $this->db->select('eec.id_competencia,ec.competencia,eec.id_subcompetencia,esc.subcompetencia');
            $this->db->select('eec.u_r1,eec.u_r2,eec.u_r3,eec.u_r4,eec.s_r1,eec.s_r2,eec.s_r3,eec.s_r4');
            $this->db->from('ed_evaluaciones ee');
            $this->db->join('ed_periodos p','ee.id_periodo = p.id_periodo');
            $this->db->join('ed_ec eec','ee.id_evaluacion = eec.id_evaluacion');
            $this->db->join('ed_competencias ec','eec.id_competencia = ec.id_competencia');
            $this->db->join('ed_subcompetencias esc','eec.id_subcompetencia = esc.id_subcompetencia');
            $this->db->where('ee.id_evaluacion',$id_ed);
            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $query->num_rows();
//            echo '                   num: '.$num;
            $res = $query->result_array();
            if ($num >= 1)
            {
                
                    return $res;
            }
            else
                    return 0;
              
        }
        public function dameEdParaPdf($id_ed)
        {
            $this->db->select('ee.id_evaluacion,ee.id_periodo,ee.id_estado');
            $this->db->select('ee.id_usuario,ee.usuario,ee.puesto,ee.puesto,ee.area,ee.empresa');
            $this->db->select('ee.id_usuario_supervisor,ee.supervisor,ee.puesto_supervisor,ee.area_supervisor,ee.empresa_supervisor,ee.comentario_usuario');
            $this->db->select('ee.cierre_u,ee.cierre_s');
            $this->db->select("if((ee.fecha_cierre_u is NULL || ee.fecha_cierre_u = '0000-00-00 00:00:00'),'',DATE_FORMAT(ee.fecha_cierre_u,'%d/%m/%Y %H:%i:%s')) as fecha_cierre_u",false);
            $this->db->select("if((ee.fecha_cierre_s is NULL || ee.fecha_cierre_s = '0000-00-00 00:00:00'),'',DATE_FORMAT(ee.fecha_cierre_s,'%d/%m/%Y %H:%i:%s')) as fecha_cierre_s",false);
            $this->db->select('p.periodo,p.anio');
            $this->db->from('ed_evaluaciones ee');
            $this->db->join('ed_periodos p','ee.id_periodo = p.id_periodo');
            $this->db->where('ee.id_evaluacion',$id_ed);
            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $query->num_rows();
//            echo '                   num: '.$num;
            $res = $query->result_array();
            if ($num >= 1)
            {
                
                    return $res[0];
            }
            else
                    return 0;
              
        }
        
        public function dameIdUsuarioSupervisor($id_ed)
        {
            $this->db->select("id_usuario_supervisor");
            $this->db->from("ed_evaluaciones");
            $this->db->where("id_evaluacion",$id_ed);
            $query = $this->db->get();
//            echo $this->db->last_query();
            $res = $query->result_array();
            return $res[0];
        }
        
        public function listadoExcel($start, $filtro="", $sort="",$dir="",$busqueda="",$campos="")
	{
            $this->db->select('e.id_evaluacion');
            $this->db->select('p.periodo');
            $this->db->select('pp.nombre,pp.apellido');
            $this->db->select('a.area',false);
            $this->db->select('gcia.area as gerencia',false);
            $this->db->select('concat(pp_sup.nombre,", ",pp_sup.apellido) as supervisor',false);
            $this->db->select('es.estado');
            $this->db->from('ed_evaluaciones e');
            $this->db->join('ed_estados es','es.id_estado = e.id_estado');
            $this->db->join('ed_periodos p','p.id_periodo = e.id_periodo');
            $this->db->join('sys_usuarios u','u.id_usuario = e.id_usuario');
            $this->db->join('sys_usuarios u_sup','u_sup.id_usuario=e.id_usuario_supervisor');
            $this->db->join('grl_personas pp','pp.id_persona = u.id_persona');
            $this->db->join('grl_personas pp_sup','pp_sup.id_persona = u_sup.id_persona');
            $this->db->join('gr_usuarios gru', 'gru.id_usuario=u.id_usuario', 'inner');
            $this->db->join('gr_puestos pto', 'pto.id_puesto=gru.id_puesto', 'inner');
            $this->db->join('gr_areas a', 'a.id_area=pto.id_area', 'inner');
            $this->db->join('gr_areas gcia', 'gcia.id_area=a.id_gcia', 'left');
            $this->db->join('gr_organigramas o', 'o.id_organigrama=a.id_organigrama', 'inner');
            $this->db->join('grl_empresas em', 'em.id_empresa=o.id_empresa', 'inner');
            $this->db->where("e.habilitado",1);
            
            if ($filtro!="")
            {
                $this->db->where("e.id_periodo =",$filtro); 
            }
            if ($busqueda!="" && count($campos)>0)
            {
                foreach ($campos as &$campo)
                {
                    switch($campo)
                    {
                        case 'id_evaluacion':
                            $campo = "e.id_evaluacion";
                            break;
                        case 'empleado':
                            $campo = "concat(pp.nombre,' ',pp.apellido)";
                            break;
                        case 'supervisor':
                            $campo = "concat(pp_sup.nombre,' ',pp_sup.apellido)";
                            break;
                        case 'area':
                            $campo = "a.area";
                            break;
                        case 'gerencia':
                            $campo = "gcia.area";
                            break;
                    }

                }
                 unset($campo);
                //si viene un solo campo de busqueda
                if(count($campos)==1)
                {
                    if ($campos[0] == "e.id_evaluacion")
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
                    case 'id_evaluacion':
                        $ordenar="e.id_evaluacion";
                        break;
                    case 'area':
                        $ordenar="a.area";
                        break;
                    case 'usuario':
                        $ordenar="concat(pp.nombre,' ',pp.apellido)";
                        break;
                    case 'supervisor':
                        $ordenar="concat(pp_sup.nombre,' ',pp_sup.apellido)";
                        break;
                    case 'gerencia':
                        $ordenar="gcia.area";
                        break;
                    case 'estado':
                        $ordenar="es.estado";
                        break;
                    default:
                       $ordenar="e.id_evaluacion";

                }
                $this->db->order_by($ordenar, $dir);
            }
            else {
                $this->db->order_by("e.id_evaluacion", "asc"); 
            }
            
//            $this->db->limit($limit,$start); 
            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $this->cantSql('e.id_evaluacion',$this->db->last_query());
            $res = $query->result_array();
            if ($num > 0)
            {
                foreach ($res as &$row)
                {
                    $row['a_c1_u']=$this->controlCierre_Competencias_cual_usuario($row['id_evaluacion']);
                    $row['a_c1_s']=$this->controlCierre_Competencias_cual($row['id_evaluacion']);
                    $row['a_c2']=$this->controlCierre_Competencias_cuant($row['id_evaluacion']);
                    $row['a_fyam']=$this->controlCierre_fortalezas($row['id_evaluacion']);
                    $row['a_pm']=$this->controlCierre_pm($row['id_evaluacion']);
                    $row['a_fm']=$this->controlCierre_fm($row['id_evaluacion']);
                    $row['v_peso']=$this->dameValorCumplimiento($row['id_evaluacion']);
//                    $row['v_cump']=  round($row['v_peso']/($this->dameCantidadCompetencias($row['id_evaluacion'])*100)*100,0)."%";
                    $row['v_cump']=  $row['v_peso']/($this->dameCantidadCompetencias($row['id_evaluacion'])*100);
                }
                unset($row);
//                echo $this->db->last_query();
                return $res;
            }
            else
            {
                return 0;
            }
	}
        function controlCierre_Competencias_cual_usuario($id_ed)
        {
            $this->db->select('count(ec.id_subcompetencia) as cant',false);
            $this->db->from('ed_ec ec');
             $this->db->join('ed_competencias c','c.id_competencia = ec.id_competencia');
             $this->db->join('ed_subcompetencias sc','sc.id_subcompetencia = ec.id_subcompetencia');
            $this->db->where('c.tipo',1);
            $this->db->where('ec.id_evaluacion',$id_ed);
            $this->db->where('sc.obligatoria',1);
            $this->db->where('(ec.u_r1+ec.u_r2+ec.u_r3+ec.u_r4)',0);
            $query = $this->db->get();
            $res = $query->row();
            if ($res->cant > 0)
                return 0;
            else
                return 1;
        }
        function dameIdEdPorEc($id_ec)
        {
            $this->db->select('ec.id_evaluacion',false);
            $this->db->from('ed_ec ec');
            $this->db->where('ec.id_ec',$id_ec);
            $query = $this->db->get();
            $res = $query->row();
            $num = $query->num_rows();
            if ($num > 0)
                return $res->id_evaluacion;
            else
                return 0;
        }
        function controlCierre_Competencias_cual($id_ed)
        {
            $this->db->select('count(ec.id_subcompetencia) as cant',false);
            $this->db->from('ed_ec ec');
            $this->db->join('ed_competencias c','c.id_competencia = ec.id_competencia');
            $this->db->join('ed_subcompetencias sc','sc.id_subcompetencia = ec.id_subcompetencia');
            $this->db->where('c.tipo',1);
            $this->db->where('ec.id_evaluacion',$id_ed);
            $this->db->where('sc.obligatoria',1);
            $this->db->where('(ec.s_r1+ec.s_r2+ec.s_r3+ec.s_r4)',0);
            $query = $this->db->get();
            $res = $query->row();
            if ($res->cant > 0)
                return 0;
            else
                return 1;
        }
        function controlCierre_Competencias_cuant($id_ed)
        {
            $this->db->select('count(ec.id_subcompetencia) as cant',false);
            $this->db->from('ed_ec ec');
            $this->db->join('ed_competencias c','c.id_competencia = ec.id_competencia');
            $this->db->join('ed_subcompetencias sc','sc.id_subcompetencia = ec.id_subcompetencia');
            $this->db->where('c.tipo',2);
            $this->db->where('ec.id_evaluacion',$id_ed);
            $this->db->where('sc.obligatoria',1);
            $this->db->where('ec.s_rc',null);
            $query = $this->db->get();
//            echo $this->db->last_query();
            $res = $query->row();
            if ($res->cant > 0)
                return 0;
            else
                return 1;
        }
        function controlCierre_fortalezas($id_ed)
        {
            $this->db->select('count(f.id_fortaleza) as cant',false);
            $this->db->from('ed_fortalezas f');
            $this->db->where('f.id_evaluacion',$id_ed);
            $query = $this->db->get();
            $res = $query->row();
            if ($res->cant > 0)
                return 1;
            else
                return 0;
        }
        function controlCierre_am($id_ed)
        {
            $this->db->select('count(am.id_aam) as cant',false);
            $this->db->from('ed_aam am');
            $this->db->where('am.id_evaluacion',$id_ed);
            $query = $this->db->get();
            $res = $query->row();
            if ($res->cant > 0)
                return 1;
            else
                return 0;
        }
        function controlCierre_pm($id_ed)
        {
            $this->db->select('count(ec.id_ec) as cant_sc',false);
            $this->db->select('count(pm.id_ec) as cant_pm',false);
            $this->db->select('count(pm.accion) as cant_txt',false);
            $this->db->select('count(pm.fecha_plazo) as cant_fecha',false);
            $this->db->from('ed_ec ec');
             $this->db->join('ed_competencias c','c.id_competencia = ec.id_competencia');
             $this->db->join('ed_subcompetencias sc','sc.id_subcompetencia = ec.id_subcompetencia');
             $this->db->join('ed_pm pm','pm.id_ec = ec.id_ec','left');
            $this->db->where('c.tipo',1);
            $this->db->where('ec.id_evaluacion',$id_ed);
            $this->db->where('(ec.s_r1+ec.s_r2)',1);
            $query = $this->db->get();
//            echo $this->db->last_query();
            $res = $query->row();
            if (($res->cant_sc-$res->cant_pm) == 0 && $res->cant_pm==$res->cant_txt&&$res->cant_pm==$res->cant_fecha)
                return 1;
            else
                return 0;
        }
        function controlCierre_fm($id_ed)
        {
            $this->db->select('count(m.id_meta) as cant',false);
            $this->db->from('ed_metas m');
            $this->db->where('m.id_evaluacion',$id_ed);
            $query = $this->db->get(); 
            $res = $query->row();
            if ($res->cant > 0)
                return 1;
            else
                return 0;
        }
        public function dameValorCumplimiento($id_evaluacion)
	{
            $this->db->select("SUM( CASE 
                                WHEN ec.s_r1=1 THEN 25
                                WHEN ec.s_r2=1 THEN 50
                                WHEN ec.s_r3=1 THEN 100
                                WHEN ec.s_r4=1 THEN 120
                                ELSE 0
                            END) t_cump",FALSE);
            $this->db->from('ed_ec ec');
             $this->db->join('ed_competencias c','ec.id_competencia = c.id_competencia');
            $this->db->where("ec.habilitado",1);
            $this->db->where("ec.id_evaluacion",$id_evaluacion);
            $this->db->where("c.tipo",1);
            $query = $this->db->get();
//            echo $this->db->last_query();
            $res = $query->row();
            return $res->t_cump;
	}
        public function dameCantidadCompetencias($id_evaluacion)
	{
            $this->db->select("COUNT(id_ec) q_comp",FALSE);
            $this->db->from('ed_ec ec');
            $this->db->join('ed_competencias c','ec.id_competencia = c.id_competencia');
            $this->db->where("ec.habilitado",1);
            $this->db->where("ec.id_evaluacion",$id_evaluacion);
            $this->db->where("c.tipo",1);
            $query = $this->db->get();
//            echo $this->db->last_query();
            $res = $query->row();
            return $res->q_comp;
	}
        
        public function dameUsuarioSupervisor($id_ed)
        {
            $this->db->select("supervisor");
            $this->db->from("ed_evaluaciones");
            $this->db->where("id_evaluacion",$id_ed);
            $query = $this->db->get();
//            echo $this->db->last_query();
            $res = $query->result_array();
            return $res[0];
        }
        
        public function dameUsuarioEmpleado($id_ed)
        {
            $this->db->select("e.usuario");
            $this->db->select("u.email");
            $this->db->select("p.genero");
            $this->db->from("ed_evaluaciones e");
            $this->db->join("sys_usuarios u","u.id_usuario = e.id_usuario");
            $this->db->join("grl_personas p","p.id_persona = u.id_persona");
            $this->db->where("id_evaluacion",$id_ed);
            $query = $this->db->get();
//            echo $this->db->last_query();
            $res = $query->result_array();
            return $res;
        }
        
}	
?>