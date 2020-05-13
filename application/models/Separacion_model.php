<?php
class Separacion_model extends CI_Model
{
    public function dameDocumentos($alcance)
    {
        $this->db->select("d.id_documento", false);
        $this->db->from("dms_documentos d");
        
        $this->db->where("d.alcance",$alcance);
        $this->db->order_by("d.id_documento", "asc");
        $query = $this->db->get();
//        echo $this->db->last_query();
        $num = $query->num_rows();
        $res = $query->result_array();
        if($num == 0)
            return 0;
        else
            return $res;
    }
    
    public function deleteDocumento($id_documento)
    {
        $this->db->trans_begin();

        $this->db->query("DELETE FROM dms_documentos WHERE id_documento = ".$id_documento);

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
    
    public function deleteObservaciones($id_documento)
    {
        $this->db->trans_begin();

        $this->db->query("DELETE FROM dms_observaciones WHERE id_documento = ".$id_documento);

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
    
    public function deleteGestiones($id_documento)
    {
        $this->db->trans_begin();

        $this->db->query("DELETE FROM dms_gestiones WHERE id_documento = ".$id_documento);

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
    
    public function deleteUsuariosDoc($id_documento)
    {
        $this->db->trans_begin();

        $this->db->query("DELETE FROM dms_usuarios_doc WHERE id_documento = ".$id_documento);

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
    
    public function dameArchivo($id_documento)
    {
        $this->db->select("d.archivo", false);
        $this->db->from("dms_documentos d");
        $this->db->where("d.id_documento",$id_documento);
        $query = $this->db->get();
//        echo $this->db->last_query();
        $num = $query->num_rows();
        $res = $query->row();
//         echo "<pre>".print_r($res,true)."</pre>";
        if($num == 0)
            return 0;
        else
            return $res->archivo;
    }
    
    public function dameTareas($id_empresa)
    {
        $this->db->select("t.id_tarea", false);
        $this->db->from("gr_tareas t");
        //usuario alta
        $this->db->join("gr_usuarios gu", "gu.id_usuario = t.usuario_alta","inner");
        $this->db->join("gr_puestos gp", "gp.id_puesto = gu.id_puesto","inner");
        $this->db->join("gr_areas ga", "ga.id_area = gp.id_area","inner");
        $this->db->join("gr_organigramas go", "go.id_organigrama = ga.id_organigrama","inner");
        //usuario responsable
        $this->db->join("gr_usuarios gu1", "gu1.id_usuario = t.usuario_responsable","inner");
        $this->db->join("gr_puestos gp1", "gp1.id_puesto = gu1.id_puesto","inner");
        $this->db->join("gr_areas ga1", "ga1.id_area = gp1.id_area","inner");
        $this->db->join("gr_organigramas go1", "go1.id_organigrama = ga1.id_organigrama","inner");
        
        $this->db->where("go.id_empresa",$id_empresa);
        $this->db->or_where("go1.id_empresa",$id_empresa);
        $this->db->order_by("t.id_tarea", "asc");
        $query = $this->db->get();
//        echo $this->db->last_query();die();
        $num = $query->num_rows();
        $res = $query->result_array();
        if($num == 0)
            return 0;
        else
            return $res;
    }
    
    public function deleteTarea($id_tarea)
    {
        $this->db->trans_begin();

        $this->db->query("DELETE FROM gr_tareas WHERE id_tarea = ".$id_tarea );

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
    
    public function deleteHistorial($id_tarea)
    {
        $this->db->trans_begin();

        $this->db->query("DELETE FROM gr_historial WHERE id_tarea = ".$id_tarea);

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
    
    public function deleteTareasRPyC($id_tarea)
    {
        $this->db->trans_begin();

        $this->db->query("DELETE FROM gr_tareas_rpyc WHERE id_tarea = ".$id_tarea);

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
    
    public function deleteCierres($id_tarea)
    {
        $this->db->trans_begin();

        $this->db->query("DELETE FROM gr_cierres WHERE id_tarea = ".$id_tarea);

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
    
    public function deleteArchivos($id_tarea)
    {
        $this->db->trans_begin();

        $this->db->query("DELETE FROM gr_archivos WHERE id_tarea = ".$id_tarea);

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
    
    public function deleteHistorialAcciones($id_tarea)
    {
        $this->db->trans_begin();

        $this->db->query("DELETE FROM gr_historial_acciones WHERE id_tarea = ".$id_tarea);

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
    
    public function dameArchivosTareas($id_tarea)
    {
        $this->db->select("a.archivo", false);
        $this->db->from("gr_archivos a");
        
        $this->db->where("a.id_tarea",$id_tarea);
        $query = $this->db->get();
//        echo $this->db->last_query();
        $num = $query->num_rows();
        $res = $query->result_array();
        if($num == 0)
            return 0;
        else
            return $res;
    }
}	
?>