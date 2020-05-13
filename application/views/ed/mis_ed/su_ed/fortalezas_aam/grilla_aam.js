aamDataStore = new Ext.data.Store({
      id: 'aamDataStore',
      proxy: new Ext.data.HttpProxy({
                url: CARPETA+'/listado_aam', 
                method: 'POST'
            }),
      baseParams:{tampagina: TAM_PAGINA, id_ed:ID_ED}, // this parameter is passed for any HTTP request
      reader: new Ext.data.JsonReader({
        root: 'rows',
        totalProperty: 'total',
        id: 'id_aam'
      },[ 
        {name: 'id_aam',    type: 'int',    mapping: 'id_aam'},        
        {name: 'aam',       type: 'string', mapping: 'aam'},
      ]),
      sortInfo:{field: 'id_aam', direction: "asc"},
      remoteSort : true
    });
botonesAamAction = new Ext.grid.ActionColumn({
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
        handler: clickBtnEliminarAaM
        }
    ]
});
    	
 aamColumnModel = new Ext.grid.ColumnModel(
    [new Ext.grid.RowNumberer(),{
        header: '#',
        readOnly: true,
        dataIndex: 'id_aam',
        width: 40,
        hidden: true
      },{
        header: 'Apectos a mejorar cargados...',
        dataIndex: 'aam',
        width:  ANCHO-50,
//        readOnly: !permiso_modificar,
        sortable: true,
        renderer:showQtipAam
      },botonesAamAction
      ]
    );
 
//    var aamView= new Ext.ux.grid.BufferView({
//        // custom row height
//        rowHeight: 40,
//        // render rows as they come into viewable area.
//        scrollDelay: false
//    });
 aamGridPanel =  new Ext.grid.GridPanel({
    id: 'aamGridPanel',
    store: aamDataStore,
    cm: aamColumnModel,
    enableColLock:false,
    trackMouseOver:true, //que se coloree cuando pasas el mouse por encima
    loadMask: true, //que te ponga el loading cuando se esta generando el store
    viewConfig: {
        forceFit: true
    },
//    view: aamView,
    autoExpandColumn: 'aam',
    plugins:[],
//    clicksToEdit:2,
//    layout: 'fit',
//    selModel: new Ext.grid.RowSelectionModel({singleSelect:true}),
    bbar: new Ext.PagingToolbar({
        pageSize: TAM_PAGINA,
        store: aamDataStore,
        displayMsg: 'Mostrando {0} - {1} de {2}',				
        displayInfo: true
    }),
    tbar: [
      ]
    });
     
    aamDataStore.load({params: {start: 0, limit: TAM_PAGINA}});
   
function showQtipAam(value, metaData,record){
    var deviceDetail = record.get('aam');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
    return value;
}
aamGridPanel.on('celldblclick', abrir_popup_detalle);
function abrir_popup_detalle(grid ,  rowIndex, columnIndex,  e){
    var data=grid.store.data.items[rowIndex].data;
    var nro=parseInt(rowIndex)+1;
    var txt_popup=data.aam;
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
                        title: 'Texto completo de la aam Nro '+nro,
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
function clickBtnEliminarAaM(grid,rowIndex,colIndex,item,event){
      msgProcess('Eliminando...');
      var id=grid.getStore().getAt(rowIndex).json.id_aam;
        Ext.Ajax.request({   
        waitMsg: 'Por favor espere...',
        url: CARPETA+'/eliminar_aam',
        method: 'POST',
        params: {
          id_aam:id,  
          id_ed:ID_ED  
        }, 
        success: function(response){              
          var result=eval(response.responseText);
//          Ext.MessageBox.hide(); 
          if(result.success){
            aamDataStore.removeAll();
            aamDataStore.reload();
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