rpycDataStore = new Ext.data.Store({
    id: 'rpycDataStore',
    proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/listado', 
            method: 'POST'
    }),
//      baseParams:{tampagina: TAM_PAGINA}, 
      reader: new Ext.data.JsonReader({
        root: 'rows',
        totalProperty: 'total',
        id: 'id_historial'
      },[ 
        {name: 'id_rpyc',        type: 'int',       mapping: 'id_rpyc'},        
//        {name: 'titulo',         type: 'string',    mapping: 'titulo'},
//        {name: 'descripcion',    type: 'string',    mapping: 'descripcion'},
        {name: 'usuario_alta',   type: 'string',    mapping: 'usuarioAlta'},        
        {name: 'q_usuarios',     type: 'int',       mapping: 'q_usuarios'},        
        {name: 'q_contratistas', type: 'int',       mapping: 'q_contratistas'},        
        {name: 'tareas',         type: 'int',       mapping: 'tareas'},        
        {name: 'ttareas',         type: 'string',       mapping: 'ttareas'},        
//        {name: 'programada',     type: 'int',       mapping: 'programada'},        
//        {name: 'realizada',      type: 'int',       mapping: 'realizada'},        
        {name: 'sectores',         type: 'string',    mapping: 'sectores'},        
        {name: 'fecha_alta',     type: 'string',    mapping: 'fecha_alta'}
      ]),
//      sortInfo:{field: 'id_rpyc', direction: "ASC"},
      remoteSort: true
});
var rpycPaginador= new Ext.PagingToolbar({
    pageSize: TAM_PAGINA,
    displayInfo: true,
    beforePageText:'Página',
    afterPageText:'de {0}',
    displayMsg:'Mostrando {0} - {1} de <b>{2}</b>',
    firstText:'Priemra Página',
    lastText:'Última Página',
    prevText:'Anterior',
    nextText:'Siguiente',
    refreshText:'Actualizar',
    buttonAlign:'left',
    emptyMsg:'No hay registros para listar'
});
rpycPaginador.bindStore(rpycDataStore);

botonesRpycAction = new Ext.grid.ActionColumn({
		width: 15,
                editable:false,
                menuDisabled:true,
                header:'Acciónes',
                hideable:false,
                align:'center',
                width:  90,
                tooltip:'Crear tareas para la RPyC...',
                 hidden:false,
		items:[{
                    icon:URL_BASE+'images/add.gif',
                    iconCls :'col_accion',
                    tooltip:'Agregar tarea',
                    hidden: false,
                    getClass:showBtnNuevaTareaRpyc,
//                    hidden: (!permiso_alta||!rol_editor),
                    handler: clickBtnNuevaTareaRpyc 
                }]
});
    
rpycBuscador= new Ext.ux.grid.Search({
    iconCls:'icon-zoom',
//    readonlyIndexes:['id_tarea', 'tarea','descripcion'],
    disableIndexes:['id_rpyc','fecha_alta','tareas','q_usuarios','q_contratistas','ttareas'],
    align:'right',
    minChars:3
});

rpycColumnModel = new Ext.grid.ColumnModel(
    [{
        header: '#',
        readOnly: true,
        dataIndex: 'id_rpyc',
        width: 45,        
        sortable: true,
        renderer: function(value, cell){
            cell.css = "readonlycell";
            return value;
        },
        hidden: false
      },{
        header: 'Fecha',
        dataIndex: 'fecha_alta',
        sortable: true,
        width:  100,
        fixed:true,
        readOnly: true,
        align:'center'
      },{
        header: 'Usuario Alta',
        dataIndex: 'usuario_alta',
        width:  180,
        sortable: true
      },
//      {
//        header: 'Titulo',
//        dataIndex: 'titulo',
//        width:  250,
//        sortable: true
//      },
//      {
//        header: 'Descripcion',
//        dataIndex: 'descripcion',
//        width:  320,
//        sortable: true
//      },
      {
        header: '&Aacute;reas que participaron',
        dataIndex: 'sectores',
        width:  450,
        sortable: false
      },{
        header: 'Usuarios',
        dataIndex: 'q_usuarios',
        width:  70,
        sortable: true,
        align:'center'
      },{
        header: 'Contratistas',
        dataIndex: 'q_contratistas',
        width:  70,
        sortable: true,
        align:'center'
      },/*{
        header: 'Programada',
        tooltip:'¿Fue la RPyC programada?',
        dataIndex: 'programada',
        width:  80,
        sortable: true,
        renderer:showSiNo,
        align:'center'
      },{
        header: 'Realizada',
        tooltip:'¿Fue la RPyC realizada?',
        dataIndex: 'realizada',
        width:  80,
        sortable: true,
        renderer:showSiNo,
        align:'center'
      },*/{
        header: 'Tareas',
        dataIndex: 'ttareas',
        width:  70,
        sortable: true,
        align:'center'
      },botonesRpycAction
     ]
    );

