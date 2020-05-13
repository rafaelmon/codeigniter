edUsuarioDataStore = new Ext.data.GroupingStore({
    id: 'edUsuarioDataStore',
    proxy: new Ext.data.HttpProxy({
        url: CARPETA+'/listado_competencias_cual', 
        method: 'POST'
    }),
    baseParams:{limit: TAM_PAGINA, id:ID_ED}, 
    reader: new Ext.data.JsonReader({
        root: 'rows',
        totalProperty: 'total',
        id: 'id_ec'
    },[         
        {name: 'id_ec',             type: 'int',            mapping: 'id_ec'},
        {name: 'competencia',       type: 'string',         mapping: 'competencia'},
        {name: 'subcompetencia',    type: 'string',         mapping: 'subcompetencia'},
        {name: 'u_r1',              type: 'boolean',        mapping: 'u_r1'},
        {name: 'u_r2',              type: 'boolean',        mapping: 'u_r2'},
        {name: 'u_r3',              type: 'boolean',        mapping: 'u_r3'},
        {name: 'u_r4',              type: 'boolean',        mapping: 'u_r4'},
        {name: 's_r1',              type: 'boolean',        mapping: 's_r1'},
        {name: 's_r2',              type: 'boolean',        mapping: 's_r2'},
        {name: 's_r3',              type: 'boolean',        mapping: 's_r3'},
        {name: 's_r4',              type: 'boolean',        mapping: 's_r4'},
        {name: 't_cump',            type: 'int',            mapping: 't_cump'},
    ]),
    sortInfo:{field: 'competencia', direction: "asc"},
    groupField:'competencia',
    remoteSort : true
});

r1Check = new Ext.grid.RadioColumn({
    id:'r1Check',
    header: "<p class=enc_r1>Inferior a lo<br>esperado</p>",
    dataIndex: 's_r1',
    radioGroupClass:'s_r',
    q_radios:4,
    campo_id:'id_ec',
    tooltip:'Inferior a lo esperado...25%'
});
r2Check = new Ext.grid.RadioColumn({
    id:'r2Check',
    header: "<p class=enc_r1>Necesita<br>Mejorar</p>",
    dataIndex: 's_r2',
    radioGroupClass:'s_r',
    q_radios:4,
    campo_id:'id_ec',
    tooltip:'Necesita Mejorar...75%'
});
r3Check = new Ext.grid.RadioColumn({
    id:'r3Check',
    header: "<p class=enc_r1>Bueno</p>",
    dataIndex: 's_r3',
    radioGroupClass:'s_r',
    q_radios:4,
    campo_id:'id_ec',
    tooltip:'Bueno... 100%'
});
r4Check = new Ext.grid.RadioColumn({
    id:'r4Check',
    header: "<p class=enc_r1>Destacado</p>",
    dataIndex: 's_r4',
    radioGroupClass:'s_r',
    q_radios:4,
    campo_id:'id_ec',
    tooltip:'Destacado...120%'
});
edUsuarioColumnModel = new Ext.grid.ColumnModel([
    {
        header: '#',
        readOnly: true,
        dataIndex: 'id_ec',
        width: 40,
        hideable:false,
        hidden: false,
        renderer: function(value, cell){
            cell.css = "readonlycell";
            return value;
        }
    },{
        header: 'Competencia',
        hideable:false,
        dataIndex: 'competencia',
        sortable: true,
        hidden: true,
        width:  200,
        fixed:true,
        readOnly: true
      },{
        header: 'Competencia- Subcompetencia',
        dataIndex: 'subcompetencia',
        sortable: true,
        width:  500,
        fixed:false,
        renderer:showTooltip,
        readOnly: true
      },{
        header: 'Inferior a lo esperado',
        dataIndex: 'u_r1',
        sortable: true,
        width:  50,
        align:'center',
        fixed:false,
        renderer:showMarca,
        readOnly: true
      },{
        header: 'Necesita Mejorar',
        dataIndex: 'u_r2',
        sortable: true,
        width:  50,
        align:'center',
        fixed:false,
        renderer:showMarca,
        readOnly: true
      },{
        header: 'Bueno',
        dataIndex: 'u_r3',
        sortable: true,
        width:  50,
        align:'center',
        fixed:false,
        renderer:showMarca,
        readOnly: true
      },{
        header: 'Destacado',
        dataIndex: 'u_r4',
        sortable: true,
        width:  50,
        align:'center',
        fixed:false,
        renderer:showMarca,
        readOnly: true
    },r1Check,r2Check,r3Check,r4Check
]);
edUsuarioEditorGrid =  new Ext.grid.GridPanel({
    id: 'edUsuarioEditorGrid',
    store: edUsuarioDataStore,
    cm: edUsuarioColumnModel,
    view: new Ext.grid.GroupingView({
            forceFit:false,
            headersDisabled :true,
            groupTextTpl: '{group} ({[values.rs.length]} {[values.rs.length > 1 ? "subcompetencias" : "subcompetencia"]})'
        }),
    enableColLock:false,
//    renderTo: 'grillita',
    renderTo: 'edTab0',
    layout:'form',
    stripeRows:true,
//    viewConfig: {
//        forceFit: true
//    },      
    autoScroll : true,	 
    selModel: new Ext.grid.RowSelectionModel({singleSelect:true}),
    height:500,
    plugins:[r1Check,r2Check,r3Check,r4Check],
    bbar:[],
    tbar: []
});

//
//edPanel=new Ext.grid.Panel({
//    id: 'edPanel',
//    title:'Evaluación de Desempeño',
//    renderTo: 'grillita',
//    item:[edUsuarioEditorGrid]
//    
//});


edUsuarioDataStore.load({params: {start: 0}});
var altura=Ext.getBody().getSize().height - 130;
edUsuarioEditorGrid.setHeight(altura);

Ext.getCmp('edTab0').on('resize',function(comp){
        edUsuarioEditorGrid.setWidth(this.getSize().width);
        edUsuarioEditorGrid.setHeight(Ext.getBody().getSize().height - 130);

});
function showMarca(value,metaData,record)
{
    if(value)
    {
        value="X";
        metaData.attr = 'style="background-color:#A1D6A2; color:#FFF;"';
    }            
    else
    {
        value="-";
        metaData.attr = 'style="background-color:#DCDEDC; color:#FFF;"';
    }
    return value;
};
function showTooltip (value,metaData,superData){
    var deviceDetail = value;
//    metaData.attr = 'style="background-color:#'+css_bc+'; color:#FFF;"';        
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
    return value;
}

//-->FIN PANEL SUPERIOR