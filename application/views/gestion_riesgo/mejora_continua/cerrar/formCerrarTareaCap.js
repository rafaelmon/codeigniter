//textoCerrarTareaTA = new Ext.form.TextArea({
//        id: 'detalleDocumentoTexArea',
//        fieldLabel: 'Detalle del documento',
//        maxLength:1024,
//        height:200,
//        allowBlank: true,
//        tabIndex:2,
//        anchor : '95%'    
//});

var textoCerrarTareaCapTM = new Ext.ux.TinyMCE({
    id:'textoCerrarTareaCapTM',
    name:'textoCerrarTareaCapTM',
    ref:'editor',
    enableFont : false,
    fieldLabel:'Describa lo actuado para la resolución de la tarea de capacitación',
    allowBlank: false,
    height:140,
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
//                    console.log(Ext.getCmp('textoCerrarTareaCapTM').getValue());
            }
    }
});
var continuaCapRadios = new Ext.form.RadioGroup({ 
    id:'continuaCapRadios',
    fieldLabel: '<br>¿Aún debe continuar capacitando a más personas? <div style="color:grey;display:inline;font: 9px arial, sans-serif;">Seleccione <b>Si</b> para que el sistema genere automáticamente una nueva tarea donde usted pueda seguir informando esta misma capacitacion</div>',
    tabIndex:3,
    columns: 8,
    items: [ 
          {boxLabel: 'Si', name: 'continue_cap', inputValue: '1'}, 
          {boxLabel: 'No', name: 'continue_cap', inputValue: '2', checked: true}
     ] 
});
var archivosCerrarTareaCapDataStore = new Ext.data.Store({
      id: 'archivosCerrarTareaCapDataStore',
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
archivosCerrarTareaCapDataStore.load({params: {id_tarea: TAREA.id_tarea}});    
archivosCerrarTareaCapDataStore.on('load',activarDesactivarBtnQuitarTodosTareaCap);    

var participantesCerrarTareaCapDataStore = new Ext.data.Store({
      id: 'participantesCerrarTareaCapDataStore',
      proxy: new Ext.data.HttpProxy({
                url: CARPETA_CAP+'/listadoPartCerrar', 
                method: 'POST'
            }),
      baseParams:{id_tarea: TAREA.id_tarea}, // this parameter is passed for any HTTP request
      reader: new Ext.data.JsonReader({
        root: 'rows',
        totalProperty: 'total',
        id: 'id'
      },[ 
        {name: 'id',                type: 'int',    mapping: 'id'},        
        {name: 'persona',      type: 'string', mapping: 'persona'},
        {name: 'documento',         type: 'string', mapping: 'documento'},
        {name: 'tipo',              type: 'string', mapping: 'tipo'},
        {name: 'fecha_cap',         type: 'string', mapping: 'fecha_cap'},
      ])
    
});
participantesCerrarTareaCapDataStore.load({params: {id_tarea: TAREA.id_tarea}});    

var botonesUploadArchivosCerrarTareaCapAction = new Ext.grid.ActionColumn({
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

var archivosCerrarTareaCapColumnModel = new Ext.grid.ColumnModel(
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
        },botonesUploadArchivosCerrarTareaCapAction
    ]);

var botonesParticipantesCerrarTareaCapAction = new Ext.grid.ActionColumn({
		width: 15,
                editable:false,
                menuDisabled:true,
                header:'Acciónes',
                hideable:false,
                align:'center',
                width:  20,
                tooltip:'Acciones sobre cada participante',
                 hidden:false,
		items:[{
                    icon:URL_BASE+'images/papelera.gif',
//                    iconCls :'col_accion',
//                    iconCls:'eliminar_ico', 
                    tooltip:'Eliminar',
                    hidden: true,
//                    getClass:showBtn.createDelegate(this,'cerrar',true),
//                    hidden: (!permiso_alta||!rol_editor),
                    handler: clickBtnEliminarParticipante
                }]
});
var participantesCerrarTareaCapColumnModel = new Ext.grid.ColumnModel(
    [new Ext.grid.RowNumberer(),
       {
            header: 'Part. o Facilitador',
            dataIndex: 'persona',
            readOnly: true,
            width: 130,
        },{
            header: 'Documento',
            dataIndex: 'documento',
            sortable: true,
            width:  90,
            fixed:true,
            readOnly: true,
            align:'center'
        },{
            header: 'Tipo',
            dataIndex: 'tipo',
            sortable: true,
            width:  90,
            fixed:true,
            readOnly: true,
            align:'center'
        },{
            header: 'Fecha',
            dataIndex: 'fecha_cap',
            sortable: true,
            width:  130,
            fixed:true,
            readOnly: true,
            align:'center'
        },botonesParticipantesCerrarTareaCapAction
    ]);

 
 
var archivosCerrarTareaCapListingEditorGrid =  new Ext.grid.EditorGridPanel({
    id: 'archivosCerrarTareaCapListingEditorGrid',
    store: archivosCerrarTareaCapDataStore,
    cm: archivosCerrarTareaCapColumnModel,
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
                id:'btnAgregarArchivoCerrarTareaCap',
                text: 'Adjuntar archivos',
                tooltip: 'Subir archivos (actas) relacionados al cierre de la tarea de capacitación',
                iconCls:'adjuntar_ico',                      // reference to our css
                disabled:false,
                handler: clickBtnArchivosCerrarTareaCap
            },
            {
                id:'btnQuitarTodosArchivosTareaCap',
                text: 'Quitar todos los archivos',
                tooltip: 'Quitar todos los archivos',
                iconCls:'eliminar_ico',                      // reference to our css
                disabled:false,
                handler: clickQuitarTodosArchCarrarTareaCap
            }
    ]
  });
  
  fechaCursoDateField = new Ext.form.DateField({
            id:'fechaCursoDateField',
            allowBlank: false,
            tabIndex: 5,
            fieldLabel:'Fecha dictado',
            tooltip: 'Fecha en que se dicto el curso',
            allowBlank: false,
            anchor : '95%',
            blankText:'campo requerido',
            editable: true,
//            minValue:MINDATE,
            maxValue:MAXDATE,
            format:'d/m/Y'
        });
  var tipoParticipanteArray = [
        [1, 'Participante'],
        [2, 'Facilitador']        
    ];
    
      var tipoParticipanteStore = new Ext.data.ArrayStore({
        fields: ['id', 'tipo'],
        data : tipoParticipanteArray
    });
    
    var tipoParticipantecerrarTareaCapCombo = new Ext.form.ComboBox({
        id:'tipoParticipantecerrarTareaCapCombo',
        store: tipoParticipanteStore,
        displayField:'tipo',
        valueField:'id',
        typeAhead: true,
        mode: 'local',
        forceSelection: true,
        triggerAction: 'all',
        emptyText:'Seleccione...',
        width: 90,
        selectOnFocus:true
    });
    
  personasDS = new Ext.data.Store({
        proxy: new Ext.data.HttpProxy({
            url: CARPETA_CAP+'/combo_participantes_form_cerrar',
            method: 'POST'
        }),
         baseParams:{id: TAREA.id_tarea},
        reader: new Ext.data.JsonReader({
            root: 'rows',
            totalProperty: 'total',
            id: 'id_persona'
        }, [
            {name: 'id_persona', mapping: 'id_persona'},
            {name: 'persona', mapping: 'persona'}
//            {name: 'dni', mapping: 'dni'},
        ])
    });

    // Custom rendering Template
