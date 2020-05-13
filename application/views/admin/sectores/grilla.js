sectoresDataStore = new Ext.data.Store({
    id: 'sectoresDataStore',
    proxy: new Ext.data.HttpProxy({
              url: CARPETA+'/listado', 
              method: 'POST'
          }),
    baseParams:{tampagina: TAM_PAGINA}, // this parameter is passed for any HTTP request
    reader: new Ext.data.JsonReader({
      root: 'rows',
      totalProperty: 'total',
      id: 'id_sector'
    },[ 
      {name: 'id_sector',    type: 'int',     mapping: 'id_sector'},        
      {name: 'sector',       type: 'string',  mapping: 'sector'},
      {name: 'habilitado',     type: 'boolean', mapping: 'habilitado'}
    ]),
    sortInfo:{field: 'id_sector', direction: "ASC"},
    remoteSort: true
});
paginador.bindStore(sectoresDataStore);
	
habilitadoCheck = new Ext.grid.CheckColumn({
        id:'habilitadoCheck',
        header: "Habilitado",
        dataIndex: 'habilitado',
        width: 70,
        sortable: true,
        align:'center',
        menuDisabled:true,
        pintar_deshabilitado: true,
        pintar_habilitado: false,
        pintar_deshabilitado_color: '#FF0000',
        pintar_habilitado_color: '#FF0000',
        disabled: true,
        tabla: 'grl_sectores',
        campo_id: 'id_sector'
});

sectoresColumnModel = new Ext.grid.ColumnModel(
[{
        header: '#',
        readOnly: true,
        dataIndex: 'id_sector',
        width: 40,        
        sortable: true,
        hidden: false
    },{
        header: 'Sector',
        dataIndex: 'sector',
        width: 400,
        sortable: true,
    },habilitadoCheck
]);
    
sectoresBuscador= new Ext.ux.grid.Search({
    iconCls:'icon-zoom',
//    readonlyIndexes:[''],
    disableIndexes:['id_sector','habilitado'],
    align:'left',
    minChars:3
});



sectoresListingEditorGrid =  new Ext.grid.EditorGridPanel({
    id: 'sectoresListingEditorGrid',
    title: 'Sectores',
    store: sectoresDataStore,
    cm: sectoresColumnModel,
    enableColLock:false,
    trackMouseOver:true, //que se coloree cuando pasas el mouse por encima
    loadMask: true, //que te ponga el loading cuando se esta generando el store
    renderTo: 'grillita',
    viewConfig: {
        forceFit: false
    },
    tbar: [
        {
            text: 'Nuevo Sector',
            tooltip: 'Alta nuevo sector',
            iconCls:'add',                     
            handler: clickBtnNuevoSector
         }
    ],
    plugins:[habilitadoCheck,sectoresBuscador], 
    clicksToEdit:2,
    height:500,
    layout: 'fit',
    selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
    bbar: paginador
 });   
sectoresListingEditorGrid.on('afteredit', guardarCambiosGrillaSector);
sectoresDataStore.load({params: {start: 0, limit: TAM_PAGINA}});

 
var altura=Ext.getBody().getSize().height - 60;
sectoresListingEditorGrid.setHeight(altura);

Ext.getCmp('browser').on('resize',function(comp){
    sectoresListingEditorGrid.setWidth(this.getSize().width);
    sectoresListingEditorGrid.setHeight(Ext.getBody().getSize().height - 60);

});

function msgProcess(titulo){
     Ext.MessageBox.show({
        title: titulo,
        msg: 'Procesando, por favor espere...',
        progress:true,
        progressText: 'Procesando...', 
        width:300, 
        wait:true, 
        waitConfig: {interval:200}
    });
}
function guardarCambiosGrillaSector(oGrid_event){
        var fields = [];
        fields.push(oGrid_event.field);
        var values = [];
        values.push(oGrid_event.value);
        var encoded_array_f = Ext.encode(fields);
        var encoded_array_v = Ext.encode(values);
		 
   Ext.Ajax.request({   
      waitMsg: 'Por favor espere...',
      url: CARPETA+'/update',
      params: {
		 id_sector:  oGrid_event.record.data.id_sector,     
		 campos :  encoded_array_f,
		 valores : encoded_array_v
      }, 
      success: function(response){              
         var result=eval(response.responseText);
         switch(result){
         case 1:
            sectoresDataStore.commitChanges();
            sectoresDataStore.reload();
            break;
         case 2:
            Ext.MessageBox.alert('Error','No tiene permisos para realizar la operacion solicitada.');
            sectoresDataStore.reload();
            break;                   
         default:
            Ext.MessageBox.alert('Error','No hay conexión con la base de datos. Asegurese de tener conexion');
            break;
         }
      },
      failure: function(response){
         var result=response.responseText;
         Ext.MessageBox.alert('Uh uh...','No hay conexión con la base de datos. Intenta otra vez');    
      }                      
   });   
  }