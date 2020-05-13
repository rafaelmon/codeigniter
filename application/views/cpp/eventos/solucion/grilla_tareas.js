cppTareasDataStore = new Ext.data.Store({
    id: 'cppTareasDataStore',
    proxy: new Ext.data.HttpProxy({
        url: CARPETA + '/listado_tareas',
        method: 'POST'
    }),
    baseParams: {tampagina: TAM_PAGINA}, // this parameter is passed for any HTTP request
    reader: new Ext.data.JsonReader({
        root: 'rows',
        totalProperty: 'total',
        id: 'id_tarea'
    }, [
        {name: 'id_tarea', type: 'int', mapping: 'id_tarea'},
        {name: 'hallazgo', type: 'string', mapping: 'hallazgo'},
        {name: 'id_grado_crit', type: 'string', mapping: 'id_grado_crit'},
        {name: 'grado_crit', type: 'string', mapping: 'grado_crit'},
        {name: 'tarea', type: 'string', mapping: 'tarea'},
        {name: 'fecha_vto', type: 'string', mapping: 'fecha_vto'},
        {name: 'fecha_alta', type: 'string', mapping: 'fecha_alta'},
        {name: 'fecha_accion', type: 'string', mapping: 'fecha_accion'},
        {name: 'usuario_alta', type: 'string', mapping: 'usuario_alta'},
        {name: 'usuario_responsable', type: 'string', mapping: 'usuario_responsable'},
        {name: 'id_estado', type: 'int', mapping: 'id_estado'},
        {name: 'estado', type: 'string', mapping: 'estado'},
        {name: 'area', type: 'string', mapping: 'area'},
        {name: 'editada', type: 'string', mapping: 'editada'},
        {name: 'obs', type: 'string', mapping: 'obs'},
        {name: 'archivos',              type: 'string',     mapping: 'archivos'},
        {name: 'archivos_qtip',         type: 'string',     mapping: 'archivos_qtip'},
        {name: 'archivos_ext',         type: 'string',     mapping: 'archivos_ext'},
        {name: 'id_eficiencia', type: 'int', mapping: 'id_eficiencia'},
        {name: 'eficiencia', type: 'string', mapping: 'eficiencia'},
        {name: 'id_herramienta', type: 'int', mapping: 'id_herramienta'}
    ]),
//      sortInfo:{field: 'id_tarea', direction: "ASC"},
    remoteSort: true
});
//asigno el datastore al paginador
cppTareasPaginador= new Ext.PagingToolbar({
    pageSize: parseInt(TAM_PAGINA),
    displayInfo: true,
    beforePageText:'P�gina',
    afterPageText:'de {0}',
    displayMsg:'Mostrando {0} - {1} de <b>{2}</b>',
    firstText:'Primera P�gina',
    lastText:'�ltima P�gina',
    prevText:'Anterior',
    nextText:'Siguiente',
    refreshText:'Actualizar',
    buttonAlign:'left',
    emptyMsg:'No hay registros para listar'
});
cppTareasPaginador.bindStore(cppTareasDataStore);

cppBotonesTareasAction = new Ext.grid.ActionColumn({
		width: 15,
                editable:false,
                menuDisabled:true,
                header:'Verificar Ef.',
                hideable:false,
                align:'left',
                width:  90,
                tooltip:'Verificación de eficiencia',
                 hidden:false,
		items:[
                    {
                    icon:URL_BASE+'images/aprobar3.png',
                    iconCls :'col_accion',
                    tooltip:'Si Verifica',
                    hidden: true,
                    getClass:showBotonVerificar,
                    handler: clickBtnVerificarTarea
                },{
                    icon:URL_BASE+'images/rechazar.png',
                    iconCls :'col_accion',
                    tooltip:'No Verifica',
                    hidden: true,
                    getClass:showBotonVerificar,
                    handler: clickBtnRechazarTarea
                }]
});