//    var participanteTpl = new Ext.XTemplate(
//        '<tpl for="."><div class="search-item" style="padding: 3px; border-color: #fff;">',
//            '<span><b>{persona}</b> ({dni})</span>',
//        '</div></tpl>'
//    );
    
        personaCerrarTareaCapCombo = new Ext.form.ComboBox({
        id:'personaCerrarTareaCapCombo',
        store: personasDS,
        blankText:'campo requerido',
        allowBlank: false,
        fieldLabel: 'Persona',
        displayField:'persona',
        valueField:'id_persona',
        width: 300,
        typeAhead: false,
        loadingText: 'Buscando...',
        minChars:3,
//        labelStyle: 'font-weight:bold;',
        pageSize:10,
         tabIndex: 4,
        emptyText:'Ingresa caracteres para buscar',
//        valueNotFoundText:"Nombre no encontrado",
//        tpl: participanteTpl,
//        itemSelector: 'div.search-item'
    });
// responsableFieldSet = new Ext.form.FieldSet({
//    id:'responsableFieldSet',
//    title : 'Persona responsable de gestionar la tarea',
//    anchor : '95%',
//    growMin:100,
//    items:[responsablesCombo]
//}); 
 personaCerrarTareaCapCombo.on('select',function(){
        TAREA.part=this.getValue();
    }); 
 
 
