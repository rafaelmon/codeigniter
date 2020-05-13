<? 
$permiso_listar = $permiso['Listar'];
$permiso_alta = $permiso['Alta'];
$permiso_eliminar = $permiso['Baja'];
$permiso_modificar = $permiso['Modificacion'];
$min_date=date("Y-m-d");
$id_supervisor = $id_supervisor;
$id_aprobador = $id_aprobador;
$supervisor = $supervisor;


if ($permiso_listar):
?>
	<link rel="stylesheet" type="text/css" href="<?=URL_BASE.'js/ext-3.3.0/examples/ux/css/GroupSummary.css'?>"/>	
        <!--<link rel="stylesheet" type="text/css" href="<?=URL_BASE.'js/ext-3.3.0/SuperBoxSelect/superboxselect.css'?>"/>-->
        <link rel="stylesheet" type="text/css" href="<?=URL_BASE.'js/ext-3.3.0/examples/ux/css/Spinner.css'?>"/>	
        <style type="text/css">
	.botones-panel{
		 padding:5px;
	}
	</style>
	<script type="text/javascript">
	
	// Global vars
	// variables par alos permisos
	var permiso_alta = <?php echo $permiso_alta;?>;
	var permiso_eliminar = <?php echo $permiso_eliminar;?>;
	var permiso_modificar = <?php echo $permiso_modificar;?>;
	var permiso_modificar_obj = <?php echo $permiso_modificar;?>;
	var id_supervisor = <?php echo $id_supervisor;?>;
        var id_aprobador = <?php echo $id_aprobador;?>;
        var supervisor = "<?php echo $supervisor;?>";
        
	var URL_BASE = "<?= URL_BASE?>";
	var URL_BASE_FILE = "<?= URL_BASE_FILE?>";
	var TAM_PAGINA=<?=TAM_PAGINA?>;
	var CARPETA = "<?= site_url("ddp/mitop")?>";
	var CARPETA_PDF = "<?= site_url("ddp/top")?>";
	var LINK_GENERICO = "<?= site_url("genericos")?>";
	
	//
	var TOP = <?php 
                    echo "{";
                    $n=0;
                    foreach ($top as $clave=>$valor)
                    {
                        if ($n==1)
                            echo ",";
                        if (substr($clave,0,3)=="txt")
                            $obj=$clave.":'".$valor;
                        else    
                            $obj=$clave.":".$valor;
                        echo $obj;
                        $n=1;
                    }
                    echo "}";
                ?>;
        var ID_TOP = <?=$id_top?>;
//         console.log(ID_TOP);
        var MINDATE = <?php echo "'".$min_date."'";?>;
        
        //objetivos
        var miTopObjetivosDS;
        var miTopObjetivosColumnModel;
        var miTopObjetivosGridPanel;
        
        var paginadorHistorial;
        var miTopHistorialPanel;
        var miTopDdpHistorialObjetivoDataStore;
        var miTopDdpHistorialObjetivoColumnModel;
        var miTopDdpHistorialObjetivoGrid;
        
	Ext.onReady(function(){
	
	Ext.QuickTips.init();
	
	<?php
	include_once(PATH_BASE."js/CheckColumn.js");
//	include_once(PATH_BASE."js/ext-3.3.0/Ext.ux.grid.Search/Ext.ux.grid.Search.js");
//        include_once(PATH_BASE."js/ext-3.3.0/SuperBoxSelect/SuperBoxSelect.js");
//        include_once(PATH_BASE."js/ext-3.3.0/examples/ux/GroupSummary.js");
//        include_once(PATH_BASE."js/ext-3.3.0/examples/ux/Spinner.js");
//	include_once(PATH_BASE."js/ext-3.3.0/examples/ux/SpinnerField.js");
//        include_once(PATH_BASE."js/paginador.js");
	?>  
	

		
	<?php
            include_once("altaObj.js");
            include_once("objetivos.js");
            include_once("editObjEv1.js");
            include_once("historial.js");
            include_once("evaluacion1.js");
            include_once("panelMiTop.js");
	?>
	})
	</script>	
<? else: ?>
	<h2>No tiene permisos para usar este Modulo</h2>
<? endif; ?>
<div id="grillita"></div>