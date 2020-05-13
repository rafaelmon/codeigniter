omcDataStore = new Ext.data.Store({
    id: 'omcDataStore',
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
        {name: 'id_omc',        type: 'int',    mapping: 'id_omc'},        
        {name: 'id_observador', type: 'int',    mapping: 'id_observador'},        
        {name: 'observador',    type: 'string', mapping: 'observador'},
        {name: 'acomp1',        type: 'string', mapping: 'acomp1'},
        {name: 'acomp2',        type: 'string', mapping: 'acomp2'},
        {name: 'empresa',       type: 'string', mapping: 'empresa'},
        {name: 'sitio',         type: 'string', mapping: 'sitio'},
        {name: 'sector',        type: 'string', mapping: 'sector'},
        {name: 'tareas',        type: 'string', mapping: 'tareas'},
        {name: 'ar',            type: 'string', mapping: 'ar'},
        {name: 'fecha_alta',    type: 'string', mapping: 'fecha_alta'},
        {name: 'estado',        type: 'int',    mapping: 'estado'}
      ]),
//      sortInfo:{field: 'id_omc', direction: "ASC"},
      remoteSort: true
});

var omcPaginador= new Ext.PagingToolbar({
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
omcPaginador.bindStore(omcDataStore);

omcDataStore.load({params: {start: 0, limit: TAM_PAGINA}});

botonesOmcAction = new Ext.grid.ActionColumn({
        width: 40,
        editable:false,
        menuDisabled:true,
        header:'Acciónes',
        hideable:false,
        align:'center',
        tooltip:'Acciones',
//                 hidden: !permiso_btn_add,
        items:[{
            icon:URL_BASE+'images/aprobar2.png',
            iconCls :'col_accion',
            tooltip:'Aprobar',
            hidden: false,
            getClass:showBtnAprobar,
//                    hidden: (!permiso_alta||!rol_editor),
            handler: clickBtnAprobar 
        },{
            icon:URL_BASE+'images/add.gif',
            iconCls :'col_accion',
            tooltip:'Agregar tarea',
            hidden: false,
            getClass:showBtnNuevaTarea,
//                    hidden: (!permiso_alta||!rol_editor),
            handler: clickBtnNuevaTareaOmc 
        }]
});
    
omcBuscador= new Ext.ux.grid.Search({
    iconCls:'icon-zoom',
//    readonlyIndexes:['id_tarea', 'tarea','descripcion'],
    disableIndexes:['id_omc','fecha_alta','tareas','ar','e','empresa'],
    align:'right',
    minChars:3
});

omcColumnModel = new Ext.grid.ColumnModel(
    [{
        header: '#',
        readOnly: true,
        dataIndex: 'id_omc',
        width: 50,        
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
        header: 'Observador',
        dataIndex: 'observador',
        width:  160,
        sortable: true
      },{
        header: 'Acompañante 1',
        dataIndex: 'acomp1',
        width:  160,
        sortable: true
      },{
        header: 'Acompañante 2',
        dataIndex: 'acomp2',
        width:  160,
        sortable: true
      }
//      ,{
//        header: 'Empresa',
//        dataIndex: 'empresa',
//        width:  70,
//        sortable: true
//      }
      ,{
        header: 'Sitio',
        dataIndex: 'sitio',
        width:  200,
        sortable: true
      },{
        header: 'Sector',
        dataIndex: 'sector',
        width:  200,
        sortable: true
      },{
        header: 'Tareas',
        dataIndex: 'tareas',
        width:  90,
        renderer:showQTareas,
        sortable: false
      },{
        header: 'AR Eficiente?',
        tooltip:'¿Análisis de Riesgo Eficiente?',
        dataIndex: 'ar',
        width:  80,
        sortable: true,
        renderer:showAr,
        align:'center'
      },{
        header: 'Estado',
        dataIndex: 'estado',
        width:  80,
        sortable: true,
        renderer:showE,
        align:'center'
      },botonesOmcAction
     ]
    );

omcGridPanel =  new Ext.grid.GridPanel({
    id: 'omcGridPanel',
    store: omcDataStore,
    cm: omcColumnModel,
    enableColLock:false,
    trackMouseOver:true, 
    loadMask: true,
    viewConfig: {
        forceFit: false
    },
    plugins:[omcBuscador],
    bbar:[omcPaginador],
    tbar: [
        {
            text: 'Nueva OMC',
            tooltip: 'Crear nueva OMC...',
            iconCls:'add',                      // reference to our css
            handler: clickBtnNuevaOmc,
            hidden: !permiso_btn_add
        },'&emsp;|&emsp;',
        {
            text: 'Descargar listado',
            iconCls:'archivo_excel_ico',
            handler: clickBtnExcel
        }
    ]
});   

omcPanel = new Ext.Panel(
{
        collapsible: false,
        collapsed:false,
        split: true,
        title: 'Observaciones para la Mejora Continua (OMC)',
        region: 'center',
        height: 300,
        minSize: 100,
        maxSize: 350,
        margins: '0 5 5 5',
        html:'<p>panel superior</p>',
        layout: 'fit',
        items : [omcGridPanel]
});
    
    var altura=Ext.getBody().getSize().height - 60;
	omcGridPanel.setHeight(altura);
	
	Ext.getCmp('browser').on('resize',function(comp){
            omcGridPanel.setWidth(this.getSize().width);
            omcGridPanel.setHeight(Ext.getBody().getSize().height - 60);

	});

        
//function showQtipHallazgo(value, metaData,record){
//    var deviceDetail = record.get('hallazgo');
//    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
//    return value;
//}

function clickBtnNuevaTareaOmc (grid, rowIndex){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts){var result=parseInt(response.responseText);switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':
go_clickBtnNuevaTareaOmc(grid, rowIndex);break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}

function go_clickBtnNuevaTareaOmc(grid, rowIndex){
    var record = grid.getStore().getAt(rowIndex); 
    var id_omc=record.data.id_omc;
//    console.log(id_omc);
    displayOmcTareaFormWindow(id_omc);
}
function showBtnNuevaTarea(value,metaData,record){
    var usuario=record.json.id_observador;
    if(permiso_btn==usuario)
        return 'x-grid-center-icon';                
    else
        return 'x-hide-display';  
};
function showBtnAprobar(value,metaData,record){
    var estado=record.json.estado;
    if((estado==1 && permiso_btn_gr == 1))
        return 'x-grid-center-icon';                
    else
        return 'x-hide-display';  
};
function showAr (value,metaData,superData){
    var ar=superData.json.ar;
    switch (ar)
    {
        case '0': //No eficiente
        metaData.attr = 'style="background-color:#FF0000; color:#FFF;"'; //verde
        value="No";
        break;
        case '1': //Eficiente
        metaData.attr = 'style="background-color:#088A08; color:#FFF;"'; //Rojo
        value="Si";
        break;
//        case null: //vacio
//        metaData.attr = 'style="background-color:#DF7401; color:#FFF;"'; //Naranja
//        break;
      
    }
    return value;
}
function showE (value,metaData,superData){
    var estado=superData.json.estado;
//    console.log("algo"+estado);
    switch (estado)
    {
        case '1': //No Evaluada
        metaData.attr = 'style="background-color:#AAA1A1; color:#FFF;"'; //gris
        value="No Evaluada";
        break;
        case '2': //Valida
        metaData.attr = 'style="background-color:#0101DF; color:#FFF;"'; //Azul
        value="Aprobada";
        break;      
    }
    return value;
}
function showQTareas (value,metaData,superData){
    var q=superData.json.tareas;
    if (q>0)
    {
//        value='<a href="">'+value+'</a>';
        value='<span ext:qtip="Click para ver tareas">'+value+'<span style="font-size:9px; color:gray;"> (click para ver)</span></span>';
    }
    
    return value;
}
omcGridPanel.on('celldblclick', abrir_popup_wf);
function abrir_popup_wf(grid ,  rowIndex, columnIndex,  e){
    var data=grid.store.data.items[rowIndex].data;
    var winDoc;
    var enc=['<html>','<div class="tabla_popup_grilla">'];
    var pie=['<br class="popup_clear"/></div>','</html>'];
    
//    if(data.tipo_wf==1)
//        data.revisores="No LLeva"
    for (var i in data)
        if (data[i]=='')
            data[i]="&nbsp";
    var txt_ar;
    switch (data.ar)
    {
        case '0': //No eficiente
        txt_ar = '<div style="color:#FF0000;">No</div>'; //verde
        break;
        case '1': //Eficiente
        txt_ar = '<div style="color:#088A08;">Si</div>'; //Rojo
        break;
    }
    var labels=[
        {titulo:"OMC Nro",            valor:data.id_omc}
        ,{titulo:"Fecha alta",        valor:data.fecha_alta}
        ,{titulo:"Observador",        valor:data.observador}
        ,{titulo:"Acompañante",       valor:data.acomp1}
        ,{titulo:"Empresa",           valor:data.empresa}
        ,{titulo:"Sector",            valor:data.sector}
        ,{titulo:"Cant de tareas",    valor:data.tareas}
        ,{titulo:"¿AR eficiente?",    valor:txt_ar}
        
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
            title: 'Detalle de Tarea',
            closable: true,
            modal:true,
            //closeAction: 'hide',
            width: 600,
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
        var n=omcDataStore.totalLength;
        var txt;
        if (n>0)
        {
            if (n>1000)
                txt='El listado de OMC contiene <b>'+n+'</b> registros. El archivo a descargar contendrá una cantidad máxima de <b>1000</b> registros.<br><br>¿Desea continuar descargando el archivo?';
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
                            filtros : omcDataStore.baseParams.filtros,
                            query   : omcDataStore.baseParams.query,
                            fields  : omcDataStore.baseParams.fields,
                            sort    : omcDataStore.baseParams.sort,
                            dir     : omcDataStore.baseParams.dir
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