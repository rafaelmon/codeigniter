<?php
class Cpp_tipos_consecuencias_model extends CI_Model
{
    public function listado($id_usuario, $start, $limit, $sort="",$busqueda,$campos)
    {
        $this->db->select('id_tipo_consecuencia,tipo_consecuencia,descripcion,habilitado');
        $this->db->from('cpp_tipos_consecuencias');

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
        $query = $this->db->get();

        $num = $this->cantSql('id_tipo_consecuencia',$this->db->last_query());
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
        if ($this->db->insert("cpp_tipos_consecuencias",$datos))
        {
            $last=$this->db->insert_id();
            return $last;
        }
        else
            return false;
    }

    function update($id, $datos)
    {
        $this->db->where("id_tipo_consecuencia",$id);
        if ($this->db->update("cpp_tipos_consecuencias",$datos))
            return true;
        else
            return false;
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
}	
?>