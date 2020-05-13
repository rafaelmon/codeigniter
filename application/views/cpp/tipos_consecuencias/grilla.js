tiposConsecuenciasDataStore = new Ext.data.Store({
    id: 'tiposConsecuenciasDataStore',
    proxy: new Ext.data.HttpProxy({
              url: CARPETA+'/listado', 
              method: 'POST'
          }),
    baseParams:{tampagina: TAM_PAGINA}, // this parameter is passed for any HTTP request
    reader: new Ext.data.JsonReader({
      root: 'rows',
      totalProperty: 'total',
      id: 'id_tipo_consecuencia'
    },[ 
      {name: 'id_tipo_consecuencia',    type: 'int',    mapping: 'id_tipo_consecuencia'},        
      {name: 'tipo_consecuencia',       type: 'string', mapping: 'tipo_consecuencia'},
      {name: 'descripcion',             type: 'string', mapping: 'descripcion'},
      {name: 'habilitado',              type: 'boolean', mapping: 'habilitado'}
    ]),
    sortInfo:{field: 'id_tipo_consecuencia', direction: "ASC"},
    remoteSort: true
});
paginador.bindStore(tiposConsecuenciasDataStore);
	
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
        tabla: 'cpp_tipos_consecuencias',
        campo_id: 'id_tipo_consecuencia'
});

tiposConsecuenciasColumnModel = new Ext.grid.ColumnModel(
[{
        header: '#',
        readOnly: true,
        dataIndex: 'id_tipo_consecuencia',
        width: 40,        
        sortable: true,
        hidden: false
    },{
        header: 'Tipo Consecuencia',
        dataIndex: 'tipo_consecuencia',
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
    
tiposConsecuenciasBuscador= new Ext.ux.grid.Search({
    iconCls:'icon-zoom',
//    readonlyIndexes:[''],
    disableIndexes:['id_tipo_consecuencia','habilitado'],
    align:'left',
    minChars:3
});



tiposConsecuenciasListingEditorGrid =  new Ext.grid.EditorGridPanel({
    id: 'tiposConsecuenciasListingEditorGrid',
    title: 'Tipos consecuencias',
    store: tiposConsecuenciasDataStore,
    cm: tiposConsecuenciasColumnModel,
    enableColLock:false,
    trackMouseOver:true, 
    loadMask: true, 
    renderTo: 'grillita',
    viewConfig: {
        forceFit: false
    },
    tbar: [
        {
            text: 'Nuevo Tipo Consecuencia',
            tooltip: 'Alta nuevo tipo consecuencia',
            iconCls:'add',                     
            handler: clickBtnNuevoTipoConsecuencia
         }
    ],
    plugins:[habilitadaCheck,tiposConsecuenciasBuscador], 
    clicksToEdit:2,
    height:500,
    layout: 'fit',
    selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
    bbar: paginador
 });   
tiposConsecuenciasListingEditorGrid.on('afteredit', guardarCambiosGrillaTiposConsecuencias);
tiposConsecuenciasDataStore.load({params: {start: 0, limit: TAM_PAGINA}});

 
var altura=Ext.getBody().getSize().height - 60;
tiposConsecuenciasListingEditorGrid.setHeight(altura);

Ext.getCmp('browser').on('resize',function(comp){
    tiposConsecuenciasListingEditorGrid.setWidth(this.getSize().width);
    tiposConsecuenciasListingEditorGrid.setHeight(Ext.getBody().getSize().height - 60);

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
function guardarCambiosGrillaTiposConsecuencias(oGrid_event){
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
		 id_tipo_consecuencia:  oGrid_event.record.data.id_tipo_consecuencia,     
		 campos :  encoded_array_f,
		 valores : encoded_array_v
      }, 
      success: function(response){              
         var result=eval(response.responseText);
         switch(result){
         case 1:
            tiposConsecuenciasDataStore.commitChanges();
            tiposConsecuenciasDataStore.reload();
            break;
         case 2:
            Ext.MessageBox.alert('Error','No tiene permisos para realizar la operacion solicitada.');
            tiposConsecuenciasDataStore.reload();
            break;                   
         default:
            Ext.MessageBox.alert('Error','No hay conexión con la base de datos. Asegurese de tener conexion');
            tiposConsecuenciasDataStore.reload();
            break;
         }
      },
      failure: function(response){
         var result=response.responseText;
         Ext.MessageBox.alert('Uh uh...','No hay conexión con la base de datos. Intenta otra vez');    
      }                      
   });   
  }
  
tiposConsecuenciasListingEditorGrid.on('celldblclick', abrir_popup_tiposConsecuencias);
function abrir_popup_tiposConsecuencias(grid ,  rowIndex, columnIndex,  e){
    var data=grid.store.data.items[rowIndex].data;
//    var grid= Ext.getCmp('cppEventosGridPanel');
    var cm= grid.getColumnModel();
    var WinTiposConsecuencias
    var enc=['<html>','<div class="tabla_popup_grilla">'];
    var pie=['<br class="popup_clear"/></div>','</html>'];
    var cppla=['<p><div class="titulo">Detetalle tipo consecuencia nro '+data.id_causa+'<br></div></p>'];
    
    cm.config.forEach(function(a)
    {
        if(a.header != "Acciones")
        {
            if (data[a.dataIndex]!="")
            {
                var nodo=['<p><div class="col1">'+a.header+':</div><div class="col2">'+data[a.dataIndex]+'</div></p>'];
            }
            else
            {
                var nodo=['<p><div class="col1">'+a.header+':</div><div class="col2">s/d</div></p>'];
            }
            cppla.push(nodo);
        }
        
    });

    var html = enc.concat(cppla);
    var html = html.concat(pie);

    WinTiposConsecuencias = new Ext.Window({
            title: 'Detalle tipo consecuencia nro '+data.id_causa,
            closable: true,
            modal:true,
            //closeAction: 'hide',
            width: 550,
            boxMinWidth:550,
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
                            WinTiposConsecuencias.hide();
                            WinTiposConsecuencias.destroy();

                    }
            }]
    });
    WinTiposConsecuencias.show();

}