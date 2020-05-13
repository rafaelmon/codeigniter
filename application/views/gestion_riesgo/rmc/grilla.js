
rmcDataStore = new Ext.data.Store({
    id: 'rmcDataStore',
    proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/listado', 
            method: 'POST'
    }),
//      baseParams:{tampagina: TAM_PAGINA}, 
      reader: new Ext.data.JsonReader({
        root: 'rows',
        totalProperty: 'total',
        id: 'id_rmc'
      },[ 
        {name: 'id_rmc',            type: 'int',        mapping: 'id_rmc'},        
        {name: 'id_usuario_alta',   type: 'int',        mapping: 'id_usuario_alta'},        
        {name: 'usuario_alta',      type: 'string',     mapping: 'usuario_alta'},        
        {name: 'sector',            type: 'string',     mapping: 'sector'},
        {name: 'descr',             type: 'string',     mapping: 'descr'},
        {name: 'fecha_alta',        type: 'string',     mapping: 'fecha_alta'},
        {name: 'id_estado_inv',     type: 'int',        mapping: 'id_estado_inv'},
        {name: 'estado',            type: 'string',     mapping: 'estado'},
        {name: 'id_criticidad',     type: 'int',        mapping: 'id_criticidad'},
        {name: 'criticidad',        type: 'string',     mapping: 'criticidad'},
        {name: 'fecha_set_crit',    type: 'string',     mapping: 'fecha_set_crit'},
        {name: 'fecha_vto_inv',     type: 'string',     mapping: 'fecha_vto_inv'},
        {name: 'usuario_crit',      type: 'string',     mapping: 'usuario_crit'},        
        {name: 'investigador1',     type: 'string',     mapping: 'investigador1'},        
        {name: 'investigador2',     type: 'string',     mapping: 'investigador2'},        
        {name: 'tareas',            type: 'int',        mapping: 'tareas'},
        {name: 'archivos',          type: 'int',        mapping: 'archivos'},
        {name: 'clasificacion',     type: 'string',     mapping: 'clasificacion'},
        {name: 'clasificaciont',     type: 'string',     mapping: 'clasificaciont'},
        {name: 'observacion_sector',    type: 'string',     mapping: 'observacion_sector'}
      ]),
      sortInfo:{field: 'id_rmc', direction: "DESC"},
      remoteSort: true
});

arrayClasificacionesRi = new Ext.data.JsonStore({
	url: CARPETA+'/combo_clasificaciones',
	root: 'rows',
//        method: 'POST',
	fields: ['id_clasificacion', 'clasificacion']
//        autoload: true
});
arrayClasificacionesRi.load();


arrayClasificacionesRi.on('load' , function(  js , records, options ){
											   
	var tRecord = Ext.data.Record.create(
		{name: 'id_clasificacion', type: 'int'},
		{name: 'clasificacion', type: 'string'}
	);
	var myNewT = new tRecord({
		id_clasificacion: '-1',
		clasificacion: 'Todos'
	});
	arrayClasificacionesRi.insert( 0, myNewT);	
} );

var clasificacionesRiFiltro = new Ext.form.ComboBox({
    id:'clasificacionesRiFiltro',
    forceSelection : true,
    value: 'Todos',
    store: arrayClasificacionesRi,
    editable : false,
    displayField: 'clasificacion',
    valueField:'id_clasificacion',
    allowBlank: false,
    selectOnFocus:true,
    width: 215, 
    triggerAction: 'all'
//    clearFilterOnReset : false
});

rmcPaginador= new Ext.PagingToolbar({
    pageSize: TAM_PAGINA,
    displayInfo: true,
    beforePageText:'Página',
    afterPageText:'de {0}',
    displayMsg:'Mostrando {0} - {1} de <b>{2}</b>',
    firstText:'Priemra Página',
    lastText:'Última Página',
    prevText:'Anterior',
    nextText:'Siguiente',
    refreshText:'Actualizar',
    buttonAlign:'left',
    emptyMsg:'No hay registros para listar'
});
rmcPaginador.bindStore(rmcDataStore);

