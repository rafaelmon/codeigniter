//textoCerrarTareaTA = new Ext.form.TextArea({
//        id: 'detalleDocumentoTexArea',
//        fieldLabel: 'Detalle del documento',
//        maxLength:1024,
//        height:200,
//        allowBlank: true,
//        tabIndex:2,
//        anchor : '95%'    
//});

var textoCerrarTareaTM = new Ext.ux.TinyMCE({
    id:'textoCerrarTareaTM',
    name:'textoCerrarTareaTM',
    ref:'editor',
    enableFont : false,
    fieldLabel:'Describa lo actuado para la resolución de la tarea',
    allowBlank: false,
    height:210,
    anchor:'95%',
    labelStyle: 'font-size:11px;',
    value:'',
    border: false,
    tinymceSettings: {
            theme : "advanced",
            cleanup : false,
            force_p_newlines : true,
            force_br_newlines : false,
            forced_root_block : '',
            theme_advanced_buttons1 : "bold,italic,underline,styleselect,|,justifyleft,justifycenter,justifyright,|,link,|,bullist,numlist,|,removeformat,cleanup,code",
//            theme_advanced_styles : 'Destacado=destacado',
            theme_advanced_buttons2 : "",
            theme_advanced_toolbar_location : "top",
            theme_advanced_toolbar_align : "left",
            theme_advanced_statusbar_location : "bottom",
            theme_advanced_resizing : false,
            extended_valid_elements : "a[name|href|target|title|onclick],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style],param[name|value|_value],embed[type|width|height|src|*],componente",
            accessibility_focus: true
            },
    listeners: 
    {
            change: function() {
                    //this.ownerCt.labChange.setValue( new Date() );
                    var btn_guardar=Ext.getCmp('btn_guardar');
                    btn_guardar.enable();
            },
            blur: function() {
                    //this.ownerCt.infoLabel.setText( "Blur occured in " + new Date() );
            },
            focus: function(a) {
                    //this.ownerCt.infoLabel.setText( "" );
//                    console.log(Ext.getCmp('textoCerrarTareaTM').getValue());
            }
    }
});

var archivosCerrarTareaDataStore = new Ext.data.Store({
      id: 'archivosCerrarTareaDataStore',
      proxy: new Ext.data.HttpProxy({
                url: CARPETA+'/listadoArchivosCerrar', 
                method: 'POST'
            }),
      baseParams:{id: TAREA.id_tarea}, // this parameter is passed for any HTTP request
      reader: new Ext.data.JsonReader({
        root: 'rows',
        totalProperty: 'total',
        id: 'id'
      },[ 
        {name: 'id_archivo', type: 'int', mapping: 'id_archivo'},        
        {name: 'archivo', type: 'string', mapping: 'archivo'},
        {name: 'tam', type: 'string', mapping: 'tam'},
        {name: 'fecha_upload',        type: 'string', mapping: 'fecha_upload'},
      ])
    
});
archivosCerrarTareaDataStore.load({params: {id_tarea: TAREA.id_tarea}});    
archivosCerrarTareaDataStore.on('load',activarDesactivarBtnQuitarTodos);    

