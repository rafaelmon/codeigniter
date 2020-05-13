<? 
$permiso_listar = $permiso['Listar'];
$permiso_alta = $permiso['Alta'];
$permiso_eliminar = $permiso['Baja'];
$permiso_modificar = $permiso['Modificacion'];
$esCap=$cap;
$max_date = date ( 'Y-m-d');

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
	<!--<link rel="stylesheet" type="text/css" href="<?=URL_BASE.'js/ext-3.3.0/examples/ux/css/Spinner.css'?>"/>-->
	<!--link rel="stylesheet" type="text/css" href="//<?=URL_BASE.'js/ext-3.3.0/UploadDialog/css/Ext.ux.UploadDialog.css'?>"/>	-->
	<!--<link rel="stylesheet" type="text/css" href="<?=URL_BASE.'js/ext-3.3.0/SuperBoxSelect/superboxselect.css'?>"/>-->	
        
        <style type="text/css">
            <? include_once(PATH_BASE."js/ext-3.3.0/plupload/ext.ux.plupload.css"); ?>
            .silk-accept { background-image: url(<?=URL_BASE?>js/ext-3.3.0/plupload/icon/accept.png) !important; background-repeat: no-repeat; }
            .silk-add { background-image: url(<?=URL_BASE?>js/ext-3.3.0/plupload/icon/add.gif) !important; background-repeat: no-repeat; }
            .silk-cross { background-image: url(<?=URL_BASE?>js/ext-3.3.0/plupload/icon/cross.png) !important; background-repeat: no-repeat; }
            .silk-stop { background-image: url(<?=URL_BASE?>js/ext-3.3.0/plupload/icon/stop.png) !important; background-repeat: no-repeat; }
            .silk-arrow-up { background-image: url(<?=URL_BASE?>js/ext-3.3.0/plupload/icon/upload-start.gif) !important; background-repeat: no-repeat; }
        </style>
        
        <?php /*
	<!-- <script type="text/javascript" src="<?=PATH_BASE?>js/tiny_mce/tiny_mce.js"></script> -->
	<!-- <script type="text/javascript" src="<?=PATH_BASE?>js/Ext.ux.TinyMCE.min.js"></script> -->
	*/ ?>
	<script type="text/javascript">
	
	<?php
//	 include_once(PATH_BASE."js/ext-3.3.0/examples/ux/fileuploadfield/FileUploadField.js");
//	 include_once(PATH_BASE."js/ext-3.3.0/examples/ux/Spinner.js");
//	 include_once(PATH_BASE."js/ext-3.3.0/examples/ux/SpinnerField.js");
//	 include_once(PATH_BASE."js/ext-3.3.0/SuperBoxSelect/SuperBoxSelect.js");
//	 include_once(PATH_BASE."js/ext-3.3.0/UploadDialog/Ext.ux.UploadDialog.js");
         include_once(PATH_BASE.'js/ext-3.3.0/miframe/miframe-min.js');
         include_once(PATH_BASE.'js/Ext.ux.TinyMCE.min.js');
         include_once(PATH_BASE."js/ext-3.3.0/Ext.ux.grid.Search/Ext.ux.grid.Search.js");
         include_once(PATH_BASE."js/ext-3.3.0/plupload/plupload.full.min.js");
	include_once(PATH_BASE."js/ext-3.3.0/plupload/ext.ux.plupload.js");
	include_once(PATH_BASE."js/ext-3.3.0/plupload/es.js");
	 ?>
             
	// Global vars
	// variables par alos permisos
	var permiso_alta = <?php echo $permiso_alta;?>;
	var permiso_eliminar = <?php echo $permiso_eliminar;?>;
	var permiso_modificar = <?php echo $permiso_modificar;?>;
        var MAXDATE = <?php echo "'".$max_date."'";?>;
	
	var URL_BASE = "<?= URL_BASE?>";
	var URL_BASE_FILE = "<?= URL_BASE_FILE?>";
        var URL_BASE_SITIO = "<?php echo URL_BASE_SITIO;?>";
	var TAM_PAGINA="<?php echo TAM_PAGINA;?>";
	var CARPETA = "<?= site_url("gestion_riesgo/mejora_continua")?>";
	var CARPETA_UPLOAD = "<?= site_url("archivos")?>";
	var CARPETA_PLUPLOAD = "<?= PATH_BASE."js/ext-3.3.0/plupload"?>";
	var CARPETA_CAP = "<?= site_url("cap/capacitaciones")?>";
	var LINK_GENERICO = "<?= site_url("admin/genericos")?>";
        
        var TAREA = <?php 
                    if ($tarea!='0')
                    {
                        echo "{";
                        foreach ($tarea as $clave=>$valor)
                        {
                            if($valor!=NULL)
                                $t=$clave.":".$valor.",";
                            else
                                $t=$clave.":0,";
                                
                            echo $t;
                        }
                        echo "};";
                    }
                    else
                        echo "0";
                        
                ?>;
        Ext.onReady(function(){
	
            Ext.QuickTips.init();
            // defincion de la casilla de busqueda para filtrar registros

            <?php
//            include_once("uploadArchivos.js");
            if (!$esCap)
            {
                include_once("uploadArchivosCerrarTarea.js");
                include_once("formCerrarTarea.js");
                
            }
            else
            {
                include_once("uploadArchivosCerrarTareaCap.js");
                include_once("formCerrarTareaCap.js");
            }
            ?>

            // Datos para la ventana de permisos 
	})
        
	</script>	
<? else: ?>
	<h2>Su usuario no tiene permisos para usar este Modulo</h2>
<? endif; ?>
<div id="grillita"></div>