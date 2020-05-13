
cppEventosDataStore = new Ext.data.Store({
    id: 'cppEventosDataStore',
    proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/listado', 
            waitMsg: 'Por favor espere...',
            method: 'POST'
    }),
      baseParams:{limit: TAM_PAGINA}, // this parameter is passed for any HTTP request
      reader: new Ext.data.JsonReader({
        root: 'rows',
        totalProperty: 'total',
        id: 'id'
      },[ 
        {name: 'id',                type: 'int',    mapping: 'id'},        
        {name: 'usuario_alta',      type: 'string', mapping: 'usuario_alta'},
        {name: 'fecha_alta',        type: 'string', mapping: 'fecha_alta'},
        {name: 'fh_ini',            type: 'string', mapping: 'fh_ini'},
        {name: 'fh_fin',            type: 'string', mapping: 'fh_fin'},
        {name: 'estado',            type: 'string', mapping: 'estado'},
        {name: 'id_estado',         type: 'string', mapping: 'id_estado'},
        {name: 'fecha_evento',      type: 'string', mapping: 'fecha_evento'},
        {name: 'descripcion',       type: 'string', mapping: 'descripcion'},
        {name: 'equipo',            type: 'string', mapping: 'equipo'},
        {name: 'producto',          type: 'string', mapping: 'producto'},
        {name: 'sector',            type: 'string', mapping: 'sector'},
        {name: 'criticidad',        type: 'string', mapping: 'criticidad'},
        {name: 'hrs',               type: 'float',  mapping: 'hrs'},
        {name: 'monto',             type: 'float',  mapping: 'monto'},
        {name: 'unidades_perdidas', type: 'float',  mapping: 'unidades_perdidas'},
        {name: 'ver_ini',            type: 'string', mapping: 'ver_ini'},
        {name: 'ver_fin',            type: 'string', mapping: 'ver_fin'},
        {name: 'ver_rango',          type: 'string', mapping: 'ver_rango'},
        {name: 'fecha_cierre',      type: 'string', mapping: 'fecha_cierre'},
        {name: 'btn_verificar_tareas',            type: 'string', mapping: 'btn_verificar_tareas'}
      ]),
//      sortInfo:{field: 'id', direction: "ASC"},
      remoteSort: true
    });
//    cppEventosDataStore.on('load' , function( ){
//     cppTabs_panel.collapse(true);
//});
//asigno el datastore al paginadorEventos
paginadorEventos= new Ext.PagingToolbar({
    pageSize: parseInt(TAM_PAGINA),
    displayInfo: true,
    beforePageText:'Página',
    afterPageText:'de {0}',
    displayMsg:'Mostrando {0} - {1} de <b>{2}</b>',
    firstText:'Primera Página',
    lastText:'Última Página',
    prevText:'Anterior',
    nextText:'Siguiente',
    refreshText:'Actualizar',
    buttonAlign:'left',
    emptyMsg:'No hay registros para listar'
});
paginadorEventos.bindStore(cppEventosDataStore);

