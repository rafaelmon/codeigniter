vencimientosDataStore = new Ext.data.Store({
    id: 'vencimientosDataStore',
    proxy: new Ext.data.HttpProxy({
              url: CARPETA+'/listado', 
              method: 'POST'
          }),
    baseParams:{start: 0, limit: TAM_PAGINA}, // this parameter is passed for any HTTP request
    reader: new Ext.data.JsonReader({
      root: 'rows',
      totalProperty: 'total',
      id: 'id_vencimiento'
    },[ 
      {name: 'id_vencimiento',          type: 'int',    mapping: 'id_vencimiento'},        
      {name: 'vencimiento',             type: 'string', mapping: 'vencimiento'},
      {name: 'descripcion',             type: 'string', mapping: 'descripcion'},
      {name: 'id_estado',               type: 'int',    mapping: 'id_estado'},
      {name: 'estado',                  type: 'string', mapping: 'estado'},
      {name: 'id_usuario_alta',         type: 'int',    mapping: 'id_usuario_alta'},
      {name: 'usuario_alta',            type: 'string', mapping: 'usuario_alta'},
      {name: 'fecha_alta',              type: 'string', mapping: 'fecha_alta'},
      {name: 'id_usuario_responsable',  type: 'string', mapping: 'id_usuario_responsable'},
      {name: 'usuario_responsable',     type: 'string', mapping: 'usuario_responsable'},
      {name: 'fecha_vto',               type: 'string', mapping: 'fecha_vto'},
      {name: 'fecha_aviso',             type: 'string', mapping: 'fecha_vto'},
      {name: 'q_avisos',                type: 'string', mapping: 'q_avisos'},
      {name: 'rpd',               	type: 'string', mapping: 'rpd'},
      {name: 'archivo',               	type: 'string', mapping: 'archivo'}
    ]),
    sortInfo:{field: 'fecha_vencimiento', direction: "ASC"},
    remoteSort: true
});
paginador.bindStore(vencimientosDataStore);
    
vencimientosBotonesAccion = new Ext.grid.ActionColumn({
    editable:false,
    menuDisabled:true,
    header:'Acción',
    hideable:false,
    align:'center',
    width:  80,
//    tooltip:'',
    hidden:false,
    items:[
        {
            icon:URL_BASE+'images/list-information.png',
            iconCls :'col_accion',
            tooltip:'Cerrar Vencimiento',
            getClass:showBtnCerrar,
            handler: clickBtnCerrar
        },{
            icon:URL_BASE+'images/eliminar.png',
            iconCls :'col_accion',
            tooltip:'Eliminar Vencimiento',
    //        getClass: showBtnCerrarVto,
            handler: clickBtnEliminar
        }
    ]
});


vencimientosColumnModel = new Ext.grid.ColumnModel(
[{
        header: '#',
        readOnly: true,
        dataIndex: 'id_vencimiento',
        width: 40,        
        sortable: true,
        hidden: false,
        renderer:   function(value, cell){
                        cell.css = "readonlycell";
                        return value;
                    },
    },{
        header: 'Vencimiento',
        dataIndex: 'vencimiento',
        width: 150,
        sortable: true,
    },{
        header: 'Descripción',
        dataIndex: 'descripcion',
        width: 140,
        sortable: false
    },{
        header: 'Estado',
        dataIndex: 'estado',
        width: 90,
        sortable: false,
        renderer: showEstado
    },
    {
        header: 'Usuario alta',
        dataIndex: 'usuario_alta',
        width: 150,
        sortable: false
    },{
        header: 'Fecha alta',
        dataIndex: 'fecha_alta',
        width: 90,
        sortable: false
    },{
        header: 'Usuario responsable',
        dataIndex:'usuario_responsable',
        width: 150,
        sortable: false
    },{
        header: '<b>Fecha VTO</b>',
        dataIndex: 'fecha_vto',
        width: 90,
        sortable: false
    },{
        header: 'Fecha aviso',
        dataIndex: 'fecha_aviso',
        width: 90,
        sortable: false
    },{
        header: 'Q avisos',
        dataIndex: 'q_avisos',
        width: 80,
        align: 'center',
        sortable: false
    },{
        header: 'Revisión',
        dataIndex: 'rpd',
        sortable: true,
        width:  50,
        fixed:true,
        readOnly: true,
        align:'center'
    },{
        header: 'Acta',
        dataIndex: 'archivos',
        width:  50,
        readOnly: true,
        sortable: false,
        renderer:showArchivosAdjuntos,
        align:'left'
    },vencimientosBotonesAccion
]);

vencimientosBuscador= new Ext.ux.grid.Search({
    iconCls:'icon-zoom',
//    readonlyIndexes:[''],
    disableIndexes:['fecha_alta','fecha_vto'],
    align:'left',
    minChars:3
});

vencimientosListingGrid =  new Ext.grid.GridPanel({
    id: 'vencimientosListingEditorGrid',
    title: 'Vencimientos',
    store: vencimientosDataStore,
    cm: vencimientosColumnModel,
    region:'center',
    enableColLock:false,
    trackMouseOver:true, //que se coloree cuando pasas el mouse por encima
    loadMask: true, //que te ponga el loading cuando se esta generando el store
    renderTo: 'grillita',
    viewConfig: {
        forceFit: false
    },
    tbar: [
        {
            text: 'Nuevo Vencimiento',
            tooltip: 'Nuevo Vencimiento',
            iconCls:'add',
            handler: clickBtnNuevoVencimiento,
        }
    ],
    plugins:[vencimientosBuscador],
    height:500,
    layout: 'fit',
    selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
    bbar: paginador
 });   
