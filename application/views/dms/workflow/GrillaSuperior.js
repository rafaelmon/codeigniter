estadosDocJS = new Ext.data.JsonStore({
	url: CARPETA+'/combo_estados',
	root: 'rows',
	fields: ['id_estado', 'estado']
});
estadosDocJS.load();
estadosDocJS.on('load' , function(  js , records, options ){
	var tRecord = Ext.data.Record.create(
		{name: 'id_estado', type: 'int'},
		{name: 'estado', type: 'string'}
	);
	var myNewT = new tRecord({
		id_estado: -1,
		estado   : 'Todos'
	});
	estadosDocJS.insert( 0, myNewT);	
} );

estadosDocFiltro = new Ext.form.ComboBox({
    id:'estados-doc-filtro',
    forceSelection : true,
    value: 'Todos',
    store: estadosDocJS,
    editable : false,
    displayField: 'estado',
    valueField:'id_estado',
    allowBlank: false,
    width:  200,
    selectOnFocus:true,
    triggerAction: 'all'
});

workflowDataStore = new Ext.data.GroupingStore({
    id: 'workflowDataStore',
    proxy: new Ext.data.HttpProxy({
        url: CARPETA+'/listado', 
        method: 'POST'
    }),
    baseParams:{limit: TAM_PAGINA}, 
    reader: new Ext.data.JsonReader({
        root: 'rows',
        totalProperty: 'total',
        id: 'id_tramite'
    },[         
        {name: 'id_documento',  type: 'int',        mapping: 'id_documento'},
        {name: 'documento',     type: 'string',     mapping: 'documento'},
        {name: 'detalle',       type: 'string',     mapping: 'detalle'},
        {name: 'codigo',        type: 'string',     mapping: 'codigo'},
        {name: 'fecha_alta',    type: 'string',     mapping: 'fecha_alta'},
        {name: 'id_editor',     type: 'string',     mapping: 'id_editor'},
        {name: 'editor',        type: 'string',     mapping: 'editor'},
        {name: 'aprobador',     type: 'string',     mapping: 'aprobador'},
        {name: 'estado',        type: 'string',     mapping: 'estado'},
        {name: 'id_estado',     type: 'string',     mapping: 'id_estado'},
        {name: 'tipo_wf',       type: 'string',     mapping: 'tipo_wf'},
        {name: 'twf',           type: 'string',     mapping: 'twf'},
        {name: 'revision',      type: 'string',     mapping: 'revision'},
        {name: 'revisores',     type: 'string',     mapping: 'revisores'},
        {name: 'q_obs',         type: 'int',        mapping: 'q_obs'},
        {name: 'q_ges',         type: 'int',        mapping: 'q_ges'},
        
    ]),
    sortInfo:{field: 'id_estado', direction: "asc"},
    groupField:'estado',
    remoteSort : true
});
workflowDataStore.load({params: {start: 0}});

