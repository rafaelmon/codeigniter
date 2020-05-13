metasDataStore = new Ext.data.Store({
      id: 'metasDataStore',
      proxy: new Ext.data.HttpProxy({
                url: CARPETA+'/listado_metas', 
                method: 'POST'
            }),
      baseParams:{tampagina: TAM_PAGINA, id_ed:ID_ED}, // this parameter is passed for any HTTP request
      reader: new Ext.data.JsonReader({
        root: 'rows',
        totalProperty: 'total',
        id: 'id_meta'
      },[ 
        {name: 'id_meta',    type: 'int',    mapping: 'id_meta'},        
        {name: 'meta',       type: 'string', mapping: 'meta'},
        {name: 'plazo',      type: 'string', mapping: 'plazo'},
      ]),
      sortInfo:{field: 'id_meta', direction: "asc"},
      remoteSort : true
    });
botonesMetasAction = new Ext.grid.ActionColumn({
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
        handler: clickBtnEliminarMeta
        }
    ]
});
    	
 metasColumnModel = new Ext.grid.ColumnModel(
    [new Ext.grid.RowNumberer(),{
        header: '#',
        readOnly: true,
        dataIndex: 'id_meta',
        width: 40,
        hidden: true
      },{
        header: 'Metas cargadas...',
        dataIndex: 'meta',
        width:  ANCHO-100,
        readOnly: !permiso_modificar,
        sortable: true,
        renderer:showQtipMetas
      },{
        header: 'Plazo',
        dataIndex: 'plazo',
        width:  100,
        readOnly: !permiso_modificar,
        sortable: true
      },botonesMetasAction
      ]
    );
 
//    var metasView= new Ext.ux.grid.BufferView({
//        // custom row height
//        rowHeight: 40,
//        // render rows as they come into viewable area.
//        scrollDelay: false
//    });
 metasGridPanel =  new Ext.grid.GridPanel({
    id: 'metasGridPanel',
    store: metasDataStore,
    cm: metasColumnModel,
    enableColLock:false,
    trackMouseOver:true, //que se coloree cuando pasas el mouse por encima
    loadMask: true, //que te ponga el loading cuando se esta generando el store
    viewConfig: {
        forceFit: true
    },
//    view: metasView,
    autoExpandColumn: 'meta',
    plugins:[],
//    clicksToEdit:2,
//    layout: 'fit',
//    selModel: new Ext.grid.RowSelectionModel({singleSelect:true}),
    bbar: new Ext.PagingToolbar({
        pageSize: TAM_PAGINA,
        store: metasDataStore,
        displayMsg: 'Mostrando {0} - {1} de {2}',				
        displayInfo: true
    }),
    tbar: [
      ]
    });
     
    metasDataStore.load({params: {start: 0, limit: TAM_PAGINA}});
  
function showQtipMetas(value, metaData,record){
    var deviceDetail = record.get('meta');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
    return value;
}
metasGridPanel.on('celldblclick', abrir_popup_detalle);
function abrir_popup_detalle(grid ,  rowIndex, columnIndex,  e){
    var data=grid.store.data.items[rowIndex].data;
    var nro=parseInt(rowIndex)+1;
    var txt_popup=data.meta;
    if(txt_popup!="")
    {
        var winMetaDetalle;
        var html=['<html>',
                    '<div>',
                        '<div><span>'+txt_popup+'</span>',
                        '</div>',
                        '<br class="popup_clear"/>',
                    '</div>',
                    '</html>'
                    ];
                winMetaDetalle = new Ext.Window({
                        title: 'Texto completo',
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
                                        winMetaDetalle.hide();
                                        winMetaDetalle.destroy();
                                }
                        }]
                });
    //                };
        winMetaDetalle.show();
    }
}
function clickBtnEliminarMeta(grid,rowIndex,colIndex,item,event){
      msgProcess('Eliminando...');
      var id=grid.getStore().getAt(rowIndex).json.id_meta;
        Ext.Ajax.request({   
        waitMsg: 'Por favor espere...',
        url: CARPETA+'/eliminar_meta',
        method: 'POST',
        params: {
          id_meta:id,  
          id_ed:ID_ED  
        }, 
        success: function(response){              
          var result=eval(response.responseText);
//          Ext.MessageBox.hide(); 
          if(result.success){
            metasDataStore.removeAll();
            metasDataStore.reload();
            Ext.MessageBox.alert('Operación OK','El registro ha sido eliminado correctamente');
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