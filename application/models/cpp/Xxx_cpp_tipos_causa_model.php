<?php
class Cpp_tipos_causa_model extends CI_Model
{
    public function dameCombo($start=0,$limit=15,$busqueda="")
    {
        $this->db->select('tc.id_tc,tc');
        $this->db->from('cpp_tipos_causa tc');
        if ($busqueda!="")
            $this->db->like('tc.tc',$busqueda);
        $this->db->orderby('tc.tc');
        $this->db->limit($limit,$start); 
        $query = $this->db->get();
//        echo $this->db->last_query();
        $last_query=$this->db->last_query();
        $pasQuery=$this->sqlTxt('tc.tc',$last_query);
//         echo $pasQuery;
        $num = $this->cantSql($pasQuery);
        $res = $query->result_array();
        if ($num > 0)
            {
                    return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
            }
            else
                    return '({"total":"0","rows":""})';

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
}	
?>