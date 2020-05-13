<?php
class Workflows_model extends CI_Model
{
	public function listado($usuario,$start, $limit, $filtro="", $busqueda, $campos, $sort="", $dir="")
	{
		
            $id_usuario=$usuario['id_usuario'];
            $estados=array(1,2,3,4,5);
            $this->db->select('d.id_documento,d.documento,d.detalle,d.id_usuario_editor as id_editor,d.id_estado,d.codigo,d.tipo_wf');
            $this->db->select('d.id_usuario_aprobador');
            $this->db->select("SUBSTRING(d.archivo,1,3) as archivo",false);
//            $this->db->select("SUBSTRING(d.archivo_fuente,1,3) as archivo_fuente",false);
            $this->db->select(" CASE 
                                WHEN d.tipo_wf=1 THEN 'FT Corto'
                                WHEN d.tipo_wf=2 THEN 'FT Largo'
                                ELSE '?'
                            END as twf",FALSE);
             $this->db->select("DATE_FORMAT(d.fecha_alta,'%d/%m/%Y') as fecha_alta", FALSE);
              $this->db->select("concat(p.nombre, ' ', p.apellido) as editor",false);
              $this->db->select("concat(p2.nombre,' ',p2.apellido) as aprobador",false);
              $this->db->select("e.estado");
              $this->db->select("concat((select count(g.id_gestion) from dms_gestiones g where id_tg=3 and g.id_documento=d.id_documento and g.ciclo_wf=d.ciclos_wf) ,'/',d.q_revisores) as revision",false);
              $this->db->select("(select GROUP_CONCAT(DISTINCT concat(p.nombre,' ',p.apellido,' (', case when ud.activo=0 then 'Si' else 'No' end,')') SEPARATOR '; ') from dms_usuarios_doc ud
                    inner join sys_usuarios u on u.id_usuario=ud.id_usuario
                    inner join grl_personas p on p.id_persona=u.id_persona
                    where id_rol=2 and ud.id_documento=d.id_documento and ud.habilitado=1)as revisores",false);
              $this->db->select("(select GROUP_CONCAT(DISTINCT ud.id_usuario SEPARATOR ';') from dms_usuarios_doc ud
                    where id_rol=2 and ud.id_documento=d.id_documento and ud.habilitado=1 and activo=1)as ids_revisores",false);
            $this->db->select("(select count(so.id_obs) from dms_observaciones so where so.id_documento=d.id_documento) as q_obs",false);
            $this->db->select("(select count(sg.id_gestion) from dms_gestiones sg where sg.id_documento=d.id_documento) as q_ges",false);
            $this->db->from('dms_documentos d');
            $this->db->join('sys_usuarios u','u.id_usuario = d.id_usuario_editor','inner');
            $this->db->join('sys_usuarios u2','u2.id_usuario = d.id_usuario_aprobador','inner');
            $this->db->join('grl_personas p','p.id_persona = u.id_persona','inner');
            $this->db->join('grl_personas p2','p2.id_persona = u2.id_persona','inner');
            $this->db->join('dms_estados_documento e','e.id_estado = d.id_estado','inner');
            $this->db->where('d.en_wf',1);
            $this->db->where('d.habilitado',1);
            $this->db->where_in('d.id_estado',$estados);
            
            if ($filtro!="")
                $this->db->where('d.id_estado',$filtro);
            
            if($usuario['gr']!=1)
            {
                $where="(d.id_usuario_editor=$id_usuario 
                            or d.id_usuario_aprobador=$id_usuario 
                            or d.id_documento in (select ud.id_documento from dms_usuarios_doc ud where id_rol=2 and ud.id_usuario=$id_usuario and ud.habilitado=1 and ud.id_documento=d.id_documento))";
//                $this->db->where('d.id_usuario_editor',$id_usuario);
//                $this->db->or_where('d.id_usuario_aprobador',$id_usuario);
//                $this->db->or_where_in($id_usuario,"select GROUP_CONCAT(DISTINCT ud.id_usuario SEPARATOR ',') from dms_usuarios_doc ud where id_rol=2 and ud.id_documento=d.id_documento and ud.habilitado=1",FALSE);
//                $where="d.id_documento in (select ud.id_documento from dms_usuarios_doc ud where id_rol=2 and ud.id_usuario=$id_usuario and ud.habilitado=1 and ud.id_documento=d.id_documento)";
                $this->db->where($where,"",FALSE);
            }
//            $estados = array();
//            if ($roles['Editor']==1)
//                $this->db->or_where('d.id_usuario_editor',$roles['id_usuario']);
//            if ($roles['Revisor']==1)
//            {
//                $this->db->where("d.id_documento in(select ud.id_documento from dms_usuarios_doc ud
//                                      INNER JOIN sys_usuarios u ON u.id_usuario = ud.id_usuario  
//                                      where ud.id_usuario=$id_usuario
//                                      and activo=1 and ud.habilitado=1 and u.habilitado=1)",NULL,FALSE);
//                $estados[] = 2; //en revision y rechazados
//                $estados[] = 6; //en revision y rechazados
//            }
//            if ($roles['Aprobador']==1)
//            {
//                $estados[] = 2; //en revision
//                $estados[] = 3; //en aprobación
//                $estados[] = 6; //rechazados
//            }
////            if ($roles['Publicador']==1)
//            $estados=  array_unique($estados);
//            $this->db->where_in('d.id_estado',$estados);
            if ($busqueda!="" && count($campos)>0)
            {
                foreach ($campos as &$campo)
                {
                    switch($campo)
                    {
                        case 'id_documento':
                        case 'documento':
                        case 'codigo':
                            $campo = "d.".$campo;
                            break;
                        case 'editor':
                            $campo = "concat(p.nombre, ' ', p.apellido)";
                            break;
                        case 'revisores':
                            $campo = "(select GROUP_CONCAT(DISTINCT concat(p.nombre,' ',p.apellido) SEPARATOR '; ') from dms_usuarios_doc ud
                                    inner join sys_usuarios u on u.id_usuario=ud.id_usuario
                                    inner join grl_personas p on p.id_persona=u.id_persona
                                    where id_rol=2 and ud.id_documento=d.id_documento and ud.habilitado=1)";
                            break;
                        case 'aprobador':
                            $campo = "concat(p2.nombre, ' ',p2.apellido)";
                            break;
                    }

                }
                 unset($campo);
//                echo "<pre>". print_r($campos,true)."<pre>";
                //si viene un solo campo de busqueda
                if(count($campos)==1)
                {
                    if ($campos[0] == "d.id_documento")
                        $this->db->where("$campos[0] =",$busqueda,FALSE);
                    else
                        $this->db->where("$campos[0] like","'%".$busqueda."%'",FALSE);
                }
                else
                {
                    $n=0;
                    foreach ($campos as $campo)
                    {
                        if ($n==0)
                            {
                            $this->db->where("($campo like","'%".$busqueda."%'",FALSE);
                            }
                        else
                        {
                            if ($n==count($campos)-1)
                            {
                                $this->db->or_where("$campo like","'%".$busqueda."%')",FALSE);
                            }
                            else
                            {
                                $this->db->or_where("$campo like","'%".$busqueda."%'",FALSE);
                            }
                        }
                       $n++;     
                    }
                }
            }