//asigno el datastore al paginador
var paginadorWF= new Ext.PagingToolbar({
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
paginadorWF.bindStore(workflowDataStore);
paginadorWF.on('beforechange', setParamsWf);
    
function setParamsWf(){
    workflowDataStore.setBaseParam('f_id_estado',Ext.getCmp('estados-doc-filtro').getValue());
};

botonesDocAction = new Ext.grid.ActionColumn({
		width: 15,
                editable:false,
                menuDisabled:true,
                header:'Acción Doc',
                hideable:false,
                align:'left',
                width:  55,
                tooltip:'Acciones Doc',
                 hidden:false,
		items:[{
                    icon:URL_BASE+'images/preview1.png',
                    iconCls :'col_accion',
                    tooltip:'Previsualizar',
                    hidden: true,
                    getClass:showBtnPreview,
                    handler: clickBtnPreview
                },{
                    icon:URL_BASE+'images/comments_add.png',
                    iconCls :'col_accion',
                    tooltip:'Grabar Observación',
                    handler: clickBtnObs
                }]
});
botonesWFAction = new Ext.grid.ActionColumn({
		width: 15,
                editable:false,
                menuDisabled:true,
                header:'Acción FT',
                hideable:false,
                align:'left',
                width:  80,
                tooltip:'Acciones Flujo de Trabajo',
                 hidden:false,
		items:[{
                    icon:URL_BASE+'images/edit.png',
                    iconCls :'col_accion',
                    tooltip:'Editar',
                    hidden: true,
                    getClass:showBtnEditar,
                    hidden: (!permiso_alta||!rol_editor),
                    handler: clickBtnEdit
                },{
                    icon:URL_BASE+'images/liberar2.png',
                    iconCls :'col_accion',
                    tooltip:'Liberar',
                    getClass:showBtnLiberar,
                    handler: clickBtnLiberar
                },{
                    icon:URL_BASE+'images/revisado.png',
                    iconCls :'col_accion',
                    tooltip:'Revisado',
                    getClass:showBtnRevisado,
                    handler: clickBtnRevisado
                },{
                    icon:URL_BASE+'images/aprobar3.png',
                    iconCls :'col_accion',
                    tooltip:'Aprobar',
                    getClass:showBtnAprobar,
                    handler: clickBtnAprobar
                },{
                    icon:URL_BASE+'images/publicar2.png',
                    iconCls :'col_accion',
                    getClass:showBtnPublicar,
                    tooltip:'Publicar',
                    handler: clickBtnPublicar
                },{
                    icon:URL_BASE+'images/rechazar5.png',
                    iconCls :'col_accion',
                    tooltip:'Rechazar',
                    getClass:showBtnRechazarRevisar,
                    handler: clickBtnRechazar
                },{
                    icon:URL_BASE+'images/rechazar5.png',
                    iconCls :'col_accion',
                    tooltip:'Rechazar',
                    getClass:showBtnRechazarAprobar,
                    handler: clickBtnRechazar
                },{
                    icon:URL_BASE+'images/rechazar5.png',
                    iconCls :'col_accion',
                    tooltip:'Rechazar',
                    getClass:showBtnRechazarPublicar,
                    handler: clickBtnRechazar
                },{
                    icon:URL_BASE+'images/delegar2.png',
                    iconCls :'col_accion',
                    tooltip:'Delegar',
                    getClass:showBtnDelegar,
                    handler: clickBtnDelegar
                }
                
                ]
});
btnUpDocPDFAC = new Ext.grid.ActionColumn({
                editable:false,
                menuDisabled:true,
                header:'Subir Doc (.pdf)',
                hideable:false,
                align:'center',
                width:  60,
//                renderer:showBtnPDF,
                tooltip:'Subir archivo en formato PDF',
                hidden: (!permiso_alta||!rol_editor),
		items:[{
                    icon:URL_BASE+'images/upload.png',
                    iconCls :'col_accion',
                    tooltip:'Subir PDF',
                    getClass:showBtnUpPDF,
                    hidden: true,
                    handler: clickBtnArchivos
                },{
                    icon:URL_BASE+'images/reupload.png',
                    iconCls :'col_accion',
                    tooltip:'Reemplazar PDF',
                    getClass:showBtnRupPDF,
                    hidden: true,
                    handler: clickBtnArchivos
                },{
                    icon:URL_BASE+'images/delete_upload.png',
                    iconCls :'col_accion',
                    tooltip:'Eliminar PDF',
                    getClass:showBtnDelPDF,
                    hidden: true,
                    handler: clickBtnLDelete
                }]
});
//btnUpDocFuenteAC = new Ext.grid.ActionColumn({
//                editable:false,
//                menuDisabled:true,
//                header:'Fuente',
//                hideable:false,
//                align:'center',
//                width:  60,
//                tooltip:'Subir Archivo Fuente',
//                hidden: (!permiso_alta||!rol_editor),
//		items:[{
//                    icon:URL_BASE+'images/upload.png',
//                    iconCls :'col_accion',
//                    getClass:showBtnUpAF,
//                    tooltip:'Subir Archivo Fuente',
//                    hidden: true,
//                    handler: clickBtnArchivos
//                },{
//                    icon:URL_BASE+'images/reupload.png',
//                    iconCls :'col_accion',
//                    tooltip:'Reemplazar Archivo Fuente',
//                    getClass:showBtnRupAF,
//                    hidden: true,
//                    handler: clickBtnArchivos
//                },{
//                    icon:URL_BASE+'images/delete_upload.png',
//                    iconCls :'col_accion',
//                    tooltip:'Eliminar Archivo Fuente',
//                    getClass:showBtnDelAF,
//                    hidden: true,
//                    handler: clickBtnLDelete
//                }]
//});

buscador= new Ext.ux.grid.Search({
    iconCls:'icon-zoom',
    readonlyIndexes:['id_documento'],
    disableIndexes:['id_wf','fecha_alta','twf','revision','estado'],
    align:'right',
    minChars:3
});

workflowColumnModel = new Ext.grid.ColumnModel([
    {
        header: '#',
//        readOnly: true,
        dataIndex: 'id_documento',
        width: 20,
        hideable:false,
        hidden: false,
        renderer: function(value, cell){
            cell.css = "readonlycell";
            return value;
        }
    },{
        header: 'Fecha Alta',
        dataIndex: 'fecha_alta',
        sortable: false,
        width:  80,
        fixed:true,
        readOnly: true,
        align:'center'
      },{
        header: 'Título Documento',
        dataIndex: 'documento',
        width:  100,
        hidden: false
    },{
        header: 'Tipo Flujo',
        dataIndex: 'twf',
        width:  100,
        hidden: false
    },{
        header: 'Codigo',
        dataIndex: 'codigo',
        width:  100,
        hidden: false
    },{
        header: 'Editor',
        dataIndex: 'editor',
        width:  100,
        hidden: false
    },{
        header: 'Revisiones',
        dataIndex: 'revision',
        width:  40,
        hidden: false,
        renderer:showRevision
    },{
        header: 'Revisores',
        dataIndex: 'revisores',
        width:  100,
        hidden: false,
        renderer:showRevision
    },{
        header: 'Aprobador',
        dataIndex: 'aprobador',
        width:  100,
        hidden: false
    },{
        header: 'Estado Actual',
        dataIndex: 'estado',
        width:  100,
        hidden: true
    },btnUpDocPDFAC,/*btnUpDocFuenteAC,*/botonesDocAction,botonesWFAction
    ]);
workflowEditorGrid =  new Ext.grid.GridPanel({
    id: 'workflowEditorGrid',	  
    store: workflowDataStore,
    cm: workflowColumnModel,
    view: new Ext.grid.GroupingView({
            forceFit:true,
            headersDisabled :true,
            groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "Items" : "Item"]})'
        }),
    enableColLock:false,
    stripeRows:true,
    viewConfig: {
        forceFit: true
    },      
    autoScroll : true,	 
    selModel: new Ext.grid.RowSelectionModel({singleSelect:true}),
    plugins:[buscador],
    bbar:[paginadorWF],
    tbar: [{
            text: 'Nuevo',
            tooltip: 'Crear nuevo...',
            iconCls:'add',                      // reference to our css
            handler: displayAltaDocumentoPanel,
            hidden: (!permiso_alta||!rol_editor)
          },'&emsp;&emsp;&emsp;&emsp;<b>Filtrar por:</b> Estado del documento',estadosDocFiltro]//,'-','Filtrar por:','-','->']
});

