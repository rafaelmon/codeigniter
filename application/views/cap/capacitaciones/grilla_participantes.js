capCapacitacionesParticipantesDataStore = new Ext.data.Store({
    id: 'capCapacitacionesParticipantesDataStore',
    proxy: new Ext.data.HttpProxy({
              url: CARPETA+'/listadoParticipantes', 
              method: 'POST'
          }),
    baseParams:{tampagina: TAM_PAGINA}, // this parameter is passed for any HTTP request
    reader: new Ext.data.JsonReader({
      root: 'rows',
      totalProperty: 'total',
//      id: 'id_persona'
    },[ 
      {name: 'id_persona',      type: 'int',    mapping: 'id_persona'},        
      {name: 'dni',             type: 'int',    mapping: 'dni'},        
      {name: 'id_tarea',        type: 'int',    mapping: 'id_tarea'},        
      {name: 'persona',    type: 'string', mapping: 'persona'},
       {name: 'tipo',            type: 'string', mapping: 'tipo'},  
    ]),
    sortInfo:{field: 'id_persona', direction: "ASC"},
    remoteSort: true
});

capParticipantesPaginador= new Ext.PagingToolbar({
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
capParticipantesPaginador.bindStore(capCapacitacionesParticipantesDataStore);

capCapacitacionesParticipantesColumnModel = new Ext.grid.ColumnModel(
    [{
        header: '#',
        readOnly: true,
        dataIndex: 'id_persona',
        width: 40,        
        sortable: true,
        hidden: false
    },{
        header: 'Tipo de Participación',
        dataIndex: 'tipo',
        width: 120,
        sortable: true,
        renderer: showTipo
    },{
        header: 'Persona',
        dataIndex: 'persona',
        width: 250,
        sortable: true,
        renderer: showTooltip
    },{
        header: 'Documento',
        dataIndex: 'dni',
        width: 100,
        align:'center',
        sortable: true
    },{
        header: 'Tarea',
        dataIndex: 'id_tarea',
        width: 100,
        align:'center',
        sortable: true
    }]
);
    
capCapacitacionesParticipantesBuscador= new Ext.ux.grid.Search({
    iconCls:'icon-zoom',
//    readonlyIndexes:[''],
    disableIndexes:[],
    align:'left',
    minChars:3
});

capParticipantesGridPanel =  new Ext.grid.GridPanel({
    id: 'capParticipantesGridPanel',
    store: capCapacitacionesParticipantesDataStore,
    cm: capCapacitacionesParticipantesColumnModel,
    title: 'Listado Participantes',
    enableColLock:false,
    trackMouseOver:true, 
    loadMask: true, 
    viewConfig: {
        forceFit: false
    },
    tbar: [],
    plugins:[capCapacitacionesParticipantesBuscador], 
    height:500,
    layout: 'fit',
//    region: 'south',
    anchor:'100%',
    selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
    bbar: capParticipantesPaginador
 });   

var altura=(Ext.getBody().getSize().height - 75)/2;
capParticipantesGridPanel.setHeight(altura);

function showTooltip(value, metaData,record){
    var participante = record.json.participante;
   if ( participante   != '')
   {
       metaData.attr += 'ext:qtip="'+ participante + '"';
   }
   return value;
   }
function showTipo (value,metaData,superData){
    var tipo=superData.json.tipo;
    switch (tipo)
    {
        case '1':
            metaData.attr = 'style="background-color:#088A08; color:#FFF;"';
            metaData.attr += 'ext:qtip="'+ value + '"';
            break;
        case '2':
            metaData.attr = 'style="background-color:##088A08; color:#FFF;"';
            metaData.attr += 'ext:qtip="'+ value + '"';
            break;

    }
    return value;
}