<? 
$permiso_listar = $permiso['Listar'];
$permiso_alta = $permiso['Alta'];
$permiso_eliminar = $permiso['Baja'];
$permiso_modificar = $permiso['Modificacion'];
$permiso_gr = $u_gr;
$rol_editor=$roles['Editor'];
$rol_publicador=$roles['Publicador'];
$usuario=$roles['id_usuario'];

if ($permiso_listar):
?>
	<link rel="stylesheet" type="text/css" href="<?=URL_BASE.'js/ext-3.3.0/examples/ux/fileuploadfield/css/fileuploadfield.css'?>"/>
<!--	<link rel="stylesheet" type="text/css" href="//<?=URL_BASE.'js/ext-3.3.0/UploadDialog/css/Ext.ux.UploadDialog.css'?>"/>	-->
	<script type="text/javascript">
	
	<?php
	 include_once(PATH_BASE."js/ext-3.3.0/examples/ux/fileuploadfield/FileUploadField.js");
//	 include_once(PATH_BASE."js/ext-3.3.0/UploadDialog/Ext.ux.UploadDialog.js");
	 ?>
             
	// Global vars
	// variables par alos permisos
	var permiso_alta = <?php echo $permiso_alta;?>;
	var permiso_eliminar = <?php echo $permiso_eliminar;?>;
	var permiso_modificar = <?php echo $permiso_modificar;?>;
        var permiso_gr = <?php echo $permiso_gr;?>;
        var rol_editor = <?php echo $rol_editor;?>;
        var rol_public = <?php echo $rol_publicador;?>;
        var usuarioId = <?php echo $usuario;?>;
	
	var URL_BASE = "<?= URL_BASE?>";
	var URL_BASE_FILE = "<?= URL_BASE_FILE?>";
        var URL_BASE_SITIO = "<?php echo URL_BASE_SITIO;?>";
//	var TAM_PAGINA="<?php echo TAM_PAGINA;?>";
	var TAM_PAGINA=25;
	var CARPETA = "<?= site_url("dms/workflow")?>";
	var CARPETAGESTIONES = "<?= site_url("dms/gestiones")?>";
//	var CARPETAOBS = "<?= site_url("dms/observaciones")?>";
	var LINK_GENERICO = "<?= site_url("admin/genericos")?>";
        
        //GrillaSuperior
        var workflowDataStore;
        var workflowColumnModel;
        var workflowEditorGrid;
        var superiorPanel;
        
        //GrillaInferior
        var gestionesDataStore;
        var obsDataStore;
        var gestionesColumnModel;
        var obsColumnModel;
        var gestionesGrid;
        var obsGrid;
        var inferiorPanel;
        
        //Panel
        var workflowPanel;
        var altura;
        
        //abmArchivos
        var archivosDmsCreateWindow;
        var archivosDmsCreateForm;
        
        //Rechazar
        var rechazarDocumentoCreateWindow;
        
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
//	include_once("altaDocumento2.js");
	include_once("GrillaSuperior.js");
	include_once("grabarObs.js");
	include_once("GrillaInferior.js");
	include_once("abmArchivos.js");
	include_once("delegar.js");
	include_once("rechazar.js");
	include_once("panel.js");
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