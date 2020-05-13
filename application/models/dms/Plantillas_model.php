<?php
class Plantillas_model extends CI_Model
{
	public function listado($start, $limit, $filtro, $campos,$sort="", $dir="")
	{
		
            $this->db->select('p.id_plantilla,p.id_td,p.plantilla,p.detalle,p.archivo_orig,p.habilitado');
            $this->db->select('td.td');
            $this->db->from('dms_plantillas p');
            $this->db->join('dms_tipos_documento td','td.id_td = p.id_td','inner');
            
            if ($filtro!="" && count($campos)>0)
            {
                foreach ($campos as &$campo)
                {
//                    if ($campo=='empresa')
//                        $campo="e.".$campo;
//                    else
                        $campo="p.".$campo;
                }
//                echo "<pre>". print_r($campos,true)."<pre>";
                //si viene un solo campo de busqueda
                if(count($campos)==1)
                    $this->db->where("$campos[0] like","'%".$filtro."%'",FALSE);
                else
                {
                    $n=0;
                    foreach ($campos as $campo)
                    {
                        if ($n==0)
                            $this->db->where("($campo like","'%".$filtro."%'",FALSE);
                        else
                        {
                            if ($n==count($campos)-1)
                                $this->db->or_where("$campo like","'%".$filtro."%')",FALSE);
                            else
                                $this->db->or_where("$campo like","'%".$filtro."%'",FALSE);
                                
                            
                        }
                       $n++;     
                            
                    }

                }
                    
            }
            if ($sort!="")
            {
                if ($sort=='td')
                    $sort="td.".$sort;
                else
                    $sort="p.".$sort;
                $this->db->order_by($sort, $dir);
            }
            else
                $this->db->order_by("p.plantilla", "asc"); 
            $this->db->limit($limit,$start); 
                
            $query = $this->db->get();
            $num = $this->cantSql('p.id_plantilla',$this->db->last_query());
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
        
	public function edit($id,$datos)
	{
            $this->db->where('id_plantilla', $id);
            if(!$this->db->update('dms_plantillas', $datos))
                    return false;
            else
                    return true;
		
	}
	
	public function insert($datos)
	{
            
            $this->db->trans_begin();
		
		$this->db->insert('dms_plantillas',$datos);
		
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
        
        public function dameComboTD()
        {
            $this->db->select("td.id_td,td.td");
            $this->db->from("dms_tipos_documento e");
            $this->db->where("e.habilitado",1);
            $this->db->order_by("td.td", "asc");
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