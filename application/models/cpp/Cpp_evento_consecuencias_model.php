<?php
class Cpp_evento_consecuencias_model extends CI_Model
{
    public function listado($id_evento,$start, $limit, $sort="",$dir,$busqueda="",$campos=array())
    {
        $this->db->select('ec.id_ec,ec.descripcion, ec.monto,ec.unidades_perdidas,ec.set_monto');
        $this->db->select('c.consecuencia');
        $this->db->select("concat(p.nombre,' ',p.apellido) as usuario_alta",false);
//        $this->db->select("concat(p2.nombre,' ',p2.apellido) as usuario_valoracion",false);
        $this->db->select("DATE_FORMAT(ec.fecha_alta,'%d/%m/%Y') as fecha_alta", FALSE);
//        $this->db->select("DATE_FORMAT(ec.fecha_valoracion,'%d/%m/%Y') as fecha_val", FALSE);
        $this->db->from('cpp_evento_consecuencias ec');
        $this->db->join('cpp_consecuencias c','c.id_consecuencia = ec.id_consecuencia','inner');
        $this->db->join('sys_usuarios u','u.id_usuario = ec.id_usuario_alta','inner');
        $this->db->join('grl_personas p','p.id_persona = u.id_persona','inner');
//        $this->db->join('sys_usuarios u2','u2.id_usuario = ec.id_usuario_valoracion','left');
//        $this->db->join('grl_personas p2','p2.id_persona = u2.id_persona','left');
        $this->db->where('ec.id_evento',$id_evento);
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
//        echo $this->db->last_query();
        $last_query=$this->db->last_query();
        $pasQuery=$this->sqlTxt('id_ec',$last_query);
//        $num = $query->num_rows();
        $num = $this->cantSql($pasQuery);
        $res = $query->result_array();

        if ($num > 0)
            return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
        else
            return '({"total":"0","rows":""})';
    }
    
    public function dameConsecuenciasPorEventoParaExcel($id_evento)
    {
        $this->db->select('ec.id_ec,ec.descripcion, ec.monto,ec.unidades_perdidas');
        $this->db->select('c.consecuencia');
        $this->db->select("concat(p.nombre,' ',p.apellido) as usuario_alta",false);
        $this->db->from('cpp_evento_consecuencias ec');
        $this->db->join('cpp_consecuencias c','c.id_consecuencia = ec.id_consecuencia','inner');
        $this->db->join('sys_usuarios u','u.id_usuario = ec.id_usuario_alta','inner');
        $this->db->join('grl_personas p','p.id_persona = u.id_persona','inner');
        $this->db->where('ec.id_evento',$id_evento);
        $this->db->where('ec.habilitado',1);
        $query = $this->db->get();
//        echo $this->db->last_query();
        $last_query=$this->db->last_query();
        $pasQuery=$this->sqlTxt('id_ec',$last_query);
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
    
    public function controlConsecuenciasValoradas($id_evento)
    {
        $this->db->select('id_ec');
        $this->db->from('cpp_evento_consecuencias');
        $this->db->where('id_evento',$id_evento);
        $this->db->where('id_usuario_valoracion',NULL);
        $this->db->where('monto',NULL);
        $query = $this->db->get();
//        echo $this->db->last_query();
        $num = $query->num_rows();
        if ($num == 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function insert($datos)
    {
        $insert=$this->db->insert("cpp_evento_consecuencias",$datos);
//        echo $this->db->last_query();
        if ($insert)
        {
            $last=$this->db->insert_id();
            return $last;
        }
        else
            return false;
    }

    function update($id, $datos)
    {
        $this->db->where("id_ec",$id);
        if ($this->db->update("cpp_evento_consecuencias",$datos))
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
    
    public function dameIdEventoDeConsecuencia($id_ec)
    {
        $this->db->select('id_evento');
        $this->db->from('cpp_evento_consecuencias');
        $this->db->where('id_ec',$id_ec );
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