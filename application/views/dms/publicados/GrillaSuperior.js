tiposDocJS = new Ext.data.JsonStore({
	url: CARPETA+'/combo_td',
	root: 'rows',
	fields: ['id_td', 'td']
});
tiposDocJS.load();
tiposDocJS.on('load' , function(  js , records, options ){
	var tRecord = Ext.data.Record.create(
		{name: 'id_td', type: 'int'},
		{name: 'td', type: 'string'}
	);
	var myNewT = new tRecord({
		id_td: -1,
		td   : 'Todos'
	});
	tiposDocJS.insert( 0, myNewT);	
} );

tiposDocFiltro = new Ext.form.ComboBox({
    id:'tiposDocFiltro',
    forceSelection : true,
    value: 'Todos',
    store: tiposDocJS,
    editable : false,
    displayField: 'td',
    valueField:'id_td',
    allowBlank: false,
    width:  200,
    selectOnFocus:true,
    triggerAction: 'all'
});	
gerenciasJS = new Ext.data.JsonStore({
	url: CARPETA+'/combo_gerencias',
	root: 'rows',
	fields: ['id_gerencia', 'gerencia']
});
gerenciasJS.load();
gerenciasJS.on('load' , function(  js , records, options ){
	var tRecord = Ext.data.Record.create(
		{name: 'id_gerencia', type: 'int'},
		{name: 'gerencia', type: 'string'}
	);
	var myNewT = new tRecord({
		id_gerencia: -1,
		gerencia   : 'Todas'
	});
	gerenciasJS.insert( 0, myNewT);	
} );

gerenciasFiltro = new Ext.form.ComboBox({
    id:'gerenciasFiltro',
    forceSelection : true,
    value: 'Todas',
    store: gerenciasJS,
    editable : false,
    displayField: 'gerencia',
    valueField:'id_gerencia',
    allowBlank: false,
    width:  350,
    selectOnFocus:true,
    triggerAction: 'all'
});	
			


publicadosDataStore = new Ext.data.Store({
    id: 'publicadosDataStore',
    proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/listado', 
            method: 'POST'
    }),
      baseParams:{
          limit: TAM_PAGINA
//          filtro_id_td: Ext.getCmp('tiposDocFiltro').getValue(),
//          filtro_id_gcia: Ext.getCmp('gerenciasFiltro').getValue()
            }, // this parameter is passed for any HTTP request
      reader: new Ext.data.JsonReader({
        root: 'rows',
        totalProperty: 'total',
        id: 'id_documento'
      },[ 
        {name: 'id_documento',          type: 'int',    mapping: 'id_documento'},        
        {name: 'documento',             type: 'string', mapping: 'documento'},
        {name: 'detalle',               type: 'string', mapping: 'detalle'},
        {name: 'td',                    type: 'string', mapping: 'td'},
        {name: 'archivo',               type: 'string', mapping: 'archivo'},
        {name: 'codigo',                type: 'string', mapping: 'codigo'},
        {name: 'version',               type: 'string', mapping: 'version'},
        {name: 'gerencia',              type: 'string', mapping: 'gerencia'},
        {name: 'id_gerencia_origen',    type: 'int',    mapping: 'id_gerencia_origen'},
        {name: 'alce',                  type: 'string', mapping: 'alce'},
        {name: 'editor',                type: 'string', mapping: 'editor'},
        {name: 'f_publicacion',         type: 'string', mapping: 'f_publicacion'}
      ]),
      sortInfo:{field: 'f_publicacion', direction: "desc"},
      remoteSort: true
    });
   paginador.bindStore(publicadosDataStore);
    paginador.on('beforechange', setParams);
    
    function setParams(){
        publicadosDataStore.setBaseParam('filtro_id_td',Ext.getCmp('tiposDocFiltro').getValue());
        publicadosDataStore.setBaseParam('filtro_id_gcia',Ext.getCmp('gerenciasFiltro').getValue());
    };
   
buscador= new Ext.ux.grid.Search({
    iconCls:'icon-zoom',
//    readonlyIndexes:['id_documento', 'documento','descripcion'],
    disableIndexes:['id_documento','td','f_publicacion','archivo'],
    align:'right',
    minChars:3
});

