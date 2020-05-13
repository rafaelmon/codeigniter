<?php
class Gr_historial_model extends CI_Model
{
	
    public function listado($id_tarea, $start, $limit,$sort="", $dir="")
    {
        $this->db->select('h.id_historial, h.usuario_responsable as id_responsable, h.usuario_alta as id_usuario_alta, h.hallazgo, h.tarea,h.id_estado,h.observacion as obs');
        $this->db->select("concat(p.nombre,' ',p.apellido) as usuario_alta",false);
        $this->db->select("concat(p2.nombre,' ',p2.apellido) as usuario_responsable",false);
        $this->db->select("DATE_FORMAT(h.fecha_alta,'%d/%m/%Y') as fecha_alta", FALSE);
        $this->db->select("DATE_FORMAT(h.fecha_vto,'%d/%m/%Y') as fecha_vto", FALSE);
        $this->db->select("DATE_FORMAT(h.fecha_accion,'%d/%m/%Y') as fecha_accion", FALSE);
        $this->db->select(" e.estado as estado",FALSE);
//        $this->db->select('a.area');
        $this->db->select("concat(ee.abv,'-',a.area) as area",false);
         $this->db->select("if(h.id_estado=2,(select GROUP_CONCAT(id_archivo) from gr_archivos a where a.eliminado=0 and a.id_tarea=".$id_tarea."),'')as archivos", FALSE);
         $this->db->select("if(h.id_estado=2,(select GROUP_CONCAT(archivo_nom_orig) from gr_archivos a where a.eliminado=0 and a.id_tarea=".$id_tarea."),'')as archivos_qtip", FALSE);
         $this->db->select("if(h.id_estado=2,(select GROUP_CONCAT(extension) from gr_archivos a where a.eliminado=0 and a.id_tarea=".$id_tarea."),'')as archivos_ext", FALSE);
//         $this->db->select('if(h.id_estado=2,(select GROUP_CONCAT(concat("'.PATH_TAREAS_FILES.$id_tarea.'/cierre/",a.archivo)) from gr_archivos a where a.eliminado=0 and a.id_tarea='.$id_tarea.'),"")as archivos_path', FALSE);
         
        $this->db->from('gr_historial h');
        $this->db->join('gr_estados e','e.id_estado = h.id_estado','inner');
        $this->db->join('sys_usuarios u','u.id_usuario = h.usuario_alta','left');
        $this->db->join('sys_usuarios u2','u2.id_usuario = h.usuario_responsable','left');
        $this->db->join('gr_usuarios uu','uu.id_usuario = h.usuario_alta','left');
        $this->db->join('gr_usuarios uu2','uu2.id_usuario = h.usuario_responsable','left');
        $this->db->join('gr_puestos pto','pto.id_puesto = uu.id_puesto','left');
        $this->db->join('gr_puestos pto2','pto2.id_puesto = uu2.id_puesto','left');
        $this->db->join('gr_areas a','a.id_area = pto2.id_area','left');
        $this->db->join('grl_personas p','p.id_persona = u.id_persona','left');
        $this->db->join('grl_personas p2','p2.id_persona = u2.id_persona','left');
        $this->db->join('gr_organigramas or1','or1.id_organigrama = pto2.id_organigrama','left');
        $this->db->join('grl_empresas ee','ee.id_empresa = or1.id_empresa','left');
        $this->db->where('h.id_tarea',$id_tarea);
        $this->db->order_by('h.fecha_accion', 'asc');
        $this->db->limit($limit,$start); 
//        if ($sort!="")
//        {
//            if ($sort=='id_tarea')
//                $sort="h.".$sort;
//            else
//                $sort="h.".$sort;
//        $this->db->order_by($sort, $dir);
//        }
//        else
        $query = $this->db->get();
//        echo $this->db->last_query();
        $num = $this->cantSql('h.id_historial',$this->db->last_query());
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
        
     public function insert($datos)
	{
            
            $this->db->trans_begin();
		
		$this->db->insert('gr_historial',$datos);
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
     public function insertVencidas($datos)
	{
            
            $this->db->trans_begin();
		
		$this->db->set('id_tarea',$datos['id_tarea']);
		$this->db->set('id_estado',$datos['id_estado']);
		$this->db->set('fecha_accion','now()',FALSE);
		$this->db->insert('gr_historial');
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
     public function insertReabiertas($datos)
	{
            
            $this->db->trans_begin();
		
		$this->db->set('id_tarea',$datos['id_tarea']);
		$this->db->set('id_estado',$datos['id_estado']);
		$this->db->set('fecha_accion','now()',FALSE);
		$this->db->insert('gr_historial');
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
     public function insertObsoleto($datos)
	{
            
            $this->db->trans_begin();
		
		$this->db->set('id_tarea',$datos['id_tarea']);
		$this->db->set('id_estado',$datos['id_estado']);
		$this->db->set('fecha_accion','now()',FALSE);
		$this->db->set('observacion',$datos['observacion']);
		$this->db->insert('gr_historial');
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
        
        
        
}	
?>