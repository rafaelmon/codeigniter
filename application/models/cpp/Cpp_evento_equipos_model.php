<?php
class Cpp_evento_equipos_model extends CI_Model
{
    public function insert($datos)
    {
        $insert=$this->db->insert("cpp_evento_equipos",$datos);
        if ($insert)
        {
            return true;
        }
        else
            return false;
    }

    public function delete($id)
    {
        $this->db->trans_begin();

        $this->db->query("DELETE FROM cpp_evento_equipos WHERE id_evento = ".$id);

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
     public function dameEventosPorEquipos($ids_equipos)
    {
        $this->db->select('e.id_evento');
        $this->db->from('cpp_evento_equipos e');
        $this->db->where_in('e.id_equipo',$ids_equipos);
        $this->db->orderby('e.id_evento','asc');
        $query = $this->db->get();
//        echo $this->db->last_query();
        $num=$query->num_rows();
//            echo "num:".$num;
        if ($num > 0)
        {
            $res = $query->result_array();
            return $res; 
        }
        else
            return false;
    }
    public function dameComboEquipos($start=0,$limit=15,$busqueda="")
    {
        $this->db->select('e2.id_equipo');
        $this->db->select('concat(e2.tag," - ",e2.equipo) as equipo',false);
        $this->db->from('cpp_evento_equipos e');
         $this->db->join("cpp_equipos e2","e2.id_equipo=e.id_equipo",'inner');
         $this->db->group_by("e.id_equipo");
//        $this->db->where('e.habilitado',1);
        if ($busqueda!="")
            $this->db->like('concat(e2.tag," ",e2.equipo)',$busqueda);
        $this->db->orderby('e2.equipo','asc');
        $this->db->limit($limit,$start); 
        $query = $this->db->get();
//        echo $this->db->last_query();
        $last_query=$this->db->last_query();
        $pasQuery=$this->sqlTxt('e2.id_equipo',$last_query);
//         echo $pasQuery;
        $num = $this->cantSql($pasQuery);
        $res = $query->result_array();
        if ($num > 0)
            return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
        else
            return '({"total":"0","rows":""})';

    }
    function sqlTxt($count,$sql)
    {
        $exploud=  explode('FROM', $sql);
        $exploud=  explode('ORDER BY', $exploud[1]);
        $newSql=  "SELECT count($count) as cantidad FROM ".$exploud[0];
//        echo $newSql;
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