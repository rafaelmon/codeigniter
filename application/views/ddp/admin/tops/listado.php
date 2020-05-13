<? 
//echo "<pre>".print_r(get_defined_vars(),true)."</pre>";
$permiso_listar = $permiso['Listar'];
$permiso_alta = $permiso['Alta'];
$permiso_eliminar = $permiso['Baja'];
$permiso_modificar = $permiso['Modificacion'];

if ($permiso_listar):
?>
<!--	<link rel="stylesheet" type="text/css" href="<?=URL_BASE.'js/ext-3.3.0/examples/ux/fileuploadfield/css/fileuploadfield.css'?>"/>	-->
<!--	<link rel="stylesheet" type="text/css" href="<?=URL_BASE.'js/ext-3.3.0/examples/ux/css/GroupSummary.css'?>"/>	
        <link rel="stylesheet" type="text/css" href="<?=URL_BASE.'js/ext-3.3.0/SuperBoxSelect/superboxselect.css'?>"/>
        <link rel="stylesheet" type="text/css" href="<?=URL_BASE.'js/ext-3.3.0/examples/ux/css/Spinner.css'?>"/>	-->
        <style type="text/css">
            .botones-panel{
                     padding:5px;
            }
	</style>
	<script type="text/javascript">
	
	<?php
//	 include_once(PATH_BASE."js/ext-3.3.0/examples/ux/fileuploadfield/FileUploadField.js");
	 ?>
	
	// Global vars
	// variables par alos permisos
	var permiso_alta = <?php echo $permiso_alta;?>;
	var permiso_eliminar = <?php echo $permiso_eliminar;?>;
	var permiso_modificar = <?php echo $permiso_modificar;?>;
	var permiso_modificar_obj = <?php echo $permiso_modificar;?>;
	
	
	var URL_BASE = "<?= URL_BASE?>";
	var URL_BASE_FILE = "<?= URL_BASE_FILE?>";
	var TAM_PAGINA=<?=TAM_PAGINA?>;
	var CARPETA = "<?= site_url("ddp/admin_tops")?>";
        var CARPETA_EXCEL = "<?= site_url("ddp/excel_top")?>";
        var CARPETA_MI_TOP = "<?= site_url("ddp/mitop")?>";
	var LINK_GENERICO = "<?= site_url("genericos")?>";
	var PERIODO = "<?=$periodo['periodo']?>";
      
        
       
              
	Ext.onReady(function(){
	Ext.QuickTips.init();
	
	<?php
            include_once(PATH_BASE."js/CheckColumn.js");
            include_once(PATH_BASE."js/ext-3.3.0/Ext.ux.grid.Search/Ext.ux.grid.Search.js");
//        include_once(PATH_BASE."js/ext-3.3.0/SuperBoxSelect/SuperBoxSelect.js");
//        include_once(PATH_BASE."js/ext-3.3.0/examples/ux/GroupSummary.js");
//        include_once(PATH_BASE."js/ext-3.3.0/examples/ux/Spinner.js");
//	include_once(PATH_BASE."js/ext-3.3.0/examples/ux/SpinnerField.js");
            include_once(PATH_BASE."js/paginador.js");
	?>  
	

		
	<?php
            include_once("editarSupervisor.js");
            include_once("editarAprobador.js");
//            include_once("editarTOP.js");
            include_once("grillaTopsAdmin.js");
            include_once("grillaTopsAdmin_aud.js");
            include_once("grillaTopsAdmin_panel.js");
	?>
		
	})
	</script>	
<? else: ?>
	<h2>No tiene permisos para usar este Modulo</h2>
<? endif; ?>
<div id="grillita"></div>