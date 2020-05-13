
ddpTopAuditoriaDataStore = new Ext.data.Store({
    id: 'ddpTopAuditoriaDataStore',
    name: 'ddpTopAuditoriaDataStore',
    proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/listado_auditoria', 
            method: 'POST'
        }),
    baseParams:{tampagina: 10}, // this parameter is passed for any HTTP request
    reader: new Ext.data.JsonReader({
    root: 'rows',
    totalProperty: 'total'
//    id: 'id'
    },[ 
    {name: 'id',                type: 'int',        mapping: 'id'},
    {name: 'usuario_alta',      type: 'string',     mapping: 'usuario_alta'},
    {name: 'observacion',       type: 'string',     mapping: 'observacion'},
    {name: 'accion',            type: 'string',     mapping: 'accion'},
    {name: 'fecha_alta',        type: 'string',     mapping: 'fecha_alta'},
    ]),
    sortInfo:{field: 'id', direction: "DESC"},
    remoteSort: true
});

ddpTopAuditoriaColumnModel = new Ext.grid.ColumnModel(
    [   
        {
            header: '#',
            dataIndex: 'id',
            width: 20,
            sortable: true,
            renderer: function(value, cell){
                cell.css = "readonlycell";
                return value;
            },
        }
        ,{
            header: 'Usuario actuó',
            dataIndex: 'usuario_alta',
            width: 70,
            sortable: true
        }
        ,{
            header: 'Fecha acción',
            dataIndex: 'fecha_alta',
            width: 100,
            align:'center',
            renderer: showNull,
            sortable: false
        }
        ,{
            header: 'Acción',
            dataIndex: 'accion',
            width: 90,
            sortable: true
        }
//        ,{
//            header: 'Usuario anterior',
//            dataIndex: 'usuario_anterior',
//            width: 210,
//            renderer: showNull,
//            sortable: false
//        }
        ,{
            header: 'Observacion',
            dataIndex: 'observacion',
            width: 300,
            renderer: showNull,
            sortable: false
        }
    ]
);
  
   ddpTopAuditoriaGrid =  new Ext.grid.GridPanel({
        id: 'ddpTopAuditoriaGrid',
//        title: 'Historial de objetivo:',
        store: ddpTopAuditoriaDataStore,
        cm: ddpTopAuditoriaColumnModel,
        enableColLock:false,
        trackMouseOver:true, //que se coloree cuando pasas el mouse por encima
        loadMask: true, //que te ponga el loading cuando se esta generando el store
//        renderTo: 'grillita',
//        autoExpandColumn:'',
        viewConfig: {
            forceFit: true
        },
        bodyBorder:false,
        plugins:[],
        height:220,
//        autoHeight :true,
        autoScroll :true,
//        layout: 'fit',
        bbar: [],
        tbar: []
    }); 

  
function showQtipObj(value, metaData,record){
    var deviceDetail = record.get('oe');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
    return value;
}
function showNull(value, metaData,record){
//    var value = record.json.value;
//    console.log(value);
    if (value == '')
    {
        metaData.attr = 'style="background-color:#ADD6C2;"';        
        metaData.attr += 'ext:qtip="'+ value + '"';
    }
    return value;
}