botonesRmcTareasAction = new Ext.grid.ActionColumn({
                width:  90,
                editable:false,
                menuDisabled:true,
                header:'Acc.',
                hideable:false,
                align:'center',
                tooltip:'Crear tareas para el Reporte...',
                 hidden:false,
		items:[{
                    icon:URL_BASE+'images/add.gif',
                    iconCls :'col_accion',
                    tooltip:'Agregar tarea',
                    hidden: false,
//                    renderer: showCriticidadRmc,
                    getClass:showBtnNuevaTareaRmc,
//                    hidden: (!permiso_alta||!rol_editor),
                    handler: clickBtnNuevaTareaRmc
                },{
                    icon:URL_BASE+'images/upload.png',
                    iconCls :'col_accion',
                    tooltip:'Subir Archivo',
                    getClass:showBtnUploadArchivoRi,
                    handler: clickBtnUploadArchivoRi
                }]
});
botonesRmcGrAction = new Ext.grid.ActionColumn({
		width: 25,
                editable:false,
                menuDisabled:true,
                header:'GR',
                hideable:false,
                align:'center',
                tooltip:'Definir Criticidad del Reporte',
                hidden:!permiso_btn_gr,
		items:[{
                    icon:URL_BASE+'images/investigar.png',
                    iconCls :'col_accion',
                    tooltip:'Definir Criticidad',
                    hidden: false,
//                    renderer: showCriticidadRmc,
                    getClass:showBtnCriticidadRmc,
//                    hidden: (!permiso_alta||!rol_editor),
                    handler: clickBtnSetCritRmc 
                }]
});
colEnBlanco = new Ext.grid.ActionColumn({
                editable:false,
                menuDisabled:true,
                header:'|',
                hideable:false,
                align:'center',
                width:  20,
                hidden: false,
		items:[]
});    
rmcBuscador= new Ext.ux.grid.Search({
    iconCls:'icon-zoom',
//    readonlyIndexes:['id_tarea', 'tarea','descripcion'],
    disableIndexes:['fecha_alta','tareas','criticidad','estado','fecha_set_crit','fecha_vto_inv','clasificacion','archivos'],
    align:'right',
    minChars:3
});

rmcColumnModel = new Ext.grid.ColumnModel(
    [{
        header: '#',
        readOnly: true,
        dataIndex: 'id_rmc',
        width: 90,        
        sortable: true,
        renderer: function(value, cell){
            cell.css = "readonlycell";
            return value;
        },
        hidden: false
      },{
        header: 'Fecha Alta',
        dataIndex: 'fecha_alta',
        sortable: true,
        width:  80,
        fixed:true,
        readOnly: true,
        align:'center'
      },{
        header: 'Usuario Alta',
        dataIndex: 'usuario_alta',
        width:  180,
        sortable: true
      },{
        header: 'Descipción',
        dataIndex: 'descr',
        width:  350,
        fixed:true,
        renderer: showQtipDescRmc,
        sortable: false
      },{
        header: 'Sector involucrado',
        dataIndex: 'sector',
        width:  350,
        sortable: true
      },botonesRmcGrAction,{
        header: 'Criticidad',
        dataIndex: 'criticidad',
        width:  150,
        renderer: showCriticidadRmc,
        sortable: true
      },{
        header: 'Fecha Crit.',
        dataIndex: 'fecha_set_crit',
        sortable: true,
        width:  80,
        fixed:true,
        readOnly: true,
        align:'center'
      },{
        header: 'Clasificacion',
        dataIndex: 'clasificacion',
        sortable: true,
        width:  80,
        fixed:true,
        renderer: showQTipClasificacion,
        readOnly: true,
        align:'center'
      },{
        header: 'Vto Inv.',
        dataIndex: 'fecha_vto_inv',
        sortable: true,
        width:  80,
        fixed:true,
        readOnly: true,
        align:'center'
      },{
        header: 'Estado Inv.',
        dataIndex: 'estado',
        width:  150,
        renderer: showEstadoInv,
        sortable: true
      },{
        header: '1er Investigador',
        dataIndex: 'investigador1',
        renderer: showInvestigacionRmc,
        width:  180,
        sortable: false
      },{
        header: '2do Investigador',
        dataIndex: 'investigador2',
        renderer: showInvestigacionRmc,
        width:  180,
        sortable: false
      },{
        header: 'Tareas',
        dataIndex: 'tareas',
        width:  50,
        renderer: showQTareas,
        sortable: true
      },{
        header: 'Archivos',
        dataIndex: 'archivos',
        width:  50,
        sortable: true
      },botonesRmcTareasAction,colEnBlanco
     ]
    );

