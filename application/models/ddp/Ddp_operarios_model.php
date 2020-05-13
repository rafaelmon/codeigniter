<?php
class Ddp_operarios_model extends CI_Model
{
	public function listado($start, $limit, $filtros, $busqueda, $campos,$sort="", $dir="",$id_empresa)
    {
//             echo "<pre>". print_r($usuario,true)."<pre>";
            $this->db->select('o.id_operario,o.nombre,o.apellido,o.habilitado,o.legajo');
//            $this->db->select("concat(o.nombre,' ',o.apellido) as operario",false);
            $this->db->select("e.abv as empresa",false);
            $this->db->select("concat(p.nombre,' ',p.apellido) as supervisor",false);
//            $this->db->select("(select GROUP_CONCAT(concat(sp.nombre,' ',sp.apellido) SEPARATOR ' | ') from ddp_r_operarios_supervisores sos 
//                    inner join gr_usuarios sgru on sgru.id_usuario=sos.id_usuario_supervisor
//                    inner join sys_usuarios su on su.id_usuario=sgru.id_usuario
//                    inner join grl_personas sp on sp.id_persona = su.id_persona
//                    where sos.id_operario=o.id_operario)as supervisores",false);
            $this->db->from('ddp_operarios o');
            $this->db->join('grl_empresas e','e.id_empresa = o.id_empresa','inner');
            $this->db->join('gr_usuarios gru','gru.id_usuario=o.id_usuario_supervisor','inner');
            $this->db->join('sys_usuarios u','u.id_usuario = gru.id_usuario','inner');
            $this->db->join('grl_personas p','p.id_persona = u.id_persona','inner');
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
                        case 'operario':
                        case 'detalle':
                            $campo="o.".$campo;
                            break;
                        case 'supervisores':
//                            $campo="(select GROUP_CONCAT(concat(p.nombre,' ',p.apellido) SEPARATOR ' | ') from ddp_r_operario_dimensiones soperario 
//                    inner join ddp_dimensiones sd on sd.id_dimension=so.id_dimension
//                    where so.id_operario=o.id_operario)";
                            $campo="concat(p.nombre,' ',p.apellido)";
                            break;
                        case 'empresa':
                            $campo="e.abv";
                            break;
                        case 'legajo':
                            $campo="o.legajo";
                            break;
                        default :
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
             switch ($id_empresa)
            {
                case 1:
                    break;
                case 2:
                    $this->db->where("e.id_empresa",2);
                    break;
                case 3:
                    $this->db->where("e.id_empresa",3);
                    break;
            }
        if ($sort!="")
        {
            switch ($sort)
            {
                case 'id_operario':
                    $sort="o.id_operario";
                    break;
                case 'operario':
                    $sort="concat(o.nombre,' ',o.apellido)";
                    break;
                case 'empresa':
                    $sort="e.abv";
                    break;
                case 'legajo':
                    $sort="o.legajo";
                    break;
                case 'supervisor':
                    $sort="concat(p.nombre,' ',p.apellido)";
                    break;
                default:
                    $sort="o.id_operario";
                    break;
            }
            $this->db->order_by($sort, $dir);
        }
        else
            $this->db->order_by("5", "asc"); 
        $this->db->limit($limit,$start); 

        $query = $this->db->get();
//        echo $this->db->last_query();
        $num = $this->cantSql('o.id_operario',$this->db->last_query());
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

            $this->db->insert('ddp_operarios',$datos);
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
//    public function inser_supervisor($datos)
//    {
//
//        $this->db->trans_begin();
//
//            $this->db->insert('ddp_r_operarios_supervisores',$datos);
//
//            $this->db->trans_complete();
//            if ($this->db->trans_status() === FALSE)
//            {
//                    $this->db->trans_rollback();
//                    return false;
//            }
//            else
//            {
//                    $this->db->trans_commit();
//                    return true;
//            }
//
//    }
    public function supervisoresCombo($id_empresa,$limit,$start,$query)
	{
		
            $this->db->select('gru.id_usuario');
            $this->db->select('u.usuario');
            $this->db->select('concat(pp.nombre," ",pp.apellido) as nomape',false);
            $this->db->select('concat(e.abv," - ",pto.puesto) as puesto',false);
            $this->db->from('gr_usuarios gru');
            $this->db->join('sys_usuarios u','u.id_usuario = gru.id_usuario','inner');
            $this->db->join('grl_personas pp','pp.id_persona = u.id_persona','inner');
            $this->db->join('gr_puestos pto','pto.id_puesto = gru.id_puesto','inner');
            $this->db->join('gr_organigramas o','o.id_organigrama = pto.id_organigrama','inner');
            $this->db->join('grl_empresas e','e.id_empresa = o.id_empresa','inner');
            $this->db->where("o.id_empresa",$id_empresa);
            $this->db->where("gru.habilitado",1);
            $this->db->where("gru.supervisor",1);
            $this->db->where("pp.habilitado",1);
            $this->db->where("u.habilitado",1);
            $this->db->where("(pp.nombre like","'%".$query."%'",FALSE);
            $this->db->or_where("pp.apellido like","'%".$query."%'",FALSE);
            $this->db->or_where("concat(pp.nombre,' ',pp.apellido) like","'%".$query."%')",FALSE);
            
            
            $this->db->order_by("pp.nombre", "asc"); 
            $this->db->order_by("pp.apellido", "asc"); 
            
            $this->db->limit($limit,$start); 
                
            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $this->cantSql('gru.id_usuario',$this->db->last_query());
            $res = $query->result_array();
            if ($num > 0)
            {
                return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
            }
            else
                return '({"total":"0","rows":""})';
	}
        public function chekLegajo($id_emrpesa,$legajo)
        {
            $this->db->select('o.id_operario');
             $this->db->from('ddp_operarios o');
            $this->db->where("o.legajo",$legajo);
            $this->db->where("o.id_empresa",$id_emrpesa);
            $query = $this->db->get();
		if($query->num_rows()!=0)
		{
			return false;
		}
		else
		{
			return true;
		}
        }
}
?>