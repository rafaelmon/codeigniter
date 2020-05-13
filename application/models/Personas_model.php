<?php
class Personas_model extends CI_Model
{
    public $camposBusqueda=array ("nombre","apellido","documento");
    
	public function listado($start, $limit, $filtro, $campos,$sort="", $dir="")
	{
		
            $this->db->select('p.id_persona,p.nombre,p.apellido,p.habilitado,p.documento,p.genero');
            $this->db->select('td.abv as td');
//            $this->db->select('u.usuario');
            $this->db->from('grl_personas p');
            $this->db->join('grl_tipos_documentos td','td.id_td = p.id_td','inner');
//            $this->db->join('sys_usuarios u','p.id_persona = u.id_persona','left');
            
            if ($filtro!="" && count($campos)>0)
            {
                foreach ($campos as &$campo)
                {
                    switch ($campo)
                    {
//                        case 'usuario':
//                            $campo="u.".$campo;
//                            break;
                        case 'td':
                            $campo="td.".$campo;
                            break;
                        default :
                            $campo="p.".$campo;
                            break;
                    }
                }
                unset($campo);
                //si viene un solo campo para busqueda
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
                            
//                        $this->db->where("(p.$campo like","'%".$filtro."%'",FALSE); 
                    }
                    unset($campo);

//                    $this->db->or_where("p.apellido like","'%".$filtro."%'",FALSE);
//                    $this->db->or_where("p.documento like","'%".$filtro."%'",FALSE);
                }
                if (in_array('nombre', $campos) && in_array('apellido', $campos))
                    $this->db->or_where("concat(p.nombre,' ',p.apellido) like","'%".$filtro."%'",FALSE); 
                    
            }
            if ($sort!="")
            {
                if($sort=='usuario')
                $this->db->order_by("u.$sort", $dir);
                else
                $this->db->order_by("p.$sort", $dir);
            }
             else
                $this->db->order_by("p.apellido", "asc"); 
            $this->db->limit($limit,$start); 
                
            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $this->cantSql('p.id_persona',$this->db->last_query());
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
        
	public function check_documento($documento)
	{
            $this->db->select('p.documento');
            $this->db->from('grl_personas p');
            $this->db->where("p.documento",$documento); 
            $query = $this->db->get();
            $res = $query->result();
            $num=count($res);
//            echo $num;
            if ($num > 0)
                return true; //Ya existe el nro de documento en la bd
            else
                return false; //No existe el nro de documento en la bd
		
	}
	public function checkPersonaPorId($id)
	{
            $this->db->select('p.id_persona');
            $this->db->from('grl_personas p');
            $this->db->where("p.id_persona",$id); 
            $this->db->where("p.habilitado",1); 
            $query = $this->db->get();
            $res = $query->result();
            $num=count($res);
//            echo $num;
            if ($num > 0)
                return true; //Persona ok
            else
                return false; //No existe la persona o esta deshabilitada
		
	}
	public function edit($id,$datos)
	{
            $this->db->where('id_persona', $id);
            $edit=$this->db->update('grl_personas', $datos);
//            echo $this->db->last_query();
            if(!$edit)
                    return false;
            else
                    return true;
		
	}
	
	public function insert($datos)
	{
            
            $this->db->trans_begin();
		
		$this->db->insert('grl_personas',$datos);
		
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
        public function damePersonasComboTpl($limit,$start,$filtro)
	{
		
            $this->db->select('p.id_persona,p.nombre,p.apellido');
            $this->db->select('concat(td.abv," ",p.documento) as documento',false);
            $this->db->select('concat(p.nombre," ",p.apellido) as nomape',false);
            $this->db->from('grl_personas p');
            $this->db->join('grl_tipos_documentos td','td.id_td = p.id_td','inner');
            $this->db->where("(p.nombre like","'%".$filtro."%'",FALSE);
            $this->db->or_where("p.apellido like","'%".$filtro."%'",FALSE);
            $this->db->or_where("concat(p.nombre,' ',p.apellido) like","'%".$filtro."%')",FALSE);
            $this->db->where("p.id_persona not in(select  DISTINCT(id_persona) from sys_usuarios)",NULL,FALSE);
            $this->db->where("p.habilitado", 1);
            $this->db->order_by("p.nombre", "asc"); 
            $this->db->order_by("p.apellido", "asc"); 
            
            $this->db->limit($limit,$start); 
                
            $query = $this->db->get();
//            echo $this->db->last_query();die();
            $num = $this->cantSql('p.id_persona',$this->db->last_query());
            $res = $query->result_array();
            if ($num > 0)
            {
                return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
            }
            else
                return '({"total":"0","rows":""})';
	}
        public function dameCombo($limit,$start,$query,$id_usuario_not)
        {
            $this->db->select('p.id_persona');
//            $this->db->select('p.documento as dni');
//            $this->db->select('concat(p.nombre," ",p.apellido, " (",p.documento,")") as persona',false);
            $this->db->select('concat(p.nombre," ",p.apellido) as persona',false);
            if ($query!="")
                $this->db->where('concat(p.nombre," ",p.apellido, " ",p.documento) like',"'%".$query."%'",FALSE);
//            $this->db->where("p.id_persona not in(select  DISTINCT(id_persona) from sys_usuarios)",NULL,FALSE);
            $this->db->order_by("p.nombre", "asc"); 
            $this->db->order_by("p.apellido", "asc"); 
            $this->db->from('grl_personas p');
            $this->db->limit($limit,$start); 
            $query = $this->db->get();
//            echo $this->db->last_query();die();
            $num = $this->cantSql('p.id_persona',$this->db->last_query());
            $res = $query->result_array();
            if ($num > 0)
            {
                return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
            }
            else
                return '({"total":"0","rows":""})';
            
        }
        public function dameComboParaFormCerrar($id_tarea,$limit,$start,$query,$id_usuario_not="")
        {
            $this->db->select('p.id_persona');
//            $this->db->select('p.documento as dni');
//            $this->db->select('concat(p.nombre," ",p.apellido, " (",p.documento,")") as persona',false);
            $this->db->select('concat(p.nombre," ",p.apellido) as persona',false);
            $this->db->order_by("p.nombre", "asc"); 
            $this->db->order_by("p.apellido", "asc"); 
            $this->db->from('grl_personas p');
            if ($query!="")
                $this->db->where('concat(p.nombre," ",p.apellido, " ",p.documento) like',"'%".$query."%'",FALSE);
            $this->db->where("p.id_persona not in(select  DISTINCT(id_persona) from cap_participantes where id_tarea=$id_tarea)",NULL,FALSE);
            $this->db->where("p.habilitado", 1);
            $this->db->limit($limit,$start); 
            $query = $this->db->get();
//            echo $this->db->last_query();die();
            $num = $this->cantSql('p.id_persona',$this->db->last_query());
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