cppTareasColumnModel = new Ext.grid.ColumnModel(
        [{
                header: '#',
                readOnly: true,
                dataIndex: 'id_tarea',
                width: 55,
                sortable: true,
                renderer: function (value, cell) {
                    cell.css = "readonlycell";
                    return value;
                },
                hidden: false
            }, {
                header: 'Fecha Alta',
                dataIndex: 'fecha_alta',
                sortable: false,
                width: 80,
                fixed: true,
                readOnly: true,
                align: 'center'
            }, {
//                header: 'Detalle del hallazgo',
//                dataIndex: 'hallazgo',
//                width: 220,
//                sortable: true,
//                renderer: showQtipHallazgo,
//                readOnly: true
//            }, {
                header: '&deg;Crit',
                dataIndex: 'grado_crit',
                tooltip: 'Grado de criticidad',
                sortable: true,
                width: 50,
                fixed: true,
                readOnly: true,
                renderer: showGrado,
                align: 'center'
            }, {
                header: 'Tarea a realizar',
                dataIndex: 'tarea',
                width: 220,
                sortable: true,
                renderer: showQtipTarea,
                readOnly: true
            }, {
                header: 'Fecha Limite',
                dataIndex: 'fecha_vto',
                sortable: true,
                width: 80,
                fixed: true,
//        renderer:showFecha,
                readOnly: true,
                align: 'center'
            }, {
                header: 'Estado Actual',
                dataIndex: 'estado',
                sortable: false,
                width: 80,
                fixed: true,
                readOnly: true,
                renderer: showEstadoTarea,
                align: 'center'
            }, {
                header: 'Usuario Solicitante',
                dataIndex: 'usuario_alta',
                sortable: true,
                width: 180,
                align: 'left'
            }, {
                header: 'Usuario Responsable',
                dataIndex: 'usuario_responsable',
                sortable: true,
                width: 180,
                align: 'left'
//            }, {
//                header: 'Area Responsable',
//                dataIndex: 'area',
//                sortable: true,
//                width: 100,
//                renderer: showQtipArea,
//                align: 'left'
            } ,{
                header: 'Fecha Accion',
                dataIndex: 'fecha_accion',
                sortable: false,
                width: 90,
                fixed: true,
                readOnly: true,
                align: 'center'
            }, {
                header: 'Eficiencia',
                dataIndex: 'eficiencia',
                sortable: false,
                width: 80,
                fixed: true,
                readOnly: true,
                renderer: showEficiencia,
                align: 'center'
            },cppBotonesTareasAction,{
                header: 'Archivos adjuntados',
                dataIndex: 'archivos',
                width:  350,
                readOnly: true,
                sortable: true,
                renderer:showArchivosAdjuntos,
                align:'left'
            }]
        );

cppTareasGridPanel = new Ext.grid.GridPanel({
    id: 'cppTareasGridPanel',
    store: cppTareasDataStore,
    cm: cppTareasColumnModel,
    title: 'Listado de Tareas - medidas correctivas',
    header: true,
    enableColLock: false,
//    columnWidth:.5,
    trackMouseOver: true, //que se coloree cuando pasas el mouse por encima
    loadMask: true, //que te ponga el loading cuando se esta generando el store
//        renderTo: 'grillita',
    viewConfig: {
        forceFit: false
    },
//    plugins: [],
//    height: 500,
        layout: 'fit',
    bbar: [cppTareasPaginador],
    tbar: [],
    region: 'center',
//    width: 400,
    collapsible: false,
    collapsed: false,
    border : false,
    ayoutConfig:{
                    animate:true
                },
});
cppTareasGridPanel.setHeight(ALT_INF);


function showQtipHallazgo(value, metaData, record) {
    var deviceDetail = record.get('hallazgo');
    metaData.attr += 'ext:qtip="' + deviceDetail + '"';
    return value;
}
function showQtipTarea(value, metaData, record) {
    var deviceDetail = record.get('tarea');
    metaData.attr += 'ext:qtip="' + deviceDetail + '"';
    return value;
}
function showQtipArea(value, metaData, record) {
    var deviceDetail = record.get('area');
    metaData.attr += 'ext:qtip="' + deviceDetail + '"';
    return value;
}

