<?php
class Vto_vencimientos_model extends CI_Model
{
    public function listado($start, $limit, $sort,$dir,$busqueda=0,$campos=0)
    {
        $this->db->select('vv.id_vencimiento,vv.vencimiento, vv.descripcion, vv.id_estado, vv.id_usuario_alta, vv.id_usuario_responsable,vv.fecha_alta,date_add(`fecha_vencimiento`, INTERVAL - dias_avisos DAY) as fecha_aviso,vv.q_avisos,archivo');
        $this->db->select("concat(gp.nombre,' ',gp.apellido) as usuario_alta",false);
        $this->db->select("concat(gp1.nombre,' ',gp1.apellido) as usuario_responsable",false);
        $this->db->select("DATE_FORMAT(vv.fecha_alta,'%d/%m/%Y') as fecha_alta", FALSE);
        $this->db->select("DATE_FORMAT(vv.fecha_vencimiento,'%d/%m/%Y') as fecha_vto", FALSE);
        $this->db->select('vt.estado');
        $this->db->select(" CASE 
                                WHEN vv.rpd = 1 THEN 'Si'
                                WHEN vv.rpd = 2 THEN 'No'
                                ELSE '-.-'
                            END as rpd",FALSE);
        $this->db->from('vto_vencimientos vv');
        $this->db->join('sys_usuarios su','su.id_usuario = vv.id_usuario_alta');
        $this->db->join('sys_usuarios su1','su1.id_usuario = vv.id_usuario_responsable');
        $this->db->join('grl_personas gp','gp.id_persona = su.id_persona');
        $this->db->join('grl_personas gp1','gp1.id_persona = su1.id_persona');
        $this->db->join('vto_estados vt','vt.id_estado = vv.id_estado');
        $this->db->where('vv.id_estado != 5');
        
         if ($busqueda!="" && count($campos)>0)
        {
            foreach ($campos as &$campo)
            {
                switch ($campo)
                {
                    case 'id_vencimiento':
                        $campo="vv.".$campo;
                        break;
                    case 'vencimiento':
                        $campo="vv.".$campo;
                        break;
                    case 'descripcion':
                        $campo="vv.".$campo;
                        break;
                    case 'usuario_alta':
                        $campo="concat(gp.nombre,' ',gp.apellido)";
                        break;
                    case 'usuario_responsable':
                        $campo="concat(gp1.nombre,' ',gp1.apellido)";
                        break;
                }
            }
            unset($campo);
            
            if(count($campos)==1)
            {
                $this->db->where($campos[0]. " like","'%".$busqueda."%'",FALSE);
            }
            else
            {
                $n=0;
                foreach ($campos as $campo)
                {
                    if ($n==0)
                        {
                        $this->db->where("(".$campo." like","'%".$busqueda."%'",FALSE);
                        }
                    else
                    {
                        if ($n==count($campos)-1)
                        {
                            $this->db->or_where($campo ." like","'%".$busqueda."%')",FALSE);
                        }
                        else
                        {
                            $this->db->or_where($campo." like","'%".$busqueda."%'",FALSE);
                        }
                    }
                   $n++;     
                }
            }
        }
        
        if ($sort!="")
        {
             switch ($sort)
            {
                case 'id_vencimiento':
                    $ordenar="vv.id_vencimiento";
                    break;
                default:
                    $ordenar="vv.fecha_vencimiento";
                    break;

            }
            $this->db->order_by('id_estado', $dir);
            $this->db->order_by($ordenar, $dir);
        }
        else
        {
            $this->db->order_by('vv.id_vencimiento','asc');
        }
        $this->db->limit($limit,$start);
        $query = $this->db->get();
//        echo $this->db->last_query();
        $last_query=$this->db->last_query();
        $pasQuery=$this->sqlTxt('vv.id_vencimiento',$last_query);
        $num = $this->cantSql($pasQuery);
        $res = $query->result_array();
        if ($num > 0)
        {
            return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
        }
        else
            return '({"total":"0","rows":""})';
    }
    
    function sqlTxt($count,$sql)
    {
//        echo $sql;
        $exploud=  explode('FROM', $sql);
        $exploud=  explode('ORDER BY', $exploud[1]);
        $newSql=  "SELECT count($count) as cantidad FROM ".$exploud[0];
//        echo $newSql;
        return $newSql;
    }
    function cantSql($sql)
    {
        $query =$this->db->query($sql);
        $res = $query->result();
        return $res[0]->cantidad;
    }
    
    public function insert($datos)
    {
        $this->db->trans_begin();

        $this->db->insert('vto_vencimientos',$datos);
//        echo $this->db->last_query();
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
                return $last;
        }
    }
    