rmcDataStore.load({params: {start: 0, limit: TAM_PAGINA}});

Ext.apply(Ext.form.VTypes, {
    daterange : function(val, field) {
        var date = field.parseDate(val);

        if(!date){
            return false;
        }
        if (field.startDateField && (!this.dateRangeMax || (date.getTime() != this.dateRangeMax.getTime()))) {
            var start = Ext.getCmp(field.startDateField);
            start.setMaxValue(date);
            start.validate();
            this.dateRangeMax = date;
        }
        else if (field.endDateField && (!this.dateRangeMin || (date.getTime() != this.dateRangeMin.getTime()))) {
            var end = Ext.getCmp(field.endDateField);
            end.setMinValue(date);
            end.validate();
            this.dateRangeMin = date;
        }
        return true;
    }
});

var rmcFiltroFechaDesde = new Ext.form.DateField({
    id:'rmcFiltroFechaDesde',
    fieldLabel: 'Desde',
    vtype: 'daterange',
    //anchor : '90%',
    width: 90,
    endDateField: 'rmcFiltroFechaHasta',
    maxValue: MAXDATE_FILTRO
});
var rmcFiltroFechaHasta = new Ext.form.DateField({
    id:'rmcFiltroFechaHasta',
    fieldLabel: 'Hasta',
    vtype: 'daterange',
    //anchor : '90%',
    width: 90,
    startDateField: 'rmcFiltroFechaDesde',
    maxValue: MAXDATE_FILTRO
});

var estadosComboRi = new Ext.form.ComboBox({
    typeAhead: true,
    triggerAction: 'all',
//    lazyRender:true,
    value: 'Todos',
    mode: 'local',
    store: new Ext.data.ArrayStore({
        id: 0,
        fields: [
            'id_estado',
            'estado'
        ],
        data: [[0, 'Todos'], [1, 'Abierta'], [2, 'Cerrada'], [3, 'Vencida']]
    }),
    valueField: 'id_estado',
    displayField: 'estado',
    width: 90
});
var criticidadesComboRi = new Ext.form.ComboBox({
    typeAhead: true,
    triggerAction: 'all',
//    lazyRender:true,
    value: 'Todos',
    mode: 'local',
    store: new Ext.data.ArrayStore({
        id: 0,
        fields: [
            'id_criticidad',
            'criticidad'
        ],
        data: [[0, 'Todos'], [1, 'Critico'], [2, 'Alto'], [3, 'Menor']]
    }),
    valueField: 'id_criticidad',
    displayField: 'criticidad',
    width: 90
});

