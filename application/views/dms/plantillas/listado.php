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
	var CARPETA = "<?= site_url("dms/plantillas")?>";
	var LINK_GENERICO = "<?= site_url("admin/genericos")?>";
        var URL_BASE_SITIO = "<?php echo URL_BASE_SITIO;?>";
        var URL_DMS_PLANTILLAS = "<?php echo URL_DMS_PLANTILLAS;?>";
        
        var habilitadaCheck;
        var buscador;
	
        //
        var PlantillasDataStore;
        //
        
	var PlantillaCreateWindow;
	var PlantillaCreateForm;
	var plantillaField;
        var descripcionField;        
        
	var PermisosListingWindow;
	var PermisosColumnModel;
	var UsuarioCreateWindow;
	var UsuarioCreateForm;
	var nombreUsrField;
	var usuarioField;
	var passwordField;
	var nivelField;
	var codigoField;
	var UsuariosListingEditorGrid;
	var UsuarioListingSelectedRow;
	var UsuarioListingContextMenu;
	
	var nuevopermisoWindow;
	var altapermisoForm;
	
	var items_form;
	
	Ext.onReady(function(){
	
	Ext.QuickTips.init();
	
	<?php
	// definicion del cuadro de dialogo de uploads
//	 include_once("uploadAvatar.js");
	?> 

	<?php
        // llamo al paginador
        include_once(PATH_BASE."js/paginador.js");
        ?>
	
	<?php
	include_once(PATH_BASE."js/CheckColumn.js");
	?>
	
	// Ventana y funciones Alta usuario
	
	<?php
//	include_once("AltaPlantilla.js");
//	include_once("EditPlantilla.js");
	?>		

	// defincion de la casila de busqueda para filtrar registros
	<?php
	include_once(PATH_BASE."js/ext-3.3.0/Ext.ux.grid.Search/Ext.ux.grid.Search.js");
	?>  
		
	// Datos para la grilla de usuarios
	
	<?php
	include_once("Grilla.js");
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