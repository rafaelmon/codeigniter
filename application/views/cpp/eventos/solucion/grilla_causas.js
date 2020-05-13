
cppCausasDataStore = new Ext.data.Store({
    id: 'cppCausasDataStore',
    proxy: new Ext.data.HttpProxy({
        url: CARPETA + '/listado_causas',
        method: 'POST'
    }),
    baseParams: {tampagina: TAM_PAGINA}, // this parameter is passed for any HTTP request
    reader: new Ext.data.JsonReader({
        root: 'rows',
        totalProperty: 'total',
        id: 'id_causa'
    }, [
        {name: 'id_causa',          type: 'int',    mapping: 'id_causa'},
        {name: 'ac',                type: 'string', mapping: 'ac'},
        {name: 'causa_raiz',        type: 'string', mapping: 'causa_raiz'},
        {name: 'causa_inmediata',   type: 'string', mapping: 'causa_inmediata'}
    ]),
      sortInfo:{field: 'id_causa', direction: "ASC"},
    remoteSort: true
});
cppCausasPaginador= new Ext.PagingToolbar({
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
cppCausasPaginador.bindStore(cppCausasDataStore);

botonesCppCausasAction = new Ext.grid.ActionColumn({
    width: 15,
    edicpple:false,
    menuDisabled:true,
    header:'Acciones',
    hideable:false,
    align:'left',
    width:  90,
    tooltip:'Acciones ',
     hidden:false,
    items:[
        {
        icon:URL_BASE+'images/add.gif',
        iconCls :'col_accion',
        tooltip:'Definir Medida correctiva - Tarea',
        hidden: true,
        getClass:showBtnTarea,
        handler: clickBtnNuevaTarea
        }
    ]
});


cppCausasColumnModel = new Ext.grid.ColumnModel(
    [{
        header: '#',
        readOnly: true,
        dataIndex: 'id_causa',
        width: 30,
        sortable: true,
        renderer: function (value, cell) {
            cell.css = "readonlycell";
            return value;
        },
        hidden: false
    }, {
        header: 'Area Causante',
        dataIndex: 'ac',
        sortable: false,
        width: 80,
        fixed: true,
        readOnly: true,
        align: 'center'
    }, {
        header: 'Causa Raiz',
        dataIndex: 'causa_raiz',
        width: 210,
        sortable: true,
//        renderer: showQtip,
        readOnly: permiso_modificar
    }, {
        header: 'Causa Inmediata',
        dataIndex: 'causa_inmediata',
        width: 210,
        sortable: true,
//        renderer: showQtip,
        readOnly: permiso_modificar
    }
    ,botonesCppCausasAction
    ]
);

cppCausasGridPanel = new Ext.grid.GridPanel({
    id: 'cppCausasGridPanel',
    store: cppCausasDataStore,
    cm: cppCausasColumnModel,
    title: 'Listado de Causas para el evento Nro:',
    header: true,
    enableColLock: false,
    trackMouseOver: true, //que se coloree cuando pasas el mouse por encima
    loadMask: true, //que te ponga el loading cuando se esta generando el store
    viewConfig: {
        forceFit: false
    },
//    layout: 'fit',
//    plugins: [],
//    height: 300,
    bbar: [cppCausasPaginador],
    tbar: [],
    collapsible: true,
    collapsed: false,
//    split: false,
    region: 'west',
//    width: 300,
    minSize: 100,
    maxSize: 350,
    margins: '0 5 5 5',
    border : false,
    layoutConfig:{
                    animate:true
                },
});
cppCausasGridPanel.setHeight(ALT_INF);

function showBtnTarea(value,metaData,record){
    var grid=Ext.getCmp('cppEventosGridPanel');
    var sm=grid.getSelectionModel();
    
    if(sm.selections.items.length == 1)
    {
        var row=sm.getSelected();   
        var btn_tarea=row.json.btn_tarea;

        if(btn_tarea == 1)
            return 'x-grid-center-icon'; 
        else
            return 'x-hide-display';  
    }  
                       
};

cppCausasGridPanel.on('celldblclick', abrir_popup_cppCausas);
function abrir_popup_cppCausas(grid ,  rowIndex, columnIndex,  e){
    var data=grid.store.data.items[rowIndex].data;
//    var grid= Ext.getCmp('cppEventosGridPanel');
    var cm= grid.getColumnModel();
    var WinCppICausas
    var enc=['<html>','<div class="tabla_popup_grilla">'];
    var pie=['<br class="popup_clear"/></div>','</html>'];
    var cppla=['<p><div class="titulo">Detetalle de causa nro '+data.id_causa+'<br></div></p>'];
    
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

    WinCppCausas = new Ext.Window({
            title: 'Detalle de la causa nro '+data.id_causa,
            closable: true,
            modal:true,
            //closeAction: 'hide',
            width: 550,
            boxMinWidth:550,
            height: 250,
            boxMinHeight:250,
            plain: true,
            autoScroll:true,
            layout: 'absolute',
            html: html.join(''),
//                                items: [],
            buttons: [{
                    text: 'Cerrar',
                    handler: function(){
                            WinCppCausas.hide();
                            WinCppCausas.destroy();

                    }
            }]
    });
    WinCppCausas.show();

}

