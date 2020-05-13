<?php
class Cpp_auditoria_evento_model extends CI_Model
{
    public function listado($id_evento,$start, $limit, $sort="",$dir)
    {
        $this->db->select('ae.id');
        $this->db->select('ae.obs');
        $this->db->select("DATE_FORMAT(ae.fecha_accion,'%d/%m/%Y') as fecha", FALSE);
        $this->db->select("concat(p.nombre,' ',p.apellido) as usuario",false);
        $this->db->select('e1.estado');
        $this->db->select('e2.estado');
        $this->db->select('a.accion');
        $this->db->from('cpp_auditoria_evento ae');
        $this->db->join('sys_usuarios u','u.id_usuario = ae.id_usuario_accion','inner');
        $this->db->join('grl_personas p','p.id_persona = u.id_persona','inner');
        $this->db->join('cpp_estados e1','e1.id_estado = ae.id_estado_anterior','inner');
        $this->db->join('cpp_estados e2','e2.id_estado = ae.id_estado_siguiente','inner');
        $this->db->join('cpp_acciones a','a.id_accion = ae.id_accion','inner');
        $this->db->where('ae.id_evento',$id_evento);
        $this->db->limit($limit,$start);
        $query = $this->db->get();
//        echo $this->db->last_query();
        $last_query=$this->db->last_query();
        $pasQuery=$this->sqlTxt('id',$last_query);
        $num = $this->cantSql($pasQuery);
        $res = $query->result_array();
        if ($num > 0)
        {
            return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
        }
        else
        {
            return '({"total":"0","rows":""})';
        }
    }

    function sqlTxt($count,$sql)
    {
        $exploud=  explode('FROM', $sql);
        $exploud=  explode('ORDER BY', $exploud[1]);
        $newSql=  "SELECT count($count) as cantidad FROM ".$exploud[0];
            return $newSql;
    }
    function cantSql($sql)
    {
        $query =$this->db->query($sql);
        $res = $query->result();
        return $res[0]->cantidad;
    }
    
    public function insert($datos)
    {
        if ($this->db->insert("cpp_auditoria_evento",$datos))
        {
            $last=$this->db->insert_id();
            return $last;
        }
        else
            return false;
    }

}	
?>