superiorPanel = new Ext.Panel(
{
        collapsible: false,
        split: false,
        header: true,
        title: 'Flujo de Trabajo - Listado de documentos en trámite',
        region:'center',
        height: 300,
        minSize: 100,
        maxSize: 350,	
        margins: '0 5 5 5',
        html:'<p>panel superior</p>',
        layout: 'fit',
        items:[workflowEditorGrid]
});



//recargo la grilla cuando el combo del filtro sea seleccionado
//tipoTramiteFiltro.on('select', function( combo, record, index ){
////    console.log(this.getValue());
//    tramitesDataStore.setBaseParam('filtro_id_tipo_tramite',this.getValue());
//    tramitesDataStore.load();
//});


function showBtnLiberar(value,metaData,record){
    var id=record.json.id_estado;
    var a1=record.json.archivo;
    var id_editor=record.json.id_editor;
//    var a2=record.json.archivo_fuente;
//    console.log ((id == 1 || id==2) && (a1!="" && a2!=""));
//    if ((id == 1 || id==2) && (a1!="" && a1!="") && (a2!=null && a2!=null))
    if ((id == 1 || id==2) && (a1!="" && a1!=null)&& id_editor==usuarioId)
        return 'x-grid-center-icon';                
    else 
        return 'x-hide-display';  
};
function showRevision(value, metaData, record, rowIndex, colIndex, store){
    var id=record.json.tipo_wf;
    var revisores = record.get('revisores');
    if (id==1)
        {
            metaData.attr = 'style="color:#BCF5A9;background-color:#848484;"';
            value='No lleva';
            return value;
        }
    else
        {
            
            metaData.attr += 'ext:qtip="'+ revisores + '"';
        }
        return value;
    
};
function showBtnRevisado(value,metaData,record){
    var id=record.json.id_estado;
    var ids=record.json.ids_revisores;
    if (id==3 && ids !=null)
    {
        ids=ids.split(";");
        var a = ids.indexOf(usuarioId.toString());
        if (a!=-1)
            return 'x-grid-center-icon';                
        else 
            return 'x-hide-display';  
    }
    else 
        return 'x-hide-display';  
};
function showBtnAprobar(value,metaData,record){
    var id=record.json.id_estado;
    var id_aprobador=record.json.id_usuario_aprobador;
    if (id == 4 && usuarioId==id_aprobador)
        return 'x-grid-center-icon';                
    else 
        return 'x-hide-display';  
};
function showBtnPublicar(value,metaData,record){
    var id=record.json.id_estado;
    if (id == 5 && rol_public)
        return 'x-grid-center-icon';                
    else 
        return 'x-hide-display';  
};
function showBtnPreview(value,metaData,record){
    var a=record.json.archivo;
    if(a!="" && a!=null)
        return 'x-grid-center-icon';                
    else
        return 'x-hide-display';  
};
function showBtnUpPDF(value,metaData,record){
    var id=record.data.id_estado
    var id_editor=record.json.id_editor;
    var a=record.json.archivo;
    if ((id == 1 || id==2) && id_editor==usuarioId && (a=="" || a==null)) {
        return 'x-grid-center-icon';                
    } else {
        return 'x-hide-display';  
    }
};
function showBtnRupPDF(value,metaData,record){
    var id=record.data.id_estado
    var id_editor=record.json.id_editor;
    if (id != 1 && id!=2 || id_editor!=usuarioId) {
        return 'x-hide-display';  
    } else {
        var a=record.json.archivo;
        if(a=="" || a==null)
            return 'x-hide-display';  
        else
            return 'x-grid-center-icon';                
    }
};
function showBtnDelPDF(value,metaData,record){
    var id=record.data.id_estado
    var id_editor=record.json.id_editor;
    if (id != 1 && id!=2 || id_editor!=usuarioId) {
        return 'x-hide-display';  
    } else {
        var a=record.json.archivo;
        if(a=="" || a==null)
            return 'x-hide-display';  
        else
            return 'x-grid-center-icon';                
    }
};
//function showBtnUpAF(value,metaData,record){
//    var id=record.data.id_estado
//    if (id != 1 && id!=2) {
//        return 'x-hide-display';  
//    } else {
//        var a1=record.json.archivo_fuente;
//        if(a1!="" && a1!=null)
//            return 'x-hide-display';  
//        else
//            return 'x-grid-center-icon';                
//    }
//};
//function showBtnRupAF(value,metaData,record){
//    var id=record.data.id_estado
//    if (id != 1 && id!=2) {
//        return 'x-hide-display';  
//    } else {
//        var a=record.json.archivo_fuente;
//        if(a=="" || a==null)
//            return 'x-hide-display';  
//        else
//            return 'x-grid-center-icon';                
//    }
//};
//function showBtnDelAF(value,metaData,record){
//    var id=record.data.id_estado
//    if (id != 1 && id!=2) {
//        return 'x-hide-display';  
//    } else {
//        var a=record.json.archivo_fuente;
//        if(a=="" || a==null)
//            return 'x-hide-display';  
//        else
//            return 'x-grid-center-icon';                
//    }
//};
function showBtnDelegar(value,metaData,record){
    var id=record.data.id_estado
    if ((id == 1 || id==2) && permiso_gr) {
        return 'x-grid-center-icon';                
    } else {
        return 'x-hide-display';  
    }
};
function showBtnEditar(value,metaData,record){
    var id=record.data.id_estado;
    var id_editor=record.json.id_editor;
    if (id != 1 && id!=2 || id_editor!=usuarioId) {
        return 'x-hide-display';  
    } else {
        return 'x-grid-center-icon';                
    }
};


