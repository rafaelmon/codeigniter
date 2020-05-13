historialTareaDataStore = new Ext.data.Store({
    id: 'historialTareaDataStore',
    proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/historial_cambios', 
            method: 'POST'
    }),
      baseParams:{tampagina: TAM_PAGINA}, // this parameter is passed for any HTTP request
      reader: new Ext.data.JsonReader({
        root: 'rows',
        totalProperty: 'total',
        id: 'id_historial'
      },[ 
        {name: 'id_historial',          type: 'int',        mapping: 'id_historial'},        
        {name: 'hallazgo',              type: 'string',     mapping: 'hallazgo'},
        {name: 'tarea',                 type: 'string',     mapping: 'tarea'},
        {name: 'fecha_accion',          type: 'string',     mapping: 'fecha_accion'},
        {name: 'fecha_vto',             type: 'string',     mapping: 'fecha_vto'},
        {name: 'usuario_responsable',   type: 'string',     mapping: 'usuario_responsable'},
        {name: 'id_estado',             type: 'int',        mapping: 'id_estado'},
        {name: 'estado',                type: 'string',     mapping: 'estado'},
        {name: 'obs',                   type: 'string',     mapping: 'obs'},
        {name: 'archivos',              type: 'string',     mapping: 'archivos'},
        {name: 'archivos_qtip',         type: 'string',     mapping: 'archivos_qtip'}
      ]),
//      sortInfo:{field: 'id_historialTarea', direction: "ASC"},
      remoteSort: true
    });

historialTareasColumnModel = new Ext.grid.ColumnModel(
    [/*{
        header: '#',
        readOnly: true,
        dataIndex: 'id_historial',
        width: 30,        
        sortable: true,
        renderer: function(value, cell){
            cell.css = "readonlycell";
            return value;
        },
        hidden: false
      },*/{
        header: 'Fecha',
        dataIndex: 'fecha_accion',
        sortable: false,
        width:  80,
        fixed:true,
        readOnly: true,
        align:'center'
      },{
        header: 'Estado',
        dataIndex: 'estado',
        sortable: false,
        width:  80,
        fixed:true,
        readOnly: true,
        renderer: showEstado,
        align:'center'
      },{
        header: 'Detalle del hallazgo',
        dataIndex: 'hallazgo',
        width:  220,
        sortable: true,
        renderer:showQtipHallazgo
      },{
        header: 'Tarea a realizar',
        dataIndex: 'tarea',
        width:  220,
        sortable: true,
        renderer:showQtipTarea
      },{
        header: 'Fecha Limite',
        dataIndex: 'fecha_vto',
        sortable: false,
        width:  80,
        fixed:true,
        readOnly: true,
        align:'center'
      },{
        header: 'Usuario Responsable',
        dataIndex: 'usuario_responsable',
        sortable: true,
        width:  180,
        align:'left'
      },{
        header: 'Observaciones',
        dataIndex: 'obs',
        sortable: true,
        width:  350,
        renderer:showQtipObs,
        align:'left'
    },{
        header: 'Adjuntos',
        dataIndex: 'archivos',
        width:  350,
        readOnly: true,
        sortable: true,
        renderer:showAdjuntos,
        align:'left'
      }
     ]
    );
  
   historialTareasGridPanel =  new Ext.grid.GridPanel({
        id: 'historialTareasListingGrid',
        store: historialTareaDataStore,
        cm: historialTareasColumnModel,
        enableColLock:false,
        trackMouseOver:true, //que se coloree cuando pasas el mouse por encima
        loadMask: true, //que te ponga el loading cuando se esta generando el store
        viewConfig: {
            forceFit: false
        }
    });   
    
    tareasHistorialCambiosPanel = new Ext.Panel(
{
        collapsible: false,
        collapsed:false,
        split: false,
//        title: 'Historial',
//        region: 'south',
        height: 300,
        minSize: 100,
        maxSize: 350,
        margins: '0 5 5 5',
        html:'<p>panel inferior historial de cambios</p>',
        layout: 'fit',
        items : [historialTareasGridPanel]
});

        
function showQtipHallazgo(value, metaData,record){
    var deviceDetail = record.get('hallazgo');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
    return value;
}
function showQtiphistorialTarea(value, metaData,record){
    var deviceDetail = record.get('historialTarea');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
    return value;
}
function showQtipArea(value, metaData,record){
    var deviceDetail = record.get('area');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
    return value;
}
function showQtipObs(value, metaData,record){
    var deviceDetail = record.get('obs');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
    return value;
}
function showAdjuntos (value,metaData,record){
    if (value !="")
    {
        var deviceDetail = record.get('archivos_qtip');
        deviceDetail=deviceDetail.split(",");
        var archivos=value.split(",")
        var enlace="";
        if (archivos.length>=1)
        {
            for (i=0;i<archivos.length;i++)
                {
                    enlace += "<a target='_blank' href='"+URL_BASE_SITIO+"archivos/cierre/"+archivos[i]+"/"+deviceDetail[i]+"'><img ext:qtip='"+deviceDetail[i]+"' class='x-action-col-icon x-action-col-0  ' src='"+URL_BASE+"/images/file_default.gif' alt=''></a>";

                }
        }
        return enlace;
    }
    else
        return value;
        
}
historialTareasGridPanel.on('celldblclick', abrir_popup_txtCompleto);
function abrir_popup_txtCompleto(grid ,  rowIndex, columnIndex,  event){
    var data=grid.store.data.items[rowIndex].data;
    var txt_popup=data.obs;
    
    if(txt_popup!="")
    {
        var winHistoriaTareas;
        var html=['<html>',
                    '<div>',
                        '<div><span>'+txt_popup+'</span>',
                        '</div>',
                        '<br class="popup_clear"/>',
                    '</div>',
                    '</html>'
                    ];


                winHistoriaTareas = new Ext.Window({
                        title: 'Observaciones texto completo...',
                        closable: true,
                        modal:true,
                        //closeAction: 'hide',
                        width: 450,
                        boxMinWidth:300,
                        height: 200,
                        boxMinHeight:150,
                        plain: true,
                        autoScroll:true,
                        layout: 'absolute',
                        html: html.join(''),
    //                                items: [],
                        buttons: [{
                                text: 'Cerrar',
                                handler: function(){
                                        winHistoriaTareas.hide();
                                        winHistoriaTareas.destroy();

                                }
                        }]
                });
    //                };
        winHistoriaTareas.show();
    }
    

}
        