fortalezasDataStore = new Ext.data.Store({
      id: 'fortalezasDataStore',
      proxy: new Ext.data.HttpProxy({
                url: CARPETA+'/listado_fortalezas', 
                method: 'POST'
            }),
      baseParams:{tampagina: TAM_PAGINA, id_ed:ID_ED}, // this parameter is passed for any HTTP request
      reader: new Ext.data.JsonReader({
        root: 'rows',
        totalProperty: 'total',
        id: 'id_fortaleza'
      },[ 
        {name: 'id_fortaleza',    type: 'int',    mapping: 'id_fortaleza'},        
        {name: 'fortaleza',       type: 'string', mapping: 'fortaleza'},
      ]),
      sortInfo:{field: 'id_fortaleza', direction: "asc"},
      remoteSort : true
    });
botonesFortalezasAction = new Ext.grid.ActionColumn({
    width: 15,
    editable:false,
    menuDisabled:true,
    header:'Acción',
    hideable:false,
    align:'center',
    width:  90,
    tooltip:'Acciones',
     hidden:false,
    items:[{
        icon:URL_BASE+'images/101.png',
        iconCls :'col_accion',
        tooltip:'Eliminar',
        hidden: false,
        handler: clickBtnEliminarFortaleza
        }
    ]
});
    	
 fortalezasColumnModel = new Ext.grid.ColumnModel(
    [new Ext.grid.RowNumberer(),{
        header: '#',
        readOnly: true,
        dataIndex: 'id_fortaleza',
        width: 40,
        hidden: true
      },{
        header: 'Fortalezas cargadas...',
        dataIndex: 'fortaleza',
        width:  ANCHO-50,
        readOnly: !permiso_modificar,
        sortable: true,
        renderer:showQtipFortalezas
      },botonesFortalezasAction
      ]
    );
 
//    var fortalezasView= new Ext.ux.grid.BufferView({
//        // custom row height
//        rowHeight: 40,
//        // render rows as they come into viewable area.
//        scrollDelay: false
//    });
 fortalezasGridPanel =  new Ext.grid.GridPanel({
    id: 'fortalezasGridPanel',
    store: fortalezasDataStore,
    cm: fortalezasColumnModel,
    enableColLock:false,
    trackMouseOver:true, //que se coloree cuando pasas el mouse por encima
    loadMask: true, //que te ponga el loading cuando se esta generando el store
    viewConfig: {
        forceFit: true
    },
//    view: fortalezasView,
    autoExpandColumn: 'fortaleza',
    plugins:[],
//    clicksToEdit:2,
//    layout: 'fit',
//    selModel: new Ext.grid.RowSelectionModel({singleSelect:true}),
    bbar: new Ext.PagingToolbar({
        pageSize: TAM_PAGINA,
        store: fortalezasDataStore,
        displayMsg: 'Mostrando {0} - {1} de {2}',				
        displayInfo: true
    }),
    tbar: [
      ]
    });
     
    fortalezasDataStore.load({params: {start: 0, limit: TAM_PAGINA}});
  
function showQtipFortalezas(value, metaData,record){
    var deviceDetail = record.get('fortaleza');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
    return value;
}
fortalezasGridPanel.on('celldblclick', abrir_popup_detalle);
function abrir_popup_detalle(grid ,  rowIndex, columnIndex,  e){
    var data=grid.store.data.items[rowIndex].data;
    var nro=parseInt(rowIndex)+1;
    var txt_popup=data.fortaleza;
    if(txt_popup!="")
    {
        var winFortalezaDetalle;
        var html=['<html>',
                    '<div>',
                        '<div><span>'+txt_popup+'</span>',
                        '</div>',
                        '<br class="popup_clear"/>',
                    '</div>',
                    '</html>'
                    ];
                winFortalezaDetalle = new Ext.Window({
                        title: 'Texto completo de la fortaleza Nro '+nro,
                        closable: true,
                        modal:true,
                        width: 550,
                        boxMinWidth:400,
                        height: 300,
                        boxMinHeight:200,
                        plain: true,
                        autoScroll:true,
                        layout: 'absolute',
                        html: html.join(''),
                        buttons: [{
                                text: 'Cerrar',
                                handler: function(){
                                        winFortalezaDetalle.hide();
                                        winFortalezaDetalle.destroy();
                                }
                        }]
                });
    //                };
        winFortalezaDetalle.show();
    }
}
function clickBtnEliminarFortaleza(grid,rowIndex,colIndex,item,event){
      msgProcess('Eliminando...');
      var id=grid.getStore().getAt(rowIndex).json.id_fortaleza;
        Ext.Ajax.request({   
        waitMsg: 'Por favor espere...',
        url: CARPETA+'/eliminar_fortaleza',
        method: 'POST',
        params: {
          id_fortaleza:id,  
          id_ed:ID_ED  
        }, 
        success: function(response){              
          var result=eval(response.responseText);
//          Ext.MessageBox.hide(); 
          if(result.success){
            Ext.MessageBox.alert('Operación OK','El registro ha sido eliminado correctamente');
            fortalezasDataStore.removeAll();
            fortalezasDataStore.reload();
          } 
          else
            Ext.MessageBox.alert('Error',result.msg);
              
        },
        failure: function(response){
//          var result=response.responseText;
          Ext.MessageBox.alert('Error','No se pudo conectar con el servidor. Intente m&aacute;s tarde');         
        }                      
      });
  }
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