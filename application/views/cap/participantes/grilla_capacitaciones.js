capParticipantesCapacitacionesDataStore = new Ext.data.Store({
    id: 'capParticipantesCapacitacionesDataStore',
    proxy: new Ext.data.HttpProxy({
              url: CARPETA+'/listadoCapacitaciones', 
              method: 'POST'
          }),
    baseParams:{tampagina: TAM_PAGINA}, // this parameter is passed for any HTTP request
    reader: new Ext.data.JsonReader({
      root: 'rows',
      totalProperty: 'total',
//      id: 'tipo'
    },[ 
      {name: 'tipo',            type: 'string', mapping: 'tipo'},        
      {name: 'titulo',          type: 'string', mapping: 'titulo'},
      {name: 'id_tarea',        type: 'string', mapping: 'id_tarea'},
      {name: 'fecha_cap',       type: 'string', mapping: 'fecha_cap'},
       {name: 'archivos',        type: 'string', mapping: 'archivos'},
      {name: 'archivos_qtip',        type: 'string', mapping: 'archivos_qtip'},
      {name: 'archivos_ext',        type: 'string', mapping: 'archivos_ext'}
    ]),
    sortInfo:{field: 'fecha_cap', direction: "ASC"},
    remoteSort: true
});
//capParticipantesCapacitacionesDataStore.load({params: {start: 0, limit: TAM_PAGINA}});

capParticipantesCapacitacionesPaginador= new Ext.PagingToolbar({
    pageSize: parseInt(TAM_PAGINA),
    displayInfo: true,
    beforePageText:'Página',
    afterPageText:'de {0}',
    displayMsg:'Mostrando {0} - {1} de <b>{2}</b>',
    firstText:'Primera Página',
    lastText:'Última Página',
    prevText:'Anterior',
    nextText:'Siguiente',
    refreshText:'Actualizar',
    buttonAlign:'left',
    emptyMsg:'No hay registros para listar'
});	
capParticipantesCapacitacionesPaginador.bindStore(capParticipantesCapacitacionesDataStore);

capParticipantesCapacitacionesColumnModel = new Ext.grid.ColumnModel(
[new Ext.grid.RowNumberer(),{
        header: 'Tipo de Participación',
        dataIndex: 'tipo',
        width: 120,
        sortable: true,
        renderer: showTipo
    },{
        header: 'Capacitación',
        dataIndex: 'titulo',
        width: 350,
        sortable: false,
        renderer: showTooltip
    },{
        header: 'Fecha Cap.',
        dataIndex: 'fecha_cap',
        width: 150,
        sortable: true
    },{
        header: 'Tarea',
        dataIndex: 'id_tarea',
        width: 100,
        align:'center',
        sortable: true
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
    
capParticipantesCapacitacionesBuscador= new Ext.ux.grid.Search({
    iconCls:'icon-zoom',
//    readonlyIndexes:[''],
    disableIndexes:['fecha_cap','tipo'],
    align:'left',
    minChars:3
});

capParticipantesCapacitacionesGrid =  new Ext.grid.GridPanel({
    id: 'capParticipantesCapacitacionesGrid',
    store: capParticipantesCapacitacionesDataStore,
    cm: capParticipantesCapacitacionesColumnModel,
    enableColLock:false,
    trackMouseOver:true, 
    loadMask: true, 
    viewConfig: {
        forceFit: false
    },
    tbar: [],
    plugins:[capParticipantesCapacitacionesBuscador], 
    clicksToEdit:2,
    height:500,
    layout: 'fit',
    selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
    bbar: capParticipantesCapacitacionesPaginador
 });   


capGrillaCapacitacionesPanel = new Ext.Panel(
{
    id:'capGrillaCapacitacionesPanel',
    title: 'Capacitaciónes',
    columnWidth:0.50,
    autoScroll : true,
    layout: 'fit',
    items:[capParticipantesCapacitacionesGrid]
});

var altura=Ext.getBody().getSize().height - 75;
capGrillaCapacitacionesPanel.setHeight(altura);

function showTooltip(value, metaData,record){
   if ( value   != '')
   {
       metaData.attr += 'ext:qtip="'+ value + '"';
   }
   return value;
}

function showTipo (value,metaData,superData){
    var tipo=superData.json.tipo;
    switch (tipo)
    {
        case '1':
            metaData.attr = 'style="background-color:#088A08; color:#FFF;"';
            metaData.attr += 'ext:qtip="'+ value + '"';
            break;
        case '2':
            metaData.attr = 'style="background-color:##088A08; color:#FFF;"';
            metaData.attr += 'ext:qtip="'+ value + '"';
            break;

    }
    return value;
}
function showArchivosAdjuntos (value,metaData,record){
    if (value !="" && value !=null)
    {
//        console.log(record);
        var deviceDetail = record.get('archivos_qtip');
        console.log(deviceDetail);
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