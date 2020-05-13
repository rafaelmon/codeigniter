periodosJS = new Ext.data.JsonStore({
    url: CARPETA+'/combo_periodos',
    root: 'rows',
    fields: ['id_periodo', 'periodo']
});
periodosJS.load();
periodosJS.on('load', function(  js , records, options )
{
    var tRecord = Ext.data.Record.create(
        {name: 'id_periodo', type: 'int'},
        {name: 'periodo', type: 'string'}
    );
    var myNewT = new tRecord({
        id_periodo: '-1',
        periodo   : 'Todos'
    });
    periodosJS.insert( 0, myNewT);	
});

edAdminDataStore = new Ext.data.Store({
    id: 'edAdminDataStore',
    proxy: new Ext.data.HttpProxy({
              url: CARPETA+'/listado', 
              method: 'POST'
          }),
    baseParams:{tampagina: TAM_PAGINA}, // this parameter is passed for any HTTP request
    reader: new Ext.data.JsonReader({
      root: 'rows',
      totalProperty: 'total',
      id: 'id_evaluacion'
    },[ 
      {name: 'id_evaluacion',           type: 'int',    mapping: 'id_evaluacion'},        
      {name: 'periodo',                 type: 'string', mapping: 'periodo'},
      {name: 'id_periodo',              type: 'int',    mapping: 'id_periodo'},
      {name: 'id_usuario',              type: 'int',    mapping: 'id_usuario'},
      {name: 'empleado',                type: 'string', mapping: 'empleado'},
      {name: 'area',                    type: 'string', mapping: 'area'},
      {name: 'gerencia',                type: 'string', mapping: 'gerencia'},
      {name: 'id_estado',               type: 'int',    mapping: 'id_estado'},
      {name: 'estado',                  type: 'string', mapping: 'estado'},
      {name: 'id_usuario_supervisor',   type: 'int',    mapping: 'id_usuario_supervisor'},
      {name: 'supervisor',              type: 'string', mapping: 'supervisor'},
      {name: 'a_c1_u',                  type: 'int',    mapping: 'a_c1_u'},
      {name: 'a_c1_s',                  type: 'int',    mapping: 'a_c1_s'},
      {name: 'a_c2',                    type: 'int',    mapping: 'a_c2'},
      {name: 'a_fyam',                  type: 'int',    mapping: 'a_fyam'},
      {name: 'a_pm',                    type: 'int',    mapping: 'a_pm'},
      {name: 'a_fm',                    type: 'int',    mapping: 'a_fm'},
      {name: 'v_peso',                  type: 'string', mapping: 'v_peso'},
      {name: 'v_cump',                  type: 'string', mapping: 'v_cump'}
    ]),
    sortInfo:{field: 'id_evaluacion', direction: "DESC"},
    remoteSort: true
});
paginador.bindStore(edAdminDataStore);
	
function setParamsEd(){
    edAdminDataStore.setBaseParam('f_id_periodo',Ext.getCmp('periodos-filtro').getValue());
}; 
edAdminBotonesAction = new Ext.grid.ActionColumn({
    editable:false,
    menuDisabled:true,
    header:'Evaluación',
    hideable:false,
    align:'center',
    width:  80,
//    tooltip:'',
    hidden:false,
    items:[
        {
        icon:URL_BASE+'images/doc_pdf.png',
        iconCls :'col_accion',
        getClass: showVerPDF,
        tooltip:'Ver evaluación',
        handler: clickBtnVerEdAdminPdf
    },{
        icon:URL_BASE+'images/editores.png',
        iconCls :'col_accion',
        getClass: showVerEditSupervisor,
        tooltip:'Editar Supervisor',
        handler: clickBtnEditSupervisorAdmEd
    }
]
});

 usuariosSupervisorDS = new Ext.data.Store({
    id:'idAprobadoresDS',
    proxy: new Ext.data.HttpProxy({
        url: CARPETA+'/combo_usuarios',
        method: 'POST'
    }),
    reader: new Ext.data.JsonReader({
        root: 'rows',
        totalProperty: 'total',
        id: 'id_usuario'
    }, [
        {name: 'id_usuario', mapping: 'id_usuario'},
        {name: 'nomape', mapping: 'nomape'},
//            {name: 'usuario', mapping: 'usuario'},
//            {name: 'puesto', mapping: 'puesto'}
    ])
});

