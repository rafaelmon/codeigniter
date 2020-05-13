consecuenciasDataStore = new Ext.data.Store({
    id: 'consecuenciasDataStore',
    proxy: new Ext.data.HttpProxy({
              url: CARPETA+'/listado', 
              method: 'POST'
          }),
    baseParams:{tampagina: TAM_PAGINA}, // this parameter is passed for any HTTP request
    reader: new Ext.data.JsonReader({
      root: 'rows',
      totalProperty: 'total',
      id: 'id_consecuencia'
    },[ 
      {name: 'id_consecuencia',    type: 'int',    mapping: 'id_consecuencia'},        
      {name: 'consecuencia',       type: 'string', mapping: 'consecuencia'},
      {name: 'descripcion',             type: 'string', mapping: 'descripcion'},
      {name: 'habilitado',              type: 'boolean', mapping: 'habilitado'}
    ]),
    sortInfo:{field: 'id_consecuencia', direction: "ASC"},
    remoteSort: true
});
paginador.bindStore(consecuenciasDataStore);
	
habilitadaCheck = new Ext.grid.CheckColumn({
        id:'habilitadaCheck',
        header: "Habilitada",
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
        tabla: 'cpp_consecuencias',
        campo_id: 'id_consecuencia'
});

consecuenciasColumnModel = new Ext.grid.ColumnModel(
[{
        header: '#',
        readOnly: true,
        dataIndex: 'id_consecuencia',
        width: 40,        
        sortable: true,
        hidden: false
    },{
        header: 'Tipo Consecuencia',
        dataIndex: 'consecuencia',
        width: 250,
        sortable: true,
    },{
        header: 'Descripción',
        dataIndex: 'descripcion',
        width: 400,
        sortable: true,
        editor: new Ext.form.TextField({
            disabled: !permiso_modificar,
            allowBlank: false,
            maxLength: 1028
        })
    },habilitadaCheck
]);
    
consecuenciasBuscador= new Ext.ux.grid.Search({
    iconCls:'icon-zoom',
//    readonlyIndexes:[''],
    disableIndexes:['id_consecuencia','habilitado'],
    align:'left',
    minChars:3
});



consecuenciasListingEditorGrid =  new Ext.grid.EditorGridPanel({
    id: 'consecuenciasListingEditorGrid',
    title: 'Consecuencias',
    store: consecuenciasDataStore,
    cm: consecuenciasColumnModel,
    enableColLock:false,
    trackMouseOver:true, 
    loadMask: true, 
    renderTo: 'grillita',
    viewConfig: {
        forceFit: false
    },
    tbar: [
        {
            text: 'Nueva Consecuencia',
            tooltip: 'Alta nueva consecuencia',
            iconCls:'add',                     
            handler: clickBtnNuevaConsecuencia
         }
    ],
    plugins:[habilitadaCheck,consecuenciasBuscador], 
    clicksToEdit:2,
    height:500,
    layout: 'fit',
    selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
    bbar: paginador
 });   
consecuenciasListingEditorGrid.on('afteredit', guardarCambiosGrillaConsecuencias);
consecuenciasDataStore.load({params: {start: 0, limit: TAM_PAGINA}});

 
var altura=Ext.getBody().getSize().height - 60;
consecuenciasListingEditorGrid.setHeight(altura);

Ext.getCmp('browser').on('resize',function(comp){
    consecuenciasListingEditorGrid.setWidth(this.getSize().width);
    consecuenciasListingEditorGrid.setHeight(Ext.getBody().getSize().height - 60);

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
function guardarCambiosGrillaConsecuencias(oGrid_event){
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
		 id_consecuencia:  oGrid_event.record.data.id_consecuencia,     
		 campos :  encoded_array_f,
		 valores : encoded_array_v
      }, 
      success: function(response){              
         var result=eval(response.responseText);
         switch(result){
         case 1:
            consecuenciasDataStore.commitChanges();
            consecuenciasDataStore.reload();
            break;
         case 2:
            Ext.MessageBox.alert('Error','No tiene permisos para realizar la operacion solicitada.');
            consecuenciasDataStore.reload();
            break;                   
         default:
            Ext.MessageBox.alert('Error','No hay conexión con la base de datos. Asegurese de tener conexion');
            consecuenciasDataStore.reload();
            break;
         }
      },
      failure: function(response){
         var result=response.responseText;
         Ext.MessageBox.alert('Uh uh...','No hay conexión con la base de datos. Intenta otra vez');    
      }                      
   });   
  }

consecuenciasListingEditorGrid.on('celldblclick', abrir_popup_consecuencias);
function abrir_popup_consecuencias(grid ,  rowIndex, columnIndex,  e){
    var data=grid.store.data.items[rowIndex].data;
//    var grid= Ext.getCmp('cppEventosGridPanel');
    var cm= grid.getColumnModel();
    var WinConsecuencias
    var enc=['<html>','<div class="tabla_popup_grilla">'];
    var pie=['<br class="popup_clear"/></div>','</html>'];
    var cppla=['<p><div class="titulo">Detetalle de la consecuencia nro '+data.id_consecuencia+'<br></div></p>'];
    
    cm.config.forEach(function(a)
    {
        if(a.header != "Acciones")
        {
            if (data[a.dataIndex]!="")
            {
                if(a.header == "Habilitado")
                {
                    data[a.dataIndex] = "Habilitado";
                }
                var nodo=['<p><div class="col1">'+a.header+':</div><div class="col2">'+data[a.dataIndex]+'</div></p>'];
            }
            else
            {
                if(a.header == "Habilitado")
                {
                    data[a.dataIndex] = "Deshabilitado";
                }
                var nodo=['<p><div class="col1">'+a.header+':</div><div class="col2">s/d</div></p>'];
            }
            cppla.push(nodo);
        }
        
    });

    var html = enc.concat(cppla);
    var html = html.concat(pie);

    WinConsecuencias = new Ext.Window({
            title: 'Detalle de la consecuencia nro '+data.id_consecuencia,
            closable: true,
            modal:true,
            //closeAction: 'hide',
            width: 650,
            boxMinWidth:650,
            height: 250,
            boxMinHeight:250,
            plain: true,
            autoScroll:true,
            layout: 'absolute',
            html: html.join(''),
//                                items: [],
            buttons: [{
                    text: 'Cerrar',
                    handler: function(){
                            WinConsecuencias.hide();
                            WinConsecuencias.destroy();

                    }
            }]
    });
    WinConsecuencias.show();

}