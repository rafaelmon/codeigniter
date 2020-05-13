<?php
class Cpp_equipos_model extends CI_Model
{
    public function listado($start, $limit, $sort="",$dir,$busqueda="",$campos)
    {
        $this->db->select('id_equipo,equipo,descripcion,habilitado,tag');
        $this->db->from('cpp_equipos');

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
            $this->db->order_by('id_equipo','desc');
        else
            $this->db->order_by($sort, $dir);
        
        $this->db->limit($limit,$start);
        $query = $this->db->get();
//            echo $this->db->last_query();
        $last_query=$this->db->last_query();
        $pasQuery=$this->sqlTxt('id_equipo',$last_query);
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
        if ($this->db->insert("cpp_equipos",$datos))
        {
            $last=$this->db->insert_id();
            return $last;
        }
        else
            return false;
    }

    function update($id, $datos)
    {
        $this->db->where("id_equipo",$id);
        if ($this->db->update("cpp_equipos",$datos))
            return true;
        else
            return false;
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
    public function dameCombo($start=0,$limit=15,$busqueda="")
    {
        $this->db->select('e.id_equipo');
        $this->db->select('concat(e.tag," - ",e.equipo) as equipo',false);
        $this->db->from('cpp_equipos e');
        $this->db->where('e.habilitado',1);
        if ($busqueda!="")
            $this->db->like('concat(e.tag," ",e.equipo)',$busqueda);
        $this->db->orderby('e.equipo','asc');
        $this->db->limit($limit,$start); 
        $query = $this->db->get();
//        echo $this->db->last_query();
        $last_query=$this->db->last_query();
        $pasQuery=$this->sqlTxt('e.id_equipo',$last_query);
//         echo $pasQuery;
        $num = $this->cantSql($pasQuery);
        $res = $query->result_array();
        if ($num > 0)
            return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
        else
            return '({"total":"0","rows":""})';

    }
}	
?>