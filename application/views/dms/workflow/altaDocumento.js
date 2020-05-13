
tiposDocumentosDS = new Ext.data.Store({
    id: 'tiposDocumentosDS',
    proxy: new Ext.data.HttpProxy({
        url: CARPETA+'/combo_td', 
        method: 'POST'
    }),
    reader: new Ext.data.JsonReader({
        root: 'rows',
        totalProperty: 'total'
        },[
        {name: 'id_td', type: 'int'},        
        {name: 'td', type: 'string'},
    ])
});
tiposDocumentoCombo = new Ext.form.ComboBox({
        id:'tiposDocumentoCombo',
        forceSelection : false,
        fieldLabel: 'Tipo de documento',
        store: tiposDocumentosDS,
        editable : false,
        displayField: 'td',
        allowBlank: false,
        blankText:'campo requerido',
        valueField: 'id_td',
        anchor:'95%',
        tabIndex:3,
        triggerAction: 'all',
        width: 300
    });
revisoresDS = new Ext.data.Store({
        id:'revisoresDS',
        proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/combo_revisores',
            method: 'POST'
        }),
        reader: new Ext.data.JsonReader({
            root: 'rows',
            totalProperty: 'total',
            id: 'id_usuario'
        }, [
            {name: 'id_usuario', mapping: 'id_usuario'},
            {name: 'nomape', mapping: 'nomape'},
            {name: 'usuario', mapping: 'usuario'},
            {name: 'puesto', mapping: 'puesto'}
        ])
    });
//var revisoresTpl = new Ext.XTemplate(
//        '<tpl for="."><div class="search-item">',
//            '<h3><span>{nomape}</h3>({usuario})</span>',
//        '</div></tpl>'
//);
var revisoresTpl = new Ext.XTemplate(
        '<tpl for="."><div class="search-item">',
            '<h3><span>{nomape}</h3>({puesto})</span>',
        '</div></tpl>'
    );
revisoresSBS = new Ext.ux.form.SuperBoxSelect({
        id:'revisoresSBS',
        forceSelection : false,
        fieldLabel: 'Usuario/s',
        store: revisoresDS,
//        editable : false,
        allowBlank: false,
        emptyText: 'Ingresa caracteres para buscar',
        blankText:'campo requerido',
        displayField: 'nomape',
        valueField: 'id_usuario',
        anchor : '95%',
//        displayFieldTpl: '{nomape} ({usuario})',
        mode: 'remote',
//        valueDelimiter:';',
        tpl: revisoresTpl,
        itemSelector: 'div.search-item',
        stackItems:true, //un item por línea
        anchor:'90%',
//        triggerAction: 'all',
//        forceSelection : true,
        allowQueryAll : false,
        minChars:3,
        maxSelections : 3,
        tabIndex:8
    });


revisoresFieldSet = new Ext.form.FieldSet({
    id:'revisoresFieldSet',
    title : 'Revisores',
    anchor : '95%',
    growMin:100,
    items:[revisoresSBS]
});


aprobadoresDS = new Ext.data.Store({
        id:'idAprobadoresDS',
        proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/combo_aprobadores',
            method: 'POST'
        }),
        reader: new Ext.data.JsonReader({
            root: 'rows',
            totalProperty: 'total',
            id: 'id_usuario'
        }, [
            {name: 'id_usuario', mapping: 'id_usuario'},
            {name: 'nomape', mapping: 'nomape'},
            {name: 'usuario', mapping: 'usuario'},
            {name: 'puesto', mapping: 'puesto'}
        ])
    });

    // Custom rendering Template