botonesCppAction = new Ext.grid.ActionColumn({
    edicpple:false,
    menuDisabled:true,
    header:'Acciones',
    hideable:false,
    align:'left',
    width:  150,
    tooltip:'Acciones ',
     hidden:false,
    items:[
        {
        icon:URL_BASE+'images/calificar.png',
        iconCls :'col_accion',
        tooltip:'Asignar consecuencias',
        hidden: true,
        getClass:showBtnCalificar,
        handler: clickBtnCalificarEvento
        },
        {
        icon:URL_BASE+'images/investigadores.png',
        iconCls :'col_accion',
        tooltip:'Definir equipo de investigación',
        hidden: true,
        getClass:showBtnEI,
        handler: clickBtnDesignarInvest
        },
        {
        icon:URL_BASE+'images/set_criticidad.png',
        iconCls :'col_accion',
        tooltip:'Definir/Editar criticidad',
        hidden: true,
        getClass:showBtnEditCriticidad,
        handler: clickBtnEditCriticidadEvento
        },
        {
        icon:URL_BASE+'images/causa.png',
        iconCls :'col_accion',
        tooltip:'Definir causa',
        hidden: true,
        getClass:showBtnCausa,
        handler: clickBtnNuevaCausa
        },
        {
        icon:URL_BASE+'images/edit-find.png',
        iconCls :'col_accion',
        tooltip: 'Editar evento',
        hidden:true,
        getClass:showBtnEditarEvento,
        handler: clickBtnNuevoEventoFunc
        },
        {
        icon:URL_BASE+'images/rechazar4.png',
        iconCls :'col_accion',
        tooltip:'Cancela Evento',
        hidden: true,
        getClass:showBtnCancelar,
        handler: clickBtnCancelar
        },
        {
        icon:URL_BASE+'images/aprobar2.png',
        iconCls :'col_accion',
        tooltip:'Cerrar Evento',
        hidden: true,
        getClass:showBtnCerrar,
        handler: clickBtnCierraEvento //clickBtnCerrar
        },
        {
        icon:URL_BASE+'images/eliminar4.png',
        iconCls :'col_accion',
        tooltip:'Eliminar Evento',
        hidden: true,
        getClass:showBtnEliminar,
        handler: clickBtnEliminar
        }
    ]
});

cppEventosBuscador= new Ext.ux.grid.Search({
    iconCls:'icon-zoom',
//    readonlyIndexes:[''],
    disableIndexes:['id','fecha_alta','estado','fecha_evento','criticidad','hrs','monto','unidades_perdidas','fh_ini','fh_fin','equipo'],
    align:'right',
    minChars:5
});

//Filtros
arrayfiltroCriticidad = new Ext.data.JsonStore({
    url: CARPETA+'/filtroCriticidad',
    root: 'rows',
//        method: 'POST',
    fields: ['id_criticidad', 'criticidad']
//        autoload: true
});
//arrayfiltroCriticidad.load();	
arrayfiltroCriticidad.on('load' , function(  js , records, options ){
    var tRecord = Ext.data.Record.create(
        {name: 'id_criticidad', type: 'int'},
        {name: 'criticidad',    type: 'string'}
    );
    var myNewT = new tRecord({
        id: '-1',
        criticidad: 'Todas'
    });
	arrayfiltroCriticidad.insert( 0, myNewT);	
});
arrayfiltroEstado = new Ext.data.JsonStore({
    url: CARPETA+'/filtroEstado',
    root: 'rows',
//        method: 'POST',
    fields: ['id_estado', 'estado']
//        autoload: true
});
//arrayfiltroCriticidad.load();	

arrayfiltroEstado.on('load' , function(  js , records, options ){
    var tRecord = Ext.data.Record.create(
        {name: 'id_estado', type: 'int'},
        {name: 'estado',    type: 'string'}
    );
    var myNewT = new tRecord({
        id: '-1',
        criticidad: 'Todos'
    });
	arrayfiltroCriticidad.insert( 0, myNewT);	
});
var cppFiltroCriticidad = new Ext.form.ComboBox({
    id:'cppFiltroCriticidad',
    forceSelection : true,
    value: 'Todas',
    store: arrayfiltroCriticidad,
    edicpple : false,
    displayField: 'criticidad',
    valueField:'id_criticidad',
    allowBlank: false,
    selectOnFocus:true,
    width: 150, 
    triggerAction: 'all'
//    clearFilterOnReset : false
});

var cppFiltroEstado = new Ext.form.ComboBox({
    id:'cppFiltroEstado',
    forceSelection : true,
    value: 'Todos',
    store: arrayfiltroEstado,
    edicpple : false,
    displayField: 'estado',
    valueField:'id_estado',
    allowBlank: false,
    selectOnFocus:true,
    width: 150, 
    triggerAction: 'all'
//    clearFilterOnReset : false
});
//Fin Filtros
  
