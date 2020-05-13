<?php
class Cpp_investigadores_model extends CI_Model
{
    public function listado($id_evento,$start, $limit, $sort="",$dir,$busqueda="",$campos=array())
    {
        $this->db->select('i.id_investigador,');
        $this->db->select("concat(p.nombre,' ',p.apellido) as usuario",false);
        $this->db->select("DATE_FORMAT(i.fecha_alta,'%d/%m/%Y') as fecha_alta", FALSE);
        $this->db->select('a.area');
        $this->db->select('pto.puesto');
        $this->db->join('sys_usuarios u','u.id_usuario = i.id_usuario','inner');
        $this->db->join('grl_personas p','p.id_persona = u.id_persona','inner');
        $this->db->join('gr_usuarios gu','gu.id_usuario = u.id_usuario','inner');
        $this->db->join('gr_puestos pto','pto.id_puesto = gu.id_puesto','inner');
        $this->db->join('gr_areas a','a.id_area = pto.id_area','inner');
        $this->db->from('cpp_investigadores i');
        $this->db->where('i.id_evento',$id_evento);

        if ($busqueda!="" && count($campos)>0)
        {
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
        $this->db->limit($limit,$start);
        $query = $this->db->get();
//        echo $this->db->last_query();
        $last_query=$this->db->last_query();
        $pasQuery=$this->sqlTxt('id_investigador',$last_query);
        $num = $this->cantSql($pasQuery);
        $res = $query->result_array();

        if ($num > 0)
        {
            return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
        }
        else
        {
            return '({"total":"0","rows":""})';
        }
    }

    public function insert($datos)
    {
        $insert=$this->db->insert("cpp_investigadores",$datos);
        if ($insert)
        {
            return true;
        }
        else
            return false;
    }

    function update($id, $datos)
    {
        $this->db->where("id_investigador",$id);
        if ($this->db->update("cpp_investigadores",$datos))
            return true;
        else
            return false;
    }

    function sqlTxt($count,$sql)
    {
        $exploud=  explode('FROM', $sql);
        $exploud=  explode('ORDER BY', $exploud[1]);
        $newSql=  "SELECT count($count) as cantidad FROM ".$exploud[0];
            return $newSql;
    }
    function cantSql($sql)
    {
        $query =$this->db->query($sql);
        $res = $query->result();
        return $res[0]->cantidad;
    }
   public function investigadoresCombo($limit,$start,$filtro,$idNot="")
    {

        $this->db->select('u.id_usuario,uu.usuario');
        $this->db->select('concat(e.abv," - ",pto.puesto) as puesto',false);
        $this->db->select('concat(pp.nombre," ",pp.apellido) as nomape',false);
        $this->db->from('gr_usuarios u');
        $this->db->join('sys_usuarios uu','uu.id_usuario = u.id_usuario','inner');
        $this->db->join('grl_personas pp','pp.id_persona = uu.id_persona','inner');
        $this->db->join('gr_puestos pto','pto.id_puesto = u.id_puesto','inner');
        $this->db->join('gr_organigramas or1','or1.id_organigrama = pto.id_organigrama','inner');
        $this->db->join('grl_empresas e','e.id_empresa = or1.id_empresa','inner');

        if($idNot!="")
            $this->db->where_not_in("u.id_usuario",$idNot);

        $this->db->where("pp.habilitado",1);
        $this->db->where("u.habilitado",1);
        $this->db->where("uu.habilitado",1);
        $this->db->where("e.id_empresa !=",3);
//            $this->db->where("pto.realiza_omc",1);
        $this->db->where("(pp.nombre like","'%".$filtro."%'",FALSE);
        $this->db->or_where("pp.apellido like","'%".$filtro."%'",FALSE);
        $this->db->or_where("concat(pp.nombre,' ',pp.apellido) like","'%".$filtro."%')",FALSE);

        $this->db->order_by("pp.nombre", "asc"); 
        $this->db->order_by("pp.apellido", "asc"); 

        $this->db->limit($limit,$start); 

        $query = $this->db->get();
//        echo $this->db->last_query();
        $last_query=$this->db->last_query();
        $pasQuery=$this->sqlTxt('u.id_usuario',$last_query);
        $num = $this->cantSql($pasQuery);
        $res = $query->result_array();
        if ($num > 0)
        {
            return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
        }
        else
            return '({"total":"0","rows":""})';
    }
    
    public function dameInvestigadores($id_evento)
    {
        $this->db->select('ci.id_usuario');
        $this->db->select("DATE_FORMAT(ci.fecha_alta,'%d/%m/%Y') as fecha_alta", FALSE);
        $this->db->select('concat(gp.nombre," ",gp.apellido) as nomape',false);
        $this->db->select('su.email');
        $this->db->from('cpp_investigadores ci');
        $this->db->join('sys_usuarios su','su.id_usuario = ci.id_usuario','inner');
        $this->db->join('grl_personas gp','gp.id_persona = su.id_persona','inner');
        $this->db->where('id_evento',$id_evento);
        $query = $this->db->get();
//        echo $this->db->last_query();
        $res = $query->result_array();
        $num = $query->num_rows();
        if ($num != 0)
        {
            return $res;
        }
        else
        {
            return 0;
        }
        
    }
}	
?>