//    var aprobadorTpl = new Ext.XTemplate(
//        '<tpl for="."><div class="search-item">',
//            '<h3><span>{nomape}</h3>({usuario})</span>',
//        '</div></tpl>'
//    );
    var aprobadorTpl = new Ext.XTemplate(
        '<tpl for="."><div class="search-item">',
            '<h3><span>{nomape}</h3>({puesto})</span>',
        '</div></tpl>'
    );
    
    aprobadoresCombo = new Ext.form.ComboBox({
        id:'aprobadoresCombo',
        store: aprobadoresDS,
        blankText:'campo requerido',
        allowBlank: false,
        fieldLabel: 'Usuario',
        displayField:'nomape',
        valueField:'id_usuario',
        typeAhead: false,
        loadingText: 'Buscando...',
        anchor : '95%',
        forceSelection : true,
        minChars:3,
//        labelStyle: 'font-weight:bold;',
        pageSize:10,
         tabIndex: 11,
        emptyText:'Ingresa caracteres para buscar',
        valueNotFoundText:"",
        tpl: aprobadorTpl,
        itemSelector: 'div.search-item'
    });
 aprobadorFieldSet = new Ext.form.FieldSet({
    id:'aprobadoresFieldSet',
    title : 'Aprobador',
    anchor : '95%',
    growMin:100,
    items:[aprobadoresCombo]
});

//
//publicadorDS = new Ext.data.Store({
//        proxy: new Ext.data.HttpProxy({
//            url: CARPETA+'/combo_publicadores',
//            method: 'POST'
//        }),
//        reader: new Ext.data.JsonReader({
//            root: 'rows',
//            totalProperty: 'total',
//            id: 'id_usuario'
//        }, [
//            {name: 'id_usuario', mapping: 'id_usuario'},
//            {name: 'nomape', mapping: 'nomape'},
//            {name: 'usuario', mapping: 'usuario'},
//        ])
//    });
//var publicadorTpl = new Ext.XTemplate(
//        '<tpl for="."><div class="search-item">',
//            '<h3><span>{nomape}</h3>({usuario})</span>',
//        '</div></tpl>'
//);
//publicadorSBS = new Ext.ux.form.SuperBoxSelect({
//        id:'publicadorSBS',
//        forceSelection : false,
//        fieldLabel: 'Usuario/s',
//        store: publicadorDS,
////        editable : false,
//        allowBlank: false,
//        emptyText: 'Ingresa caracteres para buscar',
//        blankText:'campo requerido',
//        displayField: 'nomape',
//        valueField: 'id_usuario',
////        displayFieldTpl: '{nomape} ({usuario})',
//        mode: 'remote',
////        valueDelimiter:';',
////        tpl: revisoresTpl,
////        itemSelector: 'div.search-item',
//        stackItems:true, //un item por línea
//        anchor:'90%',
////        triggerAction: 'all',
//        forceSelection : true,
//        allowQueryAll : false,
//        minChars:3,
//        maxSelections : 3,
//        tabIndex:12
//    });

//    publicadoresCombo = new Ext.form.ComboBox({
//        store: publicadorDS,
//        blankText:'campo requerido',
//        allowBlank: false,
//        fieldLabel: 'Usuario/s',
//        displayField:'nomape',
//        valueField:'id_usuario',
//        typeAhead: false,
//        loadingText: 'Buscando...',
//        anchor : '95%',
//        minChars:3,
////        labelStyle: 'font-weight:bold;',
//        pageSize:10,
//         tabIndex: 4,
//        emptyText:'Ingresa caracteres para buscar',
//        valueNotFoundText:""
//    });
    
// publicadorFieldSet = new Ext.form.FieldSet({
//    id:'publicadorFieldSet',
//    title : 'Publicador/es',
//    growMin:100,
//    items:[publicadorSBS]
//});

tituloDocumentoField = new Ext.form.TextField({
        id: 'tituloDocumentoField',
        name: 'tituloDocumentoField',
        fieldLabel: 'Titulo',
        allowBlank: false,
        blankText:'campo requerido',
        disabled:false,
        tabIndex:1,
        anchor : '95%'
    });

detalleDocumentoTexArea = new Ext.form.TextArea({
        id: 'detalleDocumentoTexArea',
        fieldLabel: 'Detalle de documento y revisión',
        maxLength:1024,
        height:200,
        allowBlank: true,
        tabIndex:2,
        anchor : '95%'    
});

var tipoWFRadios = new Ext.form.RadioGroup({ 
    id:'tipoWFRadios',
    fieldLabel: 'Tipo de Flujo de Trabajo',
    tabIndex:3,
    columns: 4,
    items: [ 
          {boxLabel: 'Largo', name: 'tipo_wf', inputValue: '2', checked: true}, 
          {boxLabel: 'Corto', name: 'tipo_wf', inputValue: '1'}
     ] 
});