rmcGridPanel =  new Ext.grid.GridPanel({
    id: 'rmcGridPanel',
    store: rmcDataStore,
    cm: rmcColumnModel,
    enableColLock:false,
    trackMouseOver:true, 
    loadMask: true,
    viewConfig: {
        forceFit: true
    },
    plugins:[rmcBuscador],
    bbar:[rmcPaginador],
    tbar: [
        {
            text: 'Nuevo RI',
            tooltip: 'Crear nuevo Reporte...',
            iconCls:'add',                      // reference to our css
            handler: rmcAltaDisplayFormWindow,
            hidden: !permiso_alta
        }
        ,ESTACIO+'<b>Filtros:</b>','&emsp;|&emsp;','Clasificación:',clasificacionesRiFiltro,'&emsp;|',
        '&emsp; Desde:', rmcFiltroFechaDesde, '&emsp; Hasta:',rmcFiltroFechaHasta,'&emsp;|&emsp; Criticidades:',criticidadesComboRi,'&emsp;Estados:',estadosComboRi,'&emsp;|&emsp;',
        {
            text: 'Filtrar',
            tooltip: 'Filtrar grilla',
            iconCls:'filtrar_grilla',
            handler: rmcFiltrarGrilla
        },'&emsp;',
        {
            text: 'Quitar',
            tooltip: 'Quitar filtros y mostrar todos los datos',
            iconCls:'quitar_filtros2',
            handler: clickBtnQuitarFiltros
        }
          ,'&emsp;|&emsp;',
            {
                text: 'Descargar listado',
    //            tooltip: 'e...',
                iconCls:'archivo_excel_ico',
                handler: clickBtnExcel
            }
    ]
});   

rmcPanel = new Ext.Panel(
{
        collapsible: false,
        collapsed:false,
        split: true,
        title: 'Reportes de Incidentes (RI)',
        region: 'center',
        height: 300,
        minSize: 100,
        maxSize: 350,
        margins: '0 5 5 5',
        html:'<p>panel superior</p>',
        layout: 'fit',
        items : [rmcGridPanel]
});
    
var altura=Ext.getBody().getSize().height - 60;
rmcGridPanel.setHeight(altura);
	
Ext.getCmp('browser').on('resize',function(comp){
    rmcGridPanel.setWidth(this.getSize().width);
    rmcGridPanel.setHeight(Ext.getBody().getSize().height - 60);

});


function clickBtnSetCritRmc(grid, rowIndex){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts){var result=parseInt(response.responseText);switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':
go_clickBtnSetCritRmc(grid, rowIndex);break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}
        
function go_clickBtnSetCritRmc(grid, rowIndex){
    var record = grid.getStore().getAt(rowIndex); 
    var id_rmc=record.data.id_rmc;
    rmcSetCritDisplayFormWindow(id_rmc);
}
  function clickBtnNuevaTareaRmc(grid, rowIndex){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts){var result=parseInt(response.responseText);switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':
go_clickBtnNuevaTareaRmc(grid, rowIndex);break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}

