equiposDataStore = new Ext.data.Store({
    id: 'equiposDataStore',
    proxy: new Ext.data.HttpProxy({
              url: CARPETA+'/listado', 
              method: 'POST'
          }),
    baseParams:{tampagina: TAM_PAGINA}, // this parameter is passed for any HTTP request
    reader: new Ext.data.JsonReader({
      root: 'rows',
      totalProperty: 'total',
      id: 'id_equipo'
    },[ 
      {name: 'id_equipo',    type: 'int',     mapping: 'id_equipo'},        
      {name: 'equipo',       type: 'string',  mapping: 'equipo'},
      {name: 'descripcion',  type: 'string',  mapping: 'descripcion'},
      {name: 'tag',          type: 'string',  mapping: 'tag'},
      {name: 'habilitado',   type: 'boolean', mapping: 'habilitado'}
    ]),
    sortInfo:{field: 'id_equipo', direction: "ASC"},
    remoteSort: true
});
paginador.bindStore(equiposDataStore);
	
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
        tabla: 'cpp_equipos',
        campo_id: 'id_equipo'
});

equiposColumnModel = new Ext.grid.ColumnModel(
[{
        header: '#',
        readOnly: true,
        dataIndex: 'id_equipo',
        width: 40,        
        sortable: true,
        hidden: false
    },{
        header: 'Tag',
        dataIndex: 'tag',
        width: 120,
        sortable: true,
        editor: new Ext.form.TextField({
            disabled: !permiso_modificar,
            allowBlank: false,
            maxLength: 1028
        })
    },{
        header: 'Equipo',
        dataIndex: 'equipo',
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
    
equiposBuscador= new Ext.ux.grid.Search({
    iconCls:'icon-zoom',
//    readonlyIndexes:[''],
    disableIndexes:['id_equipo','habilitado'],
    align:'left',
    minChars:3
});



equiposListingEditorGrid =  new Ext.grid.EditorGridPanel({
    id: 'equiposListingEditorGrid',
    title: 'Equipos',
    store: equiposDataStore,
    cm: equiposColumnModel,
    enableColLock:false,
    trackMouseOver:true, 
    loadMask: true, 
    renderTo: 'grillita',
    viewConfig: {
        forceFit: false
    },
    tbar: [
        {
            text: 'Nuevo Equipo',
            tooltip: 'Alta nuevo equipo',
            iconCls:'add',                     
            handler: clickBtnNuevoEquipo
         }
    ],
    plugins:[habilitadoCheck,equiposBuscador], 
    clicksToEdit:2,
    height:500,
    layout: 'fit',
    selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
    bbar: paginador
 });   
equiposListingEditorGrid.on('afteredit', guardarCambiosGrillaEquipo);
equiposDataStore.load({params: {start: 0, limit: TAM_PAGINA}});

 
var altura=Ext.getBody().getSize().height - 60;
equiposListingEditorGrid.setHeight(altura);

Ext.getCmp('browser').on('resize',function(comp){
    equiposListingEditorGrid.setWidth(this.getSize().width);
    equiposListingEditorGrid.setHeight(Ext.getBody().getSize().height - 60);

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
function guardarCambiosGrillaEquipo(oGrid_event){
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
		 id_equipo:  oGrid_event.record.data.id_equipo,     
		 campos :  encoded_array_f,
		 valores : encoded_array_v
      }, 
      success: function(response){              
         var result=eval(response.responseText);
         switch(result){
         case 1:
            equiposDataStore.commitChanges();
            equiposDataStore.reload();
            break;
         case 2:
            Ext.MessageBox.alert('Error','No tiene permisos para realizar la operacion solicitada.');
            equiposDataStore.reload();
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

equiposListingEditorGrid.on('celldblclick', abrir_popup_equipos);
function abrir_popup_equipos(grid ,  rowIndex, columnIndex,  e){
    var data=grid.store.data.items[rowIndex].data;
//    var grid= Ext.getCmp('cppEventosGridPanel');
    var cm= grid.getColumnModel();
    var WinEquipos;
    var enc=['<html>','<div class="tabla_popup_grilla">'];
    var pie=['<br class="popup_clear"/></div>','</html>'];
    var cppla=['<p><div class="titulo">Detetalle del equipo nro '+data.id_equipo+'<br></div></p>'];
    
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

    WinEquipos = new Ext.Window({
            title: 'Detalle del equipo nro '+data.id_equipo,
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
                            WinEquipos.hide();
                            WinEquipos.destroy();

                    }
            }]
    });
    WinEquipos.show();
}