cppEventosColumnModel = new Ext.grid.ColumnModel(
    [{
        header: '#',
        readOnly: true,
        dataIndex: 'id',
        width: 30,        
//        fixed:true,
        sortable: true,
        renderer: function(value, cell){
            cell.css = "readonlycell";
            return value;
        },
        hidden: false
      },{
        header: 'Usuario Alta',
        dataIndex: 'usuario_alta',
        sorcpple: true,
        width:  130,
//        fixed:true,
        readOnly: true,
        renderer: showTooltip,
//        readOnly: permiso_modificar
        align:'left'
      },{
        header: 'Fecha Alta',
        dataIndex: 'fecha_alta',
        sortable: true,
        width:  80,
        fixed:true,
        readOnly: true,
        renderer: showTooltip,
        align:'center'
      },{
        header: 'Inicio',
        dataIndex: 'fh_ini',
        sortable: true,
        width:  110,
        fixed:true,
        readOnly: true,
        renderer: showTooltip,
        align:'center'
      },{
        header: 'Fin',
        dataIndex: 'fh_fin',
        sortable: true,
        width:  110,
        fixed:true,
        readOnly: true,
        renderer: showTooltip,
        align:'center'
      },{
        header: 'Estado',
        dataIndex: 'estado',
        sortable: false,
        width:  90,
        fixed:true,
        readOnly: true,
        renderer: showEstado,
        align:'center'
//      },{
//        header: 'Fecha Evento',
//        dataIndex: 'fecha_evento',
//        sortable: true,
//        width:  80,
//        fixed:true,
//        readOnly: true,
//        renderer: showTooltip,
//        align:'center'
      },{
        header: 'Descripcion',
        dataIndex: 'descripcion',
        sorcpple: true,
        width:  250,
        fixed:true,
        readOnly: true,
        renderer: showTooltip,
        align:'left'
      },{
        header: 'Equipo/s',
        dataIndex: 'equipo',
        sorcpple: true,
        width:  150,
        fixed:true,
        readOnly: true,
        renderer: showTooltipEquipo,
        align:'center'
      },{
        header: 'Producto',
        dataIndex: 'producto',
        sortable: true,
        width:  150,
        fixed:false,
        readOnly: true,
        renderer: showTooltipProducto,
        align:'left'
      },{
        header: 'Sector',
        dataIndex: 'sector',
        sorcpple: true,
        width:  120,
        fixed:true,
        readOnly: true,
        renderer: showTooltip,
        align:'center'
      },{
        header: 'Criticidad',
        dataIndex: 'criticidad',
        sorcpple: false,
        width:  80,
//        fixed:false,
        readOnly: true,
        renderer: showCriticidadEvento,
        align:'center'
      },{
        header: 'Hrs',
        dataIndex: 'hrs',
        sorcpple: true,
        width:  70,
        fixed:false,
        readOnly: true,
        tooltip:'Horas perdidas',
        renderer: showHrs,
        align:'right'
      },{
        header: 'TN',
        dataIndex: 'unidades_perdidas',
        sorcpple: true,
        width:  80,
        fixed:false,
        readOnly: true,
        tooltip:'Toneladas perdidas',
        renderer: showToneladas,
        align:'right'
      },{
        header: 'U$S',
        dataIndex: 'monto',
        sorcpple: true,
        width:  80,
        fixed:false,
        readOnly: true,
        tooltip:'Monto U$S perdidas',
        renderer: showMonto,
        align:'right'
      },{
        header: 'Fecha Cierre',
        dataIndex: 'fecha_cierre',
        sortable: true,
        width:  80,
        fixed:true,
        readOnly: true,
        renderer: showTooltip,
        align:'center'
      },{
        hidden:false,
        header: 'Plazo Verificación',
        tooltip:'Fechas Verificación',
        dataIndex: 'ver_rango',
        sortable: true,
        width:  120,
        fixed:true,
        readOnly: true,
        renderer: showRangoPlazoVer,
        align:'center'
    },botonesCppAction
  ]
    );
  
   cppEventosGridPanel =  new Ext.grid.GridPanel({
        id: 'cppEventosGridPanel',
        store: cppEventosDataStore,
        cm: cppEventosColumnModel,
        autoScroll:true,
        enableColLock:false,
        trackMouseOver:true, 
        loadMask: true, 
//        viewConfig: {
//            forceFit: true
//        },
        plugins:[cppEventosBuscador],
        clicksToEdit:2,
        height:500,
//        layout: 'fit',
        selModel: new Ext.grid.RowSelectionModel({singleSelect:true}),
        bbar:[paginadorEventos],
        tbar: [
            {
                text: 'Nuevo Evento',
                tooltip: 'Alta nuevo evento',
                iconCls:'add',                     
                handler: clickBtnNuevoEventoFunc1
             }
            ,'&emsp;|&emsp;'
            ,{
                text: 'Descargar listado',
    //            tooltip: 'e...',
                iconCls:'archivo_excel_ico',
                handler: clickBtnEventosExcel
            }
            ,'&emsp;|&emsp;','<b>Filtros: </b>'
            ,'&emsp;|&emsp;','Criticidad:'
            ,cppFiltroCriticidad
            ,'&emsp;|&emsp;','Estado:'
            ,cppFiltroEstado
            ,{
                text: 'Quitar Filtros',
    //            tooltip: 'e...',
                iconCls:'quitar_filtros',
                handler: clickBtnQuitarFiltros
            }
        ]
    });   