//listener para los combos dependientes
//empresaCombo.on('select',function(cmb,record,index){
//	gerenciaCombo.enable();			
//	gerenciaCombo.clearValue();		
//	gerenciaCombo.reset();		
//	gerenciasDS.load({			
//            params:{
//                id_empresa:record.get('id_empresa')	
//            }	
//	});
//});

    DocumentoCreateForm = new Ext.FormPanel({
        id:'altaDocumento-form',
        title: 'Alta flujo de trabajo para nuevo documento',
        labelAlign: 'left',
        labelWidth:120,
        bodyStyle:'padding:5px',
        width: 600,
        buttonAlign:'center',
        autoScroll:true,
        items: [{
                id:'fieldset_form',
                layout:'column',
                border:false,
                items:[{
                    columnWidth:0.4,
                    layout: 'form',
                    border:false,
                    items: [tituloDocumentoField,detalleDocumentoTexArea,tiposDocumentoCombo,tipoWFRadios]
                    },{
                    columnWidth:0.4,
                    layout: 'form',
                    border:false,
                    items: [revisoresFieldSet,aprobadorFieldSet/*,publicadorFieldSet*/]
                    },{
                    columnWidth:0.3,
                    layout: 'form',
                    border:false,
                    items: []
                    }
                ]
        }
        ],
	buttons: [{
            text: 'Guardar',
            handler: createDocumento
	},{
            text: 'Cancelar',
            handler: salirAltaDocumento
            }]
    });

  var panelAltaDoc = new Ext.Panel({
    id: 'panelAltaDoc',
    layout:'fit',		
//    split: true,
//    bodyStyle: 'padding:15px',
    border: true,
    autoScroll : false,
//    height: 400,
    items: [DocumentoCreateForm],
    renderTo: 'grillita'

}); 
                
    altura=Ext.getBody().getSize().height - 100;
    panelAltaDoc.setHeight(altura);

    Ext.getCmp('browser').on('resize',function(comp){
        panelAltaDoc.setWidth(this.getSize().width);
        panelAltaDoc.setHeight(Ext.getBody().getSize().height - 100);
    });
    
 
//    documentoCreateWindow= new Ext.Window({
//        id: 'documentoCreateWindow',
//        title: 'Crear nuevo trámite',
//        closable:false,
//            modal:true,
//        width: 800,
//        height: 500,
//        plain:true,
//        layout: 'fit',
//        items: DocumentoCreateForm,
//        closeAction: 'close'
//    });		
//		
//    documentoCreateWindow.show();

