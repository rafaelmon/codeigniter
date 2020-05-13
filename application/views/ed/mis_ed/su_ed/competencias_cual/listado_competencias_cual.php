<? 
$permiso_listar = $permiso['Listar'];
$permiso_modificar = $permiso['Modificacion'];
if ($permiso_listar):
?>
	<script type="text/javascript">
	// Global vars
	// variables par alos permisos
        var permiso_listar = <?php echo $permiso_listar;?>;
	var permiso_modificar = <?php echo $permiso_modificar;?>;
	
	var URL_BASE = "<?= URL_BASE?>";
	var URL_BASE_FILE = "<?= URL_BASE_FILE?>";
	var TAM_PAGINA=25;
	var CARPETA = "<?= site_url("ed/su_ed")?>";
	var CARPETA_MIS_EEDD = "<?= site_url("ed/mis_ed")?>";
	var LINK_GENERICO = "<?= site_url("genericos")?>";
        var ID_ED = "<?=$id_ed?>";
	
	
	Ext.onReady(function(){
	
	Ext.QuickTips.init();
	
	
        <?php
        // llamo al paginador
        include_once(PATH_BASE."js/paginador.js");
        ?>

	
	// Ventana y funciones Alta usuario
	<?php
//	include_once("altaED.js");
	?>		

	// defincion de la casila de busqueda para filtrar registros
	<?php
	include_once(PATH_BASE."js/ext-3.3.0/Ext.ux.grid.Search/Ext.ux.grid.Search.js");
        include_once("RadioColumn_suEd.js");
	?>  
		
	// Datos para la grilla de usuarios
	<?php
	include_once("grilla_competencias_cual.js");
	?>
		
	
	})
	</script>	
<? else: ?>
	<h2>Su usuario no tiene permisos para usar este Modulo</h2>
<? endif; ?>
<div id="grillita"></div>
<style>.x-grid-cell-inner {
white-space: normal;
}
.enc_r1{
    color: blue;
    font-family: verdana;
    font-size: 90%;
}
        </style>