var participantesCerrarTareaCapListingEditorGrid =  new Ext.grid.EditorGridPanel({
    id: 'participantesCerrarTareaCapListingEditorGrid',
    store: participantesCerrarTareaCapDataStore,
    cm: participantesCerrarTareaCapColumnModel,
    height:180,
    anchor:'92%',
    enableColLock:false,
    enableDragDrop:false,
    viewConfig: {
        forceFit: true
    },
    autoScroll : true,
    selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
    tbar: [
            {
            xtype: 'displayfield', 
            value: '&nbsp;&nbsp;Fecha curso:'
//            cls:'rrhh_ico',                      
            },fechaCursoDateField,"|",
            {
            xtype: 'displayfield', 
            value: '&nbsp;&nbsp;&nbsp;Persona:'
//            cls:'rrhh_ico',                      
            },tipoParticipantecerrarTareaCapCombo,'&nbsp;&nbsp;&nbsp;',personaCerrarTareaCapCombo,
            {
                id:'btnAgregar2Participante',
                text: 'Agregar',
                tooltip: 'Agregar participante',
                iconCls:'add_participante_ico',                    
                disabled:false,
                handler: clickAddPart
            }
    ]
  });  

var cerrarTareaCapCreateForm = new Ext.FormPanel({
        id:'cerrarTareaCapCreateForm',
        title: 'Cierre de Tarea de Capacitación Nro:'+TAREA.id_tarea,
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
//                    textoCerrarTareaCapTM
        items: 
        [{
            id:'frameceleste',
            layout:'column',
            columnWidth:1,
            frame:true,
            border: false,
            autoScroll : true ,
            items:
            [
            {
                autoHeight: true,
                border: false,
                layout:'form',
                columnWidth:1,
                items:[textoCerrarTareaCapTM]
            },
            {
                autoHeight: true,
                border: false,
                layout:'form',
                columnWidth:0.4,
                items:[{
                        xtype: 'displayfield', 
                        value: ' Agregar acta y archivos relacionados a la capacitación',
                        cls:'subtit_form',                      
                        },archivosCerrarTareaCapListingEditorGrid]
            },
            {
                autoHeight: true,
                border: false,
                layout:'form',
                columnWidth:0.6,
                items:[{
                        xtype: 'displayfield', 
                        value: ' Agregar los participantes y facilitadores que integran el acta de capacitación',
                        cls:'subtit_form',                      
                        },participantesCerrarTareaCapListingEditorGrid,continuaCapRadios
//                        ,{
//                        xtype: 'displayfield', 
//                        boxMaxHeight: 10,
//                        fieldLabel: 'Seleccione <b>Si</b> para que el sistema genere automáticamente una nueva tarea donde usted pueda seguir informando esta misma capacitacion',
//                        labelSeparator:"",
//                        cls:'obs_form',                      
//                        }
                    ]
            },
            {
                autoHeight: true,
                border: false,
                layout:'form',
                columnWidth:1,
                items:[{
                        xtype: 'displayfield', 
                        value: '',
                        cls:'subtit_form',                      
                        }]
            }
        ]
        }],
//        items: [textoCerrarTareaCapTM,myPanelU],
	buttons: [{
            id:'btn_guardar',
            text: 'Guardar',
            disabled:false,
            handler: guardarCerrarTareaCap
	},{
            text: 'Guardar e informar tarea',
            icon:URL_BASE+'images/aprobar2.png',
            handler: createCerrarTareaCap
	},{
            text: 'Salir',
            handler: salirCerrarTareaCap
            }]
    });

