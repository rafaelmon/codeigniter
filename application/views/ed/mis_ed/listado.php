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
	var CARPETA = "<?= site_url("ed/mis_ed")?>";
        var CARPETA_MI_ED = "<?= site_url("ed/mi_ed")?>";
        var CARPETA_SU_ED = "<?= site_url("ed/su_ed")?>";
        var CARPETA_PDF = "<?= site_url("ed/pdf_ed")?>";
	var LINK_GENERICO = "<?= site_url("genericos")?>";
	
	
	Ext.onReady(function(){
	
	Ext.QuickTips.init();
	
	<?php
	include_once(PATH_BASE."js/sesionControl.js");
	?>
//        <?php
        // llamo al paginador
//        include_once(PATH_BASE."js/paginador.js");
        ?>

	
	// Ventana y funciones Alta usuario
	<?php
//	include_once("altaED.js");
	?>		

	// defincion de la casila de busqueda para filtrar registros
	<?php
	include_once(PATH_BASE."js/ext-3.3.0/Ext.ux.grid.Search/Ext.ux.grid.Search.js");
        
	?>  
		
	// Datos para la grilla de usuarios
	<?php
	include_once("cerrarMiEdConObs.js");
	include_once("grilla.js");
	?>
		
	
	})
	</script>	
<? else: ?>
	<h2>Su usuario no tiene permisos para usar este Modulo</h2>
<? endif; ?>
<div id="grillita"></div>