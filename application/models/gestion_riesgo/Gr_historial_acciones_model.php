<?php
class Gr_historial_acciones_model extends CI_Model
{
	
    public function listado($id_tarea, $start, $limit,$sort="", $dir="")
    {
        $this->db->select('ha.id, ha.id_accion');
        $this->db->select('fnStripTags(ha.texto) as texto',false);
        $this->db->select('a.accion,a.bgcolor,a.color');
        $this->db->select("if(ha.id_usuario is NULL,'Sistema',concat(p.nombre,' ',p.apellido)) as usuario",false);
        $this->db->select("DATE_FORMAT(ha.fecha_alta,'%d/%m/%Y %h:%m') as fecha", FALSE);
        $this->db->select("if(ha.id_accion=4,(select GROUP_CONCAT(id_archivo) from gr_archivos a where a.eliminado=0 and a.id_ha=ha.id and a.id_tarea=".$id_tarea."),'')as archivos", FALSE);
        $this->db->select("if(ha.id_accion=4,(select GROUP_CONCAT(archivo_nom_orig) from gr_archivos a2 where a2.eliminado=0 and a2.id_ha=ha.id and a2.id_tarea=".$id_tarea."),'')as archivos_qtip", FALSE);
        $this->db->select("if(ha.id_accion=4,(select GROUP_CONCAT(extension) from gr_archivos a3 where a3.eliminado=0 and a3.id_ha=ha.id and a3.id_tarea=".$id_tarea."),'')as archivos_ext", FALSE);
        $this->db->from('gr_historial_acciones ha');
        $this->db->join('gr_acciones a','a.id_accion = ha.id_accion','inner');
        $this->db->join('sys_usuarios u','u.id_usuario = ha.id_usuario','left');
        $this->db->join('grl_personas p','p.id_persona = u.id_persona','left');
        $this->db->where('ha.id_tarea',$id_tarea);
        $this->db->order_by('ha.fecha_alta', 'asc');
        $this->db->limit($limit,$start); 
        $query = $this->db->get();
        $num = $this->cantSql('ha.id',$this->db->last_query());
        $res = $query->result_array();
        if ($num > 0)
        {
            return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
        }
        else
            return '({"total":"0","rows":""})';
    } 
    public function dameUltimaObservadaPorTarea($id_tarea)
    {
        $this->db->select('ha.id, ha.texto');
        $this->db->select("DATE_FORMAT(ha.fecha_alta,'%d/%m/%Y %h:%m') as fecha_obs", FALSE);
        $this->db->from('gr_historial_acciones ha');
        $this->db->where('ha.id_tarea',$id_tarea);
        $this->db->where('ha.id_accion',5);
        $this->db->order_by('ha.id', 'desc');
        $this->db->limit(1,0); 
        $query = $this->db->get();
        $res = $query->result_array();
        $num=count($res);
        if ($num > 0)
            return $res[0];
        else
            return 0;
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

            $this->db->insert('gr_historial_acciones',$datos);
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