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
function createOmcTarea(a,b,id_omc){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts){var result=parseInt(response.responseText);switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':
go_createOmcTarea(a,b,id_omc);break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}

  function go_createOmcTarea(a,b,id_omc){
     if(isomcTareaFormValid()){
        msgProcess('Guardando Tarea');
        Ext.Ajax.request({   
            waitMsg: 'Por favor espere...',
            url: CARPETA+'/insert_tarea',
            method: 'POST',
            params: {
                hallazgo     : omcHallazgoField.getValue(),
                grado_crit   : omcTareaCriticidadRadios.getValue().inputValue,
//                rpd          : omcTareaRevisionRadios.getValue().inputValue,
                tarea        : omcTareaField.getValue(),
                responsable  : omcResponsablesCombo.getValue(),
                fecha        : omcTareaFechaField.getValue(),
                id_omc       : id_omc
            }, 
            success: function(response){              
                var result=eval(response.responseText);
                switch(result){
                        case 1:
                            Ext.MessageBox.alert('Alta OK','La Tarea fue creada satisfactoriamente');
                            omcDataStore.reload();
                            omcTareasDataStore.load({params: {id:id_omc,start: 0}});
                            omcTareasPanel.show();
                            omcTareaCreateWindow.close();
                            break;
                        case 3:
                            Ext.MessageBox.alert('Error','Falta completar campos requeridos');
                            break;
                        case -1:
                            Ext.MessageBox.alert('Error','Verifique sus permisos');
                            break;
                        case -2:
                            Ext.MessageBox.alert('Error','Solo el usuario Observador puede dar de alta tareas');
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
  function isomcTareaFormValid(){	  
	  var v1 = omcHallazgoField.isValid();
	  var v2 = omcTareaField.isValid();
	  var v3 = omcResponsablesCombo.isValid();
	  var v4 = omcTareaFechaField.isValid();
          var v5 = omcTareaCriticidadRadios.isValid();
//          var v6 = omcTareaRevisionRadios.isValid();
	  return( v1 && v2 && v3 && v4 && v5);// && v6);
  }
  
  // display or bring forth the form
  function displayOmcTareaFormWindow(id_omc){
//	 if(omcTareaCreateForm){
//	 	if(omcTareaCreateForm.findById('fieldsetid')) {
//		 //get the fieldset
//	
//	
//		 var oldfieldset = omcTareaCreateForm.findById('fieldset_form');
//		 //var oldfieldset = omcTareaCreateForm.items;
//		 
//			//iterate trough each of the component in the fieldset
//			oldfieldset.items.each(function(collection,item,length){
//				var i = item;
//				//destroy the object within the fieldset
//				for(i=item; i<length; i++){oldfieldset.items.get(i).destroy();}
//			});
//		}
//		omcTareaCreateForm.destroy();
//		omcTareaCreateWindow.destroy()
//	}
//		
//
  // reset the Form before opening it
  function resetPresidentForm(){
    omcHallazgoField.setValue('');
    omcTareaField.setValue('');
    omcResponsablesCombo.setValue('');
    omcTareaFechaField.setValue('');
    omcTareaRevisionRadios.setValue('');
  }	


omcHallazgoField = new Ext.form.TextArea({
    id: 'omcHallazgoField',
    fieldLabel: 'Describa el hallazgo',
    maxLength: 2000,
    maxLengthText :"Texto Demasiado Largo: Cantidad máxima 2000 caracteres",
    allowBlank: false,
    blankText:'campo requerido',
    anchor : '95%',
    tabIndex: 2
});
omcTareaCriticidadRadios = new Ext.form.RadioGroup({ 
    id:'omcTareaCriticidadRadios',
    fieldLabel: 'Grado Criticidad',
    allowBlank: false,
    anchor : '95%',
    tabIndex:2.1,
    columns: 4,
    items: [ 
          {boxLabel: 'Crítica', name: 'tarea_crit', inputValue: '1'}, //, checked: true
          {boxLabel: 'Alta',    name: 'tarea_crit', inputValue: '2'}, 
          {boxLabel: 'Menor',   name: 'tarea_crit', inputValue: '3'}
     ] 
});

omcTareaRevisionRadios = new Ext.form.RadioGroup({ 
    id:'omcTareaRevisionRadios',
    fieldLabel: 'Entrada para revisión de la dirección',
    allowBlank: false,
    anchor : '95%',
    tabIndex:3,
    columns: 4,
    items: [ 
          {boxLabel: 'Si', name: 'tarea_revision', inputValue: '1'}, //, checked: true
          {boxLabel: 'No', name: 'tarea_revision', inputValue: '2'}
     ] 
});

omcTareaField = new Ext.form.TextArea({
    id: 'omcTareaField',
    fieldLabel: 'Describa la Tarea',
    maxLength: 2000,
    maxLengthText :"Texto Demasiado Largo: Cantidad máxima 2000 caracteres",
    allowBlank: false,
    blankText:'campo requerido',
    anchor : '95%',
    tabIndex: 3
});

omcUsuariosDS = new Ext.data.Store({
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
    var omcResponsableTpl = new Ext.XTemplate(
        '<tpl for="."><div class="search-item">',
            '<h3><span>{nomape}</h3>({puesto})</span>',
        '</div></tpl>'
    );
    
    omcResponsablesCombo = new Ext.form.ComboBox({
        store: omcUsuariosDS,
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
        tpl: omcResponsableTpl,
        itemSelector: 'div.search-item'
    });
 omcResponsableFieldSet = new Ext.form.FieldSet({
    id:'omcResponsableFieldSet',
    title : 'Persona Responsable de gestionar la Tarea',
    anchor : '95%',
    growMin:100,
    items:[omcResponsablesCombo]
}); 

omcTareaFechaField = new Ext.form.DateField({
            id:'omcTareaFechaField',
            allowBlank: false,
            fieldLabel:'Fecha Límite',
            allowBlank: false,
            anchor : '95%',
            blankText:'campo requerido',
            editable: true,
            minValue:MINDATE,
//            maxValue:,
            format:'d/m/Y'
        });
  
 
  omcTareaCreateForm = new Ext.FormPanel({
        labelAlign: 'top',
        bodyStyle:'padding:5px',
        width:600,        
        items: [{
            id:'fieldset_form',
            layout:'column',
            border:false,
            items:[{
                columnWidth:1,
                layout: 'form',
                border:false,
                items: [omcHallazgoField,omcTareaCriticidadRadios,omcTareaField,omcResponsableFieldSet,omcTareaFechaField]//omcTareaRevisionRadios,
            }]
        }],
		buttons: [{
		  text: 'Guardar',
		  handler: createOmcTarea.createDelegate(this,id_omc,true)
		},{
		  text: 'Cancelar',
		  handler: function(){
			// because of the global vars, we can only instantiate one window... so let's just hide it.
			omcTareaCreateWindow.close();
		  }
		}]
    });
	
 
  omcTareaCreateWindow= new Ext.Window({
      id: 'omcTareaCreateWindow',
      title: 'Crear nueva Tarea para la OMC Nro: '+id_omc,
      closable:false,
      modal:true,
      width: 500,
      height: 580,
      plain:true,
      layout: 'fit',
      items: omcTareaCreateForm,
      closeAction: 'close'
    });		
    omcTareaCreateWindow.show();
  }