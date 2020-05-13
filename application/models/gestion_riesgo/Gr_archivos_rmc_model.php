<?php
class Gr_archivos_rmc_model extends CI_Model
{
    public function listado($id_rmc,$sort="",$dir='asc',$limit=10,$start=0)
    {
            $this->db->select('a.id_archivo, a.archivo_nom_orig as archivo');
            $this->db->select('a.titulo');
            $this->db->select('a.descripcion as descr');
            $this->db->select('a.extension as archivo_ext');
            $this->db->select('concat(a.tam div 1024," KB") as tam');
            $this->db->select("DATE_FORMAT(a.fecha_upload,'%d/%m/%Y %H:%i') as fecha_alta", FALSE);
             $this->db->select("concat(p.nombre,' ',p.apellido) as usuario_alta",false);
            $this->db->from('gr_archivos_rmc a');
            $this->db->join('sys_usuarios u','u.id_usuario = a.usuario_upload','inner');
            $this->db->join('gr_usuarios gu','gu.id_usuario = u.id_usuario','inner');
            $this->db->join('grl_personas p','p.id_persona = u.id_persona','left');
            $this->db->where('a.id_rmc',$id_rmc);
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
//    /            $this->db->order_by("a.id_rmc", "asc"); 
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
    public function dameListadoPorRmcParaMail($id_rmc)
    {
//            $this->db->select('a.archivo');
            $this->db->select('concat("'.PATH_TAREAS_FILES.$id_rmc.'/",a.archivo) as archivo',false);
            $this->db->select('a.tam');
            $this->db->from('gr_archivos_rmc a');
            $this->db->where('a.id_rmc',$id_rmc);
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
        $this->db->from('gr_archivos_rmc a');
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
    public function cuentaArchivosPorRi($id_rmc)
    {

        $this->db->select('count(a.id_archivo) as cant',false);
        $this->db->from('gr_archivos_rmc a');
        $this->db->where('a.id_rmc',$id_rmc);
        $this->db->where('a.eliminado',0);
        $query = $this->db->get();
//        echo $this->db->last_query();
        $res = $query->result();
        return $res[0]->cant;
    }

    public function insert($datos)
    {
        $this->db->trans_begin();
        $this->db->insert('gr_archivos_rmc',$datos);
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
        if(!$this->db->update('gr_archivos_rmc', $datos))
                return false;
        else
                return true;
    }
    public function delete($id)
    {
        $this->db->set('eliminado', 1);
        $this->db->set('fecha_eliminado', 'NOW()',false);
        $this->db->where('id_archivo',$id);
        $delete=$this->db->update('gr_archivos_rmc');
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
        $this->db->where('id_rmc',$id);
        $delete=$this->db->update('gr_archivos_rmc');
//            echo $this->db->last_query();
        if(!$delete)
                return false;
        else
                return true;
    }
    //actualiza el id de historial accion
    public function edit_ha($id_rmc,$id_ha)
    {
        $this->db->set('id_ha', $id_ha);
        $this->db->where('id_rmc', $id_rmc);
        if(!$this->db->update('gr_archivos_rmc'))
                return false;
        else
                return true;
    }    
        
}	
?>