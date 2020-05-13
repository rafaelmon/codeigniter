<?php
class Dropbox_model extends CI_Model
{
	
        
        public function dameArchivo($id_archivo)
	{
            $this->db->select('*');
            $this->db->from('dbx_archivos');
            $this->db->where('id_archivo',$id_archivo);
            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $query->num_rows();
            $res = $query->result_array();
            $revisores=array();
                
            if ($num > 0)
            {
                    return $res[0];
            }
            else
                    return 0;
        }
        public function dameArchivos()
	{
            $this->db->select('archivo_nom');
            $this->db->from('dbx_archivos');
            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $query->num_rows();
            $res = $query->result_array();
            $revisores=array();
                
            if ($num > 0)
            {
                    return $res;
            }
            else
                    return 0;
        }
        public function dameArchivosPorDirectorio($start, $limit, $filtro,$busqueda, $campos, $sort, $dir,$id_directorio)
	{
            $this->db->select('id_archivo,archivo_nom as archivo, ext, tam as size');
            $this->db->select('concat(tam," Kb") as size',FALSE);
            $this->db->from('dbx_archivos');
            $this->db->where('id_directorio',$id_directorio);
             $this->db->limit($limit,$start); 
            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $query->num_rows();
            $res = $query->result_array();
            $revisores=array();
                
            if ($num > 0)
            {
                    return '({"total":"'.$num.'","rows":'.json_encode($res).'})';
            }
            else
                return '({"total":"0","rows":""})';
        }
        public function dameDirectoriosRaiz()
	{
            $this->db->select('d1.id_directorio,d1.directorio as dir');
            $this->db->select('(select count(d2.id_directorio) as hijos from dbx_directorios d2 where d2.id_padre=d1.id_directorio) as hijos',FALSE);
            $this->db->select('(select count(a.id_archivo) as archivos from dbx_archivos a where a.id_directorio=d1.id_directorio) as archivos',FALSE);
            $this->db->from('dbx_directorios d1');
            $this->db->where('d1.id_padre ',null);
            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $query->num_rows();
            $res = $query->result_array();
            $revisores=array();
                
            if ($num > 0)
            {
                    return $res;
            }
            else
                    return 0;
        }
        public function dameSubDirectorios($id_directorio)
	{
            $this->db->select('d1.id_directorio,d1.directorio as dir');
            $this->db->select('(select count(a.id_archivo) as archivos from dbx_archivos a where a.id_directorio=d1.id_directorio) as archivos',FALSE);
            $this->db->from('dbx_directorios d1');
//            $this->db->where('d1.id_padre is not null', NULL, FALSE);
            $this->db->where('d1.id_padre', $id_directorio);
            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $query->num_rows();
            $res = $query->result_array();
            $revisores=array();
                
            if ($num > 0)
            {
                    return $res;
            }
            else
                    return 0;
        }
        public function dameDirectorios()
	{
            $this->db->select('d1.directorio as dir,d2.directorio as sub');
            $this->db->from('dbx_directorios d1');
            $this->db->join('dbx_directorios d2','d1.id_padre=d2.id_directorio','left');
            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $query->num_rows();
            $res = $query->result_array();
            $revisores=array();
                
            if ($num > 0)
            {
                    return $res;
            }
            else
                    return 0;
        }
        public function dameArchivosConDirectorio()
	{
            $this->db->select('a.id_archivo, a.archivo_nom,d1.directorio as sub,d2.directorio as dir');
            $this->db->from('dbx_archivos a');
            $this->db->join('dbx_directorios d1','d1.id_directorio=a.id_directorio','inner');
            $this->db->join('dbx_directorios d2','d1.id_padre=d2.id_directorio','left');
            $query = $this->db->get();
//            echo $this->db->last_query();
            $num = $query->num_rows();
            $res = $query->result_array();
            $revisores=array();
                
            if ($num > 0)
            {
                    return $res;
            }
            else
                    return 0;
        }
        public function update_archivos($id, $datos)
	{
		$this->db->where("id_archivo",$id);
		if ($this->db->update("dbx_archivos",$datos))
			return true;
		else
			return false;
	}
        
}	
?>