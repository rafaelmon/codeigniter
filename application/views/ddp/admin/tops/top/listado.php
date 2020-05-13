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
	var LINK_GENERICO = "<?= site_url("genericos")?>";
	//var TOP = "<?=$id_top?>";
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
	var USR = "<?=$usuario?>";
	
        //dimensions
//        var dimensionesDS;
//        var dimensionesColumnModel;
//        var dimensionesGridPanel;
//        var dimensionesPanel;
        
        //objetivos
        var objetivosDS;
        var botonesObjAction;
        var objetivosGridPanel;
        
        //historial
        var ddpHistorialObjetivoDataStore;
        var ddpHistorialObjetivoColumnModel;
        var ddpHistorialObjetivoGrid;
        
        //panel
        var tab1;
        var tab2;
        var tab3;
        var objetivosTabPanel;
        var objetivosPanel;
        var botonesPanel;
        var westPanel;
        var historialPanel;
        var topPanel;
        var DISABLED=true;
        var SUM_DIM=0;
        
        //edit
        var ddpObjetivoEmpresaField;
        var ddpObjetivoPersonalField;
        var ddpIndicadorObjetivoField;
        var ddpFuenteDatosObjetivoField;
        var ddpValorRefObjetivoField;
        var ddpPesoObjetivoNumberField;
        var ddpObjetivoEditForm;
        var ddpObjetivoEditWindow;
              
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
//	include_once("topAdmin_dim.js");
	include_once("topAdmin_obj.js");
	include_once("topAdmin_hist.js");
//	include_once("periodosCombo.js");
//	include_once("usuariosCombo.js");
	include_once("topAdmin_eval1.js");
	include_once("topAdmin_panel.js");
//	include_once("editObj.js");
	?>
		
	})
	</script>	
<? else: ?>
	<h2>No tiene permisos para usar este Modulo</h2>
<? endif; ?>
<div id="grillita"></div>