<? 
//echo "<pre>".print_r(get_defined_vars(),true)."</pre>";
$permiso_listar = $permiso['Listar'];
$permiso_alta = $permiso['Alta'];
$permiso_eliminar = $permiso['Baja'];
$permiso_modificar = $permiso['Modificacion'];

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
	
	
	var URL_BASE = "<?= URL_BASE?>";
	var URL_BASE_FILE = "<?= URL_BASE_FILE?>";
	var TAM_PAGINA=<?=TAM_PAGINA?>;
	var CARPETA = "<?= site_url("ddp/tops_apr")?>";
	var LINK_GENERICO = "<?= site_url("genericos")?>";
        var TOP = <?php 
                    if ($top!='0')
                    {
                        echo "{";
                        foreach ($top as $clave=>$valor)
                        {
                            $obj=$clave.":".$valor.",";
                            echo $obj;
                        }
                        echo "};";
                    }
                    else
                        echo "0";
                        
                ?>;
//                    console.log(TOP);
	var USR = "<?=$usuario?>";
	
        //objetivos
        var ddpTopAprObjetivosDS;
        var ddpTopAprBotonesObjAction;
        var ddpTopAprObjetivosColumnModel;
        var ddpTopAprObjetivosGridPanel;
        
        //historial
        var ddpHistorialObjetivoDataStore;
        var ddpHistorialObjetivoColumnModel;
        var ddpHistorialObjetivoGrid;
        
        //panel
        var ddpTopAprTab1;
        var ddpTopAprTab2;
        var ddpTopAprTab3;
        var ddpTopAprObjetivosTabPanel;
        var ddpTopAprObjetivosPanel;
        var ddpTopAprBotonesPanel;
//        var ddpTopAprWestPanel;
        var ddpTopAprHistorialPanel;
        var ddpTopAprPanel;
        var DISABLED=true;
        var SUM_DIM=0;
        
              
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
	include_once("objetivos.js");
        include_once("evaluacion.js");
	include_once("historial.js");
	include_once("panelTop.js");
//xxx        include_once("editObjEv1.js");
//xx	include_once("dim.js");
//xx	include_once("periodosCombo.js");
//xx	include_once("usuariosCombo.js");
//xx	include_once("editObj.js");
	?>
		
	})
	</script>	
<? else: ?>
	<h2>No tiene permisos para usar este Modulo</h2>
<? endif; ?>
<div id="panel_top"></div>