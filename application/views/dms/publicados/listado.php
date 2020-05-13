<? 
$permiso_listar = $permiso['Listar'];
$permiso_alta = $permiso['Alta'];
$permiso_eliminar   = $permiso['Baja'];
$permiso_modificar  = $permiso['Modificacion'];
$permiso_colAccion  = $permiso_btn_col_acc; //1 si usuario=GR o usuario=PerfilEditor
$permiso_rowAccion  = $permiso_btn_row_acc; //id_usuario
$permiso_gr         = $permiso_gr;

if ($permiso_listar):
?>

<script type="text/javascript">
	
   
	// Global vars
	// variables para los permisos
	var permiso_alta = <?php echo $permiso_alta;?>;
	var permiso_eliminar = <?php echo $permiso_eliminar;?>;
	var permiso_modificar = <?php echo $permiso_modificar;?>;
	var permiso_col_acc = <?php echo $permiso_colAccion;?>;
	var permiso_row_acc = <?php echo $permiso_rowAccion;?>;
	var permiso_gr = <?php echo $permiso_gr;?>;
	
	
	var URL_BASE = "<?= URL_BASE?>";
	var URL_BASE_FILE = "<?= URL_BASE_FILE?>";
	var TAM_PAGINA=25;
	var CARPETA = "<?= site_url("dms/publicados")?>";
        var CARPETAGESTIONES = "<?= site_url("dms/gestiones")?>";
	var LINK_GENERICO = "<?= site_url("genericos")?>";
        var URL_BASE_SITIO = "<?php echo URL_BASE_SITIO;?>";
        
        var habilitadaCheck;
        var buscador;
        var PublicadosDataStore;
        var PublicadosColumnModel;
        var PublicadosGrid;
        var tiposDocJS;
        var tiposDocFiltro;
        
    Ext.onReady(function(){
	
	Ext.QuickTips.init();
	
	
	<?php
            include_once(PATH_BASE."js/CheckColumn.js");
	?>
	
	// defincion de la casila de busqueda para filtrar registros
	<?php
	include_once(PATH_BASE."js/ext-3.3.0/Ext.ux.grid.Search/Ext.ux.grid.Search.js");
	?>  
		
	<?php
        // llamo al paginador
        include_once(PATH_BASE."js/paginador.js");
        ?>
	
	<?php
            include_once("GrillaSuperior.js");
            include_once("GrillaInferior.js");
            include_once("transferir_documento.js");
            include_once("panel.js");
	?>
		
	// Datos para la ventana de permisos 
	
    })
</script>	
<? else: ?>
	<h2>Su usuario no tiene permisos para usar este Modulo</h2>
<? endif; ?>
<div id="grillita"></div>