usuariosSupervisorCombo = new Ext.form.ComboBox({
    id:'aprobadoresCombo',
    store: usuariosSupervisorDS,
    blankText:'campo requerido',
    allowBlank: false,
    fieldLabel: 'Usuario',
    displayField:'nomape',
    valueField:'id_usuario',
    typeAhead: false,
    loadingText: 'Buscando...',
    anchor : '100%',
    forceSelection : true,
    minChars:3,
//        labelStyle: 'font-weight:bold;',
    pageSize:10,
    tabIndex: 11,
    emptyText:'Ingresa caracteres para buscar',
    valueNotFoundText:"",
//        tpl: aprobadorTpl,
//        itemSelector: 'div.search-item'
}); 

edAdminColumnModel = new Ext.grid.ColumnModel(
[{
        header: '#',
        readOnly: true,
        dataIndex: 'id_evaluacion',
        width: 40,        
        sortable: true,
        hidden: false
    },{
        header: 'Periodo',
        dataIndex: 'periodo',
        width: 130,
        sortable: true,
    },{
        header: 'Empleado',
        dataIndex: 'empleado',
        width: 130,
        sortable: true,
    },{
        header: 'Area',
        dataIndex: 'area',
        width: 130,
        sortable: true,
    },{
        header: 'Gerencia',
        dataIndex: 'gerencia',
        width: 130,
        sortable: true,
    },{
        header: 'Supervisor',
        dataIndex: 'supervisor',
        width:  200,
        sortable: true
    },{
        header: 'Estado',
        dataIndex: 'estado',
        width:  90,
        sortable: true,
        renderer: showEstado
    },{
        header: 'C1-u',
        tooltip:'Avance en evaluación de competencias cualitativas por parte del usuario',
        align:'center',
        dataIndex: 'a_c1_u',
        width:  40,
        sortable: false,
        renderer: showAvance
    },{
        header: 'C1-s',
        tooltip:'Avance en evaluación de competencias cualitativas por parte del supevisor',
        align:'center',
        dataIndex: 'a_c1_s',
        width:  40,
        sortable: false,
        renderer: showAvance
      },{
        header: 'C2',
        tooltip:'Avance en evaluación de competencias cuantitativas',
        align:'center',
        dataIndex: 'a_c2',
        width:  40,
        sortable: false,
        renderer: showAvance
      },{
        header: 'FyAM',
        tooltip:'Avance en definición de fortalezas y aspectos a mejorar',
        align:'center',
        dataIndex: 'a_fyam',
        width:  40,
        sortable: false,
        renderer: showAvance
      },{
        header: 'PM',
        tooltip:'Definición de planes de mejora',
        align:'center',
        dataIndex: 'a_pm',
        width:  40,
        sortable: false,
        renderer: showAvance
      },{
        header: 'FM',
        tooltip:'Fijación de metas',
        align:'center',
        dataIndex: 'a_fm',
        width:  40,
        sortable: false,
        renderer: showAvanceOptativo
      },{
        header: 'Peso',
        tooltip:'Peso ED',
        align:'center',
        dataIndex: 'v_peso',
        width:  55,
        sortable: false,
        renderer: showCumplimiento
      },{
        header: '% Cump',
        tooltip:'Peso ED',
        align:'center',
        dataIndex: 'v_cump',
        width:  55,
        sortable: false,
        renderer: showCumplimiento
      }
      ,edAdminBotonesAction
]);
    
edAdminPeriodosFiltro = new Ext.form.ComboBox({
    id:'periodos-filtro',
    forceSelection : true,
    value: 'Todos',
    store: periodosJS,
    editable : false,
    displayField: 'periodo',
    valueField:'id_periodo',
    allowBlank: false,
    width:  200,
    selectOnFocus:true,
    triggerAction: 'all'
});
edAdminPeriodosFiltro.on('select', filtrarGrillaEd);
function filtrarGrillaEd (combo, record, index){
    edAdminDataStore.load({
            params: {
                    f_id_periodo: Ext.getCmp('periodos-filtro').getValue()
            }
    });	
};
edAdminBuscador= new Ext.ux.grid.Search({
    iconCls:'icon-zoom',
//    readonlyIndexes:[''],
    disableIndexes:['id_evalluacion','estado','periodo','a_c1_u','a_c1_s','a_c2','a_fyam','a_pm','a_fm','v_peso','v_cump'],
    align:'left',
    minChars:3
});
  
