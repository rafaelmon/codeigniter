<?php
class Mailing_model extends CI_Model
{
	
	
	public function insert($datos)
	{
		$this->db->set("id_origen",(array_key_exists('id_origen',$datos))?$datos['id_origen']:NULL);
		$this->db->set("origen",(array_key_exists('origen',$datos))?$datos['origen']:'');
		$this->db->set("para",  is_array($datos['para'])?implode(", ",$datos['para']):$datos['para']);
		$this->db->set("cc",is_array($datos['cc'])?implode(", ",$datos['cc']):$datos['cc']);
		$this->db->set("cco",  is_array($datos['cco'])?implode(", ",$datos['cco']):$datos['cco']);
		$this->db->set("asunto",(array_key_exists('asunto',$datos))?$datos['asunto']:'');
		$this->db->set("adjunto",(array_key_exists('adjunto',$datos))?$datos['adjunto']:'');
		$this->db->set("texto",(array_key_exists('texto',$datos))?$datos['texto']:'');
		$this->db->set("echo",(array_key_exists('echo',$datos))?$datos['echo']:'');
		$this->db->set("reintentos",0);
		if ($this->db->insert("sys_mailing"))
			return true;
		else
			return false;
	}
	
	
}
?>