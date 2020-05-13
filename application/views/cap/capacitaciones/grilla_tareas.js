capTareasDataStore = new Ext.data.Store({
    id: 'capTareasDataStore',
    proxy: new Ext.data.HttpProxy({
              url: CARPETA+'/listadoTareas', 
              method: 'POST'
          }),
    baseParams:{tampagina: TAM_PAGINA}, // this parameter is passed for any HTTP request
    reader: new Ext.data.JsonReader({
      root: 'rows',
      totalProperty: 'total',
      id: 'id_tarea'
    },[ 
      {name: 'id_tarea',        type: 'int',    mapping: 'id_tarea'},
      {name: 'id_estado',       type: 'int',    mapping: 'id_estado'},
      {name: 'fecha_limite',    type: 'string', mapping: 'fecha_vto'},
      {name: 'solicitante',     type: 'string', mapping: 'usuario_alta'},
      {name: 'responsable',     type: 'string', mapping: 'usuario_responsable'},
      {name: 'estado',          type: 'string', mapping: 'estado'},
      {name: 'archivos',        type: 'string', mapping: 'archivos'},
      {name: 'archivos_qtip',        type: 'string', mapping: 'archivos_qtip'},
      {name: 'archivos_ext',        type: 'string', mapping: 'archivos_ext'}
    ]),
    sortInfo:{field: 'id_tarea', direction: "ASC"},
    remoteSort: true
});
paginador.bindStore(capTareasDataStore);

capTareasColumnModel = new Ext.grid.ColumnModel(
[   new Ext.grid.RowNumberer(),{
        header: '# Tarea',
        dataIndex: 'id_tarea',
        width: 80,
        sortable: true,
    },{
        header: 'Estado Tarea',
        dataIndex: 'estado',
        renderer: showEstado,
        width: 80,
        sortable: true,
    },{
        header: 'Responsable',
        dataIndex: 'responsable',
        width: 180,
        sortable: true,
    },{
        header: 'Solicitante',
        dataIndex: 'solicitante',
        width: 180,
        sortable: true,
    },{
        header: 'Fecha Limite',
        dataIndex: 'fecha_limite',
        width: 120,
        sortable: true,
    },{
        header: 'Archivos adjuntos',
        dataIndex: 'archivos',
        width:  200,
        readOnly: true,
        sortable: false,
        renderer:showArchivosAdjuntos,
        align:'left'
    }
]);
    
capTareasBuscador= new Ext.ux.grid.Search({
    iconCls:'icon-zoom',
//    readonlyIndexes:[''],
    disableIndexes:['fecha_limite'],
    align:'left',
    minChars:3
});

capTareasGridPanel =  new Ext.grid.GridPanel({
    id: 'capTareasGridPanel',
    title: 'Tareas de capacitación para el tema Nro:...',
//    columnWidth:.5,
//    region:'center',
    anchor:'100%',
    autoScroll : true,
    store: capTareasDataStore,
    cm: capTareasColumnModel,
    enableColLock:false,
    trackMouseOver:true, 
    loadMask: true, 
    viewConfig: {
        forceFit: true
    },
    tbar: ['&emsp;'],
    plugins:[], 
    layout: 'fit',
    selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
    bbar: paginador
 });   
//tareasListingEditorGrid.on('afteredit', guardarCambiosGrillaConsecuencias);
//capTareasDataStore.load({params: {start: 0, limit: TAM_PAGINA}});

 


//capTareasGridPanel = new Ext.Panel(
// {
//    title: 'Tareas de capacitación para el tema Nro:',
//    region:'east',
//    autoScroll : true,
//    columnWidth:.5,
//    layout: 'fit',
//    items:[capTareasGridPanel]
//});

var altura=(Ext.getBody().getSize().height - 75)/2;
capTareasGridPanel.setHeight(altura);

