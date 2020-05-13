<?php
class Cap_capacitaciones_model extends CI_Model
{
    public function listado($start, $limit, $sort,$dir,$busqueda=0,$campos=0)
    {
        $this->db->select('cc.id_capacitacion, cc.titulo,cc.descripcion,cc.id_usuario_alta');
        $this->db->select("concat(gp.nombre,' ',gp.apellido) as usuario_alta",false);
        $this->db->select("(select count(*) from gr_tareas gt1 where gt1.id_herramienta = cc.id_capacitacion and gt1.id_tipo_herramienta = 9) as qtareas",false);
        $this->db->from('cap_capacitaciones cc');
        $this->db->join('sys_usuarios su','su.id_usuario = cc.id_usuario_alta');
        $this->db->join('grl_personas gp','gp.id_persona = su.id_persona');
//        $this->db->where('a.habilitado',1);
        
         if ($busqueda!="" && count($campos)>0)
        {
            foreach ($campos as &$campo)
            {
                switch ($campo)
                {
                    case 'id_capacitacion':
                        $campo="cc.".$campo;
                        break;
                    case 'titulo':
                        $campo="cc.".$campo;
                        break;
                    case 'descripcion':
                        $campo="cc.".$campo;
                        break;
                }
            }
            unset($campo);
            
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
        
        if ($sort!="")
        {
             switch ($sort)
            {
                case 'id_capacitacion':
                    $ordenar="cc.id_capacitacion";
                    break;
                default:
                    $ordenar="cc.id_capacitacion";
                    break;

            }
            $this->db->order_by($ordenar, $dir);
        }
        else
        {
            $this->db->order_by('cc.id_capacitacion','asc');
        }
        $this->db->limit($limit,$start);
        $query = $this->db->get();
//        echo $this->db->last_query();
        $last_query=$this->db->last_query();
        $pasQuery=$this->sqlTxt('cc.id_capacitacion',$last_query);
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
//        echo $sql;
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
    
    public function insert($datos)
    {
        $this->db->trans_begin();

        $this->db->insert('cap_capacitaciones',$datos);
//        echo $this->db->last_query();
        $last=$this->db->insert_id();

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
                $this->db->trans_rollback();
                return 0;
        }
        else
        {
                $this->db->trans_commit();
                return $last;
        }
    }
    
    public function dameCapacitacion($id_capacitacion)
    {
        $this->db->select('cc.id_capacitacion,cc.titulo,cc.descripcion,cc.id_usuario_alta');
        $this->db->from('cap_capacitaciones cc');
        $this->db->where('cc.id_capacitacion',$id_capacitacion);
        $query = $this->db->get();
//        echo $this->db->last_query();
        $res = $query->result_array();
        $num = $query->num_rows();
        if ($num != 0)
        {
            return $res;
        }
        else
        {
            return 0;
        }
    }
}	
?>