function go_clickBtnNuevaTareaRmc(grid, rowIndex){
    var record = grid.getStore().getAt(rowIndex); 
    var id_rmc=record.data.id_rmc;
    displayRmcTareaFormWindow(id_rmc);
}
function showBtnNuevaTareaRmc(value,metaData,record){
    var criticidad=record.json.id_criticidad
    var inv1=record.json.id_investigador1
    var inv2=record.json.id_investigador2
//    console.log('Inv1:'+inv1+' Inv2:'+inv2+" btn:"+permiso_btn_tareas+" eval:"+(permiso_btn_tareas==inv1 || permiso_btn_tareas==inv2));
    if(permiso_btn_tareas==inv1 || permiso_btn_tareas==inv2)
    {
        if(criticidad==null||criticidad==0||criticidad==3)
            return 'x-hide-display';  
        else
            return 'x-grid-center-icon';                
    }
    else
       return 'x-hide-display';  
        
};
function showBtnUploadArchivoRi(value,metaData,record){
    if(!permiso_btn_upload)
        return 'x-hide-display';  
    else
        return 'x-grid-center-icon';                
        
};
function showBtnCriticidadRmc(value,metaData,record){
    var criticidad=record.json.id_criticidad   
    if(criticidad==null||criticidad==0)
        return 'x-grid-center-icon';                
    else
        return 'x-hide-display';  
};
function showEstadoInv (value,metaData,superData){
    var estado=superData.json.id_estado_inv;
    switch (estado)
    {
        case '1'://abierta
        metaData.attr = 'style="background-color:#01A9DB; color:#FFF;"'; //Celeste
        break;
        case '2'://cerrada
        metaData.attr = 'style="background-color:#088A08; color:#FFF;"';//verde
        break;
        case '3'://vencida
        metaData.attr = 'style="background-color:#FF0000; color:#FFF;"';//Rojo
        break;
        default:
        {
            var criticidad = superData.get('id_criticidad');
            if (criticidad==3 || criticidad == 4)
                metaData.attr = 'style="background-color:#878585; color:#878585;"';//gris gris
            else
                metaData.attr = 'style="background-color:#CAFAED; color:#CAFAED;"';//claro
        }
            
    }
    return value;
}
function showCriticidadRmc (value,metaData,superData){
    var estado=superData.json.id_criticidad;
    var deviceDetail = superData.get('criticidad');
    switch (estado)
    {
        case '1': //crítica
        metaData.attr = 'style="background-color:#FF0000; color:#FFF;"'; //Rojo
        metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
        break;
        case '2': //alta
        metaData.attr = 'style="background-color:#DF7401; color:#FFF;"'; //Naranja
        metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
        break;
        case '3': //media
        metaData.attr = 'style="background-color:#088A08; color:#FFF;"';//verde
        metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
        break;
        case '4': //Fuera de Alcance
        metaData.attr = 'style="background-color:#01A9DB; color:#FFF;"';//celeste
        metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
        break;
        default:
        metaData.attr = 'style="background-color:#CAFAED; color:#CAFAED;"';//claro
    }
    return value;
}
function showInvestigacionRmc (value,metaData,superData){
    var estado=superData.json.id_criticidad;
    var q=superData.json.tareas;
    if (estado==3 || estado == 4)
    {
        metaData.attr = 'style="background-color:#878585; color:#878585;"';//gris gris
        var deviceDetail = 'No se investiga';
        metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
    }
    return value;
}
function showQTareas (value,metaData,superData){
    var estado=superData.json.id_criticidad;
    var q=superData.json.tareas;
    if (estado==3 || estado == 4)
    {
        metaData.attr = 'style="background-color:#878585; color:#878585;"';//gris gris
        var deviceDetail = 'No se investiga';
        metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
    }
    else
    {
        if (q>0)
        {
    //        value='<a href="">'+value+'</a>';
            value='<span ext:qtip="Click para ver tareas">'+value+'<span style="font-size:9px; color:gray;"> (...)</span></span>';
        }
        
    }
    return value;
}
function showQtipDescRmc(value, metaData,record){
    var deviceDetail = record.get('descr');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
    return value;
}

function showQTipClasificacion(value, metaData,record){
    var deviceDetail = record.get('clasificaciont');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
    return value;
}

//clasificacionesRiFiltro.on('select', filtrarGrilla);

function rmcFiltrarGrilla( combo, record, index ){
    
    var id_clasificacion    = clasificacionesRiFiltro.getValue();
    var f_desde             = rmcFiltroFechaDesde.getValue();
    var f_hasta             = rmcFiltroFechaHasta.getValue();
    var id_estado           = estadosComboRi.getValue();
    var id_criticidad       = criticidadesComboRi.getValue();
    
//    var fields = [];
//        fields.push('id_clasificacion');
    var values = [];
    values.push(id_clasificacion);
    values.push(f_desde);
    values.push(f_hasta);
    values.push(id_estado);
    values.push(id_criticidad);
//    var encoded_array_f = Ext.encode(fields);
    var encoded_array_v = Ext.encode(values);
//	var encoded_array_v = Ext.encode(values);
    rmcDataStore.setBaseParam('filtros',encoded_array_v);
    rmcDataStore.load({params: {start: 0, limit: TAM_PAGINA}});
}

