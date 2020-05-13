tareasDataStore = new Ext.data.Store({
    id: 'tareasDataStore',
    proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/listado', 
            waitMsg: 'Por favor espere...',
            method: 'POST'
    }),
      baseParams:{limit: TAM_PAGINA}, // this parameter is passed for any HTTP request
      reader: new Ext.data.JsonReader({
        root: 'rows',
        totalProperty: 'total',
        id: 'id_tarea'
      },[ 
        {name: 'id_tarea',          type: 'int',    mapping: 'id_tarea'},        
        {name: 'hallazgo',          type: 'string', mapping: 'hallazgo'},
        {name: 'id_grado_crit',     type: 'string', mapping: 'id_grado_crit'},
        {name: 'grado_crit',        type: 'string', mapping: 'grado_crit'},
        {name: 'tarea',             type: 'string', mapping: 'tarea'},
        {name: 'fecha_vto',         type: 'string', mapping: 'fecha_vto'},
        {name: 'id_estado_vto',     type: 'string', mapping: 'id_estado_vto'},
        {name: 'estado_vto',        type: 'string', mapping: 'estado_vto'},
        {name: 'fecha_alta',        type: 'string', mapping: 'fecha_alta'},
        {name: 'fecha_accion',      type: 'string', mapping: 'fecha_accion'},
        {name: 'usuario_alta',      type: 'string', mapping: 'usuario_alta'},
        {name: 'usuario_responsable',type: 'string', mapping: 'usuario_responsable'},
        {name: 'id_estado',         type: 'int',    mapping: 'id_estado'},
        {name: 'estado',            type: 'string', mapping: 'estado'},
        {name: 'area',              type: 'string', mapping: 'area'},
        {name: 'editada',           type: 'string', mapping: 'editada'},
        {name: 'obs',               type: 'string', mapping: 'obs'},
        {name: 'fuente',            type: 'string', mapping: 'fuente'},
        {name: 'th',                type: 'string', mapping: 'th'},
        {name: 'txt_cierre',        type: 'string', mapping: 'txt_cierre'},
        {name: 'fecha_cierre',      type: 'string', mapping: 'fecha_cierre'},
        {name: 'fecha_aprobada',    type: 'string', mapping: 'fecha_aprobada'},
      ]),
//      sortInfo:{field: 'id_tarea', direction: "ASC"},
      remoteSort: true
    });
//asigno el datastore al paginador
paginador.bindStore(tareasDataStore);

botonesTareasAction = new Ext.grid.ActionColumn({
		width: 15,
                editable:false,
                menuDisabled:true,
                header:'Acciónes',
                hideable:false,
                align:'left',
                width:  90,
                tooltip:'Acciones sobre la tarea',
                 hidden:false,
		items:[
                    {
                    icon:URL_BASE+'images/ver_historial.png',
                    iconCls :'col_accion',
                    tooltip:'Ver historial',
                    hidden: true,
//                    getClass:,
                    handler: clickBtnVerHisotrial
                }]
});

buscador= new Ext.ux.grid.Search({
    iconCls:'icon-zoom',
//    readonlyIndexes:['id_tarea', 'tarea','descshowBtnAprobarripcion'],
    disableIndexes:['descripcion','fecha_alta','fecha_vto','fecha_accion','estado','estado','area','grado_crit'],
    align:'right',
    minChars:3
});

//Filtros
checkVencidas = new Ext.form.Checkbox({
    id:'checkVencidas',
    name: 'checkVencidas',
    checked: false,
    fieldLabel: '',
    labelSeparator: '',
    boxLabel: ''
});
checkSoloMias= new Ext.form.Checkbox({
    id:'checkSoloMias',
    name: 'checkSoloMias',
    checked: false,
    fieldLabel: '',
    labelSeparator: '',
    boxLabel: ''
});

arrayEstadosTarea = new Ext.data.JsonStore({
	url: CARPETA+'/filtro_estados',
	root: 'rows',
//        method: 'POST',
	fields: ['id_estado', 'estado']
//        autoload: true
});
arrayEstadosTarea.load();
	
