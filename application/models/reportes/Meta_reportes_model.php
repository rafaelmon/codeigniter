<?php
class Meta_reportes_model extends CI_Model
{
    public function dameUsuarios($anio,$mes)
    {
        $this->db->select('rt.id_usuario,rt.usuario,rt.puesto,rt.area,rt.gerencia,rt.empresa,rt.id_empresa,e.empresa as nomemp,rt.id_organigrama,rt.organigrama,rt.id_area,rt.id_gerencia,gp.id_organigrama as id_org');
        $this->db->select('sum(ua_q_tareas_t) as ua_q_tareas_t');
        $this->db->select('sum(ua_q_tareas_A) as ua_q_tareas_A');
        $this->db->select('sum(ua_q_tareas_A2) as ua_q_tareas_A2');
        $this->db->select('sum(ua_q_tareas_R) as ua_q_tareas_R');
        $this->db->select('sum(ua_q_tareas_Vt) as ua_q_tareas_Vt');
        $this->db->select('sum(ua_q_tareas_V1) as ua_q_tareas_V1');
        $this->db->select('sum(ua_q_tareas_V2) as ua_q_tareas_V2');
        $this->db->select('(select sum(ua_q_tareas_C) from meta_repo_tareas where MM = '.$mes.' and AAAA = '.$anio.' and id_usuario = rt.id_usuario) as ua_q_tareas_Cm');
        $this->db->select('(select sum(ua_q_tareas_C) from meta_repo_tareas where id_usuario = rt.id_usuario and !(MM = '.$mes.' and AAAA = '.$anio.')) as ua_q_tareas_Ct');
        $this->db->select('sum(ua_q_tareas_C) as ua_q_tareas_C');
        $this->db->select('sum(ur_q_tareas_t) as ur_q_tareas_t');
        $this->db->select('sum(ur_q_tareas_A) as ur_q_tareas_A');
        $this->db->select('sum(ur_q_tareas_A2) as ur_q_tareas_A2');
        $this->db->select('sum(ur_q_tareas_R) as ur_q_tareas_R');
        $this->db->select('sum(ur_q_tareas_Vt) as ur_q_tareas_Vt');
        $this->db->select('sum(ur_q_tareas_V1) as ur_q_tareas_V1');
        $this->db->select('sum(ur_q_tareas_V2) as ur_q_tareas_V2');
        $this->db->select('(select sum(ur_q_tareas_C) from meta_repo_tareas where MM = '.$mes.' and AAAA = '.$anio.' and id_usuario = rt.id_usuario) as ur_q_tareas_Cm');
        $this->db->select('(select sum(ur_q_tareas_C) from meta_repo_tareas where id_usuario = rt.id_usuario and !(MM = '.$mes.' and AAAA = '.$anio.')) as ur_q_tareas_Ct');
        $this->db->select('sum(ur_q_tareas_C) as ur_q_tareas_C');
        $this->db->select('rt.id_gcia,rt.id');
        $this->db->select('a.id_gcia_corp as id_corp,a.area as gerencia_corp');
        $this->db->from('meta_repo_tareas rt');
        $this->db->groupby('id_usuario');
        $this->db->join('gr_areas a','a.id_area = rt.id_gcia_corp');
        $this->db->join('grl_empresas e','rt.id_empresa = e.id_empresa');
        $this->db->join('gr_usuarios gu','rt.id_usuario = gu.id_usuario');
        $this->db->join('gr_puestos gp','gu.id_puesto = gp.id_puesto');
        $this->db->orderby('a.id_gcia_corp','ASC');
        $this->db->orderby('id_gcia','ASC');
        $this->db->orderby('id_area','ASC');
        $this->db->orderby('id_organigrama','ASC');
        $query = $this->db->get();
        $resultado = $query->result_array();
//        echo $this->db->last_query();die();
        return $resultado;
    }
    
}	
?>