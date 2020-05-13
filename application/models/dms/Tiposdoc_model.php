<?php
class Tiposdoc_model extends CI_Model
{
    
	public function listado($start, $limit, $filtro, $campos,$sort="", $dir="")
	{
		
            $this->db->select('td.id_td,td.td,td.detalle,td.abv,td.habilitado');
            $this->db->from('dms_tipos_documento td');
            
            if ($filtro!="" && count($campos)>0)
            {
                foreach ($campos as &$campo)
                {
                    $campo="td.".$campo;
                }
                unset($campo);
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
                $sort="td.".$sort;
                $this->db->order_by($sort, $dir);
            }
            else
                $this->db->order_by("td.td", "asc"); 
            
            
            $this->db->limit($limit,$start); 
            $query = $this->db->get();
            $num = $this->cantSql('td.id_td',$this->db->last_query());
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
            $this->db->where('id_td', $id);
            if(!$this->db->update('dms_tipos_documento', $datos))
                    return false;
            else
                    return true;
		
	}
	
	public function insert($datos)
	{
            
            $this->db->trans_begin();
		
		$this->db->insert('dms_tipos_documento',$datos);
		
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
            $this->db->from("dms_tipos_documento td");
            $this->db->where("td.habilitado",1);
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
        
        public function check_td($datos)
	{
            $this->db->select('td');
            $this->db->from('dms_tipos_documento');
            $this->db->where("td",$datos['td']); 
            $query = $this->db->get();
//            echo $this->db->last_query();
            $res = $query->result();
            $num=count($res);
//            echo "num:".$num;
            if ($num > 0)
                return true; //Ya existe el registro en la bd
            else
                return false; //No existe el registro en la bd
		
	}
        public function dameTdAbv($id_td)
	{
            $this->db->select('abv');
            $this->db->from('dms_tipos_documento');
            $this->db->where("id_td",$id_td); 
            $query = $this->db->get();
//            echo $this->db->last_quer|y();
            $res = $query->row();
            $num=count($res);
//            echo "num:".$num;
            if ($num > 0)
                return $res->abv; 
            else
                return false; 
		
	}
	
	
}	
?>