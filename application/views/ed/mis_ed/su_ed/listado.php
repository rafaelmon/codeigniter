<? 
$permiso_listar = $permiso['Listar'];
$permiso_modificar = $permiso['Modificacion'];
if ($permiso_listar):
?>

	<script type="text/javascript">
	// Global vars
	// variables par alos permisos
	var permiso_listar = <?php echo $permiso_listar;?>;
	var permiso_modificar = <?php echo $permiso_modificar;?>;
	
	var URL_BASE = "<?= URL_BASE?>";
	var URL_BASE_FILE = "<?= URL_BASE_FILE?>";
	var TAM_PAGINA=25;
	var CARPETA = "<?= site_url("ed/su_ed")?>";
	var CARPETA_MIS_EEDD = "<?= site_url("ed/mis_ed")?>";
	var CARPETA_MI_ED = "<?= site_url("ed/mi_ed")?>";
        var CARPETA_PDF = "<?= site_url("ed/pdf_ed")?>";
	var LINK_GENERICO = "<?= site_url("genericos")?>";
        var ID_ED = "<?=$id_ed?>";
        var NOM_USUARIO = "<?=$nom_usuario?>";
        var PERIODO = "<?=$periodo?>";
        var T_CUMP = parseInt("<?=$t_cump?>");
        var MAX_CUMP = parseInt("<?=$max_cump?>");
	var CUMP = Math.round(T_CUMP/MAX_CUMP*100);
	Ext.onReady(function(){
	
	Ext.QuickTips.init();
        
        var supEdPanel = new Ext.Panel({
            id: 'ed-tabs-panel',
            renderTo: 'div-tab-panel',
            title:'Supervisando Evaluación de Desempeño-> Período: <span class="username">'+PERIODO+'</span> Usuario: <span class="username">'+NOM_USUARIO+"</span>",
//            layout:'border',		
//            split: true,
//            bodyStyle: 'padding:15px',
            border: false,
        //    height: 400,
//            items: [superiorPanel,inferiorPanel],
        }); 
	
        var tabsED = new Ext.TabPanel({
		        id: 'formTabED',
                        renderTo: 'ed-tabs-panel',
		        name: 'formTabED',
                        header:true,
		        activeTab: 0,
		        plain:true,
		        layoutOnTabChange: true,
                        tbar: [{
                            text: 'Volver',
                            tooltip: 'volver a mis evaluaciones...',
                            iconCls:'atras_ico',                      // reference to our css
                            handler: function (){
                                Ext.get('browser').load({
                                    url: CARPETA_MIS_EEDD+"/index/38",
                                    scripts: true,
                                    text: "Cargando..."
                                });
                            }, 
//                          },{
//                            text: 'Cerrar evaluación',
//                            tooltip: 'Cerrar esta evaluación',
//                            iconCls:'mied_fin_ico',                      // reference to our css
//                            disabled:true,
//                            handler: function (){
//                               alert("cierre ed");
//                            }, 
//                            hidden: !permiso_alta
                          },{
                            text: 'Ver Evaluación en PDF',
                            tooltip: 'previsualizar',
                            iconCls:'mied_pdf_ico',                      // reference to our css
                            disabled:false,
                            handler: function (){
                                var id=ID_ED;
                                var nom="ED-"+id;
                                window.open(CARPETA_PDF+'/ver_ed/'+id+"/"+nom)
                            }, 
                            hidden: !permiso_alta
                          }
                          ,'->',{
                            id: 'div_total_cump',
                            text: '<b>Peso M&aacute;ximo:'+MAX_CUMP+' - Peso ED:'+T_CUMP+' - Cumplimiento:'+CUMP+'%</b>',
                            disabled:true,
                            tooltip: '',
                            iconCls:'value_area',
                            buttonAlign: 'right',
                            handler: "" 
                          }
                        ],
		        items:[
		        	{
		                title: '1)Competencias Cualitativas - C1',
		                id:'edTab0',
		                autoLoad: {url: CARPETA+'/tab_competencias_cual', params: {id:ID_ED}, method: 'POST', scripts: true},
		                autoHeight: true,
                                autoScroll:true
//                                item:[edUsuarioEditorGrid]
		            },{
		                title: '2)Competencias Cuantitativas - C2',
		                id:'edTab1',
		                autoLoad: {url: CARPETA+'/tab_competencias_cuant', params: {id:ID_ED}, method: 'POST', scripts: true},
		                autoHeight: true,
                                autoScroll:true
//                                item:[edUsuarioEditorGrid]
		            },
		            {
		                title: '3)Fortalezas y Aspectos a Mejorar - FyAM',
		                autoLoad: {url: CARPETA+'/tab_fortalezas', params: {id:ID_ED}, method: 'POST', scripts: true},
		                id:'edTab2',
		                autoHeight: true
		            },
                            {
		                title: '4)Plan de Mejora - PM',
		                id:'edTab3',
		                autoLoad: {url: CARPETA+'/tab_plan', params: {id:ID_ED}, method: 'POST', scripts: true},
		                autoHeight: true,
		            },
                            {
		                title: '5)Fijación de Metas - FM',
		                id:'edTab4',
		                autoLoad: {url: CARPETA+'/tab_metas', params: {id:ID_ED}, method: 'POST', scripts: true},
		                autoHeight: true,
		            }
	        	]
	        });
                var altura=Ext.getBody().getSize().height - 60;
                tabsED.setHeight(altura);
		
                Ext.getCmp('browser').on('resize',function(comp){
                        tabsED.setWidth(this.getSize().width);
                        tabsED.setHeight(this.getSize().height);
                        tabsED.setHeight(Ext.getBody().getSize().height - 60);
                });
        
	
        //<?php
//        // llamo al paginador
//        include_once(PATH_BASE."js/paginador.js");
//        ?>

	
	// Ventana y funciones Alta usuario
	//<?php
////	include_once("altaED.js");
//	?>		

	// defincion de la casila de busqueda para filtrar registros
	//<?php
//	include_once(PATH_BASE."js/ext-3.3.0/Ext.ux.grid.Search/Ext.ux.grid.Search.js");
//        include_once("RadioColumn.js");
//	?>  
		
	// Datos para la grilla de usuarios
	//<?php
////	include_once("grilla.js");
//	?>
		
	
	})
	</script>	
<? else: ?>
	<h2>Su usuario no tiene permisos para usar este Modulo</h2>
<? endif; ?>
<div id="div-tab-panel"></div>
<style>.x-grid-cell-inner {
white-space: normal;
}
.enc_r1{
    color: blue;
    font-family: verdana;
    font-size: 90%;
}
.username{
    color: #3d8b40;
    
}
.value_area{
    color: blue !important;
    
}
        </style>