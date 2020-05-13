<? 
$permiso_listar = $permiso['Listar'];
$permiso_alta = $permiso['Alta'];
$permiso_eliminar = $permiso['Baja'];
$permiso_modificar = $permiso['Modificacion'];
$permiso_listar_permiso = $permiso_permiso['Listar'];
$permiso_alta_permiso = $permiso_permiso['Alta'];
$permiso_eliminar_permiso = $permiso_permiso['Baja'];
if ($permiso_listar):
?>
<!--	<link rel="stylesheet" type="text/css" href="<?=URL_BASE.'js/ext-3.3.0/examples/ux/fileuploadfield/css/fileuploadfield.css'?>"/>	-->
	<script type="text/javascript">
	
	<?php
//	 include_once(PATH_BASE."js/ext-3.3.0/examples/ux/fileuploadfield/FileUploadField.js");
	 ?>
	
	// Global vars
	// variables par alos permisos
	var permiso_alta = <?php echo $permiso_alta;?>;
	var permiso_eliminar = <?php echo $permiso_eliminar;?>;
	var permiso_modificar = <?php echo $permiso_modificar;?>;
	
	
	var URL_BASE = "<?= URL_BASE?>";
	var URL_BASE_FILE = "<?= URL_BASE_FILE?>";
	var TAM_PAGINA=<?=TAM_PAGINA?>;
	var CARPETA = "<?= site_url("admin/personas")?>";
	var LINK_GENERICO = "<?= site_url("genericos")?>";
	
	var PersonasDataStore;
	var PersonasColumnModel;
	var PersonaCreateWindow;
	var PersonaCreateForm;
	var nombreField;
	var apellidoField;
	var documentoField;
	var PersonasListingEditorGrid;
	var PersonaListingSelectedRow;
	
	Ext.onReady(function(){
	
	Ext.QuickTips.init();
	
	
	// defincion de el checkbox para la grilla	
	<?php
	include_once(PATH_BASE."js/CheckColumn.js");
	?>
        <?php
        // llamo al paginador
        include_once(PATH_BASE."js/paginador.js");
        ?>
	
	// Ventana y funciones Alta Persona
	<?php
	include_once("AltaPersona.js");
	?>		

	// defincion de la casila de busqueda para filtrar registros
	<?php
	include_once(PATH_BASE."js/ext-3.3.0/Ext.ux.grid.Search/Ext.ux.grid.Search.js");
	?>  
		
	// Datos para la grilla de Personas
	<?php
	include_once("Grilla.js");
	?>
		
	
	})
	</script>	
<? else: ?>
	<h2>No tiene permisos para usar este Modulo</h2>
<? endif; ?>
<div id="grillita"></div>