var botonesUploadArchivosAction = new Ext.grid.ActionColumn({
		width: 15,
                editable:false,
                menuDisabled:true,
                header:'Acciónes',
                hideable:false,
                align:'center',
                width:  20,
                tooltip:'Acciones sobre cada archivo',
                 hidden:false,
		items:[{
                    icon:URL_BASE+'images/papelera.gif',
//                    iconCls :'col_accion',
//                    iconCls:'eliminar_ico', 
                    tooltip:'Eliminar',
                    hidden: true,
//                    getClass:showBtn.createDelegate(this,'cerrar',true),
//                    hidden: (!permiso_alta||!rol_editor),
                    handler: clickBtnEliminarArchivo
                }]
});
var archivosCerrarTareaColumnModel = new Ext.grid.ColumnModel(
    [new Ext.grid.RowNumberer(),
        {
            header: '#',
            hidden :true,
            readOnly: true,
            dataIndex: 'id_archivo',
            width: 10,        
            renderer: function(value, cell){ 
                cell.css = "readonlycell";
                return value;		 
            }
        },{
            header: 'Archivo',
            dataIndex: 'archivo',
            readOnly: true,
            width: 130,
        },{
            header: 'Tamaño',
            dataIndex: 'tam',
            readOnly: true,
            width: 30,
        },{
            header: 'Fecha',
            dataIndex: 'fecha_upload',
            sortable: true,
            width:  130,
            fixed:true,
            readOnly: true,
            align:'center'
        },botonesUploadArchivosAction
    ]);

 
var archivosCerrarTareaListingEditorGrid =  new Ext.grid.EditorGridPanel({
    id: 'archivosCerrarTareaListingEditorGrid',
    store: archivosCerrarTareaDataStore,
    cm: archivosCerrarTareaColumnModel,
    height:180,
    anchor:'95%',
    enableColLock:false,
    enableDragDrop:true,
    viewConfig: {
        forceFit: true
    },
    autoScroll : true,
    selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
    tbar: [
            {
                id:'btnAgregar',
                text: 'Adjuntar archivos',
                tooltip: 'Subir archivos relacionados al cierre de la tarea',
                iconCls:'adjuntar_ico',                      // reference to our css
                handler: clickBtnArchivosCerrarTarea
            },
            {
                id:'btnQuitarTodos',
                text: 'Quitar todos los archivos',
                tooltip: 'Quitar todos los archivos',
                iconCls:'eliminar_ico',                      // reference to our css
                disabled:false,
                handler: clickQuitarTodos
            }
    ]
  });
  
  

var cerrarTareaCreateForm = new Ext.FormPanel({
        id:'cerrarTareaCreateForm',
        title: 'Cierre de Tarea Nro:'+TAREA.id_tarea,
        labelAlign: 'top',
//        labelWidth:600,
        border: false,
        bodyStyle:'padding:1px 1px',
//        width: 600,
        height:'100%',
        buttonAlign:'center',
        autoScroll:true,
        layout:'column',
        renderTo: 'grillita',
//                    textoCerrarTareaTM
        items: 
        [{
            layout:'column',
            columnWidth:1,
            frame:true,
            border: false,
            autoScroll : true ,
            id:'frameceleste',
            items:
            [{
                autoHeight: true,
         border: false,
                layout:'form',
                columnWidth:1,
                items:[textoCerrarTareaTM,archivosCerrarTareaListingEditorGrid]
            }]
        }],
//        items: [textoCerrarTareaTM,myPanelU],
	buttons: [{
            id:'btn_guardar',
            text: 'Guardar',
            disabled:false,
            handler: guardarCerrarTarea
	},{
            text: 'Guardar e informar tarea',
            icon:URL_BASE+'images/aprobar2.png',
            handler: createCerrarTarea
	},{
            text: 'Salir',
            handler: salirCerrarTarea
            }]
    });

//  var panelCerrarTarea= new Ext.Panel({
//    id: 'panelCerrarTarea',
//    layout:'fit',		
//    split: true,
////    bodyStyle: 'padding:15px',
//    border: false,
//    autoScroll : false,
//    height: 400,
//    items: [cerrarTareaCreateForm],
//    renderTo: 'grillita'
//}); 
                
    altura=Ext.getBody().getSize().height - 100;
