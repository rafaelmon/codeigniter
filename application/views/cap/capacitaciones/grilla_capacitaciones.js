capCapacitacionesDataStore = new Ext.data.Store({
    id: 'capCapacitacionesDataStore',
    proxy: new Ext.data.HttpProxy({
              url: CARPETA+'/listado', 
              method: 'POST'
          }),
    baseParams:{tampagina: TAM_PAGINA}, // this parameter is passed for any HTTP request
    reader: new Ext.data.JsonReader({
      root: 'rows',
      totalProperty: 'total',
      id: 'id_capacitacion'
    },[ 
      {name: 'id_capacitacion', type: 'int',    mapping: 'id_capacitacion'},        
      {name: 'titulo',          type: 'string', mapping: 'titulo'},
      {name: 'descripcion',     type: 'string', mapping: 'descripcion'},
      {name: 'usuario_alta',    type: 'string', mapping: 'usuario_alta'}
    ]),
    sortInfo:{field: 'id_capacitacion', direction: "ASC"},
    remoteSort: true
});

capCapacitacionesPaginador= new Ext.PagingToolbar({
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
capCapacitacionesPaginador.bindStore(capCapacitacionesDataStore);

botonesCapacitacionAction = new Ext.grid.ActionColumn({
    editable:false,
    menuDisabled:true,
    header:'Acciones',
    hideable:false,
    align:'center',
    width:  75,
    tooltip:'Acciones',
     hidden:false,
    items:[
        {
        icon:URL_BASE+'images/editor.png',
        iconCls :'col_accion',
        tooltip: 'Nueva tarea de capacitación',
        hidden:true,
        handler: clickBtnSetResponsable
        },
    ]
});

capCapacitacionesColumnModel = new Ext.grid.ColumnModel(
[{
        header: '#',
        readOnly: true,
        dataIndex: 'id_capacitacion',
        width: 40,        
        sortable: true,
        hidden: false
    },{
        header: 'Tema (Hallazgo)',
        dataIndex: 'titulo',
        width: 250,
        sortable: true,
        renderer: showTooltipTitulo,
//    },{
//        header: 'Descripción',
//        dataIndex: 'descripcion',
//        width: 400,
//        sortable: true,
    },{
        header: 'Usuario alta',
        dataIndex: 'usuario_alta',
        width: 200,
        sortable: true,
    },botonesCapacitacionAction
]);
    
capCapacitacionesBuscador= new Ext.ux.grid.Search({
    iconCls:'icon-zoom',
//    readonlyIndexes:[''],
    disableIndexes:[],
    align:'left',
    minChars:3
});

capCapacitacionesListingEditorGridPanel =  new Ext.grid.EditorGridPanel({
    id: 'capCapacitacionesListingEditorGridPanel',
    title: 'Temas de Capacitación',
    columnWidth:.5,
    autoScroll : true,
    store: capCapacitacionesDataStore,
    cm: capCapacitacionesColumnModel,
    enableColLock:false,
    trackMouseOver:true, 
    loadMask: true, 
    viewConfig: {
        forceFit: false
    },
    tbar: [
        {
            text: 'Nuevo',
            tooltip: 'Alta nuevo tema de capacitación',
            iconCls:'add',
            hidden: !permiso_alta,
            handler: clickBtnNuevaCapacitacion
         }
    ],
    plugins:[capCapacitacionesBuscador], 
    clicksToEdit:2,
    height:500,
    layout: 'fit',
    selModel: new Ext.grid.RowSelectionModel({singleSelect:true}),
    bbar: capCapacitacionesPaginador
 });   
capCapacitacionesDataStore.load({params: {start: 0, limit: TAM_PAGINA}});

var altura=Ext.getBody().getSize().height - 75;
capCapacitacionesListingEditorGridPanel.setHeight(altura);

function showTooltipTitulo(value, metaData,record){
    var txt = record.json.descripcion;
   if ( txt   != '')
   {
       metaData.attr += 'ext:qtip="'+ txt + '"';
   }
   return value;
}
