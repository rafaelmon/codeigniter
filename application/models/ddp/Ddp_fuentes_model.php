<?php
class Ddp_fuentes_model extends CI_Model
{
	public function listado($start, $limit, $filtros, $busqueda, $campos,$sort="", $dir="")
    {
//             echo "<pre>". print_r($usuario,true)."<pre>";
            $this->db->select('fd.id_fd,fd.fd,fd.detalle,fd.habilitado');
            $this->db->select("(select GROUP_CONCAT(sd.abv SEPARATOR ' | ') from ddp_r_fd_dimensiones sfd 
                    inner join ddp_dimensiones sd on sd.id_dimension=sfd.id_dimension
                    where sfd.id_fd=fd.id_fd)as dimensiones",false);
            $this->db->from('ddp_fuentes fd');
            
//             if ($filtros!="")
//            {
//                if (isset($filtros[0]) && $filtros[0]!="")
//                    $this->db->where('t.id_estado',$filtros[0]);
//                if (isset($filtros[1]) && $filtros[1]!="")
//                    $this->db->where('t.id_tipo_herramienta',$filtros[1]);
//            }
            if ($busqueda!="" && count($campos)>0)
            {
                foreach ($campos as &$campo)
                {
                    switch ($campo)
                    {
                        case 'fd':
                        case 'detalle':
                            $campo="fd.".$campo;
                            break;
                        case 'dimensiones':
                            $campo="(select GROUP_CONCAT(sd.abv SEPARATOR ' | ') from ddp_r_fd_dimensiones sfd 
                    inner join ddp_dimensiones sd on sd.id_dimension=sfd.id_dimension
                    where sfd.id_fd=fd.id_fd)";
                            break;
                        default :
                            unset($campo);
                            break;
                    }
                }
                unset($campo);
    //                echo "<pre>". print_r($campos,true)."<pre>";
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

            }//fin if de Busqueda
        if ($sort!="")
        {
            switch ($sort)
            {
                case 'id_fd':
                case 'fd':
                    $sort="fd.".$sort;
                    break;
                case 'dimensiones':
                    $sort="5";
                    break;
                default:
                    $sort="fd.".$sort;
                    break;
            }
            $this->db->order_by($sort, $dir);
        }
        else
            $this->db->order_by("fd.fd", "asc"); 
        $this->db->limit($limit,$start); 

        $query = $this->db->get();
//        echo $this->db->last_query();
        $num = $this->cantSql('fd.id_fd',$this->db->last_query());
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
    public function insert($datos)
    {

        $this->db->trans_begin();

            $this->db->insert('ddp_fuentes',$datos);
            $last=$this->db->insert_id();

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE)
            {
                    $this->db->trans_rollback();
                    return false;
            }
            else
            {
                    $this->db->trans_commit();
                    return $last;
            }

    }
    public function inser_dimensiones($datos)
    {

        $this->db->trans_begin();

            $this->db->insert('ddp_r_fd_dimensiones',$datos);

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE)
            {
                    $this->db->trans_rollback();
                    return false;
            }
            else
            {
                    $this->db->trans_commit();
                    return true;
            }

    }
    public function dameComboFD($id_dimension)
    {
        $this->db->select("f.id_fd,f.fd");
        $this->db->from("ddp_fuentes f");
        $this->db->join('ddp_r_fd_dimensiones rd','rd.id_fd = f.id_fd','inner');
        $this->db->where("rd.id_dimension",$id_dimension);
        $this->db->order_by("f.fd", "asc");
        $query = $this->db->get();
        $num = $query->num_rows();
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