<?php
class Gestiones_model extends CI_Model
{
	public function dameGestionPorDocumento($id, $start, $limit,$sort="", $dir="")
	{
		
            $this->db->select('g.id_gestion,g.id_usuario,g.detalle');
            $this->db->select('tg.tg');
            $this->db->select('concat(p.nombre,", ",p.apellido) as persona',false);
            $this->db->select("DATE_FORMAT(g.f_gestion,'%d/%m/%Y') as fecha", FALSE);
            $this->db->select("r.rol");
            $this->db->from('dms_gestiones g');
            $this->db->join('dms_tipos_gestion tg','tg.id_tg = g.id_tg','inner');
            $this->db->join('sys_usuarios u','u.id_usuario = g.id_usuario','inner');
            $this->db->join('grl_personas p','p.id_persona = u.id_persona','left');
            $this->db->join('dms_roles r','r.id_rol = g.id_rol','inner');
            $this->db->where('g.id_documento',$id);//solo los que estan en estado publicado
            
            if ($sort!="")
            {
                $sort="g.".$sort;
                $this->db->order_by($sort, $dir);
            }
            else
                $this->db->order_by("g.id_gestion", "asc"); 
            $this->db->limit($limit,$start); 
                
            $query = $this->db->get();
            $num = $this->cantSql('g.id_gestion',$this->db->last_query());
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
		
		$this->db->insert('dms_gestiones',$datos);
                $insert_id=$this->db->insert_id();
//		echo $this->db->last_query();
		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			return false;
		}
		else
		{
                        
			$this->db->trans_commit();
			return $insert_id;
		}
		
	}
	public function contarRevisiones($id)
	{
		
            $this->db->select('count(id_gestion) as cant',false);
            $this->db->from('dms_gestiones g');
            $this->db->where('g.id_documento',$id);//solo los que estan en estado publicado
            $this->db->where('g.id_tg',3);//tipo de gestion Reviso
            $this->db->where('g.ciclo_wf','(select ciclos_wf from dms_documentos d where d.id_documento=g.id_documento)',false);
            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $query->num_rows();
            $res = $query->row();
//            echo "<pre>".print_r($res,true)."</pre>";
            if ($num > 0)
            {
                return $res->cant;
            }
            else
                return 0;
	}
        public function checkRevisor($usuario,$documento)
	{
		$this->db->select('g.id_gestion');
		$this->db->from('dms_gestiones g');
		$this->db->where('g.id_usuario',$usuario);
		$this->db->where('g.id_documento',$documento);
                $this->db->where('g.id_tg',3);//tipo de gestion Reviso
                $this->db->where('g.ciclo_wf','(select ciclos_wf from dms_documentos d where d.id_documento=g.id_documento)',false); //verifico en el ciclo actual
		$query=$this->db->get();
//                echo $this->db->last_query();
//                echo "<pre>".print_r($query,true)."</pre>";
                $num=$query->num_rows();
		if($num>0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
        public function dameRevisores($id_documento)
	{
		$this->db->select('group_concat(concat(p.nombre," ",p.apellido) SEPARATOR ", ") as personas',false);
		$this->db->from('dms_gestiones g');
                $this->db->join('sys_usuarios u','u.id_usuario = g.id_usuario','inner');
                $this->db->join('grl_personas p','p.id_persona = u.id_persona','inner');
		$this->db->where('g.id_documento',$id_documento);
                $this->db->where('g.id_tg',3);//tipo de gestion Reviso
                $this->db->where('g.ciclo_wf','(select ciclos_wf from dms_documentos d where d.id_documento=g.id_documento)',false); //verifico en el ciclo actual
		$query=$this->db->get();
                 $res = $query->row();
//                echo $this->db->last_query();
//                echo "<pre>".print_r($query,true)."</pre>";
                $num=$query->num_rows();
		if($num>0)
		{
			return $res->personas;
		}
		else
		{
			return false;
		}
	}
        public function dameAprobador($id_documento)
	{
		$this->db->select('concat(p.nombre," ",p.apellido) as persona',false);
		$this->db->from('dms_gestiones g');
                $this->db->join('sys_usuarios u','u.id_usuario = g.id_usuario','inner');
                $this->db->join('grl_personas p','p.id_persona = u.id_persona','inner');
		$this->db->where('g.id_documento',$id_documento);
                $this->db->where('g.id_tg',4);//tipo de gestion Aprobo
                $this->db->where('g.ciclo_wf','(select ciclos_wf from dms_documentos d where d.id_documento=g.id_documento)',false); //verifico en el ciclo actual
		$query=$this->db->get();
                 $res = $query->row();
//                echo $this->db->last_query();
//                echo "<pre>".print_r($query,true)."</pre>";
                $num=$query->num_rows();
		if($num>0)
		{
			return $res->persona;
		}
		else
		{
			return false;
		}
	}
        public function dameEditor($id_documento)
	{
		$this->db->select('concat(p.nombre," ",p.apellido) as persona',false);
		$this->db->from('dms_gestiones g');
                $this->db->join('sys_usuarios u','u.id_usuario = g.id_usuario','inner');
                $this->db->join('grl_personas p','p.id_persona = u.id_persona','inner');
		$this->db->where('g.id_documento',$id_documento);
                $this->db->where('g.id_tg',2);//tipo de gestion Aprobo
                $this->db->where('g.ciclo_wf','(select ciclos_wf from dms_documentos d where d.id_documento=g.id_documento)',false); //verifico en el ciclo actual
		$query=$this->db->get();
                 $res = $query->row();
//                echo $this->db->last_query();
//                echo "<pre>".print_r($query,true)."</pre>";
                $num=$query->num_rows();
		if($num>0)
		{
			return $res->persona;
		}
		else
		{
			return false;
		}
	}
        
}	
?>