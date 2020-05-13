<? 
$permiso_listar = $permiso['Listar'];
$permiso_alta = $permiso['Alta'];
$permiso_eliminar = $permiso['Baja'];
$permiso_modificar = $permiso['Modificacion'];
$min_date=date("Y-m-d", strtotime("+1 days"));
$today = date("Y-m-d");

if ($permiso_listar):
?>        
    <link rel="stylesheet" type="text/css" href="<?=URL_BASE.'js/ext-3.3.0/examples/ux/fileuploadfield/css/fileuploadfield.css'?>"/>
    <style type="text/css">
            <? include_once(PATH_BASE."js/ext-3.3.0/plupload/ext.ux.plupload.css"); ?>
            .silk-accept { background-image: url(<?=URL_BASE?>js/ext-3.3.0/plupload/icon/accept.png) !important; background-repeat: no-repeat; }
            .silk-add { background-image: url(<?=URL_BASE?>js/ext-3.3.0/plupload/icon/add.gif) !important; background-repeat: no-repeat; }
            .silk-cross { background-image: url(<?=URL_BASE?>js/ext-3.3.0/plupload/icon/cross.png) !important; background-repeat: no-repeat; }
            .silk-stop { background-image: url(<?=URL_BASE?>js/ext-3.3.0/plupload/icon/stop.png) !important; background-repeat: no-repeat; }
            .silk-arrow-up { background-image: url(<?=URL_BASE?>js/ext-3.3.0/plupload/icon/upload-start.gif) !important; background-repeat: no-repeat; }
    </style>
    
    <script type="text/javascript">

    // Global vars
    // variables par alos permisos  
    var permiso_alta = <?php echo $permiso_alta;?>;
    var permiso_eliminar = <?php echo $permiso_eliminar;?>;
    var permiso_modificar = <?php echo $permiso_modificar;?>;
    var MINDATE = <?php echo "'".$min_date."'";?>;
    var TODAY = <?php echo "'".$today."'";?>;

    var TAM_PAGINA=25;
    var URL_BASE = "<?= URL_BASE?>";
    var PATH_DOMINIO = "<?= PATH_DOMINIO?>";
    var CARPETA = "<?= site_url("vto/vencimientos")?>";
    var LINK_GENERICO = "<?= site_url("genericos")?>";
    
    
    Ext.onReady(function(){

    Ext.QuickTips.init();

    
    
    <?php
    include_once(PATH_BASE."js/FileUploadField.js");
    include_once(PATH_BASE."js/paginador.js");
    include_once(PATH_BASE."js/ext-3.3.0/Ext.ux.grid.Search/Ext.ux.grid.Search.js");
    include_once(PATH_BASE."js/ext-3.3.0/plupload/plupload.full.min.js");
    include_once(PATH_BASE."js/ext-3.3.0/plupload/ext.ux.plupload.js");
    include_once(PATH_BASE."js/ext-3.3.0/plupload/es.js");
    ?>

    <?php
    include_once("grilla_vencimientos.js");
    include_once("eliminar_vencimiento.js");
    include_once("cerrar_vencimiento.js");
    include_once("alta_vencimiento.js");
    ?>

    })
    </script>	
<? else: ?>
	<h2>Su usuario no tiene permisos para usar este Modulo</h2>
<? endif; ?>
<div id="grillita"></div>
