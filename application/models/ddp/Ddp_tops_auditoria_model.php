<?php
class Ddp_tops_auditoria_model extends CI_Model
{
    public function listado($start, $limit,$sort="", $dir="",$id_top)
    {
        $this->db->select('dat.id,dat.id_top,dat.id_usuario_alta, dat.observacion');
        
        $this->db->select('da.accion');
        
        $this->db->select("concat(gp.nombre, ' ', gp.apellido) as usuario_alta",false);
        
        $this->db->select("DATE_FORMAT(dat.fecha_alta,'%d/%m/%Y %H:%i:%S') as fecha_alta", FALSE);
        
        $this->db->from('ddp_tops_auditoria dat');
        
        $this->db->join('sys_usuarios su','su.id_usuario = dat.id_usuario_alta','inner');
        $this->db->join('grl_personas gp','gp.id_persona = su.id_persona','inner');
        
        $this->db->join('ddp_acciones da','da.id_accion = dat.id_accion','inner');
        
//        $this->db->where("dat.habilitado",1);
        $this->db->where("dat.id_top",$id_top);

        $this->db->order_by("dat.id", $dir); 

        $this->db->limit($limit,$start); 
        $query = $this->db->get();
//        echo $this->db->last_query();
        $res = $query->result_array();
        $num=count($res);
        
        $num = $this->cantSql('dat.id',$this->db->last_query());
        
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

        $this->db->insert('ddp_tops_auditoria',$datos);
        $last=$this->db->insert_id();

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
}	
?>