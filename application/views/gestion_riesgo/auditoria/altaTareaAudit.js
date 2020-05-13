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
function createHallazgoAuditoriaTarea(a,b,id_hallazgo){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts){var result=parseInt(response.responseText);switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':
go_createHallazgoAuditoriaTarea(a,b,id_hallazgo);break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}

  function go_createHallazgoAuditoriaTarea(a,b,id_hallazgo){
     if(ishallazgoAuditoriaTareaFormValid()){
        msgProcess('Guardando Tarea');
        Ext.Ajax.request({   
            waitMsg: 'Por favor espere...',
            url: CARPETA+'/insert_tarea',
            method: 'POST',
            params: {
                hallazgo     : hallazgoAuditoriaHallazgoField.getValue(),
                tarea        : hallazgoAuditoriaTareaField.getValue(),
                responsable  : hallazgoAuditoriaResponsablesCombo.getValue(),
                fecha        : hallazgoAuditoriaTareaFechaField.getValue(),
                tipoTarea    : hallazgoAuditoriaTareaTipoRadios.getValue().inputValue,
                id_hallazgo  : id_hallazgo
            }, 
            success: function(response){              
                var result=eval(response.responseText);
                switch(result){
                        case 1:
                            hallazgoAuditoriaTareaCreateWindow.close();
                            Ext.MessageBox.alert('Alta OK','La Tarea fue creada satisfactoriamente');
                            auditoriasHallazgosDataStore.reload();
                            auditoriaDataStore.reload();
                            hallazgosAuditoriasTareasDataStore.load({params: {id:id_hallazgo,start: 0}});
                            hallazgoAuditoriaTareasPanel.show();
                            break;
                        case 3:
                            Ext.MessageBox.alert('Error','Falta completar campos requeridos');
                            break;
                        case -1:
                            Ext.MessageBox.alert('Error','Verifique sus permisos');
                            break;
                        case -2:
                            Ext.MessageBox.alert('Error','Solo el auditor que dió de alta la auditoría puede dar de alta tareas');
                            break;
                        default:
                            Ext.MessageBox.alert('Error','No se pudo crear la Tarea.');
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
  function ishallazgoAuditoriaTareaFormValid(){	  
	  var v1 = hallazgoAuditoriaHallazgoField.isValid();
	  var v2 = hallazgoAuditoriaTareaField.isValid();
	  var v3 = hallazgoAuditoriaResponsablesCombo.isValid();
	  var v4 = hallazgoAuditoriaTareaFechaField.isValid();
	  return( v1 && v2 && v3 && v4);
  }
  
  // display or bring forth the form
  function displayHallazgoAuditoriaTareaFormWindow(id_hallazgo,txt_hallazgo){
//	 if(hallazgoAuditoriaTareaCreateForm){
//	 	if(hallazgoAuditoriaTareaCreateForm.findById('fieldsetid')) {
//		 //get the fieldset
//	
//	
//		 var oldfieldset = hallazgoAuditoriaTareaCreateForm.findById('fieldset_form');
//		 //var oldfieldset = hallazgoAuditoriaTareaCreateForm.items;
//		 
//			//iterate trough each of the component in the fieldset
//			oldfieldset.items.each(function(collection,item,length){
//				var i = item;
//				//destroy the object within the fieldset
//				for(i=item; i<length; i++){oldfieldset.items.get(i).destroy();}
//			});
//		}
//		hallazgoAuditoriaTareaCreateForm.destroy();
//		hallazgoAuditoriaTareaCreateWindow.destroy()
//	}
//		
//
  // reset the Form before opening it
  function resetPresidentForm(){
    hallazgoAuditoriaHallazgoField.setValue(txt_hallazgo);
    hallazgoAuditoriaTareaField.setValue('');
    hallazgoAuditoriaResponsablesCombo.setValue('');
    hallazgoAuditoriaTareaFechaField.setValue('');
  }	


hallazgoAuditoriaHallazgoField = new Ext.form.TextArea({
    id: 'hallazgoAuditoriaHallazgoField',
    fieldLabel: 'Hallazgo',
//    maxLength: 2000,
//    maxLengthText :"Texto Demasiado Largo: Cantidad máxima 2000 caracteres",
    allowBlank: false,
    blankText:'campo requerido',
    anchor : '95%',
//    cls:'x-item-disabled',
    disabled :true,
    readOnly:true,
    tabIndex: 2
});
hallazgoAuditoriaHallazgoField.setValue(txt_hallazgo);

hallazgoAuditoriaTareaField = new Ext.form.TextArea({
    id: 'hallazgoAuditoriaTareaField',
    fieldLabel: 'Describa la Tarea',
    maxLength: 2000,
    maxLengthText :"Texto Demasiado Largo: Cantidad máxima 2000 caracteres",
    allowBlank: false,
    blankText:'campo requerido',
    anchor : '95%',
    tabIndex: 3
});

hallazgoAuditoriaUsuariosDS = new Ext.data.Store({
        proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/combo_responsables',
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
            {name: 'puesto', mapping: 'puesto'},
        ])
    });

    // Custom rendering Template
    var hallazgoAuditoriaResponsableTpl = new Ext.XTemplate(
        '<tpl for="."><div class="search-item">',
            '<h3><span>{nomape}</h3>({puesto})</span>',
        '</div></tpl>'
    );
    
    hallazgoAuditoriaResponsablesCombo = new Ext.form.ComboBox({
        store: hallazgoAuditoriaUsuariosDS,
        blankText:'campo requerido',
        allowBlank: false,
        fieldLabel: 'Usuario',
        displayField:'nomape',
        valueField:'id_usuario',
        typeAhead: false,
        loadingText: 'Buscando...',
        anchor : '95%',
        minChars:3,
//        labelStyle: 'font-weight:bold;',
        pageSize:10,
         tabIndex: 11,
        emptyText:'Ingresa caracteres para buscar',
        valueNotFoundText:"",
        tpl: hallazgoAuditoriaResponsableTpl,
        itemSelector: 'div.search-item'
    });
 hallazgoAuditoriaResponsableFieldSet = new Ext.form.FieldSet({
    id:'hallazgoAuditoriaResponsableFieldSet',
    title : 'Persona Responsable de gestionar la Tarea',
    anchor : '95%',
    growMin:100,
    items:[hallazgoAuditoriaResponsablesCombo]
}); 

hallazgoAuditoriaTareaFechaField = new Ext.form.DateField({
            id:'hallazgoAuditoriaTareaFechaField',
            allowBlank: false,
            fieldLabel:'Fecha Límite',
            allowBlank: false,
            anchor : '90%',
            blankText:'campo requerido',
            editable: true,
            minValue:MINDATE,
//            maxValue:,
            format:'d/m/Y'
        });
  
 hallazgoAuditoriaTareaTipoRadios = new Ext.form.RadioGroup({ 
        id:'hallazgoAuditoriaTareaTipoRadios',
        fieldLabel: 'Tipo de Tarea',
        tabIndex:5,
        columns: 1,
        anchor : '95%',
        autoWidth: false,
        vertical: true,
        allowBlank: false,
        blankText:'Debe seleccionar una opción',
        items: [ 
            {boxLabel: 'Correccion inmediata', name: 'tipoTarea', inputValue: '1'}, 
            {boxLabel: 'No Conformidad', name: 'tipoTarea', inputValue: '2'}
        ] 
    });
  
  hallazgoAuditoriaTareaCreateForm = new Ext.FormPanel({
        labelAlign: 'top',
        bodyStyle:'padding:5px',
//        width:600,        
        items: [{
            id:'fieldset_form',
            layout:'fit',
            border:false,
            items:[{
    //                columnWidth:1,
                    anchor : '95%',
                    layout: 'form',
                    border:false,
                    items: [
                        hallazgoAuditoriaHallazgoField,
                        hallazgoAuditoriaTareaField,
                        hallazgoAuditoriaResponsableFieldSet,
                        {
                            layout: 'column',
                            border:false,
                            items: [{
                                    columnWidth:0.4,   
                                    layout: 'form',
                                    border:false,
                                    items:[hallazgoAuditoriaTareaFechaField]
                                    },
                                    {
                                    columnWidth:0.4,   
                                    layout: 'form',
                                    border:false,
                                    style: { marginLeft: '35px',lineHeight: '16px' },
                                    items:[hallazgoAuditoriaTareaTipoRadios]
                                    }]
                        }
                    ]
                }
            ]
        }],
		buttons: [{
		  text: 'Guardar',
		  handler: createHallazgoAuditoriaTarea.createDelegate(this,id_hallazgo,true)
		},{
		  text: 'Cancelar',
		  handler: function(){
			// because of the global vars, we can only instantiate one window... so let's just hide it.
			hallazgoAuditoriaTareaCreateWindow.close();
		  }
		}]
    });
	
 
  hallazgoAuditoriaTareaCreateWindow= new Ext.Window({
      id: 'hallazgoAuditoriaTareaCreateWindow',
      title: 'Crear nueva Tarea para el Hallazgo Nro: '+id_hallazgo,
      closable:false,
      modal:true,
      width: 500,
      height: 450,
      plain:true,
      layout: 'fit',
      items: hallazgoAuditoriaTareaCreateForm,
      closeAction: 'close'
    });		
    hallazgoAuditoriaTareaCreateWindow.show();
  }