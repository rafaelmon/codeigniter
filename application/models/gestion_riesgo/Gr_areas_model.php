<?php
class Gr_areas_model extends CI_Model
{
    public function dameAreasInferiores($ids)
    {
        $this->db->select('a.id_area');
        $this->db->from('gr_areas a');
        $this->db->where_in("a.id_area_padre",$ids);
        $query = $this->db->get();
        $res = $query->result_array();
        $num=count($res);
        if ($num > 0)
        {
                return $res;
        }
        else
                return 0;

    }
    public function dameComboGerenciasPorEmpresa($id_empresa)
    {
        $this->db->select('a.id_area');
        $this->db->select('a.area');
        $this->db->from('gr_areas a');
        $this->db->join('gr_organigramas o','o.id_organigrama = a.id_organigrama','inner');
        $this->db->where("a.gcia",1);
        $this->db->where("a.habilitado",1);
        $this->db->where("o.id_empresa",$id_empresa);
        $query = $this->db->get();
        $res = $query->result_array();
        $num=count($res);
        if ($num > 0)
        {
                return $res;
        }
        else
                return 0;

    }
    public function dameComboGerenciasPorOrganigrama($id_organigrama)
    {
        $this->db->select('a.id_area');
        $this->db->select('a.area');
        $this->db->from('gr_areas a');
        $this->db->join('gr_organigramas o','o.id_organigrama = a.id_organigrama','inner');
        $this->db->where("a.gcia",1);
        $this->db->where("a.habilitado",1);
        $this->db->where("o.id_organigrama",$id_organigrama);
        $query = $this->db->get();
        $res = $query->result_array();
        $num=count($res);
        if ($num > 0)
        {
                return $res;
        }
        else
                return 0;

    }
    public function dameComboAreasGRPorEmpresa($id_empresa="")
    {
        $this->db->select('a.id_area');
        $this->db->select('a.area');
        $this->db->from('gr_areas a');
        $this->db->join('gr_organigramas o','o.id_organigrama = a.id_organigrama','inner');
//        $this->db->join('grl_empresas e','e.id_empresa = o.id_empresa','inner');
        $this->db->where("a.gr",1);
        $this->db->where("a.habilitado",1);
        if($id_empresa!="")
            $this->db->where("o.id_empresa",$id_empresa);
        $query = $this->db->get();
        $res = $query->result_array();
        $num=count($res);
        if ($num > 0)
        {
                return $res;
        }
        else
                return 0;

    }
    public function dameArea($id)
    {
        $this->db->select('a.id_area,a.area,a.id_area_padre,a.gcia,a.abv');
        $this->db->from('gr_areas a');
        $this->db->where("id_area",$id);
        $query = $this->db->get();
        $res = $query->result_array();
        $num=count($res);
        if ($num > 0)
        {
                return $res[0];
        }
        else
                return 0;
    }
    public function dameTodasAreas()
    {
        $this->db->select('a.id_area,a.area,a.id_area_padre,a.gcia,a.abv');
        $this->db->from('gr_areas a');
        $this->db->where("habilitado",1);
        $query = $this->db->get();
        $res = $query->result_array();
        $num=count($res);
        if ($num > 0)
        {
                return $res;
        }
        else
                return 0;
    }
    public function dameGerencia($id)
    {
        $this->db->select('a.id_area,a.area,a.id_area_padre,a.gcia,a.abv');
        $this->db->select('o.id_empresa');
        $this->db->select('concat(e.abv,"-",a.abv) as origen', false);
        $this->db->from('gr_areas a');
        $this->db->join('gr_organigramas o','o.id_organigrama = a.id_organigrama','inner');
        $this->db->join('grl_empresas e','e.id_empresa = o.id_empresa','inner');
        $this->db->where("id_area",$id);
        $query = $this->db->get();
        $res = $query->result_array();
        $num=count($res);
        if ($num > 0)
        {
                return $res[0];
        }
        else
                return 0;
    }
    public function damePadre($id)
    {
        $this->db->select('a.id_area,a.area,a.id_area_padre,a.gcia,a.abv');
        $this->db->select('o.id_empresa');
        $this->db->select('concat(e.abv,"-",a.abv) as origen', false);
        $this->db->from('gr_areas a');
        $this->db->join('gr_organigramas o','o.id_organigrama = a.id_organigrama','inner');
        $this->db->join('grl_empresas e','e.id_empresa = o.id_empresa','inner');
        $this->db->where("id_area",$id);
        $query = $this->db->get();
        $res = $query->result_array();
        $num=count($res);
        if ($num > 0)
        {
                return $res[0];
        }
        else
                return 0;
    }
    public function checkAreaGr($id)
    {
        $this->db->select('a.id_area');
        $this->db->select('o.id_empresa');
        $this->db->select('concat(e.abv,"-",a.abv) as origen', false);
        $this->db->from('gr_areas a');
        $this->db->join('gr_organigramas o','o.id_organigrama = a.id_organigrama','inner');
        $this->db->join('grl_empresas e','e.id_empresa = o.id_empresa','inner');
        $this->db->where("id_area",$id);
        $this->db->where("gr",1);
        $query = $this->db->get();
        $res = $query->result_array();
        $num=count($res);
        if ($num > 0)
        {
                return $res[0];
        }
        else
                return 0;
    }
    public function dameAreaSuperior($id)
    {
        $this->db->select('a.id_area_padre');
        $this->db->from('gr_areas a');
        $this->db->where("id_area",$id);
        $query = $this->db->get();
        $res = $query->row();
        $num=count($res);
        if ($num > 0)
        {
                return $res->id_area_padre;
        }
        else
                return 0;
    }
    public function dameAreasEncadenadas($id_empresa="")
    {
        $this->db->select('a1.area as A1');
        $this->db->select('a2.area as A2');
        $this->db->select('a3.area as A3');
        $this->db->select('a4.area as A4');
        $this->db->select('a5.area as A5');
        $this->db->select('a6.area as A6');
        $this->db->select('CONCAT_WS("/"
                                    ,concat(e6.abv,"-",a6.area)
                                    ,concat(e5.abv,"-",a5.area)
                                    ,concat(e4.abv,"-",a4.area)
                                    ,concat(e3.abv,"-",a3.area)
                                    ,concat(e2.abv,"-",a2.area)
                                    ,concat(e1.abv,"-",a1.area)
                                    )as AreaOrganigrama',false);
        $this->db->from('gr_areas a1');
        $this->db->join('gr_organigramas o1','o1.id_organigrama = a1.id_organigrama','inner');
        $this->db->join('grl_empresas e1','e1.id_empresa = o1.id_empresa','inner');
        $this->db->join('gr_areas a2','a2.id_area = a1.id_area_padre','inner');
        $this->db->join('gr_organigramas o2','o2.id_organigrama = a2.id_organigrama','left');
        $this->db->join('grl_empresas e2','e2.id_empresa = o2.id_empresa','left');
        $this->db->join('gr_areas a3','a3.id_area = a2.id_area_padre','left');
        $this->db->join('gr_organigramas o3','o3.id_organigrama = a3.id_organigrama','left');
        $this->db->join('grl_empresas e3','e3.id_empresa = o3.id_empresa','left');
        $this->db->join('gr_areas a4','a4.id_area = a3.id_area_padre','left');
        $this->db->join('gr_organigramas o4','o4.id_organigrama = a4.id_organigrama','left');
        $this->db->join('grl_empresas e4','e4.id_empresa = o4.id_empresa','left');
        $this->db->join('gr_areas a5','a5.id_area = a4.id_area_padre','left');
        $this->db->join('gr_organigramas o5','o5.id_organigrama = a5.id_organigrama','left');
        $this->db->join('grl_empresas e5','e5.id_empresa = o5.id_empresa','left');
        $this->db->join('gr_areas a6','a6.id_area = a5.id_area_padre','left');
        $this->db->join('gr_organigramas o6','o6.id_organigrama = a6.id_organigrama','left');
        $this->db->join('grl_empresas e6','e6.id_empresa = o6.id_empresa','left');
        $this->db->where("e1.id_empresa",$id_empresa);
        $query = $this->db->get();
//        echo $this->db->last_query();
        $res = $query->result_array();
        $num=count($res);
        if ($num > 0)
        {
                return $res;
        }
        else
                return 0;
    }
    public function sp_areasInferiores($id_area_inicio)
    {
        $sp="call sp_arreasInferiores($id_area_inicio);";
        $areas=$this->db->query($sp);
        return $areas;
    }
    public function update($id, $datos)
    {
            $this->db->where("id_area",$id);
            $update=$this->db->update("gr_areas",$datos);
            if ($update)
                    return true;
            else
                    return false;
    }
        
    public function combo_gerencias_transferir_doc($id_gerencia_not, $query = "")
    {
        $this->db->select('a.id_area,a.area,a.abv');
        $this->db->from('gr_areas a');
        $this->db->join('gr_organigramas o','o.id_organigrama = a.id_organigrama','left');
        $this->db->join('grl_empresas e','e.id_empresa = o.id_empresa','inner');
        $this->db->where("e.id_empresa",2);
        $this->db->where("a.gcia",1);
        $this->db->where("a.habilitado",1);
        $this->db->where("a.area like '%",$query ."%'",false);
        $this->db->where_not_in("a.id_area",$id_gerencia_not);
        $query = $this->db->get();
//        echo $this->db->last_query();
        $res = $query->result_array();
        $num=count($res);
        if ($num > 0)
        {
                return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
        }
        else
                return '({"total":"0","rows":""})';;

    }
}	
?>