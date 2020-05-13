aaccDataStore = new Ext.data.Store({
    id: 'aaccDataStore',
    proxy: new Ext.data.HttpProxy({
              url: CARPETA+'/listado', 
              method: 'POST'
          }),
    baseParams:{tampagina: TAM_PAGINA}, // this parameter is passed for any HTTP request
    reader: new Ext.data.JsonReader({
      root: 'rows',
      totalProperty: 'total',
      id: 'id_ac'
    },[ 
      {name: 'id_ac',    type: 'int',     mapping: 'id_ac'},        
      {name: 'ac',       type: 'string',  mapping: 'ac'},
      {name: 'descripcion',  type: 'string',  mapping: 'descripcion'},
      {name: 'habilitado',   type: 'boolean', mapping: 'habilitado'}
    ]),
    sortInfo:{field: 'id_ac', direction: "ASC"},
    remoteSort: true
});
paginador.bindStore(aaccDataStore);
	
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
        tabla: 'cpp_areas_causante',
        campo_id: 'id_ac'
});

aaccColumnModel = new Ext.grid.ColumnModel(
[{
        header: '#',
        readOnly: true,
        dataIndex: 'id_ac',
        width: 40,        
        sortable: true,
        hidden: false
    },{
        header: 'Area Causante',
        dataIndex: 'ac',
        width: 450,
        sortable: true,
        editor: new Ext.form.TextArea({
            disabled: !permiso_modificar,
            allowBlank: false,
            maxLength: 1028
        })
    },{
        header: 'Descripción',
        dataIndex: 'descripcion',
        width: 650,
        sortable: true,
        editor: new Ext.form.TextArea({
            disabled: !permiso_modificar,
            allowBlank: false,
            maxLength: 1028
        })
    },habilitadoCheck
]);
    
aaccBuscador= new Ext.ux.grid.Search({
    iconCls:'icon-zoom',
//    readonlyIndexes:[''],
    disableIndexes:['id_ac','habilitado'],
    align:'left',
    minChars:3
});



aaccListingEditorGrid =  new Ext.grid.EditorGridPanel({
    id: 'aaccListingEditorGrid',
    title: 'Ñistado de Areas Causantes',
    store: aaccDataStore,
    cm: aaccColumnModel,
    enableColLock:false,
    trackMouseOver:true, 
    loadMask: true, 
    renderTo: 'grillita',
    viewConfig: {
        forceFit: false
    },
    tbar: [
        {
            text: 'Nuevo Area Causante',
            tooltip: 'Alta nueva Area Causante',
            iconCls:'add',                     
            handler: clickBtnNuevaAC
         }
    ],
    plugins:[habilitadoCheck,aaccBuscador], 
    clicksToEdit:2,
    height:500,
    layout: 'fit',
    selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
    bbar: paginador
 });   
aaccListingEditorGrid.on('afteredit', guardarCambiosGrillaAreaCausante);
aaccDataStore.load({params: {start: 0, limit: TAM_PAGINA}});

 
var altura=Ext.getBody().getSize().height - 60;
aaccListingEditorGrid.setHeight(altura);

Ext.getCmp('browser').on('resize',function(comp){
    aaccListingEditorGrid.setWidth(this.getSize().width);
    aaccListingEditorGrid.setHeight(Ext.getBody().getSize().height - 60);

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
function guardarCambiosGrillaAreaCausante(oGrid_event){
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
		 id_ac:  oGrid_event.record.data.id_ac,     
		 campos :  encoded_array_f,
		 valores : encoded_array_v
      }, 
      success: function(response){              
         var result=eval(response.responseText);
         switch(result){
         case 1:
            aaccDataStore.commitChanges();
            aaccDataStore.reload();
            break;
         case 2:
            Ext.MessageBox.alert('Error','No tiene permisos para realizar la operacion solicitada.');
            aaccDataStore.reload();
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

aaccListingEditorGrid.on('celldblclick', abrir_popup_aacc);
function abrir_popup_aacc(grid ,  rowIndex, columnIndex,  e){
    var data=grid.store.data.items[rowIndex].data;
//    var grid= Ext.getCmp('cppEventosGridPanel');
    var cm= grid.getColumnModel();
    var WinAacc;
    var enc=['<html>','<div class="tabla_popup_grilla">'];
    var pie=['<br class="popup_clear"/></div>','</html>'];
    var cppla=['<p><div class="titulo">Detetalle del àrea nro '+data.id_ac+'<br></div></p>'];
    
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

    WinAacc = new Ext.Window({
            title: 'Detalle del àrea nro '+data.id_ac,
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
                            WinAacc.hide();
                            WinAacc.destroy();

                    }
            }]
    });
    WinAacc.show();
}
  