function showBtnRechazarRevisar(value,metaData,record){
    var id=record.json.id_estado;
    var ids=record.json.ids_revisores;
    if(id==3 && ids!=null)
    {
        ids=ids.split(";");
//        console.log(record.json.id_documento+"-"+ids+"--userId:"+usuarioId);
        var a = ids.indexOf(usuarioId.toString());
        if (id != 3 ||  a==-1) {
            return 'x-hide-display';  
        } else {
            return 'x-grid-center-icon';                
        }
    }
    else
        return 'x-hide-display';  
        
    
};
function showBtnRechazarAprobar(value,metaData,record){
    var id=record.json.id_estado;
    var id_aprobador=record.json.id_usuario_aprobador;
    if (id != 4 ||  usuarioId!=id_aprobador) {
        return 'x-hide-display';  
    } else {
        return 'x-grid-center-icon';                
    }
    
};
function showBtnRechazarPublicar(value,metaData,record){
    var id=record.json.id_estado;
    if (id != 5 ||  !rol_public) {
        return 'x-hide-display';  
    } else {
        return 'x-grid-center-icon';                
    }
    
};

function displayAltaDocumentoPanel(){		
    Ext.get('browser').load({
        url: CARPETA+"/nuevoDocumento/15",
        scripts: true,
        text: "Cargando Formulario..."
    });
 };
 
 function clickBtnPreview(grid,rowIndex,colIndex,item ,event){
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
 function clickBtnLiberar(grid,rowIndex,colIndex,item,event){
      msgProcess('Liberando documento');
      var id=grid.getStore().getAt(rowIndex).json.id_documento;
        Ext.Ajax.request({   
        waitMsg: 'Por favor espere...',
        url: CARPETA+'/liberar',
        method: 'POST',
        params: {
          id:id  
        }, 
        success: function(response){              
          var result=eval(response.responseText);
//          Ext.MessageBox.hide(); 
          switch(result.error){
          case 0:
            Ext.MessageBox.alert('Operación OK','El documento ha sido liberado');
            workflowDataStore.reload();
            break;
          case -1:
            Ext.MessageBox.alert('Error','Verifique que usted tenga rol de editor.');
            break;
          case -2:
            Ext.MessageBox.alert('Error','Verifique que usted tenga rol de editor en este documento.');
            break;
          case -3:
            Ext.MessageBox.alert('Error','Ha ocurrido un error al verificar el estado del documento');
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
 function clickBtnAprobar(grid,rowIndex,colIndex,item,event){
      msgProcess('Aprobando documento');
      var id=grid.getStore().getAt(rowIndex).json.id_documento;
        Ext.Ajax.request({   
        waitMsg: 'Por favor espere...',
        url: CARPETA+'/aprobar',
        method: 'POST',
        params: {
          id:id  
        }, 
        success: function(response){              
          var result=eval(response.responseText);
          switch(result.error){
          case 0:
            Ext.MessageBox.alert('Operación OK','El documento ah sido aprobado');
            workflowDataStore.reload();
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
 function clickBtnRevisado(grid,rowIndex,colIndex,item,event){
     msgProcess('Liberando documento');
      var id=grid.getStore().getAt(rowIndex).json.id_documento;
        Ext.Ajax.request({   
        waitMsg: 'Por favor espere...',
        url: CARPETA+'/revisado',
        method: 'POST',
        params: {
          id:id  
        }, 
        success: function(response){              
          var result=eval(response.responseText);
          switch(result.error){
          case 0:
            Ext.MessageBox.alert('Operación OK','El documento ah sido revisado');
            workflowDataStore.reload();
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
 function clickBtnPublicar(grid,rowIndex,colIndex,item,event){
    var codigo=grid.getStore().getAt(rowIndex).json.codigo;
    var txt ='¿Confirma la publicación del documento código '+codigo+'?';
    Ext.MessageBox.confirm('Confirmar',txt, function(btn, text){
        if(btn=='yes'){
            msgProcess('Publicando documento');
            var id=grid.getStore().getAt(rowIndex).json.id_documento;
              Ext.Ajax.request({   
              waitMsg: 'Por favor espere...',
              url: CARPETA+'/publicar',
              method: 'POST',
              params: {
                id:id  
              }, 
              success: function(response){              
                var result=eval(response.responseText);
                switch(result.success){
                  case true:
                  case 'true':
                      Ext.MessageBox.alert('Operación OK',result.msg);
                      workflowDataStore.reload();
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
}
 
 function clickBtnLDelete(grid,rowIndex,colIndex,item,event){
    var col
    switch (colIndex)
    {
        case 8:
            col='PDF';
            break;
        case 9:
            col='Fuente';
            break;
    };
      var id=grid.getStore().getAt(rowIndex).json.id_documento;
        Ext.Ajax.request({   
        waitMsg: 'Por favor espere...',
        url: CARPETA+'/delete',
        method: 'POST',
        params: {
          id:id,
          col:col
        }, 
        success: function(response){              
          var result=eval(response.responseText);
          switch(result.error){
          case 0:
            Ext.MessageBox.alert('Operación OK','Archivo eliminado correctamente');
            workflowDataStore.reload();
            break;
          case 1:
          case '1':
            Ext.MessageBox.alert('Error','Verifique que usted tenga rol de editor.');
            break;
          case 2:
          case '2':
            Ext.MessageBox.alert('Error','Verifique que usted tenga rol de editor en este documento.');
            break;
          case 3:
          case '3':
            Ext.MessageBox.alert('Error','Ha ocurrido un error al verificar el estado del documento');
            break;
          case 4:
          case '4':
            Ext.MessageBox.alert('Error','Ha ocurrido un error al intentar borrar el archivo del servidor');
            break;
          default:
            Ext.MessageBox.alert('Error','Ha ocurrido un error, por favor comuníquese con el administrador');
            break;
          }        
        },
        failure: function(response){
//          var result=response.responseText;
          Ext.MessageBox.alert('Error','No se pudo conectar con el servidor. Intente m&aacute;s tarde');         
        }                      
      });
  }

  function clickBtnEdit(grid,rowIndex,colIndex,item,event){
      var id=grid.getStore().getAt(rowIndex).json.id_documento;
    Ext.get('browser').load({
        url: CARPETA+"/editDocumento/15",
        scripts: true,
        params: {id:id},
        text: "Cargando Formulario..."
    });
 };
 
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

estadosDocFiltro.on('select', filtrarGrillaWf);
function filtrarGrillaWf (combo, record, index){
    workflowDataStore.load({
            params: {
                    f_id_estado: Ext.getCmp('estados-doc-filtro').getValue()
            }
    });	
};

workflowEditorGrid.on('celldblclick', abrir_popup_wf);
function abrir_popup_wf(grid ,  rowIndex, columnIndex,  e){
    var data=grid.store.data.items[rowIndex].data;
    var winDoc;
    var enc=['<html>','<div class="tabla_popup_grilla">'];
    var pie=['<br class="popup_clear"/></div>','</html>'];
    
    if(data.tipo_wf==1)
        data.revisores="No LLeva"
    for (var i in data)
        if (data[i]=='')
            data[i]="&nbsp";
    
    var labels=[
        {titulo:"Documento",            valor:data.documento}
        ,{titulo:"Código",              valor:data.codigo}
        ,{titulo:"Estado",              valor:data.estado}
        ,{titulo:"Fecha alta",          valor:data.fecha_alta}
        ,{titulo:"Detalle",             valor:data.detalle}
        ,{titulo:"Editor",              valor:data.editor}
        ,{titulo:"Revisor/es(reviso?)", valor:data.revisores}
        ,{titulo:"Aprobador",           valor:data.aprobador}
        ,{titulo:"Nro control",         valor:1000000+parseInt(data.id_documento)}
        
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
            title: 'Flujo de Trabajo Nro '+data.id_documento,
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
    winDoc.show();

}

//-->FIN PANEL SUPERIR