<? 
$permiso_listar = $permiso['Listar'];
$permiso_alta = $permiso['Alta'];
$permiso_eliminar = $permiso['Baja'];
$permiso_modificar = $permiso['Modificacion'];
$permiso_btn = $btn;
$min_date=date("Y-m-d");
$hoy= date("Y-m-d");
//$hoy= date("Y-m-d",  strtotime($hoy.' + 1 days'));
if ($permiso_listar):
?>
<!--	<link rel="stylesheet" type="text/css" href="<?=URL_BASE.'js/ext-3.3.0/examples/ux/fileuploadfield/css/fileuploadfield.css'?>"/>	-->
        <link rel="stylesheet" type="text/css" href="<?=URL_BASE.'js/ext-3.3.0/SuperBoxSelect/superboxselect.css'?>"/>
        <link rel="stylesheet" type="text/css" href="<?=URL_BASE.'js/ext-3.3.0/examples/ux/css/Spinner.css'?>"/>
	<script type="text/javascript">
	
	<?php
	 include_once(PATH_BASE."js/FileUploadField.js");
	 ?>
	
	// Global vars
	// variables par alos tareas
	var permiso_alta = <?php echo $permiso_alta;?>;
	var permiso_eliminar = <?php echo $permiso_eliminar;?>;
	var permiso_modificar = <?php echo $permiso_modificar;?>;
	var permiso_btn = <?php echo $permiso_btn;?>;
	var MINDATE = <?php echo "'".$min_date."'";?>;
	var MAXDATE = <?php echo "'".$hoy."'";?>;
	
	var URL_BASE = "<?= URL_BASE?>";
	var URL_BASE_FILE = "<?= URL_BASE_FILE?>";
	var TAM_PAGINA=25;
	var CARPETA = "<?= site_url("gestion_riesgo/auditoria")?>";
	var LINK_GENERICO = "<?= site_url("genericos")?>";
        var URL_BASE_SITIO = "<?php // echo URL_BASE_SITIO;?>";
        
        var habilitadaCheck;
        var buscador;
	
        //grilla
       
        //
        
        //altaAuditoria
        var auditoriaCreateForm;
        
        
	var tareasListingWindow;
	var tareasColumnModel;
	var ESTACIO='&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;';
	
	var auditoriaPanel;
	var tareasPanel;
        
	
	Ext.onReady(function(){
	
	Ext.QuickTips.init();
	
	<?php
//	include_once(PATH_BASE."js/CheckColumn.js");
	?>
	
	// Ventana y funciones Alta usuario
	<?php
	include_once("altaAudit.js");
	include_once("altaHallazgoAudit.js");
	include_once("altaTareaAudit.js");
	?>		

	// defincion de la casila de busqueda para filtrar registros
	<?php
	include_once(PATH_BASE."js/ext-3.3.0/Ext.ux.grid.Search/Ext.ux.grid.Search.js");
        include_once(PATH_BASE."js/ext-3.3.0/SuperBoxSelect/SuperBoxSelect.js");
        include_once(PATH_BASE."js/ext-3.3.0/examples/ux/Spinner.js");
	include_once(PATH_BASE."js/ext-3.3.0/examples/ux/SpinnerField.js");
	?>  
	<?php
        // llamo al paginador
        include_once(PATH_BASE."js/paginador.js");
        ?>
		
	<?php
	include_once("grillaAudit.js");
	include_once("grillaHallazgos.js");
	include_once("grillaTareas.js");
	include_once("panelAudit.js");
	?>
		
	})
	</script>	
<? else: ?>
	<h2>Su usuario no tiene tareas para usar este Modulo</h2>
<? endif; ?>
<div id="grillita"></div>