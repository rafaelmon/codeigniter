<? 
$permiso_listar = $permiso['Listar'];
$permiso_alta = $permiso['Alta'];
$permiso_eliminar = $permiso['Baja'];
$permiso_modificar = $permiso['Modificacion'];
$min_date=date("Y-m-d");

if ($permiso_listar):
?>        
    <script type="text/javascript">

    // Global vars
    // variables par alos permisos  
    var permiso_alta = <?php echo $permiso_alta;?>;
    var permiso_eliminar = <?php echo $permiso_eliminar;?>;
    var permiso_modificar = <?php echo $permiso_modificar;?>;
    var MINDATE = <?php echo "'".$min_date."'";?>;

    var TAM_PAGINA=25;
    var URL_BASE = "<?= URL_BASE?>";
    var CARPETA = "<?= site_url("cap/participantes")?>";
    var LINK_GENERICO = "<?= site_url("genericos")?>";
    
    var capGrillaCapacitacionesPanel;
    var tareasGridPanel;
    Ext.onReady(function(){

    Ext.QuickTips.init();

    <?php
    include_once(PATH_BASE."js/paginador.js");
//	include_once(PATH_BASE."js/CheckColumn.js");
    include_once(PATH_BASE."js/ext-3.3.0/Ext.ux.grid.Search/Ext.ux.grid.Search.js");
//        include_once(PATH_BASE."js/ext-3.3.0/SuperBoxSelect/SuperBoxSelect.js");
    ?>

    <?php
    include_once("grilla_participantes.js");
    include_once("grilla_capacitaciones.js");
    include_once("panel.js");

    ?>

    })
    </script>	
<? else: ?>
	<h2>Su usuario no tiene permisos para usar este Modulo</h2>
<? endif; ?>
<div id="grillita"></div>
