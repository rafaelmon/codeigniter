<?php
class Cap_participantes_model extends CI_Model
{
    public function listadoParaFormInformarTarea($id_tarea,$start, $limit, $sort,$dir)
    {
        $this->db->select('part.id');
        $this->db->select('concat(p.nombre,", ",p.apellido) as persona',false);
        $this->db->select('p.documento');
        $this->db->select("DATE_FORMAT(part.fecha_cap,'%d/%m/%Y') as fecha_cap", FALSE);
        $this->db->select(" CASE 
                            WHEN part.tipo=1 THEN 'Participante'
                            WHEN part.tipo=2 THEN 'Facilitador'
                            ELSE '-.-'
                        END as tipo",FALSE);
        $this->db->from('cap_participantes part');
        $this->db->join('grl_personas p','p.id_persona = part.id_persona','inner');
        $this->db->where('part.id_tarea',$id_tarea);
        if ($sort!="")
        {
             switch ($sort)
            {
                case 'id':
                    $ordenar="part.id";
                    break;
                case 'participante':
                    $ordenar='concat(p.nombre,", ",p.apellido)';
                    break;
                case 'fecha_cap':
                    $ordenar="part.fecha_cap";
                    break;
                default:
                    $ordenar="part.id";
                    break;

            }
            $this->db->order_by($ordenar, $dir);
        }
        else
        {
            $this->db->order_by('part.id','desc');
        }
        $this->db->limit($limit,$start);
        $query = $this->db->get();
//        echo $this->db->last_query();
        $last_query=$this->db->last_query();
        $pasQuery=$this->sqlTxt('part.id',$last_query);
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
    public function dameParticipantesPorCap($id_cap)
    {

        $this->db->select('part.id');
        $this->db->select('concat(p.nombre,", ",p.apellido) as persona',false);
        $this->db->select("DATE_FORMAT(part.fecha_cap,'%d/%m/%Y') as fecha", FALSE);
        $this->db->from('cap_participantes part');
        $this->db->join('grl_personas p','part.id_persona = p.id_persona','inner');
        $this->db->where('part.id_capacitacion',$id);
        if ($sort!="")
        {
            $sort="part.".$sort;
            $this->db->order_by($sort, $dir);
        }
        else
            $this->db->order_by("part.id", "asc"); 
//        $this->db->limit($limit,$start); 
        $this->db->limit(50,0); 

        $query = $this->db->get();
        $num = $this->cantSql('part.id',$this->db->last_query());
        $res = $query->result_array();
        if ($num > 0)
        {
            return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
        }
        else
            return '({"total":"0","rows":""})';
    }
    public function dameParticipantesPorTarea($id_tarea)
    {

        $this->db->select('part.id');
        $this->db->select('concat(p.nombre,", ",p.apellido) as persona',false);
        $this->db->select("DATE_FORMAT(part.fecha_cap,'%d/%m/%Y') as fecha", FALSE);
        $this->db->from('cap_participantes part');
        $this->db->join('grl_personas p','p.id_persona = part.id_persona','inner');
        $this->db->where('part.id_tarea',$id_tarea);
        $this->db->order_by("part.id", "asc"); 
        $this->db->limit(50,0); 
        $query = $this->db->get();
//        echo $this->db->last_query();
        $res = $query->result_array();
        $num=count($res);
        if ($num > 0)
        {
            return $res;
        }
        else
            return 0;
    }
    public function cuentaParticipantesPorTarea($id_tarea)
    {

        $this->db->select('count(part.id) as cant',false);
        $this->db->from('cap_participantes part');
        $this->db->where('part.id_tarea',$id_tarea);
        $query = $this->db->get();
//        echo $this->db->last_query();
        $res = $query->result();
        return $res[0]->cant;
    }

    public function insert($datos)
    {
        $this->db->trans_begin();

            $this->db->insert('cap_participantes',$datos);
            $insert_id=$this->db->insert_id();
//            echo $this->db->last_query();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE)
            {
                    $this->db->trans_rollback();
                    return false;
            }
            else
            {

                    $this->db->trans_commit();
                    return $insert_id;
            }
    }
    public function delete($id,$id_tarea)
    {
        $q=$this->db->delete('cap_participantes',array('id'=>$id));
       $this->db->where('id', $id);
       $this->db->where('id_tarea', $id_tarea);
        $this->db->delete('cap_participantes'); 
        if ($q)
                return true;
        else
            return false;
    }
    public function delete_all($id_tarea)
    {
       $this->db->where('id_tarea', $id_tarea);
        if ($this->db->delete('cap_participantes'))
                return true;
        else
            return false;
    }
    
    public function listadoParticipantes($start, $limit, $busqueda, $campos,$sort, $dir)
    {
        $this->db->select('distinct(cp.id_persona)',false);
        $this->db->select('concat(gp.nombre,", ",gp.apellido) as participante',false);
        $this->db->select('gp.documento as dni',false);
//        $this->db->select('select count ',false);
        $this->db->from('cap_participantes cp');
        $this->db->join('grl_personas gp','gp.id_persona = cp.id_persona','inner');

         if ($busqueda!="" && count($campos)>0)
        {
            foreach ($campos as &$campo)
            {
                switch ($campo)
                {
                    case 'id_persona':
                        $campo="cp.".$campo;
                        break;
                    case 'dni':
                        $campo='gp.documento';
                        break;
                    case 'participante':
                        $campo='concat(gp.nombre,", ",gp.apellido)';
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
                case 'id':
                    $ordenar="gp.id_persona";
                    break;
                case 'dni':
                    $ordenar='gp.documento';
                    break;
                case 'participante':
                    $ordenar='concat(gp.nombre,", ",gp.apellido)';
                    break;
                default:
                    $ordenar="gp.id_persona";
                    break;

            }
            $this->db->order_by($ordenar, $dir);
        }
        else
        {
            $this->db->order_by('gp.id_persona','desc');
        }
        
        $this->db->limit($limit,$start);
        $query = $this->db->get();
//        echo $this->db->last_query();
        $last_query=$this->db->last_query();
        $pasQuery=$this->sqlTxt('gp.id_persona',$last_query);
        $num = $this->cantSql($pasQuery);
        $res = $query->result_array();
        if ($num > 0)
        {
            return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
        }
        else
            return '({"total":"0","rows":""})';
    }
    public function listadoParticipantesPorTareaoCapacitacion($start, $limit, $busqueda, $campos,$sort, $dir,$id_cap,$id_tarea="")
    {
        $this->db->select('cp.id_persona',false);
        $this->db->select('concat(gp.nombre,", ",gp.apellido) as persona',false);
        $this->db->select('gp.documento as dni',false);
        $this->db->select('cp.id_tarea');
        $this->db->select(" CASE 
                            WHEN cp.tipo=1 THEN 'Participante'
                            WHEN cp.tipo=2 THEN 'Facilitador'
                            ELSE '-.-'
                        END as tipo",FALSE);
//        $this->db->select('select count ',false);
        $this->db->from('cap_participantes cp');
        $this->db->join('grl_personas gp','gp.id_persona = cp.id_persona','inner');
        $this->db->where('cp.id_capacitacion',$id_cap);
        if($id_tarea<>"")
            $this->db->where('cp.id_tarea',$id_tarea);

         if ($busqueda!="" && count($campos)>0)
        {
            foreach ($campos as &$campo)
            {
                switch ($campo)
                {
                    case 'id_persona':
                        $campo="cp.".$campo;
                        break;
                    case 'dni':
                        $campo='gp.documento';
                        break;
                    case 'participante':
                        $campo='concat(gp.nombre,", ",gp.apellido)';
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
                case 'id':
                    $ordenar="gp.id_persona";
                    break;
                case 'dni':
                    $ordenar='gp.documento';
                case 'participante':
                    $ordenar='concat(gp.nombre,", ",gp.apellido)';
                    break;
                default:
                    $ordenar="gp.id_persona";
                    break;

            }
            $this->db->order_by($ordenar, $dir);
        }
        else
        {
            $this->db->order_by('gp.id_persona','desc');
        }
        
        $this->db->limit($limit,$start);
        $query = $this->db->get();
//        echo $this->db->last_query();
        $last_query=$this->db->last_query();
        $pasQuery=$this->sqlTxt('gp.id_persona',$last_query);
        $num = $this->cantSql($pasQuery);
        $res = $query->result_array();
        if ($num > 0)
        {
            return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
        }
        else
            return '({"total":"0","rows":""})';
    }
    
    public function listadoCapacitacionesPorParticipante($start, $limit, $busqueda, $campos,$sort, $dir, $id_persona)
    {
        $this->db->select(" CASE 
                                WHEN cp.tipo=1 THEN 'Participante'
                                WHEN cp.tipo=2 THEN 'Facilitador'
                            END as tipo",FALSE);
        $this->db->select("DATE_FORMAT(cp.fecha_cap,'%d/%m/%Y') as fecha_cap", FALSE);
        $this->db->select('cc.id_capacitacion,cc.titulo');
        $this->db->select('cp.id_tarea');
        $this->db->select("(select GROUP_CONCAT(id_archivo) from gr_archivos a where a.eliminado=0 and a.id_tarea=cp.id_tarea) as archivos", FALSE);
        $this->db->select("(select GROUP_CONCAT(archivo_nom_orig) from gr_archivos a where a.eliminado=0 and a.id_tarea=cp.id_tarea)as archivos_qtip", FALSE);
        $this->db->select("(select GROUP_CONCAT(extension) from gr_archivos a where a.eliminado=0 and a.id_tarea=cp.id_tarea)as archivos_ext", FALSE);
        $this->db->from('cap_participantes cp');
        $this->db->join('cap_capacitaciones cc','cc.id_capacitacion = cp.id_capacitacion','inner');
        $this->db->where('cp.id_persona',$id_persona);
         if ($busqueda!="" && count($campos)>0)
        {
            foreach ($campos as &$campo)
            {
                switch ($campo)
                {
                    case 'titulo':
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
                case 'titulo':
                    $ordenar="cc.titulo";
                    break;
                case 'id_tarea':
                    $ordenar='cp.id_tarea';
                    break;
                case 'tipo':
                    $ordenar='cp.tipo';
                    break;
                default:
                    $ordenar="cp.fecha_cap";
                    break;

            }
            $this->db->order_by($ordenar, $dir);
        }
        else
        {
            $this->db->order_by('cp.id_persona','desc');
        }
        
        $this->db->limit($limit,$start);
        $query = $this->db->get();
//        echo $this->db->last_query();
        $last_query=$this->db->last_query();
        $pasQuery=$this->sqlTxt('cp.id_persona',$last_query);
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