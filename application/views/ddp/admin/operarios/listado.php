<? 
$permiso_listar = $permiso['Listar'];
$permiso_alta = $permiso['Alta'];
$permiso_eliminar = $permiso['Baja'];
$permiso_modificar = $permiso['Modificacion'];
if ($permiso_listar):
?>
<!--	<link rel="stylesheet" type="text/css" href="<?=URL_BASE.'js/ext-3.3.0/examples/ux/fileuploadfield/css/fileuploadfield.css'?>"/>	-->
        <link rel="stylesheet" type="text/css" href="<?=URL_BASE.'js/ext-3.3.0/SuperBoxSelect/superboxselect.css'?>"/>
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
	var CARPETA = "<?= site_url("ddp/admin_operarios")?>";
	var LINK_GENERICO = "<?= site_url("genericos")?>";
	
	
	
	Ext.onReady(function(){
	
	Ext.QuickTips.init();
	
	<?php
	include_once(PATH_BASE."js/CheckColumn.js");
	include_once(PATH_BASE."js/ext-3.3.0/Ext.ux.grid.Search/Ext.ux.grid.Search.js");
        include_once(PATH_BASE."js/ext-3.3.0/SuperBoxSelect/SuperBoxSelect.js");
        include_once(PATH_BASE."js/paginador.js");
	?>  
	
	<?php
	include_once("altaOperario.js");
	?>	
                

		
	<?php
	include_once("grilla.js");
	?>
		
	
	})
	</script>	
<? else: ?>
	<h2>No tiene permisos para usar este Modulo</h2>
<? endif; ?>
<div id="grillita"></div>