arrayEstadosTarea.on('load' , function(  js , records, options ){
											   
	var tRecord = Ext.data.Record.create(
		{name: 'id_estado', type: 'int'},
		{name: 'estado', type: 'string'}
	);
	var myNewT = new tRecord({
		id_estado: '-1',
		estado: 'Todos'
	});
	arrayEstadosTarea.insert( 0, myNewT);	
} );
var estadosTareaFiltro = new Ext.form.ComboBox({
    id:'estadosTareaFiltro',
    forceSelection : true,
    value: 'Todos',
    store: arrayEstadosTarea,
    editable : false,
    displayField: 'estado',
    valueField:'id_estado',
    allowBlank: false,
    selectOnFocus:true,
    width: 150, 
    triggerAction: 'all'
//    clearFilterOnReset : false
});
  
tareasColumnModel = new Ext.grid.ColumnModel(
    [{
        header: '#',
        readOnly: true,
        dataIndex: 'id_tarea',
        width: 55,        
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
        header: 'Estado Actual',
        dataIndex: 'estado',
        tooltip:'Estado Actual',
        sortable: true,
        width:  70,
        fixed:true,
        readOnly: true,
        renderer: showEstado,
        align:'center'
      },{
        header: 'ED Nro',
        dataIndex: 'fuente',
        sortable: false,
        width:  60,
        fixed:false,
//        readOnly: true,
        align:'center'
      },{
        header: 'Detalle del hallazgo',
        dataIndex: 'hallazgo',
        width:  220,
        sortable: false,
        renderer:showQtipHallazgo,
        readOnly: permiso_modificar
      },{
        header: '&deg;Crit',
        dataIndex: 'grado_crit',
        tooltip:'Grado de criticidad',
        sortable: true,
        width:  50,
        fixed:true,
        readOnly: true,
        renderer: showGrado,
        align:'center'
      },{
        header: 'Tarea a realizar',
        dataIndex: 'tarea',
        width:  270,
        sortable: false,
        renderer:showQtipTarea,
        readOnly: permiso_modificar
      },{
        header: 'Fecha Limite',
        dataIndex: 'fecha_vto',
        sortable: true,
        width:  80,
        fixed:true,
//        renderer:showFecha,
        readOnly: true,
        align:'center'
      },{
        header: 'Vto.',
        dataIndex: 'estado_vto',
        sortable: true,
        width:  60,
        fixed:true,
        readOnly: true,
        renderer: showEstadoVto,
        align:'center'
      },{
        header: 'Usuario Solicitante',
        dataIndex: 'usuario_alta',
        sortable: true,
        width:  150,
        align:'left'
      },{
        header: 'Usuario Responsable',
        dataIndex: 'usuario_responsable',
        sortable: true,
        width:  150,
        align:'left'
      },{
        header: 'Area Responsable',
        dataIndex: 'area',
        sortable: true,
        width:  100,
        renderer:showQtipArea,
        align:'left'
      },botonesTareasAction
//     ,{
//        header: 'Fecha Accion',
//        dataIndex: 'fecha_accion',
//        sortable: true,
//        width:  90,
//        fixed:true,
//        readOnly: true,
//        align:'center'
//      }
  ]
    );
  
   tareasListingGridPanel =  new Ext.grid.GridPanel({
        id: 'tareasListingGridPanel',
        store: tareasDataStore,
        cm: tareasColumnModel,
        enableColLock:false,
        trackMouseOver:true, //que se coloree cuando pasas el mouse por encima
        loadMask: true, //que te ponga el loading cuando se esta generando el store
//        renderTo: 'grillita',
        viewConfig: {
            forceFit: true
        },
        plugins:[buscador],
        clicksToEdit:2,
        height:500,
//        layout: 'fit',
        selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
        bbar:[paginador],
        tbar: [
            {
                text: 'Descargar listado',
    //            tooltip: 'e...',
                iconCls:'archivo_excel_ico',
                handler: clickBtnExcel
            }
            ,ESTACIO+'<b>Filtros: </b>'
            ,'&emsp;|&emsp;','<span style="color:#FF0000;">Vencidas</span>',checkVencidas
            ,'&emsp;|&emsp;','<span style="color:#000;">Mis tareas</span>',checkSoloMias
            ,'&emsp;|&emsp;','Estado',estadosTareaFiltro
            ,{
                text: 'Quitar Filtros',
    //            tooltip: 'e...',
                iconCls:'quitar_filtros',
                handler: clickBtnQuitarFiltros
            }
            /*, '-', { 
                text: 'Eliminar',
                tooltip: 'Eliminar la tarea seleccionada',
                handler: confirmDeletetareas,   // Confirm before deleting
                iconCls:'remove',
                hidden: !permiso_eliminar
            }*/
        ]
    });   

