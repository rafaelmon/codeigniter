//
//objetivosEvaluacionDS = new Ext.data.GroupingStore({
//    id: 'objetivos1raev-ds',
//    proxy: new Ext.data.HttpProxy({
//            url: CARPETA+'/listado_objetivos', 
//            method: 'POST'
//        }),
//    baseParams:{tampagina: TAM_PAGINA}, // this parameter is passed for any HTTP request
//    reader: new Ext.data.JsonReader({
//        root: 'rows',
//        totalProperty: 'total',
//        idProperty: 'id_objetivo'
//    },[ 
//    {name: 'id_objetivo',   type: 'int',            mapping: 'id_objetivo'},        
//    {name: 'oe',            type: 'string',         mapping: 'oe'},
//    {name: 'op',            type: 'string',         mapping: 'op'},
//    {name: 'indicador',     type: 'string',         mapping: 'indicador'},
//    {name: 'fd',            type: 'string',         mapping: 'fd'},
//    {name: 'valor_ref',     type: 'string',         mapping: 'valor_ref'},
//    {name: 'peso',          type: 'float',          mapping: 'peso'},
//    {name: 'real1',         type: 'float',         mapping: 'real1'},
//    {name: 'orden',         type: 'int',            mapping: 'orden'},
//    {name: 'id_estado',     type: 'int',            mapping: 'id_estado'},        
//    {name: 'estado',        type: 'string',         mapping: 'estado'},
//    ]),
//    sortInfo:{field: 'orden', direction: "ASC"},
//    groupField:'estado',
//    remoteSort: true
//});

// var summaryObjEvaluacion= new Ext.ux.grid.GroupSummary();


objetivosEvaluacionColumnModel = new Ext.grid.ColumnModel(
    [new Ext.grid.RowNumberer(),
        {
        header: 'Dimension',
        dataIndex: 'dimension',
        width: 90,
        sortable: false,
        hidden: true
        },
        {
        header: 'Estado',
        dataIndex: 'actor',
        width: 90,
        sortable: false,
        hidden: false
        },
        {
        header: 'Objetivo',
        dataIndex: 'obj',
        width: 200,
        sortable: false,
        hidden: false,
        renderer: showobjetivo
        },{
        header: 'Indicador',
        dataIndex: 'indicador',
        width: 90,
        sortable: false,
        renderer: showQtipIndicador
        },{
        header: 'Fuente de datos',
        dataIndex: 'fd',
        width: 80,
        sortable: false,
        renderer: showQtipFD
        },{
        header: 'Valor referencia',
        dataIndex: 'valor_ref',
        width: 120,
        sortable: false,
        renderer: showQtipVRef
        },{
        header: 'Peso',
//        tooltip:'Pesos',
        dataIndex: 'peso',
        width: 50,
        align:'center',
        summaryType: 'sum',
        sortable: false,
        renderer: showPeso
        },{
        header: 'F. Revisión',
        dataIndex: 'fecha_evaluacion',
        width: 50,
        align:'center',
//            summaryType: 'sum',
        sortable: false
        },{
        header: '% Alcanzado',
        tooltip:'Especifique el % alcanzao para el objetivo',
        dataIndex: 'real1',
        width: 80,
        align:'center',
        renderer: showPorcentaje,
//         editor: new Ext.form.NumberField({
//            allowBlank: true,
//            disabled: false
//          }),
        summaryType: 'sum',
        sortable: false
        },{
        header: 'Peso Real',
//        tooltip:'Pesos',
        dataIndex: 'pesoreal',
        width: 80,
        align:'center',
        renderer: showCeldaEvalReal1,
//         editor: new Ext.form.NumberField({
//            allowBlank: true,
//            disabled: false
//          }),
        summaryType: 'sum',
        sortable: false
        }
    ]
);
ddpTopAprEvaluacionObjetivosGridPanel =  new Ext.grid.EditorGridPanel({
        id: 'ddpTopAprEvaluacionObjetivosGridPanel',
//        title:'objetivos',
        store: ddpTopAprObjetivosDS,
//        store: objetivosEvaluacionDS,
        cm: objetivosEvaluacionColumnModel,
        view: new Ext.grid.GroupingView({
            forceFit:true,
            showGroupName:false,
             groupTextTpl: '<p style="color:#{[values.rs[0].json.css_bc]}">{text} <b style="color:#A4A4A4">({[values.rs.length]} {[values.rs.length > 1 ? "objetivos" : "objetivo"]})</b></p>'
//            groupTextTpl: '{text} <b style="color:#A4A4A4;">({[values.rs.length]} {[values.rs.length > 1 ? "objetivos" : "objetivo"]})</b>'
        }),
        enableColLock:false,
        trackMouseOver:true, //que se coloree cuando pasas el mouse por encima
        loadMask: true, //que te ponga el loading cuando se esta generando el store
//        autoExpandColumn:'coltopobjetivo',
        anchor: 100,
        viewConfig: {
            forceFit: true
        },
        plugins:[/*summaryObjEvaluacion*/],
        clicksToEdit:3,
        height:500,
        autoScroll :true,
//        layout: 'fit',
        selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
        bbar: [],
        tbar: []
});

var altura=Ext.getBody().getSize().height - 160;
ddpTopAprEvaluacionObjetivosGridPanel.setHeight(altura);
Ext.getCmp('browser').on('resize',function(comp){
//    var dimPanel=Ext.getCmp('miTopDimensionesPanel');
//    var widthDimPanel=dimPanel.getInnerWidth();
//    console.log(this.getSize().width-widthDimPanel);
    ddpTopAprEvaluacionObjetivosGridPanel.setWidth(this.getSize().width);
    ddpTopAprEvaluacionObjetivosGridPanel.doLayout();
    ddpTopAprEvaluacionObjetivosGridPanel.setHeight(altura);
});

  
function showobjetivo (value,metaData,superData){
    var dim=superData.json.id_objetivo;
    var css_bc=superData.json.css_bc;
    var deviceDetail = value;
//    metaData.attr = 'style="background-color:#'+css_bc+'; color:#FFF;"';        
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
    return value;
}
function showQtipIndicador(value, metaData,record){
    var deviceDetail = record.get('indicador');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
    return value;
}
function showQtipFD(value, metaData,record){
    var deviceDetail = record.get('fd');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
    return value;
}
function showQtipVRef(value, metaData,record){
    var deviceDetail = record.get('valor_ref');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
    return value;
}
       
function verPermiso(){
//    console.log("x"+permiso_modificar_obj);
    return true;
}

function showCeldaEvalReal1 (value,metaData,superData){
    if(superData.json)
    {
        var val=superData.json.real1;
        if (val==null)
            value="";
    }
    metaData.attr = 'style="background-color:#C5C5C5  ; color:#000;"';
    return value;
    
}
function showPeso (value,metaData,superData){
        metaData.attr = 'style="background-color:#C5C5C5  ; color:#000;"';
        return value;
}
function showPorcentaje (value,metaData,superData){
        metaData.attr = 'style="background-color:#F9F90F  ; color:#000;"';
        return value;
}
