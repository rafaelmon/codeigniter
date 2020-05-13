competenciasDataStore = new Ext.data.Store({
      id: 'competenciasDataStore',
      proxy: new Ext.data.HttpProxy({
                url: CARPETA+'/listado', 
                method: 'POST'
            }),
      baseParams:{tampagina: TAM_PAGINA}, // this parameter is passed for any HTTP request
      reader: new Ext.data.JsonReader({
        root: 'rows',
        totalProperty: 'total',
        id: 'id_competencia'
      },[ 
        {name: 'id_competencia',    type: 'int',    mapping: 'id_competencia'},        
        {name: 'competencia',       type: 'string', mapping: 'competencia'},
        {name: 'tipo',              type: 'int',    mapping: 'tipo'},
        {name: 'q_subcomp',         type: 'int',    mapping: 'q_subcomp'},
        {name: 'habilitado',        type: 'bool',   mapping: 'habilitado'}
      ]),
      sortInfo:{field: 'id_competencia', direction: "asc"},
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
        tabla: 'ed_competencias',
        align:'center',
        campo_id: 'id_competencia'
    });
    	
 competenciasColumnModel = new Ext.grid.ColumnModel(
    [{
        header: '#',
        readOnly: true,
        dataIndex: 'id_competencia',
        width: 40,        
        renderer: function(value, cell){ 
                cell.css = "readonlycell";
        	return value;		 
        },
        hidden: false
      },{
        header: 'Competencia',
        dataIndex: 'competencia',
        width:  230,
        readOnly: !permiso_modificar,
        sortable: true,
      },{
        header: 'Tipo',
        dataIndex: 'competencia',
        width:  100,
        align:'center',
        renderer: showTipo,
        readOnly: !permiso_modificar,
        sortable: true,
      },{
        header: '&Sigma; Subcomp.',
        readOnly: true,
        align:'center',
        dataIndex: 'q_subcomp',
        width: 90,        
        hidden: false
      },habilitadaCheck
      ]
    );
 competenciasColumnModel.defaultSortable= true;
 
 competenciasListingEditorGrid =  new Ext.grid.GridPanel({
    id: 'competenciasListingEditorGrid',
    store: competenciasDataStore,
    cm: competenciasColumnModel,
    enableColLock:false,
    trackMouseOver:true, //que se coloree cuando pasas el mouse por encima
    loadMask: true, //que te ponga el loading cuando se esta generando el store
    viewConfig: {
        forceFit: false
    },
    plugins:[habilitadaCheck],
    clicksToEdit:2,
    layout: 'fit',
    selModel: new Ext.grid.RowSelectionModel({singleSelect:true}),
    bbar: new Ext.PagingToolbar({
        pageSize: TAM_PAGINA,
        store: competenciasDataStore,
        displayMsg: 'Mostrando {0} - {1} de {2}',				
        displayInfo: true
    }),
    tbar: [
          {
            text: 'Nueva Competencia',
            tooltip: 'Crear una nueva competencia',
            iconCls:'add',                      // reference to our css
            handler: altaCompetencia,
            hidden: !permiso_alta
          }
      ]
    });
     
    competenciasDataStore.load({params: {start: 0, limit: TAM_PAGINA}});
   
    competenciasListingEditorGrid.on('afteredit', guardarCompetencia);
//    competenciasListingEditorGrid.on('cellclick', guardarCompetencia);
   
    competenciasPanel = new Ext.Panel(
    {
//        collapsible: false,
//        split: false,
//        header: true,
        title: 'Lista de competencias',
        region:'west',
        columnWidth:.5,
        autoScroll : true,
//        height: 400,
//        width:30,
//        minSize: 100,
//        maxSize: 350,	
//        margins: '0 5 5 5',
//        html:'<p>panel competencias</p>',
        layout: 'fit',
        items:[competenciasListingEditorGrid]
    });
   // guarda los cambios en los datos del modulo luego de la edicion
  function guardarCompetencia(oGrid_event){
   Ext.Ajax.request({   
      waitMsg: 'Por favor espere...',
      url: CARPETA+'/modificar',
      params: {
		 id_competencia: oGrid_event.record.data.id_competencia,     
		 competencia : oGrid_event.record.data.competencia
      }, 
      success: function(response){              
         var result=eval(response.responseText);
         switch(result){
         case 1:
            competenciasDataStore.commitChanges();
            competenciasDataStore.reload();
            break;
         case 2:
            Ext.MessageBox.alert('Error','No tiene permisos para realizar la operacion solicitada.');
            competenciasDataStore.reload();
            break;
         case 3:
            Ext.MessageBox.alert('Error','El campo competencia es obligatorio.');
            competenciasDataStore.reload();
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
	competenciasPanel.setHeight(altura);
	
	Ext.getCmp('browser').on('resize',function(comp){
		
		competenciasPanel.setWidth(Ext.getCmp('browser').getSize().width);
		
		competenciasPanel.setHeight(Ext.getBody().getSize().height - 60);

	});
        
 function showTipo (value,metaData,superData){
    var tipo=superData.json.tipo;
    switch (tipo)
    {
        case '1':
            metaData.attr = 'style="background-color:#01A9DB; color:#FFF;"';
            value='Cualitativa';
        break;
        case '2':
            metaData.attr = 'style="background-color:#FF8000; color:#FFF;"';
            value='Cuantitativa';
        break;
    }
    return value;
}