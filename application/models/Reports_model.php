<?php
Class Reports_model extends CI_Model
{
	public function sp_prueba1()
        {
            $sql="SELECT 	t.id_tarea,
			concat(p.nombre,' ',p.apellido) as Creador,
			concat(p2.nombre,' ',p2.apellido) as Responsable, 
			t.hallazgo as Hallazgo, 
			t.tarea as Tarea, 
			t.fecha_alta as FechaAlta, 
			t.fecha_vto as FechaLimite,
			a.id_area as AreaResponsable,
			e.empresa as EmpresaResponsable, 
			th.herramienta as TipoHerramienta, 
			ee.estado as Estado,
			1 as Q
	FROM gr_tareas t
	inner join sys_usuarios u on u.id_usuario= t.usuario_alta
	inner join grl_personas p on p.id_persona=u.id_persona
	inner join sys_usuarios u2 on u2.id_usuario= t.usuario_responsable
	inner join grl_personas p2 on p2.id_persona=u2.id_persona
	inner join gr_usuarios uu on uu.id_usuario=t.usuario_alta
	inner join gr_usuarios uu2 on uu2.id_usuario=t.usuario_responsable
	inner join gr_puestos pto on pto.id_puesto=uu2.id_puesto
	inner join gr_areas a on a.id_area=pto.id_area
	inner join gr_organigramas o on o.id_organigrama=a.id_organigrama
	inner join grl_empresas e on e.id_empresa=o.id_empresa
	inner join gr_tipos_herramientas th on th.id_tipo_herramienta=t.id_tipo_herramienta
	inner join gr_estados ee on ee.id_estado=t.id_estado
	ORDER BY t.id_tarea limit 15;";
            
            $this->load->dbutil();
            $query=$this->db->query($sql);
            $res = $query->result_array();
//            echo "<pre>".print_r($res,true)."</pre>";
            
//            $xml = new SimpleXMLElement('<root/>');
//            array_walk_recursive($res, array ($xml, 'addChild'));
//            return $xml->asXML();


            $config = array (
                            'root'    => 'root',
                            'element' => 'element', 
                            'newline' => "\n", 
                            'tab'    => "\t"
                            );

            echo $this->dbutil->xml_from_result($query, $config);
        }
}
?>