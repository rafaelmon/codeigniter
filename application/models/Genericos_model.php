<?php
class Genericos_model extends CI_Model
{
	public function habilitar($tabla,$campo,$valor,$campo_id,$id)
	{
		$datos[$campo]=$valor;
		$this->db->where($campo_id, $id);
		if(!$this->db->update($tabla, $datos))
			return false;
		else
			return true;
	}

	public function borrar($tabla,$campo_id,$ids)
	{
		foreach($ids as $k => $v)
		{
			$this->db->where($campo_id, $v);
			$this->db->delete($tabla);	
		}
		//if(!$this->db->delete($tabla))
			//return false;
		//else
			return true;
	}
	public function checkPermisos($usuario_id,$modulo_id,$operacion)
	{
		$this->select($operacion);
		$this->from("sys_permisos"); 
		$this->where("usuario_id",$usuario_id);
		$this->where("modulo_id",$modulo_id);
		$query = $this->get();
		$res = $query->result();
		if ($res[0]->$operacion == 1)
			return true;
		else
			return false;
	}
	
	public function damePaises()
	{
		$this->db->select("id_pais, pais");
		$this->db->from("cod_paises"); 
		$this->db->order_by("pais"); 
		$query = $this->db->get();
		$res = $query->result_array();
		if ($cant=count($res) > 0)
		{
			return '({"total":"'.$cant.'","rows":'.json_encode($res).'})';
		}
		else
			return '({"total":"0","rows":""})';
	}
	
	public function dameProvincias($pais_id)
	{
		$this->db->select("id_provincia, provincia");
		$this->db->from("cod_provincias"); 
		$this->db->where("id_pais",$pais_id); 
		$this->db->order_by("provincia"); 
		$query = $this->db->get();
		$res = $query->result_array();
		if ($cant=count($res) > 0)
		{
			return '({"total":"'.$cant.'","rows":'.json_encode($res).'})';
		}
		else
			return '({"total":"0","rows":""})';
	}
	public function ultimoId($campo_id,$tabla)
	{
		$this->db->select_max($campo_id);
		$this->db->from($tabla); 
		$query = $this->db->get();
//                echo $this->db->last_query();
		$res = $query->row_array();
//                echo "<pre>".print_r($res,true)."</pre>";
		if ($cant=count($res) > 0)
		{
			return $res[$campo_id];
		}
		else
			return false;
	}
        public function dameTiposDocumento()
	{
		$this->db->select("id_td,abv as td");
//                $this->db->select('concat(td," (",abv,")") as td',false);
		$this->db->from("grl_tipos_documentos"); 
		$this->db->order_by("td"); 
		$query = $this->db->get();
		$res = $query->result_array();
                $cant=count($res);
		if ($cant > 0)
		{
			return '({"total":"'.$cant.'","rows":'.json_encode($res).'})';
		}
		else
			return '({"total":"0","rows":""})';
	}
	
	
}
?>