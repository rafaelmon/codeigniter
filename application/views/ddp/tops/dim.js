dimensionesDS = new Ext.data.GroupingStore({
    id: 'dimensiones-ds2',
    proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/listado_dimensiones', 
            method: 'POST'
        }),
    baseParams:{tampagina: TAM_PAGINA}, // this parameter is passed for any HTTP request
    reader: new Ext.data.JsonReader({
    root: 'rows',
    totalProperty: 'total',
    id: 'id'
    },[ 
    {name: 'id_dimension',   type: 'int',        mapping: 'id_dimension'},        
    {name: 'abv',            type: 'string',     mapping: 'abv'},
    {name: 'dimension',      type: 'string',     mapping: 'dimension'},
    {name: 's_pesos',        type: 'float',        mapping: 's_pesos'},
    {name: 'q_obj',           type: 'int',        mapping: 'q_obj'},
    {name: 'orden',          type: 'int',        mapping: 'orden'},
    {name: 'vacio',          type: 'string',        mapping: 'vacio'},
    ]),
    sortInfo:{field: 'orden', direction: "ASC"},
    groupField:'vacio',
    remoteSort: true
});
 var summaryDim = new Ext.ux.grid.GroupSummary();
dimensionesDS.on ('load',function(){
            var q_obj = dimensionesDS.sum('q_obj');
            console.log(q_obj);
            var btnExcel=Ext.getCmp('botonDescExcel');
            if(q_obj>0)
            {
                btnExcel.enable();
            }
            else
                btnExcel.disable();
                
            
});
dimensionesColumnModel = new Ext.grid.ColumnModel(
    [{
//        header: 'vacio',
        dataIndex: 'vacio',
        width: 5,
        sortable: true,
        hidden: true
        },{
        header: 'Dimensi&oacute;n',
        dataIndex: 'abv',
        width: 90,
        sortable: true,
        renderer: showCeldaDimension
        },{
        header: '&Sigma; <b>Obj</b>',
        tooltip:'Cantidad de Objetivos',
        dataIndex: 'q_obj',
        width: 70,
        align:'center',
        summaryType: 'sum',
        sortable: false
        },{
        header: '&Sigma; <b>Peso</b>',
        tooltip:'Sumatoria de Pesos',
        dataIndex: 's_pesos',
        width: 70,
        align:'center',
        summaryType: 'sum',
        sortable: false
        }
    ]
);
dimensionesGridPanel =  new Ext.grid.GridPanel({
        id: 'dimensiones-grid-panel2',
//        title:'Dimensiones',
        store: dimensionesDS,
        cm: dimensionesColumnModel,
        enableColLock:false,
        trackMouseOver:true, //que se coloree cuando pasas el mouse por encima
        loadMask: true, //que te ponga el loading cuando se esta generando el store
        view: new Ext.grid.GroupingView({
            forceFit:true,
            showGroupName:false,
            enableGroupingMenu :false,
            headersDisabled :true,
            hideGroupedColumn :true,
            startCollapsed :false,
            enableGrouping :true,
            groupTextTpl: ' '
        }),
        viewConfig: {
            forceFit: true
        },
        plugins:[summaryDim],
        height:500,
//        layout: 'fit',
        selModel: new Ext.grid.RowSelectionModel({singleSelect:true}),
        bbar: [],
        tbar: []
});
//dimensionesDS.load({params: {start: 0, limit: TAM_PAGINA}});
dimensionesGridPanel.on('cellclick',clickGridDimension);


dimensionesPanel = new Ext.Panel({
    id: 'dimensionesPanel2',
//    title: 'Dimensiones',
//    region: 'west',
    width: 250,
    flex: 2,
    border: false,
//    minSize: 250,
//    maxSize: 400,
//    layout:'fit',		
//    split: true,
//    collapsible: true,
//    margins: '0 0 0 5',
//    bodyStyle: 'padding:15px',
    height: 220,
    items: [dimensionesGridPanel]

});
function showCeldaDimension (value,metaData,superData){
    var dim=superData.json.id_dimension;
    var css_bc=superData.json.css_bc;
    var deviceDetail = superData.json.dimension;
    metaData.attr = 'style="background-color:#'+css_bc+'; color:#FFF;"';        
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
    SUM_DIM=SUM_DIM+superData.data.s_pesos;
//    console.log(SUM_DIM);
    return value;
}

function clickGridDimension(grid, rowIndex, columnIndex){
    //    var id_estado=grid.store.data.items[rowIndex].data.id_estado;
        var id=grid.store.data.items[rowIndex].data.id_dimension;
        var q_obj=grid.store.data.items[rowIndex].data.q_obj;
        var o_panel=Ext.getCmp('objetivosGridPanel2');
        var o_cm=o_panel.getColumnModel();
        if (q_obj>0)
        {
//            if (id==6)
//                {
//                    BTNs_acción=false;
//                     var editor=Ext.getCmp('objetivosGridPanel2');
//                     console.log(editor);
//    //                 editor.setDisabled(true);
//    //                disable()
//
//                }
                 if (id==6)
                {
                    for (var i=2;i<=7;i++)
                        o_cm.setEditable(i,false);
                }
                else
                {
                    for (var i=2;i<=7;i++)
                        o_cm.setEditable(i,true);
                }
            ID_DIM=id;
            DIM=grid.store.data.items[rowIndex].data.dimension;
            objetivosDS.load({params: {id_dimension:id,start: 0}});
        }

};