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
	var CARPETA = "<?= site_url("cpp/eventos")?>";
        var LINK_GENERICO = "<?= site_url("genericos")?>";
        var ALT_INF =(Ext.getBody().getSize().height)/3 - 40;
        var ANCH =Ext.getBody().getSize().width;
        var ANCH_INF =(Ext.getBody().getSize().width)/2;
        var MINDATE = <?php echo "'".$min_date."'";?>;
        var ID_EVENTO_SELECT = null;
        var BTN_VERIFICAR_TAREAS=false;
        
        //grilla_eventos
        var cppEventosDataStore;
        var paginadorEventos;
        var botonesCppAction;
        var cppEventosBuscador;
        var arrayfiltroCriticidad;
        var arrayfiltroEstado;
        var cppFiltroCriticidad;
        var cppFiltroEstado;
        var cppEventosColumnModel;
        var cppEventosGridPanel;
        var paginadorEventos;
        var clickBtnNuevoEvento;
        var clickBtnEventosExcel;
        var cppEventosPanel;
        //Alta Evento
        var sectoresDS;
        var sectoresCombo;
        var fechaInicioField;
        var hmInicioField;
        var fechaHoraInicio;
        var fechaFinField;
        var hmFinField;
        var fechaHoraFin;
        var equiposDS;
        var equiposCombo;
        var productosDS;
        var productosCombo;
        var descripcionEventoField;
        var altaEventoCreateForm;
        var altaEventoCreateWindow;
        //Calificar Evento
        var consecuenciasDS;
        var consecuenciasCombo;
        var descripcionField;
        var calificarEventoCreateForm;
        var calificarEventoCreateWindow;
        //Designar Investigadores
        var investigadoresDS;
        var investigadoresSBS;
        var designarInvestigadresCreateForm;
        var designarInvestigadresCreateWindow;
        //Panel
        var cppTab_1;
        var cppTab_2;
        var cppTab_3;
        var cppTab_4;
        var cppTabs_panel;
        //Set Criticidad
        var criticidadEventoRadios;
        var setCriticidadEventoCreateForm;
        var setCriticidadEventoCreateWindow;
        //Grilla Consecuencias
        var consecuenciasDataStore;
        var botonesCppConsecuenciasAction;
        var cppConsecuenciasBuscador;
        var cppConsecuenciasPaginador;
        var consecuenciasColumnModel;
        var consecuenciasGridPanel;
        //Monto Perdido
        var montoField;
        var montoCreateForm;
        var montoCreateWindow;
        //Toneladas Perdidas
        var toneladasField;
        var toneladasPerdidasCreateForm;
        var toneladasPerdidasCreateWindow;
        //Grilla Auditoria
        var auditoriaDataStore;
        var cppAuditoriaBuscador;
        var cppAuditoriaPaginador;
        var auditoriaColumnModel;
        var auditoriaGridPanel;
        //Grilla Investigadores
        var investigadoresDataStore;
        var cppInvestigadoresBuscador;
        var cppInvestigadoresPaginador;
        var investigadoresColumnModel;
        var investigadoresGridPanel;
        //Alta Tarea
        var cppEventoDescTareaField;
        var cppEventoUsuariosDS;
        var cppEventoResponsablesCombo;
        var cppEventoResponsableFieldSet;
        var cppEventoTareaFechaField;
        var cppEventoTareaCreateForm;
        var cppEventoTareaCreateWindow;
        //Grilla Causas
        var cppCausasDataStore;
        var cppCausasPaginador;
        var botonesCppCausasAction;
        var cppCausasColumnModel;
        var cppCausasGridPanel;
        var cppCausasPanel;
        //Grilla Tareas
        var cppTareasDataStore;
        var paginador;
        var cppTareasColumnModel;
        var cppTareasGridPanel;
        var cppTareasPanel;
        //Solucion Panel
        var cppSolucionPanel;
	
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
        
	include_once("cierra_evento.js");
	include_once("consecuencias/grilla_consecuencias.js");
	include_once("consecuencias/monto_perdido.js");
	include_once("consecuencias/toneladas_perdidas.js");
	include_once("auditoria/grilla_auditoria.js");
	include_once("investigadores/grilla_investigadores.js");
	include_once("alta_causa.js");
	include_once("solucion/alta_tarea.js");
	include_once("solucion/grilla_causas.js");
	include_once("solucion/grilla_tareas.js");
	include_once("solucion/solucion_panel.js");
        
	include_once("grilla_eventos.js");
	include_once("excel.js");
	include_once("designar_investigadores.js");
	include_once("calificar_evento.js");
	include_once("alta_evento.js");
//	include_once("setCriticidad.js");
        include_once("editCriticidad.js");
	include_once("panel.js");
        
	?>
		
	})
	</script>	
<? else: ?>
	<h2>Su usuario no tiene permisos para usar este Modulo</h2>
<? endif; ?>
<div id="cpp_eventos"></div>
