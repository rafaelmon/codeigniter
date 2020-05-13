
miTopDdpHistorialObjetivoDataStore = new Ext.data.Store({
    id: 'miTopDdpHistorialObjetivoDataStore',
    name: 'miTopDdpHistorialObjetivoDataStore',
    proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/listado_historial', 
            method: 'POST'
        }),
    baseParams:{tampagina: 10}, // this parameter is passed for any HTTP request
    reader: new Ext.data.JsonReader({
    root: 'rows',
    totalProperty: 'total'
//    id: 'id'
    },[ 
    {name: 'id',            type: 'string',     mapping: 'id'},
    {name: 'usuario',       type: 'string',     mapping: 'usuario'},
    {name: 'estado_top',    type: 'string',     mapping: 'estado_top'},
    {name: 'fecha',         type: 'string',     mapping: 'fecha'},
    {name: 'obj',            type: 'string',     mapping: 'obj'},
    {name: 'indicador',     type: 'string',     mapping: 'indicador'},
    {name: 'fd',            type: 'string',     mapping: 'fd'},
    {name: 'valor_ref',     type: 'string',     mapping: 'valor_ref'},
    {name: 'peso',          type: 'float',      mapping: 'peso'},
    {name: 'real1',          type: 'string',      mapping: 'real1'},
    {name: 'real2',          type: 'string',      mapping: 'real2'},
    {name: 'primero',       type: 'float',      mapping: 'primero'},
    {name: 'fecha_evaluacion',  type: 'string',         mapping: 'fecha_evaluacion'},
    ]),
    sortInfo:{field: 'id', direction: "ASC"},
    remoteSort: true
});
paginadorHistorial= new Ext.PagingToolbar({
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
paginadorHistorial.bindStore(miTopDdpHistorialObjetivoDataStore);

miTopDdpHistorialObjetivoColumnModel = new Ext.grid.ColumnModel(
    [   {
        header: 'Fecha',
        dataIndex: 'fecha',
        width: 60,
        sortable: true,
        renderer: showQtipObj
        },{
        header: 'Usuario',
        dataIndex: 'usuario',
        width: 90,
        sortable: true
        },{
        header: 'Estado TOP',
        dataIndex: 'estado_top',
        width: 50,
        hidden: true,
        sortable: false
        },{
        header: 'Objetivo',
        dataIndex: 'obj',
        width: 210,
        renderer: showNotNull,
        sortable: false
        },{
        header: 'Indicador',
        dataIndex: 'indicador',
        width: 90,
        renderer: showNotNull,
        sortable: false
        },{
        header: 'Fuente de datos',
        dataIndex: 'fd',
        width: 80,
        renderer: showNotNull,
        sortable: false
        },{
        header: 'Valor de referencia',
        dataIndex: 'valor_ref',
        width: 100,
        renderer: showNotNull,
        sortable: false
        },{
        header: 'Peso',
        dataIndex: 'peso',
        width: 50,
        align:'center',
        hidden: true,
        renderer: showNotNull,
        sortable: false
        },{
            header: '% Alcanzado',
            dataIndex: 'real1',
            width: 50,
            align:'center',
            renderer: showNotNull,
            sortable: false
        }
        ,{
            header: 'Fecha Revisión',
            dataIndex: 'fecha_evaluacion',
            width: 50,
            align:'center',
            renderer: showNotNull,
//            summaryType: 'sum',
            sortable: false
        }
    ]
);
  
   miTopDdpHistorialObjetivoGrid =  new Ext.grid.GridPanel({
        id: 'miTopDdpHistorialObjetivoGrid',
//        title: 'Historial de objetivo:',
        store: miTopDdpHistorialObjetivoDataStore,
        cm: miTopDdpHistorialObjetivoColumnModel,
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
        bbar: [paginadorHistorial],
        tbar: []
    }); 

  
//  	var altura=Ext.getBody().getSize().height - 60;
//	ddpHistorialObjetivoGrid.setHeight(altura);
	
//	Ext.getCmp('browser').on('resize',function(comp){
//		ddpHistorialObjetivoGrid.setWidth(this.getSize().width);
//		ddpHistorialObjetivoGrid.setHeight(Ext.getBody().getSize().height - 60);
//
//	});
        
function showQtipObj(value, metaData,record){
    var deviceDetail = record.get('oe');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
    return value;
}
function showNotNull(value, metaData,record){
    var reg = record.json.primero;
//    console.log(reg);
//    console.log(value);
    if (reg==1)
    {
        metaData.attr = 'style="background-color:#FFF; color:#000;"';        
        metaData.attr += 'ext:qtip="'+ value + '"';
        
    }
    else
    {
        if (value =="" || value==null || value == '00/00/0000')
        {
            metaData.attr = 'style="background-color:#ADD6C2; color:#ADD6C2;"';        
        }
        else
        {
            metaData.attr = 'style="background-color:#FFF; color:#E60000;"';        
            metaData.attr += 'ext:qtip="'+ value + '"';
        }
    }   
    return value;
}