cppEventosDataStore.load({params: {start: 0, limit: TAM_PAGINA}});

cppEventosPanel = new Ext.Panel(
{
        collapsible: false,
        split: false,
        header: true,
        title: 'Listado de eventos',
        region:'center',
        height: 300,
        minSize: 100,
        maxSize: 350,	
        margins: '0 5 5 5',
        html:'<p>panel superior</p>',
        layout: 'fit',
        items:[cppEventosGridPanel]
});
    var altura=Ext.getBody().getSize().height - 60;
    cppEventosGridPanel.setHeight(altura);

cppFiltroCriticidad.on('select', filtrarGrilla);
cppFiltroEstado.on('select', filtrarGrilla);

function filtrarGrilla( combo, record, index ){
        var id_criticidad = Ext.getCmp('cppFiltroCriticidad').getValue();
        var id_estado = Ext.getCmp('cppFiltroEstado').getValue();
        cppTabs_panel.collapse(true);
        var values = [];
            values.push(id_criticidad);
            values.push(id_estado);
            
	var encoded_array_v = Ext.encode(values);
        
        cppEventosDataStore.setBaseParam('filtros',encoded_array_v);
        cppEventosDataStore.load();
    
};
function clickBtnQuitarFiltros(){
    cppTabs_panel.collapse(true);
    var filtro1=Ext.getCmp('cppFiltroCriticidad');
    var filtro2=Ext.getCmp('cppFiltroEstado');
    filtro1.reset();
    filtro2.reset();
    cppEventosDataStore.setBaseParam('filtros','');
    cppEventosDataStore.load();
};
    