botonesImprmirAction = new Ext.grid.ActionColumn({
                editable:false,
                menuDisabled:true,
                header:'Ver | Imprimir | Descargar',
                hideable:false,
                align:'center',
                width:  70,
                tooltip:'Impresión por pantalla de CC y CNC',
                 hidden:false,
		items:[{
                    icon:URL_BASE+'images/preview1.png',
                    iconCls :'col_accion',
//                    getClass: showBtnModificar,
                    tooltip:'Previsualizar',
                    handler: clickBtnPreviewPub
                },{
                    icon:URL_BASE+'images/print.png',
                    iconCls :'col_accion',
//                    getClass: showBtnModificar,
                    tooltip:'Imprimir',
                    handler: imprimeCNC
                },{
                    icon:URL_BASE+'images/doc_pdf.png',
                    iconCls :'col_accion',
//                    getClass: showBtnModificar,
                    tooltip:'Descargar',
                    handler: clickBtnDescDocPub
                }]
});
botonesEditorYGrAction = new Ext.grid.ActionColumn({
                editable:false,
                menuDisabled:true,
                header:'Acciones',
                hideable:false,
                align:'center',
                width:  40,
                //tooltip:'Hacer Obsoleto o nueva versión de un documento',
                hidden:!permiso_col_acc,
		items:[{
                    icon:URL_BASE+'images/doc_obsoleto.png',
                    iconCls :'col_accion',
                    getClass: showBtnRowAccion,
                    tooltip:'Hacer Obsoleto',
                    handler: clickBtnHacerObsoleto
                },{
                    icon:URL_BASE+'images/nueva_version3.png',
                    iconCls :'col_accion',
                    getClass: showBtnRowAccion,
                    tooltip:'Nueva Versión',
                    handler: clickBtnNuevaVersion
                },{
                    icon:URL_BASE+'images/transfer.png',
                    iconCls :'col_accion',
                    getClass: showBtnRowAccion,
                    tooltip:'Transferir Documento',
                    handler: BtntransferirDoc
                }]
});

//botonesAccionesAction = new Ext.grid.ActionColumn({
//		width: 15,
//                editable:false,
//                menuDisabled:true,
//                header:'Acciones',
//                hideable:false,
//                align:'center',
//                width:  50,
////                tooltip:'',
//                 hidden:false,
//		items:[{
//                    icon:URL_BASE+'images/obs_grabar.png',
////                    getClass: showBtnModificar,
//                    tooltip:'Grabar observacion'
////                    handler: clickBtnCC
//                }]
//});

  
publicadosColumnModel = new Ext.grid.ColumnModel(
    [{
        header: '#',
        readOnly: true,
        dataIndex: 'id_documento',
        width: 40,        
        sortable: true,
        renderer: function(value, cell){
            cell.css = "readonlycell";
            return value;
        },
        hidden: false
      },{
        header: 'Documento',
        dataIndex: 'documento',
        width:  120,
        sortable: true,
        renderer: showDocQtip
      },{
        header: 'Fecha Vigencia',
        dataIndex: 'f_publicacion',
        sortable: true,
        width:  100,
        fixed:true,
        readOnly: true,
        align:'center'
      },{
        name:'td',
        header: 'Tipo documento',
        dataIndex: 'td',
        sortable: true,
        width:  100
      },{
        name:'codigo',
        header: 'Código',
        dataIndex: 'codigo',
        sortable: true,
        width:  70,
        renderer :showBold
      },{
        name:'descripcion',
        header: 'Descripcion',
        dataIndex: 'detalle',
        sortable: true,
        width:  200,
        renderer :showDescQtip
      },botonesEditorYGrAction,botonesImprmirAction/*,botonesAccionesAction*/]
    );
  
publicadosGrid =  new Ext.grid.GridPanel({
        id: 'publicadosGrid',
//        title: 'Listado de Documentos publicados a la fecha',
        store: publicadosDataStore,
        cm: publicadosColumnModel,
        enableColLock:false,
        trackMouseOver:true, //que se coloree cuando pasas el mouse por encima
        loadMask: true, //que te ponga el loading cuando se esta generando el store
        viewConfig: {
            forceFit: true
        },
        plugins:[buscador],
        height:500,
        layout: 'fit',
        selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
        bbar:[paginador],
      tbar: ['<b>Filtrar por:</b>&emsp; Tipo de Documento',tiposDocFiltro,'&emsp;&emsp;',' Gerencia',gerenciasFiltro,'&emsp;&emsp;',
        {
                text: 'Quitar Filtros',
    //            tooltip: 'e...',
                iconCls:'quitar_filtros',
                handler: clickBtnQuitarFiltrosDocs
        },'&emsp;|&emsp;',
        {
                text: 'Descargar listado',
    //            tooltip: 'e...',
                hidden: !permiso_gr,
                iconCls:'archivo_excel_ico',
                handler: clickBtnExcel
            }
        ]
    });   

