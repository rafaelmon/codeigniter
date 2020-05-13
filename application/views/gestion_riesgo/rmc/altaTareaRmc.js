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
  function createRmcTarea(a,b,id_rmc){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts){var result=parseInt(response.responseText);switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':go_createRmcTarea(a,b,id_rmc);break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}

  function go_createRmcTarea(a,b,id_rmc){
     if(isrmcTareaFormValid()){
        msgProcess('Guardando Tarea');
        Ext.Ajax.request({   
            waitMsg: 'Por favor espere...',
            url: CARPETA+'/insert_tarea',
            method: 'POST',
            params: {
                hallazgo     : rmcHallazgoField.getValue(),
                grado_crit   : rmcTareaCriticidadRadios.getValue().inputValue,
                tarea        : rmcTareaField.getValue(),
                responsable  : rmcResponsablesCombo.getValue(),
                fecha        : rmcTareaFechaField.getValue(),
//                rpd          : rmcTareaRevisionRadios.getValue().inputValue,
                id_rmc       : id_rmc
            }, 
            success: function(response){              
                var result=eval(response.responseText);
                switch(result){
                        case 1:
                            Ext.MessageBox.alert('Alta OK','La Tarea fue creado satisfactoriamente.');
                            rmcDataStore.reload();
                            rmcTareasDataStore.load({params: {id:id_rmc,start: 0}});
                            rmcTareasPanel.show();
                            rmcTareaCreateWindow.close();
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
  function isrmcTareaFormValid(){	  
	  var v1 = rmcHallazgoField.isValid();
	  var v2 = rmcTareaField.isValid();
	  var v3 = rmcResponsablesCombo.isValid();
	  var v4 = rmcTareaFechaField.isValid();
          var v5 = rmcTareaCriticidadRadios.isValid();
//          var v6 = rmcTareaRevisionRadios.isValid();
	  return( v1 && v2 && v3 && v4 && v5);// && v6);
  }
  
//    function displayRmcTareaFormWindow(id_rmc){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts){var result=parseInt(response.responseText);switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':
//go_displayRmcTareaFormWindow(id_rmc);break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}
  
  // display or bring forth the form
  function displayRmcTareaFormWindow(id_rmc){
//	 if(rmcTareaCreateForm){
//	 	if(rmcTareaCreateForm.findById('fieldsetid')) {
//		 //get the fieldset
//	
//	
//		 var oldfieldset = rmcTareaCreateForm.findById('fieldset_form');
//		 //var oldfieldset = rmcTareaCreateForm.items;
//		 
//			//iterate trough each of the component in the fieldset
//			oldfieldset.items.each(function(collection,item,length){
//				var i = item;
//				//destroy the object within the fieldset
//				for(i=item; i<length; i++){oldfieldset.items.get(i).destroy();}
//			});
//		}
//		rmcTareaCreateForm.destroy();
//		rmcTareaCreateWindow.destroy()
//	}
//		
//
  // reset the Form before opening it
  function resetPresidentForm(){
    rmcHallazgoField.setValue('');
    rmcTareaField.setValue('');
    rmcResponsablesCombo.setValue('');
    rmcTareaFechaField.setValue('');
    rmcTareaRevisionRadios.setValue('');
  }	


rmcHallazgoField = new Ext.form.TextArea({
    id: 'rmcHallazgoField',
    fieldLabel: 'Describa el hallazgo',
    maxLength: 2000,
    maxLengthText :"Texto Demasiado Largo: Cantidad máxima 2000 caracteres",
    allowBlank: false,
    blankText:'campo requerido',
    anchor : '95%',
    tabIndex: 2
});
rmcTareaCriticidadRadios = new Ext.form.RadioGroup({ 
    id:'rmcTareaCriticidadRadios',
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

rmcTareaRevisionRadios = new Ext.form.RadioGroup({ 
    id:'rmcTareaRevisionRadios',
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

rmcTareaField = new Ext.form.TextArea({
    id: 'rmcTareaField',
    fieldLabel: 'Describa la Tarea',
    maxLength: 2000,
    maxLengthText :"Texto Demasiado Largo: Cantidad máxima 2000 caracteres",
    allowBlank: false,
    blankText:'campo requerido',
    anchor : '95%',
    tabIndex: 3
});

rmcUsuariosDS = new Ext.data.Store({
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
    var rmcResponsableTpl = new Ext.XTemplate(
        '<tpl for="."><div class="search-item">',
            '<h3><span>{nomape}</h3>({puesto})</span>',
        '</div></tpl>'
    );
    
    rmcResponsablesCombo = new Ext.form.ComboBox({
        store: rmcUsuariosDS,
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
        tpl: rmcResponsableTpl,
        itemSelector: 'div.search-item'
    });
 rmcResponsableFieldSet = new Ext.form.FieldSet({
    id:'rmcResponsableFieldSet',
    title : 'Persona Responsable de gestionar la Tarea',
    anchor : '95%',
    growMin:100,
    items:[rmcResponsablesCombo]
}); 

rmcTareaFechaField = new Ext.form.DateField({
            id:'rmcTareaFechaField',
            allowBlank: false,
            fieldLabel:'Fecha Límite',
            anchor : '95%',
            blankText:'campo requerido',
            editable: true,
            minValue:MINDATE,
//            maxValue:,
            format:'d/m/Y'
        });
  
 
  rmcTareaCreateForm = new Ext.FormPanel({
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
                items: [rmcHallazgoField,rmcTareaCriticidadRadios,/*rmcTareaRevisionRadios,*/rmcTareaField,rmcResponsableFieldSet,rmcTareaFechaField]
            }]
        }],
		buttons: [{
		  text: 'Guardar',
		  handler: createRmcTarea.createDelegate(this,id_rmc,true)
		},{
		  text: 'Cancelar',
		  handler: function(){
			// because of the global vars, we can only instantiate one window... so let's just hide it.
			rmcTareaCreateWindow.close();
		  }
		}]
    });
	
 
  rmcTareaCreateWindow= new Ext.Window({
      id: 'rmcTareaCreateWindow',
      title: 'Crear nueva Tarea para el RI Nro: '+id_rmc,
      closable:false,
      modal:true,
      width: 500,
      height: 600,
      plain:true,
      layout: 'fit',
      items: rmcTareaCreateForm,
      closeAction: 'close'
    });		
    rmcTareaCreateWindow.show();
  }