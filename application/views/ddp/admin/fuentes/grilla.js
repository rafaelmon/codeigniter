ddpFuentesDataStore = new Ext.data.Store({
    id: 'ddpFuentesDataStore',
    proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/listado', 
            method: 'POST'
        }),
    baseParams:{tampagina: TAM_PAGINA}, // this parameter is passed for any HTTP request
    reader: new Ext.data.JsonReader({
    root: 'rows',
    totalProperty: 'total',
    id: 'id_fd'
    },[ 
    {name: 'id_fd',         type: 'int',        mapping: 'id_fd'},        
    {name: 'fd',            type: 'string',     mapping: 'fd'},
    {name: 'detalle',       type: 'string',     mapping: 'detalle'},
    {name: 'dimensiones',   type: 'string',     mapping: 'dimensiones'},
    {name: 'habilitado',    type: 'int',        mapping: 'habilitado'}
    ]),
    sortInfo:{field: 'fd', direction: "ASC"},    
    remoteSort: true
});
//asigno el datastore al paginador
paginador.bindStore(ddpFuentesDataStore);

//botonesAction = new Ext.grid.ActionColumn({
//    width: 15,
//    editable:false,
//    menuDisabled:true,
//    header:'Acción',
//    hideable:false,
//    align:'center',
//    width:  50,
//    tooltip:'Editar',
//        hidden:false,
//    items:[{
//        icon:URL_BASE+'images/tooloptions.png',
//        iconCls :'col_accion',
//        tooltip:'Editar Fuente'
////        handler: clickBtnModificaFuente
//    }]
//});

ddpFuenteshabilitadaCheck = new Ext.grid.CheckColumn({
    id:'habilitado',
    header: "Habilitado",
    dataIndex: 'habilitado',
    width: 60,
    sortable: true,
    menuDisabled:true,
    pintar_deshabilitado:true,
        disabled: false, //-->NO FUNCIONA
    tabla: 'ddp_fuentes',
    align:'center',
    campo_id: 'id_fd'
});

ddpFuentesColumnModel = new Ext.grid.ColumnModel(
    [{
        header: '#',
        readOnly: true,
        dataIndex: 'id_fd',
        width: 40,        
        sortable: true,
        renderer: function(value, cell){
            cell.css = "readonlycell";
            return value;
            },
        hidden: false
        },{
        header: 'Fuente',
        dataIndex: 'fd',
        width: 500,
        sortable: true,
        renderer: showQtipFuente
        },{
        header: 'Detalle',
        dataIndex: 'detalle',
        width: 200,
        sortable: false,
        renderer: showQtipDetalle
        },{
        header: 'Dimensiones',
        dataIndex: 'dimensiones',
        width: 130,
        sortable: true
        },ddpFuenteshabilitadaCheck
    ]
);
ddpbuscadorFuente= new Ext.ux.grid.Search({
    iconCls:'icon-zoom',
    //    readonlyIndexes:['id_convocatoria'],
    disableIndexes:['id_fd','habilitado'],
    align:'left',
    minChars:3
});
  
   ddpFuentesGrid =  new Ext.grid.GridPanel({
        id: 'ddpFuentesGrid',
        title: 'Listado de Fuentes de Datos',
        store: ddpFuentesDataStore,
        cm: ddpFuentesColumnModel,
        enableColLock:false,
        trackMouseOver:true, //que se coloree cuando pasas el mouse por encima
        loadMask: true, //que te ponga el loading cuando se esta generando el store
        renderTo: 'grillita',
        viewConfig: {
            forceFit: false
        },
        plugins:[ddpbuscadorFuente,ddpFuenteshabilitadaCheck],
        clicksToEdit:3,
        height:500,
        layout: 'fit',
        selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
        bbar: paginador,
        tbar: [
          {
            text: 'Nueva Fuente de Datos',
            tooltip: 'Crear nueva fuente de datos...',
            iconCls:'add',                      // reference to our css
            handler: dFW_ddpNuevaFuente, 
            hidden: !permiso_alta
          }
      ]
    }); 
//    function dFW_ddpNuevaFuente (){};

  ddpFuentesDataStore.load({params: {start: 0, limit: TAM_PAGINA}});

//  FuentesListingEditorGrid.on('afteredit', guardarFuente);
  
  
   // guarda los cambios en los datos del fuente luego de la edicion
//  function guardarFuente(oGrid_event)
//  {
//      console.log(oGrid_event);
//  	 //console.log(oGrid_event);
//        var fields = [];
//        fields.push(oGrid_event.field);
//	var values = [];
//       	values.push(oGrid_event.value);
// 
//	var encoded_array_f = Ext.encode(fields);
//	var encoded_array_v = Ext.encode(values);
//   Ext.Ajax.request({   
//      waitMsg: 'Por favor espere...',
//      url: CARPETA+'/modificar',
//      params: {
//		 id         : oGrid_event.record.data.id_fuente, 
//                 id_gerencia:oGrid_event.record.data.id_gerencia,
//		 campo      : encoded_array_f,
//		 valor      : encoded_array_v
//      }, 
//      success: function(response){              
//         var result=eval(response.responseText);
//         switch(result){
//         case 1:
//            FuentesDataStore.commitChanges();
//            FuentesDataStore.reload();
//            break;
//         case -1:
//            Ext.MessageBox.alert('Error','Fuente existente...');
//            FuentesDataStore.reload();
//            break;  
//         
//            break;          
//         default:
//            Ext.MessageBox.alert('Uh uh...','No se pudo actualizar...');
//            break;
//         }
//      },
//      failure: function(response){
//         var result=response.responseText;
//         Ext.MessageBox.alert('error','No se pudo conectar a la Base de Datos. Intente mas tarde');    
//      }                      
//   });  
//  }
  
 
 
   
  	var altura=Ext.getBody().getSize().height - 60;
	ddpFuentesGrid.setHeight(altura);
	
	Ext.getCmp('browser').on('resize',function(comp){
		ddpFuentesGrid.setWidth(this.getSize().width);
		ddpFuentesGrid.setHeight(Ext.getBody().getSize().height - 60);

	});
        
function showDimension (value,metaData,superData){
    var dim=superData.json.id_dimension;
    var css_bc=superData.json.css_bc;
    metaData.attr = 'style="background-color:#'+css_bc+'; color:#FFF;"';
    return value;
}
function showQtipFuente(value, metaData,record){
    var deviceDetail = record.get('fd');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
    return value;
}
function showQtipDetalle(value, metaData,record){
    var deviceDetail = record.get('detalle');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
    return value;
}