//  var panelCerrarTareaCap= new Ext.Panel({
//    id: 'panelCerrarTareaCap',
//    layout:'fit',		
//    split: true,
////    bodyStyle: 'padding:15px',
//    border: false,
//    autoScroll : false,
//    height: 400,
//    items: [cerrarTareaCapCreateForm],
//    renderTo: 'grillita'
//}); 
                
    altura=Ext.getBody().getSize().height - 100;
//    cerrarTareaCapCreateForm.setHeight(altura);

    Ext.getCmp('browser').on('resize',function(comp){
        cerrarTareaCapCreateForm.setWidth(this.getSize().width);
        cerrarTareaCapCreateForm.setHeight(Ext.getBody().getSize().height - 100);
    });
    
  function guardarCerrarTareaCap(){
     if(isCerrarTareaCapFormValid()){
      Ext.Ajax.request({   
        waitMsg: 'Por favor espere...',
        url: CARPETA+'/cerrar_guardar',
        params: {
          id        : TAREA.id_tarea,
          txt       : textoCerrarTareaCapTM.getValue(),
          continua  : continuaCapRadios.getValue().inputValue,
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
                var txt=Ext.getCmp('textoCerrarTareaCapTM');
                txt.focus(false);
            }
        });
    }
  }
  function createCerrarTareaCap(){
    msgProcess('Guardando y cerrando...')  
    if(isCerrarTareaCapFormValid()){
        
        var store=Ext.getCmp('archivosCerrarTareaCapListingEditorGrid').getStore();  
        var x=store.data.length;
        var store2=Ext.getCmp('participantesCerrarTareaCapListingEditorGrid').getStore();  
        var x2=store2.data.length;
        if (x>0)
        {
            if (x2>0)
            {
                if(x<=5)
                {
                    Ext.Ajax.request({   
                      waitMsg: 'Por favor espere...',
                      url: CARPETA+'/cerrar_confirmaCerrar',
                      params: {
                        id        : TAREA.id_tarea,
                        txt       : textoCerrarTareaCapTM.getValue(),
                        continua  : continuaCapRadios.getValue().inputValue,
                      }, 
                      success: function(response){              
                        var result=eval(response.responseText);
                        switch(result){
                          case -1:
                                Ext.MessageBox.alert('Alerta', 'Por favor, complete el campo describiendo como resolvio la tarea');
                                break;
                          case -2:
                                Ext.MessageBox.alert('Alerta', 'Por favor, adjunte como mínimo el acta correspondiente a la capacitación');
                                break;
                          case -3:
                                Ext.MessageBox.alert('Alerta', 'Por favor, informe todos los participantes de la capacitación');
                                break;
                          case 1:
                                Ext.MessageBox.alert('Alta OK','Tarea informada correctamente....',function(){
                                    var myFormPanel=Ext.getCmp("cerrarTareaCapCreateForm").getForm();
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
                                    var myFormPanel=Ext.getCmp("cerrarTareaCapCreateForm").getForm();
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
            }
            else
               Ext.MessageBox.alert('Alerta', 'Por favor, informe todos los participantes de la capacitación');
        }
        else
           Ext.MessageBox.alert('Alerta', 'Por favor, adjunte como mínimo el acta correspondiente a la capacitación');
	 
    } else {
      Ext.MessageBox.alert('Alerta', 'Por favor complete el campo describiendo como resolvi&oacute; la tarea.');
    }
  }

  
  // check if the form is valid
  function isCerrarTareaCapFormValid(){	  
	  var v1 = textoCerrarTareaCapTM.isValid();
	  return(v1);
  }
  
  function salirCerrarTareaCap(){
    Ext.MessageBox.confirm('Salir', 'Si realizó cambios y no los guardó, los perderá ¿Desea salir de todas formas?', function(btn)
    {
        if(btn=='yes')
        {
            var myFormPanel=Ext.getCmp("cerrarTareaCapCreateForm").getForm();
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
            archivosCerrarTareaCapDataStore.reload();
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
 function clickBtnEliminarParticipante(grid,rowIndex,colIndex,item,event){		
    var id=grid.getStore().getAt(rowIndex).json.id;
     msgProcess('Eliminando participante...');
        Ext.Ajax.request({   
        waitMsg: 'Por favor espere...',
        url: CARPETA_CAP+'/delete_participante',
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
            Ext.MessageBox.alert('Operación OK','Participante eliminado correctamente');
            participantesCerrarTareaCapDataStore.reload();
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
    function clickQuitarTodosArchCarrarTareaCap(){
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
            archivosCerrarTareaCapDataStore.reload();
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
//    function clickQuitarTodosPart(){
//        msgProcess('Eliminando participantes...');
//        Ext.Ajax.request({   
//        waitMsg: 'Por favor espere...',
//        url: CARPETA_CAP+'/delete_participantes_all/',
//        method: 'POST',
//        params: {
//        id_tarea:TAREA.id_tarea,  
//        }, 
//        success: function(response){              
//        var result=eval(response.responseText);
//        switch(result){
//        case 0:
//            Ext.MessageBox.alert('Error','El usuario no tiene permisos para la accion solicitada');
//            break;
//        case 1:
//            Ext.MessageBox.alert('Operación OK','Registros eliminados correctamente');
//            participantesCerrarTareaCapDataStore.reload();
//            break;
//        default:
//            Ext.MessageBox.alert('Error','No se ha podido realizar la accion, por favor reintente o informe a sistemas');
//            break;
//        }        
//        },
//        failure: function(response){
////          var result=response.responseText;
//        Ext.MessageBox.alert('Error','No se pudo conectar con el servidor. Intente m&aacute;s tarde');         
//        }                      
//    });
//    }
    function clickAddPart(){
        var partCombo=Ext.getCmp("personaCerrarTareaCapCombo");
        var tipoCombo=Ext.getCmp("tipoParticipantecerrarTareaCapCombo");
        var partComboStore=partCombo.getStore();
        var partGrid=Ext.getCmp("participantesCerrarTareaCapListingEditorGrid");
        var partGridStore=partGrid.getStore();
        var fechaField=Ext.getCmp("fechaCursoDateField");
        var tipo=tipoCombo.getValue();
        var part=partCombo.getValue();
        var fecha=fechaField.getValue();
        msgProcess('Agregando...');
        Ext.Ajax.request({   
            waitMsg: 'Por favor espere...',
            url: CARPETA_CAP+'/add_participante/',
            method: 'POST',
            params: {
                id_tarea:TAREA.id_tarea,
                tipo:tipo,
                part:part,
                fecha:fecha
            }, 
        success: function(response){              
            var result=eval(response.responseText);
            switch(result){
            case -3:
                Ext.MessageBox.alert('Error','Verifique la fecha informada');
                break;
            case -2:
                Ext.MessageBox.alert('Error','Verifique la los datos del participante');
                break;
            case -1:
                Ext.MessageBox.alert('Error','El usuario no tiene permisos para la accion solicitada');
                break;
            case 0:
                Ext.MessageBox.alert('Error','Error al insertar datos, por favor informe este error al área de IT');
                break;
            case 1:
                partCombo.reset();
                partCombo.clearInvalid();
                partComboStore.removeAll();
                partGridStore.reload();
                Ext.MessageBox.hide();
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
function activarDesactivarBtnQuitarTodosTareaCap(store){
    var x=store.data.length;
    var btnQuitar=Ext.getCmp("btnQuitarTodosArchivosTareaCap");
//    console.log(btnQuitar);
    var btnAgregar=Ext.getCmp("btnAgregarArchivoCerrarTareaCap");
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
        cerrarTareaCapCreateForm.setTitle("Informando tarea Nro "+TAREA.id_tarea+"->");
        cerrarTareaCapCapCreateForm.load({
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