            $this->db->order_by("d.id_estado", "asc"); 
            $this->db->limit($limit,$start); 
                
            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $this->cantSql('d.id_documento',$this->db->last_query());
            $res = $query->result_array();
            if ($num > 0)
            {
                return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
            }
            else
                return '({"total":"0","rows":""})';
	}
	public function dameEditores($roles,$start, $limit, $filtro, $campos,$sort="", $dir="")
	{
		
            $id_usuario=$roles['id_usuario'];
            $this->db->select('d.id_documento,d.documento,d.detalle,d.id_usuario_editor as id_editor,d.id_estado');
            $this->db->select(" CASE 
                                WHEN d.tipo_wf=1 THEN 'FT Corto'
                                WHEN d.tipo_wf=2 THEN 'FT Largo'
                                ELSE '?'
                            END as twf",FALSE);
             $this->db->select("DATE_FORMAT(d.fecha_alta,'%d/%m/%Y') as fecha_alta", FALSE);
              $this->db->select("concat(p.nombre,' ',p.apellido) as editor",false);
              $this->db->select("e.estado");
            $this->db->from('dms_documentos d');
            $this->db->join('sys_usuarios u','u.id_usuario = d.id_usuario_editor','inner');
            $this->db->join('grl_personas p','p.id_persona = u.id_persona','inner');
            $this->db->join('dms_estados_documento e','e.id_estado = d.id_estado','inner');
            $this->db->where('d.en_wf',1);
                $this->db->where('d.id_usuario_editor',$roles['id_usuario']);
//            if ($roles['Publicador']==1)
            $estados=  array_unique($estados);
            
            if ($filtro!="" && count($campos)>0)
            {
                foreach ($campos as &$campo)
                {
//                    if ($campo=='empresa')
//                        $campo="e.".$campo;
//                    else
                        $campo="d.".$campo;
                }
//                echo "<pre>". print_r($campos,true)."<pre>";
                //si viene un solo campo de busqueda
                if(count($campos)==1)
                    $this->db->where("$campos[0] like","'%".$filtro."%'",FALSE);
                else
                {
                    $n=0;
                    foreach ($campos as $campo)
                    {
                        if ($n==0)
                            $this->db->where("($campo like","'%".$filtro."%'",FALSE);
                        else
                        {
                            if ($n==count($campos)-1)
                                $this->db->or_where("$campo like","'%".$filtro."%')",FALSE);
                            else
                                $this->db->or_where("$campo like","'%".$filtro."%'",FALSE);
                        }
                       $n++;     
                    }
                }
            }
            
            
            
            
            $this->db->order_by("d.id_estado", "asc"); 
            $this->db->limit($limit,$start); 
                
            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $this->cantSql('d.id_documento',$this->db->last_query());
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
        
//	public function insert($datos)
//	{
//            
//            $this->db->trans_begin();
//		
//		$this->db->insert('dms_wfs',$datos);
//		
//		$this->db->trans_complete();
//		if ($this->db->trans_status() === FALSE)
//		{
//			$this->db->trans_rollback();
//			return false;
//		}
//		else
//		{
//			$this->db->trans_commit();
//			return true;
//		}
//		
//	}
        public function usuariosCombo($limit,$start,$filtro,$rol,$idNot="",$idNotRevisores="")
	{
		
            $this->db->select('p.id_usuario');
            $this->db->select('u.usuario');
            $this->db->select('concat(pp.nombre," ",pp.apellido) as nomape',false);
            $this->db->select('concat(e.abv," - ",pto.puesto) as puesto',false);
            $this->db->from('gr_roles p');
            $this->db->join('sys_usuarios u','u.id_usuario = p.id_usuario','inner');
            $this->db->join('gr_usuarios gru','gru.id_usuario = p.id_usuario','inner');
            $this->db->join('grl_personas pp','pp.id_persona = u.id_persona','inner');
            $this->db->join('gr_puestos pto','pto.id_puesto = gru.id_puesto','inner');
            $this->db->join('gr_organigramas o','o.id_organigrama = pto.id_organigrama','inner');
            $this->db->join('grl_empresas e','e.id_empresa = o.id_empresa','inner');
            
            switch ($rol)
            {
                case 'Editor':
                    $this->db->where("p.editor",1);
                    break;
                case 'Revisor':
                    $this->db->where("p.revisor",1);
                    break;
                case 'Aprobador':
                    $this->db->where("p.aprobador",1);
                    break;
                case 'Publicador':
                    $this->db->where("p.publicador",1);
                    break;
            }
            if($idNot!="")
                $this->db->where("u.id_usuario !=",$idNot);
            if($idNotRevisores!="")
                $this->db->where_not_in("u.id_usuario",$idNotRevisores);
            $this->db->where("pp.habilitado",1);
            $this->db->where("u.habilitado",1);
            $this->db->where("(pp.nombre like","'%".$filtro."%'",FALSE);
            $this->db->or_where("pp.apellido like","'%".$filtro."%'",FALSE);
            $this->db->or_where("concat(pp.nombre,' ',pp.apellido) like","'%".$filtro."%')",FALSE);
            
            
            $this->db->order_by("pp.nombre", "asc"); 
            $this->db->order_by("pp.apellido", "asc"); 
            
            $this->db->limit($limit,$start); 
                
            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $this->cantSql('p.id_usuario',$this->db->last_query());
            $res = $query->result_array();
            if ($num > 0)
            {
                return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
            }
            else
                return '({"total":"0","rows":""})';
	}
        
        public function dameComboEditoresPorGerencia($id_gerencia,$limit=5,$start=0,$filtro="",$idNot="")
	{
            $this->db->select('u.id_usuario,uu.usuario');
//            $this->db->select('pto.puesto');
            $this->db->select('concat(e.abv,"-",gcia.abv,"-",pto.puesto) as puesto',false);
            $this->db->select('concat(pp.nombre," ",pp.apellido) as nomape',false);
            $this->db->select('gcia.abv');
            $this->db->from('gr_usuarios u');
            $this->db->join('gr_roles r','r.id_usuario = u.id_usuario','inner');
            $this->db->join('sys_usuarios uu','uu.id_usuario = u.id_usuario','inner');
            $this->db->join('grl_personas pp','pp.id_persona = uu.id_persona','inner');
            $this->db->join('gr_puestos pto','pto.id_puesto = u.id_puesto','inner');
            $this->db->join('gr_areas a','a.id_area = pto.id_area','inner');
            $this->db->join('gr_areas gcia','gcia.id_area = a.id_gcia','inner');
            $this->db->join('gr_organigramas or1','or1.id_organigrama = pto.id_organigrama','inner');
            $this->db->join('grl_empresas e','e.id_empresa = or1.id_empresa','inner');
            $this->db->where("r.editor",1);
            $this->db->where("a.id_gcia",$id_gerencia);
            
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
	
	
}	
?>