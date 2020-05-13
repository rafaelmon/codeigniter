//PANEL INFERIOR
gestionesDataStore = new Ext.data.Store({
    id: 'gestionesDataStore',
    proxy: new Ext.data.HttpProxy({
        url: CARPETAGESTIONES+'/listado', 
        method: 'POST'
    }),
    baseParams:{limit: TAM_PAGINA}, 
    reader: new Ext.data.JsonReader({
        root: 'rows',
        totalProperty: 'total',
        id: 'id_gestion'
    },[ 
        {name: 'id_gestion',    type: 'int',    mapping: 'id_gestion'},
        {name: 'id_usuario',    type: 'string', mapping: 'id_usuario'},
        {name: 'persona',       type: 'string', mapping: 'persona'}	,
        {name: 'rol',           type: 'string', mapping: 'rol'}	,
        {name: 'tg',            type: 'string', mapping: 'tg'},
        {name: 'detalle',       type: 'string', mapping: 'detalle'},
        {name: 'fecha',         type: 'string', mapping: 'fecha'}
    ]),
    sortInfo:{field: 'id_gestion', direction: "asc"},
    remoteSort : true
});

obsDataStore = new Ext.data.Store({
    id: 'obsDataStore',
    proxy: new Ext.data.HttpProxy({
        url: CARPETA+'/listado_obs', 
        method: 'POST'
    }),
    baseParams:{limit: TAM_PAGINA}, 
    reader: new Ext.data.JsonReader({
        root: 'rows',
        totalProperty: 'total',
        id: 'id_obs'
    },[ 
        {name: 'id_obs',        type: 'int',    mapping: 'id_obs'},
        {name: 'persona',       type: 'string', mapping: 'persona'},
        {name: 'fecha',         type: 'string', mapping: 'fecha'},
        {name: 'obs',           type: 'string', mapping: 'obs'}
    ]),
    sortInfo:{field: 'id_obs', direction: "asc"},
    remoteSort : true
});


gestionesColumnModel = new Ext.grid.ColumnModel([
    {
        header: '#',
//        readOnly: true,
        dataIndex: 'id_gestion',
        width: 40,
        hidden: false,
        renderer: function(value, cell){
            cell.css = "readonlycell";
            return value;
        }
    },{
        header: 'Fecha',
        dataIndex: 'fecha',
        sortable: false,
        width:  70,
        fixed:true,
        readOnly: true,
        align:'center'
      },{
        header: 'Actor',
        dataIndex: 'persona',
        width:  110,
        renderer:showQtip,
        hidden: false
    },{
        header: 'Rol',
        dataIndex: 'rol',
        width:  60,
        hidden: false
    },{
        header: 'Tipo de Gestión',
        dataIndex: 'tg',
        width:  100,
        hidden: false
    },{
        header: 'Detalle de documento y revisión',
        dataIndex: 'detalle',
        width:  400,
        renderer:showQtipDetalle,
        hidden: false
    }
    ]);
obsColumnModel = new Ext.grid.ColumnModel([
    {
        header: '#',
//        readOnly: true,
        dataIndex: 'id_obs',
        width: 40,
        hidden: false,
        renderer: function(value, cell){
            cell.css = "readonlycell";
            return value;
        }
    },{
        header: 'Fecha',
        dataIndex: 'fecha',
        sortable: false,
        width:  70,
        fixed:true,
        readOnly: true,
        align:'center'
      },{
        header: 'Usuario',
        dataIndex: 'persona',
        width:  110,
        renderer:showQtip,
        hidden: false
    },{
        header: 'Observacion',
        dataIndex: 'obs',
        width:  400,
        renderer:showQtipObs,
        hidden: false
    }
    ]);
gestionesGrid =  new Ext.grid.GridPanel({
    id: 'gestionesGrid',	
    store: gestionesDataStore,
    cm: gestionesColumnModel,
    enableColLock:false,
    viewConfig: {
        forceFit: true
    },      
    autoScroll : true,	 
    bbar:[]
});    
obsGrid =  new Ext.grid.GridPanel({
    id: 'obsGrid',
    store: obsDataStore,
    cm: obsColumnModel,
    enableColLock:false,
    viewConfig: {
        forceFit: false
    },      
    autoScroll : true,	 
    bbar:[]
});    
colGestiones = new Ext.Panel(
{
        title: 'Historial',
//        region: 'center',
        columnWidth:.5,
        autoScroll : true,	
        height: 300,
        layout: 'fit',
//        html:'<p>panel inferior</p>',
        items : [gestionesGrid]
});
colObservaciones = new Ext.Panel(
{
        title: 'Observaciones',
//        region: 'center',
        columnWidth:.5,
        autoScroll : true,
        height: 300,
        layout: 'fit',
//        html:'<p>panel inferior</p>',
        items : [obsGrid]
});
colsPanel = new Ext.Panel(
{
//        collapsible: true,
//        collapsed:true,
        split: true,
//        title: 'Historial del Documento',
//        region: 'center',
        height: 300,
//        minSize: 100,
//        maxSize: 350,
//        margins: '0 5 5 5',
//        html:'<p>panel inferior</p>',
        layout: 'column',
        items : [colGestiones,colObservaciones]
});
inferiorPanel = new Ext.Panel(
{
        collapsible: true,
        collapsed:true,
        split: true,
//        title: 'Historial del Documento',
        region: 'south',
        height: 300,
        minSize: 100,
        maxSize: 350,
        margins: '0 5 5 5',
        html:'<p>panel inferior</p>',
        layout: 'fit',
        items : [colsPanel]
});
function showQtipObs(value, metaData,record){
    var deviceDetail = record.get('obs');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
    return value;
}
function showQtipDetalle(value, metaData,record){
    var deviceDetail = record.get('detalle');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
    return value;
}
function showQtip(value, metaData,record){
    metaData.attr += 'ext:qtip="'+String(value)+'"';
    return value;
}
gestionesGrid.on('celldblclick', abrir_popup_detalle);
obsGrid.on('celldblclick', abrir_popup_detalle);
function abrir_popup_detalle(grid ,  rowIndex, columnIndex,  e){
    var data=grid.store.data.items[rowIndex].data;
    
    switch (grid.id)
    {
        case 'obsGrid':
        var txt_popup=data.obs;
            break;
        case 'gestionesGrid':
        var txt_popup=data.detalle;
            break;
    }
    
    if(txt_popup!="")
    {
        var winTareasTareas;
        var html=['<html>',
                    '<div>',
                        '<div><span>'+txt_popup+'</span>',
                        '</div>',
                        '<br class="popup_clear"/>',
                    '</div>',
                    '</html>'
                    ];


                winTareasTareas = new Ext.Window({
                        title: 'Texto completo...',
                        closable: true,
                        modal:true,
                        //closeAction: 'hide',
                        width: 450,
                        boxMinWidth:300,
                        height: 200,
                        boxMinHeight:150,
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
    

}



//-->FIN PANEL INFERIOR