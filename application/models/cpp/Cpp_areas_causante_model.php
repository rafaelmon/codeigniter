<?php
class Cpp_areas_causante_model extends CI_Model
{
    public function listado($start, $limit, $sort="",$dir,$busqueda="",$campos)
    {
        $this->db->select('id_ac,ac,descripcion,habilitado');
        $this->db->from('cpp_areas_causante');

        if ($busqueda!="" && count($campos)>0)
        {
            if(count($campos)==1)
            {
                $this->db->where($campos[0]. " like","'%".$busqueda."%'",FALSE);
            }
            else
            {
                $n=0;
                foreach ($campos as $campo)
                {
                    if ($n==0)
                        {
                        $this->db->where("(".$campo." like","'%".$busqueda."%'",FALSE);
                        }
                    else
                    {
                        if ($n==count($campos)-1)
                        {
                            $this->db->or_where($campo ." like","'%".$busqueda."%')",FALSE);
                        }
                        else
                        {
                            $this->db->or_where($campo." like","'%".$busqueda."%'",FALSE);
                        }
                    }
                   $n++;     
                }
            }
        }
        if ($sort=="")
            $this->db->order_by('id_ac','desc');
        else
            $this->db->order_by($sort, $dir);
        
        $this->db->limit($limit,$start);
        $query = $this->db->get();
//            echo $this->db->last_query();
        $last_query=$this->db->last_query();
        $pasQuery=$this->sqlTxt('id_ac',$last_query);
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
    public function insert($datos)
    {
        if ($this->db->insert("cpp_areas_causante",$datos))
        {
            $last=$this->db->insert_id();
            return $last;
        }
        else
            return false;
    }

    function update($id, $datos)
    {
        $this->db->where("id_ac",$id);
        if ($this->db->update("cpp_areas_causante",$datos))
            return true;
        else
            return false;
    }

    public function dameCombo($start=0,$limit=15,$busqueda="")
    {
        $this->db->select('ac.id_ac,ac');
        $this->db->from('cpp_areas_causante ac');
         $this->db->where('ac.habilitado',1);
        if ($busqueda!="")
            $this->db->like('ac.ac',$busqueda);
        $this->db->orderby('ac.ac');
        $this->db->limit($limit,$start); 
        $query = $this->db->get();
//        echo $this->db->last_query();
        $last_query=$this->db->last_query();
        $pasQuery=$this->sqlTxt('ac.ac',$last_query);
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