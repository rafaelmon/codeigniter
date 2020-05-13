<? 
$permiso_listar = $permiso['Listar'];
$permiso_alta = $permiso['Alta'];
$permiso_eliminar = $permiso['Baja'];
$permiso_modificar = $permiso['Modificacion'];
$min_date=date("Y-m-d");
$id_usuario=$usuario;

if ($permiso_listar):
?>
        <!--<link rel="stylesheet" type="text/css" href="<?=URL_BASE.'js/ext-3.3.0/examples/ux/css/MultiSelect.css'?>"/>-->
        <link rel="stylesheet" type="text/css" href="<?=URL_BASE.'js/ext-3.3.0/SuperBoxSelect/superboxselect.css'?>"/>	
	<script type="text/javascript">
	
	// Global vars
	// variables par alos permisos
	var permiso_alta = <?php echo $permiso_alta;?>;
	var permiso_eliminar = <?php echo $permiso_eliminar;?>;
	var permiso_modificar = <?php echo $permiso_modificar;?>;
	var usuario = <?php echo $usuario;?>;
	
	var TAM_PAGINA=25;
        var URL_BASE = "<?= URL_BASE?>";
	var CARPETA = "<?= site_url("cpp/repo_fallas")?>";
        var LINK_GENERICO = "<?= site_url("genericos")?>";
        var ALT_INF =(Ext.getBody().getSize().height)/3 - 40;
        var ANCH_INF =(Ext.getBody().getSize().width)/2;
        var MINDATE = <?php echo "'".$min_date."'";?>;
        
        //grilla_eventos
        var cppRepoFallasDataStore;
        var paginadorRepoFallas;
        var botonesCppAction;
        var cppRepoFallasBuscador;
        var arrayfiltroCriticidad;
        var arrayfiltroEstado;
        var cppFiltroCriticidad;
        var cppFiltroEstado;
        var cppRepoFallasColumnModel;
        var cppRepoFallasGridPanel;
        var paginadorRepoFallas;
        var clickBtnNuevoEvento;
        var clickBtnRepoFallasExcel;
        var cppRepoFallasPanel;
	
	Ext.onReady(function(){
	
	Ext.QuickTips.init();
	
	<?php
	include_once(PATH_BASE."js/CheckColumn.js");
//        include_once(PATH_BASE."js/ext-3.3.0/examples/ux/MultiSelect.js");
//        include_once(PATH_BASE."js/ext-3.3.0/examples/ux/ItemSelector.js");
        include_once(PATH_BASE."js/ext-3.3.0/SuperBoxSelect/SuperBoxSelect.js");
	include_once(PATH_BASE."js/ext-3.3.0/Ext.ux.grid.Search/Ext.ux.grid.Search.js");
//        include_once(PATH_BASE."js/paginador.js");
	?>
	
	<?php
	include_once("excel.js");
	include_once("grilla.js");
	include_once("filtros.js");
	include_once("panel.js");
	?>
		
	})
	</script>	
<? else: ?>
	<h2>Su usuario no tiene permisos para usar este Modulo</h2>
<? endif; ?>
<div id="cpp_repo_fallas"></div>