function showEstadoTarea (value,metaData,superData){
    var estado=superData.json.id_estado;
//    console.log(estado);
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
function showEficiencia (value,metaData,superData){
    var id=superData.json.id_eficiencia;
    switch (id)
    {
        case '0':
        case 0:
        metaData.attr = 'style="background-color:#01A9DB; color:#FFF;"';
        break;
        case '1':
        case 1:
        metaData.attr = 'style="background-color:#088A08; color:#FFF;"';
        break;
        case 2:
        case '2':
        metaData.attr = 'style="background-color:#DF0101; color:#FFF;"';
        break;
    }
    var deviceDetail = superData.get('obs');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
        
    return value;
}
function showGrado(value, metaData, superData) {
    var grado = superData.json.id_grado_crit;
    switch (grado)
    {
        case 1:
        case '1':
            metaData.attr = 'style="background-color:#DC0000; color:#FFF;"';
            break;
        case 2:
        case '2':
            metaData.attr = 'style="background-color:#DF7401; color:#FFF;"';
            break;
        case 3:
        case '3':
            metaData.attr = 'style="background-color:#088A08; color:#FFF;"';
            break;
        default:
            metaData.attr = 'style="background-color:#D0D0D0; color:#FFF;"';
    }
    return value;
}
function showBotonVerificar(value,metaData,record){
    var btn = BTN_VERIFICAR_TAREAS;
    var estado=record.json.id_estado;
    var efic=record.json.id_eficiencia;
    if(btn == 1 && estado==9 && efic==0)
        return 'x-grid-center-icon'; 
    else
        return 'x-hide-display';  
                       
};
function showArchivosAdjuntos (value,metaData,record)
{
//    console.log(value);
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
function clickBtnVerificarTarea(grid,rowIndex,colIndex,item ,event){
    var id=grid.getStore().getAt(rowIndex).json.id_tarea;
    var idCausa=grid.getStore().getAt(rowIndex).json.id_herramienta;
    Ext.MessageBox.confirm('Cancelar','¿Confirma que desea verificar como eficiente la tarea Número '+id+'?', 
    function(btn, text){
        if(btn=='yes'){
            msgProcess('Procesando...');
                Ext.Ajax.request({   
                    waitMsg: 'Por favor espere...',
                    url: CARPETA+'/verifica_tarea',
                    method: 'POST',
                    params: {
                        id_evento :ID_EVENTO_SELECT,
                        id_causa :idCausa,
                        id_tarea :id
                    }, 
                    success: function(response){
                    var result=eval(response.responseText);
                    switch(result){
                    case -1:
                        Ext.MessageBox.alert('Error','Error verificando permisos');
                        break;
                    case -2:
                        Ext.MessageBox.alert('Error','Datos inconsistentes');
                        break;
                    case -3:
                        Ext.MessageBox.alert('Error','Error actualizando datos');
                        break;
                    case -4:
                        Ext.MessageBox.alert('Error','Accion solo permitida para el supervisor');
                        break;
                    case 1:
                        Ext.MessageBox.alert('Operación OK','Tarea verificada!');
                        cppEventosDataStore.reload();    
                        cppTareasDataStore.reload();    
                        break;
                    default:
                        Ext.MessageBox.alert('Error',result.msg);
                        break;
                    }        
                    },
                    failure: function(response){
                        var result=eval(response.responseText);
                        Ext.MessageBox.alert('error','No se pudo conectar con la base de datos. Intente mas tarde');      
                    }                      
                });
        }
     });
  };
function clickBtnRechazarTarea(grid,rowIndex,colIndex,item ,event){
    var id=grid.getStore().getAt(rowIndex).json.id_tarea;
    var idCausa=grid.getStore().getAt(rowIndex).json.id_herramienta;
    Ext.MessageBox.confirm('Cancelar','¿Confirma que desea desestimar como <b>no eficiente</b> la tarea Número '+id+'?', 
    function(btn, text){
        if(btn=='yes'){
            msgProcess('Procesando...');
                Ext.Ajax.request({   
                    waitMsg: 'Por favor espere...',
                    url: CARPETA+'/rechazar_tarea',
                    method: 'POST',
                    params: {
                        id_evento :ID_EVENTO_SELECT,
                        id_causa :idCausa,
                        id_tarea :id
                    }, 
                    success: function(response){
                    var result=eval(response.responseText);
                    switch(result){
                    case -1:
                        Ext.MessageBox.alert('Error','Error verificando permisos');
                        break;
                    case -2:
                        Ext.MessageBox.alert('Error','Datos inconsistentes');
                        break;
                    case -3:
                        Ext.MessageBox.alert('Error','Error actualizando datos');
                        break;
                    case -4:
                        Ext.MessageBox.alert('Error','Accion solo permitida para el supervisor');
                        break;
                    case 1:
                        Ext.MessageBox.alert('Operación OK','Tarea rechazada');
                        cppEventosDataStore.reload();    
                        cppTareasDataStore.reload();    
                        break;
                    default:
                        Ext.MessageBox.alert('Error',result.msg);
                        break;
                    }        
                    },
                    failure: function(response){
                        var result=eval(response.responseText);
                        Ext.MessageBox.alert('error','No se pudo conectar con la base de datos. Intente mas tarde');      
                    }                      
                });
        }
     });
  };

//estadosTareaFiltro.on('select', filtrarGrilla);

//function filtrarGrilla( combo, record, index ){
//    var id_estado            =Ext.getCmp('estadosTareaFiltro').getValue();
//    var id_tipo_herramienta  =Ext.getCmp('tiposHerramientaTareaFiltro').getValue();
//    
//    var fields = [];
//        fields.push('id_estado');
//        fields.push('id_tipo_herramienta');
//    var values = [];
//    values.push(id_estado);
//    values.push(id_tipo_herramienta);
//	var encoded_array_f = Ext.encode(fields);
//	var encoded_array_v = Ext.encode(values);
//    cppTareasDataStore.setBaseParam('filtros',encoded_array_v);
//    cppTareasDataStore.load();
//}
//function clickBtnQuitarFiltros(){
//    Ext.getCmp('estadosTareaFiltro').reset();
//    Ext.getCmp('tiposHerramientaTareaFiltro').reset();
////    store1=Ext.getCmp('estadosTareaFiltro').getStore();
////    store1.setBaseParam('id_estado','');
////    store1.load();
//    cppTareasDataStore.setBaseParam('filtros','');
//    cppTareasDataStore.load();
//}
cppTareasGridPanel.on('celldblclick', abrir_popup_tareas_ri);
function abrir_popup_tareas_ri(grid, rowIndex, columnIndex, e) {
    var data = grid.store.data.items[rowIndex].data;
    var winDoc;
    var enc = ['<html>', '<div class="tabla_popup_grilla">'];
    var pie = ['<br class="popup_clear"/></div>', '</html>'];

//    if(data.tipo_wf==1)
//        data.revisores="No LLeva"
    for (var i in data)
        if (data[i] == '')
            data[i] = "&nbsp";
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
    var labels = [
        {titulo: "Tarea Nro", valor: data.id_tarea}
        , {titulo: "Estado", valor: data.estado}
        , {titulo: "Grado Crit.", valor: data.grado_crit}
        , {titulo: "Fecha alta", valor: data.fecha_alta}
        , {titulo: "Fecha L&iacute;mite", valor: data.fecha_vto}
        , {titulo: "Usuario Alta", valor: data.usuario_alta}
        , {titulo: "Usuario Resp.", valor: data.usuario_responsable}
        , {titulo: "Area Resp.", valor: data.area}
        , {titulo: "Hallazgo", valor: data.hallazgo}
        , {titulo: "Tarea", valor: data.tarea}

    ];
    var nodos=['<p><div class="titulo">Detetalle de tarea nro '+data.id_tarea+'<br></div></p>'];

    labels.forEach(function (entry) {
        var nodo = ['<p>',
            '<div class="col1">' + String(entry.titulo) + ':</div>',
            '<div class="col2">' + String(entry.valor) + '</div>',
            '</p>'];
        nodos.push(nodo.join(''));
    });
    var html = enc;
    html.push(nodos.join(''));
    html.push(pie.join(''));

    winDoc = new Ext.Window({
        title: 'Detalle de Tarea asociada a la causa nro '+data.id_herramienta,
        closable: true,
        modal: true,
        //closeAction: 'hide',
        width: 800,
        boxMinWidth: 600,
        height: 450,
        boxMinHeight: 300,
        plain: true,
        autoScroll: true,
        layout: 'absolute',
        html: html.join(''),
//                                items: [],
        buttons: [{
                text: 'Cerrar',
                handler: function () {
                    winDoc.hide();
                    winDoc.destroy();

                }
            }]
    });
    winDoc.show();
}
