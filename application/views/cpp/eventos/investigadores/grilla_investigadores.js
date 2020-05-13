/*** grilla investigadores ***/
investigadoresDataStore = new Ext.data.Store({
    id: 'investigadoresDataStore',
    proxy: new Ext.data.HttpProxy({
              url: CARPETA+'/listado_invest', 
              method: 'POST'
          }),
    baseParams:{tampagina: TAM_PAGINA}, // this parameter is passed for any HTTP request
    reader: new Ext.data.JsonReader({
      root: 'rows',
      totalProperty: 'total',
      id: 'id'
    },[ 
      {name: 'id_investigador', type: 'int',     mapping: 'id_investigador'},        
      {name: 'usuario',         type: 'string',  mapping: 'usuario'},
      {name: 'fecha_alta',      type: 'string',  mapping: 'fecha_alta'},
      {name: 'area',            type: 'string',  mapping: 'area'},
      {name: 'puesto',          type: 'string',  mapping: 'puesto'},
    ]),
    sortInfo:{field: 'id', direction: "ASC"},
    remoteSort: true
});
cppInvestigadoresBuscador= new Ext.ux.grid.Search({
    iconCls:'icon-zoom',
//    readonlyIndexes:[''],
    disableIndexes:[],
    align:'right',
    minChars:5
});
cppInvestigadoresPaginador= new Ext.PagingToolbar({
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
investigadoresColumnModel = new Ext.grid.ColumnModel(
[{
        header: '#',
        readOnly: true,
        dataIndex: 'id_investigador',
        width: 40,        
        sortable: true,
        renderer: function(value, cell){
            cell.css = "readonlycell";
            return value;
        },
        hidden: false
    },{
        header: 'Fecha',
        dataIndex: 'fecha_alta',
        width: 100,
        sortable: true,
        align:'center'
    },{
        header: 'Investigador',
        dataIndex: 'usuario',
        width: 250,
        sortable: true,
    },{
        header: 'Area',
        dataIndex: 'area',
        width: 250,
        sortable: true,
        align:'left'
    },{
        header: 'Puesto',
        dataIndex: 'puesto',
        width: 250,
        sortable: true,
        align:'left'
    }
]);
    


investigadoresGridPanel =  new Ext.grid.GridPanel({
    id: 'investigadoresGridPanel',
    title: 'Listado de Investigadores para el evento Nro:',
    store: investigadoresDataStore,
    cm: investigadoresColumnModel,
    enableColLock:false,
    trackMouseOver:true, 
    loadMask: true, 
    viewConfig: {
        forceFit: false
    },
    tbar: [],
    plugins:[cppInvestigadoresBuscador], 
    height:500,
    layout: 'fit',
    selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
    bbar: [cppInvestigadoresPaginador]
 });   
 
investigadoresGridPanel.setHeight(ALT_INF);

investigadoresGridPanel.on('celldblclick', abrir_popup_cppInvestigadores);
function abrir_popup_cppInvestigadores(grid ,  rowIndex, columnIndex,  e){
    var data=grid.store.data.items[rowIndex].data;
//    var grid= Ext.getCmp('cppEventosGridPanel');
    var cm= grid.getColumnModel();
    var WinCppInvestigadores;
    var enc=['<html>','<div class="tabla_popup_grilla">'];
    var pie=['<br class="popup_clear"/></div>','</html>'];
    var cppla=['<p><div class="titulo">Detetalle de investigador nro '+data.id_investigador+'<br></div></p>'];
    
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

    WinCppInvestigadores = new Ext.Window({
            title: 'Detalle del investigador nro '+data.id_investigador,
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
                            WinCppInvestigadores.hide();
                            WinCppInvestigadores.destroy();

                    }
            }]
    });
    WinCppInvestigadores.show();

}