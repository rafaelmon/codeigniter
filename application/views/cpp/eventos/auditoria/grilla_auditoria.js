/*** grilla auditoria ***/
auditoriaDataStore = new Ext.data.Store({
    id: 'auditoriaDataStore',
    proxy: new Ext.data.HttpProxy({
              url: CARPETA+'/listado_audit', 
              method: 'POST'
          }),
    baseParams:{tampagina: TAM_PAGINA}, // this parameter is passed for any HTTP request
    reader: new Ext.data.JsonReader({
      root: 'rows',
      totalProperty: 'total',
      id: 'id'
    },[ 
      {name: 'id',      type: 'int',     mapping: 'id'},        
      {name: 'usuario', type: 'string',  mapping: 'usuario'},
      {name: 'fecha',   type: 'string',  mapping: 'fecha'},
      {name: 'accion',  type: 'string',  mapping: 'accion'},
      {name: 'obs',     type: 'string',  mapping: 'obs'},
    ]),
    sortInfo:{field: 'id', direction: "ASC"},
    remoteSort: true
});
cppAuditoriaBuscador= new Ext.ux.grid.Search({
    iconCls:'icon-zoom',
//    readonlyIndexes:[''],
    disableIndexes:[],
    align:'right',
    minChars:5
});
cppAuditoriaPaginador= new Ext.PagingToolbar({
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
auditoriaColumnModel = new Ext.grid.ColumnModel(
[{
        header: '#',
        readOnly: true,
        dataIndex: 'id',
        width: 40,        
        sortable: true,
        renderer: function(value, cell){
            cell.css = "readonlycell";
            return value;
        },
        hidden: false
    },{
        header: 'Fecha',
        dataIndex: 'fecha',
        width: 100,
        sortable: true,
        align:'center'
    },{
        header: 'Usuario',
        dataIndex: 'usuario',
        width: 250,
        sortable: true,
    },{
        header: 'Acción',
        dataIndex: 'accion',
        width: 250,
        sortable: true,
        align:'center'
    },{
        header: 'Observaciones',
        dataIndex: 'obs',
        width: 300,
        sortable: true,
        align:'center'
    }
]);
    


auditoriaGridPanel =  new Ext.grid.GridPanel({
    id: 'auditoriaGridPanel',
    title: 'Auditoría de acciones por evento',
    store: auditoriaDataStore,
    cm: auditoriaColumnModel,
    enableColLock:false,
    trackMouseOver:true, 
    loadMask: true, 
    viewConfig: {
        forceFit: false
    },
    tbar: [],
    plugins:[cppAuditoriaBuscador], 
    height:500,
    layout: 'fit',
    selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
    bbar: [cppAuditoriaPaginador]
 });   
 
auditoriaGridPanel.setHeight(ALT_INF);

auditoriaGridPanel.on('celldblclick', abrir_popup_cppAuditoria);
function abrir_popup_cppAuditoria(grid ,  rowIndex, columnIndex,  e){
    var data=grid.store.data.items[rowIndex].data;
//    var grid= Ext.getCmp('cppEventosGridPanel');
    var cm= grid.getColumnModel();
    var winCppAuditoria;
    var enc=['<html>','<div class="tabla_popup_grilla">'];
    var pie=['<br class="popup_clear"/></div>','</html>'];
    var cppla=['<p><div class="titulo">Detetalle de auditoria nro'+data.id+'<br></div></p>'];
    
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

    winCppAuditoria = new Ext.Window({
            title: 'Detalle de auditoria nro '+data.id,
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
                            winCppAuditoria.hide();
                            winCppAuditoria.destroy();

                    }
            }]
    });
                    winCppAuditoria.show();

}
