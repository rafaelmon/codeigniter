<? 
$permiso_listar = $permiso['Listar'];
$permiso_alta = $permiso['Alta'];
$permiso_eliminar = $permiso['Baja'];
$permiso_modificar = $permiso['Modificacion'];
if ($permiso_listar):
?>
	<link rel="STYLESHEET" type="text/css" href="<?=URL_BASE.'js/ext-3.3.0/UploadDialog/css/Ext.ux.UploadDialog.css'?>"></link>	
	<script type="text/javascript">
	
	<?php
	 include_once(PATH_BASE."js/ext-3.3.0/UploadDialog/Ext.ux.UploadDialog.js");
	 ?>	
	
	// Global vars
	// variables par alos permisos
	var permiso_alta = <?php echo $permiso_alta;?>;
	var permiso_eliminar = <?php echo $permiso_eliminar;?>;
	var permiso_modificar = <?php echo $permiso_modificar;?>;
	
	
    var TAM_PAGINA=<?=TAM_PAGINA?>;
	var CARPETA = "<?php echo site_url("admin/modulos");?>";
	var LINK_GENERICO = "<?= site_url("genericos")?>";
	
	var Modulostore;
	
	var ModulosDataStore;
	var ModulosColumnModel;
	var ModulosListingEditorGrid;
	var PermisosDataStore;
	var PermisosListingWindow;
	var PermisosColumnModel;
	var ModuloCreateWindow;
	var ModuloCreateForm;
	var tituloField;
	var accionField;
	var iconoField;
	var padreField;
	var ordenField;
	var hijosField;
	var menuField;
	//var ModulosListingEditorGrid;
	//var UsuarioListingSelectedRow;
	var ModuloListingContextMenu;
	
	var nuevopermisoWindow;
	var altapermisoForm;
	
	var items_form;
	
	Ext.onReady(function(){
	
	Ext.QuickTips.init();
	 	
	<?php
	// definicion del cuadro de dialogo de uploads
	// include_once("CuadroUploadClientes.js");
	?> 	
	 	
	<?php
	// defincion de el checkbox para la grilla
	include_once(PATH_BASE."js/CheckColumn.js");
	
	?>
	
		
	<?php
	//// Ventana y funciones Alta usuario
	include_once("AltaModulo.js");
	?>		
	
	<?php
	// defincion de la casila de busqueda para filtrar registros
	include_once(PATH_BASE."js/ext-3.3.0/Ext.ux.grid.Search/Ext.ux.grid.Search.js");
	?>

        <?php
	// llamo al paginador
	include_once(PATH_BASE."js/paginador.js");
	?>
		
		
	<?php
	// Datos para la grilla de usuarios
	include_once("Grilla.js");
	?> 
	
	
	})
	</script>	
<? else: ?>
	<h2>Su usuario no tiene permisos para usar este Modulo</h2>
<? endif; ?>

<div id="grillita"></div>
<div id="grillita_dos"></div>