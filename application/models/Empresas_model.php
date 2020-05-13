<?php
class Empresas_model extends CI_Model
{
    
	public function listado($start, $limit, $filtro, $campos,$sort="", $dir="")
	{
		
            $this->db->select('e.id_empresa,e.empresa,e.abv,e.logo,e.habilitado');
            $this->db->from('grl_empresas e');
            
            if ($filtro!="" && count($campos)>0)
            {
                //si viene un solo campo para busqueda
                if(count($campos)==1)
                    $this->db->where("p.$campos[0] like","'%".$filtro."%'",FALSE);
                else
                {
                    $n=0;
                    foreach ($campos as $campo)
                    {
                        if ($n==0)
                            $this->db->where("(p.$campo like","'%".$filtro."%'",FALSE);
                        else
                        {
                            if ($n==count($campos)-1)
                                $this->db->or_where("p.$campo like","'%".$filtro."%')",FALSE);
                            else
                                $this->db->or_where("p.$campo like","'%".$filtro."%'",FALSE);
                                
                            
                        }
                       $n++;     
                    }
                    unset($campo);

                }
                    
            }
            if ($sort!="")
                $this->db->order_by("e.$sort", $dir);
            else
                $this->db->order_by("e.empresa", "asc"); 
            $this->db->limit($limit,$start); 
            $this->db->limit($limit,$start); 
            $query = $this->db->get();
            $num = $this->cantSql('e.id_empresa',$this->db->last_query());
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
            $this->db->where('id_empresa', $id);
            if(!$this->db->update('grl_empresas', $datos))
                    return false;
            else
                    return true;
		
	}
	
	public function insert($datos)
	{
            
            $this->db->trans_begin();
		
		$this->db->insert('grl_empresas',$datos);
		
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
        public function dameComboEmpresas()
	{
		
            $this->db->select('e.id_empresa,e.empresa');
            $this->db->from('grl_empresas e');
            $this->db->where("e.habilitado",1);
            $this->db->order_by("e.empresa", "asc"); 
            $this->db->limit(30,0);                 
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
        public function empresasCombo_sbs($limit,$start,$query)
	{
		
            $this->db->select('e.id_empresa,e.empresa');
            $this->db->from('grl_empresas e');
            $this->db->where("e.habilitado",1);
            $this->db->like("e.empresa",$query);
            $this->db->order_by("e.empresa", "asc"); 
            $this->db->limit($limit,$start); 
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
        public function damelogo($id_empresa)
        {
            $this->db->select('e.logo');
            $this->db->from('grl_empresas e');
            $this->db->where("e.id_empresa",$id_empresa);
            $query = $this->db->get();
//            echo $this->db->last_query();
            $res = $query->row();
            $num = $query->num_rows();
            if ($num > 0)
            {
                return $res->logo;
            }
            else
                return 0;
            
        }
        
        public function dameComboParaSectores()
        {
            $this->db->select('e.id_empresa,e.empresa');
            $this->db->from('grl_empresas e');
            $this->db->where("e.habilitado",1);
            $this->db->where("e.id_empresa !=",1);
            $this->db->order_by("e.empresa", "asc"); 
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
        public function dameComboParaOperariosDDP()
        {
            $this->db->select('e.id_empresa,e.empresa');
            $this->db->from('grl_empresas e');
            $this->db->where("e.habilitado",1);
            $this->db->where("e.id_empresa !=",1);
            $this->db->order_by("e.empresa", "asc"); 
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
	
	
}	
?>