//    cerrarTareaCreateForm.setHeight(altura);

    Ext.getCmp('browser').on('resize',function(comp){
        cerrarTareaCreateForm.setWidth(this.getSize().width);
        cerrarTareaCreateForm.setHeight(Ext.getBody().getSize().height - 100);
    });
    
  function guardarCerrarTarea(){
     if(isCerrarTareaFormValid()){
      Ext.Ajax.request({   
        waitMsg: 'Por favor espere...',
        url: CARPETA+'/cerrar_guardar',
        params: {
          id        : TAREA.id_tarea,
          txt     : textoCerrarTareaTM.getValue()
        }, 
        success: function(response){              
          var result=eval(response.responseText);
          switch(result){
            case 1:
                Ext.MessageBox.alert('Alta OK','Datos guardados correctamente....',function(){
                    
                });
                break;
            case 2:
                Ext.MessageBox.alert('Error','Falta completar campo requerido');
                break;
          default:
                Ext.MessageBox.alert('Error','No se pudo realizar la acción, por favor informe al área de sistemas');
                break;
          }        
        },
        failure: function(response){
          var result=response.responseText;
          Ext.MessageBox.alert('Error','No se pudo conectar con el servidor. Intente m&aacute;s tarde');         
        }                      
      });
    } 
    else {
        Ext.MessageBox.alert('Error', 'Por favor complete el campo describiendo cómo resolvi&oacute; la tarea', function(btn)
        {
            if(btn=='ok')
            {
                var txt=Ext.getCmp('textoCerrarTareaTM');
                txt.focus(false);
            }
        });
    }
  }
  function createCerrarTarea(){
    msgProcess('Guardando y cerrando...')  
    if(isCerrarTareaFormValid()){
        
        var store=Ext.getCmp('archivosCerrarTareaListingEditorGrid').getStore();  
        var x=store.data.length;
        if(x<=5)
        {
            Ext.Ajax.request({   
              waitMsg: 'Por favor espere...',
              url: CARPETA+'/cerrar_confirmaCerrar',
              params: {
                id        : TAREA.id_tarea,
                txt     : textoCerrarTareaTM.getValue()
              }, 
              success: function(response){              
                var result=eval(response.responseText);
                switch(result){
                  case 1:
                      Ext.MessageBox.alert('Alta OK','Tarea informada correctamente....',function(){
                          var myFormPanel=Ext.getCmp("cerrarTareaCreateForm").getForm();
                          myFormPanel.destroy();
                          myFormPanel.cleanDestroyed();
                          Ext.get('browser').load({
                              url: CARPETA+"/index/20",
                              scripts: true,
                              text: "Cargando Grilla..."
                          });
                      });
                      break;
                  case 2:
                      Ext.MessageBox.alert('Error','Falta completar campo requerido');
                      break;
                default:
                      Ext.MessageBox.alert('Error','Falla en la recepcion de datos por la velocidad de su conexión, probablemente pudo haber habido errores, por favor revise si su gestión se realizo correctamente',function(){
                          var myFormPanel=Ext.getCmp("cerrarTareaCreateForm").getForm();
                          myFormPanel.destroy();
                          myFormPanel.cleanDestroyed();
                          Ext.get('browser').load({
                              url: CARPETA+"/index/20",
                              scripts: true,
                              text: "Cargando Grilla..."
                          });
                      });
                      
                      break;
                }        
              },
              failure: function(response){
                var result=response.responseText;
                Ext.MessageBox.alert('Error','No se pudo conectar con el servidor. Intente m&aacute;s tarde');         
              }                      
            });
            
        }
        else
            Ext.MessageBox.alert('Alerta', 'Usted adjunto '+x+' archivos y la cantidad máxima permitidos es de 5.');
	 
    } else {
      Ext.MessageBox.alert('Alerta', 'Por favor complete el campo describiendo como resolvi&oacute; la tarea.');
    }
  }

  
  // check if the form is valid
  function isCerrarTareaFormValid(){	  
	  var v1 = textoCerrarTareaTM.isValid();
	  return(v1);
  }
  
  function salirCerrarTarea(){
    Ext.MessageBox.confirm('Salir', 'Si realizó cambios y no los guardó, los perderá ¿Desea salir de todas formas?', function(btn)
    {
        if(btn=='yes')
        {
            var myFormPanel=Ext.getCmp("cerrarTareaCreateForm").getForm();
        //    console.log(myFormPanel);
            myFormPanel.destroy();
            myFormPanel.cleanDestroyed();
            Ext.get('browser').load({
                url: CARPETA+"/index/20",
                scripts: true,
//                params: "="+1,
                text: "Cargando Listado..."
            });
        }
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
 function clickBtnEliminarArchivo(grid,rowIndex,colIndex,item,event){		
    var id=grid.getStore().getAt(rowIndex).json.id_archivo;
     msgProcess('Eliminando archivo...');
        Ext.Ajax.request({   
        waitMsg: 'Por favor espere...',
        url: CARPETA+'/delete_archivo_cerrar/20',
        method: 'POST',
        params: {
        id:id,  
        id_tarea:TAREA.id_tarea,  
        }, 
        success: function(response){              
        var result=eval(response.responseText);
        switch(result){
        case 0:
            Ext.MessageBox.alert('Error','El usuario no tiene permisos para la accion solicitada');
            break;
        case 1:
            Ext.MessageBox.alert('Operación OK','Archivo eliminado correctamente');
            archivosCerrarTareaDataStore.reload();
            break;
        default:
            Ext.MessageBox.alert('Error','Falla en la recepcion de datos por la velocidad de su conexión, por favor revise si su gestión se realizo correctamente');
            break;
        }        
        },
        failure: function(response){
//          var result=response.responseText;
        Ext.MessageBox.alert('Error','No se pudo conectar con el servidor. Intente m&aacute;s tarde');         
        }                      
    });
 };
    function clickQuitarTodos(){
        msgProcess('Eliminando archivos...');
        Ext.Ajax.request({   
        waitMsg: 'Por favor espere...',
        url: CARPETA+'/delete_archivos_cerrar/20',
        method: 'POST',
        params: {
        id_tarea:TAREA.id_tarea,  
        }, 
        success: function(response){              
        var result=eval(response.responseText);
        switch(result){
        case 0:
            Ext.MessageBox.alert('Error','El usuario no tiene permisos para la accion solicitada');
            break;
        case 1:
            Ext.MessageBox.alert('Operación OK','Archivos eliminados correctamente');
            archivosCerrarTareaDataStore.reload();
            break;
        default:
            Ext.MessageBox.alert('Error','No se ha podido realizar la accion, por favor reintente o informe a sistemas');
            break;
        }        
        },
        failure: function(response){
//          var result=response.responseText;
        Ext.MessageBox.alert('Error','No se pudo conectar con el servidor. Intente m&aacute;s tarde');         
        }                      
    });
    }
function activarDesactivarBtnQuitarTodos(store){
    var x=store.data.length;
    var btnQuitar=Ext.getCmp("btnQuitarTodos");
    var btnAgregar=Ext.getCmp("btnAgregar");
    if(x==0)
        btnQuitar.disable();
    else
        btnQuitar.enable();
    if(x>=5)
    {
        btnAgregar.disable();
//        Ext.MessageBox.alert('Error','Solo se permite adjuntar hasta 5 archivos');
    }
    else
        btnAgregar.enable();
}     
if (TAREA.cierre)
    {
//        console.log(docNewRecord);
//             var myFormPanel=Ext.getCmp("altaDocumento-form").getForm();
        cerrarTareaCreateForm.setTitle("Informando que ha finalizado la tarea Nro "+TAREA.id_tarea+"->");
        cerrarTareaCreateForm.load({
            url: CARPETA+"/cierre_dameDatos/",
            params: {
                id: TAREA.id_tarea
            },
            waitMsg: 'Cargando...',
             success: function(form, action){
             },
            failure: function(form, action) {
                Ext.Msg.alert("Load failed", action.result.errorMessage);
            }
        });
    }