publicadosDataStore.load({params: {start: 0, limit: TAM_PAGINA}});

//recargo la grilla cuando el combo del filtro sea seleccionado
tiposDocFiltro.on('select', filtrarGrilla);
gerenciasFiltro.on('select', filtrarGrilla);

function filtrarGrilla (combo, record, index){
    publicadosDataStore.setBaseParam('filtro_id_td',tiposDocFiltro.getValue());
    publicadosDataStore.setBaseParam('filtro_id_gcia',gerenciasFiltro.getValue());
    
    publicadosDataStore.load({params: {start: 0, limit: TAM_PAGINA}});	
};
function clickBtnQuitarFiltrosDocs (grid,rowIndex,colIndex,item,event){
    Ext.Ajax.request({ 
        url: LINK_GENERICO+'/sesion',
        method: 'POST',
        success: function(response, opts) {
            var result=parseInt(response.responseText);
            switch (result)
            {
                case 0:
                case '0':
                    location.assign(URL_BASE_SITIO+"admin");
                    break;
                case 1:
                case '1':
                    go_clickBtnQuitarFiltrosDocs(grid,rowIndex,colIndex,item,event);
                    break;
            }
      },
        failure: function(response) {
            location.assign(URL_BASE_SITIO+"admin");
        }
    });
}
function go_clickBtnQuitarFiltrosDocs(){
    Ext.getCmp('tiposDocFiltro').reset();
    Ext.getCmp('gerenciasFiltro').reset();
//    store1=Ext.getCmp('estadosTareaFiltro').getStore();
//    store1.setBaseParam('id_estado','');
//    store1.load();
    publicadosDataStore.setBaseParam('filtro_id_td',Ext.getCmp('tiposDocFiltro').getValue());
    publicadosDataStore.setBaseParam('filtro_id_gcia',Ext.getCmp('gerenciasFiltro').getValue());
    publicadosDataStore.load();
}
   
  	var altura=Ext.getBody().getSize().height - 60;
	publicadosGrid.setHeight(altura);
	
	Ext.getCmp('browser').on('resize',function(comp){
		publicadosGrid.setWidth(this.getSize().width);
		publicadosGrid.setHeight(Ext.getBody().getSize().height - 60);

	});
 publicadosSuperiorPanel = new Ext.Panel(
{
        collapsible: false,
        split: false,
        header: true,
        title: 'Listado de documentos publicados',
        region:'center',
        height: 300,
        minSize: 100,
        maxSize: 350,	
        margins: '0 5 5 5',
        html:'<p>panel superior</p>',
        layout: 'fit',
        items:[publicadosGrid]
});       

function showDocument (value,metaData,row){
    var enlace;
    if (value!="" && row.data.habilitado==1)
        enlace = "<a target='_blank' href='"+URL_BASE_SITIO+"uploads/smn/documentos/"+value+".dot'><img ext:qtip='Descargar' class='x-action-col-icon x-action-col-0  ' src='"+URL_BASE+"/images/document_word.png' alt=''></a>";
    else
        enlace = "";
    return enlace;
    }
function showDescQtip(value, metaData,record){
    var deviceDetail = record.get('detalle');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
    return value;
}
function showDocQtip(value, metaData,record){
    var deviceDetail = record.get('documento');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
    return value;
}

function showBold (value,metaData){
     metaData.attr ='style="font-weight:bold;"';
     return value;
}
 function imprimeCNC(grid,rowIndex,colIndex,item ,event){
     var id=grid.getStore().getAt(rowIndex).json.id_documento;
//     window.open(CARPETA+'/preview_c/'+id)
     
     new Ext.Window({
        title: 'Documento ',
        height: 400,
        width: 600,
        bodyCfg: {
            tag: 'iframe',
            src: CARPETA+'/imprime/'+id,
            style: 'border: 0 none'
        }
    }).show();
  };
