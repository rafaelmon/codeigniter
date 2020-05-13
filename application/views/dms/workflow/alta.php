<? 
//echo "<pre>".print_r($permiso,true)."</pre>";
//echo "<pre>".print_r($roles,true)."</pre>";
//echo "<pre>".print_r($documento,true)."</pre>";
//echo "<pre>".print_r($documento['id_documento'],true)."</pre>";
$permiso_listar = $permiso['Listar'];
$permiso_alta = $permiso['Alta'];
$permiso_eliminar = $permiso['Baja'];
$permiso_modificar = $permiso['Modificacion'];

$rol_editor=$roles['Editor'];

if (isset($documento))
{
    $edita=1;
    $id_doc=$documento;
    
}
else
{
    $edita=0;
    $id_doc=0;
    
}
    
//echo $edita;

if ($permiso_listar):
?>
	<link rel="stylesheet" type="text/css" href="<?=URL_BASE.'js/ext-3.3.0/examples/ux/fileuploadfield/css/fileuploadfield.css'?>"/>
	<link rel="stylesheet" type="text/css" href="<?=URL_BASE.'js/ext-3.3.0/examples/ux/css/Spinner.css'?>"/>
<!--	<link rel="stylesheet" type="text/css" href="//<?=URL_BASE.'js/ext-3.3.0/UploadDialog/css/Ext.ux.UploadDialog.css'?>"/>	-->
	<link rel="stylesheet" type="text/css" href="<?=URL_BASE.'js/ext-3.3.0/SuperBoxSelect/superboxselect.css'?>"/>	
	<script type="text/javascript">
	
	<?php
	 include_once(PATH_BASE."js/ext-3.3.0/examples/ux/fileuploadfield/FileUploadField.js");
	 include_once(PATH_BASE."js/ext-3.3.0/examples/ux/Spinner.js");
	 include_once(PATH_BASE."js/ext-3.3.0/examples/ux/SpinnerField.js");
	 include_once(PATH_BASE."js/ext-3.3.0/SuperBoxSelect/SuperBoxSelect.js");
//	 include_once(PATH_BASE."js/ext-3.3.0/UploadDialog/Ext.ux.UploadDialog.js");
	 ?>
             
	// Global vars
	// variables par alos permisos
	var permiso_alta = <?php echo $permiso_alta;?>;
	var permiso_eliminar = <?php echo $permiso_eliminar;?>;
	var permiso_modificar = <?php echo $permiso_modificar;?>;
        
	var rol_editor = <?php echo $rol_editor;?>;
        var edita=<?php echo $edita;?>;
        var idDoc=<?php echo $id_doc;?>;
	
	var URL_BASE = "<?= URL_BASE?>";
	var URL_BASE_FILE = "<?= URL_BASE_FILE?>";
        var URL_BASE_SITIO = "<?php echo URL_BASE_SITIO;?>";
	var TAM_PAGINA="<?php echo TAM_PAGINA;?>";
	var CARPETA = "<?= site_url("dms/workflow")?>";
	var CARPETAGESTIONES = "<?= site_url("dms/gestiones")?>";
	var LINK_GENERICO = "<?= site_url("admin/genericos")?>";
        
        //GrillaSuperior
        var workflowDataStore;
        var workflowColumnModel;
        var workflowEditorGrid;
        var superiorPanel;
        
        //GrillaInferior
        var gestionesDataStore;
        var gestionesColumnModel;
        var gestionesGrid;
        var inferiorPanel;
        
        //Panel
        var workflowPanel;
        var altura;
        
        
        
        	Ext.onReady(function(){
	
	Ext.QuickTips.init();
	
	<?php
	// definicion del cuadro de dialogo de uploads
//	 include_once("uploadAvatar.js");
	?> 

	
	// defincion de el checkbox para la grilla	
	
	<?php
	include_once(PATH_BASE."js/CheckColumn.js");
	?>
	
	// defincion de la casilla de busqueda para filtrar registros
	<?php
	include_once(PATH_BASE."js/ext-3.3.0/Ext.ux.grid.Search/Ext.ux.grid.Search.js");
	?>  
        	

	
	<?php
//	include_once("abmArchivos2.js");
	include_once("altaDocumento.js");
//	include_once("modificaTramite.js");
//	include_once("delegarTramite.js");
	?>
		
	// Datos para la ventana de permisos 
	
	<?php
//	include_once("usuarioSeccion.js");
	?>	
	})
	</script>	
<? else: ?>
	<h2>Su usuario no tiene permisos para usar este Modulo</h2>
<? endif; ?>
<div id="grillita"></div>