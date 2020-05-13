//PANEL INFERIOR
gestionesDocsPublicadosDataStore = new Ext.data.Store({
    id: 'gestionesDocsPublicadosDataStore',
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
        {name: 'fecha',         type: 'string', mapping: 'fecha'}
    ]),
    sortInfo:{field: 'id_gestion', direction: "asc"},
    remoteSort : true
});


gestionesDocsPublicadosColumnModel = new Ext.grid.ColumnModel([
    {
        header: '#',
//        readOnly: true,
        dataIndex: 'id_gestion',
        width: 50,
        hidden: true
    },{
        header: 'Fecha',
        dataIndex: 'fecha',
        sortable: false,
        width:  100,
        fixed:true,
        readOnly: true,
        align:'center'
      },{
        header: 'Actor',
        dataIndex: 'persona',
        width:  220,
        hidden: false
    },/*{
        header: 'Rol',
        dataIndex: 'rol',
        width:  50,
        hidden: false
    },*/{
        header: 'Gestión',
        dataIndex: 'tg',
        width:  200,
        hidden: false
    }
    ]);
gestionesDocsPublicadosGrid =  new Ext.grid.GridPanel({
    id: 'gestionesDocsPublicadosEditorGrid',	  
    store: gestionesDocsPublicadosDataStore,
    cm: gestionesDocsPublicadosColumnModel,
    enableColLock:false,	 
    viewConfig: {
        forceFit: false
    },      
    autoScroll : true,	 
    bbar:[]
});    

publicadosInferiorPanel = new Ext.Panel(
{
        collapsible: true,
        collapsed:true,
        split: true,
        title: 'Historial del Documento',
        region: 'south',
        height: 300,
        minSize: 100,
        maxSize: 350,
        margins: '0 5 5 5',
        html:'<p>panel inferior</p>',
        layout: 'fit',
        items : [gestionesDocsPublicadosGrid]
});

//-->FIN PANEL INFERIOR