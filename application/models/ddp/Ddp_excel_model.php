<?php
class Ddp_excel_model extends CI_Model
{
    public function datos_cabecera($id_top) 
    {
        $this->db->select("t.id_top, t.id_periodo, t.id_usuario, t.id_supervisor, t.id_puesto, pu.puesto, t.id_estado, t.fecha_alta");
        $this->db->select("et.detalle as estado");
        $this->db->select("p.periodo");
        $this->db->select("pu1.puesto as puestosupervisor");
        $this->db->select("CONCAT(pe.nombre,', ',pe.apellido) as nombre",false);
        $this->db->select("CONCAT(pe.nombre,pe.apellido) as nomape",false);
        $this->db->select("CONCAT(pe1.nombre,', ',pe1.apellido) as supervisor",false);
        $this->db->from("ddp_tops t");
        $this->db->join("sys_usuarios u","t.id_usuario = u.id_usuario");
        $this->db->join("grl_personas pe","u.id_persona = pe.id_persona");
        $this->db->join("sys_usuarios u1","t.id_supervisor = u1.id_usuario","left");
        $this->db->join("grl_personas pe1","u1.id_persona = pe1.id_persona","left");
        $this->db->join("ddp_periodos p","t.id_periodo = p.id_periodo");
        $this->db->join("ddp_estados_top et","t.id_estado = et.id_estado","left");
        $this->db->join("gr_puestos pu","t.id_puesto = pu.id_puesto","left");
        $this->db->join("gr_usuarios gu","t.id_supervisor = gu.id_usuario","left");
        $this->db->join("gr_puestos pu1","gu.id_puesto = pu1.id_puesto","left");
        $this->db->where("t.id_top",$id_top);
        $query = $this->db->get();
//        echo $this->db->last_query();
        $resultado = $query->result_array();
        return $resultado[0];
    }
    
    public function datos_grilla($id_top) 
    {
        $this->db->select('id_objetivo, id_top, o.id_dimension, dimension, css_bc, oe, op, indicador, fd, valor_ref, o.peso, real1, real2,o.habilitado');
        $this->db->from('ddp_objetivos o');
        $this->db->join('ddp_dimensiones d','o.id_dimension = d.id_dimension');
        $this->db->where('id_top',$id_top);
        $this->db->where('o.habilitado',1);
        $this->db->order_by('o.id_dimension');
        
        $query = $this->db->get();
//        echo $this->db->last_query();
        $resultado = $query->result_array();
        return $resultado;
    }
}