// inserta usuario en DB
  function createDocumento(){
     if(isDocumentoFormValid()){
//console.log();
	 
      Ext.Ajax.request({   
        waitMsg: 'Por favor espere...',
        url: CARPETA+'/guardar',
        params: {
          id            : idDoc,
          documento     : tituloDocumentoField.getValue(),
          detalle       : detalleDocumentoTexArea.getValue(),
          tipo_wf       :tipoWFRadios.getValue().inputValue,
          id_td         :tiposDocumentoCombo.getValue(),
          revisores     :revisoresSBS.getValue(),
          aprobador     :aprobadoresCombo.getValue(),
          edit          : edita,
//          publicadores  :publicadorSBS.getValue(),
//          arch_pdf      :archivoPDF.getValue(),
//          arch_fuente   :archivoFuente.getValue()
        }, 
        success: function(response){              
          var result=eval(response.responseText);
          switch(result){
            case -10:
                Ext.MessageBox.alert('Error','Verifique sus permisos como editor');
                break;
            case -2:
                Ext.MessageBox.alert('Error','Hubo un error al modificar el registro!');
                break;
            case 1:
                Ext.MessageBox.alert('Alta OK','Datos guardados correctamente....',salirAltaDocumento);
                break;
            case 4:
                Ext.MessageBox.alert('Error','Falta completar campo requerido');
                break;
          default:
                Ext.MessageBox.alert('Error','No se pudo crear el documento.');
                break;
          }        
        },
        failure: function(response){
          var result=response.responseText;
          Ext.MessageBox.alert('Error','No se pudo conectar con el servidor. Intente m&aacute;s tarde');         
        }                      
      });
    } else {
      Ext.MessageBox.alert('Alerta', 'Los datos no son v&aacute;lidos. Complete correctamente el formulario.');
    }
  }

  
  // check if the form is valid
  function isDocumentoFormValid(){	  
	  var v1 = tituloDocumentoField.isValid();
	  var v2 = tiposDocumentoCombo.isValid();
	  var v3 = tipoWFRadios.isValid();
	  var v4 = tiposDocumentoCombo.isValid();
	  var v5 = revisoresSBS.isValid();
	  var v6 = aprobadoresCombo.isValid();
	  return(v1&&v2&&v3&&v4&&v5&&v6);
  }
  
  function salirAltaDocumento(){
      
    var myFormPanel=Ext.getCmp("altaDocumento-form").getForm();
//    console.log(myFormPanel);
    myFormPanel.destroy();
    myFormPanel.cleanDestroyed();
    
    Ext.get('browser').load({
        url: CARPETA+"/index/15",
        scripts: true,
        //params: "id="+ID_EDICION_PARAM,
        text: "Cargando Grilla..."
    });
 }

 Ext.getCmp('tipoWFRadios').on('change',function(radio){
       var n=parseInt(radio.getValue().inputValue);
       switch (n){
            case 1:
                Ext.getCmp('revisoresSBS').reset();
//                Ext.getCmp('qRevisoresRadios1').reset();
//                Ext.getCmp('qRevisoresRadios2').reset();
//                Ext.getCmp('qRevisoresSpinner').reset();
                Ext.getCmp('revisoresSBS').disable();
//                Ext.getCmp('qRevisoresRadios1').disable();
//                Ext.getCmp('qRevisoresRadios2').disable();
                Ext.getCmp('revisoresFieldSet').hide();
//                Ext.getCmp('qRevisoresRadios1').hide();
//                Ext.getCmp('qRevisoresRadios2').hide();
//                Ext.getCmp('revisoresFieldSet').disable();
                break;
            case 2:
                Ext.getCmp('revisoresSBS').enable();
//                Ext.getCmp('qRevisoresSpinner').enable();
                Ext.getCmp('revisoresFieldSet').enable();
                Ext.getCmp('revisoresFieldSet').show();
                break;
           
       }
    });
 
// create a Record constructor from a description of the fields
var docRecord = Ext.data.Record.create([ // creates a subclass of Ext.data.Record
    {name: 'tituloDocumentoField', mapping: 'tituloDocumentoField',dataIndex:'tituloDocumentoField'},
]);
var docNewRecord = new docRecord(
    {
        tituloDocumentoField: 'Probandoooo'
        
    },
    id // optionally specify the id of the record otherwise one is auto-assigned
);
    if (edita)
    {
//        console.log(docNewRecord);
        var ds1=Ext.getCmp("tiposDocumentoCombo").getStore();
        var ds2=Ext.getCmp("aprobadoresCombo").getStore();
        var ds3=Ext.getCmp("revisoresSBS").getStore();
        var editaPanel=Ext.getCmp("altaDocumento-form");
        editaPanel.setTitle("Editando flujo de trabajo Nro "+idDoc);
        
        ds1.load();
        ds2.load();
        ds3.load();
        
        ds2.on('load', function(){
             var myFormPanel=Ext.getCmp("altaDocumento-form").getForm();
//        console.log(form);
//        Ext.getCmp("altaDocumento-form").getForm().loadRecord(docNewRecord);
    myFormPanel.load({
    url: CARPETA+"/dameDocumento/",
    params: {
        id: idDoc
    },
    waitMsg: 'Cargando...',
     success: function(form, action){
//         var combo_td=Ext.getCmp("tiposDocumentoCombo").getStore();
//         combo_td.reload();
//        var sbs=Ext.getCmp("revisoresSBS");
//        sbs.setValue('1,20');
//         console.log(action);
     },
    failure: function(form, action) {
        Ext.Msg.alert("Load failed", action.result.errorMessage);
    }
});
        });
        
        
       
    }