rpycDataStore.load({params: {start: 0, limit: TAM_PAGINA}});

rpycGridPanel =  new Ext.grid.GridPanel({
    id: 'rpycGridPanel',
    store: rpycDataStore,
    cm: rpycColumnModel,
    enableColLock:false,
    trackMouseOver:true, 
    loadMask: true,
    viewConfig: {
        forceFit: false
    },
    plugins:[rpycBuscador],
    bbar:[rpycPaginador],
    tbar: [
        {
            text: 'Nueva RPyC',
            tooltip: 'Crear nueva RPyC...',
            iconCls:'add',                      // reference to our css
            handler: clickBtnNuevaRpyc,
            hidden: !permiso_alta
        },'&emsp;|&emsp;',
        {
            text: 'Descargar listado',
            iconCls:'archivo_excel_ico',
            handler: clickBtnExcel
        }
    ]
});   

rpycPanel = new Ext.Panel(
{
        collapsible: false,
        collapsed:false,
        split: true,
        title: 'Reuniones de Participación y Consulta (RPyC)',
        region: 'center',
        height: 300,
        minSize: 100,
        maxSize: 350,
        margins: '0 5 5 5',
        html:'<p>panel superior</p>',
        layout: 'fit',
        items : [rpycGridPanel]
});
    
    var altura=Ext.getBody().getSize().height - 60;
	rpycGridPanel.setHeight(altura);
	
	Ext.getCmp('browser').on('resize',function(comp){
            rpycGridPanel.setWidth(this.getSize().width);
            rpycGridPanel.setHeight(Ext.getBody().getSize().height - 60);

	});

        
//function showQtipHallazgo(value, metaData,record){
//    var deviceDetail = record.get('hallazgo');
//    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
//    return value;
//}

function clickBtnNuevaTareaRpyc (grid, rowIndex){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts){var result=parseInt(response.responseText);switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':
go_clickBtnNuevaTareaRpyc(grid, rowIndex);break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}

