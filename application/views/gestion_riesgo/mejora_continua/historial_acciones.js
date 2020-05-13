historialAccionesTareaDataStore = new Ext.data.Store({
    id: 'historialAccionesTareaDataStore',
    proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/historial_acciones', 
            method: 'POST'
    }),
      baseParams:{tampagina: TAM_PAGINA}, // this parameter is passed for any HTTP request
      reader: new Ext.data.JsonReader({
        root: 'rows',
        totalProperty: 'total',
        id: 'id'
      },[ 
        {name: 'id',                    type: 'int',        mapping: 'id'},        
        {name: 'id_accion',             type: 'string',     mapping: 'id_accion'},
        {name: 'accion',                type: 'string',     mapping: 'accion'},
        {name: 'bgcolor',               type: 'string',     mapping: 'bgcolor'},
        {name: 'color',                 type: 'string',     mapping: 'color'},
        {name: 'fecha',                 type: 'string',     mapping: 'fecha'},
        {name: 'usuario',               type: 'string',     mapping: 'usuario'},
        {name: 'texto',                 type: 'string',     mapping: 'texto'},
        {name: 'archivos',              type: 'string',     mapping: 'archivos'},
        {name: 'archivos_qtip',         type: 'string',     mapping: 'archivos_qtip'},
        {name: 'archivos_ext',         type: 'string',     mapping: 'archivos_ext'}
      ]),
//      sortInfo:{field: 'idAccionesTarea', direction: "ASC"},
      remoteSort: true
    });

historialAccionesTareasColumnModel = new Ext.grid.ColumnModel(
    [/*{
        header: '#',
        readOnly: true,
        dataIndex: 'id',
        width: 30,        
        sortable: true,
        renderer: function(value, cell){
            cell.css = "readonlycell";
            return value;
        },
        hidden: false
      },*/{
        header: 'Fecha',
        dataIndex: 'fecha',
        sortable: false,
        width:  120,
        fixed:true,
        readOnly: true,
        align:'center'
      },{
        header: 'Usuario',
        dataIndex: 'usuario',
        sortable: true,
        width:  180,
        align:'left'
      },{
        header: 'Accion',
        dataIndex: 'accion',
        sortable: false,
        width:  80,
        fixed:true,
        readOnly: true,
        renderer: showEstado,
        renderer:showAccion,
        align:'center'
      },{
        header: 'Detalle',
        dataIndex: 'texto',
        sortable: true,
        width:  650,
        renderer:showQtipTexto,
        align:'left'
    },{
        header: 'Archivos adjuntados',
        dataIndex: 'archivos',
        width:  350,
        readOnly: true,
        sortable: true,
        renderer:showArchivosAdjuntos,
        align:'left'
      }
     ]
    );
  
   historialAccionesTareasGridPanel =  new Ext.grid.GridPanel({
        id: 'historialAccionesTareasListingGrid',
        store: historialAccionesTareaDataStore,
        cm: historialAccionesTareasColumnModel,
        enableColLock:false,
        trackMouseOver:true, //que se coloree cuando pasas el mouse por encima
        loadMask: true, //que te ponga el loading cuando se esta generando el store
        viewConfig: {
            forceFit: false
        }
    });   
    
tareasHistorialAccionesPanel = new Ext.Panel(
{
    ccollapsible: false,
    collapsed:false,
    split: false,
//        title: 'Historial',
//        region: 'south',
    height: 300,
    minSize: 100,
    maxSize: 350,
    margins: '0 5 5 5',
//    html:'<p>panel inferior historial de acciones</p>',
    layout: 'fit',
    items : [historialAccionesTareasGridPanel]
});

altura=Ext.getBody().getSize().height - 60;
tareasHistorialAccionesPanel.setHeight(altura);
	
//Ext.getCmp('browser').on('resize',function(comp){
//    tareasGrlPanel.setWidth(this.getSize().width);
//    tareasGrlPanel.setHeight(Ext.getBody().getSize().height - 60);
//});        


function showQtipTexto(value, metaData,record){
//    var deviceDetail = record.get('texto');
    var deviceDetail = record.json.texto;
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
    return value;
}
function showAccion (value,metaData,superData){
    var id=superData.json.id_accion;
    var bg_color=superData.json.bgcolor;
    var color=superData.json.color;
    metaData.attr = 'style="background-color:'+bg_color+'; color:'+color+';"';
    return value;
}
//function showAdjuntos (value,metaData,record){
//    if (value !="")
//    {
//        var deviceDetail = record.get('archivos_qtip');
//        deviceDetail=deviceDetail.split(",");
//        var archivos=value.split(",")
//        var enlace="";
//        if (archivos.length>=1)
//        {
//            for (i=0;i<archivos.length;i++)
//                {
//                    enlace += "<a target='_blank' href='"+URL_BASE_SITIO+"archivos/cierre/"+archivos[i]+"/"+deviceDetail[i]+"'><img ext:qtip='"+deviceDetail[i]+"' class='x-action-col-icon x-action-col-0  ' src='"+URL_BASE+"/images/file_default.gif' alt=''></a>";
//
//                }
//        }
//        return enlace;
//    }
//    else
//        return value;
//        
//}
historialAccionesTareasGridPanel.on('celldblclick', abrir_popup_textodetalleCompleto);
function abrir_popup_textodetalleCompleto(grid ,  rowIndex, columnIndex,  event){
    var data=grid.store.data.items[rowIndex].data;
    var txt_popup=data.texto;
    
    if(txt_popup!="")
    {
        var winHistorialAccionesTareas;
        var html=['<html>',
                    '<div>',
                        '<div><span>'+txt_popup+'</span>',
                        '</div>',
                        '<br class="popup_clear"/>',
                    '</div>',
                    '</html>'
                    ];


                winHistorialAccionesTareas = new Ext.Window({
                        title: 'Texto completo...',
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
                                        winHistorialAccionesTareas.hide();
                                        winHistorialAccionesTareas.destroy();

                                }
                        }]
                });
    //                };
        winHistorialAccionesTareas.show();
    }
}
function showArchivosAdjuntos (value,metaData,record){
    if (value !="")
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
                    enlace += "<a target='_blank' href='"+URL_BASE_SITIO+"archivos/cierre/"+archivos[i]+"/"+deviceDetail[i]+"'><img ext:qtip='"+deviceDetail[i]+"' class='x-action-col-icon x-action-col-0  ' src='"+URL_BASE+"/images/"+img+"' alt=''></a>";

                }
        }
        return enlace;
    }
    else
        return value;
        
}
        