<?php
class Rep_administrar_top_model extends CI_Model
{
    public function dameTops($periodo)
    {
        $this->db->select('dt.id_top as idTop');
        $this->db->select('concat(gp.nombre, " ", gp.apellido) as Usuario',false);
//        $this->db->select('ge.empresa AS Empresa');
        $this->db->select('if(det.estado is null, "-.-",dp.periodo) as Periodo');
        $this->db->select('gpo.puesto as Puesto');
        $this->db->select('ga1.area as Gerencia');
        $this->db->select('concat(gp1.nombre, " ", gp1.apellido) as Supervisor',false);
        $this->db->select('if(det.estado is null, "Pendiente", det.estado) as Estado');
        $this->db->select('if(det.estado is null, 0,count(dos.id_objetivo)) as "SumObj"');
        $this->db->select('if(det.estado is null, 0,sum(dos.peso)) as "SumPeso"');
//        $this->db->select('if(det.estado is null, 0,(select count(ddpo.id_objetivo) from ddp_objetivos ddpo where ddpo.id_estado = 6 and ddpo.id_top = dt.id_top)) as Aprobado');
//        $this->db->select('if(det.estado is null, 0,(select count(ddpo1.id_objetivo) from ddp_objetivos ddpo1 where ddpo1.id_estado IN (1, 2, 3, 4, 5) and ddpo1.id_top = dt.id_top)) as ParaAprobar');
        $this->db->select(" CASE 
                                WHEN de.estado!='' THEN de.estado
                                ELSE 'Pendiente'
                            END as Estado",FALSE);
        $this->db->from('gr_usuarios gu');
        $this->db->join('ddp_tops dt', 'gu.id_usuario = dt.id_usuario','left');
        $this->db->join('ddp_estados_top det', 'dt.id_estado = det.id_estado', 'left');
        $this->db->join('sys_usuarios su', 'gu.id_usuario = su.id_usuario'); 
        $this->db->join('grl_personas gp', 'su.id_persona = gp.id_persona'); 
        $this->db->join('ddp_periodos dp', 'dt.id_periodo = dp.id_periodo','left');
        $this->db->join('gr_puestos gpo', 'gu.id_puesto = gpo.id_puesto');
        $this->db->join('gr_areas ga', 'gpo.id_area = ga.id_area');
        $this->db->join('gr_organigramas gor', 'ga.id_organigrama = gor.id_organigrama');
//        $this->db->join('grl_empresas ge', 'gor.id_empresa = ge.id_empresa');
        $this->db->join('gr_areas ga1', 'ga.id_gcia = ga1.id_area');
        $this->db->join('gr_puestos gpo1', 'gpo.id_puesto_superior = gpo1.id_puesto');
        $this->db->join('gr_usuarios gu1', 'gpo1.id_puesto = gu1.id_puesto'); 
        $this->db->join('sys_usuarios su1', 'gu1.id_usuario = su1.id_usuario');
        $this->db->join('grl_personas gp1', 'su1.id_persona = gp1.id_persona');
        $this->db->join('ddp_objetivos dos', 'dt.id_top = dos.id_top', 'left');
        $this->db->join('ddp_estados_top de','de.id_estado = dt.id_estado','left');
        $this->db->where('gu.habilitado = 1');
        $this->db->where('su.habilitado = 1');
        $this->db->where('gp.habilitado = 1'); 
        $this->db->groupby('gu.id_usuario');
        $query = $this->db->get();
        $res = $query->result_array();
        $num = $query->num_rows();
//        echo $this->db->last_query();die();
        if ($num > 0)
                return $res;
        else
                return 0;
    }
}	
?>
