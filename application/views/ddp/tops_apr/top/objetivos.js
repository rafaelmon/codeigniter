ddpTopAprObjetivosDS = new Ext.data.GroupingStore({
    id: 'ddpTopAprObjetivosDS',
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
        {name: 'id_objetivo',       type: 'int',            mapping: 'id_objetivo'},        
        {name: 'dimension',         type: 'string',         mapping: 'dimension'},        
    //    {name: 'oe',            type: 'string',         mapping: 'oe'},
        {name: 'obj',               type: 'string',         mapping: 'obj'},
        {name: 'indicador',         type: 'string',         mapping: 'indicador'},
        {name: 'fd',                type: 'string',         mapping: 'fd'},
        {name: 'valor_ref',         type: 'string',         mapping: 'valor_ref'},
        {name: 'peso',              type: 'float',          mapping: 'peso'},
        {name: 'orden',             type: 'int',            mapping: 'orden'},
        {name: 'id_estado',         type: 'int',            mapping: 'id_estado'},        
        {name: 'estado',            type: 'string',         mapping: 'estado'},
        {name: 'actor',             type: 'string',         mapping: 'actor'},
        {name: 'real1',             type: 'float',          mapping: 'real1'},
        {name: 'real2',             type: 'float',          mapping: 'real2'},
        {name: 'pesoreal',          type: 'float',          mapping: 'pesoreal'},
        {name: 'fecha_evaluacion',  type: 'string',         mapping: 'fecha_evaluacion'}
    ]),
    sortInfo:{field: 'orden', direction: "ASC"},
    groupField:'dimension',
    remoteSort: true
});
ddpTopAprObjetivosDS.load({params: {start: 0, limit: TAM_PAGINA}});
// var summaryObj = new Ext.ux.grid.GroupSummary();


ddpTopAprObjetivosColumnModel = new Ext.grid.ColumnModel(
    [new Ext.grid.RowNumberer(),
        {
        header: 'dimension',
        dataIndex: 'dimension',
        width: 90,
        sortable: false,
        menuDisabled: true,
        hidden: true
        },
        {
        header: 'Estado',
        dataIndex: 'actor',
        width: 90,
        sortable: false,
        menuDisabled: true,
        hidden: false
        }
        ,{
        header: 'Objetivo',
        dataIndex: 'obj',
        width: 200,
        sortable: false,
        hidden: false,
        renderer: showobjetivo
        }
        ,{
        header: 'Indicador',
        dataIndex: 'indicador',
        width: 90,
        sortable: false,
//        editor: new Ext.form.TextField({
//            allowBlank: true,
//            disabled: !permiso_modificar_obj
//          }),
        renderer: showQtipIndicador
        },{
        header: 'Fuente de datos',
        dataIndex: 'fd',
        width: 80,
        sortable: false,
//        editor: new Ext.form.TextField({
//            allowBlank: true,
//            disabled: !permiso_modificar_obj
//          }),
        renderer: showQtipFD
        },{
        header: 'Valor referencia',
        dataIndex: 'valor_ref',
//        editor: new Ext.form.TextField({
//            allowBlank: true,
//            disabled: !permiso_modificar_obj
//          }),
        width: 120,
//        summaryRenderer: function(v, params, data){
//                    return  '<div style="text-align:right;">Subtotal:</div>';
//                },
        sortable: false,
        renderer: showQtipVRef
        }
        ,{
            header: 'Peso',
    //        tooltip:'Pesos',
            dataIndex: 'peso',
            width: 50,
            align:'center',
    //         editor: new Ext.ux.form.SpinnerField({
    //            allowBlank: true,
    //            disabled: !permiso_modificar
    //          }),
//            summaryType: 'sum',
            sortable: false
        }
       ,{
            header: 'F. Revisión',
            dataIndex: 'fecha_evaluacion',
            width: 50,
            align:'center',
            sortable: false
        }
    ]
);

ddpTopAprObjetivosGridPanel =  new Ext.grid.EditorGridPanel({
        id: 'ddpTopAprObjetivosGridPanel',
//        title:'ddpTopAprObjetivos',
        store: ddpTopAprObjetivosDS,
        cm: ddpTopAprObjetivosColumnModel,
        view: new Ext.grid.GroupingView({
            forceFit:true,
            frame:false,
            showGroupName:false,
            enableGroupingMenu :false,
//            groupTextTpl: '<p style="color:#{[values.rs[0].json.css_bc]}">{text} <b style="color:#A4A4A4">({[values.rs.length]} {[res]})</b></p>'
            groupTextTpl: '<p style="color:#{[values.rs[0].json.css_bc]}">{text} <b style="color:#A4A4A4">({[values.rs.length]} de {[values.rs[0].json.q_obj]})</b></p>'
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
        bbar: []
});

var altura=Ext.getBody().getSize().height - 180;
ddpTopAprObjetivosGridPanel.setHeight(altura);
Ext.getCmp('browser').on('resize',function(comp){
    ddpTopAprObjetivosGridPanel.setWidth(this.getSize().width);
    ddpTopAprObjetivosGridPanel.doLayout();
    ddpTopAprObjetivosGridPanel.setHeight(altura);
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
       

 