cppEventosGridPanel.on('celldblclick', abrir_popup_cppEventos);
function abrir_popup_cppEventos(grid ,  rowIndex, columnIndex,  e){
    var data=grid.store.data.items[rowIndex].data;
//    var grid= Ext.getCmp('cppEventosGridPanel');
    var cm= grid.getColumnModel();
    var winCppEventos;
    var enc=['<html>','<div class="tabla_popup_grilla">'];
    var pie=['<br class="popup_clear"/></div>','</html>'];
    var cppla=['<p><div class="titulo">Detetalle de evento nro'+data.id+'<br></div></p>'];
    
    cm.config.forEach(function(a)
    {
        if(a.header != "Acciones")
        {
            if (data[a.dataIndex]!="")
            {
                var nodo=['<p><div class="col1">'+a.header+':</div><div class="col2">'+data[a.dataIndex]+'</div></p>'];
            }
            else
            {
                var nodo=['<p><div class="col1">'+a.header+':</div><div class="col2">s/d</div></p>'];
            }
            cppla.push(nodo);
        }
        
    });

    var html = enc.concat(cppla);
    var html = html.concat(pie);

    winCppEventos = new Ext.Window({
            title: 'Detalle del evento nro '+data.id,
            closable: true,
            modal:true,
            //closeAction: 'hide',
            width: 650,
            boxMinWidth:650,
            height: 500,
            boxMinHeight:550,
            plain: true,
            autoScroll:true,
            layout: 'absolute',
            html: html.join(''),
//                                items: [],
            buttons: [{
                    text: 'Cerrar',
                    handler: function(){
                            winCppEventos.hide();
                            winCppEventos.destroy();

                    }
            }]
    });
//                };
                winCppEventos.show();

}

function showTooltipProducto(value, metaData,record){
    var prod = record.get('producto');
//    console.log(prod);
    if (prod == '')
    {
        value = 'Sin productos asociados';
        metaData.attr = 'style="background-color:#D0D0D0; color:#FFF;"'; //gris
        metaData.attr += 'ext:qtip="'+ value + '"';
    }
    else
    {
        metaData.attr += 'ext:qtip="'+ value + '"';
    }
    return value;
}

function showTooltipEquipo(value, metaData,record){
    var equipo = record.get('equipo');

    if (equipo == '')
    {
        value = 'Sin equipos asociados';
        metaData.attr = 'style="background-color:#D0D0D0; color:#FFF;"'; //gris
        metaData.attr += 'ext:qtip="'+ value + '"';
    }
    else
    {
        metaData.attr += 'ext:qtip="'+ value + '"';
    }
    return value;
}

function showCriticidadEvento (value,metaData,superData){
    var id=superData.json.id_criticidad;
    var deviceDetail = superData.json.criticidad;

    switch (id)
    {
        case '1': //critica
            metaData.attr = 'style="background-color:#FF0000; color:#FFF;"'; //Rojo
            metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
            break;
        case '2': //alta
            metaData.attr = 'style="background-color:#DF7401; color:#FFF;"'; //Naranja
            metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
            break;
        case '3': //Menor
            metaData.attr = 'style="background-color:#088A08; color:#FFF;"';//verde
            metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
            break;
        default:
            break;
    }
    return value;
};
function showRangoPlazoVer (value,metaData,superData){
    var estado = superData.json.estado_ver;
     var deviceDetail = value;
    switch (estado)
    {
        case '0': //Pendiente
            metaData.attr = 'style="background-color:#01A9DB; color:#FFF;"'; //azul
            metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
            break;
        case '1': //Evaluado
            metaData.attr = 'style="background-color:#088A08; color:#FFF;"';//verde
            metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
            break;
        case '2': //Rechazado
            metaData.attr = 'style="background-color:#FF0000; color:#FFF;"'; //Rojo
            metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
            break;
        case '3': //Vencida
            metaData.attr = 'style="background-color:#DF7401; color:#FFF;"'; //Naranja
            metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
            break;
        default:
            break;
    }
    return value;
};

