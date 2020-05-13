function clickBtnEditarTarea (grid,rowIndex,colIndex,item,event){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts){var result=parseInt(response.responseText);switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':
go_clickBtnEditarTarea(grid,rowIndex,colIndex,item,event);break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}
  // display or bring forth the form
  function go_clickBtnEditarTarea(grid, rowIndex){
//	 if(tareaCreateForm){
//	 	if(tareaCreateForm.findById('fieldsetid')) {
//		 //get the fieldset
//		 var oldfieldset = tareaCreateForm.findById('fieldset_form');
//		 //var oldfieldset = tareaCreateForm.items;
//		 
//			//iterate trough each of the component in the fieldset
//			oldfieldset.items.each(function(collection,item,length){
//				var i = item;
//				//destroy the object within the fieldset
//				for(i=item; i<length; i++){oldfieldset.items.get(i).destroy();}
//			});
//		}
//		tareaCreateForm.destroy();
//		tareaUpdateWindow.destroy()
//	}
		
  var record = grid.getStore().getAt(rowIndex); 
  var id_tarea=record.data.id_tarea; 
  
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
function updatetarea (){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts){var result=parseInt(response.responseText);switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':
go_updatetarea();break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}

  function go_updatetarea(){
     if(istareaFormValid()){
        msgProcess('Guardando Tarea');
        Ext.Ajax.request({   
            waitMsg: 'Por favor espere...',
            url: CARPETA+'/tarea_edit',
            method: 'POST',
            params: {
                id_tarea        : id_tarea,
//                herramienta     : herramientasComboU.getValue(),
                hallazgo        : hallazgoFieldU.getValue(),
                grado_crit       :tareaCriticidadRadios.getValue().inputValue,
                tarea           : tareaFieldU.getValue(),
                responsable     : responsablesComboU.getValue(),
                fecha           : fechaFieldU.getValue()
            }, 
            success: function(response){              
                var result=eval(response.responseText);
                switch(result){
                        case 1:
                            Ext.MessageBox.alert('Alta OK','La tarea ha sido modificada');
                            tareasDataStore.reload();
                            tareaUpdateWindow.close();
                            break;
                        case 3:
                            Ext.MessageBox.alert('Error','Falta completar campos requeridos');
                            break;
                        default:
                            Ext.MessageBox.alert('Error','No se pudo crear la tarea.');
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
  function istareaFormValid(){	  
	  var v1 = hallazgoFieldU.isValid();
	  var v2 = tareaFieldU.isValid();
	  var v3 = responsablesComboU.isValid();
	  var v4 = fechaFieldU.isValid();
//	  var v5 = herramientasComboU.isValid();
          var v6 = tareaCriticidadRadios.isValid();
	  return( v1 && v2 && v3 && v4 && v6);
  } 
  

//herramientasDSU = new Ext.data.Store({
//    id: 'herramientasDS',
//    proxy: new Ext.data.HttpProxy({
//        url: CARPETA+'/combo_herramientas', 
//        method: 'POST'
//    }),
//    reader: new Ext.data.JsonReader({
//        root: 'rows',
//        totalProperty: 'total'
//        },[
//        {name: 'id_tipo_herramienta', type: 'int'},        
//        {name: 'tipo_herramienta', type: 'string'},
//    ])
//});
//herramientasDSU.load();
//herramientasComboU = new Ext.form.ComboBox({
//        id:'herramientasCombo',
//        name:'id_tipo_herramienta',
//        forceSelection : false,
//        fieldLabel: 'Tipo de Herramienta',
//        store: herramientasDSU,
//        editable : false,
//        displayField: 'tipo_herramienta',
//        allowBlank: false,
//        blankText:'campo requerido',
//        valueField: 'id_tipo_herramienta',
//        anchor:'95%',
//        tabIndex:3,
//        triggerAction: 'all',
//        width: 300
//    });
//    
  
hallazgoFieldU = new Ext.form.TextArea({
    id: 'hallazgoField',
    name:'hallazgo',
    fieldLabel: 'Describa el hallazgo',
    maxLength: 2000,
    maxLengthText :"Texto Demasiado Largo: Cantidad máxima 2000 caracteres",
    allowBlank: false,
    blankText:'campo requerido',
    anchor : '95%',
    tabIndex: 2
});
tareaCriticidadRadios = new Ext.form.RadioGroup({ 
    id:'tareaCriticidadRadios',
    name:'id_grado_crit',
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
tareaFieldU = new Ext.form.TextArea({
    id: 'tareaField',
    name:'tarea',
    fieldLabel: 'Describa la Tarea',
    maxLength: 2000,
    maxLengthText :"Texto Demasiado Largo: Cantidad máxima 2000 caracteres",
    allowBlank: false,
    blankText:'campo requerido',
    anchor : '95%',
    tabIndex: 3
});

usuariosDSU = new Ext.data.Store({
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
usuariosDSU.load({params:{query:record.data.usuario_responsable}});
    // Custom rendering Template
    var responsableTpl = new Ext.XTemplate(
        '<tpl for="."><div class="search-item">',
            '<h3><span>{nomape}</h3>({puesto})</span>',
        '</div></tpl>'
    );
    
    responsablesComboU = new Ext.form.ComboBox({
        name:'usuario_responsable',
        store: usuariosDSU,
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
        tpl: responsableTpl,
        itemSelector: 'div.search-item'
    });
 responsableFieldSetU = new Ext.form.FieldSet({
    id:'responsableFieldSet',
    title : 'Persona responsable de gestionar la tarea',
    anchor : '95%',
    growMin:100,
    items:[responsablesComboU]
}); 

fechaFieldU = new Ext.form.DateField({
            name: 'fecha_vto',
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
  
 
  tareaUpdateForm = new Ext.FormPanel({
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
                items: [hallazgoFieldU,tareaCriticidadRadios,tareaFieldU,responsableFieldSetU,fechaFieldU]
            }]
        }],
		buttons: [{
		  text: 'Guardar',
		  handler: updatetarea
		},{
		  text: 'Cancelar',
		  handler: function(){
			// because of the global vars, we can only instantiate one window... so let's just hide it.
			tareaUpdateWindow.close();
		  }
		}]
    });
    tareaUpdateForm.load(
	{
		url: CARPETA+'/traer_tarea',
		params: {id_tarea: id_tarea},
		waitMsg: 'Cargando Datos...',
		success:function(data)
		{
//                    console.log(data);
		}
	});
    
//    setForm();
 
  tareaUpdateWindow= new Ext.Window({
      id: 'tareaUpdateWindow',
      title: 'Edita tarea nro:'+id_tarea,
      closable:false,
      modal:true,
      width: 500,
      height: 500,
      plain:true,
      layout: 'fit',
      items: tareaUpdateForm,
      closeAction: 'close'
    });		
		
		
	    tareaUpdateWindow.show();
  }