function showEstado (value,metaData,superData){
    var estado=superData.json.id_estado;
    switch (estado)
    {
        case '1':
        metaData.attr = 'style="background-color:#01A9DB; color:#FFF;"';
        break;
        case '2':
        metaData.attr = 'style="background-color:#F7FE2E; color:#848484;"';
        break;
        case '3':
        metaData.attr = 'style="background-color:#DF7401; color:#FFF;"';
        break;
        case '4':
        metaData.attr = 'style="background-color:#037DA2; color:#FFF;"';
        break;
        case '5':
        metaData.attr = 'style="background-color:#FF0000; color:#FFF;"';
        break;
        case '6':
        metaData.attr = 'style="background-color:#A4A4A4; color:#FFF;"';
        break;
        case '7':
        metaData.attr = 'style="background-color:#FFFF00; color:#FFF;"';
        break;
        case '8':
        metaData.attr = 'style="background-color:#151515; color:#FFF;"';
        break;
        case '9':
        metaData.attr = 'style="background-color:#088A08; color:#FFF;"';
        break;
        case '10':
        metaData.attr = 'style="background-color:#DF7401; color:#FFF;"';
        break;
        
    }
    var deviceDetail = superData.get('obs');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
        
    return value;
}

capTareasGridPanel.on('celldblclick', abrir_popup_tareasCapacitaciones);
function abrir_popup_tareasCapacitaciones(grid ,  rowIndex, columnIndex,  e){
    var data=grid.store.data.items[rowIndex].data;
                var panelContentTareas;
                var winTareasTareas;
                var enc=['<html>','<div class="tabla_popup_grilla">'];
                var pie=['<br class="popup_clear"/></div>','</html>'];
                var nodos=[
                            '<p>',
                                '<div class="titulo">Detalle Tarea Nro '+data.id_tarea+'<br></div>',
                            '</p>',
                            '<p>',
                                '<div class="col1">Estado:</div>',
                                '<div class="col2">'+data.estado+'</div>',
                            '</p>',
                            '<p>',
                                '<div class="col1">Responsable:</div>',
                                '<div class="col2">'+data.responsable+'</div>',
                            '</p>',
                            '<p>',
                                '<div class="col1">Solicitante:</div>',
                                '<div class="col2">'+data.solicitante+'</div>',
                            '</p>',
                            '<p>',
                                '<div class="col1">Fecha Limite:</div>',
                                '<div class="col2">'+data.fecha_limite+'</div>',
                            '</p>'
                ];
                
                var html = enc.concat(nodos);
                var html = html.concat(pie);

                        winTareasCapacitaciones = new Ext.Window({
                                title: 'Tarea Nro '+data.id_tarea,
                                closable: true,
                                modal:true,
                                //closeAction: 'hide',
                                width: 790,
                                boxMinWidth:790,
                                height: 550,
                                boxMinHeight:550,
                                plain: true,
                                autoScroll:true,
                                layout: 'absolute',
                                html: html.join(''),
//                                items: [],
                                buttons: [{
                                        text: 'Cerrar',
                                        handler: function(){
                                                winTareasCapacitaciones.hide();
                                                winTareasCapacitaciones.destroy();

                                        }
                                }]
                        });
//                };
                winTareasCapacitaciones.show();
}

function showArchivosAdjuntos (value,metaData,record){
    if (value !="" && value !=null)
    {
        var deviceDetail = record.get('archivos_qtip');
        var exts = record.get('archivos_ext');
        deviceDetail=deviceDetail.split(",");
        exts=exts.split(",");
        var archivos=value.split(",")
        var enlace="";
        var img="";
        if (archivos.length>=1)
        {
            for (i=0;i<archivos.length;i++)
                {
                    switch (exts[i])
                    {
                        case 'pdf':
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
                    enlace += "<a target='_blank' href='"+URL_BASE_SITIO+"archivos/cap/"+archivos[i]+"/"+deviceDetail[i]+"'><img ext:qtip='"+deviceDetail[i]+"' class='x-action-col-icon x-action-col-0  ' src='"+URL_BASE+"/images/"+img+"' alt=''></a>";

                }
        }
        return enlace;
    }
    else
        return value;
        
}