function showEstado (value,metaData,superData)
{
    var estilo_color = superData.json.estilo_color;
    var id_estado    = superData.json.id_estado;
    if(id_estado == 5 || id_estado == 4)
    {
        metaData.attr = 'style="background-color:'+estilo_color+'; color:#000;"';
        metaData.attr += 'ext:qtip="'+ value + '"';
    }
    else
    {
        metaData.attr = 'style="background-color:'+estilo_color+'; color:#FFF;"';
        metaData.attr += 'ext:qtip="'+ value + '"';
    }
    return value;
}
function showMonto (value,metaData,superData)
{
    value = Ext.util.Format.number(value,'0.000,00/i');
    var set_monto = superData.json.set_monto;
    if(set_monto == 0)
    {
        value = 'Sin Monto';
        metaData.attr = 'style="background-color:#DF0101; color:#DF0101;"';
        metaData.attr += 'ext:qtip="'+ value + '"';
    }
    else
    {
        metaData.attr += 'ext:qtip="'+ value + '"';
    }
    return value;
}

function showBtnCalificar(value,metaData,record){
    var btn_calificar = record.json.btn_calificar;
    
    if(btn_calificar == 1)
        return 'x-grid-center-icon'; 
    else
        return 'x-hide-display';       
};

function showBtnCancelar(value,metaData,record){
    var btn_cancelar = record.json.btn_cancelar;
    
    if(btn_cancelar == 1)
        return 'x-grid-center-icon'; 
    else
        return 'x-hide-display';  
                       
};

function showBtnCriticidad(value,metaData,record){
    var btn_crit = record.json.btn_crit;
    
    if(btn_crit == 1)
        return 'x-grid-center-icon'; 
    else
        return 'x-hide-display';  
                       
};

function showBtnEditCriticidad(value,metaData,record){
    var btn_crit = record.json.btn_crit;
    var btn_edit_crit = record.json.btn_edit_crit;
    
    if(btn_crit == 1 || btn_edit_crit == 1)
        return 'x-grid-center-icon';
    else
        return 'x-hide-display';  
                       
};

function showBtnEI(value,metaData,record){
    var btn_ei = record.json.btn_ei;
    
    if(btn_ei == 1)
        return 'x-grid-center-icon'; 
    else
        return 'x-hide-display';  
                       
};

function showBtnCausa(value,metaData,record){
    var btn_causa = record.json.btn_causa;
    
    if(btn_causa == 1)
        return 'x-grid-center-icon'; 
    else
        return 'x-hide-display'; 
                       
};

function clickBtnCancelar(grid,rowIndex,colIndex,item ,event){
    var id=grid.getStore().getAt(rowIndex).json.id;
    Ext.MessageBox.confirm('Cancelar','¿Confirma que desea cancelar el evento Número '+id+'?', 
    function(btn, text){
        if(btn=='yes'){
            var id=grid.getStore().getAt(rowIndex).json.id;
            msgProcess('Procesando...');
                Ext.Ajax.request({   
                    waitMsg: 'Por favor espere...',
                    url: CARPETA+'/cancelarEvento',
                    method: 'POST',
                    params: {
                        id_evento :id
                    }, 
                    success: function(response){
                    var result=eval(response.responseText);
                    switch(result.msg){
                    case 0:
                        Ext.MessageBox.alert('Operación OK','Evento cancelado.');
                        cppEventosDataStore.reload();    
                        break;
                    default:
                        Ext.MessageBox.alert('Error',result.msg);
                        break;
                    }        
                    },
                    failure: function(response){
                        var result=eval(response.responseText);
                        Ext.MessageBox.alert('error','No se pudo conectar con la base de datos. Intente mas tarde');      
                    }                      
                });
        }
     });
  };