    public function update($id_vencimiento, $datos)
    {
            $this->db->where("id_vencimiento",$id_vencimiento);
            $update=$this->db->update("vto_vencimientos",$datos);
//            echo $this->db->last_query();
            if ($update)
                    return true;
            else
                    return false;
    }
    public function dameVencimientoParaRenovacion($id_vencimiento)
    {
        $this->db->select('vv.vencimiento,vv.descripcion,vv.id_usuario_responsable,vv.dias_avisos,vv.q_avisos');
        $this->db->from('vto_vencimientos vv');
        $this->db->where('vv.id_vencimiento',$id_vencimiento);
        $query = $this->db->get();
//         echo $this->db->last_query();
        $res = $query->row_array();
        return $res;
    }
    
    public function dameArchivo($id_vencimiento)
    {
        $this->db->select("v.archivo");
        $this->db->from("vto_vencimientos v");
        $this->db->where("v.id_vencimiento",$id_vencimiento);
        $query = $this->db->get();
//        echo $this->db->last_query();
        $res = $query->row();
        $num = $query->num_rows();
        if ($num > 0)
        {
            return $res->archivo;
        }
        else
            return "";

    }
    
    public function dameMesAnioFechaAlta($id_vencimiento)
    {
        $this->db->select("YEAR(fecha_alta) as anio",false);
        $this->db->select("MONTH(fecha_alta) as mes",false);
        $this->db->from("vto_vencimientos v");
        $this->db->where("v.id_vencimiento",$id_vencimiento);
        $query = $this->db->get();
//        echo $this->db->last_query();
        $res = $query->row_array();
        $num = $query->num_rows();
        if ($num > 0)
        {
            return $res;
        }
        else
            return "";

    }
    public function dameVtoDatosMail($id_vencimiento)
    {
        $this->db->select('v.vencimiento,v.descripcion,v.id_usuario_responsable,v.dias_avisos,v.q_avisos');
        $this->db->select("concat(p.nombre,' ',p.apellido,' - ',pto.puesto,' (',e1.abv,')') as usuario_alta",false);
        $this->db->select('u.email as email_alta,u.mailing as mailing_alta');
        $this->db->select("concat(p2.nombre,' ',p2.apellido,' - ',pto2.puesto,' (',e2.abv,')') as usuario_responsable",false);
        $this->db->select('u2.email as email_responsable,u2.mailing as mailing_responsable');
        $this->db->select("DATE_FORMAT(v.fecha_alta,'%d/%m/%Y') as fecha_alta", FALSE);
        $this->db->select("DATE_FORMAT(v.fecha_vencimiento,'%d/%m/%Y') as fecha_vto", FALSE);
        $this->db->from('vto_vencimientos v');
        $this->db->join('gr_usuarios gu','gu.id_usuario = v.id_usuario_alta','inner');
        $this->db->join('gr_usuarios gu2','gu2.id_usuario = v.id_usuario_responsable','inner');
        $this->db->join('sys_usuarios u','u.id_usuario = gu.id_usuario','inner');
        $this->db->join('sys_usuarios u2','u2.id_usuario = gu2.id_usuario','left');
        $this->db->join('grl_personas p','p.id_persona = u.id_persona','left');
        $this->db->join('grl_personas p2','p2.id_persona = u2.id_persona','left'); 
        $this->db->join('gr_puestos pto','pto.id_puesto = gu.id_puesto','inner');
        $this->db->join('gr_puestos pto2','pto2.id_puesto = gu2.id_puesto','inner');
        $this->db->join('gr_organigramas or1','or1.id_organigrama = pto.id_organigrama','inner');
        $this->db->join('gr_organigramas or2','or2.id_organigrama = pto2.id_organigrama','inner');
        $this->db->join('grl_empresas e1','e1.id_empresa = or1.id_empresa','inner');
        $this->db->join('grl_empresas e2','e2.id_empresa = or2.id_empresa','inner');
        $this->db->where('v.id_vencimiento',$id_vencimiento);
        $query = $this->db->get();
//         echo $this->db->last_query();
        $res = $query->row_array();
        return $res;
    }
}	
?>