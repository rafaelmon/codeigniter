ddpObjetivosDataStore = new Ext.data.Store({
    id: 'ddpObjetivosDataStore',
    proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/listado', 
            method: 'POST'
        }),
    baseParams:{tampagina: TAM_PAGINA}, // this parameter is passed for any HTTP request
    reader: new Ext.data.JsonReader({
    root: 'rows',
    totalProperty: 'total',
    id: 'id'
    },[ 
    {name: 'id_oe',         type: 'int',        mapping: 'id_oe'},        
    {name: 'oe',            type: 'string',     mapping: 'oe'},
    {name: 'empresas',       type: 'string',     mapping: 'empresas'},
    {name: 'id_dimension',  type: 'int',        mapping: 'id_dimension'},
    {name: 'dimension',     type: 'string',     mapping: 'dimension'},
    {name: 'periodo',       type: 'string',     mapping: 'periodo'},
    {name: 'detalle',       type: 'string',     mapping: 'detalle'},
    {name: 'usuario_alta',  type: 'string',     mapping: 'usuario_alta'},
    {name: 'habilitado',    type: 'string',     mapping: 'habilitado'}
    ]),
    sortInfo:{field: 'oe', direction: "ASC"},
    remoteSort: true
});
//asigno el datastore al paginador
paginador.bindStore(ddpObjetivosDataStore);

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
//        tooltip:'Editar Objetivo'
////        handler: clickBtnModificaObjetivo
//    }]
//});

ddpObjetivoshabilitadaCheck = new Ext.grid.CheckColumn({
    id:'habilitado',
    header: "Habilitado",
    dataIndex: 'habilitado',
    width: 60,
    sortable: true,
    menuDisabled:true,
    pintar_deshabilitado:true,
        disabled: false, //-->NO FUNCIONA
    tabla: 'ddp_objetivos_empresa',
    align:'center',
    campo_id: 'id_oe'
});

ddpObjetivosColumnModel = new Ext.grid.ColumnModel(
    [{
        header: '#',
        readOnly: true,
        dataIndex: 'id_oe',
        width: 40,        
        sortable: true,
        renderer: function(value, cell){
            cell.css = "readonlycell";
            return value;
            },
        hidden: false
        },{
        header: 'Periodo',
        dataIndex: 'periodo',
        width: 70,
        sortable: true,
        renderer: function(value, cell){ 
            cell.css = "coolcell";
            return value;
            }
        },{
        header: 'Dimension',
        dataIndex: 'dimension',
        width: 70,
        sortable: true,
        renderer: showDimension
        },{
        header: 'Objetivo empresa',
        dataIndex: 'oe',
        width: 500,
        sortable: true,
        renderer: showQtipObj
        },{
        header: 'Empresas',
        dataIndex: 'empresas',
        width: 120,
        sortable: false
        },{
        header: 'Usuario Alta',
        dataIndex: 'usuario_alta',
        width: 140,
        sortable: false
        },ddpObjetivoshabilitadaCheck
    ]
);
ddpbuscadorObjetivo= new Ext.ux.grid.Search({
    iconCls:'icon-zoom',
    //    readonlyIndexes:['id_convocatoria'],
    disableIndexes:['id_oe','habilitado'],
    align:'left',
    minChars:3
});
  
   ddpObjetivosGrid =  new Ext.grid.GridPanel({
        id: 'ddpObjetivosGrid',
        title: 'Listado de Objetivos de empresa',
        store: ddpObjetivosDataStore,
        cm: ddpObjetivosColumnModel,
        enableColLock:false,
        trackMouseOver:true, //que se coloree cuando pasas el mouse por encima
        loadMask: true, //que te ponga el loading cuando se esta generando el store
        renderTo: 'grillita',
        viewConfig: {
            forceFit: false
        },
        plugins:[ddpbuscadorObjetivo,ddpObjetivoshabilitadaCheck],
        clicksToEdit:3,
        height:500,
        layout: 'fit',
        selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
        bbar: paginador,
        tbar: [
          {
            text: 'Nuevo Objetivo',
            tooltip: 'Crear un nuevo objetivo...',
            iconCls:'add',                      // reference to our css
            handler: dFW_ddpNuevoObetivo,
            hidden: !permiso_alta
          }
      ]
    }); 
//    function dFW_ddpNuevoObetivo (){};

  ddpObjetivosDataStore.load({params: {start: 0, limit: TAM_PAGINA}});

//  ObjetivosListingEditorGrid.on('afteredit', guardarObjetivo);
  
  
   // guarda los cambios en los datos del objetivo luego de la edicion
//  function guardarObjetivo(oGrid_event)
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
//		 id         : oGrid_event.record.data.id_objetivo, 
//                 id_gerencia:oGrid_event.record.data.id_gerencia,
//		 campo      : encoded_array_f,
//		 valor      : encoded_array_v
//      }, 
//      success: function(response){              
//         var result=eval(response.responseText);
//         switch(result){
//         case 1:
//            ObjetivosDataStore.commitChanges();
//            ObjetivosDataStore.reload();
//            break;
//         case -1:
//            Ext.MessageBox.alert('Error','Objetivo existente...');
//            ObjetivosDataStore.reload();
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
	ddpObjetivosGrid.setHeight(altura);
	
	Ext.getCmp('browser').on('resize',function(comp){
		ddpObjetivosGrid.setWidth(this.getSize().width);
		ddpObjetivosGrid.setHeight(Ext.getBody().getSize().height - 60);

	});
        
function showDimension (value,metaData,superData){
    var dim=superData.json.id_dimension;
    var css_bc=superData.json.css_bc;
    metaData.attr = 'style="background-color:#'+css_bc+'; color:#FFF;"';
//    switch (dim)
//    {
//        case '1':
//        break;
//        case '2':
//        metaData.attr = 'style="background-color:#088A08; color:#FFF;"';
//        break;
//        case '3':
//        metaData.attr = 'style="background-color:#DF7401; color:#FFF;"';
//        break;
//        case '4':
//        metaData.attr = 'style="background-color:#037DA2; color:#FFF;"';
//        break;
//        case '5':
//        metaData.attr = 'style="background-color:#FF0000; color:#FFF;"';
//        break;
//        case '6':
//        metaData.attr = 'style="background-color:#A4A4A4; color:#FFF;"';
//        break;
//        case '7':
//        metaData.attr = 'style="background-color:#151515; color:#FFF;"';
//        break;
//        
//    }
//    var deviceDetail = superData.get('obs');
//    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
        
    return value;
}
function showQtipObj(value, metaData,record){
    var deviceDetail = record.get('oe');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
    return value;
}