function clickBtnQuitarFiltros (grid,rowIndex,colIndex,item,event){
        Ext.Ajax.request({ 
            url: LINK_GENERICO+'/sesion',
            method: 'POST',
            success: function(response, opts) {
                var result=parseInt(response.responseText);
                switch (result)
                {
                    case 0:
                    case '0':
                        location.assign(URL_BASE_SITIO+"admin");
                        break;
                    case 1:
                    case '1':
                        go_clickBtnQuitarFiltros(grid,rowIndex,colIndex,item,event);
                        break;
                }
          },
            failure: function(response) {
                location.assign(URL_BASE_SITIO+"admin");
            }
        });
    }
function go_clickBtnQuitarFiltros(){
    clasificacionesRiFiltro.reset();
    rmcFiltroFechaDesde.reset();
    rmcFiltroFechaHasta.reset();
    estadosComboRi.reset();
    criticidadesComboRi.reset();
    rmcDataStore.setBaseParam('filtros','');
    rmcDataStore.load({params: {start: 0, limit: TAM_PAGINA}});
}

rmcGridPanel.on('celldblclick', abrir_popup_rmc);
function abrir_popup_rmc(grid ,  rowIndex, columnIndex,  e){
    var data=grid.store.data.items[rowIndex].data;
    if(columnIndex<(grid.colModel.config.length)-1)
    {
        var panelContent;
        var win;
        var html = [
            '<html>',
                '<div style=" font-size:12px">',
                    '<div style="text-align:center;"><h1>Detalle RI Nro '+2000000+data.id_rmc+'</h1><br></div>',
                    '<table>',
                            '<tr>',
                                    '<td><b>Fecha de Alta</b></td>',
                                    '<td><b>&emsp;:&emsp;</b></td>',
                                    '<td style="background-color:#E6E6E6;width:75%">'+data.fecha_alta+'</td>',
                            '</tr>',
                            '<tr>',
                                    '<td><b>Usuario Alta</b></td>',
                                    '<td><b>&emsp;:&emsp;</b></td>',
                                    '<td style="background-color:#E6E6E6;width:75%">'+data.usuario_alta+'</td>',
                            '</tr>',
                            '<tr>',
                                    '<td><b>Descripci&oacute;n</b></td>',
                                    '<td><b>&emsp;:&emsp;</b></td>',
                                    '<td style="background-color:#E6E6E6;width:75%">'+data.descr+'</td>',
                            '</tr>',
                            '<tr>',
                                    '<td><b>Sector involucrado</b></td>',
                                    '<td><b>&emsp;:&emsp;</b></td>',
                                    '<td style="background-color:#E6E6E6;width:75%">'+data.sector+'</td>',
                            '</tr>',
                            '<tr>',
                                    '<td><b>Observaci&oacute;n sector</b></td>',
                                    '<td><b>&emsp;:&emsp;</b></td>',
                                    '<td style="background-color:#E6E6E6;width:75%">'+data.observacion_sector+'</td>',
                            '</tr>',
                            '<tr>',
                                    '<td><b>Nivel de Criticidad</b></td>',
                                    '<td><b>&emsp;:&emsp;</b></td>',
                                    '<td style="background-color:#E6E6E6;width:75%">'+data.criticidad+'</td>',
                            '</tr>',
                            '<tr>',
                                    '<td><b>Fecha Criticidad</b></td>',
                                    '<td><b>&emsp;:&emsp;</b></td>',
                                    '<td style="background-color:#E6E6E6;width:75%">'+data.fecha_set_crit+'</td>',
                            '</tr>',
                            '<tr>',
                                    '<td><b>Clasificación</b></td>',
                                    '<td><b>&emsp;:&emsp;</b></td>',
                                    '<td style="background-color:#E6E6E6;width:75%">'+data.clasificaciont+'</td>',
                            '</tr>',
                            '<tr>',
                                    '<td><b>Fecha Vto. Invest.</b></td>',
                                    '<td><b>&emsp;:&emsp;</b></td>',
                                    '<td style="background-color:#E6E6E6;width:75%">'+data.fecha_vto_inv+'</td>',
                            '</tr>',
                            '<tr>',
                                    '<td><b>Estado Invest.</b></td>',
                                    '<td><b>&emsp;:&emsp;</b></td>',
                                    '<td style="background-color:#E6E6E6;width:75%">'+data.estado+'</td>',
                            '</tr>',
                            '<tr>',
                                    '<td><b>Investigador1</b></td>',
                                    '<td><b>&emsp;:&emsp;</b></td>',
                                    '<td style="background-color:#E6E6E6;width:75%">'+data.investigador1+'</td>',
                            '</tr>',
                            '<tr>',
                                    '<td><b>Investigador2</b></td>',
                                    '<td><b>&emsp;:&emsp;</b></td>',
                                    '<td style="background-color:#E6E6E6;width:75%">'+data.investigador2+'</td>',
                            '</tr>',
                            '<tr>',
                                    '<td><b>Tareas relalizadas</b></td>',
                                    '<td><b>&emsp;:&emsp;</b></td>',
                                    '<td style="background-color:#E6E6E6;width:75%">'+data.tareas+'</td>',
                            '</tr>',
                    '</table>',
                '</div>',
            '</html>',
        ];

        win = new Ext.Window({
            title: 'Vista Reporte Nro:'+2000000+data.id_rmc,
            closable: true,
            modal:true,
            //closeAction: 'hide',
            width: 600,
            height: 380,						
            plain: true,
            layout: 'absolute',
            html: html.join(''),
            buttons: [{
                    text: 'Cerrar',
                    handler: function(){
                            win.hide();
                            win.destroy();
                    }
            }]
        });
        win.show();
        }
}

