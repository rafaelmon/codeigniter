<?php
class Ddp_objetivos_historial1_model extends CI_Model
{
	public function listado($id_objetivo)
    {
            $limit=30;
            $start=0;
//             echo "<pre>". print_r($usuario,true)."<pre>";
            $this->db->select('h.obj,h.indicador,h.fd,h.valor_ref,h.peso,h.real1,h.real2,h.primero');
//            $this->db->select('et.estado as estado_top',false);
            $this->db->select('h.id_editor');
            $this->db->select("concat(pp.nombre,' ',pp.apellido) as usuario",false);
            $this->db->select("DATE_FORMAT(h.fecha_edicion,'%d/%m/%y - %H:%i') as fecha", FALSE);
            $this->db->select("DATE_FORMAT(h.fecha_evaluacion,'%d/%m/%Y') as fecha_evaluacion", FALSE);
//            $this->db->select('d.abv as dimension,d.css_bc');
            $this->db->from('ddp_objetivos_historial1 h');
            $this->db->where("h.id_objetivo",$id_objetivo);
//             $this->db->join('ddp_dimensiones d','d.id_dimension = h.id_dimension','inner');
              $this->db->join('sys_usuarios u','u.id_usuario = h.id_editor','inner');
             $this->db->join('grl_personas pp','pp.id_persona = u.id_persona','inner');
//             $this->db->join('ddp_estados_top et','et.id_estado = h.id_estado_top','inner');
            $this->db->order_by("h.id", "desc"); 
        $this->db->limit($limit,$start); 

        $query = $this->db->get();
//        echo $this->db->last_query();
        $num = $this->cantSql('h.id',$this->db->last_query());
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

            $this->db->insert('ddp_objetivos_historial1',$datos);
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
                    return 1;
            }

    }
}
?>