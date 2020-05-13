<?php
class Documentos_model extends CI_Model
{
	public function publicados($start, $limit, $filtros, $busqueda,$campos,$sort="", $dir="",$usuario)
	{
		
            $this->db->select('d.id_documento,d.documento,d.detalle,d.version,d.codigo,d.alcance,d.id_gerencia_origen');
            $this->db->select('d.id_usuario_editor as id_editor');
            $this->db->select("DATE_FORMAT(d.fecha_public,'%d/%m/%Y') as f_publicacion", FALSE);
            $this->db->select('td.td');
            $this->db->select("concat(e.abv,'-',a.area,' (',a.abv,')') as gerencia",false);
            $this->db->select(" CASE 
                                WHEN d.alcance=0 THEN 'Todos'
                                WHEN d.alcance=1 THEN 'Sales de Jujuy'
                                WHEN d.alcance=2 THEN 'Borax Argentina'
                                ELSE '?'
                            END as alce",FALSE);
            $this->db->select("concat(p.nombre,' ',p.apellido,' - ',pto.puesto,' (',ee.abv,')') as editor",false);
            $this->db->from('dms_documentos d');
            $this->db->join('dms_tipos_documento td','td.id_td = d.id_td','inner');
            $this->db->join("gr_areas a","a.id_area=d.id_gerencia_origen",'inner');
            $this->db->join('gr_organigramas org','org.id_organigrama = a.id_organigrama','inner');
            $this->db->join('grl_empresas e','e.id_empresa = org.id_empresa','inner');
            
            $this->db->join('gr_usuarios gu','gu.id_usuario = d.id_usuario_editor','inner');
            $this->db->join('sys_usuarios u','u.id_usuario = gu.id_usuario','inner');
            $this->db->join('grl_personas p','p.id_persona = u.id_persona','left');
            $this->db->join('gr_puestos pto','pto.id_puesto = gu.id_puesto','inner');
            $this->db->join('gr_organigramas org2','org2.id_organigrama = pto.id_organigrama','inner');
            $this->db->join('grl_empresas ee','ee.id_empresa = org2.id_empresa','inner');
            
            $this->db->where('d.id_estado','6');//solo los que estan en estado publicado
            $this->db->where('d.habilitado',1);
            
            if($usuario['gr']!=1)
            {
                switch ($usuario['id_empresa']){
                    case 1:
                        $in=array(0,1,2);
                        break;
                    case 2:
                        $in=array(0,1);
                        break;
                    case 3:
                        $in=array(0,2);
                        break;
                }
                $this->db->where_in('d.alcance',$in);
            }
            if (!empty($filtros))
            {
                if ($filtros['id_td']!="")
                    $this->db->where('d.id_td',$filtros['id_td']);
                if ($filtros['id_gcia']!="")
                    $this->db->where('d.id_gerencia_origen',$filtros['id_gcia']);
            }
            
            if ($busqueda!="" && count($campos)>0)
            {
                foreach ($campos as &$campo)
                {
                    if ($campo=='td')
                        $campo="td.".$campo;
                    else
                        $campo="d.".$campo;
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
            if ($sort!="")
            {
                switch ($sort){
                    case 'id_documento':
                        $orden="d.id_documento";
                        break;
                    case 'documento':
                        $orden="d.documento";
                        break;
                    case 'td':
                        $orden="td.td";
                        break;
                    case 'codigo':
                        $orden="d.codigo";
                        break;
                    case 'detalle':
                        $orden="d.detalle";
                        break;
                    case 'f_publicacion':
                        $orden="d.fecha_public";
                        break;
                    default:
                        $orden="d.fecha_public";
                        break;
                }
                
                $this->db->order_by($orden, $dir);
            }
            else
                $this->db->order_by("d.fecha_public", "desc"); 
            $this->db->limit($limit,$start); 
                
            $query = $this->db->get();
            $num = $this->cantSql('d.id_documento',$this->db->last_query());
            $res = $query->result_array();
            if ($num > 0)
            {
                return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
            }
            else
                return '({"total":"0","rows":""})';
	}
	public function obsoletos($start, $limit, $filtro, $busqueda,$campos,$sort="", $dir="asc")
	{
		
            $this->db->select('d.id_documento,d.documento,d.detalle,d.version,d.archivo,d.codigo');
            $this->db->select("DATE_FORMAT(d.fecha_public,'%d/%m/%Y') as f_publicacion", FALSE);
            $this->db->select("DATE_FORMAT(d.fecha_obsoleto,'%d/%m/%Y') as f_obsoleto", FALSE);
            $this->db->select('td.td');
            $this->db->select("concat(p.nombre,' ',p.apellido,' - ',pto.puesto,' (',ee.abv,')') as usuario",false);
            $this->db->from('dms_documentos d');
            $this->db->join('dms_tipos_documento td','td.id_td = d.id_td','inner');
            $this->db->join('gr_usuarios gu','gu.id_usuario = d.id_usuario_obsoleto','inner');
            $this->db->join('sys_usuarios u','u.id_usuario = gu.id_usuario','inner');
            $this->db->join('grl_personas p','p.id_persona = u.id_persona','left');
            $this->db->join('gr_puestos pto','pto.id_puesto = gu.id_puesto','inner');
            $this->db->join('gr_organigramas org','org.id_organigrama = pto.id_organigrama','inner');
            $this->db->join('grl_empresas ee','ee.id_empresa = org.id_empresa','inner');
            $this->db->where('d.id_estado','7');//solo los que estan en estado obsoleto (7)
            
            if ($filtro!="")
                $this->db->where('d.id_td',$filtro);
            
            if ($busqueda!="" && count($campos)>0)
            {
                foreach ($campos as &$campo)
                {
                    if ($campo=='td')
                        $campo="td.".$campo;
                    else
                        $campo="d.".$campo;
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
            if ($sort!="")
            {
                switch ($sort)
                {
                    case 'f_obsoleto':
                        $sort='d.fecha_obsoleto';
                        break;
                    case 'f_publicacion':
                        $sort='d.fecha_public';
                        break;
                    case 'td':
                        $sort='td.td';
                        break;
                    case 'codigo':
                        $sort='d.codigo';
                        break;
                    case 'usuario':
                        $sort="concat(p.nombre,' ',p.apellido,' - ',pto.puesto,' (',ee.abv,')')";
                        break;
                    default:
                        $sort='d.id_documento';
                        break;
                }
                
                $this->db->order_by($sort, $dir);
            }
            else
                $this->db->order_by("d.fecha_obsoleto", "desc"); 
            $this->db->limit($limit,$start); 
                
            $query = $this->db->get();
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
        
//	public function edit($id,$datos)
//	{
//            $this->db->where('id_documento', $id);
//            if(!$this->db->update('dms_documentos', $datos))
//                    return false;
//            else
//                    return true;
//		
//	}
	
	public function insert($datos)
	{
            
            $this->db->trans_begin();
		
		$this->db->insert('dms_documentos',$datos);
                $insert_id=$this->db->insert_id();
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
        public function update($id, $datos)
	{
		$this->db->where("id_documento",$id);
                $update=$this->db->update("dms_documentos",$datos);
		if ($update)
			return true;
		else
			return false;
	}
        
         public function dameComboTDPublicados($usuario)
        {
            $this->db->select("distinct(e.id_td)",false);
//            $this->db->select("td.td");
            $this->db->select("concat(td.td,' (',td.abv,')') as td",false);
            $this->db->from("dms_documentos e");
            $this->db->join("dms_tipos_documento td","td.id_td=e.id_td",'inner');
            $this->db->where("e.habilitado",1);
            $this->db->where("e.id_estado",6); //estado 6 == Publicado
            if($usuario['gr']!=1)
            {
                switch ($usuario['id_empresa']){
                    case 1:
                        $in=array(0,1,2);
                        break;
                    case 2:
                        $in=array(0,1);
                        break;
                    case 3:
                        $in=array(0,2);
                        break;
                }
                $this->db->where_in('e.alcance',$in);
            }
            $this->db->order_by("td.td", "asc");
            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $query->num_rows();
            $res = $query->result_array();
            if ($num > 0)
            {
                    return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
            }
            else
                    return '({"total":"0","rows":""})';
        }

        public function dameComboGerenciasPublicados()
        {
            $this->db->select("distinct(d.id_gerencia_origen) as id_gerencia",false);
//            $this->db->select("a.area as gerencia");
            $this->db->select("concat(e.abv,'-',a.area,' (',a.abv,')') as gerencia",false);
            $this->db->from("dms_documentos d");
            $this->db->join("gr_areas a","a.id_area=d.id_gerencia_origen",'inner');
            $this->db->join('gr_organigramas org','org.id_organigrama = a.id_organigrama','inner');
            $this->db->join('grl_empresas e','e.id_empresa = org.id_empresa','inner');
            $this->db->where("d.habilitado",1);
            $this->db->where("d.id_estado",6); //estado 6 == Publicado
            //-->quito para que todos puedan ver todos los documentos
            /*
            if($usuario['gr']!=1)
            {
                switch ($usuario['id_empresa']){
                    case 1:
                        $in=array(0,1,2);
                        break;
                    case 2:
                        $in=array(0,1);
                        break;
                    case 3:
                        $in=array(0,2);
                        break;
                }
                $this->db->where_in('d.alcance',$in);
            }
             */
             $this->db->order_by("field(e.abv, 'ORO','SDJ','BXA')", "asc"); 
            $this->db->order_by("a.area", "asc");
            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $query->num_rows();
            $res = $query->result_array();
            if ($num > 0)
            {
                    return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
            }
            else
                    return '({"total":"0","rows":""})';
        }
        public function dameComboTDObsoletos()
        {
            $this->db->select("distinct(e.id_td)",false);
            $this->db->select("td.td");
            $this->db->from("dms_documentos e");
            $this->db->where("e.id_estado",7);
            $this->db->join("dms_tipos_documento td","td.id_td=e.id_td",'inner');
            $this->db->order_by("td.td", "asc");
            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $query->num_rows();
            $res = $query->result_array();
            if ($num > 0)
            {
                    return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
            }
            else
                    return '({"total":"0","rows":""})';
        }
        public function dameDocumento($id_documento)
        {
            $this->db->select("d.id_documento, d.id_td, d.id_estado, d.id_usuario_editor, d.id_usuario_aprobador, d.id_empresa_origen, d.id_gerencia_origen");
            $this->db->select("d.alcance, d.tipo_wf, d.documento, d.codigo, d.detalle, d.archivo, d.archivo_nom_orig, d.archivo_fuente, d.archivo_fuente_nom_orig");
            $this->db->select("d.numero, d.version, d.padre_id, d.fecha_alta, d.id_usuario_public, d.obsoleto, d.fecha_obsoleto, d.id_usuario_obsoleto, d.q_revisores, d.en_wf, d.ciclos_wf, d.habilitado,d.en_nv");
            $this->db->select("DATE_FORMAT(d.fecha_public,'%d/%m/%Y') as fecha_public", FALSE);
            $this->db->from("dms_documentos d");
            $this->db->where("d.habilitado",1);
            $this->db->where("d.id_documento",$id_documento);
            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $query->num_rows();
            $res = $query->result_array();
            if ($num == 1)
            {
                    return $res[0];
            }
            else
                    return 0;
        }
        public function dameDocumentoPublicado($id_documento)
        {
            $this->db->select("d.id_documento, d.id_td, id_estado, d.id_usuario_editor, d.id_usuario_aprobador, d.id_empresa_origen, d.id_gerencia_origen,d.id_estado");
            $this->db->select("d.alcance, d.tipo_wf, d.documento, d.codigo, d.detalle, d.archivo, d.archivo_nom_orig, d.archivo_fuente, d.archivo_fuente_nom_orig");
            $this->db->select("d.numero, d.version, d.padre_id, d.fecha_alta, d.id_usuario_public, d.obsoleto, d.fecha_obsoleto, d.id_usuario_obsoleto, d.q_revisores, d.en_wf, d.ciclos_wf, d.habilitado,d.en_nv");
            $this->db->select("DATE_FORMAT(d.fecha_public,'%d/%m/%Y') as fecha_public", FALSE);
            $this->db->from("dms_documentos d");
            $this->db->where("d.habilitado",1);
            $this->db->where("d.id_estado",6);
            $this->db->where("d.id_documento",$id_documento);
            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $query->num_rows();
            $res = $query->result_array();
            if ($num == 1)
            {
                    return $res[0];
            }
            else
                    return 0;
        }
        public function dameDocumentoAPublicar($id_documento)
        {
            $this->db->select("d.id_documento, d.id_td, id_estado, d.id_usuario_editor, d.id_usuario_aprobador, d.id_empresa_origen, d.id_gerencia_origen,d.id_estado");
            $this->db->select("d.alcance, d.tipo_wf, d.documento, d.codigo, d.detalle, d.archivo, d.archivo_nom_orig, d.archivo_fuente, d.archivo_fuente_nom_orig");
            $this->db->select("d.numero, d.version, d.padre_id, d.fecha_alta, d.id_usuario_public, d.obsoleto, d.fecha_obsoleto, d.id_usuario_obsoleto, d.q_revisores, d.en_wf, d.ciclos_wf, d.habilitado,d.en_nv");
            $this->db->select("DATE_FORMAT(d.fecha_public,'%d/%m/%Y') as fecha_public", FALSE);
            $this->db->from("dms_documentos d");
            $this->db->where("d.habilitado",1);
            $this->db->where("d.id_estado",5);
            $this->db->where("d.id_documento",$id_documento);
            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $query->num_rows();
            $res = $query->result_array();
            if ($num == 1)
            {
                    return $res[0];
            }
            else
                    return 0;
        }
        public function dameDocumentoObsoleto($id_documento)
        {
            $this->db->select("d.id_documento, d.id_td, id_estado, d.id_usuario_editor, d.id_usuario_aprobador, d.id_empresa_origen, d.id_gerencia_origen,d.id_estado");
            $this->db->select("d.alcance, d.tipo_wf, d.documento, d.codigo, d.detalle, d.archivo, d.archivo_nom_orig, d.archivo_fuente, d.archivo_fuente_nom_orig");
            $this->db->select("d.numero, d.version, d.padre_id, d.fecha_alta, d.id_usuario_public, d.obsoleto, d.fecha_obsoleto, d.id_usuario_obsoleto, d.q_revisores, d.en_wf, d.ciclos_wf, d.habilitado,d.en_nv");
            $this->db->select("DATE_FORMAT(d.fecha_public,'%d/%m/%Y') as fecha_public", FALSE);
            $this->db->select("DATE_FORMAT(d.fecha_obsoleto,'%d/%m/%Y') as fecha_obsoleto", FALSE);
            $this->db->from("dms_documentos d");
            $this->db->where("d.id_estado",7);
            $this->db->where("d.id_documento",$id_documento);
            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $query->num_rows();
            $res = $query->result_array();
            if ($num == 1)
            {
                    return $res[0];
            }
            else
                    return 0;
        }
        public function dameDocumentoPublicadoUObsoleto($id_documento)
        {
            $this->db->select("d.id_documento, d.id_td, id_estado, d.id_usuario_editor, d.id_usuario_aprobador, d.id_empresa_origen, d.id_gerencia_origen,d.id_estado");
            $this->db->select("d.alcance, d.tipo_wf, d.documento, d.codigo, d.detalle, d.archivo, d.archivo_nom_orig, d.archivo_fuente, d.archivo_fuente_nom_orig");
            $this->db->select("d.numero, d.version, d.padre_id, d.fecha_alta, d.id_usuario_public, d.obsoleto, d.fecha_obsoleto, d.id_usuario_obsoleto, d.q_revisores, d.en_wf, d.ciclos_wf, d.habilitado,d.en_nv,d.transferido");
            $this->db->select("DATE_FORMAT(d.fecha_public,'%d/%m/%Y') as fecha_public", FALSE);
            $this->db->select("DATE_FORMAT(d.fecha_obsoleto,'%d/%m/%Y') as fecha_obsoleto", FALSE);
            $this->db->from("dms_documentos d");
            $estados=array(6,7);
            $this->db->where_in("d.id_estado",$estados);
            $this->db->where("d.id_documento",$id_documento);
            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $query->num_rows();
            $res = $query->result_array();
            if ($num == 1)
            {
                    return $res[0];
            }
            else
                    return 0;
        }
        public function dameObsoletos()
        {
            $this->db->select("d.id_documento", FALSE);
            $this->db->from("dms_documentos d");
            $this->db->where("d.id_estado",7);
            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $query->num_rows();
            $res = $query->result_array();
            if ($num > 0)
            {
                    return $res;
            }
            else
                    return 0;
        }
        public function dameDocumentoParaEditar($id_documento)
        {
            $this->db->select("d.detalle");
            $this->db->from("dms_documentos d");
            $this->db->where("d.habilitado",1);
            $this->db->where("d.id_documento",$id_documento);
            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $query->num_rows();
            $res = $query->result_array();
            if ($num == 1)
            {
                    return $res[0];
            }
            else
                    return 0;
        }
        public function dameDocumentoParaEditar2($id_documento)
        {
            $this->db->select("d.documento as tituloDocumentoField");
            $this->db->select("d.detalle as detalleDocumentoTexArea");
            $this->db->select("d.id_td as tiposDocumentoCombo");
            $this->db->select("d.id_usuario_aprobador as aprobadoresCombo");
            $this->db->select("d.alcance as alcanceCombo");
            $this->db->select("d.tipo_wf as tipoWFRadios");
            $this->db->select("(select GROUP_CONCAT(DISTINCT ud.id_usuario) from dms_usuarios_doc ud
                    where id_rol=2 and ud.id_documento=d.id_documento and ud.habilitado=1)as revisoresSBS",false);
            $this->db->from("dms_documentos d");
            $this->db->where("d.habilitado",1);
            $this->db->where("d.id_documento",$id_documento);
            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $query->num_rows();
            $res = $query->result_array();
            if ($num == 1)
            {
                     return '({"success": true,"data":'.json_encode($res[0]).'})';
            }
            else
                     return '({"success": false,"errorMessage":"Error!!!..."})';
        }
        
        public function dameUltimoEstado($id_documento)
        {
            $this->db->select("d.id_estado,tipo_wf");
            $this->db->from("dms_documentos d");
            $this->db->where("d.habilitado",1);
            $this->db->where("d.id_documento",$id_documento);
            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $query->num_rows();
            $res = $query->result_array();
            if ($num == 1)
            {
                    return $res[0];
            }
            else
                    return 0;
        }
        public function dameDatosParaMailing($id_documento)
        {
            $this->db->select("d.id_documento,d.id_estado,d.documento as titulo,d.codigo,d.detalle,d.tipo_wf,d.alcance");
            $this->db->select("ed.estado");
            $this->db->select("concat(p.nombre,' ',p.apellido,' - ',pto.puesto,' (',ee.abv,')') as editor",false);
            $this->db->select("concat(p.nombre,' ',p.apellido) as editor_nom",false);
            $this->db->select("concat(p2.nombre,' ',p2.apellido,' - ',pto2.puesto,' (',ee2.abv,')') as aprobador",false);
            $this->db->select("concat(p2.nombre,' ',p2.apellido) as aprobador_nom",false);
            $this->db->select("if(d.tipo_wf = 1,concat(p2.nombre,' ',p2.apellido,' - ',pto2.puesto,' (',ee2.abv,')'), (select GROUP_CONCAT(concat(sp.nombre,' ',sp.apellido,' - ',spto.puesto,' (',se.abv,')') SEPARATOR ';<BR>') from dms_usuarios_doc sud
                    inner join gr_usuarios sgu on sgu.id_usuario = sud.id_usuario 
                    inner join sys_usuarios su on su.id_usuario = sgu.id_usuario 
                    inner join grl_personas sp on  sp.id_persona = su.id_persona 
                    inner join gr_puestos spto on  spto.id_puesto = sgu.id_puesto 
                    inner join gr_organigramas sorg on sorg.id_organigrama = spto.id_organigrama
                    inner join grl_empresas se on se.id_empresa = sorg.id_empresa 
                    where id_rol=2 and sud.id_documento=d.id_documento and sud.habilitado=1))as revisores",false);
            $this->db->select("if(d.tipo_wf = 1,concat(p2.nombre,' ',p2.apellido), (select GROUP_CONCAT(concat(sp.nombre,' ',sp.apellido) SEPARATOR ', ') from dms_usuarios_doc sud
                    inner join gr_usuarios sgu on sgu.id_usuario = sud.id_usuario 
                    inner join sys_usuarios su on su.id_usuario = sgu.id_usuario 
                    inner join grl_personas sp on  sp.id_persona = su.id_persona 
                    where id_rol=2 and sud.id_documento=d.id_documento and sud.habilitado=1))as revisores_nom",false);
            $this->db->from("dms_documentos d");
            $this->db->join("dms_estados_documento ed","ed.id_estado=d.id_estado",'inner');
            $this->db->join('gr_usuarios gu','gu.id_usuario = d.id_usuario_editor','inner');
            $this->db->join('sys_usuarios u','u.id_usuario = gu.id_usuario','inner');
            $this->db->join('grl_personas p','p.id_persona = u.id_persona','left');
            $this->db->join('gr_puestos pto','pto.id_puesto = gu.id_puesto','inner');
            $this->db->join('gr_organigramas org','org.id_organigrama = pto.id_organigrama','inner');
            $this->db->join('grl_empresas ee','ee.id_empresa = org.id_empresa','inner');
            $this->db->join('gr_usuarios gu2','gu2.id_usuario = d.id_usuario_aprobador','inner');
            $this->db->join('sys_usuarios u2','u2.id_usuario = gu2.id_usuario','inner');
            $this->db->join('grl_personas p2','p2.id_persona = u2.id_persona','left');
            $this->db->join('gr_puestos pto2','pto2.id_puesto = gu2.id_puesto','inner');
            $this->db->join('gr_organigramas org2','org2.id_organigrama = pto2.id_organigrama','inner');
            $this->db->join('grl_empresas ee2','ee2.id_empresa = org2.id_empresa','inner');
//            $this->db->join('sys_usuarios u','u.id_usuario = d.id_usuario_editor','inner');
//            $this->db->join('grl_personas p','p.id_persona = u.id_persona','inner');
            $this->db->where("d.habilitado",1);
            $this->db->where("d.id_documento",$id_documento);
            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $query->num_rows();
            $res = $query->result_array();
            if ($num == 1)
            {
                    return $res[0];
            }
            else
                    return 0;
        }
        public function contarPorOrigen($id_gerencia)
	{
		
            $this->db->select('count(id_documento) as cant',false);
            $this->db->from('dms_documentos d');
//            $this->db->where('d.id_empresa_origen',$id_empresa);
            $this->db->where('d.id_gerencia_origen',$id_gerencia);
            $query = $this->db->get();
            $num = $query->num_rows();
            $res = $query->row();
//            echo "<pre>".print_r($res,true)."</pre>";
            if ($num > 0)
            {
                return $res->cant;
            }
            else
                return 0;
	}
        public function maxNroPorOrigen($id_gerencia)
	{
		
            $this->db->select('max(numero) as max',false);
            $this->db->from('dms_documentos d');
//            $this->db->where('d.id_empresa_origen',$id_empresa);
            $this->db->where('d.id_gerencia_origen',$id_gerencia);
            $query = $this->db->get();
            $num = $query->num_rows();
            $res = $query->row();
//            echo "<pre>".print_r($res,true)."</pre>";
            if ($num > 0)
            {
                return $res->max;
            }
            else
                return 0;
	}
	
	
        public function listadoDocumentosFechaAlerta()
        {
            $this->db->select("d.id_documento");
//            $this->db->select("CURDATE()");
//            $this->db->select("DATE_ADD(fecha_public, INTERVAL 1 YEAR) as fecha_alerta",FALSE);
            $this->db->from("dms_documentos d");
            $this->db->where("d.id_estado",6);
            $this->db->where("d.habilitado",1);
            $this->db->where("d.fecha_public IS NOT NULL");
            $this->db->where("DATE_ADD(fecha_public, INTERVAL 1 YEAR) = CURDATE()");//'2016-07-26'
            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $query->num_rows();
            $res = $query->result_array();
            if ($num > 0)
            {
                    return $res;
            }
            else
                    return 0;
        }
        
        public function dameDatosDocumentoParaMail($id_documento)
        {
            $this->db->select("d.codigo,d.id_usuario_editor");
            $this->db->select("(select GROUP_CONCAT(DISTINCT concat(p.nombre,' ',p.apellido) SEPARATOR ', ') from dms_usuarios_doc ud
                    inner join sys_usuarios u on u.id_usuario=ud.id_usuario
                    inner join grl_personas p on p.id_persona=u.id_persona
                    where id_rol=2 and ud.id_documento=d.id_documento and ud.habilitado=1)as revisores",false);
            $this->db->select("(select GROUP_CONCAT(DISTINCT u.email SEPARATOR ', ') from dms_usuarios_doc ud
                    inner join sys_usuarios u on u.id_usuario=ud.id_usuario
                    inner join grl_personas p on p.id_persona=u.id_persona
                    where id_rol=2 and ud.id_documento=d.id_documento and ud.habilitado=1)as email_revisores",false);
            $this->db->select("concat(p1.nombre,' ',p1.apellido) as editor",false);
            $this->db->select("p1.genero as generoEditor",false);
            $this->db->select("concat(p2.nombre,' ',p2.apellido) as aprobador",false);
            $this->db->select("u1.email as email_editor",false);
            $this->db->select("u2.email as email_aprobador",false);
            $this->db->from("dms_documentos d");
            $this->db->join('sys_usuarios u1','u1.id_usuario = d.id_usuario_editor','inner');
            $this->db->join('grl_personas p1','p1.id_persona = u1.id_persona','inner');
            $this->db->join('sys_usuarios u2','u2.id_usuario = d.id_usuario_aprobador','inner');
            $this->db->join('grl_personas p2','p2.id_persona = u2.id_persona','inner');
            $this->db->where("d.id_documento",$id_documento);
            $this->db->where("d.id_estado",6);
            $this->db->where("d.habilitado",1);
            $this->db->where("d.fecha_public IS NOT NULL");
            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $query->num_rows();
            $res = $query->result_array();
            if ($num > 0)
            {
                    return $res;
            }
            else
                    return 0;
        }
        
    public function publicados_excel($start, $limit, $filtros, $busqueda,$campos,$sort="", $dir="",$usuario)
    {

        $this->db->select("d.id_documento as '#',d.documento as Documento");
        $this->db->select("DATE_FORMAT(d.fecha_public,'%d/%m/%Y') as 'Fecha Publicacion'", FALSE);
        $this->db->select("td.td as 'Tipo documento'");
        $this->db->select('d.codigo as Código,d.detalle as Descripción');
        $this->db->from('dms_documentos d');
        $this->db->join('dms_tipos_documento td','td.id_td = d.id_td','inner');
        $this->db->where('d.id_estado','6');//solo los que estan en estado publicado
        $this->db->where('d.habilitado',1);

        if($usuario['gr']!=1)
        {
            switch ($usuario['id_empresa']){
                case 1:
                    $in=array(0,1,2);
                    break;
                case 2:
                    $in=array(0,1);
                    break;
                case 3:
                    $in=array(0,2);
                    break;
            }
            $this->db->where_in('d.alcance',$in);
        }
        if (!empty($filtros))
        {
            if ($filtros['id_td']!="")
                $this->db->where('d.id_td',$filtros['id_td']);
            if ($filtros['id_gcia']!="")
                $this->db->where('d.id_gerencia_origen',$filtros['id_gcia']);
        }

        if ($busqueda!="" && count($campos)>0)
        {
            foreach ($campos as &$campo)
            {
                if ($campo=='td')
                    $campo="td.".$campo;
                else
                    $campo="d.".$campo;
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
        if ($sort!="")
        {
            switch ($sort){
                case 'id_documento':
                    $orden="d.id_documento";
                    break;
                case 'documento':
                    $orden="d.documento";
                    break;
                case 'td':
                    $orden="td.td";
                    break;
                case 'codigo':
                    $orden="d.codigo";
                    break;
                case 'detalle':
                    $orden="d.detalle";
                    break;
                case 'f_publicacion':
                    $orden="d.fecha_public";
                    break;
                default:
                    $orden="d.fecha_public";
                    break;
            }

            $this->db->order_by($orden, $dir);
        }
        else
            $this->db->order_by("d.fecha_public", "desc"); 

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