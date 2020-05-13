ddpOperariosDataStore = new Ext.data.Store({
    id: 'ddpOperariosDataStore',
    proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/listado', 
            method: 'POST'
        }),
    baseParams:{tampagina: TAM_PAGINA}, // this parameter is passed for any HTTP request
    reader: new Ext.data.JsonReader({
    root: 'rows',
    totalProperty: 'total',
    id: 'id_operario'
    },[ 
    {name: 'id_operario',   type: 'int',        mapping: 'id_operario'},        
    {name: 'nombre',        type: 'string',     mapping: 'nombre'},
    {name: 'apellido',      type: 'string',     mapping: 'apellido'},
    {name: 'supervisor',  type: 'string',     mapping: 'supervisor'},
    {name: 'empresa',       type: 'string',     mapping: 'empresa'},
    {name: 'legajo',        type: 'string',     mapping: 'legajo'},
    {name: 'habilitado',    type: 'int',        mapping: 'habilitado'}
    ]),
    sortInfo:{field: 'operario', direction: "ASC"},    
    remoteSort: true
});
//asigno el datastore al paginador
paginador.bindStore(ddpOperariosDataStore);

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
//        tooltip:'Editar Operario'
////        handler: clickBtnModificaOperario
//    }]
//});

ddpOperarioshabilitadaCheck = new Ext.grid.CheckColumn({
    id:'habilitado',
    header: "Habilitado",
    dataIndex: 'habilitado',
    width: 60,
    sortable: true,
    menuDisabled:true,
    pintar_deshabilitado:true,
        disabled: false, //-->NO FUNCIONA
    tabla: 'ddp_operarios',
    align:'center',
    campo_id: 'id_operario'
});

ddpOperariosColumnModel = new Ext.grid.ColumnModel(
    [{
        header: '#',
        readOnly: true,
        dataIndex: 'id_operario',
        width: 40,        
        sortable: true,
        renderer: function(value, cell){
            cell.css = "readonlycell";
            return value;
            },
        hidden: false
        },{
        header: 'Apellido',
        dataIndex: 'apellido',
        width: 250,
        sortable: true,
        renderer: showQtipOperario
        },{
        header: 'Nombre',
        dataIndex: 'nombre',
        width: 250,
        sortable: true,
        renderer: showQtipOperario
        },{
        header: 'Empresa',
        dataIndex: 'empresa',
        width: 80,
        sortable: true,
        renderer: showQtipOperario
        },{
        header: 'Legajo',
        dataIndex: 'legajo',
        width: 80,
        sortable: true,
        renderer: showQtipOperario
        },{
        header: 'Supervisor',
        dataIndex: 'supervisor',
        width: 250,
        sortable: true,
        renderer: showQtipSupervisores
        },ddpOperarioshabilitadaCheck
    ]
);
ddpbuscadorOperario= new Ext.ux.grid.Search({
    iconCls:'icon-zoom',
    //    readonlyIndexes:['id_convocatoria'],
    disableIndexes:['id_operario','habilitado'],
    align:'left',
    minChars:3
});
  
   ddpOperariosGrid =  new Ext.grid.GridPanel({
        id: 'ddpOperariosGrid',
        title: 'Listado de Operarios',
        store: ddpOperariosDataStore,
        cm: ddpOperariosColumnModel,
        enableColLock:false,
        trackMouseOver:true, //que se coloree cuando pasas el mouse por encima
        loadMask: true, //que te ponga el loading cuando se esta generando el store
        renderTo: 'grillita',
        viewConfig: {
            forceFit: false
        },
        plugins:[ddpbuscadorOperario,ddpOperarioshabilitadaCheck],
        clicksToEdit:3,
        height:500,
        layout: 'fit',
        selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
        bbar: paginador,
        tbar: [
          {
            text: 'Nuevo Operario',
            tooltip: 'Ingresar nueva operario...',
            iconCls:'add',                      // reference to our css
            handler: dFW_ddpNuevaOperario, 
            hidden: !permiso_alta
          }
      ]
    }); 
//    function dFW_ddpNuevaOperario (){};

  ddpOperariosDataStore.load({params: {start: 0, limit: TAM_PAGINA}});

//  OperariosListingEditorGrid.on('afteredit', guardarOperario);
  
  
   // guarda los cambios en los datos del operario luego de la edicion
//  function guardarOperario(oGrid_event)
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
//		 id         : oGrid_event.record.data.id_operario, 
//                 id_gerencia:oGrid_event.record.data.id_gerencia,
//		 campo      : encoded_array_f,
//		 valor      : encoded_array_v
//      }, 
//      success: function(response){              
//         var result=eval(response.responseText);
//         switch(result){
//         case 1:
//            OperariosDataStore.commitChanges();
//            OperariosDataStore.reload();
//            break;
//         case -1:
//            Ext.MessageBox.alert('Error','Operario existente...');
//            OperariosDataStore.reload();
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
	ddpOperariosGrid.setHeight(altura);
	
	Ext.getCmp('browser').on('resize',function(comp){
		ddpOperariosGrid.setWidth(this.getSize().width);
		ddpOperariosGrid.setHeight(Ext.getBody().getSize().height - 60);

	});
function showQtipOperario(value, metaData,record){
    var deviceDetail = record.get('operario');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
    return value;
}
function showQtipSupervisores(value, metaData,record){
    var deviceDetail = record.get('supervisores');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
    return value;
}