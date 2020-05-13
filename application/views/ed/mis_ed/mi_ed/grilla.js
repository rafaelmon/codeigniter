miEDDataStore = new Ext.data.GroupingStore({
    id: 'miEDDataStore',
    proxy: new Ext.data.HttpProxy({
        url: CARPETA_MI_ED+'/listado', 
        method: 'POST'
    }),
    baseParams:{limit: TAM_PAGINA, id:ID_ED}, 
    reader: new Ext.data.JsonReader({
        root: 'rows',
        totalProperty: 'total',
        id: 'id_ec'
    },[         
        {name: 'id_ec',             type: 'int',        mapping: 'id_ec'},
        {name: 'competencia',       type: 'string',     mapping: 'competencia'},
        {name: 'subcompetencia',    type: 'string',     mapping: 'subcompetencia'},
        {name: 'u_r1',              type: 'boolean',         mapping: 'u_r1'},
        {name: 'u_r2',              type: 'boolean',         mapping: 'u_r2'},
        {name: 'u_r3',              type: 'boolean',         mapping: 'u_r3'},
        {name: 'u_r4',              type: 'boolean',         mapping: 'u_r4'},
        {name: 's_r1',              type: 'boolean',        mapping: 's_r1'},
        {name: 's_r2',              type: 'boolean',        mapping: 's_r2'},
        {name: 's_r3',              type: 'boolean',        mapping: 's_r3'},
        {name: 's_r4',              type: 'boolean',        mapping: 's_r4'}
    ]),
    sortInfo:{field: 'competencia', direction: "asc"},
    groupField:'competencia',
    remoteSort : true
});

//asigno el datastore al paginador
var paginadorED= new Ext.PagingToolbar({
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
paginadorED.bindStore(miEDDataStore);

buscador= new Ext.ux.grid.Search({
    iconCls:'icon-zoom',
//    readonlyIndexes:['id_documento'],
    disableIndexes:['id_ec','competencia','subcompetencia','s_r1','s_r2','s_r3','s_r4','u_r1','u_r2','u_r3','u_r4'],
    align:'right',
    minChars:3
});
//
r1Check = new Ext.grid.RadioColumn({
    id:'r1Check',
    header: "<p class=enc_r1>Inferior a lo<br>esperado</p>",
    dataIndex: 'u_r1',
    radioGroupClass:'u_r',
    q_radios:4,
    campo_id:'id_ec',
    tooltip:'Inferior a lo esperado'
});
r2Check = new Ext.grid.RadioColumn({
    id:'r2Check',
    header: "<p class=enc_r1>Necesita<br>Mejorar</p>",
    dataIndex: 'u_r2',
    radioGroupClass:'u_r',
    q_radios:4,
    campo_id:'id_ec',
    tooltip:'Necesita Mejorar'
});
r3Check = new Ext.grid.RadioColumn({
    id:'r3Check',
    header: "<p class=enc_r1>Bueno</p>",
    dataIndex: 'u_r3',
    radioGroupClass:'u_r',
    q_radios:4,
    campo_id:'id_ec',
    tooltip:'Bueno'
});
r4Check = new Ext.grid.RadioColumn({
    id:'r4Check',
    header: "<p class=enc_r1>Destacado</p>",
    dataIndex: 'u_r4',
    radioGroupClass:'u_r',
    q_radios:4,
    campo_id:'id_ec',
    tooltip:'Destacado'
});

miEDColumnModel = new Ext.grid.ColumnModel([
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
    },{hideable:false,
        header: 'Competencia',
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
        readOnly: true
    },r1Check,r2Check,r3Check,r4Check,{
        header: 'Inferior a lo esperado',
        dataIndex: 's_r1',
        sortable: true,
        width:  50,
        align:'center',
        fixed:false,
        renderer:showMarca,
        readOnly: true,
        hidden: showCalificacion()
      },{
        header: 'Necesita Mejorar',
        dataIndex: 's_r2',
        sortable: true,
        width:  50,
        align:'center',
        fixed:false,
        renderer:showMarca,
        readOnly: true,
        hidden: showCalificacion()
      },{
        header: 'Bueno',
        dataIndex: 's_r3',
        sortable: true,
        width:  50,
        align:'center',
        fixed:false,
        renderer:showMarca,
        readOnly: true,
        hidden: showCalificacion()
      },{
        header: 'Destacado',
        id: 'destacado',
        dataIndex: 's_r4',
        sortable: true,
        width:  50,
        align:'center',
        fixed:false,
        readOnly: true,
        renderer:showMarca,
        hidden: showCalificacion()
    }
]);

miEDEditorGrid =  new Ext.grid.GridPanel({
    id: 'miEDEditorGrid',
    title:'Evaluación de Desempeño',
    store: miEDDataStore,
    cm: miEDColumnModel,
    view: new Ext.grid.GroupingView({
            forceFit:false,
            headersDisabled :true,
            groupTextTpl: '{group} ({[values.rs.length]} {[values.rs.length > 1 ? "subcompetencias" : "subcompetencia"]})'
        }),
    enableColLock:false,
    renderTo: 'grillita',
    stripeRows:true,
//    viewConfig: {
//        forceFit: true
//    },      
    autoScroll : true,	 
    selModel: new Ext.grid.RowSelectionModel({singleSelect:true}),
    height:500,
    plugins:[buscador,r1Check,r2Check,r3Check,r4Check],
    bbar:[paginadorED],
    tbar: [{
            text: 'Volver',
            tooltip: 'volver a mis evaluaciones...',
            iconCls:'atras_ico',                      // reference to our css
            handler: function (){
                Ext.get('browser').load({
                    url: CARPETA_MIS_EEDD+"/index/38",
                    scripts: true,
                    text: "Cargando..."
                });
            }, 
          }
//          {
//            text: 'Cerrar evaluación',
//            tooltip: 'Cerrar esta evaluación',
//            iconCls:'mied_fin_ico',                      // reference to our css
//            handler: function (){
//               alert("cierre ed");
//            }, 
//            hidden: !permiso_alta
//          }
            ,{
                text: 'Ver Evaluación en PDF',
                tooltip: 'previsualizar',
                iconCls:'mied_pdf_ico',                      // reference to our css
                disabled:false,
                handler: function (){
                    var id=ID_ED;
                    var nom="ED-"+id;
                    window.open(CARPETA_PDF+'/ver_ed/'+id+"/"+nom)
                }, 
                hidden: !permiso_alta
            }
          ,'->',{
            id: 'div_total_cump',
            text: '<b>Peso M&aacute;ximo:'+MAX_CUMP+' - Peso ED:'+T_CUMP+' - Cumplimiento:'+CUMP+'%</b>',
            disabled:true,
            tooltip: '',
            iconCls:'value_area',
            buttonAlign: 'right',
            handler: "",
            hidden: showCalificacion()
          }
    ]
});

miEDDataStore.load({params: {start: 0}});
var altura=Ext.getBody().getSize().height - 60;
miEDEditorGrid.setHeight(altura);


Ext.getCmp('browser').on('resize',function(comp){
        miEDEditorGrid.setWidth(this.getSize().width);
        miEDEditorGrid.setHeight(Ext.getBody().getSize().height - 60);

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

function showCalificacion(cm){
    if(CIERRE_S==0)
        return true;
};

//-->FIN PANEL SUPERIOR