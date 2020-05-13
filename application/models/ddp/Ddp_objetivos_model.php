<?php
class Ddp_objetivos_model extends CI_Model
{
	public function listado_usuario($datos,$start, $limit, $filtros, $busqueda, $campos,$sort="", $dir="")
    {
//             echo "<pre>". print_r($usuario,true)."<pre>";
            $this->db->select('obj.id_objetivo,obj.id_dimension,obj.id_estado,obj.obj,obj.indicador,obj.fd,obj.valor_ref,obj.peso,obj.real1,obj.real2');
            $this->db->select('d.q_obj');
            $this->db->select('((obj.peso*obj.real1)/100) as pesoreal',false);
            $this->db->select('p.periodo');
            $this->db->select('d.abv,d.css_bc,d.dimension');
            $this->db->select("DATE_FORMAT(obj.fecha_evaluacion,'%d/%m/%Y') as fecha_evaluacion", FALSE);
            $this->db->select('e.estado');
            $this->db->select("case obj.id_estado
                when 1 then 'Editor'
                when 2 then 'Supervisor'
                when 3 then 'Supervisor'
                when 4 then 'Editor'
                when 5 then 'Editor'
                when 6 then 'Aprobado Fase1'
                when 7 then 'Rechazado'
                when 8 then 'Anulado'
                when 9 then 'Editor'
                when 10 then 'Supervisor'
                when 11 then 'Supervisor'
                when 12 then 'Editor'
                when 13 then 'Editor'
                when 14 then 'Aprobado Ev'
                when 15 then 'Supervisor'
                when 16 then 'Supervisor'
                when 17 then 'Supervisor'
                when 18 then 'Aprobado Ev2'
                end as actor",false);
//            $this->db->select("concat(pp.nombre,' ',pp.apellido) as usuario_alta",false);
//            $this->db->select('d.abv as dimension,d.css_bc');
            $this->db->from('ddp_objetivos obj');
            $this->db->join('ddp_tops t','t.id_top = obj.id_top','inner');
            $this->db->join('ddp_periodos p','p.id_periodo = t.id_periodo','inner');
            $this->db->join('ddp_dimensiones d','d.id_dimension = obj.id_dimension','left');
            $this->db->join('ddp_estados_objs e','e.id_estado = obj.id_estado','inner');
            if ($datos['id_dimension'])
                $this->db->where("obj.id_dimension",$datos['id_dimension']);
//            $this->db->where("obj.id_usuario_alta",$datos['id_usuario']);
            $this->db->where("obj.id_top",$datos['id_top']);
            $this->db->where("d.habilitado",1);
//             $this->db->join('ddp_dimensiones d','d.id_dimension = obj.id_dimension','inner');
//              $this->db->join('gr_usuarios uu','uu.id_usuario = obj.id_usuario_alta','inner');
//              $this->db->join('sys_usuarios u','u.id_usuario = uu.id_usuario','inner');
//             $this->db->join('grl_personas pp','pp.id_persona = u.id_persona','inner');
            
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
                        case 'oe':
                            $campo="obj.".$campo;
                            break;
                        case 'periodo':
                            $campo="p.".$campo;
                            break;
                        default :
                            unset($campo);
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
//        if ($sort!="")
//        {
//            switch ($sort)
//            {
//                case 'id_objetivo':
//                case 'op':
//                    $sort="obj.".$sort;
//                    break;
//            }
//            $this->db->order_by($sort, $dir);
//        }
//        else
            $this->db->order_by("d.orden", "asc");
            $this->db->order_by("obj.id_estado", "asc"); 
            $this->db->order_by("obj.id_objetivo", "asc"); 
//        $this->db->limit($limit,$start); 
        $this->db->limit(100,0); 

        $query = $this->db->get();
//        echo $this->db->last_query();die();
        $num = $this->cantSql('obj.id_objetivo',$this->db->last_query());
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

            $this->db->insert('ddp_objetivos',$datos);
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

    public function update($id, $datos)
    {
            $this->db->where("id_objetivo",$id);
            $update=$this->db->update("ddp_objetivos",$datos);
//                echo $update;
//                echo $this->db->last_query();
            if ($update)
                    return 1;
            else
                    return 0;
    }
    public function rechazarObjsTop($id_top)
    {
            $this->db->set("id_estado",1);
            $this->db->where("id_top",$id_top);
            $this->db->where("id_dimension !=",3);
            $update=$this->db->update("ddp_objetivos");
//                echo $update;
//                echo $this->db->last_query();
            if ($update)
                    return 1;
            else
                    return 0;
    }
    public function dameObjetivo($id_obj)
    {
        $this->db->select("o.*");
        $this->db->select("t.id_supervisor");
        $this->db->from("ddp_objetivos o");
         $this->db->join('ddp_tops t','t.id_top = o.id_top','inner');
        $this->db->where("o.id_objetivo",$id_obj);
        $this->db->where("o.habilitado",1);
        $query = $this->db->get();
//            echo $this->db->last_query();
        $num = $query->num_rows();
        $res = $query->result_array();
        if ($num == 1)
        {
                return $res[0];
        }
        else
                return 0;
    }
    public function dameObjetivosTop($id_top)
    {
        $this->db->select("o.id_dimension,o.obj,o.indicador,o.fd,o.valor_ref,o.peso,o.real1");
        $this->db->select("DATE_FORMAT(o.fecha_evaluacion,'%d/%m/%Y') as fecha_evaluacion", FALSE);
        $this->db->from("ddp_objetivos o");
         $this->db->join('ddp_tops t','t.id_top = o.id_top','inner');
        $this->db->where("o.id_top",$id_top);
        $this->db->where("o.habilitado",1);
        $query = $this->db->get();
//            echo $this->db->last_query();
        $num = $query->num_rows();
        $res = $query->result_array();
        if ($num > 0)
        {
                return $res;
        }
        else
                return 0;
    }
    public function dameCopiaObjetivosTop($id_top)
    {
        $this->db->select("o.id_dimension,o.obj,o.indicador,o.fd,o.valor_ref,o.peso,o.fecha_evaluacion");
        $this->db->from("ddp_objetivos o");
         $this->db->join('ddp_tops t','t.id_top = o.id_top','inner');
        $this->db->where("o.id_top",$id_top);
        $this->db->where("o.habilitado",1);
        //$this->db->where("t.id_estado !=",1);
        $this->db->where("o.id_dimension",3);
        $query = $this->db->get();
//            echo $this->db->last_query();
        $num = $query->num_rows();
        $res = $query->result_array();
        if ($num > 0)
        {
                return $res;
        }
        else
                return 0;
    }
    public function damePesoTotal($id_top)
    {
        $this->db->select("sum(o.peso) as total_peso");
        $this->db->from("ddp_objetivos o");
        $this->db->where("o.habilitado",1);
        $this->db->where("o.id_top",$id_top);
        $query = $this->db->get();
//            echo $this->db->last_query();
        $num = $query->num_rows();
        $res = $query->result_array();
        if ($num == 1)
        {
                return $res[0];
        }
        else
                return 0;
    }
    public function verificaTodosConPeso($id_top)
    {
        $this->db->select("o.id_objetivo");
        $this->db->from("ddp_objetivos o");
        $this->db->where("o.habilitado",1);
        $this->db->where("o.id_top",$id_top);
        $this->db->where("o.peso <=",0);
        $query = $this->db->get();
//        echo $this->db->last_query();
        $num = $query->num_rows();
        $res = $query->result_array();
        if ($num >= 1)
        {
                return $num;
        }
        else
                return 0;
    }
    public function verificaTodosAprobados($id_top)
    {
        $this->db->select("o.id_objetivo");
        $this->db->from("ddp_objetivos o");
        $this->db->where("o.habilitado",1);
        $this->db->where("o.id_estado !=",6);
        $this->db->where("o.id_top",$id_top);
        $this->db->where("o.id_dimension !=",3); //saco la diomension organizacional que no se aprueban
        $query = $this->db->get();
//        echo $this->db->last_query();
        $num = $query->num_rows();
        $res = $query->result_array();
        if ($num >= 1)
        {
                return $num;
        }
        else
                return 0;
    }
    public function verificaTodosAprobadosEv1($id_top)
    {
        $this->db->select("o.id_objetivo");
        $this->db->from("ddp_objetivos o");
        $this->db->where("o.habilitado",1);
        $this->db->where("o.id_estado !=",14);
        $this->db->where("o.id_top",$id_top);
        $query = $this->db->get();
//        echo $this->db->last_query();
        $num = $query->num_rows();
        $res = $query->result_array();
        if ($num >= 1)
        {
                return $num;
        }
        else
                return 0;
    }
    public function dameObjetivoEditado($id_obj)
    {
        $this->db->select("obj,indicador,fd,valor_ref");
        $this->db->select("DATE_FORMAT(fecha_evaluacion,'%d/%m/%Y') as fecha_evaluacion", FALSE);
        $this->db->from("ddp_objetivos");
        $this->db->where("id_objetivo",$id_obj);
        $this->db->where("habilitado",1);
        $query = $this->db->get();
//            echo $this->db->last_query();
        $num = $query->num_rows();
        $res = $query->result_array();
        if ($num == 1)
        {
                return $res[0];
        }
        else
                return 0;
    }
    public function dameObjetivoParaForm($id_obj)
    {
        $this->db->select("o.id_objetivo");
//        $this->db->select("o.oe as ddpObjetivoEmpresaField");
        $this->db->select("o.obj as ddpObjetivoField");
        $this->db->select("o.indicador as ddpIndicadorObjetivoField");
        $this->db->select("o.fd as ddpFuenteDatosObjetivoField");
        $this->db->select("o.valor_ref as ddpValorRefObjetivoField");
        $this->db->select("o.peso as ddpPesoObjetivoNumberField");
        $this->db->select("o.real1 as ddpReal1ObjetivoNumberField");
        $this->db->select("o.real2 as ddpReal2ObjetivoNumberField");
//        $this->db->select("DATE_FORMAT(o.fecha_evaluacion,'%d/%m/%Y') as ddpFechaEvaluacionField");
        $this->db->select("DATE_FORMAT(o.fecha_evaluacion,'%d/%m/%Y') as ddpFechaEvaluacionField", FALSE);
        $this->db->from("ddp_objetivos o");
        $this->db->where("id_objetivo",$id_obj);
        $this->db->where("habilitado",1);
        $query = $this->db->get();
//            echo $this->db->last_query();
        $num = $query->num_rows();
        $res = $query->result_array();
        if ($num == 1)
        {
                return "{ 'success': true, 'msg': 'dada', 'data': ".json_encode($res[0])."}";
        }
        else
                return 0;
    }
    public function dameObjetivoParaFormEditSupTop($id_obj)
    {
        $this->db->select("o.id_objetivo");
//        $this->db->select("o.oe as ddpSupTopObjetivoEmpresaField");
        $this->db->select("o.obj as ddpObjetivoField");
        $this->db->select("o.indicador as ddpSupTopIndicadorObjetivoField");
        $this->db->select("o.fd as ddpSupTopFuenteDatosObjetivoField");
        $this->db->select("o.valor_ref as ddpSupTopValorRefObjetivoField");
//        $this->db->select("o.peso as ddpSupTopPesoObjetivoNumberField");
        $this->db->select("o.peso as ddpSupTopPesoObjetivoField");
        $this->db->select("o.real1 as ddpSupTopReal1ObjetivoNumberField");
        $this->db->select("o.real2 as ddpSupTopReal2ObjetivoNumberField");
        $this->db->select("DATE_FORMAT(o.fecha_evaluacion,'%d/%m/%Y') as ddpFechaEvaluacionField", FALSE);
        $this->db->from("ddp_objetivos o");
        $this->db->where("id_objetivo",$id_obj);
        $this->db->where("habilitado",1);
        $query = $this->db->get();
//            echo $this->db->last_query();
        $num = $query->num_rows();
        $res = $query->result_array();
        if ($num == 1)
        {
                return "{ 'success': true, 'msg': 'dada', 'data': ".json_encode($res[0])."}";
        }
        else
                return 0;
    }
    public function dameObjetivoParaFormEditSupTop_ev1($id_obj)
    {
        $this->db->select("o.id_objetivo");
        $this->db->select("o.obj as ddpSupTopObjetivoField");
        $this->db->select("o.indicador as ddpSupTopIndicadorObjetivoField");
        $this->db->select("o.fd as ddpSupTopFuenteDatosObjetivoField");
        $this->db->select("o.valor_ref as ddpSupTopValorRefObjetivoField");
         $this->db->select("o.peso as ddpSupTopPesoObjetivoNumberField");
        $this->db->select("o.real1 as ddpSupTopReal1ObjetivoNumberField");
        $this->db->select("o.real2 as ddpSupTopReal2ObjetivoNumberField");
        $this->db->select("DATE_FORMAT(o.fecha_evaluacion,'%d/%m/%Y') as ddpFechaEvaluacionField", FALSE);
        $this->db->from("ddp_objetivos o");
        $this->db->where("id_objetivo",$id_obj);
        $this->db->where("habilitado",1);
        $query = $this->db->get();
//            echo $this->db->last_query();
        $num = $query->num_rows();
        $res = $query->result_array();
        if ($num == 1)
        {
                return "{ 'success': true, 'msg': 'dada', 'data': ".json_encode($res[0])."}";
        }
        else
                return 0;
    }
    public function dameUsuarios($id_obj)
    {
        $this->db->select("t.id_usuario,t.id_supervisor");
        $this->db->from("ddp_objetivos obj");
        $this->db->join('ddp_tops t','t.id_top = obj.id_top','inner');
        $this->db->where("id_objetivo",$id_obj);
        $this->db->where("obj.habilitado",1);
        $query = $this->db->get();
//            echo $this->db->last_query();
        $num = $query->num_rows();
        $res = $query->result_array();
        if ($num == 1)
        {
                return $res[0];
        }
        else
                return 0;
    }
    public function cantidadObjPorDim($id_top,$id_dimension)
    {
        $this->db->select('count(id_objetivo) as cant',false);
        $this->db->from('ddp_objetivos');
        $this->db->where("id_top",$id_top);
        $this->db->where("id_dimension",$id_dimension);
        $query = $this->db->get();
//        echo $this->db->last_query();
//        $res = $query->result_array();
        $res = $query->row();
        $num=$query->num_rows();
        if ($num == 1)
        {
                return $res->cant;
        }
        else
                return 0;

    }
    public function delete($id)
    {
            $this->db->trans_begin();

            $this->db->query("DELETE FROM ddp_objetivos_historial1 WHERE id_objetivo = ".$id);
            $this->db->query("DELETE FROM ddp_objetivos WHERE id_objetivo = ".$id);

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
    
    public function dame_q_obj ($id_dimension,$id_top)
    {
        $this->db->select('count(o.id_objetivo) as q_obj');
        $this->db->from('ddp_objetivos o');
        $this->db->where("o.id_dimension",$id_dimension);
        $this->db->where("o.id_top",$id_top);
        $query = $this->db->get();
//        echo $this->db->last_query();
        $res = $query->result_array();
        $num=count($res);
        if ($num == 1)
            return $res[0]['q_obj'];
        else
            return 0;
        
    }
    public function dameTotalObjTop ($id_top)
    {
        $this->db->select('count(o.id_objetivo) as q_obj');
        $this->db->from('ddp_objetivos o');
        $this->db->where("o.id_top",$id_top);
        $query = $this->db->get();
//        echo $this->db->last_query();
        $res = $query->result_array();
        $num=count($res);
        if ($num == 1)
            return $res[0]['q_obj'];
        else
            return 0;
        
    }
        
}
?>