tareasDataStore.load({params: {start: 0, limit: TAM_PAGINA}});

tareasSuperiorPanel = new Ext.Panel(
{
        collapsible: false,
        split: false,
        header: true,
        title: 'Listado de Tareas generadas por Evaluaciones de Desempeño',
        region:'center',
        height: 300,
        minSize: 100,
        maxSize: 350,	
        margins: '0 5 5 5',
        html:'<p>panel superior</p>',
        layout: 'fit',
        items:[tareasListingGridPanel]
});

    var altura=Ext.getBody().getSize().height - 60;
    tareasListingGridPanel.setHeight(altura);

    Ext.getCmp('browser').on('resize',function(comp){
            tareasListingGridPanel.setWidth(this.getSize().width);
            tareasListingGridPanel.setHeight(Ext.getBody().getSize().height - 60);

    });
        
function showQtipHallazgo(value, metaData,record){
    var deviceDetail = record.get('hallazgo');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
    return value;
}
function showQtipTarea(value, metaData,record){
    var deviceDetail = record.get('tarea');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
    return value;
}
function showQtipArea(value, metaData,record){
    var deviceDetail = record.get('area');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
    return value;
}


function showEstado (value,metaData,superData){
    var estado=superData.json.id_estado;
    switch (estado)
    {
        case '1':
        metaData.attr = 'style="background-color:#01A9DB; color:#FFF;"';
        break;
        case '2':
        metaData.attr = 'style="background-color:#F7FE2E; color:#848484;"';
        break;
        case '3':
        metaData.attr = 'style="background-color:#DF7401; color:#FFF;"';
        break;
        case '4':
        metaData.attr = 'style="background-color:#037DA2; color:#FFF;"';
        break;
        case '5':
        metaData.attr = 'style="background-color:#FF0000; color:#FFF;"';
        break;
        case '6':
        metaData.attr = 'style="background-color:#A4A4A4; color:#FFF;"';
        break;
        case '7':
        metaData.attr = 'style="background-color:#FFFF00; color:#FFF;"';
        break;
        case '8':
        metaData.attr = 'style="background-color:#151515; color:#FFF;"';
        break;
        case '9':
        metaData.attr = 'style="background-color:#088A08; color:#FFF;"';
        break;
        case '10':
        metaData.attr = 'style="background-color:#DF7401; color:#FFF;"';
        break;
        
    }
    var deviceDetail = superData.get('obs');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
        
    return value;
}
function showEstadoVto (value,metaData,superData){
    var estado=superData.json.id_estado_vto;
    switch (estado)
    {
        case '0':
//        metaData.attr = 'style="background-color:#088A08; color:#FFF;"';
        metaData.attr = 'style="background-color:#FFF; color:#FFF;"';
        break;
        case '1':
        metaData.attr = 'style="background-color:#FF0000; color:#FFF;"';
        break;
        
    }
    var deviceDetail = superData.get('obs');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
        
    return value;
}
function showGrado (value,metaData,superData){
    var grado=superData.json.id_grado_crit;
    switch (grado)
    {
        case 1:
        case '1':
        metaData.attr = 'style="background-color:#DC0000; color:#FFF;"';
        break;
        case 2:
        case '2':
        metaData.attr = 'style="background-color:#DF7401; color:#FFF;"';
        break;
        case 3:
        case '3':
        metaData.attr = 'style="background-color:#088A08; color:#FFF;"';
        break;
        default:
        metaData.attr = 'style="background-color:#D0D0D0; color:#FFF;"';
    }
    return value;
}


