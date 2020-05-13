<?php
class Gr_archivos_model extends CI_Model
{
    public function listadoTarea($id_tarea,$sort="",$dir='asc',$limit=10,$start=0)
    {
            $this->db->select(' a.id_archivo, a.archivo_nom_orig as archivo');
            $this->db->select('concat(a.tam div 1024," KB") as tam');
            $this->db->select("DATE_FORMAT(a.fecha_upload,'%d/%m/%Y %H:%i') as fecha_upload", FALSE);
            $this->db->from('gr_archivos a');
            $this->db->where('a.id_tarea',$id_tarea);
            $this->db->where('a.eliminado',0);

           
            if ($sort!="")
            {
                if ($sort=='archivo')
                    $sort="a.".$sort;
                else
                    $sort="a.".$sort;
                $this->db->order_by($sort, $dir);
            }
                $this->db->order_by("id_archivo", "desc"); 
//    /            $this->db->order_by("a.id_tarea", "asc"); 
            $this->db->limit($limit,$start); 

            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $this->cantSql('a.id_archivo',$this->db->last_query());
            $res = $query->result_array();
            if ($num > 0)
            {
                return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
            }
            else
                return '({"total":"0","rows":""})';
    }
    public function dameListadoPorTareaParaMail($id_tarea)
    {
//            $this->db->select('a.archivo');
            $this->db->select('concat("'.PATH_TAREAS_FILES.$id_tarea.'/cierre/",a.archivo) as archivo',false);
            $this->db->select('a.tam');
            $this->db->from('gr_archivos a');
            $this->db->where('a.id_tarea',$id_tarea);
            $this->db->where('a.eliminado',0);
            $this->db->limit(2,0); 
            $query = $this->db->get();
//            echo $this->db->last_query();
            $res = $query->result_array();
            $num = $query->num_rows();
            if ($num > 0)
                 return $res;
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

    public function dameArchivo($id)
    {
        $this->db->select('*');
        $this->db->from('gr_archivos a');
        $this->db->where("a.id_archivo",$id);
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
    public function cuentaArchivosPorTarea($id_tarea)
    {

        $this->db->select('count(a.id_archivo) as cant',false);
        $this->db->from('gr_archivos a');
        $this->db->where('a.id_tarea',$id_tarea);
        $this->db->where('a.eliminado',0);
        $query = $this->db->get();
//        echo $this->db->last_query();
        $res = $query->result();
        return $res[0]->cant;
    }
//    public function copy($id_tarea)
//    {
//        $sql="INSERT INTO gr_historial(id_tarea, usuario_alta, usuario_responsable, id_tipo_herramienta, id_estado, hallazgo, tarea, fecha_alta, fecha_vto, fecha_accion, observacion) 
//                select id_tarea, usuario_alta, usuario_responsable, id_tipo_herramienta, id_estado, hallazgo, tarea, fecha_alta, fecha_vto, fecha_accion, observacion  from gr_archivos 
//                where id_tarea=".$id_tarea;
//        $copy=$this->db->query($sql);
////            echo $this->db->last_query();
////            return $copy;
//        if(!$copy)
//                return false;
//        else
//                return true;
//
//    }

    public function insert($datos)
    {
        $this->db->trans_begin();
        $this->db->insert('gr_archivos',$datos);
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
    public function edit($id,$datos)
    {
        $this->db->where('id_archivo', $id);
        if(!$this->db->update('gr_archivos', $datos))
                return false;
        else
                return true;
    }
    public function delete($id)
    {
        $this->db->set('eliminado', 1);
        $this->db->set('fecha_eliminado', 'NOW()',false);
        $this->db->where('id_archivo',$id);
        $delete=$this->db->update('gr_archivos');
//            echo $this->db->last_query();
        if(!$delete)
                return false;
        else
                return true;
    }
    public function deleteAll($id)
    {
        $this->db->set('eliminado', 1);
        $this->db->set('fecha_eliminado', 'NOW()',false);
        $this->db->where('id_tarea',$id);
        $delete=$this->db->update('gr_archivos');
//            echo $this->db->last_query();
        if(!$delete)
                return false;
        else
                return true;
    }
    //actualiza el id de historial accion
    public function edit_ha($id_tarea,$id_ha)
    {
        $this->db->set('id_ha', $id_ha);
        $this->db->where('id_tarea', $id_tarea);
        if(!$this->db->update('gr_archivos'))
                return false;
        else
                return true;
    }    
        
}	
?>