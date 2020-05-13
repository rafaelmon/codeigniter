<? 
$permiso_listar = $permiso['Listar'];
$permiso_modificar = $permiso['Modificacion'];
if ($permiso_listar):
?>
	<script type="text/javascript">
        <?php
	 include_once(PATH_BASE."js/ext-3.3.0/examples/ux/fileuploadfield/FileUploadField.js");
//         include_once(PATH_BASE."js/ext-3.3.0/examples/ux/BufferView.js");
	 ?>
	// Global vars for listado_plan
	// variables par alos permisos
	var permiso_listar = <?php echo $permiso_listar;?>;
	var permiso_modificar = <?php echo $permiso_modificar;?>;
	
	var URL_BASE = "<?= URL_BASE?>";
	var URL_BASE_FILE = "<?= URL_BASE_FILE?>";
	var TAM_PAGINA=25;
	var CARPETA = "<?= site_url("ed/su_ed")?>";
	var LINK_GENERICO = "<?= site_url("genericos")?>";
        var ID_ED = "<?=$id_ed?>";
	
	Ext.onReady(function(){
	
	Ext.QuickTips.init();
        ANCHO=(Ext.getCmp('browser').getSize().width)/2;
        
	// Datos para la grilla de usuarios
	
	<?php
	include_once("form_fortalezas.js");
	include_once("grilla_fortalezas.js");
	include_once("form_aam.js");
	include_once("grilla_aam.js");
	include_once("panel.js");
	?>
		
	
	})
	</script>
        
<? else: ?>
	<h2>Su usuario no tiene permisos para usar este Modulo</h2>
<? endif; ?>
<div id="grillita_fortalezas_aam"></div>