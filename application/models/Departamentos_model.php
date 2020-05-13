<?php
class Departamentos_model extends CI_Model
{
    public $camposBusqueda=array ("nombre","apellido","documento");
    
	public function listado($start, $limit, $filtro, $campos,$sort="", $dir="")
	{
		
            $this->db->select('p.id_departamento,p.id_gerencia,p.departamento,p.abv,p.habilitado');
            $this->db->select('e.empresa');
            $this->db->select('g.gerencia');
            $this->db->from('grl_departamentos p');
            $this->db->join('grl_gerencias g','g.id_gerencia = p.id_gerencia','inner');
            $this->db->join('grl_empresas e','e.id_empresa = g.id_empresa','inner');
            
            if ($filtro!="" && count($campos)>0)
            {
                foreach ($campos as &$campo)
                {
                    if ($campo=='empresa')
                        $campo="e.".$campo;
                    elseif ($campo=='gerencia')
                        $campo="g.".$campo;
                    else
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
                if ($sort=='empresa')
                    $sort="e.".$sort;
                elseif ($sort=='gerencia')
                    $sort="g.".$sort;
                else
                    $sort="p.".$sort;
                $this->db->order_by($sort, $dir);
            }
            else
                $this->db->order_by("p.departamento", "asc"); 
            $this->db->limit($limit,$start); 
                
            $query = $this->db->get();
            $num = $this->cantSql('p.id_departamento',$this->db->last_query());
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
        
	public function check_departamentoGerencia($datos)
	{
            $this->db->select('departamento');
            $this->db->from('grl_departamentos');
            $this->db->where("departamento",$datos['departamento']); 
            $this->db->where("id_gerencia",$datos['id_gerencia']); 
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
	public function edit($id,$datos)
	{
            $this->db->where('id_departamento', $id);
            if(!$this->db->update('grl_departamentos', $datos))
                    return false;
            else
                    return true;
		
	}
	
	public function insert($datos)
	{
            
            $this->db->trans_begin();
		
		$this->db->insert('grl_departamentos',$datos);
		
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
            $this->db->select("e.id_empresa,e.empresa");
            $this->db->from("grl_empresas e");
            $this->db->where("e.habilitado",1);
            $this->db->order_by("e.empresa", "asc");
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
        public function dameComboGerencias($id_empresa)
        {
            $this->db->select("g.id_gerencia,g.gerencia");
		$this->db->from("grl_gerencias g");
                $this->db->where("g.id_empresa",$id_empresa);
                $this->db->where("g.habilitado",1);
		$this->db->order_by("g.gerencia", "asc");
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
	public function dameComboDepartamentos($id_gerencia)
        {
            $this->db->select("d.id_departamento,d.departamento");
            $this->db->from("grl_departamentos d");
            $this->db->where("d.habilitado",1);
            $this->db->where("d.id_gerencia",$id_gerencia);
            $this->db->order_by("d.departamento", "asc");
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