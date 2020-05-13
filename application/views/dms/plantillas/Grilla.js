PlantillasDataStore = new Ext.data.Store({
    id: 'PlantillasDataStore',
    proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/listado', 
            method: 'POST'
    }),
      baseParams:{tampagina: TAM_PAGINA}, // this parameter is passed for any HTTP request
      reader: new Ext.data.JsonReader({
        root: 'rows',
        totalProperty: 'total',
        id: 'id_plantilla'
      },[ 
        {name: 'id_plantilla',  type: 'int',    mapping: 'id_plantilla'},        
        {name: 'plantilla',     type: 'string', mapping: 'plantilla'},
        {name: 'detalle',   type: 'string', mapping: 'detalle'},
        {name: 'td',            type: 'string', mapping: 'td'},
        {name: 'archivo_orig',       type: 'string', mapping: 'archivo_orig'},
        {name: 'habilitado',    type: 'bool',   mapping: 'habilitado'}
      ]),
      sortInfo:{field: 'id_plantilla', direction: "ASC"},
      remoteSort: true
    });
     paginador.bindStore(PlantillasDataStore);
    
habilitadaCheck = new Ext.grid.CheckColumn({
        id:'habilitado',
        header: "Habilitado",
        dataIndex: 'habilitado',
        width: 15,
        hidden: !permiso_modificar,
//        menuDisabled:true,
        sortable: true,
        pintar_deshabilitado:true,
        tabla: 'dms_plantillas',
        align:'center',
        campo_id: 'id_plantilla'
    });

buscador= new Ext.ux.grid.Search({
    iconCls:'icon-zoom',
//    readonlyIndexes:['id_plantilla', 'plantilla','descripcion'],
    disableIndexes:['id_plantilla','descripcion','fecha_alta','archivo','habilitado'],
    align:'right',
    minChars:3
});

  
PlantillasColumnModel = new Ext.grid.ColumnModel(
    [{
        header: '#',
        readOnly: true,
        dataIndex: 'id_plantilla',
        width: 10,        
        sortable: true,
        renderer: function(value, cell){
            cell.css = "readonlycell";
            return value;
        },
        hidden: false
      },{
        header: 'Plantilla',
        dataIndex: 'plantilla',
        width:  60,
        sortable: true,
        readOnly: permiso_modificar
      },{
        header: 'Tipo de Documento',
        dataIndex: 'td',
        width:  60,
        sortable: true,
        readOnly: permiso_modificar
      },{
          name:'detalle',
        header: 'Descripción - Detalle',
        dataIndex: 'detalle',
        sortable: true,
        width:  200,
        readOnly: permiso_modificar,
        renderer :showQtip,
        editor: new Ext.form.TextArea({
            disabled: !permiso_modificar,
            allowBlank: true,
            maxLength: 2000
        })/*,
        renderer: function(value, cell){ 
         cell.css = "coolcell";
         return value;
        }*/
      },{
        header: 'Descargar',
        dataIndex: 'archivo_orig',
        sortable: true,
        width:  20,
        readOnly: permiso_modificar,
        renderer: showDocument,
        align:'center'
      },habilitadaCheck]
    );
  
   PlantillasListingEditorGrid =  new Ext.grid.EditorGridPanel({
        id: 'PermisosListingEditorGrid',
        title: 'Plantillas',
        store: PlantillasDataStore,
        cm: PlantillasColumnModel,
        enableColLock:false,
        trackMouseOver:true, //que se coloree cuando pasas el mouse por encima
        loadMask: true, //que te ponga el loading cuando se esta generando el store
        renderTo: 'grillita',
        viewConfig: {
            forceFit: true
        },
        plugins:[habilitadaCheck,buscador],
        clicksToEdit:2,
        height:500,
        layout: 'fit',
        selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
        bbar:[paginador]/*,
        tbar: [
          {
            text: 'Nuevo plantilla',
            tooltip: 'Crear una nueva plantilla...',
            iconCls:'add',                      // reference to our css
            handler: displayFormWindow,
            hidden: !permiso_alta
          }, '-', { 
            text: 'Eliminar',
            tooltip: 'Eliminar la plantilla seleccionada',
            handler: confirmDeletePlantillas,   // Confirm before deleting
            iconCls:'remove',
            hidden: !permiso_eliminar
          }
      ]*/
    });   

PlantillasDataStore.load({params: {start: 0, limit: TAM_PAGINA}});