function go_clickBtnNuevaTareaRpyc(grid, rowIndex){
    var record = grid.getStore().getAt(rowIndex); 
    var id_rpyc=record.data.id_rpyc;
//    console.log(id_rpyc);
    displayRpycTareaFormWindow(id_rpyc);
}
function showBtnNuevaTareaRpyc(value,metaData,record){
    var usuario=record.json.id_usuario_alta
    if(permiso_btn==usuario)
        return 'x-grid-center-icon';                
    else
        return 'x-hide-display';  
};
function showSiNo (value,metaData,superData){
//    console.log(value);
    switch (value)
    {
        case 0: 
            metaData.attr = 'style="background-color:#FF0000; color:#FFF;"'; //verde
            value="No";
            break;
        case 1: 
            metaData.attr = 'style="background-color:#088A08; color:#FFF;"'; //Rojo
            value="Si";
        break;
//        case null: //vacio
//        metaData.attr = 'style="background-color:#DF7401; color:#FFF;"'; //Naranja
//        break;
      
    }
    return value;
}
rpycGridPanel.on('celldblclick', abrir_popup_rpyc);
function abrir_popup_rpyc(grid ,  rowIndex, columnIndex,  e){
    var data=grid.store.data.items[rowIndex].data;
    var winDoc;
    var enc=['<html>','<div class="tabla_popup_grilla">'];
    var pie=['<br class="popup_clear"/></div>','</html>'];
    
//    if(data.tipo_wf==1)
//        data.revisores="No LLeva"
    for (var i in data)
        if (data[i]=='')
            data[i]="&nbsp";
    var labels=[
        {titulo:"RPyC Nro",             valor:data.id_rpyc}
        ,{titulo:"Fecha alta",          valor:data.fecha_alta}
        ,{titulo:"Usuario Alta",        valor:data.usuario_alta}
        ,{titulo:"Sectores",            valor:data.sectores}
        ,{titulo:"Cant. usuarios",      valor:data.q_usuarios}
        ,{titulo:"Cant. Contrat.",      valor:data.q_contratistas}
        ,{titulo:"Tareas asoc.",        valor:data.tareas}
        
    ];
    var nodos= [];
    
    labels.forEach(function(entry){
        var nodo=['<p>',
                    '<div class="col1">'+String(entry.titulo)+':</div>',
                    '<div class="col2">'+String(entry.valor)+'</div>',
                '</p>'];
        nodos.push(nodo.join(''));
    });
    var html=enc;
    html.push(nodos.join(''));
    html.push(pie.join(''));

    winDoc = new Ext.Window({
            title: 'Detalle de RPyC ',
            closable: true,
            modal:true,
            //closeAction: 'hide',
            width: 800,
            boxMinWidth:600,
            height: 300,
            boxMinHeight:250,
            plain: true,
            autoScroll:true,
            layout: 'absolute',
            html: html.join(''),
//                                items: [],
            buttons: [{
                    text: 'Cerrar',
                    handler: function(){
                            winDoc.hide();
                            winDoc.destroy();

                    }
            }]
    });
    winDoc.show();
}

function clickBtnExcel (){Ext.Ajax.request({ url: LINK_GENERICO+'/sesion',method: 'POST',waitMsg: 'Por favor espere...',success: function(response, opts) {var result=parseInt(response.responseText);
switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':go_clickBtnExcel();break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}

function go_clickBtnExcel(){
        var n=rpycDataStore.totalLength;
        var txt;
        if (n>0)
        {
            if (n>1000)
                txt='El listado de RPyC contiene <b>'+n+'</b> registros. El archivo a descargar contendrá una cantidad máxima de <b>1000</b> registros.<br><br>¿Desea continuar descargando el archivo?';
            else
                txt='¿Confirma la descarga del archivo?';
            Ext.MessageBox.confirm('Confirmar',txt, function(btn, text){
                if(btn=='yes'){
                    msgProcess('Generando...');
                    var body = Ext.getBody();
                    var downloadFrame = body.createChild({
                         tag: 'iframe',
                         cls: 'x-hidden',
                         id: 'app-upload-frame',
                         name: 'uploadframe'
                     });
                    var downloadForm = body.createChild({
                         tag: 'form',
                         cls: 'x-hidden',
                         id: 'app-upload-form',
                         target: 'app-upload-frame'
                     });
                    Ext.Ajax.request({
                        url: CARPETA+'/excel/',
                        timeout:10000,
                        scope :this,
                        params: {
                            filtros : rpycDataStore.baseParams.filtros,
                            query   : rpycDataStore.baseParams.query,
                            fields  : rpycDataStore.baseParams.fields,
                            sort    : rpycDataStore.baseParams.sort,
                            dir     : rpycDataStore.baseParams.dir
                        },
                        form: downloadForm,
                        callback:function (){
                        Ext.Msg.alert('Status', 'Datos generados correctamente');
                    },
                        isUpload: true,
                         success: function(response, opts) {
                         },
                        failure: function(response, opts) {
                         }
                    });
                    Ext.Msg.alert('Descarga de archivo', 'Descarga en proceso. Por Favor aguarde a que se abra la ventana de descarga...');
                }
            });
        }
        else
            Ext.Msg.alert('Descarga de archivo', 'No hay registros para generar el archivo. Por favor redefina los filtros de la grilla');
    }