vencimientosDataStore.load({params: {start: 0, limit: TAM_PAGINA}});

 
var altura=Ext.getBody().getSize().height - 60;
vencimientosListingGrid.setHeight(altura);

Ext.getCmp('browser').on('resize',function(comp){
    vencimientosListingGrid.setWidth(this.getSize().width);
    vencimientosListingGrid.setHeight(Ext.getBody().getSize().height - 60);

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

function showEstado (value,metaData,superData){
    
    var id_estado=superData.json.id_estado;
//    var estado=superData.json.estado;
    switch (id_estado)
    {
        case '1':
            metaData.attr = 'style="background-color:#049027; color:#FFF;"';
            break;
        case '2':
            metaData.attr = 'style="background-color:#FF8000; color:#FFF;"';
            break;
        case '3':
            metaData.attr = 'style="background-color:#08208A; color:#FFF;"';
            break;
        case '4':
            metaData.attr = 'style="background-color:#E7031D; color:#FFF;"';
            break;
    }
    var deviceDetail = superData.get('estado');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
        
    return value;
}

function clickBtnEliminar (grid,rowIndex,colIndex,item ,event){
    clickBtnEliminarVencimiento(grid,rowIndex,colIndex,item ,event);
}
function clickBtnCerrar (grid,rowIndex,colIndex,item ,event){
    //clickBtnCerrarVencimiento(grid,rowIndex,colIndex,item ,event);
    clickBtnArchivoVencimiento(grid,rowIndex,colIndex,item,event);
}

function showBtnCerrar(value,metaData,record){
    var id_estado = record.json.id_estado;
            
    if(id_estado == 1 || id_estado == 4)
        return 'x-grid-center-icon'; 
    else
        return 'x-hide-display'; 
                       
};

vencimientosListingGrid.on('celldblclick', abrir_popup_vencimientos);
function abrir_popup_vencimientos(grid ,  rowIndex, columnIndex,  e){
    var data=grid.store.data.items[rowIndex].data;
                var enc=['<html>','<div class="tabla_popup_grilla">'];
                var pie=['<br class="popup_clear"/></div>','</html>'];
                var nodos=[
                            '<p>',
                                '<div class="titulo">Detalle vencimiento nro '+data.id_vencimiento+'<br></div>',
                            '</p>',
                            '<p>',
                                '<div class="col1">Vencimiento:</div>',
                                '<div class="col2">'+data.vencimiento+'</div>',
                            '</p>',
                            '<p>',
                                '<div class="col1">Descripciòn:</div>',
                                '<div class="col2">'+data.descripcion+'</div>',
                            '</p>',
                            '<p>',
                                '<div class="col1">Estado:</div>',
                                '<div class="col2">'+data.estado+'</div>',
                            '</p>',
                            '<p>',
                                '<div class="col1">Usuario alta:</div>',
                                '<div class="col2">'+data.usuario_alta+'</div>',
                            '</p>',
                            '<p>',
                                '<div class="col1">Fecha alta:</div>',
                                '<div class="col2">'+data.fecha_alta+'</div>',
                            '</p>',
                            '<p>',
                                '<div class="col1">Usuario responsable:</div>',
                                '<div class="col2">'+data.usuario_responsable+'</div>',
                            '</p>',
                            '<p>',
                                '<div class="col1"><b>Fecha VTO:</b></div>',
                                '<div class="col2">'+data.fecha_vto+'</div>',
                            '</p>'
                ];
                
                var html = enc.concat(nodos);
                var html = html.concat(pie);

                        winVencimientos = new Ext.Window({
                                title: 'Vencimiento nro '+data.id_vencimiento,
                                closable: true,
                                modal:true,
                                //closeAction: 'hide',
                                width: 790,
                                boxMinWidth:790,
                                height: 350,
                                boxMinHeight:350,
                                plain: true,
                                autoScroll:true,
                                layout: 'absolute',
                                html: html.join(''),
//                                items: [],
                                buttons: [{
                                        text: 'Cerrar',
                                        handler: function(){
                                                winVencimientos.hide();
                                                winVencimientos.destroy();

                                        }
                                }]
                        });
//                };
                winVencimientos.show();

}

function showArchivosAdjuntos (value,metaData,record){
        var id = record.get('id_vencimiento');
        var deviceDetail = record.get('archivo');
        var nombre = record.get('archivo');
        deviceDetail=deviceDetail.split(".");
        var enlace="";
        var img="";
        if (nombre != "")
        {
            switch (deviceDetail[1])
            {
                case 'pdf':
                    img='file_pdf.png';
                    break;
                case 'tif':
                    img='file_pdf.png';
                    break;
                case 'txt':
                    img='file_txt.png';
                    break;
                case 'doc':case 'docx':
                    img='file_word.png';
                    break;
                case 'xls':case 'xlsx':
                    img='file_excel.png';
                    break;
                case 'ppt':case 'pps':case 'pptx':
                    img='file_excel.png';
                    break;
                case 'png':case 'jpg':case 'jpeg':case 'tif':case 'bmp':
                    img='file_image.png';
                    break;
                case 'rar':case 'zip':
                    img='file_zip.png';
                    break;
                default:
                    img='file_default.png';
                    break;

            }
            return enlace += "<a target='_blank' href='"+URL_BASE_SITIO+"vto/vencimientos/preview/"+id+"/"+nombre+"'><img ext:qtip='"+nombre+"' class='x-action-col-icon x-action-col-0  ' src='"+URL_BASE+"/images/"+img+"' alt=''></a>";
        }
        return enlace;
//    }
//    else
//        return value;
        
}