PlantillasListingEditorGrid.on('afteredit', guardarPlantilla);
  
  
// guarda los cambios en los datos del Plantilla luego de la edicion
function guardarPlantilla(oGrid_event)
{
//    console.log(oGrid_event);
    var fields = [];
    fields.push(oGrid_event.field);
    var values = [];
    values.push(oGrid_event.value);
    var encoded_array_f = Ext.encode(fields);
    var encoded_array_v = Ext.encode(values);
    Ext.Ajax.request({   
        waitMsg: 'Por favor espere...',
        url: CARPETA+'/modificar',
        params: {
            id: oGrid_event.record.data.id_plantilla,     
            campos : encoded_array_f,
            valores : encoded_array_v
        }, 
        success: function(response){              
            var result=eval(response.responseText);
            switch(result){
                case 1:
                    PlantillasDataStore.commitChanges();
                    PlantillasDataStore.reload();
                    break;
                default:
                    Ext.MessageBox.alert('Uh uh...','No se pudo actualizar...');
                    break;
            }
        },
        failure: function(response){
            var result=response.responseText;
            Ext.MessageBox.alert('error','No se pudo conectar a la Base de Datos. Intente mas tarde');    
        }                      
    });  
}
  
  
    // This was added in Tutorial 6
//  function confirmDeletePlantillas(){
//    if(PlantillasListingEditorGrid.selModel.getCount() == 1) // only one president is selected here
//    {
//      Ext.MessageBox.confirm('Confirmation','Est&aacute; por borrar un Plantilla. Desea continuar?', deletePlantillas);
//    } else if(PlantillasListingEditorGrid.selModel.getCount() > 1){
//      Ext.MessageBox.confirm('Confirmation','Desea borrar estos Plantillas?', deletePlantillas);
//    } else {
//      Ext.MessageBox.alert('Uh oh...','Para borrar un Plantilla debe seleccionar alguno del listado');
//    }
//  }  
   // This was added in Tutorial 6
//  function deletePlantillas(btn){
//    if(btn=='yes'){
//         var selections = PlantillasListingEditorGrid.selModel.getSelections();
//         var prez = [];
//         for(i = 0; i< PlantillasListingEditorGrid.selModel.getCount(); i++){
//          prez.push(selections[i].json.id);
//		 
//         }
//         var encoded_array = Ext.encode(prez);
//		  //alert(encoded_array);
//         Ext.Ajax.request({  
//            waitMsg: 'Por favor espere',
//            url: CARPETA+'/eliminar', 
//            params: { 
//               tarea: "borrar", 
//			   tabla: "Plantilla",
//			   campo_id: "id",
//               ids:  encoded_array
//              }, 
//            success: function(response){
//              var result=eval(response.responseText);
//              switch(result){
//              case 1:  // Success : simply reload
//                PlantillasDataStore.reload();
//                break;
//              default:
//                Ext.MessageBox.alert('Warning','No se pudo eliminar el registro seleccionado.');
//                break;
//              }
//            },
//            failure: function(response){
//              var result=response.responseText;
//              Ext.MessageBox.alert('error','No se pudo conectar con la base de datos. Intente mas tarde');      
//              }
//         });
//      }  
//  }
   
  	var altura=Ext.getBody().getSize().height - 60;
	PlantillasListingEditorGrid.setHeight(altura);
	
	Ext.getCmp('browser').on('resize',function(comp){
		PlantillasListingEditorGrid.setWidth(this.getSize().width);
		PlantillasListingEditorGrid.setHeight(Ext.getBody().getSize().height - 60);

	});
        
//Agregados FM
function showDocument (value,metaData,row){
//    console.log(value);
    var enlace;
    var id=row.data.id_plantilla;
    if (value!="" && row.data.habilitado==1)
//        enlace = "<a target='_blank' type='application/msword' href='"+URL_BASE_SITIO+"uploads/dms/plantillas/"+value+"'><img ext:qtip='Descargar' class='x-action-col-icon x-action-col-0  ' src='"+URL_BASE+"/images/document_word.png' alt=''></a>";
        enlace = "<a target='_blank' href='"+URL_DMS_PLANTILLAS+id+"/"+value+"'><img ext:qtip='Descargar' class='x-action-col-icon x-action-col-0  ' src='"+URL_BASE+"/images/document_word.png' alt=''></a>";
    else
        enlace = "";
    return enlace;
    }
function showQtip(value, metaData,record){
    var deviceDetail = record.get('descripcion');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
    return value;
}