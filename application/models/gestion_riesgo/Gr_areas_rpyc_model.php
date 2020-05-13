<?php
class Gr_areas_rpyc_model extends CI_Model
{
     public function listado($start, $limit, $busqueda, $campos,$sort="", $dir="")
    {
            $this->db->select('a.id_area, a.id_empresa, a.id_area_padre, a.area, a.habilitado');
            $this->db->select('e.empresa');
            $this->db->select('ap.area as area_padre');
//            $this->db->select("concat(p.nombre,' ',p.apellido) as usuario_alta",false);
//            $this->db->select(" CASE 
//                                WHEN t.id_grado_crit=1 THEN 'Critica'
//                                WHEN t.id_grado_crit=2 THEN 'Alta'
//                                WHEN t.id_grado_crit=3 THEN 'Menor'
//                                ELSE '-.-'
//                            END as grado_crit",FALSE);
            $this->db->from('gr_areas_rpyc a');
            $this->db->join('grl_empresas e','e.id_empresa = a.id_empresa','inner');
            $this->db->join('gr_areas_rpyc ap','ap.id_area = a.id_area_padre','left');
//            $this->db->join('gr_estados e','e.id_estado = t.id_estado','inner');
//            $this->db->where('',);

            if ($busqueda!="" && count($campos)>0)
            {
                foreach ($campos as &$campo)
                {
                    switch ($campo)
                    {
                        case 'area':
                            $campo="a.area";
                            break;
                        
                        case 'area_padre':
                            $campo="ap.area";
                            break;
                    }
                }
                unset($campo);
//                    echo "<pre>". print_r($campos,true)."<pre>";
                //si viene un solo campo de busqueda
                if(count($campos)==1)
                    $this->db->where("$campos[0] like","'%".$busqueda."%'",FALSE);
                else
                {
                    $n=0;
                    foreach ($campos as $campo)
                    {
                        if ($n==0)
                            $this->db->where("($campo like","'%".$busqueda."%'",FALSE);
                        else
                        {
                            if ($n==count($campos)-1)
                                $this->db->or_where("$campo like","'%".$busqueda."%')",FALSE);
                            else
                                $this->db->or_where("$campo like","'%".$busqueda."%'",FALSE);


                        }
                    $n++;     

                    }

                }

            }
            if ($sort!="")
            {
                switch ($sort) {
                    case 'id_area':
                    case 'area':
                    case 'habilitado':
                        $sort="a.".$sort;
                        break;
                    case 'empresa':
                        $sort="e.".$sort;
                        break;
                    case 'area_padre':
                        $sort="ap.area";
                        break;
                    default:
                        $sort="a.id_area";
                        break;
                }
                $this->db->order_by($sort, $dir);
            }
            else
                $this->db->order_by("area", "asc"); 
//    /            $this->db->order_by("t.id_tarea", "asc"); 
            $this->db->limit($limit,$start); 

            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $this->cantSql('a.id_area',$this->db->last_query());
            $res = $query->result_array();
            if ($num > 0)
            {
                return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
            }
            else
                return '({"total":"0","rows":""})';
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
    public function dameCombo($query,$id_empresa)
    {
        $this->db->select('a.id_area');
        $this->db->select(" IF(a.id_area_padre!='NULL',
            concat((SELECT b.area from gr_areas_rpyc b where b.id_area=a.id_area_padre),' - ',a.area),
            a.area
            ) AS area",FALSE);
//        $this->db->select(" CASE 
//                                WHEN a.id_area_padre!='NULL' THEN concat('xxxx:',a.area) 
//                                ELSE a.area
//                            END as sector",FALSE);
        $this->db->from('gr_areas_rpyc a');
        $this->db->where('a.habilitado',1);
        $this->db->where('a.id_empresa',$id_empresa);
//        $this->db->like('a.area',$query);
        $this->db->orderby('a.id_area');
        $query = $this->db->get();
        $res = $query->result_array();
        $num=count($res);
        if ($num > 0)
            {
                    return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
            }
            else
                    return '({"total":"0","rows":""})';

    }
//    public function insert($datos)
//    {
//        $this->db->trans_begin();
//
//        $this->db->insert('gr_areas_rpyc',$datos);
//        $last=$this->db->insert_id();
//
//        $this->db->trans_complete();
//        if ($this->db->trans_status() === FALSE)
//        {
//                $this->db->trans_rollback();
//                return false;
//        }
//        else
//        {
//                $this->db->trans_commit();
//                return true;
//        }
//    }
    public function check_area($area,$area_nueva)
    {
        $this->db->select('id_area');
        $this->db->from('gr_areas_rpyc');
        $this->db->where('area',$area_nueva);
        $this->db->where('id_empresa',$area['id_empresa']);
        $query=$this->db->get();
//                echo $this->db->last_query();
        if($query->num_rows()>0)
            return false;
        else
            return true;
    }
    public function check_empresa($area,$empresa_nueva)
    {
        $this->db->select('id_area');
        $this->db->from('gr_areas_rpyc');
        $this->db->where('id_empresa',$empresa_nueva);
        $this->db->where('area',$area['area']);
        $query=$this->db->get();
//                echo $this->db->last_query();
        if($query->num_rows()>0)
            return false;
        else
            return true;
    }
    public function edit($id,$datos)
    {
        $this->db->where('id_area', $id);
        if(!$this->db->update('gr_areas_rpyc', $datos))
            return false;
        else
            return true;
    }
    public function dameArea($id)
    {
        $this->db->select('a.id_area,a.area,a.id_empresa');
        $this->db->from('gr_areas_rpyc a');
        $this->db->where("a.id_area",$id);
        $this->db->where("a.habilitado",1);
        $query = $this->db->get();
        $res = $query->result_array();
        $num=count($res);
        if ($num > 0)
        {
                return $res[0];
        }
        else
            return 0;
    }
}	

?>