function clickBtnExcel (){Ext.Ajax.request({ url: LINK_GENERICO+'/sesion',method: 'POST',waitMsg: 'Por favor espere...',success: function(response, opts) {var result=parseInt(response.responseText);
switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':go_clickBtnExcel();break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}

function go_clickBtnExcel(){
        var n=rmcDataStore.totalLength;
        var txt;
        if (n>0)
        {
            if (n>1000)
                txt='El listado de tareas que usted filtro contiene <b>'+n+'</b> registros. El archivo a descargar contendrá una cantidad máxima de <b>1000</b> registros.<br><br>¿Desea continuar descargando el archivo?';
            else
                txt='¿Confirma la descarga del archivo?';
            Ext.MessageBox.confirm('Confirmar',txt, function(btn, text){
                if(btn=='yes'){
                    msgProcess('Generando...');
                    var body = Ext.getBody();
                    var downloadFrame = body.createChild({
                         tag: 'iframe',
                         cls: 'x-hidden',
                         id: 'app-upload-frame',
                         name: 'uploadframe'
                     });
                    var downloadForm = body.createChild({
                         tag: 'form',
                         cls: 'x-hidden',
                         id: 'app-upload-form',
                         target: 'app-upload-frame'
                     });
                    Ext.Ajax.request({
                        url: CARPETA+'/excel/',
                        timeout:10000,
                        scope :this,
                        params: {
                            filtros : rmcDataStore.baseParams.filtros,
                            query   : rmcDataStore.baseParams.query,
                            fields  : rmcDataStore.baseParams.fields,
                            sort    : rmcDataStore.baseParams.sort,
                            dir     : rmcDataStore.baseParams.dir
                        },
                        form: downloadForm,
                        callback:function (){
                        Ext.Msg.alert('Status', 'Datos generados correctamente');
                    },
                        isUpload: true,
                         success: function(response, opts) {
                         },
                        failure: function(response, opts) {
                         }
                    });
                    Ext.Msg.alert('Descarga de archivo', 'Descarga en proceso. Por Favor aguarde a que se abra la ventana de descarga...');
                }
            });
        }
        else
            Ext.Msg.alert('Descarga de archivo', 'No hay registros para generar el archivo. Por favor redefina los filtros de la grilla');
    }