// function imprimeCC(grid,rowIndex,colIndex,item ,event){
//     var id=grid.getStore().getAt(rowIndex).json.id_documento;
////     window.open(CARPETA+'/preview_c/'+id)
//     
//     new Ext.Window({
//        title: 'Documento ',
//        height: 400,
//        width: 600,
//        bodyCfg: {
//            tag: 'iframe',
//            src: CARPETA+'/preview_cc/'+id,
//            style: 'border: 0 none'
//        }
//    }).show();
//  };
 function clickBtnPreviewPub(grid,rowIndex,colIndex,item ,event){
     var id=grid.getStore().getAt(rowIndex).json.id_documento;
    var nom=grid.getStore().getAt(rowIndex).json.codigo;
     window.open(CARPETA+'/preview/'+id+"/"+nom)
     
//     new Ext.Window({
//        title: 'My PDF',
//        height: 400,
//        width: 600,
//        bodyCfg: {
//        tag: 'iframe',
//        src: CARPETA+'/preview'+id,
//        style: 'border: 0 none'
//        }
//    }).show();
  };
 function clickBtnDescDocPub(grid,rowIndex,colIndex,item ,event){
     var id=grid.getStore().getAt(rowIndex).json.id_documento;
    var nom=grid.getStore().getAt(rowIndex).json.codigo;
     window.open(CARPETA+'/descargar/'+id+"/"+nom)
  };
 function clickBtnHacerObsoleto(grid,rowIndex,colIndex,item ,event){
    var codigo=grid.getStore().getAt(rowIndex).json.codigo;
    var doc=grid.getStore().getAt(rowIndex).json.documento;
    Ext.MessageBox.confirm('Obsoleto','¿Confirma que desea convertir en obsoleto y quitar de publicado el documento?<br><i>Código: </i><b>'+codigo+'</b><br><i>Título :</i><b> '+doc+'</b>', function(btn, text){
        if(btn=='yes'){
            var id=grid.getStore().getAt(rowIndex).json.id_documento;
            msgProcess('Procesando...');
                Ext.Ajax.request({   
                    waitMsg: 'Por favor espere...',
                    url: CARPETA+'/obsoleto',
                    method: 'POST',
                    params: {
                    id:id  
                    }, 
                    success: function(response){              
                    var result=eval(response.responseText);
                    switch(result.error){
                    case 0:
                        Ext.MessageBox.alert('Operación OK','El documento quedo obsoleto');
                        publicadosDataStore.reload();
                        break;
                    default:
                        Ext.MessageBox.alert('Error',result.error);
                        break;
                    }        
                    },
                    failure: function(response){
            //          var result=response.responseText;
                        Ext.MessageBox.alert('Error','No se pudo conectar con el servidor. Intente m&aacute;s tarde');         
                    }                      
                });
        }
     });
  };
 function clickBtnNuevaVersion(grid,rowIndex,colIndex,item ,event){
    var codigo=grid.getStore().getAt(rowIndex).json.codigo;
    var doc=grid.getStore().getAt(rowIndex).json.documento;
    Ext.MessageBox.confirm('Nueva Version','¿Confirma que desea comenzar una nueva version del documento?<br><i>Código: </i><b>'+codigo+'</b><br><i>Título :</i><b> '+doc+'</b>', function(btn, text){
        if(btn=='yes'){
            var id=grid.getStore().getAt(rowIndex).json.id_documento;
            msgProcess('Procesando...');
                Ext.Ajax.request({   
                waitMsg: 'Por favor espere...',
                url: CARPETA+'/nuevaVersion',
                method: 'POST',
                params: {
                id:id  
                }, 
                success: function(response){              
                var result=eval(response.responseText);
                switch(result.error){
                case 0:
                    var nuevo=result.id;
                    Ext.MessageBox.alert('Operación OK','Se ha creado una nueva version del documento y se encuentra disponible en la bandeja de trabajo del editor bajo el identificador Nro:<b>'+nuevo+'</b>');
                    publicadosDataStore.reload();
                    break;
                default:
                    Ext.MessageBox.alert('Error',result.error);
                    break;
                }        
                },
                failure: function(response){
        //          var result=response.responseText;
                Ext.MessageBox.alert('Error','No se pudo conectar con el servidor. Intente m&aacute;s tarde');         
                }                      
            });
        }
     });
  };
    function showBtnRowAccion(value,metaData,record){
        var id=record.json.id_editor;
        if (permiso_gr) {
            return 'x-grid-center-icon';                
        } else {
            if (id!=permiso_row_acc)
                return 'x-hide-display';  
            else
                return 'x-grid-center-icon';                

        }
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
    
    publicadosGrid.on('celldblclick', abrir_popup_documentoPub);
    function abrir_popup_documentoPub(grid ,  rowIndex, columnIndex,  e){
    var data=grid.store.data.items[rowIndex].data;
                var panelContentDoc;
                var winDoc;
                var detalle=(data.detalle!="")?data.detalle:"&nbsp";
                var enc=['<html>','<div class="tabla_popup_grilla">'];
                var pie=['<br class="popup_clear"/></div>','</html>'];
                var nodos=[
                            '<p>',
                                '<div class="titulo">Detalle Documento Código '+data.codigo+'<br></div>',
                            '</p>',
                            '<p>',
                                '<div class="col1">Documento:</div>',
                                '<div class="col2">'+data.documento+'</div>',
                            '</p>',
                            '<p>',
                                '<div class="col1">Fecha Publicado:</div>',
                                '<div class="col2">'+data.f_publicacion+'</div>',
                            '</p>',
                            '<p>',
                                '<div class="col1">Detalle:</div>',
                                '<div class="col2">'+detalle+'</div>',
                            '</p>',
                            '<p>',
                                '<div class="col1">Version:</div>',
                                '<div class="col2">'+data.version+'</div>',
                            '</p>',
                            '<p>',
                                '<div class="col1">Tipo:</div>',
                                '<div class="col2">'+data.td+'</div>',
                            '</p>',
                            '<p>',
                                '<div class="col1">Origen:</div>',
                                '<div class="col2">'+data.gerencia+'</div>',
                            '</p>',
                            '<p>',
                                '<div class="col1">Alcance:</div>',
                                '<div class="col2">'+data.alce+'</div>',
                            '</p>',
                            '<p>',
                                '<div class="col1">Editor:</div>',
                                '<div class="col2">'+data.editor+'</div>',
                            '</p>',
                            '<p>',
                                '<div class="col1">Nro control:</div>',
                                '<div class="col2">'+1000000+parseInt(data.id_documento)+'</div>',
                            '</p>'
                            
                ];
                
                
                var html = enc.concat(nodos);
                var html = html.concat(pie);

                        winDoc = new Ext.Window({
                                title: 'Documento '+data.codigo,
                                closable: true,
                                modal:true,
                                //closeAction: 'hide',
                                width: 600,
                                boxMinWidth:600,
                                height: 350,
                                boxMinHeight:300,
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
//                };
                winDoc.show();

    }
    
    function clickBtnExcel (){Ext.Ajax.request({ url: LINK_GENERICO+'/sesion',method: 'POST',waitMsg: 'Por favor espere...',success: function(response, opts) {var result=parseInt(response.responseText);
    switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':go_clickBtnExcel();break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}

    function go_clickBtnExcel(){
        var n=publicadosDataStore.totalLength;
        var txt;
        if (n>0)
        {
            if (n>1000)
                txt='El listado de tareas que usted filtro contiene <b>'+n+'</b> registros. El archivo a descargar contendrá una cantidad máxima de <b>1000</b> registros.<br><br>¿Desea continuar descargando el archivo?';
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
                        url: CARPETA+'/listado_excel/',
                        timeout:10000,
                        scope :this,
                        params: {
                            filtro_id_td    : publicadosDataStore.baseParams.filtro_id_td,
                            filtro_id_gcia  : publicadosDataStore.baseParams.filtro_id_gcia,
                            query           : publicadosDataStore.baseParams.query,
                            fields          : publicadosDataStore.baseParams.fields,
                            sort            : publicadosDataStore.sortInfo.field,
                            dir             : publicadosDataStore.sortInfo.direction
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
    
function BtntransferirDoc(grid,rowIndex,colIndex,item ,event){
    var id      = grid.getStore().getAt(rowIndex).json.id_documento;
    var nom     = grid.getStore().getAt(rowIndex).json.codigo;
    var id_g    = grid.getStore().getAt(rowIndex).json.id_gerencia_origen;
    clickBtntransferirDoc(id,nom,id_g);
};