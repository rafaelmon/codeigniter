<? 
$permiso_listar = $permiso['Listar'];
$permiso_alta = $permiso['Alta'];
$permiso_eliminar = $permiso['Baja'];
$permiso_modificar = $permiso['Modificacion'];
$btn_nuevaTop = $btn_nuevaTop;
$min_date=date("Y-m-d");
$id_supervisor = $id_supervisor;
$id_aprobador = $id_aprobador;

if ($permiso_listar):
?>
<!--	<link rel="stylesheet" type="text/css" href="<?=URL_BASE.'js/ext-3.3.0/examples/ux/fileuploadfield/css/fileuploadfield.css'?>"/>	-->
	<link rel="stylesheet" type="text/css" href="<?=URL_BASE.'js/ext-3.3.0/examples/ux/css/GroupSummary.css'?>"/>	
        <!--<link rel="stylesheet" type="text/css" href="<?=URL_BASE.'js/ext-3.3.0/SuperBoxSelect/superboxselect.css'?>"/>-->
        <link rel="stylesheet" type="text/css" href="<?=URL_BASE.'js/ext-3.3.0/examples/ux/css/Spinner.css'?>"/>	
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
        var id_supervisor = <?php echo $id_supervisor;?>;
        var id_aprobador = <?php echo $id_aprobador;?>;
	
	var URL_BASE = "<?= URL_BASE?>";
	var URL_BASE_FILE = "<?= URL_BASE_FILE?>";
	var TAM_PAGINA=<?=TAM_PAGINA?>;
	var CARPETA = "<?= site_url("ddp/mitop")?>";
	var CARPETA_PDF = "<?= site_url("ddp/top")?>";
	var CARPETA_ADMIN_TOPS = "<?= site_url("ddp/admin_tops")?>";
	var LINK_GENERICO = "<?= site_url("genericos")?>";
	
	var BTN_NUEVATOP = <?=$btn_nuevaTop?>;

//         console.log(TOP);
        var ID_DIM;
        var DIM;
        var SUM_DIM=0;
        var MINDATE = <?php echo "'".$min_date."'";?>;
        
        //objetivos
        var miTopObjetivosDS;
        var miTopObjetivosColumnModel;
        var miTopObjetivosGridPanel;
        
        //dimensiones
        var miTopDimensionesDS;
        var miTopDimensionesColumnModel;
        var miTopDimensionesGridPanel;
        
        var paginadorHistorial;
        var miTopHistorialPanel;
        var miTopDdpHistorialObjetivoDataStore;
        var miTopDdpHistorialObjetivoColumnModel;
        var miTopDdpHistorialObjetivoGrid;
        
	Ext.onReady(function(){
	
	Ext.QuickTips.init();
	
	<?php
	include_once(PATH_BASE."js/CheckColumn.js");
	include_once(PATH_BASE."js/ext-3.3.0/Ext.ux.grid.Search/Ext.ux.grid.Search.js");
        include_once(PATH_BASE."js/ext-3.3.0/SuperBoxSelect/SuperBoxSelect.js");
        include_once(PATH_BASE."js/ext-3.3.0/examples/ux/GroupSummary.js");
        include_once(PATH_BASE."js/ext-3.3.0/examples/ux/Spinner.js");
	include_once(PATH_BASE."js/ext-3.3.0/examples/ux/SpinnerField.js");
        include_once(PATH_BASE."js/paginador.js");
	?>  
	

		
	<?php
//	include_once("altaObj.js");
//	include_once("objetivos.js");
//	include_once("editObjEv1.js");
//	include_once("historial.js");
////	include_once("dim.js");
	include_once("altaTOP.js");
//	include_once("evaluacion1.js");
	include_once("grillaMisTops.js");
	include_once("grillaMisTopsAud.js");
	include_once("panelMisTops.js");
	?>
	})
	</script>	
<? else: ?>
	<h2>No tiene permisos para usar este Modulo</h2>
<? endif; ?>
<div id="grillita"></div>