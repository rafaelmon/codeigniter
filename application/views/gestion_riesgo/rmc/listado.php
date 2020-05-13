<? 
$permiso_listar = $permiso['Listar'];
$permiso_alta = $permiso['Alta'];
$permiso_eliminar = $permiso['Baja'];
$permiso_modificar = $permiso['Modificacion'];
$permiso_btn_tareas = $btn_tareas;
$permiso_btn_gr = $btn_gr;
$min_date=date("Y-m-d");

if ($permiso_listar):
?>
        <link rel="stylesheet" type="text/css" href="<?=URL_BASE.'js/ext-3.3.0/SuperBoxSelect/superboxselect.css'?>"/>	
        <link rel="stylesheet" type="text/css" href="<?=URL_BASE.'js/ext-3.3.0/examples/ux/fileuploadfield/css/fileuploadfield.css'?>"/>
	<script type="text/javascript">
	
	<?php
        include_once(PATH_BASE."js/ext-3.3.0/SuperBoxSelect/SuperBoxSelect.js");
         include_once(PATH_BASE."js/ext-3.3.0/examples/ux/fileuploadfield/FileUploadField.js");
	 ?>
	
	// Global vars
	// variables par alos tareas
	var permiso_alta = <?php echo $permiso_alta;?>;
	var permiso_eliminar = <?php echo $permiso_eliminar;?>;
	var permiso_modificar = <?php echo $permiso_modificar;?>;
	var permiso_btn_tareas = <?php echo $permiso_btn_tareas;?>;
	var permiso_btn_gr = <?php echo $permiso_btn_gr;?>;
	var permiso_btn_upload = true;
	var MINDATE = <?php echo "'".$min_date."'";?>;
	var MAXDATE_FILTRO = <?php echo "'".$min_date."'";?>;
        
	var URL_BASE = "<?= URL_BASE?>";
	var URL_BASE_FILE = "<?= URL_BASE_FILE?>";
	var TAM_PAGINA=25;
	var CARPETA = "<?= site_url("gestion_riesgo/rmc")?>";
        var CARPETA_UPLOAD = "<?= site_url("archivos")?>";
	var LINK_GENERICO = "<?= site_url("genericos")?>";
        var URL_BASE_SITIO = "<?php // echo URL_BASE_SITIO;?>";
        
        var habilitadaCheck;
        var rmcBuscador;
	
        //grilla
        var rmcDataStore;
        var rmcPaginador;
        var botonesRmcAction;
        var rmcColumnModel;
        var rmcGridPanel;
        var rmcPanel;
        
        //altaRmc
        var omcAltaCreateForm;
        var descAltaRmcTextfield;
        var empresasAltaRmcDS;
        var empresaAltaRmcCombo;
        var sectorAltaRmcDS;
        var sectorAltaRmcCombo;
        var sectorAltaRmcFieldSet;
        var rmcAltaCreateForm;
        var rmcAltaCreateWindow;
        
        //setCrit
        var criticidadRmcRadios;
        var inv1RmcDS;
        var inv1RmcCombo;
        var inv2RmcDS;
        var inv2RmcCombo;
        var invRmcFieldSet;
        var rmcSetCritCreateForm;
        var rmcSetCritCreateWindow;
        
        //grilla Tareas
	var tareasListingWindow;
	var rmcTareasListingGridPanel;
	var tareasColumnModel;
	var rmcTareaCriticidadRadios;
        var clasificacionesRiFiltro;
	var ESTACIO='&emsp;';
	
        //GrillaArchivos
        var rmcArchivosDataStore;
        var rmcArchivosPanel;
        
        //uploadArchivos
        var rmcArchivoTituloField;
        var rmcArchivoDescField;
        var archivosRiCreateForm;
        var archivosRiCreateWindow;
        
        //Panel
        var rmcTab_1;
        var rmcTab_2;
        var rmcTabs_panel;
	
	Ext.onReady(function(){
	
	Ext.QuickTips.init();
	
	<?php
//	include_once(PATH_BASE."js/CheckColumn.js");
	?>

	// defincion de la casila de busqueda para filtrar registros
	<?php
	include_once(PATH_BASE."js/ext-3.3.0/Ext.ux.grid.Search/Ext.ux.grid.Search.js");
	?>  
	<?php
        // llamo al paginador
        include_once(PATH_BASE."js/paginador.js");
        ?>
		
	<?php
        include_once("altaRmc.js");
	include_once("setCriticidad.js");
	include_once("altaTareaRmc.js");
	include_once("grilla.js");
	include_once("grillaTareas.js");
	include_once("grillaArchivos.js");
        include_once("uploadArchivo.js");
	include_once("panel.js");
	?>
		
	})
	</script>	
<? else: ?>
	<h2>Su usuario no tiene tareas para usar este Modulo</h2>
<? endif; ?>
<div id="grillita"></div>