edAdminListingEditorGrid =  new Ext.grid.EditorGridPanel({
    id: 'edAdminListingEditorGrid',
    title: 'Administrar Evaluaciones de Desempeño',
    store: edAdminDataStore,
    cm: edAdminColumnModel,
    enableColLock:false,
    trackMouseOver:true, //que se coloree cuando pasas el mouse por encima
    loadMask: true, //que te ponga el loading cuando se esta generando el store
    renderTo: 'grillita',
    viewConfig: {
        forceFit: false
    },
    tbar: ['<b>Filtrar por:</b> Periodos',edAdminPeriodosFiltro,'&emsp;|&emsp;',
            {
                text: 'Descargar listado',
    //            tooltip: 'e...',
                iconCls:'archivo_excel_ico',
                handler: go_clickBtnEDAdminExcel
            }],
    plugins:[edAdminBuscador], 
    clicksToEdit:2,
    height:500,
    layout: 'fit',
    selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
    bbar: paginador
 });   

  edAdminDataStore.load({params: {start: 0, limit: TAM_PAGINA}});
  
function showEstado (value,metaData,superData){
    var estado=superData.json.id_estado;
    switch (estado)
    {
        case '1':
        metaData.attr = 'style="background-color:#FF8000; color:#FFF;"';
        break;
        case '2':
        metaData.attr = 'style="background-color:#FFE500; color:#8B8B8A;"';
        break;
        case '3':
        metaData.attr = 'style="background-color:#688A08; color:#FFF;"';
        break;     
    }
    var deviceDetail = superData.get('obs');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';

    return value;
}
   
var altura=Ext.getBody().getSize().height - 60;
edAdminListingEditorGrid.setHeight(altura);

Ext.getCmp('browser').on('resize',function(comp){
    edAdminListingEditorGrid.setWidth(this.getSize().width);
    edAdminListingEditorGrid.setHeight(Ext.getBody().getSize().height - 60);

});
function showCumplimiento (value,metaData,superData){
    var tipo=superData.json.tipo;
    var cierre_s=superData.json.cierre_s;
    if(tipo==1 && cierre_s==0)
        value="-";
    metaData.attr = 'style="background-color:#000; color:#FFF;"';
    return value;
}
  function showAvance (value,metaData,record){
    if (value==1)
    {
        metaData.attr = 'style="background-color:#04B404; color:#FFF;"';
        metaData.attr += 'ext:qtip="Completo"';
        value='&checkmark;';
    }
    else
    {
        metaData.attr = 'style="background-color:#FF0000; color:#FFF;"';
        metaData.attr += 'ext:qtip="Incompleto-Obligatorio"';
        value='x';
    } 
        
    return value;
}
  function showAvanceOptativo (value,metaData,record){
    var tipo=record.json.tipo;
    if (value==1)
    {
        metaData.attr = 'style="background-color:#04B404; color:#FFF;"';
        metaData.attr += 'ext:qtip="Completo"';
        value='&checkmark;';
    }
    else
    {
        metaData.attr = 'style="background-color:#FF8000; color:#FFF;"';
        metaData.attr += 'ext:qtip="Incompleto-Opcional"';
        value='x';
    } 
        
    return value;
}
function clickBtnVerEdAdminPdf(grid,rowIndex,colIndex,item ,event){
    var record=grid.getStore().getAt(rowIndex);
    var id=record.json.id_evaluacion;
    var semestre=record.json.semestre;
    var anio=record.json.anio;
    var nom="ED-"+anio+semestre+"-"+1000000+id;
    window.open(CARPETA_PDF+'/ver_ed_admin/'+id+"/"+nom)
  };
function showVerPDF(value,metaData,record){
    var e=record.json.id_estado;
    if(e==3)
        return 'x-grid-center-icon';                
    else
        return 'x-hide-display';  
};
  
  function showVerEditSupervisor(value,metaData,record){
    var e=record.json.id_estado;
    if(e==1 && permiso_modificar == 1)
        return 'x-grid-center-icon';                
    else
        return 'x-hide-display';  
}; 