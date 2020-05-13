rmcArchivosDataStore = new Ext.data.Store({
    id: 'rmcArchivosDataStore',
    proxy: new Ext.data.HttpProxy({
        url: CARPETA + '/listado_archivos',
        method: 'POST'
    }),
    baseParams: {tampagina: TAM_PAGINA}, // this parameter is passed for any HTTP request
    reader: new Ext.data.JsonReader({
        root: 'rows',
        totalProperty: 'total',
        id: 'id_archivo'
    }, [
        {name: 'id_archivo',    type: 'int',    mapping: 'id_archivo'},
        {name: 'usuario_alta',  type: 'string', mapping: 'usuario_alta'},
        {name: 'fecha_alta',    type: 'string', mapping: 'fecha_alta'},
        {name: 'titulo',        type: 'string', mapping: 'titulo'},
        {name: 'descr',         type: 'string', mapping: 'descr'},
        {name: 'archivo',       type: 'string', mapping: 'archivo'},
        {name: 'archivo_ext',   type: 'string', mapping: 'archivo_ext'}
    ]),
//      sortInfo:{field: 'id_archivo', direction: "ASC"},
    remoteSort: true
});
//asigno el datastore al paginador
var rmcArchivosPaginador= new Ext.PagingToolbar({
    pageSize: 8,
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
rmcArchivosPaginador.bindStore(rmcArchivosDataStore);

rmcArchivosColumnModel = new Ext.grid.ColumnModel(
        [{
                header: '#',
                readOnly: true,
                dataIndex: 'id_archivo',
                width: 55,
                sortable: true,
                renderer: function (value, cell) {
                    cell.css = "readonlycell";
                    return value;
                },
                hidden: false
            }, {
                header: 'Fecha',
                dataIndex: 'fecha_alta',
                sortable: false,
                width: 80,
                fixed: true,
                readOnly: true,
                align: 'center'
            },{
                header: 'Subido por',
                dataIndex: 'usuario_alta',
                sortable: true,
                width:  150,
                align:'left'
            }, {
                header: 'Título',
                dataIndex: 'titulo',
                width: 220,
                sortable: true,
                renderer: showQtipTitulo,
                readOnly: permiso_modificar
            }, {
                header: 'Descripción',
                dataIndex: 'descr',
                tooltip: 'Descripción',
                sortable: true,
                width: 350,
                fixed: true,
                readOnly: true,
                renderer: showQtipDesc,
                align: 'center'
            }, {
                header: 'Nombre Archivo',
                dataIndex: 'archivo',
                width: 220,
                sortable: true,
                renderer: showQtipTitulo,
                readOnly: permiso_modificar
            },{
                header: 'Archivo',
                dataIndex: 'id_archivo',
                width:  50,
                readOnly: true,
                sortable: true,
                renderer:showArchivoIco,
                align:'left'
            }
//      ,botonesRmcArchivosAction
            ]
        );

rmcArchivosListingGridPanel = new Ext.grid.GridPanel({
    id: 'rmcArchivosListingGridPanel',
    store: rmcArchivosDataStore,
    cm: rmcArchivosColumnModel,
    enableColLock: false,
    trackMouseOver: true, //que se coloree cuando pasas el mouse por encima
    loadMask: true, //que te ponga el loading cuando se esta generando el store
//        renderTo: 'grillita',
    viewConfig: {
        forceFit: false
    },
    plugins: [],
    clicksToEdit: 2,
    height: 500,
//        layout: 'fit',
    selModel: new Ext.grid.RowSelectionModel({singleSelect: false}),
    bbar: [rmcArchivosPaginador],
    tbar: []
});


rmcArchivosPanel = new Ext.Panel(
        {
            collapsible: true,
            collapsed: true,
            split: false,
            header: true,
            title: 'Listado de Archivos',
            region: 'south',
            height: 300,
            minSize: 100,
            maxSize: 350,
            margins: '0 5 5 5',
            html: '<p>panel inferior</p>',
            layout: 'fit',
            items: [rmcArchivosListingGridPanel]
        });
var altura = 250;
rmcArchivosListingGridPanel.setHeight(altura);

Ext.getCmp('browser').on('resize', function (comp) {
    rmcArchivosListingGridPanel.setWidth(this.getSize().width);
    rmcArchivosListingGridPanel.setHeight(Ext.getBody().getSize().height - 60);

});

function showQtipDesc(value, metaData, record) {
    var deviceDetail = record.get('descr');
    metaData.attr += 'ext:qtip="' + deviceDetail + '"';
    return value;
}
function showQtipTitulo(value, metaData, record) {
    var deviceDetail = record.get('titulo');
    metaData.attr += 'ext:qtip="' + deviceDetail + '"';
    return value;
}

function msgProcess(titulo) {
    Ext.MessageBox.show({
        title: titulo,
        msg: 'Procesando, por favor espere...',
        progress: true,
        progressText: 'Procesando...',
        width: 300,
        wait: true,
        waitConfig: {interval: 200}
    });
}
function showArchivoIco (value,metaData,record){
    if (value !="")
    {
        var nro = value;
        var deviceDetail = record.get('archivo');
        var exts = record.get('archivo_ext');
//        deviceDetail=deviceDetail.split(",");
//        exts=exts.split(",");
//        var archivos=value.split(",")
        var enlace="";
        var img="";
        switch (exts)
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
        enlace += "<a target='_blank' href='"+URL_BASE_SITIO+"archivos/ri/"+nro+"/"+deviceDetail+"'><img ext:qtip='"+deviceDetail+"' class='x-action-col-icon x-action-col-0  ' src='"+URL_BASE+"/images/"+img+"' alt=''></a>";
        return enlace;
    }
    else
        return value;
        
}