<? 
$permiso_listar = $permiso['Listar'];
$permiso_alta = $permiso['Alta'];
$permiso_eliminar = $permiso['Baja'];
$permiso_modificar = $permiso['Modificacion'];

$permiso_permiso_listar = $permiso_permiso['Listar'];
$permiso_permiso_alta = $permiso_permiso['Alta'];
$permiso_permiso_eliminar = $permiso_permiso['Baja'];
$permiso_permiso_modificar = $permiso_permiso['Modificacion'];

if ($permiso_listar):
?>	
	<script type="text/javascript">
	
	
	// Global vars
	// variables par alos permisos
	var permiso_alta = <?php echo $permiso_alta;?>;
	var permiso_eliminar = <?php echo $permiso_eliminar;?>;
	var permiso_modificar = <?php echo $permiso_modificar;?>;
        
        var permiso_permiso_listar = <?php echo $permiso_permiso_listar;?>;
	var permiso_permiso_alta = <?php echo $permiso_permiso_alta;?>;
	var permiso_permiso_eliminar = <?php echo $permiso_permiso_eliminar;?>;
	var permiso_permiso_modificar = <?php echo $permiso_permiso_modificar;?>;
	
	var TAM_PAGINA = 25;
	var CARPETA = "<? echo site_url("admin/perfiles");?>";
	var CARPETA_COMBO_UUAA = "<? echo site_url("tablas/uuaa");?>";
	var CARPETA_COMBO_SECTOR = "<? echo site_url("tablas/sectores");?>";
	var LINK_GENERICO = "<?= site_url("genericos")?>";
	
	var PerfilesColumnModel;
	var PerfilesListingEditorGrid;
	var PerfilCreateWindow;
	var PerfilCreateForm;
        
	
	
	Ext.onReady(function(){
	
	Ext.QuickTips.init();
	 	 	
	<?php
	// defincion de el checkbox para la grilla
	include_once(PATH_BASE."js/CheckColumn.js");
	
	?>
	
	<?php
	//// Ventana y funciones Alta usuario
	include_once("AltaPerfil.js");
	
	//// Ventana y funciones Permiso perfiles
	include_once("permisos/Permisos.js");
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