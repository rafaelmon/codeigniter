/*** grilla consecuencias ***/
consecuenciasDataStore = new Ext.data.Store({
    id: 'consecuenciasDataStore',
    proxy: new Ext.data.HttpProxy({
              url: CARPETA+'/listado_cons', 
              method: 'POST'
          }),
    baseParams:{tampagina: TAM_PAGINA}, // this parameter is passed for any HTTP request
    reader: new Ext.data.JsonReader({
      root: 'rows',
      totalProperty: 'total',
      id: 'id_ec'
    },[ 
      {name: 'id_ec',               type: 'int',        mapping: 'id_ec'},        
      {name: 'consecuencia',        type: 'string',     mapping: 'consecuencia'},
      {name: 'descripcion',         type: 'string',     mapping: 'descripcion'},
      {name: 'fecha_alta',          type: 'string',     mapping: 'fecha_alta'},
      {name: 'fecha_val',           type: 'string',     mapping: 'fecha_val'},
      {name: 'monto',               type: 'float',      mapping: 'monto'},
      {name: 'unidades_perdidas',   type: 'float',      mapping: 'unidades_perdidas'}
    ]),
    sortInfo:{field: 'id', direction: "ASC"},
    remoteSort: true
});
botonesCppConsecuenciasAction = new Ext.grid.ActionColumn({
    
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
        icon:URL_BASE+'images/bascula.png',
        iconCls :'col_accion',
        tooltip:'Toneladas pérdidas',
        hidden: true,
        getClass:showBtnTn,
        handler: clickBtnToneladasPerdidas
        },
        {
        icon:URL_BASE+'images/monto.png',
        iconCls :'col_accion',
        tooltip:'Monto pérdido',
        hidden: true,
        getClass:showBtnMonto,
        handler: clickBtnMonto
        }
//        {
//        icon:URL_BASE+'images/delete.gif',
//        iconCls :'col_accion',
//        tooltip:'Borrar consecuencia',
//        hidden: true,
//        getClass:showBtnValorizar,
//       handler: clickBtnValoracion
//        }
    ]
});
cppConsecuenciasBuscador= new Ext.ux.grid.Search({
    iconCls:'icon-zoom',
//    readonlyIndexes:[''],
    disableIndexes:[],
    align:'right',
    minChars:5
});
cppConsecuenciasPaginador= new Ext.PagingToolbar({
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
cppConsecuenciasPaginador.bindStore(consecuenciasDataStore);
consecuenciasColumnModel = new Ext.grid.ColumnModel(
[{
        header: '#',
        readOnly: true,
        dataIndex: 'id_ec',
        width: 40,        
        sortable: true,
        renderer: function(value, cell){
            cell.css = "readonlycell";
            return value;
        },
        hidden: false
    },{
        header: 'Fecha alta',
        dataIndex: 'fecha_alta',
        width: 100,
        sortable: true,
        align:'center'
    },{
        header: 'Consecuencia',
        dataIndex: 'consecuencia',
        width: 250,
        sortable: true,
    },{
        header: 'Descripción',
        dataIndex: 'descripcion',
        width: 350,
        sortable: true,
        align:'center'
    },{
        header: 'Unidades Perdidas (TN)',
        dataIndex: 'unidades_perdidas',
        width: 160,
        sortable: true,
        renderer: Ext.util.Format.numberRenderer('0.000,00/i'),
        align:'left'
    },{
        header: 'Monto (U$S)',
        dataIndex: 'monto',
        width: 140,
        sortable: true,
        renderer: Ext.util.Format.numberRenderer('0.000,00/i'),
        align:'left'
    },botonesCppConsecuenciasAction
]);
    


consecuenciasGridPanel =  new Ext.grid.GridPanel({
    id: 'consecuenciasGridPanel',
    title: 'Listado de consecuencias del evento nro:',
    store: consecuenciasDataStore,
    cm: consecuenciasColumnModel,
    enableColLock:false,
    trackMouseOver:true, 
    loadMask: true, 
//    viewConfig: {
//        forceFit: false
//    },
    tbar: [],
    plugins:[cppConsecuenciasBuscador], 
//    height:500,
//    layout: 'fit',
//    selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
    bbar:[cppConsecuenciasPaginador]
 });   
 
consecuenciasGridPanel.setHeight(ALT_INF);

function showBtnMonto(value,metaData,record){
    var grid=Ext.getCmp('cppEventosGridPanel');
    var sm=grid.getSelectionModel();
    
    if(sm.selections.items.length == 1)
    {
        var row=sm.getSelected();   
        var btn_monto=row.json.btn_monto;

        if(btn_monto == 1)
            return 'x-grid-center-icon'; 
        else
            return 'x-hide-display';  
    } 
                       
};
function showBtnTn(value,metaData,record){
    var grid=Ext.getCmp('cppEventosGridPanel');
    var sm=grid.getSelectionModel();
    
    if(sm.selections.items.length == 1)
    {
        var row=sm.getSelected();   
        var btn_tn=row.json.btn_tn;

        if(btn_tn == 1)
            return 'x-grid-center-icon'; 
        else
            return 'x-hide-display';  
    } 
                       
};

consecuenciasGridPanel.on('celldblclick', abrir_popup_cppConsecuencias);
function abrir_popup_cppConsecuencias(grid ,  rowIndex, columnIndex,  e){
    var data=grid.store.data.items[rowIndex].data;
//    var grid= Ext.getCmp('cppEventosGridPanel');
    var cm= grid.getColumnModel();
    var WinCppConsecuencias;
    var enc=['<html>','<div class="tabla_popup_grilla">'];
    var pie=['<br class="popup_clear"/></div>','</html>'];
    var cppla=['<p><div class="titulo">Detetalle de consecuencia nro '+data.id_ec+'<br></div></p>'];
    
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

    WinCppConsecuencias = new Ext.Window({
            title: 'Detalle de consecuencia nro '+data.id_ec,
            closable: true,
            modal:true,
            //closeAction: 'hide',
            width: 550,
            boxMinWidth:550,
            height: 300,
            boxMinHeight:300,
            plain: true,
            autoScroll:true,
            layout: 'absolute',
            html: html.join(''),
//                                items: [],
            buttons: [{
                    text: 'Cerrar',
                    handler: function(){
                            WinCppConsecuencias.hide();
                            WinCppConsecuencias.destroy();

                    }
            }]
    });
    WinCppConsecuencias.show();

}
