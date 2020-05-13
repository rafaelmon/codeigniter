subcompetenciasDataStore = new Ext.data.Store({
      id: 'subcompetenciasDataStore',
      proxy: new Ext.data.HttpProxy({
                url: CARPETA+'/listado_sub', 
                method: 'POST'
            }),
      baseParams:{tampagina: TAM_PAGINA}, // this parameter is passed for any HTTP request
      reader: new Ext.data.JsonReader({
        root: 'rows',
        totalProperty: 'total',
        id: 'id_subcompetencia'
      },[ 
        {name: 'id_subcompetencia', type: 'int', mapping: 'id_subcompetencia'},        
        {name: 'subcompetencia', type: 'string', mapping: 'subcompetencia'},
        {name: 'obligatoria', type: 'string', mapping: 'obligatoria'},
        {name: 'habilitado', type: 'bool', mapping: 'habilitado'}
      ]),
      sortInfo:{field: 'id_subcompetencia', direction: "asc"},
      remoteSort : true
    });

habilitadaCheck = new Ext.grid.CheckColumn({
        id:'habilitado',
        header: "Habilitada",
        dataIndex: 'habilitado',
        width: 60,
        sortable: true,
        menuDisabled:true,
        pintar_deshabilitado:true,
         disabled: false, //-->NO FUNCIONA
        tabla: 'ed_subcompetencias',
        align:'center',
        campo_id: 'id_subcompetencia'
    });
    	
subcompetenciasColumnModel = new Ext.grid.ColumnModel(
    [{
        header: '#',
        readOnly: true,
        dataIndex: 'id_subcompetencia',
        width: 40,        
        renderer: function(value, cell){ 
                cell.css = "readonlycell";
        	return value;		 
        },
        hidden: false
      },{
        header: 'Subompetencia',
        dataIndex: 'subcompetencia',
        width:  230,
        readOnly: !permiso_modificar,
        sortable: true,
      },{
        header: 'Obligatoria',
        dataIndex: 'obligatoria',
        width:  100,
        align:'center',
        readOnly: !permiso_modificar,
        sortable: true,
        renderer: showObligatoria
      },habilitadaCheck
      ]
    );
 subcompetenciasColumnModel.defaultSortable= true;
 
subcompetenciasListingEditorGrid =  new Ext.grid.EditorGridPanel({
    id: 'subcompetenciasListingEditorGrid',
    store: subcompetenciasDataStore,
    cm: subcompetenciasColumnModel,
    enableColLock:false,
    trackMouseOver:true, //que se coloree cuando pasas el mouse por encima
    loadMask: true, //que te ponga el loading cuando se esta generando el store
    viewConfig: {
        forceFit: false
    },
    plugins:[habilitadaCheck],
    clicksToEdit:2,
    height:500,
    layout: 'fit',
    selModel: new Ext.grid.RowSelectionModel({singleSelect:true}),
    bbar: new Ext.PagingToolbar({
        pageSize: TAM_PAGINA,
        store: subcompetenciasDataStore,
        displayMsg: 'Mostrando {0} - {1} de {2}',				
        displayInfo: true
    }),
    tbar: [
          {
            text: 'Nueva Subcompetencia',
            tooltip: 'Crear una nueva subcompetencia',
            iconCls:'add',                      // reference to our css
            handler: altaSubcompetencia,
			hidden: !permiso_alta
          }
      ]
    });
     
//   subcompetenciasDataStore.load({params: {start: 0, limit: TAM_PAGINA}});
   
   subcompetenciasListingEditorGrid.on('afteredit', guardarSubcompetencia);
   
   subcompetenciasPanel = new Ext.Panel(
    {
//        collapsible: false,
//        split: false,
//        header: true,
        title: 'Subcompetencias',
        region:'east',
        autoScroll : true,
        columnWidth:.5,
//        height: 400,
//        minSize: 100,
//        maxSize: 350,	
//        margins: '0 5 5 5',
//        html:'<p>panel subcompetencias</p>',
        layout: 'fit',
        items:[subcompetenciasListingEditorGrid]
    });
    
   // guarda los cambios en los datos del modulo luego de la edicion
  function guardarSubcompetencia(oGrid_event){
   Ext.Ajax.request({   
      waitMsg: 'Por favor espere...',
      url: CARPETASUBCOMPETENCIA+'/modificar',
      params: {
		 id_subcompetencia: oGrid_event.record.data.id_subcompetencia,     
		 subcompetencia : oGrid_event.record.data.subcompetencia
      }, 
      success: function(response){              
         var result=eval(response.responseText);
         switch(result){
         case 1:
            subcompetenciasDataStore.commitChanges();
            subcompetenciasDataStore.reload();
            break;
         case 2:
            Ext.MessageBox.alert('Error','No tiene permisos para realizar la operacion solicitada.');
            subcompetenciasDataStore.reload();
            break;
         case 3:
            Ext.MessageBox.alert('Error','El campo subcompetencia es obligatorio.');
            subcompetenciasDataStore.reload();
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
var altura=Ext.getBody().getSize().height - 75;
subcompetenciasPanel.setHeight(altura);

Ext.getCmp('browser').on('resize',function(comp){

    subcompetenciasPanel.setWidth(Ext.getCmp('browser').getSize().width);

    subcompetenciasPanel.setHeight(Ext.getBody().getSize().height - 60);

});
function showObligatoria (value,metaData,superData){
    var tipo=superData.json.obligatoria;
    switch (tipo)
    {
        case '1':
        metaData.attr = 'style="background-color:#DF0101; color:#FFF;"';
        value='Si';
        break;
        case '0':
        metaData.attr = 'style="background-color:#0B610B; color:#FFF;"';
        value='No';
        break;
    }
    return value;
}