<?php
class Ed_calificaciones_model extends CI_Model
{
    function listado($start, $limit,$id_periodo=0)
    {
        $this->db->select('c.id_calificacion, c.id_periodo,c.calificacion,c.valor,c.habilitado');
        $this->db->select('p.periodo');
        $this->db->from('ed_calificaciones c');
        $this->db->join('ed_periodos p','p.id_periodo = c.id_periodo');
        if ($id_periodo != 0)
            $this->db->where('c.id_periodo',$id_periodo);
        $query = $this->db->get();

        $num = $this->cantSql('id_calificacion',$this->db->last_query());
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
        
    function cantSql($count,$last_query)
    {
        $sql=  explode('FROM', $last_query);
        $sql=  explode('ORDER BY', $sql[1]);
        $sql=  "SELECT count($count) as cantidad FROM ".$sql[0];
        $query =$this->db->query($sql);
        $res = $query->result();
        return $res[0]->cantidad;
    }
    
    public function edit($id,$datos)
    {
        $this->db->where('id_calificacion', $id);
        if(!$this->db->update('ed_calificaciones', $datos))
                return false;
        else
                return true;
    }

    public function insert($datos)
    {
        if ($this->db->insert("ed_calificaciones",$datos))
            return true;
        else
            return false;
    }
}

