participantesDataStore = new Ext.data.Store({
    id: 'participantesDataStore',
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
      {name: 'participante',    type: 'string', mapping: 'participante'}
    ]),
    sortInfo:{field: 'id_persona', direction: "ASC"},
    remoteSort: true
});
participantesDataStore.load({params: {start: 0, limit: TAM_PAGINA}});

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
capParticipantesPaginador.bindStore(participantesDataStore);

participantesColumnModel = new Ext.grid.ColumnModel(
    [{
        header: '#',
        readOnly: true,
        dataIndex: 'id_persona',
        width: 40,        
        sortable: true,
        hidden: false
    },{
        header: 'Persona',
        dataIndex: 'participante',
        width: 200,
        sortable: true,
        renderer: showTooltip
    },{
        header: 'Documento',
        dataIndex: 'dni',
        width: 100,
        align:'center',
        sortable: true
    }]
);
    
participantesBuscador= new Ext.ux.grid.Search({
    iconCls:'icon-zoom',
//    readonlyIndexes:[''],
    disableIndexes:[],
    align:'left',
    minChars:3
});

participantesGrid =  new Ext.grid.GridPanel({
    id: 'participantesGrid',
    store: participantesDataStore,
    cm: participantesColumnModel,
    enableColLock:false,
    trackMouseOver:true, 
    loadMask: true, 
    viewConfig: {
        forceFit: false
    },
    tbar: [],
    plugins:[participantesBuscador], 
    height:500,
    layout: 'fit',
    selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
    bbar: capParticipantesPaginador
 });   


capGrillaParticipantesPanel = new Ext.Panel(
{
        title: 'Listado Participantes',
        columnWidth:.50,
        autoScroll : true,
        layout: 'fit',
        items:[participantesGrid]
    });

var altura=Ext.getBody().getSize().height - 75;
capGrillaParticipantesPanel.setHeight(altura);

function showTooltip(value, metaData,record){
    var participante = record.json.participante;
   if ( participante   != '')
   {
       metaData.attr += 'ext:qtip="'+ participante + '"';
   }
   return value;
   }