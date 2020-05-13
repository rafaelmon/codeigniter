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
	<link rel="stylesheet" type="text/css" href="<?=URL_BASE.'js/ext-3.3.0/examples/ux/fileuploadfield/css/fileuploadfield.css'?>"/>	
	<link rel="stylesheet" type="text/css" href="<?=URL_BASE.'js/ext-3.3.0/examples/ux/treegrid/treegrid.css'?>"/>	
        
        
        <script type="text/javascript">
	
	<?php
	 include_once(PATH_BASE."js/FileUploadField.js");
	 ?>
	
	// Global vars
	// variables par alos tareas
	var permiso_alta = <?php echo $permiso_alta;?>;
	var permiso_eliminar = <?php echo $permiso_eliminar;?>;
	var permiso_modificar = <?php echo $permiso_modificar;?>;
	var permiso_btn_tareas = <?php echo $permiso_btn_tareas;?>;
	var permiso_btn_gr = <?php echo $permiso_btn_gr;?>;
	var MINDATE = <?php echo "'".$min_date."'";?>;
	
	var URL_BASE = "<?= URL_BASE?>";
	var URL_BASE_FILE = "<?= URL_BASE_FILE?>";
	var TAM_PAGINA=25;
	var CARPETA = "<?= site_url("gestion_riesgo/bc")?>";
	var LINK_GENERICO = "<?= site_url("genericos")?>";
        var URL_BASE_SITIO = "<?php // echo URL_BASE_SITIO;?>";
        
        var habilitadaCheck;
        var bcBuscador;
	
        //grilla
        var bcDataStore;
        var bcPaginador;
        var botonesBcAction;
        var bcColumnModel;
        var bcGridPanel;
        var bcPanel;
        
        //altaBc
        var omcAltaCreateForm;
        var descAltaBcTextfield;
        var empresasAltaBcDS;
        var empresaAltaBcCombo;
        var sectorAltaBcDS;
        var sectorAltaBcCombo;
        var sectorAltaBcFieldSet;
        var bcAltaCreateForm;
        var bcAltaCreateWindow;
        
        //RechazarBc
        var textoRechazarBcField;
        var rechazarBcCreateForm;
        var rechazarBcCreateWindow;
        
        
	var ESTACIO='&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;';
	
        
	
	Ext.onReady(function(){
	
	Ext.QuickTips.init();
	
	<?php
//	include_once(PATH_BASE."js/CheckColumn.js");
	?>

	// defincion de la casila de busqueda para filtrar registros
	<?php
	include_once(PATH_BASE."js/ext-3.3.0/Ext.ux.grid.Search/Ext.ux.grid.Search.js");
	include_once(PATH_BASE."js/ext-3.3.0/examples/ux/treegrid/TreeGridSorter.js");
	include_once(PATH_BASE."js/ext-3.3.0/examples/ux/treegrid/TreeGridColumnResizer.js");
	include_once(PATH_BASE."js/ext-3.3.0/examples/ux/treegrid/TreeGridNodeUI.js");
	include_once(PATH_BASE."js/ext-3.3.0/examples/ux/treegrid/TreeGridLoader.js");
	include_once(PATH_BASE."js/ext-3.3.0/examples/ux/treegrid/TreeGridColumns.js");
	include_once(PATH_BASE."js/ext-3.3.0/examples/ux/treegrid/TreeGrid.js");
	?>  
	<?php
        // llamo al paginador
        include_once(PATH_BASE."js/paginador.js");
        ?>
		
	<?php
        include_once("altaBc.js");
	include_once("rechazarBc.js");
	include_once("cancelarBc.js");
	include_once("grilla.js");
//	include_once("arbol.js");
//	include_once("grillaTareas.js");
	include_once("panel.js");
	?>
		
	})
	</script>	
<? else: ?>
	<h2>Su usuario no tiene tareas para usar este Modulo</h2>
<? endif; ?>
<div id="grillita"></div>