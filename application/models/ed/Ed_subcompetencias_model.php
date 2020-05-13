<?php
class Ed_subcompetencias_model extends CI_Model
{
    function listado($id_competencia,$start, $limit)
    {
        $this->db->select('id_subcompetencia, subcompetencia,habilitado,obligatoria');
        $this->db->from('ed_subcompetencias');
        $this->db->where('id_competencia',$id_competencia);
        $query = $this->db->get();

        $num = $this->cantSql('id_subcompetencia',$this->db->last_query());
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
    
    function dameSubcompetenciasParaPdf($id_competencia)
    {
        $this->db->select('id_subcompetencia, subcompetencia');
        $this->db->from('ed_subcompetencias');
        $this->db->where('id_competencia',$id_competencia);
        $this->db->where('habilitado',1);
        $query = $this->db->get();

        $num = $this->cantSql('id_subcompetencia',$this->db->last_query());
        $res = $query->result_array();
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
        $this->db->where("id_subcompetencia",$id);
        if ($this->db->update("ed_subcompetencias",$datos))
            return true;
        else
            return false;
    }

    public function insert($datos)
    {
            if ($this->db->insert("ed_subcompetencias",$datos))
                    return true;
            else
                    return false;
    }
    public function dameCompetenciasSubcompetencias()
        {
            $this->db->select('sc.id_competencia,sc.id_subcompetencia');
            $this->db->from('ed_subcompetencias sc');
            $this->db->join('ed_competencias c','c.id_competencia = sc.id_competencia');
            $this->db->where("c.habilitado",1);
            $this->db->where("sc.habilitado",1);
            $query = $this->db->get();
            $num = $query->num_rows();
            $res = $query->result_array();
            if ($num > 0)
                    return $res;
            else
                    return 0;
        }
        
}


