<?php
class Ed_competencias_model extends CI_Model
{
    function listado($start, $limit)
    {
        $this->db->select('c.id_competencia, c.competencia,c.tipo,c.habilitado');
        $this->db->select('(select count(es.id_subcompetencia)from ed_subcompetencias es where es.id_competencia = c.id_competencia) as q_subcomp');
        $this->db->from('ed_competencias c');
//        $this->db->where('habilitado',1);
        $query = $this->db->get();

        $num = $this->cantSql('id_competencia',$this->db->last_query());
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
    
    function dameCompetenciasParaPdf()
    {
        $this->db->select('distinct c.id_competencia, c.competencia', false);
        $this->db->from('ed_competencias c');
        $this->db->join('ed_subcompetencias sc','sc.id_competencia = c.id_competencia','left');
        $this->db->where('c.habilitado',1);
        $this->db->where('sc.habilitado',1);
        $query = $this->db->get();
        $res = $query->result_array();
        $num = count($res);
        if ($num > 0)
        {
                return $res;
        }
        else
        {
                return 0;
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
    
    function update($id, $datos)
    {
        $this->db->where("id_competencia",$id);
        if ($this->db->update("ed_competencias",$datos))
            return true;
        else
            return false;
    }

    public function insert($datos)
    {
            if ($this->db->insert("ed_competencias",$datos))
                    return true;
            else
                    return false;
    }
}

