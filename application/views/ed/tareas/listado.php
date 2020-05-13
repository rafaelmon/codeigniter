<? 
$permiso_listar = $permiso['Listar'];
$permiso_alta = $permiso['Alta'];
$permiso_eliminar = $permiso['Baja'];
$permiso_modificar = $permiso['Modificacion'];
$permiso_btn = $btn;
//$permiso_btn_obs = $btn_obsoleto;
$permiso_gr = $u_gr;
//$min_date= date("Y-m-d");
$min_date = strtotime ( '+1 day' , strtotime ( date('Y-m-j') ) ) ;
$min_date = date ( 'Y-m-d' , $min_date );

if ($permiso_listar):
?>
	<link rel="stylesheet" type="text/css" href="<?=URL_BASE.'js/ext-3.3.0/examples/ux/fileuploadfield/css/fileuploadfield.css'?>"/>	
	<script type="text/javascript">
	
	<?php
	 include_once(PATH_BASE."js/FileUploadField.js");
	 ?>
	
	// Global vars
	// variables par alos tareas
	var permiso_alta = <?php echo $permiso_alta;?>;
	var permiso_eliminar = <?php echo $permiso_eliminar;?>;
	var permiso_modificar = <?php echo $permiso_modificar;?>;
	var permiso_btn = <?php echo $permiso_btn;?>;
	
	var permiso_gr = <?php echo $permiso_gr;?>;
	var MINDATE = <?php echo "'".$min_date."'";?>;
	
	var URL_BASE = "<?= URL_BASE?>";
	var URL_BASE_FILE = "<?= URL_BASE_FILE?>";
	var TAM_PAGINA=25;
	var ID_TAREA=0;
	var CARPETA = "<?= site_url("ed/tareas_ed")?>";
	var LINK_GENERICO = "<?= site_url("genericos")?>";
        var URL_BASE_SITIO = "<?php // echo URL_BASE_SITIO;?>";
        
        var habilitadaCheck;
        var buscador;
	
        //
        var tareasDataStore;
        var tareasColumnModel;
        var tareasListingEditorGrid;
        //
        
        //alta.js
        var herramientasDS;
        var herramientasCombo;
	var hallazgoField;
	var tareaField;
        var usuariosDS;
        var responsablesCombo;
        var responsableFieldSet;
        var fechaField;        
	var tareaCreateForm;
	var tareaCreateWindow;
        var tareaCriticidadRadios;
        
        //edita.js
        var herramientasDSU;
        var herramientasComboU;
	var hallazgoFieldU;
	var tareaFieldU;
        var usuariosDSU;
        var responsablesComboU;
        var responsableFieldSetU;
        var fechaFieldU;        
	var tareaUpdateForm;
        var tareaUpdateWindow;
        
        //historial_acciones
        var historialAccionesTareaDataStore;
        var historialAccionesTareasColumnModel;
        var historialAccionesTareasGridPanel;
        var tareasHistorialAccionesPanel;
        var tareasHistorialAccionesPanel;
        
	var tareasListingWindow;
	var tareasColumnModel;
	var ESTACIO='&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;';
	
	var inferiorHistorialPanel;
        
	
	Ext.onReady(function(){
	
	Ext.QuickTips.init();
	
	<?php
	include_once(PATH_BASE."js/sesionControl.js");
	?>
	
	// defincion de la casila de busqueda para filtrar registros
	<?php
	include_once(PATH_BASE."js/ext-3.3.0/Ext.ux.grid.Search/Ext.ux.grid.Search.js");
	?>  
	<?php
        // llamo al paginador
        include_once(PATH_BASE."js/paginador.js");
        ?>
		
	<?php
	include_once("grilla.js");
	include_once("historial_acciones.js");
	include_once("historial.js");
	include_once("panel.js");
	?>
		
	})
	</script>	
<? else: ?>
	<h2>Su usuario no tiene tareas para usar este Modulo</h2>
<? endif; ?>
<div id="grillita"></div>