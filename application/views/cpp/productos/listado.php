<? 
$permiso_listar = $permiso['Listar'];
$permiso_alta = $permiso['Alta'];
$permiso_eliminar = $permiso['Baja'];
$permiso_modificar = $permiso['Modificacion'];
if ($permiso_listar):
?>
	<script type="text/javascript">
	// Global vars
	// variables par alos permisos
	var permiso_alta = <?php echo $permiso_alta;?>;
	var permiso_eliminar = <?php echo $permiso_eliminar;?>;
	var permiso_modificar = <?php echo $permiso_modificar;?>;
	
	var URL_BASE = "<?= URL_BASE?>";
	var URL_BASE_FILE = "<?= URL_BASE_FILE?>";
	var TAM_PAGINA=25;
	var CARPETA = "<?= site_url("cpp/productos")?>";
	var LINK_GENERICO = "<?= site_url("genericos")?>";
	
	
	Ext.onReady(function(){
	
	Ext.QuickTips.init();
	
	
        <?php
        // llamo al paginador
        include_once(PATH_BASE."js/paginador.js");
        include_once(PATH_BASE."js/CheckColumn.js");
//        include_once(PATH_BASE."js/RadioColumn.js");
        ?>

	
	// defincion de la casila de busqueda para filtrar registros
	<?php
	include_once(PATH_BASE."js/ext-3.3.0/Ext.ux.grid.Search/Ext.ux.grid.Search.js");
	?>  
		
	// Datos para la grilla de usuarios
	<?php
	include_once("grilla.js");
	include_once("alta_producto.js");
	?>
		
	
	})
	</script>	
<? else: ?>
	<h2>Su usuario no tiene permisos para usar este Modulo</h2>
<? endif; ?>
<div id="grillita"></div>