estadosTareaFiltro.on('select', filtrarGrilla);
checkVencidas.on('check', filtrarGrilla);
checkSoloMias.on('check', filtrarGrilla);

function filtrarGrilla( combo, record, index ){
    var id_estado           =Ext.getCmp('estadosTareaFiltro').getValue();
    var vencidas            =Ext.getCmp('checkVencidas').getValue();
    var solo_mias           =Ext.getCmp('checkSoloMias').getValue();
    if(vencidas) vencidas='1'; else vencidas='0';
    if(solo_mias) solo_mias='1'; else solo_mias='0';
        
        
    
    var fields = [];
        fields.push('id_estado');
        fields.push('vencidas');
        fields.push('solo_mias');
    var values = [];
    values.push(id_estado);
    values.push(vencidas);
    values.push(solo_mias);
// 
	var encoded_array_f = Ext.encode(fields);
	var encoded_array_v = Ext.encode(values);
//	var encoded_array_v = Ext.encode(values);
    tareasDataStore.setBaseParam('filtros',encoded_array_v);
    tareasDataStore.load();
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
        Ext.getCmp('estadosTareaFiltro').reset();
        Ext.getCmp('checkVencidas').reset();
        Ext.getCmp('checkSoloMias').reset();
        tareasDataStore.setBaseParam('filtros','');
        tareasDataStore.load();
    }
    function clickBtnExcel (){
        Ext.Ajax.request({ 
            url: LINK_GENERICO+'/sesion',
            method: 'POST',
            waitMsg: 'Por favor espere...',
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
                        go_clickBtnExcel();
                        break;
                }
          },
            failure: function(response) {
                location.assign(URL_BASE_SITIO+"admin");
            }
        });
    }
    function go_clickBtnExcel(){
        var n=tareasDataStore.totalLength;
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
                            filtros:tareasDataStore.baseParams.filtros,
                            busqueda:tareasDataStore.baseParams.query,
                            campos:tareasDataStore.baseParams.fields,
                            sort:tareasDataStore.baseParams.sort,
                            dir:tareasDataStore.baseParams.dir
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
function msgProcess(titulo){
 Ext.MessageBox.show({
        title: titulo,
        msg: 'Procesando, por favor espere...',
        progress:true,
        progressText: 'Procesando...', 
        width:300, 
        wait:true, 
        waitConfig: {interval:200}
    });
}  
  
tareasListingGridPanel.on('cellclick',clickBtnVerHisotrial);
function clickBtnVerHisotrial(grid, rowIndex, columnIndex){
    console.log(columnIndex);
//    if(columnIndex<=10 || columnIndex==12)
//    {
        ID_TAREA=grid.store.data.items[rowIndex].data.id_tarea;
        historialTareaDataStore.load({params: {id:ID_TAREA,start: 0}});
        historialAccionesTareaDataStore.load({params: {id:ID_TAREA,start: 0}});
        tareasHistorialesPanel.setTitle('Historial de la Taréa Nro: '+ID_TAREA);
        tareasHistorialesPanel.show();
        tareasHistorialesPanel.expand(false);
//        return iDTramite;
//    }
}
tareasListingGridPanel.on('celldblclick', abrir_popup_tareasTareas);
function abrir_popup_tareasTareas(grid ,  rowIndex, columnIndex,  e){
    var data=grid.store.data.items[rowIndex].data;
                var panelContentTareas;
                var winTareasTareas;
                var enc=['<html>','<div class="tabla_popup_grilla">'];
                var pie=['<br class="popup_clear"/></div>','</html>'];
                var nodos=[
                            '<p>',
                                '<div class="titulo">Detalle Tarea Nro '+data.id_tarea+'<br></div>',
                            '</p>',
                            '<p>',
                                '<div class="col1">Fecha de Alta:</div>',
                                '<div class="col2">'+data.fecha_alta+'</div>',
                            '</p>',
                            '<p>',
                                '<div class="col1">Fecha Vencimiento:</div>',
                                '<div class="col2">'+data.fecha_vto+'</div>',
                            '</p>',
                            '<p>',
                                '<div class="col1">ED Nro:</div>',
                                '<div class="col2">'+data.fuente+'</div>',
                            '</p>',
                            '<p>',
                                '<div class="col1">Usuario Alta:</div>',
                                '<div class="col2">'+data.usuario_alta+'</div>',
                            '</p>',
                            '<p>',
                                '<div class="col1">Usuario Responsable:</div>',
                                '<div class="col2">'+data.usuario_responsable+'</div>',
                            '</p>',
                            '<p>',
                                '<div class="col1">Area:</div>',
                                '<div class="col2">'+data.area+'</div>',
                            '</p>',
                            '<p>',
                                '<div class="col1">Estado:</div>',
                                '<div class="col2">'+data.estado+'</div>',
                            '</p>',
                            '<p>',
                                '<div class="col1">Grado criticidad:</div>',
                                '<div class="col2">'+data.grado_crit+'</div>',
                            '</p>',
                            '<p>',
                                '<div class="col1">Hallazgo:</div>',
                                '<div class="col2_s">'+data.hallazgo+'</div>',
                            '</p>',
                            '<p>',
                                '<div class="col1">Tarea:</div>',
                                '<div class="col2_s">'+data.tarea+'</div>',
                            '</p>'
                ];
                
                if (data.obs!="")
                {
                    nodos.push('<p>');
                    nodos.push('<div class="col1"><div class ="col_rechazo">Motivo rechazo:</div></div>');
                    nodos.push('<div class="col2_s"><div class ="col_rechazo">'+data.obs+'</div></div>');
                    nodos.push('</p>');
                }
                if (data.id_estado==2 || data.id_estado>=9)
                {
                    nodos.push('<p>');
                    nodos.push('<div class="col1"><div class ="col_cerrada">Resolución:</div></div>');
                    nodos.push('<div class="col2_s"><div class ="col_cerrada">'+data.txt_cierre+'</div></div>');
                    nodos.push('</p>');
                    nodos.push('<p>');
                    nodos.push('<div class="col1"><div class ="col_cerrada">Fecha:</div></div>');
                    nodos.push('<div class="col2"><div class ="col_cerrada">'+data.fecha_cierre+'</div></div>');
                    nodos.push('</p>');
                }
                if (data.id_estado>=9)
                {
                    nodos.push('<p>');
                    nodos.push('<div class="col1"><div class ="col_cerrada">Fecha Aprobada:</div></div>');
                    nodos.push('<div class="col2"><div class ="col_cerrada">'+data.fecha_aprobada+'</div></div>');
                    nodos.push('</p>');
                }
                
                var html = enc.concat(nodos);
                var html = html.concat(pie);

                        winTareasTareas = new Ext.Window({
                                title: 'Tarea Nro '+data.id_tarea,
                                closable: true,
                                modal:true,
                                //closeAction: 'hide',
                                width: 790,
                                boxMinWidth:790,
                                height: 550,
                                boxMinHeight:550,
                                plain: true,
                                autoScroll:true,
                                layout: 'absolute',
                                html: html.join(''),
//                                items: [],
                                buttons: [{
                                        text: 'Cerrar',
                                        handler: function(){
                                                winTareasTareas.hide();
                                                winTareasTareas.destroy();

                                        }
                                }]
                        });
//                };
                winTareasTareas.show();

}
