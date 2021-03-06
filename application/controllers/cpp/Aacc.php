<?php
class Aacc extends CI_Controller
{
    private $modulo=52;
    private $user;
    private $permisos;
    
	function __construct()
	{
            parent::__construct();
            header ("Expires: Thu, 31 Dic 1976 23:59:00 GMT"); //la pagina expira en una fecha pasada
            header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); //ultima actualizacion ahora cuando la cargamos
            header ("Cache-Control: no-cache, must-revalidate"); //no guardar en CACHE
            header ("Pragma: no-cache");
            $this->user=$this->session->userdata(USER_DATA_SESSION);
                if (!($this->user['id']>0)) 
                    redirect("admin/admin/index_js","location", 301);
            $this->load->model("permisos_model","permisos_model",true);
            $this->permisos= $this->permisos_model->checkIn($this->user['perfil_id'],$this->modulo);
//            echo "<pre>Origen".print_r($this,true)."</pre>";
	}
	
	public function index()
	{
            $modulo = $this->uri->segment(SEGMENTOS_DOM+2);
            if (!isset($modulo)) $modulo=0;
            $this->load->model('permisos_model','permisos_model',true);
            $variables['permiso'] = $this->permisos_model->checkIn($this->user['perfil_id'],$modulo);
            $this->load->view('cpp/aacc/listado',$variables);
	}
	
	public function listado()
	{
            if ($this->permisos['Listar'])
            {
                $this->load->model('cpp/cpp_areas_causante_model','aacc',true);
                $start = $this->input->post("start");
                $limit = $this->input->post("limit");
                $sort = $this->input->post("sort");
                $dir = $this->input->post("dir");
                $campos = "";
                if ($this->input->post("fields"))
                {
                    $campos = str_replace('"', "", $this->input->post("fields"));
                    $campos = str_replace('[', "", $campos);
                    $campos = str_replace(']', "", $campos);
                    $campos = explode(",",$campos);
                }
                $busqueda  = ($this->input->post("query"))?$this->input->post("query"):"";

                $listado = $this->aacc->listado($start, $limit, $sort,$dir,$busqueda,$campos);
                echo $listado;
            }
            else
                echo -1; //No tiene permisos
	}
        
        public function insert()
	{
            if ($this->permisos['Alta'])
            {
                $datos['ac']                = $this->input->post("ac");
                $datos['descripcion']       = $this->input->post("descripcion");
//                echo "<pre>".print_r($datos,true)."</pre>";die();
                if ($datos['ac'] != '')
                {
                    $this->load->model('cpp/Cpp_areas_causante_model','aacc',true);	
                    if ($this->aacc->insert($datos))
                        echo 1;
                    else
                        echo 0;
                }
                else
                    echo 2;
            }
            else
                echo "({success: false, error :'No tiene permisos para realizar la acciòn solicitada'})"; //
	}
        
        public function update()
	{
            if ($this->permisos['Modificacion'])
            {
                $id = $this->input->post("id_ac");
                
                $campos = json_decode($this->input->post("campos"));
                $valores = json_decode($this->input->post("valores"));
                if (count($campos)>0)
                {
                    foreach ($campos as $indice=>$camp)
                    {
//                        echo "<pre>".print_r($indice,true)."</pre>";die();
                        $datos[$camp]=$valores[$indice];
                    }
                    $this->load->model('cpp/cpp_areas_causante_model','aacc',true);
                    
                    
                    if ($this->aacc->update($id,$datos))
                    {
                        echo 1;
                    }
                    else
                        echo 0;
                }
                else
                    echo 0;
            }
            else
                echo 2;
        }
}
?>