function showBtnEliminar(value,metaData,record){
    var btn_eliminar = record.json.btn_eliminar;
            
    if(btn_eliminar == 1)
        return 'x-grid-center-icon'; 
    else
        return 'x-hide-display'; 
                       
};
function clickBtnEliminar(grid,rowIndex,colIndex,item ,event){
    var id=grid.getStore().getAt(rowIndex).json.id;
    Ext.MessageBox.confirm('Cancelar','¿Confirma que desea eliminar el evento Número '+id+'?', 
    function(btn, text){
        if(btn=='yes'){
            var id=grid.getStore().getAt(rowIndex).json.id;
            msgProcess('Procesando...');
                Ext.Ajax.request({   
                    waitMsg: 'Por favor espere...',
                    url: CARPETA+'/eliminarEvento',
                    method: 'POST',
                    params: {
                        id_evento :id
                    }, 
                    success: function(response){
                    var result=eval(response.responseText);
                    switch(result.msg){
                    case 0:
                        Ext.MessageBox.alert('Operación OK','Evento eliminado.');
                        cppEventosDataStore.reload();    
                        break;
                    default:
                        Ext.MessageBox.alert('Error',result.msg);
                        break;
                    }        
                    },
                    failure: function(response){
                        var result=eval(response.responseText);
                        Ext.MessageBox.alert('error','No se pudo conectar con la base de datos. Intente mas tarde');      
                    }                      
                });
        }
     });
  };
  function showBtnCerrar(value,metaData,record){
    var btn_cerrar = record.json.btn_cerrar;
            
    if(btn_cerrar == 1)
        return 'x-grid-center-icon'; 
    else
        return 'x-hide-display'; 
                       
};
//function clickBtnCerrar(grid,rowIndex,colIndex,item ,event){
//    var id=grid.getStore().getAt(rowIndex).json.id;
//    Ext.MessageBox.confirm('Cerrar','¿Confirma que desea cerrar el evento Número '+id+'?', 
//    function(btn, text){
//        if(btn=='yes'){
//            var id=grid.getStore().getAt(rowIndex).json.id;
//            msgProcess('Procesando...');
//                Ext.Ajax.request({   
//                    waitMsg: 'Por favor espere...',
//                    url: CARPETA+'/cerrarEvento',
//                    method: 'POST',
//                    params: {
//                        id_evento :id
//                    }, 
//                    success: function(response){
//                    var result=eval(response.responseText);
//                    switch(result.msg){
//                    case 0:
//                        Ext.MessageBox.alert('Operación OK','Evento cerrado.');
//                        cppEventosDataStore.reload();    
//                        break;
//                    default:
//                        Ext.MessageBox.alert('Error',result.msg);
//                        break;
//                    }        
//                    },
//                    failure: function(response){
//                        var result=eval(response.responseText);
//                        Ext.MessageBox.alert('error','No se pudo conectar con la base de datos. Intente mas tarde');      
//                    }                      
//                });
//        }
//     });
//  };
  
function clickBtnNuevoEventoFunc(grid,rowIndex,colIndex,item,event){
    var id=grid.getStore().getAt(rowIndex).json.id;
//    console.log(id);
    clickBtnNuevoEvento(grid,rowIndex,colIndex,item,event,id);

};
function clickBtnNuevoEventoFunc1(grid,rowIndex,colIndex,item,event){
    var id = 0;
//    console.log(id);
    clickBtnNuevoEvento(grid,rowIndex,colIndex,item,event,id);

};
  function showBtnEditarEvento(value,metaData,record){
    var btn_edit_evento = record.json.btn_edit_evento;
            
    if(btn_edit_evento == 1)
        return 'x-grid-center-icon'; 
    else
        return 'x-hide-display'; 
                       
};

function showTooltip(value, metaData,record){
   if ( value   != '')
   {
       metaData.attr += 'ext:qtip="'+ value + '"';
   }
   return value;
   }

function showToneladas(value, metaData,record){
    value = Ext.util.Format.number(value,'0.000,00/i');
    var unidades_perdidas = record.get('unidades_perdidas');
    if (unidades_perdidas != 0)
    {
        metaData.attr += 'ext:qtip="'+ value + '"';
    }
    else
    {
        metaData.attr += 'ext:qtip= "Sin valor asignado"';
    }
    return value;
}

function showHrs(value, metaData,record){
    value = Ext.util.Format.number(value,'0.000,00/i');
    var hrs = record.get('hrs');
    metaData.attr += 'ext:qtip="'+ value + '"';
    return value;
}


