function msgProcess(titulo){
 Ext.MessageBox.show({
        title: titulo,
        msg: 'Procesando, por favor espere...',
        progress:true,
        progressText: 'Procesando...', 
        width:400, 
        wait:true, 
        waitConfig: {interval:200}
    });
}
objetivosDS = new Ext.data.GroupingStore({
    id: 'objetivos-ds2',
    proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/listado_objetivos', 
            method: 'POST'
        }),
    baseParams:{tampagina: TAM_PAGINA, top:TOP.id_top}, // this parameter is passed for any HTTP request
    reader: new Ext.data.JsonReader({
        root: 'rows',
        totalProperty: 'total',
        idProperty: 'id_objetivo'
    },[ 
    {name: 'id_objetivo',   type: 'int',            mapping: 'id_objetivo'},        
    {name: 'dimension',     type: 'string',         mapping: 'dimension'},        
    {name: 'obj',            type: 'string',         mapping: 'obj'},
    {name: 'indicador',     type: 'string',         mapping: 'indicador'},
    {name: 'fd',            type: 'string',         mapping: 'fd'},
    {name: 'valor_ref',     type: 'string',         mapping: 'valor_ref'},
    {name: 'peso',          type: 'float',          mapping: 'peso'},
    {name: 'orden',         type: 'int',            mapping: 'orden'},
    {name: 'id_estado',     type: 'int',            mapping: 'id_estado'},
    {name: 'actor',        type: 'string',         mapping: 'actor'},
    {name: 'estado',        type: 'string',         mapping: 'estado'},
    {name: 'real1',        type: 'float',         mapping: 'real1'},
    {name: 'pesoreal',        type: 'float',         mapping: 'pesoreal'}
    ]),
    sortInfo:{field: 'orden', direction: "ASC"},
    groupField:'dimension',
    remoteSort: true
});
objetivosDS.load({params: {start: 0, limit: TAM_PAGINA}});
// var summaryObj = new Ext.ux.grid.GroupSummary();

var paginadorObjSup= new Ext.PagingToolbar({
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
paginadorObjSup.bindStore(objetivosDS);

botonesObjAction = new Ext.grid.ActionColumn({
    width: 15,
    editable:false,
    menuDisabled:true,
    header:'Acci&oacute;n',
    hideable:false,
    align:'center',
    width:  100,
    tooltip:'Editar',
        hidden:false,
    items:[{
            icon:URL_BASE+'images/edit-find.png',
            iconCls :'col_accion',
            tooltip:'Ver historial',
            handler: clickBtnVerHistorial
        }]
});

objetivosColumnModel = new Ext.grid.ColumnModel(
    [   
            {
            header: '#',
            readOnly: true,
            dataIndex: 'id_objetivo',
            width: 40,        
            sortable: true,
            renderer: function(value, cell){
                cell.css = "readonlycell";
                return value;
                },
            hidden: false
        }
        ,{
        header: 'dimension',
        dataIndex: 'dimension',
        width: 90,
        sortable: false,
        menuDisabled: true,
        hidden: true
        },{
        header: 'estado',
        dataIndex: 'estado',
        width: 90,
        sortable: false,
        menuDisabled: true,
        hidden: true
        },{
        header: 'Objetivos',
        dataIndex: 'obj',
        width: 200,
        sortable: false,
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
        sortable: false
        }
         ,{
            header: 'F. Revisión',
            dataIndex: 'fecha_evaluacion',
            width: 50,
            align:'center',
            sortable: false
        }
        ,botonesObjAction
    ]
);
objetivosGridPanel =  new Ext.grid.EditorGridPanel({
        id: 'objetivosGridPanel2',
//        title:'objetivos',
        store: objetivosDS,
        cm: objetivosColumnModel,
        view: new Ext.grid.GroupingView({
            forceFit:true,
            frame:false,
            showGroupName:false,
            enableGroupingMenu :false,
            groupTextTpl: '<p style="color:#{[values.rs[0].json.css_bc]}">{text} <b style="color:#A4A4A4">({[values.rs.length]} {[values.rs.length > 1 ? "objetivos" : "objetivo"]})</b></p>'
        }),
        enableColLock:false,
        stripeRows:true,
        trackMouseOver:true, //que se coloree cuando pasas el mouse por encima
        loadMask: true, //que te ponga el loading cuando se esta generando el store
//        autoExpandColumn:'coltopobjetivo',
        anchor: 100,
        viewConfig: {
            forceFit: true
        },
        plugins:[/*summaryObj*/],
//        clicksToEdit:3,
        height:500,
        autoScroll :true,
//        layout: 'fit',
//        selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
        tbar: [],
        bbar: [paginadorObjSup]
});
objetivosSupPanel = new Ext.Panel(
{
        id:'objetivosSupPanel',
        collapsible: false,
        split: false,
        header: true,
        region:'center',
        minSize: 100,
        margins: '0 5 5 5',
        html:'<p>panel superior</p>',
        layout: 'fit',
        items:[objetivosGridPanel]
});
var altura=Ext.getBody().getSize().height - 180;
objetivosSupPanel.setHeight(altura);
Ext.getCmp('browser').on('resize',function(comp){
//    var dimPanel=Ext.getCmp('dimensiones-grid-panel2');
//    var widthDimPanel=dimPanel.getInnerWidth();
//    console.log(this.getSize().width-widthDimPanel);
    objetivosSupPanel.setWidth(this.getSize().width );
    objetivosSupPanel.doLayout();
    objetivosSupPanel.setHeight(altura);
});


function showobjetivo (value,metaData,superData){
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

