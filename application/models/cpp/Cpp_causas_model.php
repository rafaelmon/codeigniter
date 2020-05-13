<?php
class Cpp_causas_model extends CI_Model
{
    public function dameCausa($id_causa)
    {
        $this->db->select('c.*');
       $this->db->select("concat(p.nombre,' ',p.apellido) as usuario_alta",false); 
        $this->db->from('cpp_causas c');
        $this->db->join('sys_usuarios u','u.id_usuario = c.id_usuario_alta','inner');
        $this->db->join('grl_personas p','p.id_persona = u.id_persona','inner');
        $this->db->join('cpp_areas_causante ac','ac.id_ac = c.id_ac','inner');
        $this->db->where('c.id_causa',$id_causa);
        $query = $this->db->get();
//         echo $this->db->last_query();
        $res = $query->row_array();
        return $res;
    }
    public function dameCausasPorIdEvento($id_evento)
    {
        $this->db->select('c.id_causa');
        $this->db->from('cpp_causas c');
        $this->db->where('c.id_evento',$id_evento);
        $query = $this->db->get();
//         echo $this->db->last_query();
        $num = $query->num_rows();
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
    public function dameCausasPorEventoParaExcel($id_evento)
    {
        $this->db->select('c.id_causa');
        $this->db->select('c.causa_inmediata');
        $this->db->select('c.causa_raiz');
        $this->db->select('ac.ac');
        $this->db->from('cpp_causas c');
        $this->db->join('cpp_areas_causante ac','ac.id_ac = c.id_ac','inner');
        $this->db->where('c.id_evento',$id_evento);
        $this->db->where('c.habilitado',1);
        $query = $this->db->get();
//         echo $this->db->last_query();
        $num = $query->num_rows();
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
    
    public function insert($datos)
    {
        if ($this->db->insert("cpp_causas",$datos))
        {
            $last=$this->db->insert_id();
            return $last;
        }
        else
            return false;
    }

    function update($id, $datos)
    {
        $this->db->where("id_causa",$id);
        if ($this->db->update("cpp_causas",$datos))
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
    
    public function listado($id_evento,$start, $limit, $sort="",$dir,$busqueda="",$campos=array())
    {
        $this->db->select('c.id_causa,c.id_ac,causa_raiz,causa_inmediata');
        $this->db->select('ac.ac');
        $this->db->from('cpp_causas c');
        $this->db->join('cpp_areas_causante ac','ac.id_ac = c.id_ac','inner');
        
        $this->db->where('c.id_evento',$id_evento);

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
        if($sort == "")
            $this->db->order_by('id_causa','desc');
        $this->db->limit($limit,$start);
        $query = $this->db->get();
//        echo $this->db->last_query();
        $last_query=$this->db->last_query();
        $pasQuery=$this->sqlTxt('c.id_causa',$last_query);
//        $num = $query->num_rows();
        $num = $this->cantSql($pasQuery);
        $res = $query->result_array();

        if ($num > 0)
            return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
        else
            return '({"total":"0","rows":""})';
    }
    
    public function dameIdEventoDeCausa($id_causa)
    {
        $this->db->select('id_evento');
        $this->db->from('cpp_causas');
        $this->db->where('id_causa',$id_causa );
        $query = $this->db->get();
//        echo $this->db->last_query();
        $res = $query->row();
        $num = $query->num_rows();
        if ($num > 0)
        {
            return $res->id_evento;
        }
        else
            return 0;

    }
}	
?>