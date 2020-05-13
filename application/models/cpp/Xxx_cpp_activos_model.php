<?php
class Cpp_activos_model extends CI_Model
{
    public function listado($start, $limit, $sort="",$dir,$busqueda="",$campos)
    {
        $this->db->select('id_activo,activo,descripcion,habilitado');
        $this->db->from('cpp_activos');

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
        $this->db->limit($limit,$start);
        $query = $this->db->get();
        $last_query=$this->db->last_query();
        $pasQuery=$this->sqlTxt('id_activo',$last_query);
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
//            echo $this->db->last_query();
    }

    public function insert($datos)
    {
        if ($this->db->insert("cpp_activos",$datos))
        {
            $last=$this->db->insert_id();
            return $last;
        }
        else
            return false;
    }

    function update($id, $datos)
    {
        $this->db->where("id_activo",$id);
        if ($this->db->update("cpp_activos",$datos))
            return true;
        else
            return false;
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
    public function dameCombo($start=0,$limit=15,$busqueda="")
    {
        $this->db->select('a.id_activo,activo');
        $this->db->from('cpp_activos a');
        $this->db->where('a.habilitado',1);
        if ($busqueda!="")
            $this->db->like('a.activo',$busqueda);
        $this->db->orderby('a.activo');
        $this->db->limit($limit,$start); 
        $query = $this->db->get();
//        echo $this->db->last_query();
        $last_query=$this->db->last_query();
        $pasQuery=$this->sqlTxt('a.id_activo',$last_query);
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
}	
?>