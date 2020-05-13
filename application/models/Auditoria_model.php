<?php
class Auditoria_model extends CI_Model
{
    public function cargarAccion($datos)
    {
            if ($this->db->insert("sys_usuarios_historial",$datos))
                    return true;
            else
                    return false;
    }
    
    public function guardarPedidoExcel($datos)
    {
            if ($this->db->insert("aud_excel",$datos))
                    return true;
            else
                    return false;
    }
}
?>