<? 
$permiso_listar = $permiso['Listar'];
$permiso_alta = $permiso['Alta'];
$permiso_eliminar = $permiso['Baja'];
$permiso_modificar = $permiso['Modificacion'];
$permiso_btn = $btn;
$permiso_btn_nueva = $btn_nueva;
$permiso_gr = $gr;
$min_date=date("Y-m-d");

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
	var permiso_btn_add = <?php echo $permiso_btn_nueva;?>;
	var permiso_btn_gr = <?php echo $permiso_gr;?>;
	var MINDATE = <?php echo "'".$min_date."'";?>;
	
	var URL_BASE = "<?= URL_BASE?>";
	var URL_BASE_FILE = "<?= URL_BASE_FILE?>";
	var TAM_PAGINA=25;
	var CARPETA = "<?= site_url("gestion_riesgo/omc")?>";
	var LINK_GENERICO = "<?= site_url("genericos")?>";
        var URL_BASE_SITIO = "<?php // echo URL_BASE_SITIO;?>";
        
        var habilitadaCheck;
        var buscador;
	
        //altaTareas
       var omcTareaCriticidadRadios
        //
        
        //altaOmc
        var sitioOmcDS;
        var sitioOmcCombo;
        var omcCreateForm;
        var sectorOmcDS;
        var sectorOmcCombo;
        var analisisRiesgoRmcRadios;
        var analisisRiesgoFieldSet;
        var sectorFieldSet;
        var clickBtnAprobar;
        var go_clickBtnAprobar;
        
        
	var tareasListingWindow;
	var tareasColumnModel;
	var ESTACIO='&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;';
	
	var omcPanel;
	var tareasPanel;
        
	
	Ext.onReady(function(){
	
	Ext.QuickTips.init();
	
	<?php
//	include_once(PATH_BASE."js/CheckColumn.js");
	?>
	
	// Ventana y funciones Alta usuario
	<?php
	include_once("altaOmc.js");
	include_once("altaTarea.js");
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
	include_once("tareas.js");
	include_once("panel.js");
	include_once("aprobar.js");
	?>
		
	})
	</script>	
<? else: ?>
	<h2>Su usuario no tiene tareas para usar este Modulo</h2>
<? endif; ?>
<div id="grillita"></div>