<?php
class Rep_reportes_model extends CI_Model
{
     public function listado($start, $limit, $busqueda, $campos,$sort="", $dir="")
    {
            $this->db->select('r.id_reporte, r.reporte,r.detalle,r.habilitado,r.nom_archivo');
            $this->db->select("DATE_FORMAT(r.fecha_alta,'%d/%m/%Y') as fecha_alta", FALSE);
            $this->db->from('rep_reportes r');

            if ($busqueda!="" && count($campos)>0)
            {
                foreach ($campos as &$campo)
                {
                    switch ($campo)
                    {
                        case 'area':
                            $campo="r.area";
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
                    $this->db->where("$campos[0] like","'%".$busquedr."%'",FALSE);
                else
                {
                    $n=0;
                    foreach ($campos as $campo)
                    {
                        if ($n==0)
                            $this->db->where("($campo like","'%".$busquedr."%'",FALSE);
                        else
                        {
                            if ($n==count($campos)-1)
                                $this->db->or_where("$campo like","'%".$busquedr."%')",FALSE);
                            else
                                $this->db->or_where("$campo like","'%".$busquedr."%'",FALSE);


                        }
                    $n++;     

                    }

                }

            }
            if ($sort!="")
            {
                switch ($sort) {
                    case 'id_reporte':
                    case 'reporte':
                    case 'detalle':
                        $sort="r.".$sort;
                        break;
                    default:
                        $sort="r.id_reporte";
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
            $num = $this->cantSql('r.id_reporte',$this->db->last_query());
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
    
}	

?>