<? 
$permiso_listar = $permiso['Listar'];
$permiso_alta = $permiso['Alta'];
$permiso_eliminar = $permiso['Baja'];
$permiso_modificar = $permiso['Modificacion'];

if ($permiso_listar):
?>
	<link rel="stylesheet" type="text/css" href="<?=URL_BASE.'js/ext-3.3.0/examples/ux/fileuploadfield/css/fileuploadfield.css'?>"/>	
	<script type="text/javascript">
	
	<?php
	 include_once(PATH_BASE."js/FileUploadField.js");
	 ?>
	
	// Global vars
	// variables par alos permisos
	var permiso_alta = <?php echo $permiso_alta;?>;
	var permiso_eliminar = <?php echo $permiso_eliminar;?>;
	var permiso_modificar = <?php echo $permiso_modificar;?>;
	
	
	var URL_BASE = "<?= URL_BASE?>";
	var URL_BASE_FILE = "<?= URL_BASE_FILE?>";
	var TAM_PAGINA=25;
	var CARPETA = "<?= site_url("dms/obsoletos")?>";
        var CARPETAGESTIONES = "<?= site_url("dms/gestiones")?>";
	var LINK_GENERICO = "<?= site_url("admin/genericos")?>";
        var URL_BASE_SITIO = "<?php echo URL_BASE_SITIO;?>";
        
        var paginadorObsoletos;
        var habilitadaCheck;
        var buscador;
	
        //
        var obsoletosDataStore;
        var obsoletosColumnModel;
        var obsoletosGrid;
        var tiposDocJS;
        var tiposDocFiltro;
        
	Ext.onReady(function(){
	
	Ext.QuickTips.init();
	
	<?php
	// definicion del cuadro de dialogo de uploads
//	 include_once("uploadAvatar.js");
	?> 

	
	// defincion de el checkbox para la grilla	
	
	<?php
	include_once(PATH_BASE."js/CheckColumn.js");
	?>
	
	// Ventana y funciones Alta usuario
	
	<?php
//	include_once("AltaDocumento.js");
//	include_once("EditDocumento.js");
	?>		

	// defincion de la casila de busqueda para filtrar registros
	<?php
	include_once(PATH_BASE."js/ext-3.3.0/Ext.ux.grid.Search/Ext.ux.grid.Search.js");
	?>  
		
	// Datos para la grilla de usuarios
	
	<?php
	include_once("Grilla.js");
        include_once("GrillaInferior.js");
        include_once("panel.js");
	?>
		
	// Datos para la ventana de permisos 
	
	<?php
//	include_once("usuarioSeccion.js");
	?>	
	})
	</script>	
<? else: ?>
	<h2>Su usuario no tiene permisos para usar este Modulo</h2>
<? endif; ?>
<div id="grillita"></div>